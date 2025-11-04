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
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}
?>