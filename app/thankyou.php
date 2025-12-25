<?php

require '_base.php';

// session_start(); // Already called in _base.php

$_title = 'Thank You';

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

body {

    background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);

    margin: 0;

    padding: 0;

}



body.transparent-header-page main, main {

    margin-top: 70px !important;

    padding-top: 0 !important;

    max-width: 100% !important;

    padding-left: 0 !important;

    padding-right: 0 !important;

    background: none !important;

}



.thankyou-container {

    background: transparent;

    min-height: calc(100vh - 70px);

    padding: 60px 20px;

    display: flex;

    justify-content: center;

    align-items: center;

}



.thankyou-card {

    max-width: 800px;

    width: 100%;

    background: #ffffff;

    border-radius: 20px;

    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);

    border: 1px solid rgba(255, 111, 0, 0.1);

    overflow: hidden;

    animation: slideUp 0.6s ease-out;

}



@keyframes slideUp {

    from {

        opacity: 0;

        transform: translateY(30px);

    }

    to {

        opacity: 1;

        transform: translateY(0);

    }

}



/* Success Header */

.success-header {

    background: linear-gradient(135deg, #ff8f00 0%, #ff6f00 100%);

    padding: 50px 40px;

    text-align: center;

    color: white;

    position: relative;

    overflow: hidden;

}



.success-header::before {

    content: '';

    position: absolute;

    top: -50%;

    left: -50%;

    width: 200%;

    height: 200%;

    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);

    animation: pulse 3s ease-in-out infinite;

}



@keyframes pulse {

    0%, 100% { transform: scale(1); opacity: 0.5; }

    50% { transform: scale(1.1); opacity: 0.8; }

}



.success-icon {

    width: 80px;

    height: 80px;

    background: white;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    margin: 0 auto 20px;

    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);

    position: relative;

    animation: bounceIn 0.8s ease-out;

}



@keyframes bounceIn {

    0% { transform: scale(0); }

    50% { transform: scale(1.1); }

    100% { transform: scale(1); }

}



.success-icon i {

    color: #ff6f00;

    font-size: 40px;

}



.success-header h1 {

    font-size: 2rem;

    font-weight: 700;

    margin: 0 0 10px 0;

    letter-spacing: -0.5px;

}



.success-header p {

    font-size: 1.1rem;

    margin: 0;

    opacity: 0.95;

}



/* Content Section */

.thankyou-content {

    padding: 40px;

}



.order-info-section {

    margin-bottom: 30px;

}



.section-title {

    font-size: 1.2rem;

    font-weight: 700;

    color: #1a1a1a;

    margin-bottom: 20px;

    display: flex;

    align-items: center;

    gap: 10px;

}



.section-title::before {

    content: '';

    width: 4px;

    height: 20px;

    background: linear-gradient(180deg, #ff8f00 0%, #ff6f00 100%);

    border-radius: 2px;

}



.section-title i {

    color: #ff6f00;

}



.info-grid {

    display: grid;

    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));

    gap: 20px;

    margin-bottom: 30px;

}



.info-item {

    background: #fafbfc;

    padding: 16px;

    border-radius: 10px;

    border: 1px solid #f0f0f0;

}



.info-label {

    font-size: 0.85rem;

    color: #666;

    margin-bottom: 6px;

    font-weight: 600;

}



.info-value {

    font-size: 1rem;

    color: #333;

    font-weight: 600;

}



/* Order Items */

.order-items {

    background: #fafbfc;

    border-radius: 12px;

    padding: 20px;

    border: 1px solid #f0f0f0;

}



.item-row {

    display: flex;

    align-items: center;

    padding: 12px;

    background: white;

    border-radius: 10px;

    margin-bottom: 12px;

    border: 1px solid #f0f0f0;

    transition: all 0.2s;

}



.item-row:last-child {

    margin-bottom: 0;

}



.item-row:hover {

    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);

    transform: translateX(4px);

}



.item-img {

    width: 60px;

    height: 60px;

    object-fit: contain;

    background: white;

    border: 1px solid #e0e0e0;

    border-radius: 8px;

    padding: 6px;

    margin-right: 16px;

}



.item-details {

    flex: 1;

}



.item-name {

    font-weight: 600;

    color: #333;

    font-size: 0.95rem;

    margin-bottom: 4px;

}



.item-quantity {

    color: #666;

    font-size: 0.85rem;

}



.item-price {

    font-weight: 700;

    color: #ff6f00;

    font-size: 1.05rem;

}



/* Order Summary */

