<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("location:../../../login.php?pesan=belum_login");
    exit();
}

$user_id = $_SESSION['user']; 
include '../../../koneksi.php';

$sql = "SELECT nama, email, alamat, nomortlp, foto FROM pengguna WHERE id_pengguna = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    $nama_pengguna = "Pengguna Tidak Ditemukan";
    $email = "";
    $alamat = "";
    $nomortlp = "";
    $foto_pengguna = "default.jpg"; 
} else {
    $nama_pengguna = $user['nama'];
    $email = $user['email'];
    $alamat = $user['alamat'];
    $nomortlp = $user['nomortlp'];
    $foto_pengguna = $user['foto'] ? $user['foto'] : "default.jpg"; 
}

$connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=poppins");

        * {
            font-family: "Ubuntu", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --green1: rgba(20, 116, 114, 1);
            --green2: rgba(3, 178, 176, 1);
            --green3: rgba(186, 231, 228, 1);
            --green4: rgba(12, 109, 108, 0.61);
            --green5: rgba(3, 178, 176, 0.29);
            --green6: rgba(240, 243, 243, 1);
            --green7: rgba(228, 240, 240, 1);
            --green8: rgba(136, 181, 181, 0.26);
            --white: #fff;
            --gray: #f5f5f5;
            --black1: #222;
            --black2: #999;
            --black3: rgba(0, 0, 0, 0.4);
        }

        body {
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            position: relative;
            width: 100%;
        }

        .navigation {
            position: fixed;
            width: 230px;
            height: 100%;
            background: var(--green2);
            border-left: 10px solid var(--green2);
            transition: 0.5s;
            overflow: hidden;
            z-index: 1000;
        }

        .navigation.active {
            width: 80px;
        }

        .navigation ul {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }

        .navigation ul li {
            position: relative;
            width: 100%;
            list-style: none;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }

        .navigation ul li:hover,
        .navigation ul li.hovered {
            background-color: var(--white);
        }

        .navigation ul li:nth-child(1) {
            margin-bottom: -30px;
            pointer-events: none;
        }

        .navigation ul li a {
            position: relative;
            display: flex;
            width: 100%;
            text-decoration: none;
            color: var(--white);
        }

        .navigation ul li:hover a,
        .navigation ul li.hovered a {
            color: var(--green2);
        }

        .navigation ul li a .icon {
            display: block;
            min-width: 60px;
            height: 60px;
            line-height: 75px;
            text-align: center;
        }

        .navigation ul li.signout {
            position: absolute;
            bottom: -150px;
            width: 100%;
        }

        .navigation ul li.signout:hover,
        .navigation ul li.signout.hovered {
            background-color: var(--white);
        }

        .navigation ul li.signout a {
            display: flex;
            width: 100%;
            text-decoration: none;
            color: var(--white);
        }

        .navigation ul li.signout:hover a {
            color: var(--green2);
        }

        .navigation ul li.signout a .icon {
            display: block;
            min-width: 60px;
            height: 60px;
            line-height: 75px;
            text-align: center;
        }

        .navigation ul li.signout a .icon ion-icon {
            font-size: 1.75rem;
        }

        .navigation ul li.signout a .title {
            font-size: 16px;
            color: black;
            white-space: nowrap;
        }

        .navigation ul li a .icon ion-icon {
            font-size: 1.75rem;
        }

        .navigation ul li a .title-logo {
            display: block;
            font-family: 'Poppins', sans-serif;
            font-size: 22px;
            color: black;
            padding: 0 10px;
            height: 230px;
            line-height: 70px;
            text-align: start;
            white-space: nowrap;
        }

        .navigation ul li a .title {
            display: block;
            font-family: 'Poppins', sans-serif;
            color: black;
            padding: 0 10px;
            height: 70px;
            line-height: 60px;
            text-align: start;
            white-space: nowrap;
        }

        .navigation ul li:hover a::before,
        .navigation ul li.hovered a::before {
            content: "";
            position: absolute;
            right: 0;
            top: -50px;
            width: 50px;
            height: 50px;
            background-color: transparent;
            border-radius: 50%;
            box-shadow: 35px 35px 0 10px var(--white);
            pointer-events: none;
        }

        .navigation ul li:hover a::after,
        .navigation ul li.hovered a::after {
            content: "";
            position: absolute;
            right: 0;
            bottom: -50px;
            width: 50px;
            height: 50px;
            background-color: transparent;
            border-radius: 50%;
            box-shadow: 35px -35px 0 10px var(--white);
            pointer-events: none;
        }

        .main {
            position: absolute;
            width: calc(100% - 300px);
            left: 250px;
            min-height: 100vh;
            background: var(--white);
            transition: 0.5s;
        }

        .main.active {
            width: calc(100% - 80px);
            left: 80px;
        }

        .topbar {
            width: 100%;
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            z-index: 1001;
        }

        .toggle {
            position: relative;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2.5rem;
            cursor: pointer;
            color: var(--black1);
            left: -30px;
            z-index: 1002;
        }

        .user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user span {
            font-size: 16px;
            color: var(--black1);
        }

        .notification {
            font-size: 1.5rem;
            color: var(--black1);
            cursor: pointer;
            margin-right: 10px;
        }

        .settings {
            padding: 20px;
            margin: 70px 20px 20px;
            display: flex;
            gap: 20px;
            max-height: 80vh;
            overflow-y: auto;
            z-index: 1001;
        }

        .settings .left-section, .settings .right-section {
            flex: 1;
        }

        .settings .separator {
            width: 1px;
            background: var(--green8);
            margin: 0 20px;
        }

        .settings .card {
            background: var(--white);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px var(--black3);
        }

        .settings .card h3 {
            color: var(--black1);
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .settings .profile-photo-container {
            text-align: center;
            margin-bottom: 15px;
        }

        .settings .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--green2);
        }

        .settings .photo-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .settings .photo-actions button, 
        .settings .photo-actions label {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            text-align: center;
            transition: all 0.3s;
        }

        .settings .photo-actions .delete-btn {
            background: #e74c3c;
            color: var(--white);
        }

        .settings .photo-actions .delete-btn:hover {
            background: #c0392b;
        }

        .settings .photo-actions .update-btn {
            background: var(--green2);
            color: var(--white);
        }

        .settings .photo-actions .update-btn:hover {
            background: var(--green1);
        }

        .settings .form-group {
            margin-bottom: 20px;
        }

        .settings .form-group label {
            display: block;
            color: var(--black2);
            margin-bottom: 5px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .settings .form-group input[type="text"],
        .settings .form-group input[type="email"],
        .settings .form-group input[type="tel"],
        .settings .form-group input[type="password"],
        .settings .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--green8);
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 0.9rem;
            transition: border-color 0.3s;
        }

        .settings .form-group input[type="text"]:focus,
        .settings .form-group input[type="email"]:focus,
        .settings .form-group input[type="tel"]:focus,
        .settings .form-group input[type="password"]:focus {
            border-color: var(--green2);
            outline: none;
        }

        .settings .form-group button {
            padding: 10px 20px;
            background: var(--green2);
            color: var(--white);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s;
            width: 100%;
        }

        .settings .form-group button:hover {
            background: var(--green1);
        }

        .settings .notification-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .settings .notification-toggle input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: none;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 991px) {
            .navigation {
                left: -300px;
            }
            .navigation.active {
                width: 300px;
                left: 0;
            }
            .main {
                width: 100%;
                left: 0;
            }
            .main.active {
                left: 300px;
            }
            .settings {
                flex-direction: column;
            }
            .settings .separator {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .settings {
                margin: 10px;
            }
            .settings .card h3 {
                font-size: 1rem;
            }
            .settings .form-group input,
            .settings .form-group select {
                font-size: 0.85rem;
            }
            .settings .profile-photo {
                width: 120px;
                height: 120px;
            }
        }

        @media (max-width: 480px) {
            .card h3 {
                font-size: 0.9rem;
            }
            .user {
                min-width: 40px;
            }
            .navigation {
                width: 100%;
                left: -100%;
                z-index: 1000;
            }
            .navigation.active {
                width: 100%;
                left: 0;
            }
            .toggle {
                z-index: 10001;
            }
            .main.active .toggle {
                color: #fff;
                position: fixed;
                right: 0;
                left: initial;
            }
            .topbar {
                padding: 0 10px;
            }
            .user span {
                display: none;
            }
            .settings .form-group input,
            .settings .form-group select {
                font-size: 0.8rem;
            }
            .settings .form-group button {
                width: 100%;
            }
            .settings .photo-actions button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="../../beranda.php">
                        <span class="icon">
                            <img src="../../../assets/microscope.png" alt="logo">
                        </span>
                        <span class="title-logo">MedPath</span>
                    </a>
                </li>

                <li>
                    <a href="../../beranda.php">
                        <span class="icon">
                            <img src="../../../assets/dashboard.png" alt="dashboard">
                        </span>
                        <span class="title">Beranda</span>
                    </a>
                </li>

                <li>
                    <a href="../../pengajuan/pages/pengajuan.php">
                        <span class="icon">
                            <img src="../../../assets/pengajuan.png" alt="pengajuan">
                        </span>
                        <span class="title">Pengajuan</span>
                    </a>
                </li>

                <li>
                    <a href="../../pengambilan/pages/pengambilan.php">
                        <span class="icon">
                            <img src="../../../assets/pengambilan.png" alt="pengambilan">
                        </span>
                        <span class="title">Pengambilan</span>
                    </a>
                </li>

                <li>
                    <a href="../../prosesUji/pages/pengujian.php">
                        <span class="icon">
                            <img src="../../../assets/prosesuji.png" alt="prosesuji">
                        </span>
                        <span class="title">Proses Uji</span>
                    </a>
                </li>

                <li>
                    <a href="../../prosesUji/pages/riwayat.php">
                        <span class="icon">
                            <img src="../../../assets/riwayat.png" alt="riwayat">
                        </span>
                        <span class="title">Riwayat Uji</span>
                    </a>
                </li>
                <li>
                    <a href="../../pembayaran/pages/pembayaran.php">
                        <span class="icon">
                            <img src="../../../assets/money.png" alt="money">
                        </span>
                        <span class="title">Pembayaran</span>
                    </a>
                </li>

                <li>
                    <a href="pengaturan.php">
                        <span class="icon">
                            <img src="../../../assets/setting.png" alt="setting">
                        </span>
                        <span class="title">Pengaturan</span>
                    </a>
                </li>

                <li class="signout">
                    <a href="../../signout.php">
                        <span class="icon">
                            <ion-icon name="log-out-outline" style="color: black"></ion-icon>
                        </span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>
                <div class="user">
                    <ion-icon class="notification" name="notifications-outline"></ion-icon>
                    <span><?php echo htmlspecialchars($nama_pengguna); ?></span>
                    <img src="../../../assets/imgs/<?php echo htmlspecialchars($foto_pengguna); ?>" alt="User">
                </div>
            </div>

            <div class="settings">
                <div id="alert-message" class="alert" style="display: none;"></div>
                
                <?php if (!$user): ?>
                    <p style="color: var(--black1);">Pengguna tidak ditemukan.</p>
                <?php else: ?>
                    <div class="left-section">
                        <div class="card">
                            <h3>Profil</h3>
                            <div class="profile-photo-container">
                                <img src="../../../assets/imgs/<?php echo htmlspecialchars($foto_pengguna); ?>" 
                                     alt="Profile Photo" 
                                     class="profile-photo"
                                     id="profile-photo-preview">
                            </div>
                            
                            <form id="profile-form" action="../process/proPengaturan.php" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="photo_upload">Foto Profil</label>
                                    <input type="file" id="photo_upload" name="photo_upload" accept="image/*">
                                </div>
                                
                                <div class="form-group">
                                    <label for="nama_lengkap">Nama Lengkap</label>
                                    <input type="text" id="nama_lengkap" name="nama_lengkap" 
                                           value="<?php echo htmlspecialchars($nama_pengguna); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <input type="text" id="alamat" name="alamat" 
                                           value="<?php echo htmlspecialchars($alamat); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nomortlp">Nomor Telepon</label>
                                    <input type="tel" id="nomortlp" name="nomortlp" 
                                           value="<?php echo htmlspecialchars($nomortlp); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" id="save-profile-btn">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="separator"></div>
                    
                    <div class="right-section">
                        <div class="card">
                            <h3>Keamanan</h3>
                            <form id="security-form">
                                <div class="form-group">
                                    <label for="current_password">Kata Sandi Saat Ini</label>
                                    <input type="password" id="current_password" name="current_password" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="new_password">Kata Sandi Baru</label>
                                    <input type="password" id="new_password" name="new_password" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_password">Konfirmasi Kata Sandi</label>
                                    <input type="password" id="confirm_password" name="confirm_password" required>
                                </div>
                                
                                <div class="form-group">
                                    <button type="button" id="update-password-btn">Perbarui Kata Sandi</button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="card">
                            <h3>Notifikasi</h3>
                            <div class="form-group notification-toggle">
                                <input type="checkbox" id="notification_toggle" name="notification_toggle">
                                <label for="notification_toggle">Aktifkan Notifikasi</label>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Navigation functionality
        let list = document.querySelectorAll(".navigation li");

        function activeLink() {
            list.forEach((item) => {
                item.classList.remove("hovered");
            });
            this.classList.add("hovered");
        }

        list.forEach((item) => item.addEventListener("mouseover", activeLink));

        let toggle = document.querySelector(".toggle");
        let navigation = document.querySelector(".navigation");
        let main = document.querySelector(".main");

        toggle.onclick = function () {
            navigation.classList.toggle("active");
            main.classList.toggle("active");
        };

        // Photo preview functionality
        document.getElementById('photo_upload').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('profile-photo-preview').src = e.target.result;
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Profile form submission
        document.getElementById('profile-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const saveBtn = document.getElementById('save-profile-btn');
            const alertBox = document.getElementById('alert-message');
            
            saveBtn.disabled = true;
            saveBtn.textContent = 'Menyimpan...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Profil berhasil diperbarui', 'success');
                    // Update profile photo in topbar if changed
                    if (data.foto) {
                        document.querySelector('.user img').src = '../../../assets/imgs/' + data.foto;
                        document.getElementById('profile-photo-preview').src = '../../../assets/imgs/' + data.foto;
                    }
                } else {
                    showAlert(data.error || 'Gagal memperbarui profil', 'error');
                }
            })
            .catch(error => {
                showAlert('Terjadi kesalahan saat menyimpan profil', 'error');
                console.error('Error:', error);
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Simpan Perubahan';
            });
        });

        // Password update functionality
        document.getElementById('update-password-btn').addEventListener('click', function() {
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const alertBox = document.getElementById('alert-message');
            
            if (newPassword !== confirmPassword) {
                showAlert('Kata sandi baru dan konfirmasi tidak cocok', 'error');
                return;
            }
            
            if (newPassword.length < 6) {
                showAlert('Kata sandi minimal 6 karakter', 'error');
                return;
            }
            
            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Memperbarui...';
            
            fetch('../process/proPengaturan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'update_password',
                    current_password: currentPassword,
                    new_password: newPassword
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Kata sandi berhasil diperbarui', 'success');
                    document.getElementById('security-form').reset();
                } else {
                    showAlert(data.error || 'Gagal memperbarui kata sandi', 'error');
                }
            })
            .catch(error => {
                showAlert('Terjadi kesalahan saat memperbarui kata sandi', 'error');
                console.error('Error:', error);
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Perbarui Kata Sandi';
            });
        });

        // Helper function to show alerts
        function showAlert(message, type) {
            const alertBox = document.getElementById('alert-message');
            alertBox.textContent = message;
            alertBox.className = 'alert alert-' + type;
            alertBox.style.display = 'block';
            
            setTimeout(() => {
                alertBox.style.display = 'none';
            }, 5000);
        }
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>