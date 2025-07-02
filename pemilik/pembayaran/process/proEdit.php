<?php
session_start();

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

ob_start();

if (!isset($_SESSION['user'])) {
    error_log("update_payment.php - Unauthorized access: Session user not set");
    $_SESSION['notifikasi'] = [
        'status' => 'error',
        'pesan' => 'Anda harus login untuk melakukan aksi ini.'
    ];
    header("Location: ../pages/pembayaran.php");
    ob_end_flush();
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    error_log("update_payment.php - Invalid CSRF token");
    $_SESSION['notifikasi'] = [
        'status' => 'error',
        'pesan' => 'Token keamanan tidak valid.'
    ];
    header("Location: ../pages/pembayaran.php");
    ob_end_flush();
    exit();
}

include '../../../koneksi.php';

if (!$connect) {
    error_log("update_payment.php - Database connection failed: " . mysqli_connect_error());
    $_SESSION['notifikasi'] = [
        'status' => 'error',
        'pesan' => 'Terjadi kesalahan pada database.'
    ];
    header("Location: ../pages/pembayaran.php");
    ob_end_flush();
    exit();
}

if (!isset($_POST['id_pembayaran']) || !is_numeric($_POST['id_pembayaran']) ||
    !isset($_POST['nama_pengirim']) || empty(trim($_POST['nama_pengirim'])) ||
    !isset($_POST['tanggal_pembayaran']) || empty(trim($_POST['tanggal_pembayaran'])) ||
    !isset($_POST['status_pembayaran']) || !in_array($_POST['status_pembayaran'], ['Sudah Bayar', 'Belum Bayar'])) {
    error_log("update_payment.php - Invalid or missing POST data");
    $_SESSION['notifikasi'] = [
        'status' => 'error',
        'pesan' => 'Data tidak valid.'
    ];
    header("Location: ../pages/pembayaran.php");
    ob_end_flush();
    exit();
}

$payment_id = $_POST['id_pembayaran'];
$nama_pengirim = trim($_POST['nama_pengirim']);
$tanggal_pembayaran = $_POST['tanggal_pembayaran'];
$status_pembayaran = $_POST['status_pembayaran'];

$sql_check = "SELECT id_pembayaran FROM pembayaran WHERE id_pembayaran = ?";
$stmt_check = $connect->prepare($sql_check);
if (!$stmt_check) {
    error_log("update_payment.php - Prepare failed for check query: " . $connect->error);
    $_SESSION['notifikasi'] = [
        'status' => 'error',
        'pesan' => 'Terjadi kesalahan pada database.'
    ];
    header("Location: ../pages/pembayaran.php");
    ob_end_flush();
    exit();
}
$stmt_check->bind_param("i", $payment_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check->num_rows === 0) {
    error_log("update_payment.php - Payment ID not found: $payment_id");
    $_SESSION['notifikasi'] = [
        'status' => 'error',
        'pesan' => 'Pembayaran tidak ditemukan.'
    ];
    header("Location: ../pages/pembayaran.php");
    $stmt_check->close();
    ob_end_flush();
    exit();
}
$stmt_check->close();

$sql = "UPDATE pembayaran SET nama_pengirim = ?, tanggal_pembayaran = ?, status_pembayaran = ? WHERE id_pembayaran = ?";
$stmt = $connect->prepare($sql);
if (!$stmt) {
    error_log("update_payment.php - Prepare failed: " . $connect->error);
    $_SESSION['notifikasi'] = [
        'status' => 'error',
        'pesan' => 'Terjadi kesalahan pada database.'
    ];
    header("Location: ../pages/pembayaran.php");
    ob_end_flush();
    exit();
}
$stmt->bind_param("sssi", $nama_pengirim, $tanggal_pembayaran, $status_pembayaran, $payment_id);
if ($stmt->execute()) {
    error_log("update_payment.php - Payment updated: ID=$payment_id");
    $_SESSION['notifikasi'] = [
        'status' => 'success',
        'pesan' => 'Pembayaran berhasil diperbarui.'
    ];
    header("Location: ../pages/pembayaran.php?pesan=status_updated");
} else {
    error_log("update_payment.php - Update failed: " . $stmt->error);
    $_SESSION['notifikasi'] = [
        'status' => 'error',
        'pesan' => 'Gagal memperbarui pembayaran.'
    ];
    header("Location: ../pages/pembayaran.php");
}
$stmt->close();
$connect->close();
ob_end_flush();
?>