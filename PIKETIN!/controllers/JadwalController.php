<?php
/**
 * Jadwal Controller
 * File: controllers/JadwalController.php
 * Auto-generate jadwal piket untuk sebulan
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
    case 'generate':
        $response = generateJadwal($_POST);
        break;
    case 'hapus':
        $response = hapusJadwal($_POST);
        break;
}

echo json_encode($response);

/**
 * Generate Jadwal Piket Otomatis
 */
function generateJadwal($data) {
    global $db;
    
    $id_kelas = (int) $data['id_kelas'];
    $bulan = (int) $data['bulan'];
    $tahun = (int) $data['tahun'];
    $siswa_per_hari = (int) $data['siswa_per_hari'];
    
    if (empty($id_kelas) || empty($bulan) || empty($tahun)) {
        return ['success' => false, 'message' => 'Data tidak lengkap'];
    }
    
    // Get siswa list dari kelas
    $siswa_list = db_fetch_all("
        SELECT id_siswa, nama_lengkap 
        FROM tb_siswa 
        WHERE id_kelas = $id_kelas AND is_active = 1
        ORDER BY nama_lengkap
    ");
    
    if (count($siswa_list) < $siswa_per_hari) {
        return ['success' => false, 'message' => "Jumlah siswa di kelas ini (" . count($siswa_list) . ") kurang dari siswa per hari ($siswa_per_hari)"];
    }
    
    // Hapus jadwal lama jika ada
    db_query("DELETE jp, ap FROM tb_jadwal_piket jp 
              LEFT JOIN tb_anggota_piket ap ON jp.id_jadwal = ap.id_jadwal
              WHERE jp.id_kelas = $id_kelas 
              AND MONTH(jp.tanggal) = $bulan 
              AND YEAR(jp.tanggal) = $tahun");
    
    // Get jumlah hari dalam bulan
    $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
    
    // Nama hari
    $nama_hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
                  'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    
    $total_jadwal = 0;
    $siswa_index = 0;
    $total_siswa = count($siswa_list);
    
    // Loop untuk setiap hari dalam bulan
    for ($hari = 1; $hari <= $jumlah_hari; $hari++) {
        $tanggal = "$tahun-$bulan-$hari";
        $timestamp = strtotime($tanggal);
        $hari_nama = $nama_hari[date('l', $timestamp)];
        
        // Skip Sabtu dan Minggu
        if ($hari_nama == 'Sabtu' || $hari_nama == 'Minggu') {
            continue;
        }
        
        // Hitung minggu ke berapa
        $minggu_ke = ceil($hari / 7);
        
        // Insert jadwal piket
        $query = "INSERT INTO tb_jadwal_piket (id_kelas, hari, minggu_ke, bulan, tahun, tanggal) 
                  VALUES ($id_kelas, '$hari_nama', $minggu_ke, $bulan, $tahun, '$tanggal')";
        
        if (db_query($query)) {
            $id_jadwal = getDB()->lastInsertId();
            
            // Assign siswa ke jadwal (rotating)
            for ($i = 0; $i < $siswa_per_hari; $i++) {
                $siswa = $siswa_list[$siswa_index % $total_siswa];
                
                db_query("INSERT INTO tb_anggota_piket (id_jadwal, id_siswa) 
                          VALUES ($id_jadwal, {$siswa['id_siswa']})");
                
                $siswa_index++;
            }
            
            $total_jadwal++;
        }
    }
    
    if ($total_jadwal > 0) {
        logActivity(getUserId(), 'Admin', "Generate jadwal piket: $total_jadwal jadwal untuk bulan $bulan/$tahun");
        return [
            'success' => true, 
            'message' => "Berhasil generate $total_jadwal jadwal piket untuk bulan $bulan/$tahun"
        ];
    }
    
    return ['success' => false, 'message' => 'Gagal generate jadwal'];
}

/**
 * Hapus Jadwal
 */
function hapusJadwal($data) {
    $id_jadwal = (int) $data['id_jadwal'];
    
    if (empty($id_jadwal)) {
        return ['success' => false, 'message' => 'ID jadwal tidak valid'];
    }
    
    // Check if sudah ada absensi
    $check = db_fetch_one("SELECT id_absensi FROM tb_absensi_piket WHERE id_jadwal = $id_jadwal");
    if ($check) {
        return ['success' => false, 'message' => 'Tidak dapat menghapus jadwal yang sudah ada absensinya'];
    }
    
    // Get info for log
    $jadwal = db_fetch_one("
        SELECT jp.*, k.nama_kelas 
        FROM tb_jadwal_piket jp 
        INNER JOIN tb_kelas k ON jp.id_kelas = k.id_kelas 
        WHERE jp.id_jadwal = $id_jadwal
    ");
    
    // Delete anggota piket dulu
    db_query("DELETE FROM tb_anggota_piket WHERE id_jadwal = $id_jadwal");
    
    // Delete jadwal
    if (db_query("DELETE FROM tb_jadwal_piket WHERE id_jadwal = $id_jadwal")) {
        logActivity(getUserId(), 'Admin', "Menghapus jadwal: " . $jadwal['nama_kelas'] . " - " . $jadwal['tanggal']);
        return ['success' => true, 'message' => 'Jadwal berhasil dihapus'];
    }
    
    return ['success' => false, 'message' => 'Gagal menghapus jadwal'];
}

function logActivity($user_id, $role, $activity) {
    $ip = $_SERVER['REMOTE_ADDR'];
    db_query("INSERT INTO tb_activity_log (user_id, user_role, activity, ip_address) VALUES ($user_id, '$role', '" . db_escape($activity) . "', '$ip')");
}
?>