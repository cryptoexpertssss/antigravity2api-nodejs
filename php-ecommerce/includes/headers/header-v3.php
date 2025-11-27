<?php
/**
 * Header V3: Vertical Sidebar Menu
 * Compact top header with vertical category menu
 */
?>

<header class="site-header header-v3">
    <!-- Top Compact Header -->
    <div class="header-compact bg-white shadow-sm py-2">
        <div class="container-fluid px-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    <button class="btn btn-primary" id="sidebarToggle">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                </div>
                <div class="col-auto">
                    <a href="/" class="logo text-decoration-none">
                        <h3 class="mb-0 fw-bold text-primary">WOODMART</h3>
                    </a>
                </div>
                <div class="col">
                    <div class="search-compact">
                        <form class="d-flex">
                            <input type="search" class="form-control" placeholder="Search products...">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="header-icons-compact d-flex gap-3">
                        <a href="/account" class="icon-btn">
                            <i class="bi bi-person fs-4"></i>
                        </a>
                        <a href="/wishlist" class="icon-btn position-relative">
                            <i class="bi bi-heart fs-4"></i>
                            <span class="badge bg-danger rounded-pill">7</span>
                        </a>
                        <a href="/cart" class="icon-btn position-relative">
                            <i class="bi bi-cart fs-4"></i>
                            <span class="badge bg-primary rounded-pill">3</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Vertical Sidebar Menu -->
<div class="sidebar-menu" id="sidebarMenu">
    <div class="sidebar-header d-flex justify-content-between align-items-center p-3 border-bottom">
        <h5 class="mb-0">Categories</h5>
        <button class="btn-close" id="sidebarClose"></button>
    </div>
    <div class="sidebar-body">
        <nav class="vertical-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="/">
                        <i class="bi bi-house-door"></i> Home
                    </a>
                </li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="#booksSubmenu" data-bs-toggle="collapse">
                        <i class="bi bi-book"></i> Books
                        <i class="bi bi-chevron-down float-end"></i>
                    </a>
                    <ul class="collapse submenu" id="booksSubmenu">
                        <li><a href="/books/fiction">Fiction</a></li>
                        <li><a href="/books/non-fiction">Non-Fiction</a></li>
                        <li><a href="/books/children">Children's Books</a></li>
                        <li><a href="/books/textbooks">Textbooks</a></li>
                    </ul>
                </li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="#electronicsSubmenu" data-bs-toggle="collapse">
                        <i class="bi bi-laptop"></i> Electronics
                        <i class="bi bi-chevron-down float-end"></i>
                    </a>
                    <ul class="collapse submenu" id="electronicsSubmenu">
                        <li><a href="/electronics/smartphones">Smartphones</a></li>
                        <li><a href="/electronics/laptops">Laptops</a></li>
                        <li><a href="/electronics/tablets">Tablets</a></li>
                        <li><a href="/electronics/accessories">Accessories</a></li>
                    </ul>
                </li>
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="#fashionSubmenu" data-bs-toggle="collapse">
                        <i class="bi bi-bag"></i> Fashion
                        <i class="bi bi-chevron-down float-end"></i>
                    </a>
                    <ul class="collapse submenu" id="fashionSubmenu">
                        <li><a href="/fashion/men">Men</a></li>
                        <li><a href="/fashion/women">Women</a></li>
                        <li><a href="/fashion/kids">Kids</a></li>
                        <li><a href="/fashion/accessories">Accessories</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/deals">
                        <i class="bi bi-tag"></i> Hot Deals
                        <span class="badge bg-danger float-end">Sale</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/new-arrivals">
                        <i class="bi bi-star"></i> New Arrivals
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/blog">
                        <i class="bi bi-newspaper"></i> Blog
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/contact">
                        <i class="bi bi-envelope"></i> Contact Us
                    </a>
                </li>
            </ul>
        </nav>
        <div class="sidebar-footer p-3 border-top">
            <h6>Need Help?</h6>
            <p class="small mb-2"><i class="bi bi-telephone"></i> +1 (234) 567-890</p>
            <p class="small mb-0"><i class="bi bi-envelope"></i> support@woodmart.com</p>
        </div>
    </div>
</div>

<!-- Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<style>
.header-v3 .search-compact form {
    max-width: 600px;
    margin: 0 auto;
}

.header-v3 .icon-btn {
    color: var(--text-color);
    text-decoration: none;
    position: relative;
}

.header-v3 .icon-btn .badge {
    position: absolute;
    top: -5px;
    right: -10px;
    font-size: 0.7rem;
}

.sidebar-menu {
    position: fixed;
    left: -320px;
    top: 0;
    width: 320px;
    height: 100vh;
    background: white;
    box-shadow: var(--shadow-xl);
    transition: left var(--transition-normal);
    z-index: 1050;
    overflow-y: auto;
}

.sidebar-menu.active {
    left: 0;
}

.sidebar-body {
    padding: 1rem 0;
}

.vertical-nav .nav-link {
    padding: 0.75rem 1.5rem;
    color: var(--text-color);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all var(--transition-fast);
}

.vertical-nav .nav-link:hover {
    background: var(--primary-color);
    color: white;
}

.vertical-nav .submenu {
    list-style: none;
    padding: 0;
    background: #f8f9fa;
}

.vertical-nav .submenu li a {
    display: block;
    padding: 0.5rem 1.5rem 0.5rem 4rem;
    color: var(--text-color);
    text-decoration: none;
    transition: background var(--transition-fast);
}

.vertical-nav .submenu li a:hover {
    background: #e9ecef;
}

.sidebar-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    visibility: hidden;
    transition: all var(--transition-normal);
    z-index: 1040;
}

.sidebar-overlay.active {
    opacity: 1;
    visibility: visible;
}
</style>

<script>
// Sidebar Toggle
document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('sidebarMenu').classList.add('active');
    document.getElementById('sidebarOverlay').classList.add('active');
});

document.getElementById('sidebarClose').addEventListener('click', closeSidebar);
document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);

function closeSidebar() {
    document.getElementById('sidebarMenu').classList.remove('active');
    document.getElementById('sidebarOverlay').classList.remove('active');
}
</script>
