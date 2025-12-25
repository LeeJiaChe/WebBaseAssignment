<?php
require __DIR__ . '/_auth.php';
require __DIR__ . '/../_base.php';

global $db;
if (!isset($db)) {
    $db = require __DIR__ . '/../lib/db.php';
}

$_title = 'Admin - Order Details';
$_bodyClass = 'transparent-header-page';

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    header('Location: orders.php');
    exit;
}

// Get order info
$stmt = $db->prepare('SELECT 
    o.id, o.user_id, o.total_amount, o.status, o.payment_method, 
    o.shipping_address, o.phone, o.notes, o.created_at, o.updated_at,
    u.name as user_name, u.email, u.phone as user_phone
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE o.id = ? LIMIT 1');
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Get order items
$itemsStmt = $db->prepare('SELECT 
    oi.id, oi.product_id, oi.quantity, oi.unit_price,
    p.name as product_name, p.image_path
FROM order_items oi
JOIN products p ON oi.product_id = p.id
WHERE oi.order_id = ?
ORDER BY oi.id ASC');
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status update
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $newStatus = $_POST['status'] ?? '';
        if (in_array($newStatus, ['pending', 'processing', 'shipped', 'delivered'])) {
            try {
                $updateStmt = $db->prepare('UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?');
                $updateStmt->execute([$newStatus, $orderId]);
                $message = 'Order status updated successfully';
                $order['status'] = $newStatus;
            } catch (Exception $e) {
                $error = 'Error updating status: ' . $e->getMessage();
            }
        }
    }
}

