<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bantuan - PIKETIN</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-help {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 0;
            color: white;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .container-help {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .help-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .help-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            margin-right: 20px;
        }
        
        .step-number {
            width: 35px;
            height: 35px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 10px;
        }
        
        .faq-item {
            background: #f8f9fc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .faq-item:hover {
            background: #e7ebf7;
        }
        
        .faq-answer {
            display: none;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            margin-top: 15px;
        }
        
        .video-tutorial {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 10px;
        }
        
        .video-tutorial iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar-help">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0"><i class="fas fa-question-circle"></i> Pusat Bantuan PIKETIN</h2>
                    <p class="mb-0 opacity-75">Panduan lengkap penggunaan sistem</p>
                </div>
                <a href="index.php" class="btn btn-light">
                    <i class="fas fa-home"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    
    <div class="container-help">
        <!-- Quick Links -->
        <div class="help-card">
            <h4 class="mb-4"><i class="fas fa-link"></i> Link Cepat</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="login.php" class="btn btn-outline-primary w-100">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="forgot-account.php" class="btn btn-outline-warning w-100">
                        <i class="fas fa-question"></i> Lupa Akun
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="#faq" class="btn btn-outline-info w-100">
                        <i class="fas fa-comments"></i> FAQ
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Panduan Siswa -->
        <div class="help-card">
            <div class="d-flex align-items-center mb-4">
                <div class="help-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <h3 class="mb-0">Panduan untuk Siswa</h3>
                    <p class="mb-0 text-muted">Cara melakukan absensi piket</p>
                </div>
            </div>
            
            <h5 class="mt-4 mb-3">Langkah-langkah Absensi Piket:</h5>
            
            <div class="mb-3">
                <span class="step-number">1</span>
                <strong>Login ke Sistem</strong>
                <p class="ms-5 mt-2">Buka halaman login, pilih role "Siswa", masukkan username (nama lengkap) dan password (NIS/NISN), lalu pilih kelas Anda.</p>
            </div>
            
            <div class="mb-3">
                <span class="step-number">2</span>
                <strong>Cek Jadwal Piket</strong>
                <p class="ms-5 mt-2">Setelah login, di dashboard akan muncul notifikasi jika Anda terjadwal piket hari ini. Atau bisa cek di menu "Jadwal Piket".</p>
            </div>
            
            <div class="mb-3">
                <span class="step-number">3</span>
                <strong>Lakukan Piket</strong>
                <p class="ms-5 mt-2">Bersihkan kelas bersama anggota piket lainnya sebelum pukul 07:00.</p>
            </div>
            
            <div class="mb-3">
                <span class="step-number">4</span>
                <strong>Upload Foto Absensi</strong>
                <p class="ms-5 mt-2">Masuk ke menu "Absensi Piket", upload 4 foto:
                    <ul class="mt-2">
                        <li>1 foto anggota piket (foto bersama)</li>
                        <li>3 foto area kelas yang sudah dibersihkan</li>
                    </ul>
                    Pastikan foto jelas dan aktifkan GPS pada kamera.
                </p>
            </div>
            
            <div class="mb-3">
                <span class="step-number">5</span>
                <strong>Submit Absensi</strong>
                <p class="ms-5 mt-2">Centang konfirmasi dan klik "Kirim Absensi". Selesai!</p>
            </div>
            
            <div class="alert alert-info mt-4">
                <i class="fas fa-lightbulb"></i>
                <strong>Tips:</strong>
                <ul class="mb-0 mt-2">
                    <li>Upload absensi sebelum pukul 07:00 agar tidak terlambat</li>
                    <li>Pastikan semua anggota ada di foto</li>
                    <li>Foto harus jelas, terang, dan menunjukkan kelas yang bersih</li>
                    <li>Jika berhalangan, ajukan izin melalui menu perizinan</li>
                </ul>
            </div>
        </div>
        
        <!-- Panduan Guru -->
        <div class="help-card">
            <div class="d-flex align-items-center mb-4">
                <div class="help-icon" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <h3 class="mb-0">Panduan untuk Guru</h3>
                    <p class="mb-0 text-muted">Monitoring dan laporan piket</p>
                </div>
            </div>
            
            <h5 class="mt-4 mb-3">Fungsi Utama:</h5>
            
            <div class="mb-3">
                <span class="step-number">1</span>
                <strong>Monitoring Kelas</strong>
                <p class="ms-5 mt-2">Lihat jadwal piket kelas yang Anda ampu di dashboard. Cek apakah siswa sudah melakukan absensi atau belum.</p>
            </div>
            
            <div class="mb-3">
                <span class="step-number">2</span>
                <strong>Lihat Riwayat Absensi</strong>
                <p class="ms-5 mt-2">Menu "Riwayat Absensi" menampilkan semua absensi piket kelas Anda lengkap dengan foto dokumentasi.</p>
            </div>
            
            <div class="mb-3">
                <span class="step-number">3</span>
                <strong>Generate Laporan</strong>
                <p class="ms-5 mt-2">Di menu "Laporan Piket", pilih tipe (mingguan/bulanan), lalu klik "Tampilkan". Anda bisa langsung cetak dengan klik tombol "Cetak Laporan".</p>
            </div>
        </div>
        
        <!-- Panduan Admin -->
        <div class="help-card">
            <div class="d-flex align-items-center mb-4">
                <div class="help-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h3 class="mb-0">Panduan untuk Admin</h3>
                    <p class="mb-0 text-muted">Kelola sistem dan data</p>
                </div>
            </div>
            
            <h5 class="mt-4 mb-3">Alur Kerja Admin:</h5>
            
            <div class="mb-3">
                <span class="step-number">1</span>
                <strong>Setup Data Master</strong>
                <p class="ms-5 mt-2">
                    Tambahkan data secara berurutan:
                    <ul class="mt-2">
                        <li>Jurusan (RPL, TKJ, MM, dll)</li>
                        <li>Guru (tentukan siapa wali kelas)</li>
                        <li>Kelas (assign ke jurusan dan wali kelas)</li>
                        <li>Siswa (assign ke kelas masing-masing)</li>
                    </ul>
                </p>
            </div>
            
            <div class="mb-3">
                <span class="step-number">2</span>
                <strong>Generate Jadwal Piket</strong>
                <p class="ms-5 mt-2">
                    Menu "Jadwal Piket" → "Generate Jadwal Otomatis" → Pilih kelas, bulan, tahun → Set jumlah siswa per hari → Generate. 
                    Sistem akan otomatis membuat jadwal untuk 1 bulan (Senin-Jumat).
                </p>
            </div>
            
            <div class="mb-3">
                <span class="step-number">3</span>
                <strong>Monitor & Laporan</strong>
                <p class="ms-5 mt-2">
                    Pantau absensi melalui menu "Riwayat Absensi". Generate laporan di menu "Laporan Piket".
                </p>
            </div>
        </div>
        
        <!-- FAQ -->
        <div class="help-card" id="faq">
            <h3 class="mb-4"><i class="fas fa-comments"></i> Frequently Asked Questions</h3>
            
            <div class="faq-item" onclick="toggleFaq(this)">
                <h6 class="mb-0">
                    <i class="fas fa-chevron-right"></i> Bagaimana jika lupa username atau password?
                </h6>
                <div class="faq-answer">
                    Klik menu "Lupa Akun?" di halaman login, pilih role Anda, cari nama Anda di daftar untuk melihat username dan info password.
                </div>
            </div>
            
            <div class="faq-item" onclick="toggleFaq(this)">
                <h6 class="mb-0">
                    <i class="fas fa-chevron-right"></i> Bagaimana jika tidak bisa piket karena sakit?
                </h6>
                <div class="faq-answer">
                    Ajukan perizinan melalui menu "Perizinan" (jika tersedia), atau hubungi wali kelas Anda. Upload bukti surat dokter jika ada.
                </div>
            </div>
            
            <div class="faq-item" onclick="toggleFaq(this)">
                <h6 class="mb-0">
                    <i class="fas fa-chevron-right"></i> Foto yang diupload harus seperti apa?
                </h6>
                <div class="faq-answer">
                    Foto harus jelas, terang, dan menunjukkan:
                    <ul>
                        <li>Foto anggota: Semua anggota piket terlihat</li>
                        <li>Foto area 1-3: Kelas yang sudah bersih dan rapi</li>
                    </ul>
                    Aktifkan GPS pada kamera untuk timestamp lokasi.
                </div>
            </div>
            
            <div class="faq-item" onclick="toggleFaq(this)">
                <h6 class="mb-0">
                    <i class="fas fa-chevron-right"></i> Kenapa absensi ditolak atau gagal?
                </h6>
                <div class="faq-answer">
                    Kemungkinan penyebab:
                    <ul>
                        <li>Foto tidak lengkap (harus 4 foto)</li>
                        <li>Ukuran file terlalu besar (max 5MB per foto)</li>
                        <li>Anda bukan anggota jadwal tersebut</li>
                        <li>Absensi sudah pernah dilakukan untuk jadwal tersebut</li>
                    </ul>
                </div>
            </div>
            
            <div class="faq-item" onclick="toggleFaq(this)">
                <h6 class="mb-0">
                    <i class="fas fa-chevron-right"></i> Bagaimana cara mencetak laporan?
                </h6>
                <div class="faq-answer">
                    Untuk Guru: Menu "Laporan Piket" → Pilih tipe & periode → Klik "Tampilkan" → Klik "Cetak Laporan" atau tekan Ctrl+P.
                </div>
            </div>
        </div>
        
        <!-- Contact -->
        <div class="help-card">
            <h4><i class="fas fa-phone"></i> Butuh Bantuan Lebih Lanjut?</h4>
            <p>Hubungi administrator sistem atau wali kelas Anda:</p>
            <ul>
                <li><i class="fas fa-envelope"></i> Email: admin@smkn2.sch.id</li>
                <li><i class="fas fa-phone"></i> Telepon: (031) 1234-5678</li>
                <li><i class="fas fa-map-marker-alt"></i> SMK Negeri 2, Surabaya</li>
            </ul>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleFaq(element) {
            const answer = element.querySelector('.faq-answer');
            const icon = element.querySelector('i');
            
            if (answer.style.display === 'block') {
                answer.style.display = 'none';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-right');
            } else {
                answer.style.display = 'block';
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-down');
            }
        }
    </script>
</body>
</html>