<?php
/**
 * Riwayat Absensi - Siswa
 * File: pages/siswa/riwayat-absensi.php
 */

require_once '../../config/config.php';
requireLogin();
requireRole('Siswa');

$db = getDB();
$id_siswa = getUserId();

// Filter
$filter_bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$filter_tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Get riwayat absensi
$riwayat = db_fetch_all("
    SELECT ab.*, jp.tanggal, jp.hari, jp.minggu_ke,
           k.nama_kelas, j.nama_jurusan,
           GROUP_CONCAT(s.nama_lengkap SEPARATOR ', ') as anggota_nama
    FROM tb_absensi_piket ab
    INNER JOIN tb_jadwal_piket jp ON ab.id_jadwal = jp.id_jadwal
    INNER JOIN tb_kelas k ON jp.id_kelas = k.id_kelas
    INNER JOIN tb_jurusan j ON k.id_jurusan = j.id_jurusan
    INNER JOIN tb_anggota_piket ap ON jp.id_jadwal = ap.id_jadwal
    LEFT JOIN tb_siswa s ON ap.id_siswa = s.id_siswa
    WHERE ap.id_siswa = $id_siswa
    AND MONTH(ab.tanggal_absensi) = $filter_bulan
    AND YEAR(ab.tanggal_absensi) = $filter_tahun
    GROUP BY ab.id_absensi
    ORDER BY ab.tanggal_absensi DESC
");

$bulan_indo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

$page_title = "Riwayat Absensi";
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
        .riwayat-item { border-left: 4px solid #667eea; padding: 20px; background: white; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .foto-thumb { width: 150px; height: 150px; object-fit: cover; border-radius: 10px; cursor: pointer; transition: all 0.3s; margin: 5px; }
        .foto-thumb:hover { transform: scale(1.05); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .foto-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin-top: 15px; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-clipboard-check fa-2x mb-2"></i>
            <h3>PIKETIN</h3>
            <small>Siswa Panel</small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="jadwal-piket.php"><i class="fas fa-calendar-check"></i> Jadwal Piket</a></li>
            <li><a href="absensi-piket.php"><i class="fas fa-camera"></i> Absensi Piket</a></li>
            <li><a href="riwayat-absensi.php" class="active"><i class="fas fa-history"></i> Riwayat Absensi</a></li>
            <li><a href="profil.php"><i class="fas fa-user-cog"></i> Profil</a></li>
            <li><a href="../../controllers/AuthController.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <div>
                <h4 class="mb-0"><?php echo $page_title; ?></h4>
                <small class="text-muted">Lihat riwayat absensi piket Anda</small>
            </div>
            <div class="user-info">
                <div class="text-end">
                    <div class="fw-bold"><?php echo getUserName(); ?></div>
                    <small class="text-muted"><?php echo $_SESSION['nama_kelas']; ?></small>
                </div>
                <img src="../../uploads/foto-siswa/<?php echo $_SESSION['foto_profil']; ?>" 
                     alt="Avatar" class="user-avatar" onerror="this.src='https://via.placeholder.com/45'">
            </div>
        </div>
        
        <!-- Filter -->
        <div class="card-custom">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Bulan</label>
                    <select class="form-select" name="bulan" onchange="this.form.submit()">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo $filter_bulan == $m ? 'selected' : ''; ?>>
                                <?php echo $bulan_indo[$m]; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tahun</label>
                    <select class="form-select" name="tahun" onchange="this.form.submit()">
                        <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                            <option value="<?php echo $y; ?>" <?php echo $filter_tahun == $y ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Riwayat List -->
        <h5 class="mb-3">
            <i class="fas fa-list"></i> Riwayat <?php echo $bulan_indo[$filter_bulan] . ' ' . $filter_tahun; ?>
            <span class="badge bg-info ms-2"><?php echo count($riwayat); ?> Absensi</span>
        </h5>
        
        <?php if (count($riwayat) > 0): ?>
            <?php foreach ($riwayat as $item): ?>
            <div class="riwayat-item">
                <div class="row">
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">
                                    <?php echo formatTanggal($item['tanggal'], 'D, d F Y'); ?>
                                </h5>
                                <span class="badge bg-primary"><?php echo $item['hari']; ?></span>
                                <span class="badge bg-secondary">Minggu ke-<?php echo $item['minggu_ke']; ?></span>
                            </div>
                            <div>
                                <?php if ($item['status'] == 'Tepat Waktu'): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i> Tepat Waktu
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock"></i> Terlambat
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <p class="mb-2">
                            <i class="fas fa-clock text-muted"></i> 
                            <strong>Waktu Absensi:</strong> <?php echo $item['waktu_absensi']; ?>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-users text-muted"></i> 
                            <strong>Anggota:</strong> <?php echo $item['anggota_nama']; ?>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-map-marker-alt text-muted"></i> 
                            <strong>Lokasi:</strong> <?php echo $item['lokasi_gps']; ?>
                        </p>
                        <?php if ($item['keterangan']): ?>
                        <p class="mb-0">
                            <i class="fas fa-comment text-muted"></i> 
                            <strong>Keterangan:</strong> <?php echo $item['keterangan']; ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-4">
                        <h6 class="mb-2"><i class="fas fa-images"></i> Foto Dokumentasi</h6>
                        <div class="foto-grid">
                            <img src="../../uploads/foto-absensi/<?php echo $item['foto_anggota']; ?>" 
                                 class="foto-thumb" alt="Anggota"
                                 onclick="showImage('<?php echo $item['foto_anggota']; ?>', 'Foto Anggota')"
                                 onerror="this.src='https://via.placeholder.com/150'">
                            <img src="../../uploads/foto-absensi/<?php echo $item['foto_area1']; ?>" 
                                 class="foto-thumb" alt="Area 1"
                                 onclick="showImage('<?php echo $item['foto_area1']; ?>', 'Foto Area 1')"
                                 onerror="this.src='https://via.placeholder.com/150'">
                            <img src="../../uploads/foto-absensi/<?php echo $item['foto_area2']; ?>" 
                                 class="foto-thumb" alt="Area 2"
                                 onclick="showImage('<?php echo $item['foto_area2']; ?>', 'Foto Area 2')"
                                 onerror="this.src='https://via.placeholder.com/150'">
                            <img src="../../uploads/foto-absensi/<?php echo $item['foto_area3']; ?>" 
                                 class="foto-thumb" alt="Area 3"
                                 onclick="showImage('<?php echo $item['foto_area3']; ?>', 'Foto Area 3')"
                                 onerror="this.src='https://via.placeholder.com/150'">
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <p class="mb-0">Belum ada riwayat absensi untuk bulan ini</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Modal View Image -->
    <div class="modal fade" id="modalImage" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageTitle">Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImageSrc" src="" class="img-fluid" style="max-height: 80vh;">
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function showImage(filename, title) {
            document.getElementById('modalImageSrc').src = '../../uploads/foto-absensi/' + filename;
            document.getElementById('imageTitle').textContent = title;
            new bootstrap.Modal(document.getElementById('modalImage')).show();
        }
    </script>
       <script src="../../assets/js/darkmode.js"></script>
</body>
</html>