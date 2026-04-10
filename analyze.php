<?php
require 'vendor/autoload.php';

function extractTextFromPdf($filePath) {
    try {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($filePath);
        return $pdf->getText();
    } catch (Exception $e) {
        return "";
    }
}

function extractTextFromDocx($filePath) {
    $text = "";
    $zip = new ZipArchive;
    if ($zip->open($filePath) === true) {
        if (($index = $zip->locateName("word/document.xml")) !== false) {
            $data = $zip->getFromIndex($index);
            // Strip tags
            $text = strip_tags(str_replace("<w:p", "\n<w:p", $data));
        }
        $zip->close();
    }
    return $text;
}

function analyzeResume($text, $jobRole) {
    $textLower = strtolower($text);
    $jobRoleLower = strtolower($jobRole);
    
    $sections = ['skills', 'education', 'experience', 'projects'];
    $foundSections = [];
    $missingSections = [];
    
    foreach ($sections as $section) {
        if (strpos($textLower, $section) !== false || preg_match("/\b".preg_quote($section, '/')."\b/", $textLower)) {
            $foundSections[] = ucfirst($section);
        } else {
            $missingSections[] = ucfirst($section);
        }
    }
    
    // Keyword library mapping
    $roleKeywords = [
        'java' => ['java', 'spring', 'hibernate', 'maven', 'sql', 'rest', 'api', 'junit', 'git', 'oop', 'microservices'],
        'web' => ['html', 'css', 'javascript', 'react', 'node.js', 'express', 'vue', 'angular', 'bootstrap', 'tailwind', 'api', 'git'],
        'ml' => ['python', 'machine learning', 'deep learning', 'tensorflow', 'pytorch', 'scikit-learn', 'pandas', 'numpy', 'sql', 'statistics'],
        'data scientist' => ['python', 'r', 'sql', 'tableau', 'statistics', 'machine learning', 'data analysis', 'pandas'],
        'php' => ['php', 'laravel', 'symfony', 'mysql', 'html', 'css', 'javascript', 'api', 'git', 'composer'],
        'full stack' => ['html', 'css', 'javascript', 'react', 'node', 'express', 'mongodb', 'sql', 'git', 'api', 'docker'],
        'frontend' => ['html', 'css', 'javascript', 'react', 'vue', 'ui', 'ux', 'responsive design', 'sass', 'webpack'],
        'backend' => ['python', 'java', 'php', 'node', 'sql', 'nosql', 'api', 'docker', 'aws', 'rest']
    ];
    
    // Fallback if role not in predefined list
    $expectedSkills = [];
    foreach ($roleKeywords as $key => $keywords) {
        if (strpos($jobRoleLower, $key) !== false) {
            $expectedSkills = $keywords;
            break;
        }
    }
    
    // If we didn't find a direct mapping, just use a generic list based on word extraction
    if (empty($expectedSkills)) {
        // generic tech skills
        $expectedSkills = ['git', 'api', 'sql', 'communication', 'problem solving', 'agile', 'teamwork', 'leadership'];
    }
    
    $foundSkills = [];
    $missingSkills = [];
    
    foreach ($expectedSkills as $skill) {
        if (strpos($textLower, $skill) !== false) {
            $foundSkills[] = $skill;
        } else {
            $missingSkills[] = $skill;
        }
    }
    
    // Calculate Score Length
    $wordCount = str_word_count($textLower);
    $lengthFeedback = "";
    $lengthScore = 0;
    
    if ($wordCount < 150) {
        $lengthFeedback = "Resume is too short. Try elaborating on your experiences and adding measurable achievements.";
        $lengthScore = 10;
    } else if ($wordCount > 800) {
        $lengthFeedback = "Resume is very long. Consider keeping it concise and highly relevant to the job description to maintain the recruiter's attention.";
        $lengthScore = 20;
    } else {
        $lengthFeedback = "Resume length is optimal.";
        $lengthScore = 30; // Max 30 points for length
    }
    
    // Score based on sections
    $sectionScore = count($foundSections) * (30 / count($sections)); // Max 30 points for sections
    
    // Score based on skills match
    $skillScore = 0;
    if (count($expectedSkills) > 0) {
        $skillScore = (count($foundSkills) / count($expectedSkills)) * 40; // Max 40 points for skills
    } else {
        $skillScore = 40;
    }
    
    $totalScore = round($lengthScore + $sectionScore + $skillScore);
    if ($totalScore > 100) $totalScore = 100;
    
    // General Feedback
    $feedback = [];
    if (!empty($missingSections)) {
        $feedback[] = "Your resume is missing key sections: " . implode(', ', $missingSections) . ". Add them for better structure.";
    }
    
    if (count($foundSkills) < count($expectedSkills) * 0.5) {
        $feedback[] = "Add more technical skills relevant to the '$jobRole' role to pass Applicant Tracking Systems (ATS).";
    }
    
    // Simple heuristic for measurable achievements
    if (strpos($textLower, '%') === false && strpos($textLower, '$') === false && !preg_match('/\d+/', $textLower)) {
        $feedback[] = "Include measurable achievements (e.g., 'Increased sales by 20%'). Your current bullet points lack quantifiable data.";
    }
    
    if ($lengthFeedback !== "Resume length is optimal.") {
        $feedback[] = $lengthFeedback;
    }
    
    if (empty($feedback)) {
        $feedback[] = "Your resume looks solid and is well-tailored for this role! Ensure formatting is clean.";
    }

    return [
        'score' => $totalScore,
        'found_skills' => $foundSkills,
        'missing_skills' => $missingSkills,
        'missing_sections' => $missingSections,
        'feedback' => $feedback,
        'word_count' => $wordCount
    ];
}
?>
