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

// Thiết lập thông tin tài khoản
$accountNo = "840337857323"; // Số tài khoản MBBank của Lê Văn Hoàng
$accountName = "LE VAN HOANG"; // Tên tài khoản
$bankId = "970422"; // Mã BIN của MBBank
$addInfo = "Thanh toan don hang DH{$order_id}"; // Nội dung chuyển khoản (có mã đơn hàng)
$template = "compact"; // Kiểu hiển thị QR

// URL encode các tham số để xử lý khoảng trắng và ký tự đặc biệt
$encodedAccountName = urlencode($accountName);
$encodedAddInfo = urlencode($addInfo);

// Tạo Quick Link VietQR
$qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png?amount={$amount}&addInfo={$encodedAddInfo}&accountName={$encodedAccountName}";

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
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .payment-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .qr-code {
            margin: 20px 0;
        }
        .qr-image {
            max-width: 250px;
            height: auto;
            border: 2px solid #ddd;
            border-radius: 5px;
            padding: 5px;
        }
        .info {
            color: #555;
            margin: 10px 0;
        }
        .confirm-btn {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .confirm-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1>THANH TOÁN BẰNG MÃ QR</h1>

        <div class="qr-code">
            <img src="<?php echo htmlspecialchars($qrUrl); ?>" alt="Mã QR Thanh Toán" class="qr-image">
        </div>
        <div class="info">
            <p><strong>Mã đơn hàng:</strong> DH<?php echo $order_id; ?></p>
            <p><strong>Tên tài khoản:</strong> Ninh Văn Sang </p>
            <p><strong>Số tài khoản:</strong> 11040467555 </p>
            <p><strong>Ngân hàng:</strong> MBBank</p>
            <p><strong>Số tiền:</strong> <?php echo number_format($amount, 0, ',', '.'); ?> VND</p>
            <p><strong>Nội dung:</strong> <?php echo htmlspecialchars($addInfo); ?></p>
        </div>

        <form method="POST" action="">
            <button type="submit" name="confirm_payment" class="confirm-btn">Xác nhận thanh toán</button>
        </form>
    </div>
</body>
</html>