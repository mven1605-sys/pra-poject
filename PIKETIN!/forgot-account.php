<?php
/**
 * Forgot Account
 * File: forgot-account.php
 * Bantuan untuk lupa username/password
 */

require_once 'config/config.php';

$role = isset($_GET['role']) ? $_GET['role'] : 'siswa';

// Get data berdasarkan role
if ($role == 'admin') {
    $accounts = db_fetch_all("SELECT username, 'Hidden for security' as password_info, nama_lengkap FROM tb_admin WHERE is_active = 1");
} elseif ($role == 'guru') {
    $accounts = db_fetch_all("
        SELECT g.username, g.nip as password_info, g.nama_lengkap, k.nama_kelas 
        FROM tb_guru g
        LEFT JOIN tb_kelas k ON k.id_wali_kelas = g.id_guru
        WHERE g.is_active = 1
        ORDER BY g.nama_lengkap
    ");
} else {
    $kelas_filter = isset($_GET['kelas']) ? (int)$_GET['kelas'] : 0;
    
    $where = "WHERE s.is_active = 1";
    if ($kelas_filter > 0) {
        $where .= " AND s.id_kelas = $kelas_filter";
    }
    
    $accounts = db_fetch_all("
        SELECT s.username, s.nis as password_info, s.nama_lengkap, k.nama_kelas
        FROM tb_siswa s
        INNER JOIN tb_kelas k ON s.id_kelas = k.id_kelas
        $where
        ORDER BY k.nama_kelas, s.nama_lengkap
    ");
    
    $kelas_list = db_fetch_all("SELECT * FROM tb_kelas ORDER BY tingkat, nama_kelas");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Akun - PIKETIN</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px 20px;
        }
        
        .container-custom {
            max-width: 900px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            margin: 0 auto;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .content {
            padding: 30px;
        }
        
        .role-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        .role-tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            background: #f8f9fc;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: #333;
        }
        
        .role-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .role-tab:hover {
            transform: translateY(-2px);
        }
        
        .search-box {
            margin-bottom: 20px;
        }
        
        .account-item {
            background: #f8f9fc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        
        .account-item h6 {
            margin: 0 0 10px 0;
            color: #667eea;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <div class="header">
            <i class="fas fa-question-circle fa-3x mb-3"></i>
            <h2>Lupa Akun?</h2>
            <p class="mb-0">Temukan username dan informasi login Anda</p>
        </div>
        
        <div class="content">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Panduan:</strong> Pilih role Anda, lalu cari nama Anda di daftar untuk melihat username dan info password.
            </div>
            
            <!-- Role Tabs -->
            <div class="role-tabs">
                <a href="?role=siswa" class="role-tab <?php echo $role == 'siswa' ? 'active' : ''; ?>">
                    <i class="fas fa-user-graduate fa-2x mb-2"></i>
                    <div>Siswa</div>
                </a>
                <a href="?role=guru" class="role-tab <?php echo $role == 'guru' ? 'active' : ''; ?>">
                    <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
                    <div>Guru</div>
                </a>
                <a href="?role=admin" class="role-tab <?php echo $role == 'admin' ? 'active' : ''; ?>">
                    <i class="fas fa-user-shield fa-2x mb-2"></i>
                    <div>Admin</div>
                </a>
            </div>
            
            <?php if ($role == 'siswa' && isset($kelas_list)): ?>
            <!-- Filter Kelas (khusus siswa) -->
            <div class="search-box">
                <form method="GET">
                    <input type="hidden" name="role" value="siswa">
                    <div class="row">
                        <div class="col-md-8">
                            <select class="form-select" name="kelas" onchange="this.form.submit()">
                                <option value="0">-- Pilih Kelas Anda --</option>
                                <?php foreach ($kelas_list as $kelas): ?>
                                    <option value="<?php echo $kelas['id_kelas']; ?>" <?php echo $kelas_filter == $kelas['id_kelas'] ? 'selected' : ''; ?>>
                                        <?php echo $kelas['nama_kelas']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            
            <!-- Search -->
            <div class="search-box">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari nama Anda...">
            </div>
            
            <!-- Accounts List -->
            <div id="accountsList">
                <?php if (count($accounts) > 0): ?>
                    <?php foreach ($accounts as $account): ?>
                    <div class="account-item">
                        <h6><i class="fas fa-user"></i> <?php echo $account['nama_lengkap']; ?></h6>
                        <?php if ($role == 'siswa'): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <span class="info-label">Username:</span> <?php echo $account['username']; ?>
                                </div>
                                <div class="col-md-6">
                                    <span class="info-label">Password (NIS):</span> <?php echo $account['password_info']; ?>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <span class="info-label">Kelas:</span> <?php echo $account['nama_kelas']; ?>
                                </div>
                            </div>
                        <?php elseif ($role == 'guru'): ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <span class="info-label">Username:</span> <?php echo $account['username']; ?>
                                </div>
                                <div class="col-md-4">
                                    <span class="info-label">Password (NIP):</span> <?php echo $account['password_info']; ?>
                                </div>
                                <div class="col-md-4">
                                    <span class="info-label">Wali Kelas:</span> <?php echo $account['nama_kelas'] ?: '-'; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <span class="info-label">Username:</span> <?php echo $account['username']; ?>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <small class="text-muted">Untuk admin, silakan hubungi administrator sistem untuk reset password</small>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Tidak ada data. Silakan pilih kelas terlebih dahulu.
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="login.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Login
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const items = document.querySelectorAll('.account-item');
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>