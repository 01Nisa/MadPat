<?php
session_start();
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($connect, "SELECT * FROM pengguna WHERE email = '$email' AND password = '$password'") or die(mysqli_error($connect));
    
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        
        $_SESSION['user'] = [
            'id' => $user['id_pengguna'],
            'nama' => $user['nama'], 
            'email' => $user['email']
        ];
        
        if ($email == 'kurirmadpat123@gmail.com') {
            $_SESSION['user']['role'] = 'kurir';
            header("Location: kurir/beranda.php");
        } 
        elseif ($email == 'petugasmadpat123@gmail.com') {
            $_SESSION['user']['role'] = 'petugas';
            header("Location: petugas/beranda.php");
        }
        elseif ($email == 'pemilikmadpat123@gmail.com') {
            $_SESSION['user']['role'] = 'pemilik';
            header("Location: pemilik/beranda.php");
        }
        else {
            $_SESSION['user']['role'] = 'pelanggan';
            header("Location: pelanggan/beranda.php");
        }
        exit();
    } else {
        header("Location: login.php?pesan=gagal");
        exit();
    }
}
?>