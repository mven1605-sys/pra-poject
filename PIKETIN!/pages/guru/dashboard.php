<?php
/**
 * Dashboard Guru
 * File: pages/guru/dashboard.php
 */

require_once '../../config/config.php';
requireLogin();
requireRole('Guru');

$db = getDB();
$id_guru = getUserId();
$id_kelas = $_SESSION['id_kelas'];
$today = getCurrentDate();

// Check if guru adalah wali kelas
if (empty($id_kelas)) {
    header('Location: ../../index.php');
    exit();
}

// Get jadwal hari ini untuk kelas yang diampu
$jadwal_hari_ini = db_fetch_one("
    SELECT jp.*, ab.id_absensi, ab.status as status_absensi,
           GROUP_CONCAT(s.nama_lengkap SEPARATOR ', ') as anggota_nama,
           COUNT(DISTINCT ap.id_siswa) as jumlah_anggota
    FROM tb_jadwal_piket jp
    INNER JOIN tb_anggota_piket ap ON jp.id_jadwal = ap.id_jadwal
    LEFT JOIN tb_siswa s ON ap.id_siswa = s.id_siswa
    LEFT JOIN tb_absensi_piket ab ON jp.id_jadwal = ab.id_jadwal
    WHERE jp.id_kelas = $id_kelas AND jp.tanggal = '$today'
    GROUP BY jp.id_jadwal
");

// Get statistik bulan ini
$current_month = date('n');
$current_year = date('Y');

$stats = db_fetch_one("
    SELECT 
        COUNT(DISTINCT jp.id_jadwal) as total_jadwal,
        COUNT(DISTINCT ab.id_absensi) as total_absen,
        SUM(CASE WHEN ab.status = 'Tepat Waktu' THEN 1 ELSE 0 END) as tepat_waktu,
        SUM(CASE WHEN ab.status = 'Terlambat' THEN 1 ELSE 0 END) as terlambat
    FROM tb_jadwal_piket jp
    LEFT JOIN tb_absensi_piket ab ON jp.id_jadwal = ab.id_jadwal
    WHERE jp.id_kelas = $id_kelas
    AND MONTH(jp.tanggal) = $current_month
    AND YEAR(jp.tanggal) = $current_year
");

// Get total siswa di kelas
$total_siswa = db_fetch_one("SELECT COUNT(*) as total FROM tb_siswa WHERE id_kelas = $id_kelas AND is_active = 1")['total'];

// Get recent activities (5 terakhir)
$recent_activities = db_fetch_all("
    SELECT ab.*, jp.tanggal, jp.hari,
           GROUP_CONCAT(s.nama_lengkap SEPARATOR ', ') as anggota_nama
    FROM tb_absensi_piket ab
    INNER JOIN tb_jadwal_piket jp ON ab.id_jadwal = jp.id_jadwal
    INNER JOIN tb_anggota_piket ap ON jp.id_jadwal = ap.id_jadwal
    LEFT JOIN tb_siswa s ON ap.id_siswa = s.id_siswa
    WHERE jp.id_kelas = $id_kelas
    GROUP BY ab.id_absensi
    ORDER BY ab.created_at DESC
    LIMIT 5
");

$page_title = "Dashboard Guru";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link href="../../assets/css/darkmode.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - PIKETIN</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body { background: #f8f9fc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { width: 260px; height: 100vh; background: linear-gradient(180deg, #667eea 0%, #764ba2 100%); position: fixed; left: 0; top: 0; color: white; overflow-y: auto; z-index: 1000; }
        .sidebar-header { padding: 25px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h3 { font-size: 24px; font-weight: 700; margin: 0; }
        .sidebar-menu { list-style: none; padding: 20px 0; margin: 0; }
        .sidebar-menu li a { display: flex; align-items: center; padding: 15px 25px; color: white; text-decoration: none; transition: all 0.3s; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: rgba(255,255,255,0.1); border-left: 4px solid white; padding-left: 21px; }
        .sidebar-menu li a i { margin-right: 15px; font-size: 18px; width: 20px; }
        .main-content { margin-left: 260px; padding: 20px; min-height: 100vh; }
        .top-navbar { background: white; padding: 15px 25px; border-radius: 10px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
        .card-custom { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 25px; }
        .stat-number { font-size: 36px; font-weight: 700; }
        .quick-action { background: white; border-radius: 15px; padding: 25px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: all 0.3s; text-decoration: none; display: block; color: inherit; }
        .quick-action:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); color: inherit; }
        .quick-action i { font-size: 48px; margin-bottom: 15px; }
        .activity-item { padding: 15px; border-left: 3px solid #667eea; margin-bottom: 15px; background: #f8f9fc; border-radius: 5px; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-clipboard-check fa-2x mb-2"></i>
            <h3>PIKETIN</h3>
            <small>Guru Panel</small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="riwayat-absensi.php"><i class="fas fa-history"></i> Riwayat Absensi</a></li>
            <li><a href="laporan-piket.php"><i class="fas fa-file-alt"></i> Laporan Piket</a></li>
            <li><a href="profil.php"><i class="fas fa-user-cog"></i> Profil</a></li>
            <li><a href="../../controllers/AuthController.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <div>
                <h4 class="mb-0">Selamat Datang, <?php echo getUserName(); ?>! 👨‍🏫</h4>
                <small class="text-muted">Wali Kelas <?php echo $_SESSION['nama_kelas']; ?> - <?php echo $_SESSION['nama_jurusan']; ?></small>
            </div>
            <div class="user-info">
                <div class="text-end">
                    <div class="fw-bold"><?php echo formatTanggal($today, 'lengkap'); ?></div>
                    <small class="text-muted">NIP: <?php echo $_SESSION['nip']; ?></small>
                </div>
                <img src="../../uploads/foto-guru/<?php echo $_SESSION['foto_profil']; ?>" 
                     alt="Avatar" class="user-avatar" onerror="this.src='https://via.placeholder.com/45'">
            </div>
        </div>
        
        <!-- Jadwal Hari Ini -->
        <?php if ($jadwal_hari_ini): ?>
            <div class="alert alert-<?php echo $jadwal_hari_ini['id_absensi'] ? 'success' : 'warning'; ?>" role="alert">
                <h5 class="alert-heading">
                    <i class="fas fa-calendar-day"></i> Jadwal Piket Hari Ini
                </h5>
                <p class="mb-2">
                    <strong>Anggota (<?php echo $jadwal_hari_ini['jumlah_anggota']; ?> siswa):</strong> 
                    <?php echo $jadwal_hari_ini['anggota_nama']; ?>
                </p>
                <hr>
                <?php if ($jadwal_hari_ini['id_absensi']): ?>
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle"></i> Sudah Absen - <?php echo $jadwal_hari_ini['status_absensi']; ?>
                    </span>
                <?php else: ?>
                    <span class="badge bg-warning">
                        <i class="fas fa-clock"></i> Belum Absen
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <a href="riwayat-absensi.php" class="quick-action">
                    <i class="fas fa-history text-primary"></i>
                    <h6>Riwayat Absensi</h6>
                    <small class="text-muted">Lihat absensi kelas</small>
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="laporan-piket.php" class="quick-action">
                    <i class="fas fa-file-alt text-success"></i>
                    <h6>Laporan Piket</h6>
                    <small class="text-muted">Generate laporan</small>
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="profil.php" class="quick-action">
                    <i class="fas fa-user text-info"></i>
                    <h6>Profil</h6>
                    <small class="text-muted">Edit profil Anda</small>
                </a>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_siswa; ?></div>
                    <div>Total Siswa</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <div class="stat-number"><?php echo $stats['total_jadwal'] ?: 0; ?></div>
                    <div>Jadwal Bulan Ini</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="stat-number"><?php echo $stats['tepat_waktu'] ?: 0; ?></div>
                    <div>Tepat Waktu</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <div class="stat-number"><?php echo $stats['terlambat'] ?: 0; ?></div>
                    <div>Terlambat</div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activities -->
        <div class="card-custom">
            <h5 class="mb-4"><i class="fas fa-list"></i> Aktivitas Terakhir</h5>
            
            <?php if (count($recent_activities) > 0): ?>
                <?php foreach ($recent_activities as $activity): ?>
                <div class="activity-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1"><?php echo formatTanggal($activity['tanggal'], 'D, d F Y'); ?></h6>
                            <small class="text-muted"><?php echo $activity['anggota_nama']; ?></small>
                            <p class="mb-0 mt-1">
                                <i class="fas fa-clock"></i> <?php echo $activity['waktu_absensi']; ?>
                                <span class="badge bg-<?php echo $activity['status'] == 'Tepat Waktu' ? 'success' : 'warning'; ?> ms-2">
                                    <?php echo $activity['status']; ?>
                                </span>
                            </p>
                        </div>
                        <a href="riwayat-absensi.php" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> Belum ada aktivitas
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
     <script src="../../assets/js/darkmode.js"></script>
</body>
</html>