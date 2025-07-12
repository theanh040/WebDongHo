<?php

include 'db_connect.php'; // Kết nối database (nếu cần trong các trang khác)

// Xử lý xóa sản phẩm khỏi giỏ hàng
if (isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    // Không cần chuyển hướng, chỉ cập nhật giao diện
}

// Tính tổng giá tiền
$total_amount = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id => $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
}
?>

<!-- Nhúng giao diện giỏ hàng -->
<div class="cart-overlay" id="cartOverlay">
    <div class="cart-sidebar">
        <div class="cart-header">
            <h2>Giỏ hàng</h2>
            <button class="close-cart" onclick="toggleCart()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="cart-items">
            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                    <div class="cart-item">
                        <img src="/Jewerlry/uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="item-price"><?php echo number_format($item['price'], 0, ',', '.'); ?>₫</p>
                            <p class="item-quantity">Số lượng: <?php echo $item['quantity']; ?></p>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                <button type="submit" name="remove_item" class="remove-item">Xóa</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-cart">Giỏ hàng của bạn đang trống.</p>
            <?php endif; ?>
        </div>

        <div class="cart-footer">
            <div class="cart-total">
                <span>Tổng cộng:</span>
                <span class="total-amount"><?php echo number_format($total_amount, 0, ',', '.'); ?>₫</span>
            </div>
            <button class="checkout-btn" onclick="window.location.href='thanhtoan.php'">
                Thanh toán
            </button>
        </div>
    </div>
</div>

<!-- JavaScript cơ bản để hiển thị/ẩn giỏ hàng -->
<script>
function toggleCart() {
    const cartOverlay = document.getElementById('cartOverlay');
    cartOverlay.classList.toggle('active');
}
</script>

<!-- CSS nhúng trực tiếp (hoặc chuyển sang file cart.css) -->
<style>
.cart-overlay {
    position: fixed;
    top: 0;
    right: -400px; /* Ẩn ban đầu */
    width: 400px;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    transition: right 0.3s ease;
    display: none; /* Ẩn mặc định */
}

.cart-overlay.active {
    right: 0; /* Trồi ra khi active */
    display: block;
}

.cart-sidebar {
    width: 100%;
    height: 100%;
    background-color: #fff;
    padding: 20px;
    box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
    overflow-y: auto; /* Cuộn nếu nội dung vượt quá chiều cao */
}

.cart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}

.cart-header h2 {
    font-size: 1.5rem;
    color: #333;
}

.close-cart {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #999;
}

.close-cart:hover {
    color: #e74c3c;
}

.cart-items {
    max-height: calc(100vh - 200px); /* Chiều cao tối đa trừ header và footer */
    overflow-y: auto;
}

.cart-item {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.cart-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    margin-right: 10px;
    border-radius: 5px;
}

.item-details {
    flex-grow: 1;
}

.item-details h3 {
    font-size: 1rem;
    margin: 0 0 5px 0;
    color: #333;
}

.item-price, .item-quantity {
    font-size: 0.9rem;
    color: #666;
    margin: 2px 0;
}

.remove-item {
    background-color: #dc3545;
    color: #fff;
    border: none;
    padding: 2px 10px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 0.8rem;
}

.remove-item:hover {
    background-color: #c82333;
}

.empty-cart {
    text-align: center;
    color: #999;
    padding: 20px 0;
}

.cart-footer {
    position: sticky;
    bottom: 0;
    background-color: #fff;
    padding-top: 10px;
    border-top: 1px solid #ddd;
    margin-top: 20px;
}

.cart-total {
    display: flex;
    justify-content: space-between;
    font-size: 1.1rem;
    margin-bottom: 10px;
    color: #333;
}

.total-amount {
    font-weight: bold;
    color: #e74c3c;
}

.checkout-btn {
    width: 100%;
    padding: 10px;
    background-color: #e74c3c;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
}

.checkout-btn:hover {
    background-color: #c0392b;
}
</style>