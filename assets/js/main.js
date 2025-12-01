// assets/js/main.js
document.addEventListener('DOMContentLoaded', function () {
    updateCartCount();

    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const productId = this.dataset.id;
            const quantity = 1; // Default to 1

            addToCart(productId, quantity);
        });
    });

    // Quantity inputs in cart
    const quantityInputs = document.querySelectorAll('.cart-quantity');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function () {
            const productId = this.dataset.id;
            const quantity = parseInt(this.value);

            if (quantity > 0) {
                updateCartItem(productId, quantity);
            }
        });
    });

    // Remove from cart buttons
    const removeButtons = document.querySelectorAll('.remove-from-cart');
    removeButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const productId = this.dataset.id;

            if (confirm('Voulez-vous vraiment retirer cet article du panier ?')) {
                removeFromCart(productId);
            }
        });
    });
});

function addToCart(productId, quantity) {
    fetch('cart_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&product_id=${productId}&quantity=${quantity}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount();
                alert('Produit ajouté au panier !');
            } else {
                alert('Erreur lors de l\'ajout au panier.');
            }
        })
        .catch(error => console.error('Error:', error));
}

function updateCartItem(productId, quantity) {
    fetch('cart_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update&product_id=${productId}&quantity=${quantity}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Reload to update totals
            }
        });
}

function removeFromCart(productId) {
    fetch('cart_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=remove&product_id=${productId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
}

function updateCartCount() {
    fetch('cart_action.php?action=count')
        .then(response => response.json())
        .then(data => {
            const countElement = document.getElementById('cart-count');
            if (countElement) {
                countElement.textContent = data.count;
            }
        })
        .catch(error => console.error('Error:', error));
}
