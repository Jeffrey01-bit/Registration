<?php
require_once 'vendor/autoload.php';

try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $db = $client->selectDatabase('guvi_users');
    $collection = $db->selectCollection('users');
    
    // Create test user
    $testUser = [
        'id' => 1,
        'username' => 'testuser',
        'email' => 'test@test.com',
        'password' => password_hash('test123', PASSWORD_DEFAULT),
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ];
    
    // Check if user exists
    $existing = $collection->findOne(['email' => 'test@test.com']);
    
    if (!$existing) {
        $result = $collection->insertOne($testUser);
        echo "✅ Test user created successfully<br>";
        echo "Email: test@test.com<br>";
        echo "Password: test123<br>";
    } else {
        echo "✅ Test user already exists<br>";
        echo "Email: test@test.com<br>";
        echo "Password: test123<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>