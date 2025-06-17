<?php
include '../../../koneksi.php';

error_log(print_r($_POST, true));

$jumlah = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : 1;
if ($jumlah < 1) {
    error_log("Invalid number of submissions: $jumlah");
    header("Location: ../../pengajuan.php?error=Invalid number of submissions");
    exit();
}

$connect->begin_transaction();
$tahun = date('Y');

try {
    for ($i = 0; $i < $jumlah; $i++) {
        // Data pelanggan
        $nama_pasien = $_POST["namaPasien_$i"] ?? '';
        $usia = isset($_POST["usia_$i"]) ? intval($_POST["usia_$i"]) : 0;
        $jenis_kelamin = $_POST["jk_$i"] ?? '';
        $negara = $_POST["negara_$i"] ?? '';
        $alamat = $_POST["alamat_$i"] ?? '';

        $missing_pelanggan = [];
        if (empty($nama_pasien)) $missing_pelanggan[] = "nama_pasien";
        if ($usia <= 0) $missing_pelanggan[] = "usia";
        if (empty($jenis_kelamin)) $missing_pelanggan[] = "jenis_kelamin";
        if (empty($negara)) $missing_pelanggan[] = "negara";
        if (empty($alamat)) $missing_pelanggan[] = "alamat";
        if (!empty($missing_pelanggan)) {
            throw new Exception("Missing required patient fields for submission " . ($i + 1) . ": " . implode(", ", $missing_pelanggan));
        }

        $stmt_pelanggan = $connect->prepare("INSERT INTO pelanggan (nama_pasien, usia, jenis_kelamin, negara, alamat) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt_pelanggan) {
            throw new Exception("Prepare failed for pelanggan: " . $connect->error);
        }
        $stmt_pelanggan->bind_param("sisss", $nama_pasien, $usia, $jenis_kelamin, $negara, $alamat);
        if (!$stmt_pelanggan->execute()) {
            throw new Exception("Error inserting into pelanggan: " . $stmt_pelanggan->error);
        }
        $id_pelanggan = $connect->insert_id;
        $stmt_pelanggan->close();

        // Data pengajuan jaringan
        $nama_dokter = $_POST["namaDokter_$i"] ?? '';
        $alamat_rs = $_POST["rs_$i"] ?? '';
        $asal_jaringan = $_POST["asal_$i"] ?? '';
        $perendaman = $_POST["perendaman_$i"] ?? '';
        $diagnosis_klinik = $_POST["diagKlinik_$i"] ?? '';
        $keterangan_penyakit = $_POST["keterangan_$i"] ?? '';
        $pemeriksaan_patologi = $_POST["patologi_$i"] ?? '';
        $nomor_pemeriksaan = isset($_POST["noPemeriksa_$i"]) && !empty($_POST["noPemeriksa_$i"]) ? $_POST["noPemeriksa_$i"] : null;
        $tanggal_pemeriksaan = isset($_POST["tglPeriksa_$i"]) && !empty($_POST["tglPeriksa_$i"]) ? $_POST["tglPeriksa_$i"] : null;
        $diagnosis_pemeriksaan = isset($_POST["diagPeriksa_$i"]) && !empty($_POST["diagPeriksa_$i"]) ? $_POST["diagPeriksa_$i"] : null;
        $poliklinik = isset($_POST["poliklinik_$i"]) && !empty($_POST["poliklinik_$i"]) ? $_POST["poliklinik_$i"] : null;
        $klas = isset($_POST["klas_$i"]) && !empty($_POST["klas_$i"]) ? $_POST["klas_$i"] : null;
        $tanggal_pengajuan = date('Y-m-d');

        $missing_pengajuan = [];
        if (empty($nama_dokter)) $missing_pengajuan[] = "nama_dokter";
        if (empty($alamat_rs)) $missing_pengajuan[] = "alamat_rs";
        if (empty($asal_jaringan)) $missing_pengajuan[] = "asal_jaringan";
        if (empty($perendaman)) $missing_pengajuan[] = "perendaman";
        if (empty($diagnosis_klinik)) $missing_pengajuan[] = "diagnosis_klinik";
        if (empty($keterangan_penyakit)) $missing_pengajuan[] = "keterangan_penyakit";
        if (empty($pemeriksaan_patologi)) $missing_pengajuan[] = "pemeriksaan_patologi";
        if (!empty($missing_pengajuan)) {
            throw new Exception("Missing required submission fields for submission " . ($i + 1) . ": " . implode(", ", $missing_pengajuan));
        }

        // Hitung urutan berdasarkan jumlah pengajuan di tahun ini
        $stmt_count = $connect->prepare("SELECT COUNT(*) FROM pengajuan WHERE YEAR(tanggal_pengajuan) = ?");
        $stmt_count->bind_param("i", $tahun);
        $stmt_count->execute();
        $stmt_count->bind_result($count);
        $stmt_count->fetch();
        $stmt_count->close();
        $urutan = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        $id_pengajuan = "JRM-$tahun-$urutan";

        $stmt_pengajuan = $connect->prepare("INSERT INTO pengajuan (
            id_pengajuan, id_pelanggan, nama_dokter, alamat_rs, asal_jaringan, perendaman,
            diagnosis_klinik, keterangan_penyakit, pemeriksaan_patologi,
            nomor_pemeriksaan, tanggal_pemeriksaan, diagnosis_pemeriksaan,
            poliklinik, klas, tanggal_pengajuan, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt_pengajuan) {
            throw new Exception("Prepare failed for pengajuan: " . $connect->error);
        }

        $status = 'Menunggu Verifikasi';
        $stmt_pengajuan->bind_param(
            "sissssssssssssss",
            $id_pengajuan, $id_pelanggan, $nama_dokter, $alamat_rs, $asal_jaringan, $perendaman,
            $diagnosis_klinik, $keterangan_penyakit, $pemeriksaan_patologi,
            $nomor_pemeriksaan, $tanggal_pemeriksaan, $diagnosis_pemeriksaan,
            $poliklinik, $klas, $tanggal_pengajuan, $status
        );
        if (!$stmt_pengajuan->execute()) {
            throw new Exception("Error inserting into pengajuan: " . $stmt_pengajuan->error);
        }
        $stmt_pengajuan->close();
    }

    $connect->commit();
    header("Location: ../pages/pengajuan.php?success=Data successfully submitted");
    exit();
} catch (Exception $e) {
    $connect->rollback();
    error_log($e->getMessage());
    header("Location: ../pages/pengajuan.php?error=" . urlencode($e->getMessage()));
    exit();
}

$connect->close();
?>