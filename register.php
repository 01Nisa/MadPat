<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .regis-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-image: url('assets/bg-login.png');
            background-color: rgba(103, 195, 192, 0.6); /* transparan */
            background-blend-mode: overlay; /* opsional, biar nyatu */
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }

        .regis-section {

            background-color: #FFFFFF;
            border-radius: 35px;
            padding: 2rem;
            margin: 1rem;
            color: #000000;
            max-width: 400px;
            width: 400px;
            height: 510px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .regis-logo {
            display: block;
            margin-left: 118px;
            width: 180px;
            height: auto;
            margin-top: 10px;
        }

       


        .regis-section button {
            width: 100%;
            padding: 0.75rem;
            border-radius: 10px;
            background-color: #147472;
            border: none;
            color: #fff;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .regis-section button:hover {
            background-color: #67C3C0;
        }

        .login {
            text-align: center;
            margin-top: 1rem;
        }

        .login p a {
            color: #147472;
            text-decoration: none;
        }

        .login p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="regis-page">
    <?php
        if (isset($_GET['pesan'])) {
            echo "<div class='message'>";
            if ($_GET['pesan'] == 'gagal') {
                echo '<script>alert("Incorrect email or password!");</script>';
            } else if ($_GET['pesan'] == 'logout') {
                echo '<script>alert("You have successfully logged out!");</script>';
            } else if ($_GET['pesan'] == 'belum_login') {
                echo '<script>alert("You must login!");</script>';
            }
            echo "</div>";
        }
    ?>
    <div class="regis-container">
        <section class="regis-section">
            <img src="assets/logo.png" alt="Logo" class="regis-logo">
            <form action="cek-login.php" method="POST">
                <div class="inputbox">
                    <ion-icon name="person-outline"></ion-icon>
                    <input type="text" name="nama" id="nama" required>
                    <label for="nama">Nama Lengkap</label>
                </div>
                <div class="inputbox">
                    <ion-icon name="location-outline"></ion-icon>
                    <input type="text" name="alamat" id="alamat" required>
                    <label for="alamat">Alamat</label>
                </div>
                <div class="inputbox">
                    <ion-icon name="telephone-outline"></ion-icon>
                    <input type="number" name="=nohp" id="nohp" required>
                    <label for="nohp">Nomor Telepon</label>
                </div>
                <div class="inputbox">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="email" name="email" id="email" required>
                    <label for="email">Email</label>
                </div>
                <div class="inputbox">
                    <ion-icon class="password-sow" name="eye-off-outline" id="togglePasswordIcon"></ion-icon>
                    <input type="password" name="password" id="password" required>
                    <label for="password">Password</label>
                </div>
                <div class="inputbox">
                    <ion-icon class="password-sow" name="eye-off-outline" id="togglePasswordIcon"></ion-icon>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                    <label for="confirm_password">Confirm Password</label>
                </div>
                <button type="submit">Daftar</button>
                <div class="login">
                    <p>Sudah punya akun? <a href="login.php">Masuk</a></p>
                </div>
            </form>
        </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons.js"></script>
    <script src="Javascript.js"></script>
</body>
</html>
