<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .login-page {
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

        .login-section {

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

        .login-logo {
            display: block;
            margin-left: 118px;
            width: 180px;
            height: auto;
            margin-top: 10px;
        }

        .login-section h4 {
            text-align: center;
            margin-top: 1rem;
            margin-bottom: 2rem;
            font-size: 30px;
            font-weight: bold;
            color: #147472;
        }

        .inputbox {
            position: relative;
            margin-bottom: 2rem;
        }

        .inputbox label {
            position: absolute;
            top: 0.75rem;
            left: 0.5rem;
            font-size: 1rem;
            font-size: 18px;
            color: #000000;
            transition: 0.3s;
            pointer-events: none;
        }

        .inputbox input {
            width: 100%;
            padding: 0.75rem 0.5rem;
            background: transparent;
            border: none;
            border-bottom: 2px solid #000000;
            color: #000000;
            outline: none;
            font-size: 1rem;
        }

        .kata{
            margin: -35px;
            margin-top:-30px;
            margin-bottom: 30px;
            margin-left: 232px;
            font-size: 14px;
            color:#147472;
        }

        .inputbox input:focus ~ label,
        .inputbox input:valid ~ label {
            top: -1.25rem;
            left: 0;
            font-size: 0.75rem;
            font-size: 16px;
            color: #147472;
        }

        .inputbox ion-icon {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: #000000;
            cursor: pointer;
        }


        .login-section button {
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

        .login-section button:hover {
            background-color: #67C3C0;
        }

        .register {
            text-align: center;
            margin-top: 1rem;
        }

        .register p a {
            color: #147472;
            text-decoration: none;
        }

        .register p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="login-page">
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
    <div class="login-container">
        <section class="login-section">
            <img src="assets/logo.png" alt="Logo" class="login-logo">
            <form action="cek-login.php" method="POST">
                <h4>Selamat datang!</h4>
                <div class="inputbox">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="email" name="email" id="email" required>
                    <label for="email">Email</label>
                </div>
                <div class="inputbox">
                    <ion-icon class="password-show" name="eye-off-outline" id="togglePasswordIcon"></ion-icon>
                    <input type="password" name="password" id="password" required>
                    <label for="password">Password</label>
                </div>
                <div class="kata">Lupa kata sandi?</div>
                <button type="submit">Masuk</button>
                <div class="register">
                    <p>Tidak punya akun? <a href="register.php">Daftar</a></p>
                </div>
            </form>
        </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons.js"></script>
    <script src="Javascript.js"></script>
</body>
</html>
