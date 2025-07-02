<?php
session_start();

error_log("beranda.php - Session user ID: " . ($_SESSION['user'] ?? 'Not set'));

if (!isset($_SESSION['user'])) {
    error_log("beranda.php - Redirecting to login: Session user not set");
    header("location:../login.php?pesan=belum_login");
    exit();
}

$user_id = $_SESSION['user'];
include '../koneksi.php';

if (!$connect) {
    error_log("beranda.php - Database connection failed: " . mysqli_connect_error());
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT nama, foto FROM pengguna WHERE id_pengguna = ?";
$stmt = $connect->prepare($sql);
if (!$stmt) {
    error_log("beranda.php - Prepare failed: " . $connect->error);
    die("Prepare failed: " . $connect->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$nama_pengguna = $user['nama'] ?? 'Suci Puji';
$foto_pengguna = $user['foto'] ?? "profil.jpg";
$image_path = (strpos($foto_pengguna, 'Uploads/') === 0 && file_exists("../$foto_pengguna"))
    ? "../$foto_pengguna"
    : "../assets/imgs/profil.jpg";

$sql1 = "SELECT COUNT(*) as total FROM pengajuan WHERE status_pengajuan = 'Menunggu Verifikasi'";
$result1 = $connect->query($sql1);
$jumlahPengajuan = ($result1 && $row1 = $result1->fetch_assoc()) ? $row1['total'] : 0;

$sql2 = "SELECT COUNT(*) as total FROM pengujian WHERE status_pengujian = 'Diproses'";
$result2 = $connect->query($sql2);
$jumlahDiproses = ($result2 && $row2 = $result2->fetch_assoc()) ? $row2['total'] : 0;

$sql3 = "SELECT COUNT(*) as total FROM pengujian WHERE status_pengujian = 'Selesai'";
$result3 = $connect->query($sql3);
$jumlahSelesai = ($result3 && $row3 = $result3->fetch_assoc()) ? $row3['total'] : 0;

$sql4 = "SELECT COUNT(*) AS total FROM detail_pembayaran dp JOIN pembayaran p ON dp.id_pembayaran = p.id_pembayaran WHERE p.status_pembayaran = 'Sudah Bayar'";
$result4 = $connect->query($sql4);
$jumlahDetailPembayaran = ($result4 && $row4 = $result4->fetch_assoc()) ? $row4['total'] : 0;

$query = "SELECT id_pengujian, nama_pasien, tanggal_terima, status_pengujian, tanggal_jadi 
          FROM pengujian";
$result = $connect->query($query);

$jumlah_jaringan = 0;
$jumlah_ginekologi = 0;
$jumlah_non_ginekologi = 0;

$sql_chart = "SELECT id_pengujian FROM pengujian WHERE status_pengujian = 'Diproses'";
$result_chart = $connect->query($sql_chart);
if ($result_chart && $result_chart->num_rows > 0) {
    while ($row = $result_chart->fetch_assoc()) {
        $id = $row['id_pengujian'];
        if (strpos($id, 'JRM-') === 0) {
            $jumlah_jaringan++;
        } elseif (strpos($id, 'SRM-') === 0) {
            $jumlah_ginekologi++;
        } elseif (strpos($id, 'SNRM-') === 0) {
            $jumlah_non_ginekologi++;
        }
    }
}

$error_message = isset($_GET['pesan']) ? htmlspecialchars($_GET['pesan']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - MedPath</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <style>
        * {
            font-family: "Ubuntu", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --green1: rgba(20, 116, 114, 1);
            --green2: rgba(3, 178, 176, 1);
            --green3: rgba(186, 231, 228, 1);
            --green4: rgba(12, 109, 108, 0.61);
            --green5: rgba(3, 178, 176, 0.29);
            --green6: rgba(240, 243, 243, 1);
            --green7: rgba(228, 240, 240, 1);
            --green8: rgba(136, 181, 181, 0.26);
            --white: #fff;
            --gray: #f5f5f5;
            --black1: #222;
            --black2: #999;
            --black3: rgba(0, 0, 0, 0.4);
        }

        body {
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            position: relative;
            width: 100%;
        }

        .navigation {
            position: fixed;
            width: 226px;
            height: 100%;
            background: var(--green2);
            border-left: 10px solid var(--green2);
            transition: 0.5s;
            overflow: hidden;
            z-index: 999;
        }

        .navigation.active {
            width: 80px;
        }

        .navigation ul {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }

        .navigation ul li {
            position: relative;
            width: 100%;
            list-style: none;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }

        .navigation ul li:hover,
        .navigation ul li.hovered {
            background-color: var(--white);
        }

        .navigation ul li:nth-child(1) {
            margin-bottom: -130px;
            pointer-events: none;
        }

        .navigation ul li a {
            position: relative;
            display: flex;
            width: 100%;
            text-decoration: none;
            color: var(--white);
        }

        .navigation ul li:hover a,
        .navigation ul li.hovered a {
            color: var(--green2);
        }

        .navigation ul li a .icon {
            display: block;
            min-width: 60px;
            height: 60px;
            line-height: 75px;
            text-align: center;
        }

        .navigation ul li a .icon img {
            width: 24px;
            height: 24px;
            position: relative;
            top: 50%;
            transform: translateY(-50%);
            left: 20px;
        }

        .navigation ul li.signout {
            position: absolute;
            bottom: -110px;
            width: 100%;
        }

        .navigation ul li.signout:hover,
        .navigation ul li.signout.hovered {
            background-color: var(--white);
        }

        .navigation ul li.signout a .icon ion-icon {
            font-size: 1.75rem;
        }

        .navigation ul li.signout a .title {
            font-size: 16px;
            color: black;
            white-space: nowrap;
        }

        .navigation ul li a .title-logo {
            font-family: 'Poppins', sans-serif;
            font-size: 22px;
            color: black;
            padding: 0 10px;
            height: 230px;
            line-height: 70px;
            text-align: start;
            white-space: nowrap;
        }

        .navigation ul li a .title {
            font-family: 'Poppins', sans-serif;
            color: black;
            padding: 0 10px;
            height: 70px;
            line-height: 60px;
            text-align: start;
            white-space: nowrap;
        }

        .navigation ul li:hover a::before,
        .navigation ul li.hovered a::before {
            content: "";
            position: absolute;
            right: 0;
            top: -50px;
            width: 50px;
            height: 50px;
            background-color: transparent;
            border-radius: 50%;
            box-shadow: 35px 35px 0 10px var(--white);
            pointer-events: none;
        }

        .navigation ul li:hover a::after,
        .navigation ul li.hovered a::after {
            content: "";
            position: absolute;
            right: 0;
            bottom: -50px;
            width: 50px;
            height: 50px;
            background-color: transparent;
            border-radius: 50%;
            box-shadow: 35px -35px 0 10px var(--white);
            pointer-events: none;
        }

        .main {
            position: absolute;
            width: calc(100% - 280px);
            left: 250px;
            min-height: 100vh;
            background: var(--white);
            transition: 0.5s;
        }

        .main.active {
            width: calc(100% - 80px);
            left: 80px;
        }

        .topbar {
            width: 100%;
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            z-index: 1001;
        }

        .toggle {
            position: relative;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2.5rem;
            cursor: pointer;
            color: var(--black1);
            left: -30px;
            z-index: 1001;
        }

        .user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user span {
            font-size: 16px;
            color: var(--black1);
        }

        .notification {
            font-size: 1.5rem;
            color: var(--black1);
            cursor: pointer;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .welcome {
            margin-left: 20px;
            margin-top: 20px;
            padding: 20px;
        }

        .welcome h1 {
            font-size: 32px;
            margin-bottom: 10px;
            color: var(--black1);
        }

        .welcome span {
            font-size: 32px;
            color: var(--green2);
            font-weight: 600;
            font-style: italic;
        }

        .cardBox {
            position: relative;
            width: 100%;
            padding: 10px;
            display: grid;
            grid-template-columns: repeat(2, 0.6fr);
            grid-gap: 20px;
            margin-left: 10px;
            margin-right: 20px;
            animation: fadeIn 0.5s ease-in-out;
        }

        .cardBox .card {
            position: relative;
            background: #147472;
            padding: 5px 30px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: left;
            cursor: pointer;
            box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .cardBox .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .cardBox .card .iconBx {
            font-size: 2.5rem;
            color: var(--white);
            margin-top: 2px;
        }

        .cardBox .card .cardName {
            position: relative;
            font-weight: 600;
            font-size: 1.3rem;
            color: white;
            text-align: left;
        }

        .cardBox .card .numbers {
            color: white;
            font-weight: 600;
            font-size: 2.2rem;
            margin-top: 1px;
            text-align: left;
        }

        .details {
            position: relative;
            width: 100%;
            padding: 20px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .left-column {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .pengajuan {
            background: var(--green7);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
            height: 300px;
            max-height: 300px;
            overflow-y: auto;
        }

        .pengajuan .cardHeader {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .pengajuan .cardHeader h2 {
            font-weight: 600;
            color: var(--black1);
        }

        .column-titles {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--black1);
            margin: 15px 5px;
            padding: 10px 1px;
            height: 40px;
            border-radius: 10px;
            gap: 20px;
        }

        .column-titles span {
            flex: 1;
            text-align: center;
            font-size: 14px;
            line-height: 40px;
            margin-right: -5px;
            margin-left: -5px;
        }

        .pengajuan .row {
            height: 60px;
            flex-shrink: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 10px;
            gap: 5px;
            background: var(--white);
            margin-bottom: 10px;
            border-radius: 15px;
            transition: background 0.3s;
        }

        .pengajuan .row:hover {
            background: var(--green6);
        }

        .pengajuan .row span {
            flex: 1;
            text-align: center;
            padding: 0;
            margin: 0 10px;
            line-height: 30px;
        }

        .status_pengujian {
            width: 97px;
            height: 37px;
            padding: 2px 10px;
            border-radius: 15px;
            font-size: 14px;
            margin-right: 50px;
            margin-left: 50px;
            position: relative;
            bottom: 2px;
        }

        .status_pengujian.selesai {
            background: rgba(255, 182, 126, 1);
            color: var(--black1);
        }

        .status_pengujian.diproses {
            background: rgba(138, 242, 150, 1);
            color: var(--black1);
        }

        .pengajuan::-webkit-scrollbar {
            width: 6px;
        }

        .pengajuan::-webkit-scrollbar-track {
            background: var(--green7);
        }

        .pengajuan::-webkit-scrollbar-thumb {
            background: var(--green2);
            border-radius: 10px;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .action-btn {
            width: 37px;
            height: 37px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .action-btn ion-icon {
            font-size: 16px;
        }

        .chart-container {
            background: var(--green7);
            margin-top: 30px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            font-weight: 600;
            font-size: 1.15rem;
            color: var(--black1);
            margin-bottom: 20px;
        }

        .chart-header .chart-extra {
            display: flex;
            gap: 4px;
            font-weight: 400;
            color: var(--black2);
            font-size: 0.85rem;
        }

        .chart-legend {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            margin-top: 16px;
            font-size: 0.9rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--black1);
        }

        .legend-color {
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }

        .legend-jaringan { background-color: #1d7171; }
        .legend-sitologi-ginekologi { background-color: #5b9595; }
        .legend-sitologi-non-ginekologi { background-color: #accfcf; }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1007;
            animation: fadeIn 0.3s ease-in-out;
        }

        .message-box {
            position: relative;
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 1008;
            text-align: center;
            width: 400px;
            max-width: 90%;
            font-family: 'Ubuntu', sans-serif;
            animation: slideIn 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .message-box img {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            display: block;
        }

        .message-box p {
            margin-bottom: 1rem;
            font-size: 18px;
            font-weight: 500;
            color: #147472;
        }

        .message-box.error p {
            color: #721c24;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            font-weight: bold;
            color: #147472;
            cursor: pointer;
            transition: color 0.2s;
        }

        .close-btn:hover {
            color: #000000;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (max-width: 991px) {
            .navigation {
                left: -300px;
            }
            .navigation.active {
                width: 300px;
                left: 0;
            }
            .main {
                width: 100%;
                left: 0;
            }
            .main.active {
                left: 300px;
            }
            .details {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .cardBox {
                grid-template-columns: 1fr;
            }
            .pengajuan {
                overflow-x: auto;
            }
            .column-titles span {
                font-size: 12px;
            }
            .pengajuan .row {
                padding: 8px 15px;
                height: 50px;
            }
            .pengajuan .row span {
                font-size: 12px;
            }
            .action-buttons {
                gap: 5px;
            }
            .action-btn {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }
            .message-box {
                width: 90%;
                max-width: 350px;
            }
        }

        @media (max-width: 480px) {
            .welcome h1, .welcome span {
                font-size: 24px;
            }
            .cardHeader h2 {
                font-size: 18px;
            }
            .user span {
                display: none;
            }
            .navigation {
                width: 100%;
                left: -100%;
                z-index: 1000;
            }
            .navigation.active {
                width: 100%;
                left: 0;
            }
            .toggle {
                z-index: 10001;
            }
            .main.active .toggle {
                color: #fff;
                position: fixed;
                right: 0;
                left: initial;
            }
            .column-titles span {
                font-size: 10px;
            }
            .pengajuan .row {
                height: 45px;
                padding: 6px 10px;
            }
            .pengajuan .row span {
                font-size: 10px;
            }
            .action-buttons {
                gap: 3px;
            }
            .action-btn {
                width: 25px;
                height: 25px;
                font-size: 12px;
            }
            .message-box {
                width: 90%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="beranda.php">
                        <span class="icon">
                            <img src="../assets/microscope.png" alt="logo">
                        </span>
                        <span class="title-logo">MedPath</span>
                    </a>
                </li>
                <li>
                    <a href="beranda.php">
                        <span class="icon">
                            <img src="../assets/dashboard.png" alt="dashboard">
                        </span>
                        <span class="title">Beranda</span>
                    </a>
                </li>
                <li>
                    <a href="pengajuan/pages/pengajuan.php">
                        <span class="icon">
                            <img src="../assets/pengajuan.png" alt="pengajuan">
                        </span>
                        <span class="title">Pengajuan</span>
                    </a>
                </li>
                <li>
                    <a href="pengambilan/pages/pengambilan.php">
                        <span class="icon">
                            <img src="../assets/pengambilan.png" alt="pengambilan">
                        </span>
                        <span class="title">Pengambilan</span>
                    </a>
                </li>
                <li>
                    <a href="prosesUji/pages/pengujian.php">
                        <span class="icon">
                            <img src="../assets/prosesuji.png" alt="prosesuji">
                        </span>
                        <span class="title">Proses Uji</span>
                    </a>
                </li>
                <li>
                    <a href="prosesUji/pages/riwayat.php">
                        <span class="icon">
                            <img src="../assets/riwayat.png" alt="riwayat">
                        </span>
                        <span class="title">Riwayat Uji</span>
                    </a>
                </li>
                <li>
                    <a href="pembayaran.php">
                        <span class="icon">
                            <img src="../assets/money.png" alt="money">
                        </span>
                        <span class="title">Pembayaran</span>
                    </a>
                </li>
                <li>
                    <a href="pengaturan/pages/pengaturan.php">
                        <span class="icon">
                            <img src="../assets/setting.png" alt="setting">
                        </span>
                        <span class="title">Pengaturan</span>
                    </a>
                </li>
                <li class="signout">
                    <a href="../signout.php">
                        <span class="icon">
                            <ion-icon name="log-out-outline" style="color: black"></ion-icon>
                        </span>
                        <span class="title">Keluar</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>
                <div class="user">
                    <ion-icon class="notification" name="notifications-outline"></ion-icon>
                    <span><?php echo htmlspecialchars($nama_pengguna); ?></span>
                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="User">
                </div>
            </div>

            <?php if ($error_message): ?>
                <div class="overlay" id="messageOverlay" style="display: block;">
                    <div class="message-box <?php echo ($error_message == 'status_pengajuan_updated' || $error_message == 'pengujian_deleted') ? '' : 'error'; ?>">
                        <span class="close-btn" onclick="hideAlert()">×</span>
                        <img src="../assets/<?php echo ($error_message == 'status_pengajuan_updated' || $error_message == 'pengujian_deleted') ? 'success.png' : 'peringatan.png'; ?>" alt="<?php echo ($error_message == 'status_pengajuan_updated' || $error_message == 'pengujian_deleted') ? 'success' : 'warning'; ?>">
                        <p>
                            <?php
                            if ($error_message == 'status_pengajuan_updated') {
                                echo "Berhasil menyetujui pengajuan!";
                            } elseif ($error_message == 'pengujian_deleted') {
                                echo "Pengujian berhasil dihapus!";
                            } elseif ($error_message == 'unauthorized') {
                                echo "Anda harus login untuk melakukan aksi ini.";
                            } elseif ($error_message == 'invalid_request') {
                                echo "Permintaan tidak valid.";
                            } elseif ($error_message == 'invalid_status') {
                                echo "Status pengajuan tidak valid.";
                            } elseif ($error_message == 'database_error') {
                                echo "Terjadi kesalahan pada database.";
                            } elseif ($error_message == 'pengajuan_not_found') {
                                echo "Pengajuan tidak ditemukan.";
                            } else {
                                echo "Terjadi kesalahan saat memproses pengajuan.";
                            }
                            ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="welcome">
                <h1>Selamat Datang <span><?php echo htmlspecialchars($nama_pengguna); ?></span></h1>
            </div>

            <div class="details">
                <div class="left-column">
                    <div class="cardBox">
                        <div class="card">
                            <div class="iconBx">
                                <ion-icon name="document-text-outline"></ion-icon>
                            </div>
                            <div class="cardName">Sampel Diajukan</div>
                            <div class="numbers"><?php echo $jumlahPengajuan; ?></div>
                        </div>
                        <div class="card">
                            <div class="iconBx">
                                <ion-icon name="sync-outline"></ion-icon>
                            </div>
                            <div class="cardName">Sampel Diproses</div>
                            <div class="numbers"><?php echo $jumlahDiproses; ?></div>
                        </div>
                    </div>
                    <div class="cardBox">
                        <div class="card">
                            <div class="iconBx">
                                <ion-icon name="checkmark-done-outline"></ion-icon>
                            </div>
                            <div class="cardName">Pengujian Selesai</div>
                            <div class="numbers"><?php echo $jumlahSelesai; ?></div>
                        </div>
                        <div class="card">
                            <div class="iconBx">
                                <ion-icon name="cash-outline"></ion-icon>
                            </div>
                            <div class="cardName">Pembayaran Selesai</div>
                            <div class="numbers"><?php echo $jumlahDetailPembayaran; ?></div>
                        </div>
                    </div>
                    <div class="pengajuan">
                        <div class="cardHeader">
                            <h2>Pengujian Sampel</h2>
                        </div>
                        <div class="column-titles">
                            <span>Sampel Atas Nama</span>
                            <span>Tanggal Terima</span>
                            <span>Jenis</span>
                            <span>Status</span>
                            <span>Tanggal Jadi</span>
                        </div>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $id_pengujian = $row['id_pengujian'];
                                $jenis_pengujian = '';
                                if (strpos($id_pengujian, 'JRM-') === 0) {
                                    $jenis_pengujian = 'Jaringan';
                                } elseif (strpos($id_pengujian, 'SRM-') === 0) {
                                    $jenis_pengujian = 'Sitologi Ginekologi';
                                } elseif (strpos($id_pengujian, 'SNRM-') === 0) {
                                    $jenis_pengujian = 'Sitologi Non Ginekologi';
                                }
                                $status_class = ($row['status_pengujian'] == 'Selesai') ? 'selesai' : ($row['status_pengujian'] == 'Diproses' ? 'diproses' : '');
                                $tanggal_terima = !empty($row['tanggal_terima']) ? date('Y/m/d', strtotime($row['tanggal_terima'])) : '-';
                                $tanggal_jadi = !empty($row['tanggal_jadi']) ? date('Y/m/d', strtotime($row['tanggal_jadi'])) : '-';
                        ?>
                                <div class="row">
                                    <span><?php echo htmlspecialchars($row['nama_pasien']); ?></span>
                                    <span><?php echo $tanggal_terima; ?></span>
                                    <span><?php echo htmlspecialchars($jenis_pengujian); ?></span>
                                    <span class="status_pengujian <?php echo $status_class; ?>"><?php echo htmlspecialchars($row['status_pengujian']); ?></span>
                                    <span><?php echo $tanggal_jadi; ?></span>
                                </div>
                        <?php
                            }
                        } else {
                            echo "<div class='row' style='text-align:center; color:var(--black1);'><span colspan='5'>Tidak ada data yang ditemukan.</span></div>";
                        }
                        ?>
                    </div>
                </div>

                <div class="right-column">
                    <div class="chart-container">
                        <div class="chart-header">
                            <div>Statistik Jenis Pengujian Sampel</div>
                            <div class="chart-extra">
                                <span>Hari ini</span>
                                <ion-icon name="filter-outline"></ion-icon>
                            </div>
                        </div>
                        <canvas id="donutChart" width="320" height="320"></canvas>
                        <div class="chart-legend">
                            <div class="legend-item"><span class="legend-color legend-jaringan"></span>Jaringan</div>
                            <div class="legend-item"><span class="legend-color legend-sitologi-ginekologi"></span>Sitologi Ginekologi</div>
                            <div class="legend-item"><span class="legend-color legend-sitologi-non-ginekologi"></span>Sitologi Non Ginekologi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let list = document.querySelectorAll(".navigation li");

        function activeLink() {
            list.forEach((item) => {
                item.classList.remove("hovered");
            });
            this.classList.add("hovered");
        }

        list.forEach((item) => item.addEventListener("mouseover", activeLink));

        let toggle = document.querySelector(".toggle");
        let navigation = document.querySelector(".navigation");
        let main = document.querySelector(".main");

        toggle.onclick = function () {
            navigation.classList.toggle("active");
            main.classList.toggle("active");
        };

        function hideAlert() {
            document.getElementById('messageOverlay').style.display = 'none';
        }

        const ctx = document.getElementById('donutChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Jaringan', 'Sitologi Ginekologi', 'Sitologi Non Ginekologi'],
                datasets: [{
                    data: [<?php echo $jumlah_jaringan; ?>, <?php echo $jumlah_ginekologi; ?>, <?php echo $jumlah_non_ginekologi; ?>],
                    backgroundColor: ['#1d7171', '#5b9595', '#accfcf']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>
<?php $connect->close(); ?>