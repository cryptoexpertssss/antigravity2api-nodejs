# WoodMart Clone - Premium E-commerce System
## Core PHP Multi-Niche Store with Dynamic Theme Engine

---

## 🚀 **Step 1 Complete: Database & Structure**

### ✅ **Completed Tasks:**

#### 1. **Professional Folder Structure Created:**
```
php-ecommerce/
├── app/
│   ├── controllers/      # Business logic controllers
│   ├── models/           # Database models
│   ├── views/            # View templates
│   ├── helpers/          # Helper classes (Database.php included)
│   └── middleware/       # Authentication & authorization
├── public/
│   ├── index.php         # Entry point
│   ├── assets/           # CSS, JS, Images
│   └── uploads/          # User uploaded files
├── themes/
│   └── default/          # Default theme with templates & assets
├── admin/
│   ├── controllers/      # Admin controllers
│   ├── views/            # Admin panel views
│   └── assets/           # Admin specific assets
├── config/
│   ├── database.php      # Database configuration
│   └── app.php           # Application settings
├── storage/
│   ├── logs/             # Application logs
│   ├── cache/            # Cache files
│   └── sessions/         # Session data
└── database/
    └── schema.sql        # Complete database schema
```

#### 2. **Complete SQL Database Schema:**

**Core Tables Created:**

✅ **`users`** - User management with roles (customer, admin, vendor)

✅ **`categories`** - Hierarchical category structure with parent-child relationships

✅ **`products`** - Multi-niche product support with:
   - **`json_attributes`** column for flexible data:
     - **Books:** ISBN, Author, Publisher, Pages, Language
     - **Fashion:** Material, Fit, Pattern, Occasion, Care Instructions
     - **Electronics:** Brand, Model, Warranty, Specifications (RAM, Storage)
   - Product types: `books`, `electronics`, `fashion`, `general`
   - Full-text search on name & description
   - Featured, New Arrival, On Sale flags

✅ **`variations`** - SKU-based variations for:
   - Size variations (S, M, L, XL, XXL)
   - Color variations (Red, Blue, Black, etc.)
   - Combined variations (Size + Color)
   - Individual stock tracking per variation
   - JSON attributes: `{"size": "L", "color": "Red"}`

✅ **`theme_settings`** - **Dynamic Theme Engine** with:
   - **Colors:** primary_color, secondary_color, text_color, background_color
   - **Typography:** font_family, font_size_base, heading_font
   - **Layout Options:**
     - `header_layout_id` (1-5 different header styles)
     - `product_card_style` (1-5 card designs)
     - `sidebar_position` (left/right)
     - `grid_columns` (2-6 columns)
   - **Features:**
     - `lazy_load_enabled` (boolean)
     - `quick_view_enabled` (boolean)
     - `wishlist_enabled` (boolean)
     - `ajax_cart_enabled` (boolean)
   - **Homepage Sections:** hero slider, categories, featured products, new arrivals

✅ **`mega_menu`** - Advanced navigation:
   - Hierarchical menu structure
   - Icon support
   - Badge support (NEW, HOT, SALE)
   - Mega menu with multiple columns
   - Custom HTML content support

✅ **`product_images`** - Multiple images per product with primary image flag

✅ **`cart`** - Shopping cart (supports both logged-in users & guests via session)

✅ **`orders` & `order_items`** - Complete order management with JSON address storage

✅ **`reviews`** - Product reviews with ratings (1-5 stars) and verification

✅ **`wishlist`** - Save favorite products

✅ **`coupons`** - Discount management (percentage, fixed, free shipping)

✅ **`settings`** - General site settings & SEO configuration

#### 3. **Secure PDO Database Connection Class:**

**File:** `/app/php-ecommerce/app/helpers/Database.php`

**Features:**
- ✅ Singleton pattern (single connection instance)
- ✅ Prepared statements (SQL injection protection)
- ✅ Error handling with try-catch
- ✅ Helper methods: `query()`, `fetch()`, `fetchAll()`, `insert()`, `update()`, `delete()`
- ✅ Transaction support: `beginTransaction()`, `commit()`, `rollback()`
- ✅ PDO with proper options (ERRMODE_EXCEPTION, FETCH_ASSOC)

**Usage Example:**
```php
<?php
require_once 'app/helpers/Database.php';

$db = Database::getInstance();

// Fetch all products
$products = $db->fetchAll("SELECT * FROM products WHERE status = :status", ['status' => 'active']);

// Insert product
$productId = $db->insert('products', [
    'sku' => 'BOOK-001',
    'name' => 'PHP Mastery',
    'price' => 29.99,
    'category_id' => 1,
    'product_type' => 'books',
    'json_attributes' => json_encode(['isbn' => '978-1234567890', 'author' => 'John Doe'])
]);
```

---

## 📊 **Database Schema Highlights:**

### **Dynamic Theme Engine - Key Settings:**
```sql
-- Admin can change these without coding:
primary_color: #ff6b6b
header_layout_id: 1-5 (5 different designs)
product_card_style: 1-5 (5 card styles)
lazy_load_enabled: true/false
grid_columns: 2-6
font_family: Poppins, sans-serif
```

### **Multi-Niche Product Example:**

**Book Product:**
```json
{
  "isbn": "978-3-16-148410-0",
  "author": "John Doe",
  "publisher": "ABC Books",
  "pages": 350,
  "language": "English"
}
```

**Fashion Product with Variations:**
```json
Product: {
  "material": "Cotton",
  "fit": "Slim",
  "pattern": "Solid"
}

Variations:
- SKU: TSHIRT-RED-L   {"size": "L", "color": "Red"}    Stock: 50
- SKU: TSHIRT-BLUE-M  {"size": "M", "color": "Blue"}   Stock: 30
- SKU: TSHIRT-BLACK-XL {"size": "XL", "color": "Black"} Stock: 20
```

---

## 🔧 **Next Steps for cPanel Deployment:**

1. **Upload files to cPanel:**
   - Upload entire `php-ecommerce/` folder to `public_html/`

2. **Create MySQL Database:**
   - Go to cPanel → MySQL Databases
   - Create database: `woodmart_clone`
   - Import: `database/schema.sql`
   - Update credentials in: `config/database.php`

3. **Set Permissions:**
   ```bash
   chmod 755 public/
   chmod 777 storage/logs/
   chmod 777 storage/cache/
   chmod 777 public/uploads/
   ```

4. **.htaccess Configuration:**
   - URL rewriting for clean URLs
   - Security headers
   - (Will be provided in next step)

---

## ✨ **Features Ready:**

✅ Multi-niche support (Books, Electronics, Fashion)  
✅ Dynamic theme customization without coding  
✅ JSON-based flexible product attributes  
✅ SKU-based variation management  
✅ Mega menu with badges  
✅ Cart & Checkout system  
✅ Review & Rating system  
✅ Wishlist functionality  
✅ Coupon management  
✅ Secure PDO database class  
✅ Full-text product search  
✅ SEO-friendly structure  

---

**Status:** ✅ **Step 1 Complete - Database & Structure Ready!**

**Next:** Step 2 will cover routing, controllers, and frontend theme implementation.
