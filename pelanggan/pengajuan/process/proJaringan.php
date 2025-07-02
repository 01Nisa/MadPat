<?php
session_start();

error_log("proJaringan.php - Session user ID: " . ($_SESSION['user'] ?? 'Not set'));

if (!isset($_SESSION['user'])) {
    error_log("proJaringan.php - Redirecting to login: Session user not set");
    header("location:../../../login.php?pesan=belum_login");
    exit();
}

$user_id = $_SESSION['user'];

include '../../../koneksi.php';

if (!$connect) {
    error_log("proJaringan.php - Database connection failed: " . mysqli_connect_error());
    header("Location: ../pages/pengajuan.php?error=" . urlencode("Database connection failed"));
    exit();
}

$stmt_check_user = $connect->prepare("SELECT id_pengguna FROM pengguna WHERE id_pengguna = ?");
if (!$stmt_check_user) {
    error_log("proJaringan.php - Prepare failed for user check: " . $connect->error);
    header("Location: ../pages/pengajuan.php?error=" . urlencode("Failed to validate user"));
    exit();
}
$stmt_check_user->bind_param("i", $user_id);
$stmt_check_user->execute();
if ($stmt_check_user->get_result()->num_rows === 0) {
    error_log("proJaringan.php - Invalid id_pengguna: $user_id");
    header("Location: ../pages/pengajuan.php?error=" . urlencode("Invalid user ID"));
    exit();
}
$stmt_check_user->close();

error_log("proJaringan.php - POST data: " . print_r($_POST, true));

$jumlah = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : 1;
if ($jumlah < 1) {
    error_log("proJaringan.php - Invalid number of submissions: $jumlah");
    header("Location: ../pages/pengajuan.php?error=" . urlencode("Invalid number of submissions"));
    exit();
}

$connect->begin_transaction();
$tahun = date('Y');
$jenis = "JRM";
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

        $missing_pengajuan = [];
        if (empty($nama_dokter)) $missing_pengajuan[] = "nama_dokter";
        if (empty($alamat_rs)) $missing_pengajuan[] = "alamat_rs";
        if (empty($nama_pasien)) $missing_pengajuan[] = "nama_pasien";
        if ($usia <= 0) $missing_pengajuan[] = "usia";
        if (empty($jenis_kelamin)) $missing_pengajuan[] = "jenis_kelamin";
        if (empty($negara)) $missing_pengajuan[] = "negara";
        if (empty($alamat)) $missing_pengajuan[] = "alamat";
        if (empty($asal_jaringan)) $missing_pengajuan[] = "asal_jaringan";
        if (empty($perendaman)) $missing_pengajuan[] = "perendaman";
        if (empty($diagnosis_klinik)) $missing_pengajuan[] = "diagnosis_klinik";
        if (empty($keterangan_penyakit)) $missing_pengajuan[] = "keterangan_penyakit";
        if (empty($pemeriksaan_patologi)) $missing_pengajuan[] = "pemeriksaan_patologi";
        if (!empty($missing_pengajuan)) {
            throw new Exception("Missing required patient fields for submission " . ($i + 1) . ": " . implode(", ", $missing_pengajuan));
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

        $stmt_pengajuan = $connect->prepare("INSERT INTO pengajuan (
            id_pengajuan, id_pengguna, nama_dokter, alamat_rs, nama_pasien, usia, jenis_kelamin, 
            negara, alamat, tanggal_pengajuan, asal_jaringan, perendaman, diagnosis_klinik, 
            keterangan_penyakit, pemeriksaan_patologi, nomor_pemeriksaan, tanggal_pemeriksaan, 
            diagnosis_pemeriksaan, poliklinik, klas, status_pengajuan
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt_pengajuan) {
            throw new Exception("Prepare failed for pengajuan: " . $connect->error);
        }

        $status_pengajuan = 'Menunggu Verifikasi';
        $stmt_pengajuan->bind_param(
            "sisssisssssssssssssss",
            $id_pengajuan, $user_id, $nama_dokter, $alamat_rs, $nama_pasien, $usia, $jenis_kelamin, 
            $negara, $alamat, $tanggal_pengajuan, $asal_jaringan, $perendaman, 
            $diagnosis_klinik, $keterangan_penyakit, $pemeriksaan_patologi, 
            $nomor_pemeriksaan, $tanggal_pemeriksaan, $diagnosis_pemeriksaan, 
            $poliklinik, $klas, $status_pengajuan
        );
        if (!$stmt_pengajuan->execute()) {
            throw new Exception("Error inserting into pengajuan: " . $stmt_pengajuan->error);
        }
        $stmt_pengajuan->close();
        error_log("proJaringan.php - Successfully inserted pengajuan: $id_pengajuan for id_pengguna: $user_id");
    }

    $connect->commit();
    error_log("proJaringan.php - Transaction committed for $jumlah submissions");
    header("Location: ../pages/pengajuan.php?success=" . urlencode("Data successfully submitted"));
    exit();
} catch (Exception $e) {
    $connect->rollback();
    error_log("proJaringan.php - Transaction rolled back: " . $e->getMessage());
    header("Location: ../pages/pengajuan.php?error=" . urlencode($e->getMessage()));
    exit();
}

$connect->close();
?>