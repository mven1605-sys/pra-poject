<?php
/**
 * Logout System - Stockify
 * Menghapus semua session dan redirect ke login
 */

session_start();

// Hapus semua session variables
$_SESSION = array();

// Destroy session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy session
session_destroy();

// Redirect ke halaman login
header("Location: login.php?logout=1");
exit();
?>