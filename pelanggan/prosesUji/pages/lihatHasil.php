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
    <title>Hasil Pemeriksaan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            line-height: 1.6;
            background-color: rgba(136, 181, 181, 0.26);
            padding: 20px;
        }

        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .icon-button {
            background: none;
            border: none;
            padding: 5px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .icon-button:hover {
            transform: scale(1.1);
        }
        
        .nav-icon {
            width: 24px;
            height: 24px;
        }

        .container {
            width: 638px;
            height: 782px;
            margin: 40px auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .letterhead {
            display: flex;
            text-align: center;
            align-items: center;
            padding: 2px 0;
            border-bottom: 2px solid #000;
        }

        .letterhead .logo {
            margin-right: 30px;
        }

        .letterhead .logo img {
            margin-left: 10px;
            width: 85px;
            height: 85px;
        }

        .letterhead .header-info {
            flex-grow: 1;
        }

        .letterhead .header-info h1 {
            font-size: 16px;
            color: #333;
            margin-bottom: 5px;
            text-align: center;
            margin-left: -35px;
        }

        .letterhead .header-info .contact {
            display: flex;
            margin-left: -10px; 
            align-items: center;
            font-size: 14px;
            color: #666;
        }

        .letterhead .header-info .contact img {
            width: 16px;
            height: 16px;
            margin-left: 3px;
            text-align: center; 
        }

        .title h2 {
            font-size: 16px;
            color: #333;
            margin: 20px 0 10px;
            text-align: center;
        }

        .boxes table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            position: relative;
        }

        .boxes table::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 1px;
            background-color: #000;
            z-index: 1;
        }

        .boxes td {
            border: 1px solid #000;
            padding: 10px;
            vertical-align: top;
            width: 50%;
        }

        .boxes td:first-child {
            border-right: none; 
        }

        .boxes td:last-child {
            border-left: none; 
        }

        .boxes div {
            font-size: 14px;
            color: #444;
        }

        .isi {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .signature {
            text-align: right;
        }

        .signature p {
            font-size: 14px;
            color: #333;
        }

        @media (max-width: 600px) {
            .letterhead {
                flex-direction: column;
                text-align: center;
            }
            .letterhead .logo {
                margin-right: 0;
                margin-bottom: 10px;
            }
            .boxes table::after {
                display: none;
            }
            .boxes td {
                width: 100%;
                border: 1px solid #000; 
            }
            .letterhead .header-info h1 {
                font-size: 20px;
            }
            .title {
                font-size: 16px;
            }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>
    <div class="navigation-buttons">
        <a href="pengujian.php" class="icon-button">
            <img src="../../../assets/back.png" alt="Kembali" class="nav-icon">
        </a>
        <button class="icon-button" onclick="generatePDF()">
            <img src="../../../assets/download.png" alt="Unduh" class="nav-icon">
        </button>
    </div>
    
    <div class="container" id="contentToPrint">
        <?php
        include '../../../koneksi.php';
        $test_id = $_GET['id'];
        
        $jenis_pengujian = '';
        if (strpos($test_id, 'JRM-') === 0) {
            $jenis_pengujian = 'Jaringan';
        } elseif (strpos($test_id, 'SRM-') === 0) {
            $jenis_pengujian = 'Sitologi Ginekologi';
        } elseif (strpos($test_id, 'SNRM-') === 0) {
            $jenis_pengujian = 'Sitologi Non Ginekologi';
        }
        ?>
        
        <div class="letterhead">
            <div class="logo">
                <img src="../../../assets/logo-perusahaan.png" alt="Company Logo">
            </div>
            <div class="header-info">
                <h1>LABORATORIUM KHUSUS PATOLOGI ANATOMI <br> RAHMA MEDIKA</h1>
                <div class="contact">
                    <span>Jalan Bantul KM 9 Cepit Pendowoharjo Sewon Bantul <img src="../../../assets/phone.png" alt="Phone Icon"> 085110197891</span>
                </div>
            </div>
        </div>
        
        <div class="title"><h2>Hasil Pemeriksaan <?php echo $jenis_pengujian; ?></h2></div>
        
        <div class="boxes">
            <table>
                <tr>
                    <td>
                        <?php
                        $query_left = "SELECT tanggal_terima, tanggal_jadi, id_pengujian FROM pengujian WHERE id_pengujian = '$test_id'";
                        $result_left = mysqli_query($connect, $query_left);

                        if (mysqli_num_rows($result_left) > 0) {
                            $kolom_kiri = mysqli_fetch_assoc($result_left);
                            $tanggal_terima = $kolom_kiri['tanggal_terima'];
                            $tanggal_jadi = $kolom_kiri['tanggal_jadi'];
                            $test_id_value = htmlspecialchars($kolom_kiri['id_pengujian']);
                            echo "<div>Tanggal Terima: $tanggal_terima</div>";
                            echo "<div>Tanggal Jadi: $tanggal_jadi</div>";
                            echo "<div>No. Laboratorium: $test_id_value</div>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $query_right = "SELECT nomor_pemeriksaan, nama_pasien, usia, alamat FROM pengujian WHERE id_pengujian = '$test_id'";
                        $result_right = mysqli_query($connect, $query_right);
                        
                        if (mysqli_num_rows($result_right) > 0) {
                            $kolom_kanan = mysqli_fetch_assoc($result_right);
                            $no_pemeriksaan = htmlspecialchars($kolom_kanan['nomor_pemeriksaan']);
                            $nama_pasien = htmlspecialchars($kolom_kanan['nama_pasien']);
                            $usia = htmlspecialchars($kolom_kanan['usia']);
                            $alamat = htmlspecialchars($kolom_kanan['alamat']);
                            echo "<div>Nomor Pemeriksaan: $no_pemeriksaan</div>";
                            echo "<div>Nama: $nama_pasien</div>";
                            echo "<div>Usia: $usia</div>";
                            echo "<div>Alamat: $alamat</div>";
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php
        $query_asal = "SELECT asal_sediaan, diagnosa_klinik, keterangan_klinik, mikroskopis, makroskopis, kesimpulan FROM pengujian WHERE id_pengujian = '$test_id'";
        $result_asal = mysqli_query($connect, $query_asal);
        if (mysqli_num_rows($result_asal) > 0) {
            $data_asal = mysqli_fetch_assoc($result_asal);
            $asal_sediaan = htmlspecialchars($data_asal['asal_sediaan']);
            $diagnosa_klinik = htmlspecialchars($data_asal['diagnosa_klinik']);
            $keterangan_klinik = htmlspecialchars($data_asal['keterangan_klinik']);
            $mikroskopis = htmlspecialchars($data_asal['mikroskopis']);
            $makroskopis = htmlspecialchars($data_asal['makroskopis']);
            $kesimpulan = htmlspecialchars($data_asal['kesimpulan']);
        }
        ?>
        
        <div class="isi"><strong>Asal Sediaan:</strong> <?php echo $asal_sediaan; ?></div>
        <div class="isi"><strong>Diagnosa Klinik:</strong> <?php echo $diagnosa_klinik; ?></div>
        <div class="isi"><strong>Keterangan Klinik:</strong> <?php echo $keterangan_klinik; ?></div>
        <div class="isi"><strong>Mikroskopis:</strong><br><?php echo $mikroskopis; ?></div>
        <div class="isi"><strong>Makroskopis:</strong><br><?php echo $makroskopis; ?></div>
        <div class="isi"><strong>Kesimpulan:</strong><br><?php echo $kesimpulan; ?></div>
        
        <div class="signature">
            <p>Yogyakarta, <?php echo $tanggal_jadi; ?></p><br><br>
            <p>dr. Agus Handoko, Sp.PA</p>
        </div>
    </div>

    <script>
        function generatePDF() {
            const element = document.getElementById('contentToPrint');
            
            const examType = "<?php echo $jenis_pengujian; ?>";
            const filename = 'Hasil_Pemeriksaan_' + examType + '_<?php echo $test_id; ?>.pdf';
        
            const opt = {
                margin: 10,
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2,
                    width: 638,
                    windowWidth: 638
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'portrait' 
                }
            };

            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>