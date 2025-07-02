<?php
session_start();

error_log("penerimaan.php - Session user ID: " . ($_SESSION['user'] ?? 'Not set'));

if (!isset($_SESSION['user'])) {
    error_log("penerimaan.php - Redirecting to login: Session user not set");
    header("location:../login.php?pesan=belum_login");
    exit();
}

$user_id = $_SESSION['user'];
include '../koneksi.php';

if (!$connect) {
    error_log("penerimaan.php - Database connection failed: " . mysqli_connect_error());
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch user data
$sql = "SELECT nama, foto FROM pengguna WHERE id_pengguna = ?";
$stmt = $connect->prepare($sql);
if (!$stmt) {
    error_log("penerimaan.php - Prepare failed: " . $connect->error);
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

// Handle reject action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tolak_id'])) {
    $id_pengajuan = $_POST['tolak_id'];
    $sql = "UPDATE pengajuan SET status_pengajuan = 'Ditolak' WHERE id_pengajuan = ?";
    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        error_log("penerimaan.php - Prepare failed for reject: " . $connect->error);
        die("Prepare failed: " . $connect->error);
    }
    $stmt->bind_param("s", $id_pengajuan);
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?tolak=1");
        exit();
    } else {
        error_log("penerimaan.php - Execute failed for reject: " . $stmt->error);
        die("Gagal memperbarui status: " . $stmt->error);
    }
    $stmt->close();
}

// Handle approve action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setujui_id'])) {
    $id_pengajuan = $_POST['setujui_id'];

    // Fetch pengajuan data
    $query_select = "SELECT * FROM pengajuan WHERE id_pengajuan = ?";
    $stmt_select = $connect->prepare($query_select);
    if (!$stmt_select) {
        error_log("penerimaan.php - Prepare failed for select: " . $connect->error);
        die("Prepare failed: " . $connect->error);
    }
    $stmt_select->bind_param("s", $id_pengajuan);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();

    if ($result_select && $result_select->num_rows > 0) {
        $data = $result_select->fetch_assoc();
        $id_pengujian = $data['id_pengajuan'];
        $nama_pasien = $data['nama_pasien'];
        $usia = $data['usia'];
        $alamat = $data['alamat'];
        $nomor_pemeriksaan = $data['nomor_pemeriksaan'];
        $tanggal_terima = date('Y-m-d');
        $status_pengujian = 'Diproses';

        if (strpos($id_pengujian, 'JRM-') === 0) {
            $tanggal_jadi = date('Y-m-d', strtotime($tanggal_terima . ' +5 days'));
        } elseif (strpos($id_pengujian, 'SRM-') === 0) {
            $tanggal_jadi = date('Y-m-d', strtotime($tanggal_terima . ' +7 days'));
        } elseif (strpos($id_pengujian, 'SNRM-') === 0) {
            $tanggal_jadi = date('Y-m-d', strtotime($tanggal_terima . ' +8 days'));
        } else {
            $tanggal_jadi = NULL;
        }

        // Generate ID Pengambilan
        $sql_max = "SELECT MAX(id_pengambilan) AS max_id FROM pengambilan";
        $result_max = $connect->query($sql_max);
        $max_id = 'PA-000';

        if ($result_max && $row = $result_max->fetch_assoc()) {
            $max_id = $row['max_id'] ?: 'PA-000';
        }

        $nomor_terakhir = (int) substr($max_id, 3);
        $nomor_baru = $nomor_terakhir + 1;
        $id_pengambilan_baru = 'PA-' . str_pad($nomor_baru, 3, '0', STR_PAD_LEFT);

        // Insert into pengambilan
        $sql_pengambilan = "INSERT INTO pengambilan (id_pengambilan, id_pengajuan, tanggal_pengambilan, status_pengambilan)
                            VALUES (?, ?, ?, 'Selesai')";
        $stmt_pengambilan = $connect->prepare($sql_pengambilan);
        if (!$stmt_pengambilan) {
            error_log("penerimaan.php - Prepare failed for pengambilan: " . $connect->error);
            die("Prepare failed: " . $connect->error);
        }
        $stmt_pengambilan->bind_param("sss", $id_pengambilan_baru, $id_pengujian, $tanggal_terima);
        if (!$stmt_pengambilan->execute()) {
            error_log("penerimaan.php - Execute failed for pengambilan: " . $stmt_pengambilan->error);
            die("Gagal insert ke pengambilan: " . $stmt_pengambilan->error);
        }
        $stmt_pengambilan->close();

        // Insert into pengujian
        $sql_pengujian = "INSERT INTO pengujian (id_pengujian, id_pengambilan, nama_pasien, usia, tanggal_terima, status_pengujian, tanggal_jadi)
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_pengujian = $connect->prepare($sql_pengujian);
        if (!$stmt_pengujian) {
            error_log("penerimaan.php - Prepare failed for pengujian: " . $connect->error);
            die("Prepare failed: " . $connect->error);
        }
        $stmt_pengujian->bind_param("sssiss", $id_pengujian, $id_pengambilan_baru, $nama_pasien, $usia, $tanggal_terima, $status_pengujian, $tanggal_jadi);
        if ($stmt_pengujian->execute()) {
            // Update status pengajuan
            $sql_update = "UPDATE pengajuan SET status_pengajuan = 'Verifikasi' WHERE id_pengajuan = ?";
            $stmt_update = $connect->prepare($sql_update);
            if (!$stmt_update) {
                error_log("penerimaan.php - Prepare failed for update: " . $connect->error);
                die("Prepare failed: " . $connect->error);
            }
            $stmt_update->bind_param("s", $id_pengajuan);
            if ($stmt_update->execute()) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
                exit();
            } else {
                error_log("penerimaan.php - Execute failed for update: " . $stmt_update->error);
                die("Gagal update status pengajuan: " . $stmt_update->error);
            }
            $stmt_update->close();
        } else {
            error_log("penerimaan.php - Execute failed for pengujian: " . $stmt_pengujian->error);
            die("Gagal insert ke pengujian: " . $stmt_pengujian->error);
        }
        $stmt_pengujian->close();
    }
    $stmt_select->close();
}

