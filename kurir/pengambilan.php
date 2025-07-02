<?php
include '../koneksi.php';

$sql = "SELECT 
            pengambilan.id_pengambilan,
            pengambilan.id_pengajuan,
            pengambilan.tanggal_pengambilan,
            pengambilan.status_pengambilan,
            pengambilan.tanggal_pengambilan_ulang,
            pengajuan.nama_pasien,
            pengajuan.alamat
        FROM pengambilan
        JOIN pengajuan ON pengambilan.id_pengajuan = pengajuan.id_pengajuan
        ORDER BY pengambilan.tanggal_pengambilan DESC";

$result = $connect->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pengambilan</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background-color: #f0f4f4;
        }

        h2 {
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 12px 16px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #147472;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .btn {
            padding: 6px 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn.edit {
            background-color: #2196F3;
            color: white;
        }

        .btn.delete {
            background-color: #f44336;
            color: white;
        }

        .btn.edit:hover {
            background-color: #1976D2;
        }

        .btn.delete:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>

<h2>Data Pengambilan Sampel</h2>

<table>
    <tr>
        <th>Nama</th>
        <th>Lokasi</th>
        <th>Jenis Sampel</th>
        <th>Tanggal Pengambilan</th>
        <th>Status Pengambilan</th>
        <th>Pengambilan Ulang</th>
        <th>Tindakan</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
                $id_pengajuan = $row['id_pengajuan'];
                $jenis_pengajuan = '';

                if (strpos($id_pengajuan, 'JRM-') === 0) {
                    $jenis_pengajuan = 'Jaringan';
                } elseif (strpos($id_pengajuan, 'SRM-') === 0) {
                    $jenis_pengajuan = 'Sitologi Ginekologi';
                } elseif (strpos($id_pengajuan, 'SNRM-') === 0) {
                    $jenis_pengajuan = 'Sitologi Non Ginekologi';
                }
            ?>
            <tr>
                <td><?= htmlspecialchars($row['nama_pasien']) ?></td>
                <td><?= htmlspecialchars($row['alamat']) ?></td>
                <td><?= $jenis_pengajuan ?></td>
                <td><?= date('Y/m/d', strtotime($row['tanggal_pengambilan'])) ?></td>
                <td><?= htmlspecialchars($row['status_pengambilan']) ?></td>
                <td><?= htmlspecialchars($row['tanggal_pengambilan_ulang']) ?></td>
                <td>
                    <a href="edit_pengambilan.php?id=<?= $row['id_pengambilan'] ?>" class="btn edit">Edit</a>
                    <button class="btn delete" onclick="hapusData('<?= $row['id_pengambilan'] ?>')">Hapus</button>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="7">Tidak ada data pengambilan.</td>
        </tr>
    <?php endif; ?>
</table>

<script>
    function hapusData(id) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "hapus_pengambilan.php?id=" + id;
            }
        });
    }
</script>

</body>
</html>
