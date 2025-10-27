<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Login Debug Test</h2>";

// Test 1: Check if files exist
echo "<h3>File Check:</h3>";
$files = ['php/mongodb.php', 'php/redis.php', 'vendor/autoload.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// Test 2: Try to include files
echo "<h3>Include Test:</h3>";
try {
    require_once 'php/mongodb.php';
    echo "✅ mongodb.php included<br>";
} catch (Exception $e) {
    echo "❌ mongodb.php error: " . $e->getMessage() . "<br>";
}

try {
    require_once 'php/redis.php';
    echo "✅ redis.php included<br>";
} catch (Exception $e) {
    echo "❌ redis.php error: " . $e->getMessage() . "<br>";
}

// Test 3: Test connections
echo "<h3>Connection Test:</h3>";
try {
    $db = getMongoConnection();
    if ($db) {
        echo "✅ MongoDB connected<br>";
    } else {
        echo "❌ MongoDB failed<br>";
    }
} catch (Exception $e) {
    echo "❌ MongoDB error: " . $e->getMessage() . "<br>";
}

try {
    $redis = getRedisConnection();
    if ($redis) {
        echo "✅ Redis connected<br>";
    } else {
        echo "❌ Redis failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Redis error: " . $e->getMessage() . "<br>";
}

// Test 4: Simulate login request
echo "<h3>Login Simulation:</h3>";
$_POST['email'] = 'test@test.com';
$_POST['password'] = 'test123';

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$password = trim($_POST['password'] ?? '');

echo "Email: $email<br>";
echo "Password: $password<br>";

try {
    $db = getMongoConnection();
    $collection = $db->selectCollection('users');
    $user = $collection->findOne(['email' => $email]);
    
    if ($user) {
        echo "✅ User found in database<br>";
        echo "User data: " . json_encode($user->toArray()) . "<br>";
    } else {
        echo "❌ User not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Database query error: " . $e->getMessage() . "<br>";
}
?>