<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

try {
    $host = 'localhost';
    $dbname = 'guvi_users';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("UPDATE guvi1users SET 
        username = ?, first_name = ?, last_name = ?, age = ?, dob = ?, 
        contact = ?, gender = ?, occupation = ?, address = ?, city = ?, 
        state = ?, zip_code = ?, company = ?, updated_at = NOW() 
        WHERE id = ?");
    
    $stmt->execute([
        $_POST['username'],
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['age'],
        $_POST['dob'],
        $_POST['contact'],
        $_POST['gender'],
        $_POST['occupation'],
        $_POST['address'],
        $_POST['city'],
        $_POST['state'],
        $_POST['zipCode'],
        $_POST['company'],
        $_SESSION['user_id']
    ]);
    
    echo json_encode(['status' => 'success', 'message' => 'Profile updated']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Update failed']);
}
?>