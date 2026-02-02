<?php
$test_files = [
    'debug.php',
    'simple-login.php',
    'create-admin.php',
    'simple-dashboard.php',
    'logout.php',
    'test-complete.php',
    'cleanup.php'
];

echo "<h2>Cleanup Test Files</h2>";

if ($_GET['confirm'] == 'yes') {
    foreach ($test_files as $file) {
        if (file_exists($file)) {
            if (unlink($file)) {
                echo "✅ Deleted: $file<br>";
            } else {
                echo "❌ Failed: $file<br>";
            }
        }
    }
    echo "<br>✅ Cleanup completed!<br>";
    echo "<a href='index.php'>Go to Home</a>";
} else {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cleanup</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-warning">
                <h3>Cleanup Test Files</h3>
            </div>
            <div class="card-body">
                <p>Apakah Anda yakin ingin menghapus semua file test?</p>
                <ul>
                    <?php foreach ($test_files as $file): ?>
                    <li><?php echo $file; ?></li>
                    <?php endforeach; ?>
                </ul>
                
                <a href="cleanup.php?confirm=yes" class="btn btn-danger">Ya, Hapus Semua</a>
                <a href="test-complete.php" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php } ?>