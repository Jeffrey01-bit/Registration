# GUVI Internship - Registration & Login System

A complete user registration and login system built with HTML, CSS, JavaScript, PHP, and MySQL.

## Features

- **User Registration**: Secure signup with validation
- **User Login**: Authentication with session management  
- **User Profile**: Display user information after login
- **Responsive Design**: Bootstrap-based UI
- **Security**: Password hashing, input validation, SQL injection prevention

## Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (jQuery), Bootstrap 5
- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Server**: Apache/Nginx (XAMPP/WAMP recommended for local development)

## Setup Instructions

### 1. Prerequisites
- XAMPP/WAMP/LAMP stack installed
- Web server running (Apache)
- MySQL/MariaDB running

### 2. Database Setup
1. Open phpMyAdmin or MySQL command line
2. Import the `database.sql` file to create the database and tables
3. Or run the SQL commands manually:
   ```sql
   CREATE DATABASE guvi_users;
   USE guvi_users;
   -- (copy contents from database.sql)
   ```

### 3. Environment Configuration
Create a `.env` file or set environment variables:
```
DB_HOST=localhost
DB_NAME=guvi_users
DB_USER=root
DB_PASS=your_password
```

### 4. File Placement
- Copy all files to your web server directory (e.g., `htdocs` for XAMPP)
- Ensure PHP has read/write permissions

### 5. Access the Application
- Open `http://localhost/Registration/index.html` in your browser
- Register a new account or login with existing credentials

## Project Structure

```
Registration/
├── assets/
│   └── css/
│       └── style.css
├── js/
│   ├── login.js
│   ├── profile.js
│   └── register.js
├── php/
│   ├── db.php
│   ├── login.php
│   ├── logout.php
│   ├── profile.php
│   └── register.php
├── index.html
├── login.html
├── profile.html
├── register.html
├── database.sql
└── README.md
```

## Flow

1. **Register** → **Login** → **Profile**
2. Users can register with username, email, and password
3. Successful login redirects to profile page
4. Profile displays user information with logout option

## Security Features

- Password hashing using PHP's `password_hash()`
- Prepared statements to prevent SQL injection
- Input validation and sanitization
- Session management for authentication
- Environment variables for database credentials

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+