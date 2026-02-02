<?php
/**
 * Jurusan Controller
 * File: controllers/JurusanController.php
 * Handle CRUD operations untuk Jurusan
 */

session_start();
require_once '../config/config.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !hasRole('Admin')) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

header('Content-Type: application/json');

$db = getDB();
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'tambah':
            $response = tambahJurusan($_POST);
            break;
            
        case 'edit':
            $response = editJurusan($_POST);
            break;
            
        case 'hapus':
            $response = hapusJurusan($_POST);
            break;
            
        default:
            $response = [
                'success' => false,
                'message' => 'Invalid action'
            ];
    }
}

echo json_encode($response);

/**
 * Tambah Jurusan Baru
 */
function tambahJurusan($data) {
    global $db;
    
    // Validasi input
    $kode_jurusan = sanitize(strtoupper($data['kode_jurusan']));
    $nama_jurusan = sanitize($data['nama_jurusan']);
    
    if (empty($kode_jurusan) || empty($nama_jurusan)) {
        return [
            'success' => false,
            'message' => 'Kode dan Nama Jurusan harus diisi'
        ];
    }
    
    // Check if kode already exists
    $check = db_fetch_one("SELECT id_jurusan FROM tb_jurusan WHERE kode_jurusan = '" . db_escape($kode_jurusan) . "'");
    
    if ($check) {
        return [
            'success' => false,
            'message' => 'Kode jurusan sudah digunakan'
        ];
    }
    
    // Insert data
    $query = "INSERT INTO tb_jurusan (kode_jurusan, nama_jurusan) 
              VALUES ('" . db_escape($kode_jurusan) . "', '" . db_escape($nama_jurusan) . "')";
    
    if (db_query($query)) {
        // Log activity
        logActivity(getUserId(), 'Admin', "Menambah jurusan baru: $nama_jurusan");
        
        return [
            'success' => true,
            'message' => 'Jurusan berhasil ditambahkan'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Gagal menambahkan jurusan'
    ];
}

/**
 * Edit Jurusan
 */
function editJurusan($data) {
    global $db;
    
    // Validasi input
    $id_jurusan = (int) $data['id_jurusan'];
    $kode_jurusan = sanitize(strtoupper($data['kode_jurusan']));
    $nama_jurusan = sanitize($data['nama_jurusan']);
    
    if (empty($id_jurusan) || empty($kode_jurusan) || empty($nama_jurusan)) {
        return [
            'success' => false,
            'message' => 'Data tidak lengkap'
        ];
    }
    
    // Check if new kode already exists (exclude current id)
    $check = db_fetch_one("
        SELECT id_jurusan 
        FROM tb_jurusan 
        WHERE kode_jurusan = '" . db_escape($kode_jurusan) . "' 
        AND id_jurusan != $id_jurusan
    ");
    
    if ($check) {
        return [
            'success' => false,
            'message' => 'Kode jurusan sudah digunakan'
        ];
    }
    
    // Update data
    $query = "UPDATE tb_jurusan 
              SET kode_jurusan = '" . db_escape($kode_jurusan) . "',
                  nama_jurusan = '" . db_escape($nama_jurusan) . "'
              WHERE id_jurusan = $id_jurusan";
    
    if (db_query($query)) {
        // Log activity
        logActivity(getUserId(), 'Admin', "Mengedit jurusan: $nama_jurusan");
        
        return [
            'success' => true,
            'message' => 'Jurusan berhasil diupdate'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Gagal mengupdate jurusan'
    ];
}

/**
 * Hapus Jurusan
 */
function hapusJurusan($data) {
    global $db;
    
    $id_jurusan = (int) $data['id_jurusan'];
    
    if (empty($id_jurusan)) {
        return [
            'success' => false,
            'message' => 'ID jurusan tidak valid'
        ];
    }
    
    // Check if jurusan has classes
    $check = db_fetch_one("
        SELECT COUNT(*) as total 
        FROM tb_kelas 
        WHERE id_jurusan = $id_jurusan
    ");
    
    if ($check['total'] > 0) {
        return [
            'success' => false,
            'message' => 'Tidak dapat menghapus jurusan yang masih memiliki kelas'
        ];
    }
    
    // Get jurusan name for log
    $jurusan = db_fetch_one("SELECT nama_jurusan FROM tb_jurusan WHERE id_jurusan = $id_jurusan");
    
    // Delete data
    $query = "DELETE FROM tb_jurusan WHERE id_jurusan = $id_jurusan";
    
    if (db_query($query)) {
        // Log activity
        logActivity(getUserId(), 'Admin', "Menghapus jurusan: " . $jurusan['nama_jurusan']);
        
        return [
            'success' => true,
            'message' => 'Jurusan berhasil dihapus'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Gagal menghapus jurusan'
    ];
}

/**
 * Log Activity Helper
 */
function logActivity($user_id, $role, $activity) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $query = "INSERT INTO tb_activity_log (user_id, user_role, activity, ip_address) 
              VALUES ($user_id, '$role', '" . db_escape($activity) . "', '$ip_address')";
    db_query($query);
}
?>