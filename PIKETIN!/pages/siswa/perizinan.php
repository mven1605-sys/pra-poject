<?php
/**
 * Perizinan - Siswa
 * File: pages/siswa/perizinan.php
 * Form izin untuk siswa yang tidak bisa piket
 */

require_once '../../config/config.php';
requireLogin();
requireRole('Siswa');

$db = getDB();
$id_siswa = getUserId();

// Get jadwal yang akan datang (belum absen)
$jadwal_upcoming = db_fetch_all("
    SELECT jp.*, ab.id_absensi
    FROM tb_jadwal_piket jp
    INNER JOIN tb_anggota_piket ap ON jp.id_jadwal = ap.id_jadwal
    LEFT JOIN tb_absensi_piket ab ON jp.id_jadwal = ab.id_jadwal
    WHERE ap.id_siswa = $id_siswa
    AND jp.tanggal >= CURDATE()
    AND ab.id_absensi IS NULL
    ORDER BY jp.tanggal
    LIMIT 10
");

// Get riwayat perizinan
$riwayat_izin = db_fetch_all("
    SELECT pz.*, jp.tanggal, jp.hari
    FROM tb_perizinan pz
    INNER JOIN tb_jadwal_piket jp ON pz.id_jadwal = jp.id_jadwal
    WHERE pz.id_siswa = $id_siswa
    ORDER BY pz.created_at DESC
    LIMIT 10
");

$page_title = "Perizinan";
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
            <li><a href="riwayat-absensi.php"><i class="fas fa-history"></i> Riwayat Absensi</a></li>
            <li><a href="profil.php"><i class="fas fa-user-cog"></i> Profil</a></li>
            <li><a href="../../controllers/AuthController.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <div>
                <h4 class="mb-0"><?php echo $page_title; ?></h4>
                <small class="text-muted">Form izin tidak dapat mengikuti piket</small>
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
        
        <!-- Form Izin -->
        <div class="card-custom">
            <h5 class="mb-4"><i class="fas fa-file-alt"></i> Form Perizinan</h5>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Informasi:</strong> Ajukan izin jika Anda tidak dapat mengikuti piket karena sakit atau keperluan mendadak. Perizinan akan direview oleh wali kelas.
            </div>
            
            <form id="formIzin" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Pilih Jadwal <span class="text-danger">*</span></label>
                    <select class="form-select" name="id_jadwal" required>
                        <option value="">-- Pilih Jadwal Yang Akan Diizinkan --</option>
                        <?php foreach ($jadwal_upcoming as $jadwal): ?>
                            <option value="<?php echo $jadwal['id_jadwal']; ?>">
                                <?php echo formatTanggal($jadwal['tanggal'], 'D, d F Y') . ' (' . $jadwal['hari'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Jenis Izin <span class="text-danger">*</span></label>
                    <select class="form-select" name="jenis_izin" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Izin">Izin (Keperluan Keluarga/Lainnya)</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Alasan <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="alasan" rows="4" required 
                              placeholder="Jelaskan alasan Anda tidak dapat mengikuti piket..."></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Bukti (Opsional)</label>
                    <input type="file" class="form-control" name="bukti_izin" accept="image/*,application/pdf">
                    <small class="text-muted">Upload surat keterangan dokter / surat izin (jika ada)</small>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Ajukan Izin
                </button>
            </form>
        </div>
        
        <!-- Riwayat Izin -->
        <div class="card-custom">
            <h5 class="mb-4"><i class="fas fa-history"></i> Riwayat Perizinan</h5>
            
            <?php if (count($riwayat_izin) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th>Diajukan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayat_izin as $izin): ?>
                            <tr>
                                <td><?php echo formatTanggal($izin['tanggal'], 'd-m-Y'); ?></td>
                                <td><span class="badge bg-info"><?php echo $izin['jenis_izin']; ?></span></td>
                                <td><?php echo substr($izin['alasan'], 0, 50) . '...'; ?></td>
                                <td>
                                    <?php if ($izin['status_approval'] == 'Approved'): ?>
                                        <span class="badge bg-success">Disetujui</span>
                                    <?php elseif ($izin['status_approval'] == 'Rejected'): ?>
                                        <span class="badge bg-danger">Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo formatTanggal($izin['created_at'], 'd-m-Y'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> Belum ada riwayat perizinan
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>
    <script>
        document.getElementById('formIzin').addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Yakin ingin mengajukan perizinan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ajukan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(this);
                    formData.append('action', 'ajukan_izin');
                    
                    Swal.fire({
                        title: 'Mengirim...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    
                    fetch('../../controllers/PerizinanController.php', {
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
    </script>
     <script src="../../assets/js/darkmode.js"></script>
</body>
</html>