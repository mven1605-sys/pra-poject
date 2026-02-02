<?php
echo "<h2>Test Config Access</h2>";

echo "Testing config include...<br>";

try {
    require_once 'config/config.php';
    echo "✅ Config loaded successfully<br>";
    echo "BASE_URL: " . BASE_URL . "<br>";
    
    // Test database
    $db = getDB();
    echo "✅ Database connected<br>";
    
    // Test functions
    echo "✅ Functions loaded<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='create_admin.php'>Create Admin</a> | <a href='test_auth.php'>Test Auth</a>";
?>