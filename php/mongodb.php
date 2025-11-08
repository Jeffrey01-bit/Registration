<?php
class MongoProfile {
    private static $collection = null;
    
    private static function getCollection() {
        if (self::$collection === null) {
            if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
                throw new Exception("MongoDB composer dependencies not installed");
            }
            
            require_once __DIR__ . '/../vendor/autoload.php';
            
            if (!class_exists('MongoDB\Client')) {
                throw new Exception("MongoDB client not available");
            }
            
            $client = new MongoDB\Client("mongodb://localhost:27017");
            $client->listDatabases();
            self::$collection = $client->guvi_profiles->profiles;
        }
        return self::$collection;
    }
    
    private static function getFallbackFile($userId) {
        $dir = __DIR__ . '/../profiles/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir . "profile_$userId.json";
    }
    
    public static function save($userId, $profileData) {
        try {
            $collection = self::getCollection();
            $profileData['user_id'] = (int)$userId;
            $profileData['updated_at'] = new MongoDB\BSON\UTCDateTime();
            
            return $collection->replaceOne(
                ['user_id' => (int)$userId], 
                $profileData, 
                ['upsert' => true]
            );
        } catch (Exception $e) {
            // Fallback to file storage
            $file = self::getFallbackFile($userId);
            $profileData['user_id'] = (int)$userId;
            $profileData['updated_at'] = date('Y-m-d H:i:s');
            return file_put_contents($file, json_encode($profileData)) !== false;
        }
    }
    
    public static function get($userId) {
        try {
            $collection = self::getCollection();
            $result = $collection->findOne(['user_id' => (int)$userId]);
            return $result ? json_decode(json_encode($result), true) : null;
        } catch (Exception $e) {
            // Fallback to file storage
            $file = self::getFallbackFile($userId);
            if (file_exists($file)) {
                return json_decode(file_get_contents($file), true);
            }
            return null;
        }
    }
    
    public static function delete($userId) {
        try {
            $collection = self::getCollection();
            return $collection->deleteOne(['user_id' => (int)$userId]);
        } catch (Exception $e) {
            // Fallback to file storage
            $file = self::getFallbackFile($userId);
            if (file_exists($file)) {
                return unlink($file);
            }
            return true;
        }
    }
}
?>