<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-image: url('assets/bg-login.png');
            background-color: rgba(103, 195, 192, 0.6); 
            background-blend-mode: overlay; 
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
            margin-top: 7px;
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

        .kata-sandi{
            margin: -35px;
            margin-top:-30px;
            margin-bottom: 30px;
            margin-left: 232px;
            font-size: 14px;
            color:#147472;
        }

        .kata-sandi a {
            color: #147472;
            text-decoration: none;
        }

        .kata-sandi a:hover {
            text-decoration: underline;
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

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6); 
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999; 
        }

        .message-box {
            position: fixed;
            background-color: #ffffff;
            padding: 2rem 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 1000; 
            text-align: center;
            height: 200px;
            width: 400px;
            max-width: 400px;
            left: 50%;
            top: 30%;
            transform: translate(-50%, -50%);
        }

        .message-box img {
            width: 64px;
            height: 64px;
            margin-bottom: 2rem;
        }

        .message-box p {
            margin-bottom: 1rem;
            font-size: 18px;
            color: #147472;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            font-weight: bold;
            color: #147472;
            cursor: pointer;
        }

        .close-btn:hover {
            color: #000000;
        }

    </style>
</head>
<body class="login-page">
<?php if (isset($_GET['pesan'])): ?>
        <div class="overlay" id="messageOverlay">
            <div class="message-box">
                <img src="assets/peringatan.png" alt="peringatan">
                <span class="close-btn" onclick="document.getElementById('messageOverlay').style.display='none'">&times;</span>
                <p>
                    <?php
                        switch ($_GET['pesan']) {
                            case 'gagal':
                                echo "Maaf email atau password Anda salah!";
                                break;
                            case 'logout':
                                echo "Berhasil logout.";
                                break;
                            case 'belum_login':
                                echo "Silakan login terlebih dahulu.";
                                break;
                            default:
                                echo "Terjadi kesalahan.";
                        }
                    ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

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
                    <label for="password">Kata Sandi</label>
                </div>
                <div class="kata-sandi">
                    <a href = "reset-sandi.php">Lupa kata sandi? </a> 
                </div>
                <button type="submit">Masuk</button>
                <div class="register">
                    <p>Tidak punya akun? <a href="register.php">Daftar</a></p>
                </div>
            </form>
        </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons.js"></script>
    <script>
    const togglePasswordIcon = document.getElementById("togglePasswordIcon");
    const passwordInput = document.getElementById("password");

    togglePasswordIcon.addEventListener("click", function () {
      const isPasswordVisible = passwordInput.type === "text";
      passwordInput.type = isPasswordVisible ? "password" : "text";
      togglePasswordIcon.setAttribute("name", isPasswordVisible ? "eye-off-outline" : "eye-outline");
    });
  </script>
</body>
</html>
