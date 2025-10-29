<?php
// Quick database setup script
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect to MySQL server
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS guvi_users";
    if ($conn->query($sql) === TRUE) {
        echo "Database 'guvi_users' created successfully or already exists.<br>";
    }
    
    // Select the database
    $conn->select_db('guvi_users');
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        first_name VARCHAR(50),
        last_name VARCHAR(50),
        age INT,
        dob DATE,
        gender ENUM('male', 'female', 'other'),
        contact VARCHAR(15),
        address TEXT,
        city VARCHAR(50),
        state VARCHAR(50),
        zip_code VARCHAR(10),
        occupation VARCHAR(100),
        company VARCHAR(100),
        session_token VARCHAR(64),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table 'users' created successfully or already exists.<br>";
    }
    
    // Create indexes
    $conn->query("CREATE INDEX IF NOT EXISTS idx_email ON users(email)");
    $conn->query("CREATE INDEX IF NOT EXISTS idx_username ON users(username)");
    $conn->query("CREATE INDEX IF NOT EXISTS idx_session_token ON users(session_token)");
    
    echo "Database setup completed successfully!<br>";
    echo "<a href='index.html'>Go to Registration System</a>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>