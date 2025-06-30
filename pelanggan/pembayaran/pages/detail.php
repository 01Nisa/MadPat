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
            height: auto;
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

        .payment-section {
            margin-top: 20px;
        }

        .payment-section h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .payment-section table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            page-break-inside: auto; 
        }

        .payment-section td, .payment-section th {
            border: 1px solid #000;
            padding: 1px;
            text-align: left;
            word-wrap: break-word; 
            max-width: 0;
        }

        .payment-section .total {
            font-weight: bold;
            text-align: right;
        }

        .summary-table {
            margin-top: 20px;
            width: 100%;
            page-break-inside: auto;
        }

        .summary-table td, .summary-table th {
            border: 1px solid #000;
            padding: 1px;
            text-align: left;
            word-wrap: break-word;
            max-width: 0;
        }

        .signature {
            text-align: right;
            margin-top: 20px;
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
            .payment-section table {
                font-size: 12px;
            }
        }

        @page {
            size: A4;
            margin: 10mm;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>
    <div class="navigation-buttons">
        <a href="pembayaran.php" class="icon-button">
            <img src="../../../assets/back.png" alt="Kembali" class="nav-icon">
        </a>
        <button class="icon-button" onclick="downloadPaymentDetails()">
            <img src="../../../assets/download.png" alt="Unduh" class="nav-icon">
        </button>
    </div>
    
    <div class="container" id="contentToPrint">
        <?php
        include '../../../koneksi.php';
        $id_pengujian = $_GET['id_pengujian'] ?? '';

        $query_pengujian = "SELECT p.*, dp.biaya, dp.id_pembayaran 
                            FROM pengujian p 
                            LEFT JOIN detail_pembayaran dp ON p.id_pengujian = dp.id_pengujian 
                            WHERE p.id_pengujian = ? OR dp.id_pembayaran IS NOT NULL";
        $stmt = $connect->prepare($query_pengujian);
        $stmt->bind_param("s", $id_pengujian);
        $stmt->execute();
        $result_pengujian = $stmt->get_result();
        $pengujian_data = $result_pengujian->fetch_assoc();
        $stmt->close();

        $jenis_pengujian = '';
        if (strpos($id_pengujian, 'JRM-') === 0) {
            $jenis_pengujian = 'Jaringan';
        } elseif (strpos($id_pengujian, 'SRM-') === 0) {
            $jenis_pengujian = 'Sitologi Ginekologi';
        } elseif (strpos($id_pengujian, 'SNRM-') === 0) {
            $jenis_pengujian = 'Sitologi Non Ginekologi';
        }

        $tanggal_jadi = $pengujian_data['tanggal_jadi'] ?? date('Y-m-d');
        $nama_pasien = $pengujian_data['nama_pasien'] ?? 'N/A';
        $biaya = $pengujian_data['biaya'] ?? 0;
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
        
        <div class="title"><h2>JUMLAH TAGIHAN SAMPEL</h2></div>
        
        <?php
        $current_month = date('m');
        $current_year = date('Y');
        $query_details = "
            SELECT p.id_pengujian, p.nama_pasien, dp.biaya, p.tanggal_jadi
            FROM pengujian p
            JOIN detail_pembayaran dp ON p.id_pengujian = dp.id_pengujian
            WHERE MONTH(p.tanggal_jadi) = ? AND YEAR(p.tanggal_jadi) = ?
        ";
        $stmt = $connect->prepare($query_details);
        $stmt->bind_param("ii", $current_month, $current_year);
        $stmt->execute();
        $result_details = $stmt->get_result();

        $jaringan_total = 0;
        $sitologi_ginekologi_total = 0;
        $sitologi_non_ginekologi_total = 0;
        $jaringan_samples = [];
        $sitologi_ginekologi_samples = [];
        $sitologi_non_ginekologi_samples = [];

        while ($row = $result_details->fetch_assoc()) {
            $exam_type = '';
            if (strpos($row['id_pengujian'], 'JRM-') === 0) {
                $exam_type = 'Jaringan';
                $jaringan_samples[] = $row;
                $jaringan_total += $row['biaya'];
            } elseif (strpos($row['id_pengujian'], 'SRM-') === 0) {
                $exam_type = 'Sitologi Ginekologi';
                $sitologi_ginekologi_samples[] = $row;
                $sitologi_ginekologi_total += $row['biaya'];
            } elseif (strpos($row['id_pengujian'], 'SNRM-') === 0) {
                $exam_type = 'Sitologi Non Ginekologi';
                $sitologi_non_ginekologi_samples[] = $row;
                $sitologi_non_ginekologi_total += $row['biaya'];
            }
        }
        $stmt->close();

        $total_keseluruhan = $jaringan_total + $sitologi_ginekologi_total + $sitologi_non_ginekologi_total;
        ?>

        <div class="payment-section">
            <?php if (!empty($jaringan_samples)): ?>
                <h3>A. Pengujian Jaringan</h3>
                <table>
                    <?php foreach ($jaringan_samples as $sample): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sample['id_pengujian']); ?></td>
                            <td><?php echo htmlspecialchars($sample['nama_pasien']); ?></td>
                            <td><?php echo number_format($sample['biaya'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" class="total">Pembayaran Pengujian Jaringan</td>
                        <td class="total"><?php echo number_format($jaringan_total, 0, ',', '.'); ?></td>
                    </tr>
                </table>
            <?php endif; ?>

            <?php if (!empty($sitologi_ginekologi_samples)): ?>
                <h3>B. Pengujian Sitologi Ginekologi</h3>
                <table>
                    <?php foreach ($sitologi_ginekologi_samples as $sample): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sample['id_pengujian']); ?></td>
                            <td><?php echo htmlspecialchars($sample['nama_pasien']); ?></td>
                            <td><?php echo number_format($sample['biaya'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" class="total">Pembayaran Pengujian Sitologi Ginekologi</td>
                        <td class="total"><?php echo number_format($sitologi_ginekologi_total, 0, ',', '.'); ?></td>
                    </tr>
                </table>
            <?php endif; ?>

            <?php if (!empty($sitologi_non_ginekologi_samples)): ?>
                <h3>C. Pengujian Sitologi Non Ginekologi</h3>
                <table>
                    <?php foreach ($sitologi_non_ginekologi_samples as $sample): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sample['id_pengujian']); ?></td>
                            <td><?php echo htmlspecialchars($sample['nama_pasien']); ?></td>
                            <td><?php echo number_format($sample['biaya'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" class="total">Pembayaran Pengujian Sitologi Non Ginekologi</td>
                        <td class="total"><?php echo number_format($sitologi_non_ginekologi_total, 0, ',', '.'); ?></td>
                    </tr>
                </table>
            <?php endif; ?>

            <h3>Rincian Total Tagihan</h3>
            <table class="summary-table">
                <tr>
                    <td>Pengujian Jaringan</td>
                    <td><?php echo count($jaringan_samples); ?></td>
                    <td><?php echo number_format($jaringan_total, 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td>Pengujian Sitologi Ginekologi</td>
                    <td><?php echo count($sitologi_ginekologi_samples); ?></td>
                    <td><?php echo number_format($sitologi_ginekologi_total, 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td>Pengujian Sitologi Non Ginekologi</td>
                    <td><?php echo count($sitologi_non_ginekologi_samples); ?></td>
                    <td><?php echo number_format($sitologi_non_ginekologi_total, 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td colspan="2" class="total">Total Tagihan Pengujian Sampel</td>
                    <td class="total"><?php echo number_format($total_keseluruhan, 0, ',', '.'); ?></td>
                </tr>
            </table>
        </div>

        <div class="signature">
            <p>Yogyakarta, <?php echo date('d F Y', strtotime($tanggal_jadi)); ?></p><br><br>
            <p>dr. Agus Handoko, Sp.PA</p>
        </div>
    </div>

    <script>
        function downloadPaymentDetails() {
            const element = document.getElementById('contentToPrint');
        
            const loadingIndicator = document.createElement('div');
            loadingIndicator.style.position = 'fixed';
            loadingIndicator.style.top = '50%';
            loadingIndicator.style.left = '50%';
            loadingIndicator.style.transform = 'translate(-50%, -50%)';
            loadingIndicator.style.backgroundColor = 'rgba(0,0,0,0.7)';
            loadingIndicator.style.color = 'white';
            loadingIndicator.style.padding = '20px';
            loadingIndicator.style.borderRadius = '10px';
            loadingIndicator.style.zIndex = '10000';
            loadingIndicator.textContent = 'Menyiapkan dokumen...';
            document.body.appendChild(loadingIndicator);

            const filename = 'Detail_Pembayaran_<?php echo $id_pengujian; ?>_<?php echo date('Ymd_His', strtotime('2025-06-30 21:00:00')); ?>.pdf'; 
            
            const opt = {
                margin: 10,
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2, 
                    useCORS: true, 
                    width: 638,
                    windowWidth: 638
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'portrait',
                    putOnlyUsedFonts: true,
                    floatPrecision: 16 
                },
                pagebreak: { 
                    mode: ['css', 'legacy'],
                    avoid: ['table', 'tr'] 
                }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                loadingIndicator.remove();
            }).catch(error => {
                loadingIndicator.textContent = 'Gagal mengunduh: ' + error.message;
                setTimeout(() => loadingIndicator.remove(), 3000);
            });
        }
    </script>
</body>
</html>