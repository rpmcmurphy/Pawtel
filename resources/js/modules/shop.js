// Shop functionality
export const Shop = {
    init() {
        this.initAddToCartHandlers();
        this.initCartUpdateHandlers();
        this.initCartRemoveHandlers();
        this.updateCartBadge();
    },

    async addToCart(productId, quantity = 1) {
        try {
            const response = await fetch('/shop/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            const data = await response.json();

            if (data.success) {
                App.showAlert(data.message || 'Item added to cart!', 'success');
                this.updateCartBadge(data.cart_count);
                
                // If on product page, show success feedback
                const addBtn = document.querySelector(`[data-product-id="${productId}"].add-to-cart-btn`);
                if (addBtn) {
                    const originalHtml = addBtn.innerHTML;
                    addBtn.innerHTML = '<i class="fas fa-check me-2"></i>Added!';
                    addBtn.disabled = true;
                    setTimeout(() => {
                        addBtn.innerHTML = originalHtml;
                        addBtn.disabled = false;
                    }, 2000);
                }
            } else {
                App.showAlert(data.message || 'Failed to add item to cart', 'danger');
            }
        } catch (error) {
            console.error('Add to cart error:', error);
            App.showAlert('An error occurred. Please try again.', 'danger');
        }
    },

    async updateCartItem(cartItemId, quantity) {
        try {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/shop/cart/${cartItemId}`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content;
            form.appendChild(csrfInput);

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = 'quantity';
            quantityInput.value = quantity;
            form.appendChild(quantityInput);

            document.body.appendChild(form);
            form.submit();
        } catch (error) {
            console.error('Update cart error:', error);
            App.showAlert('An error occurred. Please try again.', 'danger');
        }
    },

    async removeFromCart(cartItemId) {
        if (!confirm('Are you sure you want to remove this item from your cart?')) {
            return;
        }

        try {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/shop/cart/${cartItemId}`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content;
            form.appendChild(csrfInput);

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        } catch (error) {
            console.error('Remove from cart error:', error);
            App.showAlert('An error occurred. Please try again.', 'danger');
        }
    },

    updateCartBadge(count = null) {
        // Update cart badge if count is provided
        // Otherwise, the badge will be updated server-side on page load
        if (count !== null) {
            const cartBadge = document.querySelector('.cart-count');
            if (cartBadge) {
                cartBadge.textContent = count;
                cartBadge.style.display = count > 0 ? 'inline' : 'none';
            }
        }
    },

    initAddToCartHandlers() {
        document.addEventListener('click', (e) => {
            const addBtn = e.target.closest('.add-to-cart-btn');
            if (addBtn && addBtn.dataset.productId) {
                e.preventDefault();
                const productId = parseInt(addBtn.dataset.productId);
                const quantityInput = document.getElementById('quantity');
                const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;
                
                // Check if user is authenticated
                if (!this.isAuthenticated()) {
                    window.location.href = '/auth/login';
                    return;
                }

                this.addToCart(productId, quantity);
            }
        });
    },

    initCartUpdateHandlers() {
        document.addEventListener('change', (e) => {
            const quantityInput = e.target.closest('.cart-quantity-input');
            if (quantityInput && quantityInput.dataset.cartItemId) {
                const cartItemId = quantityInput.dataset.cartItemId;
                const quantity = parseInt(quantityInput.value) || 1;
                
                if (quantity < 1) {
                    quantityInput.value = 1;
                    return;
                }

                this.updateCartItem(cartItemId, quantity);
            }
        });
    },

    initCartRemoveHandlers() {
        document.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.remove-cart-item');
            if (removeBtn && removeBtn.dataset.cartItemId) {
                e.preventDefault();
                const cartItemId = removeBtn.dataset.cartItemId;
                this.removeFromCart(cartItemId);
            }
        });
    },

    isAuthenticated() {
        // Check if user is authenticated via session
        // This is a simple check - the actual auth check happens on the server
        return document.querySelector('meta[name="csrf-token"]') !== null;
    }
};

window.Shop = Shop;
