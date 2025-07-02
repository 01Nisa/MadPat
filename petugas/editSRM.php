<?php
include '../koneksi.php';

// Ambil id_pengujian dari URL
$id_pengujian = $_GET['id'] ?? 0;

// Query untuk ambil data dari tabel pengujian
$query = "SELECT * FROM pengujian WHERE id_pengujian = '$id_pengujian'";
$result = $connect->query($query);

// Cek apakah data ditemukan
if ($result && $result->num_rows > 0) {
  $data = $result->fetch_assoc();
} else {
  echo "Data tidak ditemukan.";
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Form Hasil Pengujian Jaringan</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      background-color: #a2c1c1;
      margin: 0;
      height: 180vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    :root {
      --green1: rgba(20, 116, 114, 1);
      --green2: rgba(3, 178, 176, 1);
      --green3: rgba(186, 231, 228, 1);
      --green4: rgba(12, 109, 108, 0.61);
      --green5: rgba(3, 178, 176, 0.29);
      --green6: rgba(240, 243, 243, 1);
    }
    .form-container {
      width: 90%;
      max-width: 1100px;
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 5px 5px 15px rgba(0,0,0,0.2);
      position: relative;
    }
    .form-container h2 {
      color: var(--green1);
      margin-bottom: 20px;
    }

    .form-container h3 {
      color: var(--green1);
      margin-bottom: 20px;
      font-size: 17px;
    }
    .form-group {
      margin-bottom: 16px;
    }
    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
    }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      border: 1.5px solid var(--green1);
      border-radius: 6px;
      font-size: 14px;
    }
    input:focus, select:focus, textarea:focus {
      outline: none;
      border-color: var(--green5);
      background-color: #fff;
    }
    .inline-group {
      display: flex;
      gap: 20px;
    }
    .inline-group .form-group {
      flex: 1;
    }
    .radio-group {
      display: flex;
      gap: 20px;
      margin-top: 6px;
    }
    .button-group {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 30px;
    }
    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
    }
    .btn-submit {
      background-color: var(--green1);
      color: white;
    }
    .btn-cancel {
      background-color: #eee;
      color: #333;
    }
    .btn:hover {
      opacity: 0.9;
    }
    @media screen and (max-width: 768px) {
      .inline-group {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Form Hasil Pengujian Jaringan</h2>
    <form action="proseseditSRM.php" method="post">
      <div class="inline-group">
        <div class="form-group">
          <label for="tanggal_terima">Tanggal Terima<span style="color:red">*</span></label>
          <input type="date" id="tanggal_terima" name="tanggal_terima"
                 value="<?php echo htmlspecialchars($data['tanggal_terima']); ?>" required>
        </div>
        <div class="form-group">
          <label for="tanggal_jadi">Tanggal Jadi<span style="color:red">*</span></label>
          <input type="date" id="tanggal_jadi" name="tanggal_jadi"
                 value="<?php echo htmlspecialchars($data['tanggal_jadi']); ?>" required>
        </div>
      </div>

        <div class="form-group">
          <label for="id_pengujian">Nomor Laboratorium<span style="color:red">*</span></label>
          <input type="text" id="id_pengujian" name="id_pengujian"
                 value="<?php echo htmlspecialchars($data['id_pengujian']); ?>" readonly required />
        </div>
      

       <h3>Data Pasien</h3> 
      <div class="form-group">
        <label for="nama_pasien">Nama Pasien<span style="color:red">*</span></label>
        <input type="text" id="nama_pasien" name="nama_pasien"
               value="<?php echo htmlspecialchars($data['nama_pasien']); ?>" required>
      </div>

      <div class="form-group">
        <label for="alamat">Alamat<span style="color:red">*</span></label>
        <textarea id="alamat" name="alamat" required><?php echo htmlspecialchars($data['alamat']); ?></textarea>
      </div>

      <div class="inline-group">
        <div class="form-group">
          <label for="nomor_pemeriksaan">Nomor Pemeriksaan</label>
          <input type="text" id="nomor_pemeriksaan" name="nomor_pemeriksaan"
                 value="<?php echo htmlspecialchars($data['nomor_pemeriksaan'] ?? ''); ?>" readonly>
        </div>
        <div class="form-group">
          <label for="usia">Usia<span style="color:red">*</span></label>
          <input type="number" id="usia" name="usia" min="0" max="120"
                 value="<?php echo htmlspecialchars($data['usia']); ?>" placeholder="Masukkan usia pasien" required>
        </div>
      </div>

      <h3>Hasil Pengujian Sampel</h3>
      <div class="form-group">
        <label for="alamat">Asal sediaan<span style="color:red">*</span></label>
        <textarea id="asal_sediaan" name="asal_sediaan" required><?php echo htmlspecialchars($data['asal_sediaan']); ?></textarea>
      </div>
      <div class="form-group">
        <label for="alamat">Diagnosis klinik<span style="color:red">*</span></label>
        <textarea id="diagnosa_klinik" name="diagnosa_klinik" required><?php echo htmlspecialchars($data['diagnosa_klinik']); ?></textarea>
      </div>
      <div class="form-group">
        <label for="alamat">Keterangan klinik<span style="color:red">*</span></label>
        <textarea id="keterangan_klinik" name="keterangan_klinik" required><?php echo htmlspecialchars($data['keterangan_klinik']); ?></textarea>
      </div>
      <div class="form-group">
        <label for="alamat">Makroskopis<span style="color:red">*</span></label>
        <textarea id="makroskopis" name="makroskopis" required><?php echo htmlspecialchars($data['makroskopis']); ?></textarea>
      </div>

      <h3>Kesimpulan</h3>
       <div class="form-group">
        <label for="alamat">Kesimpulan<span style="color:red">*</span></label>
        <textarea id="kesimpulan" name="kesimpulan" required><?php echo htmlspecialchars($data['kesimpulan']); ?></textarea>
      </div>
    
      <div class="button-group">
        <button type="reset" class="btn btn-cancel">Batal</button>
        <button type="submit" class="btn btn-submit">Kirim</button>
      </div>
    </form>
  </div>
</body>
</html>
