<?php
require_once 'koneksi.php';

// Proteksi: Hanya Admin yang bisa cetak laporan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data untuk laporan
$query_barang = "SELECT * FROM barang ORDER BY kategori, nama_barang";
$result_barang = $koneksi->query($query_barang);

// Statistik
$total_barang = $koneksi->query("SELECT COUNT(*) as total FROM barang")->fetch_assoc()['total'];
$total_stok = $koneksi->query("SELECT SUM(stok) as total FROM barang")->fetch_assoc()['total'];
$stok_kritis = $koneksi->query("SELECT COUNT(*) as total FROM barang WHERE stok < 10")->fetch_assoc()['total'];
$total_nilai_beli = $koneksi->query("SELECT SUM(harga_beli * stok) as total FROM barang")->fetch_assoc()['total'];
$total_nilai_jual = $koneksi->query("SELECT SUM(harga_jual * stok) as total FROM barang")->fetch_assoc()['total'];
$potensi_profit = $total_nilai_jual - $total_nilai_beli;

// Query untuk data per kategori
$query_kategori = "SELECT kategori, COUNT(*) as jumlah, SUM(stok) as total_stok, 
                   SUM(harga_beli * stok) as nilai_beli, SUM(harga_jual * stok) as nilai_jual
                   FROM barang GROUP BY kategori ORDER BY kategori";
