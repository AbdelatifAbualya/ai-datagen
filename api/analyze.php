<?php
header('Content-Type: application/json');

// --- Helper Functions ---
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// --- Main Logic ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}

if (!isset($_FILES['dataset']) || $_FILES['dataset']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'File upload failed.']);
    exit;
}

$filePath = $_FILES['dataset']['tmp_name'];
$fileSize = $_FILES['dataset']['size'];

$totalRecords = 0;
$emptyRecords = 0;
$lineHashes = [];
$totalResponseLength = 0;
$responseCount = 0;

$fileHandle = fopen($filePath, 'r');
if (!$fileHandle) {
    echo json_encode(['error' => 'Could not open uploaded file.']);
    exit;
}

while (($line = fgets($fileHandle)) !== false) {
    $line = trim($line);
    if (empty($line)) {
        $emptyRecords++;
        continue;
    }
    
    $totalRecords++;
    $lineHashes[] = md5($line);
    
    $record = json_decode($line, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($record)) {
        // Try to find a 'response' or 'output' or 'assistant' key for length analysis
        $response_key = '';
        if (isset($record['response'])) $response_key = 'response';
        elseif (isset($record['output'])) $response_key = 'output';
        elseif (isset($record['messages']) && is_array($record['messages'])) {
            foreach($record['messages'] as $msg) {
                if(isset($msg['role']) && $msg['role'] === 'assistant' && isset($msg['content'])) {
                    $response_key = 'assistant';
                    $record[$response_key] = $msg['content'];
                    break;
                }
            }
        }
        
        if (!empty($response_key) && !empty($record[$response_key])) {
            $totalResponseLength += str_word_count($record[$response_key]);
            $responseCount++;
        }
    }
}
fclose($fileHandle);

// --- Calculations ---
$duplicateCount = count($lineHashes) - count(array_unique($lineHashes));
$uniquenessScore = $totalRecords > 0 ? (1 - ($duplicateCount / $totalRecords)) * 100 : 0;
$avgResponseLength = $responseCount > 0 ? round($totalResponseLength / $responseCount) : 0;

// --- Generate Recommendations ---
$recommendations = [];
if ($emptyRecords > 0) {
    $recommendations[] = "Found {$emptyRecords} empty lines. Consider removing them for cleaner data.";
}
if ($duplicateCount > 0) {
    $recommendations[] = "Found {$duplicateCount} groups of exact duplicate records. Remove these to improve data quality.";
}
if ($avgResponseLength > 0 && $avgResponseLength < 50) {
    $recommendations[] = "Average response length is quite short ({$avgResponseLength} words). Consider including more detailed responses for better training quality.";
}
if ($uniquenessScore < 80) {
    $recommendations[] = "Low uniqueness score (" . round($uniquenessScore) . "%). Significant duplicate content detected.";
}
if (empty($recommendations)) {
    $recommendations[] = "Dataset looks structurally sound. No major issues detected from basic analysis.";
}

// --- Determine Quality Grade ---
$grade = 'A';
$trainingReady = 'Recommended';
if ($uniquenessScore < 50 || $avgResponseLength < 20) {
    $grade = 'F';
    $trainingReady = 'Not-Recommended';
} elseif ($uniquenessScore < 80 || $duplicateCount > ($totalRecords * 0.1)) {
    $grade = 'D';
    $trainingReady = 'Needs-Review';
} elseif ($avgResponseLength < 50) {
    $grade = 'C';
    $trainingReady = 'Needs-Review';
} elseif ($duplicateCount > 0) {
    $grade = 'B';
}


// --- Final JSON Output ---
$output = [
    'fileName' => $_FILES['dataset']['name'],
    'fileSize' => formatBytes($fileSize),
    'totalRecords' => $totalRecords,
    'duplicateCount' => $duplicateCount,
    'uniquenessScore' => round($uniquenessScore, 2),
    'avgResponseLength' => $avgResponseLength,
    'recommendations' => $recommendations,
    'qualityGrade' => $grade,
    'trainingReady' => $trainingReady,
];

echo json_encode($output);
