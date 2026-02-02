<?php
require_once 'koneksi.php';

// PENTING: Proteksi halaman - harus login dulu
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user dari session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role']; // 'admin' atau 'karyawan'

// Statistik Dashboard
$total_barang = $koneksi->query("SELECT COUNT(*) as total FROM barang")->fetch_assoc()['total'];
$total_stok = $koneksi->query("SELECT SUM(stok) as total FROM barang")->fetch_assoc()['total'];
$stok_kritis = $koneksi->query("SELECT COUNT(*) as total FROM barang WHERE stok < 10")->fetch_assoc()['total'];

// Hitung total nilai (hanya admin yang bisa lihat)
if ($role === 'admin') {
    $nilai_beli = $koneksi->query("SELECT SUM(harga_beli * stok) as total FROM barang")->fetch_assoc()['total'];
    $nilai_jual = $koneksi->query("SELECT SUM(harga_jual * stok) as total FROM barang")->fetch_assoc()['total'];
    $potensi_profit = $nilai_jual - $nilai_beli;
}

// Ambil data barang
$query_barang = "SELECT * FROM barang ORDER BY created_at DESC";
$result_barang = $koneksi->query($query_barang);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Stockify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        body {
            background: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            position: fixed;
            width: 250px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stat-card.blue { border-color: #4285f4; }
        .stat-card.green { border-color: #0f9d58; }
        .stat-card.orange { border-color: #f4b400; }
        .stat-card.red { border-color: #db4437; }
        .stat-card.purple { border-color: #ab47bc; }
        .stok-kritis {
            color: #dc3545;
            font-weight: bold;
        }
        .user-badge {
            background: rgba(255,255,255,0.2);
            padding: 10px;
            border-radius: 10px;
            margin: 20px;
        }
        .role-admin {
            background: #28a745;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .role-karyawan {
            background: #17a2b8;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
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
        
        <div class="user-badge text-center">
            <i class="bi bi-person-circle fs-3"></i>
            <h6 class="mt-2 mb-1"><?= htmlspecialchars($username) ?></h6>
            <span class="role-<?= $role ?>"><?= strtoupper($role) ?></span>
        </div>
        
        <nav class="nav flex-column mt-4">
            <a class="nav-link active" href="dashboard.php">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a class="nav-link" href="barang.php">
                <i class="bi bi-box me-2"></i> Data Barang
            </a>
            <?php if ($role === 'admin'): ?>
            <a class="nav-link" href="analytics.php">
                <i class="bi bi-file-earmark-text me-2"></i> Analytics
            </a>
            <a class="nav-link" href="#" onclick="alert('Fitur Kelola User sedang dalam pengembangan')">
                <i class="bi bi-people me-2"></i> Kelola User
            </a>
            <?php endif; ?>
            <a class="nav-link" href="#" onclick="alert('Fitur Profil sedang dalam pengembangan')">
                <i class="bi bi-person me-2"></i> Profil
            </a>
            <hr style="border-color: rgba(255,255,255,0.3);">
            <a class="nav-link" href="logout.php" onclick="return confirm('Yakin ingin logout?')">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
                    <p class="text-muted mb-0">Selamat datang, <strong><?= htmlspecialchars($username) ?></strong></p>
                </div>
                <div>
                    <span class="text-muted"><i class="bi bi-calendar me-2"></i><?= date('d F Y') ?></span>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stat-card blue">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Total Barang</p>
                                    <h3 class="mb-0"><?= $total_barang ?></h3>
                                </div>
                                <div>
                                    <i class="bi bi-box fs-1 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card stat-card green">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Total Stok</p>
                                    <h3 class="mb-0"><?= number_format($total_stok) ?></h3>
                                </div>
                                <div>
                                    <i class="bi bi-boxes fs-1 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card stat-card orange">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Stok Kritis</p>
                                    <h3 class="mb-0 text-danger"><?= $stok_kritis ?></h3>
                                </div>
                                <div>
                                    <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($role === 'admin'): ?>
                <!-- ADMIN ONLY: Nilai Inventory -->
                <div class="col-md-3">
                    <div class="card stat-card purple">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted mb-1 small">Potensi Profit</p>
                                    <h6 class="mb-0"><?= format_rupiah($potensi_profit) ?></h6>
                                </div>
                                <div>
                                    <i class="bi bi-cash-stack fs-1 text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Aksi Cepat</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2 flex-wrap">
                                <?php if ($role === 'admin'): ?>
                                <a href="barang.php" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Barang
                                </a>
                                <?php endif; ?>
                                <a href="barang.php" class="btn btn-info">
                                    <i class="bi bi-eye me-2"></i>Lihat Semua Barang
                                </a>
                                <a href="analytics.php" class="btn btn-success">
                                    <i class="bi bi-graph-up me-2"></i>Analytics
                                </a>
                                <a href="export_excel.php" class="btn btn-success">
                                    <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                                </a>
                                <?php if ($role === 'admin'): ?>
                                <a href="cetak_laporan.php" class="btn btn-danger" target="_blank">
                                    <i class="bi bi-file-pdf me-2"></i>Cetak PDF
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Barang -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Daftar Barang</h5>
                    <input type="text" class="form-control w-25" id="searchInput" placeholder="Cari barang...">
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="barangTable">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <?php if ($role === 'admin'): ?>
                                    <!-- ADMIN ONLY: Lihat harga -->
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <?php endif; ?>
                                    <th>Lokasi Rak</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while ($row = $result_barang->fetch_assoc()): 
                                    // Tentukan class untuk stok kritis
                                    $stok_class = $row['stok'] < 10 ? 'stok-kritis' : '';
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['kategori']) ?></span></td>
                                    <td class="<?= $stok_class ?>">
                                        <?= $row['stok'] ?>
                                        <?php if ($row['stok'] < 10): ?>
                                        <i class="bi bi-exclamation-triangle-fill text-danger ms-1" title="Stok Kritis!"></i>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($role === 'admin'): ?>
                                    <td><?= format_rupiah($row['harga_beli']) ?></td>
                                    <td><?= format_rupiah($row['harga_jual']) ?></td>
                                    <?php endif; ?>
                                    <td><span class="badge bg-info"><?= htmlspecialchars($row['lokasi_rak']) ?></span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="barang.php" 
                                               class="btn btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            <!-- KARYAWAN: Hanya bisa update stok -->
                                            <?php if ($role === 'karyawan'): ?>
                                            <a href="update_stok.php?id=<?= $row['id'] ?>" 
                                               class="btn btn-warning" title="Update Stok">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </a>
                                            <?php endif; ?>
                                            
                                            <!-- ADMIN: Bisa edit dan delete -->
                                            <?php if ($role === 'admin'): ?>
                                            <a href="edit_barang.php?id=<?= $row['id'] ?>" 
                                               class="btn btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="delete_barang.php?id=<?= $row['id'] ?>" 
                                               class="btn btn-danger" title="Hapus"
                                               onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#barangTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
    </script>
</body>
</html>