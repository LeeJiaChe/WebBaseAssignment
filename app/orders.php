<?php
/**
 * 订单历史页面
 * 路径：app/orders.php
 *
 * 显示当前登录用户的所有订单列表
 */

require '_base.php';
require_once __DIR__ . '/lib/orders.php';

$_title = 'My Orders';

// ===== 检查用户是否登录 =====
if (empty($_SESSION['user_id'])) {
    header('Location: login.php?redirect=orders.php');
    exit;
}

include '_head.php';

// ===== 获取订单列表 =====
$orders = [];
$error_message = null;

try {
    $orders = get_user_orders($db, $_SESSION['user_id']);

    if (empty($orders)) {
        // 这不是错误，只是用户还没有订单
        $message = 'You haven\'t placed any orders yet.';
    }
} catch (Exception $e) {
    error_log('Error in orders.php: ' . $e->getMessage());
    $error_message = 'Error loading orders. Please try again later.';
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

.orders-container {
    padding: 60px 20px;
    max-width: 1000px;
    margin: 0 auto;
}

.orders-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 30px;
    color: #1a1a1a;
    display: flex;
    align-items: center;
    gap: 12px;
}

.orders-title i {
    color: #ff6f00;
    font-size: 1.5rem;
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

.alert-info {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state-icon {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 20px;
}

.empty-state-text {
    color: #999;
    font-size: 1.1rem;
    margin-bottom: 20px;
}

.empty-state-link {
    display: inline-block;
    padding: 10px 20px;
    background: linear-gradient(135deg, #ff8f00 0%, #ff6f00 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: transform 0.2s;
}

.empty-state-link:hover {
    transform: translateY(-2px);
}

.order-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 16px;
    padding: 28px;
    margin-bottom: 24px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.order-card:hover {
    box-shadow: 0 8px 24px rgba(255, 111, 0, 0.15);
    border-color: #ff6f00;
    transform: translateY(-4px);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #f5f5f5;
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.order-id {
    font-weight: 700;
    color: #1a1a1a;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.order-id i {
    color: #ff6f00;
    font-size: 1rem;
}

.order-status {
    font-weight: 600;
    font-size: 0.9rem;
    padding: 6px 12px;
    border-radius: 6px;
    text-transform: uppercase;
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

.order-details {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 24px;
    align-items: center;
}

.order-info {
    flex: 1;
}

.order-info p {
    margin: 10px 0;
    color: #666;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.order-info p i {
    color: #ff6f00;
    width: 16px;
    text-align: center;
}

.order-info p strong {
    color: #1a1a1a;
    margin-right: 6px;
}

.order-actions {
    text-align: right;
}

.btn-view {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #ff8f00 0%, #ff6f00 100%);
    color: white;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 111, 0, 0.2);
}

.btn-view:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 111, 0, 0.4);
    background: linear-gradient(135deg, #ff6f00 0%, #ff5000 100%);
}

@media (max-width: 768px) {
    .orders-container {
        padding: 60px 15px;
    }

    .orders-title {
        font-size: 1.5rem;
    }

    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .order-details {
        flex-direction: column;
        gap: 20px;
    }

    .order-actions {
        text-align: left;
    }
}
</style>

<div class="orders-container">
    <h1 class="orders-title">
        <i class="fas fa-history"></i>
        Order History
    </h1>

    <!-- 错误提示 -->
    <?php if ($error_message): ?>
        <div class="alert alert-error">
            <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <!-- 空状态 -->
    <?php if (empty($orders) && !$error_message): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-box-open"></i>
            </div>
            <p class="empty-state-text">You haven't placed any orders yet.</p>
            <a href="index.php" class="empty-state-link">
                <i class="fas fa-shopping-cart"></i> Start Shopping
            </a>
        </div>
    <?php endif; ?>

    <!-- 订单列表 -->
    <?php if (!empty($orders)): ?>
        <div style="color: #666; margin-bottom: 20px;">
            Found <strong><?= count($orders) ?></strong> order(s)
        </div>

        <?php foreach ($orders as $order):
            // 根据状态设置样式类
            $status = strtolower($order['status'] ?? 'pending');
            $status_class = "status-" . $status;
            ?>
            <div class="order-card">
                <div class="order-header">
                    <span class="order-id">
                        <i class="fas fa-receipt"></i>
                        Order #<?= htmlspecialchars($order['id']) ?>
                    </span>
                    <span class="order-status <?= $status_class ?>">
                        <?= htmlspecialchars(strtoupper($status)) ?>
                    </span>
                </div>

                <div class="order-details">
                    <div class="order-info">
                        <p>
                            <i class="fas fa-calendar-alt"></i>
                            <strong>Date:</strong>
                            <?= date('M d, Y H:i', strtotime($order['created_at'])) ?>
                        </p>
                        <p>
                            <i class="fas fa-money-bill-wave"></i>
                            <strong>Total:</strong>
                            RM <?= number_format($order['total_amount'], 2) ?>
                        </p>
                    </div>
                    <div class="order-actions">
                        <a href="order_detail.php?id=<?= urlencode($order['id']) ?>" class="btn-view">
                            <i class="fas fa-eye"></i>
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '_foot.php'; ?>
