<?php
echo "<h2>Test AuthController Direct Access</h2>";

// Test if we can include AuthController
echo "Testing AuthController include...<br>";

try {
    // Change to controllers directory context
    chdir('controllers');
    include 'AuthController.php';
    echo "✅ AuthController included successfully<br>";
} catch (Exception $e) {
    echo "❌ Error including AuthController: " . $e->getMessage() . "<br>";
}

echo "<br><a href='test_direct_login.php'>Test Direct Login</a>";
?>