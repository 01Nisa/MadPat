<?php
include 'koneksi.php';

// 1. Semua pengajuan
$sql1 = "SELECT COUNT(*) as total FROM pengajuan";
$result1 = $koneksi->query($sql1);
$jumlahPengajuan = ($result1 && $row1 = $result1->fetch_assoc()) ? $row1['total'] : 0;

// 2. Pengujian Diproses
$sql2 = "SELECT COUNT(*) as total FROM pengujian WHERE status_pengujian = 'Diproses'";
$result2 = $koneksi->query($sql2);
$jumlahDiproses = ($result2 && $row2 = $result2->fetch_assoc()) ? $row2['total'] : 0;

// 3. Pengujian Selesai
$sql3 = "SELECT COUNT(*) as total FROM pengujian WHERE status_pengujian = 'Selesai'";
$result3 = $koneksi->query($sql3);
$jumlahSelesai = ($result3 && $row3 = $result3->fetch_assoc()) ? $row3['total'] : 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>

    <!-- Google Material Icons -->
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

    <!-- Chart.js CDN -->
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
      <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>


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
        --green:#009688;
        --white: #fff;
        --gray: #f5f5f5;
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

      /* welcome text */
      .welcome{
      margin-left: 50px;
      margin-top: 80px;
      }
      .welcome h1{
        font-size: 32px;
        margin-bottom: 10px;
        color: black;
        
      }
      .welcome span {
          font-size: 32px;
          color: black;
          font-weight: 600;
          margin-top: 15px;
          font-style: italic;
      }
      /* ===================== Main ===================== */
      .main {
        position: absolute;
        width: calc(100% - 150px);
        left: 250px;
        right: 430px;
        min-height: 100vh;
        background: var(--white);
        transition: 0.5s;
        grid-template-columns: max-content;
        
      }
      .main.active {
        width: calc(100% - 150px);
        left: 350px;
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
          position: sticky;

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
        grid-template-columns: repeat(2, 0.3fr);
        grid-gap: 30px;
        margin-left: 20px;
        margin-right: 50px;
      }

      .cardBox .card {
        position: relative;
        background: #147472;
        padding: 30px;
        border-radius: 20px;
        /* display: flex; */
        justify-content: space-between;
        cursor: pointer;
        box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
      }

      .cardBox .card .numbers {
        color: white;
        font-size: 2rem;
        margin-top: 5px;
        
      }

      .cardBox .card .cardName {
        position: relative;
        font-weight: 500;
        font-size: 1.5rem;
        color: white;
      }

      .cardBox .card .iconBx {
        font-size: 2.5rem;
        color: var(--white);
      }

      .cardBox .card:hover {
        background: var(--green);
      }
      .cardBox .card:hover .numbers,
      .cardBox .card:hover .cardName,
      .cardBox .card:hover .iconBx {
        color: var(--white);
      }

      /* Main content */
      .details {
        position: relative;
        width: 100%;
        padding: 20px;
        display: grid;
        grid-template-columns: 2fr 1fr;
        grid-gap: 30px;
      }

      .details .pengajuan {
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
        color: black;
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
      .details .pengajuan table tr {
        color: var(--black1);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
      }
      .details .pengajuan table tr:last-child {
        border-bottom: none;
      }
      .details .pengajuan table tbody tr:hover {
        background: var(--green);
        color: var(--white);
      }
      .details .pengajuan table tr td {
        padding: 10px;
      }
      .details .pengajuan table tr td:last-child {
        text-align: end;
      }
      .details .pengajuan table tr td:nth-child(2) {
        text-align: end;
      }
      .details .pengajuan table tr td:nth-child(3) {
        text-align: center;
      }
      .status.menunggu {
        padding: 2px 4px;
        background: #FFB67E;
        color: var(--white);
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
      }

      .status.terverifikasi {
        padding: 2px 4px;
        background: #8DDAB3;
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

      .sidebar {
        position: fixed;
        top: 50px;        
        right: 20px;      
        width: 250px;
        height: calc(100vh - 40px); 
        background: var(--white);
        border-left: 2px solid var(--white);
        transition: 0.5s;
        overflow-y: auto;
        z-index: 1000;
        border-radius: 8px; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
      }



      /* approval */
      .penerimaan {
        border-radius: 10px;
        background-color: var(--gray);
        padding: 0px;
        width: 100%;
        overflow: hidden;
      }
      table.approval-request {
          width: 100%;
          border-collapse: separate;
          border-spacing: 0 16px;
          font-size: 0.9rem;  
        }
        table.approval-request thead th {
          padding-bottom: 12px;
          font-weight: var(--font-bold);
          color: var(--text-dark);
          text-align: left;
          white-space: nowrap;
        }
        table.approval-request tbody tr {
          background-color: var(--surface-bg);
          border-radius: var(--surface-radius-sm);
          box-shadow: none;
          vertical-align: middle;
        }
        table.approval-request tbody tr td {
          padding: 12px 8px;
          vertical-align: middle;
        }
        table.approval-request tbody tr td.name-col {
          font-weight: var(--font-semibold);
          color: var(--text-dark);
        }
        table.approval-request tbody tr td.type-col {
          font-size: 0.75rem;
          color: var(--text-muted);
          white-space: nowrap;
        }
        table.approval-request tbody tr td.date-col {
          white-space: nowrap;
          color: var(--text-muted);
          font-size: 0.9rem;
        }
        .approval-icons {
          display: flex;
          gap: 8px;
          justify-content: flex-end;
        }
        .icon-btn {
          width: 30px;
          height: 30px;
          border-radius: 6px;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
        }
        .icon-btn.reject {
          background-color: #ef9a9a;
          color: white;
        }
        .icon-btn.accept {
          background-color: #80cbc4;
          color: white;
        }
        .icon-btn.view {
          background-color: #90caf9;
          color: white;
        }
        .icon-btn .material-icons {
          font-size: 18px;
        }

        /* chart */
        .chart-container {
          background-color: var(--surface-bg);
          border-radius: var(--surface-radius);
          padding: var(--gap-base);
          width: 100%;
          min-height: 320px;
          position: relative;
        }
        .chart-header {
          display: flex;
          justify-content: space-between;
          font-weight: var(--font-bold);
          font-size: 1.15rem;
          margin-bottom: 20px;
          color: var(--text-dark);
        }
        .chart-header .chart-extra {
          display: flex;
          gap: 4px;
          font-weight: 400;
          color: var(--text-muted);
          font-size: 0.85rem;
        }
        .chart-legend {
          display: flex;
          gap: 24px;
          flex-wrap: wrap;
          margin-top: 16px;
          font-size: 0.9rem;
          cursor: default;
        }
        .legend-item {
          display: flex;
          align-items: center;
          gap: 8px;
          color: var(--text-dark);
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
        .pengajuan {
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
                            <img src="assets/microscope.png" alt="logo">
                        </span>
                        <span class="title-logo">MedPath</span>
                    </a>
                </li>

                <li>
                    <a href="beranda.php">
                        <span class="icon">
                            <img src="assets/dashboard.png" alt="dashboard">
                        </span>
                        <span class="title">Beranda</span>
                    </a>
                </li>

                <li>
                    <a href="pengujian.php">
                        <span class="icon">
                            <img src="assets/sample.png" alt="sample">
                        </span>
                        <span class="title">Pengujian</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <img src="assets/setting.png" alt="setting">
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
         <div class="main-content">
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

              <!-- welcome text -->
            <div class="welcome">
              <h1>Selamat Datang <span>Suci Puji</span></h1>
            </div>


            <!-- ======================= Cards ================== -->
            <div class="cardBox">
                <div class="card">
                  <div class="iconBx">
                  <ion-icon name="document-text-outline"></ion-icon>
                </div>
                    <div>
                        <div class="cardName">Sampel Diajukan</div>
                        <div class="numbers"><?php echo $jumlahPengajuan; ?></div>
                    </div>
                </div>

                <div class="card">
                 <div class="iconBx">
                  <ion-icon name="sync-outline"></ion-icon>
                  </div>
                    <div>
                      <div class="cardName">Sampel Diproses</div>
                      <div class="numbers"><?php echo $jumlahDiproses; ?></div>
                    </div>
                </div>

                <div class="card">
                  <div class="iconBx">
                  <ion-icon name="checkmark-done-outline"></ion-icon>
                  </div>
                    <div>
                      <div class="cardName">Pengujian Selesai</div>
                       <div class="numbers"><?php echo $jumlahSelesai; ?></div>
                    </div>
                </div>

                <div class="card">
                  <div class="iconBx">
                      <ion-icon name="cash-outline"></ion-icon>
                  </div>
                    <div>
                      <div class="cardName">Pembayaran Selesai</div>
                      <div class="numbers">$7,842</div>
                    </div>

                </div>
            </div>

            <!-- ================ Order Details List ================= -->
            
            <?php include 'koneksi.php'; ?>
<div class="details">
    <div class="pengajuan">
        <div class="cardHeader">
            <h2>Pengujian Sampel</h2>
            <a href="#" class="btn">View All</a>
        </div>

        <table>
            <thead>
                <tr>
                    <td>Sampel Atas Nama</td>
                    <td>Tanggal Terima</td>
                    <td>Jenis</td>
                    <td>Status</td>
                    <td>Tanggal Jadi</td>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT nama_pasien, tanggal_pengajuan, jenis_pengajuan, status_pengajuan  FROM pengajuan";
                $result = $koneksi->query($query);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $statusClass = strtolower($row['status_pengajuan']); // contoh: "Selesai" → selesai
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nama_pasien']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tanggal_pengajuan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jenis_pengajuan']) . "</td>";
                        echo "<td><span class='status $statusClass'>" . htmlspecialchars($row['status_pengajuan']) . "</span></td>";
                        echo "<td>" . htmlspecialchars($row['tanggal_pengajuan']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Tidak ada data.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
                   


                <!-- ================= New Customers ================ -->
                
                        
       <div class="sidebar">
  
       <div class="penerimaan">
        <h3>Persetujuan Penerimaan Pengujian Sampel</h3>
        <table class="approval-request" role="table" aria-describedby="permintaan-persetujuan-desc">
          <thead>
            <tr>
              <th scope="col">Nama</th>
              <th scope="col">Tanggal</th>
              <th scope="col" style="text-align:right;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr tabindex="0">
              <td>
                <div class="name-col">Bagas Andikara</div>
                <div class="type-col">Jaringan</div>
              </td>
              <td class="date-col">2025/03/19</td>
              <td class="approval-icons">
                <button class="icon-btn reject" aria-label="Reject Bagas Andikara Sample Test"><span class="material-icons">close</span></button>
                <button class="icon-btn accept" aria-label="Accept Bagas Andikara Sample Test"><span class="material-icons">check</span></button>
                <button class="icon-btn view" aria-label="View Bagas Andikara Sample Test"><span class="material-icons">visibility</span></button>
              </td>
            </tr>
            <tr tabindex="0">
              <td>
                <div class="name-col">Bagas Andikara</div>
                <div class="type-col">Jaringan</div>
              </td>
              <td class="date-col">2025/03/19</td>
              <td class="approval-icons">
                <button class="icon-btn reject" aria-label="Reject Bagas Andikara Sample Test"><span class="material-icons">close</span></button>
                <button class="icon-btn accept" aria-label="Accept Bagas Andikara Sample Test"><span class="material-icons">check</span></button>
                <button class="icon-btn view" aria-label="View Bagas Andikara Sample Test"><span class="material-icons">visibility</span></button>
              </td>
            </tr>
            <tr tabindex="0">
              <td>
                <div class="name-col">Bagas Andikara</div>
                <div class="type-col">Jaringan</div>
              </td>
              <td class="date-col">2025/03/19</td>
              <td class="approval-icons">
                <button class="icon-btn reject" aria-label="Reject Bagas Andikara Sample Test"><span class="material-icons">close</span></button>
                <button class="icon-btn accept" aria-label="Accept Bagas Andikara Sample Test"><span class="material-icons">check</span></button>
                <button class="icon-btn view" aria-label="View Bagas Andikara Sample Test"><span class="material-icons">visibility</span></button>
              </td>
            </tr>
            
              </tbody>
              </table>
              
            </div> 
            <br>
            <br>
            <div class="statistik">
    <div class="chart-header">
          <div><strong>Statistik Jenis<br />Pengujian Sampel</strong></div>
          <div class="chart-extra" aria-label="Statistics Date Range">
            <span>Hari ini</span>
            <span class="material-icons" aria-hidden="true" style="font-size: 18px;">filter_list</span>
          </div>
        </div>
        <canvas id="donutChart" role="img" aria-label="Donut chart statistik jenis pengujian sampel" width="320" height="320"></canvas>
        <div class="chart-legend" aria-hidden="true">
          <div class="legend-item"><span class="legend-color legend-jaringan"></span>Jaringan (5)</div>
          <div class="legend-item"><span class="legend-color legend-sitologi-ginekologi"></span>Sitologi Ginekologi (2)</div>
          <div class="legend-item"><span class="legend-color legend-sitologi-non-ginekologi"></span>Sitologi Non Ginekologi (3)</div>
        </div>
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

document.querySelectorAll('.has-submenu > a').forEach(menu => {
  menu.addEventListener('click', function (e) {
    e.preventDefault();
    this.parentElement.classList.toggle('active');
  });
});

const ctx = document.getElementById('donutChart').getContext('2d');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Jaringan', 'Sitologi Ginekologi', 'Sitologi Non Ginekologi'],
      datasets: [{
        data: [5, 2, 3],
        backgroundColor: [
          '#4CAF50', // Jaringan
          '#FFC107', // Sitologi Ginekologi
          '#03A9F4'  // Sitologi Non Ginekologi
        ]
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: false // karena kamu pakai custom legend
        }
      }
    }
  });

    </script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
