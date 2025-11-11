# 🚀 Live Deployment Guide

## 📋 Pre-Deployment Checklist

### 1. **Choose Hosting Provider**
**Recommended Free Options:**
- **InfinityFree** (PHP, MySQL, No MongoDB/Redis)
- **000webhost** (PHP, MySQL, No MongoDB/Redis)
- **Heroku** (Full stack support)
- **Railway** (Full stack support)

**Paid Options:**
- **DigitalOcean** ($5/month)
- **Linode** ($5/month)
- **AWS EC2** (Variable pricing)

### 2. **Prepare Files for Upload**

**Files to Upload:**
```
✅ All HTML files (4)
✅ assets/ folder (CSS, icons)
✅ js/ folder (3 JavaScript files)
✅ php/ folder (8 PHP files)
✅ .htaccess
✅ composer.json
✅ .env.production (rename to .env)
```

**Create These Folders on Server:**
```
✅ uploads/ (chmod 755)
✅ profiles/ (chmod 755)
✅ sessions/ (chmod 755)
```

## 🔧 Configuration Steps

### Step 1: Update .env File
```env
DB_HOST=localhost
DB_NAME=your_cpanel_db_name
DB_USER=your_cpanel_db_user
DB_PASS=your_cpanel_db_password
```

### Step 2: Database Setup
1. **Create MySQL Database** in cPanel/hosting panel
2. **Import Database** using phpMyAdmin
3. **Run setup_database.php** once on live server

### Step 3: File Permissions
```bash
chmod 755 uploads/
chmod 755 profiles/
chmod 755 sessions/
chmod 644 *.html
chmod 644 *.php
```

## 🌐 Deployment Options

### Option A: Basic PHP Hosting (Recommended for Demo)

**What Works:**
- ✅ Registration/Login
- ✅ Profile Management (File-based)
- ✅ Photo Upload
- ✅ Session Management (File-based)

**What Won't Work:**
- ❌ MongoDB (falls back to JSON files)
- ❌ Redis (falls back to file sessions)

**Steps:**
1. Upload all files via FTP/File Manager
2. Create database in cPanel
3. Update .env with database credentials
4. Visit yourdomain.com/setup_database.php
5. Test registration/login

### Option B: Full Stack Hosting (Complete Features)

**Providers:** Heroku, Railway, DigitalOcean

**What Works:**
- ✅ Everything including MongoDB & Redis

**Steps:**
1. Deploy to cloud platform
2. Add MongoDB Atlas (free tier)
3. Add Redis Cloud (free tier)
4. Update environment variables

## 📁 Upload Structure

```
public_html/
├── index.html
├── login.html
├── register.html
├── profile.html
├── assets/
├── js/
├── php/
├── uploads/ (create, chmod 755)
├── profiles/ (create, chmod 755)
├── sessions/ (create, chmod 755)
├── .htaccess
└── .env
```

## 🔍 Testing Checklist

After deployment:
- [ ] Visit main page loads
- [ ] Registration works
- [ ] Login works
- [ ] Profile page loads
- [ ] Photo upload works
- [ ] Profile editing works
- [ ] Account deletion works

## 🚨 Common Issues & Solutions

**Database Connection Failed:**
- Check .env credentials
- Verify database exists
- Run setup_database.php

**File Upload Errors:**
- Check uploads/ folder permissions (755)
- Verify PHP upload_max_filesize

**Session Issues:**
- Check sessions/ folder permissions (755)
- Clear browser localStorage

## 📞 Quick Deploy Commands

**For cPanel/FTP:**
```bash
# Zip project files
zip -r guvi-project.zip . -x "vendor/*" "node_modules/*"

# Upload and extract on server
# Update .env file
# Run setup_database.php
```

**For Git Deployment:**
```bash
git add .
git commit -m "Production ready"
git push origin main
```

## 🎯 Final Steps

1. **Upload files** to hosting
2. **Create database** 
3. **Update .env** with live credentials
4. **Set folder permissions**
5. **Run setup_database.php**
6. **Test all functionality**
7. **Share live URL**

Your project is now ready for live deployment! 🚀