<?php
/**
 * Email Notification Helper
 * File: helpers/email_helper.php
 * Send email notifications using PHPMailer
 */

// Menggunakan PHPMailer (install via Composer atau download manual)
// composer require phpmailer/phpmailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send Email
 * 
 * @param string $to Email penerima
 * @param string $subject Subject email
 * @param string $body Isi email (HTML)
 * @param string $alt_body Isi email (plain text)
 * @return array Response
 */
function sendEmail($to, $subject, $body, $alt_body = '') {
    // Jika email tidak tersedia di config, log saja
    if (!defined('SMTP_HOST') || empty(SMTP_HOST)) {
        error_log("Email: [To: $to] Subject: $subject");
        return [
            'success' => false,
            'message' => 'Email not configured',
            'dev_mode' => true
        ];
    }
    
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $alt_body ?: strip_tags($body);
        
        $mail->send();
        
        // Log
        logEmailNotification($to, $subject, 'sent', '');
        
        return [
            'success' => true,
            'message' => 'Email sent successfully'
        ];
        
    } catch (Exception $e) {
        // Log error
        logEmailNotification($to, $subject, 'failed', $mail->ErrorInfo);
        
        return [
            'success' => false,
            'message' => 'Email failed to send',
            'error' => $mail->ErrorInfo
        ];
    }
}

/**
 * Get Email Template
 */
