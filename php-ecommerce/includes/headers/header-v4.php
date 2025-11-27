<?php
/**
 * Header V4: Minimal Transparent
 * Perfect for hero sliders and full-width backgrounds
 */
?>

<header class="site-header header-v4">
    <div class="header-transparent">
        <div class="container-custom py-3">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-3">
                    <a href="/" class="logo-minimal">
                        <h3 class="mb-0 fw-bold text-white">WOODMART</h3>
                    </a>
                </div>

                <!-- Center Navigation -->
                <div class="col-6">
                    <nav class="nav-minimal justify-content-center">
                        <a href="/" class="nav-link-minimal">Home</a>
                        <a href="/shop" class="nav-link-minimal">Shop</a>
                        <a href="/categories" class="nav-link-minimal">Categories</a>
                        <a href="/deals" class="nav-link-minimal">
                            Deals <span class="badge-hot">Hot</span>
                        </a>
                        <a href="/about" class="nav-link-minimal">About</a>
                    </nav>
                </div>

                <!-- Right Icons -->
                <div class="col-3">
                    <div class="icons-minimal d-flex justify-content-end gap-3">
                        <button class="icon-btn-minimal" data-bs-toggle="modal" data-bs-target="#searchModal">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="/account" class="icon-btn-minimal">
                            <i class="bi bi-person"></i>
                        </a>
                        <a href="/wishlist" class="icon-btn-minimal position-relative">
                            <i class="bi bi-heart"></i>
                            <span class="badge-count">3</span>
                        </a>
                        <a href="/cart" class="icon-btn-minimal position-relative">
                            <i class="bi bi-bag"></i>
                            <span class="badge-count">2</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Search Modal -->
<div class="modal fade" id="searchModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-0">
                <h5 class="modal-title">Search Products</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center">
                <div class="w-100" style="max-width: 800px;">
                    <form>
                        <div class="input-group input-group-lg">
                            <input type="search" class="form-control form-control-lg" placeholder="What are you looking for?" autofocus>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </form>
                    <div class="mt-4">
                        <h6 class="text-muted">Popular Searches:</h6>
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <a href="#" class="badge bg-secondary p-2">iPhone 15</a>
                            <a href="#" class="badge bg-secondary p-2">Winter Jacket</a>
                            <a href="#" class="badge bg-secondary p-2">Best Sellers</a>
                            <a href="#" class="badge bg-secondary p-2">Books</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.header-v4 {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background: linear-gradient(to bottom, rgba(0,0,0,0.7), transparent);
}

.header-v4 .logo-minimal {
    text-decoration: none;
    transition: transform var(--transition-fast);
    display: inline-block;
}

.header-v4 .logo-minimal:hover {
    transform: scale(1.05);
}

.header-v4 .nav-minimal {
    display: flex;
    gap: 2rem;
}

.header-v4 .nav-link-minimal {
    color: white;
    text-decoration: none;
    font-weight: 500;
    position: relative;
    transition: all var(--transition-fast);
    padding: 0.5rem 0;
}

.header-v4 .nav-link-minimal::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--primary-color);
    transition: width var(--transition-fast);
}

.header-v4 .nav-link-minimal:hover::after {
    width: 100%;
}

.header-v4 .badge-hot {
    background: var(--primary-color);
    color: white;
    padding: 0.125rem 0.5rem;
    border-radius: 10px;
    font-size: 0.7rem;
    margin-left: 0.25rem;
}

.header-v4 .icon-btn-minimal {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    color: white;
    text-decoration: none;
    transition: all var(--transition-fast);
    backdrop-filter: blur(10px);
}

.header-v4 .icon-btn-minimal:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

.header-v4 .badge-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background: var(--primary-color);
    color: white;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    font-weight: bold;
}

/* Sticky behavior on scroll */
.header-v4.scrolled {
    position: fixed;
    background: var(--header-bg);
    box-shadow: var(--shadow-md);
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        transform: translateY(-100%);
    }
    to {
        transform: translateY(0);
    }
}
</style>

<script>
// Sticky header on scroll
window.addEventListener('scroll', function() {
    const header = document.querySelector('.header-v4');
    if (window.scrollY > 100) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});
</script>
