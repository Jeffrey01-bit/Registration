<?php
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    $host = $env['DB_HOST'];
    $dbname = $env['DB_NAME'];
    $username = $env['DB_USER'];
    $password = $env['DB_PASS'];
} else {
    $host = 'localhost';
    $dbname = 'guvi_users';
    $username = 'root';
    $password = '';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    
    // Check if this is an API request
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        http_response_code(500);
        header('Content-Type: application/json');
        die(json_encode(['success' => false, 'message' => 'Database connection failed']));
    } else {
        // For web pages, show a user-friendly error
        http_response_code(500);
        die('<h2>Database Connection Error</h2><p>Please check if XAMPP/MySQL is running and try again.</p><p><a href="setup_database.php">Setup Database</a></p>');
    }
}
?>