<?php
session_start();
include 'db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($username) || empty($password)) {
        $error = "Vui lòng điền đầy đủ thông tin.";
    } elseif (strlen($username) > 50) {
        $error = "Tên đăng nhập quá dài (tối đa 50 ký tự).";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role IN ('admin', 'customer')");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                // check role
                if ($user['role'] === 'admin') {
                    header("Location: admin/index.php");
                } elseif ($user['role'] === 'customer') {
                    header("Location: profile.php");
                }
                exit;
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng.";
                error_log("Failed login attempt for username: $username", 3, 'errors.log');
            }
        } catch (PDOException $e) {
            $error = "Lỗi hệ thống, vui lòng thử lại sau.";
            error_log("Login error: " . $e->getMessage(), 3, 'errors.log');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 1.8rem;
        }

        .login-form .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .login-form .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .login-form .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .login-form button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-form button:hover {
            background-color: #218838;
        }

        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .register-link {
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
                margin: 10px;
            }

            .login-container h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>ĐĂNG NHẬP</h2>
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form class="login-form" method="POST" action="">
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" id="username" name="username" value="" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Đăng nhập</button>
        </form>
        <p>Không phải admin? <a href="index.php" class="register-link">Quay về trang chủ</a></p>
    </div>
</body>
</html>
