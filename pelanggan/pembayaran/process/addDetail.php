<?php
include '../../../koneksi.php';
session_start();

function generateIdPembayaran($year, $month) {
    $base_id = 2500; 
    $base_year = 2025; 
    
    $year_diff = $year - $base_year;
    $month_offset = ($year_diff * 12) + $month; 
    return $base_id + $month_offset; 
}

$sql = "
    SELECT p.id_pengujian, p.tanggal_jadi
    FROM pengujian p
    LEFT JOIN detail_pembayaran dp ON p.id_pengujian = dp.id_pengujian
    WHERE p.status_pengujian = 'Selesai' AND dp.id_detail IS NULL
";
$result = $connect->query($sql);

$monthly_data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_pengujian = $row['id_pengujian'];
        $tanggal_jadi = $row['tanggal_jadi'];
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

        $key = "$year-$month";
        if (!isset($monthly_data[$key])) {
            $monthly_data[$key] = [
                'year' => $year,
                'month' => $month,
                'pengujian_list' => [],
                'total_biaya' => 0
            ];
        }

        $monthly_data[$key]['pengujian_list'][] = [
            'id_pengujian' => $id_pengujian,
            'biaya' => $biaya
        ];
        $monthly_data[$key]['total_biaya'] += $biaya;
    }

    foreach ($monthly_data as $key => $data) {
        $year = $data['year'];
        $month = $data['month'];
        $total_biaya = $data['total_biaya'];
        $pengujian_list = $data['pengujian_list'];

        $id_pembayaran = generateIdPembayaran($year, $month);

        $sql_check = "SELECT id_pembayaran, total_bayar FROM pembayaran 
                      WHERE id_pembayaran = ? AND status_pembayaran = 'Belum Bayar'";
        $stmt_check = $connect->prepare($sql_check);
        $stmt_check->bind_param("i", $id_pembayaran);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $row = $result_check->fetch_assoc();
            $existing_total = $row['total_bayar'];
            $new_total = $existing_total + $total_biaya;

            $sql_update = "UPDATE pembayaran SET total_bayar = ? 
                          WHERE id_pembayaran = ?";
            $stmt = $connect->prepare($sql_update);
            $stmt->bind_param("ii", $new_total, $id_pembayaran);
            $stmt->execute();
            $stmt->close();
        } else {
            $sql_pembayaran = "
                INSERT INTO pembayaran 
                (id_pembayaran, nama_pengirim, tanggal_pembayaran, waktu_pembayaran, 
                 jenis_pembayaran, total_bayar, bukti_pembayaran, status_pembayaran) 
                VALUES (?, 'Auto System', CURRENT_DATE(), CURRENT_TIME(), 'Auto', ?, '', 'Belum Bayar')
            ";
            $stmt = $connect->prepare($sql_pembayaran);
            $stmt->bind_param("ii", $id_pembayaran, $total_biaya);
            $stmt->execute();
            $stmt->close();
        }
        $stmt_check->close();
        
        $sql_detail = "INSERT INTO detail_pembayaran (id_pembayaran, id_pengujian, biaya) VALUES (?, ?, ?)";
        $stmt = $connect->prepare($sql_detail);
        foreach ($pengujian_list as $pengujian) {
            $stmt->bind_param("isi", $id_pembayaran, $pengujian['id_pengujian'], $pengujian['biaya']);
            $stmt->execute();
        }
        $stmt->close();
    }
    echo "success"; 
} else {
    echo "no_new_data"; 
}

$connect->close();
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    function checkAndGenerate() {
        fetch('../process/proGeneratePembayaran.php')
            .then(response => response.text())
            .then(data => {
                console.log("Auto-generate result:", data);
                if (data.trim() === "success") {
                    console.log("New payment records generated.");
                } else if (data.trim() === "no_new_data") {
                    console.log("No new data to process.");
                }
            })
            .catch(error => console.error("Error auto-generate:", error));
    }

    checkAndGenerate();
    setInterval(checkAndGenerate, 5000);
});
</script>