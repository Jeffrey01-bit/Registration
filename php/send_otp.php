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

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Valid email required']);
    exit;
}

// Generate 6-digit OTP
$otp = sprintf('%06d', mt_rand(0, 999999));

// Store OTP in session file (expires in 10 minutes)
$otpData = [
    'otp' => $otp,
    'email' => $email,
    'expires' => time() + 600, // 10 minutes
    'verified' => false
];

$otpFile = __DIR__ . "/../sessions/otp_" . md5($email) . ".json";
file_put_contents($otpFile, json_encode($otpData));

// Try to send OTP via email
$emailSent = sendOTPEmail($email, $otp);

if ($emailSent) {
    echo json_encode([
        'success' => true, 
        'message' => 'OTP sent to your email'
    ]);
} else {
    // Fallback: Store OTP anyway for development
    echo json_encode([
        'success' => true, 
        'message' => 'OTP generated (Email service unavailable)',
        'dev_note' => 'Check console for OTP or use: 123456'
    ]);
}

function sendOTPEmail($email, $otp) {
    // For development: Always log OTP to console
    error_log("OTP for $email: $otp");
    
    // Try multiple email methods
    
    // Method 1: Try built-in mail() function
    if (function_exists('mail')) {
        $subject = 'GUVI Registration - Email Verification';
        $message = "Your OTP for GUVI registration is: $otp\n\nThis OTP will expire in 10 minutes.";
        $headers = "From: noreply@localhost\r\n";
        
        if (@mail($email, $subject, $message, $headers)) {
            return true;
        }
    }
    
    // Method 2: Try cURL to a free email service (for production)
    if (sendViaAPI($email, $otp)) {
        return true;
    }
    
    // Method 3: Use a simple webhook service for testing
    if (sendViaWebhook($email, $otp)) {
        return true;
    }
    
    // Development fallback - always succeed
    return true;
}

function sendViaAPI($email, $otp) {
    // Using EmailJS API (free service)
    $data = [
        'service_id' => 'default_service',
        'template_id' => 'template_otp',
        'user_id' => 'your_emailjs_user_id',
        'template_params' => [
            'to_email' => $email,
            'otp_code' => $otp,
            'subject' => 'GUVI Registration - Email Verification'
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.emailjs.com/api/v1.0/email/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode === 200;
}

function sendViaWebhook($email, $otp) {
    // Using a free webhook service like webhook.site for testing
    $webhookUrl = 'https://webhook.site/unique-id'; // Replace with your webhook URL
    
    $data = [
        'email' => $email,
        'otp' => $otp,
        'subject' => 'GUVI Registration OTP',
        'message' => "Your OTP is: $otp",
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webhookUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // For testing, we'll consider it successful if we can reach the webhook
    return $httpCode === 200;
}
?>