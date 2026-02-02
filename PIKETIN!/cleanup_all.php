<?php
echo "<h2>Cleanup All Test Files</h2>";

$test_files = [
    'test_simple.php',
    'test_login_simple.php',
    'test_url.php',
    'test_auth.php',
    'create_admin.php',
    'test_config.php',
    'test_direct_login.php',
    'test_auth_direct.php',
    'setup_admin.php',
    'test_all.php',
    'pages/admin/dashboard_test.php',
    'cleanup_all.php'
];

foreach ($test_files as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "✅ Deleted: $file<br>";
        } else {
            echo "❌ Failed to delete: $file<br>";
        }
    }
}

echo "<br>✅ Cleanup completed!<br>";
echo "<a href='login.php'>Go to Login</a>";
?>