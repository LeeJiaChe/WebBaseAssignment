<?php
require '_base.php';
session_start();
$_title = 'Checkout';
include '_head.php';
?>

<style>
/* Page-specific header override: make header visible on light backgrounds */
header.main-header {
	background: #ffffff !important;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
	color: #000;
}
header.main-header nav a { color: #000 !important; }
header.main-header nav a:hover { background: #eeeeee !important; }
header.main-header .icon-button,
header.main-header .cart-button { color: #222 !important; }
header.main-header .logo-img { filter: none !important; }
</style>

<section class="content">
    <div class="checkout-container">
        <div class="checkout-form">
            <h2>Checkout</h2>
            <form id="checkoutForm">
                <div class="field">
                    <label class="sr-only">Email</label>
                    <input type="email" id="custEmail" name="email" class="form-input" placeholder="Email" required>
                </div>

                <div class="field">
                    <label class="sr-only">Delivery method</label>
                    <div>Ship</div>
                </div>

                <div class="field">
                    <label class="sr-only">Country / Region</label>
                    <select id="country" name="country" class="form-input" required>
                        <option value="" disabled selected>Select country / region</option>
                        <option value="MY">Malaysia</option>
                        <option value="SG">Singapore</option>
                        <option value="US">United States</option>
                        <option value="OTHER">Other</option>
                    </select>
                </div>

                <div class="grid-2">
                    <div class="field"><label class="sr-only">First name</label><input id="firstName" name="firstName" class="form-input" placeholder="First name" required></div>
                    <div class="field"><label class="sr-only">Last name</label><input id="lastName" name="lastName" class="form-input" placeholder="Last name" required></div>
                </div>

                <div class="field"><label class="sr-only">Address</label><input id="address" name="address" class="form-input" placeholder="Address" required></div>
                <div class="field"><label class="sr-only">Apartment, suite, etc. (optional)</label><input id="apt" name="apt" class="form-input" placeholder="Apartment, suite, etc. (optional)"></div>

                <div class="grid-3">
                    <div class="field"><label class="sr-only">Postcode</label><input id="postcode" name="postcode" class="form-input" placeholder="Postcode" required></div>
                    <div class="field"><label class="sr-only">City</label><input id="city" name="city" class="form-input" placeholder="City" required></div>
                    <div class="field"><label class="sr-only">State</label><input id="state" name="state" class="form-input" placeholder="State"></div>
                </div>

                <div class="field"><label class="sr-only">Phone number</label><input id="phone" name="phone" class="form-input" placeholder="Phone number" required></div>

                <div class="field">
                    <label class="sr-only">Payment method</label>
                    <select id="paymentMethod" name="paymentMethod" class="form-input">
                        <option value="" disabled selected>Select payment method</option>
                        <option value="card">Credit / Debit Card</option>
                        <option value="paypal">PayPal</option>
                        <option value="cod">Cash on Delivery</option>
                    </select>
                </div>

                <div style="margin-top:12px;">
                    <button type="submit" id="placeOrder" class="checkout-btn">Pay</button>
                </div>
            </form>
        </div>

        <aside class="order-summary">
            <h3>Order summary</h3>
            <div id="summaryItems">Loading...</div>
            <div id="summaryTotals"></div>
        </aside>
    </div>
</section>

<script>
$(function(){
    // prefill email from PHP session if available
    const phpEmail = <?= json_encode($_SESSION['user_email'] ?? '') ?>;
    if (phpEmail) $('#custEmail').val(phpEmail);

    function renderSummary() {
        let cart = [];
        try { cart = JSON.parse(localStorage.getItem('cart') || '[]'); } catch(e){ cart = []; }
        const $items = $('#summaryItems');
        $items.empty();
        if (!cart.length) { $items.html('<div class="cart-empty">Your cart is empty.</div>'); $('#summaryTotals').empty(); return; }

        let total = 0;
        cart.forEach(it => {
            const qty = it.qty || 1;
            const price = parseFloat(it.price || 0);
            total += price * qty;
            const $row = $('<div class="summary-row"></div>');
            $row.append('<img src="'+(it.image||'')+'" alt="" style="width:60px;height:44px;object-fit:cover;margin-right:8px;"/>');
            $row.append('<div style="flex:1"><div style="font-weight:600">'+(it.name||'')+'</div><div style="color:#666">RM'+price.toFixed(2)+' × '+qty+'</div></div>');
            $items.append($row);
        });

        const shipping = total > 0 ? 10.00 : 0.00; // flat shipping
        const grand = total + shipping;
        $('#summaryTotals').html('<div style="margin-top:12px;font-weight:700">Subtotal: RM'+total.toFixed(2)+'</div>'+
            '<div>Shipping: RM'+shipping.toFixed(2)+'</div>'+
            '<div style="margin-top:8px;font-size:1.1rem;font-weight:800">Total: RM'+grand.toFixed(2)+'</div>');

        // update checkout button text
        $('#placeOrder').text('Pay — RM'+grand.toFixed(2));
    }

    renderSummary();

    $('#checkoutForm').on('submit', function(e){
        e.preventDefault();
        // basic client-side collect
        const data = {
            email: $('#custEmail').val(),
            country: $('#country').val(),
            firstName: $('#firstName').val(),
            lastName: $('#lastName').val(),
            address: $('#address').val(),
            apt: $('#apt').val(),
            postcode: $('#postcode').val(),
            city: $('#city').val(),
            state: $('#state').val(),
            phone: $('#phone').val(),
            paymentMethod: $('#paymentMethod').val(),
            cart: JSON.parse(localStorage.getItem('cart')||'[]')
        };

        // Save a simple lastOrder in localStorage for the thank you page
        localStorage.setItem('lastOrder', JSON.stringify({meta: data, created: Date.now()}));
        // clear cart
        localStorage.removeItem('cart');
        if (typeof updateCartBadge === 'function') updateCartBadge();

        // Redirect to thank you
        location = 'thankyou.php';
    });
});
</script>

<?php include '_foot.php'; ?>
