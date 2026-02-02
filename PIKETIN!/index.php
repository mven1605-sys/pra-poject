<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIKETIN - Sistem Absensi & Monitoring Piket SMK Negeri 2</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 24px;
        }
        
        .navbar-brand i {
            margin-right: 10px;
        }
        
        .btn-login-nav {
            background: white;
            color: #667eea;
            padding: 10px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-login-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 100px 0;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,144C960,149,1056,139,1152,122.7C1248,107,1344,85,1392,74.7L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
        }
        
        .hero-title {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
            animation: fadeInUp 1s ease-out;
        }
        
        .hero-subtitle {
            font-size: 20px;
            margin-bottom: 30px;
            opacity: 0.9;
            animation: fadeInUp 1s ease-out 0.2s backwards;
        }
        
        .hero-description {
            font-size: 16px;
            margin-bottom: 40px;
            line-height: 1.8;
            opacity: 0.85;
            animation: fadeInUp 1s ease-out 0.4s backwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .btn-get-started {
            background: white;
            color: #667eea;
            padding: 15px 40px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 18px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            animation: fadeInUp 1s ease-out 0.6s backwards;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-get-started:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
            color: #667eea;
        }
        
        .hero-illustration {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        /* Features Section */
        .features-section {
            padding: 80px 0;
            background: #f8f9fc;
        }
        
        .section-title {
            text-align: center;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #333;
        }
        
        .section-subtitle {
            text-align: center;
            font-size: 18px;
            color: #666;
            margin-bottom: 60px;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.2);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }
        
        .feature-icon i {
            font-size: 36px;
            color: white;
        }
        
        .feature-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #333;
        }
        
        .feature-description {
            font-size: 15px;
            color: #666;
            line-height: 1.6;
        }
        
        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px 0;
            color: white;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 16px;
            opacity: 0.9;
        }
        
        /* Footer */
        .footer {
            background: #2d3748;
            color: white;
            padding: 40px 0 20px;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .footer-logo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .footer-text {
            font-size: 14px;
            opacity: 0.8;
            line-height: 1.6;
        }
        
        .footer-links h4 {
            font-size: 18px;
            margin-bottom: 15px;
        }
        
        .footer-links ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-links ul li {
            margin-bottom: 10px;
        }
        
        .footer-links ul li a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            opacity: 0.8;
            transition: all 0.3s;
        }
        
        .footer-links ul li a:hover {
            opacity: 1;
            padding-left: 5px;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            text-align: center;
            font-size: 14px;
            opacity: 0.8;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 32px;
            }
            
            .hero-subtitle {
                font-size: 18px;
            }
            
            .section-title {
                font-size: 28px;
            }
            
            .footer-content {
                flex-direction: column;
                gap: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center w-100">
                <a class="navbar-brand" href="#">
                    <i class="fas fa-clipboard-check"></i>
                    PIKETIN
                </a>
                <a href="login.php" class="btn btn-login-nav">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">Sistem Absensi Piket Digital</h1>
                        <p class="hero-subtitle">SMK Negeri 2 - Lebih Modern, Lebih Efisien</p>
                        <p class="hero-description">
                            PIKETIN adalah solusi digital untuk monitoring dan absensi piket siswa secara real-time. 
                            Memudahkan guru, siswa, dan admin dalam mengelola jadwal dan laporan piket dengan lebih efisien dan terorganisir.
                        </p>
                        <a href="login.php" class="btn-get-started">
                            <i class="fas fa-rocket"></i> Mulai Sekarang
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-illustration">
                        <i class="fas fa-users-cog" style="font-size: 250px; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Fitur Unggulan</h2>
            <p class="section-subtitle">Solusi lengkap untuk manajemen piket sekolah</p>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-camera"></i>
                        </div>
                        <h3 class="feature-title">Absensi Foto</h3>
                        <p class="feature-description">
                            Sistem absensi dengan upload foto anggota dan area kelas yang dilengkapi GPS dan timestamp real-time.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="feature-title">Jadwal Otomatis</h3>
                        <p class="feature-description">
                            Pembuatan jadwal piket otomatis untuk seluruh kelas dengan sistem multiple student per jadwal.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Laporan Real-time</h3>
                        <p class="feature-description">
                            Monitoring dan laporan absensi mingguan/bulanan yang dapat dicetak kapan saja dengan format rapi.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3 class="feature-title">Notifikasi Otomatis</h3>
                        <p class="feature-description">
                            Notifikasi WhatsApp otomatis untuk siswa yang alpha atau tidak melakukan absensi piket.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3 class="feature-title">Multi Role Access</h3>
                        <p class="feature-description">
                            Sistem akses bertingkat untuk Admin, Guru, dan Siswa dengan fitur yang disesuaikan untuk setiap role.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <h3 class="feature-title">Manajemen Data</h3>
                        <p class="feature-description">
                            Kelola data siswa, guru, kelas, dan jurusan dengan sistem CRUD yang mudah dan terorganisir.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-item">
                        <div class="stat-number"><i class="fas fa-users"></i> 500+</div>
                        <div class="stat-label">Siswa Aktif</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-item">
                        <div class="stat-number"><i class="fas fa-chalkboard-teacher"></i> 50+</div>
                        <div class="stat-label">Guru</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-item">
                        <div class="stat-number"><i class="fas fa-door-open"></i> 15+</div>
                        <div class="stat-label">Kelas</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><i class="fas fa-check-circle"></i> 100%</div>
                        <div class="stat-label">Digital</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="footer-logo">
                        <i class="fas fa-clipboard-check"></i> PIKETIN
                    </div>
                    <p class="footer-text">
                        Sistem Absensi & Monitoring Piket Digital untuk SMK Negeri 2. 
                        Membantu meningkatkan kedisiplinan dan efisiensi pengelolaan piket sekolah.
                    </p>
                </div>
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="footer-links">
                        <h4>Menu Cepat</h4>
                        <ul>
                            <li><a href="index.php"><i class="fas fa-home"></i> Beranda</a></li>
                            <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                            <li><a href="help.php"><i class="fas fa-question-circle"></i> Bantuan</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="footer-links">
                        <h4>Kontak</h4>
                        <ul>
                            <li><i class="fas fa-map-marker-alt"></i> SMK Negeri 2, Surabaya</li>
                            <li><i class="fas fa-phone"></i> (031) 1234-5678</li>
                            <li><i class="fas fa-envelope"></i> info@smkn2.sch.id</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; 2025 PIKETIN - SMK Negeri 2. All Rights Reserved.
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>