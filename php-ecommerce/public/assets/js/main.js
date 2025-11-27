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
            const addToCartBtn = e.target.closest('.btn-add-to-cart, .btn-add-cart-book, .btn-add-cart-fashion, .btn-primary-tech, .btn-cart-minimal');
            
            if (addToCartBtn) {
                e.preventDefault();
                
                const productId = addToCartBtn.dataset.productId;
                const variationId = addToCartBtn.dataset.variationId || null;
                const quantity = parseInt(addToCartBtn.dataset.quantity || 1);
                
                // Add loading state
                const originalHTML = addToCartBtn.innerHTML;
                addToCartBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Adding...';
                addToCartBtn.disabled = true;
                
                // Make AJAX call
                fetch(CONFIG.CART_API, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'add',
                        product_id: productId,
                        variation_id: variationId,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Success
                        addToCartBtn.innerHTML = '<i class=\"bi bi-check\"></i> Added!';
                        
                        // Update cart count
                        updateCartCount(data.cart_count);
                        
                        // Open mini cart
                        openMiniCart();
                        
                        // Reload mini cart content
                        loadMiniCartContent();
                        
                        // Show notification
                        showNotification('Product added to cart successfully!', 'success');
                        
                        // Reset button after 2 seconds
                        setTimeout(() => {
                            addToCartBtn.innerHTML = originalHTML;
                            addToCartBtn.disabled = false;
                        }, 2000);
                    } else {
                        throw new Error(data.message || 'Failed to add to cart');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    addToCartBtn.innerHTML = originalHTML;
                    addToCartBtn.disabled = false;
                    showNotification(error.message || 'Failed to add product to cart', 'error');
                });
            }
        });
    }
    
    // ==========================================
    // MINI CART
    // ==========================================
    
    function initMiniCart() {
        // Close mini cart on overlay click
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('mini-cart-overlay')) {
                closeMiniCart();
            }
        });
        
        // Handle quantity updates in mini cart
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('mini-cart-qty-btn')) {
                e.preventDefault();
                const cartItemId = e.target.dataset.cartItemId;
                const action = e.target.dataset.action;
                const qtyInput = document.querySelector(`input[data-cart-item-id="${cartItemId}"]`);
                
                if (qtyInput) {
                    let newQty = parseInt(qtyInput.value);
                    if (action === 'increase') {
                        newQty++;
                    } else if (action === 'decrease' && newQty > 1) {
                        newQty--;
                    }
                    
                    updateCartItemQuantity(cartItemId, newQty);
                }
            }
            
            // Remove item
            if (e.target.closest('.mini-cart-remove-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.mini-cart-remove-btn');
                const cartItemId = btn.dataset.cartItemId;
                removeCartItem(cartItemId);
            }
        });
    }
    
    function openMiniCart() {
        const miniCart = document.getElementById('miniCart');
        const overlay = document.getElementById('miniCartOverlay');
        
        if (miniCart && overlay) {
            miniCart.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeMiniCart() {
        const miniCart = document.getElementById('miniCart');
        const overlay = document.getElementById('miniCartOverlay');
        
        if (miniCart && overlay) {
            miniCart.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    function loadMiniCartContent() {
        fetch(CONFIG.CART_API)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateMiniCartUI(data.items, data.total);
                }
            })
            .catch(error => console.error('Error loading cart:', error));
    }
    
    function updateMiniCartUI(items, total) {
        const cartItemsContainer = document.getElementById('miniCartItems');
        const cartTotalElement = document.getElementById('miniCartTotal');
        
        if (!cartItemsContainer) return;
        
        if (items.length === 0) {
            cartItemsContainer.innerHTML = '<p class="text-center text-muted py-5">Your cart is empty</p>';
            if (cartTotalElement) cartTotalElement.textContent = '$0.00';
            return;
        }
        
        let html = '';
        items.forEach(item => {
            const variationText = item.attributes ? 
                Object.entries(item.attributes).map(([key, val]) => `${key}: ${val}`).join(', ') : '';
            
            html += `
                <div class="mini-cart-item" data-cart-item-id="${item.id}">
                    <img src="${item.image || '/assets/images/placeholder.jpg'}" alt="${item.name}">
                    <div class="mini-cart-item-details">
                        <h6>${item.name}</h6>
                        ${variationText ? `<small class="text-muted">${variationText}</small>` : ''}
                        <div class="mini-cart-item-price">$${parseFloat(item.price).toFixed(2)}</div>
                        <div class="mini-cart-qty">
                            <button class="mini-cart-qty-btn" data-cart-item-id="${item.id}" data-action="decrease">-</button>
                            <input type="number" value="${item.quantity}" min="1" data-cart-item-id="${item.id}" readonly>
                            <button class="mini-cart-qty-btn" data-cart-item-id="${item.id}" data-action="increase">+</button>
                        </div>
                    </div>
                    <button class="mini-cart-remove-btn" data-cart-item-id="${item.id}">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `;
        });
        
        cartItemsContainer.innerHTML = html;
        if (cartTotalElement) {
            cartTotalElement.textContent = `$${parseFloat(total).toFixed(2)}`;
        }
    }
    
    function updateCartItemQuantity(cartItemId, quantity) {
        fetch(CONFIG.CART_API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'update',
                cart_item_id: cartItemId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
                loadMiniCartContent();
                showNotification('Cart updated', 'success');
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            showNotification(error.message, 'error');
            loadMiniCartContent(); // Reload to reset
        });
    }
    
    function removeCartItem(cartItemId) {
        if (!confirm('Remove this item from cart?')) return;
        
        fetch(CONFIG.CART_API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'remove',
                cart_item_id: cartItemId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
                loadMiniCartContent();
                showNotification('Item removed from cart', 'info');
            }
        })
        .catch(error => {
            showNotification('Failed to remove item', 'error');
        });
    }
    
    function loadCartCount() {
        fetch(CONFIG.CART_API + '?action=count')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartCount(data.count);
                }
            })
            .catch(error => console.error('Error loading cart count:', error));
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
