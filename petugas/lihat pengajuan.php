<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pengajuan Pengujian Jaringan</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #fdfdfd;
      display: flex;
      justify-content: center;
      padding: 40px;
    }

    .container {
      width: 800px;
      background: #fff;
      padding: 40px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      position: relative;
    }

    .back-button {
      position: absolute;
      top: 20px;
      left: 20px;
      background-color: #ccc;
      color: black;
      padding: 6px 14px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    .info-table {
      width: 100%;
      border: 1px solid #000;
      border-collapse: collapse;
      margin-bottom: 30px;
    }

    .info-table td {
      padding: 6px 10px;
      vertical-align: top;
    }

    .info-table td:first-child {
      width: 150px;
    }

    .info-table .section-title {
      font-weight: bold;
    }

    .section {
      margin-bottom: 20px;
    }

    .section-title {
      font-weight: bold;
      margin-bottom: 5px;
    }

    .field {
      margin-left: 20px;
      line-height: 1.6;
    }

    .field span {
      display: inline-block;
      width: 180px;
      font-weight: bold;
    }

    .button-group {
      text-align: center;
      margin-top: 40px;
    }

    .action-button {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 10px 20px;
      margin: 0 10px;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }

    .action-button.reject {
      background-color: #e74c3c;
    }

    .action-button:hover {
      opacity: 0.9;
    }
  </style>
</head>
<body>

  <div class="container">
    <button class="back-button" onclick="history.back()">← Kembali</button>

    <h2>Pengajuan Pengujian Jaringan</h2>

    <table class="info-table">
      <tr>
        <td class="section-title">Dokter :</td>
        <td class="section-title">Yang diperiksa :</td>
      </tr>
      <tr>
        <td>
          <div class="field"><span>Nama</span>: dr. Bayu Purnomo Sp.PD</div>
          <div class="field"><span>Alamat/RS</span>: RS Indah Permata</div>
        </td>
        <td>
          <div class="field"><span>Nama</span>: Bagas Andikara</div>
          <div class="field"><span>Jenis Kelamin</span>: Laki-laki</div>
          <div class="field"><span>Usia</span>: 24</div>
          <div class="field"><span>Bangsa</span>: Indonesia</div>
          <div class="field"><span>Alamat</span>: Jl. Cangkring Sleman</div>
        </td>
      </tr>
    </table>

    <div class="section">
      <div class="section-title">Pemeriksaan Jaringan Tubuh</div>
      <div class="field"><span>Jaringan didapat dengan</span>: Biopsi</div>
      <div class="field"><span>Berasal dari</span>: Antrum gaster</div>
      <div class="field"><span>Direndam dalam</span>: -</div>
      <div class="field"><span>Diagnosa Klinik</span>: Dispepsia</div>
    </div>

    <div class="section">
      <div class="section-title">Keterangan penyakit penderita</div>
      <div class="field">Apakah terdapat Helicobacter pylori?</div>
    </div>

    <div class="section">
      <div class="field"><span>Pemeriksaan patologi</span>: Belum</div>
      <div class="field"><span>Nomor Pemeriksaan</span>: -</div>
      <div class="field"><span>Tanggal Pemeriksaan</span>: -</div>
      <div class="field"><span>Diagnosa Pemeriksaan</span>: -</div>
    </div>

    <div class="button-group">
      <button class="action-button">Terima</button>
      <button class="action-button reject">Tolak</button>
    </div>
  </div>

</body>
</html>
