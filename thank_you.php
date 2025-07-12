<?php
session_start();
include 'db_connect.php';
include 'header.php';

$order_id = $_GET['order_id'] ?? null;
if ($order_id) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<main class="thank-you-page">
    <h2>ĐẶT HÀNG THÀNH CÔNG</h2>
    <?php if ($order): ?>
        <p>Cảm ơn bạn đã đặt hàng! Mã đơn hàng của bạn là: <strong>#<?php echo $order['id']; ?></strong></p>
        <p>Chúng tôi sẽ liên hệ qua số điện thoại <strong><?php echo htmlspecialchars($order['phone']); ?></strong> để xác nhận.</p>
        <p>Tổng tiền: <strong><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>₫</strong></p>
        <p>Trạng thái thanh toán: <strong><?php echo $order['payment_status'] === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán'; ?></strong></p>
        <a href="sanpham.php" class="continue-shopping">Tiếp tục mua sắm</a>
    <?php else: ?>
        <p>Đã có lỗi xảy ra. Vui lòng liên hệ hỗ trợ.</p>
    <?php endif; ?>
</main>

<style>
/* CSS cho trang thank_you.php */
.thank-you-page {
    max-width: 800px;
    margin: 40px auto;
    padding: 30px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    text-align: center;
    font-family: Arial, sans-serif;
}

.thank-you-page h2 {
    font-size: 2rem;
    color: black; /* Màu xanh lá cây để báo thành công */
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.thank-you-page p {
    font-size: 1.1rem;
    color: #555;
    margin: 15px 0;
    line-height: 1.6;
}

.thank-you-page strong {
    color: #333;
    font-weight: 700;
}

.thank-you-page .continue-shopping {
    display: inline-block;
    margin-top: 30px;
    padding: 12px 30px;
    background-color: black; /* Màu đỏ nhạt */
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.thank-you-page .continue-shopping:hover {
    background-color: #000; /* Nền đen */
    color: #fff; /* Chữ trắng */
    text-shadow: 0 0 5px rgba(255, 255, 255, 0.5); /* Chữ phát sáng */
}

@media (max-width: 600px) {
    .thank-you-page {
        margin: 20px;
        padding: 20px;
    }

    .thank-you-page h2 {
        font-size: 1.5rem;
    }

    .thank-you-page p {
        font-size: 1rem;
    }

    .thank-you-page .continue-shopping {
        padding: 10px 20px;
        font-size: 0.9rem;
    }
}
</style>

<?php include 'footer.php'; ?>