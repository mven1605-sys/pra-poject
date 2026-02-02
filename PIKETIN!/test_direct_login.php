<?php
session_start();
require_once 'config/config.php';

echo "<h2>Test Direct Login</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    echo "Attempting login with username: $username<br>";
    
    // Get admin user
    $admin = db_fetch_one("SELECT * FROM tb_admin WHERE username = '" . db_escape($username) . "' AND is_active = 1 LIMIT 1");
    
    if ($admin) {
        echo "✅ Admin user found<br>";
        
        if (password_verify($password, $admin['password'])) {
            echo "✅ Password correct<br>";
            
            // Set session
            $_SESSION['user_id'] = $admin['id_admin'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['nama_lengkap'] = $admin['nama_lengkap'];
            $_SESSION['role'] = 'Admin';
            $_SESSION['foto_profil'] = $admin['foto_profil'] ?? 'default-avatar.png';
            $_SESSION['login_time'] = time();
            
            echo "✅ Session set<br>";
            
            $redirect_url = BASE_URL . 'pages/admin/dashboard_test.php';
            echo "Redirecting to: $redirect_url<br>";
            
            echo "<script>setTimeout(function(){ window.location.href = '$redirect_url'; }, 2000);</script>";
            echo "<a href='$redirect_url'>Click here if not redirected</a>";
            
        } else {
            echo "❌ Password incorrect<br>";
        }
    } else {
        echo "❌ Admin user not found<br>";
    }
} else {
?>

<form method="POST">
    <div>
        <label>Username:</label>
        <input type="text" name="username" value="admin" required>
    </div>
    <div>
        <label>Password:</label>
        <input type="password" name="password" value="admin123" required>
    </div>
    <button type="submit">Direct Login</button>
</form>

<br>
<a href="test_config.php">Test Config</a> | 
<a href="create_admin.php">Create Admin</a>

<?php } ?>