<?php 
session_start(); 
$host = 'localhost';
$dbname = 'jewerlryy'; 
$username = 'root';
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Kết nối database thất bại: " . $e->getMessage());
}


try {
    
    $user_id = $_SESSION['user_id']; 
    $query_user = "SELECT * FROM users WHERE id = ? AND role = 'customer'";
    $stmt_user = $pdo->prepare($query_user);
    $stmt_user->execute([$user_id]);
    $user = $stmt_user->fetch(PDO::FETCH_ASSOC);
    
    
    if (!$user) {
        die("Không tìm thấy thông tin người dùng!");
    }

    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        
        
        if (empty($full_name) || empty($email)) {
            $error_message = "Tên và email không được để trống!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Email không hợp lệ!";
        } else {
            $update_query = "UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
            $update_stmt = $pdo->prepare($update_query);
            
            if ($update_stmt->execute([$full_name, $email, $phone, $address, $user_id])) {
                $success_message = "Cập nhật thông tin thành công!";
                // Cập nhật lại thông tin user
                $stmt_user->execute([$user_id]);
                $user = $stmt_user->fetch(PDO::FETCH_ASSOC);
            } else {
                $error_message = "Có lỗi xảy ra. Vui lòng thử lại!";
            }
        }
    }

    
    $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'home';

    $category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $query_products = "SELECT * FROM products WHERE stock_quantity > 0";
    $params = [];

    if ($category_filter !== 'all') {
        $query_products .= " AND category = ?";
        $params[] = $category_filter;
    }

    if (!empty($search)) {
        $query_products .= " AND (name LIKE ? OR description LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
    }

    $query_products .= " ORDER BY created_at DESC LIMIT 12";
    $stmt_products = $pdo->prepare($query_products);
    $stmt_products->execute($params);
    $products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);

    // Lấy đơn hàng của khách hàng
    $query_orders = "SELECT o.*, COUNT(od.id) as item_count
                     FROM orders o
                     LEFT JOIN order_details od ON o.id = od.order_id
                     WHERE o.user_id = ?
                     GROUP BY o.id
                     ORDER BY o.created_at DESC";
    $stmt_orders = $pdo->prepare($query_orders);
    $stmt_orders->execute([$user_id]);
    $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

    // Lấy chi tiết đơn hàng nếu có order_id
    $order_details = null;
    if (isset($_GET['order_id']) && $current_tab === 'orders') {
        $order_id = (int)$_GET['order_id']; // Cast to int for security
        $query_details = "SELECT od.*, p.name as product_name, p.image_url
                          FROM order_details od
                          JOIN products p ON od.product_id = p.id
                          JOIN orders o ON od.order_id = o.id
                         WHERE od.order_id = ? AND o.user_id = ?";
        $stmt_details = $pdo->prepare($query_details);
        $stmt_details->execute([$order_id, $user_id]);
        $order_details = $stmt_details->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error_message = "Có lỗi xảy ra khi truy cập dữ liệu: " . $e->getMessage();
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    $error_message = "Có lỗi xảy ra: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Watch Collection - Customer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
/* Import Google Fonts cho thương hiệu đồng hồ sang trọng */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;1,400&display=swap');

/* Variables cho thương hiệu đồng hồ */
:root {
    --luxury-black: #0a0a0a;
    --pure-white: #ffffff;
    --gold-accent: #c9a96e;
    --rose-gold: #e8b4a0;
    --silver-accent: #c0c0c0;
    --charcoal: #2c2c2c;
    --warm-gray: #8a8a8a;
    --light-gray: #f5f5f5;
    --transition-luxury: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
    --shadow-luxury: 0 15px 35px rgba(0, 0, 0, 0.1);
}

/* Global Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background-color: var(--luxury-black);
    color: var(--pure-white);
    overflow-x: hidden;
    font-family: 'Inter', sans-serif;
}

/* Hero Collection Section - Luxury Watch Style */
.hero1-collection {
    padding: 8rem 5% 6rem;
    text-align: center;
    background: linear-gradient(135deg, #000 0%, #1a1a1a 100%);
    position: relative;
}

.hero1-collection::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 50% 30%, rgba(201, 169, 110, 0.08) 0%, transparent 70%);
    pointer-events: none;
}

