<?php
session_start();

// Simple check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "Not logged in as admin. <a href='../../login.php'>Login</a>";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin Test</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="alert alert-success">
            <h2>✅ Dashboard Admin Test Berhasil!</h2>
            <p>Selamat datang, <?php echo $_SESSION['nama_lengkap'] ?? 'Admin'; ?>!</p>
            <p>Role: <?php echo $_SESSION['role']; ?></p>
            <p>User ID: <?php echo $_SESSION['user_id']; ?></p>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h5>Quick Links</h5>
                <a href="dashboard.php" class="btn btn-primary">Dashboard Asli</a>
                <a href="../../controllers/AuthController.php?action=logout" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>