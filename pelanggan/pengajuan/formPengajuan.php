<?php
// Simple PHP to handle form submission (example)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data here
    $nama_dokter = $_POST['nama_dokter'];
    $alamat_rs = $_POST['alamat_rs'];
    $nama_pasien = $_POST['nama_pasien'];
    $usia = $_POST['usia'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $negara = $_POST['negara'];
    $alamat_pasien = $_POST['alamat_pasien'];
    $berasal_dari = $_POST['berasal_dari'];
    $direndam_dalam = $_POST['direndam_dalam'];
    // Add database insertion logic here if needed
    // For example:
    /*
    try {
        $conn = new PDO("mysql:host=localhost;dbname=your_database", "username", "password");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("INSERT INTO pengajuan (nama_dokter, alamat_rs, nama_pasien, usia, jenis_kelamin, negara, alamat_pasien, berasal_dari, direndam_dalam) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nama_dokter, $alamat_rs, $nama_pasien, $usia, $jenis_kelamin, $negara, $alamat_pasien, $berasal_dari, $direndam_dalam]);
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    */
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Form Pengajuan Jaringan</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: #f4f7fa;
      padding: 20px;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
    }

    .container {
      display: flex;
      width: 100%;
      max-width: 900px;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .sidebar {
      width: 200px;
      background-color: #e0f7fa;
      padding: 20px 15px;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }

    .sidebar h3 {
      color: #00695c;
      font-size: 1.2em;
      margin-bottom: 25px;
      font-weight: 600;
    }

    .step {
      display: flex;
      align-items: center;
      margin-bottom: 25px;
      width: 100%;
    }

    .step-circle {
      width: 24px;
      height: 24px;
      background-color: #b0bec5;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 0.9em;
      margin-right: 10px;
      transition: background-color 0.3s;
    }

    .step-circle.active {
      background-color: #00695c;
    }

    .step-circle.completed {
      background-color: #00695c;
    }

    .step-circle.completed::before {
      content: "✔";
    }

    .step-line {
      flex-grow: 1;
      height: 2px;
      background-color: #b0bec5;
      margin-left: 5px;
      transition: background-color 0.3s;
    }

    .step-line.active {
      background-color: #00695c;
    }

    .step span {
      color: #004d40;
      font-size: 0.9em;
    }

    .form-container {
      flex: 1;
      padding: 30px;
      max-height: 600px;
      overflow-y: auto;
    }

    .form-container h2 {
      color: #00695c;
      text-align: center;
      margin-bottom: 20px;
      font-size: 1.5em;
      font-weight: 600;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      color: #004d40;
      margin-bottom: 5px;
      font-weight: 600;
    }

    .form-group .required:after {
      content: " *";
      color: #d32f2f;
    }

    input[type="text"],
    input[type="number"],
    textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #b0bec5;
      border-radius: 5px;
      background-color: #fff;
      font-size: 0.95em;
      transition: border-color 0.3s;
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    textarea:focus {
      border-color: #00695c;
      outline: none;
    }

    .inline-inputs {
      display: flex;
      gap: 10px;
    }

    .inline-inputs div {
      flex: 1;
    }

    .radio-group {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .radio-group input[type="radio"] {
      margin: 0;
    }

    .radio-group label {
      margin: 0 5px;
      font-weight: normal;
    }

    .btn-submit {
      background-color: #00695c;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      font-weight: 600;
      margin-top: 20px;
      transition: background-color 0.3s;
    }

    .btn-submit:hover {
      background-color: #004d40;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        max-width: 100%;
      }

      .sidebar {
        width: 100%;
        padding: 15px;
        flex-direction: row;
        justify-content: space-around;
        align-items: center;
      }

      .step {
        flex-direction: column;
        align-items: center;
        margin: 0 10px;
      }

      .step-circle {
        margin-bottom: 5px;
        margin-right: 0;
      }

      .step-line {
        display: none;
      }

      .form-container {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <!-- Sidebar: Progress Steps -->
  <div class="sidebar">
    <h3>Lembar Pengajuan</h3>
    <div class="step">
      <div class="step-circle completed">1</div>
      <div class="step-line active"></div>
      <span>Pengajuan 1</span>
    </div>
    <div class="step">
      <div class="step-circle active">2</div>
      <div class="step-line"></div>
      <span>Pengajuan 2</span>
    </div>
    <div class="step">
      <div class="step-circle">3</div>
      <span>Pengajuan 3</span>
    </div>
  </div>

  <!-- Form Container -->
  <div class="form-container">
    <h2>Form Pengajuan Jaringan</h2>

    <div class="form-group">
      <label class="required">Data Dokter</label>
      <div class="form-group">
        <label class="required">Nama</label>
        <input type="text" name="nama_dokter" placeholder="Masukkan nama dokter" required>
      </div>
      <div class="form-group">
        <label class="required">Alamat/RS</label>
        <input type="text" name="alamat_rs" placeholder="Masukkan alamat/RS dokter" required>
      </div>
    </div>

    <div class="form-group">
      <label class="required">Data Pasien</label>
      <div class="form-group">
        <label class="required">Nama</label>
        <input type="text" name="nama_pasien" placeholder="Masukkan nama pasien" required>
      </div>
      <div class="inline-inputs">
        <div>
          <label class="required">Usia</label>
          <input type="number" name="usia" placeholder="Usia" required>
        </div>
        <div>
          <label class="required">Negara</label>
          <input type="text" name="negara" placeholder="Negara" required>
        </div>
      </div>
      <div class="form-group">
        <label class="required">Jenis Kelamin</label>
        <div class="radio-group">
          <input type="radio" name="jenis_kelamin" value="Laki-laki" required><label>Laki-laki</label>
          <input type="radio" name="jenis_kelamin" value="Perempuan" required><label>Perempuan</label>
        </div>
      </div>
      <div class="form-group">
        <label class="required">Alamat</label>
        <input type="text" name="alamat_pasien" placeholder="Masukkan alamat pasien" required>
      </div>
    </div>

    <div class="form-group">
      <label class="required">Pemeriksaan Jaringan Tubuh</label>
      <div class="form-group">
        <label class="required">Berasal dari</label>
        <input type="text" name="berasal_dari" placeholder="Berasal dari" required>
      </div>
      <div class="form-group">
        <label class="required">Direndam dalam</label>
        <input type="text" name="direndam_dalam" placeholder="Direndam dalam" required>
      </div>
    </div>

    <button type="submit" class="btn-submit">Selanjutnya</button>
  </div>
</div>
</body>
</html>