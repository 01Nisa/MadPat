<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Formulir Pembayaran Sampel</title>
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

    :root {
      --green1: rgba(20, 116, 114, 1);
      --green2: rgba(3, 178, 176, 1);
      --green3: rgba(186, 231, 228, 1);
      --green4: rgba(12, 109, 108, 0.61);
      --green5: rgba(3, 178, 176, 0.29);
      --green6: rgba(240, 243, 243, 1);
    }

    .container {
      position: relative;
      display: flex;
      flex-direction: row;
      gap: 2px;
      width: 90%;
      max-width: 1200px;
      min-height: 780px;
      background-color: var(--green6);
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .close-button {
      position: absolute;
      top: 15px;
      right: 20px;
      font-size: 24px;
      font-weight: bold;
      color: var(--green1);
      cursor: pointer;
      transition: color 0.3s;
    }

    .close-button:hover {
      color: var(--green5);
    }

    .form-container {
      width: 100%;
      max-width: 1100px;
      min-height: 710px;
      overflow-y: auto;
      background-color: #fff;
      margin: 20px 20px;
      padding: 20px 30px;
      position: relative;
    }

    .form-container h2 {
      text-align: left;
      color: var(--green2);
      margin-bottom: 20px;
    }

    .form-section-title {
      font-size: 20px;
      font-weight: bold;
      color: var(--green1);
      margin-top: 16px;
      margin-bottom: 1px;
    }

    .form-group {
      margin-bottom: 12px;
    }

    label {
      display: block;
      margin-bottom: 4px;
      color: #333;
      font-weight: 500;
      font-size: 16px;
    }

    input[type="text"],
    input[type="date"],
    input[type="time"],
    select,
    textarea {
      width: 100%;
      padding: 10px;
      border: 2px solid var(--green1);
      border-radius: 8px;
      background-color: white;
      transition: border-color 0.3s, background-color 0.3s;
    }

    input:hover,
    select:hover,
    textarea:hover,
    input:focus,
    select:focus,
    textarea:focus {
      border-color: var(--green5);
      background-color: #fff;
      outline: none;
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    .button-group {
      display: flex;
      justify-content: flex-end;
      margin-top: 250px;
      margin-bottom: 10px;
    }

    .btn {
      background-color: var(--green1);
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      font-weight: bold;
      transition: background-color 0.3s;
      min-width: 120px;
      margin-left: 10px;
    }

    .btn:hover {
      background-color: var(--green5);
    }

    .inline-group {
      display: flex;
      gap: 20px;
    }

    .inline-group .form-group {
      flex: 1;
    }

    .upload-group {
      margin-bottom: 12px;
    }

    .upload-group label {
      margin-bottom: 8px;
    }

    .upload-group input[type="file"] {
      padding: 8px 0;
    }

    @media (max-width: 900px) {
      .container {
        flex-direction: column;
        height: auto;
      }

      .form-container {
        max-width: 100%;
      }

      .inline-group {
        flex-direction: column;
      }

      .button-group {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
      }

      .btn {
        margin-left: 0;
        width: 100%;
      }
    }
  </style>
  <script>
    function formatRupiah(input) {
      let value = input.value.replace(/[^0-9]/g, ''); 
      if (value === '') value = '0';
      let number = parseInt(value, 10);
      if (number < 0) number = 0; 
      input.value = number.toLocaleString('id-ID'); 
    }
  </script>
</head>
<body>
<div class="container">
  <div class="close-button" onclick="window.location='pembayaran.php'">×</div>
  <div class="form-container">
    <h2>Formulir Pembayaran Sampel</h2>
    <form id="formPembayaran" action="../process/proDiajukan.php" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label for="nama_pengirim">Nama Pengirim</label>
        <input type="text" id="nama_pengirim" name="nama_pengirim" required />
      </div>

      <div class="inline-group">
        <div class="form-group">
          <label for="tanggal_pembayaran">Tanggal Pembayaran</label>
          <input type="date" id="tanggal_pembayaran" name="tanggal_pembayaran" required />
        </div>
        <div class="form-group">
          <label for="waktu_pembayaran">Waktu Pembayaran</label>
          <input type="time" id="waktu_pembayaran" name="waktu_pembayaran" required />
        </div>
      </div>

      <div class="inline-group">
        <div class="form-group">
          <label for="jenis_pembayaran">Jenis Pembayaran</label>
          <select name="jenis_pembayaran" required>
            <option value="">Pilih Jenis Pembayaran</option>
            <option value="TransferBank">Transfer Bank</option>
            <option value="E-Wallet">E-Wallet</option>
            <option value="Tunai">Tunai</option>
          </select>
        </div>
        <div class="form-group">
          <label for="total_bayar">Total Pembayaran (Rp)</label>
          <input type="text" id="total_bayar" name="total_bayar" oninput="formatRupiah(this)" required />
        </div>
      </div>

      <div class="upload-group">
        <label for="bukti_pembayaran">Unggah Bukti Pembayaran (Gambar)</label>
        <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept=".png,.jpg,.jpeg" required />
      </div>

      <div class="button-group">
        <button type="button" class="btn" onclick="window.location='pembayaran.php'">Batal</button>
        <button type="submit" class="btn">Kirim</button>
      </div>
    </form>
  </div>
</div>
</body>
</html>