.order-summary {

    background: linear-gradient(135deg, #fafbfc 0%, #f5f7fa 100%);

    padding: 20px;

    border-radius: 12px;

    margin-top: 20px;

}



.summary-row {

    display: flex;

    justify-content: space-between;

    margin-bottom: 10px;

    color: #666;

    font-size: 0.95rem;

}



.summary-row span:last-child {

    font-weight: 600;

    color: #333;

}



.summary-total {

    display: flex;

    justify-content: space-between;

    padding-top: 15px;

    margin-top: 15px;

    border-top: 2px dashed #e0e0e0;

}



.summary-total .label {

    font-size: 1.1rem;

    font-weight: 700;

    color: #333;

}



.summary-total .value {

    font-size: 1.5rem;

    font-weight: 800;

    background: linear-gradient(135deg, #ff8f00 0%, #ff6f00 100%);

    -webkit-background-clip: text;

    -webkit-text-fill-color: transparent;

    background-clip: text;

}



/* Action Buttons */

.action-buttons {

    display: flex;

    gap: 16px;

    margin-top: 30px;

}



.btn {

    flex: 1;

    padding: 16px 24px;

    border-radius: 12px;

    font-size: 1rem;

    font-weight: 600;

    cursor: pointer;

    transition: all 0.3s ease;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 10px;

    text-decoration: none;

    border: none;

}



.btn-primary {

    background: linear-gradient(135deg, #ff8f00 0%, #ff6f00 100%);

    color: white;

    box-shadow: 0 4px 15px rgba(255, 111, 0, 0.3);

}



.btn-primary:hover {

    background: linear-gradient(135deg, #d97700 0%, #c86400 100%);

    transform: translateY(-2px);

    box-shadow: 0 6px 20px rgba(200, 100, 0, 0.4);

}



.btn-secondary {

    background: white;

    color: #333;

    border: 2px solid #e8ecf1;

}



.btn-secondary:hover {

    background: #333;

    color: #ffffff;

    transform: translateY(-2px);

}



/* What's Next Section */

.whats-next {

    background: #fff9f0;

    border: 2px solid #ffe4cc;

    border-radius: 12px;

    padding: 20px;

    margin-top: 30px;

}



.whats-next-title {

    font-size: 1.1rem;

    font-weight: 700;

    color: #ff6f00;

    margin-bottom: 16px;

    display: flex;

    align-items: center;

    gap: 10px;

}



.whats-next-list {

    list-style: none;

    padding: 0;

    margin: 0;

}



.whats-next-list li {

    padding: 10px 0;

    color: #555;

    display: flex;

    align-items: start;

    gap: 12px;

}



.whats-next-list li i {

    color: #ff6f00;

    margin-top: 4px;

}



/* Responsive */

@media (max-width: 768px) {

    .thankyou-container {

        padding: 30px 15px;

    }

    

    .thankyou-card {

        border-radius: 16px;

    }

    

    .success-header {

        padding: 40px 24px;

    }

    

    .success-header h1 {

        font-size: 1.5rem;

    }

    

    .thankyou-content {

        padding: 24px;

    }

    

    .action-buttons {

        flex-direction: column;

    }

    

    .info-grid {

        grid-template-columns: 1fr;

    }

}

</style>



<div class="thankyou-container">

    <div class="thankyou-card">

        <div class="success-header">

            <div class="success-icon">

                <i class="fas fa-check"></i>

            </div>

            <h1>Order Confirmed!</h1>

            <p>Thank you for your purchase</p>

        </div>

        

        <div class="thankyou-content">

            <div id="orderDetails">

                <p style="text-align: center; color: #999; padding: 20px;">Loading order information...</p>

            </div>

        </div>

    </div>

</div>



<script>

$(function(){

    let last = null;

    try { 

        last = JSON.parse(localStorage.getItem('lastOrder') || 'null'); 

    } catch(e){ 

        last = null; 

    }

    

    const $details = $('#orderDetails');

    

    if (!last || !last.meta) { 

        $details.html(`

            <div style="text-align: center; padding: 40px 20px;">

                <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #ff6f00; margin-bottom: 16px;"></i>

                <p style="color: #666; margin-bottom: 24px;">No order information found.</p>

                <a href="index.php" class="btn btn-primary" style="display: inline-flex; max-width: 300px;">

                    <i class="fas fa-home"></i>

                    Back to Main Menu

                </a>

            </div>

        `);

        return; 

    }



    const meta = last.meta;

    const cart = meta.cart || [];

    

    // Calculate totals

    let subtotal = 0;

    cart.forEach(item => {

        const price = parseFloat(item.price || 0);

        const qty = parseInt(item.qty || 1);

        subtotal += price * qty;

    });

    const shipping = 10.00;

    const total = subtotal + shipping;

    

    // Build order details HTML

    let html = `

        <div class="order-info-section">

            <div class="section-title">

                <i class="fas fa-info-circle"></i>

                Order Information

            </div>

            

            <div class="info-grid">

                <div class="info-item">

                    <div class="info-label">Email</div>

                    <div class="info-value">${meta.email || 'N/A'}</div>

                </div>

                <div class="info-item">

                    <div class="info-label">Payment Method</div>

                    <div class="info-value">${getPaymentMethodLabel(meta.paymentMethod)}</div>

                </div>

                <div class="info-item">

                    <div class="info-label">Delivery Method</div>

                    <div class="info-value">Standard Shipping</div>

                </div>

                <div class="info-item">

                    <div class="info-label">Order Date</div>

                    <div class="info-value">${formatDate(last.created)}</div>

                </div>

            </div>

            

            <div class="info-item" style="margin-bottom: 0;">

                <div class="info-label">Shipping Address</div>

                <div class="info-value">

                    ${meta.firstName || ''} ${meta.lastName || ''}<br>

                    ${meta.address || ''}<br>

                    ${meta.postcode || ''} ${meta.city || ''}<br>

                    ${meta.country || ''}<br>

                    ${meta.phone || ''}

                </div>

            </div>

        </div>

        

        <div class="order-info-section">

            <div class="section-title">

                <i class="fas fa-box-open"></i>

                Order Items

            </div>

            

            <div class="order-items">

    `;

    

    // Add items

    cart.forEach(item => {

        const price = parseFloat(item.price || 0);

        const qty = parseInt(item.qty || 1);

        const itemTotal = price * qty;

        

        html += `

            <div class="item-row">

                <img src="${item.image || '/images/placeholder.png'}" class="item-img" alt="${item.name || 'Product'}">

                <div class="item-details">

                    <div class="item-name">${item.name || 'Product'}</div>

                    <div class="item-quantity">Quantity: ${qty}</div>

                </div>

                <div class="item-price">RM${itemTotal.toFixed(2)}</div>

            </div>

        `;

    });

    

    html += `

            </div>

            

            <div class="order-summary">

                <div class="summary-row">

                    <span>Subtotal</span>

                    <span>RM${subtotal.toFixed(2)}</span>

                </div>

                <div class="summary-row">

                    <span>Shipping</span>

                    <span>RM${shipping.toFixed(2)}</span>

                </div>

                <div class="summary-total">

                    <span class="label">Total Paid</span>

                    <span class="value">RM ${total.toFixed(2)}</span>

                </div>

            </div>

        </div>

        

        <div class="whats-next">

            <div class="whats-next-title">

                <i class="fas fa-clock"></i>

                What's Next?

            </div>

            <ul class="whats-next-list">

                <li>

                    <i class="fas fa-check-circle"></i>

                    <span>You'll receive an order confirmation email at <strong>${meta.email || 'your email'}</strong></span>

                </li>

                <li>

                    <i class="fas fa-truck"></i>

                    <span>Your order will be processed and shipped within 1-2 business days</span>

                </li>

                <li>

                    <i class="fas fa-box"></i>

                    <span>Expected delivery: 3-5 business days from shipment</span>

                </li>

            </ul>

        </div>

        

        <div class="action-buttons" style="justify-content: center;">

            <a href="index.php" class="btn btn-primary">

                <i class="fas fa-home"></i>

                Back to Main Menu

            </a>

        </div>

    `;

    

    $details.html(html);

    

    // Helper functions

    function getPaymentMethodLabel(method) {
        const key = (method || '').toString().trim().toLowerCase();
        if (!key) return 'Unknown payment method';
        const labels = {
            'card': 'Credit/Debit Card',
            'credit_card': 'Credit/Debit Card',
            'debit_card': 'Credit/Debit Card',
            'fpx': 'FPX Online Banking',
            'ewallet': 'E-Wallet (GrabPay, Touch n Go)',
            'e-wallet': 'E-Wallet (GrabPay, Touch n Go)',
            'bank_transfer': 'Bank Transfer',
            'cash': 'Cash on Delivery',
            'cod': 'Cash on Delivery',
            'paypal': 'PayPal'
        };
        return labels[key] || key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    }

    

    function formatDate(timestamp) {

        if (!timestamp) return 'N/A';

        const date = new Date(timestamp);

        return date.toLocaleDateString('en-MY', {

            year: 'numeric',

            month: 'long',

            day: 'numeric',

            hour: '2-digit',

            minute: '2-digit'

        });

    }

});

</script>



<?php include '_foot.php'; ?>