// Calculate statistics for cardBox
$jaringan = 0;
$ginekologi = 0;
$non_ginekologi = 0;

$sql = "SELECT id_pengajuan FROM pengajuan WHERE status_pengajuan = 'Menunggu Verifikasi'";
$result = $connect->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_pengajuan = $row['id_pengajuan'];
        if (strpos($id_pengajuan, 'JRM-') === 0) {
            $jaringan++;
        } elseif (strpos($id_pengajuan, 'SRM-') === 0) {
            $ginekologi++;
        } elseif (strpos($id_pengajuan, 'SNRM-') === 0) {
            $non_ginekologi++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penerimaan - MedPath</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            margin-bottom: -40px;
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
            bottom: -150px;
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
            width: calc(100% - 226px);
            left: 226px;
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
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .cardBox .card:hover {
            background: var(--white);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .cardBox .card .cardName {
            color: var(--white);
            font-size: 1.8rem;
        }

        .cardBox .card .numbertext {
            font-size: 3.5rem;
            color: var(--white);
        }

        .cardBox .card:hover .cardName,
        .cardBox .card:hover .numbertext {
            color: var(--green1);
        }

        .details {
            padding: 20px;
            z-index: 1001;
        }

        .Approval, .pengujian {
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

        .Approval .column-titles, .Approval .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 5px;
            padding: 10px 0;
            height: 40px;
            border-radius: 10px;
            gap: 1px;
        }

        .Approval .column-titles span, .Approval .row span {
            flex: 1;
            text-align: center;
            font-size: 14px;
            line-height: 30px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 100px;
        }

        .Approval .column-titles span:nth-child(1), .Approval .row span:nth-child(1) {
            flex: 1.5;
            min-width: 150px;
        }

        .Approval .column-titles span:nth-child(2), .Approval .row span:nth-child(2) {
            flex: 1;
            min-width: 120px;
        }

        .Approval .column-titles span:nth-child(3), .Approval .row span:nth-child(3) {
            flex: 1.5;
            min-width: 150px;
        }

        .Approval .column-titles span:nth-child(4), .Approval .row span:nth-child(4) {
            flex: 1;
            min-width: 100px;
        }

        .Approval .column-titles span:nth-child(5), .Approval .row span:nth-child(5) {
            flex: 1.8;
            min-width: 180px;
        }

        .Approval .column-titles span:nth-child(6), .Approval .row span:nth-child(6) {
            flex: 1.5;
            min-width: 150px;
        }

        .pengujian .column-titles, .pengujian .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 5px;
            padding: 10px 0;
            height: 40px;
            border-radius: 10px;
            gap: 10px;
        }

        .pengujian .column-titles span, .pengujian .row span {
            flex: 1;
            text-align: center;
            font-size: 14px;
            line-height: 30px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 100px;
        }

        .pengujian .column-titles span:nth-child(1), .pengujian .row span:nth-child(1) {
            min-width: 100px;
        }

        .pengujian .column-titles span:nth-child(2), .pengujian .row span:nth-child(2) {
            min-width: 100px;
        }

        .pengujian .column-titles span:nth-child(3), .pengujian .row span:nth-child(3) {
            min-width: 100px;
        }

        .pengujian .column-titles span:nth-child(4), .pengujian .row span:nth-child(4) {
            min-width: 100px;
        }

        .pengujian .column-titles span:nth-child(5), .pengujian .row span:nth-child(5) {
            min-width: 150px;
        }

        .pengujian .column-titles span:nth-child(6), .pengujian .row span:nth-child(6) {
            min-width: 80px;
        }

        .pengujian .column-titles span:nth-child(7), .pengujian .row span:nth-child(7) {
            min-width: 120px;
        }

        .Approval .row, .pengujian .row {
            height: 69px;
            flex-shrink: 0;
            background: var(--white);
            margin-bottom: 15px;
            border-radius: 15px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .Approval .row:hover, .pengujian .row:hover {
            background: var(--green6);
        }

        .status_pengujian {
            width: 100%;
            height: 37px;
            padding: 2px 10px;
            border-radius: 15px;
            font-size: 14px;
            margin: 0 auto;
            position: relative;
            bottom: 2px;
            text-align: center;
            line-height: 37px;
        }

        .status_pengujian.verified {
            background: rgba(255, 182, 126, 1);
            color: var(--black1);
        }

        .status_pengujian.pending {
            background: rgba(138, 242, 150, 1);
            color: var(--black1);
        }

        .status_pengujian.rejected {
            background: rgba(255, 99, 71, 1);
            color: var(--white);
        }

        .result-btn {
            width: 37px;
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

        .result-btn.accept {
            background-color: #28a745;
            color: white;
        }

        .result-btn.reject {
            background-color: #d33;
            color: white;
        }

        .result-btn.download {
            background-color: #777;
            color: white;
        }

        .Approval .row span.actions, .pengujian .row span.actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        @media (max-width: 991px) {
            .navigation {
                left: -226px;
            }
            .navigation.active {
                width: 226px;
                left: 0;
            }
            .main {
                width: 100%;
                left: 0;
            }
            .main.active {
                left: 226px;
            }
            .cardBox {
                grid-template-columns: repeat(2, 1fr);
            }
            .details {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .cardBox {
                grid-template-columns: 1fr;
            }
            .Approval, .pengujian {
                overflow-x: auto;
            }
            .Approval .column-titles, .Approval .row {
                min-width: 750px;
            }
            .pengujian .column-titles, .pengujian .row {
                min-width: 750px;
            }
            .Approval .column-titles span, .Approval .row span,
            .pengujian .column-titles span, .pengujian .row span {
                font-size: 12px;
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
            .Approval .column-titles span, .Approval .row span,
            .pengujian .column-titles span, .pengujian .row span {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <?php
    if (isset($_GET['success'])) {
        echo "<script>document.addEventListener('DOMContentLoaded', function() { showSuccess(); });</script>";
    }
    if (isset($_GET['tolak'])) {
        echo "<script>document.addEventListener('DOMContentLoaded', function() { showReject(); });</script>";
    }
    ?>
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
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
                <li class="hovered">
                    <a href="penerimaan.php">
                        <span class="icon">
                            <img src="../assets/penerimaan.png" alt="penerimaan">
                        </span>
                        <span class="title">Penerimaan</span>
                    </a>
                </li>
                <li>
                    <a href="pengujian.php">
                        <span class="icon">
                            <img src="../assets/prosesuji.png" alt="proses_uji">
                        </span>
                        <span class="title">Proses Uji</span>
                    </a>
                </li>
                <li>
                    <a href="riwayat_uji.php">
                        <span class="icon">
                            <img src="../assets/riwayat.png" alt="riwayat_uji">
                        </span>
                        <span class="title">Riwayat Uji</span>
                    </a>
                </li>
                <li>
                    <a href="pengaturan.php">
                        <span class="icon">
                            <img src="../assets/setting.png" alt="pengaturan">
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

            <div class="cardBox">
                <div class="card">
                    <div>
                        <div class="cardName">Pengajuan Jaringan</div>
                    </div>
                    <div class="numbertext"><?php echo $jaringan; ?></div>
                </div>
                <div class="card">
                    <div>
                        <div class="cardName">Pengajuan Sitologi Ginekologi</div>
                    </div>
                    <div class="numbertext"><?php echo $ginekologi; ?></div>
                </div>
                <div class="card">
                    <div>
                        <div class="cardName">Pengajuan Sitologi Non Ginekologi</div>
                    </div>
                    <div class="numbertext"><?php echo $non_ginekologi; ?></div>
                </div>
            </div>

            <div class="details">
                <div class="Approval">
                    <div class="cardHeader">
                        <h2>Persetujuan Penerimaan Sampel</h2>
                    </div>
                    <div class="column-titles">
                        <span>Sampel Atas Nama</span>
                        <span>Tanggal Pengajuan</span>
                        <span>Jenis Pengajuan</span>
                        <span>Status Pengajuan</span>
                        <span>Pengajuan</span>
                        <span>Verifikasi</span>
                    </div>
                    <?php
                    $query = "SELECT id_pengajuan, nama_pasien, tanggal_pengajuan, status_pengajuan FROM pengajuan WHERE status_pengajuan = 'Menunggu Verifikasi'";
                    $stmt = $connect->prepare($query);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $jenis_pengajuan = '';
                            $link_detail = '#';
                            $id = $row['id_pengajuan'];

                            if (strpos($id, 'JRM-') === 0) {
                                $jenis_pengajuan = 'Jaringan';
                                $link_detail = 'PengajuanJRM.php?id=' . $id;
                            } elseif (strpos($id, 'SRM-') === 0) {
                                $jenis_pengajuan = 'Sitologi Ginekologi';
                                $link_detail = 'PengajuanSRM.php?id=' . $id;
                            } elseif (strpos($id, 'SNRM-') === 0) {
                                $jenis_pengajuan = 'Sitologi Non Ginekologi';
                                $link_detail = 'PengajuanSNRM.php?id=' . $id;
                            }

                            $status_class = 'pending';
                            ?>
                            <div class="row">
                                <span><?php echo htmlspecialchars($row['nama_pasien']); ?></span>
                                <span><?php echo htmlspecialchars(date('Y/m/d', strtotime($row['tanggal_pengajuan']))); ?></span>
                                <span><?php echo htmlspecialchars($jenis_pengajuan); ?></span>
                                <span class="status_pengujian <?php echo $status_class; ?>">Menunggu Verifikasi</span>
                                <span class="actions">
                                    <a href="<?php echo $link_detail; ?>" class="result-btn completed" title="Lihat">
                                        <ion-icon name="eye-outline"></ion-icon>
                                    </a>
                                    <a href="download_pengajuan.php?id=<?php echo urlencode($row['id_pengajuan']); ?>" class="result-btn download" title="Download">
                                        <ion-icon name="download-outline"></ion-icon>
                                    </a>
                                    <button class="result-btn reject" onclick="konfirmasiTolak('<?php echo $row['id_pengajuan']; ?>')" title="Tolak">
                                        <ion-icon name="close-outline"></ion-icon>
                                    </button>
                                    <form id="formTolak_<?php echo $row['id_pengajuan']; ?>" method="POST" style="display:none;">
                                        <input type="hidden" name="tolak_id" value="<?php echo htmlspecialchars($row['id_pengajuan']); ?>">
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="setujui_id" value="<?php echo htmlspecialchars($row['id_pengajuan']); ?>">
                                        <button type="submit" class="result-btn accept" title="Setujui">
                                            <ion-icon name="checkmark-outline"></ion-icon>
                                        </button>
                                    </form>
                                </span>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<div class='row' style='text-align:center; color:var(--black2);'><span colspan='6'>Tidak ada pengajuan yang ditemukan.</span></div>";
                    }
                    $stmt->close();
                    ?>
                </div>
            </div>

            <div class="details">
                <div class="pengujian">
                    <div class="cardHeader">
                        <h2>Pengujian Sampel</h2>
                    </div>
                    <div class="column-titles">
                        <span>Id Pengambilan</span>
                        <span>No Lab</span>
                        <span>Tanggal Terima</span>
                        <span>Tanggal Jadi</span>
                        <span>Sampel Atas Nama</span>
                        <span>Umur</span>
                        <span>Tindakan</span>
                    </div>
                    <?php
                    $currentYear = date('Y');
                    $sql = "SELECT a.id_pengambilan, u.id_pengujian, u.nama_pasien, u.tanggal_terima, u.usia, u.tanggal_jadi
                            FROM pengambilan a
                            JOIN pengujian u ON a.id_pengambilan = u.id_pengambilan
                            WHERE YEAR(u.tanggal_terima) = ?
                            ORDER BY u.tanggal_terima DESC";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("i", $currentYear);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $tanggal_terima = date('Y/m/d', strtotime($row['tanggal_terima']));
                            $tanggal_jadi = !empty($row['tanggal_jadi']) ? date('Y/m/d', strtotime($row['tanggal_jadi'])) : '-';
                            $id_pengujian = $row['id_pengujian'];
                            $link_update = '#';
                            if (strpos($id_pengujian, 'JRM-') === 0) {
                                $link_update = 'updateJRM.php?id=' . $id_pengujian;
                            } elseif (strpos($id_pengujian, 'SRM-') === 0) {
                                $link_update = 'updateSRM.php?id=' . $id_pengujian;
                            } elseif (strpos($id_pengujian, 'SNRM-') === 0) {
                                $link_update = 'updateSNRM.php?id=' . $id_pengujian;
                            }
                            ?>
                            <div class="row" data-href="tampil.php?id=<?php echo urlencode($row['id_pengambilan']); ?>">
                                <span><?php echo htmlspecialchars($row['id_pengambilan']); ?></span>
                                <span><?php echo htmlspecialchars($row['id_pengujian']); ?></span>
                                <span><?php echo $tanggal_terima; ?></span>
                                <span><?php echo $tanggal_jadi; ?></span>
                                <span><?php echo htmlspecialchars($row['nama_pasien']); ?></span>
                                <span><?php echo htmlspecialchars($row['usia']); ?></span>
                                <span class="actions">
                                    <a href="<?php echo htmlspecialchars($link_update); ?>" class="result-btn completed" title="Update">
                                        <ion-icon name="create-outline"></ion-icon>
                                    </a>
                                    <button class="result-btn reject" onclick="hapusData('<?php echo $id_pengujian; ?>')" title="Hapus">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </button>
                                </span>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<div class='row' style='text-align:center; color:var(--black1);'><span colspan='7'>Tidak ada data yang ditemukan.</span></div>";
                    }
                    $stmt->close();
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

        function showSuccess() {
            let title = '<?php echo isset($_GET['success']) && $_GET['success'] == 'delete' ? 'Pengujian berhasil dihapus' : 'Berhasil menyetujui pengajuan'; ?>';
            Swal.fire({
                icon: 'success',
                title: title,
                showConfirmButton: false,
                timer: 1500,
                customClass: {
                    popup: 'swal-custom'
                }
            });
        }

        function konfirmasiTolak(id) {
            Swal.fire({
                title: 'Tolak Pengajuan?',
                text: "Anda yakin ingin menolak pengajuan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'swal-custom'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formTolak_' + id).submit();
                }
            });
        }

        function showReject() {
            Swal.fire({
                icon: 'success',
                title: 'Pengajuan berhasil ditolak',
                showConfirmButton: false,
                timer: 1500,
                customClass: {
                    popup: 'swal-custom'
                }
            });
        }

        function hapusData(id) {
            Swal.fire({
                title: 'Hapus Pengujian?',
                text: "Anda yakin ingin menghapus pengujian ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'swal-custom'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_pengujian.php?id=' + id;
                }
            });
        }

        document.querySelectorAll('.pengujian .row').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('.result-btn')) {
                    window.location.href = this.getAttribute('data-href');
                }
            });
        });
    </script>
</body>
</html>