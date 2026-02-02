<?php
/**
 * Jadwal Piket
 * File: pages/admin/jadwal-piket.php
 */

require_once '../../config/config.php';
requireLogin();
requireRole('Admin');

$db = getDB();

// Get all kelas untuk dropdown
$kelas_list = db_fetch_all("
    SELECT k.*, j.nama_jurusan, j.kode_jurusan,
           (SELECT COUNT(*) FROM tb_siswa WHERE id_kelas = k.id_kelas AND is_active = 1) as total_siswa
    FROM tb_kelas k
    INNER JOIN tb_jurusan j ON k.id_jurusan = j.id_jurusan
    ORDER BY k.tingkat, k.nama_kelas
");

// Get current month jadwal (default view)
$current_month = date('n');
$current_year = date('Y');

$view_month = isset($_GET['month']) ? (int)$_GET['month'] : $current_month;
$view_year = isset($_GET['year']) ? (int)$_GET['year'] : $current_year;
$view_kelas = isset($_GET['kelas']) ? (int)$_GET['kelas'] : 0;

// Get jadwal for selected month
$jadwal_query = "
    SELECT jp.*, k.nama_kelas, j.kode_jurusan,
           GROUP_CONCAT(s.nama_lengkap SEPARATOR ', ') as anggota_nama,
           COUNT(DISTINCT ap.id_siswa) as jumlah_anggota,
           ab.id_absensi,
           ab.status as status_absensi
    FROM tb_jadwal_piket jp
    INNER JOIN tb_kelas k ON jp.id_kelas = k.id_kelas
    INNER JOIN tb_jurusan j ON k.id_jurusan = j.id_jurusan
    LEFT JOIN tb_anggota_piket ap ON jp.id_jadwal = ap.id_jadwal
    LEFT JOIN tb_siswa s ON ap.id_siswa = s.id_siswa
    LEFT JOIN tb_absensi_piket ab ON jp.id_jadwal = ab.id_jadwal
    WHERE MONTH(jp.tanggal) = $view_month AND YEAR(jp.tanggal) = $view_year
";

if ($view_kelas > 0) {
    $jadwal_query .= " AND jp.id_kelas = $view_kelas";
}

$jadwal_query .= " GROUP BY jp.id_jadwal ORDER BY jp.tanggal, k.nama_kelas";
$jadwal_list = db_fetch_all($jadwal_query);

// Bulan Indonesia
$bulan_indo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

$page_title = "Jadwal Piket";
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.min.css" rel="stylesheet">
    
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
        .btn-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; }
        .btn-gradient:hover { background: linear-gradient(135deg, #5568d3 0%, #65418b 100%); color: white; }
        .filter-section { background: #f8f9fc; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .jadwal-card { border-left: 4px solid #667eea; padding: 15px; background: white; border-radius: 8px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); transition: all 0.3s; }
        .jadwal-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); transform: translateY(-2px); }
        .status-badge { padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .calendar-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
        .calendar-item { background: white; border-radius: 10px; padding: 15px; border: 2px solid #e3e6f0; transition: all 0.3s; }
        .calendar-item:hover { border-color: #667eea; }
        .calendar-item.has-jadwal { border-color: #28a745; background: #f0fff4; }
        .calendar-item.has-absensi { border-color: #17a2b8; background: #e7f7fa; }
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
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="kelola-siswa.php"><i class="fas fa-user-graduate"></i> Kelola Siswa</a></li>
            <li><a href="kelola-guru.php"><i class="fas fa-chalkboard-teacher"></i> Kelola Guru</a></li>
            <li><a href="kelola-kelas.php"><i class="fas fa-door-open"></i> Kelola Kelas</a></li>
            <li><a href="kelola-jurusan.php"><i class="fas fa-book"></i> Kelola Jurusan</a></li>
            <li><a href="jadwal-piket.php" class="active"><i class="fas fa-calendar-alt"></i> Jadwal Piket</a></li>
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
                <h4 class="mb-0"><?php echo $page_title; ?></h4>
                <small class="text-muted">Kelola jadwal piket siswa</small>
            </div>
            <div class="user-info">
                <div class="text-end">
                    <div class="fw-bold"><?php echo getUserName(); ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
                <img src="../../uploads/foto-admin/<?php echo $_SESSION['foto_profil']; ?>" 
                     alt="Avatar" class="user-avatar" onerror="this.src='https://via.placeholder.com/45'">
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="card-custom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0"><i class="fas fa-calendar-plus"></i> Kelola Jadwal</h5>
                    <small class="text-muted">Generate atau edit jadwal piket</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#modalGenerate">
                        <i class="fas fa-magic"></i> Generate Jadwal Otomatis
                    </button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahManual">
                        <i class="fas fa-plus"></i> Tambah Manual
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Bulan</label>
                    <select class="form-select" name="month" onchange="this.form.submit()">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo $view_month == $m ? 'selected' : ''; ?>>
                                <?php echo $bulan_indo[$m]; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <select class="form-select" name="year" onchange="this.form.submit()">
                        <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                            <option value="<?php echo $y; ?>" <?php echo $view_year == $y ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Filter Kelas</label>
                    <select class="form-select" name="kelas" onchange="this.form.submit()">
                        <option value="0">Semua Kelas</option>
                        <?php foreach ($kelas_list as $kelas): ?>
                            <option value="<?php echo $kelas['id_kelas']; ?>" <?php echo $view_kelas == $kelas['id_kelas'] ? 'selected' : ''; ?>>
                                <?php echo $kelas['nama_kelas'] . ' - ' . $kelas['kode_jurusan']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filter</button>
                </div>
            </form>
        </div>
        
        <!-- Jadwal List -->
        <div class="card-custom">
            <h5 class="mb-4">
                <i class="fas fa-list"></i> Jadwal Piket - <?php echo $bulan_indo[$view_month] . ' ' . $view_year; ?>
                <span class="badge bg-info ms-2"><?php echo count($jadwal_list); ?> Jadwal</span>
            </h5>
            
            <?php if (count($jadwal_list) > 0): ?>
                <?php foreach ($jadwal_list as $jadwal): ?>
                <div class="jadwal-card">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="mb-0 text-primary"><?php echo date('d', strtotime($jadwal['tanggal'])); ?></h3>
                                <small class="text-muted"><?php echo formatTanggal($jadwal['tanggal'], 'D, M Y'); ?></small>
                                <div class="mt-1">
                                    <span class="badge bg-secondary"><?php echo $jadwal['hari']; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">
                                <span class="badge bg-primary"><?php echo $jadwal['nama_kelas']; ?></span>
                                <span class="badge bg-info"><?php echo $jadwal['kode_jurusan']; ?></span>
                            </h6>
                            <p class="mb-1">
                                <i class="fas fa-users text-muted"></i> 
                                <strong>Anggota (<?php echo $jadwal['jumlah_anggota']; ?>):</strong>
                                <small><?php echo $jadwal['anggota_nama'] ?: 'Belum ada anggota'; ?></small>
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> Minggu ke-<?php echo $jadwal['minggu_ke']; ?>
                            </small>
                        </div>
                        <div class="col-md-2 text-center">
                            <?php if ($jadwal['id_absensi']): ?>
                                <span class="status-badge bg-success text-white">
                                    <i class="fas fa-check-circle"></i> Sudah Absen
                                </span>
                            <?php else: ?>
                                <span class="status-badge bg-warning text-white">
                                    <i class="fas fa-clock"></i> Belum Absen
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-2 text-end">
                            <button class="btn btn-sm btn-info" onclick='detailJadwal(<?php echo json_encode($jadwal); ?>)'>
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" onclick='editJadwal(<?php echo $jadwal['id_jadwal']; ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="hapusJadwal(<?php echo $jadwal['id_jadwal']; ?>, '<?php echo $jadwal['nama_kelas'] . ' - ' . formatTanggal($jadwal['tanggal']); ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <p class="mb-0">Belum ada jadwal piket untuk bulan ini.</p>
                    <p class="mb-0">Klik tombol <strong>"Generate Jadwal Otomatis"</strong> untuk membuat jadwal.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Modal Generate Jadwal -->
    <div class="modal fade" id="modalGenerate" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="modal-title"><i class="fas fa-magic"></i> Generate Jadwal Piket Otomatis</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formGenerate">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Info:</strong> Sistem akan membuat jadwal piket otomatis untuk seluruh siswa dalam kelas yang dipilih, 
                            dari Senin - Jumat, untuk 5 minggu dalam bulan yang ditentukan.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pilih Kelas <span class="text-danger">*</span></label>
                                <select class="form-select" name="id_kelas" id="gen_id_kelas" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php foreach ($kelas_list as $kelas): ?>
                                        <option value="<?php echo $kelas['id_kelas']; ?>" data-siswa="<?php echo $kelas['total_siswa']; ?>">
                                            <?php echo $kelas['nama_kelas'] . ' - ' . $kelas['kode_jurusan']; ?> 
                                            (<?php echo $kelas['total_siswa']; ?> siswa)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Bulan <span class="text-danger">*</span></label>
                                <select class="form-select" name="bulan" required>
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?php echo $m; ?>" <?php echo $m == date('n') ? 'selected' : ''; ?>>
                                            <?php echo $bulan_indo[$m]; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                <select class="form-select" name="tahun" required>
                                    <?php for ($y = date('Y'); $y <= date('Y') + 1; $y++): ?>
                                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jumlah Siswa per Hari <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="siswa_per_hari" value="5" min="3" max="10" required>
                            <small class="text-muted">Berapa siswa yang piket per hari (disarankan 4-6 siswa)</small>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Perhatian:</strong> Jika sudah ada jadwal untuk kelas dan bulan ini, jadwal lama akan dihapus dan diganti dengan yang baru.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-magic"></i> Generate Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Detail (will be populated by JS) -->
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detail Jadwal Piket</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Will be filled by JavaScript -->
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>
    <script>
        // Generate Jadwal
        document.getElementById('formGenerate').addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Yakin ingin generate jadwal piket otomatis?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Generate!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(this);
                    formData.append('action', 'generate');
                    
                    Swal.fire({
                        title: 'Generating...',
                        text: 'Mohon tunggu, sedang membuat jadwal',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    
                    fetch('../../controllers/JadwalController.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message
                            });
                        }
                    });
                }
            });
        });
        
        // Detail Jadwal
        function detailJadwal(data) {
            const content = `
                <div class="row">
                    <div class="col-md-12">
                        <table class="table">
                            <tr><th>Tanggal</th><td>${data.tanggal}</td></tr>
                            <tr><th>Hari</th><td>${data.hari}</td></tr>
                            <tr><th>Kelas</th><td>${data.nama_kelas} - ${data.kode_jurusan}</td></tr>
                            <tr><th>Minggu Ke</th><td>${data.minggu_ke}</td></tr>
                            <tr><th>Anggota</th><td>${data.anggota_nama || 'Belum ada'}</td></tr>
                            <tr><th>Status</th><td>${data.id_absensi ? '<span class="badge bg-success">Sudah Absen</span>' : '<span class="badge bg-warning">Belum Absen</span>'}</td></tr>
                        </table>
                    </div>
                </div>
            `;
            
            document.getElementById('detailContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('modalDetail')).show();
        }
        
        // Edit Jadwal
        function editJadwal(id) {
            // TODO: Implement edit functionality
            Swal.fire('Info', 'Fitur edit akan segera tersedia', 'info');
        }
        
        // Hapus Jadwal
        function hapusJadwal(id, info) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Yakin ingin menghapus jadwal "${info}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'hapus');
                    formData.append('id_jadwal', id);
                    
                    fetch('../../controllers/JadwalController.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message
                            });
                        }
                    });
                }
            });
        }
    </script>
    <script src="../../assets/js/darkmode.js"></script>
</body>
</html>