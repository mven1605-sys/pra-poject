<?php
/**
 * Kelola Guru
 * File: pages/admin/kelola-guru.php
 */

require_once '../../config/config.php';
requireLogin();
requireRole('Admin');

$db = getDB();

// Get all guru with wali kelas info
$guru_list = db_fetch_all("
    SELECT g.*, k.nama_kelas 
    FROM tb_guru g
    LEFT JOIN tb_kelas k ON k.id_wali_kelas = g.id_guru
    ORDER BY g.nama_lengkap ASC
");

$page_title = "Kelola Guru";
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
        .guru-avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
        .upload-area { border: 2px dashed #667eea; border-radius: 10px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s; }
        .upload-area:hover { background: #f8f9fc; }
        .upload-area.dragover { background: #e7ebf7; border-color: #5568d3; }
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
            <li><a href="kelola-guru.php" class="active"><i class="fas fa-chalkboard-teacher"></i> Kelola Guru</a></li>
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
                <small class="text-muted">Kelola data guru dan wali kelas</small>
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
                <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Guru</h5>
                <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah Guru
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>NIP</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Wali Kelas</th>
                            <th>Kontak</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($guru_list) > 0): ?>
                            <?php foreach ($guru_list as $index => $guru): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <img src="../../uploads/foto-guru/<?php echo $guru['foto_profil']; ?>" 
                                         alt="Foto" class="guru-avatar" 
                                         onerror="this.src='https://via.placeholder.com/50'">
                                </td>
                                <td><span class="badge bg-primary"><?php echo $guru['nip']; ?></span></td>
                                <td><strong><?php echo $guru['nama_lengkap']; ?></strong></td>
                                <td><?php echo $guru['username']; ?></td>
                                <td>
                                    <?php if ($guru['nama_kelas']): ?>
                                        <span class="badge bg-success"><?php echo $guru['nama_kelas']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($guru['no_telepon']): ?>
                                        <i class="fas fa-phone text-success"></i> <?php echo $guru['no_telepon']; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($guru['is_active']): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Non-Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info" onclick='detailGuru(<?php echo json_encode($guru); ?>)'>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick='editGuru(<?php echo json_encode($guru); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="hapusGuru(<?php echo $guru['id_guru']; ?>, '<?php echo $guru['nama_lengkap']; ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada data guru</p>
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Guru Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTambah" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3 text-center mb-3">
                                <label class="form-label fw-bold">Foto Profil</label>
                                <div class="upload-area" id="uploadArea" onclick="document.getElementById('foto_profil').click()">
                                    <img src="https://via.placeholder.com/100" alt="Preview" class="foto-preview mb-2" id="previewTambah">
                                    <p class="mb-0 text-muted small">Klik untuk upload foto</p>
                                    <small class="text-muted">Max 5MB (JPG, PNG)</small>
                                </div>
                                <input type="file" class="d-none" id="foto_profil" name="foto_profil" accept="image/*" onchange="previewImage(this, 'previewTambah')">
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIP <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nip" required placeholder="Masukkan NIP">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nik" required placeholder="Masukkan NIK" maxlength="16">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama_lengkap" required placeholder="Masukkan nama lengkap">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="username" required placeholder="Username untuk login">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="password" required placeholder="Password (default: NIP)">
                                        <small class="text-muted">Kosongkan untuk menggunakan NIP sebagai password</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        <h6 class="mb-3"><i class="fas fa-info-circle"></i> Informasi Kontak</h6>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="alamat" rows="2" placeholder="Alamat lengkap"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" name="no_telepon" placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="email@example.com">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Koordinat Rumah</label>
                                <input type="text" class="form-control" name="koordinat_rumah" placeholder="-7.250445, 112.768845">
                                <small class="text-muted">Format: latitude, longitude</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Link Google Maps</label>
                                <input type="url" class="form-control" name="gmaps_link" placeholder="https://maps.google.com/...">
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Data Guru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEdit" enctype="multipart/form-data">
                    <input type="hidden" name="id_guru" id="edit_id_guru">
                    <input type="hidden" name="foto_lama" id="edit_foto_lama">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3 text-center mb-3">
                                <label class="form-label fw-bold">Foto Profil</label>
                                <div class="upload-area" onclick="document.getElementById('edit_foto_profil').click()">
                                    <img src="https://via.placeholder.com/100" alt="Preview" class="foto-preview mb-2" id="previewEdit">
                                    <p class="mb-0 text-muted small">Klik untuk ganti foto</p>
                                </div>
                                <input type="file" class="d-none" id="edit_foto_profil" name="foto_profil" accept="image/*" onchange="previewImage(this, 'previewEdit')">
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIP <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nip" id="edit_nip" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nik" id="edit_nik" required maxlength="16">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama_lengkap" id="edit_nama_lengkap" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="username" id="edit_username" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Password Baru</label>
                                        <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="is_active" id="edit_is_active">
                                        <option value="1">Aktif</option>
                                        <option value="0">Non-Aktif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        <h6 class="mb-3"><i class="fas fa-info-circle"></i> Informasi Kontak</h6>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="alamat" id="edit_alamat" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" name="no_telepon" id="edit_no_telepon">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="edit_email">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Koordinat Rumah</label>
                                <input type="text" class="form-control" name="koordinat_rumah" id="edit_koordinat_rumah">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Link Google Maps</label>
                                <input type="url" class="form-control" name="gmaps_link" id="edit_gmaps_link">
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
    
    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-user"></i> Detail Guru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <img src="" alt="Foto" class="foto-preview mb-3" id="detail_foto">
                            <h6 id="detail_status"></h6>
                        </div>
                        <div class="col-md-9">
                            <table class="table table-borderless">
                                <tr><th width="200">Nama Lengkap</th><td id="detail_nama">:</td></tr>
                                <tr><th>NIP</th><td id="detail_nip">:</td></tr>
                                <tr><th>NIK</th><td id="detail_nik">:</td></tr>
                                <tr><th>Username</th><td id="detail_username">:</td></tr>
                                <tr><th>Wali Kelas</th><td id="detail_wali">:</td></tr>
                                <tr><th>Alamat</th><td id="detail_alamat">:</td></tr>
                                <tr><th>No. Telepon</th><td id="detail_telepon">:</td></tr>
                                <tr><th>Email</th><td id="detail_email">:</td></tr>
                                <tr><th>Koordinat</th><td id="detail_koordinat">:</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>
    <script>
        // Preview Image
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Drag & Drop
        const uploadArea = document.getElementById('uploadArea');
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => uploadArea.classList.add('dragover'));
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => uploadArea.classList.remove('dragover'));
        });
        
        uploadArea.addEventListener('drop', function(e) {
            const file = e.dataTransfer.files[0];
            document.getElementById('foto_profil').files = e.dataTransfer.files;
            previewImage(document.getElementById('foto_profil'), 'previewTambah');
        });
        
        // Form Tambah
        document.getElementById('formTambah').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'tambah');
            
            Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            
            fetch('../../controllers/GuruController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                }
            });
        });
        
        // Form Edit
        document.getElementById('formEdit').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'edit');
            
            Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            
            fetch('../../controllers/GuruController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                }
            });
        });
        
        // Edit Guru
        function editGuru(data) {
            document.getElementById('edit_id_guru').value = data.id_guru;
            document.getElementById('edit_foto_lama').value = data.foto_profil;
            document.getElementById('edit_nip').value = data.nip;
            document.getElementById('edit_nik').value = data.nik;
            document.getElementById('edit_nama_lengkap').value = data.nama_lengkap;
            document.getElementById('edit_username').value = data.username;
            document.getElementById('edit_is_active').value = data.is_active;
            document.getElementById('edit_alamat').value = data.alamat || '';
            document.getElementById('edit_no_telepon').value = data.no_telepon || '';
            document.getElementById('edit_email').value = data.email || '';
            document.getElementById('edit_koordinat_rumah').value = data.koordinat_rumah || '';
            document.getElementById('edit_gmaps_link').value = data.gmaps_link || '';
            document.getElementById('previewEdit').src = '../../uploads/foto-guru/' + data.foto_profil;
            
            new bootstrap.Modal(document.getElementById('modalEdit')).show();
        }
        
        // Detail Guru
        function detailGuru(data) {
            document.getElementById('detail_foto').src = '../../uploads/foto-guru/' + data.foto_profil;
            document.getElementById('detail_nama').textContent = ': ' + data.nama_lengkap;
            document.getElementById('detail_nip').textContent = ': ' + data.nip;
            document.getElementById('detail_nik').textContent = ': ' + data.nik;
            document.getElementById('detail_username').textContent = ': ' + data.username;
            document.getElementById('detail_wali').textContent = ': ' + (data.nama_kelas || 'Bukan wali kelas');
            document.getElementById('detail_alamat').textContent = ': ' + (data.alamat || '-');
            document.getElementById('detail_telepon').textContent = ': ' + (data.no_telepon || '-');
            document.getElementById('detail_email').textContent = ': ' + (data.email || '-');
            document.getElementById('detail_koordinat').textContent = ': ' + (data.koordinat_rumah || '-');
            document.getElementById('detail_status').innerHTML = data.is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Non-Aktif</span>';
            
            new bootstrap.Modal(document.getElementById('modalDetail')).show();
        }
        
        // Hapus Guru
        function hapusGuru(id, nama) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Yakin ingin menghapus guru "${nama}"?`,
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
                    formData.append('id_guru', id);
                    
                    fetch('../../controllers/GuruController.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Terhapus!', text: data.message, timer: 1500, showConfirmButton: false })
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