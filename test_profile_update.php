<?php
session_start();
require_once 'php/db.php';

// Simulate a logged-in user (replace 23 with your actual user ID)
$_SESSION['user_id'] = 23;

// Test data
$firstName = 'Jeffrey';
$lastName = 'Test';
$age = 25;
$dob = '1999-01-01';
$gender = 'male';
$contact = '1234567890';
$address = '123 Test St';
$city = 'Test City';
$state = 'Test State';
$zipCode = '12345';
$occupation = 'Developer';
$company = 'Test Company';

try {
    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, age=?, dob=?, gender=?, contact=?, address=?, city=?, state=?, zip_code=?, occupation=?, company=? WHERE id=?");
    $stmt->bind_param("ssisssssssssi", $firstName, $lastName, $age, $dob, $gender, $contact, $address, $city, $state, $zipCode, $occupation, $company, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        echo "SUCCESS: Profile updated. Affected rows: " . $stmt->affected_rows . "<br>";
        
        // Verify the update
        $checkStmt = $conn->prepare("SELECT first_name, last_name, age, city FROM users WHERE id=?");
        $checkStmt->bind_param("i", $_SESSION['user_id']);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $user = $result->fetch_assoc();
        
        echo "Verification:<br>";
        echo "First Name: " . ($user['first_name'] ?? 'NULL') . "<br>";
        echo "Last Name: " . ($user['last_name'] ?? 'NULL') . "<br>";
        echo "Age: " . ($user['age'] ?? 'NULL') . "<br>";
        echo "City: " . ($user['city'] ?? 'NULL') . "<br>";
        
    } else {
        echo "FAILED: " . $stmt->error;
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>