-- ============================================
-- DATABASE STOCKIFY dengan Authentication
-- ============================================

CREATE DATABASE IF NOT EXISTS stockify;
USE stockify;

-- Tabel Users untuk Authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'karyawan') NOT NULL DEFAULT 'karyawan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Barang
CREATE TABLE barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    harga_beli DECIMAL(10,2) NOT NULL,
    harga_jual DECIMAL(10,2) NOT NULL,
    lokasi_rak VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_kategori (kategori),
    INDEX idx_stok (stok)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel History untuk Log Aktivitas (opsional, untuk audit trail)
CREATE TABLE history_stok (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barang_id INT NOT NULL,
    user_id INT NOT NULL,
    jenis_transaksi ENUM('masuk', 'keluar') NOT NULL,
    jumlah INT NOT NULL,
    stok_sebelum INT NOT NULL,
    stok_sesudah INT NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Data User Default (password: admin123 dan karyawan123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@warehouse.com', '$2y$10$3.CEt6LDw0x/MGnGvhH3c.O/BvwafGQaxjDVZcOReaCEPL9fSMJPi', 'admin'),
('karyawan1', 'karyawan@warehouse.com', '$2y$10$AT86QzV.tEET.bv4XwI1zO1rodeLvcsQ50V7JGqnLzKdzEi.crfTy', 'karyawan');

-- Insert Data Barang Sample
INSERT INTO barang (nama_barang, kategori, stok, harga_beli, harga_jual, lokasi_rak) VALUES
('Laptop ASUS ROG', 'Elektronik', 15, 12000000.00, 15000000.00, 'A1'),
('Mouse Logitech', 'Elektronik', 8, 150000.00, 250000.00, 'A2'),
('Keyboard Mechanical', 'Elektronik', 5, 500000.00, 750000.00, 'A3'),
('Monitor LG 24"', 'Elektronik', 12, 2000000.00, 2500000.00, 'B1'),
('Headset Gaming', 'Elektronik', 20, 300000.00, 450000.00, 'B2'),
('Flashdisk 32GB', 'Elektronik', 50, 80000.00, 120000.00, 'C1'),
('Hard Disk 1TB', 'Elektronik', 7, 600000.00, 850000.00, 'C2'),
('Webcam HD', 'Elektronik', 3, 400000.00, 600000.00, 'C3'),
('Speaker Bluetooth', 'Elektronik', 18, 250000.00, 400000.00, 'D1'),
('Power Bank 20000mAh', 'Elektronik', 25, 200000.00, 300000.00, 'D2');

-- Password Hash Info:
-- admin123 = $2y$10$3.CEt6LDw0x/MGnGvhH3c.O/BvwafGQaxjDVZcOReaCEPL9fSMJPi
-- karyawan123 = $2y$10$AT86QzV.tEET.bv4XwI1zO1rodeLvcsQ50V7JGqnLzKdzEi.crfTy