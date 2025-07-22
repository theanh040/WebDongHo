<?php
session_start();
include '../db_connect.php';

// Khởi tạo biến
$bank_bin = '';
$account_number = '';
$account_name = '';
$message = '';
$qr_url = '';
$show_qr = false;

// Lấy thông tin tài khoản hiện tại từ database
$stmt = $conn->prepare("SELECT * FROM bank ORDER BY update_at DESC LIMIT 1");
$stmt->execute();
$current_account = $stmt->fetch(PDO::FETCH_ASSOC);

if ($current_account) {
    $bank_bin = $current_account['bank_bin'];
    $account_number = $current_account['account_number'];
    $account_name = $current_account['account_name'];
}

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bank_bin = trim($_POST['bank_bin']);
    $account_number = trim($_POST['account_number']);
    $account_name = trim($_POST['account_name']);
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($bank_bin) || empty($account_number) || empty($account_name)) {
        $message = '<div class="alert alert-danger">Vui lòng điền đầy đủ thông tin!</div>';
    } else {
        // Hàm kiểm tra API VietQR
        function testVietQRAPI($bank_bin, $account_number, $account_name) {
            $amount = 50000; // Số tiền test
            $addInfo = "Test connection";
            $template = "compact";
            
            $encodedAccountName = urlencode($account_name);
            $encodedAddInfo = urlencode($addInfo);
            
            $qrUrl = "https://img.vietqr.io/image/{$bank_bin}-{$account_number}-{$template}.png?amount={$amount}&addInfo={$encodedAddInfo}&accountName={$encodedAccountName}";
            
            // Kiểm tra xem URL có trả về ảnh hợp lệ không
            $headers = @get_headers($qrUrl);
            if ($headers && strpos($headers[0], '200') !== false) {
                $content_type = '';
                foreach ($headers as $header) {
                    if (strpos($header, 'Content-Type:') === 0) {
                        $content_type = $header;
                        break;
                    }
                }
                // Kiểm tra xem có phải là ảnh không
                if (strpos($content_type, 'image') !== false) {
                    return ['success' => true, 'qr_url' => $qrUrl];
                }
            }
            return ['success' => false, 'qr_url' => ''];
        }
        
        if (isset($_POST['check_account'])) {
            // Chức năng kiểm tra
            $test_result = testVietQRAPI($bank_bin, $account_number, $account_name);
            
            if ($test_result['success']) {
                $message = '<div class="alert alert-success">Thông tin tài khoản hợp lệ! Mã QR đã được tạo thành công.</div>';
                $qr_url = $test_result['qr_url'];
                $show_qr = true;
            } else {
                $message = '<div class="alert alert-danger">Thông tin tài khoản không hợp lệ hoặc không thể kết nối đến VietQR API!</div>';
            }
        } elseif (isset($_POST['save_account'])) {
            // Chức năng lưu (kiểm tra trước khi lưu)
            $test_result = testVietQRAPI($bank_bin, $account_number, $account_name);
            
            if ($test_result['success']) {
                try {
                    // Xóa thông tin cũ
                    $stmt = $conn->prepare("DELETE FROM bank");
                    $stmt->execute();
                    
                    // Lưu thông tin mới
                    $stmt = $conn->prepare("INSERT INTO bank (bank_bin, account_number, account_name, update_at) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$bank_bin, $account_number, $account_name]);
                    
                    $message = '<div class="alert alert-success">Thông tin chính xác, đã lưu thành công!</div>';
                } catch (Exception $e) {
                    $message = '<div class="alert alert-danger">Lỗi khi lưu: ' . $e->getMessage() . '</div>';
                }
            } else {
                $message = '<div class="alert alert-danger">Thông tin tài khoản không hợp lệ! Vui lòng kiểm tra lại.</div>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thiết lập tài khoản thụ hưởng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/manage_bank.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'nav_bar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>THIẾT LẬP TÀI KHOẢN THỤ HƯỞNG</h1>
            </header>
            
            <div class="container">
                <?php if ($message): ?>
                    <?php echo $message; ?>
                <?php endif; ?>
                
                <div class="content-wrapper">
                    <div class="form-section">
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="bank_display">Chọn ngân hàng:</label>
                                <div class="bank-search-container">
                                    <input type="text" id="bank_display" class="bank-search-input" placeholder="Chọn ngân hàng..." readonly>
                                    <i class="bi bi-chevron-down bank-search-icon"></i>
                                    <div class="bank-dropdown">
                                        <div class="bank-search-box">
                                            <input type="text" id="bank_search" placeholder="Tìm kiếm ngân hàng...">
                                        </div>
                                        <div class="bank-list" id="bank_list">
                                            <!-- Danh sách ngân hàng sẽ được tạo bởi JavaScript -->
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="bank_bin" name="bank_bin" value="<?php echo htmlspecialchars($bank_bin); ?>">
                                <div class="help-text">Tìm kiếm theo tên ngân hàng hoặc mã ngắn</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="account_number">Số tài khoản:</label>
                                <input type="text" id="account_number" name="account_number" value="<?php echo htmlspecialchars($account_number); ?>" required>
                                <div class="help-text">Nhập đúng số tài khoản ngân hàng</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="account_name">Tên tài khoản:</label>
                                <input type="text" id="account_name" name="account_name" value="<?php echo htmlspecialchars($account_name); ?>" required>
                                <div class="help-text">Tên chủ tài khoản (viết hoa, không dấu)</div>
                            </div>
                            
                            <div class="button-group">
                                <button type="submit" name="check_account" class="btn btn-primary">Kiểm tra</button>
                                <button type="submit" name="save_account" class="btn btn-success">Lưu thông tin</button>
                            </div>
                        </form>
                    </div>
                    
                    <?php if ($show_qr && $qr_url): ?>
                    <div class="qr-section">
                        <h3>Mã QR mẫu</h3>
                        <img src="<?php echo htmlspecialchars($qr_url); ?>" alt="Mã QR Test" class="qr-image">
                        <div class="qr-info">
                            <p><strong>Ngân hàng:</strong> <?php echo htmlspecialchars($bank_bin); ?></p>
                            <p><strong>Số TK:</strong> <?php echo htmlspecialchars($account_number); ?></p>
                            <p><strong>Tên TK:</strong> <?php echo htmlspecialchars($account_name); ?></p>
                            <p><strong>Số tiền test:</strong> 50,000 VND</p>
                            <p><strong>Nội dung:</strong> Test connection</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     <script src="../js/searchform.js"></script>
    
</body>
</html>