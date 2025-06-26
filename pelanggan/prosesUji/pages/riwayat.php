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

        .history {
            height: 1000px;
            flex-shrink: 0;
            background: var(--green7);
            padding: 20px;
            border-radius: 10px;
            margin: 70px 20px;
            max-height: 80vh;
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
            margin: 0;
        }

        .cardHeader .btn {
            padding: 5px 10px;
            background: var(--green2);
            text-decoration: none;
            color: var(--white);
            border-radius: 6px;
            font-size: 14px;
        }

        .history .row {
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background: var(--white);
            margin-bottom: 10px;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s;
            z-index: 1001;
        }

        .history .row:hover {
            background: var(--green6);
        }

        .history .row span {
            flex: 1;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 14px;
        }

        .status_history {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            display: inline-block;
        }

        .status_history.verified {
            background: rgba(138, 242, 150, 1);
            color: var(--black);
        }

        .status_history.pending {
            background: rgba(255, 226, 110, 1);
            color: var(--black);
        }

        .status_history.waiting {
            background: rgba(255, 133, 128, 1);
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
            .history {
                margin: 10px;
            }
            .history .row {
                padding: 8px 15px;
                height: 50px;
            }
            .history .row span {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .cardHeader h2 {
                font-size: 18px;
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
            .menu-card {
                width: 200px;
                right: 5px;
            }
            .history .row {
                height: 45px;
                padding: 6px 10px;
            }
            .history .row span {
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
                    <a href="../../pengajuan/pages/pengajuan.php">
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
                    <a href="riwayat.php">
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
                    <a href="../../signout.php">
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
                    <img src="assets/imgs/customer01.jpg" alt="User">
                    <span>RS Indah Permata</span>
                </div>
            </div>

            <div class="details">
                <div class="history">
                    <div class="cardHeader">
                        <h2>Riwayat Pengujian Sampel</h2>
                    </div>
                    <?php
                    include '../../../koneksi.php';

                    $sql = "SELECT pg.nama_pasien, pg.tanggal_pengajuan, png.tanggal_pengambilan, png.status_pengambilan, png.tanggal_pengambilan_ulang 
                            FROM pengajuan pg 
                            JOIN pengambilan png ON pg.id_pengajuan = png.id_pengajuan
                            ORDER BY pg.tanggal_pengajuan DESC LIMIT 8";
                    $result = $connect->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $tanggal_pengajuan = date('Y/m/d', strtotime($row['tanggal_pengajuan']));
                            $tanggal_pengambilan = !empty($row['tanggal_pengambilan']) ? date('Y/m/d', strtotime($row['tanggal_pengambilan'])) : '-';
                            $status_history_class = ($row['status_history'] == 'Berhasil') ? 'verified' :
                            (($row['status_history'] == 'Tertunda') ? 'pending' :
                            (($row['status_history'] == 'Menunggu history') ? 'waiting' : ''));
                            $tanggal_history_ulang = !empty($row['tanggal_history_ulang']) ? date('Y/m/d', strtotime($row['tanggal_history_ulang'])) : '-';
                    ?>
                            <div class="row" data-href="tampil.php">
                                <span><?php echo htmlspecialchars($row['nama_pasien']); ?></span>
                                <span><?php echo $tanggal_pengajuan; ?></span>
                                <span><?php echo $tanggal_history; ?></span>
                                <span class="status_history <?php echo $status_history_class; ?>"><?php echo htmlspecialchars($row['status_history']); ?></span>
                                <span><?php echo $tanggal_history_ulang; ?></span>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<div class='row' style='text-align:center; color:var(--black1);'><span colspan='5'>Tidak ada data yang ditemukan.</span></div>";
                    }
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

    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>