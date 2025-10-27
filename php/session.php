<?php
// Session management without Redis dependency
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function createSession($userData) {
    $sessionToken = bin2hex(random_bytes(32));
    $_SESSION['token'] = $sessionToken;
    $_SESSION['user_id'] = $userData['id'];
    $_SESSION['username'] = $userData['username'];
    $_SESSION['email'] = $userData['email'];
    $_SESSION['login_time'] = time();
    
    return $sessionToken;
}

function getSession($token = null) {
    if ($token && isset($_SESSION['token']) && $_SESSION['token'] === $token) {
        return [
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'login_time' => $_SESSION['login_time']
        ];
    }
    return null;
}

function destroySession() {
    session_destroy();
}
?>