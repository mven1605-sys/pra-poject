<?php
session_start();

// Set session manually untuk test
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['nama_lengkap'] = 'Administrator';
$_SESSION['role'] = 'Admin';
$_SESSION['foto_profil'] = 'default-avatar.png';
$_SESSION['login_time'] = time();

echo "<h2>Test Login Session Set</h2>";
echo "Session has been set manually.<br>";
echo "User ID: " . $_SESSION['user_id'] . "<br>";
echo "Role: " . $_SESSION['role'] . "<br>";

echo "<br><a href='pages/admin/dashboard.php'>Test Dashboard Access</a><br>";
echo "<a href='controllers/AuthController.php?action=logout'>Logout</a>";
?>