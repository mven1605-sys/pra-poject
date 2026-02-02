<?php
/**
 * Application Configuration
 * File: config/config.php
 * Konfigurasi umum aplikasi
 */

// Start session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error Reporting (ubah ke 0 saat production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ===================================
// PATH & URL CONFIGURATION
// ===================================

// Base URL (sesuaikan dengan lokasi project Anda)
define('BASE_URL', 'http://localhost/piketin/');

// Base Path
define('BASE_PATH', dirname(dirname(__FILE__)) . '/');

// Assets Path
define('ASSETS_URL', BASE_URL . 'assets/');
define('CSS_URL', ASSETS_URL . 'css/');
define('JS_URL', ASSETS_URL . 'js/');
define('IMG_URL', ASSETS_URL . 'img/');

// Upload Path
define('UPLOAD_PATH', BASE_PATH . 'uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

// ===================================
// APPLICATION SETTINGS
// ===================================

define('APP_NAME', 'PIKETIN');
define('SCHOOL_NAME', 'SMK Negeri 2');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'Sistem Absensi & Monitoring Piket');

// ===================================
// UPLOAD SETTINGS
// ===================================

// Max upload size (dalam bytes) - 5MB
define('MAX_UPLOAD_SIZE', 5242880);

// Allowed file extensions untuk foto
define('ALLOWED_IMAGE_EXT', ['jpg', 'jpeg', 'png', 'gif']);

// Allowed file extensions untuk dokumen
define('ALLOWED_DOC_EXT', ['pdf', 'doc', 'docx']);

// ===================================
// PIKET SETTINGS
// ===================================

// Waktu mulai piket
define('PIKET_START_TIME', '06:30:00');

// Batas waktu absensi piket (lewat dari ini = terlambat)
define('PIKET_END_TIME', '07:00:00');

// Hari piket
define('PIKET_DAYS', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']);

// ===================================
// PAGINATION SETTINGS
// ===================================

define('ITEMS_PER_PAGE', 10);

// ===================================
// DATE & TIME FORMAT
// ===================================

define('DATE_FORMAT', 'd-m-Y');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'd-m-Y H:i:s');

// ===================================
// SESSION TIMEOUT (dalam detik)
// ===================================

define('SESSION_TIMEOUT', 3600); // 1 jam

// ===================================
// WHATSAPP API (untuk notifikasi)
// ===================================

define('WA_API_URL', ''); // Isi dengan URL API WhatsApp Anda (Fonnte/Wablas)
define('WA_API_KEY', 'rYHxbCXQQkhKPtgH5CNi'); // Isi dengan API Key WhatsApp Anda

// ===================================
// EMAIL CONFIGURATION (SMTP)
// ===================================

define('SMTP_HOST', 'smtp.gmail.com');              // SMTP server
define('SMTP_PORT', 587);                            // SMTP port (587 atau 465)
define('SMTP_USER', '');                             // Email Anda
define('SMTP_PASS', '');                             // Password atau App Password
define('SMTP_FROM_EMAIL', 'noreply@smkn2.sch.id'); // Email pengirim
define('SMTP_FROM_NAME', 'Sistem Piketin SMK N 2'); // Nama pengirim

// ===================================
// ROLES CONSTANT
// ===================================

define('ROLE_ADMIN', 'Admin');
define('ROLE_GURU', 'Guru');
define('ROLE_SISWA', 'Siswa');

// ===================================
// STATUS CONSTANT
// ===================================

// Status Kehadiran
define('STATUS_HADIR', 'Hadir');
define('STATUS_IZIN', 'Izin');
define('STATUS_SAKIT', 'Sakit');
define('STATUS_ALPHA', 'Alpha');

// Status Jadwal
define('STATUS_BELUM', 'Belum');
define('STATUS_SELESAI', 'Selesai');
define('STATUS_TERLAMBAT', 'Terlambat');

// ===================================
// INCLUDE FILES
// ===================================

// Include database configuration
require_once BASE_PATH . 'config/database.php';

// Include helper functions
require_once BASE_PATH . 'config/functions.php';

// ===================================
// CHECK SESSION TIMEOUT
// ===================================

if (isset($_SESSION['last_activity'])) {
    $inactive = time() - $_SESSION['last_activity'];
    
    if ($inactive >= SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . 'login.php?timeout=1');
        exit();
    }
}

$_SESSION['last_activity'] = time();

?>