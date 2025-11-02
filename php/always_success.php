<?php
header('Content-Type: application/json');

echo json_encode([
    'status' => 'success',
    'token' => 'test_token_123',
    'user' => [
        'id' => 1,
        'username' => 'testuser',
        'email' => 'test@test.com'
    ]
]);
?>