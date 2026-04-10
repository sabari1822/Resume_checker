<?php
require 'db.php';
require 'analyze.php';

// Directory to store uploads temporarily
$targetDir = "uploads/";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES["resume_file"])) {
    $jobRole = $_POST['job_role'] ?? 'General';
    
    $fileName = basename($_FILES["resume_file"]["name"]);
    $fileTmpName = $_FILES["resume_file"]["tmp_name"];
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Check if valid type
    if($fileType !== "pdf" && $fileType !== "docx") {
        header("Location: index.php?error=Only PDF or DOCX files are allowed");
        exit;
    }
    
    // Create unique filename to prevent overwriting
    $newFileName = uniqid() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", $fileName);
    $targetFilePath = $targetDir . $newFileName;
    
    if (move_uploaded_file($fileTmpName, $targetFilePath)) {
        // Extract Text
        $extractedText = "";
        if ($fileType === "pdf") {
            $extractedText = extractTextFromPdf($targetFilePath);
        } else {
            $extractedText = extractTextFromDocx($targetFilePath);
        }
        
        if (trim($extractedText) === "") {
             // In case text extraction fails drastically
             header("Location: index.php?error=Could not extract text from the document. The file might be an image-only PDF.");
             @unlink($targetFilePath);
             exit;
        }
        
        // Analyze
        $analysisResult = analyzeResume($extractedText, $jobRole);
        
        // Save to DB
        try {
            $stmt = $pdo->prepare("INSERT INTO resumes (filename, job_role, extracted_text, score, missing_keywords, feedback) VALUES (?, ?, ?, ?, ?, ?)");
            $missingKeywordsJson = json_encode($analysisResult['missing_skills']);
            $feedbackJson = json_encode($analysisResult['feedback']);
            
            $stmt->execute([
                $fileName,
                $jobRole,
                substr($extractedText, 0, 10000), // Avoid crazy huge text saving
                $analysisResult['score'],
                $missingKeywordsJson,
                $feedbackJson
            ]);
        } catch (\PDOException $e) {
            // Ignore DB insertion error for local environments if DB not configured properly, proceed to show result
            error_log("Database Error: " . $e->getMessage());
        }
        
        // Optional: you can delete the file after analysis
        // @unlink($targetFilePath); 
        
        // Output the UI directly
        renderResult($analysisResult, $jobRole);
        
    } else {
        header("Location: index.php?error=Error uploading file.");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}

function renderResult($result, $role) {
    // Determine score class
    $scoreClass = "score-low";
    if ($result['score'] >= 75) {
        $scoreClass = "score-high";
    } else if ($result['score'] >= 50) {
        $scoreClass = "score-medium";
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Analysis Result - Resume Analyzer AI</title>
        <link rel="stylesheet" href="assets/css/style.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    </head>
    <body>
        <div class="background-shape shape-1"></div>
        <div class="background-shape shape-2"></div>
        
        <main class="container">
            <header class="app-header" style="margin-bottom: 1.5rem;">
                <h1>Analysis <span class="highlight">Result</span></h1>
                <p>Role targeted: <strong><?= htmlspecialchars($role) ?></strong></p>
            </header>

            <div class="upload-card" style="padding-top: 2rem;">
                <div class="score-container">
                    <div class="score-circle <?= $scoreClass ?>">
                        <?= htmlspecialchars($result['score']) ?>
                    </div>
                    <div class="score-label">Resume Match Score</div>
                </div>

                <div class="result-section">
                    <h3>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="highlight"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        Detected Skills
                    </h3>
                    <div class="pills-container">
                        <?php if (empty($result['found_skills'])): ?>
                            <span style="color: var(--text-muted); font-size: 0.9rem;">No specific keywords were found.</span>
                        <?php else: ?>
                            <?php foreach($result['found_skills'] as $skill): ?>
                                <span class="pill pill-success"><?= htmlspecialchars(ucfirst($skill)) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="result-section">
                    <h3>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--error)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                        Missing Keywords
                    </h3>
                    <div class="pills-container">
                        <?php if (empty($result['missing_skills'])): ?>
                            <span style="color: var(--success); font-size: 0.9rem;">You have all the expected key technical skills!</span>
                        <?php else: ?>
                            <?php foreach($result['missing_skills'] as $skill): ?>
                                <span class="pill pill-error"><?= htmlspecialchars(ucfirst($skill)) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="result-section" style="border: none; background: transparent; padding: 0;">
                    <h3 style="padding-left: 0.5rem; background: transparent; border-bottom: none;"><span class="highlight">►</span> Actionable Feedback</h3>
                    <ul class="feedback-list">
                        <?php foreach($result['feedback'] as $fb): ?>
                            <li><?= htmlspecialchars($fb) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <a href="index.php" class="btn btn-secondary" style="width: 100%; margin-top: 1rem;">
                    Analyze Another Resume
                </a>
            </div>
        </main>
    </body>
    </html>
    <?php
}
?>