function getPaymentMethodLabel($method) {
    $method = trim((string)$method);
    if ($method === '') {
        return 'Unknown payment method';
    }
    $key = strtolower($method);
    $methods = [
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
    if (isset($methods[$key])) {
        return $methods[$key];
    }
    // Fallback: prettify unknown values
    return ucfirst(str_replace('_', ' ', $key));
}

require __DIR__ . '/head.php';
?>

<style>
.brand-header {
    background: linear-gradient(135deg, #333333 0%, #8c8c8c 100%);
    color: white;
    padding: 135px 0 50px 0;
    margin: -20px -50px 30px -50px;
    text-align: center;
}
.brand-header h1 { margin: 0; font-size: 2.5rem; }
.brand-header p { margin: 10px 0 0 0; font-size: 1.1rem; opacity: 0.9; }

.content {
    max-width: 1000px;
    margin: 20px auto 40px;
    padding: 28px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(34,34,34,0.06);
    color: #222;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    padding: 8px 16px;
    background: #f0f4f8;
    color: #333;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.2s;
}

.back-button:hover {
    background: #e0e8f0;
}

.alert {
    padding: 14px 18px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert-success {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
}

.alert-error {
    background: #f8d7da;
    color: #842029;
    border: 1px solid #f5c2c7;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f4f8;
}

.order-header-left h1 {
    margin: 0 0 8px 0;
    font-size: 1.8rem;
    color: #1a1a1a;
}

.order-meta {
    display: flex;
    gap: 20px;
    margin-top: 12px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #666;
    font-size: 0.9rem;
}

.meta-item i {
    color: #ff6f00;
    width: 16px;
}

.status-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-processing { background: #d1ecf1; color: #0c5460; }
.status-shipped { background: #cfe2ff; color: #084298; }
.status-delivered { background: #d1e7dd; color: #0f5132; }

.section {
    margin-bottom: 30px;
}

.section-title {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #1a1a1a;
}

.section-title i {
    color: #ff6f00;
    font-size: 1.1rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.info-item {
    padding: 16px;
    background: #f9fafb;
    border-radius: 10px;
    border: 1px solid #e8ecf1;
}

.info-label {
    font-size: 0.8rem;
    color: #999;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 8px;
}

.info-value {
    font-size: 1rem;
    color: #1a1a1a;
    font-weight: 600;
    word-break: break-word;
}

.form-group {
    display: flex;
    gap: 12px;
    align-items: flex-end;
}

.form-label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #333;
    font-size: 0.9rem;
}

.form-control {
    padding: 10px 14px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.95rem;
    min-width: 200px;
}

.btn-primary {
    padding: 10px 24px;
    background: linear-gradient(135deg, #ff8f00 0%, #ff6f00 100%);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #d97700 0%, #c86400 100%);
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 16px;
}

.items-table thead {
    background: #f5f7fa;
    border-bottom: 2px solid #e8ecf1;
}

.items-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 0.85rem;
    text-transform: uppercase;
}

.items-table td {
    padding: 12px;
    border-bottom: 1px solid #e8ecf1;
}

.items-table tbody tr:hover {
    background: #f9fafb;
}

.product-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
}

.product-info {
    display: flex;
    gap: 12px;
    align-items: center;
}

.product-name {
    font-weight: 600;
    color: #1a1a1a;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-top: 20px;
}

.summary-item {
    padding: 16px;
    background: #fff8f0;
    border-radius: 10px;
    border: 2px solid #ffe8cc;
}

.summary-label {
    font-size: 0.85rem;
    color: #666;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 8px;
}

.summary-value {
    font-size: 1.3rem;
    font-weight: 700;
    color: #ff6f00;
}

@media (max-width: 768px) {
    .order-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .form-group {
        flex-direction: column;
        align-items: flex-start;
    }
    .form-control {
        width: 100%;
        min-width: auto;
    }
}
</style>

<div class="brand-header">
    <h1><i class="fas fa-receipt" style="margin-right: 12px;"></i>Order Details</h1>
    <p>View and manage order information</p>
</div>

<div class="content">
    <a href="orders.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>

    <?php if ($message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Order Header -->
    <div class="order-header">
        <div class="order-header-left">
            <h1>Order #<?= $order['id'] ?></h1>
            <div class="order-meta">
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <?= date('M d, Y g:i A', strtotime($order['created_at'])) ?>
                </div>
                <div class="meta-item">
                    <i class="fas fa-user"></i>
                    <?= htmlspecialchars($order['user_name']) ?>
                </div>
            </div>
        </div>
        <span class="status-badge status-<?= strtolower($order['status']) ?>">
            <?= htmlspecialchars(ucfirst($order['status'])) ?>
        </span>
    </div>

    <!-- Customer Information -->
    <div class="section">
        <div class="section-title">
            <i class="fas fa-user-circle"></i>
            Customer Information
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Full Name</div>
                <div class="info-value"><?= htmlspecialchars($order['user_name']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value"><?= htmlspecialchars($order['email']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Phone</div>
                <div class="info-value"><?= htmlspecialchars($order['phone'] ?? $order['user_phone'] ?? '—') ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Shipping Address</div>
                <div class="info-value"><?= htmlspecialchars($order['shipping_address'] ?? '—') ?></div>
            </div>
        </div>
    </div>

    <!-- Order Status Management -->
    <div class="section">
        <div class="section-title">
            <i class="fas fa-tasks"></i>
            Order Status
        </div>
        
        <form method="post" class="form-group">
            <input type="hidden" name="action" value="update_status">
            
            <div>
                <label class="form-label">Update Status</label>
                <select name="status" class="form-control">
                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                    <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                    <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
        </form>
    </div>

    <!-- Payment Information -->
    <div class="section">
        <div class="section-title">
            <i class="fas fa-credit-card"></i>
            Payment Information
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Payment Method</div>
                <div class="info-value"><?= getPaymentMethodLabel($order['payment_method']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Order Total</div>
                <div class="info-value">RM <?= number_format($order['total_amount'], 2) ?></div>
            </div>
            <?php if (!empty($order['notes'])): ?>
                <div class="info-item">
                    <div class="info-label">Notes</div>
                    <div class="info-value"><?= htmlspecialchars($order['notes']) ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Order Items -->
    <div class="section">
        <div class="section-title">
            <i class="fas fa-shopping-bag"></i>
            Order Items (<?= count($items) ?>)
        </div>
        
        <?php if (!empty($items)): ?>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div class="product-info">
                                    <?php if (!empty($item['image_path'])): ?>
                                        <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="" class="product-image">
                                    <?php else: ?>
                                        <div class="product-image" style="background: #f0f4f8; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image" style="color: #999;"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="product-name"><?= htmlspecialchars($item['product_name']) ?></span>
                                </div>
                            </td>
                            <td><?= (int)$item['quantity'] ?></td>
                            <td>RM <?= number_format($item['unit_price'], 2) ?></td>
                            <td><strong>RM <?= number_format($item['quantity'] * $item['unit_price'], 2) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Order Summary -->
            <?php
                $shippingCost = 10.00;
                $computedSubtotal = max(($order['total_amount'] ?? 0) - $shippingCost, 0);
            ?>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Subtotal</div>
                    <div class="summary-value">RM <?= number_format($computedSubtotal, 2) ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Shipping</div>
                    <div class="summary-value">RM <?= number_format($shippingCost, 2) ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Grand Total</div>
                    <div class="summary-value">RM <?= number_format($order['total_amount'], 2) ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/foot.php'; ?>
