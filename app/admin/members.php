<?php
require __DIR__ . '/_auth.php';
require __DIR__ . '/../_base.php';

global $db;
if (!isset($db)) {
    $db = require __DIR__ . '/../lib/db.php';
}

$_title = 'Admin - Members';
$_bodyClass = 'transparent-header-page';

// Get search query and filter
$q = trim($_GET['q'] ?? '');
$roleFilter = trim($_GET['role'] ?? '');

$sql = 'SELECT id, name, email, role, phone, photo, created_at FROM users WHERE 1=1';
$params = [];

if ($q !== '') {
    $sql .= ' AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)';
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

if ($roleFilter !== '') {
    $sql .= ' AND role = ?';
    $params[] = $roleFilter;
}

$sql .= ' ORDER BY created_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/head.php';

// Count by role
$roleCountStmt = $db->prepare('SELECT role, COUNT(*) as cnt FROM users GROUP BY role');
$roleCountStmt->execute();
$roleCounts = [];
foreach ($roleCountStmt->fetchAll() as $row) {
    $roleCounts[$row['role']] = $row['cnt'];
}
$totalMembers = array_sum($roleCounts);
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
.content h1 { margin: 0 0 20px 0; font-size: 1.8rem; font-weight: 700; }

.stats-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 16px;
    margin-bottom: 30px;
}

