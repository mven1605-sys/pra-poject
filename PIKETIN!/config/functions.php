<?php
/**
 * Helper Functions
 * File: config/functions.php
 * Fungsi-fungsi helper yang sering digunakan
 */

// ===================================
// AUTHENTICATION FUNCTIONS
// ===================================

/**
 * Check if user is logged in
 * @return boolean
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

/**
 * Check if user has specific role
 * @param string $role
 * @return boolean
 */
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Get current user ID
 * @return int or null
 */
function getUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get current user role
 * @return string or null
 */
function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

/**
 * Get current user name
 * @return string or null
 */
function getUserName() {
    return isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : null;
}

/**
 * Redirect jika tidak login
 * @param string $redirect_to
 */
function requireLogin($redirect_to = 'login.php') {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . $redirect_to);
        exit();
    }
}

/**
 * Redirect jika tidak punya role tertentu
 * @param string $required_role
 * @param string $redirect_to
 */
function requireRole($required_role, $redirect_to = 'index.php') {
    if (!hasRole($required_role)) {
        header('Location: ' . BASE_URL . $redirect_to);
        exit();
    }
}

/**
 * Hash password
 * @param string $password
 * @return string
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify password
 * @param string $password
 * @param string $hash
 * @return boolean
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// ===================================
// SANITIZE & VALIDATION FUNCTIONS
// ===================================

/**
 * Sanitize input string
 * @param string $data
 * @return string
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email
 * @param string $email
 * @return boolean
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone number (Indonesia)
 * @param string $phone
 * @return boolean
 */
function isValidPhone($phone) {
    return preg_match('/^(\+62|62|0)[0-9]{9,12}$/', $phone);
}

// ===================================
// DATE & TIME FUNCTIONS
// ===================================

/**
 * Format tanggal Indonesia
 * @param string $date
 * @param string $format
 * @return string
 */
function formatTanggal($date, $format = 'd F Y') {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $hari = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    
    $timestamp = strtotime($date);
    $tanggal = date('d', $timestamp);
    $bulan_num = date('n', $timestamp);
    $tahun = date('Y', $timestamp);
    $nama_hari = $hari[date('l', $timestamp)];
    
    if ($format === 'lengkap') {
        return $nama_hari . ', ' . $tanggal . ' ' . $bulan[$bulan_num] . ' ' . $tahun;
    } else if ($format === 'd F Y') {
        return $tanggal . ' ' . $bulan[$bulan_num] . ' ' . $tahun;
    } else {
        return date($format, $timestamp);
    }
}

/**
 * Get current date
 * @return string
 */
function getCurrentDate() {
    return date('Y-m-d');
}

/**
 * Get current time
 * @return string
 */
function getCurrentTime() {
    return date('H:i:s');
}

/**
 * Get current datetime
 * @return string
 */
function getCurrentDateTime() {
    return date('Y-m-d H:i:s');
}

/**
 * Get nama hari dari tanggal
 * @param string $date
 * @return string
 */
function getNamaHari($date) {
    $hari = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    
    return $hari[date('l', strtotime($date))];
}

/**
 * Get minggu ke berapa dalam bulan
 * @param string $date
 * @return int
 */
function getMingguKe($date) {
    $day = date('j', strtotime($date));
    return ceil($day / 7);
}

/**
 * Check if time is late
 * @param string $time Current time
 * @param string $deadline Deadline time
 * @return boolean
 */
function isLate($time, $deadline) {
    return strtotime($time) > strtotime($deadline);
}

// ===================================
// FILE UPLOAD FUNCTIONS
// ===================================

/**
 * Upload file
 * @param array $file $_FILES array
 * @param string $destination Upload destination folder
 * @param array $allowed_ext Allowed extensions
 * @return array [success => boolean, message => string, filename => string]
 */
function uploadFile($file, $destination, $allowed_ext = ['jpg', 'jpeg', 'png']) {
    $result = [
        'success' => false,
        'message' => '',
        'filename' => ''
    ];
    
    // Check if file exists
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        $result['message'] = 'Tidak ada file yang diupload';
        return $result;
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = 'Error saat upload file';
        return $result;
    }
    
    // Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $result['message'] = 'Ukuran file terlalu besar (Max: ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB)';
        return $result;
    }
    
    // Get file extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check extension
    if (!in_array($ext, $allowed_ext)) {
        $result['message'] = 'Ekstensi file tidak diperbolehkan';
        return $result;
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $filepath = $destination . $filename;
    
    // Create directory if not exists
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $result['success'] = true;
        $result['message'] = 'File berhasil diupload';
        $result['filename'] = $filename;
    } else {
        $result['message'] = 'Gagal memindahkan file';
    }
    
    return $result;
}

/**
 * Delete file
 * @param string $filepath
 * @return boolean
 */
function deleteFile($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

// ===================================
// ALERT & MESSAGE FUNCTIONS
// ===================================

/**
 * Set flash message
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get flash message
 * @return array or null
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message
 * @return string HTML
 */
function displayFlash() {
    $flash = getFlash();
    
    if ($flash) {
        $alertClass = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];
        
        $class = isset($alertClass[$flash['type']]) ? $alertClass[$flash['type']] : 'alert-info';
        
        return '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">
                    ' . $flash['message'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    }
    
    return '';
}

// ===================================
// UTILITY FUNCTIONS
// ===================================

/**
 * Redirect to URL
 * @param string $url
 */
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

/**
 * Generate random string
 * @param int $length
 * @return string
 */
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)))), 1, $length);
}

/**
 * Debug print (hanya untuk development)
 * @param mixed $data
 */
function dd($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    die();
}

/**
 * Format rupiah
 * @param int $number
 * @return string
 */
function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

/**
 * Pagination helper
 * @param int $total Total data
 * @param int $current_page Current page
 * @param int $per_page Items per page
 * @return array
 */
function paginate($total, $current_page = 1, $per_page = ITEMS_PER_PAGE) {
    $total_pages = ceil($total / $per_page);
    $offset = ($current_page - 1) * $per_page;
    
    return [
        'total' => $total,
        'per_page' => $per_page,
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'offset' => $offset,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages
    ];
}

?>