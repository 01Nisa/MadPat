<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registrasi</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>
  <style>
    .regis-page {
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

    .regis-section {
        background-color: #FFFFFF;
        border-radius: 35px;
        padding: 2rem;
        margin: 1rem;
        color: #000000;
        max-width: 450px;
        width: 450px;
        height: 610px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .regis-logo {
      display: block;
      margin: 2px auto 10px 145px;
      width: 180px;
    }

    .inputbox {
      margin-bottom: 1rem;
    }

    .inputbox label {
      font-size: 14px;
      margin-left: 30px;
      margin-bottom: -2px;
      display: block;
      color: #000;
    }

    .input-wrapper {
      display: flex;
      align-items: center;
    }

    .input-wrapper ion-icon {
      font-size: 18px;
      margin-right: 10px;
      color: #000000;
    }

    .input-wrapper input {
      flex: 1;
      padding: 4px 8px;
      border-radius: 5px;
      border: 2px solid #147472;
      background-color: #ffffff;
      color: #000000;
      outline: none;
      font-size: 13px;
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
      background-color: #67c3c0;
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
  
  <div class="regis-container">
    <section class="regis-section">
      <img src="assets/logo.png" alt="Logo" class="regis-logo" />
      <form action="cek-register.php" method="POST">
        <div class="inputbox">
          <label for="nama">Nama Lengkap</label>
          <div class="input-wrapper">
            <ion-icon name="person-outline"></ion-icon>
            <input type="text" name="nama" id="nama" required />
          </div>
        </div>
        <div class="inputbox">
          <label for="alamat">Alamat</label>
          <div class="input-wrapper">
            <ion-icon name="location-outline"></ion-icon>
            <input type="text" name="alamat" id="alamat" required />
          </div>
        </div>
        <div class="inputbox">
          <label for="nohp">Nomor Telepon</label>
          <div class="input-wrapper">
            <ion-icon name="call-outline"></ion-icon>
            <input type="tel" name="nomortlp" id="nomortlp" required />
          </div>
        </div>
        <div class="inputbox">
          <label for="email">Email</label>
          <div class="input-wrapper">
            <ion-icon name="mail-outline"></ion-icon>
            <input type="email" name="email" id="email" required />
          </div>
        </div>
        <div class="inputbox">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <ion-icon name="lock-closed-outline"></ion-icon>
            <input type="password" name="password" id="password" required />
          </div>
        </div>
        <div class="inputbox">
          <label for="confirm_password">Konfirmasi Password</label>
          <div class="input-wrapper">
            <ion-icon name="lock-closed-outline"></ion-icon>
            <input type="password" name="konfirpassword" id="konfirpassword" required />
          </div>
        </div>
        <button type="submit">Daftar</button>
        <div class="login">
          <p>Sudah punya akun? <a href="login.php">Masuk</a></p>
        </div>
      </form>
    </section>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons.js"></script>
</body>
</html>
