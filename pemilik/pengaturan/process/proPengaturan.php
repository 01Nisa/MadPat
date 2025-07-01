<?php
session_start();

error_log("proPengaturan.php - Session user ID: " . ($_SESSION['user'] ?? 'Not set'));

if (!isset($_SESSION['user'])) {
    header('Content-Type: application/json');
    error_log("proPengaturan.php - Redirecting: Session user not set");
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user'];
include '../../../koneksi.php';

if (!$connect) {
    error_log("proPengaturan.php - Database connection failed: " . mysqli_connect_error());
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Koneksi database gagal']);
    exit();
}

header('Content-Type: application/json');

$action = json_decode(file_get_contents('php://input'), true)['action'] ?? (isset($_POST['action']) ? $_POST['action'] : '');

if ($action === 'delete_photo') {
    $sql = "SELECT foto FROM pengguna WHERE id_pengguna = ?";
    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        error_log("proPengaturan.php - Prepare failed for select foto: " . $connect->error);
        echo json_encode(['success' => false, 'error' => 'Gagal menyiapkan query']);
        exit();
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user['foto'] && $user['foto'] !== 'profil.jpg' && file_exists("../../../{$user['foto']}")) {
        if (!unlink("../../../{$user['foto']}")) {
            error_log("proPengaturan.php - Failed to delete photo: ../../../{$user['foto']}");
        } else {
            error_log("proPengaturan.php - Deleted old photo: ../../../{$user['foto']}");
        }
    }

    $sql = "UPDATE pengguna SET foto = 'profil.jpg' WHERE id_pengguna = ?";
    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        error_log("proPengaturan.php - Prepare failed for update foto: " . $connect->error);
        echo json_encode(['success' => false, 'error' => 'Gagal menyiapkan query']);
        exit();
    }
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        error_log("proPengaturan.php - Photo deleted successfully for user ID: $user_id");
        echo json_encode(['success' => true, 'foto' => 'profil.jpg']);
    } else {
        error_log("proPengaturan.php - Failed to update foto in database: " . $stmt->error);
        echo json_encode(['success' => false, 'error' => 'Gagal menghapus foto']);
    }
    $stmt->close();
    $connect->close();
    exit();
}

if ($action === 'update_password') {
    $data = json_decode(file_get_contents('php://input'), true);
    $current_password = $data['current_password'] ?? '';
    $new_password = $data['new_password'] ?? '';

    if (empty($current_password) || empty($new_password)) {
        error_log("proPengaturan.php - Missing password fields");
        echo json_encode(['success' => false, 'error' => 'Semua field kata sandi harus diisi']);
        $connect->close();
        exit();
    }

    if (strlen($new_password) > 20) {
        error_log("proPengaturan.php - New password exceeds 20 characters");
        echo json_encode(['success' => false, 'error' => 'Kata sandi maksimal 20 karakter']);
        $connect->close();
        exit();
    }

    $sql = "SELECT password FROM pengguna WHERE id_pengguna = ?";
    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        error_log("proPengaturan.php - Prepare failed for select password: " . $connect->error);
        echo json_encode(['success' => false, 'error' => 'Gagal menyiapkan query']);
        $connect->close();
        exit();
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user['password'] !== $current_password) {
        error_log("proPengaturan.php - Current password incorrect for user ID: $user_id");
        echo json_encode(['success' => false, 'error' => 'Kata sandi saat ini salah']);
        $connect->close();
        exit();
    }

    $sql = "UPDATE pengguna SET password = ? WHERE id_pengguna = ?";
    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        error_log("proPengaturan.php - Prepare failed for update password: " . $connect->error);
        echo json_encode(['success' => false, 'error' => 'Gagal menyiapkan query']);
        $connect->close();
        exit();
    }
    $stmt->bind_param("si", $new_password, $user_id);
    if ($stmt->execute()) {
        error_log("proPengaturan.php - Password updated successfully for user ID: $user_id");
        echo json_encode(['success' => true]);
    } else {
        error_log("proPengaturan.php - Failed to update password: " . $stmt->error);
        echo json_encode(['success' => false, 'error' => 'Gagal memperbarui kata sandi']);
    }
    $stmt->close();
    $connect->close();
    exit();
}

