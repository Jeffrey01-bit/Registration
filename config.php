<?php
// Production Configuration Handler
class Config {
    private static $config = null;
    
    public static function load() {
        if (self::$config === null) {
            // Try to load .env file
            $envFile = __DIR__ . '/.env';
            if (!file_exists($envFile)) {
                $envFile = __DIR__ . '/.env.production';
            }
            
            if (file_exists($envFile)) {
                $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos($line, '#') === 0) continue;
                    if (strpos($line, '=') !== false) {
                        list($key, $value) = explode('=', $line, 2);
                        $_ENV[trim($key)] = trim($value);
                    }
                }
            }
            
            // Set defaults for production
            self::$config = [
                'db_host' => $_ENV['DB_HOST'] ?? 'localhost',
                'db_name' => $_ENV['DB_NAME'] ?? 'guvi_users',
                'db_user' => $_ENV['DB_USER'] ?? 'root',
                'db_pass' => $_ENV['DB_PASS'] ?? '',
                'mongo_host' => $_ENV['MONGO_HOST'] ?? 'localhost',
                'mongo_port' => $_ENV['MONGO_PORT'] ?? 27017,
                'mongo_db' => $_ENV['MONGO_DB'] ?? 'guvi_profiles',
                'redis_host' => $_ENV['REDIS_HOST'] ?? 'localhost',
                'redis_port' => $_ENV['REDIS_PORT'] ?? 6379,
                'session_timeout' => $_ENV['SESSION_TIMEOUT'] ?? 3600,
                'upload_max_size' => $_ENV['UPLOAD_MAX_SIZE'] ?? 5242880,
                'site_url' => $_ENV['SITE_URL'] ?? '',
                'site_name' => $_ENV['SITE_NAME'] ?? 'GUVI Internship Project'
            ];
        }
        return self::$config;
    }
    
    public static function get($key) {
        $config = self::load();
        return $config[$key] ?? null;
    }
}
?>