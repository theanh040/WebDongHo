<?php
session_start();
include 'db_connect.php'; // Kết nối database


// Lấy dữ liệu đơn hàng từ session
$order_items = [];
$total_amount = 0;

// Trường hợp 1: Từ cart.php (toàn bộ giỏ hàng)
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $order_items = $_SESSION['cart'];
    foreach ($order_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
}

// Trường hợp 2: Từ chitietsp.php (mua ngay)
if (isset($_GET['id']) && empty($order_items)) {
    $product_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $images = $product['image_url'] ? explode(',', $product['image_url']) : [];
        $quantity = 1; // Số lượng mặc định nếu mua ngay
        $order_items[$product_id] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $images[0] ?? 'default.jpg',
            'quantity' => $quantity
        ];
        $total_amount = $product['price'] * $quantity;
    }
}

// Xử lý đặt hàng khi nhấn "Đặt hàng"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $customer_name = $_POST['customer_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? ''; // Gộp địa chỉ thành một trường
    $payment_status = 'pending'; // Mặc định là pending vì chỉ có chuyển khoản

    // Kiểm tra dữ liệu hợp lệ
    if (empty($customer_name) || empty($phone) || empty($address)) {
        $error = "Vui lòng điền đầy đủ thông tin giao hàng.";
    } elseif (empty($order_items)) {
        $error = "Giỏ hàng của bạn đang trống.";
    } else {
        // Lưu đơn hàng vào bảng orders
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, phone, address, total_amount, payment_status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$customer_name, $phone, $address, $total_amount, $payment_status]);

        // Lấy ID của đơn hàng vừa tạo
        $order_id = $conn->lastInsertId();

        // Lưu chi tiết sản phẩm vào bảng order_details
        foreach ($order_items as $product_id => $item) {
            $stmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $product_id, $item['quantity'], $item['price']]);
        }

        // Lưu order_id vào session để sử dụng ở payment.php
        $_SESSION['order_id'] = $order_id;

        // Xóa giỏ hàng sau khi đặt hàng
        unset($_SESSION['cart']);
        unset($_SESSION['product_id']);

        // Chuyển hướng đến trang payment.php với tổng tiền và order_id
        header("Location: payment.php?amount=" . $total_amount . "&order_id=" . $order_id);
        exit();
    }
}
?>

<link rel="stylesheet" href="css/thanhtoan.css">

<main class="checkout-page">
    <div class="checkout-container">
        <!-- Bên trái - Form thông tin giao hàng -->
        <div class="shipping-info">
            <h2>THÔNG TIN GIAO HÀNG</h2>
            
            <?php if (isset($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form class="checkout-form" method="POST">
                <div class="form-group">
                    <label>ĐỊA CHỈ (Gồm tỉnh/thành, quận/huyện, phường/xã)</label>
                    <textarea name="address" placeholder="Nhập địa chỉ đầy đủ" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label>HỌ VÀ TÊN</label>
                    <input type="text" name="customer_name" placeholder="Họ và tên" required>
                </div>

                <div class="form-group">
                    <label>SỐ ĐIỆN THOẠI</label>
                    <input type="tel" name="phone" placeholder="Số điện thoại" required>
                </div>

                <div class="payment-method">
                    <h3>THANH TOÁN BẰNG CHUYỂN KHOẢN</h3>
                    <div class="payment-options">
                        <p>Vui lòng chuyển khoản vào tài khoản sau:</p>
                        <p><strong>Ngân hàng:</strong> MBBank</p>
                        <p><strong>Số tài khoản:</strong> 840337857323</p>
                        <p><strong>Chủ tài khoản:</strong> Lê Văn Hoàng</p>
                        <p><strong>Nội dung chuyển khoản:</strong> [Mã đơn hàng] - [Họ tên]</p>
                        <p>Mã đơn hàng sẽ hiển thị sau khi bạn nhấn "Đặt hàng".</p>
                    </div>
                </div>

                <button type="submit" name="place_order" class="order-button">ĐẶT HÀNG</button>
            </form>
        </div>

        <!-- Bên phải - Tóm tắt đơn hàng -->
        <div class="order-summary">
            <div class="summary-header">
                <h3>TÓM TẮT ĐƠN HÀNG</h3>
                <span class="total-amount"><?php echo number_format($total_amount, 0, ',', '.'); ?>₫</span>
            </div>

            <div class="order-items">
                <?php if (!empty($order_items)): ?>
                    <?php foreach ($order_items as $product_id => $item): ?>
                        <div class="order-item">
                            <img src="/Jewerlry/uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="item-info">
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <span class="item-price"><?php echo number_format($item['price'], 0, ',', '.'); ?>₫</span>
                                <span class="item-quantity">Số lượng: <?php echo $item['quantity']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Không có sản phẩm trong đơn hàng.</p>
                <?php endif; ?>
            </div>

            <div class="coupon-section">
                <label>MÃ GIẢM GIÁ</label>
                <div class="coupon-input">
                    <input type="text" placeholder="Nhập Mã Giảm Giá">
                    <button class="apply-coupon">SỬ DỤNG</button>
                </div>
            </div>

            <div class="order-totals">
                <div class="subtotal">
                    <span>Tạm Tính</span>
                    <span><?php echo number_format($total_amount, 0, ',', '.'); ?>₫</span>
                </div>
                <div class="shipping">
                    <span>Phí Vận Chuyển</span>
                    <span>-</span>
                </div>
                <div class="total">
                    <span>TỔNG CỘNG</span>
                    <span><?php echo number_format($total_amount, 0, ',', '.'); ?>₫</span>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>