$nama_lengkap = $_POST['nama_lengkap'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$email = $_POST['email'] ?? '';
$nomortlp = $_POST['nomortlp'] ?? '';

error_log("proPengaturan.php - Form data - nama: $nama_lengkap, alamat: $alamat, email: $email, nomortlp: $nomortlp, photo_upload: " . (isset($_FILES['photo_upload']) ? $_FILES['photo_upload']['name'] : 'Not set'));

if (empty($nama_lengkap) || empty($alamat) || empty($email) || empty($nomortlp)) {
    error_log("proPengaturan.php - Missing required fields");
    echo json_encode(['success' => false, 'error' => 'Semua field harus diisi']);
    $connect->close();
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("proPengaturan.php - Invalid email format: $email");
    echo json_encode(['success' => false, 'error' => 'Format email tidak valid']);
    $connect->close();
    exit();
}

$sql = "SELECT email, foto FROM pengguna WHERE id_pengguna = ?";
$stmt = $connect->prepare($sql);
if (!$stmt) {
    error_log("proPengaturan.php - Prepare failed for select email/foto: " . $connect->error);
    echo json_encode(['success' => false, 'error' => 'Gagal menyiapkan query']);
    $connect->close();
    exit();
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$current_user = $result->fetch_assoc();
$stmt->close();

if ($email !== $current_user['email']) {
    $sql = "SELECT id_pengguna FROM pengguna WHERE email = ? AND id_pengguna != ?";
    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        error_log("proPengaturan.php - Prepare failed for email check: " . $connect->error);
        echo json_encode(['success' => false, 'error' => 'Gagal menyiapkan query']);
        $connect->close();
        exit();
    }
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        error_log("proPengaturan.php - Email already taken: $email");
        echo json_encode(['success' => false, 'error' => 'Email sudah terdaftar']);
        $connect->close();
        exit();
    }
    $stmt->close();
}

$foto_path = $current_user['foto'] ?? 'profil.jpg';
if (!empty($_FILES['photo_upload']['name'])) {
    $target_dir = "../../../Uploads/";
    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            error_log("proPengaturan.php - Failed to create directory: $target_dir");
            echo json_encode(['success' => false, 'error' => 'Gagal membuat direktori Uploads']);
            $connect->close();
            exit();
        }
    }

    $original_filename = basename($_FILES["photo_upload"]["name"]);
    $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
    $unique_filename = uniqid() . '_' . $original_filename;
    $target_file = $target_dir . $unique_filename;

    error_log("proPengaturan.php - File upload - Name: {$original_filename}, Size: {$_FILES['photo_upload']['size']}, Type: $imageFileType, Target: $target_file");

    $check = getimagesize($_FILES["photo_upload"]["tmp_name"]);
    if ($check === false) {
        error_log("proPengaturan.php - File is not an image: $original_filename");
        echo json_encode(['success' => false, 'error' => 'File bukan gambar']);
        $connect->close();
        exit();
    }

    if ($_FILES["photo_upload"]["size"] > 5000000) {
        error_log("proPengaturan.php - File too large: {$_FILES['photo_upload']['size']} bytes");
        echo json_encode(['success' => false, 'error' => 'File terlalu besar (maksimal 5MB)']);
        $connect->close();
        exit();
    }

    $allowed_types = ["jpg", "jpeg", "png"];
    if (!in_array($imageFileType, $allowed_types)) {
        error_log("proPengaturan.php - Invalid file type: $imageFileType");
        echo json_encode(['success' => false, 'error' => 'Hanya file JPG, JPEG, dan PNG yang diizinkan']);
        $connect->close();
        exit();
    }

    if (move_uploaded_file($_FILES["photo_upload"]["tmp_name"], $target_file)) {
        $foto_path = "Uploads/" . $unique_filename;
        if ($current_user['foto'] && $current_user['foto'] !== 'profil.jpg' && file_exists("../../../{$current_user['foto']}")) {
            if (!unlink("../../../{$current_user['foto']}")) {
                error_log("proPengaturan.php - Failed to delete old photo: ../../../{$current_user['foto']}");
            } else {
                error_log("proPengaturan.php - Deleted old photo: ../../../{$current_user['foto']}");
            }
        }
        error_log("proPengaturan.php - Photo uploaded successfully: $foto_path");
    } else {
        error_log("proPengaturan.php - Failed to move uploaded file: $target_file, Error: " . $_FILES['photo_upload']['error']);
        echo json_encode(['success' => false, 'error' => 'Gagal mengunggah foto: ' . $_FILES['photo_upload']['error']]);
        $connect->close();
        exit();
    }
}

$sql = "UPDATE pengguna SET nama = ?, alamat = ?, email = ?, nomortlp = ?, foto = ? WHERE id_pengguna = ?";
$stmt = $connect->prepare($sql);
if (!$stmt) {
    error_log("proPengaturan.php - Prepare failed for update profile: " . $connect->error);
    echo json_encode(['success' => false, 'error' => 'Gagal menyiapkan query']);
    $connect->close();
    exit();
}
$stmt->bind_param("sssssi", $nama_lengkap, $alamat, $email, $nomortlp, $foto_path, $user_id);
if ($stmt->execute()) {
    $_SESSION['user_nama'] = $nama_lengkap; 
    error_log("proPengaturan.php - Profile updated successfully for user ID: $user_id, Foto: $foto_path");
    echo json_encode(['success' => true, 'foto' => $foto_path]);
} else {
    error_log("proPengaturan.php - Failed to update profile: " . $stmt->error);
    echo json_encode(['success' => false, 'error' => 'Gagal memperbarui profil: ' . $stmt->error]);
}
$stmt->close();
$connect->close();
?>