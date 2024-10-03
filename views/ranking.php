<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xếp Hạng Người Chơi - Quản Lý Game Lật Bài</title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>

<body id="body">
    <div id="header">
        <?php include 'header.php'; ?>
    </div>

    <main>
        <section class="player-ranking">
            <h2 style="margin-top: 20px;">Xếp Hạng Người Chơi</h2>
            <table>
                <thead>
                    <tr>
                        <th>Hạng</th>
                        <th>ID Người Chơi</th>
                        <th>Người Chơi</th>
                        <th>Tổng Điểm</th>
                        <th>Cập Nhật Gần Nhất</th>
                    </tr>
                </thead>
                <tbody id="leaderboard-body">
                    <!-- Dữ liệu sẽ được chèn vào đây bởi JavaScript -->
                </tbody>
            </table>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('../app/api/readLeaderboard.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('leaderboard-body');
                    tbody.innerHTML = ''; // Xóa nội dung cũ

                    if (data.status === 200 && Array.isArray(data.data) && data.data.length > 0) {
                        data.data.forEach((row, index) => {
                            const formattedDate = new Date(row.last_update).toLocaleDateString('vi-VN');
                            let rankImage = '';

                            // Thứ hạng dựa vào chỉ số index + 1 (để thứ hạng bắt đầu từ 1)
                            const rank = index + 1;

                            if (rank === 1) {
                                rankImage = '<img style="width: 40px;" src="../public/img/1.png" alt="Cup 1">';
                            } else if (rank === 2) {
                                rankImage = '<img style="width: 40px;" src="../public/img/2.png" alt="Cup 2">';
                            } else if (rank === 3) {
                                rankImage = '<img style="width: 40px;" src="../public/img/3.png" alt="Cup 3">';
                            } else {
                                rankImage = rank;
                            }

                            tbody.innerHTML += `
                                <tr>
                                    <td>${rankImage}</td>
                                    <td>${row.user_id}</td>
                                    <td>${row.username || 'Chưa có tên'}</td>
                                    <td>${row.max_total_score}</td>
                                    <td>${formattedDate}</td>
                                </tr>
                            `;
                        });
                    } else {
                        tbody.innerHTML = "<tr><td colspan='5'>Không có dữ liệu</td></tr>";
                    }
                })
                .catch(error => {
                    console.error('Error fetching leaderboard data:', error);
                    document.getElementById('leaderboard-body').innerHTML = "<tr><td colspan='5'>Đã xảy ra lỗi khi tải dữ liệu</td></tr>";
                });
        });
    </script>
</body>

</html>