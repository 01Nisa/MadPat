<?php
session_start();

error_log("beranda.php - Session user ID: " . ($_SESSION['user'] ?? 'Not set'));

if (!isset($_SESSION['user'])) {
    error_log("beranda.php - Redirecting to login: Session user not set");
    header("location:../../../login.php?pesan=belum_login");
    exit();
}

$user_id = $_SESSION['user'];
include '../../../koneksi.php';

if (!$connect) {
    error_log("beranda.php - Database connection failed: " . mysqli_connect_error());
    header("Location: ../pages/pengajuan.php?error=" . urlencode("Database connection failed"));
    exit();
}

$sql = "SELECT nama, foto FROM pengguna WHERE id_pengguna = ?";
$stmt = $connect->prepare($sql);
if (!$stmt) {
    error_log("beranda.php - Prepare failed for user profile: " . $connect->error);
    header("Location: ../pages/pengajuan.php?error=" . urlencode("Failed to fetch user profile"));
    exit();
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    error_log("beranda.php - User not found for id_pengguna: $user_id");
    $nama_pengguna = "Pengguna Tidak Ditemukan";
    $foto_pengguna = "profil.jpg";
} else {
    $nama_pengguna = $user['nama'];
    $foto_pengguna = $user['foto'] ?: "profil.jpg";
}

