<footer>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <div class="footer-content">
        <div class="footer-section about">
            <h1 class="logo-text"><span>Red</span> Jewelry</h1>
            <p>
                Red Jewelry - Nơi kết tinh của vẻ đẹp, phong cách và sự thanh lịch. Chúng tôi tự hào mang đến những bộ trang sức tuyệt đẹp cho quý khách.
            </p>
            <div class="contact">
                <span><i class="fas fa-map-marker-alt"></i> Hà Nội, Việt Nam</span>
                <span><i class="fas fa-phone"></i> +84 24 1234 5678</span>
                <span><i class="fas fa-envelope"></i> info@redjewelry.vn</span>
            </div>

        </div>

        <div class="footer-section newsletter">
            <h2>Newsletter</h2>
            <p>Đăng ký nhận tin tức mới nhất từ chúng tôi</p>
            <form action="#" method="post">
                <input type="email" name="email" class="text-input contact-input" placeholder="Email của bạn..." style="border: 1px solid #ddd  ; border-radius: 4px;">
                <button type="submit" class="btn btn-big contact-btn"><i class="fas fa-envelope"></i> Đăng ký</button>
            </form>
        </div>

        <div class="footer-section links">
            <h2>Liên kết hữu ích</h2>
            <ul>
                <li><a href="#">Về chúng tôi</a></li>
                <li><a href="#">Liên hệ</a></li>
                <li><a href="#">Câu hỏi thường gặp</a></li>
                <li><a href="#">Chính sách bảo mật</a></li>
                <li><a href="#">Điều khoản sử dụng</a></li>
            </ul>
        </div>

        <div class="footer-section legal">
            <h2>Thông tin pháp lý</h2>
            <ul>
                <li><a href="#">Chính sách bảo mật</a></li>
                <li><a href="#">Điều khoản sử dụng</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        &copy; 2025 Red Jewelry | Designed by Red Team
    </div>
    <li><a href="login.php" class="login-link" title="Đăng nhập"><i class="fas fa-user"></i></a></li>
</footer>

<!-- Stylesheet cho footer -->
<style>
    .footer-content {
        display: flex;
        justify-content: space-around;
        padding: 20px;
        background-color: white  ;
        color: #000;
        padding-top: 100px;
        border-top: 1px solid #ddd;
    }

    .footer-section {
        flex: 1;
        margin: 10px;
    }

    .logo-text {
        font-size: 24px;
        font-weight: bold;
    }

    .contact span, .socials a {
        display: block;
        margin: 5px 0;
    }

    .socials a {
        margin-right: 10px;
    }

    .newsletter form {
        display: flex;
        flex-direction: column;
    }

    .newsletter .text-input {
        margin-bottom: 10px;
        padding: 10px;
        border: none;
    }

    .newsletter .contact-btn {
        padding: 10px;
        background-color: #ddd;
        color: #fff;
        border: none;
    }

    .footer-bottom {
        text-align: center;
        padding: 10px;
        background-color: white ;
    }

    .links ul, .legal ul {
        list-style: none;
    }

    .links li, .legal li {
        margin: 5px 0;
    }

    .links a, .legal a {
        text-decoration: none;
        color: #000;
    }

   
</style>
