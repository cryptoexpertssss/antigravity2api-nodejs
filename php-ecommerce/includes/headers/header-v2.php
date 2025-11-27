<?php
/**
 * Header V2: Standard E-commerce
 * Logo Left, Search Center, Account Right
 */
?>

<header class="site-header header-v2">
    <div class="header-wrapper bg-white shadow-sm">
        <div class="container-custom py-3">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-lg-2">
                    <a href="/" class="logo d-flex align-items-center text-decoration-none">
                        <div class="logo-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-shop fs-4"></i>
                        </div>
                        <div class="ms-2">
                            <h4 class="mb-0 fw-bold">WoodMart</h4>
                            <small class="text-muted">Since 2024</small>
                        </div>
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="col-lg-6">
                    <div class="search-wrapper">
                        <form class="search-form position-relative">
                            <select class="category-select" name="category">
                                <option value="">All Categories</option>
                                <option value="books">Books</option>
                                <option value="electronics">Electronics</option>
                                <option value="fashion">Fashion</option>
                            </select>
                            <input type="search" class="search-input" placeholder="Search for products, brands and more..." name="q">
                            <button type="submit" class="search-btn">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Icons -->
                <div class="col-lg-4">
                    <div class="header-actions d-flex justify-content-end gap-4">
                        <!-- Account -->
                        <div class="action-item dropdown">
                            <a href="#" class="action-link" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-3"></i>
                                <div class="action-text">
                                    <small class="d-block text-muted">Hello, Sign in</small>
                                    <strong>Account</strong>
                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/login">Login</a></li>
                                <li><a class="dropdown-item" href="/register">Register</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/orders">My Orders</a></li>
                                <li><a class="dropdown-item" href="/wishlist">Wishlist</a></li>
                            </ul>
                        </div>

                        <!-- Wishlist -->
                        <a href="/wishlist" class="action-item action-link">
                            <div class="position-relative">
                                <i class="bi bi-heart fs-3"></i>
                                <span class="badge bg-danger rounded-circle position-absolute" style="top: -5px; right: -10px; font-size: 0.7rem;">12</span>
                            </div>
                            <div class="action-text">
                                <small class="d-block text-muted">Favorites</small>
                                <strong>Wishlist</strong>
                            </div>
                        </a>

                        <!-- Cart -->
                        <a href="/cart" class="action-item action-link">
                            <div class="position-relative">
                                <i class="bi bi-cart3 fs-3"></i>
                                <span class="badge bg-primary rounded-circle position-absolute" style="top: -5px; right: -10px; font-size: 0.7rem;">8</span>
                            </div>
                            <div class="action-text">
                                <small class="d-block text-muted">Your Cart</small>
                                <strong>$459.99</strong>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--header-bg);">
        <div class="container-custom">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-bold" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-grid-3x3-gap"></i> All Categories
                        </a>
                        <ul class="dropdown-menu mega-menu">
                            <li><a class="dropdown-item" href="/books"><i class="bi bi-book"></i> Books</a></li>
                            <li><a class="dropdown-item" href="/electronics"><i class="bi bi-laptop"></i> Electronics</a></li>
                            <li><a class="dropdown-item" href="/fashion"><i class="bi bi-bag"></i> Fashion</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/shop">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="/deals"><span class="badge bg-danger">Hot Deals</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/new-arrivals">New Arrivals</a></li>
                    <li class="nav-item"><a class="nav-link" href="/blog">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="tel:+1234567890"><i class="bi bi-telephone"></i> +1 (234) 567-890</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<style>
.header-v2 .search-form {
    display: flex;
    background: #f5f5f5;
    border-radius: var(--radius-lg);
    overflow: hidden;
    border: 2px solid #ddd;
}

.header-v2 .category-select {
    border: none;
    background: white;
    padding: 0.75rem 1rem;
    border-right: 1px solid #ddd;
    min-width: 150px;
}

.header-v2 .search-input {
    flex: 1;
    border: none;
    padding: 0.75rem 1rem;
    background: transparent;
}

.header-v2 .search-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 600;
    cursor: pointer;
    transition: background var(--transition-fast);
}

.header-v2 .search-btn:hover {
    background: var(--primary-hover);
}

.header-v2 .action-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    color: inherit;
}

.header-v2 .action-text small {
    font-size: 0.75rem;
}

.header-v2 .action-text strong {
    font-size: 0.9rem;
}
</style>
