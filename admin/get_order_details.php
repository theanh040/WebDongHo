<?php
include '../db_connect.php';

header('Content-Type: application/json');

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    $stmt = $conn->prepare("
        SELECT o.*, od.product_id, od.quantity, od.price, p.name AS product_name
        FROM orders o
        LEFT JOIN order_details od ON o.id = od.order_id
        LEFT JOIN products p ON od.product_id = p.id
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rows) {
        $order = [
            'id' => $rows[0]['id'],
            'customer_name' => $rows[0]['customer_name'],
            'phone' => $rows[0]['phone'],
            'address' => $rows[0]['address'],
            'total_amount' => $rows[0]['total_amount'],
            'details' => []
        ];
        
        foreach ($rows as $row) {
            if ($row['product_id']) {
                $order['details'][] = [
                    'product_name' => $row['product_name'],
                    'quantity' => $row['quantity'],
                    'price' => $row['price']
                ];
            }
        }
        echo json_encode($order);
    } else {
        echo json_encode(['error' => 'Order not found']);
    }
} else {
    echo json_encode(['error' => 'No order ID provided']);
}
?>