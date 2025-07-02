<?php
session_start();

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

ob_start();

if (!isset($_SESSION['user'])) {
    error_log("delete.php - Unauthorized access: Session user not set");
    header("Location: ../pages/pembayaran.php?pesan=unauthorized");
    ob_end_flush();
    exit();
}

include '../../../koneksi.php';

if (!$connect) {
    error_log("delete.php - Database connection failed: " . mysqli_connect_error());
    header("Location: ../pages/pembayaran.php?pesan=database_error");
    ob_end_flush();
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    error_log("delete.php - Invalid or missing payment ID");
    header("Location: ../pages/pembayaran.php?pesan=invalid_request");
    ob_end_flush();
    exit();
}

$payment_id = $_GET['id'];

$connect->begin_transaction();

try {
    $sql_check = "SELECT id_pembayaran FROM pembayaran WHERE id_pembayaran = ?";
    $stmt_check = $connect->prepare($sql_check);
    if (!$stmt_check) {
        error_log("delete.php - Prepare failed for check query: " . $connect->error);
        throw new Exception('database_error');
    }
    $stmt_check->bind_param("i", $payment_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows === 0) {
        error_log("delete.php - Payment ID not found: $payment_id");
        throw new Exception('payment_not_found');
    }
    $stmt_check->close();

    $sql_detail = "DELETE FROM detail_pembayaran WHERE id_pembayaran = ?";
    $stmt_detail = $connect->prepare($sql_detail);
    if (!$stmt_detail) {
        error_log("delete.php - Prepare failed for detail_pembayaran delete: " . $connect->error);
        throw new Exception('database_error');
    }
    $stmt_detail->bind_param("i", $payment_id);
    $stmt_detail->execute();
    $stmt_detail->close();

    $sql = "DELETE FROM pembayaran WHERE id_pembayaran = ?";
    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        error_log("delete.php - Prepare failed for payment delete: " . $connect->error);
        throw new Exception('database_error');
    }
    $stmt->bind_param("i", $payment_id);
    if (!$stmt->execute()) {
        if ($stmt->errno == 1451) { 
            error_log("delete.php - Foreign key constraint violation for payment ID: $payment_id");
            throw new Exception('foreign_key_error');
        }
        error_log("delete.php - Delete failed: " . $stmt->error);
        throw new Exception('database_error');
    }
    $stmt->close();

    $connect->commit();
    error_log("delete.php - Payment deleted: ID=$payment_id");
    header("Location: ../pages/pembayaran.php?pesan=payment_deleted");

} catch (Exception $e) {
    $connect->rollback();
    error_log("delete.php - Error: " . $e->getMessage());
    header("Location: ../pages/pembayaran.php?pesan=" . $e->getMessage());
}

$connect->close();
ob_end_flush();
?>