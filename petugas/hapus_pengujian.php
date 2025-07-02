<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt1 = $connect->prepare("DELETE FROM detail_pembayaran WHERE id_pengujian = ?");
    $stmt1->bind_param("s", $id);
    $stmt1->execute();
    $stmt1->close();

    $stmt2 = $connect->prepare("DELETE FROM pengujian WHERE id_pengujian = ?");
    $stmt2->bind_param("s", $id);

    if ($stmt2->execute()) {
        header("Location: pengujian.php?status=hapus_sukses");
    } else {
        header("Location: pengujian.php?status=gagal_db");
    }

    $stmt2->close();
}

$connect->close();
?>