function getEmailTemplate($title, $content, $footer = '') {
    $template = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #ffffff; padding: 30px; border: 1px solid #e3e6f0; }
            .footer { background: #f8f9fc; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; font-size: 12px; color: #666; }
            .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .info-box { background: #f0f7ff; border-left: 4px solid #667eea; padding: 15px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>' . $title . '</h1>
                <p>Sistem Piketin - SMK Negeri 2</p>
            </div>
            <div class="content">
                ' . $content . '
            </div>
            <div class="footer">
                ' . ($footer ?: 'Email ini dikirim otomatis oleh Sistem Piketin SMK Negeri 2') . '
                <br>© ' . date('Y') . ' SMK Negeri 2. All rights reserved.
            </div>
        </div>
    </body>
    </html>
    ';
    
    return $template;
}

/**
 * Send Welcome Email (siswa/guru baru)
 */
function sendWelcomeEmail($email, $name, $username, $role) {
    $content = '
        <h2>Selamat Datang, ' . $name . '!</h2>
        <p>Akun Anda telah berhasil dibuat di Sistem Piketin SMK Negeri 2.</p>
        
        <div class="info-box">
            <strong>Informasi Login:</strong><br>
            Role: ' . $role . '<br>
            Username: <strong>' . $username . '</strong><br>
            Password: <em>Sesuai dengan NIS/NISN/NIP Anda</em>
        </div>
        
        <p>Silakan login ke sistem untuk mulai menggunakan aplikasi:</p>
        <a href="' . BASE_URL . 'login.php" class="button">Login Sekarang</a>
        
        <p>Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi administrator.</p>
    ';
    
    $body = getEmailTemplate('Selamat Datang!', $content);
    
    return sendEmail($email, 'Selamat Datang di Sistem Piketin', $body);
}

/**
 * Send Piket Reminder Email
 */
function sendPiketReminderEmail($email, $name, $tanggal, $kelas, $anggota_list) {
    $content = '
        <h2>Pengingat Piket Hari Ini</h2>
        <p>Halo <strong>' . $name . '</strong>,</p>
        
        <p>Ini adalah pengingat bahwa Anda terjadwal piket hari ini:</p>
        
        <div class="info-box">
            <strong>Detail Jadwal:</strong><br>
            📅 Tanggal: ' . formatTanggal($tanggal, 'lengkap') . '<br>
            🏫 Kelas: ' . $kelas . '<br>
            ⏰ Batas waktu: 07:00 WIB<br><br>
            
            <strong>Anggota Piket:</strong><br>
            ' . nl2br($anggota_list) . '
        </div>
        
        <p><strong>Jangan lupa:</strong></p>
        <ul>
            <li>Upload 4 foto (1 foto anggota + 3 foto area kelas)</li>
            <li>Pastikan kelas sudah bersih dan rapi</li>
            <li>Submit sebelum jam 07:00 agar tidak terlambat</li>
        </ul>
        
        <a href="' . BASE_URL . 'pages/siswa/absensi-piket.php" class="button">Upload Absensi</a>
    ';
    
    $body = getEmailTemplate('Pengingat Piket', $content);
    
    return sendEmail($email, 'Pengingat: Anda Terjadwal Piket Hari Ini', $body);
}

/**
 * Send Alpha Notification Email
 */
function sendAlphaEmail($email, $name, $tanggal, $kelas) {
    $content = '
        <h2 style="color: #dc3545;">Peringatan: Alpha Piket</h2>
        <p>Kepada <strong>' . $name . '</strong>,</p>
        
        <p>Anda dinyatakan <strong style="color: #dc3545;">ALPHA</strong> karena tidak melakukan absensi piket pada:</p>
        
        <div class="info-box" style="background: #fff5f5; border-color: #dc3545;">
            📅 Tanggal: ' . formatTanggal($tanggal, 'lengkap') . '<br>
            🏫 Kelas: ' . $kelas . '
        </div>
        
        <p>Mohon segera menghubungi wali kelas untuk konfirmasi dan penjelasan lebih lanjut.</p>
        
        <p><strong>Catatan:</strong> Ketidakhadiran tanpa keterangan dapat mempengaruhi nilai sikap Anda.</p>
    ';
    
    $body = getEmailTemplate('Peringatan Piket', $content);
    
    return sendEmail($email, 'Peringatan: Alpha Piket - ' . formatTanggal($tanggal, 'd/m/Y'), $body);
}

/**
 * Send Izin Approved Email
 */
function sendIzinApprovedEmail($email, $name, $tanggal, $jenis_izin) {
    $content = '
        <h2 style="color: #28a745;">Perizinan Disetujui</h2>
        <p>Kepada <strong>' . $name . '</strong>,</p>
        
        <p>Perizinan Anda telah <strong style="color: #28a745;">DISETUJUI</strong> oleh wali kelas.</p>
        
        <div class="info-box" style="background: #f0fff4; border-color: #28a745;">
            📅 Tanggal: ' . formatTanggal($tanggal, 'd F Y') . '<br>
            📝 Jenis: ' . $jenis_izin . '<br>
            ✅ Status: <strong>Disetujui</strong>
        </div>
        
        <p>Semoga cepat sembuh dan dapat segera kembali beraktivitas normal.</p>
    ';
    
    $body = getEmailTemplate('Perizinan Disetujui', $content);
    
    return sendEmail($email, 'Perizinan Anda Telah Disetujui', $body);
}

/**
 * Send Weekly Report to Guru
 */
function sendWeeklyReportEmail($email, $guru_name, $kelas, $stats, $week_dates) {
    $content = '
        <h2>Laporan Mingguan Piket</h2>
        <p>Kepada <strong>' . $guru_name . '</strong>,</p>
        
        <p>Berikut adalah laporan piket kelas <strong>' . $kelas . '</strong> untuk periode:</p>
        <p><strong>' . $week_dates . '</strong></p>
        
        <div class="info-box">
            <strong>Statistik:</strong><br>
            📊 Total Jadwal: ' . $stats['total_jadwal'] . '<br>
            ✅ Sudah Absen: ' . $stats['total_absen'] . '<br>
            ⏰ Tepat Waktu: ' . $stats['tepat_waktu'] . '<br>
            ⏱️ Terlambat: ' . $stats['terlambat'] . '<br>
            ❌ Alpha: ' . $stats['alpha'] . '
        </div>
        
        <a href="' . BASE_URL . 'pages/guru/laporan-piket.php" class="button">Lihat Laporan Lengkap</a>
        
        <p>Terima kasih atas perhatian dan bimbingannya.</p>
    ';
    
    $body = getEmailTemplate('Laporan Mingguan', $content);
    
    return sendEmail($email, 'Laporan Mingguan Piket - ' . $kelas, $body);
}

/**
 * Send Password Reset Email
 */
function sendPasswordResetEmail($email, $name, $reset_link) {
    $content = '
        <h2>Reset Password</h2>
        <p>Halo <strong>' . $name . '</strong>,</p>
        
        <p>Kami menerima permintaan untuk reset password akun Anda.</p>
        
        <p>Klik tombol di bawah ini untuk mengatur password baru:</p>
        
        <a href="' . $reset_link . '" class="button">Reset Password</a>
        
        <div class="info-box">
            <strong>⚠️ Catatan Keamanan:</strong><br>
            - Link ini hanya berlaku selama 1 jam<br>
            - Jika Anda tidak meminta reset password, abaikan email ini<br>
            - Jangan berikan link ini kepada siapapun
        </div>
    ';
    
    $body = getEmailTemplate('Reset Password', $content);
    
    return sendEmail($email, 'Reset Password - Sistem Piketin', $body);
}

/**
 * Log Email Notification
 */
function logEmailNotification($to, $subject, $status, $error) {
    $query = "INSERT INTO tb_email_log (email_to, subject, status, error_message, created_at) 
              VALUES (
                  '" . db_escape($to) . "',
                  '" . db_escape($subject) . "',
                  '" . db_escape($status) . "',
                  '" . db_escape($error) . "',
                  NOW()
              )";
    
    db_query($query);
}

// Add email configuration constants ke config.php
/*
// Email Configuration (SMTP)
define('SMTP_HOST', 'smtp.gmail.com');          // SMTP server
define('SMTP_PORT', 587);                        // SMTP port (587 atau 465)
define('SMTP_USER', 'your-email@gmail.com');    // Email Anda
define('SMTP_PASS', 'your-app-password');       // Password atau App Password
define('SMTP_FROM_EMAIL', 'noreply@smkn2.sch.id');
define('SMTP_FROM_NAME', 'Sistem Piketin SMK N 2');
*/
?>