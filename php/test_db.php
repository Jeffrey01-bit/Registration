<?php
try {
    $host = 'localhost';
    $dbname = 'guvi_users';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check table structure
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Table Structure:</h3>";
    foreach ($columns as $column) {
        echo $column['Field'] . " - " . $column['Type'] . "<br>";
    }
    
    // Check existing data
    $stmt = $pdo->query("SELECT * FROM users LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Sample Data:</h3>";
    echo "<pre>" . print_r($users, true) . "</pre>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>