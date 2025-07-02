<?php
include '../koneksi.php';
session_start();

// Ambil data dari form
$id_pengujian        = $_POST['id_pengujian'];
$tanggal_terima      = $_POST['tanggal_terima'];
$tanggal_jadi        = $_POST['tanggal_jadi'];
$nama_pasien         = $_POST['nama_pasien'];
$alamat              = $_POST['alamat'];
$nomor_pemeriksaan   = $_POST['nomor_pemeriksaan'];
$usia                = $_POST['usia'];
$asal_sediaan        = $_POST['asal_sediaan'];
$diagnosa_klinik     = $_POST['diagnosa_klinik'];
$keterangan_klinik   = $_POST['keterangan_klinik'];
$makroskopis         = $_POST['makroskopis'];
$kesimpulan          = $_POST['kesimpulan'];

// Cek apakah id_pengujian sudah ada
$cek = "SELECT id_pengujian FROM pengujian WHERE id_pengujian = '$id_pengujian'";
$result = $connect->query($cek);

if ($result && $result->num_rows > 0) {
    // Update
    $sql = "UPDATE pengujian SET 
      tanggal_terima = '$tanggal_terima',
      tanggal_jadi = '$tanggal_jadi',
      nama_pasien = '$nama_pasien',
      alamat = '$alamat',
      nomor_pemeriksaan = '$nomor_pemeriksaan',
      usia = '$usia',
      asal_sediaan = '$asal_sediaan',
      diagnosa_klinik = '$diagnosa_klinik',
      keterangan_klinik = '$keterangan_klinik',
      makroskopis = '$makroskopis',
      kesimpulan = '$kesimpulan',
      status_pengujian = 'Selesai'
    WHERE id_pengujian = '$id_pengujian'";
} else {
    // Insert
    $sql = "INSERT INTO pengujian (
      id_pengujian, tanggal_terima, tanggal_jadi, nama_pasien, alamat,
      nomor_pemeriksaan, usia, asal_sediaan, diagnosa_klinik, keterangan_klinik,
      makroskopis, kesimpulan, status_pengujian
    ) VALUES (
      '$id_pengujian', '$tanggal_terima', '$tanggal_jadi', '$nama_pasien', '$alamat',
      '$nomor_pemeriksaan', '$usia', '$asal_sediaan', '$diagnosa_klinik', '$keterangan_klinik',
      '$makroskopis', '$kesimpulan', 'Selesai'
    )";
}

// Eksekusi simpan data
if ($connect->query($sql) === TRUE) {

    // ============== AUTO MASUKKAN KE DETAIL PEMBAYARAN ==============
    function generateIdPembayaran($year, $month) {
        $base_id = 2500;
        $base_year = 2025;
        $year_diff = $year - $base_year;
        $month_offset = ($year_diff * 12) + $month;
        return $base_id + $month_offset;
    }

    // Cek biaya berdasarkan prefix ID
    $biaya = 0;
    if (strpos($id_pengujian, 'JRM-') === 0) {
        $biaya = 160000;
    } elseif (strpos($id_pengujian, 'SRM-') === 0) {
        $biaya = 75000;
    } elseif (strpos($id_pengujian, 'SNRM-') === 0) {
        $biaya = 70000;
    }

    $date = new DateTime($tanggal_jadi);
    $year = (int)$date->format('Y');
    $month = (int)$date->format('m');
    $id_pembayaran = generateIdPembayaran($year, $month);

    // Cek apakah pembayaran untuk bulan itu sudah ada
    $sql_check = "SELECT id_pembayaran, total_bayar FROM pembayaran 
                  WHERE id_pembayaran = ? AND status_pembayaran = 'Belum Bayar'";
    $stmt_check = $connect->prepare($sql_check);
    $stmt_check->bind_param("i", $id_pembayaran);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Jika sudah ada, update total_bayar
        $row = $result_check->fetch_assoc();
        $new_total = $row['total_bayar'] + $biaya;

        $sql_update = "UPDATE pembayaran SET total_bayar = ? 
                      WHERE id_pembayaran = ?";
        $stmt = $connect->prepare($sql_update);
        $stmt->bind_param("ii", $new_total, $id_pembayaran);
        $stmt->execute();
        $stmt->close();
    } else {
        // Jika belum ada, buat entri baru pembayaran
        $sql_pembayaran = "
            INSERT INTO pembayaran 
            (id_pembayaran, nama_pengirim, tanggal_pembayaran, waktu_pembayaran, 
             jenis_pembayaran, total_bayar, bukti_pembayaran, status_pembayaran) 
            VALUES (?, 'Auto System', CURRENT_DATE(), CURRENT_TIME(), 'Auto', ?, '', 'Belum Bayar')
        ";
        $stmt = $connect->prepare($sql_pembayaran);
        $stmt->bind_param("ii", $id_pembayaran, $biaya);
        $stmt->execute();
        $stmt->close();
    }
    $stmt_check->close();

    // Masukkan ke detail_pembayaran jika belum ada
    $cek_detail = "SELECT * FROM detail_pembayaran WHERE id_pengujian = '$id_pengujian'";
    $cek_result = $connect->query($cek_detail);
    if ($cek_result->num_rows === 0) {
        $stmt_detail = $connect->prepare("INSERT INTO detail_pembayaran (id_pembayaran, id_pengujian, biaya) VALUES (?, ?, ?)");
        $stmt_detail->bind_param("isi", $id_pembayaran, $id_pengujian, $biaya);
        $stmt_detail->execute();
        $stmt_detail->close();
    }

    // ============== NOTIFIKASI BERHASIL ==============
    echo "
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data berhasil disimpan.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'pengujian.php';
                }
            });
        </script>
    </body>
    </html>
    ";
} else {
    echo "Error saat menyimpan data: " . $connect->error;
}
?>
