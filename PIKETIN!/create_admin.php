<?php
require_once 'config/config.php';

echo "<h2>Create Admin User</h2>";

try {
    // Check if admin exists
    $admin = db_fetch_one("SELECT * FROM tb_admin WHERE username = 'admin'");
    
    if ($admin) {
        echo "Admin user already exists.<br>";
        echo "ID: " . $admin['id_admin'] . "<br>";
        echo "Username: " . $admin['username'] . "<br>";
        echo "Name: " . $admin['nama_lengkap'] . "<br>";
        echo "Active: " . ($admin['is_active'] ? 'Yes' : 'No') . "<br>";
    } else {
        echo "Creating admin user...<br>";
        
        // Create admin with known password
        $password_hash = password_hash('admin123', PASSWORD_BCRYPT);
        $query = "INSERT INTO tb_admin (username, password, nama_lengkap, email, is_active) 
                  VALUES ('admin', '$password_hash', 'Administrator', 'admin@smkn2.sch.id', 1)";
        
        if (db_query($query)) {
            echo "✅ Admin user created successfully!<br>";
            echo "Username: admin<br>";
            echo "Password: admin123<br>";
        } else {
            echo "❌ Failed to create admin user<br>";
        }
    }
    
    // Test password
    if ($admin) {
        $test_passwords = ['password', 'admin123', 'admin'];
        foreach ($test_passwords as $test_pass) {
            if (password_verify($test_pass, $admin['password'])) {
                echo "✅ Password '$test_pass' works!<br>";
                break;
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='test_auth.php'>Test Auth</a> | <a href='login.php'>Login</a>";
?>