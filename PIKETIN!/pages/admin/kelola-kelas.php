<?php
/**
 * Kelola Kelas
 * File: pages/admin/kelola-kelas.php
 */

require_once '../../config/config.php';
requireLogin();
requireRole('Admin');

$db = getDB();

// Get all kelas with relations
$kelas_list = db_fetch_all("
    SELECT k.*, j.nama_jurusan, j.kode_jurusan, 
           g.nama_lengkap as wali_kelas_nama,
           (SELECT COUNT(*) FROM tb_siswa WHERE id_kelas = k.id_kelas) as total_siswa
    FROM tb_kelas k
    INNER JOIN tb_jurusan j ON k.id_jurusan = j.id_jurusan
    LEFT JOIN tb_guru g ON k.id_wali_kelas = g.id_guru
    ORDER BY k.tingkat, j.kode_jurusan, k.nama_kelas
");

// Get jurusan untuk dropdown
$jurusan_list = db_fetch_all("SELECT * FROM tb_jurusan ORDER BY nama_jurusan");

// Get guru yang belum menjadi wali kelas
$guru_list = db_fetch_all("
    SELECT g.* 
    FROM tb_guru g
    WHERE g.is_active = 1
    AND g.id_guru NOT IN (SELECT id_wali_kelas FROM tb_kelas WHERE id_wali_kelas IS NOT NULL)
    ORDER BY g.nama_lengkap
");

$page_title = "Kelola Kelas";
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
        .badge-tingkat { padding: 6px 12px; border-radius: 20px; font-weight: 600; }
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
            <li><a href="kelola-kelas.php" class="active"><i class="fas fa-door-open"></i> Kelola Kelas</a></li>
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
                <small class="text-muted">Kelola data kelas dan wali kelas</small>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Kelas</h5>
                <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah Kelas
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tingkat</th>
                            <th>Nama Kelas</th>
                            <th>Jurusan</th>
                            <th>Wali Kelas</th>
                            <th>Total Siswa</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($kelas_list) > 0): ?>
                            <?php foreach ($kelas_list as $index => $kelas): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <span class="badge-tingkat 
                                        <?php echo $kelas['tingkat'] == 10 ? 'bg-success' : ($kelas['tingkat'] == 11 ? 'bg-primary' : 'bg-danger'); ?>">
                                        Kelas <?php echo $kelas['tingkat']; ?>
                                    </span>
                                </td>
                                <td><strong><?php echo $kelas['nama_kelas']; ?></strong></td>
                                <td><span class="badge bg-info"><?php echo $kelas['kode_jurusan']; ?></span> <?php echo $kelas['nama_jurusan']; ?></td>
                                <td>
                                    <?php if ($kelas['wali_kelas_nama']): ?>
                                        <i class="fas fa-user-tie text-primary"></i> <?php echo $kelas['wali_kelas_nama']; ?>
                                    <?php else: ?>
                                        <span class="text-muted"><i class="fas fa-minus"></i> Belum ada</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-secondary"><?php echo $kelas['total_siswa']; ?> siswa</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning" onclick='editKelas(<?php echo json_encode($kelas); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="hapusKelas(<?php echo $kelas['id_kelas']; ?>, '<?php echo $kelas['nama_kelas']; ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada data kelas</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Kelas Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTambah">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_kelas" required 
                                       placeholder="Contoh: XI RPL 1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                                <select class="form-select" name="tingkat" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    <option value="10">Kelas 10</option>
                                    <option value="11">Kelas 11</option>
                                    <option value="12">Kelas 12</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <select class="form-select" name="id_jurusan" required>
                                    <option value="">-- Pilih Jurusan --</option>
                                    <?php foreach ($jurusan_list as $jurusan): ?>
                                        <option value="<?php echo $jurusan['id_jurusan']; ?>">
                                            <?php echo $jurusan['kode_jurusan'] . ' - ' . $jurusan['nama_jurusan']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Wali Kelas <small class="text-muted">(Opsional)</small></label>
                                <select class="form-select" name="id_wali_kelas">
                                    <option value="">-- Pilih Wali Kelas --</option>
                                    <?php foreach ($guru_list as $guru): ?>
                                        <option value="<?php echo $guru['id_guru']; ?>">
                                            <?php echo $guru['nama_lengkap'] . ' (NIP: ' . $guru['nip'] . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-gradient"><i class="fas fa-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Kelas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEdit">
                    <input type="hidden" name="id_kelas" id="edit_id_kelas">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_kelas" id="edit_nama_kelas" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                                <select class="form-select" name="tingkat" id="edit_tingkat" required>
                                    <option value="10">Kelas 10</option>
                                    <option value="11">Kelas 11</option>
                                    <option value="12">Kelas 12</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <select class="form-select" name="id_jurusan" id="edit_id_jurusan" required>
                                    <?php foreach ($jurusan_list as $jurusan): ?>
                                        <option value="<?php echo $jurusan['id_jurusan']; ?>">
                                            <?php echo $jurusan['kode_jurusan'] . ' - ' . $jurusan['nama_jurusan']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Wali Kelas</label>
                                <select class="form-select" name="id_wali_kelas" id="edit_id_wali_kelas">
                                    <option value="">-- Tidak Ada Wali Kelas --</option>
                                    <option value="" id="current_wali_option"></option>
                                    <?php foreach ($guru_list as $guru): ?>
                                        <option value="<?php echo $guru['id_guru']; ?>">
                                            <?php echo $guru['nama_lengkap'] . ' (NIP: ' . $guru['nip'] . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>
    <script>
        document.getElementById('formTambah').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'tambah');
            
            fetch('../../controllers/KelasController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 })
                    .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                }
            });
        });
        
        document.getElementById('formEdit').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'edit');
            
            fetch('../../controllers/KelasController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1500 })
                    .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                }
            });
        });
        
        function editKelas(data) {
            document.getElementById('edit_id_kelas').value = data.id_kelas;
            document.getElementById('edit_nama_kelas').value = data.nama_kelas;
            document.getElementById('edit_tingkat').value = data.tingkat;
            document.getElementById('edit_id_jurusan').value = data.id_jurusan;
            document.getElementById('edit_id_wali_kelas').value = data.id_wali_kelas || '';
            
            const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
            modal.show();
        }
        
        function hapusKelas(id, nama) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Yakin ingin menghapus kelas "${nama}"?`,
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
                    formData.append('id_kelas', id);
                    
                    fetch('../../controllers/KelasController.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Terhapus!', text: data.message, showConfirmButton: false, timer: 1500 })
                            .then(() => location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                        }
                    });
                }
            });
        }
    </script>
       <script src="../../assets/js/darkmode.js"></script>
</body>
</html>