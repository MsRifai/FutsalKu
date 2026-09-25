<?php
if ($_SESSION['role'] != 'admin') {
    header("Location: ../user/dashboard.php");
    exit;
}
?>
