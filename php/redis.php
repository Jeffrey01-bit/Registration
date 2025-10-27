<?php
// Redis connection and session management using Predis

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

function getRedisConnection() {
    $host = $_ENV['REDIS_HOST'] ?? '127.0.0.1';
    $port = $_ENV['REDIS_PORT'] ?? 6379;
    
    try {
        $redis = new Predis\Client([
            'scheme' => 'tcp',
            'host'   => $host,
            'port'   => $port,
        ]);
        $redis->ping(); // Test connection
        return $redis;
    } catch (Exception $e) {
        error_log("Redis connection failed: " . $e->getMessage());
        return false;
    }
}

function setRedisSession($token, $sessionData, $expiry = null) {
    if ($expiry === null) {
        $expiry = $_ENV['SESSION_EXPIRY'] ?? 3600;
    }
    $redis = getRedisConnection();
    if (!$redis) return false;
    
    try {
        return $redis->setex("session:$token", $expiry, json_encode($sessionData));
    } catch (Exception $e) {
        error_log("Redis set session failed: " . $e->getMessage());
        return false;
    }
}

function getRedisSession($token) {
    $redis = getRedisConnection();
    if (!$redis) return false;
    
    try {
        $data = $redis->get("session:$token");
        return $data ? json_decode($data, true) : false;
    } catch (Exception $e) {
        error_log("Redis get session failed: " . $e->getMessage());
        return false;
    }
}

function deleteRedisSession($token) {
    $redis = getRedisConnection();
    if (!$redis) return false;
    
    try {
        return $redis->del("session:$token") > 0;
    } catch (Exception $e) {
        error_log("Redis delete session failed: " . $e->getMessage());
        return false;
    }
}
?>