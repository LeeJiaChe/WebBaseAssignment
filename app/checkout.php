<?php








require '_base.php';

// session_start(); // Already called in _base.php

if (empty($_SESSION['user_id']) && empty($_SESSION['user_email'])) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}
$_title = 'Checkout';
include '_head.php';
?>







<style>



/* --- HEADER OVERRIDES --- */



header.main-header {



    position: fixed !important;



    background: #ffffff !important;



    box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;



    height: 70px !important;



    top: 0;



    width: 100%;



    border-bottom: 1px solid #f0f0f0;



    z-index: 1000;



}







header.main-header nav a {



    color: #1a1a1a !important;



    font-weight: 500;



}



header.main-header.scrolled {

    position: fixed !important;



    background: #ffffff !important;



    box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;



    height: 70px !important;



    top: 0;



    width: 100%;



    border-bottom: 1px solid #f0f0f0;



    z-index: 1000;

}



header.main-header.scrolled nav a {



    color: #1a1a1a !important;



    font-weight: 500;



}







header.main-header .icon-button, 



header.main-header .cart-button {



    color: #1a1a1a !important;



}







header.main-header .logo-img {



    filter: none !important; 



    height: 40px !important;



}







/* --- PAGE LAYOUT --- */



body.transparent-header-page main, main {



    margin-top: 70px !important;



    padding-top: 0 !important;



    max-width: 100% !important;



    padding-left: 0 !important;



    padding-right: 0 !important;



}







/* Background with subtle pattern */



.checkout-container {



    background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);



    min-height: calc(100vh - 70px);



    padding: 40px 20px;



}







.checkout-wrapper {



    display: flex;



    width: 1200px;



    margin: 0px auto 30px 100px;



    gap: 70px;



    align-items: flex-start;



}







/* --- LEFT SIDE: FORM CARD --- */



.checkout-main {

    

    flex: 1 1 58%;



    background: #ffffff;



    border-radius: 16px;



    padding: 40px;



    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);



    border: 1px solid rgba(255, 111, 0, 0.1);



    animation: slideInLeft 0.6s ease-out;



}







/* --- RIGHT SIDE: SUMMARY CARD --- */



.checkout-sidebar {



    flex: 1 1 42%;



    background: #ffffff;



    border-radius: 16px;



    padding: 40px;



    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);



    border: 1px solid rgba(255, 111, 0, 0.1);



    position: sticky;



    top: 90px;



    animation: slideInRight 0.6s ease-out;



}







/* Animations */



@keyframes slideInLeft {



    from {



        opacity: 0;



        transform: translateX(-30px);



    }



    to {



        opacity: 1;



        transform: translateX(0);



    }



}







@keyframes slideInRight {



    from {



        opacity: 0;



        transform: translateX(30px);



    }



    to {



        opacity: 1;



        transform: translateX(0);



    }



}







/* Typography */



h2 {



    font-size: 1.5rem;



    font-weight: 700;



    color: #1a1a1a;



    margin-bottom: 24px;



    letter-spacing: -0.5px;



    display: flex;



    align-items: center;



    gap: 12px;



}







