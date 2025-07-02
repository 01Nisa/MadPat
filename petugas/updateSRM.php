<?php
include '../koneksi.php';

$id_pengambilan = $_GET['id'] ?? 0;

// Ambil data dari tabel pengujian berdasarkan id_pengambilan
$query = "SELECT p.*, 
          FROM pengujian p
          JOIN pengambilan pg ON p.id_pengambilan = pg.id_pengambilan
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

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pengambilan = $_POST['id_pengambilan'];
    $id_pengujian = $_POST['id_pengujian'];
    $tindakan = $_POST['tindakan'];

    // Simpan tindakan dan id_pengujian baru
    $update = $connect->prepare("UPDATE pengujian SET id_pengujian = ?, tindakan = ? WHERE id_pengambilan = ?");
    $update->bind_param("ssi", $id_pengujian, $tindakan, $id_pengambilan);

    if ($update->execute()) {
        header("Location: pengujian.php?sukses=1");
        exit;
    } else {
        echo "Gagal menyimpan data: " . $update->error;
    }
}

// Hitung tanggal jadi berdasarkan jenis pengujian dari id_pengujian
$tanggal_terima = $data['tanggal_terima'] ?? date('Y-m-d');
$tanggal_jadi = null;
if (strpos($data['id_pengujian'], 'JRM-') === 0) {
    $tanggal_jadi = date('Y-m-d', strtotime($tanggal_terima . ' +5 weekdays'));
} elseif (strpos($data['id_pengujian'], 'SRM-') === 0) {
    $tanggal_jadi = date('Y-m-d', strtotime($tanggal_terima . ' +7 weekdays'));
} elseif (strpos($data['id_pengujian'], 'SNRM-') === 0) {
    $tanggal_jadi = date('Y-m-d', strtotime($tanggal_terima . ' +8 weekdays'));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Form Hasil Pengujian</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #89B2B2;
      display: flex;
      justify-content: center;
      padding: 40px;
    }

    .form-container {
      background: white;
      padding: 30px;
      border-radius: 10px;
      width: 600px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }

    h2 {
      color: #075E54;
      margin-bottom: 24px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }

    input[type="text"],
    input[type="date"],
    textarea {
      width: 100%;
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #888;
    }

    .btn-group {
      margin-top: 20px;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    .btn {
      padding: 10px 20px;
      font-weight: bold;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .btn.save {
      background-color: #00796B;
      color: white;
    }

    .btn.cancel {
      background-color: #ccc;
      color: #333;
    }
  </style>
</head>
<body>

<div class="form-container">
  <h2>Form Hasil Pengujian</h2>
  <form method="POST">
    <div class="form-group">
      <label>ID Pengambilan</label>
      <input type="text" name="id_pengambilan" value="<?= htmlspecialchars($data['id_pengambilan']) ?>" readonly>
    </div>

    <div class="form-group">
      <label>No Lab</label>
      <input type="text" name="id_pengujian" value="<?= htmlspecialchars($data['id_pengujian']) ?>" required>
    </div>

    <div class="form-group">
      <label>Tanggal Terima</label>
      <input type="date" name="tanggal_terima" value="<?= htmlspecialchars($tanggal_terima) ?>" readonly>
    </div>

    <div class="form-group">
      <label>Tanggal Jadi</label>
      <input type="date" name="tanggal_jadi" value="<?= htmlspecialchars($tanggal_jadi) ?>" readonly>
    </div>

    <div class="form-group">
      <label>Nama Pasien</label>
      <input type="text" value="<?= htmlspecialchars($data['nama_pasien']) ?>" readonly>
    </div>

    <div class="form-group">
      <label>Usia</label>
      <input type="text" value="<?= htmlspecialchars($data['usia']) ?>" readonly>
    </div>

    <div class="form-group">
      <label>Tindakan</label>
      <textarea name="tindakan" rows="4"><?= htmlspecialchars($data['tindakan'] ?? '') ?></textarea>
    </div>

    <div class="btn-group">
      <button type="submit" class="btn save">Simpan</button>
      <button type="button" onclick="history.back()" class="btn cancel">Batal</button>
    </div>
  </form>
</div>

</body>
</html>
