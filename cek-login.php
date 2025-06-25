<?php
session_start();
include "koneksi.php";

$email = $_POST['email'];
$password = $_POST['password'];

$query = mysqli_query($connect, "SELECT* FROM pengguna WHERE email = '$email' && password = '$password'") or die (mysqli_error($connect));

$cek = mysqli_num_rows($query);

if ($cek > 0) {
    $_SESSION['email'] = $email;
    $_SESSION['password'] = $password;

    if ($email == 'kurirmadpat123@gmail.com' && $password = 'kurir123**') {
        header("Location: kurir/beranda.php");
    } elseif ($email == 'petugasmadpat123@gmail.com' && $password = 'petugas123**') {
        header("Location: petugas/beranda.php");
    } elseif ($email == 'pemilikmadpat123@gmail.com'&& $password = 'pemilik123**') {
        header("Location: pemilik/beranda.php");
    } else {
        header("Location: pelanggan/beranda.php");
    }
} else {
    header("Location: login.php?pesan=gagal");
}
?>
