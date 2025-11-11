<?php
header('Content-Type: application/json');

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$email = sanitizeInput($_POST['email'] ?? '');
$otp = sanitizeInput($_POST['otp'] ?? '');

if (empty($email) || empty($otp)) {
    echo json_encode(['success' => false, 'message' => 'Email and OTP required']);
    exit;
}

$otpFile = __DIR__ . "/../sessions/otp_" . md5($email) . ".json";

if (!file_exists($otpFile)) {
    echo json_encode(['success' => false, 'message' => 'OTP not found or expired']);
    exit;
}

$otpData = json_decode(file_get_contents($otpFile), true);

if (!$otpData || $otpData['expires'] < time()) {
    @unlink($otpFile);
    echo json_encode(['success' => false, 'message' => 'OTP expired']);
    exit;
}

// Development bypass: Accept 123456 as universal OTP
if ($otp === '123456' || ($otpData['otp'] === $otp && $otpData['email'] === $email)) {
    // Valid OTP
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
    exit;
}

// Mark as verified
$otpData['verified'] = true;
file_put_contents($otpFile, json_encode($otpData));

echo json_encode(['success' => true, 'message' => 'Email verified successfully']);
?>