<?php
/**
 * Absensi Controller
 * File: controllers/AbsensiController.php
 * Handle upload absensi piket dengan 4 foto
 */

session_start();
require_once '../config/config.php';

if (!isLoggedIn() || !hasRole('Siswa')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$response = ['success' => false, 'message' => ''];

if ($action === 'absensi') {
    $response = submitAbsensi($_POST, $_FILES);
}

echo json_encode($response);

/**
 * Submit Absensi Piket
 */
function submitAbsensi($data, $files) {
    $id_jadwal = (int) $data['id_jadwal'];
    $keterangan = sanitize($data['keterangan']);
    $id_siswa = getUserId();
    
    if (empty($id_jadwal)) {
        return ['success' => false, 'message' => 'ID jadwal tidak valid'];
    }
    
    // Validasi: Cek apakah siswa ini ada di jadwal
    $check_anggota = db_fetch_one("
        SELECT ap.id_anggota_piket 
        FROM tb_anggota_piket ap
        WHERE ap.id_jadwal = $id_jadwal AND ap.id_siswa = $id_siswa
    ");
    
    if (!$check_anggota) {
        return ['success' => false, 'message' => 'Anda tidak terdaftar dalam jadwal piket ini'];
    }
    
    // Cek apakah sudah absen
    $check_absen = db_fetch_one("SELECT id_absensi FROM tb_absensi_piket WHERE id_jadwal = $id_jadwal");
    if ($check_absen) {
        return ['success' => false, 'message' => 'Absensi untuk jadwal ini sudah dilakukan'];
    }
    
    // Validasi file upload
    $required_files = ['foto_anggota', 'foto_area1', 'foto_area2', 'foto_area3'];
    foreach ($required_files as $field) {
        if (!isset($files[$field]) || $files[$field]['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Semua foto harus diupload'];
        }
    }
    
    // Upload semua foto
    $uploaded_files = [];
    $upload_errors = [];
    
    foreach ($required_files as $field) {
        $upload = uploadFile($files[$field], UPLOAD_PATH . 'foto-absensi/', ALLOWED_IMAGE_EXT);
        
        if ($upload['success']) {
            $uploaded_files[$field] = $upload['filename'];
        } else {
            $upload_errors[] = $upload['message'];
        }
    }
    
    // Jika ada error upload, hapus file yang sudah terupload
    if (count($upload_errors) > 0) {
        foreach ($uploaded_files as $filename) {
            deleteFile(UPLOAD_PATH . 'foto-absensi/' . $filename);
        }
        return ['success' => false, 'message' => 'Upload foto gagal: ' . implode(', ', $upload_errors)];
    }
    
    // Get waktu dan tanggal
    $tanggal_absensi = getCurrentDate();
    $waktu_absensi = getCurrentTime();
    
    // Cek apakah terlambat
    $status = 'Tepat Waktu';
    if (isLate($waktu_absensi, PIKET_END_TIME)) {
        $status = 'Terlambat';
    }
    
    // Simulasi GPS (dalam production, ambil dari EXIF foto atau JavaScript geolocation)
    $lokasi_gps = '-7.250445, 112.768845'; // Default koordinat sekolah
    
    // Insert absensi
    $query = "INSERT INTO tb_absensi_piket (
                id_jadwal, tanggal_absensi, waktu_absensi, 
                foto_anggota, foto_area1, foto_area2, foto_area3,
                lokasi_gps, keterangan, status
              ) VALUES (
                $id_jadwal,
                '$tanggal_absensi',
                '$waktu_absensi',
                '" . db_escape($uploaded_files['foto_anggota']) . "',
                '" . db_escape($uploaded_files['foto_area1']) . "',
                '" . db_escape($uploaded_files['foto_area2']) . "',
                '" . db_escape($uploaded_files['foto_area3']) . "',
                '$lokasi_gps',
                '" . db_escape($keterangan) . "',
                '$status'
              )";
    
    if (db_query($query)) {
        // Update status jadwal
        db_query("UPDATE tb_jadwal_piket SET status = 'Selesai' WHERE id_jadwal = $id_jadwal");
        
        // Update status kehadiran anggota
        db_query("UPDATE tb_anggota_piket SET status_kehadiran = 'Hadir' WHERE id_jadwal = $id_jadwal");
        
        // Log activity
        logActivity($id_siswa, 'Siswa', "Melakukan absensi piket: Jadwal ID $id_jadwal");
        
        return [
            'success' => true, 
            'message' => 'Absensi piket berhasil dikirim! Status: ' . $status
        ];
    }
    
    // Jika gagal insert, hapus file yang sudah terupload
    foreach ($uploaded_files as $filename) {
        deleteFile(UPLOAD_PATH . 'foto-absensi/' . $filename);
    }
    
    return ['success' => false, 'message' => 'Gagal menyimpan absensi'];
}

function logActivity($user_id, $role, $activity) {
    $ip = $_SERVER['REMOTE_ADDR'];
    db_query("INSERT INTO tb_activity_log (user_id, user_role, activity, ip_address) VALUES ($user_id, '$role', '" . db_escape($activity) . "', '$ip')");
}
?>