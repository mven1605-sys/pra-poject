<?php
/**
 * Setup Admin User
 */
require_once 'config/config.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create admin user
        $password_hash = password_hash('admin123', PASSWORD_BCRYPT);
        
        // Delete existing admin first
        db_query("DELETE FROM tb_admin WHERE username = 'admin'");
        
        // Insert new admin
        $query = "INSERT INTO tb_admin (username, password, nama_lengkap, email, is_active) 
                  VALUES ('admin', '$password_hash', 'Administrator', 'admin@smkn2.sch.id', 1)";
        
        if (db_query($query)) {
            $message = 'Admin user berhasil dibuat!';
            $success = true;
        } else {
            $message = 'Gagal membuat admin user.';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Admin - PIKETIN</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-cog"></i> Setup Admin User</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                        <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>">
                            <?php echo $message; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                        <div class="alert alert-info">
                            <strong>Login Credentials:</strong><br>
                            Username: <code>admin</code><br>
                            Password: <code>admin123</code>
                        </div>
                        <a href="login.php" class="btn btn-success">
                            <i class="fas fa-sign-in-alt"></i> Go to Login
                        </a>
                        <?php else: ?>
                        <p>Klik tombol di bawah untuk membuat user admin default.</p>
                        <form method="POST">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Create Admin User
                            </button>
                        </form>
                        <?php endif; ?>
                        
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            File ini akan membuat user admin dengan username "admin" dan password "admin123".
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>