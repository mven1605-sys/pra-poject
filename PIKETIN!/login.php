<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PIKETIN SMK Negeri 2</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --info-color: #36b9cc;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .login-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .login-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .login-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .role-switch {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        .role-btn {
            flex: 1;
            padding: 12px;
            border: 2px solid #e3e6f0;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        
        .role-btn:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        
        .role-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
        }
        
        .role-btn i {
            font-size: 24px;
            display: block;
            margin-bottom: 8px;
        }
        
        .role-btn span {
            font-size: 13px;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            color: #5a5c69;
            font-size: 14px;
        }
        
        .form-control {
            border: 2px solid #e3e6f0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #858796;
        }
        
        .input-group .form-control {
            padding-right: 45px;
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .forgot-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .forgot-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        
        .forgot-link a:hover {
            text-decoration: underline;
        }
        
        .back-home {
            text-align: center;
            margin-top: 15px;
        }
        
        .back-home a {
            color: #858796;
            text-decoration: none;
            font-size: 13px;
        }
        
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        
        .loading-overlay.active {
            display: flex;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .alert {
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <img src="asset/img/logo/logo-smkn2.png" alt="Logo" onerror="this.src='https://via.placeholder.com/80'">
            <h2>PIKETIN</h2>
            <p>Sistem Absensi & Monitoring Piket<br>SMK Negeri 2</p>
        </div>
        
        <!-- Body -->
        <div class="login-body">
            <!-- Alert -->
            <div id="alertContainer"></div>
            
            <!-- Role Switch -->
            <div class="role-switch">
                <button type="button" class="role-btn active" data-role="admin" onclick="switchRole('admin')">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin</span>
                </button>
                <button type="button" class="role-btn" data-role="guru" onclick="switchRole('guru')">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Guru</span>
                </button>
                <button type="button" class="role-btn" data-role="siswa" onclick="switchRole('siswa')">
                    <i class="fas fa-user-graduate"></i>
                    <span>Siswa</span>
                </button>
            </div>
            
            <!-- Login Form -->
            <form id="loginForm" method="POST" action="controllers/AuthController.php">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="role" id="roleInput" value="admin">
                
                <!-- Admin Form -->
                <div id="adminForm" class="role-form">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Username</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="username" placeholder="Masukkan username" required>
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="adminPassword" placeholder="Masukkan password" required>
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('adminPassword')"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Guru Form -->
                <div id="guruForm" class="role-form" style="display: none;">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Username</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="username" placeholder="Masukkan username">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> NIP</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="nip" placeholder="Masukkan NIP">
                            <i class="fas fa-id-card"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-chalkboard"></i> Wali Kelas</label>
                        <select class="form-control" name="wali_kelas">
                            <option value="">-- Pilih Kelas --</option>
                            <option value="X-RPL-1">X RPL 1</option>
                            <option value="X-RPL-2">X RPL 2</option>
                            <option value="XI-RPL-1">XI RPL 1</option>
                            <option value="XI-RPL-2">XI RPL 2</option>
                            <option value="XII-RPL-1">XII RPL 1</option>
                            <option value="XII-RPL-2">XII RPL 2</option>
                        </select>
                    </div>
                </div>
                
                <!-- Siswa Form -->
                <div id="siswaForm" class="role-form" style="display: none;">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Username</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="username" placeholder="Masukkan nama lengkap">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> NIS / NISN</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="nis" placeholder="Masukkan NIS atau NISN">
                            <i class="fas fa-id-card"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-school"></i> Kelas</label>
                        <select class="form-control" name="kelas">
                            <option value="">-- Pilih Kelas --</option>
                            <option value="X-RPL-1">X RPL 1</option>
                            <option value="X-RPL-2">X RPL 2</option>
                            <option value="XI-RPL-1">XI RPL 1</option>
                            <option value="XI-RPL-2">XI RPL 2</option>
                            <option value="XII-RPL-1">XII RPL 1</option>
                            <option value="XII-RPL-2">XII RPL 2</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="forgot-link">
                <a href="forgot-account.php"><i class="fas fa-question-circle"></i> Lupa Akun?</a>
            </div>
            
            <div class="back-home">
                <a href="index.php"><i class="fas fa-home"></i> Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        <?php if (isset($_SESSION['login_error'])): ?>
        window.addEventListener('DOMContentLoaded', function() {
            showAlert('error', '<?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?>');
        });
        <?php endif; ?>
        
        // Switch Role Function
        function switchRole(role) {
            // Update active button
            document.querySelectorAll('.role-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-role="${role}"]`).classList.add('active');
            
            // Update hidden input
            document.getElementById('roleInput').value = role;
            
            // Hide all forms
            document.getElementById('adminForm').style.display = 'none';
            document.getElementById('guruForm').style.display = 'none';
            document.getElementById('siswaForm').style.display = 'none';
            
            // Show selected form
            document.getElementById(role + 'Form').style.display = 'block';
            
            // Clear alert
            document.getElementById('alertContainer').innerHTML = '';
        }
        
        // Toggle Password Visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Form Submit Handler
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            // Tidak perlu preventDefault lagi, biarkan form submit ke AuthController
            // Show loading
            document.getElementById('loadingOverlay').classList.add('active');
        });
        
        // Show Alert Function
        function showAlert(type, message) {
            const alertClass = {
                'success': 'alert-success',
                'error': 'alert-danger',
                'warning': 'alert-warning',
                'info': 'alert-info'
            };
            
            const alert = `
                <div class="alert ${alertClass[type]} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            document.getElementById('alertContainer').innerHTML = alert;
        }
        
        // Check for timeout parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('timeout') === '1') {
            showAlert('warning', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }
    </script>
</body>
</html>