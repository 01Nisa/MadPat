<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("location:../../../login.php?pesan=belum_login");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengajuan Sampel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            line-height: 1.6;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .letterhead {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #000;
        }

        .letterhead img {
            max-width: 150px;
            margin-bottom: 10px;
        }

        .letterhead h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }

        .letterhead p {
            font-size: 14px;
            color: #666;
        }

        .letterhead .date {
            font-size: 12px;
            margin-top: 10px;
            color: #444;
        }

        .content {
            padding: 20px;
            text-align: justify;
        }

        .content h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }

        .content p {
            font-size: 14px;
            color: #444;
            margin-bottom: 15px;
        }

        .content .signature {
            margin-top: 40px;
            text-align: right;
        }

        .content .signature p {
            font-size: 14px;
            color: #333;
        }

        @media (max-width: 600px) {
            .container {
                width: 100%;
                padding: 10px;
            }
            .letterhead img {
                max-width: 100px;
            }
            .letterhead h1 {
                font-size: 20px;
            }
            .content h2 {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="letterhead">
            <h1>Pengajuan Pengujian Jaringan</h1>
        </div>
        <div class="content">
            <?php
            $nama_pasien = "John Doe";
            $id_pengujian = "JRM-001";
            $tanggal_pengujian = "2025-06-15";
            $hasil = "Normal";

            echo "<p>Kepada Yth. $nama_pasien,</p>";
            echo "<p>Dengan hormat, berikut adalah hasil pengujian sampel dengan ID $id_pengujian yang dilakukan pada tanggal " . date('d F Y', strtotime($tanggal_pengujian)) . ":</p>";
            echo "<p><strong>Hasil Pengujian:</strong> $hasil</p>";
            echo "<p>Untuk detail lebih lanjut, silakan menghubungi kami melalui kontak yang tertera di kop surat. Terima kasih atas kepercayaan Anda kepada MedPath Laboratory.</p>";
            ?>
            <div class="signature">
                <p>Hormat Kami,</p>
                <p>Dr. Anna Widjaja<br>Direktur MedPath Laboratory</p>
            </div>
        </div>
    </div>
</body>
</html>