<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Quản Lý Game Lật Bài</title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>

<body class="body-login">

    <?php
    // Khởi động session
    session_start();

    // Mảng người dùng mẫu (có thể thay bằng truy vấn CSDL)
    $users = [
        'admin' => 'password123',
        'user1' => 'user1password',
        'user2' => 'user2password'
    ];

    $error = '';

    // Xử lý khi form đăng nhập được gửi
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Kiểm tra thông tin đăng nhập
        if (isset($users[$username]) && $users[$username] === $password) {
            // Lưu thông tin đăng nhập vào session
            $_SESSION['username'] = $username;
            header("Location: home.php"); // Điều hướng đến trang chủ sau khi đăng nhập thành công
            exit();
        } else {
            $error = "Tên đăng nhập hoặc mật khẩu không chính xác!";
        }
    }
    ?>

    <div class="login-container">
        <h2>Đăng Nhập</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Đăng Nhập</button>
        </form>
    </div>

</body>

</html>