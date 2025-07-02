<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../../login.php?pesan=belum_login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/pembayaran.php?pesan=invalid_request");
    exit();
}

if (!isset($_POST['id_pembayaran']) || !isset($_POST['status'])) {
    header("Location: ../pages/pembayaran.php?pesan=invalid_request");
    exit();
}

$id_pembayaran = $_POST['id_pembayaran'];
$status = $_POST['status'];

$allowed_statuses = ['Sudah Bayar', 'Belum Bayar'];
if (!in_array($status, $allowed_statuses)) {
    header("Location: ../pages/pembayaran.php?pesan=invalid_status");
    exit();
}

include '../../../koneksi.php';

$sql = "UPDATE pembayaran SET status_pembayaran = ?, tanggal_pembayaran = ? WHERE id_pembayaran = ?";
$stmt = $connect->prepare($sql);

if (!$stmt) {
    error_log("konfirmasi.php - Prepare failed: " . $connect->error);
    header("Location: ../pages/pembayaran.php?pesan=database_error");
    exit();
}

$tanggal_pembayaran = ($status === 'Sudah Bayar') ? date('Y-m-d H:i:s') : NULL;

$stmt->bind_param("ssi", $status, $tanggal_pembayaran, $id_pembayaran);

if ($stmt->execute()) {
    header("Location: ../pages/pembayaran.php?pesan=status_updated");
} else {
    error_log("konfirmasi.php - Execute failed: " . $stmt->error);
    header("Location: ../pages/pembayaran.php?pesan=database_error");
}

$stmt->close();
$connect->close();
exit();
?>