<?php
echo "<h2>Test AuthController Access</h2>";

echo "<strong>File Check:</strong><br>";
echo "AuthController exists: " . (file_exists('controllers/AuthController.php') ? 'YES' : 'NO') . "<br>";

if (file_exists('controllers/AuthController.php')) {
    echo "File size: " . filesize('controllers/AuthController.php') . " bytes<br>";
    echo "File readable: " . (is_readable('controllers/AuthController.php') ? 'YES' : 'NO') . "<br>";
}

echo "<br><strong>Test Form:</strong><br>";
?>

<form method="POST" action="controllers/AuthController.php">
    <input type="hidden" name="action" value="login">
    <input type="hidden" name="role" value="admin">
    
    <div>
        <label>Username:</label>
        <input type="text" name="username" value="admin" required>
    </div>
    
    <div>
        <label>Password:</label>
        <input type="password" name="password" value="admin123" required>
    </div>
    
    <button type="submit">Test Login</button>
</form>

<br>
<a href="test_simple.php">Test Simple</a> | 
<a href="test_url.php">Test URL</a> | 
<a href="login.php">Login Page</a>