
document.addEventListener('DOMContentLoaded', () => {
    // --- STATE MANAGEMENT ---
    let currentFile = null;
    let apiKey = localStorage.getItem('fireworks_api_key') || '';
    let generationStatusInterval = null;

    // --- DOM ELEMENT SELECTORS ---
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const apiKeyInput = document.getElementById('api-key-input');
    const saveApiKeyBtn = document.getElementById('save-api-key');

    const navItems = document.querySelectorAll('.nav-item');
    const views = document.querySelectorAll('.view');
    const headerTitle = document.getElementById('header-title');
    const headerSubtitle = document.getElementById('header-subtitle');

    const analysisSummary = document.getElementById('analysis-summary');
    const analysisResults = document.getElementById('analysis-results');
    const startAnalysisBtn = document.getElementById('start-analysis-btn');

    // --- GENERATION SETTINGS ELEMENTS ---
    const startGenerationBtn = document.getElementById('start-generation-btn');
    const generationFormSection = document.getElementById('generation-form-section');
    const generationProgressSection = document.getElementById('generation-progress-section');
    const generationCompleteSection = document.getElementById('generation-complete-section');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const generatedCountEl = document.getElementById('generated-count');
    const targetCountEl = document.getElementById('target-count');
    const downloadLink = document.getElementById('download-link');
    const finalGeneratedCount = document.getElementById('final-generated-count');
    const restartBtn = document.getElementById('restart-btn');


    // --- INITIALIZATION ---
    apiKeyInput.value = apiKey;

    // --- EVENT LISTENERS ---

    // API Key Handling
    saveApiKeyBtn.addEventListener('click', () => {
        apiKey = apiKeyInput.value;
        if (apiKey) {
            localStorage.setItem('fireworks_api_key', apiKey);
            alert('API Key saved successfully!');
        } else {
            localStorage.removeItem('fireworks_api_key');
            alert('API Key removed.');
        }
    });

    // Navigation
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const viewId = item.getAttribute('data-view');
            switchView(viewId);
        });
    });
    
    document.getElementById('show-generation-settings-btn').addEventListener('click', () => {
        switchView('settings-view');
    });

    // File Drop Zone
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('drag-over');
    });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });
    dropZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFile(fileInput.files[0]);
        }
    });

    // Analysis and Generation Buttons
    startAnalysisBtn.addEventListener('click', runAnalysis);
    startGenerationBtn.addEventListener('click', runGeneration);
    restartBtn.addEventListener('click', () => window.location.reload());

    // --- FUNCTIONS ---

    function switchView(viewId) {
        views.forEach(view => view.classList.remove('active-view'));
        document.getElementById(viewId).classList.add('active-view');

        navItems.forEach(nav => {
            if (nav.getAttribute('data-view') === viewId) {
                nav.classList.add('active');
                headerTitle.textContent = nav.textContent;
                headerSubtitle.textContent = viewId === 'upload-view'
                    ? 'Upload and analyze your dataset'
                    : 'Configure and start data generation';
            } else {
                nav.classList.remove('active');
            }
        });
    }

    function handleFile(file) {
        if (!file.name.endsWith('.jsonl')) {
            alert('Invalid file type. Please upload a .jsonl file.');
            return;
        }
        currentFile = file;
        
        // Show the summary card, hide the drop zone
        dropZone.classList.add('hidden');
        analysisSummary.classList.remove('hidden');
        analysisResults.classList.add('hidden'); // Hide old results

        // Update summary card
        document.getElementById('dataset-filename-summary').textContent = file.name;
        document.getElementById('file-size').textContent = `${(file.size / 1024).toFixed(2)} KB`;
        document.getElementById('file-format').textContent = 'JSONL';
        document.getElementById('total-records').textContent = '...';
        document.getElementById('analysis-status-value').textContent = 'Ready to Analyze';
        startAnalysisBtn.disabled = false;
    }

    async function runAnalysis() {
        if (!currentFile) return;

        startAnalysisBtn.disabled = true;
        startAnalysisBtn.innerHTML = `
            <svg class="spinner" viewBox="0 0 50 50"><circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle></svg>
            Analyzing...`;
        
        const formData = new FormData();
        formData.append('dataset', currentFile);

        try {
            const response = await fetch('api/analyze.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error(`Server error: ${response.statusText}`);

            const results = await response.json();
            
            if (results.error) throw new Error(results.error);

            displayAnalysisResults(results);

        } catch (error) {
            alert(`Analysis failed: ${error.message}`);
            startAnalysisBtn.disabled = false;
            startAnalysisBtn.innerHTML = `Start Analysis`;
        }
    }

    function displayAnalysisResults(results) {
        // Hide summary and show results
        analysisSummary.classList.add('hidden');
        analysisResults.classList.remove('hidden');

        // Populate recommendations
        const recommendationsList = document.getElementById('recommendations-list');
        recommendationsList.innerHTML = '';
        results.recommendations.forEach(rec => {
            const li = document.createElement('li');
            li.textContent = rec;
            recommendationsList.appendChild(li);
        });

        // Populate AI Assessment
        const gradeEl = document.getElementById('quality-grade');
        gradeEl.textContent = results.qualityGrade;
        gradeEl.className = results.qualityGrade; // For styling

        const readyEl = document.getElementById('training-ready');
        readyEl.textContent = results.trainingReady.replace(/-/g, ' ');
        readyEl.className = results.trainingReady;
    }

    async function runGeneration() {
        if (!apiKey) {
            alert('Please enter and save your Fireworks.ai API Key first.');
            apiKeyInput.focus();
            return;
        }

        const settings = {
            targetCount: document.getElementById('target-record-count').value,
            generationModel: document.getElementById('generation-model').value,
            validationModel: document.getElementById('validation-model').value,
            userPrompt: document.getElementById('user-prompt').value,
            apiKey: apiKey,
        };

        const formData = new FormData();
        formData.append('dataset', currentFile);
        formData.append('settings', JSON.stringify(settings));

        startGenerationBtn.disabled = true;
        generationFormSection.classList.add('hidden');
        generationProgressSection.classList.remove('hidden');
        
        targetCountEl.textContent = settings.targetCount;

        try {
            // Start generation - this request returns immediately with a session ID
            const startResponse = await fetch('api/generate.php', {
                method: 'POST',
                body: formData
            });

            if (!startResponse.ok) {
                const errorText = await startResponse.text();
                throw new Error(`Failed to start generation: ${errorText}`);
            }

            const { sessionId } = await startResponse.json();

            // Start polling for status
            generationStatusInterval = setInterval(() => checkGenerationStatus(sessionId), 2000);

        } catch (error) {
            alert(`Error: ${error.message}`);
            // Reset UI
            startGenerationBtn.disabled = false;
            generationFormSection.classList.remove('hidden');
            generationProgressSection.classList.add('hidden');
        }
    }
    
    async function checkGenerationStatus(sessionId) {
        try {
            const response = await fetch(`api/generate.php?session_id=${sessionId}`);
            const status = await response.json();

            if (status.error) {
                throw new Error(status.error);
            }

            // Update UI
            const progress = status.progress || 0;
            progressBar.style.width = `${progress}%`;
            progressText.textContent = status.currentStep;
            generatedCountEl.textContent = status.generatedCount;
            
            if (status.status === 'COMPLETED' || status.status === 'FAILED') {
                clearInterval(generationStatusInterval);
                generationProgressSection.classList.add('hidden');
                
                if (status.status === 'COMPLETED') {
                    generationCompleteSection.classList.remove('hidden');
                    finalGeneratedCount.textContent = status.generatedCount;
                    downloadLink.href = status.outputFile;
                } else {
                    alert(`Generation failed: ${status.currentStep}`);
                    // Optionally reset to the form
                    generationFormSection.classList.remove('hidden');
                }
            }
        } catch (error) {
            console.error('Status check failed:', error);
            clearInterval(generationStatusInterval);
            alert('Lost connection to the generation process. Please check server logs.');
        }
    }
});

// Add spinner CSS to be injected
const style = document.createElement('style');
style.innerHTML = `
.spinner {
  animation: rotate 2s linear infinite;
  width: 16px;
  height: 16px;
}
.spinner .path {
  stroke: var(--bg-color);
  stroke-linecap: round;
  animation: dash 1.5s ease-in-out infinite;
}
@keyframes rotate {
  100% { transform: rotate(360deg); }
}
@keyframes dash {
  0% { stroke-dasharray: 1, 150; stroke-dashoffset: 0; }
  50% { stroke-dasharray: 90, 150; stroke-dashoffset: -35; }
  100% { stroke-dasharray: 90, 150; stroke-dashoffset: -124; }
}
`;
document.head.appendChild(style);
