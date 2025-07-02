<?php 
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tolak_id'])) {
    $id_pengajuan = $_POST['tolak_id'];
    $sql = "UPDATE pengajuan SET status_pengajuan = 'Ditolak' WHERE id_pengajuan = '$id_pengajuan'";

    if ($connect->query($sql) === TRUE) {
        header("Location: penerimaan.php?tolak=1");
        exit();
    } else {
        echo "Gagal memperbarui status: " . $connect->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setujui_id'])) {
    $id_pengajuan = $_POST['setujui_id'];

    // Ambil data dari pengajuan
    $query_select = "SELECT * FROM pengajuan WHERE id_pengajuan = '$id_pengajuan'";
    $result_select = $connect->query($query_select);

    if ($result_select && $result_select->num_rows > 0) {
        $data = $result_select->fetch_assoc();

      
        $id_pengujian = $data['id_pengajuan']; 
        $nama_pasien = $data['nama_pasien'];
        $usia = $data['usia'];
        $alamat = $data['alamat'];
        $nomor_pemeriksaan = $data['nomor_pemeriksaan'];
        $tanggal_terima = date('Y-m-d'); // hari ini
        $status_pengujian = 'Diproses';

        // Tentukan tanggal_jadi berdasarkan jenis pengujian
        $jenis_pengajuan = '';
        if (strpos($id_pengujian, 'JRM-') === 0) {
            $jenis_pengajuan = 'Jaringan';
            $tanggal_jadi = date('Y-m-d', strtotime($tanggal_terima . ' +5 days'));
        } elseif (strpos($id_pengujian, 'SRM-') === 0) {
            $jenis_pengajuan = 'Sitologi Ginekologi';
            $tanggal_jadi = date('Y-m-d', strtotime($tanggal_terima . ' +7 days'));
        } elseif (strpos($id_pengujian, 'SNRM-') === 0) {
            $jenis_pengajuan = 'Sitologi Non Ginekologi';
            $tanggal_jadi = date('Y-m-d', strtotime($tanggal_terima . ' +8 days'));
        } else {
            $tanggal_jadi = NULL; // fallback kalau jenis tidak dikenali
        }

        // Insert ke tabel pengujian
        $sql_insert = "INSERT INTO pengujian (id_pengujian, nama_pasien, usia, alamat, nomor_pemeriksaan, tanggal_terima, tanggal_jadi, status_pengujian) 
                      VALUES ('$id_pengujian', '$nama_pasien', '$usia', '$alamat', '$nomor_pemeriksaan', '$tanggal_terima', '$tanggal_jadi', '$status_pengujian')";

        if ($connect->query($sql_insert) === TRUE) {
            // Update status pengajuan
            $sql_update = "UPDATE pengajuan SET status_pengajuan = 'Verifikasi' WHERE id_pengajuan = '$id_pengajuan'";
            if ($connect->query($sql_update) === TRUE) {
                header("Location: penerimaan.php?success=1");
                exit();
            } else {
                echo "Gagal update status pengajuan.";
            }
        } else {
            echo "Gagal insert ke pengujian: " . $connect->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Pengajuan Pengujian</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: #89B2B2;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 40px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background: white;
      padding: 40px;
      border-radius: 8px;
      max-width: 700px;
      width: 100%;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 24px;
    }

    .section {
      margin-bottom: 20px;
    }

    .flexbox {
      display: flex;
      justify-content: space-between;
      border: 1px solid #000;
      padding: 12px;
      margin-bottom: 20px;
      gap: 20px;
    }

    .flexbox > div {
      width: 48%;
      word-wrap: break-word;
    }

    .label {
      font-weight: bold;
    }

    .button-group {
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      margin-top: 24px;
    }

    button {
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      cursor: pointer;
    }

    .btn-tolak {
      background-color: #ccc;
      color: #333;
    }

    .btn-terima {
      background-color: #075E54;
      color: white;
    }
  </style>
</head>
<body>
<?php
include 'koneksi.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';
$query = "SELECT * FROM pengajuan WHERE id_pengajuan = '$id'";
$result = $connect->query($query);
$data = $result->fetch_assoc();
?>

  <div class="container">
    <h2>Pengajuan Pengujian Sitologi Ginekologi</h2>

    <div class="flexbox">
      <div>
        <div class="label">Dokter :</div>
        Nama : <?= htmlspecialchars($data['nama_dokter']) ?><br>
        Alamat/RS : <?= htmlspecialchars($data['alamat_rs']) ?>
      </div>
      <div>
        <div class="label">Yang diperiksa :</div>
          <table>
            <tr>
              <td>Nama</td>
              <td>:</td>
              <td><?= htmlspecialchars($data['nama_pasien']) ?></td>
            </tr>
            <tr>
              <td>Jenis Kelamin</td>
              <td>:</td>
              <td><?= htmlspecialchars($data['jenis_kelamin']) ?></td>
            </tr>
            <tr>
              <td>Usia</td>
              <td>:</td>
              <td><?= htmlspecialchars($data['usia']) ?></td>
            </tr>
            <tr>
              <td>Bangsa</td>
              <td>:</td>
              <td><?= htmlspecialchars($data['negara']) ?></td>
            </tr>
            <tr>
              <td>Alamat</td>
              <td>:</td>
              <td><?= htmlspecialchars($data['alamat']) ?></td>
            </tr>
          </table>
      </div>
    </div>

        <div class="section">
      <div class="label">Keterangan Sampel</div>
      <table>
        <tr><td>Bahan sediaan</td><td>:</td><td><?= htmlspecialchars($data['bahan_tersedia'] ?? '-') ?: '-' ?></td></tr>
        <tr><td>Diambil dengan</td><td>:</td><td><?= htmlspecialchars($data['diambil_dengan'] ?? '-') ?: '-' ?></td></tr>
        <tr><td>Jumlah sampel dikirim</td><td>:</td><td><?= htmlspecialchars($data['jumlah_sampel'] ?? '-') ?: '-' ?></td></tr>
        <tr><td>Jenis preparat</td><td>:</td><td><?= htmlspecialchars($data['jenis_preparat'] ?? '-') ?: '-' ?></td></tr>
        <tr><td>Fiksasi</td><td>:</td><td><?= htmlspecialchars($data['fiksasi'] ?? '-') ?: '-' ?></td></tr>
      </table>
    </div>

    <div class="section">
      <div class="label">Keterangan Klinik</div>
      <table>
        <tr><td>Status</td><td>:</td><td><?= htmlspecialchars($data['status_diri'] ?? '-') ?: '-' ?></td></tr>
        <tr><td>Jumlah anak</td><td>:</td><td><?= htmlspecialchars($data['jumlah_anak'] ?? '-') ?: '-' ?></td></tr>
        <tr><td>Kontrasepsi</td><td>:</td><td><?= htmlspecialchars($data['kontrasepsi'] ?? '-') ?: '-' ?></td></tr>
        <tr><td>Keluhan</td><td>:</td><td><?= htmlspecialchars($data['keluhan'] ?? '-') ?: '-' ?></td></tr>
      </table>
    </div>

    <div class="section">
      <div class="label">Pemeriksaan</div>
      <table>
        <tr><td>Cairan vagina</td><td>:</td><td><?= htmlspecialchars($data['cairan_vagina'] ?? '-') ?: '-' ?></td></tr>
        <tr><td>Keadaan servik</td><td>:</td><td><?= htmlspecialchars($data['keadaan_servix'] ?? '-') ?: '-' ?></td></tr>
        <tr><td>Pemeriksaan sitologi</td><td>:</td><td><?= htmlspecialchars($data['pemeriksaan_sitologi'] ?? '-') ?: '-' ?></td></tr>
        <tr><td>Nomor Pemeriksaan</td><td>:</td><td><?= htmlspecialchars($data['nomor_pemeriksaan'] ?? '-') ?: '-' ?></td></tr>
        <tr><td>Diagnosis</td><td>:</td><td><?= htmlspecialchars($data['diagnosis_pemeriksaan'] ?? '-') ?: '-' ?></td></tr>
        <tr><td>Keterangan klinik</td><td>:</td><td><?= htmlspecialchars($data['keterangan_penyakit'] ?? '-') ?: '-' ?></td></tr>
        <tr><td>Diagnosa klinik</td><td>:</td><td><?= htmlspecialchars($data['diagnosis_klinik'] ?? '-') ?: '-' ?></td></tr>
      </table>
    </div>


    <div class="button-group">
      <form method="post">
        <input type="hidden" name="setujui_id" value="<?= htmlspecialchars($data['id_pengajuan']) ?>">
        <button type="button" class="btn-tolak" onclick="konfirmasiTolak('<?= $data['id_pengajuan'] ?>')">Tolak</button>
        <button type="submit" class="btn-terima" name="setujui_id" value="<?= $data['id_pengajuan'] ?>">Terima</button>
      </form>
    </div>
  </div>
 


    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
      <script>alert('Berhasil menyetujui pengajuan!');</script>
    <?php endif; ?>

    <?php if (isset($_GET['tolak']) && $_GET['tolak'] == 1): ?>
      <script>alert('Pengajuan telah ditolak.');</script>
    <?php endif; ?>


  <script>
  function konfirmasiTolak(id) {
    Swal.fire({
      title: 'Tolak Pengajuan?',
      text: "Anda yakin ingin menolak pengajuan ini?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        // Kirim form tolak menggunakan POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = ''; // Arahkan ke halaman ini sendiri

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'tolak_id';
        input.value = id;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
</script>
</body>
</html>
