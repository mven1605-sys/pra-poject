<?php
require_once 'config/config.php';

echo "<h2>Test URL Configuration</h2>";
echo "BASE_URL: " . BASE_URL . "<br>";
echo "Current URL: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Server Name: " . $_SERVER['SERVER_NAME'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

$dashboard_url = BASE_URL . 'pages/admin/dashboard.php';
echo "<br>Dashboard URL: " . $dashboard_url . "<br>";

echo "<br><strong>Test Links:</strong><br>";
echo "<a href='" . $dashboard_url . "'>Dashboard (Full URL)</a><br>";
echo "<a href='pages/admin/dashboard.php'>Dashboard (Relative)</a><br>";

echo "<br><strong>File Check:</strong><br>";
echo "Dashboard file exists: " . (file_exists('pages/admin/dashboard.php') ? 'YES' : 'NO') . "<br>";

if (file_exists('pages/admin/dashboard.php')) {
    echo "File size: " . filesize('pages/admin/dashboard.php') . " bytes<br>";
    echo "File permissions: " . substr(sprintf('%o', fileperms('pages/admin/dashboard.php')), -4) . "<br>";
}
?>