<?php
/**
 * Order Detail Page
 * Displays details for a specific order
 */

require '_base.php';
require_once __DIR__ . '/lib/orders.php';

$_title = 'Order Details';

// Check if user is logged in
if (empty($_SESSION['user_id'])) {
    header('Location: login.php?redirect=orders.php');
    exit;
}

// Get order ID from URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    header('Location: orders.php');
    exit;
}

// Handle order cancellation
$cancel_message = null;
$cancel_error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    try {
        // Fetch order to verify ownership and status
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$order_id, $_SESSION['user_id']]);
        $order_to_cancel = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order_to_cancel) {
            $cancel_error = 'Order not found or you do not have permission to cancel it.';
        } elseif ($order_to_cancel['status'] === 'cancelled') {
            $cancel_error = 'This order has already been cancelled.';
        } elseif (in_array($order_to_cancel['status'], ['shipped', 'delivered', 'completed'])) {
            $cancel_error = 'This order cannot be cancelled as it has already been ' . $order_to_cancel['status'] . '.';
        } else {
            // Update order status to cancelled
            $update_stmt = $db->prepare("UPDATE orders SET status = 'cancelled', updated_at = NOW() WHERE id = ? AND user_id = ?");
            $update_stmt->execute([$order_id, $_SESSION['user_id']]);
            
            $cancel_message = 'Order has been successfully cancelled.';
            
            // Refresh the page to show updated status
            header('Location: order_detail.php?id=' . $order_id . '&cancelled=1');
            exit;
        }
    } catch (Exception $e) {
        error_log('Error cancelling order: ' . $e->getMessage());
        $cancel_error = 'Error cancelling order. Please try again later.';
    }
}

// Check for cancellation success message from redirect
if (isset($_GET['cancelled']) && $_GET['cancelled'] == 1) {
    $cancel_message = 'Order has been successfully cancelled.';
}

include '_head.php';

// Helper function to get full payment method name
function getPaymentMethodLabel($method) {
    $method = trim((string)$method);
    if ($method === '') {
        return 'Unknown payment method';
    }
    $key = strtolower($method);
    $labels = [
        'fpx' => 'FPX Online Banking',
        'credit_card' => 'Credit / Debit Card',
        'debit_card' => 'Credit / Debit Card',
        'card' => 'Credit / Debit Card',
        'ewallet' => 'E-Wallet (GrabPay, Touch n Go)',
        'e-wallet' => 'E-Wallet (GrabPay, Touch n Go)',
        'bank_transfer' => 'Bank Transfer',
        'cash' => 'Cash on Delivery',
        'cod' => 'Cash on Delivery',
        'paypal' => 'PayPal'
    ];
    if (isset($labels[$key])) {
        return $labels[$key];
    }
    // Fallback: prettify unknown values
    return ucfirst(str_replace('_', ' ', $key));
}

// Fetch order information
$order = null;
$order_items = [];
$error_message = null;

try {
    // Get main order record
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        $error_message = 'Order not found or you do not have permission to view it.';
    } else {
        // Get order items
        $order_items = get_order_details($db, $order_id);
    }
} catch (Exception $e) {
    error_log('Error in order_detail.php: ' . $e->getMessage());
    $error_message = 'Error loading order details. Please try again later.';
}

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
}

.order-detail-container {
    padding: 60px 20px;
    max-width: 1000px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 30px;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #f5f5f5;
    color: #333;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.2s;
}

.back-button:hover {
    background: #e0e0e0;
}

.page-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
}

.alert {
    padding: 16px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.alert-error {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.order-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 20px;
}

.order-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.summary-item {
    padding: 16px;
    background: #f9f9f9;
    border-radius: 8px;
}

.summary-label {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 6px;
}

.summary-value {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a1a;
}

.order-status {
    font-weight: 600;
    font-size: 0.9rem;
    padding: 6px 12px;
    border-radius: 6px;
    text-transform: uppercase;
    display: inline-block;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-processing {
    background-color: #d1ecf1;
    color: #0c5460;
}

.status-shipped {
    background-color: #cfe2ff;
    color: #084298;
}

.status-delivered {
    background-color: #d1e7dd;
    color: #0f5132;
}

.status-cancelled {
    background-color: #f8d7da;
    color: #842029;
}

.cancel-order-section {
    margin-top: 30px;
    padding: 20px;
    background: #fff8f0;
    border: 1px solid #ffe8cc;
    border-radius: 8px;
}

.cancel-order-btn {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.cancel-order-btn:hover {
    background-color: #c82333;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220,53,69,0.3);
}

.cancel-order-btn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
    transform: none;
}

.section-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: #ff6f00;
}

.items-list {
    border-top: 1px solid #f0f0f0;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px 0;
    border-bottom: 1px solid #f0f0f0;
}

.order-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 80px;
    height: 80px;
    object-fit: contain;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 8px;
    background: white;
}

.item-details {
    flex: 1;
}

.item-name {
    font-weight: 600;
    color: #1a1a1a;
    font-size: 1rem;
    margin-bottom: 8px;
}

