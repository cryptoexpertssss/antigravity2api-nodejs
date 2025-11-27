<?php
/**
 * Header V5: Mobile-First Bottom Navigation
 * Modern bottom tab bar design
 */
?>

<header class="site-header header-v5">
    <!-- Top Bar (Desktop) -->
    <div class="header-top-v5 d-none d-lg-block bg-dark text-white py-2">
        <div class="container-custom">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="d-flex gap-3">
                        <a href="tel:+1234567890" class="text-white text-decoration-none small">
                            <i class="bi bi-telephone"></i> +1 (234) 567-890
                        </a>
                        <a href="/track-order" class="text-white text-decoration-none small">
                            <i class="bi bi-truck"></i> Track Order
                        </a>
                    </div>
                </div>
                <div class="col-6 text-end">
                    <div class="d-flex justify-content-end gap-3">
                        <a href="/account" class="text-white text-decoration-none small">Sign In</a>
                        <span class="text-white">|</span>
                        <a href="/register" class="text-white text-decoration-none small">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="header-main-v5 bg-white py-3 shadow-sm">
        <div class="container-custom">
            <div class="row align-items-center">
                <div class="col-3 col-lg-2">
                    <a href="/" class="logo">
                        <h4 class="mb-0 fw-bold text-primary">WOODMART</h4>
                    </a>
                </div>
                <div class="col-6 col-lg-8">
                    <div class="search-bar-v5">
                        <form class="d-flex">
                            <button type="button" class="btn-category dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-grid-3x3-gap"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/books">Books</a></li>
                                <li><a class="dropdown-item" href="/electronics">Electronics</a></li>
                                <li><a class="dropdown-item" href="/fashion">Fashion</a></li>
                            </ul>
                            <input type="search" class="form-control border-0" placeholder="Search...">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-3 col-lg-2">
                    <div class="d-none d-lg-flex justify-content-end gap-3">
                        <a href="/wishlist" class="icon-link position-relative">
                            <i class="bi bi-heart fs-4"></i>
                            <span class="badge bg-danger rounded-pill position-absolute top-0 start-100">5</span>
                        </a>
                        <a href="/cart" class="icon-link position-relative">
                            <i class="bi bi-cart3 fs-4"></i>
                            <span class="badge bg-primary rounded-pill position-absolute top-0 start-100">3</span>
                        </a>
                    </div>
                    <button class="btn btn-link d-lg-none" type="button">
                        <i class="bi bi-three-dots-vertical fs-4"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Navigation -->
    <nav class="navbar-v5 d-none d-lg-block border-top">
        <div class="container-custom">
            <ul class="nav-v5">
                <li><a href="/">Home</a></li>
                <li class="has-mega">
                    <a href="/shop">Shop <i class="bi bi-chevron-down"></i></a>
                </li>
                <li><a href="/deals" class="text-danger fw-bold">Hot Deals 🔥</a></li>
                <li><a href="/new-arrivals">New Arrivals</a></li>
                <li><a href="/bestsellers">Bestsellers</a></li>
                <li><a href="/blog">Blog</a></li>
                <li><a href="/contact">Contact</a></li>
            </ul>
        </div>
    </nav>
</header>

<!-- Bottom Navigation (Mobile) -->
<nav class="bottom-nav d-lg-none">
    <a href="/" class="bottom-nav-item active">
        <i class="bi bi-house-door"></i>
        <span>Home</span>
    </a>
    <a href="/categories" class="bottom-nav-item">
        <i class="bi bi-grid-3x3-gap"></i>
        <span>Categories</span>
    </a>
    <a href="/cart" class="bottom-nav-item">
        <div class="position-relative">
            <i class="bi bi-cart3"></i>
            <span class="badge bg-primary rounded-pill position-absolute" style="top: -8px; right: -12px; font-size: 0.6rem;">3</span>
        </div>
        <span>Cart</span>
    </a>
    <a href="/wishlist" class="bottom-nav-item">
        <div class="position-relative">
            <i class="bi bi-heart"></i>
            <span class="badge bg-danger rounded-pill position-absolute" style="top: -8px; right: -12px; font-size: 0.6rem;">5</span>
        </div>
        <span>Wishlist</span>
    </a>
    <a href="/account" class="bottom-nav-item">
        <i class="bi bi-person"></i>
        <span>Account</span>
    </a>
</nav>

<style>
.header-v5 .search-bar-v5 form {
    display: flex;
    border: 2px solid var(--primary-color);
    border-radius: 25px;
    overflow: hidden;
}

.header-v5 .btn-category {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0;
}

.header-v5 .search-bar-v5 input {
    flex: 1;
}

.header-v5 .search-bar-v5 button[type="submit"] {
    border-radius: 0;
}

.header-v5 .icon-link {
    color: var(--text-color);
    text-decoration: none;
}

.navbar-v5 {
    background: #f8f9fa;
    padding: 0.75rem 0;
}

.nav-v5 {
    display: flex;
    list-style: none;
    gap: 2rem;
    margin: 0;
    padding: 0;
    align-items: center;
}

.nav-v5 li a {
    color: var(--text-color);
    text-decoration: none;
    font-weight: 500;
    transition: color var(--transition-fast);
}

.nav-v5 li a:hover {
    color: var(--primary-color);
}

/* Bottom Navigation */
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-around;
    padding: 0.5rem 0;
    z-index: 1000;
}

.bottom-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    color: #666;
    text-decoration: none;
    flex: 1;
    padding: 0.5rem;
    transition: all var(--transition-fast);
}

.bottom-nav-item i {
    font-size: 1.5rem;
}

.bottom-nav-item span {
    font-size: 0.7rem;
}

.bottom-nav-item.active {
    color: var(--primary-color);
}

.bottom-nav-item:hover {
    color: var(--primary-color);
    transform: translateY(-2px);
}

/* Add padding to body for bottom nav on mobile */
@media (max-width: 991px) {
    body {
        padding-bottom: 70px;
    }
}
</style>

<script>
// Bottom nav active state
document.querySelectorAll('.bottom-nav-item').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('.bottom-nav-item').forEach(i => i.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>