$result_kategori = $koneksi->query($query_kategori);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Barang - Stockify</title>
    <style>
        @media print {
            .no-print { display: none; }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .info-section {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .info-box {
            background: white;
            padding: 10px;
            border-left: 4px solid #667eea;
            border-radius: 3px;
        }
        
        .info-box h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #667eea;
        }
        
        .info-box p {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        th {
            background: #667eea;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        tbody tr:hover {
            background: #f0f0f0;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .stok-kritis {
            background: #ffebee !important;
            color: #c62828;
            font-weight: bold;
        }
        
        .kategori-badge {
            background: #e3f2fd;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            color: #1976d2;
        }
        
        .total-row {
            background: #e8eaf6 !important;
            font-weight: bold;
            font-size: 13px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            font-size: 11px;
            color: #666;
        }
        
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
        
        .btn-print {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .btn-print:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="btn-print no-print">
        🖨️ Cetak Laporan
    </button>
    
    <button onclick="window.location.href='dashboard.php'" class="btn-print no-print" 
            style="background: #6c757d; margin-left: 10px;">
        ← Kembali ke Dashboard
    </button>

    <!-- Header Laporan -->
    <div class="header">
        <h1>📦 LAPORAN STOK BARANG</h1>
        <h2 style="margin: 5px 0;">STOCKIFY SYSTEM</h2>
        <p>Jl. Warehouse Utama No. 123, Surabaya</p>
        <p>Telp: (031) 1234567 | Email: info@stockify.com</p>
    </div>

    <!-- Info Laporan -->
    <div style="margin-bottom: 20px;">
        <table style="border: none; width: 100%;">
            <tr style="border: none;">
                <td style="border: none; width: 50%;"><strong>Tanggal Cetak:</strong> <?= date('d F Y H:i:s') ?></td>
                <td style="border: none; text-align: right;"><strong>Dicetak Oleh:</strong> <?= htmlspecialchars($_SESSION['username']) ?> (ADMIN)</td>
            </tr>
        </table>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="info-section">
        <h3 style="margin-top: 0; color: #667eea;">📊 RINGKASAN STATISTIK</h3>
        <div class="info-grid">
            <div class="info-box">
                <h3>Total Jenis Barang</h3>
                <p><?= $total_barang ?> Items</p>
            </div>
            <div class="info-box">
                <h3>Total Unit Stok</h3>
                <p><?= number_format($total_stok) ?> Unit</p>
            </div>
            <div class="info-box" style="border-left-color: #dc3545;">
                <h3 style="color: #dc3545;">Stok Kritis</h3>
                <p style="color: #dc3545;"><?= $stok_kritis ?> Items</p>
            </div>
            <div class="info-box" style="border-left-color: #28a745;">
                <h3 style="color: #28a745;">Total Nilai Beli</h3>
                <p style="color: #28a745; font-size: 14px;"><?= format_rupiah($total_nilai_beli) ?></p>
            </div>
            <div class="info-box" style="border-left-color: #17a2b8;">
                <h3 style="color: #17a2b8;">Total Nilai Jual</h3>
                <p style="color: #17a2b8; font-size: 14px;"><?= format_rupiah($total_nilai_jual) ?></p>
            </div>
            <div class="info-box" style="border-left-color: #ffc107;">
                <h3 style="color: #ffc107;">Potensi Profit</h3>
                <p style="color: #ffc107; font-size: 14px;"><?= format_rupiah($potensi_profit) ?></p>
            </div>
        </div>
    </div>

    <!-- Tabel Ringkasan per Kategori -->
    <h3 style="color: #667eea;">📋 RINGKASAN PER KATEGORI</h3>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Jumlah Item</th>
                <th>Total Stok</th>
                <th>Total Nilai Beli</th>
                <th>Total Nilai Jual</th>
                <th>Profit</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $result_kategori->data_seek(0); // Reset pointer
            while ($row = $result_kategori->fetch_assoc()): 
                $profit = $row['nilai_jual'] - $row['nilai_beli'];
            ?>
            <tr>
                <td><strong><?= htmlspecialchars($row['kategori']) ?></strong></td>
                <td class="text-center"><?= $row['jumlah'] ?></td>
                <td class="text-center"><?= number_format($row['total_stok']) ?></td>
                <td class="text-right"><?= format_rupiah($row['nilai_beli']) ?></td>
                <td class="text-right"><?= format_rupiah($row['nilai_jual']) ?></td>
                <td class="text-right"><?= format_rupiah($profit) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Tabel Detail Barang -->
    <h3 style="color: #667eea; margin-top: 30px;">📦 DETAIL STOK BARANG</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Barang</th>
                <th style="width: 12%;">Kategori</th>
                <th style="width: 8%;">Stok</th>
                <th style="width: 13%;">Harga Beli</th>
                <th style="width: 13%;">Harga Jual</th>
                <th style="width: 12%;">Nilai Total</th>
                <th style="width: 12%;">Lokasi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $result_barang->data_seek(0);
            while ($row = $result_barang->fetch_assoc()): 
                $nilai_total = $row['harga_jual'] * $row['stok'];
                $stok_class = $row['stok'] < 10 ? 'stok-kritis' : '';
            ?>
            <tr class="<?= $stok_class ?>">
                <td class="text-center"><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td class="text-center">
                    <span class="kategori-badge"><?= htmlspecialchars($row['kategori']) ?></span>
                </td>
                <td class="text-center">
                    <?= $row['stok'] ?>
                    <?php if ($row['stok'] < 10): ?>
                        ⚠️
                    <?php endif; ?>
                </td>
                <td class="text-right"><?= format_rupiah($row['harga_beli']) ?></td>
                <td class="text-right"><?= format_rupiah($row['harga_jual']) ?></td>
                <td class="text-right"><?= format_rupiah($nilai_total) ?></td>
                <td class="text-center"><?= htmlspecialchars($row['lokasi_rak']) ?></td>
            </tr>
            <?php endwhile; ?>
            
            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="3" class="text-center">TOTAL KESELURUHAN</td>
                <td class="text-center"><?= number_format($total_stok) ?></td>
                <td class="text-right"><?= format_rupiah($total_nilai_beli) ?></td>
                <td class="text-right"><?= format_rupiah($total_nilai_jual) ?></td>
                <td class="text-right"><?= format_rupiah($total_nilai_jual) ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- Catatan -->
    <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin-top: 20px;">
        <h4 style="margin: 0 0 10px 0; color: #856404;">📌 Catatan Penting:</h4>
        <ul style="margin: 0; padding-left: 20px; color: #856404;">
            <li>Item dengan latar belakang <span style="background: #ffebee; padding: 2px 6px;">merah muda</span> menandakan stok kritis (< 10 unit) dan perlu segera direstock</li>
            <li>Total nilai dihitung berdasarkan: Harga Jual × Jumlah Stok</li>
            <li>Potensi profit merupakan selisih antara total nilai jual dengan total nilai beli</li>
            <li>Laporan ini bersifat rahasia dan hanya untuk kalangan internal</li>
        </ul>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature">
        <div class="signature-box">
            <p>Dibuat Oleh,</p>
            <div class="signature-line">
                <strong><?= htmlspecialchars($_SESSION['username']) ?></strong><br>
                <small>Admin Warehouse</small>
            </div>
        </div>
        
        <div class="signature-box">
            <p>Mengetahui,</p>
            <div class="signature-line">
                <strong>_________________</strong><br>
                <small>Manager Gudang</small>
            </div>
        </div>
        
        <div class="signature-box">
            <p>Menyetujui,</p>
            <div class="signature-line">
                <strong>_________________</strong><br>
                <small>Direktur</small>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p style="text-align: center; margin: 0;">
            Dokumen ini dicetak otomatis oleh sistem Stockify pada <?= date('d F Y H:i:s') ?><br>
            <small>© <?= date('Y') ?> Stockify System - All Rights Reserved</small>
        </p>
    </div>
</body>
</html>