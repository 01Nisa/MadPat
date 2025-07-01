<?php
session_start();
include "koneksi.php";

$name = $_POST['nama'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$nomortlp = $_POST['nomortlp'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$konfirpassword = $_POST['konfirpassword'] ?? '';

$errors = [];

if ($password !== $konfirpassword) {
    $errors[] = "Password dan konfirmasi password tidak cocok.";
}

if (empty($name) || empty($alamat) || empty($nomortlp) || empty($email) || empty($password)) {
    $errors[] = "Semua field harus diisi.";
}

if (strlen($password) > 20) {
    $errors[] = "Password tidak boleh lebih dari 20 karakter.";
}

$stmt_check = $connect->prepare("SELECT id_pengguna FROM pengguna WHERE email = ?");
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check->num_rows > 0) {
    $errors[] = "Email sudah terdaftar.";
}
$stmt_check->close();

if (empty($errors)) {
    $connect->begin_transaction();

    try {
        $source_file = "assets/profil.jpg";
        $target_dir = "Uploads/";
        $default_photo = "profil.jpg"; 

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $unique_filename = uniqid() . '_profil.jpg';
        $target_file = $target_dir . $unique_filename;

        if (!file_exists($source_file)) {
            throw new Exception("File profil.jpg tidak ditemukan di assets/imgs/.");
        }

        if (copy($source_file, $target_file)) {
            $relative_path = "Uploads/" . $unique_filename;
        } else {
            $relative_path = $default_photo; 
            error_log("Failed to copy profil.jpg to $target_file");
        }

        $stmt_users = $connect->prepare("INSERT INTO pengguna (email, password, nama, alamat, nomortlp, foto) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt_users) {
            throw new Exception("Gagal mempersiapkan query: " . $connect->error);
        }
        $stmt_users->bind_param("ssssss", $email, $password, $name, $alamat, $nomortlp, $relative_path);
        if (!$stmt_users->execute()) {
            throw new Exception("Gagal mengeksekusi query: " . $stmt_users->error);
        }

        $connect->commit();

        echo '<script>
                alert("Pendaftaran berhasil! Silakan login.");
                window.location.href = "login.php";
              </script>';

    } catch (Exception $e) {
        $connect->rollback();
        echo "<div class='alert alert-danger'>Pendaftaran gagal: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

    $stmt_users->close();
} else {
    foreach ($errors as $error) {
        echo "<div class='alert alert-danger'>" . htmlspecialchars($error) . "</div>";
    }
}

$connect->close();
?>