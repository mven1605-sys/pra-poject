<?php
/**
 * Kelola Siswa
 * File: pages/admin/kelola-siswa.php
 */

require_once '../../config/config.php';
requireLogin();
requireRole('Admin');

$db = getDB();

// Get filter parameters
$filter_kelas = isset($_GET['kelas']) ? (int) $_GET['kelas'] : 0;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$where = "WHERE s.is_active = 1";
if ($filter_kelas > 0) {
    $where .= " AND s.id_kelas = $filter_kelas";
}
if (!empty($search)) {
    $where .= " AND (s.nama_lengkap LIKE '%" . db_escape($search) . "%' OR s.nis LIKE '%" . db_escape($search) . "%' OR s.nisn LIKE '%" . db_escape($search) . "%')";
}

// Get siswa list
$siswa_list = db_fetch_all("
    SELECT s.*, k.nama_kelas, k.tingkat, j.nama_jurusan, j.kode_jurusan
    FROM tb_siswa s
    INNER JOIN tb_kelas k ON s.id_kelas = k.id_kelas
    INNER JOIN tb_jurusan j ON k.id_jurusan = j.id_jurusan
    $where
    ORDER BY k.tingkat, k.nama_kelas, s.nama_lengkap
");

// Get kelas untuk filter dan form
$kelas_list = db_fetch_all("
    SELECT k.*, j.nama_jurusan, j.kode_jurusan
    FROM tb_kelas k
    INNER JOIN tb_jurusan j ON k.id_jurusan = j.id_jurusan
    ORDER BY k.tingkat, k.nama_kelas
");

$page_title = "Kelola Siswa";
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
        .card-custom { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .btn-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; }
        .btn-gradient:hover { background: linear-gradient(135deg, #5568d3 0%, #65418b 100%); color: white; }
        .foto-preview { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #667eea; }
        .siswa-avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
        .upload-area { border: 2px dashed #667eea; border-radius: 10px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s; }
        .upload-area:hover { background: #f8f9fc; }
        .filter-section { background: #f8f9fc; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
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
            <li><a href="kelola-siswa.php" class="active"><i class="fas fa-user-graduate"></i> Kelola Siswa</a></li>
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
        <div class="top-navbar">
            <div>
                <h4 class="mb-0"><?php echo $page_title; ?></h4>
                <small class="text-muted">Kelola data siswa sekolah</small>
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
        
        <div class="card-custom">
            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Filter Kelas</label>
                        <select class="form-select" name="kelas" onchange="this.form.submit()">
                            <option value="0">Semua Kelas</option>
                            <?php foreach ($kelas_list as $kelas): ?>
                                <option value="<?php echo $kelas['id_kelas']; ?>" <?php echo $filter_kelas == $kelas['id_kelas'] ? 'selected' : ''; ?>>
                                    <?php echo $kelas['nama_kelas'] . ' - ' . $kelas['kode_jurusan']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pencarian</label>
                        <input type="text" class="form-control" name="search" placeholder="Cari nama, NIS, atau NISN..." value="<?php echo $search; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Cari</button>
                    </div>
                </form>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Siswa</h5>
                    <small class="text-muted">Total: <?php echo count($siswa_list); ?> siswa</small>
                </div>
                <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah Siswa
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>NIS/NISN</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Username</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($siswa_list) > 0): ?>
                            <?php foreach ($siswa_list as $index => $siswa): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <img src="../../uploads/foto-siswa/<?php echo $siswa['foto_profil']; ?>" 
                                         alt="Foto" class="siswa-avatar" onerror="this.src='https://via.placeholder.com/50'">
                                </td>
                                <td>
                                    <small class="d-block text-muted">NIS: <?php echo $siswa['nis']; ?></small>
                                    <small class="d-block text-muted">NISN: <?php echo $siswa['nisn']; ?></small>
                                </td>
                                <td><strong><?php echo $siswa['nama_lengkap']; ?></strong></td>
                                <td>
                                    <span class="badge bg-<?php echo $siswa['tingkat'] == 10 ? 'success' : ($siswa['tingkat'] == 11 ? 'primary' : 'danger'); ?>">
                                        <?php echo $siswa['nama_kelas']; ?>
                                    </span>
                                </td>
                                <td><span class="badge bg-info"><?php echo $siswa['kode_jurusan']; ?></span></td>
                                <td><?php echo $siswa['username']; ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info" onclick='detailSiswa(<?php echo json_encode($siswa); ?>)'>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick='editSiswa(<?php echo json_encode($siswa); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="hapusSiswa(<?php echo $siswa['id_siswa']; ?>, '<?php echo $siswa['nama_lengkap']; ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada data siswa</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal Tambah (will continue in next artifact) -->
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>
     <script src="../../assets/js/darkmode.js"></script>
</body>
</html>