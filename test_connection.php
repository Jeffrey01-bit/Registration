<?php
// Test MongoDB and Redis connections
require_once 'php/mongodb.php';
require_once 'php/redis.php';

echo "<h2>Connection Tests</h2>";

// Test MongoDB
echo "<h3>MongoDB Test:</h3>";
$db = getMongoConnection();
if ($db) {
    echo "✅ MongoDB connected successfully<br>";
    
    // Test collections
    $collections = $db->listCollections();
    echo "Collections: ";
    foreach ($collections as $collection) {
        echo $collection->getName() . " ";
    }
    echo "<br>";
} else {
    echo "❌ MongoDB connection failed<br>";
}

// Test Redis
echo "<h3>Redis Test:</h3>";
try {
    $redis = getRedisConnection();
    if ($redis) {
        echo "✅ Redis connected successfully<br>";
        
        // Test set/get
        $testKey = "test_key";
        $testValue = "test_value";
        
        if ($redis->set($testKey, $testValue)) {
            echo "✅ Redis SET operation successful<br>";
            
            $getValue = $redis->get($testKey);
            if ($getValue === $testValue) {
                echo "✅ Redis GET operation successful<br>";
                $redis->del($testKey); // cleanup
            } else {
                echo "❌ Redis GET operation failed<br>";
            }
        } else {
            echo "❌ Redis SET operation failed<br>";
        }
    } else {
        echo "❌ Redis connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Redis error: " . $e->getMessage() . "<br>";
}
?>