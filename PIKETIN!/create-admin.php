<?php
require_once 'config/config.php';

echo "<h2>Create Admin User</h2>";

if ($_POST) {
    try {
        // Delete existing admin
        db_query("DELETE FROM tb_admin WHERE username = 'admin'");
        
        // Create new admin
        $password_hash = password_hash('admin123', PASSWORD_BCRYPT);
        $query = "INSERT INTO tb_admin (username, password, nama_lengkap, email, is_active) 
                  VALUES ('admin', '$password_hash', 'Administrator', 'admin@smkn2.sch.id', 1)";
        
        if (db_query($query)) {
            echo "✅ Admin created successfully!<br>";
            echo "Username: admin<br>";
            echo "Password: admin123<br>";
            echo "<a href='simple-login.php'>Test Login</a><br>";
        } else {
            echo "❌ Failed to create admin<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
} else {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header">
                        <h3>Create Admin User</h3>
                    </div>
                    <div class="card-body">
                        <p>Click button below to create admin user with:</p>
                        <ul>
                            <li>Username: admin</li>
                            <li>Password: admin123</li>
                        </ul>
                        
                        <form method="POST">
                            <button type="submit" class="btn btn-success">Create Admin</button>
                        </form>
                        
                        <hr>
                        <a href="debug.php" class="btn btn-info">Debug Info</a>
                        <a href="simple-login.php" class="btn btn-primary">Test Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php } ?>