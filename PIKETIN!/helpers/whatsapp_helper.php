<?php
/**
 * WhatsApp Notification Helper
 * File: helpers/whatsapp_helper.php
 * Send notifications via WhatsApp API
 */

/**
 * Send WhatsApp Message
 * 
 * Menggunakan API Fonnte (gratis untuk testing)
 * Daftar di https://fonnte.com untuk mendapatkan API key
 * 
 * @param string $phone Nomor WhatsApp (format: 628xxx)
 * @param string $message Pesan yang akan dikirim
 * @return array Response dari API
 */
function sendWhatsAppMessage($phone, $message) {
    // Get API key from settings
    $api_key = WA_API_KEY;
    
    // Jika API key kosong, log saja (untuk development)
    if (empty($api_key)) {
        error_log("WhatsApp: [To: $phone] $message");
        return [
            'success' => false,
            'message' => 'WhatsApp API key not configured',
            'dev_mode' => true
        ];
    }
    
    // Format nomor telepon (hapus 0 di depan, tambah 62)
    $phone = formatPhoneNumber($phone);
    
    // API URL (Fonnte)
    $url = 'https://api.fonnte.com/send';
    
    // Data yang akan dikirim
    $data = [
        'target' => $phone,
        'message' => $message,
        'countryCode' => '62'
    ];
    
    // cURL request
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => [
            'Authorization: ' . $api_key,
            'Content-Type: application/x-www-form-urlencoded'
        ],
    ]);
    
    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    curl_close($curl);
    
    // Parse response
    $result = json_decode($response, true);
    
    // Log ke database
    logWhatsAppNotification($phone, $message, $http_code, $response);
    
    if ($http_code == 200 && isset($result['status']) && $result['status']) {
        return [
            'success' => true,
            'message' => 'WhatsApp sent successfully',
            'response' => $result
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to send WhatsApp',
            'error' => $error ?: $response,
            'http_code' => $http_code
        ];
    }
}

/**
 * Format Phone Number
 * Convert 08xxx ke 628xxx
 */
function formatPhoneNumber($phone) {
    // Hapus karakter non-numeric
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Jika dimulai dengan 0, ganti dengan 62
    if (substr($phone, 0, 1) === '0') {
        $phone = '62' . substr($phone, 1);
    }
    
    // Jika tidak dimulai dengan 62, tambahkan
    if (substr($phone, 0, 2) !== '62') {
        $phone = '62' . $phone;
    }
    
    return $phone;
}

/**
 * Send Alpha Notification
 * Kirim notifikasi untuk siswa yang alpha
 */
