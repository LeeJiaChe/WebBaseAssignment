<?php
require __DIR__ . '/_auth.php';
require __DIR__ . '/../_base.php';

global $db;
if (!isset($db)) {
    $db = require __DIR__ . '/../lib/db.php';
}

$_title = 'Admin - Member Details';
$_bodyClass = 'transparent-header-page';

$memberId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($memberId <= 0) {
    header('Location: members.php');
    exit;
}

// Get member info
$stmt = $db->prepare('SELECT id, name, email, phone, role, photo, created_at FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$memberId]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    header('Location: members.php');
    exit;
}

// Handle role update
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_role') {
        $newRole = $_POST['role'] ?? '';
        if (in_array($newRole, ['user', 'admin'])) {
            try {
                $updateStmt = $db->prepare('UPDATE users SET role = ? WHERE id = ?');
                $updateStmt->execute([$newRole, $memberId]);
                $message = 'Role updated successfully';
                $member['role'] = $newRole;
            } catch (Exception $e) {
                $error = 'Error updating role: ' . $e->getMessage();
            }
        }
    }
}

// Get member's order stats
$orderStmt = $db->prepare('SELECT COUNT(*) as total, SUM(total_amount) as spent FROM orders WHERE user_id = ?');
$orderStmt->execute([$memberId]);
$orderStats = $orderStmt->fetch(PDO::FETCH_ASSOC);

// Get recent orders
$recentStmt = $db->prepare('SELECT id, total_amount, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
$recentStmt->execute([$memberId]);
$recentOrders = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

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

.member-header {
    display: flex;
    gap: 24px;
    align-items: flex-start;
    margin-bottom: 30px;
    padding-bottom: 30px;
    border-bottom: 2px solid #f0f4f8;
}

.member-photo-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #ff6f00;
    flex-shrink: 0;
}

.member-photo-empty {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #f0f4f8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: #999;
    border: 3px solid #e8ecf1;
    flex-shrink: 0;
}

.member-header-info h1 {
    margin: 0 0 8px 0;
    font-size: 1.8rem;
}

.member-header-info .meta {
    display: flex;
    gap: 16px;
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

.role-badge {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-top: 12px;
}

.role-admin {
    background: #ffe8cc;
    color: #c86400;
}

.role-user {
    background: #d1ecf1;
    color: #0c5460;
}

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
    margin-bottom: 6px;
}

.info-value {
    font-size: 1rem;
    color: #1a1a1a;
    font-weight: 600;
}

.form-group {
    margin-bottom: 16px;
}

.form-label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #333;
    font-size: 0.9rem;
}

.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.95rem;
    max-width: 300px;
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

.orders-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
}

.orders-table thead {
    background: #f5f7fa;
    border-bottom: 2px solid #e8ecf1;
}

.orders-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 0.85rem;
}

.orders-table td {
    padding: 12px;
    border-bottom: 1px solid #e8ecf1;
}

.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 16px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-processing { background: #d1ecf1; color: #0c5460; }
.status-shipped { background: #cfe2ff; color: #084298; }
.status-delivered { background: #d1e7dd; color: #0f5132; }

.no-orders {
    padding: 20px;
    text-align: center;
    color: #999;
}

@media (max-width: 768px) {
    .member-header { flex-direction: column; }
    .info-grid { grid-template-columns: 1fr; }
}
</style>

<div class="brand-header">
    <h1><i class="fas fa-user-circle" style="margin-right: 12px;"></i>Member Details</h1>
    <p>View and manage member information</p>
</div>

<div class="content">
    <a href="members.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Members
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

    <!-- Member Header -->
    <div class="member-header">
        <?php if (!empty($member['photo'])): ?>
            <img src="<?= htmlspecialchars($member['photo']) ?>" alt="<?= htmlspecialchars($member['name']) ?>" class="member-photo-large">
        <?php else: ?>
            <div class="member-photo-empty">
                <i class="fas fa-user"></i>
            </div>
        <?php endif; ?>
        
        <div class="member-header-info">
            <h1><?= htmlspecialchars($member['name']) ?></h1>
            <span class="role-badge role-<?= strtolower($member['role']) ?>">
                <?= htmlspecialchars(ucfirst($member['role'])) ?>
            </span>
            
            <div class="meta">
                <div class="meta-item">
                    <i class="fas fa-envelope"></i>
                    <?= htmlspecialchars($member['email']) ?>
                </div>
                <?php if (!empty($member['phone'])): ?>
                    <div class="meta-item">
                        <i class="fas fa-phone"></i>
                        <?= htmlspecialchars($member['phone']) ?>
                    </div>
                <?php endif; ?>
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    Joined <?= date('M d, Y', strtotime($member['created_at'])) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Information -->
    <div class="section">
        <div class="section-title">
            <i class="fas fa-id-card"></i>
            Member Information
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Full Name</div>
                <div class="info-value"><?= htmlspecialchars($member['name']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Email Address</div>
                <div class="info-value"><?= htmlspecialchars($member['email']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Phone Number</div>
                <div class="info-value"><?= !empty($member['phone']) ? htmlspecialchars($member['phone']) : '—' ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Member Since</div>
                <div class="info-value"><?= date('M d, Y', strtotime($member['created_at'])) ?></div>
            </div>
        </div>
    </div>

    <!-- Role Management -->
    <div class="section">
        <div class="section-title">
            <i class="fas fa-shield-alt"></i>
            Role Management
        </div>
        
        <form method="post">
            <input type="hidden" name="action" value="update_role">
            
            <div class="form-group">
                <label class="form-label">Current Role</label>
                <select name="role" class="form-control">
                    <option value="user" <?= $member['role'] === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $member['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                </select>
            </div>
            
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Update Role
            </button>
        </form>
    </div>

    <!-- Order Statistics -->
    <div class="section">
        <div class="section-title">
            <i class="fas fa-shopping-bag"></i>
            Order History
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Total Orders</div>
                <div class="info-value"><?= (int)($orderStats['total'] ?? 0) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Total Spent</div>
                <div class="info-value">RM <?= number_format($orderStats['spent'] ?? 0, 2) ?></div>
            </div>
        </div>

        <?php if (!empty($recentOrders)): ?>
            <h3 style="margin: 20px 0 12px 0; font-size: 1rem;">Recent Orders</h3>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>
                                <a href="order_detail.php?id=<?= $order['id'] ?>" style="color: #0066cc; text-decoration: none; font-weight: 600;">
                                    #<?= $order['id'] ?>
                                </a>
                            </td>
                            <td>RM <?= number_format($order['total_amount'], 2) ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower($order['status']) ?>">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-orders">
                <i class="fas fa-box-open" style="font-size: 2rem; margin-bottom: 8px;"></i>
                <p>No orders yet</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/foot.php'; ?>
