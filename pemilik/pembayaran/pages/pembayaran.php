<?php
session_start();

error_log("pembayaran.php - Session user ID: " . ($_SESSION['user'] ?? 'Not set'));

if (!isset($_SESSION['user'])) {
    error_log("pembayaran.php - Redirecting to login: Session user not set");
    header("location:../../../login.php?pesan=belum_login");
    exit();
}

$user_id = $_SESSION['user'];
include '../../../koneksi.php';

if (!$connect) {
    error_log("pembayaran.php - Database connection failed: " . mysqli_connect_error());
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT nama, alamat, email, nomortlp, foto FROM pengguna WHERE id_pengguna = ?";
$stmt = $connect->prepare($sql);
if (!$stmt) {
    error_log("pembayaran.php - Prepare failed: " . $connect->error);
    die("Prepare failed: " . $connect->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

error_log("pembayaran.php - User data: " . print_r($user, true));

if (!$user) {
    error_log("pembayaran.php - No user found for id_pengguna: $user_id");
    $nama_pengguna = "Pengguna Tidak Ditemukan";
    $alamat = "";
    $email = "";
    $nomortlp = "";
    $foto_pengguna = "profil.jpg";
} else {
    $nama_pengguna = $user['nama'] ?? '';
    $alamat = $user['alamat'] ?? '';
    $email = $user['email'] ?? '';
    $nomortlp = $user['nomortlp'] ?? '';
    $foto_pengguna = $user['foto'] ?? "profil.jpg";
}

$image_path = (strpos($foto_pengguna, 'Uploads/') === 0 && file_exists("../../../$foto_pengguna"))
    ? "../../../$foto_pengguna"
    : "../../../assets/imgs/profil.jpg";

error_log("pembayaran.php - Profile photo path: $image_path, Exists: " . (file_exists($image_path) ? 'Yes' : 'No'));

$sql_payment = "SELECT 
    SUM(CASE WHEN status_pembayaran = 'Sudah Bayar' THEN 1 ELSE 0 END) as sudah_bayar,
    SUM(CASE WHEN status_pembayaran = 'Belum Bayar' THEN 1 ELSE 0 END) as belum_bayar,
    SUM(CASE WHEN status_pembayaran = 'Menunggu Verifikasi' THEN 1 ELSE 0 END) as menunggu_verifikasi
    FROM pembayaran";
$result_payment = $connect->query($sql_payment);
$payment_counts = $result_payment->fetch_assoc();

$sql_pending = "SELECT id_pembayaran, nama_pengirim, tanggal_pembayaran, total_bayar 
                FROM pembayaran 
                WHERE status_pembayaran = 'Menunggu Verifikasi'
                ORDER BY tanggal_pembayaran DESC";
$result_pending = $connect->query($sql_pending);

$currentYear = date('Y');
$sql_payments = "SELECT id_pembayaran, nama_pengirim, total_bayar, status_pembayaran, 
                    tanggal_pengajuan_pembayaran, tanggal_pembayaran
                FROM pembayaran
                WHERE YEAR(tanggal_pengajuan_pembayaran) = ? OR tanggal_pengajuan_pembayaran IS NULL
                ORDER BY COALESCE(tanggal_pengajuan_pembayaran, NOW()) DESC LIMIT 8";
$stmt_payments = $connect->prepare($sql_payments);
if (!$stmt_payments) {
    error_log("pembayaran.php - Prepare failed for payments query: " . $connect->error);
    die("Prepare failed: " . $connect->error);
}
$stmt_payments->bind_param("i", $currentYear);
$stmt_payments->execute();
$result_payments = $stmt_payments->get_result();

$error_message = isset($_GET['pesan']) ? htmlspecialchars($_GET['pesan']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - MedPath</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
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
            margin-bottom: -20px;
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
            bottom: -280px;
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

        .card.sudah-bayar {
            height: 200px;
            background: var(--green1);
        }

        .card.belum-bayar {
            background: var(--green1);
        }

        .approval-card {
            background: var(--green7);
            height: 290px;
        }

        .approval-list::-webkit-scrollbar {
            width: 6px;
        }

        .approval-list::-webkit-scrollbar-track {
            background: var(--green7);
        }

        .approval-list::-webkit-scrollbar-thumb {
            background: var(--green2);
            border-radius: 10px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 5px;
            justify-content: flex-start;
            position: relative;
            z-index: 1003;
        }

        .search-bar input {
            width: 100%;
            height: 66px;
            flex-shrink: 0;
            border-radius: 10px;
            padding: 5px 50px 5px 20px;
            font-size: 16px;
            outline: none;
            border: 0px solid var(--black2);
            background: var(--green7);
        }

        .search-bar .search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            width: 27px;
            height: 27px;
            flex-shrink: 0;
            aspect-ratio: 1/1;
        }

        .filter-card {
            width: 90px;
            height: 66px;
            flex-shrink: 0;
            background: var(--green7);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1003;
        }

        .pengajuan {
            background: var(--green7);
            padding: 20px;
            border-radius: 10px;
            margin: 20px;
            max-height: 60vh;
            overflow-y: auto;
            z-index: 1001;
        }

        .cardHeader {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .cardHeader h2 {
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
            font-size: 16px;
            line-height: 40px;
            margin-right: 20px;
            margin-left: 10px;
        }

        .pengajuan .row {
            height: 69px;
            flex-shrink: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 10px;
            gap: 10px;
            background: var(--white);
            margin-bottom: 15px;
            border-radius: 15px;
            transition: background 0.3s;
            z-index: 1001;
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

        .status_pembayaran {
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

        .status_pembayaran.verified {
            background: rgba(138, 242, 150, 1);
            color: var(--black1);
        }

        .status_pembayaran.pending {
            background: rgba(255, 133, 128, 1);
            color: var(--black1);
        }

        .status_pembayaran.waiting {
            background: rgba(255, 226, 110, 1);
            color: var (--black1);
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

        .action-btn.update {
            background-color: var(--green1);
            color: white;
        }

        .action-btn.delete {
            background-color: var(--black1);
            color: white;
        }

        .action-btn ion-icon {
            font-size: 16px;
        }

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
        }

        .message-box {
            position: fixed;
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            text-align: center;
            height: 200px;
            width: 400px;
            max-width: 400px;
            left: 50%;
            top: 30%;
            transform: translate(-50%, -50%);
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
        }

        .close-btn:hover {
            color: #000000;
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
        }

        @media (max-width: 768px) {
            .search-bar {
                flex-direction: column;
                gap: 10px;
            }
            .search-bar input,
            .filter-card {
                width: 100%;
            }
            .pengajuan {
                margin: 10px;
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
            .topbar {
                padding: 0 10px;
            }
            .search-bar {
                margin: 10px;
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
                    <a href="">
                        <span class="icon">
                            <img src="../../../assets/microscope.png" alt="logo">
                        </span>
                        <span class="title-logo">MedPath</span>
                    </a>
                </li>
                <li class="hovered">
                    <a href="pembayaran.php">
                        <span class="icon">
                            <img src="../../../assets/money.png" alt="money">
                        </span>
                        <span class="title">Pembayaran</span>
                    </a>
                </li>
                <li>
                    <a href="../../pengaturan/pages/pengaturan.php">
                        <span class="icon">
                            <img src="../../../assets/setting.png" alt="setting">
                        </span>
                        <span class="title">Pengaturan</span>
                    </a>
                </li>
                <li class="signout">
                    <a href="../../../signout.php">
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
                    <div class="message-box <?php echo ($error_message == 'status_updated' || $error_message == 'payment_deleted') ? '' : 'error'; ?>">
                        <span class="close-btn" onclick="hideAlert()">×</span>
                        <img src="../../../assets/<?php echo ($error_message == 'status_updated' || $error_message == 'payment_deleted') ? 'success.png' : 'peringatan.png'; ?>" alt="<?php echo ($error_message == 'status_updated' || $error_message == 'payment_deleted') ? 'success' : 'warning'; ?>">
                        <p>
                            <?php
                            if ($error_message == 'status_updated') {
                                echo "Status pembayaran berhasil diperbarui!";
                            } elseif ($error_message == 'payment_deleted') {
                                echo "Pembayaran berhasil dihapus!";
                            } elseif ($error_message == 'unauthorized') {
                                echo "Anda harus login untuk melakukan aksi ini.";
                            } elseif ($error_message == 'invalid_request') {
                                echo "Permintaan tidak valid.";
                            } elseif ($error_message == 'invalid_status') {
                                echo "Status pembayaran tidak valid.";
                            } elseif ($error_message == 'database_error') {
                                echo "Terjadi kesalahan pada database.";
                            } elseif ($error_message == 'payment_not_found') {
                                echo "Pembayaran tidak ditemukan.";
                            } elseif ($error_message == 'foreign_key_error') {
                                echo "Pembayaran tidak dapat dihapus karena terkait dengan data lain.";
                            } else {
                                echo "Terjadi kesalahan saat memproses pembayaran.";
                            }
                            ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="flex flex-col lg:flex-row gap-5 p-6">
                <div class="flex-1 lg:w-7/12 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="card sudah-bayar text-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-transform transform hover:-translate-y-1">
                            <h3 class="text-lg font-semibold">Sudah Bayar</h3>
                            <p class="text-4xl font-bold"><?php echo $payment_counts['sudah_bayar'] ?? 0; ?></p>
                        </div>
                        <div class="card belum-bayar text-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-transform transform hover:-translate-y-1">
                            <h3 class="text-lg font-semibold">Belum Bayar</h3>
                            <p class="text-4xl font-bold"><?php echo $payment_counts['belum_bayar'] ?? 0; ?></p>
                        </div>
                    </div>

                    <div class="search-bar">
                        <div class="relative flex-1">
                            <input type="text" id="searchInput" placeholder="Search pembayaran" class="w-full p-4 rounded-xl bg-green7 focus:outline-none focus:ring-2 focus:ring-green2">
                            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                <path d="M17.1809 0C11.7598 0 7.36172 4.39805 7.36172 9.81914C7.36172 11.5857 7.83633 13.2363 8.64844 14.6707L0 23.3191L1.22871 25.7713L3.68086 27L12.3293 18.3516C13.7584 19.1689 15.4143 19.6383 17.1809 19.6383C22.602 19.6383 27 15.2402 27 9.81914C27 4.39805 22.602 0 17.1809 0ZM17.1809 16.5691C13.4525 16.5691 10.4309 13.5475 10.4309 9.81914C10.4309 6.09082 13.4525 3.06914 17.1809 3.06914C20.9092 3.06914 23.9309 6.09082 23.9309 9.81914C23.9309 13.5475 20.9092 16.5691 17.1809 16.5691Z" fill="black"/>
                            </svg>
                        </div>
                        <div class="filter-card">
                            <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34" fill="none">
                                <path d="M31.3438 11.1562H2.65625C2.23356 11.1562 1.82818 10.9883 1.5293 10.6895C1.23041 10.3906 1.0625 9.98519 1.0625 9.5625C1.0625 9.13981 1.23041 8.73443 1.5293 8.43555C1.82818 8.13666 2.23356 7.96875 2.65625 7.96875H31.3438C31.7664 7.96875 32.1718 8.13666 32.4707 8.43555C32.7696 8.73443 32.9375 9.13981 32.9375 9.5625C32.9375 9.98519 32.7696 10.3906 32.4707 10.6895C32.1718 10.9883 31.7664 11.1562 31.3438 11.1562ZM26.0312 18.5938H7.96875C7.54606 18.5938 7.14068 18.4258 6.8418 18.127C6.54291 17.8281 6.375 17.4227 6.375 17C6.375 16.5773 6.54291 16.1719 6.8418 15.873C7.14068 15.5742 7.54606 15.4062 7.96875 15.4062H26.0312C26.4539 15.4062 26.8593 15.5742 27.1582 15.873C27.4571 16.1719 27.625 17C27.625 17.4227 27.4571 17.8281 27.1582 18.127C26.8593 18.4258 26.4539 18.5938 26.0312 18.5938ZM19.6562 26.0312H14.3438C13.9211 26.0312 13.5157 25.8633 13.2168 25.5645C12.9179 25.2656 12.75 24.8602 12.75 24.4375C12.75 24.0148 12.9179 23.6094 13.2168 23.3105C13.5157 23.0117 13.9211 22.8438 14.3438 22.8438H19.6562C20.0789 22.8438 20.4843 23.0117 20.7832 23.3105C21.0821 23.6094 21.25 24.0148 21.25 24.4375C21.25 24.8602 21.0821 25.2656 20.7832 25.5645C20.4843 25.8633 20.0789 26.0312 19.6562 26.0312Z" fill="black"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="w-full lg:w-5/12">
                    <div class="approval-card text-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-transform transform hover:-translate-y-1">
                        <h3 class="text-lg font-semibold mb-4" style="color: black;">Persetujuan Pembayaran</h3>
                        <div class="approval-list max-h-64 overflow-y-auto pr-2">
                            <?php if ($result_pending->num_rows > 0): ?>
                                <?php while ($row = $result_pending->fetch_assoc()): ?>
                                    <div class="approval-item flex justify-between items-center p-4 bg-teal-50 text-gray-800 rounded-lg mb-3">
                                        <div>
                                            <div class="font-medium"><?php echo htmlspecialchars($row['nama_pengirim']); ?></div>
                                            <div class="text-sm text-gray-600"><?php echo !empty($row['tanggal_pembayaran']) ? date('Y/m/d', strtotime($row['tanggal_pembayaran'])) : '-'; ?></div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <form action="../process/konfirmasi.php" method="post" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pembayaran ini?');">
                                                <input type="hidden" name="id_pembayaran" value="<?php echo $row['id_pembayaran']; ?>">
                                                <input type="hidden" name="status" value="Sudah Bayar">
                                                <button type="submit" class="approve-btn bg-green-500 text-white w-8 h-8 rounded-full flex items-center justify-center">
                                                    <ion-icon name="checkmark-outline"></ion-icon>
                                                </button>
                                            </form>
                                            <form action="../process/konfirmasi.php" method="post" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menolak pembayaran ini?');">
                                                <input type="hidden" name="id_pembayaran" value="<?php echo $row['id_pembayaran']; ?>">
                                                <input type="hidden" name="status" value="Belum Bayar">
                                                <button type="submit" class="reject-btn bg-red-500 text-white w-8 h-8 rounded-full flex items-center justify-center">
                                                    <ion-icon name="close-outline"></ion-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center text-gray-400">Tidak ada permintaan</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="details">
                <div class="pengajuan">
                    <div class="cardHeader">
                        <h2>Pembayaran</h2>
                    </div>
                    <div class="column-titles">
                        <span>Nama Pelanggan</span>
                        <span>Jumlah Tagihan</span>
                        <span>Status Pembayaran</span>
                        <span>Tanggal Pembayaran</span>
                        <span>Batas Pembayaran</span>
                        <span>Aksi</span>
                    </div>
                    <?php
                    if ($result_payments->num_rows > 0) {
                        while ($row = $result_payments->fetch_assoc()) {
                            $tanggal_pengajuan_pembayaran = !empty($row['tanggal_pengajuan_pembayaran']) ? date('Y/m/d', strtotime($row['tanggal_pengajuan_pembayaran'])) : '-';
                            $tanggal_pembayaran = !empty($row['tanggal_pembayaran']) ? date('Y/m/d', strtotime($row['tanggal_pembayaran'])) : '-';
                           $status_pembayaran_class = ($row['status_pembayaran'] == 'Menunggu Verifikasi') ? 'waiting' :
                           (($row['status_pembayaran'] == 'Sudah Bayar') ? 'verified' :
                           (($row['status_pembayaran'] == 'Belum Bayar') ? 'pending' : ''));
                    ?>
                            <div class="row">
                                <span><?php echo htmlspecialchars($row['nama_pengirim']); ?></span>
                                <span>Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></span>
                                <span class="status_pembayaran <?php echo $status_pembayaran_class; ?>"><?php echo htmlspecialchars($row['status_pembayaran']); ?></span>
                                <span><?php echo $tanggal_pengajuan_pembayaran; ?></span>
                                <span><?php echo $tanggal_pembayaran; ?></span>
                                <span class="action-buttons">
                                    <button class="action-btn update" onclick="editPayment(<?php echo $row['id_pembayaran']; ?>)">
                                        <ion-icon name="pencil-outline"></ion-icon>
                                    </button>
                                    <button class="action-btn delete" onclick="deletePayment(<?php echo $row['id_pembayaran']; ?>)">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </button>
                                </span>
                            </div>
                    <?php
                        }
                    } else {
                        error_log("pembayaran.php - No payment data found for Year: $currentYear");
                        echo "<div class='row' style='text-align:center; color:var(--black1);'><span colspan='6'>Tidak ada data yang ditemukan.</span></div>";
                    }
                    $stmt_payments->close();
                    $connect->close();
                    ?>
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

        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.pengajuan .row');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        function editPayment(id_pembayaran) {
            if (confirm('Apakah Anda yakin ingin mengedit pembayaran ini?')) {
                window.location.href = `edit.php?id=${id_pembayaran}`;
            }
        }

        function deletePayment(id_pembayaran) {
            if (confirm('Apakah Anda yakin ingin menghapus pembayaran ini?')) {
                window.location.href = `../process/delete.php?id=${id_pembayaran}`;
            }
        }

        function hideAlert() {
            document.getElementById('messageOverlay').style.display = 'none';
        }
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>