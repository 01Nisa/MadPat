<?php
    include "koneksi.php";

    $id=$_POST['id'];
    $email = $_POST['email'];
    $pw= $_POST['password']
    $no_hp= $_POST['no_hp'];
    $kota = $_POST['kota'];
    $alamat = $_POST['alamat'];
    $waktu = $_POST['waktu'];
    $detail = $_POST['detail'];


    $query = mysqli_query($connect, "UPDATE kirimair SET email='$email', nama='$nama', no_hp='$no_hp', kota='$kota', alamat='$alamat', waktu='$waktu', detail='$detail' WHERE id='$id'");
    if($query){
        echo '<script>
                alert("Your data has been successfully edited");
                window.location.href = "coba.php";
              </script>';
    } else{
        echo '<script>
                alert("Your data failed to edit");
                window.location.href = "coba.php";
             </script>';
    }
?>