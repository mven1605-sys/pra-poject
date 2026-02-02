<?php
session_start();
session_unset();
session_destroy();

header('Location: simple-login.php');
exit();
?>