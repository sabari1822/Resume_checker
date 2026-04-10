<?php
$host = '127.0.0.1';
$port = 3307;
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$charset = 'utf8mb4';
$dbname = 'resume_analyzer';

// Connect to MySQL server first (without database)
$dsn_setup = "mysql:host=$host;port=$port;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn_setup, $user, $pass, $options);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    
    // Now connect to the actual database
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Create the resumes table if it doesn't exist
    $tableQuery = "
    CREATE TABLE IF NOT EXISTS resumes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL,
        job_role VARCHAR(255) NOT NULL,
        extracted_text LONGTEXT,
        score INT,
        missing_keywords TEXT,
        feedback TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    $pdo->exec($tableQuery);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
