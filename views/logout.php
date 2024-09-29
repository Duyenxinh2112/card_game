<?php
session_start();
session_destroy();  // Hủy session để đăng xuất người dùng
header("Location: login.php");  // Chuyển về trang đăng nhập
exit();
?>
