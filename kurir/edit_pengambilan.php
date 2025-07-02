<?php
include '../koneksi.php';

// Ambil ID dari GET (untuk ambil data awal)
$id_pengambilan = $_GET['id'] ?? 0;

$query = "SELECT p.*, pj.nama_pasien, pj.alamat AS alamat_rs
          FROM pengambilan p
          JOIN pengajuan pj ON p.id_pengajuan = pj.id_pengajuan
          WHERE p.id_pengambilan = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("i", $id_pengambilan);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    echo "Data tidak ditemukan.";
    exit;
}

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pengambilan = $_POST['id_pengambilan'] ?? null;
    $tanggal_pengambilan = $_POST['tanggal_pengambilan'] ?? '';
    $tanggal_pengambilan_ulang = $_POST['tanggal_pengambilan_ulang'] ?? null;
    $status_pengambilan = $_POST['status'] ?? '';

    if (!$id_pengambilan) {
        echo "ID pengambilan tidak ditemukan.";
        exit;
    }

    

    // Update data
    $update = $connect->prepare("UPDATE pengambilan 
                                 SET tanggal_pengambilan = ?, 
                                     tanggal_pengambilan_ulang = ?, 
                                     status_pengambilan = ?
                                 WHERE id_pengambilan = ?");
    $update->bind_param("sssi", $tanggal_pengambilan, $tanggal_pengambilan_ulang, $status_pengambilan, $id_pengambilan);
    
    if ($update->execute()) {
        if ($status_pengambilan === 'Selesai') {
            $cek = $connect->prepare("SELECT COUNT(*) as total FROM pengujian WHERE id_pengambilan = ?");
            $cek->bind_param("i", $id_pengambilan);
            $cek->execute();
            $cek_result = $cek->get_result()->fetch_assoc();

            if ($cek_result['total'] == 0) {
                $insert = $connect->prepare("INSERT INTO pengujian (id_pengambilan, status_pengujian, tanggal_terima) 
                                             VALUES (?, 'Diproses', ?)");
                $insert->bind_param("is", $id_pengambilan, $tanggal_pengambilan);
                $insert->execute();
            }
        }

        header("Location: pengambilan.php");
        exit;
    } else {
        echo "Gagal menyimpan data: " . $update->error;
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Pengambilan Sampel</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #89B2B2;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .modal {
      background-color: #fff;
      border-radius: 16px;
      box-shadow: 8px 8px 0 rgba(0, 0, 0, 0.2);
      padding: 40px;
      width: 720px;
    }

    .modal h2 {
      margin-bottom: 24px;
      color: #00A19D;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
    }

    input[type="text"],
    input[type="date"] {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #009688;
      border-radius: 8px;
      outline: none;
      font-size: 14px;
    }

    .form-row {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    .form-row .form-group {
      flex: 1;
    }

    .radio-group {
      display: flex;
      gap: 30px;
      margin-top: 10px;
    }

    .radio-group label {
      font-weight: normal;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-group {
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      margin-top: 32px;
    }

    .btn {
      padding: 10px 20px;
      border-radius: 8px;
      border: none;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
    }

    .btn.cancel {
      background-color: #f0f0f0;
      color: #aaa;
    }

    .btn.submit {
      background-color: #00796B;
      color: white;
    }
  </style>
</head>
<body>

<div class="modal">
  <h2>Edit Pengambilan Sampel</h2>
  <form method="POST" action="">
    <input type="hidden" name="id_pengambilan" value="<?= htmlspecialchars($data['id_pengambilan']) ?>">

    <div class="form-group">
      <label for="nama_pasien">Nama Pasien</label>
      <input type="text" id="nama_pasien" value="<?= htmlspecialchars($data['nama_pasien']) ?>" readonly />
    </div>

    <div class="form-group">
      <label for="lokasi_rs">Lokasi/RS</label>
      <input type="text" id="lokasi_rs" value="<?= htmlspecialchars($data['alamat_rs']) ?>" readonly />
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="tanggal_pengambilan">Tanggal Pengambilan</label>
        <input type="date" id="tanggal_pengambilan" name="tanggal_pengambilan"
               value="<?= htmlspecialchars($data['tanggal_pengambilan']) ?>" required />
      </div>
      <div class="form-group">
        <label for="tanggal_pengambilan_ulang">Tanggal Pengambilan Ulang</label>
        <input type="date" id="tanggal_pengambilan_ulang" name="tanggal_pengambilan_ulang"
               value="<?= htmlspecialchars($data['tanggal_pengambilan_ulang']) ?>" />
      </div>
    </div>

    <div class="form-group">
      <label>Status Pengambilan</label>
      <div class="radio-group">
        <label>
          <input type="radio" name="status" value="Menunggu" <?= ($data['status_pengambilan'] == 'Menunggu') ? 'checked' : '' ?>> Menunggu
        </label>
        <label>
          <input type="radio" name="status" value="Tertunda" <?= ($data['status_pengambilan'] == 'Tertunda') ? 'checked' : '' ?>> Tertunda
        </label>
        <label>
          <input type="radio" name="status" value="Selesai" <?= ($data['status_pengambilan'] == 'Selesai') ? 'checked' : '' ?>> Selesai
        </label>
      </div>
    </div>

    <div class="btn-group">
      <button type="button" class="btn cancel" onclick="history.back()">Batal</button>
      <button type="submit" class="btn submit">Kirim</button>
    </div>
  </form>
</div>

</body>
</html>
