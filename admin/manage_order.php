<!-- filepath: /c:/wamp64/www/Jewerlry/admin/manage_order.php -->
<?php
session_start();
include '../db_connect.php';
 

// Xử lý cập nhật trạng thái thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment_status'])) {
    $order_id = $_POST['order_id'];
    $payment_status = $_POST['payment_status'];
    $stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
    $stmt->execute([$payment_status, $order_id]);
    header("Location: manage_order.php");
    exit();
}

// Xử lý cập nhật trạng thái đơn hàng (Approve/Cancel)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_status'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->execute([$order_status, $order_id]);
    header("Location: manage_order.php");
    exit();
}

// Lấy danh sách đơn hàng
$stmt = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Jewelry Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Reset mặc định */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            color: #333;
            line-height: 1.6;
        }

        /* Layout chính */
        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar (từ nav_bar.php) */
        .sidebar {
            width: 250px;
            background-color: #e74c3c;
            padding: 20px;
            color: #fff;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: width 0.3s;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 30px;
            text-align: center;
            color: #fff;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .sidebar ul li a i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .sidebar ul li.active a,
        .sidebar ul li a:hover {
            background-color: #c0392b;
        }

        .sidebar ul li:last-child a {
            margin-top: 20px;
            border-top: 1px solid #fff;
            padding-top: 15px;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            margin-left: 250px; /* Đảm bảo nội dung không bị che bởi sidebar */
            padding: 30px;
            background-color: #fff;
        }

        .main-content header h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #e74c3c;
            background-color: #ffebee;
            padding: 10px 20px;
            border-radius: 5px;
        }

        /* Table */
        .table {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            background-color: #e74c3c;
            color: #fff;
            border: none;
        }

        .table tbody tr:hover {
            background-color: #ffebee;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.9rem;
        }

        .status-approved {
            background-color: #28a745;
            color: #fff;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.9rem;
        }

        .status-canceled {
            background-color: #dc3545;
            color: #fff;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.9rem;
        }

        .status-pending {
            background-color: #ffc107;
            color: #333;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.9rem;
        }

        /* Modal */
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background-color: #e74c3c;
            color: #fff;
            border-bottom: none;
        }

        .modal-title {
            font-size: 1.25rem;
        }

        .btn-close {
            filter: invert(1);
        }

        .modal-body {
            padding: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
                padding: 10px;
            }

            .sidebar h2 {
                font-size: 1rem;
                margin-bottom: 15px;
            }

            .sidebar ul li a {
                justify-content: center;
                padding: 10px;
            }

            .sidebar ul li a span {
                display: none;
            }

            .sidebar ul li a i {
                margin-right: 0;
            }

            .main-content {
                margin-left: 80px;
                padding: 15px;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .btn-sm {
                padding: 3px 6px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'nav_bar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>Manage Orders</h1>
            </header>
            <section>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total (VND)</th>
                                <th>Payment Status</th>
                                <th>Order Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <select name="payment_status" onchange="this.form.submit()">
                                                <option value="pending" <?php echo $order['payment_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="paid" <?php echo $order['payment_status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                            </select>
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <input type="hidden" name="update_payment_status" value="1">
                                        </form>
                                    </td>
                                    <td>
                                        <span class="status-<?php echo $order['order_status']; ?>">
                                            <?php echo ucfirst($order['order_status']); ?>
                                        </span>
                                        <div class="mt-1">
                                            <?php if ($order['order_status'] !== 'approved'): ?>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to approve this order?');">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <input type="hidden" name="order_status" value="approved">
                                                    <input type="hidden" name="update_order_status" value="1">
                                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                </form>
                                            <?php endif; ?>
                                            <?php if ($order['order_status'] !== 'canceled'): ?>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <input type="hidden" name="order_status" value="canceled">
                                                    <input type="hidden" name="update_order_status" value="1">
                                                    <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary view-order-btn" data-order-id="<?php echo $order['id']; ?>" data-bs-toggle="modal" data-bs-target="#orderDetailsModal">View</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="order-details-content">
                    <!-- Nội dung chi tiết đơn hàng sẽ được điền bằng AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý nút View để lấy chi tiết đơn hàng
            document.querySelectorAll('.view-order-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-order-id');
                    fetch(`get_order_details.php?order_id=${orderId}`)
                        .then(response => response.json())
                        .then(data => {
                            const content = document.getElementById('order-details-content');
                            content.innerHTML = `
                                <p><strong>Order ID:</strong> #${data.id}</p>
                                <p><strong>Customer:</strong> ${data.customer_name}</p>
                                <p><strong>Phone:</strong> ${data.phone}</p>
                                <p><strong>Address:</strong> ${data.address}</p>
                                <h6>Products:</h6>
                                <ul>
                                    ${data.details.map(item => `
                                        <li>${item.quantity}x ${item.product_name} - ${item.price.toLocaleString('vi-VN')} VND</li>
                                    `).join('')}
                                </ul>
                                <p><strong>Total:</strong> ${data.total_amount.toLocaleString('vi-VN')} VND</p>
                            `;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('order-details-content').innerHTML = `<p>Lỗi khi tải chi tiết đơn hàng.</p>`;
                        });
                });
            });
        });
    </script>
</body>
</html>