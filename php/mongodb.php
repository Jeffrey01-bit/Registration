<?php
// MongoDB connection and profile management

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
function loadEnv() {
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
}

loadEnv();

function getMongoConnection() {
    $host = $_ENV['MONGODB_HOST'] ?? 'localhost';
    $port = $_ENV['MONGODB_PORT'] ?? '27017';
    $database = $_ENV['MONGODB_DATABASE'] ?? 'guvi_users';
    
    try {
        $client = new MongoDB\Client("mongodb://{$host}:{$port}");
        return $client->selectDatabase($database);
    } catch (Exception $e) {
        error_log("MongoDB connection failed: " . $e->getMessage());
        return false;
    }
}

function getMongoProfile($userId) {
    $db = getMongoConnection();
    if (!$db) return [];
    
    try {
        $collection = $db->selectCollection('profiles');
        $profile = $collection->findOne(['user_id' => (int)$userId]);
        
        if ($profile) {
            $profileArray = $profile->toArray();
            unset($profileArray['_id']); // Remove MongoDB ObjectId
            return $profileArray;
        }
        
        return [];
    } catch (Exception $e) {
        error_log("MongoDB get profile failed: " . $e->getMessage());
        return [];
    }
}

function updateMongoProfile($userId, $profileData) {
    $db = getMongoConnection();
    if (!$db) return false;
    
    try {
        $collection = $db->selectCollection('profiles');
        
        $result = $collection->replaceOne(
            ['user_id' => (int)$userId],
            $profileData,
            ['upsert' => true]
        );
        
        return $result->getModifiedCount() > 0 || $result->getUpsertedCount() > 0;
    } catch (Exception $e) {
        error_log("MongoDB update profile failed: " . $e->getMessage());
        return false;
    }
}

function deleteMongoProfile($userId) {
    $db = getMongoConnection();
    if (!$db) return false;
    
    try {
        $collection = $db->selectCollection('profiles');
        $result = $collection->deleteOne(['user_id' => (int)$userId]);
        
        return $result->getDeletedCount() > 0;
    } catch (Exception $e) {
        error_log("MongoDB delete profile failed: " . $e->getMessage());
        return false;
    }
}
?>