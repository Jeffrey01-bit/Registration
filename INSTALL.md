# Installation Instructions for GUVI Requirements

## Prerequisites
1. **XAMPP/WAMP** with PHP 8.0+
2. **Redis Server** (Windows: https://github.com/microsoftarchive/redis/releases)
3. **MongoDB** (Windows: https://www.mongodb.com/try/download/community)
4. **Composer** (https://getcomposer.org/)

## Setup Steps

### 1. Install Dependencies
```bash
cd "d:\VS Code\Guvi 1\Registration"
composer install
```

### 2. Start Services
- Start XAMPP (Apache + MySQL)
- Start Redis server: `redis-server`
- Start MongoDB: `mongod`

### 3. Install PHP Extensions
Add to php.ini:
```ini
extension=redis
extension=mongodb
```

### 4. Verify Installation
Visit: `http://localhost/setup_database.php`

## Architecture
- **MySQL**: User registration data (guvi1users table)
- **MongoDB**: User profile data (profiles collection)
- **Redis**: Session storage
- **LocalStorage**: Client-side session tokens

## Requirements Fulfilled
✅ Separate files (HTML, JS, CSS, PHP)
✅ jQuery AJAX (no form submission)
✅ Bootstrap design
✅ MySQL for registration data
✅ MongoDB for profile data
✅ Redis for session storage
✅ LocalStorage for client sessions