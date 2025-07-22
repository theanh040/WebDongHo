<!-- filepath: /c:/wamp64/www/Jewerlry/admin/nav_bar.php -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
            <a href="index.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'manage_product.php' ? 'active' : ''; ?>">
            <a href="manage_product.php">
                <i class="bi bi-box"></i> Products
            </a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'manage_order.php' ? 'active' : ''; ?>">
            <a href="manage_order.php">
                <i class="bi bi-cart"></i> Orders
            </a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'manage_admin.php' ? 'active' : ''; ?>">
            <a href="manage_admin.php">
                <i class="bi bi-people"></i> Admins
            </a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'manage_bankacc.php' ? 'active' : ''; ?>">
            <a href="manage_bankacc.php">
                <i class="bi bi-bank"></i> Bank Accounts
            </a>
        </li>
        <li>
            <a href="../index.php">
                <i class="bi bi-house"></i> Back to Site
            </a>
        </li>
    </ul>
</div>