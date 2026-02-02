<?php
/**
 * Final Cleanup - Remove all test files
 */

$test_files = [
    'test_simple.php',
    'test_login_simple.php', 
    'test_url.php',
    'test_auth.php',
    'create_admin.php',
    'test_config.php',
    'test_direct_login.php',
    'test_auth_direct.php',
    'test_all.php',
    'cleanup_all.php',
    'pages/admin/dashboard_test.php',
    'setup.php',
    'quick-login.php',
    'cleanup-final.php'
];

echo "<h2>Final Cleanup</h2>";
echo "<p>Menghapus semua file test...</p>";

$deleted = 0;
$failed = 0;

foreach ($test_files as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "✅ Deleted: $file<br>";
            $deleted++;
        } else {
            echo "❌ Failed to delete: $file<br>";
            $failed++;
        }
    }
}

echo "<br><strong>Summary:</strong><br>";
echo "Deleted: $deleted files<br>";
echo "Failed: $failed files<br>";

echo "<br>✅ Cleanup completed!<br>";
echo "<a href='index.php'>Go to Home</a> | <a href='login.php'>Go to Login</a>";
?>