<?php
session_start();

// Check if logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "<h2>Not Logged In</h2>";
    echo "<p>You need to login first.</p>";
    echo "<a href='simple-login.php'>Login</a>";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand">
                <i class="fas fa-clipboard-check"></i> PIKETIN - Admin Dashboard
            </span>
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="alert alert-success">
            <h4><i class="fas fa-check-circle"></i> Dashboard Berhasil Diakses!</h4>
            <p>Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?>!</p>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-user-graduate"></i> Siswa</h5>
                        <h2>0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-chalkboard-teacher"></i> Guru</h5>
                        <h2>0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-door-open"></i> Kelas</h5>
                        <h2>0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-book"></i> Jurusan</h5>
                        <h2>0</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5>Menu Admin</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="pages/admin/kelola-siswa.php" class="btn btn-primary w-100">
                            <i class="fas fa-user-graduate"></i> Kelola Siswa
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="pages/admin/kelola-guru.php" class="btn btn-success w-100">
                            <i class="fas fa-chalkboard-teacher"></i> Kelola Guru
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="pages/admin/kelola-kelas.php" class="btn btn-warning w-100">
                            <i class="fas fa-door-open"></i> Kelola Kelas
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="pages/admin/jadwal-piket.php" class="btn btn-info w-100">
                            <i class="fas fa-calendar-alt"></i> Jadwal Piket
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h6>Session Info</h6>
            </div>
            <div class="card-body">
                <small>
                    User ID: <?php echo $_SESSION['user_id']; ?><br>
                    Username: <?php echo $_SESSION['username']; ?><br>
                    Role: <?php echo $_SESSION['role']; ?><br>
                    Name: <?php echo $_SESSION['nama_lengkap']; ?><br>
                    Login Time: <?php echo date('d-m-Y H:i:s', $_SESSION['login_time']); ?>
                </small>
            </div>
        </div>
    </div>
</body>
</html>