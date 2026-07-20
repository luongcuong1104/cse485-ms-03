<?php
// logout.php
session_start();

// Hủy bỏ session hiện tại
$_SESSION = [];
session_destroy();

// Chuyển hướng về trang đăng nhập
header('Location: login.php');
exit;
