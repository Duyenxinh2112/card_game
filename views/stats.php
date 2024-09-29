<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê - Quản Lý Game Lật Bài</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body id="body">

    <?php include 'header.php'; ?>

    <main>
        <section class="stats-section">
            <h2 style="margin-top: 20px;">Thống Kê Trận Đấu</h2>
            <canvas id="statsChart" width="400" height="200"></canvas>
            <script>
                Promise.all([
                        fetch('../app/api/readCountMatchesAI.php')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok ' + response.statusText);
                            }
                            return response.json();
                        }),
                        fetch('../app/api/readCountMatchesUser.php')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok ' + response.statusText);
                            }
                            return response.json();
                        })
                    ])
                    .then(([aiData, userData]) => {
                        if (aiData.status === 200 && aiData.data.length > 0 && userData.status === 200 && userData.data.length > 0) {
                            renderChart(aiData, userData);
                        } else {
                            console.error('Dữ liệu không hợp lệ:', aiData, userData);
                        }
                    })
                    .catch(error => console.error('Có vấn đề với việc gọi API:', error));

                function renderChart(aiStatistic, userStatistic) {
                    const aiWins = parseInt(aiStatistic.data[0].total_wins, 10);
                    const aiLosses = parseInt(aiStatistic.data[0].total_losses, 10);
                    const playerWins = parseInt(userStatistic.data[0].total_wins, 10);
                    const playerLosses = parseInt(userStatistic.data[0].total_losses, 10);

                    const labels = ['Trận Thắng', 'Trận Thua'];
                    const chartData = {
                        labels: labels,
                        datasets: [{
                            label: 'Người Chơi',
                            data: [playerWins, playerLosses],
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }, {
                            label: 'AI',
                            data: [aiWins, aiLosses],
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }]
                    };

                    const config = {
                        type: 'bar',
                        data: chartData,
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        },
                    };

                    // Vẽ biểu đồ
                    const statsChart = new Chart(
                        document.getElementById('statsChart'),
                        config
                    );
                }
            </script>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Quản Lý Game Lật Bài. All rights reserved.</p>
    </footer>

</body>

</html>