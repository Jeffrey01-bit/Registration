# 📧 Email OTP Setup Guide

## 🔧 Current Status
- **Development Mode**: Uses fallback OTP `123456`
- **Production Ready**: Configure email service for live deployment

## 🚀 For Development Testing

### Quick Test:
1. Enter any email address
2. Click "Send OTP"
3. Use OTP: `123456`
4. Click "Verify OTP"
5. ✅ Email verified

## 📨 For Production Deployment

### Option 1: Free Email Services

**SendGrid (Free tier: 100 emails/day)**
```php
// In send_otp.php, update sendViaAPI function:
$apiKey = 'your-sendgrid-api-key';
$url = 'https://api.sendgrid.com/v3/mail/send';
```

**Mailgun (Free tier: 5,000 emails/month)**
```php
$apiKey = 'your-mailgun-api-key';
$url = 'https://api.mailgun.net/v3/your-domain/messages';
```

**EmailJS (Free tier: 200 emails/month)**
```javascript
// Client-side email sending
emailjs.send('service_id', 'template_id', {
    to_email: email,
    otp_code: otp
});
```

### Option 2: SMTP Configuration

**Gmail SMTP**
```php
// Use PHPMailer with Gmail
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-app-password';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
```

### Option 3: Hosting Provider SMTP

Most hosting providers (cPanel, etc.) have built-in SMTP:
```php
// Usually works on shared hosting
mail($email, $subject, $message, $headers);
```

## 🔄 To Enable Real Email:

1. **Choose email service** from options above
2. **Get API key** or SMTP credentials
3. **Update `send_otp.php`** with your configuration
4. **Remove development bypass** in `verify_otp.php`
5. **Test with real email**

## 🧪 Current Development Features:
- ✅ OTP generation and storage
- ✅ Timer functionality (30 seconds)
- ✅ Resend OTP capability
- ✅ Visual feedback and validation
- ✅ Universal test OTP: `123456`

**For now, use OTP `123456` to test the complete registration flow!**