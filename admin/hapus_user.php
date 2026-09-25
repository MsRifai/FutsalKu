<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . base_url('user/dashboard.php'));
    exit;
}

$id = clean($_GET['id'] ?? '');

if (!empty($id)) {
    mysqli_query($conn, "DELETE FROM users WHERE id_user='$id' AND role='user'");
}

header("Location: kelola_user.php");
exit;
