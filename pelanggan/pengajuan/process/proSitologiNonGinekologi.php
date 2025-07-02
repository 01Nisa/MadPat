<?php
session_start();

error_log("proSitologiNonGinekologi.php - Session user ID: " . ($_SESSION['user'] ?? 'Not set'));

if (!isset($_SESSION['user'])) {
    error_log("proSitologiNonGinekologi.php - Redirecting to login: Session user not set");
    header("location:../../../login.php?pesan=belum_login");
    exit();
}


$user_id = $_SESSION['user'];

include '../../../koneksi.php';

if (!$connect) {
    error_log("proSitologiNonGinekologi.php - Database connection failed: " . mysqli_connect_error());
    header("Location: ../pages/pengajuan.php?error=" . urlencode("Database connection failed"));
    exit();
}

$stmt_check_user = $connect->prepare("SELECT id_pengguna FROM pengguna WHERE id_pengguna = ?");
if (!$stmt_check_user) {
    error_log("proSitologiNonGinekologi.php - Prepare failed for user check: " . $connect->error);
    header("Location: ../pages/pengajuan.php?error=" . urlencode("Failed to validate user"));
    exit();
}

$stmt_check_user->bind_param("i", $user_id);
$stmt_check_user->execute();
if ($stmt_check_user->get_result()->num_rows === 0) {
    error_log("proSitologiNonGinekologi.php - Invalid id_pengguna: $user_id");
    header("Location: ../pages/pengajuan.php?error=" . urlencode("Invalid user ID"));
    exit();
}
$stmt_check_user->close();

error_log("proSitologiNonGinekologi.php - POST data: " . print_r($_POST, true));

$jumlah = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : 1;
if ($jumlah < 1) {
    error_log("Invalid number of submissions: $jumlah");
    header("Location: ../../pengajuan.php?error=Invalid number of submissions");
    exit();
}

$connect->begin_transaction();
$tahun = date('Y');
$jenis = "SNRM";
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
        $jumlah_sampel = isset($_POST["jumlahSampel_$i"]) ? intval($_POST["jumlahSampel_$i"]) : 0;
        $jenis_preparat = $_POST["jenis_$i"] ?? '';
        $fiksasi = isset($_POST["perendaman_$i"]) && is_array($_POST["perendaman_$i"]) ? implode(",", $_POST["perendaman_$i"]) : '';
        $pemeriksaan_sitologi = $_POST["sitologi_$i"] ?? '';
        $nomor_pemeriksaan = $_POST["noPemeriksa_$i"] ?? '';
        $jumlah_rokok = isset($_POST["jumlahRokok_$i"]) ? intval($_POST["jumlahRokok_$i"]) : 0;
        $lain = $_POST["lain_$i"] ?? '';
        $tumor = $_POST["tumor_$i"] ?? '';
        $kelenjar_regional = $_POST["kelenjarRegional_$i"] ?? '';
        $jenis_lesi = $_POST["jenisLesi_$i"] ?? '';
        $asal_lesi = $_POST["asalLesi_$i"] ?? '';
        $metastasis = $_POST["metastasis_$i"] ?? '';
        $ro_foto = $_POST["roFoto_$i"] ?? '';
        $tindakan_pemeriksaan = $_POST["tindakanPemeriksaan_$i"] ?? '';
        $status_tindakan = $_POST["statusTindakan_$i"] ?? '';
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
        if ($jumlah_sampel <= 0) $missing_pengajuan[] = "jumlah_sampel";
        if (empty($jenis_preparat)) $missing_pengajuan[] = "jenis_preparat";
        if (empty($fiksasi)) $missing_pengajuan[] = "fiksasi";
        if (empty($pemeriksaan_sitologi)) $missing_pengajuan[] = "pemeriksaan_sitologi";
        if (!empty($missing_pengajuan)) {
            throw new Exception("Missing required submission fields for submission " . ($i + 1) . ": " . implode(", ", $missing_pengajuan));
        }

        $stmt_count = $connect->prepare("SELECT COUNT(*) FROM pengajuan WHERE id_pengajuan LIKE ?");
        if (!$stmt_count) {
            throw new Exception("Prepare failed for count query: " . $connect->error);
        }
        $stmt_count->bind_param("s", $prefix);
        $stmt_count->execute();
        $stmt_count->bind_result($count);
        $stmt_count->fetch();
        $stmt_count->close();
        $urutan = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        $id_pengajuan = "$jenis-$tahun-$urutan";


        $stmt_sitologiNon = $connect->prepare("INSERT INTO pengajuan (
            id_pengajuan, id_pengguna, nama_dokter, alamat_rs, nama_pasien, usia, jenis_kelamin, negara, alamat, tanggal_pengajuan, bahan_tersedia, jumlah_sampel,
            jenis_preparat, fiksasi, pemeriksaan_sitologi, nomor_pemeriksaan, jumlah_rokok,
            lain, tumor, kelenjar_regional, jenis_lesi, asal_lesi, metastasis, ro_foto,
            tindakan_pemeriksaan, status_tindakan, diagnosis_klinik, keterangan_penyakit, status_pengajuan
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt_sitologiNon) {
            throw new Exception("Prepare failed for pengajuan: " . $connect->error);
        }
        $status_pengajuan = 'Menunggu Verifikasi';
        $stmt_sitologiNon->bind_param(
            "sisssisssssissssissssssssssss",
            $id_pengajuan, $user_id, $nama_dokter, $alamat_rs,$nama_pasien, $usia, $jenis_kelamin, $negara, $alamat, $tanggal_pengajuan, $bahan_tersedia, $jumlah_sampel,
            $jenis_preparat, $fiksasi, $pemeriksaan_sitologi, $nomor_pemeriksaan, $jumlah_rokok,
            $lain, $tumor, $kelenjar_regional, $jenis_lesi, $asal_lesi, $metastasis, $ro_foto,
            $tindakan_pemeriksaan, $status_tindakan, $diagnosis_klinik, $keterangan_penyakit, $status_pengajuan
        );
        if (!$stmt_sitologiNon->execute()) {
            throw new Exception("Error inserting into pengajuan: " . $stmt_sitologiNon->error);
        }
        $stmt_sitologiNon->close();
        error_log("proSitologiNonGinekologi.php - Successfully inserted pengajuan: $id_pengajuan for id_pengguna: $user_id");
    }

    $connect->commit();
    error_log("proSitologiNonGinekologi.php - Transaction committed for $jumlah submissions");
    header("Location: ../pages/pengajuan.php?success=" . urlencode("Data successfully submitted"));
    exit();
} catch (Exception $e) {
    $connect->rollback();
    error_log("proSitologiNonGinekologi.php - Transaction rolled back: " . $e->getMessage());
    header("Location: ../pages/pengajuan.php?error=" . urlencode($e->getMessage()));
    exit();
}

$connect->close();
?>