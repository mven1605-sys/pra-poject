<?php
/**
 * Laporan Piket - Guru
 * File: pages/guru/laporan-piket.php
 */

require_once '../../config/config.php';
requireLogin();
requireRole('Guru');

$db = getDB();
$id_kelas = $_SESSION['id_kelas'];

// Filter
$tipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'bulanan';
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
$minggu = isset($_GET['minggu']) ? (int)$_GET['minggu'] : 1;

$bulan_indo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// Get laporan data
if ($tipe == 'mingguan') {
    // Hitung tanggal awal dan akhir minggu
    $first_day = "$tahun-$bulan-01";
    $start_date = date('Y-m-d', strtotime("$first_day +" . (($minggu - 1) * 7) . " days"));
    $end_date = date('Y-m-d', strtotime("$start_date +6 days"));
    
    $where = "jp.tanggal BETWEEN '$start_date' AND '$end_date' AND jp.minggu_ke = $minggu";
    $periode = "Minggu ke-$minggu, " . $bulan_indo[$bulan] . " $tahun";
} else {
    $where = "MONTH(jp.tanggal) = $bulan AND YEAR(jp.tanggal) = $tahun";
    $periode = $bulan_indo[$bulan] . " $tahun";
}

$laporan_data = db_fetch_all("
    SELECT jp.*, ab.waktu_absensi, ab.status,
           GROUP_CONCAT(DISTINCT s.nama_lengkap SEPARATOR ', ') as anggota_nama,
           COUNT(DISTINCT ap.id_siswa) as jumlah_anggota
    FROM tb_jadwal_piket jp
    INNER JOIN tb_anggota_piket ap ON jp.id_jadwal = ap.id_jadwal
    LEFT JOIN tb_siswa s ON ap.id_siswa = s.id_siswa
    LEFT JOIN tb_absensi_piket ab ON jp.id_jadwal = ab.id_jadwal
    WHERE jp.id_kelas = $id_kelas AND $where
    GROUP BY jp.id_jadwal
    ORDER BY jp.tanggal
");

// Hitung summary
$total_jadwal = count($laporan_data);
$total_hadir = 0;
$total_tepat_waktu = 0;
$total_terlambat = 0;
$total_belum = 0;

foreach ($laporan_data as $item) {
    if ($item['waktu_absensi']) {
        $total_hadir++;
        if ($item['status'] == 'Tepat Waktu') {
            $total_tepat_waktu++;
        } else {
            $total_terlambat++;
        }
    } else {
        $total_belum++;
    }
}

$page_title = "Laporan Piket";
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
        
        /* Print Styles */
        @media print {
            body { background: white; }
            .sidebar, .top-navbar, .no-print { display: none !important; }
            .main-content { margin-left: 0; padding: 20px; }
            .card-custom { box-shadow: none; border: 1px solid #ddd; }
        }
        
        .print-header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #667eea; padding-bottom: 20px; }
        .print-header h3 { margin: 0; font-weight: 700; }
        .summary-box { background: #f8f9fc; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar no-print">
        <div class="sidebar-header">
            <i class="fas fa-clipboard-check fa-2x mb-2"></i>
            <h3>PIKETIN</h3>
            <small>Guru Panel</small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="riwayat-absensi.php"><i class="fas fa-history"></i> Riwayat Absensi</a></li>
            <li><a href="laporan-piket.php" class="active"><i class="fas fa-file-alt"></i> Laporan Piket</a></li>
            <li><a href="profil.php"><i class="fas fa-user-cog"></i> Profil</a></li>
            <li><a href="../../controllers/AuthController.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar no-print">
            <div>
                <h4 class="mb-0"><?php echo $page_title; ?></h4>
                <small class="text-muted">Generate dan cetak laporan piket</small>
            </div>
            <div class="user-info">
                <div class="text-end">
                    <div class="fw-bold"><?php echo getUserName(); ?></div>
                    <small class="text-muted"><?php echo $_SESSION['nama_kelas']; ?></small>
                </div>
                <img src="../../uploads/foto-guru/<?php echo $_SESSION['foto_profil']; ?>" 
                     alt="Avatar" class="user-avatar" onerror="this.src='https://via.placeholder.com/45'">
            </div>
        </div>
        
        <!-- Filter -->
        <div class="card-custom no-print">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tipe Laporan</label>
                        <select class="form-select" name="tipe" onchange="toggleMinggu(this.value)">
                            <option value="bulanan" <?php echo $tipe == 'bulanan' ? 'selected' : ''; ?>>Bulanan</option>
                            <option value="mingguan" <?php echo $tipe == 'mingguan' ? 'selected' : ''; ?>>Mingguan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bulan</label>
                        <select class="form-select" name="bulan">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?php echo $m; ?>" <?php echo $bulan == $m ? 'selected' : ''; ?>>
                                    <?php echo $bulan_indo[$m]; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tahun</label>
                        <select class="form-select" name="tahun">
                            <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?php echo $y; ?>" <?php echo $tahun == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2" id="mingguField" style="display: <?php echo $tipe == 'mingguan' ? 'block' : 'none'; ?>;">
                        <label class="form-label">Minggu Ke</label>
                        <select class="form-select" name="minggu">
                            <option value="1" <?php echo $minggu == 1 ? 'selected' : ''; ?>>Minggu 1</option>
                            <option value="2" <?php echo $minggu == 2 ? 'selected' : ''; ?>>Minggu 2</option>
                            <option value="3" <?php echo $minggu == 3 ? 'selected' : ''; ?>>Minggu 3</option>
                            <option value="4" <?php echo $minggu == 4 ? 'selected' : ''; ?>>Minggu 4</option>
                            <option value="5" <?php echo $minggu == 5 ? 'selected' : ''; ?>>Minggu 5</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="no-print mb-3">
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>
        
        <!-- Print Area -->
        <div class="card-custom">
            <!-- Header Laporan -->
            <div class="print-header">
                <h3>LAPORAN PIKET KELAS</h3>
                <h4><?php echo $_SESSION['nama_kelas']; ?> - <?php echo $_SESSION['nama_jurusan']; ?></h4>
                <p class="mb-0">SMK Negeri 2</p>
                <p class="mb-0"><strong>Periode: <?php echo $periode; ?></strong></p>
            </div>
            
            <!-- Summary -->
            <div class="summary-box">
                <div class="row text-center">
                    <div class="col-3">
                        <h4><?php echo $total_jadwal; ?></h4>
                        <small>Total Jadwal</small>
                    </div>
                    <div class="col-3">
                        <h4><?php echo $total_hadir; ?></h4>
                        <small>Sudah Absen</small>
                    </div>
                    <div class="col-3">
                        <h4><?php echo $total_tepat_waktu; ?></h4>
                        <small>Tepat Waktu</small>
                    </div>
                    <div class="col-3">
                        <h4><?php echo $total_terlambat; ?></h4>
                        <small>Terlambat</small>
                    </div>
                </div>
            </div>
            
            <!-- Table Data -->
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Tanggal</th>
                        <th width="10%">Hari</th>
                        <th width="35%">Anggota Piket</th>
                        <th width="15%">Waktu Absensi</th>
                        <th width="20%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($laporan_data) > 0): ?>
                        <?php foreach ($laporan_data as $index => $item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo formatTanggal($item['tanggal'], 'd-m-Y'); ?></td>
                            <td><?php echo $item['hari']; ?></td>
                            <td><?php echo $item['anggota_nama']; ?></td>
                            <td><?php echo $item['waktu_absensi'] ?: '-'; ?></td>
                            <td>
                                <?php if ($item['waktu_absensi']): ?>
                                    <?php echo $item['status']; ?>
                                <?php else: ?>
                                    Belum Absen
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data untuk periode ini</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Signature -->
            <div class="row mt-5">
                <div class="col-6"></div>
                <div class="col-6 text-center">
                    <p>Surabaya, <?php echo formatTanggal(date('Y-m-d'), 'd F Y'); ?></p>
                    <p>Wali Kelas</p>
                    <br><br><br>
                    <p><strong><u><?php echo getUserName(); ?></u></strong></p>
                    <p>NIP: <?php echo $_SESSION['nip']; ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleMinggu(tipe) {
            const mingguField = document.getElementById('mingguField');
            mingguField.style.display = tipe === 'mingguan' ? 'block' : 'none';
        }
    </script>
     <script src="../../assets/js/darkmode.js"></script>
</body>
</html>