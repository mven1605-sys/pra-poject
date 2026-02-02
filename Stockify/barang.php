<?php
require_once 'koneksi.php';

// Proteksi halaman - harus login dulu
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Handle actions
$action = $_GET['action'] ?? 'list';
$success = '';
$error = '';

// Handle Add/Edit Barang (hanya admin)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $role === 'admin') {
    if (isset($_POST['add_barang'])) {
        $nama_barang = clean_input($koneksi, $_POST['nama_barang']);
        $kategori = clean_input($koneksi, $_POST['kategori']);
        $stok = (int)$_POST['stok'];
        $harga_beli = (float)$_POST['harga_beli'];
        $harga_jual = (float)$_POST['harga_jual'];
        $lokasi_rak = clean_input($koneksi, $_POST['lokasi_rak']);
        
        $stmt = $koneksi->prepare("INSERT INTO barang (nama_barang, kategori, stok, harga_beli, harga_jual, lokasi_rak) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssidds", $nama_barang, $kategori, $stok, $harga_beli, $harga_jual, $lokasi_rak);
        
        if ($stmt->execute()) {
            $success = "Barang berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan barang: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Get all barang
$query = "SELECT * FROM barang ORDER BY created_at DESC";
$result = $koneksi->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang - Stockify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-primary text-white p-3">
                <h4><i class="bi bi-box-seam me-2"></i>Stockify</h4>
                <nav class="nav flex-column mt-4">
                    <a class="nav-link text-white" href="dashboard.php">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a class="nav-link text-white-50 active" href="barang.php">
                        <i class="bi bi-box me-2"></i> Data Barang
                    </a>
                    <?php if ($role === 'admin'): ?>
                    <a class="nav-link text-white" href="analytics.php">
                        <i class="bi bi-graph-up me-2"></i> Analytics
                    </a>
                    <?php endif; ?>
                    <hr>
                    <a class="nav-link text-white" href="logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-box me-2"></i>Data Barang</h2>
                    <?php if ($role === 'admin'): ?>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Barang
                    </button>
                    <?php endif; ?>
                </div>
                
                <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i><?= $success ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Tabel Barang -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Stok</th>
                                        <th>Harga Beli</th>
                                        <th>Harga Jual</th>
                                        <th>Lokasi Rak</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                        <td><?= htmlspecialchars($row['kategori']) ?></td>
                                        <td>
                                            <span class="badge <?= $row['stok'] < 10 ? 'bg-danger' : 'bg-success' ?>">
                                                <?= $row['stok'] ?>
                                            </span>
                                        </td>
                                        <td><?= format_rupiah($row['harga_beli']) ?></td>
                                        <td><?= format_rupiah($row['harga_jual']) ?></td>
                                        <td><?= htmlspecialchars($row['lokasi_rak']) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="update_stock.php?id=<?= $row['id'] ?>" class="btn btn-warning" title="Update Stok">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Add Barang (Admin Only) -->
    <?php if ($role === 'admin'): ?>
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Barang Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" name="nama_barang" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <input type="text" class="form-control" name="kategori" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stok Awal</label>
                            <input type="number" class="form-control" name="stok" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga Beli</label>
                            <input type="number" class="form-control" name="harga_beli" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga Jual</label>
                            <input type="number" class="form-control" name="harga_jual" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi Rak</label>
                            <input type="text" class="form-control" name="lokasi_rak" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_barang" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>