.stat-card {
    background: linear-gradient(135deg, #f0f4f8 0%, #f9fafb 100%);
    padding: 16px;
    border-radius: 10px;
    text-align: center;
    border: 1px solid #e8ecf1;
    cursor: pointer;
    transition: all 0.2s;
}

.stat-card:hover {
    background: linear-gradient(135deg, #e8ecf1 0%, #f0f4f8 100%);
    border-color: #ff6f00;
}

.stat-card.active {
    background: linear-gradient(135deg, #ff8f00 0%, #ff6f00 100%);
    color: white;
    border-color: #ff6f00;
}

.stat-number {
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 0.85rem;
    opacity: 0.8;
}

.search-section {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    align-items: center;
}

.search-input {
    flex: 1;
    min-width: 200px;
    padding: 10px 14px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.95rem;
}

.search-select {
    padding: 10px 14px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: white;
    cursor: pointer;
}

.search-btn {
    padding: 10px 24px;
    background: linear-gradient(135deg, #ff8f00 0%, #ff6f00 100%);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
}

.search-btn:hover {
    background: linear-gradient(135deg, #d97700 0%, #c86400 100%);
}

.members-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.members-table thead {
    background: #f5f7fa;
    border-bottom: 2px solid #e8ecf1;
}

.members-table th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 0.9rem;
    text-transform: uppercase;
}

.members-table td {
    padding: 16px;
    border-bottom: 1px solid #e8ecf1;
    vertical-align: middle;
}

.members-table tbody tr:hover {
    background: #f9fafb;
}

.member-photo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e8ecf1;
}

.member-photo.empty {
    background: #f0f4f8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #999;
}

.member-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.member-name {
    font-weight: 600;
    color: #1a1a1a;
}

.member-email {
    color: #666;
    font-size: 0.9rem;
}

.role-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.role-admin {
    background: #ffe8cc;
    color: #c86400;
}

.role-user {
    background: #d1ecf1;
    color: #0c5460;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-view {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-view:hover {
    background: linear-gradient(135deg, #0052a3 0%, #003d7a 100%);
    transform: translateY(-1px);
}

.members-count {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e8ecf1;
    color: #666;
    font-size: 0.9rem;
}

.no-members {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.no-members i {
    font-size: 3rem;
    margin-bottom: 16px;
    display: block;
}

@media (max-width: 768px) {
    .content { padding: 20px; }
    .search-section { flex-direction: column; }
    .search-input, .search-select, .search-btn { width: 100%; }
    .members-table { font-size: 0.85rem; }
    .members-table th, .members-table td { padding: 12px 8px; }
}
</style>

<div class="brand-header">
    <h1><i class="fas fa-users" style="margin-right: 12px;"></i>Member Management</h1>
    <p>View and manage all registered members</p>
</div>

<div class="content">
    <h1>Members (<span id="memberCount"><?= count($members) ?></span>)</h1>

    <!-- Statistics -->
    <div class="stats-section">
        <div class="stat-card <?= $roleFilter === '' ? 'active' : '' ?>" data-role="">
            <div class="stat-number"><?= $totalMembers ?></div>
            <div class="stat-label">Total Members</div>
        </div>
        <div class="stat-card <?= $roleFilter === 'user' ? 'active' : '' ?>" data-role="user">
            <div class="stat-number"><?= $roleCounts['user'] ?? 0 ?></div>
            <div class="stat-label">Regular Users</div>
        </div>
        <div class="stat-card <?= $roleFilter === 'admin' ? 'active' : '' ?>" data-role="admin">
            <div class="stat-number"><?= $roleCounts['admin'] ?? 0 ?></div>
            <div class="stat-label">Administrators</div>
        </div>
    </div>

    <!-- Search & Filter -->
    <form method="get" class="search-section" id="searchForm">
        <input type="text" 
               name="q" 
               class="search-input" 
               placeholder="Search by name, email, or phone..." 
               value="<?= htmlspecialchars($q) ?>">
        
        <select name="role" class="search-select">
            <option value="">All Roles</option>
            <option value="user" <?= $roleFilter === 'user' ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
        
        <button type="submit" class="search-btn">
            <i class="fas fa-search"></i> Search
        </button>
        
        <?php if ($q !== '' || $roleFilter !== ''): ?>
            <a href="members.php" class="search-btn" style="background: #999; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-times"></i> Clear
            </a>
        <?php endif; ?>
    </form>

    <!-- Members Table -->
    <?php if (empty($members)): ?>
        <div class="no-members">
            <i class="fas fa-inbox"></i>
            <p>No members found</p>
        </div>
    <?php else: ?>
        <table class="members-table">
            <thead>
                <tr>
                    <th style="width: 50px;"></th>
                    <th style="width: 35%;">Name & Email</th>
                    <th style="width: 20%;">Phone</th>
                    <th style="width: 15%;">Role</th>
                    <th style="width: 15%;">Joined</th>
                    <th style="width: 15%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $member): ?>
                    <tr>
                        <td>
                            <?php if (!empty($member['photo'])): ?>
                                <img src="<?= htmlspecialchars($member['photo']) ?>" alt="<?= htmlspecialchars($member['name']) ?>" class="member-photo">
                            <?php else: ?>
                                <div class="member-photo empty" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="member-info">
                                <div>
                                    <div class="member-name"><?= htmlspecialchars($member['name']) ?></div>
                                    <div class="member-email"><?= htmlspecialchars($member['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="color: #666; font-size: 0.9rem;">
                                <?= !empty($member['phone']) ? htmlspecialchars($member['phone']) : '—' ?>
                            </span>
                        </td>
                        <td>
                            <span class="role-badge role-<?= strtolower($member['role']) ?>">
                                <?= htmlspecialchars(ucfirst($member['role'])) ?>
                            </span>
                        </td>
                        <td>
                            <span style="color: #666; font-size: 0.9rem;">
                                <?= date('M d, Y', strtotime($member['created_at'])) ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="member_detail.php?id=<?= $member['id'] ?>" class="btn-view">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="members-count">
            Showing <strong><?= count($members) ?></strong> of <strong><?= $totalMembers ?></strong> members
        </div>
    <?php endif; ?>
</div>

<script>
$(function() {
    // Stats card click for quick filter
    $('.stat-card').on('click', function() {
        const role = $(this).data('role');
        window.location.href = 'members.php' + (role ? '?role=' + role : '');
    });
});
</script>

<?php require __DIR__ . '/foot.php'; ?>
