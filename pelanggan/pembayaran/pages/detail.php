<?php
include '../../../koneksi.php';
session_start();

// AUTO GENERATE setiap kali halaman ini diakses
$sql = "
    SELECT p.id_pengujian
    FROM pengujian p
    LEFT JOIN detail_pembayaran dp ON p.id_pengujian = dp.id_pengujian
    WHERE p.status_pengujian = 'Selesai' AND dp.id_detail IS NULL
";
$result = $connect->query($sql);

$pengujian_list = [];
$total_biaya = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_pengujian = $row['id_pengujian'];
        $biaya = 0;

        if (strpos($id_pengujian, 'JRM-') === 0) {
            $biaya = 160000;
        } elseif (strpos($id_pengujian, 'SRM-') === 0) {
            $biaya = 75000;
        } elseif (strpos($id_pengujian, 'SNRM-') === 0) {
            $biaya = 70000;
        }

        $pengujian_list[] = [
            'id_pengujian' => $id_pengujian,
            'biaya' => $biaya
        ];

        $total_biaya += $biaya;
    }

    if (count($pengujian_list) > 0) {
        // Insert ke tabel pembayaran
        $sql_pembayaran = "
            INSERT INTO pembayaran 
            (nama_pengirim, tanggal_pembayaran, waktu_pembayaran, jenis_pembayaran, total_bayar, bukti_pembayaran, status_pembayaran) 
            VALUES ('Auto System', CURRENT_DATE(), CURRENT_TIME(), 'Auto', ?, '', 'Belum Dikonfirmasi')
        ";
        $stmt = $connect->prepare($sql_pembayaran);
        $stmt->bind_param("i", $total_biaya);
        $stmt->execute();
        $id_pembayaran = $stmt->insert_id;
        $stmt->close();

        // Insert ke detail_pembayaran
        $sql_detail = "INSERT INTO detail_pembayaran (id_pembayaran, id_pengujian, biaya) VALUES (?, ?, ?)";
        $stmt = $connect->prepare($sql_detail);
        foreach ($pengujian_list as $pengujian) {
            $stmt->bind_param("isi", $id_pembayaran, $pengujian['id_pengujian'], $pengujian['biaya']);
            $stmt->execute();
        }
        $stmt->close();
    }
}

$connect->close();
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('../process/proGeneratePembayaran.php')
        .then(response => response.text())
        .then(data => {
            console.log("Auto-generate result:", data);

            if (data.trim() === "success") {
                // Kalau berhasil generate, reload tabel pembayaran (AJAX atau location.reload)
                location.reload();
            }
        })
        .catch(error => console.error("Error auto-generate:", error));
});
</script>
