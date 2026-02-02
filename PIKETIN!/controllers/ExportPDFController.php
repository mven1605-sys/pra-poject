<?php
/**
 * Export PDF Controller
 * File: controllers/ExportPDFController.php
 */

session_start();
require_once '../config/config.php';
require_once '../helpers/pdf_helper.php';

// Check login dan role
if (!isLoggedIn() || !hasRole('Guru')) {
    header('Location: ../login.php');
    exit();
}

$id_kelas = $_SESSION['id_kelas'];

// Get parameters
$tipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'bulanan';
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
$minggu = isset($_GET['minggu']) ? (int)$_GET['minggu'] : 1;

$bulan_indo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// Build query
if ($tipe == 'mingguan') {
    $first_day = "$tahun-$bulan-01";
    $start_date = date('Y-m-d', strtotime("$first_day +" . (($minggu - 1) * 7) . " days"));
    $end_date = date('Y-m-d', strtotime("$start_date +6 days"));
    
    $where = "jp.tanggal BETWEEN '$start_date' AND '$end_date' AND jp.minggu_ke = $minggu";
    $periode = "Minggu ke-$minggu, " . $bulan_indo[$bulan] . " $tahun";
} else {
    $where = "MONTH(jp.tanggal) = $bulan AND YEAR(jp.tanggal) = $tahun";
    $periode = $bulan_indo[$bulan] . " $tahun";
}

// Get laporan data
$laporan_data = db_fetch_all("
    SELECT jp.*, ab.waktu_absensi, ab.status,
           GROUP_CONCAT(DISTINCT s.nama_lengkap SEPARATOR ', ') as anggota_nama,
           COUNT(DISTINCT ap.id_siswa) as jumlah_anggota
    FROM tb_jadwal_piket jp
    INNER JOIN tb_anggota_piket ap ON jp.id_jadwal = ap.id_jadwal
    LEFT JOIN tb_siswa s ON ap.id_siswa = s.id_siswa
    LEFT JOIN tb_absensi_piket ab ON jp.id_jadwal = ab.id_jadwal
    WHERE jp.id_kelas = $id_kelas AND $where
    GROUP BY jp.id_jadwal
    ORDER BY jp.tanggal
");

// Calculate summary
$total_jadwal = count($laporan_data);
$total_hadir = 0;
$total_tepat_waktu = 0;
$total_terlambat = 0;

foreach ($laporan_data as $item) {
    if ($item['waktu_absensi']) {
        $total_hadir++;
        if ($item['status'] == 'Tepat Waktu') {
            $total_tepat_waktu++;
        } else {
            $total_terlambat++;
        }
    }
}

$summary = [
    'total_jadwal' => $total_jadwal,
    'total_hadir' => $total_hadir,
    'tepat_waktu' => $total_tepat_waktu,
    'terlambat' => $total_terlambat
];

// Generate and download PDF
downloadLaporanPDF(
    $laporan_data,
    $periode,
    $_SESSION['nama_kelas'],
    $summary,
    getUserName(),
    $_SESSION['nip']
);
?>