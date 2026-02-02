-- ================================================
-- DATABASE SCHEMA: PIKETIN - SMK NEGERI 2
-- Sistem Absensi & Monitoring Piket
-- ================================================

CREATE DATABASE IF NOT EXISTS db_piketin;
USE db_piketin;

-- ================================================
-- TABEL MASTER DATA
-- ================================================

-- Tabel Jurusan
CREATE TABLE tb_jurusan (
    id_jurusan INT PRIMARY KEY AUTO_INCREMENT,
    kode_jurusan VARCHAR(10) NOT NULL UNIQUE,
    nama_jurusan VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Kelas
CREATE TABLE tb_kelas (
    id_kelas INT PRIMARY KEY AUTO_INCREMENT,
    nama_kelas VARCHAR(50) NOT NULL,
    tingkat INT NOT NULL COMMENT '10, 11, 12',
    id_jurusan INT NOT NULL,
    id_wali_kelas INT NULL COMMENT 'ID Guru sebagai wali kelas',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_jurusan) REFERENCES tb_jurusan(id_jurusan) ON DELETE CASCADE
);

-- Tabel Guru
CREATE TABLE tb_guru (
    id_guru INT PRIMARY KEY AUTO_INCREMENT,
    nip VARCHAR(20) NOT NULL UNIQUE,
    nik VARCHAR(16) NOT NULL UNIQUE,
    nama_lengkap VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    alamat TEXT,
    no_telepon VARCHAR(15),
    email VARCHAR(100),
    foto_profil VARCHAR(255) DEFAULT 'default-avatar.png',
    koordinat_rumah VARCHAR(100) COMMENT 'Format: latitude,longitude',
    gmaps_link TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tambah Foreign Key untuk wali kelas setelah tabel guru dibuat
ALTER TABLE tb_kelas 
ADD FOREIGN KEY (id_wali_kelas) REFERENCES tb_guru(id_guru) ON DELETE SET NULL;

-- Tabel Siswa
CREATE TABLE tb_siswa (
    id_siswa INT PRIMARY KEY AUTO_INCREMENT,
    nis VARCHAR(20) NOT NULL UNIQUE,
    nisn VARCHAR(20) NOT NULL UNIQUE,
    nik VARCHAR(16) NOT NULL UNIQUE,
    nama_lengkap VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    id_kelas INT NOT NULL,
    alamat TEXT,
    no_telepon VARCHAR(15),
    email VARCHAR(100),
    foto_profil VARCHAR(255) DEFAULT 'default-avatar.png',
    koordinat_rumah VARCHAR(100) COMMENT 'Format: latitude,longitude',
    gmaps_link TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kelas) REFERENCES tb_kelas(id_kelas) ON DELETE CASCADE
);

-- Tabel Admin
CREATE TABLE tb_admin (
    id_admin INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    foto_profil VARCHAR(255) DEFAULT 'default-avatar.png',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ================================================
-- TABEL JADWAL & ABSENSI
-- ================================================

-- Tabel Jadwal Piket
CREATE TABLE tb_jadwal_piket (
    id_jadwal INT PRIMARY KEY AUTO_INCREMENT,
    id_kelas INT NOT NULL,
    hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat') NOT NULL,
    minggu_ke INT NOT NULL COMMENT '1-5',
    bulan INT NOT NULL COMMENT '1-12',
    tahun INT NOT NULL,
    tanggal DATE NOT NULL,
    status ENUM('Belum', 'Selesai', 'Terlambat') DEFAULT 'Belum',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kelas) REFERENCES tb_kelas(id_kelas) ON DELETE CASCADE,
    UNIQUE KEY unique_jadwal (id_kelas, tanggal)
);

-- Tabel Anggota Jadwal Piket (relasi many-to-many)
CREATE TABLE tb_anggota_piket (
    id_anggota_piket INT PRIMARY KEY AUTO_INCREMENT,
    id_jadwal INT NOT NULL,
    id_siswa INT NOT NULL,
    status_kehadiran ENUM('Hadir', 'Izin', 'Sakit', 'Alpha') DEFAULT 'Hadir',
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_jadwal) REFERENCES tb_jadwal_piket(id_jadwal) ON DELETE CASCADE,
    FOREIGN KEY (id_siswa) REFERENCES tb_siswa(id_siswa) ON DELETE CASCADE,
    UNIQUE KEY unique_anggota (id_jadwal, id_siswa)
);

-- Tabel Perizinan
CREATE TABLE tb_perizinan (
    id_izin INT PRIMARY KEY AUTO_INCREMENT,
    id_siswa INT NOT NULL,
    id_jadwal INT NOT NULL,
    jenis_izin ENUM('Izin', 'Sakit') NOT NULL,
    alasan TEXT NOT NULL,
    tanggal_izin DATE NOT NULL,
    bukti_izin VARCHAR(255) COMMENT 'Surat izin/keterangan dokter',
    status_approval ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_siswa) REFERENCES tb_siswa(id_siswa) ON DELETE CASCADE,
    FOREIGN KEY (id_jadwal) REFERENCES tb_jadwal_piket(id_jadwal) ON DELETE CASCADE
);

