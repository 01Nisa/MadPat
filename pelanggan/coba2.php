<?php
// No PHP logic needed here, just a simple form
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Input Jumlah Pengajuan</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #f4f7fa;
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background-color: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      width: 400px;
      text-align: center;
    }

    h2 {
      color: #03b2b0;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      color: #333;
      font-weight: 500;
    }

    input[type="number"] {
      width: 100%;
      padding: 10px;
      border: 2px solid #147472;
      border-radius: 8px;
      background-color: white;
    }

    input[type="number"]:hover,
    input[type="number"]:focus {
      border-color: #03b2b0;
      outline: none;
    }

    .btn-submit {
      background-color: #147472;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      font-weight: bold;
    }

    .btn-submit:hover {
      background-color: #03b2b0;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Jumlah Pengajuan Sampel</h2>
    <form action="pengajuan/formPengajuan.php" method="get">
      <div class="form-group">
        <label for="jumlah">Masukkan Jumlah Pengajuan</label>
        <input type="number" id="jumlah" name="jumlah" min="1" value="1" required />
      </div>
      <button type="submit" class="btn-submit">Lanjutkan</button>
    </form>
  </div>
</body>
</html>