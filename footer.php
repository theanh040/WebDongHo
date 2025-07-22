<footer>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <div class="footer-content">
        <div class="footer-section about">
            <h1 class="logo-text">
                <i class="fas fa-crown"></i>
                <span>The Jewerly.</span>
            </h1>
            <p class="about-text">
                Nơi kết tinh của vẻ đẹp, phong cách và sự thanh lịch. Chúng tôi tự hào mang đến những bộ trang sức tuyệt đẹp cho quý khách.
            </p>
            <div class="contact">
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i> 
                    <span>Hà Nội, Việt Nam</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i> 
                    <span>+84 24 1234 5678</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i> 
                    <span>info@thejewelry.vn</span>
                </div>
            </div>
            <div class="social-links">
                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

        <div class="footer-section newsletter">
            <h2>Newsletter</h2>
            <p>Đăng ký nhận tin tức và ưu đãi mới nhất</p>
            <form action="#" method="post" class="newsletter-form">
                <div class="input-group">
                    <input type="email" name="email" class="newsletter-input" placeholder="Nhập email của bạn..." required>
                    <button type="submit" class="newsletter-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="footer-section links">
            <h2>Liên kết hữu ích</h2>
            <ul class="footer-links">
                <li><a href="about.php"><i class="fas fa-angle-right"></i> Về chúng tôi</a></li>
                <li><a href="products.php"><i class="fas fa-angle-right"></i> Sản phẩm</a></li>
                <li><a href="contact.php"><i class="fas fa-angle-right"></i> Liên hệ</a></li>
                <li><a href="support.php"><i class="fas fa-angle-right"></i> Hỗ trợ khách hàng</a></li>
                <li><a href="guide.php"><i class="fas fa-angle-right"></i> Hướng dẫn mua hàng</a></li>
            </ul>
        </div>

        <div class="footer-section legal">
            <h2>Chính sách</h2>
            <ul class="footer-links">
                <li><a href="privacy.php"><i class="fas fa-angle-right"></i> Chính sách bảo mật</a></li>
                <li><a href="terms.php"><i class="fas fa-angle-right"></i> Điều khoản sử dụng</a></li>
                <li><a href="return.php"><i class="fas fa-angle-right"></i> Chính sách đổi trả</a></li>
                <li><a href="shipping.php"><i class="fas fa-angle-right"></i> Chính sách vận chuyển</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="footer-bottom-content">
            <p>&copy; 2025 The Jewerly. Tất cả quyền được bảo lưu.</p>
            <p>Thiết kế bởi <span class="highlight">The Jewerly Team</span></p>
        </div>
        <div class="login-section">
            <?php if (isset($_SESSION['user_id']) && in_array($_SESSION['role'], ['admin', 'customer'])): ?>
                <!-- User is logged in -->
                <a href="<?php echo $_SESSION['role'] === 'admin' ? 'admin/index.php' : 'customer/account.php'; ?>" class="login-link" title="Quản lý tài khoản">
                    <i class="fas fa-user"></i>
                    <span>Quản lý tài khoản</span>
                </a>
                <a href="?action=logout" class="login-link logout-btn" title="Đăng xuất">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Đăng xuất</span>
                </a>
            <?php else: ?>
            
            <?php endif; ?>
        </div>
    </div>
</footer>

