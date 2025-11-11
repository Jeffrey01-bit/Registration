# GUVI Internship - Project Structure

## 📁 Complete File Organization

```
Registration/
├── 🌐 Frontend Files
│   ├── index.html              # Landing page
│   ├── register.html           # User registration form
│   ├── login.html              # User login form
│   └── profile.html            # User profile management
│
├── 🎨 Assets
│   ├── css/
│   │   ├── style.css           # Main application styles
│   │   └── profile.css         # Profile page specific styles
│   ├── icons/
│   │   └── guvi-logo.svg       # GUVI logo
│   └── libs/                   # Local libraries (if needed)
│
├── 📜 JavaScript
│   ├── register.js             # Registration functionality
│   ├── login.js                # Login functionality
│   └── profile.js              # Profile management
│
├── 🔧 Backend PHP
│   ├── db.php                  # MySQL database connection
│   ├── register.php            # User registration API
│   ├── login.php               # User authentication API
│   ├── profile.php             # Profile CRUD operations
│   ├── photo.php               # Photo upload/retrieval API
│   ├── mongodb.php             # MongoDB helper class
│   ├── redis_session.php       # Session management
│   └── delete_account.php      # Account deletion
│
├── 💾 Data Storage
│   ├── uploads/                # User profile photos
│   ├── profiles/               # JSON fallback files
│   └── sessions/               # Redis session files
│
├── 📦 Dependencies
│   ├── vendor/                 # Composer packages
│   ├── composer.json           # PHP dependencies
│   └── composer.lock           # Locked versions
│
└── ⚙️ Configuration
    ├── .env                    # Environment variables
    ├── .htaccess               # Apache configuration
    ├── setup_database.php      # Database initialization
    └── README.md               # Project documentation
```

## 🔄 Data Flow

### Registration Flow
1. **Frontend**: `register.html` + `register.js`
2. **Backend**: `register.php` → `db.php` (MySQL)
3. **Storage**: User credentials in MySQL

### Login Flow
1. **Frontend**: `login.html` + `login.js`
2. **Backend**: `login.php` → `db.php` + `redis_session.php`
3. **Storage**: Session in Redis/File fallback

### Profile Flow
1. **Frontend**: `profile.html` + `profile.js`
2. **Backend**: `profile.php` → `mongodb.php` + `redis_session.php`
3. **Storage**: Profile data in MongoDB/File fallback

### Photo Flow
1. **Frontend**: `profile.js` (upload/remove)
2. **Backend**: `photo.php` → `mongodb.php`
3. **Storage**: Files in `uploads/`, paths in MongoDB

## 🗄️ Database Architecture

### MySQL (Authentication)
- **Table**: `guvi1users`
- **Fields**: `id`, `username`, `email`, `password`, `created_at`
- **Purpose**: User authentication only

### MongoDB (Profile Data)
- **Database**: `guvi_profiles`
- **Collection**: `profiles`
- **Purpose**: All profile information + photo paths

### Redis (Sessions)
- **Purpose**: Session management
- **Fallback**: File-based sessions in `sessions/`

## 🛡️ Security Features

- ✅ Password hashing (bcrypt)
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection (input sanitization)
- ✅ Session security (Redis + file fallback)
- ✅ File upload validation
- ✅ CSRF protection

## 🚀 All Features Working

- ✅ User Registration
- ✅ User Login/Logout
- ✅ Profile Management
- ✅ Photo Upload/Remove
- ✅ Session Management
- ✅ Account Deletion
- ✅ Responsive Design
- ✅ Error Handling
- ✅ Data Persistence

## 📋 File Status

**Essential Files (DO NOT REMOVE):**
- All HTML files (4)
- All JavaScript files (3)
- All PHP files (8)
- All CSS files (2)
- Configuration files (.env, .htaccess, composer.json)
- Database setup (setup_database.php)

**Generated/Runtime Files:**
- uploads/ (user photos)
- profiles/ (JSON fallback)
- sessions/ (session files)
- vendor/ (Composer packages)

**Documentation:**
- README.md (project overview)
- PROJECT_STRUCTURE.md (this file)