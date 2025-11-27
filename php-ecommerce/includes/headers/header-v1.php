<?php
/**
 * Header V1: Logo Center, Menu Split
 * Classic centered layout with navigation split on both sides
 */
?>

<header class="site-header header-v1">
    <!-- Top Bar -->
    <div class="header-top bg-dark text-white py-2">
        <div class="container-custom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="header-top-left d-flex gap-3">
                        <a href="tel:+1234567890" class="text-white text-decoration-none">
                            <i class="bi bi-telephone"></i> +1 (234) 567-890
                        </a>
                        <a href="mailto:info@woodmart.com" class="text-white text-decoration-none">
                            <i class="bi bi-envelope"></i> info@woodmart.com
                        </a>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="header-top-right d-flex justify-content-end gap-3">
                        <a href="#" class="text-white text-decoration-none">Track Order</a>
                        <a href="#" class="text-white text-decoration-none">Help</a>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-sm btn-link text-white dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-globe"></i> English
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">English</a></li>
                                <li><a class="dropdown-item" href="#">Spanish</a></li>
                                <li><a class="dropdown-item" href="#">French</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="header-main py-3 border-bottom">
        <div class="container-custom">
            <div class="row align-items-center">
                <!-- Left Menu -->
                <div class="col-lg-4">
                    <nav class="navbar navbar-expand-lg">
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="/shop">Shop</a></li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Categories</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/books">Books</a></li>
                                    <li><a class="dropdown-item" href="/electronics">Electronics</a></li>
                                    <li><a class="dropdown-item" href="/fashion">Fashion</a></li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>

                <!-- Center Logo -->
                <div class="col-lg-4 text-center">
                    <a href="/" class="logo-center">
                        <h2 class="mb-0 fw-bold">WOODMART</h2>
                        <small class="text-muted">Premium Store</small>
                    </a>
                </div>

                <!-- Right Menu -->
                <div class="col-lg-4">
                    <nav class="navbar navbar-expand-lg justify-content-end">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item"><a class="nav-link" href="/deals">Deals</a></li>
                            <li class="nav-item"><a class="nav-link" href="/blog">Blog</a></li>
                            <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Header with Icons -->
    <div class="header-bottom py-2 bg-light">
        <div class="container-custom">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="search-bar">
                        <form class="d-flex" role="search">
                            <input class="form-control" type="search" placeholder="Search products..." aria-label="Search">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="header-icons d-flex justify-content-end gap-3">
                        <a href="/account" class="icon-link" data-bs-toggle="tooltip" title="Account">
                            <i class="bi bi-person fs-4"></i>
                        </a>
                        <a href="/wishlist" class="icon-link position-relative" data-bs-toggle="tooltip" title="Wishlist">
                            <i class="bi bi-heart fs-4"></i>
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">3</span>
                        </a>
                        <a href="/cart" class="icon-link position-relative" data-bs-toggle="tooltip" title="Cart">
                            <i class="bi bi-bag fs-4"></i>
                            <span class="badge bg-primary position-absolute top-0 start-100 translate-middle">5</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
.header-v1 .logo-center {
    display: block;
    text-decoration: none;
    color: inherit;
}

.header-v1 .logo-center h2 {
    color: var(--primary-color);
    letter-spacing: 2px;
}

.header-v1 .search-bar form {
    border: 2px solid var(--primary-color);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.header-v1 .search-bar input {
    border: none;
    flex: 1;
}

.header-v1 .search-bar button {
    border-radius: 0;
}

.header-v1 .icon-link {
    color: var(--text-color);
    text-decoration: none;
    transition: color var(--transition-fast);
}

.header-v1 .icon-link:hover {
    color: var(--primary-color);
}
</style>
