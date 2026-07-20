<?php
// login.php
session_start();
require_once 'data.php';

// Nếu đã đăng nhập thì tự động chuyển hướng sang dashboard
if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Tài khoản cứng kiểm tra đăng nhập
    if ($username === 'admin' && $password === 'MiniShop@03') {
        $_SESSION['auth'] = true;
        $_SESSION['username'] = 'admin';
        
        // Khởi tạo giỏ hàng đặt thử nếu chưa có
        if (!isset($_SESSION['orders'])) {
            $_SESSION['orders'] = [];
        }

        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Sai tài khoản hoặc mật khẩu!';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập — MiniShop</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>MiniShop</h2>
            <p>Vui lòng đăng nhập hệ thống quản lý</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-msg">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Tài khoản</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Nhập tài khoản" required autofocus autocomplete="off">
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="password">Mật khẩu</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Nhập mật khẩu" required autocomplete="off">
            </div>

            <button type="submit" class="btn-primary">Đăng nhập</button>
        </form>
    </div>
</body>
</html>
