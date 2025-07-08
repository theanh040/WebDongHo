<?php
session_start();
$pageTitle = 'Red - Fashion Eyewear';
include 'db_connect.php'; // Kết nối database
include 'header.php';
?>

<link rel="stylesheet" href="css/sanpham.css?v=2">

<main class="products-main">
    <div class="products-header">
        <h1>Bộ Sưu Tập</h1>
        <div class="filter-sort">
            <select name="sort" id="sort">
                <option value="">Sắp xếp</option>
                <option value="price-asc">Giá: Thấp đến Cao</option>
                <option value="price-desc">Giá: Cao đến Thấp</option>
            </select>
        </div>
    </div>

    <?php
    // Lấy tất cả sản phẩm từ database
    $stmt = $conn->query("SELECT * FROM products ORDER BY id ASC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Phân chia sản phẩm
    $total_products = count($products);
    $first_two = array_slice($products, 0, 2); // 2 sản phẩm đầu
    $middle_products = array_slice($products, 2, $total_products - 4); // Các sản phẩm giữa
    $last_two = array_slice($products, -2); // 2 sản phẩm cuối

    // Hàm lấy ảnh đầu tiên từ chuỗi ảnh (nếu có nhiều ảnh)
    function getFirstImage($image_string) {
        $images = $image_string ? explode(',', $image_string) : [];
        return !empty($images) ? '/Jewerlry/uploads/' . htmlspecialchars($images[0]) : '/Jewerlry/uploads/default.jpg';
    }
    ?>

    <!-- Product List 1: 2 sản phẩm đầu -->
    <section class="product-list">
        <div class="product-grid">
            <video autoplay muted loop playsinline style="width: 100%; height: 90%; object-fit: cover;">
                <source src="uploads/woman.mp4" type="video/mp4">
            </video>
            <?php foreach ($first_two as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <span class="new-tag">New</span>
                        <a href="chitietsp.php?id=<?php echo $product['id']; ?>">
                            <img src="<?php echo getFirstImage($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                    </div>
                    <div class="product-info">
                        <a href="chitietsp.php?id=<?php echo $product['id']; ?>"><h3><?php echo htmlspecialchars($product['name']); ?></h3></a>
                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Product List 2: Các sản phẩm giữa -->
    <section class="product-list">
        <div class="product-grid">
            <?php foreach ($middle_products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <span class="new-tag">New</span>
                        <a href="chitietsp.php?id=<?php echo $product['id']; ?>">
                            <img src="<?php echo getFirstImage($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                    </div>
                    <div class="product-info">
                        <a href="chitietsp.php?id=<?php echo $product['id']; ?>"><h3><?php echo htmlspecialchars($product['name']); ?></h3></a>
                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Product List 3: 2 sản phẩm cuối -->
    <section class="product-list">
        <div class="product-grid">
            <?php foreach ($last_two as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <span class="new-tag">New</span>
                        <a href="chitietsp.php?id=<?php echo $product['id']; ?>">
                            <img src="<?php echo getFirstImage($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                    </div>
                    <div class="product-info">
                        <a href="chitietsp.php?id=<?php echo $product['id']; ?>"><h3><?php echo htmlspecialchars($product['name']); ?></h3></a>
                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</p>
                    </div>
                </div>
            <?php endforeach; ?>
            <video autoplay muted loop playsinline style="width: 100%; height: 90%; object-fit: cover;">
                <source src="uploads/gt1.mp4" type="video/mp4">
            </video>
        </div>
    </section>

    <script src="js/sanpham.js"></script>
    <script>
        // Xử lý sắp xếp (giả định logic sắp xếp)
        document.getElementById('sort').addEventListener('change', function() {
            const sortValue = this.value;
            let sortedProducts = <?php echo json_encode($products); ?>;
            if (sortValue === 'price-asc') {
                sortedProducts.sort((a, b) => a.price - b.price);
            } else if (sortValue === 'price-desc') {
                sortedProducts.sort((a, b) => b.price - a.price);
            }
            // Cập nhật lại giao diện (giả định, cần AJAX để reload danh sách)
            console.log('Sorted by:', sortValue);
            // Ở đây bạn cần thêm AJAX để gửi sortValue đến server và reload danh sách
        });
    </script>
</main>

<?php include 'footer.php'; ?>