<?php

// 1. Set header to return JSON
header('Content-Type: application/json');

// 2. IMPORTANT: Use '../' to reach the base file from inside the api folder
require '../_base.php';

// session_start() is already called in _base.php

try {
    // 3. Verify user is logged in
    if (empty($_SESSION['user_id'])) {
        throw new Exception('User is not logged in.');
    }

    // 4. Get the JSON data sent from JavaScript
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        throw new Exception('No data received.');
    }

    $userId = $_SESSION['user_id'];
    $total = $data['total'];
    $items = $data['items'];
    
    // Get additional order data
    $paymentMethodRaw = $data['paymentMethod'] ?? '';
    $paymentMethod = is_string($paymentMethodRaw) ? trim($paymentMethodRaw) : '';
    $paymentMethodMap = [
        'card' => 'card',
        'credit_card' => 'card',
        'debit_card' => 'card',
        'fpx' => 'fpx',
        'ewallet' => 'ewallet',
        'e-wallet' => 'ewallet',
        'bank_transfer' => 'bank_transfer',
        'cash' => 'cod',
        'cod' => 'cod',
        'paypal' => 'paypal'
    ];

    $methodKey = strtolower($paymentMethod);
    if ($methodKey === '' || !isset($paymentMethodMap[$methodKey])) {
        throw new Exception('Please select a valid payment method.');
    }

    $paymentMethod = $paymentMethodMap[$methodKey];
    $shippingAddress = '';
    if (!empty($data['address'])) {
        $shippingAddress = trim($data['firstName'] ?? '') . ' ' . trim($data['lastName'] ?? '') . "\n";
        $shippingAddress .= trim($data['address'] ?? '') . "\n";
        $shippingAddress .= trim($data['postcode'] ?? '') . ' ' . trim($data['city'] ?? '') . "\n";
        $shippingAddress .= trim($data['country'] ?? '');
    }
    $phone = $data['phone'] ?? null;

    // 5. Start a Database Transaction
    $db->beginTransaction();

    // --- Insert into your 'orders' table ---
    $stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_address, phone, created_at) VALUES (?, ?, 'Pending', ?, ?, ?, NOW())");
    $stmt->execute([$userId, $total, $paymentMethod, $shippingAddress, $phone]);

    $orderId = $db->lastInsertId();

    // --- Insert each product into 'order_items' table ---
    $stmtItem = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");

    foreach ($items as $item) {
        $stmtItem->execute([
            $orderId,
            $item['id'],
            $item['qty'],
            $item['price']
        ]);
    }

    // 6. If everything worked, save to database
    $db->commit();

    // 7. Send order confirmation email
    try {
        // Get user details
        $userStmt = $db->prepare("SELECT name, email FROM users WHERE id = ? LIMIT 1");
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        // Get order items with product names
        $itemsStmt = $db->prepare("
            SELECT oi.quantity, oi.unit_price, p.name
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $itemsStmt->execute([$orderId]);
        $orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($user) {
            $m = get_mail();
            $m->addAddress($user['email'], $user['name']);
            $m->Subject = 'Order Confirmation #' . $orderId . ' - Thank You for Your Purchase';
            $m->isHTML(true);
            
            // Build order items HTML
            $itemsHtml = '';
            $subtotal = 0;
            foreach ($orderItems as $item) {
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemTotal;
                $itemsHtml .= '
                <tr>
                    <td style="padding: 12px; border-bottom: 1px solid #eee;">' . htmlspecialchars($item['name']) . '</td>
                    <td style="padding: 12px; border-bottom: 1px solid #eee; text-align: center;">' . $item['quantity'] . '</td>
                    <td style="padding: 12px; border-bottom: 1px solid #eee; text-align: right;">RM ' . number_format($item['unit_price'], 2) . '</td>
                    <td style="padding: 12px; border-bottom: 1px solid #eee; text-align: right;">RM ' . number_format($itemTotal, 2) . '</td>
                </tr>';
            }
            
            $shippingFee = $total - $subtotal;
            $orderDate = date('F d, Y \a\t H:i');
            
            $m->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <div style="background: linear-gradient(135deg, #ff6f00 0%, #ff8f00 100%); color: #fff; padding: 40px 30px; text-align: center;">
                    <h1 style="margin: 0; font-size: 28px;">Thank You for Your Order!</h1>
                    <p style="margin: 10px 0 0 0; font-size: 16px;">Order #' . $orderId . '</p>
                </div>
                <div style="padding: 30px; background: #f9f9f9;">
                    <p style="color: #333; line-height: 1.6;">Hi ' . htmlspecialchars($user['name']) . ',</p>
                    <p style="color: #333; line-height: 1.6;">Thank you for shopping with <strong>VisionX</strong>! We\'re excited to confirm that we\'ve received your order and it\'s being processed.</p>
                    
                    <div style="background: #fff; border: 1px solid #eee; border-radius: 6px; padding: 20px; margin: 20px 0;">
                        <p style="margin: 0 0 12px 0; color: #666;"><strong>Order Date:</strong> ' . $orderDate . '</p>
                        <p style="margin: 0; color: #666;"><strong>Payment Method:</strong> ' . htmlspecialchars($paymentMethod) . '</p>
                    </div>
                    
                    <h3 style="margin: 20px 0 15px 0; color: #333; font-size: 16px;">Order Items</h3>
                    <table width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #eee; border-radius: 6px; overflow: hidden;">
                        <thead>
                            <tr style="background-color: #f5f5f5;">
                                <th style="padding: 12px; text-align: left; font-size: 14px; color: #666;">Product</th>
                                <th style="padding: 12px; text-align: center; font-size: 14px; color: #666;">Qty</th>
                                <th style="padding: 12px; text-align: right; font-size: 14px; color: #666;">Price</th>
                                <th style="padding: 12px; text-align: right; font-size: 14px; color: #666;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $itemsHtml . '
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="padding: 12px; text-align: right; color: #666;">Subtotal:</td>
                                <td style="padding: 12px; text-align: right; color: #333;">RM ' . number_format($subtotal, 2) . '</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding: 12px; text-align: right; color: #666;">Shipping:</td>
                                <td style="padding: 12px; text-align: right; color: #333;">RM ' . number_format($shippingFee, 2) . '</td>
                            </tr>
                            <tr style="background-color: #f5f5f5;">
                                <td colspan="3" style="padding: 12px; text-align: right; font-weight: bold; color: #333;">Total Paid:</td>
                                <td style="padding: 12px; text-align: right; font-weight: bold; color: #ff6f00; font-size: 16px;">RM ' . number_format($total, 2) . '</td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <div style="background-color: #fff8f0; border-left: 4px solid #ff6f00; padding: 20px; margin: 20px 0; border-radius: 6px;">
                        <h3 style="margin: 0 0 12px 0; color: #ff6f00; font-size: 16px;">📦 What\'s Next?</h3>
                        <ul style="margin: 0; padding-left: 20px; color: #666; line-height: 1.8; font-size: 14px;">
                            <li>You\'ll receive updates about your order</li>
                            <li>Your order will be processed and shipped within 1-2 business days</li>
                            <li>Expected delivery: 3-5 business days from shipment</li>
                        </ul>
                    </div>
                </div>
                <div style="background: #333; color: #fff; padding: 20px; text-align: center; font-size: 12px;">
                    <p style="margin: 0;">Need help? Contact us at <strong>support@visionx.com</strong></p>
                    <p style="margin: 8px 0 0 0;">&copy; 2025 VisionX Official Store Malaysia. All rights reserved.</p>
                </div>
            </div>';
            
            $m->AltBody = "Thank You for Your Order!\n\nOrder #" . $orderId . "\n\nHi " . $user['name'] . ",\n\nThank you for shopping with VisionX! We're excited to confirm that we've received your order and it's being processed.\n\nOrder Date: " . $orderDate . "\n\nYour order will be processed and shipped within 1-2 business days.\nExpected delivery: 3-5 business days from shipment\n\nTotal Paid: RM " . number_format($total, 2) . "\n\nNeed help? Contact us at support@visionx.com\n\n© 2025 VisionX Official Store Malaysia";
            
            $m->send();
        }
    } catch (Exception $emailError) {
        error_log("Email sending error: " . $emailError->getMessage());
        // Continue even if email fails - order is already saved
    }

    // 8. Tell the JavaScript it was successful
    echo json_encode(['success' => true, 'orderId' => $orderId]);

} catch (Exception $e) {
    // If anything goes wrong, cancel the database changes
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    // Log the actual error for debugging
    error_log("Order placement error: " . $e->getMessage() . " | File: " . $e->getFile() . " | Line: " . $e->getLine());

    // Send the specific error message to the JavaScript console
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
