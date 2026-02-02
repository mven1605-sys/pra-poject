<?php
/**
 * Kelola Jurusan
 * File: pages/admin/kelola-jurusan.php
 */

require_once '../../config/config.php';
requireLogin();
requireRole('Admin');

$db = getDB();

// Get all jurusan
$jurusan_list = db_fetch_all("
    SELECT j.*, 
           (SELECT COUNT(*) FROM tb_kelas WHERE id_jurusan = j.id_jurusan) as total_kelas
    FROM tb_jurusan j
    ORDER BY j.nama_jurusan ASC
");

$page_title = "Kelola Jurusan";
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
        
        .card-custom {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        
        .btn-gradient:hover {
            background: linear-gradient(135deg, #5568d3 0%, #65418b 100%);
            color: white;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8f9fc;
        }
        
        .badge-custom {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
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
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="kelola-siswa.php"><i class="fas fa-user-graduate"></i> Kelola Siswa</a></li>
            <li><a href="kelola-guru.php"><i class="fas fa-chalkboard-teacher"></i> Kelola Guru</a></li>
            <li><a href="kelola-kelas.php"><i class="fas fa-door-open"></i> Kelola Kelas</a></li>
            <li><a href="kelola-jurusan.php" class="active"><i class="fas fa-book"></i> Kelola Jurusan</a></li>
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
                <small class="text-muted">Kelola data jurusan sekolah</small>
            </div>
            <div class="user-info">
                <div class="text-end">
                    <div class="fw-bold"><?php echo getUserName(); ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
                <img src="../../uploads/foto-admin/<?php echo $_SESSION['foto_profil']; ?>" 
                     alt="Avatar" class="user-avatar" 
                     onerror="this.src='https://via.placeholder.com/45'">
            </div>
        </div>
        
        <!-- Alert Area -->
        <div id="alertArea"></div>
        
        <!-- Main Card -->
        <div class="card-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Jurusan</h5>
                <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah Jurusan
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Kode</th>
                            <th width="45%">Nama Jurusan</th>
                            <th width="15%">Total Kelas</th>
                            <th width="20%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($jurusan_list) > 0): ?>
                            <?php foreach ($jurusan_list as $index => $jurusan): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><span class="badge bg-primary"><?php echo $jurusan['kode_jurusan']; ?></span></td>
                                <td><?php echo $jurusan['nama_jurusan']; ?></td>
                                <td><span class="badge-custom bg-info text-white"><?php echo $jurusan['total_kelas']; ?> Kelas</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning" onclick="editJurusan(<?php echo htmlspecialchars(json_encode($jurusan)); ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="hapusJurusan(<?php echo $jurusan['id_jurusan']; ?>, '<?php echo $jurusan['nama_jurusan']; ?>')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada data jurusan</p>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Jurusan Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTambah">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kode Jurusan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kode_jurusan" required 
                                   placeholder="Contoh: RPL, TKJ, MM" maxlength="10">
                            <small class="text-muted">Gunakan singkatan (max 10 karakter)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_jurusan" required 
                                   placeholder="Contoh: Rekayasa Perangkat Lunak" maxlength="100">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Jurusan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEdit">
                    <input type="hidden" name="id_jurusan" id="edit_id_jurusan">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kode Jurusan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kode_jurusan" id="edit_kode_jurusan" required maxlength="10">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_jurusan" id="edit_nama_jurusan" required maxlength="100">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>
    <script>
        // Handle Form Tambah
        document.getElementById('formTambah').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'tambah');
            
            fetch('../../controllers/JurusanController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem'
                });
            });
        });
        
        // Handle Form Edit
        document.getElementById('formEdit').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'edit');
            
            fetch('../../controllers/JurusanController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                }
            });
        });
        
        // Function Edit
        function editJurusan(data) {
            document.getElementById('edit_id_jurusan').value = data.id_jurusan;
            document.getElementById('edit_kode_jurusan').value = data.kode_jurusan;
            document.getElementById('edit_nama_jurusan').value = data.nama_jurusan;
            
            const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
            modal.show();
        }
        
        // Function Hapus
        function hapusJurusan(id, nama) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Yakin ingin menghapus jurusan "${nama}"?`,
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
                    formData.append('id_jurusan', id);
                    
                    fetch('../../controllers/JurusanController.php', {
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
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
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