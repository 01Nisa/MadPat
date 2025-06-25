<?php
session_start();
include '../../../koneksi.php';

$id_users = $_SESSION['id_users']; 

$sql = "SELECT u.nama, u.negara, u.kota, u.alamat, u.no_telepon, l.email 
        FROM users u 
        JOIN login l ON u.id = l.id_users 
        WHERE u.id = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $id_users);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $negara = $_POST['negara'];
    $kota = $_POST['kota'];
    $alamat = $_POST['alamat'];
    $email = $_POST['email'];
    $no_telepon = $_POST['no_telepon'];

    // Handle profile picture upload
    $foto = $user['foto'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/"; // Create this directory in your project
        $target_file = $target_dir . basename($_FILES["foto"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = array('jpg', 'jpeg', 'png');

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                $foto = $target_file;
            }
        }
    }

    // Handle photo deletion
    if (isset($_POST['delete_photo']) && !empty($user['foto'])) {
        unlink($user['foto']); // Delete the existing photo
        $foto = 'assets/imgs/default.png';
    }

    // Update user data
    $sql_update = "UPDATE users u 
                   JOIN login l ON u.id = l.id_users 
                   SET u.nama = ?, u.negara = ?, u.kota = ?, u.alamat = ?, u.no_telepon = ?, u.foto = ?, l.email = ? 
                   WHERE u.id = ?";
    $stmt_update = $connect->prepare($sql_update);
    $stmt_update->bind_param("sssssssi", $nama, $negara, $kota, $alamat, $no_telepon, $foto, $email, $id_users);
    $stmt_update->execute();
    $stmt_update->close();

    // Redirect to refresh data
    header("Location: pengaturan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan</title>
    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
            background: var(--green7);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: var(--black1);
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--green8);
            border-radius: 5px;
        }
        .profile-img {
            max-width: 150px;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        .btn {
            background: var(--green1);
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background: var(--green2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Pengaturan Profil</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <img src="<?php echo !empty($user['foto']) ? $user['foto'] : 'assets/imgs/default.png'; ?>" alt="Profile" class="profile-img">
                <input type="file" name="foto" accept="image/*">
                <button type="submit" name="delete_photo" class="btn" style="margin-top: 10px;">Hapus Foto</button>
            </div>
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
            </div>
            <div class="form-group">
                <label for="negara">Negara</label>
                <input type="text" name="negara" value="<?php echo htmlspecialchars($user['negara']); ?>" required>
            </div>
            <div class="form-group">
                <label for="kota">Kota</label>
                <input type="text" name="kota" value="<?php echo htmlspecialchars($user['kota']); ?>" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" required><?php echo htmlspecialchars($user['alamat']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="no_telepon">Nomor Telepon</label>
                <input type="text" name="no_telepon" value="<?php echo htmlspecialchars($user['no_telepon']); ?>" required>
            </div>
            <button type="submit" class="btn">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>
<?php
$connect->close();
?>