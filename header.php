<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'The Jewerly. - Cửa hàng trang sức sang trọng'; ?></title>
    <link rel="stylesheet" href="css/header.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/cart-overlay.css">
</head>
<body>
    <header>
        <nav class="main-nav">
            <div class="logo">
                <a href="index.php" style="color: black; text-decoration: none;">
                <h1>The Jewerly.</h1>
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="sanpham.php">MEN</a></li>
                <li><a href="sanpham.php">WOMEN</a></li>
            
                <li><a href="#" onclick="toggleCart(); return false;"><i class="fas fa-shopping-cart"></i></a></li>
               
            </nav>
            <section class="hero">
                <div class="hero-content">
                <h1>The Jewerly.</h1>
                <p class="hero-text">
                Chào mừng bạn đến với The Jewerly. – nơi mỗi tuyệt tác trang sức là một biểu tượng của sự tinh xảo và đẳng cấp
                </p>
                </div>
            </section>
    </header>
    <?php include 'cart.php'; ?>
    <script src="js/cart.js"></script>
</body>
</html>