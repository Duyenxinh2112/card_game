<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");  // Chuyển về trang đăng nhập nếu chưa đăng nhập
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ - Quản Lý Game Lật Bài</title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>
<body id = "body">
    <div id="header">
    <?php include 'header.php'; ?>
    </div>

    <main>
        <img style="width: 100%" src="../public/img/start.png" alt="">
    </main>

    <footer>
        <p>&copy; 2024 Quản Lý Game Lật Bài. All rights reserved.</p>
    </footer>

    <script src="loadHeader.js"></script>
</body>
</html>
