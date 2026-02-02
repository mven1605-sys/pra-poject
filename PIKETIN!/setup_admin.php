<?php
require_once 'config/config.php';

echo "<h2>Setup Admin User</h2>";

try {
    // Delete existing admin if any
    db_query("DELETE FROM tb_admin WHERE username = 'admin'");
    
    // Create new admin
    $password_hash = password_hash('admin123', PASSWORD_BCRYPT);
    $query = "INSERT INTO tb_admin (username, password, nama_lengkap, email, is_active) 
              VALUES ('admin', '$password_hash', 'Administrator', 'admin@smkn2.sch.id', 1)";
    
    if (db_query($query)) {
        echo "✅ Admin user created successfully!<br>";
        echo "<strong>Login Credentials:</strong><br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        
        // Test the password
        $admin = db_fetch_one("SELECT * FROM tb_admin WHERE username = 'admin'");
        if ($admin && password_verify('admin123', $admin['password'])) {
            echo "✅ Password verification works!<br>";
        } else {
            echo "❌ Password verification failed!<br>";
        }
        
    } else {
        echo "❌ Failed to create admin user<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='login.php'>Go to Login</a> | <a href='test_direct_login.php'>Test Direct Login</a>";
?>