<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>
    <!-- ======= Styles ====== -->
    <style>
        /* =========== Google Fonts ============ */
        @import url("https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=poppins");

        /* =============== Globals ============== */
        * {
            font-family: "Ubuntu", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --green: rgba(103, 195, 192, 1);
            --green2: rgba(228, 240, 240, 1);
            --white: #fff;
            --gray: #f5f5f5;
            --black1: #222;
            --black2: #999;
        }

        body {
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            position: relative;
            width: 100%;
        }

        /* =============== Navigation ================ */
        .navigation {
            position: fixed;
            width: 230px;
            height: 100%;
            background: var(--green);
            border-left: 10px solid var(--green);
            transition: 0.5s;
            overflow: hidden;
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
            margin-bottom: 40px;
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
            color: var(--green);
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
            bottom: -300px;
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
            color: var(--green);
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

        /* --------- curve outside ---------- */
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

        /* ===================== Main ===================== */
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
            border-bottom: 1px solid var(--black2);
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

        /* ================== Search and Button ============== */
        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px;
            justify-content: flex-start;
        }

        .search-bar input {
            width: 300px;
            height: 40px;
            border-radius: 20px;
            padding: 5px 20px 5px 40px;
            font-size: 16px;
            outline: none;
            border: 1px solid var(--black2);
            background: var(--green2) url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>') no-repeat 10px center;
        }

        .filter-card {
            width: 40px;
            height: 40px;
            background: var(--green2);
            border: 1px solid var(--black2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .filter-card ion-icon {
            font-size: 1.5rem;
            color: var(--black1);
        }

        .tambah-card {
            width: 262px;
            height: 66px;
            flex-shrink: 0;
            background: var(--green);
            color: var(--white);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
        }

        .tambah-card:hover {
            background: #1795ce;
        }

        /* ================== Detail Pengajuan ============== */
        .details {
            padding: 20px;
        }

        .pengajuan {
            background: var(--green2);
            padding: 20px;
            border-radius: 10px;
            max-height: 60vh;
            overflow-y: auto;
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
            background: var(--green);
            text-decoration: none;
            color: var(--white);
            border-radius: 6px;
        }

        .pengajuan .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: var(--white);
            margin-bottom: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .pengajuan .row:hover {
            background: #f0f0f0;
        }

        .pengajuan .row span {
            flex: 1;
            text-align: center;
        }

        .pengajuan .row .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
        }

        .status.verified {
            background: #8de02c;
            color: var(--white);
        }

        .status.pending {
            background: #e9b10a;
            color: var(--white);
        }

        /* ================== Menu Card ============== */
        .menu-card {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 310px;
            height: 186px;
            background: var(--green2);
            padding: 20px;
            box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            z-index: 1000;
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
            text-align: center;
            padding: 10px 0;
        }

        .menu-option:hover {
            color: var(--green);
        }

        .menu-option.selected {
            background: var(--green);
            color: var(--white);
            border-radius: 5px;
        }

        .menu-separator {
            width: 100%;
            height: 1px;
            background: var(--black2);
            margin: 5px 0;
        }

        /* ================== Jumlah Card ============== */
        .jumlah-card {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 310px;
            height: 186px;
            background: var(--green2);
            padding: 20px;
            box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            z-index: 1000;
            flex-direction: column;
            justify-content: space-around;
            align-items: center;
            display: none;
        }

        .jumlah-card.active {
            display: flex;
        }

        .jumlah-card h2 {
            font-size: 18px;
            color: var(--black1);
            margin-bottom: 10px;
        }

        .jumlah-card .form-group {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .jumlah-card label {
            font-size: 16px;
            color: var(--black1);
            font-weight: 500;
            margin-bottom: 5px;
        }

        .jumlah-card input {
            width: 100px;
            height: 40px;
            padding: 5px 10px;
            border: 1px solid var(--black2);
            border-radius: 6px;
            font-size: 16px;
            text-align: center;
        }

        .jumlah-card .btn-submit {
            height: 40px;
            padding: 5px 15px;
            background: var(--green);
            color: var(--white);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
        }

        .jumlah-card .btn-submit:hover {
            background: #1795ce;
        }

        /* ====================== Responsive Design ========================== */
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
            .search-bar input, .filter-card, .tambah-card {
                width: 100%;
            }
            .tambah-card {
                margin-left: 0;
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
        }
    </style>
</head>

<body>
    <!-- =============== Navigation ================ -->
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <span class="icon">
                            <img src="../../assets/microscope.png" alt="logo">
                        </span>
                        <span class="title-logo">MedPath</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <img src="../../assets/dashboard.png" alt="dashboard">
                        </span>
                        <span class="title">Beranda</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <img src="../../assets/sample.png" alt="sample">
                        </span>
                        <span class="title">Pengujian</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="icon">
                            <img src="../../assets/money.png" alt="money">
                        </span>
                        <span class="title">Pembayaran</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <img src="../../assets/setting.png" alt="setting">
                        </span>
                        <span class="title">Pengaturan</span>
                    </a>
                </li>

                <li class="signout">
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="log-out-outline" style="color: black"></ion-icon>
                        </span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- ========================= Main ==================== -->
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

            <!-- ====================== Search and Button ==================== -->
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search pengajuan">
                <div class="filter-card">
                    <ion-icon name="ellipsis-vertical"></ion-icon>
                </div>
                <div class="tambah-card">Tambah Pengajuan</div>
            </div>

            <!-- ================ Pengajuan Sampel ================= -->
            <div class="details">
                <div class="pengajuan">
                    <div class="cardHeader">
                        <h2>Pengajuan Sampel</h2>
                        <a href="#" class="btn">View All</a>
                    </div>
                    <div class="row" data-href="tampil.php">
                        <span>Andi Barokah</span>
                        <span>2025/03/18</span>
                        <span>Jaringan</span>
                        <span class="status verified">Diverifikasi</span>
                    </div>
                    <div class="row" data-href="tampil.php">
                        <span>Agus Budiyanto</span>
                        <span>2025/03/18</span>
                        <span>Sitologi Non Ginekologi</span>
                        <span class="status verified">Diverifikasi</span>
                    </div>
                    <div class="row" data-href="tampil.php">
                        <span>Suci Aminah</span>
                        <span>2025/03/18</span>
                        <span>Sitologi Ginekologi</span>
                        <span class="status verified">Diverifikasi</span>
                    </div>
                    <div class="row" data-href="tampil.php">
                        <span>Bagas Andikara</span>
                        <span>2025/03/19</span>
                        <span>Jaringan</span>
                        <span class="status verified">Diverifikasi</span>
                    </div>
                    <div class="row" data-href="tampil.php">
                        <span>Cinta Andini</span>
                        <span>2025/03/19</span>
                        <span>Jaringan</span>
                        <span class="status pending">Menunggu Verifikasi</span>
                    </div>
                    <div class="row" data-href="tampil.php">
                        <span>Budiman</span>
                        <span>2025/03/19</span>
                        <span>Jaringan</span>
                        <span class="status pending">Menunggu Verifikasi</span>
                    </div>
                    <!-- Additional rows for scrolling example -->
                    <div class="row" data-href="tampil.php">
                        <span>Extra 1</span>
                        <span>2025/03/20</span>
                        <span>Jaringan</span>
                        <span class="status verified">Diverifikasi</span>
                    </div>
                    <div class="row" data-href="tampil.php">
                        <span>Extra 2</span>
                        <span>2025/03/20</span>
                        <span>Sitologi</span>
                        <span class="status pending">Menunggu Verifikasi</span>
                    </div>
                </div>
            </div>

            <!-- ================== Menu Card ================= -->
            <div class="menu-card">
                <div class="menu-option" data-type="jaringan">Jaringan</div>
                <div class="menu-separator"></div>
                <div class="menu-option" data-type="sitologi">Sitologi</div>
                <div class="menu-separator"></div>
                <div class="menu-option" data-type="non-sitologi">Non Sitologi</div>
            </div>

            <!-- ================== Jumlah Card ================= -->
            <div class="jumlah-card">
                <h2>Jumlah Pengajuan Sampel</h2>
                <form id="jumlahForm" action="" method="get">
                    <div class="form-group">
                        <label for="jumlah">Masukkan Jumlah Pengajuan</label>
                        <input type="number" id="jumlah" name="jumlah" min="1" value="1" required />
                    </div>
                    <button type="submit" class="btn-submit">Lanjutkan</button>
                    <input type="hidden" id="type" name="type">
                </form>
            </div>
        </div>
    </div>

    <!-- =========== Scripts =========  -->
    <script>
        // add hovered class to selected list item
        let list = document.querySelectorAll(".navigation li");

        function activeLink() {
            list.forEach((item) => {
                item.classList.remove("hovered");
            });
            this.classList.add("hovered");
        }

        list.forEach((item) => item.addEventListener("mouseover", activeLink));

        // Menu Toggle
        let toggle = document.querySelector(".toggle");
        let navigation = document.querySelector(".navigation");
        let main = document.querySelector(".main");

        toggle.onclick = function () {
            navigation.classList.toggle("active");
            main.classList.toggle("active");
        };

        // Search Functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.row');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Make rows clickable to tampil.php
        const rows = document.querySelectorAll('.row');
        rows.forEach(row => {
            row.addEventListener('click', function() {
                window.location.href = this.getAttribute('data-href');
            });
        });

        // Tambah Pengajuan Menu and Jumlah
        const tambahCard = document.querySelector('.tambah-card');
        const menuCard = document.querySelector('.menu-card');
        const jumlahCard = document.querySelector('.jumlah-card');
        const menuOptions = document.querySelectorAll('.menu-option');
        const jumlahForm = document.getElementById('jumlahForm');
        const typeInput = document.getElementById('type');

        tambahCard.addEventListener('click', function() {
            menuCard.classList.add('active');
            jumlahCard.classList.remove('active');
            menuOptions.forEach(option => option.classList.remove('selected'));
        });

        menuOptions.forEach(option => {
            option.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                menuOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                typeInput.value = type;
                jumlahCard.classList.add('active');
                menuCard.style.display = 'flex'; // Keep menu visible
                jumlahForm.querySelector('#jumlah').focus();
            });
        });

        // Form Submission with Conditional Redirect
        jumlahForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const jumlah = jumlahForm.querySelector('#jumlah').value;
            const type = typeInput.value;
            let redirectUrl = '';

            if (type === 'jaringan') {
                redirectUrl = `formJaringan.php?jumlah=${jumlah}`;
            } else if (type === 'sitologi') {
                redirectUrl = `formSitologi.php?jumlah=${jumlah}`;
            } else if (type === 'non-sitologi') {
                redirectUrl = `formNonSitologi.php?jumlah=${jumlah}`;
            }

            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        });

        // Close cards on outside click
        document.addEventListener('click', function(event) {
            if (!menuCard.contains(event.target) && !tambahCard.contains(event.target)) {
                menuCard.classList.remove('active');
            }
            if (!jumlahCard.contains(event.target) && !menuCard.contains(event.target)) {
                jumlahCard.classList.remove('active');
            }
        });
    </script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>