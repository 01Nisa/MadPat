<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("location:../../../login.php?pesan=belum_login");
    exit();
}

$user = $_SESSION['user'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../../../koneksi.php';

    $id_pembayaran = $_POST['id_pembayaran'] ?? '';
    $nama_pengirim = $_POST['nama_pengirim'] ?? '';
    $tanggal_pembayaran = $_POST['tanggal_pembayaran'] ?? '';
    $waktu_pembayaran = $_POST['waktu_pembayaran'] ?? '';
    $jenis_pembayaran = $_POST['jenis_pembayaran'] ?? '';
    $total_bayar = str_replace(['.', ','], '', $_POST['total_bayar'] ?? '0');

    $target_dir = "../../../Uploads/";
    $original_filename = basename($_FILES["bukti_pembayaran"]["name"] ?? '');
    $unique_filename = uniqid() . '_' . $original_filename;
    $target_file = $target_dir . $unique_filename;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (empty($id_pembayaran) || empty($nama_pengirim) || empty($tanggal_pembayaran) || 
        empty($waktu_pembayaran) || empty($jenis_pembayaran) || empty($total_bayar)) {
        $_SESSION['notifikasi'] = [
            'status' => 'error',
            'pesan' => 'Semua field harus diisi!'
        ];
        header("Location: ../pages/pembayaran.php");
        exit();
    }

    if (empty($original_filename)) {
        $_SESSION['notifikasi'] = [
            'status' => 'error',
            'pesan' => 'Harap pilih file bukti pembayaran.'
        ];
        header("Location: ../pages/pembayaran.php");
        exit();
    }

    $check = getimagesize($_FILES["bukti_pembayaran"]["tmp_name"]);
    if ($check === false) {
        $_SESSION['notifikasi'] = [
            'status' => 'error',
            'pesan' => 'File bukan gambar.'
        ];
        header("Location: ../pages/pembayaran.php");
        exit();
    }

    if ($_FILES["bukti_pembayaran"]["size"] > 5000000) {
        $_SESSION['notifikasi'] = [
            'status' => 'error',
            'pesan' => 'Maaf, file terlalu besar (maksimal 5MB).'
        ];
        header("Location: ../pages/pembayaran.php");
        exit();
    }

    $allowed_types = ["jpg", "jpeg", "png"];
    if (!in_array($imageFileType, $allowed_types)) {
        $_SESSION['notifikasi'] = [
            'status' => 'error',
            'pesan' => 'Maaf, hanya file JPG, JPEG, dan PNG yang diizinkan.'
        ];
        header("Location: ../pages/pembayaran.php");
        exit();
    }

    if (move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $target_file)) {
        $relative_path = "Uploads/" . $unique_filename;

        $sql = "UPDATE pembayaran SET 
                nama_pengirim = ?, 
                tanggal_pembayaran = ?, 
                waktu_pembayaran = ?, 
                jenis_pembayaran = ?, 
                total_bayar = ?, 
                bukti_pembayaran = ?, 
                status_pembayaran = 'Sudah Bayar'
                WHERE id_pembayaran = ?";
        $stmt = $connect->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssssiss", 
                $nama_pengirim, 
                $tanggal_pembayaran, 
                $waktu_pembayaran, 
                $jenis_pembayaran, 
                $total_bayar, 
                $relative_path, 
                $id_pembayaran
            );
            
            if ($stmt->execute()) {
                $_SESSION['notifikasi'] = [
                    'status' => 'success',
                    'pesan' => 'Pembayaran berhasil disimpan.'
                ];
            } else {
                $_SESSION['notifikasi'] = [
                    'status' => 'error',
                    'pesan' => 'Error saat menyimpan ke database: ' . $stmt->error
                ];
            }
            $stmt->close();
        } else {
            $_SESSION['notifikasi'] = [
                'status' => 'error',
                'pesan' => 'Error preparing statement: ' . $connect->error
            ];
        }
    } else {
        $_SESSION['notifikasi'] = [
            'status' => 'error',
            'pesan' => 'Maaf, terjadi kesalahan saat mengunggah file.'
        ];
    }

    header("Location: ../pages/pembayaran.php");
    exit();
}