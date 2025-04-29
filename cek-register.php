<?php

    include "koneksi.php";

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $query = 

    $erros = [];
    if ($password !== $confirm_password) {
        $errors[] = "Password dan konfirmasi password tidak cocok";
    }

    if (empty($errors)) {
        $query = $sambungan->prepare("INSERT INTO users (name, email, password) VALUES (?,?,?)");
        $query ->bind_param("sss", $name, $email, $password);

        if ($query->execute()){
            echo '<script>
                    alert("Login gaga email dan password salah!");
                    </script>';

            // <!-- echo "<div class='alert alert-success'>Pendaftaran berhasil!"; -->
        }else {
            echo "<div class='alert alert-danger'> Pendaftaran gagal" . $quert->error. "</div>";          
        }

        $query->close();
        $sambungan->close();
    }else{
        foreach ($errors as $error){
            echo "<div class = 'alert-danger'>$error</div>";
        }
    }

?>
