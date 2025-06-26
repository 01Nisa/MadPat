<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("location:../../../login.php?pesan=belum_login");
    exit();
}

include '../../../koneksi.php';

// Get current user data
$user = $_SESSION['user'];
$current_email = $user['email'];

// Initialize variables
$nama = mysqli_real_escape_string($connect, $_POST['nama']);
$email = mysqli_real_escape_string($connect, $_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Validate email (should match session email)
if ($email != $current_email) {
    header("Location: pengaturan.php?pesan=gagal");
    exit();
}

// Validate password if changed
if (!empty($password)) {
    if ($password != $confirm_password) {
        header("Location: pengaturan.php?pesan=gagal");
        exit();
    }
    // In production, use password_hash():
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $hashed_password = $password; // For now, store plain text (not recommended for production)
}

// Handle file upload
$target_dir = "../../../uploads/";
$foto_name = '';

if (!empty($_FILES['foto']['name'])) {
    // Create upload directory if not exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $original_filename = basename($_FILES["foto"]["name"]);
    $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
    
    // Generate unique filename
    $foto_name = uniqid() . '_' . $original_filename;
    $target_file = $target_dir . $foto_name;
    
    // Check if image file is a actual image
    $check = getimagesize($_FILES["foto"]["tmp_name"]);
    if ($check === false) {
        header("Location: pengaturan.php?pesan=gagal");
        exit();
    }
    
    // Check file size (max 2MB)
    if ($_FILES["foto"]["size"] > 2000000) {
        header("Location: pengaturan.php?pesan=gagal");
        exit();
    }
    
    // Allow certain file formats
    $allowed_types = ["jpg", "jpeg", "png"];
    if (!in_array($imageFileType, $allowed_types)) {
        header("Location: pengaturan.php?pesan=gagal");
        exit();
    }
    
    // Try to upload file
    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
        header("Location: pengaturan.php?pesan=gagal");
        exit();
    }
    
    // Delete old photo if it exists and isn't default.png
    $old_photo_query = mysqli_query($connect, "SELECT foto FROM pengguna WHERE email = '$current_email'");
    $old_photo_data = mysqli_fetch_assoc($old_photo_query);
    $old_photo = $old_photo_data['foto'] ?? '';
    
    if (!empty($old_photo) && $old_photo != 'default.png' && file_exists($target_dir . $old_photo)) {
        unlink($target_dir . $old_photo);
    }
}

// Prepare SQL query
if (!empty($password)) {
    if (!empty($foto_name)) {
        $sql = "UPDATE pengguna SET nama = '$nama', password = '$hashed_password', foto = '$foto_name' WHERE email = '$current_email'";
    } else {
        $sql = "UPDATE pengguna SET nama = '$nama', password = '$hashed_password' WHERE email = '$current_email'";
    }
} else {
    if (!empty($foto_name)) {
        $sql = "UPDATE pengguna SET nama = '$nama', foto = '$foto_name' WHERE email = '$current_email'";
    } else {
        $sql = "UPDATE pengguna SET nama = '$nama' WHERE email = '$current_email'";
    }
}

// Execute query
if (mysqli_query($connect, $sql)) {
    // Update session data
    $_SESSION['user']['nama'] = $nama;
    if (!empty($foto_name)) {
        $_SESSION['user']['foto'] = $foto_name;
    }
    
    header("Location: pengaturan.php?pesan=sukses");
} else {
    // If there was an error, delete the uploaded photo if it exists
    if (!empty($foto_name) && file_exists($target_dir . $foto_name)) {
        unlink($target_dir . $foto_name);
    }
    header("Location: pengaturan.php?pesan=gagal");
}

mysqli_close($connect);
?>