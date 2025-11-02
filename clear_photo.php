<?php
try {
    $host = 'localhost';
    $dbname = 'guvi_users';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("UPDATE guvi1users SET photo = NULL WHERE id = 1");
    $stmt->execute();
    
    echo "Photo path cleared for user ID 1";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>