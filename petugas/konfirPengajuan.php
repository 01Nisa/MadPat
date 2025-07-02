<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php?pesan=belum_login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: beranda.php?pesan=invalid_request");
    exit();
}

if (!isset($_POST['id_pengajuan']) || !isset($_POST['status'])) {
    header("Location: beranda.php?pesan=invalid_request");
    exit();
}

$id_pengajuan = $_POST['id_pengajuan'];
$status = $_POST['status'];

$status_pengajuan = ($status === 'Diterima') ? 'Verifikasi' : 'Menunggu Verifikasi';
$allowed_statuses = ['Verifikasi', 'Menunggu Verifikasi'];
if (!in_array($status_pengajuan, $allowed_statuses)) {
    header("Location: beranda.php?pesan=invalid_status");
    exit();
}

include '../koneksi.php';

$sql_check = "SELECT tanggal_pengajuan FROM pengajuan WHERE id_pengajuan = ?";
$stmt_check = $connect->prepare($sql_check);
if (!$stmt_check) {
    error_log("konfirPengajuan.php - Prepare check failed: " . $connect->error);
    header("Location: beranda.php?pesan=database_error");
    exit();
}
$stmt_check->bind_param("s", $id_pengajuan);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check->num_rows === 0) {
    $stmt_check->close();
    header("Location: beranda.php?pesan=pengajuan_not_found");
    exit();
}
$row_check = $result_check->fetch_assoc();
$existing_tanggal_pengajuan = $row_check['tanggal_pengajuan'];
$stmt_check->close();

$sql = "UPDATE pengajuan SET status_pengajuan = ?, tanggal_pengajuan = ? WHERE id_pengajuan = ?";
$stmt = $connect->prepare($sql);
if (!$stmt) {
    error_log("konfirPengajuan.php - Prepare failed: " . $connect->error);
    header("Location: beranda.php?pesan=database_error");
    exit();
}

$tanggal_pengajuan = ($status_pengajuan === 'Verifikasi') ? date('Y-m-d H:i:s') : $existing_tanggal_pengajuan;

$stmt->bind_param("sss", $status_pengajuan, $tanggal_pengajuan, $id_pengajuan);

if ($stmt->execute()) {
    header("Location: beranda.php?pesan=status_pengajuan_updated");
} else {
    error_log("konfirPengajuan.php - Execute failed: " . $stmt->error);
    header("Location: beranda.php?pesan=database_error");
}

$stmt->close();
$connect->close();
exit();
?>