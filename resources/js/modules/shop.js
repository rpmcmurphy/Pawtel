// Shop functionality
export const Shop = {
    cart: [],

    init() {
        this.loadCart();
        this.initCartHandlers();
        this.initQuantityHandlers();
    },

    loadCart() {
        const savedCart = localStorage.getItem("pawtel_cart");
        this.cart = savedCart ? JSON.parse(savedCart) : [];
        this.updateCartDisplay();
    },

    addToCart(productId, quantity = 1) {
        const existingItem = this.cart.find((item) => item.id === productId);

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.cart.push({ id: productId, quantity });
        }

        this.saveCart();
        this.updateCartDisplay();
        App.showAlert("Item added to cart!", "success");
    },

    removeFromCart(productId) {
        this.cart = this.cart.filter((item) => item.id !== productId);
        this.saveCart();
        this.updateCartDisplay();
        App.showAlert("Item removed from cart", "info");
    },

    updateQuantity(productId, quantity) {
        const item = this.cart.find((item) => item.id === productId);
        if (item) {
            item.quantity = Math.max(1, quantity);
            this.saveCart();
            this.updateCartDisplay();
        }
    },

    saveCart() {
        localStorage.setItem("pawtel_cart", JSON.stringify(this.cart));
    },

    updateCartDisplay() {
        const cartCount = this.cart.reduce(
            (total, item) => total + item.quantity,
            0
        );
        const cartBadge = document.querySelector(".cart-count");
        if (cartBadge) {
            cartBadge.textContent = cartCount;
            cartBadge.style.display = cartCount > 0 ? "inline" : "none";
        }
    },

    initCartHandlers() {
        document.addEventListener("click", (e) => {
            if (e.target.matches(".add-to-cart")) {
                e.preventDefault();
                const productId = parseInt(e.target.dataset.productId);
                const quantity = parseInt(e.target.dataset.quantity) || 1;
                this.addToCart(productId, quantity);
            }

            if (e.target.matches(".remove-from-cart")) {
                e.preventDefault();
                const productId = parseInt(e.target.dataset.productId);
                this.removeFromCart(productId);
            }
        });
    },

    initQuantityHandlers() {
        document.addEventListener("change", (e) => {
            if (e.target.matches(".cart-quantity")) {
                const productId = parseInt(e.target.dataset.productId);
                const quantity = parseInt(e.target.value);
                this.updateQuantity(productId, quantity);
            }
        });
    },
};

window.Shop = Shop;
