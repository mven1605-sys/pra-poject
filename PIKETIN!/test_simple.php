<?php
echo "<h2>Test Simple Access</h2>";
echo "This file works!<br>";
echo "Current directory: " . __DIR__ . "<br>";
echo "File exists check:<br>";

$files_to_check = [
    'pages/admin/dashboard.php',
    'controllers/AuthController.php',
    'config/config.php'
];

foreach ($files_to_check as $file) {
    $exists = file_exists($file);
    $readable = is_readable($file);
    echo "- $file: " . ($exists ? 'EXISTS' : 'NOT FOUND') . " | " . ($readable ? 'READABLE' : 'NOT READABLE') . "<br>";
}

echo "<br><a href='pages/admin/dashboard.php'>Test Direct Dashboard Access</a><br>";
echo "<a href='login.php'>Back to Login</a>";
?>