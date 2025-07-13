<?php
session_start();
include 'db_connect.php'; // Kết nối database

// Nhận số tiền và order_id từ query string
$amount = isset($_GET['amount']) ? (int)$_GET['amount'] : 0;
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Kiểm tra nếu không có số tiền hoặc order_id
if ($amount <= 0 || $order_id <= 0) {
    die("Lỗi: Không tìm thấy thông tin đơn hàng hoặc số tiền.");
}

// Truy vấn thông tin tài khoản từ database
$stmt = $conn->prepare("SELECT * FROM bank ORDER BY update_at DESC LIMIT 1");
$stmt->execute();
$bank_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Kiểm tra nếu không có thông tin tài khoản trong database
if (!$bank_info) {
    die("Lỗi: Không tìm thấy thông tin tài khoản thụ hưởng. Vui lòng liên hệ admin.");
}

// Lấy thông tin tài khoản từ database
$accountNo = $bank_info['account_number'];
$accountName = $bank_info['account_name'];
$bankId = $bank_info['bank_bin'];
$addInfo = "Thanh toan don hang DH{$order_id}"; // Nội dung chuyển khoản (có mã đơn hàng)
$template = "compact"; // Kiểu hiển thị QR

// URL encode các tham số để xử lý khoảng trắng và ký tự đặc biệt
$encodedAccountName = urlencode($accountName);
$encodedAddInfo = urlencode($addInfo);

// Tạo Quick Link VietQR
$qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png?amount={$amount}&addInfo={$encodedAddInfo}&accountName={$encodedAccountName}";

// Danh sách ngân hàng để hiển thị tên ngân hàng
$banks = [
    "970425" => "Ngân hàng TMCP An Bình (ABB)",
    "970416" => "Ngân hàng TMCP Á Châu (ACB)",
    "970409" => "Ngân hàng TMCP Bắc Á (BAB)",
    "970418" => "Ngân hàng TMCP Đầu tư và Phát triển Việt Nam (BIDV)",
    "970438" => "Ngân hàng TMCP Bảo Việt (BVB)",
    "546034" => "CAKE by VPBank",
    "970444" => "Ngân hàng Xây dựng Việt Nam (CBB)",
    "422589" => "CIMB Việt Nam",
    "533948" => "Citibank",
    "970446" => "Ngân hàng Hợp tác xã Việt Nam (COOPBANK)",
    "796500" => "DBS Bank (HCM)",
    "970406" => "Ngân hàng TMCP Đông Á (DOB)",
    "970431" => "Ngân hàng TMCP Xuất Nhập khẩu Việt Nam (EIB)",
    "970408" => "GPB Bank",
    "970437" => "Ngân hàng TMCP Phát triển TP Hồ Chí Minh (HDB)",
    "970442" => "Hong Leong Việt Nam",
    "458761" => "HSBC Việt Nam",
    "970456" => "Ngân hàng Công nghiệp Hàn Quốc - HCM",
    "970455" => "Ngân hàng Công nghiệp Hàn Quốc - Hà Nội",
    "970415" => "Ngân hàng TMCP Công thương Việt Nam (VietinBank)",
    "970434" => "Indovina Bank",
    "970463" => "Kookmin HCM",
    "970462" => "Kookmin Hà Nội",
    "668888" => "Kasikornbank",
    "970466" => "KEB Hana HCM",
    "970467" => "KEB Hana Hà Nội",
    "970452" => "Ngân hàng TMCP Kiên Long",
    "970449" => "Liên Việt PostBank",
    "970422" => "MB Bank",
    "970426" => "Maritime Bank",
    "970428" => "Nam Á Bank",
    "970419" => "NCB",
    "801011" => "NH Nonghyup",
    "970448" => "OCB",
    "970414" => "OceanBank",
    "970439" => "Public Bank",
    "970430" => "Petrolimex Bank",
    "970412" => "PVCB",
    "970429" => "Saigon Commercial Bank (SCB)",
    "970410" => "Standard Chartered Vietnam",
    "970440" => "SeABank",
    "970400" => "SGICB",
    "970443" => "SHB",
    "970424" => "Shinhan Vietnam",
    "970403" => "Sacombank",
    "970407" => "Techcombank",
    "963388" => "Timo by Ban Viet Bank",
    "970423" => "TPBank",
    "970458" => "UOB HCM",
    "546035" => "Ubank by VPBank",
    "970427" => "VietABank",
    "970405" => "Agribank",
    "999888" => "VBSP (Ngân hàng Chính sách Xã hội)",
    "970436" => "Vietcombank",
    "970454" => "VCCB",
    "970441" => "VIB",
    "970433" => "Vietbank",
    "970432" => "VPBank",
    "970421" => "VRB",
    "970457" => "Woori Vietnam",
    "momo" => "Ví MoMo"
];

