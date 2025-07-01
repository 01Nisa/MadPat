<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tolak_id'])) {
    $id_pengajuan = $_POST['tolak_id'];
    $sql = "UPDATE pengajuan SET status_pengajuan = 'Ditolak' WHERE id_pengajuan = '$id_pengajuan'";

    if ($connect->query($sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?tolak=1");
        exit();
    } else {
        echo "Gagal memperbarui status: " . $connect->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setujui_id'])) {
    $id_pengajuan = $_POST['setujui_id'];

    // Ambil data dari pengajuan
    $query_select = "SELECT * FROM pengajuan WHERE id_pengajuan = '$id_pengajuan'";
    $result_select = $connect->query($query_select);

    if ($result_select && $result_select->num_rows > 0) {
        $data = $result_select->fetch_assoc();

      
        $id_pengujian = $data['id_pengajuan']; 
        $nama_pasien = $data['nama_pasien'];
        $usia = $data['usia'];
        $alamat = $data['alamat'];
        $nomor_pemeriksaan = $data['nomor_pemeriksaan'];
        $tanggal_terima = date('Y-m-d'); // hari ini
        $status_pengujian = 'Diproses';

        // Tentukan tanggal_jadi berdasarkan jenis pengujian
        $jenis_pengajuan = '';
        if (strpos($id_pengujian, 'JRM-') === 0) {
            $jenis_pengajuan = 'Jaringan';
            $tanggal_jadi = date('Y-m-d', strtotime($tanggal_terima . ' +5 days'));
        } elseif (strpos($id_pengujian, 'SRM-') === 0) {
            $jenis_pengajuan = 'Sitologi Ginekologi';
            $tanggal_jadi = date('Y-m-d', strtotime($tanggal_terima . ' +7 days'));
        } elseif (strpos($id_pengujian, 'SNRM-') === 0) {
            $jenis_pengajuan = 'Sitologi Non Ginekologi';
            $tanggal_jadi = date('Y-m-d', strtotime($tanggal_terima . ' +8 days'));
        } else {
            $tanggal_jadi = NULL; // fallback kalau jenis tidak dikenali
        }

        // Insert ke tabel pengujian
        $sql_insert = "INSERT INTO pengujian (id_pengujian, nama_pasien, usia, alamat, nomor_pemeriksaan, tanggal_terima, tanggal_jadi, status_pengujian) 
                      VALUES ('$id_pengujian', '$nama_pasien', '$usia', '$alamat', '$nomor_pemeriksaan', '$tanggal_terima', '$tanggal_jadi', '$status_pengujian')";

        if ($connect->query($sql_insert) === TRUE) {
            // Update status pengajuan
            $sql_update = "UPDATE pengajuan SET status_pengajuan = 'Verifikasi' WHERE id_pengajuan = '$id_pengajuan'";
            if ($connect->query($sql_update) === TRUE) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
                exit();
            } else {
                echo "Gagal update status pengajuan.";
            }
        } else {
            echo "Gagal insert ke pengujian: " . $connect->error;
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
    <title>Penerimaan</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- ======= Styles ====== -->
    <style>
        /* =========== Google Fonts ============ */
@import url("https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap");


/* =============== Globals ============== */
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

/* =============== Navigation ================ */
.navigation {
  position: fixed;
  width: 230px;
  height: 100%;
  background: #67C3C0;
  border-left: 10px solid #67C3C0;
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
  font: Poppins;
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
  font: Poppins;
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
  width: calc(100% - 250px);
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
  padding: 0 10px;
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
}

.header-right {
    display: flex;
    align-items: center;
    gap: 24px;
  }
  .header-right .material-icons {
    font-size: 28px;
    color: var(--text-dark);
    cursor: pointer;
  }
  .profile {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: var(--font-semibold);
    color: var(--text-dark);
  }
  .profile img {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 1.5px solid var(--primary-teal-bg);
  }




/* ======================= Cards ====================== */
.cardBox {
  position: relative;
  width: 100%;
  padding: 20px;
  display: grid;
  grid-template-columns: repeat(3, 0.7fr);
  grid-gap: 30px;
}

.cardBox .card {
  position: relative;
  background: #147472;
  padding: 30px;
  border-radius: 20px;
  display: flex;
  justify-content: space-between;
  cursor: pointer;
  box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
}



.cardBox .card .cardName {
  color: white;
  font-size: 1.8rem;
}

.cardBox .card .numbertext {
  font-size: 3.5rem;
  color: white;
}

.cardBox .card:hover {
  background: var(--green3);
}

.cardBox .card:hover .cardName,
.cardBox .card:hover .numbertext {
  color:var(--black1);
}

/* ================== Order Details List ============== */
.details {
  position: relative;
  width: 100%;
  padding: 20px;
  display: grid;
  grid-template-columns: 2fr 0fr;
  grid-gap: 30px;
  /* margin-top: 10px; */
}

.details .Approval {
  position: relative;
  display: grid;
  min-height: 500px;
  background: var(--white);
  padding: 20px;
  box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
  border-radius: 20px;
}

.details .cardHeader {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}
.cardHeader h2 {
  font-weight: 600;
  color: var(--green);
}
.cardHeader .btn {
  position: relative;
  padding: 5px 10px;
  background: var(--green);
  text-decoration: none;
  color: var(--white);
  border-radius: 6px;
}

.details table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}
.details table thead td {
  font-weight: 600;
}
.details .Approval table tr {
  color: var(--black1);
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.details .Approval table tr:last-child {
  border-bottom: none;
}
.details .Approval table tbody tr:hover {
  background: var(--green3);
  color: var(--black1);
}
.details .Approval table tr td {
  padding: 10px;
  vertical-align: top;
}


.details .Approval table tr td:last-child {
  text-align: end;
}
.details .Approval table tr td:nth-child(2) {
  text-align: end;
}
.details .Approval table tr td:nth-child(3) {
  text-align: center;
}
.status.delivered {
  padding: 2px 4px;
  background: #8de02c;
  color: var(--white);
  border-radius: 4px;
  font-size: 14px;
  font-weight: 500;
}
.status.pending {
  padding: 2px 4px;
  background: #e9b10a;
  color: var(--white);
  border-radius: 4px;
  font-size: 14px;
  font-weight: 500;
}
.status.return {
  padding: 2px 4px;
  background: #f00;
  color: var(--white);
  border-radius: 4px;
  font-size: 14px;
  font-weight: 500;
}
.status.inProgress {
  padding: 2px 4px;
  background: #1795ce;
  color: var(--white);
  border-radius: 4px;
  font-size: 14px;
  font-weight: 500;
}

.recentCustomers {
  position: relative;
  display: grid;
  min-height: 500px;
  padding: 20px;
  background: var(--white);
  box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
  border-radius: 20px;
}
.recentCustomers .imgBx {
  position: relative;
  width: 40px;
  height: 40px;
  border-radius: 50px;
  overflow: hidden;
}
.recentCustomers .imgBx img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.recentCustomers table tr td {
  padding: 12px 10px;
}
.recentCustomers table tr td h4 {
  font-size: 16px;
  font-weight: 500;
  line-height: 1.2rem;
}
.recentCustomers table tr td h4 span {
  font-size: 14px;
  color: var(--black2);
}
.recentCustomers table tr:hover {
  background: var(--green);
  color: var(--white);
}
.recentCustomers table tr:hover td h4 span {
  color: var(--white);
}

/* Tombol icon utama */
.icon-btn {
  background-color: #fff;
  border: 1.5px solid #ccc;
  border-radius: 6px;
  padding: 4px 6px;
  margin: 0 2px;
  cursor: pointer;
  transition: background-color 0.2s ease, transform 0.2s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

/* Hover effect */
.icon-btn:hover {
  background-color: #f5f5f5;
  transform: scale(1.05);
}

/* Ikon */
.icon-btn .material-icons {
  font-size: 20px;
}

/* Warna khusus per aksi */
.icon-btn.accept .material-icons {
  color: #28a745; /* Hijau */
}
.icon-btn.reject .material-icons {
  color: red; /* Biru */
}
.icon-btn.view .material-icons {
  color: #444; /* Abu kehitaman */
  text-decoration: none;
}
.icon-btn,
.icon-btn span {
  text-decoration: none;
}
.icon-btn.download .material-icons {
  color: #777; /* Abu medium */
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
  .cardBox {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .details {
    grid-template-columns: 1fr;
  }
  .Approval {
    overflow-x: auto;
  }
  .status.inProgress {
    white-space: nowrap;
  }
}

@media (max-width: 480px) {
  .cardBox {
    grid-template-columns: repeat(1, 1fr);
  }
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
}
.swal2-popup.swal-custom {
  border-radius: 12px;
  padding: 2rem;
}
.swal2-title {
  font-size: 1.1rem;
  color: #67C3C0;
}
.swal2-icon.swal2-success {
  border-color: #67C3C0;
  color: #67C3C0;
}

    </style>
</head>
<?php
if (isset($_GET['success'])) {
    echo "<script>document.addEventListener('DOMContentLoaded', function() { showSuccess(); });</script>";
}

if (isset($_GET['tolak'])) {
    echo "<script>document.addEventListener('DOMContentLoaded', function() { showReject(); });</script>";
}
?>


<body>
    <!-- =============== Navigation ================ -->
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

                <li>
                    <a href="pengujian.php">
                        <span class="icon">
                            <img src="../assets/sample.png" alt="sample">
                        </span>
                        <span class="title">Pengujian</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="icon">
                            <img src="../assets/setting.png" alt="setting">
                        </span>
                        <span class="title">Pengaturan</span>
                    </a>
                </li>

                <li class = "signout">
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="log-out-outline" style = "color: black"></ion-icon>
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

                <div class="header-right">
                   <span class="material-icons" role="img" aria-label="Notification">notifications</span>
               <div class="profile" tabindex="0" aria-label="User Profile">
                 <img src="https://placehold.co/36x36?text=SP&bg=69a3a3&fg=ffffff&font=roboto" alt="Profile picture of Suci Puji" />
                  <span>Suci Puji</span>
                  </div>
                </div>
            </div>

            <!-- ======================= Cards ================== -->
            <div class="cardBox">
                <div class="card">
                    <div>
                        <div class="cardName">Pengujian Jaringan</div>
                    </div>
                    <div class="numbertext">
                        12
                    </div>
                </div>

                <div class="card">
                    <div>
                        <div class="cardName">Pengujian Sitologi Ginekologi</div>
                    </div>

                    <div class="numbertext">
                        11
                    </div>
                </div>
                <div class="card">
                    <div>
                        <div class="cardName">Pengujian Sitologi Non Ginekologi</div>
                    </div>
                    <div class="numbertext">
                        2
                    </div>
                </div>
            </div>

            <!-- ================ Order Details List ================= -->
              <?php include 'koneksi.php'; ?>
            <div class="details">
                <div class="Approval">
                    <div class="cardHeader">
                        <h2>Persetujuan Penerimaan Sampel</h2>
                        
                    </div>

                    <table class="approval request">
                        <thead>
                            <tr>
                                <td>Sampel Atas Nama</td>
                                <td>Tanggal Pengajuan</td>
                                <td>Jenis Pengajuan Sampel</td>
                                <td>Pengajuan</td>
                                <td>Verifikasi</td>
                            </tr>
                        </thead>

                        <tbody>
                          <?php
              $query = "SELECT id_pengajuan, nama_pasien, tanggal_pengajuan FROM pengajuan WHERE status_pengajuan='menunggu verifikasi'";
              $result = $connect->query($query);

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

                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($row['nama_pasien']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['tanggal_pengajuan']) . "</td>";
                      echo "<td>" . $jenis_pengajuan . "</td>";
                      echo "<td>
                            <a href='$link_detail' class='icon-btn view'>
                              <span class='material-icons'>visibility</span>
                            </a>
                            <a href='download_pengajuan.php?id=" . $row['id_pengajuan'] . "' class='icon-btn download'>
                              <span class='material-icons'>download</span>
                            </a>
                          </td>";

                      echo "<td>
                              <button class='icon-btn reject' onclick='konfirmasiTolak(\"" . $row['id_pengajuan'] . "\")'>
                                <span class='material-icons'>close</span>
                              </button>
                              <form id='formTolak_" . $row['id_pengajuan'] . "' method='POST' style='display:none;'>
                                <input type='hidden' name='tolak_id' value='" . htmlspecialchars($row['id_pengajuan']) . "'>
                              </form>
                              <form method='POST' style='display:inline;'>
                                <input type='hidden' name='setujui_id' value='" . htmlspecialchars($row['id_pengajuan']) . "'>
                                <button type='submit' class='icon-btn accept'><span class='material-icons'>check</span></button>
                              </form>
                            </td>";
                      echo "</tr>";
                  }
              }

                ?>      
                        </tbody>
                    </table>
                </div>

                <!-- ================= New Customers ================ -->
                
                        
                            
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

function showSuccess() {
    Swal.fire({
      icon: 'success',
      title: 'Berhasil menyetujui pengajuan',
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


    </script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
