<?php
/**
 * 订单管理函数库
 * 路径：app/lib/orders.php
 */

/**
 * 获取用户的所有订单列表（按日期倒序）
 *
 * @param PDO $db 数据库连接
 * @param int $user_id 用户 ID
 * @return array 订单列表
 */
function get_user_orders($db, $user_id)
{
    try {
        $stmt = $db->prepare("
            SELECT * FROM orders 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([(int)$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching user orders: ' . $e->getMessage());
        return [];
    }
}

/**
 * 获取订单详情及其包含的产品
 *
 * @param PDO $db 数据库连接
 * @param int $order_id 订单 ID
 * @return array 订单项目列表
 */
function get_order_details($db, $order_id)
{
    try {
        $stmt = $db->prepare("
            SELECT oi.*, p.name, p.image_path 
            FROM order_items oi 
            LEFT JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
            ORDER BY oi.id ASC
        ");
        $stmt->execute([(int)$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching order details: ' . $e->getMessage());
        return [];
    }
}

/**
 * 保存新订单及其项目
 * 使用事务确保数据一致性
 *
 * @param PDO $db 数据库连接
 * @param int $user_id 用户 ID
 * @param float $total 订单总额
 * @param array $items 订单项目数组，每项应包含 'id', 'qty', 'price'
 * @return int|false 成功返回订单 ID，失败返回 false
 */
function save_order($db, $user_id, $total, $items)
{
    try {
        // 验证输入参数
        if (!$db instanceof PDO) {
            throw new Exception('Invalid database connection');
        }

        if (!is_numeric($user_id) || $user_id <= 0) {
            throw new Exception('Invalid user ID');
        }

        if (!is_numeric($total) || $total <= 0) {
            throw new Exception('Invalid total amount: ' . $total);
        }

        if (!is_array($items) || empty($items)) {
            throw new Exception('Items array is empty or invalid');
        }

        // 启动事务
        $db->beginTransaction();

        // ===== 1. 插入主订单记录 =====
        $orderStmt = $db->prepare("
            INSERT INTO orders (user_id, total_amount, status, created_at) 
            VALUES (?, ?, 'pending', NOW())
        ");

        $orderStmt->execute([
            (int)$user_id,
            (float)$total
        ]);

        // 获取新订单的 ID
        $order_id = $db->lastInsertId();

        if (!$order_id) {
            throw new Exception('Failed to get order ID from database');
        }

        error_log("Order inserted: ID=$order_id, User=$user_id, Total=$total");

        // ===== 2. 插入订单项目 =====
        $itemStmt = $db->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, unit_price) 
            VALUES (?, ?, ?, ?)
        ");

        foreach ($items as $index => $item) {
            // 验证项目数据
            if (empty($item['id'])) {
                throw new Exception("Item #$index missing product ID");
            }

            if (!isset($item['qty']) || $item['qty'] <= 0) {
                throw new Exception("Item #$index has invalid quantity");
            }

            if (!isset($item['price']) || $item['price'] < 0) {
                throw new Exception("Item #$index has invalid price");
            }

            // 执行插入
            $itemStmt->execute([
                (int)$order_id,
                (int)$item['id'],
                (int)$item['qty'],
                (float)$item['price']
            ]);

            error_log("Item inserted: OrderID=$order_id, ProductID={$item['id']}, Qty={$item['qty']}, Price={$item['price']}");
        }

        // 提交事务
        $db->commit();

        error_log("Order saved successfully: ID=$order_id");

        return $order_id;

    } catch (PDOException $e) {
        // 数据库错误
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log('PDO Error in save_order: ' . $e->getMessage());
        return false;

    } catch (Exception $e) {
        // 其他错误
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log('Error in save_order: ' . $e->getMessage());
        return false;
    }
}

/**
 * 获取单个订单信息
 *
 * @param PDO $db 数据库连接
 * @param int $order_id 订单 ID
 * @param int $user_id 用户 ID（用于验证权限）
 * @return array|null 订单信息，如果不存在返回 null
 */
function get_order($db, $order_id, $user_id)
{
    try {
        $stmt = $db->prepare("
            SELECT * FROM orders 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([(int)$order_id, (int)$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
        error_log('Error fetching order: ' . $e->getMessage());
        return null;
    }
}

/**
 * 更新订单状态
 *
 * @param PDO $db 数据库连接
 * @param int $order_id 订单 ID
 * @param string $status 新状态（pending, processing, shipped, delivered, cancelled）
 * @return bool 成功返回 true，失败返回 false
 */
function update_order_status($db, $order_id, $status)
{
    try {
        $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $valid_statuses)) {
            throw new Exception('Invalid status: ' . $status);
        }

        $stmt = $db->prepare("
            UPDATE orders 
            SET status = ? 
            WHERE id = ?
        ");

        $result = $stmt->execute([$status, (int)$order_id]);

        if ($result) {
            error_log("Order status updated: ID=$order_id, Status=$status");
        }

        return $result;

    } catch (Exception $e) {
        error_log('Error updating order status: ' . $e->getMessage());
        return false;
    }
}
