<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Analyzer AI</title>
    <meta name="description" content="Get instant AI-driven feedback on your resume based on your target job role.">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="background-shape shape-1"></div>
    <div class="background-shape shape-2"></div>
    
    <main class="container">
        <header class="app-header">
            <h1>Resume Analyzer<span class="highlight">.</span></h1>
            <p>Upload your resume and get instant AI-powered feedback customized for your target job role.</p>
        </header>

        <section class="upload-card">
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <form action="upload.php" method="POST" enctype="multipart/form-data" id="upload-form">
                <div class="form-group">
                    <label for="job_role">Target Job Role <span class="required">*</span></label>
                    <input type="text" id="job_role" name="job_role" placeholder="e.g. Full Stack Developer, Data Scientist..." required>
                </div>

                <div class="dropzone" id="dropzone">
                    <div class="dropzone-content">
                        <svg class="upload-icon" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        <p class="dropzone-text">Drag & drop your resume here</p>
                        <p class="dropzone-subtext">Supports PDF and DOCX</p>
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('resume_file').click()">Browse Files</button>
                    </div>
                    <input type="file" id="resume_file" name="resume_file" accept=".pdf, .docx, application/pdf, application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="hidden-input" required>
                </div>
                
                <div class="selected-file" id="selected-file" style="display: none;">
                    <div class="file-info">
                        <svg class="file-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                        <span id="file-name-display">resume.pdf</span>
                    </div>
                    <button type="button" class="remove-file" id="remove-file" aria-label="Remove file">×</button>
                </div>

                <button type="submit" class="btn btn-primary analyze-btn" id="analyze-btn">
                    <span>Analyze Resume</span>
                    <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </form>
        </section>
    </main>

    <script src="assets/js/script.js"></script>
</body>
</html>
