<?php
/**
 * Absensi Piket - Siswa
 * File: pages/siswa/absensi-piket.php
 * Upload 4 foto untuk absensi piket
 */

require_once '../../config/config.php';
requireLogin();
requireRole('Siswa');

$db = getDB();
$id_siswa = getUserId();
$today = getCurrentDate();

// Get jadwal piket hari ini untuk siswa ini
$jadwal_hari_ini = db_fetch_one("
    SELECT jp.*, k.nama_kelas, j.nama_jurusan,
           ab.id_absensi, ab.status as status_absensi,
           GROUP_CONCAT(s.nama_lengkap SEPARATOR ', ') as anggota_nama
    FROM tb_jadwal_piket jp
    INNER JOIN tb_kelas k ON jp.id_kelas = k.id_kelas
    INNER JOIN tb_jurusan j ON k.id_jurusan = j.id_jurusan
    INNER JOIN tb_anggota_piket ap ON jp.id_jadwal = ap.id_jadwal
    LEFT JOIN tb_siswa s ON ap.id_siswa = s.id_siswa
    LEFT JOIN tb_absensi_piket ab ON jp.id_jadwal = ab.id_jadwal
    WHERE jp.tanggal = '$today'
    AND ap.id_siswa = $id_siswa
    GROUP BY jp.id_jadwal
");

$page_title = "Absensi Piket";
$waktu_sekarang = getCurrentTime();
$batas_waktu = PIKET_END_TIME; // 07:00:00
$is_terlambat = strtotime($waktu_sekarang) > strtotime($batas_waktu);
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
        
        .upload-box { 
            border: 3px dashed #667eea; 
            border-radius: 15px; 
            padding: 30px; 
            text-align: center; 
            cursor: pointer; 
            transition: all 0.3s;
            background: #f8f9fc;
            position: relative;
        }
        .upload-box:hover { background: #e7ebf7; border-color: #5568d3; }
        .upload-box.has-image { border-color: #28a745; background: #f0fff4; }
        .upload-preview { 
            max-width: 100%; 
            max-height: 250px; 
            border-radius: 10px; 
            margin-top: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .remove-image { 
            position: absolute; 
            top: 10px; 
            right: 10px; 
            background: #dc3545; 
            color: white; 
            border: none; 
            border-radius: 50%; 
            width: 30px; 
            height: 30px; 
            cursor: pointer;
            z-index: 10;
        }
        .info-box { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 20px; 
            border-radius: 15px; 
            margin-bottom: 25px;
        }
        .time-box {
            background: white;
            color: #333;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }
        .btn-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; padding: 12px 30px; font-size: 16px; font-weight: 600; }
        .btn-gradient:hover { background: linear-gradient(135deg, #5568d3 0%, #65418b 100%); color: white; transform: translateY(-2px); }
        .photo-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        @media (max-width: 768px) { .photo-grid { grid-template-columns: 1fr; } }
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
            <li><a href="absensi-piket.php" class="active"><i class="fas fa-camera"></i> Absensi Piket</a></li>
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
                <small class="text-muted">Upload foto absensi piket hari ini</small>
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
        
        <?php if ($jadwal_hari_ini): ?>
            <?php if ($jadwal_hari_ini['id_absensi']): ?>
                <!-- Sudah Absen -->
                <div class="alert alert-success text-center">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h5>Absensi Piket Sudah Selesai!</h5>
                    <p>Terima kasih sudah melakukan absensi piket hari ini.</p>
                    <a href="riwayat-absensi.php" class="btn btn-success mt-2">
                        <i class="fas fa-history"></i> Lihat Riwayat
                    </a>
                </div>
            <?php else: ?>
                <!-- Form Absensi -->
                <div class="info-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-calendar-day"></i> Jadwal Piket Hari Ini</h4>
                            <p class="mb-0"><?php echo formatTanggal($today, 'lengkap'); ?></p>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0"><?php echo $jadwal_hari_ini['nama_kelas']; ?></h5>
                            <span class="badge bg-light text-dark"><?php echo $jadwal_hari_ini['nama_jurusan']; ?></span>
                        </div>
                    </div>
                    
                    <div class="time-box">
                        <div class="row text-center">
                            <div class="col-md-6">
                                <i class="fas fa-clock fa-2x text-primary mb-2"></i>
                                <h5>Waktu Sekarang</h5>
                                <h3 id="currentTime"><?php echo date('H:i:s'); ?></h3>
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-hourglass-end fa-2x text-danger mb-2"></i>
                                <h5>Batas Waktu</h5>
                                <h3><?php echo $batas_waktu; ?></h3>
                                <?php if ($is_terlambat): ?>
                                    <span class="badge bg-danger">TERLAMBAT</span>
                                <?php else: ?>
                                    <span class="badge bg-success">TEPAT WAKTU</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-custom">
                    <h5 class="mb-3"><i class="fas fa-users"></i> Anggota Piket Hari Ini</h5>
                    <p><?php echo $jadwal_hari_ini['anggota_nama']; ?></p>
                </div>
                
                <div class="card-custom">
                    <h5 class="mb-4"><i class="fas fa-camera"></i> Upload Foto Absensi</h5>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Petunjuk:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Upload <strong>4 foto</strong>: 1 foto anggota + 3 foto area kelas</li>
                            <li>Pastikan foto jelas dan terang</li>
                            <li>Aktifkan GPS/lokasi pada kamera untuk timestamp otomatis</li>
                            <li>Ukuran maksimal per foto: 5MB</li>
                        </ul>
                    </div>
                    
                    <form id="formAbsensi" enctype="multipart/form-data">
                        <input type="hidden" name="id_jadwal" value="<?php echo $jadwal_hari_ini['id_jadwal']; ?>">
                        
                        <div class="photo-grid mb-4">
                            <!-- Foto Anggota -->
                            <div>
                                <h6 class="mb-3"><i class="fas fa-users"></i> 1. Foto Anggota Piket <span class="text-danger">*</span></h6>
                                <div class="upload-box" id="box1" onclick="document.getElementById('foto_anggota').click()">
                                    <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                    <p class="mb-0">Klik untuk upload foto anggota</p>
                                    <small class="text-muted">Foto bersama seluruh anggota piket</small>
                                    <img id="preview1" class="upload-preview" style="display: none;">
                                </div>
                                <input type="file" id="foto_anggota" name="foto_anggota" accept="image/*" class="d-none" required onchange="previewImage(this, 'preview1', 'box1')">
                            </div>
                            
                            <!-- Foto Area 1 -->
                            <div>
                                <h6 class="mb-3"><i class="fas fa-broom"></i> 2. Foto Area Kelas 1 <span class="text-danger">*</span></h6>
                                <div class="upload-box" id="box2" onclick="document.getElementById('foto_area1').click()">
                                    <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                    <p class="mb-0">Klik untuk upload</p>
                                    <small class="text-muted">Area depan kelas / papan tulis</small>
                                    <img id="preview2" class="upload-preview" style="display: none;">
                                </div>
                                <input type="file" id="foto_area1" name="foto_area1" accept="image/*" class="d-none" required onchange="previewImage(this, 'preview2', 'box2')">
                            </div>
                            
                            <!-- Foto Area 2 -->
                            <div>
                                <h6 class="mb-3"><i class="fas fa-broom"></i> 3. Foto Area Kelas 2 <span class="text-danger">*</span></h6>
                                <div class="upload-box" id="box3" onclick="document.getElementById('foto_area2').click()">
                                    <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                    <p class="mb-0">Klik untuk upload</p>
                                    <small class="text-muted">Area tengah kelas / meja siswa</small>
                                    <img id="preview3" class="upload-preview" style="display: none;">
                                </div>
                                <input type="file" id="foto_area2" name="foto_area2" accept="image/*" class="d-none" required onchange="previewImage(this, 'preview3', 'box3')">
                            </div>
                            
                            <!-- Foto Area 3 -->
                            <div>
                                <h6 class="mb-3"><i class="fas fa-broom"></i> 4. Foto Area Kelas 3 <span class="text-danger">*</span></h6>
                                <div class="upload-box" id="box4" onclick="document.getElementById('foto_area3').click()">
                                    <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                    <p class="mb-0">Klik untuk upload</p>
                                    <small class="text-muted">Area belakang kelas / pojok</small>
                                    <img id="preview4" class="upload-preview" style="display: none;">
                                </div>
                                <input type="file" id="foto_area3" name="foto_area3" accept="image/*" class="d-none" required onchange="previewImage(this, 'preview4', 'box4')">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control" name="keterangan" rows="3" placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirmCheck" required>
                                <label class="form-check-label" for="confirmCheck">
                                    Saya menyatakan bahwa foto yang diupload adalah hasil piket hari ini dan sesuai dengan kondisi sebenarnya.
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-gradient btn-lg">
                                <i class="fas fa-paper-plane"></i> Kirim Absensi Piket
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Tidak Ada Jadwal -->
            <div class="alert alert-warning text-center">
                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                <h5>Tidak Ada Jadwal Piket Hari Ini</h5>
                <p>Anda tidak terjadwal piket pada hari ini (<?php echo formatTanggal($today, 'lengkap'); ?>)</p>
                <a href="jadwal-piket.php" class="btn btn-warning mt-2">
                    <i class="fas fa-calendar-alt"></i> Lihat Jadwal
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>
    <script>
        // Update waktu real-time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID');
            const timeElement = document.getElementById('currentTime');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }
        setInterval(updateTime, 1000);
        
        // Preview Image
        function previewImage(input, previewId, boxId) {
            const preview = document.getElementById(previewId);
            const box = document.getElementById(boxId);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    box.classList.add('has-image');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Submit Form
        document.getElementById('formAbsensi').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Check all photos uploaded
            const foto_anggota = document.getElementById('foto_anggota').files[0];
            const foto_area1 = document.getElementById('foto_area1').files[0];
            const foto_area2 = document.getElementById('foto_area2').files[0];
            const foto_area3 = document.getElementById('foto_area3').files[0];
            
            if (!foto_anggota || !foto_area1 || !foto_area2 || !foto_area3) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Foto Belum Lengkap!',
                    text: 'Harap upload semua 4 foto yang diperlukan'
                });
                return;
            }
            
            Swal.fire({
                title: 'Konfirmasi Absensi',
                text: 'Yakin ingin mengirim absensi piket?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(this);
                    formData.append('action', 'absensi');
                    
                    Swal.fire({
                        title: 'Mengirim Absensi...',
                        text: 'Mohon tunggu, sedang upload foto',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    
                    fetch('../../controllers/AbsensiController.php', {
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
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan sistem'
                        });
                    });
                }
            });
        });
    </script>
       <script src="../../assets/js/darkmode.js"></script>
</body>
</html>