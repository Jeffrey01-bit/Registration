<?php
class RedisSession {
    private static $redis = null;
    private static $fallback_dir = null;
    
    private static function getRedis() {
        if (self::$redis === null) {
            if (!extension_loaded('redis')) {
                throw new Exception("Redis extension not loaded");
            }
            
            self::$redis = new Redis();
            if (!self::$redis->connect('127.0.0.1', 6379, 2)) {
                throw new Exception("Cannot connect to Redis server");
            }
            self::$redis->ping();
        }
        return self::$redis;
    }
    
    private static function getFallbackDir() {
        if (self::$fallback_dir === null) {
            self::$fallback_dir = __DIR__ . '/../sessions/';
            if (!is_dir(self::$fallback_dir)) {
                mkdir(self::$fallback_dir, 0755, true);
            }
        }
        return self::$fallback_dir;
    }
    
    public static function store($token, $userData) {
        try {
            $redis = self::getRedis();
            return $redis->setex("session:$token", 3600, json_encode($userData));
        } catch (Exception $e) {
            // Fallback to file storage
            $file = self::getFallbackDir() . "session_$token.json";
            $data = [
                'data' => $userData,
                'expires' => time() + 3600
            ];
            return file_put_contents($file, json_encode($data)) !== false;
        }
    }
    
    public static function get($token) {
        try {
            $redis = self::getRedis();
            $data = $redis->get("session:$token");
            return $data ? json_decode($data, true) : null;
        } catch (Exception $e) {
            // Fallback to file storage
            $file = self::getFallbackDir() . "session_$token.json";
            if (file_exists($file)) {
                $content = json_decode(file_get_contents($file), true);
                if ($content && $content['expires'] > time()) {
                    return $content['data'];
                } else {
                    unlink($file);
                }
            }
            return null;
        }
    }
    
    public static function delete($token) {
        try {
            $redis = self::getRedis();
            return $redis->del("session:$token");
        } catch (Exception $e) {
            // Fallback to file storage
            $file = self::getFallbackDir() . "session_$token.json";
            if (file_exists($file)) {
                return unlink($file);
            }
            return true;
        }
    }
}
?>