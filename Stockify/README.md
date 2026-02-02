# 📦 Stockify - Sistem Manajemen Warehouse

Sistem manajemen warehouse sederhana untuk mengelola stok barang dengan fitur authentication dan role-based access.

## 🚀 Fitur

- **Authentication System**: Login/Register dengan role Admin dan Karyawan
- **Dashboard**: Overview stok, statistik, dan quick actions
- **Manajemen Barang**: CRUD barang (Admin), Update stok (Karyawan)
- **Analytics**: Grafik dan analisis stok
- **Export**: Export data ke Excel dan PDF
- **Responsive Design**: Bootstrap 5 UI yang mobile-friendly

## 👥 Role & Permissions

### Admin
- ✅ Tambah/Edit/Hapus barang
- ✅ Lihat semua data dan analytics
- ✅ Export laporan
- ✅ Kelola stok

### Karyawan
- ✅ Lihat data barang
- ✅ Update stok barang
- ✅ Lihat analytics terbatas

## 🛠️ Instalasi

1. **Persiapan Server**
   ```bash
   # Pastikan XAMPP/WAMP sudah terinstall dan running
   # Apache dan MySQL harus aktif
   ```

2. **Setup Database**
   ```sql
   # Import file stockify_database.sql ke MySQL
   # Atau jalankan query di phpMyAdmin
   ```

3. **Konfigurasi**
   ```php
   # Edit koneksi.php jika perlu (default: localhost, root, no password)
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'stockify');
   ```

4. **Akses Aplikasi**
   ```
   http://localhost/Stockify/
   ```

## 🔐 Default Login

### Admin
- **Username**: `admin`
- **Password**: `admin123`

### Karyawan
- **Username**: `karyawan1`
- **Password**: `karyawan123`

## 📁 Struktur File

```
Stockify/
├── login.php              # Halaman login
├── dashboard.php          # Dashboard utama
├── barang.php            # Manajemen data barang
├── analytics.php         # Analytics dan grafik
├── update_stock.php      # Update stok barang
├── export_excel.php      # Export ke Excel
├── cetak_laporan.php     # Cetak laporan PDF
├── register.php          # Registrasi user baru
├── forgot_password.php   # Reset password
├── logout.php            # Logout
├── koneksi.php           # Konfigurasi database
├── stockify_database.sql # Database schema
├── index.php             # Redirect ke login
├── .htaccess             # Apache configuration
└── README.md             # Dokumentasi
```

## 🔧 Troubleshooting

### Error 404 / File Not Found
1. Pastikan folder berada di `htdocs` (XAMPP) atau `www` (WAMP)
2. Akses melalui `http://localhost/Stockify/`
3. Pastikan Apache sudah running

### Database Connection Error
1. Pastikan MySQL sudah running
2. Cek konfigurasi di `koneksi.php`
3. Import `stockify_database.sql`

### Login Gagal
1. Pastikan database sudah ter-import
2. Gunakan kredensial default yang sudah disediakan
3. Cek apakah password hash sudah benar

## 📝 Changelog

### v1.0.0
- ✅ Sistem authentication lengkap
- ✅ Dashboard dengan statistik
- ✅ CRUD barang untuk admin
- ✅ Update stok untuk karyawan
- ✅ Export Excel dan PDF
- ✅ Analytics dengan grafik
- ✅ Responsive design
- ✅ Security improvements

## 🤝 Kontribusi

Silakan buat issue atau pull request untuk perbaikan dan fitur baru.

## 📄 Lisensi

MIT License - bebas digunakan untuk keperluan apapun.