<style>
    :root {
        --luxury-black: #0a0a0a;
        --pure-white: #ffffff;
        --gold-accent: #c9a96e;
        --rose-gold: #e8b4a0;
        --transition-luxury: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
    }

    footer {
        background: var(--luxury-black);
        color: var(--pure-white);
        margin-top: 80px;
        position: relative;
        overflow: hidden;
    }

    footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--gold-accent), transparent);
    }

    .footer-content {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 40px;
        padding: 60px 40px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .footer-section h2 {
        color: var(--pure-white);
        font-family: 'Playfair Display', serif;
        font-size: 1.4rem;
        margin-bottom: 25px;
        position: relative;
        padding-bottom: 10px;
    }

    .footer-section h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 2px;
        background: var(--gold-accent);
        border-radius: 2px;
    }

    /* About Section */
    .logo-text {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--gold-accent);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .logo-text i {
        font-size: 2rem;
    }

    .about-text {
        color: var(--warm-gray);
        line-height: 1.7;
        margin-bottom: 30px;
        font-family: 'Inter', sans-serif;
        font-size: 1rem;
    }

    .contact-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        color: var(--warm-gray);
        transition: color 0.3s ease;
        font-family: 'Inter', sans-serif;
    }

    .contact-item:hover {
        color: var(--gold-accent);
    }

    .contact-item i {
        width: 20px;
        margin-right: 15px;
        color: var(--gold-accent);
    }

    .social-links {
        display: flex;
        gap: 15px;
    }

    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        color: var(--pure-white);
        text-decoration: none;
        transition: var(--transition-luxury);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .social-link:hover {
        background: var(--gold-accent);
        color: var(--luxury-black);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(201, 169, 110, 0.4);
    }

    /* Newsletter Section */
    .newsletter p {
        color: var(--warm-gray);
        margin-bottom: 25px;
        line-height: 1.6;
        font-family: 'Inter', sans-serif;
    }

    .input-group {
        display: flex;
        border-radius: 25px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .newsletter-input {
        flex: 1;
        padding: 15px 20px;
        background: transparent;
        border: none;
        color: var(--pure-white);
        font-family: 'Inter', sans-serif;
        font-size: 1rem;
        outline: none;
    }

    .newsletter-input::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .newsletter-btn {
        padding: 15px 20px;
        background: linear-gradient(135deg, var(--gold-accent), var(--rose-gold));
        border: none;
        color: var(--luxury-black);
        cursor: pointer;
        transition: var(--transition-luxury);
        min-width: 60px;
    }

    .newsletter-btn:hover {
        background: linear-gradient(135deg, var(--rose-gold), var(--gold-accent));
        transform: scale(1.05);
    }

    /* Links Section */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
    }

    .footer-links a {
        color: var(--warm-gray);
        text-decoration: none;
        transition: var(--transition-luxury);
        display: flex;
        align-items: center;
        padding: 8px 0;
        border-radius: 5px;
        padding-left: 10px;
        margin-left: -10px;
        font-family: 'Inter', sans-serif;
    }

    .footer-links a:hover {
        color: var(--gold-accent);
        background: rgba(201, 169, 110, 0.1);
        transform: translateX(5px);
    }

    .footer-links a i {
        margin-right: 10px;
        font-size: 0.8rem;
        transition: transform 0.3s ease;
    }

    .footer-links a:hover i {
        transform: translateX(3px);
    }

    /* Footer Bottom */
    .footer-bottom {
        background: rgba(0, 0, 0, 0.3);
        padding: 25px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-bottom-content {
        color: var(--warm-gray);
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
    }

    .highlight {
        color: var(--gold-accent);
        font-weight: 600;
    }

    .login-section {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .login-link {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--pure-white);
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 25px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: var(--transition-luxury);
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
    }

    .login-link:hover {
        background: var(--gold-accent);
        color: var(--luxury-black);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(201, 169, 110, 0.4);
    }

    .logout-btn {
        color: var(--rose-gold);
    }

    .logout-btn:hover {
        color: var(--luxury-black);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr;
            gap: 30px;
            padding: 40px 20px;
        }
        
        .footer-bottom {
            flex-direction: column;
            gap: 15px;
            text-align: center;
            padding: 20px;
        }
        
        .logo-text {
            font-size: 2rem;
        }
        
        .input-group {
            flex-direction: column;
            border-radius: 10px;
        }
        
        .newsletter-btn {
            border-radius: 0 0 10px 10px;
        }
        
        .newsletter-input {
            border-radius: 10px 10px 0 0;
        }
    }

    @media (max-width: 480px) {
        .footer-content {
            padding: 30px 15px;
        }
        
        .social-links {
            justify-content: center;
        }
        
        .footer-section h2 {
            font-size: 1.2rem;
        }
        
        .login-section {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>
