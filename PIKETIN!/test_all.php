<?php
echo "<h2>Test All Components</h2>";

echo "<h3>1. File Existence Check</h3>";
$files = [
    'config/config.php' => 'Config file',
    'pages/admin/dashboard.php' => 'Dashboard original',
    'pages/admin/dashboard_test.php' => 'Dashboard test',
    'login_handler.php' => 'Login handler',
    'login.php' => 'Login page'
];

foreach ($files as $file => $desc) {
    $exists = file_exists($file);
    echo "- $desc: " . ($exists ? '✅ EXISTS' : '❌ MISSING') . "<br>";
}

echo "<h3>2. Config Test</h3>";
try {
    require_once 'config/config.php';
    echo "✅ Config loaded<br>";
    echo "BASE_URL: " . BASE_URL . "<br>";
} catch (Exception $e) {
    echo "❌ Config error: " . $e->getMessage() . "<br>";
}

echo "<h3>3. Database Test</h3>";
try {
    $db = getDB();
    echo "✅ Database connected<br>";
    
    // Check admin table
    $admin_count = db_fetch_one("SELECT COUNT(*) as count FROM tb_admin");
    echo "Admin users: " . ($admin_count['count'] ?? 0) . "<br>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<h3>4. Quick Actions</h3>";
echo "<a href='setup_admin.php' class='btn'>Setup Admin</a> | ";
echo "<a href='login.php' class='btn'>Login Page</a> | ";
echo "<a href='pages/admin/dashboard_test.php' class='btn'>Test Dashboard</a>";

echo "<style>.btn { display: inline-block; padding: 8px 16px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 4px; }</style>";
?>