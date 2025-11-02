<?php
// ===== USER REGISTRATION =====
require_once 'config.php';

header('Content-Type: application/json');

class UserRegistration {
    private $pdo;
    
    public function __construct() {
        $this->pdo = DatabaseConfig::getConnection();
    }
    
    public function register() {
        // Validate request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ResponseHelper::error('Invalid request method');
            return;
        }
        
        // Get and validate input
        $input = $this->getInput();
        if (!$input) return;
        
        // Check for existing users
        if ($this->userExists($input['email'], $input['username'])) return;
        
        // Create new user
        $this->createUser($input);
    }
    
    private function getInput() {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        // Validate required fields
        if (empty($username) || empty($email) || empty($password)) {
            echo ResponseHelper::error('All fields are required');
            return false;
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo ResponseHelper::error('Invalid email format');
            return false;
        }
        
        return compact('username', 'email', 'password');
    }
    
    private function userExists($email, $username) {
        // Check email
        $stmt = $this->pdo->prepare("SELECT id FROM " . DatabaseConfig::DB_TABLE . " WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo ResponseHelper::error('Email already registered');
            return true;
        }
        
        // Check username
        $stmt = $this->pdo->prepare("SELECT id FROM " . DatabaseConfig::DB_TABLE . " WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            echo ResponseHelper::error('Username already taken');
            return true;
        }
        
        return false;
    }
    
    private function createUser($input) {
        try {
            $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare(
                "INSERT INTO " . DatabaseConfig::DB_TABLE . " (username, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())"
            );
            $stmt->execute([$input['username'], $input['email'], $hashedPassword]);
            
            echo ResponseHelper::success(null, 'Registration successful');
        } catch (Exception $e) {
            echo ResponseHelper::error('Registration failed');
        }
    }
}

// Execute registration
$registration = new UserRegistration();
$registration->register();
?>