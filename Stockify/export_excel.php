<?php
require_once 'koneksi.php';

// Proteksi halaman
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

// Set header untuk download Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Data_Stok_Barang_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Ambil data barang
$query = "SELECT * FROM barang ORDER BY kategori, nama_barang";
$result = $koneksi->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Export Data Barang</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .stok-kritis {
            background-color: #ffcccc;
            color: #cc0000;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>DATA STOK BARANG - STOCKIFY</h2>
    <p>Tanggal Export: <?= date('d F Y H:i:s') ?></p>
    <p>Diekspor oleh: <?= htmlspecialchars($_SESSION['username']) ?> (<?= strtoupper($role) ?>)</p>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Stok</th>
                <?php if ($role === 'admin'): ?>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Total Nilai Beli</th>
                <th>Total Nilai Jual</th>
                <?php endif; ?>
                <th>Lokasi Rak</th>
                <th>Tanggal Input</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $total_stok = 0;
            $total_nilai_beli = 0;
            $total_nilai_jual = 0;
            
            while ($row = $result->fetch_assoc()): 
                $stok_class = $row['stok'] < 10 ? 'stok-kritis' : '';
                $total_stok += $row['stok'];
                
                if ($role === 'admin') {
                    $nilai_beli = $row['harga_beli'] * $row['stok'];
                    $nilai_jual = $row['harga_jual'] * $row['stok'];
                    $total_nilai_beli += $nilai_beli;
                    $total_nilai_jual += $nilai_jual;
                }
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td><?= htmlspecialchars($row['kategori']) ?></td>
                <td class="<?= $stok_class ?> text-right"><?= $row['stok'] ?></td>
                <?php if ($role === 'admin'): ?>
                <td class="text-right"><?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($nilai_beli, 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($nilai_jual, 0, ',', '.') ?></td>
                <?php endif; ?>
                <td><?= htmlspecialchars($row['lokasi_rak']) ?></td>
                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
            
            <!-- Total Row -->
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="3">TOTAL</td>
                <td class="text-right"><?= number_format($total_stok) ?></td>
                <?php if ($role === 'admin'): ?>
                <td colspan="2"></td>
                <td class="text-right"><?= number_format($total_nilai_beli, 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($total_nilai_jual, 0, ',', '.') ?></td>
                <?php endif; ?>
                <td colspan="2"></td>
            </tr>
            
            <?php if ($role === 'admin'): ?>
            <tr style="background-color: #d4edda; font-weight: bold;">
                <td colspan="6">POTENSI PROFIT</td>
                <td colspan="2" class="text-right">
                    <?= number_format($total_nilai_jual - $total_nilai_beli, 0, ',', '.') ?>
                </td>
                <td colspan="2"></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <p style="margin-top: 20px; font-size: 11px; color: #666;">
        <strong>Keterangan:</strong><br>
        - Stok dengan background merah menandakan stok kritis (< 10 unit)<br>
        <?php if ($role === 'admin'): ?>
        - Data harga hanya dapat dilihat oleh ADMIN<br>
        <?php endif; ?>
        - Export otomatis dilakukan pada <?= date('d F Y H:i:s') ?>
    </p>
</body>
</html>