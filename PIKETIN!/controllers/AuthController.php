<?php
/**
 * Authentication Controller
 * File: controllers/AuthController.php
 * Menangani proses login, logout, dan autentikasi
 */

// Start session
session_start();

// Include config
require_once __DIR__ . '/../config/config.php';

class AuthController {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Login Process
     * @param array $data POST data from form
     * @return array response
     */
    public function login($data) {
        $role = isset($data['role']) ? sanitize($data['role']) : '';
        
        switch ($role) {
            case 'admin':
                return $this->loginAdmin($data);
                break;
            case 'guru':
                return $this->loginGuru($data);
                break;
            case 'siswa':
                return $this->loginSiswa($data);
                break;
            default:
                return [
                    'success' => false,
                    'message' => 'Role tidak valid'
                ];
        }
    }
    
    /**
     * Login Admin
     */
    private function loginAdmin($data) {
        $username = sanitize($data['username']);
        $password = $data['password'];
        
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Username dan password harus diisi'
            ];
        }
        
        // Query admin
        $query = "SELECT * FROM tb_admin WHERE username = '" . db_escape($username) . "' AND is_active = 1 LIMIT 1";
        $admin = db_fetch_one($query);
        
        if ($admin) {
            // Verify password
            if (verifyPassword($password, $admin['password'])) {
                // Set session
                $_SESSION['user_id'] = $admin['id_admin'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['nama_lengkap'] = $admin['nama_lengkap'];
                $_SESSION['role'] = 'Admin';
                $_SESSION['foto_profil'] = $admin['foto_profil'];
                $_SESSION['login_time'] = time();
                
                // Log activity
                $this->logActivity($admin['id_admin'], 'Admin', 'Login ke sistem');
                
                return [
                    'success' => true,
                    'message' => 'Login berhasil',
                    'redirect' => BASE_URL . 'simple-dashboard.php'
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Username atau password salah'
        ];
    }
    
    /**
     * Login Guru
     */
    private function loginGuru($data) {
        $username = sanitize($data['username']);
        $nip = sanitize($data['nip']);
        
        if (empty($username) || empty($nip)) {
            return [
                'success' => false,
                'message' => 'Username dan NIP harus diisi'
            ];
        }
        
        // Query guru dengan join ke kelas
        $query = "SELECT g.*, k.nama_kelas, k.id_kelas, j.nama_jurusan 
                  FROM tb_guru g
                  LEFT JOIN tb_kelas k ON k.id_wali_kelas = g.id_guru
                  LEFT JOIN tb_jurusan j ON k.id_jurusan = j.id_jurusan
                  WHERE g.username = '" . db_escape($username) . "' 
                  AND g.nip = '" . db_escape($nip) . "'
                  AND g.is_active = 1 
                  LIMIT 1";
        
        $guru = db_fetch_one($query);
        
        if ($guru) {
            // Set session
            $_SESSION['user_id'] = $guru['id_guru'];
            $_SESSION['username'] = $guru['username'];
            $_SESSION['nama_lengkap'] = $guru['nama_lengkap'];
            $_SESSION['nip'] = $guru['nip'];
            $_SESSION['role'] = 'Guru';
            $_SESSION['foto_profil'] = $guru['foto_profil'];
            $_SESSION['id_kelas'] = $guru['id_kelas'];
            $_SESSION['nama_kelas'] = $guru['nama_kelas'];
            $_SESSION['nama_jurusan'] = $guru['nama_jurusan'];
            $_SESSION['login_time'] = time();
            
            // Log activity
            $this->logActivity($guru['id_guru'], 'Guru', 'Login ke sistem');
            
            return [
                'success' => true,
                'message' => 'Login berhasil',
                'redirect' => BASE_URL . 'pages/guru/dashboard.php'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Username atau NIP salah'
        ];
    }
    
    /**
     * Login Siswa
     */
    private function loginSiswa($data) {
        $username = sanitize($data['username']);
        $nis = sanitize($data['nis']);
        $kelas = isset($data['kelas']) ? sanitize($data['kelas']) : '';
        
        if (empty($username) || empty($nis)) {
            return [
                'success' => false,
                'message' => 'Username dan NIS/NISN harus diisi'
            ];
        }
        
        // Query siswa dengan join ke kelas dan jurusan
        $query = "SELECT s.*, k.nama_kelas, k.tingkat, j.nama_jurusan, j.kode_jurusan
                  FROM tb_siswa s
                  INNER JOIN tb_kelas k ON s.id_kelas = k.id_kelas
                  INNER JOIN tb_jurusan j ON k.id_jurusan = j.id_jurusan
                  WHERE s.username = '" . db_escape($username) . "' 
                  AND (s.nis = '" . db_escape($nis) . "' OR s.nisn = '" . db_escape($nis) . "')
                  AND s.is_active = 1
                  LIMIT 1";
        
        $siswa = db_fetch_one($query);
        
        if ($siswa) {
            // Jika kelas dipilih, validasi kelas
            if (!empty($kelas)) {
                $kelasFormat = $siswa['nama_kelas'];
                if (strtoupper(str_replace(' ', '-', $kelasFormat)) !== strtoupper($kelas)) {
                    return [
                        'success' => false,
                        'message' => 'Kelas tidak sesuai dengan data siswa'
                    ];
                }
            }
            
            // Set session
            $_SESSION['user_id'] = $siswa['id_siswa'];
            $_SESSION['username'] = $siswa['username'];
            $_SESSION['nama_lengkap'] = $siswa['nama_lengkap'];
            $_SESSION['nis'] = $siswa['nis'];
            $_SESSION['nisn'] = $siswa['nisn'];
            $_SESSION['role'] = 'Siswa';
            $_SESSION['foto_profil'] = $siswa['foto_profil'];
            $_SESSION['id_kelas'] = $siswa['id_kelas'];
            $_SESSION['nama_kelas'] = $siswa['nama_kelas'];
            $_SESSION['tingkat'] = $siswa['tingkat'];
            $_SESSION['nama_jurusan'] = $siswa['nama_jurusan'];
            $_SESSION['kode_jurusan'] = $siswa['kode_jurusan'];
            $_SESSION['login_time'] = time();
            
            // Log activity
            $this->logActivity($siswa['id_siswa'], 'Siswa', 'Login ke sistem');
            
            return [
                'success' => true,
                'message' => 'Login berhasil',
                'redirect' => BASE_URL . 'pages/siswa/dashboard.php'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Username atau NIS/NISN salah'
        ];
    }
    
    /**
     * Logout Process
     */
    public function logout() {
        // Log activity sebelum destroy session
        if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
            $this->logActivity($_SESSION['user_id'], $_SESSION['role'], 'Logout dari sistem');
        }
        
        // Destroy session
        session_unset();
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Logout berhasil',
            'redirect' => BASE_URL . 'login.php'
        ];
    }
    
    /**
     * Log Activity
     */
    private function logActivity($user_id, $role, $activity) {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $description = $activity;
        
        $query = "INSERT INTO tb_activity_log (user_id, user_role, activity, description, ip_address) 
                  VALUES ($user_id, '$role', '$activity', '$description', '$ip_address')";
        
        db_query($query);
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }
}

// ===================================
// PROCESS REQUEST
// ===================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController();
    
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'login') {
        $result = $auth->login($_POST);
        
        if ($result['success']) {
            // Redirect ke dashboard
            header('Location: ' . $result['redirect']);
            exit();
        } else {
            // Redirect back dengan error message
            $_SESSION['login_error'] = $result['message'];
            header('Location: ' . BASE_URL . 'login.php');
            exit();
        }
    }
}

// Handle GET request untuk logout
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $auth = new AuthController();
    
    if ($_GET['action'] === 'logout') {
        $result = $auth->logout();
        header('Location: ' . $result['redirect']);
        exit();
    }
}

?>