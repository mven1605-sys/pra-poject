<?php
/**
 * Siswa Controller
 * File: controllers/SiswaController.php
 * Handle CRUD untuk Siswa (mirip dengan GuruController)
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
        $response = tambahSiswa($_POST, $_FILES);
        break;
    case 'edit':
        $response = editSiswa($_POST, $_FILES);
        break;
    case 'hapus':
        $response = hapusSiswa($_POST);
        break;
}

echo json_encode($response);

function tambahSiswa($data, $files) {
    $nis = sanitize($data['nis']);
    $nisn = sanitize($data['nisn']);
    $nik = sanitize($data['nik']);
    $nama_lengkap = sanitize($data['nama_lengkap']);
    $username = sanitize($data['username']);
    $password = !empty($data['password']) ? $data['password'] : $nis;
    $id_kelas = (int) $data['id_kelas'];
    $alamat = sanitize($data['alamat']);
    $no_telepon = sanitize($data['no_telepon']);
    $email = sanitize($data['email']);
    $koordinat_rumah = sanitize($data['koordinat_rumah']);
    $gmaps_link = sanitize($data['gmaps_link']);
    
    if (empty($nis) || empty($nisn) || empty($nama_lengkap) || empty($id_kelas)) {
        return ['success' => false, 'message' => 'Data wajib harus diisi'];
    }
    
    // Check duplicates
    if (db_fetch_one("SELECT id_siswa FROM tb_siswa WHERE nis = '" . db_escape($nis) . "'")) {
        return ['success' => false, 'message' => 'NIS sudah terdaftar'];
    }
    if (db_fetch_one("SELECT id_siswa FROM tb_siswa WHERE nisn = '" . db_escape($nisn) . "'")) {
        return ['success' => false, 'message' => 'NISN sudah terdaftar'];
    }
    if (db_fetch_one("SELECT id_siswa FROM tb_siswa WHERE nik = '" . db_escape($nik) . "'")) {
        return ['success' => false, 'message' => 'NIK sudah terdaftar'];
    }
    if (db_fetch_one("SELECT id_siswa FROM tb_siswa WHERE username = '" . db_escape($username) . "'")) {
        return ['success' => false, 'message' => 'Username sudah digunakan'];
    }
    
    // Handle foto
    $foto_profil = 'default-avatar.png';
    if (isset($files['foto_profil']) && $files['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadFile($files['foto_profil'], UPLOAD_PATH . 'foto-siswa/', ALLOWED_IMAGE_EXT);
        if ($upload['success']) {
            $foto_profil = $upload['filename'];
        } else {
            return ['success' => false, 'message' => 'Upload foto gagal: ' . $upload['message']];
        }
    }
    
    $password_hash = hashPassword($password);
    
    $query = "INSERT INTO tb_siswa (nis, nisn, nik, nama_lengkap, username, password, id_kelas, alamat, no_telepon, email, foto_profil, koordinat_rumah, gmaps_link) 
              VALUES (
                  '" . db_escape($nis) . "',
                  '" . db_escape($nisn) . "',
                  '" . db_escape($nik) . "',
                  '" . db_escape($nama_lengkap) . "',
                  '" . db_escape($username) . "',
                  '" . db_escape($password_hash) . "',
                  $id_kelas,
                  '" . db_escape($alamat) . "',
                  '" . db_escape($no_telepon) . "',
                  '" . db_escape($email) . "',
                  '" . db_escape($foto_profil) . "',
                  '" . db_escape($koordinat_rumah) . "',
                  '" . db_escape($gmaps_link) . "'
              )";
    
    if (db_query($query)) {
        logActivity(getUserId(), 'Admin', "Menambah siswa: $nama_lengkap");
        return ['success' => true, 'message' => 'Siswa berhasil ditambahkan'];
    }
    
    return ['success' => false, 'message' => 'Gagal menambahkan siswa'];
}

function editSiswa($data, $files) {
    $id_siswa = (int) $data['id_siswa'];
    $nis = sanitize($data['nis']);
    $nisn = sanitize($data['nisn']);
    $nik = sanitize($data['nik']);
    $nama_lengkap = sanitize($data['nama_lengkap']);
    $username = sanitize($data['username']);
    $id_kelas = (int) $data['id_kelas'];
    $alamat = sanitize($data['alamat']);
    $no_telepon = sanitize($data['no_telepon']);
    $email = sanitize($data['email']);
    $koordinat_rumah = sanitize($data['koordinat_rumah']);
    $gmaps_link = sanitize($data['gmaps_link']);
    $is_active = (int) $data['is_active'];
    $foto_lama = $data['foto_lama'];
    
    if (empty($id_siswa)) {
        return ['success' => false, 'message' => 'ID siswa tidak valid'];
    }
    
    // Check duplicates (exclude current ID)
    if (db_fetch_one("SELECT id_siswa FROM tb_siswa WHERE nis = '" . db_escape($nis) . "' AND id_siswa != $id_siswa")) {
        return ['success' => false, 'message' => 'NIS sudah digunakan'];
    }
    if (db_fetch_one("SELECT id_siswa FROM tb_siswa WHERE nisn = '" . db_escape($nisn) . "' AND id_siswa != $id_siswa")) {
        return ['success' => false, 'message' => 'NISN sudah digunakan'];
    }
    if (db_fetch_one("SELECT id_siswa FROM tb_siswa WHERE nik = '" . db_escape($nik) . "' AND id_siswa != $id_siswa")) {
        return ['success' => false, 'message' => 'NIK sudah digunakan'];
    }
    if (db_fetch_one("SELECT id_siswa FROM tb_siswa WHERE username = '" . db_escape($username) . "' AND id_siswa != $id_siswa")) {
        return ['success' => false, 'message' => 'Username sudah digunakan'];
    }
    
    // Handle foto
    $foto_profil = $foto_lama;
    if (isset($files['foto_profil']) && $files['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadFile($files['foto_profil'], UPLOAD_PATH . 'foto-siswa/', ALLOWED_IMAGE_EXT);
        if ($upload['success']) {
            if ($foto_lama !== 'default-avatar.png') {
                deleteFile(UPLOAD_PATH . 'foto-siswa/' . $foto_lama);
            }
            $foto_profil = $upload['filename'];
        }
    }
    
    $query = "UPDATE tb_siswa SET 
              nis = '" . db_escape($nis) . "',
              nisn = '" . db_escape($nisn) . "',
              nik = '" . db_escape($nik) . "',
              nama_lengkap = '" . db_escape($nama_lengkap) . "',
              username = '" . db_escape($username) . "',
              id_kelas = $id_kelas,
              alamat = '" . db_escape($alamat) . "',
              no_telepon = '" . db_escape($no_telepon) . "',
              email = '" . db_escape($email) . "',
              foto_profil = '" . db_escape($foto_profil) . "',
              koordinat_rumah = '" . db_escape($koordinat_rumah) . "',
              gmaps_link = '" . db_escape($gmaps_link) . "',
              is_active = $is_active";
    
    if (!empty($data['password'])) {
        $password_hash = hashPassword($data['password']);
        $query .= ", password = '" . db_escape($password_hash) . "'";
    }
    
    $query .= " WHERE id_siswa = $id_siswa";
    
    if (db_query($query)) {
        logActivity(getUserId(), 'Admin', "Mengedit siswa: $nama_lengkap");
        return ['success' => true, 'message' => 'Data siswa berhasil diupdate'];
    }
    
    return ['success' => false, 'message' => 'Gagal mengupdate data siswa'];
}

function hapusSiswa($data) {
    $id_siswa = (int) $data['id_siswa'];
    
    if (empty($id_siswa)) {
        return ['success' => false, 'message' => 'ID siswa tidak valid'];
    }
    
    $siswa = db_fetch_one("SELECT nama_lengkap, foto_profil FROM tb_siswa WHERE id_siswa = $id_siswa");
    
    if (db_query("DELETE FROM tb_siswa WHERE id_siswa = $id_siswa")) {
        if ($siswa['foto_profil'] !== 'default-avatar.png') {
            deleteFile(UPLOAD_PATH . 'foto-siswa/' . $siswa['foto_profil']);
        }
        
        logActivity(getUserId(), 'Admin', "Menghapus siswa: " . $siswa['nama_lengkap']);
        return ['success' => true, 'message' => 'Siswa berhasil dihapus'];
    }
    
    return ['success' => false, 'message' => 'Gagal menghapus siswa'];
}

function logActivity($user_id, $role, $activity) {
    $ip = $_SERVER['REMOTE_ADDR'];
    db_query("INSERT INTO tb_activity_log (user_id, user_role, activity, ip_address) VALUES ($user_id, '$role', '" . db_escape($activity) . "', '$ip')");
}
?>