# GamingToday CMS - cPanel Installation Guide

## 🎯 Features
- ✅ Articles Management (Create, Edit, Delete)
- ✅ Categories Management
- ✅ Casino Listings with Rankings
- ✅ User Reviews System (with moderation)
- ✅ Affiliate Links Management
- ✅ Advertisement System
- ✅ Secure Admin Login
- ✅ Image Upload
- ✅ SEO Features

## 📋 Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- cPanel hosting account
- 50MB disk space minimum

## 🚀 Installation Steps

### Step 1: Upload Files
1. Download all files
2. Login to your cPanel
3. Go to File Manager
4. Navigate to `public_html` folder
5. Upload all files here
6. Extract if uploaded as ZIP

### Step 2: Create Database
1. In cPanel, go to "MySQL Databases"
2. Create new database (e.g., `yourusername_gaming`)
3. Create new MySQL user with password
4. Add user to database with ALL PRIVILEGES
5. Note down:
   - Database name
   - Database user
   - Database password

### Step 3: Configure
1. Open `config.php` file
2. Update these lines:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database_name');     // Your database name
   define('DB_USER', 'your_database_user');     // Your database user
   define('DB_PASS', 'your_database_password'); // Your database password
   define('SITE_URL', 'http://yourdomain.com'); // Your website URL
   ```
3. Save the file

### Step 4: Run Installation
1. Visit: `http://yourdomain.com/install.php`
2. Fill in admin details:
   - Admin Username
   - Admin Email
   - Admin Full Name
   - Admin Password (strong password!)
3. Click "Install Now"
4. Wait for success message

### Step 5: Login
1. Visit: `http://yourdomain.com/login.php`
2. Enter your admin credentials
3. You'll be redirected to admin panel

### Step 6: Security (IMPORTANT!)
After successful installation:
1. Delete or rename `install.php` file
2. Make sure `uploads/` folder is writable (777 permissions)
3. Change SECURE_KEY in config.php to random string

## 🔐 Default Admin Access
After installation, use the credentials you set during setup.

## 📁 File Structure
```
/
├── config.php              # Database & site configuration
├── install.php            # Installation script (DELETE AFTER INSTALL)
├── login.php              # Admin login page
├── index.php              # Homepage
├── article.php            # Single article page
├── category.php           # Category archive
├── casinos.php            # Casino listings
├── casino-detail.php      # Single casino page
├── includes/              # Helper functions
│   ├── functions.php
│   └── header.php
├── admin/                 # Admin panel
│   ├── index.php         # Dashboard
│   ├── articles.php      # Manage articles
│   ├── categories.php    # Manage categories
│   ├── casinos.php       # Manage casinos
│   ├── reviews.php       # Manage reviews
│   ├── affiliate.php     # Manage affiliate links
│   └── ads.php           # Manage advertisements
└── uploads/               # Uploaded images (writable)
```

## 🎨 Features Included

### Admin Panel
- Dashboard with statistics
- CRUD operations for all content
- Image upload system
- User review moderation
- Affiliate link tracking
- Advertisement management

### Public Website
- Responsive design
- SEO friendly URLs
- Casino rankings
- User reviews with ratings
- Article system
- Category filtering

## ⚙️ Configuration Options

### In config.php you can change:
- `SITE_NAME` - Your site name
- `SITE_URL` - Your website URL
- `ADMIN_EMAIL` - Admin email address
- `DB_*` - Database credentials

## 🔒 Security Features
- Password hashing (bcrypt)
- SQL injection protection (PDO prepared statements)
- XSS protection (input sanitization)
- CSRF protection
- Session management
- Admin-only access control

## 📞 Support
For issues or questions:
- Check cPanel error logs
- Verify database connection
- Ensure proper file permissions
- Check PHP version compatibility

## 🎯 First Time Setup Checklist
- [ ] Files uploaded to cPanel
- [ ] Database created
- [ ] config.php updated with database credentials
- [ ] Visited install.php and completed installation
- [ ] Logged in successfully
- [ ] Deleted install.php
- [ ] uploads/ folder is writable (777)
- [ ] Changed SECURE_KEY in config.php

## 💡 Tips
1. Always backup database before updates
2. Use strong passwords
3. Keep admin credentials secure
4. Regular backups recommended
5. Monitor uploads folder size

## 🚀 Ready to Use!
After installation, your gaming news website is ready!
- Public site: http://yourdomain.com
- Admin panel: http://yourdomain.com/admin
- Login: http://yourdomain.com/login.php

Enjoy your new CMS! 🎉
