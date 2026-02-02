<?php
/**
 * Guru Controller
 * File: controllers/GuruController.php
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
        $response = tambahGuru($_POST, $_FILES);
        break;
    case 'edit':
        $response = editGuru($_POST, $_FILES);
        break;
    case 'hapus':
        $response = hapusGuru($_POST);
        break;
}

echo json_encode($response);

/**
 * Tambah Guru
 */
function tambahGuru($data, $files) {
    // Validasi input
    $nip = sanitize($data['nip']);
    $nik = sanitize($data['nik']);
    $nama_lengkap = sanitize($data['nama_lengkap']);
    $username = sanitize($data['username']);
    $password = !empty($data['password']) ? $data['password'] : $nip; // Default password = NIP
    $alamat = sanitize($data['alamat']);
    $no_telepon = sanitize($data['no_telepon']);
    $email = sanitize($data['email']);
    $koordinat_rumah = sanitize($data['koordinat_rumah']);
    $gmaps_link = sanitize($data['gmaps_link']);
    
    if (empty($nip) || empty($nik) || empty($nama_lengkap) || empty($username)) {
        return ['success' => false, 'message' => 'Data wajib harus diisi'];
    }
    
    // Check duplicate NIP
    $check = db_fetch_one("SELECT id_guru FROM tb_guru WHERE nip = '" . db_escape($nip) . "'");
    if ($check) {
        return ['success' => false, 'message' => 'NIP sudah terdaftar'];
    }
    
    // Check duplicate NIK
    $check = db_fetch_one("SELECT id_guru FROM tb_guru WHERE nik = '" . db_escape($nik) . "'");
    if ($check) {
        return ['success' => false, 'message' => 'NIK sudah terdaftar'];
    }
    
    // Check duplicate Username
    $check = db_fetch_one("SELECT id_guru FROM tb_guru WHERE username = '" . db_escape($username) . "'");
    if ($check) {
        return ['success' => false, 'message' => 'Username sudah digunakan'];
    }
    
    // Handle foto upload
    $foto_profil = 'default-avatar.png';
    if (isset($files['foto_profil']) && $files['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadFile($files['foto_profil'], UPLOAD_PATH . 'foto-guru/', ALLOWED_IMAGE_EXT);
        if ($upload['success']) {
            $foto_profil = $upload['filename'];
        } else {
            return ['success' => false, 'message' => 'Upload foto gagal: ' . $upload['message']];
        }
    }
    
    // Hash password
    $password_hash = hashPassword($password);
    
    // Insert data
    $query = "INSERT INTO tb_guru (nip, nik, nama_lengkap, username, password, alamat, no_telepon, email, foto_profil, koordinat_rumah, gmaps_link) 
              VALUES (
                  '" . db_escape($nip) . "',
                  '" . db_escape($nik) . "',
                  '" . db_escape($nama_lengkap) . "',
                  '" . db_escape($username) . "',
                  '" . db_escape($password_hash) . "',
                  '" . db_escape($alamat) . "',
                  '" . db_escape($no_telepon) . "',
                  '" . db_escape($email) . "',
                  '" . db_escape($foto_profil) . "',
                  '" . db_escape($koordinat_rumah) . "',
                  '" . db_escape($gmaps_link) . "'
              )";
    
    if (db_query($query)) {
        logActivity(getUserId(), 'Admin', "Menambah guru: $nama_lengkap");
        return ['success' => true, 'message' => 'Guru berhasil ditambahkan'];
    }
    
    return ['success' => false, 'message' => 'Gagal menambahkan guru'];
}

/**
 * Edit Guru
 */
function editGuru($data, $files) {
    $id_guru = (int) $data['id_guru'];
    $nip = sanitize($data['nip']);
    $nik = sanitize($data['nik']);
    $nama_lengkap = sanitize($data['nama_lengkap']);
    $username = sanitize($data['username']);
    $alamat = sanitize($data['alamat']);
    $no_telepon = sanitize($data['no_telepon']);
    $email = sanitize($data['email']);
    $koordinat_rumah = sanitize($data['koordinat_rumah']);
    $gmaps_link = sanitize($data['gmaps_link']);
    $is_active = (int) $data['is_active'];
    $foto_lama = $data['foto_lama'];
    
    if (empty($id_guru) || empty($nip) || empty($nama_lengkap)) {
        return ['success' => false, 'message' => 'Data tidak lengkap'];
    }
    
    // Check duplicate NIP
    $check = db_fetch_one("SELECT id_guru FROM tb_guru WHERE nip = '" . db_escape($nip) . "' AND id_guru != $id_guru");
    if ($check) {
        return ['success' => false, 'message' => 'NIP sudah digunakan'];
    }
    
    // Check duplicate NIK
    $check = db_fetch_one("SELECT id_guru FROM tb_guru WHERE nik = '" . db_escape($nik) . "' AND id_guru != $id_guru");
    if ($check) {
        return ['success' => false, 'message' => 'NIK sudah digunakan'];
    }
    
    // Check duplicate Username
    $check = db_fetch_one("SELECT id_guru FROM tb_guru WHERE username = '" . db_escape($username) . "' AND id_guru != $id_guru");
    if ($check) {
        return ['success' => false, 'message' => 'Username sudah digunakan'];
    }
    
    // Handle foto upload
    $foto_profil = $foto_lama;
    if (isset($files['foto_profil']) && $files['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadFile($files['foto_profil'], UPLOAD_PATH . 'foto-guru/', ALLOWED_IMAGE_EXT);
        if ($upload['success']) {
            // Delete old photo
            if ($foto_lama !== 'default-avatar.png') {
                deleteFile(UPLOAD_PATH . 'foto-guru/' . $foto_lama);
            }
            $foto_profil = $upload['filename'];
        } else {
            return ['success' => false, 'message' => 'Upload foto gagal: ' . $upload['message']];
        }
    }
    
    // Build query
    $query = "UPDATE tb_guru SET 
              nip = '" . db_escape($nip) . "',
              nik = '" . db_escape($nik) . "',
              nama_lengkap = '" . db_escape($nama_lengkap) . "',
              username = '" . db_escape($username) . "',
              alamat = '" . db_escape($alamat) . "',
              no_telepon = '" . db_escape($no_telepon) . "',
              email = '" . db_escape($email) . "',
              foto_profil = '" . db_escape($foto_profil) . "',
              koordinat_rumah = '" . db_escape($koordinat_rumah) . "',
              gmaps_link = '" . db_escape($gmaps_link) . "',
              is_active = $is_active";
    
    // Update password if provided
    if (!empty($data['password'])) {
        $password_hash = hashPassword($data['password']);
        $query .= ", password = '" . db_escape($password_hash) . "'";
    }
    
    $query .= " WHERE id_guru = $id_guru";
    
    if (db_query($query)) {
        logActivity(getUserId(), 'Admin', "Mengedit guru: $nama_lengkap");
        return ['success' => true, 'message' => 'Data guru berhasil diupdate'];
    }
    
    return ['success' => false, 'message' => 'Gagal mengupdate data guru'];
}

/**
 * Hapus Guru
 */
function hapusGuru($data) {
    $id_guru = (int) $data['id_guru'];
    
    if (empty($id_guru)) {
        return ['success' => false, 'message' => 'ID guru tidak valid'];
    }
    
    // Check if guru is wali kelas
    $check = db_fetch_one("SELECT COUNT(*) as total FROM tb_kelas WHERE id_wali_kelas = $id_guru");
    if ($check['total'] > 0) {
        return ['success' => false, 'message' => 'Tidak dapat menghapus guru yang masih menjadi wali kelas'];
    }
    
    // Get guru data for log and photo deletion
    $guru = db_fetch_one("SELECT nama_lengkap, foto_profil FROM tb_guru WHERE id_guru = $id_guru");
    
    if (db_query("DELETE FROM tb_guru WHERE id_guru = $id_guru")) {
        // Delete photo
        if ($guru['foto_profil'] !== 'default-avatar.png') {
            deleteFile(UPLOAD_PATH . 'foto-guru/' . $guru['foto_profil']);
        }
        
        logActivity(getUserId(), 'Admin', "Menghapus guru: " . $guru['nama_lengkap']);
        return ['success' => true, 'message' => 'Guru berhasil dihapus'];
    }
    
    return ['success' => false, 'message' => 'Gagal menghapus guru'];
}

function logActivity($user_id, $role, $activity) {
    $ip = $_SERVER['REMOTE_ADDR'];
    db_query("INSERT INTO tb_activity_log (user_id, user_role, activity, ip_address) VALUES ($user_id, '$role', '" . db_escape($activity) . "', '$ip')");
}
?>