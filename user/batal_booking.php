<?php
require_once '../includes/session.php';
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: " . base_url('login.php'));
    exit;
}

$id_booking = clean($_GET['id'] ?? '');
$id_user = $_SESSION['id_user'];

if (!empty($id_booking)) {
    // Pastikan booking milik user bersangkutan dan statusnya 'menunggu'
    $query = mysqli_query($conn, "SELECT status FROM booking WHERE id_booking='$id_booking' AND id_user='$id_user'");
    $booking = mysqli_fetch_assoc($query);

    if ($booking && $booking['status'] === 'menunggu') {
        mysqli_query($conn, "UPDATE booking SET status='batal' WHERE id_booking='$id_booking'");
        $_SESSION['booking_success'] = "Booking telah berhasil dibatalkan.";
    }
}

header("Location: history.php");
exit;
