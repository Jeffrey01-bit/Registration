<?php
header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Return test user data
    echo json_encode([
        'status' => 'success',
        'user' => [
            'id' => 1,
            'username' => 'testuser',
            'email' => 'test@test.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'age' => 25,
            'dob' => '1999-01-01',
            'gender' => 'male',
            'contact' => '+1234567890',
            'address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'zip_code' => '12345',
            'occupation' => 'Developer',
            'company' => 'Test Company'
        ]
    ]);
} else {
    echo json_encode(['status' => 'success', 'message' => 'Profile updated']);
}
?>