-- Tabel Absensi Piket
CREATE TABLE tb_absensi_piket (
    id_absensi INT PRIMARY KEY AUTO_INCREMENT,
    id_jadwal INT NOT NULL,
    tanggal_absensi DATE NOT NULL,
    waktu_absensi TIME NOT NULL,
    foto_anggota VARCHAR(255) NOT NULL,
    foto_area1 VARCHAR(255) NOT NULL,
    foto_area2 VARCHAR(255) NOT NULL,
    foto_area3 VARCHAR(255) NOT NULL,
    lokasi_gps VARCHAR(100) COMMENT 'Format: latitude,longitude',
    keterangan TEXT,
    status ENUM('Tepat Waktu', 'Terlambat') DEFAULT 'Tepat Waktu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_jadwal) REFERENCES tb_jadwal_piket(id_jadwal) ON DELETE CASCADE
);

-- ================================================
-- TABEL LAPORAN & NOTIFIKASI
-- ================================================

-- Tabel Laporan Absensi
CREATE TABLE tb_laporan (
    id_laporan INT PRIMARY KEY AUTO_INCREMENT,
    id_kelas INT NOT NULL,
    tipe_laporan ENUM('Mingguan', 'Bulanan') NOT NULL,
    periode_awal DATE NOT NULL,
    periode_akhir DATE NOT NULL,
    total_hadir INT DEFAULT 0,
    total_izin INT DEFAULT 0,
    total_sakit INT DEFAULT 0,
    total_alpha INT DEFAULT 0,
    generated_by INT NOT NULL COMMENT 'ID Admin/Guru yang generate',
    generated_role ENUM('Admin', 'Guru') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kelas) REFERENCES tb_kelas(id_kelas) ON DELETE CASCADE
);

-- Tabel Notifikasi
CREATE TABLE tb_notifikasi (
    id_notifikasi INT PRIMARY KEY AUTO_INCREMENT,
    id_siswa INT NOT NULL,
    id_jadwal INT NOT NULL,
    jenis_notif ENUM('Alpha', 'Peringatan', 'Info') NOT NULL,
    pesan TEXT NOT NULL,
    is_sent TINYINT(1) DEFAULT 0 COMMENT 'Status pengiriman WA',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_siswa) REFERENCES tb_siswa(id_siswa) ON DELETE CASCADE,
    FOREIGN KEY (id_jadwal) REFERENCES tb_jadwal_piket(id_jadwal) ON DELETE CASCADE
);

-- ================================================
-- TABEL SETTINGS & LOG
-- ================================================

-- Tabel Settings System
CREATE TABLE tb_settings (
    id_setting INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Activity Log
CREATE TABLE tb_activity_log (
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    user_role ENUM('Admin', 'Guru', 'Siswa') NOT NULL,
    activity VARCHAR(255) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================
-- TABEL NOTIFICATION LOGS
-- ================================================

-- Tabel WhatsApp Notification Log
CREATE TABLE tb_notifikasi_log (
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    phone VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    status_code INT,
    response TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Email Notification Log
CREATE TABLE tb_email_log (
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    email_to VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    status ENUM('sent', 'failed') NOT NULL,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================================
-- INSERT DATA DEFAULT
-- ================================================

-- Insert Admin Default
INSERT INTO tb_admin (username, password, nama_lengkap, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@smkn2.sch.id');
-- Password default: password

-- Insert Jurusan Default
INSERT INTO tb_jurusan (kode_jurusan, nama_jurusan) VALUES
('RPL', 'Rekayasa Perangkat Lunak'),
('TKJ', 'Teknik Komputer dan Jaringan'),
('MM', 'Multimedia'),
('OTKP', 'Otomatisasi Tata Kelola Perkantoran'),
('AKL', 'Akuntansi dan Keuangan Lembaga');

-- Insert Settings Default
INSERT INTO tb_settings (setting_key, setting_value, description) VALUES
('app_name', 'PIKETIN - SMK Negeri 2', 'Nama Aplikasi'),
('school_name', 'SMK Negeri 2', 'Nama Sekolah'),
('max_upload_size', '5242880', 'Max upload size in bytes (5MB)'),
('piket_start_time', '06:30:00', 'Waktu mulai piket'),
('piket_end_time', '07:00:00', 'Batas waktu absensi piket'),
('whatsapp_api_key', '', 'API Key untuk notifikasi WhatsApp'),
('timezone', 'Asia/Jakarta', 'Timezone aplikasi');

-- ================================================
-- INDEXES untuk Performance
-- ================================================

CREATE INDEX idx_siswa_kelas ON tb_siswa(id_kelas);
CREATE INDEX idx_jadwal_tanggal ON tb_jadwal_piket(tanggal);
CREATE INDEX idx_absensi_tanggal ON tb_absensi_piket(tanggal_absensi);
CREATE INDEX idx_notif_siswa ON tb_notifikasi(id_siswa, is_read);
CREATE INDEX idx_log_user ON tb_activity_log(user_id, user_role);

-- ================================================
-- END OF SCHEMA
-- ================================================