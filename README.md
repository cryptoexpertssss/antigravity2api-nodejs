# GamingToday - WordPress-Style Gaming News CMS

Ek modern aur feature-rich gaming news aur casino review website, gamingtoday.com se inspire hokar banaya gaya hai. Complete backend control ke sath full-fledged Content Management System (CMS).

## 🎮 Features

### Public Website
- **Homepage** - Featured article hero section with latest news grid
- **Article Pages** - Rich content display with featured images, tags, and categories
- **Category Pages** - Filter articles by category
- **Casino Rankings** - Professional comparison tables with rankings, offers, and features
- **Responsive Design** - Mobile-friendly layout
- **Modern UI** - Clean, professional design with smooth animations

### Admin Panel
- **Dashboard** - Overview of all content statistics
- **Article Management** - Create, edit, delete articles with rich text editor
- **Category Management** - Organize content with custom categories
- **Casino Listings** - Manage casino rankings and offers
- **Image Upload** - Direct image upload functionality
- **Status Control** - Draft/Published status for articles

## 🚀 Quick Start

### Access Points
- **Website**: https://reviewhub-22.preview.emergentagent.com
- **Admin Panel**: https://reviewhub-22.preview.emergentagent.com/admin

### Sample Data Included
- ✅ 3 Categories (Casino, Sports Betting, Industry News)
- ✅ 5 Sample Articles
- ✅ 5 Casino Listings with rankings

## 📝 Admin Panel Usage

### Creating Articles
1. Admin → Articles → "Create Article"
2. Fill: Title, Slug, Category, Author, Content
3. Upload featured image (optional)
4. Add tags (optional)
5. Set status: Published/Draft

### Managing Categories  
1. Admin → Categories → "Create Category"
2. Enter: Name, Slug, Description

### Managing Casinos
1. Admin → Casinos → "Create Casino"
2. Fill: Name, Rank, Offer details
3. Add features (multiple)
4. Set rating and featured status

## 🛠️ Tech Stack
- **Backend**: FastAPI + MongoDB + Motor
- **Frontend**: React + Tailwind CSS + Shadcn UI
- **Editor**: React Quill (Rich text)
- **Database**: MongoDB

## 🔧 Maintenance

### Reseed Database
```bash
cd /app/backend && python seed_data.py
```

### Restart Services
```bash
sudo supervisorctl restart backend frontend
```

---

**✨ WordPress-style CMS with full backend control - Ready to use!**
