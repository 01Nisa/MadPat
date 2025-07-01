<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("location:../../../login.php?pesan=belum_login");
    exit();
}

$user_id = $_SESSION['user'];
include '../../../koneksi.php';

$sql = "SELECT nama, foto FROM pengguna WHERE id_pengguna = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$connect->close();

if (!$user) {
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
            margin-bottom: 80px;
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
            top: 770px;
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

        .cardBox {
            position: relative;
            width: 100%;
            padding: 20px;
            margin-top: 90px;
            display: grid;
            grid-template-columns: repeat(3, 0.7fr);
            grid-gap: 30px;
        }

        .cardBox .card {
            position: relative;
            background: var(--green1);
            padding: 30px;
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            cursor: pointer;
            box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
        }

        .cardBox .card .jenisPengujian {
            position: relative;
            font-weight: 500;
            font-size: 30px;
            color: var(--white);
        }

        .cardBox .card .cardName {
            color: var(--black2);
            font-size: 1.1rem;
            margin-top: 5px;
        }

        .cardBox .card .jumlah {
            font-size: 3.5rem;
            padding-left: 20px;
            color: var(--white);
        }

        .cardBox .card:hover {
            background: var(--white);
        }
        .cardBox .card:hover .jenisPengujian,
        .cardBox .card:hover .cardName,
        .cardBox .card:hover .jumlah {
            color: var(--green1);
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
            margin-top: 20px;
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
            margin-top: 30px;
        }

        .filter-card {
          width: 90px;
          height: 66px;
          flex-shrink: 0;
          margin-top: 20px;
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

        .unduh-card {
            width: 397px;
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
            margin-top: 20px;
            z-index: 1001;
        }

        .unduh-card:hover {
            background: var(--green6);
        }

        .unduh-card img {
            margin-left: -80px;
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
            border-radius: 10px;
            margin-bottom: 100px;
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
        .cardHeader .btn {
            padding: 5px 10px;
            background: var(--green2);
            text-decoration: none;
            color: var(--white);
            border-radius: 6px;
        }

        .column-titles {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--black1);
            margin-bottom: 10px;
            padding: 0 30px;
            height: 40px;
            border-radius: 10px;
        }

        .column-titles span {
            flex: 1;
            text-align: center;
            font-size: 16px;
            line-height: 40px; 
        }

        .pengajuan .row {
            height: 69px;
            flex-shrink: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
            gap: 20px;
            background: var(--white);
            margin-bottom: 15px;
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
            text-align: center;
            padding: 0;
            margin: 0 60px; 
            line-height: 30px; 
        }

        .pengajuan .row .status_pengujian {
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

        .status_pengujian.verified {
            background: rgba(255, 182, 126, 1);
            color: var(--black);
        }

        .status_pengujian.pending {
            background: rgba(138, 242, 150, 1);
            color: var(--black);
        }

        .result-btn {
            width: 97px;
            height: 37px;
            border-radius: 15px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px; 
            position: relative;
            bottom: 2px; 
        }

        .result-btn.completed {
            background-color: #4285f4; 
            color: white;
        }

        .result-btn.in-progress {
            background-color: #ccc; 
            color: #666;
            cursor: default;
            pointer-events: none;
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
            top: 55%;
            right: 330px; 
            transform: translateY(-50%);
            width: 100px;
            height: 130px;
            background: var(--white);
            padding: 20px;
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
            margin-top: 2px;
            margin-bottom: 2px;
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

        .menu-separator {
            width: 100%;
            height: 1px;
            background: var(--black2);
            margin: 5px 0;
        }

        .close-button {
            position: absolute;
            top: 4px;
            right: 10px;
            font-size: 24px;
            font-weight: bold;
            color: var(--green1);
            cursor: pointer;
            transition: color 0.3s;
        }

        .close-button:hover {
            color: var(--green5);
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
            .column-titles span {
                font-size: 12px;
            }
            .pengajuan {
                overflow-x: auto;
            }
            .search-bar {
                flex-direction: column;
                gap: 10px;
            }
            .search-bar input, .filter-card, .unduh-card {
                width: 100%;
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
            .column-titles span {
                font-size: 10px;
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
                        <span class="title">Sign Out</span>
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
            <div class="cardBox">
                <?php
                    include '../../../koneksi.php';

                    $currentYear = date('Y');
                    $sqlCount = "SELECT 
                        SUM(CASE WHEN id_pengujian LIKE 'JRM-%' THEN 1 ELSE 0 END) as jaringan_count,
                        SUM(CASE WHEN id_pengujian LIKE 'SRM-%' THEN 1 ELSE 0 END) as sitologi_ginekologi_count,
                        SUM(CASE WHEN id_pengujian LIKE 'SNRM-%' THEN 1 ELSE 0 END) as sitologi_non_ginekologi_count
                        FROM pengujian
                        WHERE YEAR(tanggal_terima) = ?";
                    $stmtCount = $connect->prepare($sqlCount);
                    $stmtCount->bind_param("i", $currentYear);
                    $stmtCount->execute();
                    $resultCount = $stmtCount->get_result();
                    $counts = $resultCount->fetch_assoc();

                    $jaringanCount = $counts['jaringan_count'];
                    $sitologiGinekologiCount = $counts['sitologi_ginekologi_count'];
                    $sitologiNonGinekologiCount = $counts['sitologi_non_ginekologi_count'];

                    $stmtCount->close();
                ?>
                <div class="card">
                    <div>
                        <div class="jenisPengujian">Pengujian Jaringan</div>
                    </div>
                    <div class="jumlah">
                        <?php echo $jaringanCount; ?>
                    </div>
                </div>

                <div class="card">
                    <div>
                        <div class="jenisPengujian">Pengujian Sitologi Ginekologi</div>
                    </div>
                    <div class="jumlah">
                        <?php echo $sitologiGinekologiCount; ?>
                    </div>
                </div>

                <div class="card">
                    <div>
                        <div class="jenisPengujian">Pengujian Sitologi Non Ginekologi</div>
                    </div>
                    <div class="jumlah">
                        <?php echo $sitologiNonGinekologiCount; ?>
                    </div>
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
                <div class="unduh-card"><img src="../../../assets/install.png" alt="Install Icon">Unduh Hasil Pengujian</div>
            </div>

            <div class="details">
                <div class="pengajuan">
                    <div class="cardHeader">
                        <h2>Pengajuan Sampel</h2>
                    </div>
                    <div class="column-titles">
                        <span>Sampel Atas Nama</span>
                        <span>Tanggal Terima</span>
                        <span>Jenis Pengujian Sampel</span>
                        <span>Status Pengujian</span>
                        <span>Tanggal Jadi</span>
                        <span>Hasil Pengujian</span>
                    </div>
                    <?php
                    include '../../../koneksi.php'; 

                    $currentYear = date('Y'); 
                    $sql = "SELECT id_pengujian, nama_pasien, tanggal_terima, status_pengujian, tanggal_jadi
                            FROM pengujian
                            WHERE YEAR(tanggal_terima) = ? 
                            ORDER BY tanggal_terima DESC LIMIT 8";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("i", $currentYear);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $tanggal_terima = date('Y/m/d', strtotime($row['tanggal_terima'])); 
                            $tanggal_jadi = date('Y/m/d', strtotime($row['tanggal_jadi'])); 
                            $status_pengujian_class = ($row['status_pengujian'] == 'Selesai' || $row['status_pengujian'] == 'Diverifikasi') ? 'verified' : ($row['status_pengujian'] == 'Diproses' ? 'pending' : '');

                            $id_pengujian = $row['id_pengujian'];
                            $jenis_pengujian = '';
                            if (strpos($id_pengujian, 'JRM-') === 0) {
                                $jenis_pengujian = 'Jaringan';
                            } elseif (strpos($id_pengujian, 'SRM-') === 0) {
                                $jenis_pengujian = 'Sitologi Ginekologi';
                            } elseif (strpos($id_pengujian, 'SNRM-') === 0) {
                                $jenis_pengujian = 'Sitologi Non Ginekologi';
                            }
                    ?>
                            <div class="row">
                                <span><?php echo htmlspecialchars($row['nama_pasien']); ?></span>
                                <span><?php echo $tanggal_terima; ?></span>
                                <span><?php echo htmlspecialchars($jenis_pengujian); ?></span>
                                <span class="status_pengujian <?php echo $status_pengujian_class; ?>"><?php echo htmlspecialchars($row['status_pengujian']); ?></span>
                                <span><?php echo $tanggal_jadi; ?></span>
                                <span>
                                    <button class="result-btn <?php echo ($row['status_pengujian'] == 'Selesai' || $row['status_pengujian'] == 'Diverifikasi') ? 'completed' : 'in-progress'; ?>" 
                                            data-id="<?php echo urlencode($id_pengujian); ?>">Lihat
                                        <ion-icon name="<?php echo ($row['status_pengujian'] == 'Selesai' || $row['status_pengujian'] == 'Diverifikasi') ? 'eye-outline' : 'eye-off-outline'; ?>"></ion-icon>
                                    </button> 
                                </span>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<div class='row'><span colspan='6'>Tidak ada data yang ditemukan.</span></div>";
                    }
                    $stmt->close();
                    $connect->close();
                    ?>
                </div>
            </div>
        
            <div class="overlay"></div>

            <div class="menu-card">
               <div class="close-button" onclick="window.location='pengujian.php'">×</div>
                <div class="menu-option" data-type="pdf">pdf</div>
                <div class="menu-separator"></div>
                <div class="menu-option" data-type="word">word</div>
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

        const resultButtons = document.querySelectorAll('.result-btn.completed');
        resultButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                window.location.href = `lihatHasil.php?id=${id}`;
            });
        });
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>