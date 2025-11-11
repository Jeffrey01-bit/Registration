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
            self::$fallback_dir = realpath(__DIR__ . '/../sessions/') . '/';
            if (!is_dir(self::$fallback_dir)) {
                mkdir(self::$fallback_dir, 0755, true);
                self::$fallback_dir = realpath(self::$fallback_dir) . '/';
            }
        }
        return self::$fallback_dir;
    }
    
    private static function sanitizeToken($token) {
        // Only allow alphanumeric characters and ensure reasonable length
        if (!preg_match('/^[a-zA-Z0-9]{32,128}$/', $token)) {
            throw new Exception('Invalid token format');
        }
        return $token;
    }
    
    public static function store($token, $userData) {
        $token = self::sanitizeToken($token);
        try {
            $redis = self::getRedis();
            return $redis->setex("session:$token", 3600, json_encode($userData));
        } catch (Exception $e) {
            // Fallback to file storage
            $file = self::getFallbackDir() . "session_$token.json";
            // Ensure file is within the sessions directory
            if (strpos(realpath(dirname($file)), realpath(self::getFallbackDir())) !== 0) {
                throw new Exception('Invalid file path');
            }
            $data = [
                'data' => $userData,
                'expires' => time() + 3600
            ];
            return file_put_contents($file, json_encode($data), LOCK_EX) !== false;
        }
    }
    
    public static function get($token) {
        $token = self::sanitizeToken($token);
        try {
            $redis = self::getRedis();
            $data = $redis->get("session:$token");
            return $data ? json_decode($data, true) : null;
        } catch (Exception $e) {
            // Fallback to file storage
            $file = self::getFallbackDir() . "session_$token.json";
            // Ensure file is within the sessions directory
            if (strpos(realpath(dirname($file)), realpath(self::getFallbackDir())) !== 0) {
                return null;
            }
            if (file_exists($file)) {
                $content = json_decode(file_get_contents($file), true);
                if ($content && $content['expires'] > time()) {
                    return $content['data'];
                } else {
                    @unlink($file);
                }
            }
            return null;
        }
    }
    
    public static function delete($token) {
        $token = self::sanitizeToken($token);
        try {
            $redis = self::getRedis();
            return $redis->del("session:$token");
        } catch (Exception $e) {
            // Fallback to file storage
            $file = self::getFallbackDir() . "session_$token.json";
            // Ensure file is within the sessions directory
            if (strpos(realpath(dirname($file)), realpath(self::getFallbackDir())) !== 0) {
                return true;
            }
            if (file_exists($file)) {
                return @unlink($file);
            }
            return true;
        }
    }
}
?>