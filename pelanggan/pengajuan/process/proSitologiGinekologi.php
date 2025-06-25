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
$jenis = "SRM";
$prefix = $jenis . '-' . $tahun . '-%';

try {
    for ($i = 0; $i < $jumlah; $i++) {
        $nama_dokter = $_POST["namaDokter_$i"] ?? '';
        $alamat_rs = $_POST["rs_$i"] ?? '';
        $nama_pasien = $_POST["namaPasien_$i"] ?? '';
        $usia = isset($_POST["usia_$i"]) ? intval($_POST["usia_$i"]) : 0;
        $jenis_kelamin = $_POST["jk_$i"] ?? '';
        $negara = $_POST["negara_$i"] ?? '';
        $alamat = $_POST["alamat_$i"] ?? '';
        $tanggal_pengajuan = date('Y-m-d');
        $bahan_tersedia = isset($_POST["bahan_$i"]) && is_array($_POST["bahan_$i"]) ? implode(",", $_POST["bahan_$i"]) : '';
        $diambil_dengan = isset($_POST["diambil_$i"]) && is_array($_POST["diambil_$i"]) ? implode(",", $_POST["diambil_$i"]) : '';
        $jumlah_sampel = isset($_POST["jumlahSampel_$i"]) ? intval($_POST["jumlahSampel_$i"]) : 0;
        $jenis_preparat = $_POST["jenis_$i"] ?? '';
        $fiksasi = isset($_POST["perendaman_$i"]) && is_array($_POST["perendaman_$i"]) ? implode(",", $_POST["perendaman_$i"]) : '';
        $status_diri = isset($_POST["statusDiri_$i"]) && is_array($_POST["statusDiri_$i"]) ? implode(",", $_POST["statusDiri_$i"]) : '';
        $jumlah_anak = isset($_POST["jumlahAnak_$i"]) ? intval($_POST["jumlahAnak_$i"]) : 0;
        $kontrasepsi = isset($_POST["kontrasepsi_$i"]) && is_array($_POST["kontrasepsi_$i"]) ? implode(",", $_POST["kontrasepsi_$i"]) : '';
        $keluhan = isset($_POST["keluhan_$i"]) && is_array($_POST["keluhan_$i"]) ? implode(",", $_POST["keluhan_$i"]) : '';
        $cairan_vagina = isset($_POST["cairanVagina_$i"]) && is_array($_POST["cairanVagina_$i"]) ? implode(",", $_POST["cairanVagina_$i"]) : '';
        $keadaan_servix = isset($_POST["keadaanServix_$i"]) && is_array($_POST["keadaanServix_$i"]) ? implode(",", $_POST["keadaanServix_$i"]) : '';
        $pemeriksaan_sitologi = $_POST["sitologi_$i"] ?? '';
        $nomor_pemeriksaan = isset($_POST["noPemeriksa_$i"]) && !empty($_POST["noPemeriksa_$i"]) ? $_POST["noPemeriksa_$i"] : null;
        $diagnosis_klinik = $_POST["diagKlinik_$i"] ?? '';
        $keterangan_penyakit = $_POST["keterangan_$i"] ?? '';

        $missing_pengajuan = [];
        if (empty($nama_dokter)) $missing_pengajuan[] = "nama_dokter";
        if (empty($alamat_rs)) $missing_pengajuan[] = "alamat_rs";
        if (empty($nama_pasien)) $missing_pengajuan[] = "nama_pasien";
        if ($usia <= 0) $missing_pengajuan[] = "usia";
        if (empty($jenis_kelamin)) $missing_pengajuan[] = "jenis_kelamin";
        if (empty($negara)) $missing_pengajuan[] = "negara";
        if (empty($alamat)) $missing_pengajuan[] = "alamat";
        if (empty($bahan_tersedia)) $missing_pengajuan[] = "bahan_tersedia";
        if (empty($diambil_dengan)) $missing_pengajuan[] = "diambil_dengan";
        if ($jumlah_sampel <= 0) $missing_pengajuan[] = "jumlah_sampel";
        if (empty($jenis_preparat)) $missing_pengajuan[] = "jenis_preparat";
        if (empty($fiksasi)) $missing_pengajuan[] = "fiksasi";
        if (empty($status_diri)) $missing_pengajuan[] = "status_diri";
        if (empty($keluhan)) $missing_pengajuan[] = "keluhan";
        if (empty($cairan_vagina)) $missing_pengajuan[] = "cairan_vagina";
        if (empty($keadaan_servix)) $missing_pengajuan[] = "keadaan_servix";
        if (empty($pemeriksaan_sitologi)) $missing_pengajuan[] = "pemeriksaan_sitologi";
        if (!empty($missing_pengajuan)) {
            throw new Exception("Missing required submission fields for submission " . ($i + 1) . ": " . implode(", ", $missing_pengajuan));
        }

        $stmt_count = $connect->prepare("SELECT COUNT(*) FROM pengajuan WHERE id_pengajuan LIKE ?");
        $stmt_count->bind_param("s", $prefix);
        $stmt_count->execute();
        $stmt_count->bind_result($count);
        $stmt_count->fetch();
        $stmt_count->close();

        $urutan = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        $id_pengajuan = "$jenis-$tahun-$urutan";

        $stmt_sitologi = $connect->prepare("INSERT INTO pengajuan (
            id_pengajuan, id_pengguna, nama_dokter, alamat_rs, nama_pasien, usia, jenis_kelamin, negara, alamat, tanggal_pengajuan, bahan_tersedia, diambil_dengan,
            jumlah_sampel, jenis_preparat, fiksasi, status_diri, jumlah_anak,
            kontrasepsi, keluhan, cairan_vagina, keadaan_servix, pemeriksaan_sitologi,
            nomor_pemeriksaan, diagnosis_klinik, keterangan_penyakit, status_pengajuan
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt_sitologi) {
            throw new Exception("Prepare failed for pengajuan: " . $connect->error);
        }
        $status_pengajuan = 'Menunggu Verifikasi';
        $stmt_sitologi->bind_param(
            "sisssissssssisssisssssssss",
            $id_pengajuan, $id_pengguna, $nama_dokter, $alamat_rs, $nama_pasien, $usia, $jenis_kelamin, $negara, $alamat, $tanggal_pengajuan, $bahan_tersedia, $diambil_dengan,
            $jumlah_sampel, $jenis_preparat, $fiksasi, $status_diri, $jumlah_anak,
            $kontrasepsi, $keluhan, $cairan_vagina, $keadaan_servix, $pemeriksaan_sitologi,
            $nomor_pemeriksaan, $diagnosis_klinik, $keterangan_penyakit, $status_pengajuan
        );
        if (!$stmt_sitologi->execute()) {
            throw new Exception("Error inserting into pengajuan: " . $stmt_sitologi->error);
        }
        $stmt_sitologi->close();
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