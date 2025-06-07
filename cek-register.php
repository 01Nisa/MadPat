<?php
    include "koneksi.php";

    $name           = $_POST['nama'] ?? '';
    $alamat         = $_POST['alamat'] ?? '';
    $nomortlp       = $_POST['nomortlp'] ?? '';
    $email          = $_POST['email'] ?? '';
    $password       = $_POST['password'] ?? '';
    $konfirpassword = $_POST['konfirpassword'] ?? '';

    $errors = [];

    if ($password !== $konfirpassword) {
        $errors[] = "Password dan konfirmasi password tidak cocok.";
    }

    if (empty($errors)) {
        $plainPassword = $password;

        $connect->begin_transaction();

        try {
            $stmtUsers = $connect->prepare("INSERT INTO users (nama, alamat, nomortlp) VALUES (?, ?, ?)");
            $stmtUsers->bind_param("sss", $name, $alamat, $nomortlp);
            $stmtUsers->execute();

            $stmtLogin = $connect->prepare("INSERT INTO login (email, password) VALUES (?, ?)");
            $stmtLogin->bind_param("ss", $email, $plainPassword);
            $stmtLogin->execute();

            $connect->commit();

            echo '<script>
                    alert("Pendaftaran berhasil! Silakan login.");
                    window.location.href = "login.php";
                  </script>';

        } catch (Exception $e) {
            $connect->rollback();
            echo "<div class='alert alert-danger'>Pendaftaran gagal: " . $e->getMessage() . "</div>";
        }

        $stmtUsers->close();
        $stmtLogin->close();
        $connect->close();

    } else {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
?>
