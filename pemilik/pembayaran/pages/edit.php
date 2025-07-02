<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user'])) {
    header("location:../../../login.php?pesan=belum_login");
    exit();
}

$user_id = $_SESSION['user'];
include '../../../koneksi.php';

if (!$connect) {
    error_log("edit.php - Database connection failed: " . mysqli_connect_error());
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['notifikasi'] = [
        'status' => 'error',
        'pesan' => 'ID pembayaran tidak valid.'
    ];
    header("Location: pembayaran.php");
    exit();
}

$payment_id = $_GET['id'];

$sql = "SELECT id_pembayaran, nama_pengirim, total_bayar, tanggal_pembayaran, status_pembayaran 
        FROM pembayaran 
        WHERE id_pembayaran = ?";
$stmt = $connect->prepare($sql);
if (!$stmt) {
    error_log("edit.php - Prepare failed: " . $connect->error);
    $_SESSION['notifikasi'] = [
        'status' => 'error',
        'pesan' => 'Terjadi kesalahan pada database.'
    ];
    header("Location: pembayaran.php");
    exit();
}
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();
$stmt->close();

if (!$payment) {
    error_log("edit.php - No payment found for id_pembayaran: $payment_id");
    $_SESSION['notifikasi'] = [
        'status' => 'error',
        'pesan' => 'Pembayaran tidak ditemukan.'
    ];
    header("Location: pembayaran.php");
    exit();
}

$id_pembayaran = $payment['id_pembayaran'];
$nama_pengirim = $payment['nama_pengirim'];
$total_bayar = number_format($payment['total_bayar'], 0, ',', '.');
$tanggal_pembayaran = $payment['tanggal_pembayaran'] ? date('Y-m-d', strtotime($payment['tanggal_pembayaran'])) : '';
$status_pembayaran = $payment['status_pembayaran'];

$connect->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Edit Pembayaran - MedPath</title>
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
          max-width: 1100px;
          max-height: 680px;
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
          max-height: 610px;
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
      select {
          width: 100%;
          padding: 10px;
          border: 2px solid var(--green1);
          border-radius: 8px;
          background-color: white;
          transition: border-color 0.3s, background-color 0.3s;
          color: #000;
      }

      input:hover,
      select:hover,
      input:focus,
      select:focus {
          border-color: var(--green5);
          background-color: #fff;
          outline: none;
      }

      input[readonly] {
          background-color: #f0f0f0;
          cursor: not-allowed;
          color: #000;
      }

      select {
          appearance: none;
          background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
          background-repeat: no-repeat;
          background-position: right 0.5rem center;
          background-size: 1.5em;
      }

      .button-group {
          display: flex;
          justify-content: flex-end;
          margin-top: 20px;
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
</head>
<body>
<div class="container">
    <div class="close-button" onclick="window.location='pembayaran.php'">×</div>
    <div class="form-container">
        <h2>Edit Pembayaran</h2>
        <form id="formPembayaran" action="../process/proEdit.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-group">
                <label for="id_pembayaran">Nomor Pembayaran</label>
                <input type="text" id="id_pembayaran" name="id_pembayaran" value="<?php echo htmlspecialchars($id_pembayaran); ?>" required />
            </div>
            <div class="form-group">
                <label for="nama_pengirim">Nama Pengirim</label>
                <input type="text" id="nama_pengirim" name="nama_pengirim" value="<?php echo htmlspecialchars($nama_pengirim); ?>" required />
            </div>
            <div class="form-group">
                <label for="total_bayar">Total Pembayaran</label>
                <input type="text" id="total_bayar" name="total_bayar" value="<?php echo htmlspecialchars($total_bayar); ?>" required />
            </div>
            <div class="form-group">
                <label for="tanggal_pembayaran">Tanggal Pembayaran</label>
                <input type="date" id="tanggal_pembayaran" name="tanggal_pembayaran" value="<?php echo htmlspecialchars($tanggal_pembayaran); ?>" required />
            </div>
            <div class="form-group">
                <label for="status_pembayaran">Status Pembayaran</label>
                <select id="status_pembayaran" name="status_pembayaran" required>
                    <option value="Sudah Bayar" <?php echo $status_pembayaran == 'Sudah Bayar' ? 'selected' : ''; ?>>Sudah Bayar</option>
                    <option value="Belum Bayar" <?php echo $status_pembayaran == 'Belum Bayar' ? 'selected' : ''; ?>>Belum Bayar</option>
                </select>
            </div>
            <div class="button-group">
                <button type="button" class="btn" onclick="window.location='pembayaran.php'">Batal</button>
                <button type="submit" class="btn">Simpan</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>