.hero1-collection h1 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(3rem, 6vw, 5rem);
    font-weight: 400;
    margin-bottom: 1.5rem;
    color: var(--pure-white);
    letter-spacing: 4px;
    position: relative;
    z-index: 1;
    animation: fadeInUp 1.2s ease-out;
}

.subtitle {
    font-family: 'Inter', sans-serif;
    font-size: clamp(1.1rem, 2.5vw, 1.4rem);
    font-weight: 300;
    color: var(--gold-accent);
    margin-bottom: 5rem;
    letter-spacing: 6px;
    text-transform: uppercase;
    position: relative;
    z-index: 1;
    animation: fadeInUp 1.2s ease-out 0.3s both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Luxury Navigation Menu */
.luxury-nav {
    background: linear-gradient(135deg, var(--charcoal) 0%, #1a1a1a 100%);
    padding: 2rem 0;
    border-top: 1px solid var(--gold-accent);
    border-bottom: 1px solid var(--gold-accent);
    position: sticky;
    top: 0;
    z-index: 100;
    backdrop-filter: blur(10px);
}

.nav-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
}

.nav-menu {
    display: flex;
    justify-content: center;
    gap: 3rem;
    list-style: none;
}

.nav-item {
    position: relative;
}

.nav-link {
    font-family: 'Inter', sans-serif;
    font-weight: 400;
    font-size: 1.1rem;
    color: var(--pure-white);
    text-decoration: none;
    padding: 1rem 2rem;
    border: 1px solid transparent;
    border-radius: 50px;
    transition: var(--transition-luxury);
    letter-spacing: 1px;
    text-transform: uppercase;
    position: relative;
    overflow: hidden;
}

.nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, var(--gold-accent), transparent);
    transition: var(--transition-luxury);
}

.nav-link:hover::before,
.nav-link.active::before {
    left: 100%;
}

.nav-link:hover,
.nav-link.active {
    color: var(--gold-accent);
    border-color: var(--gold-accent);
    transform: translateY(-2px);
    box-shadow: var(--shadow-luxury);
}

.nav-link i {
    margin-right: 0.5rem;
    font-size: 1.2rem;
}

/* Content Sections */
.content-section {
    padding: 4rem 0;
    min-height: 80vh;
}

.container-luxury {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* Product Cards */
.product-card {
    background: linear-gradient(135deg, var(--charcoal) 0%, #1f1f1f 100%);
    border: 1px solid var(--gold-accent);
    border-radius: 20px;
    overflow: hidden;
    transition: var(--transition-luxury);
    position: relative;
}

.product-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(201, 169, 110, 0.05) 0%, transparent 100%);
    opacity: 0;
    transition: var(--transition-luxury);
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(201, 169, 110, 0.2);
}

.product-card:hover::before {
    opacity: 1;
}

.product-image {
    height: 280px;
    object-fit: cover;
    width: 100%;
    transition: var(--transition-luxury);
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.card-body {
    padding: 2rem;
    position: relative;
    z-index: 2;
}

.card-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    color: var(--pure-white);
    margin-bottom: 1rem;
}

.price-tag {
    font-family: 'Inter', sans-serif;
    font-size: 1.8rem;
    font-weight: 600;
    color: var(--gold-accent);
    margin-bottom: 1rem;
}

.btn-luxury {
    background: linear-gradient(135deg, var(--gold-accent) 0%, var(--rose-gold) 100%);
    color: var(--luxury-black);
    border: none;
    padding: 0.8rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: var(--transition-luxury);
    font-family: 'Inter', sans-serif;
}

