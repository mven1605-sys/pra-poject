<?php
/**
 * PDF Export Helper
 * File: helpers/pdf_helper.php
 * Export laporan ke PDF menggunakan TCPDF atau FPDF
 */

// Install TCPDF via Composer: composer require tecnickcom/tcpdf
// Atau download manual dari https://tcpdf.org/

require_once(dirname(__FILE__) . '/../vendor/tecnickcom/tcpdf/tcpdf.php');

/**
 * Generate Laporan Piket PDF
 * 
 * @param array $data Data laporan
 * @param string $periode Periode laporan
 * @param string $kelas Nama kelas
 * @param array $summary Summary statistik
 * @param string $wali_kelas Nama wali kelas
 * @param string $nip NIP wali kelas
 * @return string Path to generated PDF
 */
function generateLaporanPDF($data, $periode, $kelas, $summary, $wali_kelas, $nip) {
    // Create new PDF document
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Sistem Piketin');
    $pdf->SetAuthor('SMK Negeri 2');
    $pdf->SetTitle('Laporan Piket - ' . $kelas);
    $pdf->SetSubject('Laporan Piket');
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', '', 10);
    
    // === HEADER ===
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'LAPORAN PIKET KELAS', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 8, $kelas, 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'SMK Negeri 2 Surabaya', 0, 1, 'C');
    $pdf->Cell(0, 6, 'Periode: ' . $periode, 0, 1, 'C');
    
    // Line
    $pdf->Ln(2);
    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
    $pdf->Ln(5);
    
    // === SUMMARY BOX ===
    $pdf->SetFillColor(240, 247, 255);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 8, 'RINGKASAN', 1, 1, 'C', true);
    
    $pdf->SetFont('helvetica', '', 10);
    
    // Summary in 4 columns
    $col_width = 45;
    $pdf->Cell($col_width, 7, 'Total Jadwal', 1, 0, 'C', true);
    $pdf->Cell($col_width, 7, 'Sudah Absen', 1, 0, 'C', true);
    $pdf->Cell($col_width, 7, 'Tepat Waktu', 1, 0, 'C', true);
    $pdf->Cell($col_width, 7, 'Terlambat', 1, 1, 'C', true);
    
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell($col_width, 7, $summary['total_jadwal'], 1, 0, 'C');
    $pdf->Cell($col_width, 7, $summary['total_hadir'], 1, 0, 'C');
    $pdf->Cell($col_width, 7, $summary['tepat_waktu'], 1, 0, 'C');
    $pdf->Cell($col_width, 7, $summary['terlambat'], 1, 1, 'C');
    
    $pdf->Ln(5);
    
    // === TABLE HEADER ===
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor(230, 230, 230);
    
    $pdf->Cell(10, 7, 'No', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Tanggal', 1, 0, 'C', true);
    $pdf->Cell(20, 7, 'Hari', 1, 0, 'C', true);
    $pdf->Cell(65, 7, 'Anggota Piket', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Waktu', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Status', 1, 1, 'C', true);
    
    // === TABLE DATA ===
    $pdf->SetFont('helvetica', '', 8);
    
    if (count($data) > 0) {
        foreach ($data as $index => $item) {
            // Check if need new page
            if ($pdf->GetY() > 250) {
                $pdf->AddPage();
                
                // Repeat header
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetFillColor(230, 230, 230);
                
                $pdf->Cell(10, 7, 'No', 1, 0, 'C', true);
                $pdf->Cell(30, 7, 'Tanggal', 1, 0, 'C', true);
                $pdf->Cell(20, 7, 'Hari', 1, 0, 'C', true);
                $pdf->Cell(65, 7, 'Anggota Piket', 1, 0, 'C', true);
                $pdf->Cell(25, 7, 'Waktu', 1, 0, 'C', true);
                $pdf->Cell(30, 7, 'Status', 1, 1, 'C', true);
                
                $pdf->SetFont('helvetica', '', 8);
            }
            
            $status = $item['waktu_absensi'] ? $item['status'] : 'Belum Absen';
            
            $pdf->Cell(10, 7, $index + 1, 1, 0, 'C');
            $pdf->Cell(30, 7, date('d-m-Y', strtotime($item['tanggal'])), 1, 0, 'C');
            $pdf->Cell(20, 7, $item['hari'], 1, 0, 'C');
            $pdf->Cell(65, 7, substr($item['anggota_nama'], 0, 35), 1, 0, 'L');
            $pdf->Cell(25, 7, $item['waktu_absensi'] ?: '-', 1, 0, 'C');
            $pdf->Cell(30, 7, $status, 1, 1, 'C');
        }
    } else {
        $pdf->Cell(180, 7, 'Tidak ada data untuk periode ini', 1, 1, 'C');
    }
    
    // === SIGNATURE ===
    $pdf->Ln(10);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(90, 6, '', 0, 0);
    $pdf->Cell(90, 6, 'Surabaya, ' . date('d F Y'), 0, 1, 'C');
    
    $pdf->Cell(90, 6, '', 0, 0);
    $pdf->Cell(90, 6, 'Wali Kelas', 0, 1, 'C');
    
    $pdf->Ln(15);
    
    $pdf->SetFont('helvetica', 'BU', 10);
    $pdf->Cell(90, 6, '', 0, 0);
    $pdf->Cell(90, 6, $wali_kelas, 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(90, 6, '', 0, 0);
    $pdf->Cell(90, 6, 'NIP: ' . $nip, 0, 1, 'C');
    
    // === SAVE PDF ===
    $filename = 'Laporan_Piket_' . str_replace(' ', '_', $kelas) . '_' . date('YmdHis') . '.pdf';
    $filepath = dirname(__FILE__) . '/../reports/temp/' . $filename;
    
    // Create reports/temp folder if not exists
    if (!is_dir(dirname(__FILE__) . '/../reports/temp/')) {
        mkdir(dirname(__FILE__) . '/../reports/temp/', 0755, true);
    }
    
    // Save PDF to file
    $pdf->Output($filepath, 'F');
    
    return [
        'success' => true,
        'filename' => $filename,
        'filepath' => $filepath
    ];
}

/**
 * Generate and Download PDF
 * Call this from controller
 */
function downloadLaporanPDF($data, $periode, $kelas, $summary, $wali_kelas, $nip) {
    $result = generateLaporanPDF($data, $periode, $kelas, $summary, $wali_kelas, $nip);
    
    if ($result['success']) {
        // Set headers for download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
        header('Content-Length: ' . filesize($result['filepath']));
        
        // Output file
        readfile($result['filepath']);
        
        // Delete temp file after download
        unlink($result['filepath']);
        
        exit();
    }
    
    return $result;
}

/**
 * Generate PDF Absensi Detail (dengan foto)
 * Untuk export detail absensi lengkap dengan foto
 */
function generateAbsensiDetailPDF($absensi_data, $jadwal_info) {
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
    $pdf->SetCreator('Sistem Piketin');
    $pdf->SetTitle('Detail Absensi Piket');
    
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 10);
    
    // Header
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'DETAIL ABSENSI PIKET', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'SMK Negeri 2 Surabaya', 0, 1, 'C');
    
    $pdf->Ln(5);
    
    // Info
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(40, 6, 'Tanggal', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, ': ' . formatTanggal($jadwal_info['tanggal'], 'lengkap'), 0, 1);
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(40, 6, 'Kelas', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, ': ' . $jadwal_info['kelas'], 0, 1);
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(40, 6, 'Waktu Absensi', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, ': ' . $absensi_data['waktu_absensi'], 0, 1);
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(40, 6, 'Status', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, ': ' . $absensi_data['status'], 0, 1);
    
    $pdf->Ln(5);
    
    // Anggota
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'Anggota Piket:', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(0, 6, $jadwal_info['anggota_nama'], 0, 'L');
    
    $pdf->Ln(5);
    
    // Dokumentasi Foto
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'Dokumentasi Foto:', 0, 1);
    
    $pdf->Ln(3);
    
    // Add photos (4 photos in 2x2 grid)
    $foto_path = dirname(__FILE__) . '/../uploads/foto-absensi/';
    $foto_width = 85;
    $foto_height = 60;
    $spacing = 5;
    
    $x_start = 15;
    $y_start = $pdf->GetY();
    
    // Foto 1 - Anggota (top left)
    if (file_exists($foto_path . $absensi_data['foto_anggota'])) {
        $pdf->Image($foto_path . $absensi_data['foto_anggota'], $x_start, $y_start, $foto_width, $foto_height);
        $pdf->SetXY($x_start, $y_start + $foto_height + 1);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell($foto_width, 4, 'Foto Anggota Piket', 0, 0, 'C');
    }
    
    // Foto 2 - Area 1 (top right)
    if (file_exists($foto_path . $absensi_data['foto_area1'])) {
        $pdf->Image($foto_path . $absensi_data['foto_area1'], $x_start + $foto_width + $spacing, $y_start, $foto_width, $foto_height);
        $pdf->SetXY($x_start + $foto_width + $spacing, $y_start + $foto_height + 1);
        $pdf->Cell($foto_width, 4, 'Foto Area Kelas 1', 0, 0, 'C');
    }
    
    // Move to next row
    $y_start += $foto_height + 10;
    
    // Foto 3 - Area 2 (bottom left)
    if (file_exists($foto_path . $absensi_data['foto_area2'])) {
        $pdf->Image($foto_path . $absensi_data['foto_area2'], $x_start, $y_start, $foto_width, $foto_height);
        $pdf->SetXY($x_start, $y_start + $foto_height + 1);
        $pdf->Cell($foto_width, 4, 'Foto Area Kelas 2', 0, 0, 'C');
    }
    
    // Foto 4 - Area 3 (bottom right)
    if (file_exists($foto_path . $absensi_data['foto_area3'])) {
        $pdf->Image($foto_path . $absensi_data['foto_area3'], $x_start + $foto_width + $spacing, $y_start, $foto_width, $foto_height);
        $pdf->SetXY($x_start + $foto_width + $spacing, $y_start + $foto_height + 1);
        $pdf->Cell($foto_width, 4, 'Foto Area Kelas 3', 0, 0, 'C');
    }
    
    // Save
    $filename = 'Detail_Absensi_' . date('YmdHis') . '.pdf';
    $filepath = dirname(__FILE__) . '/../reports/temp/' . $filename;
    
    $pdf->Output($filepath, 'F');
    
    return [
        'success' => true,
        'filename' => $filename,
        'filepath' => $filepath
    ];
}

/**
 * Clean old temp PDF files (older than 1 hour)
 * Jalankan via CRON job atau saat generate PDF baru
 */
function cleanOldPDFs() {
    $temp_dir = dirname(__FILE__) . '/../reports/temp/';
    
    if (!is_dir($temp_dir)) return;
    
    $files = glob($temp_dir . '*.pdf');
    $now = time();
    $deleted = 0;
    
    foreach ($files as $file) {
        if (is_file($file)) {
            // Delete files older than 1 hour
            if ($now - filemtime($file) >= 3600) {
                unlink($file);
                $deleted++;
            }
        }
    }
    
    return $deleted;
}
?>