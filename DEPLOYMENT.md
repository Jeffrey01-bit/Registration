# 🚀 Deployment Guide

## Prerequisites
- XAMPP/WAMP/LAMP server
- PHP 8.0+
- MySQL 5.7+
- MongoDB (optional)
- Redis (optional)

## Quick Setup

1. **Start Services**
   ```bash
   # Start Apache and MySQL in XAMPP
   ```

2. **Database Setup**
   ```
   Visit: http://localhost/setup_database.php
   ```

3. **Environment Configuration**
   ```
   Update .env file with your database credentials
   ```

4. **Access Application**
   ```
   Main: http://localhost/
   Register: http://localhost/register.html
   Login: http://localhost/login.html
   ```

## File Permissions
- `uploads/` - 755 (writable)
- `profiles/` - 755 (writable)  
- `sessions/` - 755 (writable)

## Production Checklist
- [ ] Update database credentials in .env
- [ ] Set proper file permissions
- [ ] Configure MongoDB connection
- [ ] Setup Redis server
- [ ] Enable HTTPS
- [ ] Configure backup strategy

## Troubleshooting
- Check Apache error logs
- Verify PHP extensions (mysqli, mongodb, redis)
- Ensure database connectivity
- Check file write permissions