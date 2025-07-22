<!-- filepath: /c:/wamp64/www/Jewerlry/admin/manage_admin.php -->
<?php
session_start();
include '../db_connect.php';

// Xử lý thêm admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admins (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$username, $email, $password]);
    header("Location: manage_admin.php");
    exit();
}

// Xử lý chỉnh sửa admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_admin'])) {
    $admin_id = $_POST['admin_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($password) {
        $stmt = $conn->prepare("UPDATE admins SET username = ?, email = ?, password = ? WHERE id = ?");
        $stmt->execute([$username, $email, $password, $admin_id]);
    } else {
        $stmt = $conn->prepare("UPDATE admins SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $admin_id]);
    }
    header("Location: manage_admin.php");
    exit();
}

// Xử lý xóa admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_admin'])) {
    $admin_id = $_POST['admin_id'];
    $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
    $stmt->execute([$admin_id]);
    header("Location: manage_admin.php");
    exit();
}

// Lấy danh sách admins
$stmt = $conn->query("SELECT * FROM admins");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins - Jewelry Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Reset mặc định */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            color: #333;
            line-height: 1.6;
        }

        /* Layout chính */
        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar (từ nav_bar.php) */
        .sidebar {
            width: 250px;
            background-color: #e74c3c;
            padding: 20px;
            color: #fff;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: width 0.3s;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 30px;
            text-align: center;
            color: #fff;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .sidebar ul li a i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .sidebar ul li.active a,
        .sidebar ul li a:hover {
            background-color: #c0392b;
        }

        .sidebar ul li:last-child a {
            margin-top: 20px;
            border-top: 1px solid #fff;
            padding-top: 15px;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            margin-left: 250px; /* Đảm bảo nội dung không bị che bởi sidebar */
            padding: 30px;
            background-color: #fff;
        }

        .main-content header h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #e74c3c;
            background-color: #ffebee;
            padding: 10px 20px;
            border-radius: 5px;
        }

        /* Table */
        .table {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            background-color: #e74c3c;
            color: #fff;
            border: none;
        }

        .table tbody tr:hover {
            background-color: #ffebee;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.9rem;
        }

        /* Modal */
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background-color: #e74c3c;
            color: #fff;
            border-bottom: none;
        }

        .modal-title {
            font-size: 1.25rem;
        }

        .btn-close {
            filter: invert(1);
        }

        .modal-body {
            padding: 20px;
        }

        .form-label {
            font-weight: 500;
            color: #e74c3c;
        }

        .form-control:focus {
            border-color: #e74c3c;
            box-shadow: 0 0 5px rgba(231, 76, 60, 0.5);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
                padding: 10px;
            }

            .sidebar h2 {
                font-size: 1rem;
                margin-bottom: 15px;
            }

            .sidebar ul li a {
                justify-content: center;
                padding: 10px;
            }

            .sidebar ul li a span {
                display: none;
            }

            .sidebar ul li a i {
                margin-right: 0;
            }

            .main-content {
                margin-left: 80px;
                padding: 15px;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .btn-sm {
                padding: 3px 6px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'nav_bar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>Manage Admins</h1>
            </header>
            <section>
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                    <i class="bi bi-plus"></i> Add New Admin
                </button>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $admin): ?>
                                <tr>
                                    <td><?php echo $admin['id']; ?></td>
                                    <td><?php echo htmlspecialchars($admin['username']); ?></td>
                                    <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($admin['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editAdminModal_<?php echo $admin['id']; ?>">Edit</button>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                            <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                            <input type="hidden" name="delete_admin" value="1">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Admin Modal -->
                                <div class="modal fade" id="editAdminModal_<?php echo $admin['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Admin #<?php echo $admin['id']; ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST">
                                                    <div class="mb-3">
                                                        <label class="form-label">Username</label>
                                                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">New Password (leave blank to keep current)</label>
                                                        <input type="password" name="password" class="form-control">
                                                    </div>
                                                    <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                                    <input type="hidden" name="edit_admin" value="1">
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" form="editAdminForm_<?php echo $admin['id']; ?>" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <!-- Add Admin Modal -->
    <div class="modal fade" id="addAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addAdminForm" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <input type="hidden" name="add_admin" value="1">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="addAdminForm" class="btn btn-primary">Add Admin</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>