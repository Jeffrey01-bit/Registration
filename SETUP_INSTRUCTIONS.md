# Quick Setup Instructions

## 1. Start XAMPP
- Start Apache and MySQL services in XAMPP Control Panel

## 2. Setup Database
- Open your browser and go to: `http://localhost/Registration/setup_db.php`
- This will create the database and table automatically
- You should see "Setup completed!" message

## 3. Test the System
- Go to: `http://localhost/Registration/index.html`
- Click "Register" to create a new account
- Fill in the form and submit
- After successful registration, you'll be redirected to login
- Login with your credentials
- You'll be taken to the profile page

## 4. Troubleshooting
If you get "Something went wrong. Try again." error:

### Check Database Connection:
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Verify `guvi_users` database exists
3. Verify `users` table exists with columns: id, username, email, password, created_at

### Check File Permissions:
- Ensure all PHP files are in the correct location
- Make sure XAMPP can read the files

### Check Browser Console:
- Press F12 in browser
- Check Console tab for JavaScript errors
- Check Network tab for failed requests

## File Structure Should Be:
```
htdocs/Registration/
├── php/
│   ├── db.php
│   ├── register.php
│   ├── login.php
│   └── profile.php
├── js/
│   └── register.js
├── assets/css/
│   └── style.css
├── index.html
├── register.html
└── setup_db.php
```

## Common Issues Fixed:
- ✅ Database connection errors
- ✅ SQL injection vulnerabilities  
- ✅ Proper error handling
- ✅ File include issues
- ✅ Missing database setup