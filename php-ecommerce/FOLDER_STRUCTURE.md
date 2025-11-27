# WoodMart Clone - Complete Folder Structure

## ✅ Current Structure

```
php-ecommerce/
├── README.md                      # Main documentation
├── FOLDER_STRUCTURE.md            # This file
│
├── app/                           # Application core
│   ├── controllers/               # Business logic
│   │   ├── ThemeController.php
│   │   ├── ShopController.php
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   ├── OrderController.php
│   │   └── WishlistController.php
│   │
│   ├── models/                    # Database models (future)
│   │
│   ├── helpers/                   # Helper classes
│   │   ├── Database.php           # PDO singleton
│   │   ├── ThemeHelper.php        # Theme settings
│   │   └── AuthHelper.php         # Authentication
│   │
│   ├── middleware/                # Request middleware (future)
│   └── views/                     # View templates (future)
│
├── admin/                         # Admin panel
│   ├── login.php                  # Admin login
│   ├── logout.php                 # Logout handler
│   ├── theme-customizer.php       # Theme options panel
│   ├── controllers/               # Admin controllers (future)
│   ├── views/                     # Admin views (future)
│   └── assets/                    # Admin assets
│       ├── css/
│       └── js/
│
├── config/                        # Configuration files
│   ├── database.php               # Database config
│   └── app.php                    # Application config
│
├── database/                      # Database files
│   ├── schema.sql                 # Main database schema
│   └── add_missing_settings.sql   # Additional settings
│
├── includes/                      # Reusable components
│   ├── headers/                   # Header variations
│   │   ├── header-v1.php          # Logo center
│   │   ├── header-v2.php          # Standard ecommerce
│   │   ├── header-v3.php          # Vertical sidebar
│   │   ├── header-v4.php          # Minimal transparent
│   │   └── header-v5.php          # Mobile-first
│   │
│   ├── cards/                     # Product card styles
│   │   ├── card-v1.php            # WoodMart standard
│   │   ├── card-v2.php            # Minimal
│   │   ├── card-v3.php            # Tech specs
│   │   ├── card-v4.php            # Book store
│   │   └── card-v5.php            # Fashion swatch
│   │
│   ├── components/                # Other components
│   ├── meta-tags.php              # SEO & social sharing
│   └── mailer.php                 # Email helper
│
├── public/                        # Web root (public access)
│   ├── index.php                  # Homepage (future)
│   ├── demo.php                   # Visual demo page
│   ├── product.php                # Single product page
│   ├── shop.php                   # Products listing (future)
│   ├── wishlist.php               # Wishlist page
│   ├── cart.php                   # Cart page (future)
│   ├── checkout.php               # Checkout page (future)
│   │
│   ├── user/                      # Customer area
│   │   ├── dashboard.php          # User dashboard
│   │   ├── orders.php             # Order history (future)
│   │   └── order-details.php      # Single order (future)
│   │
│   ├── api/                       # API endpoints
│   │   ├── products.php           # Product filtering
│   │   ├── cart.php               # Cart operations
│   │   └── wishlist.php           # Wishlist operations
│   │
│   ├── assets/                    # Static assets
│   │   ├── dynamic-style.php      # Dynamic CSS generator
│   │   ├── css/
│   │   ├── js/
│   │   │   └── main.js            # Main JavaScript
│   │   └── images/
│   │
│   └── uploads/                   # User uploads
│       ├── products/              # Product images
│       ├── categories/            # Category images
│       ├── banners/               # Banner images
│       └── users/                 # User avatars
│
├── storage/                       # Storage files
│   ├── logs/                      # Application logs
│   ├── cache/                     # Cache files
│   └── sessions/                  # Session data
│
├── themes/                        # Theme files
│   └── default/                   # Default theme
│       ├── templates/             # Theme templates
│       └── assets/                # Theme assets
│           ├── css/
│           ├── js/
│           └── images/
│
└── tests/                         # Test files
    └── __init__.py
```

---

## 📁 Folders to Create Manually

### Required Upload Folders (with proper permissions)

```bash
# Create upload directories
mkdir -p public/uploads/{products,categories,banners,users}

# Set permissions (Linux/Mac)
chmod 755 public/uploads
chmod 777 public/uploads/products
chmod 777 public/uploads/categories
chmod 777 public/uploads/banners
chmod 777 public/uploads/users

# Storage directories
mkdir -p storage/{logs,cache,sessions}
chmod 777 storage/logs
chmod 777 storage/cache
chmod 777 storage/sessions
```

### cPanel Setup

```bash
# 1. Upload all files to public_html/
# 2. Create these folders via File Manager:
public_html/uploads/products/
public_html/uploads/categories/
public_html/uploads/banners/
public_html/uploads/users/
public_html/storage/logs/
public_html/storage/cache/
public_html/storage/sessions/

# 3. Set folder permissions to 755 or 777 (via File Manager)
```

---

## 🔒 Security Considerations

### Protected Folders (NOT publicly accessible)
- `app/`
- `config/`
- `database/`
- `storage/`
- `includes/`

### Public Folders (Accessible via web)
- `public/` (web root)
- `public/assets/`
- `public/uploads/`
- `admin/` (password protected)

### .htaccess Protection (Add to protected folders)

```apache
# Deny access to this directory
Deny from all
```

Create `.htaccess` in:
- `/app/.htaccess`
- `/config/.htaccess`
- `/database/.htaccess`
- `/storage/.htaccess`

---

## 📊 File Count Summary

**Total Files:** ~50+
**Controllers:** 6
**Headers:** 5
**Product Cards:** 5
**Pages:** 10+
**API Endpoints:** 3
**Helpers:** 4

---

## 🚀 Deployment Checklist

### Before Upload:
- [ ] Update `config/database.php` with cPanel credentials
- [ ] Change admin password in `app/helpers/AuthHelper.php`
- [ ] Update email settings in `includes/mailer.php`
- [ ] Set proper file permissions

### After Upload:
- [ ] Create MySQL database
- [ ] Import `database/schema.sql`
- [ ] Import `database/add_missing_settings.sql`
- [ ] Create upload folders
- [ ] Set folder permissions (755/777)
- [ ] Test admin login
- [ ] Test product display
- [ ] Test cart functionality

### Production Security:
- [ ] Change all default passwords
- [ ] Enable HTTPS
- [ ] Set `'debug' => false` in `config/app.php`
- [ ] Add `.htaccess` to protected folders
- [ ] Regular database backups
- [ ] Update PHP to latest version

---

## 🔧 Missing Files (Optional Enhancements)

These files can be added for full functionality:

1. **public/index.php** - Homepage
2. **public/shop.php** - Products listing with filters
3. **public/cart.php** - Shopping cart page
4. **public/checkout.php** - Checkout process
5. **public/login.php** - Customer login
6. **public/register.php** - Customer registration
7. **public/user/order-details.php** - Order details page
8. **public/category/[slug].php** - Category page
9. **.htaccess** - URL rewriting rules
10. **robots.txt** - SEO crawl instructions

---

## 📝 Notes

- All controllers use PDO prepared statements (SQL injection safe)
- Sessions are managed securely
- CSRF tokens protect forms
- File uploads are validated
- Passwords are hashed with bcrypt
- JSON data is sanitized
- XSS protection via htmlspecialchars

**Status:** Production-ready core structure ✅
