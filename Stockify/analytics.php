<?php
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];

// Data untuk grafik kategori
$query_kategori = "SELECT kategori, COUNT(*) as jumlah, SUM(stok) as total_stok 
                   FROM barang GROUP BY kategori ORDER BY total_stok DESC";
$result_kategori = $koneksi->query($query_kategori);

$kategori_labels = [];
$kategori_data = [];
$stok_data = [];

while ($row = $result_kategori->fetch_assoc()) {
    $kategori_labels[] = $row['kategori'];
    $kategori_data[] = $row['jumlah'];
    $stok_data[] = $row['total_stok'];
}

// Data stok kritis
$query_kritis = "SELECT nama_barang, stok, lokasi_rak FROM barang WHERE stok < 10 ORDER BY stok ASC";
$result_kritis = $koneksi->query($query_kritis);

// Top 5 barang dengan stok terbanyak
$query_top = "SELECT nama_barang, stok, kategori FROM barang ORDER BY stok DESC LIMIT 5";
$result_top = $koneksi->query($query_top);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Stockify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: fixed;
            width: 250px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .chart-container {
            position: relative;
            height: 400px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="text-center py-4">
            <i class="bi bi-box-seam fs-1"></i>
            <h4 class="mt-2">Stockify</h4>
        </div>
        
        <div class="text-center py-3" style="background: rgba(255,255,255,0.2);">
            <h6><?= htmlspecialchars($username) ?></h6>
            <small><?= strtoupper($role) ?></small>
        </div>
        
        <nav class="nav flex-column mt-3">
            <a class="nav-link" href="dashboard.php">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a class="nav-link" href="barang.php">
                <i class="bi bi-box me-2"></i> Data Barang
            </a>
            <a class="nav-link active" href="analytics.php">
                <i class="bi bi-graph-up me-2"></i> Analytics
            </a>
            <a class="nav-link" href="logout.php">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4"><i class="bi bi-graph-up me-2"></i>Dashboard Analytics</h2>

            <div class="row mb-4">
                <!-- Grafik Kategori -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <i class="bi bi-bar-chart-fill me-2"></i>Jumlah Barang per Kategori
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="kategoriChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grafik Stok per Kategori -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <i class="bi bi-pie-chart-fill me-2"></i>Total Stok per Kategori
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="stokChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Stok Kritis -->
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Peringatan: Stok Kritis
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th>Stok</th>
                                            <th>Lokasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result_kritis->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                            <td><span class="badge bg-danger"><?= $row['stok'] ?></span></td>
                                            <td><?= htmlspecialchars($row['lokasi_rak']) ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top 5 Barang -->
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <i class="bi bi-trophy-fill me-2"></i>Top 5 Barang Terbanyak
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th>Kategori</th>
                                            <th>Stok</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result_top->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                            <td><span class="badge bg-secondary"><?= $row['kategori'] ?></span></td>
                                            <td><span class="badge bg-success"><?= $row['stok'] ?></span></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Data dari PHP
        const kategoriLabels = <?= json_encode($kategori_labels) ?>;
        const kategoriData = <?= json_encode($kategori_data) ?>;
        const stokData = <?= json_encode($stok_data) ?>;

        // Grafik Kategori (Bar Chart)
        const ctxKategori = document.getElementById('kategoriChart').getContext('2d');
        new Chart(ctxKategori, {
            type: 'bar',
            data: {
                labels: kategoriLabels,
                datasets: [{
                    label: 'Jumlah Barang',
                    data: kategoriData,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Distribusi Barang Berdasarkan Kategori'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Grafik Stok (Pie Chart)
        const ctxStok = document.getElementById('stokChart').getContext('2d');
        new Chart(ctxStok, {
            type: 'pie',
            data: {
                labels: kategoriLabels,
                datasets: [{
                    data: stokData,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    title: {
                        display: true,
                        text: 'Persentase Total Stok per Kategori'
                    }
                }
            }
        });
    </script>
</body>
</html>