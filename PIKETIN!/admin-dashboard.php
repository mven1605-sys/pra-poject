<?php
/**
 * Admin Dashboard - Direct Access
 */
session_start();

// Simple auth check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    // If not logged in, redirect to login
    header('Location: login.php');
    exit();
}

// Include config
require_once 'config/config.php';

// Get basic stats
$total_siswa = 0;
$total_guru = 0;
$total_kelas = 0;
$total_jurusan = 0;

try {
    $result = db_fetch_one("SELECT COUNT(*) as total FROM tb_siswa WHERE is_active = 1");
    $total_siswa = $result ? $result['total'] : 0;
    
    $result = db_fetch_one("SELECT COUNT(*) as total FROM tb_guru WHERE is_active = 1");
    $total_guru = $result ? $result['total'] : 0;
    
    $result = db_fetch_one("SELECT COUNT(*) as total FROM tb_kelas");
    $total_kelas = $result ? $result['total'] : 0;
    
    $result = db_fetch_one("SELECT COUNT(*) as total FROM tb_jurusan");
    $total_jurusan = $result ? $result['total'] : 0;
} catch (Exception $e) {
    // If tables don't exist, use defaults
}

$page_title = "Dashboard Admin";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - PIKETIN</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            position: fixed;
            left: 0;
            top: 0;
            color: white;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h3 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
            margin: 0;
        }
        
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: rgba(255,255,255,0.1);
            border-left: 4px solid white;
            padding-left: 21px;
        }
        
        .sidebar-menu li a i {
            margin-right: 15px;
            font-size: 18px;
            width: 20px;
        }
        
        .main-content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
        }
        
        .top-navbar {
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border-left: 4px solid #667eea;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #333;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .card-custom {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-clipboard-check fa-2x mb-2"></i>
            <h3>PIKETIN</h3>
            <small>Admin Panel</small>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="admin-dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="pages/admin/kelola-siswa.php"><i class="fas fa-user-graduate"></i> Kelola Siswa</a></li>
            <li><a href="pages/admin/kelola-guru.php"><i class="fas fa-chalkboard-teacher"></i> Kelola Guru</a></li>
            <li><a href="pages/admin/kelola-kelas.php"><i class="fas fa-door-open"></i> Kelola Kelas</a></li>
            <li><a href="pages/admin/kelola-jurusan.php"><i class="fas fa-book"></i> Kelola Jurusan</a></li>
            <li><a href="pages/admin/jadwal-piket.php"><i class="fas fa-calendar-alt"></i> Jadwal Piket</a></li>
            <li><a href="pages/admin/riwayat-absensi.php"><i class="fas fa-history"></i> Riwayat Absensi</a></li>
            <li><a href="pages/admin/laporan-piket.php"><i class="fas fa-file-alt"></i> Laporan Piket</a></li>
            <li><a href="pages/admin/profil.php"><i class="fas fa-user-cog"></i> Profil</a></li>
            <li><a href="controllers/AuthController.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div>
                <h4 class="mb-0"><?php echo $page_title; ?></h4>
                <small class="text-muted"><?php echo date('d F Y'); ?></small>
            </div>
            <div class="user-info">
                <div class="text-end">
                    <div class="fw-bold"><?php echo $_SESSION['nama_lengkap'] ?? 'Admin'; ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
                <img src="https://via.placeholder.com/45" alt="Avatar" class="user-avatar">
            </div>
        </div>
        
        <!-- Success Alert -->
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> Dashboard admin berhasil diperbaiki dan dapat diakses!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?php echo $total_siswa; ?></div>
                            <div class="stat-label">Total Siswa</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?php echo $total_guru; ?></div>
                            <div class="stat-label">Total Guru</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?php echo $total_kelas; ?></div>
                            <div class="stat-label">Total Kelas</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?php echo $total_jurusan; ?></div>
                            <div class="stat-label">Total Jurusan</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card-custom mb-4">
            <h5 class="mb-3"><i class="fas fa-bolt"></i> Aksi Cepat</h5>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <a href="pages/admin/kelola-siswa.php" class="btn btn-primary w-100">
                        <i class="fas fa-user-graduate"></i> Kelola Siswa
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="pages/admin/kelola-guru.php" class="btn btn-success w-100">
                        <i class="fas fa-chalkboard-teacher"></i> Kelola Guru
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="pages/admin/jadwal-piket.php" class="btn btn-warning w-100">
                        <i class="fas fa-calendar-alt"></i> Jadwal Piket
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="pages/admin/laporan-piket.php" class="btn btn-info w-100">
                        <i class="fas fa-file-alt"></i> Laporan
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Info -->
        <div class="card-custom">
            <h6><i class="fas fa-info-circle"></i> Informasi Sistem</h6>
            <p class="mb-0">Dashboard admin telah berhasil diperbaiki. Semua fitur dapat diakses melalui menu sidebar.</p>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>