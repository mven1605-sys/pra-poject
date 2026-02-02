<?php
/**
 * File Koneksi Database - Stockify System
 * Menggunakan MySQLi dengan error handling
 */

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'stockify');

// Koneksi ke Database
$koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek Koneksi
if ($koneksi->connect_error) {
    die("Koneksi Database Gagal: " . $koneksi->connect_error);
}

// Set Charset UTF-8
$koneksi->set_charset("utf8mb4");

/**
 * Function untuk Sanitasi Input (Mencegah SQL Injection)
 * @param mysqli $conn - Koneksi database
 * @param string $data - Data yang akan disanitasi
 * @return string - Data yang sudah aman
 */
function clean_input($conn, $data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

/**
 * Function untuk Format Rupiah
 * @param int $angka - Nominal yang akan diformat
 * @return string - Format Rupiah
 */
function format_rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

/**
 * Function untuk Format Tanggal Indonesia
 * @param string $tanggal - Tanggal format Y-m-d H:i:s
 * @return string - Format tanggal Indonesia
 */
function format_tanggal($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $pecahkan = explode(' ', $tanggal);
    $tanggal_pecah = explode('-', $pecahkan[0]);
    
    return $tanggal_pecah[2] . ' ' . $bulan[(int)$tanggal_pecah[1]] . ' ' . $tanggal_pecah[0];
}

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set 1 jika menggunakan HTTPS
    session_start();
}
?>