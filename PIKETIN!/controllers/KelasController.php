<?php
/**
 * Kelas Controller
 * File: controllers/KelasController.php
 */

session_start();
require_once '../config/config.php';

if (!isLoggedIn() || !hasRole('Admin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$response = ['success' => false, 'message' => ''];

switch ($action) {
    case 'tambah':
        $response = tambahKelas($_POST);
        break;
    case 'edit':
        $response = editKelas($_POST);
        break;
    case 'hapus':
        $response = hapusKelas($_POST);
        break;
}

echo json_encode($response);

function tambahKelas($data) {
    $nama_kelas = sanitize($data['nama_kelas']);
    $tingkat = (int) $data['tingkat'];
    $id_jurusan = (int) $data['id_jurusan'];
    $id_wali_kelas = !empty($data['id_wali_kelas']) ? (int) $data['id_wali_kelas'] : 'NULL';
    
    if (empty($nama_kelas) || empty($tingkat) || empty($id_jurusan)) {
        return ['success' => false, 'message' => 'Data tidak lengkap'];
    }
    
    // Check duplicate
    $check = db_fetch_one("SELECT id_kelas FROM tb_kelas WHERE nama_kelas = '" . db_escape($nama_kelas) . "'");
    if ($check) {
        return ['success' => false, 'message' => 'Nama kelas sudah ada'];
    }
    
    $query = "INSERT INTO tb_kelas (nama_kelas, tingkat, id_jurusan, id_wali_kelas) 
              VALUES ('" . db_escape($nama_kelas) . "', $tingkat, $id_jurusan, $id_wali_kelas)";
    
    if (db_query($query)) {
        logActivity(getUserId(), 'Admin', "Menambah kelas: $nama_kelas");
        return ['success' => true, 'message' => 'Kelas berhasil ditambahkan'];
    }
    
    return ['success' => false, 'message' => 'Gagal menambahkan kelas'];
}

function editKelas($data) {
    $id_kelas = (int) $data['id_kelas'];
    $nama_kelas = sanitize($data['nama_kelas']);
    $tingkat = (int) $data['tingkat'];
    $id_jurusan = (int) $data['id_jurusan'];
    $id_wali_kelas = !empty($data['id_wali_kelas']) ? (int) $data['id_wali_kelas'] : 'NULL';
    
    if (empty($id_kelas) || empty($nama_kelas)) {
        return ['success' => false, 'message' => 'Data tidak lengkap'];
    }
    
    // Check duplicate
    $check = db_fetch_one("SELECT id_kelas FROM tb_kelas WHERE nama_kelas = '" . db_escape($nama_kelas) . "' AND id_kelas != $id_kelas");
    if ($check) {
        return ['success' => false, 'message' => 'Nama kelas sudah ada'];
    }
    
    $query = "UPDATE tb_kelas 
              SET nama_kelas = '" . db_escape($nama_kelas) . "',
                  tingkat = $tingkat,
                  id_jurusan = $id_jurusan,
                  id_wali_kelas = $id_wali_kelas
              WHERE id_kelas = $id_kelas";
    
    if (db_query($query)) {
        logActivity(getUserId(), 'Admin', "Mengedit kelas: $nama_kelas");
        return ['success' => true, 'message' => 'Kelas berhasil diupdate'];
    }
    
    return ['success' => false, 'message' => 'Gagal mengupdate kelas'];
}

function hapusKelas($data) {
    $id_kelas = (int) $data['id_kelas'];
    
    // Check siswa
    $check = db_fetch_one("SELECT COUNT(*) as total FROM tb_siswa WHERE id_kelas = $id_kelas");
    if ($check['total'] > 0) {
        return ['success' => false, 'message' => 'Tidak dapat menghapus kelas yang masih memiliki siswa'];
    }
    
    $kelas = db_fetch_one("SELECT nama_kelas FROM tb_kelas WHERE id_kelas = $id_kelas");
    
    if (db_query("DELETE FROM tb_kelas WHERE id_kelas = $id_kelas")) {
        logActivity(getUserId(), 'Admin', "Menghapus kelas: " . $kelas['nama_kelas']);
        return ['success' => true, 'message' => 'Kelas berhasil dihapus'];
    }
    
    return ['success' => false, 'message' => 'Gagal menghapus kelas'];
}

function logActivity($user_id, $role, $activity) {
    $ip = $_SERVER['REMOTE_ADDR'];
    db_query("INSERT INTO tb_activity_log (user_id, user_role, activity, ip_address) VALUES ($user_id, '$role', '" . db_escape($activity) . "', '$ip')");
}
?>