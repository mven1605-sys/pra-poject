<?php
require_once 'koneksi.php';

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

// Proses Registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = clean_input($koneksi, $_POST['username']);
    $email = clean_input($koneksi, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = clean_input($koneksi, $_POST['role']);
    
    // Validasi Input
    if (strlen($username) < 4) {
        $error = "Username minimal 4 karakter!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan Konfirmasi Password tidak sama!";
    } elseif (!in_array($role, ['admin', 'karyawan'])) {
        $error = "Role tidak valid!";
    } else {
        // Cek apakah username atau email sudah ada
        $stmt = $koneksi->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username atau Email sudah terdaftar!";
        } else {
            // Hash password menggunakan bcrypt (lebih aman)
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert user baru
            $stmt = $koneksi->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
            
            if ($stmt->execute()) {
                header("Location: login.php?registered=1");
                exit();
            } else {
                $error = "Registrasi gagal! Silakan coba lagi.";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Stockify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .register-container {
            max-width: 550px;
            margin: auto;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .btn-register:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            background: #e0e0e0;
        }
        .password-strength.weak { background: #ff4444; }
        .password-strength.medium { background: #ffbb33; }
        .password-strength.strong { background: #00C851; }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="card">
                <div class="card-header text-center">
                    <i class="bi bi-person-plus-fill fs-1"></i>
                    <h3 class="mb-0 mt-2">Registrasi Akun Baru</h3>
                    <p class="mb-0 small">Lengkapi form untuk membuat akun</p>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" id="registerForm">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-person-fill me-2"></i>Username</label>
                            <input type="text" class="form-control" name="username" required minlength="4"
                                   placeholder="Minimal 4 karakter">
                            <small class="text-muted">Username akan digunakan untuk login</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-envelope-fill me-2"></i>Email</label>
                            <input type="email" class="form-control" name="email" required
                                   placeholder="contoh@email.com">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-lock-fill me-2"></i>Password</label>
                            <input type="password" class="form-control" name="password" id="password" 
                                   required minlength="6" placeholder="Minimal 6 karakter">
                            <div class="password-strength" id="strengthBar"></div>
                            <small class="text-muted" id="strengthText"></small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-lock-fill me-2"></i>Konfirmasi Password</label>
                            <input type="password" class="form-control" name="confirm_password" required
                                   placeholder="Ulangi password">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-shield-fill me-2"></i>Role / Hak Akses</label>
                            <select class="form-select" name="role" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin">Admin (Akses Penuh)</option>
                                <option value="karyawan">Karyawan (Akses Terbatas)</option>
                            </select>
                            <small class="text-muted">
                                <strong>Admin:</strong> Dapat CRUD semua data<br>
                                <strong>Karyawan:</strong> Hanya lihat & update stok
                            </small>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agree" required>
                            <label class="form-check-label" for="agree">
                                Saya setuju dengan syarat dan ketentuan
                            </label>
                        </div>
                        
                        <button type="submit" name="register" class="btn btn-primary btn-register w-100">
                            <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-0">
                            Sudah punya akun? 
                            <a href="login.php" class="text-decoration-none fw-bold">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login Disini
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password Strength Checker
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        
        passwordInput.addEventListener('input', function() {
            const val = this.value;
            let strength = 0;
            
            if (val.length >= 6) strength++;
            if (val.length >= 10) strength++;
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) strength++;
            if (/\d/.test(val)) strength++;
            if (/[^a-zA-Z0-9]/.test(val)) strength++;
            
            strengthBar.className = 'password-strength';
            if (strength <= 2) {
                strengthBar.classList.add('weak');
                strengthText.textContent = 'Password lemah';
                strengthText.className = 'text-danger';
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
                strengthText.textContent = 'Password sedang';
                strengthText.className = 'text-warning';
            } else {
                strengthBar.classList.add('strong');
                strengthText.textContent = 'Password kuat';
                strengthText.className = 'text-success';
            }
        });
    </script>
</body>
</html>