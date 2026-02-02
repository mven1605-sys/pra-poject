<?php
/**
 * Quick Login for Testing
 */
session_start();
require_once 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = 'admin';
    $password = 'admin123';
    
    // Get admin user
    $admin = db_fetch_one("SELECT * FROM tb_admin WHERE username = 'admin' AND is_active = 1 LIMIT 1");
    
    if ($admin && password_verify($password, $admin['password'])) {
        // Set session
        $_SESSION['user_id'] = $admin['id_admin'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['nama_lengkap'] = $admin['nama_lengkap'];
        $_SESSION['role'] = 'Admin';
        $_SESSION['foto_profil'] = $admin['foto_profil'] ?? 'default-avatar.png';
        $_SESSION['login_time'] = time();
        
        // Redirect to dashboard
        header('Location: admin-dashboard.php');
        exit();
    } else {
        $error = 'Login gagal. Pastikan admin user sudah dibuat.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Login - PIKETIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header bg-success text-white">
                        <h4><i class="fas fa-bolt"></i> Quick Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                        <?php endif; ?>
                        
                        <p>Login langsung sebagai admin untuk testing.</p>
                        
                        <form method="POST">
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-sign-in-alt"></i> Login as Admin
                            </button>
                        </form>
                        
                        <hr>
                        <div class="d-grid gap-2">
                            <a href="setup.php" class="btn btn-primary">
                                <i class="fas fa-cog"></i> Setup Admin User
                            </a>
                            <a href="login.php" class="btn btn-secondary">
                                <i class="fas fa-sign-in-alt"></i> Normal Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>