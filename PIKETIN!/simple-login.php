<?php
session_start();

if ($_POST) {
    // Manual login without AuthController
    require_once 'config/config.php';
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    echo "<h2>Login Attempt</h2>";
    echo "Username: $username<br>";
    echo "Password: $password<br>";
    
    try {
        $admin = db_fetch_one("SELECT * FROM tb_admin WHERE username = 'admin' LIMIT 1");
        
        if ($admin) {
            echo "✅ Admin found<br>";
            echo "Admin ID: " . $admin['id_admin'] . "<br>";
            echo "Admin Name: " . $admin['nama_lengkap'] . "<br>";
            
            if (password_verify($password, $admin['password'])) {
                echo "✅ Password correct<br>";
                
                // Set session manually
                $_SESSION['user_id'] = $admin['id_admin'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['nama_lengkap'] = $admin['nama_lengkap'];
                $_SESSION['role'] = 'Admin';
                $_SESSION['foto_profil'] = 'default-avatar.png';
                $_SESSION['login_time'] = time();
                
                echo "✅ Session set<br>";
                echo "<a href='admin-dashboard.php'>Go to Dashboard</a><br>";
                
            } else {
                echo "❌ Password incorrect<br>";
            }
        } else {
            echo "❌ Admin not found<br>";
            echo "<a href='create-admin.php'>Create Admin</a><br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
    
} else {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header">
                        <h3>Simple Login Test</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label>Username:</label>
                                <input type="text" name="username" class="form-control" value="admin" required>
                            </div>
                            <div class="mb-3">
                                <label>Password:</label>
                                <input type="password" name="password" class="form-control" value="admin123" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>
                        
                        <hr>
                        <a href="debug.php" class="btn btn-info">Debug Info</a>
                        <a href="create-admin.php" class="btn btn-success">Create Admin</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php } ?>