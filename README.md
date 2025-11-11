# GUVI Internship - Registration & Login System

A complete user registration and login system built with HTML, CSS, JavaScript, PHP, and MySQL following GUVI internship requirements.

## 🎯 Problem Statement

Create a signup page where a user can register and a login page to log in with the details provided during registration. Successful login should redirect to a profile page which should contain additional details such as age, dob, contact, etc. The user can update these details.

**Flow:** Register → Login → Profile

## ✅ Requirements Fulfilled

### Technical Requirements
- ✅ **Separate Files**: HTML, JS, CSS, and PHP code in separate files
- ✅ **jQuery AJAX**: All backend interactions use jQuery AJAX (no form submission)
- ✅ **Bootstrap Design**: Responsive design using Bootstrap 5
- ✅ **MySQL Database**: User data stored in MySQL with prepared statements
- ✅ **LocalStorage Sessions**: Login sessions maintained using browser localStorage
- ✅ **Redis Integration**: Optional Redis support for session storage

### Security Features
- ✅ **Password Hashing**: Secure password hashing using PHP's password_hash()
- ✅ **Prepared Statements**: SQL injection prevention
- ✅ **Input Validation**: Client and server-side validation
- ✅ **XSS Prevention**: Output sanitization

## 🚀 Quick Start

### Prerequisites
- XAMPP/WAMP/LAMP server
- PHP 8.0+
- MySQL 5.7+
- Web browser

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Registration
   ```

2. **Setup Database**
   - Start XAMPP/WAMP
   - Visit `http://localhost/setup_database.php`
   - Database and tables will be created automatically

3. **Configure Environment**
   - Update `.env` file with your database credentials:
   ```
   DB_HOST=localhost
   DB_NAME=guvi_users
   DB_USER=root
   DB_PASS=your_password
   ```

4. **Access Application**
   - Main Page: `http://localhost/`
   - Register: `http://localhost/register.html`
   - Login: `http://localhost/login.html`

## 📁 Project Structure

```
Registration/
├── assets/css/
│   ├── style.css              # Main styling
│   └── profile.css            # Profile page styling
├── js/
│   ├── login.js               # Login functionality
│   ├── profile.js             # Profile management
│   └── register.js            # Registration functionality
├── php/
│   ├── db.php                 # MySQL connection
│   ├── login.php              # Login API
│   ├── register.php           # Registration API
│   ├── profile.php            # Profile management API
│   ├── photo.php              # Photo upload/retrieval API
│   ├── mongodb.php            # MongoDB helper class
│   ├── redis_session.php      # Session management
│   └── delete_account.php     # Account deletion
├── uploads/                   # User profile photos
├── profiles/                  # JSON fallback files
├── sessions/                  # Redis session files
├── vendor/                    # Composer dependencies
├── index.html                 # Landing page
├── login.html                 # Login form
├── profile.html               # User profile page
├── register.html              # Registration form
└── setup_database.php         # Database initialization
```

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (jQuery), Bootstrap 5
- **Backend**: PHP 8.2
- **Database**: MySQL/MariaDB
- **Server**: Apache
- **Optional**: Redis for session storage

## 🔧 Features

### User Registration
- Username and email validation
- Password hashing with bcrypt
- Duplicate user prevention
- AJAX form submission

### User Login
- Email/username authentication
- Session management with localStorage
- Secure password verification
- Automatic redirect to profile

### User Profile
- Complete profile management
- Photo upload functionality
- Editable user information
- Account deletion option
- Responsive design

### Additional Fields
- Personal: First Name, Last Name, Age, DOB
- Contact: Phone, Email, Address
- Location: City, State, Zip Code
- Professional: Occupation, Gender

## 🔒 Security Features

1. **Password Security**: Bcrypt hashing with salt
2. **SQL Injection Prevention**: Prepared statements
3. **XSS Protection**: Input/output sanitization
4. **Session Security**: LocalStorage with server validation
5. **File Upload Security**: Type validation and secure storage

## 📱 Responsive Design

- Mobile-first approach
- Bootstrap 5 grid system
- Cross-browser compatibility
- Modern UI with animations

## 🚀 Usage

1. **Register**: Create a new account with username, email, and password
2. **Login**: Sign in with your credentials
3. **Profile**: View and edit your profile information
4. **Upload**: Add a profile picture
5. **Update**: Modify your personal details
6. **Logout**: Securely end your session

## 🧪 Testing

Visit `http://localhost/setup_database.php` to ensure database setup is complete, then:

1. Register a new user account
2. Login with the created credentials
3. Access and update profile information
4. Test photo upload functionality
5. Verify responsive design on different devices

## 📋 Database Schema

```sql
CREATE TABLE guvi1users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    age INT,
    dob DATE,
    contact VARCHAR(15),
    gender ENUM('male', 'female', 'other'),
    occupation VARCHAR(100),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    zip_code VARCHAR(10),
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🤝 Contributing

This project was developed as part of the GUVI internship program. All requirements have been successfully implemented according to the provided specifications.

## 📄 License

This project is created for educational purposes as part of the GUVI internship program.

---

**Note**: This project fulfills all GUVI internship requirements including separate file structure, jQuery AJAX usage, Bootstrap design, MySQL integration, and localStorage session management.