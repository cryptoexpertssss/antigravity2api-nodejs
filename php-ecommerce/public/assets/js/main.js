/**
 * WoodMart Clone - Main JavaScript
 * Advanced AJAX functionality for filters, cart, and product pages
 */

(function() {
    'use strict';

    // ==========================================
    // CONFIGURATION
    // ==========================================
    
    const CONFIG = {
        API_BASE: '/api',
        CART_API: '/api/cart.php',
        PRODUCTS_API: '/api/products.php',
        DEBOUNCE_DELAY: 300
    };

    // ==========================================
    // INITIALIZATION
    // ==========================================
    
    document.addEventListener('DOMContentLoaded', function() {
        initTooltips();
        initLazyLoading();
        initAjaxCart();
        initMiniCart();
        initWishlist();
        initQuickView();
        initAjaxFilters();
        initProductVariations();
        initSearchFilters();
        initMobileMenu();
        loadCartCount();
    });

    // ==========================================
    // BOOTSTRAP TOOLTIPS
    // ==========================================
    
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // ==========================================
    // LAZY LOADING IMAGES
    // ==========================================
    
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src || img.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[loading="lazy"]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    // ==========================================
    // AJAX ADD TO CART
    // ==========================================
    
    function initAjaxCart() {
        document.addEventListener('click', function(e) {
            const addToCartBtn = e.target.closest('.btn-add-to-cart, .btn-add-cart-book, .btn-add-cart-fashion, .btn-primary-tech');
            
            if (addToCartBtn) {
                e.preventDefault();
                const productId = addToCartBtn.dataset.productId;
                
                // Add loading state
                const originalHTML = addToCartBtn.innerHTML;
                addToCartBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Adding...';
                addToCartBtn.disabled = true;
                
                // Simulate AJAX call (replace with actual API call)
                fetch('/api/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Success
                    addToCartBtn.innerHTML = '<i class="bi bi-check"></i> Added!';
                    addToCartBtn.classList.add('btn-success');
                    
                    // Update cart count
                    updateCartCount(data.cart_count);
                    
                    // Show notification
                    showNotification('Product added to cart successfully!', 'success');
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        addToCartBtn.innerHTML = originalHTML;
                        addToCartBtn.disabled = false;
                        addToCartBtn.classList.remove('btn-success');
                    }, 2000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    addToCartBtn.innerHTML = originalHTML;
                    addToCartBtn.disabled = false;
                    showNotification('Failed to add product to cart', 'error');
                });
            }
        });
    }

    // ==========================================
    // WISHLIST FUNCTIONALITY
    // ==========================================
    
    function initWishlist() {
        document.addEventListener('click', function(e) {
            const wishlistBtn = e.target.closest('.btn-wishlist, .btn-wishlist-book, [data-action="wishlist"]');
            
            if (wishlistBtn) {
                e.preventDefault();
                const productId = wishlistBtn.closest('[data-product-id]').dataset.productId;
                
                // Toggle heart icon
                const icon = wishlistBtn.querySelector('i');
                const isFilled = icon.classList.contains('bi-heart-fill');
                
                if (isFilled) {
                    icon.classList.remove('bi-heart-fill');
                    icon.classList.add('bi-heart');
                    showNotification('Removed from wishlist', 'info');
                } else {
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill');
                    showNotification('Added to wishlist', 'success');
                }
                
                // Simulate AJAX call
                fetch('/api/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .catch(error => console.error('Error:', error));
            }
        });
    }

    // ==========================================
    // QUICK VIEW MODAL
    // ==========================================
    
    function initQuickView() {
        document.addEventListener('click', function(e) {
            const quickViewBtn = e.target.closest('.btn-quick-view, [data-bs-target="#quickViewModal"]');
            
            if (quickViewBtn) {
                const productId = quickViewBtn.dataset.productId || 
                                quickViewBtn.closest('[data-product-id]').dataset.productId;
                
                // Load product data via AJAX
                loadQuickViewData(productId);
            }
        });
    }
    
    function loadQuickViewData(productId) {
        // Simulate loading product data
        console.log('Loading quick view for product:', productId);
        
        // In production, fetch product data via AJAX
        // fetch(`/api/products/${productId}`)
        //     .then(response => response.json())
        //     .then(data => populateQuickView(data));
    }

    // ==========================================
    // SEARCH & FILTERS
    // ==========================================
    
    function initSearchFilters() {
        const searchInput = document.querySelector('input[type="search"]');
        
        if (searchInput) {
            let searchTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                
                searchTimeout = setTimeout(() => {
                    const query = this.value;
                    if (query.length >= 3) {
                        performSearch(query);
                    }
                }, 300);
            });
        }
    }
    
    function performSearch(query) {
        console.log('Searching for:', query);
        
        // Implement live search functionality
        // fetch(`/api/search?q=${encodeURIComponent(query)}`)
        //     .then(response => response.json())
        //     .then(data => displaySearchResults(data));
    }

    // ==========================================
    // MOBILE MENU
    // ==========================================
    
    function initMobileMenu() {
        const mobileMenuBtn = document.querySelector('[data-mobile-menu]');
        
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                document.body.classList.toggle('mobile-menu-open');
            });
        }
    }

    // ==========================================
    // UTILITY FUNCTIONS
    // ==========================================
    
    function updateCartCount(count) {
        document.querySelectorAll('.badge.bg-primary').forEach(badge => {
            if (badge.closest('[href*="cart"]')) {
                badge.textContent = count;
            }
        });
    }
    
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 150);
        }, 3000);
    }
    
    // ==========================================
    // PRODUCT ZOOM (Optional Enhancement)
    // ==========================================
    
    function initProductZoom() {
        const zoomImages = document.querySelectorAll('.product-image');
        
        zoomImages.forEach(img => {
            img.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.2)';
            });
            
            img.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    }
    
    // ==========================================
    // SMOOTH SCROLL
    // ==========================================
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId !== '#' && document.querySelector(targetId)) {
                e.preventDefault();
                document.querySelector(targetId).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // ==========================================
    // BACK TO TOP BUTTON
    // ==========================================
    
    window.addEventListener('scroll', function() {
        const backToTop = document.querySelector('.back-to-top');
        
        if (backToTop) {
            if (window.scrollY > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        }
    });

})();

// ==========================================
// EXPORT FOR USE IN OTHER SCRIPTS
// ==========================================

window.WoodMart = {
    showNotification: function(message, type) {
        // Reuse the notification function
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 150);
        }, 3000);
    }
};
