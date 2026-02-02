<?php
echo "<h1>Debug Information</h1>";

echo "<h2>1. Current Directory</h2>";
echo "Current working directory: " . getcwd() . "<br>";
echo "Script directory: " . __DIR__ . "<br>";

echo "<h2>2. Server Information</h2>";
echo "Server Name: " . $_SERVER['SERVER_NAME'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

echo "<h2>3. File Existence Check</h2>";
$files_to_check = [
    'config/config.php',
    'controllers/AuthController.php',
    'pages/admin/dashboard.php',
    'admin-dashboard.php',
    'login.php'
];

foreach ($files_to_check as $file) {
    $exists = file_exists($file);
    $readable = is_readable($file);
    echo "- $file: " . ($exists ? 'EXISTS' : 'NOT FOUND') . " | " . ($readable ? 'READABLE' : 'NOT READABLE') . "<br>";
}

echo "<h2>4. Config Test</h2>";
try {
    require_once 'config/config.php';
    echo "✅ Config loaded successfully<br>";
    echo "BASE_URL: " . BASE_URL . "<br>";
} catch (Exception $e) {
    echo "❌ Config error: " . $e->getMessage() . "<br>";
}

echo "<h2>5. Database Test</h2>";
try {
    $db = getDB();
    echo "✅ Database connected<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<h2>6. Session Info</h2>";
session_start();
if (isset($_SESSION['user_id'])) {
    echo "User logged in: " . $_SESSION['username'] . " (" . $_SESSION['role'] . ")<br>";
} else {
    echo "No active session<br>";
}

echo "<h2>7. Test Links</h2>";
echo "<a href='admin-dashboard.php'>Test Admin Dashboard</a><br>";
echo "<a href='pages/admin/dashboard.php'>Test Original Dashboard</a><br>";
echo "<a href='login.php'>Login Page</a><br>";
?>