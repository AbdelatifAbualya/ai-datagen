
<?php
// Increase execution time for the generation process.
// For a real production app, a proper background job queue is recommended.
set_time_limit(0);
ignore_user_abort(true);

// --- Helper function to call Fireworks.ai API ---
function callFireworksAPI($apiKey, $model, $messages, $maxTokens, $temperature) {
    $url = 'https://api.fireworks.ai/inference/v1/chat/completions';
    $data = [
        'model' => $model,
        'messages' => $messages,
        'max_tokens' => $maxTokens,
        'temperature' => $temperature,
    ];
    // For validation model, request JSON output if possible
    if (strpos($model, 'firellava') !== false) { // A heuristic
        // Note: Not all models support this reliably. This is an attempt.
        // The prompt should still explicitly ask for JSON.
    }

    $payload = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
        'Content-Length: ' . strlen($payload)
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        // Log detailed error
        file_put_contents('error.log', "Fireworks API Error ($http_code) for model $model: $response\n", FILE_APPEND);
        return null;
    }

    return json_decode($response, true);
}


// --- Function to update the session status file ---
function updateStatus($sessionId, $status, $progress, $generatedCount, $targetCount, $currentStep, $outputFile = null) {
    $statusData = [
        'sessionId' => $sessionId,
        'status' => $status,
        'progress' => $progress,
        'generatedCount' => $generatedCount,
        'targetCount' => $targetCount,
        'currentStep' => $currentStep,
        'outputFile' => $outputFile,
        'timestamp' => time(),
    ];
    file_put_contents("../generated/{$sessionId}.status.json", json_encode($statusData));
}

// --- Main Request Handler ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // START a new generation session
    $sessionId = uniqid('gen_', true);
    $settings = json_decode($_POST['settings'], true);

    if (!isset($_FILES['dataset']) || json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid settings or no file uploaded.']);
        exit;
    }

    // Move uploaded file to a permanent location for this session
    $sourceFilePath = "../uploads/{$sessionId}_source.jsonl";
    if (!move_uploaded_file($_FILES['dataset']['tmp_name'], $sourceFilePath)) {
        http_response_code(500);
        echo json_encode(['error' => 'Could not save source dataset.']);
        exit;
    }

    // Immediately respond to the client so it can start polling
    header('Content-Type: application/json');
    echo json_encode(['sessionId' => $sessionId]);
    
    // Close connection and run in the background
    session_write_close();
    fastcgi_finish_request();

    // --- BACKGROUND PROCESSING ---
    $targetCount = (int)$settings['targetCount'];
    $apiKey = $settings['apiKey'];
    $genModel = $settings['generationModel'];
    $valModel = $settings['validationModel'];
    $userPrompt = $settings['userPrompt'];

    $outputFileName = "{$sessionId}_generated.jsonl";
    $outputFilePath = "../generated/{$outputFileName}";
    $outputFileWebPath = "generated/{$outputFileName}";
    
    $generatedCount = 0;
    
    updateStatus($sessionId, 'RUNNING', 0, 0, $targetCount, 'Initializing...');
    
    $sourceData = file($sourceFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if(empty($sourceData)) {
         updateStatus($sessionId, 'FAILED', 0, 0, $targetCount, 'Source dataset is empty or unreadable.');
         exit;
    }

    $outputHandle = fopen($outputFilePath, 'w');

    while ($generatedCount < $targetCount) {
        $progress = round(($generatedCount / $targetCount) * 100);
        updateStatus($sessionId, 'RUNNING', $progress, $generatedCount, $targetCount, "Generating record " . ($generatedCount + 1) . "...");
        
        // Pick a random line from the source data as a seed
        $seedRecord = $sourceData[array_rand($sourceData)];

        // 1. GENERATE NEW DATA
        $generationMessages = [
            ['role' => 'system', 'content' => $userPrompt],
            ['role' => 'user', 'content' => "Here is an example record:\n\n```json\n" . $seedRecord . "\n```\n\nNow, generate a new, unique record based on this example."]
        ];
        
        $genResult = callFireworksAPI($apiKey, $genModel, $generationMessages, 2048, 0.5);

        if (!$genResult || empty($genResult['choices'][0]['message']['content'])) {
            // Skip this iteration if generation fails
            updateStatus($sessionId, 'RUNNING', $progress, $generatedCount, $targetCount, "Generation failed for record " . ($generatedCount + 1) . ". Retrying...");
            sleep(1);
            continue;
        }

        $newContent = $genResult['choices'][0]['message']['content'];
        // Clean up markdown code blocks if the model includes them
        $newContent = preg_replace('/^```json\s*|\s*```$/', '', $newContent);

        // 2. VALIDATE THE GENERATED DATA
        updateStatus($sessionId, 'RUNNING', $progress, $generatedCount, $targetCount, "Validating record " . ($generatedCount + 1) . "...");

        $validationMessages = [
            ['role' => 'system', 'content' => 'You are a strict data quality evaluator. Evaluate the following data record. Respond ONLY with a JSON object with two keys: "score" (a number from 0 to 100, where >70 is good) and "isValidJSON" (a boolean).'],
            ['role' => 'user', 'content' => $newContent]
        ];
        
        $valResult = callFireworksAPI($apiKey, $valModel, $validationMessages, 100, 0.1);
        
        $score = 0;
        $isValidJson = false;
        if ($valResult && !empty($valResult['choices'][0]['message']['content'])) {
            $valJson = json_decode($valResult['choices'][0]['message']['content'], true);
            if(json_last_error() === JSON_ERROR_NONE && isset($valJson['score']) && isset($valJson['isValidJSON'])) {
                $score = (int)$valJson['score'];
                $isValidJson = (bool)$valJson['isValidJSON'];
            }
        }
        
        // Also self-validate JSON structure
        if (!$isValidJson) {
            json_decode($newContent);
            if (json_last_error() === JSON_ERROR_NONE) {
                $isValidJson = true;
            }
        }
        
        // 3. SAVE IF VALID
        if ($isValidJson && $score >= 70) {
            fwrite($outputHandle, $newContent . "\n");
            $generatedCount++;
        } else {
            // Log rejection if needed
        }
    }
    
    fclose($outputHandle);
    updateStatus($sessionId, 'COMPLETED', 100, $generatedCount, $targetCount, 'Generation complete!', $outputFileWebPath);
    exit;

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['session_id'])) {
    // GET session status
    $sessionId = basename($_GET['session_id']); // Basic sanitization
    $statusFile = "../generated/{$sessionId}.status.json";

    header('Content-Type: application/json');
    if (file_exists($statusFile)) {
        echo file_get_contents($statusFile);
    } else {
        echo json_encode(['error' => 'Session not found.', 'status' => 'FAILED']);
    }
    exit;
}

// Fallback for invalid requests
http_response_code(400);
echo json_encode(['error' => 'Invalid request.']);
