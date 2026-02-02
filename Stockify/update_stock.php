<?php
require_once 'koneksi.php';

// Proteksi halaman - harus login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Ambil ID barang
$barang_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data barang
$stmt = $koneksi->prepare("SELECT * FROM barang WHERE id = ?");
$stmt->bind_param("i", $barang_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$barang = $result->fetch_assoc();
$error = '';
$success = '';

// Proses Update Stok
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_stok'])) {
    $jenis = clean_input($koneksi, $_POST['jenis_transaksi']);
    $jumlah = (int)$_POST['jumlah'];
    $keterangan = clean_input($koneksi, $_POST['keterangan']);
    
    if ($jumlah <= 0) {
        $error = "Jumlah harus lebih dari 0!";
    } else {
        $stok_lama = $barang['stok'];
        
        // Hitung stok baru
        if ($jenis === 'masuk') {
            $stok_baru = $stok_lama + $jumlah;
        } else { // keluar
            if ($jumlah > $stok_lama) {
                $error = "Stok tidak mencukupi! Stok tersedia: $stok_lama";
            } else {
                $stok_baru = $stok_lama - $jumlah;
            }
        }
        
        // Update jika tidak ada error
        if (empty($error)) {
            // Update stok barang
            $stmt = $koneksi->prepare("UPDATE barang SET stok = ? WHERE id = ?");
            $stmt->bind_param("ii", $stok_baru, $barang_id);
            
            if ($stmt->execute()) {
                // Insert ke history_stok
                $stmt_history = $koneksi->prepare(
                    "INSERT INTO history_stok (barang_id, user_id, jenis_transaksi, jumlah, stok_sebelum, stok_sesudah, keterangan) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt_history->bind_param("iisiiis", $barang_id, $user_id, $jenis, $jumlah, $stok_lama, $stok_baru, $keterangan);
                $stmt_history->execute();
                
                $success = "Stok berhasil diupdate! Stok lama: $stok_lama → Stok baru: $stok_baru";
                
                // Update data barang untuk ditampilkan
                $barang['stok'] = $stok_baru;
            } else {
                $error = "Gagal update stok!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Stok - Stockify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .stok-display {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
        }
        .stok-kritis {
            color: #dc3545 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-arrow-repeat me-2"></i>Update Stok Barang
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="bi bi-check-circle-fill me-2"></i><?= $success ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Info Barang -->
                        <div class="info-box">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="mb-3">
                                        <i class="bi bi-box-seam me-2"></i><?= htmlspecialchars($barang['nama_barang']) ?>
                                    </h5>
                                    <p class="mb-1">
                                        <strong>Kategori:</strong> 
                                        <span class="badge bg-secondary"><?= htmlspecialchars($barang['kategori']) ?></span>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Lokasi:</strong> 
                                        <span class="badge bg-info"><?= htmlspecialchars($barang['lokasi_rak']) ?></span>
                                    </p>
                                    <?php if ($role === 'admin'): ?>
                                    <p class="mb-0">
                                        <strong>Harga Jual:</strong> <?= format_rupiah($barang['harga_jual']) ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4 text-center">
                                    <p class="mb-1 text-muted">Stok Saat Ini</p>
                                    <div class="stok-display <?= $barang['stok'] < 10 ? 'stok-kritis' : '' ?>">
                                        <?= $barang['stok'] ?>
                                    </div>
                                    <small class="text-muted">unit</small>
                                    <?php if ($barang['stok'] < 10): ?>
                                    <div class="mt-2">
                                        <span class="badge bg-danger">
                                            <i class="bi bi-exclamation-triangle me-1"></i>Stok Kritis!
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Form Update -->
                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-arrow-left-right me-2"></i>Jenis Transaksi
                                    </label>
                                    <select class="form-select" name="jenis_transaksi" required id="jenis">
                                        <option value="">-- Pilih --</option>
                                        <option value="masuk">Stok Masuk (+)</option>
                                        <option value="keluar">Stok Keluar (-)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-hash me-2"></i>Jumlah
                                    </label>
                                    <input type="number" class="form-control" name="jumlah" 
                                           required min="1" id="jumlah" placeholder="Masukkan jumlah">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-chat-left-text me-2"></i>Keterangan
                                </label>
                                <textarea class="form-control" name="keterangan" rows="3" 
                                          placeholder="Tambahkan catatan (opsional)"></textarea>
                            </div>

                            <!-- Preview Hasil -->
                            <div class="alert alert-info" id="preview" style="display: none;">
                                <h6>Preview:</h6>
                                <p class="mb-0" id="previewText"></p>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="update_stok" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Update Stok
                                </button>
                                <a href="dashboard.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- History (jika ada) -->
                <?php
                $query_history = "SELECT h.*, u.username 
                                 FROM history_stok h 
                                 JOIN users u ON h.user_id = u.id 
                                 WHERE h.barang_id = ? 
                                 ORDER BY h.created_at DESC LIMIT 5";
                $stmt = $koneksi->prepare($query_history);
                $stmt->bind_param("i", $barang_id);
                $stmt->execute();
                $result_history = $stmt->get_result();
                
                if ($result_history->num_rows > 0):
                ?>
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i>Riwayat Transaksi Terakhir
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Jenis</th>
                                        <th>Jumlah</th>
                                        <th>Stok Akhir</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($h = $result_history->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
                                        <td>
                                            <?php if ($h['jenis_transaksi'] === 'masuk'): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-arrow-down"></i> Masuk
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-arrow-up"></i> Keluar
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $h['jumlah'] ?></td>
                                        <td><strong><?= $h['stok_sesudah'] ?></strong></td>
                                        <td><?= htmlspecialchars($h['username']) ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview calculator
        const jenis = document.getElementById('jenis');
        const jumlah = document.getElementById('jumlah');
        const preview = document.getElementById('preview');
        const previewText = document.getElementById('previewText');
        const stokSekarang = <?= $barang['stok'] ?>;

        function updatePreview() {
            const jenisValue = jenis.value;
            const jumlahValue = parseInt(jumlah.value) || 0;
            
            if (jenisValue && jumlahValue > 0) {
                let stokBaru;
                let icon;
                let color;
                
                if (jenisValue === 'masuk') {
                    stokBaru = stokSekarang + jumlahValue;
                    icon = '↑';
                    color = 'success';
                } else {
                    stokBaru = stokSekarang - jumlahValue;
                    icon = '↓';
                    color = stokBaru < 0 ? 'danger' : 'warning';
                }
                
                preview.style.display = 'block';
                preview.className = `alert alert-${color}`;
                previewText.innerHTML = `
                    <strong>${icon} Stok akan berubah:</strong><br>
                    Sekarang: <strong>${stokSekarang}</strong> unit 
                    ${jenisValue === 'masuk' ? '+' : '-'} ${jumlahValue} = 
                    <strong>${stokBaru}</strong> unit
                    ${stokBaru < 0 ? '<br><span class="text-danger">⚠️ STOK TIDAK CUKUP!</span>' : ''}
                    ${stokBaru < 10 && stokBaru >= 0 ? '<br><span class="text-warning">⚠️ Stok akan kritis!</span>' : ''}
                `;
            } else {
                preview.style.display = 'none';
            }
        }

        jenis.addEventListener('change', updatePreview);
        jumlah.addEventListener('input', updatePreview);
    </script>
</body>
</html>