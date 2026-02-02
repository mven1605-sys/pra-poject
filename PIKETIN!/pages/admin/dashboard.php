<?php
/**
 * Admin Dashboard
 * File: pages/admin/dashboard.php
 */

// Include config
require_once '../../config/config.php';

// Check login dan role
requireLogin();
requireRole('Admin');

// Get database
$db = getDB();

// Get statistics
$total_siswa = db_fetch_one("SELECT COUNT(*) as total FROM tb_siswa WHERE is_active = 1");
$total_siswa = $total_siswa ? $total_siswa['total'] : 0;

$total_guru = db_fetch_one("SELECT COUNT(*) as total FROM tb_guru WHERE is_active = 1");
$total_guru = $total_guru ? $total_guru['total'] : 0;

$total_kelas = db_fetch_one("SELECT COUNT(*) as total FROM tb_kelas");
$total_kelas = $total_kelas ? $total_kelas['total'] : 0;

$total_jurusan = db_fetch_one("SELECT COUNT(*) as total FROM tb_jurusan");
$total_jurusan = $total_jurusan ? $total_jurusan['total'] : 0;

// Get today's schedule - simplified query
$today = getCurrentDate();
$hari_ini = getNamaHari($today);

// Check if tables exist first
$jadwal_hari_ini = [];
try {
    $jadwal_hari_ini = db_fetch_all("
        SELECT jp.*, k.nama_kelas, j.nama_jurusan
        FROM tb_jadwal_piket jp
        INNER JOIN tb_kelas k ON jp.id_kelas = k.id_kelas
        INNER JOIN tb_jurusan j ON k.id_jurusan = j.id_jurusan
        WHERE jp.tanggal = '$today'
        ORDER BY k.nama_kelas
        LIMIT 10
    ");
} catch (Exception $e) {
    // If tables don't exist, use empty array
    $jadwal_hari_ini = [];
}

// Get recent activities - simplified
$recent_activities = [];
try {
    $recent_activities = db_fetch_all("
        SELECT * FROM tb_activity_log 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
} catch (Exception $e) {
    // If table doesn't exist, use empty array
    $recent_activities = [];
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
    
    <!-- Custom CSS -->
    <link href="../../asset/css/dashboard.css" rel="stylesheet">
    <link href="../../asset/css/darkmode.css" rel="stylesheet">
    
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
        
        .card-header-custom {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .badge-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-belum {
            background: #ffc107;
            color: #fff;
        }
        
        .badge-selesai {
            background: #28a745;
            color: #fff;
        }
        
        .badge-terlambat {
            background: #dc3545;
            color: #fff;
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
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="kelola-siswa.php"><i class="fas fa-user-graduate"></i> Kelola Siswa</a></li>
            <li><a href="kelola-guru.php"><i class="fas fa-chalkboard-teacher"></i> Kelola Guru</a></li>
            <li><a href="kelola-kelas.php"><i class="fas fa-door-open"></i> Kelola Kelas</a></li>
            <li><a href="kelola-jurusan.php"><i class="fas fa-book"></i> Kelola Jurusan</a></li>
            <li><a href="jadwal-piket.php"><i class="fas fa-calendar-alt"></i> Jadwal Piket</a></li>
            <li><a href="riwayat-absensi.php"><i class="fas fa-history"></i> Riwayat Absensi</a></li>
            <li><a href="laporan-piket.php"><i class="fas fa-file-alt"></i> Laporan Piket</a></li>
            <li><a href="profil.php"><i class="fas fa-user-cog"></i> Profil</a></li>
            <li><a href="../../controllers/AuthController.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div>
                <h4 class="mb-0"><?php echo $page_title; ?></h4>
                <small class="text-muted"><?php echo formatTanggal($today, 'lengkap'); ?></small>
            </div>
            <div class="user-info">
                <div class="text-end">
                    <div class="fw-bold"><?php echo getUserName(); ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
                <img src="../../uploads/foto-admin/<?php echo $_SESSION['foto_profil'] ?? 'default-avatar.png'; ?>" 
                     alt="Avatar" class="user-avatar" 
                     onerror="this.src='https://via.placeholder.com/45'">
            </div>
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
        
        <!-- Jadwal Piket Hari Ini -->
        <div class="card-custom mb-4">
            <div class="card-header-custom">
                <h5 class="mb-0"><i class="fas fa-calendar-day"></i> Jadwal Piket Hari Ini (<?php echo $hari_ini; ?>)</h5>
            </div>
            
            <?php if (count($jadwal_hari_ini) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Jumlah Anggota</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jadwal_hari_ini as $index => $jadwal): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $jadwal['nama_kelas']; ?></td>
                            <td><?php echo $jadwal['nama_jurusan']; ?></td>
                            <td>-</td>
                            <td>
                                <span class="badge-status badge-belum">Belum Absen</span>
                            </td>
                            <td>
                                <a href="riwayat-absensi.php?id=<?php echo $jadwal['id_jadwal']; ?>" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i> Tidak ada jadwal piket untuk hari ini.
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Recent Activities -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h5 class="mb-0"><i class="fas fa-list"></i> Aktivitas Terbaru</h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_activities as $activity): ?>
                        <tr>
                            <td><?php echo formatTanggal($activity['created_at'], 'd-m-Y H:i'); ?></td>
                            <td>ID: <?php echo $activity['user_id']; ?></td>
                            <td><span class="badge bg-primary"><?php echo $activity['user_role']; ?></span></td>
                            <td><?php echo $activity['activity']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../../asset/js/darkmode.js"></script>
</body>
</html>