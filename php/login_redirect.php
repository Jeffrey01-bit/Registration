<?php
session_start();

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    header('Location: ../login.html?error=empty');
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=guvi_users", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT * FROM guvi1users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        
        header('Location: ../profile_new.html');
        exit;
    } else {
        header('Location: ../login.html?error=invalid');
        exit;
    }
} catch (Exception $e) {
    header('Location: ../login.html?error=db');
    exit;
}
?>