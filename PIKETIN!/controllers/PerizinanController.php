<?php
/**
 * Perizinan Controller
 * File: controllers/PerizinanController.php
 */

session_start();
require_once '../config/config.php';

if (!isLoggedIn() || !hasRole('Siswa')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'ajukan_izin') {
    $response = ajukanIzin($_POST, $_FILES);
    echo json_encode($response);
}

function ajukanIzin($data, $files) {
    $id_siswa = getUserId();
    $id_jadwal = (int) $data['id_jadwal'];
    $jenis_izin = sanitize($data['jenis_izin']);
    $alasan = sanitize($data['alasan']);
    
    if (empty($id_jadwal) || empty($jenis_izin) || empty($alasan)) {
        return ['success' => false, 'message' => 'Data tidak lengkap'];
    }
    
    // Get tanggal jadwal
    $jadwal = db_fetch_one("SELECT tanggal FROM tb_jadwal_piket WHERE id_jadwal = $id_jadwal");
    if (!$jadwal) {
        return ['success' => false, 'message' => 'Jadwal tidak ditemukan'];
    }
    
    // Check duplicate
    $check = db_fetch_one("
        SELECT id_izin FROM tb_perizinan 
        WHERE id_siswa = $id_siswa AND id_jadwal = $id_jadwal
    ");
    
    if ($check) {
        return ['success' => false, 'message' => 'Anda sudah mengajukan izin untuk jadwal ini'];
    }
    
    // Upload bukti if exists
    $bukti_izin = NULL;
    if (isset($files['bukti_izin']) && $files['bukti_izin']['error'] === UPLOAD_ERR_OK) {
        $allowed = array_merge(ALLOWED_IMAGE_EXT, ALLOWED_DOC_EXT);
        $upload = uploadFile($files['bukti_izin'], UPLOAD_PATH . 'bukti-izin/', $allowed);
        if ($upload['success']) {
            $bukti_izin = $upload['filename'];
        }
    }
    
    $bukti_value = $bukti_izin ? "'" . db_escape($bukti_izin) . "'" : "NULL";
    
    $query = "INSERT INTO tb_perizinan (id_siswa, id_jadwal, jenis_izin, alasan, tanggal_izin, bukti_izin, status_approval) 
              VALUES ($id_siswa, $id_jadwal, '$jenis_izin', '" . db_escape($alasan) . "', '{$jadwal['tanggal']}', $bukti_value, 'Pending')";
    
    if (db_query($query)) {
        // Update status anggota piket
        db_query("UPDATE tb_anggota_piket SET status_kehadiran = '$jenis_izin' WHERE id_jadwal = $id_jadwal AND id_siswa = $id_siswa");
        
        logActivity($id_siswa, 'Siswa', "Mengajukan izin: $jenis_izin untuk jadwal ID $id_jadwal");
        return ['success' => true, 'message' => 'Perizinan berhasil diajukan'];
    }
    
    return ['success' => false, 'message' => 'Gagal mengajukan izin'];
}

function logActivity($user_id, $role, $activity) {
    $ip = $_SERVER['REMOTE_ADDR'];
    db_query("INSERT INTO tb_activity_log (user_id, user_role, activity, ip_address) VALUES ($user_id, '$role', '" . db_escape($activity) . "', '$ip')");
}
?>