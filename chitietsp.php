<?php
session_start();
include 'db_connect.php'; // Kết nối database

// Lấy product_id từ query string và lưu vào session
$product_id = $_GET['id'] ?? null;
if ($product_id) {
    $_SESSION['product_id'] = $product_id;
}

// Lấy thông tin sản phẩm từ database
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Sản phẩm không tồn tại.");
}

// Lấy danh sách ảnh sản phẩm (nếu có nhiều ảnh)
$images = $product['image_url'] ? explode(',', $product['image_url']) : [];

// Lấy sản phẩm gợi ý (3 sản phẩm ngẫu nhiên bất kỳ)
$stmt = $conn->query("SELECT * FROM products ORDER BY RAND() LIMIT 3");
$related_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý thêm sản phẩm vào giỏ hàng
if (isset($_POST['add_to_cart'])) {
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $images[0] ?? 'default.jpg',
            'quantity' => $quantity
        ];
    }
    // Chuyển hướng lại trang hiện tại để cập nhật (hoặc bạn có thể thêm thông báo)
    header("Location: chitietsp.php?id=" . $product_id);
    exit();
}

include 'header.php';
?>

<link rel="stylesheet" href="css/chitietsp.css">

<main class="product-detail">
    <div class="product-container">
        <!-- Phần hình ảnh sản phẩm -->
        <div class="product-images">
            <div class="main-image">
                <img src="/Jewerlry/uploads/<?php echo htmlspecialchars($images[0] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" id="mainImage">
            </div>
            <div class="thumbnail-images">
                <?php foreach ($images as $image): ?>
                    <img src="/Jewerlry/uploads/<?php echo htmlspecialchars($image); ?>" alt="Detail" data-image="/Jewerlry/uploads/<?php echo htmlspecialchars($image); ?>">
                <?php endforeach; ?>
                <?php if (empty($images)): ?>
                    <img src="/Jewerlry/uploads/default.jpg" alt="Default" data-image="/Jewerlry/uploads/default.jpg">
                <?php endif; ?>
            </div>
        </div>

        <!-- Phần thông tin sản phẩm -->
        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <div class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</div>
            
            <div class="product-specs">
                <h3>Thông số kỹ thuật</h3>
                <ul>
                    <li>Kích thước: <?php echo htmlspecialchars($product['size'] ?: 'N/A'); ?></li>
                    <li>Chất liệu: <?php echo htmlspecialchars($product['material'] ?: 'N/A'); ?></li>
                    <li>Kim cương: <?php echo htmlspecialchars($product['diamond_weight'] ?: 'N/A'); ?> carat, độ tinh khiết <?php echo htmlspecialchars($product['diamond_clarity'] ?: 'N/A'); ?></li>
                    <li>Độ trong: <?php echo htmlspecialchars($product['diamond_color'] ?: 'N/A'); ?></li>
                </ul>
            </div>

            <div class="product-description">
                <h3>Mô tả sản phẩm</h3>
                <p><?php echo htmlspecialchars($product['description'] ?: 'Không có mô tả.'); ?></p>
            </div>

            <form method="POST">
                <div class="quantity-selector">
                    <button class="quantity-btn minus">-</button>
                    <input type="number" name="quantity" value="1" min="1" max="10">
                    <button class="quantity-btn plus">+</button>
                </div>

                <div class="action-buttons">
                    <a href="thanhtoan.php?id=<?php echo $product['id']; ?>" class="buy-now">Mua ngay</a>
                    <button type="submit" name="add_to_cart" class="add-to-cart">Thêm vào giỏ hàng</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Phần sản phẩm gợi ý -->
    <section class="related-products">
        <h2>Sản phẩm tương tự</h2>
        <div class="product-grid">
            <?php foreach ($related_products as $related): ?>
                <?php $related_images = $related['image_url'] ? explode(',', $related['image_url']) : []; ?>
                <div class="product-card">
                    <a href="chitietsp.php?id=<?php echo $related['id']; ?>">
                        <img src="/Jewerlry/uploads/<?php echo htmlspecialchars($related_images[0] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                    </a>
                    <a href="chitietsp.php?id=<?php echo $related['id']; ?>"><h3><?php echo htmlspecialchars($related['name']); ?></h3></a>
                    <p class="price"><?php echo number_format($related['price'], 0, ',', '.'); ?>₫</p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<script>
    // Chọn ảnh thumbnail
    document.querySelectorAll('.thumbnail-images img').forEach(thumb => {
        thumb.addEventListener('click', function() {
            document.getElementById('mainImage').src = this.getAttribute('data-image');
        });
    });

    // Xử lý tăng giảm số lượng sản phẩm
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Ngăn hành vi mặc định của button

            const input = this.parentElement.querySelector('input[name="quantity"]');
            let quantity = parseInt(input.value);

            if (this.classList.contains('plus')) {
                if (quantity < parseInt(input.max)) {
                    quantity++;
                }
            } else if (this.classList.contains('minus')) {
                if (quantity > parseInt(input.min)) {
                    quantity--;
                }
            }

            input.value = quantity;
        });
    });

    // Xử lý khi người dùng thay đổi trực tiếp giá trị input
    document.querySelector('input[name="quantity"]').addEventListener('change', function() {
        let quantity = parseInt(this.value);
        const min = parseInt(this.min);
        const max = parseInt(this.max);

        if (quantity < min) {
            this.value = min;
        } else if (quantity > max) {
            this.value = max;
        }
    });
</script>