function sendAlphaNotification($id_siswa, $tanggal, $nama_kelas) {
    // Get siswa data
    $siswa = db_fetch_one("
        SELECT nama_lengkap, no_telepon 
        FROM tb_siswa 
        WHERE id_siswa = $id_siswa
    ");
    
    if (!$siswa || empty($siswa['no_telepon'])) {
        return ['success' => false, 'message' => 'No phone number'];
    }
    
    // Format pesan
    $message = "*PERINGATAN PIKET*\n\n";
    $message .= "Kepada: *{$siswa['nama_lengkap']}*\n";
    $message .= "Kelas: *{$nama_kelas}*\n\n";
    $message .= "Anda dinyatakan *ALPHA* karena tidak melakukan absensi piket pada:\n";
    $message .= "📅 Tanggal: " . formatTanggal($tanggal, 'lengkap') . "\n\n";
    $message .= "Harap segera hubungi wali kelas untuk konfirmasi.\n\n";
    $message .= "_Sistem Piketin - SMK Negeri 2_";
    
    // Send WhatsApp
    return sendWhatsAppMessage($siswa['no_telepon'], $message);
}

/**
 * Send Reminder Notification
 * Kirim pengingat untuk siswa yang terjadwal piket hari ini
 */
function sendPiketReminder($id_siswa, $anggota_list, $tanggal) {
    // Get siswa data
    $siswa = db_fetch_one("
        SELECT s.nama_lengkap, s.no_telepon, k.nama_kelas
        FROM tb_siswa s
        INNER JOIN tb_kelas k ON s.id_kelas = k.id_kelas
        WHERE s.id_siswa = $id_siswa
    ");
    
    if (!$siswa || empty($siswa['no_telepon'])) {
        return ['success' => false, 'message' => 'No phone number'];
    }
    
    // Format pesan
    $message = "*PENGINGAT PIKET*\n\n";
    $message .= "Halo *{$siswa['nama_lengkap']}*,\n\n";
    $message .= "Anda terjadwal piket hari ini:\n";
    $message .= "📅 " . formatTanggal($tanggal, 'lengkap') . "\n";
    $message .= "🏫 Kelas: *{$siswa['nama_kelas']}*\n\n";
    $message .= "👥 Anggota Piket:\n{$anggota_list}\n\n";
    $message .= "⏰ Batas waktu absensi: 07:00 WIB\n";
    $message .= "📸 Jangan lupa upload 4 foto!\n\n";
    $message .= "_Sistem Piketin - SMK Negeri 2_";
    
    // Send WhatsApp
    return sendWhatsAppMessage($siswa['no_telepon'], $message);
}

/**
 * Send Perizinan Approved
 */
function sendIzinApproved($id_siswa, $tanggal, $jenis_izin) {
    $siswa = db_fetch_one("SELECT nama_lengkap, no_telepon FROM tb_siswa WHERE id_siswa = $id_siswa");
    
    if (!$siswa || empty($siswa['no_telepon'])) {
        return ['success' => false, 'message' => 'No phone number'];
    }
    
    $message = "*PERIZINAN DISETUJUI*\n\n";
    $message .= "Kepada: *{$siswa['nama_lengkap']}*\n\n";
    $message .= "Perizinan Anda untuk tidak mengikuti piket telah *DISETUJUI*:\n";
    $message .= "📅 Tanggal: " . formatTanggal($tanggal, 'd F Y') . "\n";
    $message .= "📝 Jenis: *{$jenis_izin}*\n\n";
    $message .= "Semoga cepat sembuh dan segera bisa kembali beraktivitas.\n\n";
    $message .= "_Sistem Piketin - SMK Negeri 2_";
    
    return sendWhatsAppMessage($siswa['no_telepon'], $message);
}

/**
 * Log WhatsApp Notification
 * Simpan log notifikasi ke database
 */
function logWhatsAppNotification($phone, $message, $status_code, $response) {
    $query = "INSERT INTO tb_notifikasi_log (phone, message, status_code, response, created_at) 
              VALUES (
                  '" . db_escape($phone) . "',
                  '" . db_escape($message) . "',
                  " . (int)$status_code . ",
                  '" . db_escape($response) . "',
                  NOW()
              )";
    
    db_query($query);
}

/**
 * Check Daily Piket and Send Reminders
 * Fungsi ini bisa dijadwalkan via CRON job
 * Jalankan setiap pagi jam 06:00
 */
function checkAndSendDailyReminders() {
    $today = getCurrentDate();
    
    // Get semua jadwal piket hari ini
    $jadwal_list = db_fetch_all("
        SELECT jp.id_jadwal, jp.id_kelas, k.nama_kelas,
               GROUP_CONCAT(s.nama_lengkap SEPARATOR '\n- ') as anggota_list
        FROM tb_jadwal_piket jp
        INNER JOIN tb_kelas k ON jp.id_kelas = k.id_kelas
        INNER JOIN tb_anggota_piket ap ON jp.id_jadwal = ap.id_jadwal
        INNER JOIN tb_siswa s ON ap.id_siswa = s.id_siswa
        WHERE jp.tanggal = '$today'
        GROUP BY jp.id_jadwal
    ");
    
    $sent_count = 0;
    foreach ($jadwal_list as $jadwal) {
        // Get semua anggota di jadwal ini
        $anggota = db_fetch_all("
            SELECT ap.id_siswa
            FROM tb_anggota_piket ap
            WHERE ap.id_jadwal = {$jadwal['id_jadwal']}
        ");
        
        $anggota_list = "- " . $jadwal['anggota_list'];
        
        foreach ($anggota as $siswa) {
            $result = sendPiketReminder($siswa['id_siswa'], $anggota_list, $today);
            if ($result['success']) {
                $sent_count++;
            }
        }
    }
    
    return [
        'success' => true,
        'message' => "Sent $sent_count reminders"
    ];
}

/**
 * Check Alpha Students and Send Notifications
 * Jalankan setiap sore jam 16:00 untuk cek yang belum absen
 */
function checkAndSendAlphaNotifications() {
    $today = getCurrentDate();
    
    // Get jadwal yang belum di-absen
    $jadwal_list = db_fetch_all("
        SELECT jp.id_jadwal, jp.id_kelas, k.nama_kelas
        FROM tb_jadwal_piket jp
        INNER JOIN tb_kelas k ON jp.id_kelas = k.id_kelas
        LEFT JOIN tb_absensi_piket ab ON jp.id_jadwal = ab.id_jadwal
        WHERE jp.tanggal = '$today'
        AND ab.id_absensi IS NULL
    ");
    
    $sent_count = 0;
    foreach ($jadwal_list as $jadwal) {
        // Get anggota yang tidak izin/sakit
        $anggota_alpha = db_fetch_all("
            SELECT ap.id_siswa
            FROM tb_anggota_piket ap
            LEFT JOIN tb_perizinan pz ON ap.id_siswa = pz.id_siswa AND ap.id_jadwal = pz.id_jadwal
            WHERE ap.id_jadwal = {$jadwal['id_jadwal']}
            AND (pz.id_izin IS NULL OR pz.status_approval = 'Rejected')
            AND ap.status_kehadiran = 'Alpha'
        ");
        
        foreach ($anggota_alpha as $siswa) {
            $result = sendAlphaNotification($siswa['id_siswa'], $today, $jadwal['nama_kelas']);
            if ($result['success']) {
                $sent_count++;
            }
        }
    }
    
    return [
        'success' => true,
        'message' => "Sent $sent_count alpha notifications"
    ];
}
?>