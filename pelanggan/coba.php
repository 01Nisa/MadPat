<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Formulir Pengajuan Sampel</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
      flex-direction: row;
      gap: 30px;
      width: 100%;
      max-width: 1600px;
      background-color: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .progress-section {
      flex: 1;
      padding-right: 20px;
      border-right: 1px solid #ddd;
    }

    .progress-section h3 {
      margin-bottom: 20px;
      color: #007bff;
    }

    .progress-step {
      margin-bottom: 16px;
      padding: 10px;
      background-color: #eef4ff;
      border-radius: 8px;
      border-left: 5px solid #007bff;
    }

    .form-container {
      flex: 2;
    }

    .form-container h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 16px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      color: #333;
      font-weight: 600;
    }

    input[type="text"],
    input[type="email"],
    input[type="date"],
    select,
    textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      background-color: #f9f9f9;
      transition: border 0.3s;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="date"]:focus,
    select:focus,
    textarea:focus {
      border-color: #007bff;
      outline: none;
      background-color: #fff;
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    .btn-submit {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .btn-submit:hover {
      background-color: #0056b3;
    }

    @media (max-width: 900px) {
      .container {
        flex-direction: column;
      }

      .progress-section {
        border-right: none;
        border-bottom: 1px solid #ddd;
        padding-bottom: 20px;
        margin-bottom: 20px;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <!-- Kolom Kiri: Progress -->
  <div class="progress-section">
    <h3>Status Proses</h3>
    <div class="progress-step">1. Input Data</div>
    <div class="progress-step">2. Verifikasi Admin</div>
    <div class="progress-step">3. Pengujian Laboratorium</div>
    <div class="progress-step">4. Hasil Diterbitkan</div>
  </div>

  <!-- Kolom Kanan: Formulir -->
  <div class="form-container">
    <h2>Formulir Pengajuan Sampel</h2>
    <form action="#" method="POST">
      <label for="data">Data Dokter</label>
      <div class="form-group">
        <label for="nama">Nama</label>
        <input type="text" id="namaDokter" name="namaDokter" required />
      </div>

      <div class="form-group">
        <label for="rs">Alamat/RS</label>
        <textarea name="rs" id="rs" required></textarea>
      </div>

      <label for="data">Data Pasien</label>
        <div class="form-group">
            <label for="nama">Nama</label>
            <input type="text" id="namaPasien" name="namaPasien" required />
        </div>

      <div class="form-group">
        <label for="nama">Usia</label>
        <input type="number" id="usia" name="usia" required />
      </div>

      <div class="form-group">
        <label for="jk">Jenis Kelamin</label>
        <input type="radio" id="jk" name="jk" value="perempuan" required /><a>Perempuan</a>
        <input type="radio" id="jk" name="jk" value="laki-laki" required /><a>Laki-laki</a>
      </div>

      <div class="form-group">
        <label for="negara">Negara</label>
        <input type="text" id="negara" name="negara" required />
      </div>

      <div class="form-group">
        <label for="alamat">Alamat</label>
        <textarea name="alamat" id="alamat" required></textarea>
      </div>

      <label for="data">Pemeriksaan Jaringan Tubuh</label>
        <div class="form-group">
            <label for="asal">Berasal dari</label>
            <input type="text" id="asal" name="asal" required />
        </div>
        <div class="form-group">
            <label for="perendaman">Direndam dalam</label>
            <input type="text" id="prendaman" name="perendaman" required />
        </div>
        <div class="form-group">
            <label for="diagKlinik">Diagnosis Klinik</label>
            <textarea name="diagKlinik" id="diagKlinik" required></textarea>
        </div>

       <label for="data">Penyakit Pasien</label>
        <div class="form-group">
            <label for="keterangan">Keterangan penyakit pasien</label>
            <textarea name="keterangan" id="keterangan" required></textarea>
        </div>

        <div class="form-group">
            <label for="patologi">Pemeriksaan Patologi</label>
            <input type="radio" id="patologi" name="patologi" value="sudah" required /><a>Sudah</a>
            <input type="radio" id="patologi" name="patologi" value="belum" required /><a>Belum</a>
        </div>

        <label for="data-patologi">Data di bawah ini diisi ketika sudah dilakukan pemeriksaan patologi</label>
        <div class="form-group">
            <label for="noPemeriksa">Nomor Pemeriksaan</label>
            <input type="text" id="noPemeriksa" name="noPemeriksa" required />
        </div>

        <div class="form-group">
            <label for="tglPeriksa">Tanggal Pemeriksaan</label>
            <input type="date" id="tglPeriksa" name="tglPeriksa" required />
        </div>

        <div class="form-group">
            <label for="diagPeriksa">Diagnosis Pemeriksaan</label>
            <textarea name="diagPeriksa" id="diagPeriksa" required></textarea>
        </div>

        <div class="form-group">
            <label for="poliklinik">Poliklinik</label>
            <input type="text" id="poliklinik" name="poliklinik" required />
        </div>

        <div class="form-group">
            <label for="klas">Klas</label>
            <input type="text" id="klas" name="klas" required />
        </div>

      <button type="submit" class="btn-submit">Kirim Pengajuan</button>
    </form>
  </div>
</div>
</body>
</html>
