
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI DataGen - Dataset Expansion</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="app-container">
        <!-- ===== SIDEBAR ===== -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <svg class="logo-icon" viewBox="0 0 24 24"><path d="M12.5 3.5c0-.8-.7-1.5-1.5-1.5s-1.5.7-1.5 1.5v1c-2.2 0-4 1.8-4 4v1.5c-1.9 0-3.5 1.6-3.5 3.5s1.6 3.5 3.5 3.5h1.5c0 2.2 1.8 4 4 4h1c.8 0 1.5-.7 1.5-1.5s-.7-1.5-1.5-1.5h-1c-1.1 0-2-.9-2-2h1.5c1.9 0 3.5-1.6 3.5-3.5S16.4 8 14.5 8H13V7c0-1.1.9-2 2-2h1.5c.8 0 1.5-.7 1.5-1.5S17.3 2 16.5 2H15c-2.2 0-4 1.8-4 4v1h2.5c.8 0 1.5.7 1.5 1.5s-.7 1.5-1.5 1.5H11V7c0-1.1.9-2 2-2s2 .9 2 2v1.5c1.9 0 3.5 1.6 3.5 3.5s-1.6 3.5-3.5 3.5H13v1.5c0 1.1-.9 2-2 2s-2-.9-2-2V16H7.5c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5H9c2.2 0 4-1.8 4-4v-1H7.5c-.8 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5H10V7c0-2.2-1.8-4-4-4H4.5c-.8 0-1.5.7-1.5 1.5S3.7 6 4.5 6H6c1.1 0 2 .9 2 2v1.5c-1.9 0-3.5 1.6-3.5 3.5S2.6 16 4.5 16H6V14c0-1.1-.9-2-2-2H2.5c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5h1c1.1 0 2 .9 2 2v1.5c-1.9 0-3.5 1.6-3.5 3.5s1.6 3.5 3.5 3.5h11c1.9 0 3.5-1.6 3.5-3.5s-1.6-3.5-3.5-3.5z"/></svg>
                <div class="sidebar-title">
                    <h1>AI DataGen</h1>
                    <span>Dataset Expansion</span>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="#" class="nav-item active" data-view="upload-view"><svg viewBox="0 0 24 24"><path d="M4 14h4v6h6v-6h4l-7-7-7 7zm16-4h-4V4H8v6H4l7 7 7-7z"/></svg>Upload & Analysis</a>
                <a href="#" class="nav-item" data-view="settings-view"><svg viewBox="0 0 24 24"><path d="M19.4 7.6c-.2-.5-.4-1-.7-1.3l-2.8-2.8c-.4-.4-.8-.6-1.3-.7C14.1 2.4 13.6 2 13 2h-2c-.6 0-1.1.4-1.6.8-.5.2-1 .4-1.3.7L5.3 5.3c-.4.4-.6.8-.7 1.3-.4.5-.8 1-.8 1.6v2c0 .6.4 1.1.8 1.6.2.5.4 1 .7 1.3l2.8 2.8c.4.4.8.6 1.3.7.5.4 1 .8 1.6.8h2c.6 0 1.1-.4 1.6-.8.5-.2 1-.4 1.3-.7l2.8-2.8c.4-.4.6-.8.7-1.3.4-.5.8-1 .8-1.6v-2c0-.6-.4-1.1-.8-1.6zM12 15c-1.7 0-3-1.3-3-3s1.3-3 3-3 3 1.3 3 3-1.3 3-3 3z"/></svg>Generation Settings</a>
            </nav>
            <div class="sidebar-footer">
                <div class="status-light online"></div>
                <span>All systems online</span>
            </div>
        </aside>

        <!-- ===== MAIN CONTENT ===== -->
        <main class="main-content">
            <!-- ===== HEADER ===== -->
            <header class="main-header">
                <div>
                    <h2 id="header-title">Dashboard</h2>
                    <p id="header-subtitle">Monitor your AI dataset expansion pipeline</p>
                </div>
                <div class="header-controls">
                    <input type="password" id="api-key-input" placeholder="Enter Fireworks.ai API Key...">
                    <button id="save-api-key" title="Save API Key">
                        <svg viewBox="0 0 24 24"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>
                    </button>
                </div>
            </header>

            <!-- View 1: Upload & Initial State -->
            <div id="upload-view" class="view active-view">
                <div id="drop-zone">
                    <svg viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"/></svg>
                    <h3>Drag & drop a .jsonl file here</h3>
                    <p>or</p>
                    <input type="file" id="file-input" accept=".jsonl" style="display: none;">
                    <button class="button" onclick="document.getElementById('file-input').click();">Select File</button>
                </div>

                <div id="analysis-summary" class="hidden">
                    <h3 class="section-title">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                        Dataset Overview: <span id="dataset-filename-summary"></span>
                    </h3>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h4>Total Records</h4>
                            <p id="total-records">--</p>
                        </div>
                        <div class="stat-card">
                            <h4>File Size</h4>
                            <p id="file-size">--</p>
                        </div>
                        <div class="stat-card">
                            <h4>Format</h4>
                            <p id="file-format">--</p>
                        </div>
                        <div class="stat-card">
                            <h4 id="analysis-status-text">Analysis Status</h4>
                            <p id="analysis-status-value">Not Started</p>
                        </div>
                    </div>
                    <div class="button-container">
                        <button id="start-analysis-btn" class="button primary" disabled>
                            <svg viewBox="0 0 24 24"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
                            Start Analysis
                        </button>
                    </div>
                </div>

                <div id="analysis-results" class="hidden">
                    <h3 class="section-title">
                        <svg viewBox="0 0 24 24"><path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z"/></svg>
                        Analysis Results
                    </h3>
                    <div id="recommendations">
                         <h4>Recommendations</h4>
                         <ul id="recommendations-list">
                            <!-- JS will populate this -->
                         </ul>
                    </div>
                    <div id="ai-assessment">
                        <h4>AI Assessment</h4>
                        <p>Quality Grade: <strong id="quality-grade"></strong> | Training Ready: <strong id="training-ready"></strong></p>
                        <span>Enhanced AI analysis completed</span>
                    </div>
                    <div class="button-container space-between">
                         <button class="button" onclick="alert('Data Cleaning feature coming soon!')">
                            <svg viewBox="0 0 24 24"><path d="M2.5 19h19v2h-19v-2zm19.57-9.36c-.21-.8-1.04-1.28-1.84-1.06L14.92 10H11v2h2.58l3.4-1.21 1.39 3.79-1.83.67-.55-1.5-3.41 1.22c-.67.24-1.39.06-1.86-.46l-3.3-3.67-1.42 1.42 3.3 3.67c1.03 1.14 2.65 1.48 4.04 1.03l4.27-1.52c.8-.28 1.28-1.11 1.06-1.91z"/></svg>
                            Start Data Cleaning
                         </button>
                         <button id="show-generation-settings-btn" class="button primary">
                            <svg viewBox="0 0 24 24"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
                            Generate More Data
                         </button>
                    </div>
                </div>
            </div>

            <!-- View 2: Generation Settings & Progress -->
            <div id="settings-view" class="view">
                 <div id="generation-form-section">
                    <h3 class="section-title">
                        <svg viewBox="0 0 24 24"><path d="M19.4 7.6c-.2-.5-.4-1-.7-1.3l-2.8-2.8c-.4-.4-.8-.6-1.3-.7C14.1 2.4 13.6 2 13 2h-2c-.6 0-1.1.4-1.6.8-.5.2-1 .4-1.3-.7L5.3 5.3c-.4.4-.6.8-.7 1.3-.4.5-.8 1-.8 1.6v2c0 .6.4 1.1.8 1.6.2.5.4 1 .7 1.3l2.8 2.8c.4.4.8.6 1.3.7.5.4 1 .8 1.6.8h2c.6 0 1.1-.4 1.6-.8.5-.2 1-.4 1.3-.7l2.8-2.8c.4-.4.6-.8-.7-1.3.4-.5.8-1 .8-1.6v-2c0-.6-.4-1.1-.8-1.6zM12 15c-1.7 0-3-1.3-3-3s1.3-3 3-3 3 1.3 3 3-1.3 3-3 3z"/></svg>
                        Generation Settings
                    </h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="target-record-count">Target Record Count</label>
                            <input type="number" id="target-record-count" value="100" min="10" max="1000">
                            <span>Number of new records to generate (10 - 1000)</span>
                        </div>
                        <div class="form-group">
                            <label for="generation-model">Generation Model</label>
                            <select id="generation-model">
                                <option value="accounts/fireworks/models/deepseek-v2-coder-instruct">DeepSeek Coder v2</option>
                                <option value="accounts/fireworks/models/qwen-72b-chat">Qwen 72B Chat</option>
                            </select>
                            <span>Model used for creating new data.</span>
                        </div>
                         <div class="form-group">
                            <label for="validation-model">Validation Model</label>
                            <select id="validation-model">
                                 <option value="accounts/fireworks/models/firellava-13b">FiRellava 13B (Fast)</option>
                                 <option value="accounts/fireworks/models/mixtral-8x7b-instruct">Mixtral 8x7B</option>
                            </select>
                            <span>Model used for quality scoring.</span>
                        </div>
                    </div>
                    <h3 class="section-title">
                        <svg viewBox="0 0 24 24"><path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18zM18 14H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
                        Main Prompt / Instruction
                    </h3>
                    <div class="form-group">
                        <textarea id="user-prompt" rows="4">You are an expert data creator. Based on the provided example, generate a new, unique, and high-quality data entry in the same JSON format. The new entry should be substantially different from the example but retain the same style, topic, and structure. Focus on technical accuracy and detail.</textarea>
                    </div>
                     <div class="button-container">
                        <button id="start-generation-btn" class="button primary large">
                             <svg viewBox="0 0 24 24"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
                            Start Generation
                        </button>
                    </div>
                </div>

                <div id="generation-progress-section" class="hidden">
                    <h3 class="section-title">
                        <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/></svg>
                        Generation In Progress...
                    </h3>
                    <div class="progress-bar-container">
                        <div id="progress-bar" style="width: 0%;"></div>
                    </div>
                    <div class="progress-details">
                        <p id="progress-text">Initializing...</p>
                        <p>Generated: <span id="generated-count">0</span> / <span id="target-count">0</span></p>
                    </div>
                </div>

                <div id="generation-complete-section" class="hidden">
                     <h3 class="section-title">
                        <svg viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                        Generation Complete!
                    </h3>
                    <p>Successfully generated <span id="final-generated-count">0</span> new records.</p>
                    <div class="button-container">
                        <a href="#" id="download-link" class="button primary" download>
                           <svg viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                           Download Generated Dataset
                        </a>
                        <button id="restart-btn" class="button">
                            <svg viewBox="0 0 24 24"><path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/></svg>
                            Start Over
                        </button>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>
