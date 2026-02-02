<?php
require_once 'koneksi.php';

$error = '';
$success = '';

// Proses Reset Password (Simulasi Sederhana)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset'])) {
    $email = clean_input($koneksi, $_POST['email']);
    
    // Cek apakah email terdaftar
    $stmt = $koneksi->prepare("SELECT id, username FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // SIMULASI: Generate temporary password
        $temp_password = substr(md5(time()), 0, 8);
        $hashed_temp = password_hash($temp_password, PASSWORD_BCRYPT);
        
        // Update password sementara
        $stmt = $koneksi->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_temp, $email);
        
        if ($stmt->execute()) {
            $success = "Password sementara telah dibuat: <strong>$temp_password</strong><br>
                       Silakan login dengan password ini, lalu ubah di pengaturan.<br>
                       <small class='text-muted'>(Dalam sistem nyata, ini akan dikirim via email)</small>";
        }
    } else {
        $error = "Email tidak terdaftar dalam sistem!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Stockify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .forgot-container {
            max-width: 450px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="forgot-container">
            <div class="card">
                <div class="card-header text-center">
                    <i class="bi bi-question-circle fs-1"></i>
                    <h3 class="mb-0 mt-2">Lupa Password?</h3>
                    <p class="mb-0 small">Masukkan email untuk reset password</p>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i><?= $success ?>
                        </div>
                        <a href="login.php" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Kembali ke Login
                        </a>
                    <?php else: ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-envelope-fill me-2"></i>Email</label>
                                <input type="email" class="form-control" name="email" required
                                       placeholder="Masukkan email terdaftar">
                            </div>
                            <button type="submit" name="reset" class="btn btn-primary w-100">
                                <i class="bi bi-key-fill me-2"></i>Reset Password
                            </button>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <a href="login.php" class="text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i>Kembali ke Login
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>