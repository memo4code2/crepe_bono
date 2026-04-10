function getCart() {
    return JSON.parse(localStorage.getItem("cart")) || [];
}

function saveCart(cart) {
    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartCount();
}

function updateCartCount() {
    let cart = getCart();
    let count = cart.reduce((sum, item) => sum + item.quantity, 0);
    let badge = document.getElementById("cart-count");
    if (badge) {
        badge.innerText = count;
    }
}

function addToCart(id, name, price) {
    let cart = getCart();
    let existing = cart.find(item => item.product_id === id);

    if (existing) {
        existing.quantity++;
    } else {
        cart.push({
            product_id: id,
            name: name,
            price: price,
            quantity: 1
        });
    }

    saveCart(cart);
    alert("Added to Cart ✅");
}

function displayCart() {
    let cart = getCart();
    let container = document.getElementById("cart-items");
    let totalElement = document.getElementById("total");
    let checkoutBtn = document.getElementById("checkout-btn");

    if (!container || !totalElement) return;

    container.innerHTML = "";

    if (cart.length === 0) {
        container.innerHTML = "<p>Your cart is empty. Add items from the menu.</p>";
        totalElement.innerText = "0 EGP";
        if (checkoutBtn) {
            checkoutBtn.classList.add("disabled");
            checkoutBtn.href = "#";
        }
        return;
    }

    let total = 0;

    cart.forEach((item, index) => {
        total += item.price * item.quantity;

        container.innerHTML += `
            <div class="cart-item">
                <h4>${item.name}</h4>
                <p>Price: ${item.price} EGP</p>

                <div class="quantity">
                    <button onclick="decrease(${index})">-</button>
                    <span>${item.quantity}</span>
                    <button onclick="increase(${index})">+</button>
                </div>

                <button class="remove" onclick="removeItem(${index})">Remove</button>
            </div>
        `;
    });

    totalElement.innerText = total + " EGP";

    if (checkoutBtn) {
        checkoutBtn.classList.remove("disabled");
        checkoutBtn.href = "checkout.html";
    }
}

function increase(index) {
    let cart = getCart();
    if (cart[index]) {
        cart[index].quantity++;
        saveCart(cart);
        displayCart();
    }
}

function decrease(index) {
    let cart = getCart();
    if (cart[index] && cart[index].quantity > 1) {
        cart[index].quantity--;
        saveCart(cart);
        displayCart();
    }
}

function removeItem(index) {
    let cart = getCart();
    if (cart[index]) {
        cart.splice(index, 1);
        saveCart(cart);
        displayCart();
    }
}

function populateCartData() {
    let cart = JSON.stringify(getCart());
    let cartInput = document.getElementById("cart_data");
    if (cartInput) {
        cartInput.value = cart;
    }
}

function displayCheckout() {
    let cart = getCart();
    let container = document.getElementById("checkout-summary-items");
    let totalElement = document.getElementById("checkout-total");
    let submitButton = document.querySelector(".checkout-form button[type='submit']");

    if (!container || !totalElement) return;

    container.innerHTML = "";
    let total = 0;

    if (cart.length === 0) {
        container.innerHTML = "<p>Your cart is empty. Add items before checkout.</p>";
        totalElement.innerText = "0 EGP";
        if (submitButton) {
            submitButton.disabled = true;
        }
        return;
    }

    cart.forEach(item => {
        let itemTotal = item.price * item.quantity;
        total += itemTotal;
        container.innerHTML += `<p>${item.name} x${item.quantity} - ${itemTotal} EGP</p>`;
    });

    totalElement.innerText = total + " EGP";
    if (submitButton) {
        submitButton.disabled = false;
    }
}

function clearCart() {
    localStorage.removeItem("cart");
    updateCartCount();
}

updateCartCount();
if (document.getElementById("cart-items")) {
    displayCart();
}
if (document.getElementById("cart_data")) {
    populateCartData();
}
if (document.getElementById("checkout-summary-items")) {
    displayCheckout();
}