h2::before {



    content: '';



    width: 4px;



    height: 24px;



    background: linear-gradient(180deg, #ff8f00 0%, #ff6f00 100%);



    border-radius: 2px;



}







.checkout-section {



    margin-bottom: 40px;



    padding-bottom: 30px;



    border-bottom: 1px solid #f0f0f0;



}







.checkout-section:last-of-type {



    border-bottom: none;



    padding-bottom: 0;



}







/* Form Styling */



.form-group {



    margin-bottom: 20px;



}







.form-row {



    display: flex;



    gap: 16px;



}







.form-col {



    flex: 1;



}







.field-label {



    font-size: 0.9rem;



    font-weight: 600;



    color: #333;



    margin-bottom: 8px;



    display: block;



    display: flex;



    align-items: center;



    gap: 6px;



}







.field-label i {



    color: #ff6f00;



    font-size: 0.85rem;



}







.form-input {



    width: 100%;



    padding: 14px 16px;



    border: 2px solid #e8ecf1;



    border-radius: 10px;



    font-size: 15px;



    background: #fafbfc;



    transition: all 0.3s ease;



    color: #333;



}







.form-input:focus {



    border-color: #ff6f00;



    background: #fff;



    box-shadow: 0 0 0 4px rgba(255, 111, 0, 0.08);



    outline: none;



    transform: translateY(-1px);



}







.form-input::placeholder {



    color: #aaa;



}







/* Delivery Method Card */



.delivery-method {



    border: 2px solid #ff6f00; 



    background: linear-gradient(135deg, #fff9f0 0%, #fffbf5 100%);



    padding: 20px;



    border-radius: 12px;



    display: flex;



    justify-content: space-between;



    align-items: center;



    color: #333;



    box-shadow: 0 4px 12px rgba(255, 111, 0, 0.1);



    transition: transform 0.2s;



}







.delivery-method:hover {



    transform: translateY(-2px);



    box-shadow: 0 6px 16px rgba(255, 111, 0, 0.15);



}







.delivery-method-icon {



    display: flex;



    align-items: center;



    gap: 12px;



    font-weight: 600;



    font-size: 1.05rem;



}







.check-icon {



    color: #ff6f00;



    font-size: 1.3rem;



}







/* Summary Header */



.summary-header {



    background: linear-gradient(135deg, #ff8f00 0%, #ff6f00 100%);



    margin: -40px -40px 30px -40px;



    padding: 24px 40px;



    border-radius: 16px 16px 0 0;



    color: white;



}







.summary-header h2 {



    color: white;



    margin: 0;



}







.summary-header h2::before {



    background: white;



}







/* Summary Items */



.summary-items {



    margin-bottom: 30px;



    max-height: 400px;



    overflow-y: auto;



    padding-right: 10px;



}







.summary-items::-webkit-scrollbar {



    width: 6px;



}







.summary-items::-webkit-scrollbar-track {



    background: #f1f1f1;



    border-radius: 10px;



}







.summary-items::-webkit-scrollbar-thumb {



    background: #ff6f00;



    border-radius: 10px;



}







.summary-row {



    display: flex;



    align-items: center;



    margin-bottom: 20px;



    padding: 12px;



    background: #fafbfc;



    border-radius: 12px;



    border: 1px solid #f0f0f0;



    transition: all 0.2s;



}







.summary-row:hover {



    background: #fff;



    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);



    transform: translateX(4px);



}







.summary-img-box {



    position: relative;



    width: 70px;



    height: 70px;



    background: #fff;



    border: 2px solid #f0f0f0;



    border-radius: 12px;



    padding: 8px;



    margin-right: 16px;



}







.summary-img {



    width: 100%;



    height: 100%;



    object-fit: contain;



}







.qty-badge {



    position: absolute;



    top: -8px;



    right: -8px;



    background: linear-gradient(135deg, #ff8f00 0%, #ff6f00 100%);



    color: #fff;



    font-size: 12px;



    min-width: 24px;



    height: 24px;



    border-radius: 12px;



    display: flex;



    align-items: center;



    justify-content: center;



    font-weight: 700;



    box-shadow: 0 2px 8px rgba(255, 111, 0, 0.3);



}







.summary-details {



    flex: 1;



}







.prod-name {



    font-weight: 600;



    color: #333;



    font-size: 0.95rem;



    margin-bottom: 4px;



}







.prod-price {



    font-weight: 700;



    color: #ff6f00;



    font-size: 1.1rem;



}







/* Totals */



.totals-box {



    border-top: 2px solid #f0f0f0;



    padding-top: 20px;



    background: linear-gradient(135deg, #fafbfc 0%, #f5f7fa 100%);



    margin: 0 -40px -40px -40px;



    padding: 24px 40px;



    border-radius: 0 0 16px 16px;



}







.total-row {



    display: flex;



    justify-content: space-between;



    margin-bottom: 12px;



    color: #666;



    font-size: 0.95rem;



}







.total-row span:first-child {



    font-weight: 500;



}







.total-row span:last-child {



    font-weight: 600;



    color: #333;



}







.grand-total {



    margin-top: 15px;



    padding-top: 15px;



    border-top: 2px dashed #e0e0e0;



    display: flex;



    justify-content: space-between;



    align-items: baseline;



}







.grand-total .label {



    font-size: 1.2rem;



    font-weight: 700;



    color: #333;



}







.grand-total .value {



    font-size: 1.8rem;



    font-weight: 800;



    background: linear-gradient(135deg, #ff8f00 0%, #ff6f00 100%);



    -webkit-background-clip: text;



    -webkit-text-fill-color: transparent;



    background-clip: text;



}







/* Buttons */



.pay-btn {



    width: 100%;



    background: linear-gradient(135deg, #ff8f00 0%, #ff6f00 100%);



    color: #fff;



    border: none;



    padding: 18px;



    font-size: 1.15rem;



    font-weight: 700;



    border-radius: 12px;



    cursor: pointer;



    box-shadow: 0 6px 20px rgba(255, 111, 0, 0.3);



    transition: all 0.3s ease;



    margin-top: 20px;



    display: flex;



    align-items: center;



    justify-content: center;



    gap: 10px;



}







.pay-btn:hover {



    transform: translateY(-3px);



    box-shadow: 0 8px 25px rgba(255, 111, 0, 0.4);



}







.pay-btn:active {



    transform: translateY(-1px);



}







.pay-btn i {



    font-size: 1.2rem;



}







.return-link {



    text-align: center;



    margin-top: 20px;



}







.return-link a {



    color: #666;



    text-decoration: none;



    font-size: 0.95rem;



    font-weight: 500;



    display: inline-flex;



    align-items: center;



    gap: 8px;



    transition: all 0.2s;



}







.return-link a:hover {



    color: #ff6f00;



    gap: 12px;



}







/* Security Badge */



.security-badge {



    text-align: center;



    margin-top: 20px;



    padding: 16px;



    background: #f8f9fa;



    border-radius: 8px;



    color: #666;



    font-size: 0.85rem;



}







.security-badge i {



    color: #28a745;



    margin-right: 6px;



}







/* Responsive */



@media (max-width: 1024px) {



    .checkout-wrapper { 



        flex-direction: column; 



    }



    



    .checkout-main, 



    .checkout-sidebar { 



        flex: auto; 



        width: 100%;



    }



    



    .checkout-sidebar { 



        position: static;



    }



}







@media (max-width: 768px) {



    .checkout-container {



        padding: 20px 15px;



    }



    



    .checkout-main,



    .checkout-sidebar {



        padding: 24px;



        border-radius: 12px;



    }



    



    .summary-header {



        margin: -24px -24px 20px -24px;



        padding: 20px 24px;



        border-radius: 12px 12px 0 0;



    }



    



    .totals-box {



        margin: 0 -24px -24px -24px;



        padding: 20px 24px;



        border-radius: 0 0 12px 12px;



    }



    



    .form-row {



        flex-direction: column;



        gap: 0;



    }



    



    h2 {



        font-size: 1.3rem;



    }



}

.checkout-container::before {

    display: none !important;

}



</style>







<div class="checkout-container">



    <div class="checkout-wrapper">



        <div class="checkout-main">



            <form id="checkoutForm">



                <div class="checkout-section">



                    <h2><i class="fas fa-envelope"></i> Contact Information</h2>



                    <div class="form-group">



                        <label class="field-label">



                            <i class="fas fa-at"></i>



                            Email Address



                        </label>



                        <input type="email" id="custEmail" name="email" class="form-input" placeholder="e.g. name@example.com" required>



                    </div>



                </div>







                <div class="checkout-section">



                    <h2><i class="fas fa-map-marker-alt"></i> Shipping Address</h2>



                    



                    <div class="form-group">



                        <label class="field-label">



                            <i class="fas fa-globe"></i>



                            Country / Region



                        </label>



                        <select id="country" name="country" class="form-input" required>



                            <option value="MY">Malaysia</option>



                            <option value="SG">Singapore</option>



                            <option value="US">United States</option>



                        </select>



                    </div>







                    <div class="form-row">



                        <div class="form-col form-group">



                            <label class="field-label">



                                <i class="fas fa-user"></i>



                                First Name



                            </label>



                            <input id="firstName" name="firstName" class="form-input" required>



                        </div>



                        <div class="form-col form-group">



                            <label class="field-label">



                                <i class="fas fa-user"></i>



                                Last Name



                            </label>



                            <input id="lastName" name="lastName" class="form-input" required>



                        </div>



                    </div>







                    <div class="form-group">



                        <label class="field-label">



                            <i class="fas fa-home"></i>



                            Address



                        </label>



                        <input id="address" name="address" class="form-input" placeholder="Street address, P.O. box" required>



                    </div>







                    <div class="form-row">



                        <div class="form-col form-group">



                            <label class="field-label">



                                <i class="fas fa-mail-bulk"></i>



                                Postcode



                            </label>



                            <input id="postcode" name="postcode" class="form-input" required>



                        </div>



                        <div class="form-col form-group">



                            <label class="field-label">



                                <i class="fas fa-city"></i>



                                City



                            </label>



                            <input id="city" name="city" class="form-input" required>



                        </div>



                    </div>



                    



                    <div class="form-group">



                        <label class="field-label">



                            <i class="fas fa-phone"></i>



                            Phone



                        </label>



                        <input id="phone" name="phone" class="form-input" placeholder="+60" required>



                    </div>



                </div>







                <div class="checkout-section">



                    <h2><i class="fas fa-shipping-fast"></i> Shipping Method</h2>



                    <div class="delivery-method">



                        <div class="delivery-method-icon">



                            <span class="check-icon"><i class="fas fa-check-circle"></i></span>



                            <span>Standard Shipping (3-5 business days)</span>



                        </div>



                        <strong>RM 10.00</strong>



                    </div>



                </div>







                <div class="checkout-section">



                    <h2><i class="fas fa-credit-card"></i> Payment</h2>



                    <div class="form-group">



                        <label class="field-label">



                            <i class="fas fa-money-check-alt"></i>



                            Payment Method



                        </label>



                        <select id="paymentMethod" name="paymentMethod" class="form-input" required>



                            <option value="card">Credit / Debit Card</option>



                            <option value="fpx">FPX Online Banking</option>



                            <option value="ewallet">E-Wallet (GrabPay, TnG)</option>



                            <option value="cod">Cash on Delivery</option>



                        </select>



                    </div>



                </div>







                <button type="submit" id="placeOrder" class="pay-btn">



                    <i class="fas fa-lock"></i>



                    <span>Pay Now</span>



                </button>



                



                <div class="security-badge">



                    <i class="fas fa-shield-alt"></i>



                    Secure checkout powered by industry-standard encryption



                </div>



                



                <div class="return-link">



                    <a href="cart.php">



                        <i class="fas fa-arrow-left"></i>



                        Return to Cart



                    </a>



                </div>



            </form>



        </div>







        <div class="checkout-sidebar">



            <div class="summary-header">



                <h2><i class="fas fa-shopping-bag"></i> Order Summary</h2>



            </div>



            



            <div id="summaryItems" class="summary-items">



                <p>Loading items...</p>



            </div>



            



            <div id="summaryTotals" class="totals-box"></div>



        </div>



    </div>



</div>







<script>



$(function(){



    // prefill email if logged in



    const phpEmail = <?= json_encode($_SESSION['user_email'] ?? '') ?>;



    if (phpEmail) $('#custEmail').val(phpEmail);







    function renderSummary() {



        let cart = [];



        try { cart = JSON.parse(localStorage.getItem('cart') || '[]'); } catch(e){ cart = []; }



        const $items = $('#summaryItems');

        $items.empty();





        if (!cart||cart.length===0) { 



            $items.html('<p style="text-align: center; color: #999; padding: 20px;">Your cart is empty.</p>');

        return;        }







        let total = 0;



        cart.forEach(it => {



            const qty = it.qty || 1;



            const price = parseFloat(it.price || 0);



            total += price * qty;






            const $row = $(`



                <div class="summary-row">



                    <div class="summary-img-box">



                        <img src="${it.image || '/images/placeholder.png'}" class="summary-img">



                        <div class="qty-badge">${qty}</div>



                    </div>



                    <div class="summary-details">



                        <div class="prod-name">${it.name}</div>



                    </div>



                    <div class="prod-price">RM${(price * qty).toFixed(2)}</div>



                </div>



            `);



            $items.append($row);



        });
        const shipping = 10.00;
        const grand = total + shipping;

      $totals.html(`

        <div class="total-row">
            <span>Subtotal</span>
            <span>RM${total.toFixed(2)}</span>
        </div>

        <div class="total-row">
            <span>Shipping</span>
            <span>RM${shipping.toFixed(2)}</span>
        </div>

        <div class="grand-total">
            <span class="label">Total</span>
            <span class="value">RM ${grand.toFixed(2)}</span>
        </div>

      `);

      $('#placeOrder span').text(`Pay RM ${grand.toFixed(2)}`);
    }

    const $totals = $('#summaryTotals');
    renderSummary();

    window.addEventListener('storage', function(e) {
        if (e.key === 'cart') {
            renderSummary();
        }
    });
    $(document).on('click', '.remove-item, .qty-incr, .qty-decr', function() {
        setTimeout(renderSummary, 100);
    });

  $('#checkoutForm').on('submit', function(e){

    e.preventDefault();
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');

    let total = 0;

    cart.forEach(it => total += (it.price * it.qty));

    const formData = new FormData(this);
    const orderData = {

      total: total + 10.00, // 加上运费
      items: cart.map(it => ({
          id: it.id,    // <--- THIS MUST BE THE NUMERIC ID FROM THE 'products' TABLE
          qty: it.qty,
          price: it.price
      })),
      cart: cart,
      email: formData.get('email'),
      firstName: formData.get('firstName'),
      lastName: formData.get('lastName'),
      address: formData.get('address'),
      city: formData.get('city'),
      postcode: formData.get('postcode'),
      country: formData.get('country'),
      phone: formData.get('phone'),
      paymentMethod: formData.get('paymentMethod')

    };

    fetch('api/place-order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(orderData)
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            localStorage.setItem('lastOrder', JSON.stringify({meta: orderData, created: Date.now()}));
            localStorage.removeItem('cart');
            window.location.href = 'thankyou.php';
        } else {
          alert('Order failed: ' + res.message);
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('An error occurred while placing the order.');
    });
  });
});
</script>
