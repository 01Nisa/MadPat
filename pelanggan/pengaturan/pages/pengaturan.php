<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("location:../../../login.php?pesan=belum_login");
    exit();
}

include '../../../koneksi.php';
$user = $_SESSION['user'];

// Get current user data
$query = mysqli_query($connect, "SELECT * FROM pengguna WHERE email = '".$user['email']."'");
$user_data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - <?php echo htmlspecialchars($user_data['nama']); ?></title>
    <style>
        /* [Keep all your existing navigation styles] */
        
        /* Settings Page Specific Styles */
        .settings-container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .settings-card {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
        }
        
        .settings-header {
            margin-bottom: 30px;
        }
        
        .settings-header h2 {
            color: var(--green1);
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .settings-header p {
            color: var(--black2);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--black1);
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--black2);
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--green1);
        }
        
        .profile-picture {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .profile-picture img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
            border: 3px solid var(--green3);
        }
        
        .btn {
            background: var(--green1);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background: var(--green2);
        }
        
        .file-input {
            display: none;
        }
        
        .file-label {
            display: inline-block;
            padding: 10px 15px;
            background: var(--green3);
            color: var(--green1);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .file-label:hover {
            background: var(--green5);
        }
        
        .success-message {
            color: var(--green1);
            background: var(--green3);
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        
        .error-message {
            color: #f44336;
            background: #ffebee;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- [Keep your existing navigation code] -->
        
        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>
                <div class="user">
                    <ion-icon class="notification" name="notifications-outline"></ion-icon>
                    <img src="../../../uploads/<?php echo htmlspecialchars($user_data['foto'] ?? 'default.png'); ?>" alt="User">
                    <span><?php echo htmlspecialchars($user_data['nama']); ?></span>
                </div>
            </div>
            
            <div class="settings-container">
                <div class="settings-card">
                    <div class="settings-header">
                        <h2>Pengaturan Akun</h2>
                        <p>Kelola informasi profil Anda</p>
                    </div>
                    
                    <?php 
                    if (isset($_GET['pesan'])) {
                        if ($_GET['pesan'] == 'sukses') {
                            echo '<div class="success-message" style="display:block;">Profil berhasil diperbarui!</div>';
                        } elseif ($_GET['pesan'] == 'gagal') {
                            echo '<div class="error-message" style="display:block;">Gagal memperbarui profil. Silakan coba lagi.</div>';
                        }
                    }
                    ?>
                    
                    <form action="proses_pengaturan.php" method="post" enctype="multipart/form-data">
                        <div class="profile-picture">
                            <img id="previewImage" src="../../../uploads/<?php echo htmlspecialchars($user_data['foto'] ?? 'default.png'); ?>" alt="Profile Picture">
                            <div>
                                <input type="file" id="foto" name="foto" class="file-input" accept="image/*">
                                <label for="foto" class="file-label">Ubah Foto</label>
                                <p style="font-size: 12px; color: var(--black2); margin-top: 5px;">Format: JPG, PNG (max 2MB)</p>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" id="nama" name="nama" class="form-control" value="<?php echo htmlspecialchars($user_data['nama']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_data['email']); ?>" required readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password Baru (Biarkan kosong jika tidak ingin mengubah)</label>
                            <input type="password" id="password" name="password" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Password Baru</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                        </div>
                        
                        <button type="submit" class="btn">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preview image when selected
        document.getElementById('foto').addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                const reader = new FileReader();
                
                reader.onload = function(event) {
                    document.getElementById('previewImage').src = event.target.result;
                };
                
                reader.readAsDataURL(file);
            }
        });
        
        // [Keep your existing navigation toggle script]
        let toggle = document.querySelector(".toggle");
        let navigation = document.querySelector(".navigation");
        let main = document.querySelector(".main");

        toggle.onclick = function () {
            navigation.classList.toggle("active");
            main.classList.toggle("active");
        };
    </script>
    
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>