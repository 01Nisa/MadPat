<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Form Penerimaan Sampel</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #e0e4e5;
      margin: 0;
      padding: 40px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .form-container {
      width: 800px;
      background-color: #fff;
      padding: 30px 40px;
      border-radius: 14px;
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
      color: #009f9d;
      margin-bottom: 30px;
    }

    .form-row {
      display: flex;
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-group {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    label {
      margin-bottom: 6px;
      font-weight: bold;
      color: #222;
    }

    input[type="text"],
    input[type="date"],
    input[type="number"] {
      padding: 10px 14px;
      border: 2px solid #1D7D7D;
      border-radius: 8px;
      font-size: 16px;
      box-sizing: border-box;
      outline: none;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
      opacity: 1;
      -webkit-appearance: inner-spin-button;
      margin: 0;
    }

    .radio-group {
      display: flex;
      gap: 20px;
      align-items: center;
      padding-top: 10px;
    }

    .form-actions {
      display: flex;
      justify-content: flex-end;
      margin-top: 30px;
      gap: 10px;
    }

    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }

    .btn-cancel {
      background-color: #f3f3f3;
      color: #999;
    }

    .btn-submit {
      background-color: #007a76;
      color: #fff;
    }

    .btn:hover {
      opacity: 0.9;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Penerimaan Sampel</h2>
    <form>
      <div class="form-row">
        <div class="form-group">
          <label for="no-lab">Nomor Laboratorium</label>
          <input type="text" id="no-lab" placeholder="JRM 25-1786">
        </div>
        <div class="form-group">
          <label for="tgl-terima">Tanggal Terima</label>
          <input type="date" id="tgl-terima">
        </div>
      </div>

      <div class="form-group">
        <label for="nama-pasien">Nama Pasien</label>
        <input type="text" id="nama-pasien" placeholder="Andi Barokah">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Status Pengambilan</label>
          <div class="radio-group">
            <label><input type="radio" name="status" value="Perempuan"> Perempuan</label>
            <label><input type="radio" name="status" value="Laki-laki"> Laki-laki</label>
          </div>
        </div>
        <div class="form-group">
          <label for="usia">Usia</label>
          <input type="number" id="usia" placeholder="Masukkan usia" min="0" max="120">
        </div>
      </div>

      <div class="form-actions">
        <button type="reset" class="btn btn-cancel">Batal</button>
        <button type="submit" class="btn btn-submit">Kirim</button>
      </div>
    </form>
  </div>
</body>
</html>
