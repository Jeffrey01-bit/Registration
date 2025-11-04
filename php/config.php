<?php
// ===== DATABASE CONFIGURATION =====

class DatabaseConfig {
    // Database connection parameters
    const DB_HOST = 'localhost';
    const DB_NAME = 'guvi_users';
    const DB_USER = 'root';
    const DB_PASS = 'Goldenjef8';
    const DB_TABLE = 'guvi1users';
    
    // Get PDO connection
    public static function getConnection() {
        try {
            $pdo = new PDO(
                "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME,
                self::DB_USER,
                self::DB_PASS
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
}

// ===== RESPONSE HELPER =====
class ResponseHelper {
    public static function success($data = null, $message = 'Success') {
        return json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
    }
    
    public static function error($message = 'Error occurred') {
        return json_encode([
            'status' => 'error',
            'message' => $message
        ]);
    }
}

// ===== SESSION HELPER =====
class SessionHelper {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function isLoggedIn() {
        self::start();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    public static function getUserId() {
        self::start();
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function setUser($userId, $username, $email) {
        self::start();
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['logged_in'] = true;
    }
    
    public static function destroy() {
        self::start();
        session_destroy();
    }
}
?>