.btn-luxury:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(201, 169, 110, 0.4);
    color: var(--luxury-black);
}

/* Order Cards */
.order-card {
    background: linear-gradient(135deg, var(--charcoal) 0%, #1f1f1f 100%);
    border: 1px solid var(--silver-accent);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    transition: var(--transition-luxury);
}

.order-card:hover {
    border-color: var(--gold-accent);
    transform: translateY(-5px);
    box-shadow: var(--shadow-luxury);
}

.order-status {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.9rem;
}

.status-paid {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.status-pending {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: var(--luxury-black);
}

/* Form Styles */
.form-luxury {
    background: linear-gradient(135deg, var(--charcoal) 0%, #1f1f1f 100%);
    border: 1px solid var(--gold-accent);
    border-radius: 20px;
    padding: 3rem;
    margin: 2rem 0;
}

.form-control-luxury {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--warm-gray);
    border-radius: 10px;
    color: var(--pure-white);
    padding: 1rem 1.5rem;
    font-family: 'Inter', sans-serif;
    transition: var(--transition-luxury);
}

.form-control-luxury:focus {
    background: rgba(255, 255, 255, 0.08);
    border-color: var(--gold-accent);
    box-shadow: 0 0 20px rgba(201, 169, 110, 0.3);
    color: var(--pure-white);
}

.form-label-luxury {
    color: var(--gold-accent);
    font-weight: 500;
    margin-bottom: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.9rem;
}

/* Search and Filter */
.search-luxury {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--gold-accent);
    border-radius: 50px;
    padding: 1rem 2rem;
    color: var(--pure-white);
    transition: var(--transition-luxury);
}

.search-luxury:focus {
    background: rgba(255, 255, 255, 0.08);
    box-shadow: 0 0 25px rgba(201, 169, 110, 0.4);
    color: var(--pure-white);
}

.filter-select {
    background: var(--charcoal);
    border: 1px solid var(--gold-accent);
    color: var(--pure-white);
    border-radius: 10px;
    padding: 1rem;
}

/* Alerts */
.alert-luxury-success {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.2) 0%, rgba(32, 201, 151, 0.2) 100%);
    border: 1px solid #28a745;
    color: #28a745;
    border-radius: 15px;
    padding: 1rem 2rem;
    margin: 2rem 0;
}

.alert-luxury-error {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.2) 0%, rgba(255, 105, 97, 0.2) 100%);
    border: 1px solid #dc3545;
    color: #dc3545;
    border-radius: 15px;
    padding: 1rem 2rem;
    margin: 2rem 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .nav-menu {
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .nav-link {
        padding: 0.8rem 1.5rem;
        font-size: 1rem;
    }
    
    .hero1-collection {
        padding: 4rem 5% 3rem;
    }
    
    .hero1-collection h1 {
        font-size: 2.5rem;
    }
    
    .subtitle {
        font-size: 1rem;
        letter-spacing: 3px;
    }
}

.section-title {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem;
    color: var(--pure-white);
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--gold-accent), transparent);
}

