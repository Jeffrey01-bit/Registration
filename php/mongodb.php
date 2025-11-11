<?php
class MongoProfile {
    public static function save($userId, $profileData) {
        // Try MongoDB first
        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            $client = new MongoDB\Client("mongodb://localhost:27017");
            $collection = $client->guvi_profiles->profiles;
            
            $profileData['user_id'] = (int)$userId;
            $profileData['updated_at'] = new MongoDB\BSON\UTCDateTime();
            
            $result = $collection->replaceOne(
                ['user_id' => (int)$userId], 
                $profileData, 
                ['upsert' => true]
            );
            return ($result->getModifiedCount() > 0 || $result->getUpsertedCount() > 0);
        } catch (Exception $e) {
            // File fallback
            $file = __DIR__ . "/../profiles/profile_{$userId}.json";
            $profileData['user_id'] = (int)$userId;
            $profileData['updated_at'] = date('Y-m-d H:i:s');
            return file_put_contents($file, json_encode($profileData)) !== false;
        }
    }
    
    public static function get($userId) {
        // Try MongoDB first
        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            $client = new MongoDB\Client("mongodb://localhost:27017");
            $collection = $client->guvi_profiles->profiles;
            
            $result = $collection->findOne(['user_id' => (int)$userId]);
            if ($result) {
                return json_decode(json_encode($result), true);
            }
        } catch (Exception $e) {
            // File fallback
            $file = __DIR__ . "/../profiles/profile_{$userId}.json";
            if (file_exists($file)) {
                return json_decode(file_get_contents($file), true);
            }
        }
        return null;
    }
}
?>