$image_path = (strpos($foto_pengguna, 'Uploads/') === 0 && file_exists("../../../$foto_pengguna"))
    ? "../../../$foto_pengguna"
    : "../../../assets/imgs/profil.jpg";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=poppins");

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
            --green9: rgba(136, 181, 181, 0.51);
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
            width: 230px;
            height: 100%;
            background: var(--green2);
            border-left: 10px solid var(--green2);
            transition: 0.5s;
            overflow: hidden;
            z-index: 1000;
        }

        .navigation.active {
            width: 80px;
        }

        .navigation ul {
            position: absolute;
            top: 0px;
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
            margin-bottom: -30px;
            pointer-events: none;
        }

        .navigation ul li a {
            position: relative;
            display: block;
            width: 100%;
            display: flex;
            text-decoration: none;
            color: var(--white);
        }

        .navigation ul li:hover a,
        .navigation ul li.hovered a {
            color: var(--green2);
        }

        .navigation ul li a .icon {
            position: relative;
            display: block;
            min-width: 60px;
            height: 60px;
            line-height: 75px;
            text-align: center;
        }

        .navigation ul li.signout {
            position: absolute;
            bottom: -150px;
            width: 100%;
        }

        .navigation ul li.signout:hover,
        .navigation ul li.signout.hovered {
            background-color: var(--white);
        }

        .navigation ul li.signout a {
            position: relative;
            display: block;
            width: 100%;
            display: flex;
            text-decoration: none;
            color: var(--white);
        }

        .navigation ul li.signout:hover a {
            color: var(--green2);
        }

        .navigation ul li.signout a .icon {
            position: relative;
            display: block;
            min-width: 60px;
            height: 60px;
            line-height: 75px;
            text-align: center;
        }

        .navigation ul li.signout a .icon ion-icon {
            font-size: 1.75rem;
        }

        .navigation ul li.signout a .title {
            font-size: 16px;
            color: black;
            white-space: nowrap;
        }

        .navigation ul li a .icon ion-icon {
            font-size: 1.75rem;
        }

        .navigation ul li a .title-logo {
            position: relative;
            display: block;
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
            position: relative;
            display: block;
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
            width: calc(100% - 300px);
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
            z-index: 1002;
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
        }

        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px;
            justify-content: flex-start;
            position: relative;
            z-index: 1001;
        }

        .search-bar input {
            width: 853px;
            height: 66px;
            margin-top: 100px;
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
            left: 790px;
            top: 30%;
            transform: translateY(-50%);
            width: 27px;
            height: 27px;
            flex-shrink: 0;
            aspect-ratio: 1/1;
            margin-top: 85px;
        }

        .filter-card {
            width: 90px;
            height: 66px;
            flex-shrink: 0;
            margin-top: 100px;
            background: var(--green7);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1001;
        }

        .filter-card ion-icon {
            font-size: 1.5rem;
            color: var(--black1);
        }

        .tambah-card {
            width: 280px;
            height: 66px;
            flex-shrink: 0;
            background: var(--green1);
            color: var(--white);
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            margin-top: 100px;
            z-index: 1001;
        }

        .tambah-card:hover {
            background: var(--green6);
        }

        .tambah-card img {
            margin-left: -10px;
            margin-right: 20px;
            width: 30px;
            height: 30px;
        }

        .details {
            padding: 20px;
            z-index: 1001;
        }

        .pengajuan {
            background: var(--green7);
            padding: 20px;
            border-radius: 15px;
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
            margin-top: 10px;
            font-weight: 600;
            color: var(--black1);
        }

        .cardHeader .btn {
            padding: 5px 10px;
            background: var(--green2);
            text-decoration: none;
            color: var(--white);
            border-radius: 6px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            z-index: 1001;
        }

        .table table {
            width: 100%;
        }

        .table th {
            color: var(--black);
            padding: 10px;
            text-align: center;
        }

        .pengajuan .row {
            height: 69px;
            flex-shrink: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px;
            background: var(--white);
            margin-bottom: 10px;
            border-radius: 15px;
            cursor: pointer;
            transition: background 0.3s;
            z-index: 1001;
        }

        .pengajuan .row:hover {
            background: var(--green9);
        }

        .pengajuan .row span {
            flex: 1;
            margin-left: 90px;
            margin-right: 100px;
            text-align: center;
        }

        .pengajuan .row .status_pengajuan {
            padding: 10px 10px;
            border-radius: 15px;
            font-size: 14px;
            margin-right: 60px;
            margin-left: 60px;
        }

        .status_pengajuan.verified {
            background: rgba(138, 242, 150, 1);
            color: var(--black);
        }

        .status_pengajuan.pending {
            background: rgba(255, 226, 110, 1);
            color: var(--black);
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--black3);
            background-blend-mode: overlay;
            z-index: 1002;
        }

        .overlay.active {
            display: block;
        }

        .menu-card {
            position: fixed;
            top: 40%;
            right: 90px;
            transform: translateY(-50%);
            width: 310px;
            height: 186px;
            background: var(--white);
            padding: 20px;
            box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            z-index: 1003;
            flex-direction: column;
            justify-content: space-around;
            display: none;
        }

        .menu-card.active {
            display: flex;
        }

        .menu-option {
            cursor: pointer;
            font-size: 16px;
            color: var(--black1);
            text-align: left;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .menu-option:hover {
            color: var(--green1);
        }

        .menu-option.selected {
            background: var(--green8);
            color: var(--black1);
        }

        .menu-option .plus-icon {
            font-size: 1.2rem;
        }

        .menu-separator {
            width: 100%;
            height: 1px;
            background: var(--black2);
            margin: 5px 0;
        }

        .jumlah-card {
            position: fixed;
            top: 45%;
            right: 450px;
            transform: translateY(-50%);
            width: 235px;
            height: 125px;
            background: var(--white);
            padding: 20px;
            box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            z-index: 1003;
            flex-direction: column;
            justify-content: space-around;
            align-items: left;
            display: none;
        }

        .jumlah-card.active {
            display: flex;
        }

        .jumlah-card h2 {
            font-size: 16px;
            color: var(--black1);
            margin-left: -10px;
            margin-top: 3px;
            margin-bottom: 5px;
        }

        .jumlah-card .form-group {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .jumlah-card input {
            width: 214px;
            height: 25px;
            padding: 5px 10px;
            border: 2px solid var(--green1);
            border-radius: 5px;
            font-size: 16px;
            text-align: left;
        }

        .jumlah-card .btn-submit {
            width: 48px;
            height: 27px;
            padding: 5px 10px;
            margin: 10px 0 60px 150px;
            background: var(--green8);
            color: var(--black);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        .jumlah-card .btn-submit:hover {
            color: var(--white);
            background: var(--green1);
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
            .details {
                grid-template-columns: 1fr;
            }
            .pengajuan {
                overflow-x: auto;
            }
            .search-bar {
                flex-direction: column;
                gap: 10px;
            }
            .search-bar input,
            .filter-card,
            .tambah-card {
                width: 100%;
            }
            .tambah-card {
                margin-left: 0;
            }
            .menu-card {
                right: 10px;
            }
            .jumlah-card {
                left: 10px;
            }
        }

        @media (max-width: 480px) {
            .cardHeader h2 {
                font-size: 20px;
            }
            .user {
                min-width: 40px;
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
            .user span {
                display: none;
            }
            .search-bar {
                margin: 10px;
            }
            .menu-card {
                width: 250px;
                right: 5px;
            }
            .jumlah-card {
                width: 250px;
                left: 5px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="../../beranda.php">
                        <span class="icon">
                            <img src="../../../assets/microscope.png" alt="logo">
                        </span>
                        <span class="title-logo">MedPath</span>
                    </a>
                </li>

                <li>
                    <a href="../../beranda.php">
                        <span class="icon">
                            <img src="../../../assets/dashboard.png" alt="dashboard">
                        </span>
                        <span class="title">Beranda</span>
                    </a>
                </li>

                <li>
                    <a href="pengajuan.php">
                        <span class="icon">
                            <img src="../../../assets/pengajuan.png" alt="pengajuan">
                        </span>
                        <span class="title">Pengajuan</span>
                    </a>
                </li>

                <li>
                    <a href="../../pengambilan/pages/pengambilan.php">
                        <span class="icon">
                            <img src="../../../assets/pengambilan.png" alt="pengambilan">
                        </span>
                        <span class="title">Pengambilan</span>
                    </a>
                </li>

                <li>
                    <a href="../../prosesUji/pages/pengujian.php">
                        <span class="icon">
                            <img src="../../../assets/prosesuji.png" alt="prosesuji">
                        </span>
                        <span class="title">Proses Uji</span>
                    </a>
                </li>

                <li>
                    <a href="../../prosesUji/pages/riwayat.php">
                        <span class="icon">
                            <img src="../../../assets/riwayat.png" alt="riwayat">
                        </span>
                        <span class="title">Riwayat Uji</span>
                    </a>
                </li>
                <li>
                    <a href="../../pembayaran/pages/pembayaran.php">
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

            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search pengajuan">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                    <g clip-path="url(#clip0_306_10765)">
                        <path d="M17.1809 0C11.7598 0 7.36172 4.39805 7.36172 9.81914C7.36172 11.5857 7.83633 13.2363 8.64844 14.6707L0 23.3191L1.22871 25.7713L3.68086 27L12.3293 18.3516C13.7584 19.1689 15.4143 19.6383 17.1809 19.6383C22.602 19.6383 27 15.2402 27 9.81914C27 4.39805 22.602 0 17.1809 0ZM17.1809 16.5691C13.4525 16.5691 10.4309 13.5475 10.4309 9.81914C10.4309 6.09082 13.4525 3.06914 17.1809 3.06914C20.9092 3.06914 23.9309 6.09082 23.9309 9.81914C23.9309 13.5475 20.9092 16.5691 17.1809 16.5691Z" fill="black"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_306_10765">
                            <rect width="27" height="27" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>
                <div class="filter-card">
                    <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34" fill="none">
                        <path d="M31.3438 11.1562H2.65625C2.23356 11.1562 1.82818 10.9883 1.5293 10.6895C1.23041 10.3906 1.0625 9.98519 1.0625 9.5625C1.0625 9.13981 1.23041 8.73443 1.5293 8.43555C1.82818 8.13666 2.23356 7.96875 2.65625 7.96875H31.3438C31.7664 7.96875 32.1718 8.13666 32.4707 8.43555C32.7696 8.73443 32.9375 9.13981 32.9375 9.5625C32.9375 9.98519 32.7696 10.3906 32.4707 10.6895C32.1718 10.9883 31.7664 11.1562 31.3438 11.1562ZM26.0312 18.5938H7.96875C7.54606 18.5938 7.14068 18.4258 6.8418 18.127C6.54291 17.8281 6.375 17.4227 6.375 17C6.375 16.5773 6.54291 16.1719 6.8418 15.873C7.14068 15.5742 7.54606 15.4062 7.96875 15.4062H26.0312C26.4539 15.4062 26.8593 15.5742 27.1582 15.873C27.4571 16.1719 27.625 16.5773 27.625 17C27.625 17.4227 27.4571 17.8281 27.1582 18.127C26.8593 18.4258 26.4539 18.5938 26.0312 18.5938ZM19.6562 26.0312H14.3438C13.9211 26.0312 13.5157 25.8633 13.2168 25.5645C12.9179 25.2656 12.75 24.8602 12.75 24.4375C12.75 24.0148 12.9179 23.6094 13.2168 23.3105C13.5157 23.0117 13.9211 22.8438 14.3438 22.8438H19.6562C20.0789 22.8438 20.4843 23.0117 20.7832 23.3105C21.0821 23.6094 21.25 24.0148 21.25 24.4375C21.25 24.8602 21.0821 25.2656 20.7832 25.5645C20.4843 25.8633 20.0789 26.0312 19.6562 26.0312Z" fill="black"/>
                    </svg>
                </div>
                <div class="tambah-card"><img src="../../../assets/plus.png" alt="Detail Icon">Tambah Pengajuan</div>
            </div>
            <div class="details">
                <div class="pengajuan">
                    <div class="cardHeader">
                        <h2>Pengajuan Sampel</h2>
                    </div>
                    <div class="table">
                        <table>
                            <tr>
                                <th>Sampel Atas Nama</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Jenis Pengajuan</th>
                                <th>Status Pengajuan</th>
                            </tr>
                        </table>
                    </div>
                    <?php
                    include '../../../koneksi.php';

                    $currentYear = date('Y');
                    $sql = "SELECT id_pengajuan, nama_pasien, tanggal_pengajuan, status_pengajuan 
                            FROM pengajuan 
                            WHERE id_pengguna = ? AND YEAR(tanggal_pengajuan) = ? 
                            ORDER BY tanggal_pengajuan DESC";
                    $stmt = $connect->prepare($sql);
                    if (!$stmt) {
                        error_log("beranda.php - Prepare failed for pengajuan query: " . $connect->error);
                        echo "<div class='row'><span colspan='4'>Error fetching data.</span></div>";
                    } else {
                        $stmt->bind_param("ii", $user_id, $currentYear);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        error_log("beranda.php - Fetching pengajuan for id_pengguna: $user_id, year: $currentYear, rows found: " . $result->num_rows);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $tanggal = date('Y/m/d', strtotime($row['tanggal_pengajuan']));
                                $status_pengajuan_class = ($row['status_pengajuan'] == 'Verifikasi' || $row['status_pengajuan'] == 'Diverifikasi') ? 'verified' : ($row['status_pengajuan'] == 'Menunggu Verifikasi' ? 'pending' : '');
                                $id_pengajuan = $row['id_pengajuan'];
                                $jenis_pengujian = '';
                                if (strpos($id_pengajuan, 'JRM-') === 0) {
                                    $jenis_pengujian = 'Jaringan';
                                } elseif (strpos($id_pengajuan, 'SRM-') === 0) {
                                    $jenis_pengujian = 'Sitologi Ginekologi';
                                } elseif (strpos($id_pengajuan, 'SNRM-') === 0) {
                                    $jenis_pengujian = 'Sitologi Non Ginekologi';
                                }
                    ?>
                            <div class="row" data-href="tampil.php?id=<?php echo urlencode($id_pengajuan); ?>">
                                <span><?php echo htmlspecialchars($row['nama_pasien']); ?></span>
                                <span><?php echo $tanggal; ?></span>
                                <span><?php echo htmlspecialchars($jenis_pengujian); ?></span>
                                <span class="status_pengajuan <?php echo $status_pengajuan_class; ?>"><?php echo htmlspecialchars($row['status_pengajuan']); ?></span>
                            </div>
                    <?php
                            }
                        } else {
                            error_log("beranda.php - No pengajuan data found for id_pengguna: $user_id, year: $currentYear");
                            echo "<div class='row'><span colspan='4'>Tidak ada data yang ditemukan.</span></div>";
                        }
                        $stmt->close();
                    }
                    $connect->close();
                    ?>
                </div>
            </div>

            <div class="overlay"></div>

            <div class="menu-card">
                <div class="menu-option" data-type="jaringan">Jaringan <ion-icon class="plus-icon" name="add-outline"></ion-icon></div>
                <div class="menu-separator"></div>
                <div class="menu-option" data-type="Sitologi Ginekologi">Sitologi Ginekologi <ion-icon class="plus-icon" name="add-outline"></ion-icon></div>
                <div class="menu-separator"></div>
                <div class="menu-option" data-type="Sitologi Non Ginekologi">Sitologi Non Ginekologi <ion-icon class="plus-icon" name="add-outline"></ion-icon></div>
            </div>

            <div class="jumlah-card">
                <h2>Jumlah</h2>
                <form id="jumlahForm" action="" method="get">
                    <div class="form-group">
                        <input type="number" id="jumlah" name="jumlah" min="1" value="1" required />
                    </div>
                    <button type="submit" class="btn-submit">Oke</button>
                    <input type="hidden" id="type" name="type">
                </form>
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
            const rows = document.querySelectorAll('.row');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        const rows = document.querySelectorAll('.row');
        rows.forEach(row => {
            row.addEventListener('click', function() {
                const id = this.getAttribute('data-href').split('id=')[1];
                window.location.href = `tampil.php?id=${id}`;
            });
        });

        const tambahCard = document.querySelector('.tambah-card');
        const menuCard = document.querySelector('.menu-card');
        const jumlahCard = document.querySelector('.jumlah-card');
        const menuOptions = document.querySelectorAll('.menu-option');
        const jumlahForm = document.getElementById('jumlahForm');
        const typeInput = document.getElementById('type');
        const overlay = document.querySelector('.overlay');

        tambahCard.addEventListener('click', function() {
            menuCard.classList.add('active');
            overlay.classList.add('active');
            menuOptions.forEach(option => option.classList.remove('selected'));
            typeInput.value = '';
        });

        menuOptions.forEach(option => {
            option.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                menuOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                typeInput.value = type;
                jumlahCard.classList.add('active');
                jumlahForm.querySelector('#jumlah').focus();
            });
        });

        jumlahForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const jumlah = jumlahForm.querySelector('#jumlah').value;
            const type = typeInput.value;
            let redirectUrl = '';

            if (type === 'jaringan') {
                redirectUrl = `formJaringan.php?jumlah=${jumlah}`;
            } else if (type === 'Sitologi Ginekologi') {
                redirectUrl = `formSitologiGinekologi.php?jumlah=${jumlah}`;
            } else if (type === 'Sitologi Non Ginekologi') {
                redirectUrl = `formSitologiNonGinekologi.php?jumlah=${jumlah}`;
            }

            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        });

        document.addEventListener('click', function(event) {
            if (!menuCard.contains(event.target) && !tambahCard.contains(event.target) && !jumlahCard.contains(event.target)) {
                menuCard.classList.remove('active');
                jumlahCard.classList.remove('active');
                overlay.classList.remove('active');
            }
        });
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>