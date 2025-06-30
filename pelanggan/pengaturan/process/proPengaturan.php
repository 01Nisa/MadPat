<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("location:../../../login.php?pesan=belum_login");
    exit();
}

$user_id = $_SESSION['user']; 
include '../../../koneksi.php';

// Set header untuk response JSON
header('Content-Type: application/json');

// Tangani berbagai jenis aksi
$action = $_POST['action'] ?? ($_GET['action'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'update_password') {
        // Handle password update
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        
        // Validasi input
        if (empty($current_password) || empty($new_password)) {
            echo json_encode(['success' => false, 'error' => 'Semua field harus diisi']);
            exit;
        }
        
        // Verifikasi password saat ini
        $sql = "SELECT password FROM pengguna WHERE id_pengguna = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!password_verify($current_password, $user['password'])) {
            echo json_encode(['success' => false, 'error' => 'Kata sandi saat ini salah']);
            exit;
        }
        
        // Update password baru
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE pengguna SET password = ? WHERE id_pengguna = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Gagal memperbarui kata sandi']);
        }
        
        $stmt->close();
        exit;
    } else {
        // Handle profile update (including photo)
        $nama_lengkap = $connect->real_escape_string($_POST['nama_lengkap'] ?? '');
        $alamat = $connect->real_escape_string($_POST['alamat'] ?? '');
        $email = $connect->real_escape_string($_POST['email'] ?? '');
        $nomortlp = $connect->real_escape_string($_POST['nomortlp'] ?? '');
        $foto = null;
        
        // Handle file upload
        if (isset($_FILES['photo_upload']) && $_FILES['photo_upload']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "../../../assets/imgs/";
            $file_ext = pathinfo($_FILES['photo_upload']['name'], PATHINFO_EXTENSION);
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array(strtolower($file_ext), $allowed_ext)) {
                echo json_encode(['success' => false, 'error' => 'Format file tidak didukung']);
                exit;
            }
            
            // Generate unique filename
            $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_ext;
            $target_file = $target_dir . $new_filename;
            
            // Check file size (max 2MB)
            if ($_FILES['photo_upload']['size'] > 2097152) {
                echo json_encode(['success' => false, 'error' => 'Ukuran file maksimal 2MB']);
                exit;
            }
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['photo_upload']['tmp_name'], $target_file)) {
                $foto = $new_filename;
                
                // Delete old photo if exists and not default
                $sql = "SELECT foto FROM pengguna WHERE id_pengguna = ? AND foto != 'default.jpg'";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $old_photo = $result->fetch_assoc()['foto'];
                    if (file_exists($target_dir . $old_photo)) {
                        unlink($target_dir . $old_photo);
                    }
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'error' => 'Gagal mengunggah foto']);
                exit;
            }
        }
        
        // Update user data
        if ($foto) {
            $sql = "UPDATE pengguna SET nama = ?, alamat = ?, email = ?, nomortlp = ?, foto = ? WHERE id_pengguna = ?";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("sssssi", $nama_lengkap, $alamat, $email, $nomortlp, $foto, $user_id);
        } else {
            $sql = "UPDATE pengguna SET nama = ?, alamat = ?, email = ?, nomortlp = ? WHERE id_pengguna = ?";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("ssssi", $nama_lengkap, $alamat, $email, $nomortlp, $user_id);
        }
        
        if ($stmt->execute()) {
            $response = ['success' => true];
            if ($foto) {
                $response['foto'] = $foto;
            }
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'error' => $connect->error]);
        }
        
        $stmt->close();
        exit;
    }
}

// Jika tidak ada aksi yang valid
echo json_encode(['success' => false, 'error' => 'Aksi tidak valid']);
$connect->close();
?>