.order-details-card {
    background: linear-gradient(135deg, #1a1a1a 0%, var(--charcoal) 100%);
    border: 1px solid var(--gold-accent);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1rem;
}
    </style>
</head>
<body>

<!-- Hero Section -->
<div class="hero1-collection">
    <h1>
        <i class="fas fa-crown" style="color: var(--gold-accent); margin-right: 1rem;"></i>
        Welcome, <?php echo htmlspecialchars($user['full_name']); ?>
    </h1>
    <p class="subtitle">Luxury Timepiece Collection</p>
</div>

<!-- Navigation Menu -->
<nav class="luxury-nav">
    <div class="nav-container">
        <ul class="nav-menu">
            <li class="nav-item">
               <a href="index.php?tab=home" class="nav-link">
    <i class="fas fa-home"></i> Trang chủ
</a>
            </li>
            <li class="nav-item">
                <a href="?tab=orders" class="nav-link <?php echo $current_tab === 'orders' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-bag"></i>Đơn hàng
                </a>
            </li>
            <li class="nav-item">
                <a href="?tab=profile" class="nav-link <?php echo $current_tab === 'profile' ? 'active' : ''; ?>">
                    <i class="fas fa-user-edit"></i>Thông tin
                </a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>Đăng xuất
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Content Sections -->
<div class="content-section">
    <div class="container-luxury">
        
        <?php if ($current_tab === 'home'): ?>
            <!-- Home Tab - Products -->
            <h2 class="section-title">Bộ sưu tập đồng hồ</h2>
            
            <!-- Search and Filter -->
            <div class="row mb-5">
                <div class="col-lg-8">
                    <form method="GET" class="d-flex">
                        <input type="hidden" name="tab" value="home">
                        <input type="text" name="search" class="form-control search-luxury me-3" 
                               placeholder="Tìm kiếm đồng hồ cao cấp..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-luxury">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-lg-4">
                    <select class="form-select filter-select" onchange="filterByCategory(this.value)">
                        <option value="all" <?php echo $category_filter === 'all' ? 'selected' : ''; ?>>
                            Tất cả danh mục
                        </option>
                        <option value="dong_ho_co" <?php echo $category_filter === 'dong_ho_co' ? 'selected' : ''; ?>>
                            Đồng hồ cơ
                        </option>
                        <option value="dong_ho_dien_tu" <?php echo $category_filter === 'dong_ho_dien_tu' ? 'selected' : ''; ?>>
                            Đồng hồ điện tử
                        </option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card product-card h-100">
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                         class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <div class="product-image bg-dark d-flex align-items-center justify-content-center">
                                        <i class="fas fa-clock fa-4x" style="color: var(--gold-accent);"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="text-muted mb-3">
                                        <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?>
                                    </p>
                                    
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small style="color: var(--warm-gray);">Kích thước:</small><br>
                                            <strong style="color: var(--silver-accent);"><?php echo htmlspecialchars($product['size']); ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <small style="color: var(--warm-gray);">Chất liệu:</small><br>
                                            <strong style="color: var(--silver-accent);"><?php echo htmlspecialchars($product['material']); ?></strong>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="price-tag">
                                            <?php echo number_format($product['price'], 0, ',', '.'); ?>đ
                                        </span>
                                        <small style="color: var(--warm-gray);">
                                            Còn <?php echo $product['stock_quantity']; ?> sản phẩm
                                        </small>
                                    </div>
                                    
                                    <button class="btn btn-luxury w-100" onclick="addToCart(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ hàng
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-search fa-4x mb-3" style="color: var(--warm-gray);"></i>
                        <h4 style="color: var(--pure-white);">Không tìm thấy sản phẩm</h4>
                        <p style="color: var(--warm-gray);">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</p>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($current_tab === 'orders'): ?>
            <!-- Orders Tab -->
            <h2 class="section-title">Theo dõi đơn hàng</h2>
            
            <?php if (isset($_GET['order_id']) && $order_details): ?>
                <!-- Order Details View -->
                <div class="mb-4">
                    <a href="?tab=orders" class="btn btn-luxury">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                    </a>
                </div>
                
                <h3 style="color: var(--gold-accent); margin-bottom: 2rem;">
                    Chi tiết đơn hàng #<?php echo $_GET['order_id']; ?>
                </h3>
                
                <div class="row">
                    <?php foreach ($order_details as $detail): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="order-details-card">
                                <?php if (!empty($detail['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($detail['image_url']); ?>" 
                                         class="img-fluid rounded mb-3" style="height: 200px; object-fit: cover; width: 100%;">
                                <?php endif; ?>
                                <h6 style="color: var(--pure-white);"><?php echo htmlspecialchars($detail['product_name']); ?></h6>
                                <p style="color: var(--warm-gray);">
                                    Số lượng: <span style="color: var(--gold-accent);"><?php echo $detail['quantity']; ?></span>
                                </p>
                                <p style="color: var(--warm-gray);">
                                    Giá: <span style="color: var(--gold-accent);"><?php echo number_format($detail['price'], 0, ',', '.'); ?>đ</span>
                                </p>
                                <p style="color: var(--warm-gray);">
                                    Tổng: <span style="color: var(--rose-gold); font-weight: 600;">
                                        <?php echo number_format($detail['price'] * $detail['quantity'], 0, ',', '.'); ?>đ
                                    </span>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            
            <?php else: ?>
                <!-- Orders List View -->
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <h5 style="color: var(--gold-accent);">#<?php echo $order['id']; ?></h5>
                                    <small style="color: var(--warm-gray);">
                                        <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <p style="color: var(--pure-white); margin-bottom: 0.5rem;">
                                        <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                    </p>
                                    <small style="color: var(--warm-gray);"><?php echo htmlspecialchars($order['phone']); ?></small>
                                </div>
                                <div class="col-md-2">
                                    <p style="color: var(--warm-gray); margin-bottom: 0.5rem;">Số sản phẩm:</p>
                                    <strong style="color: var(--silver-accent);"><?php echo $order['item_count']; ?></strong>
                                </div>
                                <div class="col-md-2">
                                    <p style="color: var(--warm-gray); margin-bottom: 0.5rem;">Tổng tiền:</p>
                                    <strong style="color: var(--rose-gold); font-size: 1.2rem;">
                                        <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ
                                    </strong>
                                </div>
                                <div class="col-md-2">
                                    <span class="order-status <?php echo $order['payment_status'] === 'paid' ? 'status-paid' : 'status-pending'; ?>">
                                        <?php echo $order['payment_status'] === 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán'; ?>
                                    </span>
                                </div>
                                <div class="col-md-1">
                                    <a href="?tab=orders&order_id=<?php echo $order['id']; ?>" class="btn btn-luxury btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="mt-3" style="border-top: 1px solid var(--warm-gray); padding-top: 1rem;">
                                <p style="color: var(--warm-gray); margin-bottom: 0.5rem;">Địa chỉ giao hàng:</p>
                                <p style="color: var(--silver-accent);"><?php echo htmlspecialchars($order['address']); ?></p>
                                <p style="color: var(--warm-gray); margin-bottom: 0.5rem;">Trạng thái đơn hàng:</p>
                                <p style="color: var(--gold-accent);"><?php echo htmlspecialchars($order['order_status']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-4x mb-3" style="color: var(--warm-gray);"></i>
                        <h4 style="color: var(--pure-white);">Chưa có đơn hàng nào</h4>
                        <p style="color: var(--warm-gray);">Hãy khám phá bộ sưu tập đồng hồ của chúng tôi</p>
                        <a href="?tab=home" class="btn btn-luxury">
                            <i class="fas fa-shopping-cart me-2"></i>Mua sắm ngay
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        <?php elseif ($current_tab === 'profile'): ?>
            <!-- Profile Tab -->
            <h2 class="section-title">Thông tin cá nhân</h2>
            
            <?php if (isset($success_message)): ?>
                <div class="alert-luxury-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert-luxury-error">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form method="POST" class="form-luxury">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label-luxury">Họ và tên</label>
                                <input type="text" name="full_name" class="form-control form-control-luxury" 
                                       value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label class="form-label-luxury">Email</label>
                                <input type="email" name="email" class="form-control form-control-luxury" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label-luxury">Số điện thoại</label>
                                <input type="tel" name="phone" class="form-control form-control-luxury" 
                                       value="<?php echo htmlspecialchars($user['phone']); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label class="form-label-luxury">Tên đăng nhập</label>
                                <input type="text" class="form-control form-control-luxury" 
                                       value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                <small style="color: var(--warm-gray);">Không thể thay đổi tên đăng nhập</small>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label-luxury">Địa chỉ</label>
                            <textarea name="address" class="form-control form-control-luxury" rows="4" 
                                      placeholder="Nhập địa chỉ của bạn..."><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label-luxury">Ngày tạo tài khoản</label>
                            <input type="text" class="form-control form-control-luxury" 
                                   value="<?php echo date('d/m/Y H:i:s', strtotime($user['created_at'])); ?>" readonly>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-luxury" style="padding: 1rem 3rem; font-size: 1.1rem;">
                                <i class="fas fa-save me-2"></i>Cập nhật thông tin
                            </button>
                        </div>
                    </form>
                    
                    <!-- Change Password Section -->
                    <div class="form-luxury mt-4">
                        <h4 style="color: var(--gold-accent); margin-bottom: 2rem; text-align: center;">
                            <i class="fas fa-key me-2"></i>Đổi mật khẩu
                        </h4>
                        
                        <form id="changePasswordForm">
                            <div class="mb-4">
                                <label class="form-label-luxury">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" class="form-control form-control-luxury" 
                                       placeholder="Nhập mật khẩu hiện tại..." required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label-luxury">Mật khẩu mới</label>
                                <input type="password" name="new_password" class="form-control form-control-luxury" 
                                       placeholder="Nhập mật khẩu mới..." required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label-luxury">Xác nhận mật khẩu mới</label>
                                <input type="password" name="confirm_password" class="form-control form-control-luxury" 
                                       placeholder="Xác nhận mật khẩu mới..." required>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-luxury" style="padding: 1rem 3rem; font-size: 1.1rem;">
                                    <i class="fas fa-lock me-2"></i>Đổi mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function filterByCategory(category) {
    const url = new URL(window.location);
    url.searchParams.set('tab', 'home');
    if (category === 'all') {
        url.searchParams.delete('category');
    } else {
        url.searchParams.set('category', category);
    }
    window.location.href = url.toString();
}

function addToCart(productId) {
    // AJAX call để thêm sản phẩm vào giỏ hàng
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Tạo notification luxury
            showLuxuryNotification('Đã thêm sản phẩm vào giỏ hàng!', 'success');
        } else {
            showLuxuryNotification('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showLuxuryNotification('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
    });
}

function showLuxuryNotification(message, type) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        padding: 1rem 2rem;
        border-radius: 15px;
        color: white;
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        backdrop-filter: blur(10px);
        animation: slideInRight 0.5s ease-out;
        ${type === 'success' 
            ? 'background: linear-gradient(135deg, rgba(40, 167, 69, 0.9) 0%, rgba(32, 201, 151, 0.9) 100%); border: 1px solid #28a745;'
            : 'background: linear-gradient(135deg, rgba(220, 53, 69, 0.9) 0%, rgba(255, 105, 97, 0.9) 100%); border: 1px solid #dc3545;'
        }
    `;
    
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.5s ease-out';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 500);
    }, 3000);
}

// Change Password Form Handler
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const currentPassword = this.current_password.value;
    const newPassword = this.new_password.value;
    const confirmPassword = this.confirm_password.value;
    
    if (newPassword !== confirmPassword) {
        showLuxuryNotification('Mật khẩu xác nhận không khớp!', 'error');
        return;
    }
    
    if (newPassword.length < 6) {
        showLuxuryNotification('Mật khẩu mới phải có ít nhất 6 ký tự!', 'error');
        return;
    }
    
    // AJAX call để đổi mật khẩu
    fetch('change_password.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `current_password=${encodeURIComponent(currentPassword)}&new_password=${encodeURIComponent(newPassword)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showLuxuryNotification('Đổi mật khẩu thành công!', 'success');
            this.reset();
        } else {
            showLuxuryNotification(data.message || 'Có lỗi xảy ra!', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showLuxuryNotification('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
    });
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
`;
document.head.appendChild(style);
</script>

<?php include 'footer.php'; ?>
</body>
</html>
