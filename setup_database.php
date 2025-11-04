<?php
require_once 'php/db.php';

try {
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS guvi_users");
    $pdo->exec("USE guvi_users");
    
    // Create users table with all profile columns
    $sql = "CREATE TABLE IF NOT EXISTS guvi1users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        first_name VARCHAR(50),
        last_name VARCHAR(50),
        age INT,
        dob DATE,
        contact VARCHAR(15),
        gender ENUM('male', 'female', 'other'),
        occupation VARCHAR(100),
        address TEXT,
        city VARCHAR(50),
        state VARCHAR(50),
        zip_code VARCHAR(10),
        photo VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    
    // Also create the old table name for compatibility
    $sql2 = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        age INT,
        dob DATE,
        contact VARCHAR(15),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql2);
    
    echo "<h2>Database Setup Complete!</h2>";
    echo "<p>✓ Database 'guvi_users' created</p>";
    echo "<p>✓ 'guvi1users' table created with full profile structure</p>";
    echo "<p>✓ 'users' table created for compatibility</p>";
    echo "<p><a href='index.html'>Go to Main Page</a></p>";
    
} catch(PDOException $e) {
    echo "<h2>Database Setup Failed!</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>