// Lấy tên ngân hàng
$bankName = isset($banks[$bankId]) ? $banks[$bankId] : "Ngân hàng không xác định";

// Xử lý khi nhấn "Xác nhận thanh toán"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    // Cập nhật trạng thái thanh toán thành "paid"
    $stmt = $conn->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = ?");
    $stmt->execute([$order_id]);

    // Chuyển hướng đến trang thank_you.php
    header("Location: thank_you.php?order_id=" . $order_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Bằng Mã QR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }

        .payment-container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="20" cy="80" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="90" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
        }

        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .header .order-id {
            margin-top: 10px;
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .content {
            padding: 30px;
        }

        .qr-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .qr-image {
            max-width: 250px;
            height: auto;
            border: 3px solid #f8f9fa;
            border-radius: 10px;
            padding: 10px;
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .qr-image:hover {
            transform: scale(1.05);
        }

        .info-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-value {
            font-weight: 500;
            color: #212529;
            text-align: right;
        }

        .amount-highlight {
            color: #28a745;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .confirm-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .confirm-btn:hover {
            background: linear-gradient(135deg, #218838 0%, #1aa085 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }

        .confirm-btn:active {
            transform: translateY(0);
        }

        .instructions {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: #1565c0;
        }

        .instructions i {
            margin-right: 8px;
            color: #2196f3;
        }

        .error-message {
            background: #ffebee;
            border: 1px solid #ffcdd2;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            color: #c62828;
            text-align: center;
        }

        @media (max-width: 768px) {
            .payment-container {
                margin: 20px auto;
                border-radius: 10px;
            }
            
            .content {
                padding: 20px;
            }
            
            .qr-image {
                max-width: 200px;
            }
            
            .info-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .info-value {
                text-align: left;
            }
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="header">
            <h1><i class="bi bi-qr-code-scan"></i> Thanh Toán QR Code</h1>
            <div class="order-id">Mã đơn hàng: DH<?php echo $order_id; ?></div>
        </div>

        <div class="content">
            <div class="instructions">
                <i class="bi bi-info-circle"></i>
                <strong>Hướng dẫn:</strong> Sử dụng ứng dụng ngân hàng để quét mã QR và thực hiện thanh toán
            </div>

            <div class="qr-section">
                <img src="<?php echo htmlspecialchars($qrUrl); ?>" alt="Mã QR Thanh Toán" class="qr-image" id="qr-image">
            </div>

            <div class="info-section">
                <div class="info-item">
                    <span class="info-label">
                        <i class="bi bi-person"></i>
                        Tên tài khoản:
                    </span>
                    <span class="info-value"><?php echo htmlspecialchars($accountName); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">
                        <i class="bi bi-credit-card"></i>
                        Số tài khoản:
                    </span>
                    <span class="info-value"><?php echo htmlspecialchars($accountNo); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">
                        <i class="bi bi-bank"></i>
                        Ngân hàng:
                    </span>
                    <span class="info-value"><?php echo htmlspecialchars($bankName); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">
                        <i class="bi bi-currency-dollar"></i>
                        Số tiền:
                    </span>
                    <span class="info-value amount-highlight"><?php echo number_format($amount, 0, ',', '.'); ?> VND</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">
                        <i class="bi bi-chat-text"></i>
                        Nội dung:
                    </span>
                    <span class="info-value"><?php echo htmlspecialchars($addInfo); ?></span>
                </div>
            </div>

            <form method="POST" action="" id="payment-form">
                <button type="submit" name="confirm_payment" class="confirm-btn" id="confirm-btn">
                    <i class="bi bi-check-circle"></i> Xác nhận đã thanh toán
                </button>
            </form>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Đang xử lý thanh toán...</p>
            </div>
        </div>
    </div>

    <script>
        // Xử lý form submit
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            document.getElementById('confirm-btn').style.display = 'none';
            document.getElementById('loading').style.display = 'block';
        });

        // Tự động refresh QR code mỗi 5 phút để tránh timeout
        setInterval(function() {
            const qrImage = document.getElementById('qr-image');
            const currentSrc = qrImage.src;
            const timestamp = new Date().getTime();
            
            // Thêm timestamp để force refresh
            if (currentSrc.includes('&t=')) {
                qrImage.src = currentSrc.replace(/&t=\d+/, '&t=' + timestamp);
            } else {
                qrImage.src = currentSrc + '&t=' + timestamp;
            }
        }, 300000); // 5 phút = 300,000ms

        // Hiệu ứng click cho QR code
        document.getElementById('qr-image').addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    </script>
</body>
</html>
