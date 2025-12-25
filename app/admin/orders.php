<?php
require __DIR__ . '/_auth.php';
require __DIR__ . '/../_base.php';

global $db;
if (!isset($db)) {
    $db = require __DIR__ . '/../lib/db.php';
}

$_title = 'Admin - All Orders';
$_bodyClass = 'transparent-header-page';

// Get filters
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$sortBy = $_GET['sort'] ?? 'date_desc';

// Build query
$query = 'SELECT 
    o.id, o.total_amount, o.status, o.created_at,
    u.id as user_id, u.name as user_name, u.email
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE 1=1';

$params = [];

if (!empty($search)) {
    $query .= ' AND (o.id LIKE ? OR u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)';
    $searchTerm = '%' . $search . '%';
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

if (!empty($status)) {
    $query .= ' AND o.status = ?';
    $params[] = $status;
}

// Add ordering
if ($sortBy === 'amount_high') {
    $query .= ' ORDER BY o.total_amount DESC';
} elseif ($sortBy === 'amount_low') {
    $query .= ' ORDER BY o.total_amount ASC';
} elseif ($sortBy === 'date_asc') {
    $query .= ' ORDER BY o.created_at ASC';
} else { // date_desc default
    $query .= ' ORDER BY o.created_at DESC';
}

$stmt = $db->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get status counts
$statusCounts = [];
$countStmt = $db->prepare('SELECT status, COUNT(*) as cnt FROM orders GROUP BY status');
$countStmt->execute();
while ($row = $countStmt->fetch(PDO::FETCH_ASSOC)) {
    $statusCounts[strtolower($row['status'])] = $row['cnt'];
}

// Get total count
$totalCount = 0;
$totalStmt = $db->prepare('SELECT COUNT(*) as cnt FROM orders');
$totalStmt->execute();
$totalRow = $totalStmt->fetch(PDO::FETCH_ASSOC);
$totalCount = $totalRow['cnt'] ?? 0;

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
    max-width: 1200px;
    margin: 20px auto 40px;
    padding: 28px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(34,34,34,0.06);
    color: #222;
}

.stat-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 16px;
    margin-bottom: 30px;
}

.stat-card {
    padding: 20px;
    background: #f9fafb;
    border-radius: 10px;
    border-left: 4px solid #ff6f00;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}

.stat-card:hover {
    background: #fff8f0;
    border-left-color: #d97700;
    box-shadow: 0 2px 8px rgba(255, 111, 0, 0.1);
}

.stat-card.active {
    background: #ffe8cc;
    border-left-color: #ff6f00;
}

.stat-count {
    font-size: 1.8rem;
    font-weight: 700;
    color: #ff6f00;
}

.stat-label {
    font-size: 0.85rem;
    color: #666;
    margin-top: 6px;
    text-transform: uppercase;
    font-weight: 600;
}

.filters-section {
    background: #f5f7fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 24px;
}

.filters-section h3 {
    margin: 0 0 16px 0;
    font-size: 1rem;
    color: #333;
}

.filter-row {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: flex-end;
}

.form-group {
    flex: 1;
    min-width: 200px;
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

.btn-secondary {
    padding: 10px 24px;
    background: #e8ecf1;
    color: #333;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-secondary:hover {
    background: #d8dce5;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table thead {
    background: #f5f7fa;
    border-bottom: 2px solid #e8ecf1;
}

.orders-table th {
    padding: 14px 12px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 0.85rem;
    text-transform: uppercase;
}

.orders-table td {
    padding: 14px 12px;
    border-bottom: 1px solid #e8ecf1;
}

.orders-table tbody tr:hover {
    background: #f9fafb;
}

.order-id {
    font-weight: 700;
    color: #ff6f00;
}

.order-amount {
    font-weight: 600;
    color: #1a1a1a;
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-processing { background: #d1ecf1; color: #0c5460; }
.status-shipped { background: #cfe2ff; color: #084298; }
.status-delivered { background: #d1e7dd; color: #0f5132; }

.view-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: #f0f4f8;
    color: #0066cc;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s;
}

.view-btn:hover {
    background: #e0e8f0;
    color: #0052a3;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 12px;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .orders-table {
        font-size: 0.85rem;
    }
    .orders-table th,
    .orders-table td {
        padding: 10px 8px;
    }
    .filter-row {
        flex-direction: column;
    }
    .form-group {
        min-width: auto;
    }
}
</style>

<div class="brand-header">
    <h1><i class="fas fa-shopping-cart" style="margin-right: 12px;"></i>All Orders</h1>
    <p>Manage and track customer orders</p>
</div>

<div class="content">
    <!-- Statistics -->
    <div class="stat-cards">
        <a href="orders.php" class="stat-card <?= empty($status) ? 'active' : '' ?>">
            <div class="stat-count"><?= $totalCount ?></div>
            <div class="stat-label">Total Orders</div>
        </a>
        <a href="orders.php?status=pending" class="stat-card <?= $status === 'pending' ? 'active' : '' ?>">
            <div class="stat-count"><?= $statusCounts['pending'] ?? 0 ?></div>
            <div class="stat-label">Pending</div>
        </a>
        <a href="orders.php?status=processing" class="stat-card <?= $status === 'processing' ? 'active' : '' ?>">
            <div class="stat-count"><?= $statusCounts['processing'] ?? 0 ?></div>
            <div class="stat-label">Processing</div>
        </a>
        <a href="orders.php?status=shipped" class="stat-card <?= $status === 'shipped' ? 'active' : '' ?>">
            <div class="stat-count"><?= $statusCounts['shipped'] ?? 0 ?></div>
            <div class="stat-label">Shipped</div>
        </a>
        <a href="orders.php?status=delivered" class="stat-card <?= $status === 'delivered' ? 'active' : '' ?>">
            <div class="stat-count"><?= $statusCounts['delivered'] ?? 0 ?></div>
            <div class="stat-label">Delivered</div>
        </a>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <h3><i class="fas fa-filter" style="margin-right: 8px;"></i>Search & Filter</h3>
        <form method="get" class="filter-row">
            <div class="form-group" style="flex: 2; min-width: 250px;">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Order ID, name, email, or phone..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Sort By</label>
                <select name="sort" class="form-control">
                    <option value="date_desc" <?= $sortBy === 'date_desc' ? 'selected' : '' ?>>Latest First</option>
                    <option value="date_asc" <?= $sortBy === 'date_asc' ? 'selected' : '' ?>>Oldest First</option>
                    <option value="amount_high" <?= $sortBy === 'amount_high' ? 'selected' : '' ?>>Highest Amount</option>
                    <option value="amount_low" <?= $sortBy === 'amount_low' ? 'selected' : '' ?>>Lowest Amount</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
            <?php if (!empty($search) || !empty($status)): ?>
                <a href="orders.php" class="btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Orders Table -->
    <?php if (!empty($orders)): ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="order-id">#<?= $order['id'] ?></td>
                        <td>
                            <div style="font-weight: 600; color: #1a1a1a;"><?= htmlspecialchars($order['user_name']) ?></div>
                            <div style="font-size: 0.85rem; color: #666;"><?= htmlspecialchars($order['email']) ?></div>
                        </td>
                        <td class="order-amount">RM <?= number_format($order['total_amount'], 2) ?></td>
                        <td>
                            <span class="status-badge status-<?= strtolower($order['status']) ?>">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                        <td>
                            <a href="order_detail.php?id=<?= $order['id'] ?>" class="view-btn">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No orders found</p>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/foot.php'; ?>
