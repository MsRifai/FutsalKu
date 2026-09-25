<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . base_url('user/dashboard.php'));
    exit;
}

$id = clean($_GET['id'] ?? '');
$status = clean($_GET['status'] ?? '');
$allowed = ['menunggu', 'disetujui', 'lunas', 'ditolak', 'batal'];

if (!empty($id) && in_array($status, $allowed)) {
    mysqli_query($conn, "UPDATE booking SET status='$status' WHERE id_booking='$id'");
}

header("Location: kelola_booking.php");
exit;