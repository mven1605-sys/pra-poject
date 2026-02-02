<!DOCTYPE html>
<html>
<head>
    <title>Complete Test</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3><i class="fas fa-tools"></i> Complete Test Suite</h3>
            </div>
            <div class="card-body">
                <h5>Langkah untuk Test Dashboard Admin:</h5>
                <ol>
                    <li><strong>Debug Info</strong> - Cek semua komponen sistem</li>
                    <li><strong>Create Admin</strong> - Buat user admin (username: admin, password: admin123)</li>
                    <li><strong>Simple Login</strong> - Test login manual</li>
                    <li><strong>Dashboard</strong> - Akses dashboard setelah login</li>
                    <li><strong>Normal Login</strong> - Test login melalui form asli</li>
                </ol>
                
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>1. Debug Info</h6>
                                <a href="debug.php" class="btn btn-info">
                                    <i class="fas fa-bug"></i> Debug
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>2. Create Admin</h6>
                                <a href="create-admin.php" class="btn btn-success">
                                    <i class="fas fa-user-plus"></i> Create
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>3. Simple Login</h6>
                                <a href="simple-login.php" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>4. Dashboard</h6>
                                <a href="simple-dashboard.php" class="btn btn-warning">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>5. Normal Login</h6>
                                <a href="login.php" class="btn btn-secondary">
                                    <i class="fas fa-sign-in-alt"></i> Login Form
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>6. Home</h6>
                                <a href="index.php" class="btn btn-dark">
                                    <i class="fas fa-home"></i> Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mt-4">
                    <h6><i class="fas fa-info-circle"></i> Petunjuk:</h6>
                    <ul class="mb-0">
                        <li>Jalankan <strong>Debug</strong> untuk cek sistem</li>
                        <li>Jalankan <strong>Create Admin</strong> untuk buat user</li>
                        <li>Gunakan <strong>Simple Login</strong> untuk test manual</li>
                        <li>Akses <strong>Dashboard</strong> setelah login berhasil</li>
                        <li>Test <strong>Normal Login</strong> dengan form asli</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>