.item-meta {
    color: #666;
    font-size: 0.9rem;
}

.item-price {
    text-align: right;
}

.item-unit-price {
    color: #666;
    font-size: 0.85rem;
    margin-bottom: 4px;
}

.item-total-price {
    font-weight: 700;
    color: #ff6f00;
    font-size: 1.1rem;
}

.order-total {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #f0f0f0;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 20px;
}

.total-label {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1a1a1a;
}

.total-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ff6f00;
}

@media (max-width: 768px) {
    .order-detail-container {
        padding: 40px 15px;
    }

    .page-title {
        font-size: 1.4rem;
    }

    .order-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .item-price {
        text-align: left;
        width: 100%;
    }

    .order-summary {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="order-detail-container">
    <div class="page-header">
        <a href="orders.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
        <h1 class="page-title">Order #<?= htmlspecialchars($order_id) ?></h1>
    </div>

    <?php if ($error_message): ?>
        <div class="alert alert-error">
            <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
        </div>
        <a href="orders.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Return to Order History
        </a>
    <?php elseif ($order): ?>
        <!-- Success/Error Messages -->
        <?php if ($cancel_message): ?>
            <div class="alert" style="background-color: #d1e7dd; border: 1px solid #badbcc; color: #0f5132; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($cancel_message) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($cancel_error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($cancel_error) ?>
            </div>
        <?php endif; ?>
        
        <div class="order-card">
            <!-- Order Summary -->
            <div class="order-summary">
                <div class="summary-item">
                    <div class="summary-label">Order Date</div>
                    <div class="summary-value">
                        <?= date('M d, Y', strtotime($order['created_at'])) ?><br>
                        <span style="font-size: 0.85rem; font-weight: 400; color: #666;">
                            <?= date('H:i', strtotime($order['created_at'])) ?>
                        </span>
                    </div>
                </div>
                
                <div class="summary-item">
                    <div class="summary-label">Status</div>
                    <div class="summary-value">
                        <span class="order-status status-<?= strtolower($order['status']) ?>">
                            <?= htmlspecialchars(strtoupper($order['status'])) ?>
                        </span>
                    </div>
                </div>
                
                <div class="summary-item">
                    <div class="summary-label">Payment Method</div>
                    <div class="summary-value">
                        <?= htmlspecialchars(getPaymentMethodLabel($order['payment_method'])) ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($order['shipping_address'])): ?>
                <div style="margin-bottom: 30px; padding: 16px; background: #f9f9f9; border-radius: 8px;">
                    <div class="summary-label" style="margin-bottom: 10px;">Shipping Address</div>
                    <div style="color: #333; line-height: 1.6;">
                        <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>
                        <?php if (!empty($order['phone'])): ?>
                            <br><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Cancel Order Section -->
            <?php if (in_array(strtolower($order['status']), ['pending', 'processing'])): ?>
                <div class="cancel-order-section">
                    <h3 style="margin: 0 0 12px 0; font-size: 16px; color: #333;">
                        <i class="fas fa-times-circle"></i> Cancel This Order
                    </h3>
                    <p style="margin: 0 0 16px 0; color: #666; font-size: 14px;">
                        You can cancel this order as it hasn't been shipped yet. Once cancelled, this action cannot be undone.
                    </p>
                    <form method="post" onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                        <input type="hidden" name="cancel_order" value="1">
                        <button type="submit" class="cancel-order-btn">
                            <i class="fas fa-ban"></i> Cancel Order
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Order Items -->
            <div class="section-title">
                <i class="fas fa-box-open"></i>
                Order Items
            </div>

            <div class="items-list">
                <?php if (empty($order_items)): ?>
                    <p style="color: #999; padding: 20px 0;">No items found for this order.</p>
                <?php else: ?>
                    <?php 
                    $subtotal = 0;
                    foreach ($order_items as $item): 
                        $item_total = $item['unit_price'] * $item['quantity'];
                        $subtotal += $item_total;
                    ?>
                        <div class="order-item">
                            <?php if (!empty($item['image_path'])): ?>
                                <img src="<?= htmlspecialchars($item['image_path']) ?>" 
                                     alt="<?= htmlspecialchars($item['name']) ?>" 
                                     class="item-image">
                            <?php else: ?>
                                <div class="item-image" style="display: flex; align-items: center; justify-content: center; background: #f5f5f5;">
                                    <i class="fas fa-image" style="font-size: 2rem; color: #ccc;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="item-details">
                                <div class="item-name"><?= htmlspecialchars($item['name'] ?: 'Product') ?></div>
                                <div class="item-meta">
                                    Quantity: <?= (int)$item['quantity'] ?>
                                </div>
                            </div>
                            
                            <div class="item-price">
                                <div class="item-unit-price">
                                    RM <?= number_format($item['unit_price'], 2) ?> each
                                </div>
                                <div class="item-total-price">
                                    RM <?= number_format($item_total, 2) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Order Total -->
                    <div class="order-total">
                        <span class="total-label">Total Amount:</span>
                        <span class="total-value">RM <?= number_format($order['total_amount'], 2) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '_foot.php'; ?>
