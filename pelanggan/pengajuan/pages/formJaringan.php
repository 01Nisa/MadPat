<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("location:../../../login.php?pesan=belum_login");
    exit();
}

$user = $_SESSION['user'];
$jumlah = isset($_GET['jumlah']) ? intval($_GET['jumlah']) : 1;
$halaman = isset($_GET['halaman']) ? intval($_GET['halaman']) : 1;
$max = max(1, $jumlah);
$halaman = max(1, min($halaman, $max));
$currentStep = $halaman - 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Formulir Pengajuan Sampel</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #f4f7fa;
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    :root {
      --green1: rgba(20, 116, 114, 1);
      --green2: rgba(3, 178, 176, 1);
      --green3: rgba(186, 231, 228, 1);
      --green4: rgba(12, 109, 108, 0.61);
      --green5: rgba(3, 178, 176, 0.29);
      --green6: rgba(240, 243, 243, 1);
    }

    .container {
      position: relative;
      display: flex;
      flex-direction: row;
      gap: 2px;
      width: 1100px;
      height: 680px;
      background-color: var(--green6);
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .close-button {
      position: absolute;
      top: 3px;
      right: 20px;
      font-size: 24px;
      font-weight: bold;
      color: var(--green1);
      cursor: pointer;
      transition: color 0.3s;
    }

    .close-button:hover {
      color: var(--green5);
    }

    .progress-section {
      width: 222px;
      height: 610px;
      overflow-y: auto;
      padding-right: 20px;
      margin-top: 15px;
      margin-bottom: 15px;
      background-color: var(--green5);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
    }

    .progress-section h3 {
      margin-top: 30px;
      margin-bottom: 20px;
      color: black;
      text-align: center;
    }

    .step {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 15px;
      width: 100%;
    }

    .step-header {
      display: flex;
      flex-direction: row;
      align-items: center;
      width: 100%;
      padding-left: 20px;
    }

    .step-circle {
      width: 24px;
      height: 24px;
      background-color: #b0bec5;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 0.9em;
      margin-right: 10px;
      transition: background-color 0.3s;
    }

    .step-circle.active,
    .step-circle.completed {
      background-color: #00695c;
    }

    .step-circle.completed::before {
      content: "✓";
      font-size: 1em;
    }

    .step-circle.active::before,
    .step-circle:not(.completed)::before {
      content: attr(data-step);
    }

    .step-line {
      width: 2px;
      height: 40px;
      background-color: #b0bec5;
      margin: 10px 0 0 -65px;
      transition: background-color 0.3s;
    }

    .step-line.active {
      background-color: #00695c;
    }

    .step span {
      color: #004d40;
      font-size: 0.9em;
      text-align: left;
      flex: 1;
    }

    .form-container {
      width: 900px;
      height: 610px;
      overflow-y: auto;
      background-color: #fff;
      margin-top: 15px;
      margin-bottom: 15px;
      padding-right: 12px;
      padding-left: 30px;
      position: relative;
    }

    .form-container h2 {
      text-align: left;
      color: var(--green2);
      margin-bottom: 20px;
    }

    .form-section-title {
      font-size: 20px;
      font-weight: bold;
      color: var(--green1);
      margin-top: 16px;
      margin-bottom: 1px;
    }

    .form-note-red {
      color: red;
      font-weight: bold;
      margin-top: 16px;
      margin-bottom: 2px;
    }

    .form-note-green {
      color: var(--green1);
      font-size: 15px;
      margin-top: 4px;
    }

    .form-group {
      margin-bottom: 12px;
    }

    label {
      display: block;
      margin-right: 30px;
      margin-bottom: 4px;
      color: #333;
      font-weight: 500;
      font-size: 16px;
    }

    input[type="text"],
    input[type="email"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
      width: 100%;
      padding: 10px;
      border: 2px solid var(--green1);
      border-radius: 8px;
      background-color: white;
      transition: border-color 0.3s, background-color 0.3s;
    }

    input:hover,
    select:hover,
    textarea:hover,
    input:focus,
    select:focus,
    textarea:focus {
      border-color: var(--green5);
      background-color: #fff;
      outline: none;
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    .button-group {
      display: flex;
      justify-content: flex-end;
      margin-top: 40px;
      margin-bottom: 50px;
    }

    .btn {
      background-color: var(--green1);
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      font-weight: bold;
      transition: background-color 0.3s;
      min-width: 120px;
      margin-left: 10px;
    }

    .btn:hover {
      background-color: var(--green5);
    }

    .inline-group {
      display: flex;
      gap: 20px;
    }

    .inline-group .form-group {
      flex: 1;
    }

    @media (max-width: 900px) {
      .container {
        flex-direction: column;
        height: auto;
      }

      .progress-section {
        border-bottom: 1px solid #ddd;
        margin-bottom: 20px;
        height: auto;
      }

      .inline-group {
        flex-direction: column;
      }

      .button-group {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
      }

      .step-header {
        padding-left: 10px;
      }

      .button-group {
        justify-content: center;
      }

      .btn {
        margin-left: 0;
        width: 100%;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <div class="close-button" onclick="window.location='pengajuan.php'">×</div>
  <div class="progress-section">
    <h3>Lembar Pengajuan</h3>
    <div id="stepsContainer"></div>
  </div>

  <div class="form-container">
    <h2>Formulir Pengujian Jaringan <span id="currentStepDisplay"></span></h2>
    <form id="formPengajuan" action="../process/proJaringan.php" method="post">
      <input type="hidden" name="jumlah" value="<?= $jumlah ?>">
      <input type="hidden" name="halaman" id="halaman" value="<?= $halaman ?>">
      <div id="formPagesContainer"></div>
      <div class="button-group" id="navigationButtons"></div>
    </form>
  </div>
</div>

<noscript>
  <p style="color: red; text-align: center;">JavaScript diperlukan untuk mengisi formulir ini. Aktifkan JavaScript di browser Anda.</p>
</noscript>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const stepsContainer = document.getElementById('stepsContainer');
    const formPagesContainer = document.getElementById('formPagesContainer');
    const navigationButtons = document.getElementById('navigationButtons');
    const form = document.getElementById('formPengajuan');
    const currentStepDisplay = document.getElementById('currentStepDisplay');
    const halamanInput = document.getElementById('halaman');
    const totalSteps = <?= $jumlah ?>;
    let currentStep = <?= $currentStep ?>;

    const validateForm = () => {
      const currentPage = document.querySelector(`.form-page[data-index="${currentStep}"]`);
      let isValid = true;
      let missingFields = [];

      const requiredFields = currentPage.querySelectorAll('input[required], textarea[required], select[required]');
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          isValid = false;
          missingFields.push(field.name);
          field.style.borderColor = 'red';
        } else {
          field.style.borderColor = 'var(--green1)';
        }
      });

      const radioNames = ['jk_' + currentStep, 'patologi_' + currentStep];
      radioNames.forEach(name => {
        const checked = currentPage.querySelector(`input[name="${name}"]:checked`);
        if (!checked) {
          isValid = false;
          missingFields.push(name);
          const radioInputs = currentPage.querySelectorAll(`input[name="${name}"]`);
          radioInputs.forEach(r => r.parentElement.style.color = 'red');
        } else {
          const radioInputs = currentPage.querySelectorAll(`input[name="${name}"]`);
          radioInputs.forEach(r => r.parentElement.style.color = '#333');
        }
      });

      if (!isValid) {
        console.log('Missing required fields:', missingFields);
        alert('Mohon isi semua kolom yang wajib diisi sebelum melanjutkan: ' + missingFields.join(', '));
      }
      return isValid;
    };

    const createFormPage = (index) => {
      const page = document.createElement('div');
      page.classList.add('form-page');
      page.dataset.index = index;
      page.style.display = index === currentStep ? 'block' : 'none';

      page.innerHTML = `
      <div class="form-section-title">Data Dokter</div>
      <div class="form-group">
        <label for="namaDokter_${index}">Nama</label>
        <input type="text" id="namaDokter_${index}" name="namaDokter_${index}" required />
      </div>
      <div class="form-group">
        <label for="rs_${index}">Alamat/RS</label>
        <textarea name="rs_${index}" id="rs_${index}" required></textarea>
      </div>

      <div class="form-section-title">Data Pasien</div>
      <div class="inline-group">
        <div class="form-group">
          <label for="namaPasien_${index}">Nama</label>
          <input type="text" id="namaPasien_${index}" name="namaPasien_${index}" required />
        </div>
        <div class="form-group">
          <label for="usia_${index}">Usia</label>
          <input type="number" id="usia_${index}" name="usia_${index}" min="1" required />
        </div>
      </div>

      <div class="inline-group">
        <div class="form-group">
          <label for="jk_${index}">Jenis Kelamin</label>
          <input type="radio" name="jk_${index}" value="perempuan" required /> Perempuan
          <input type="radio" name="jk_${index}" value="laki-laki" /> Laki-laki
        </div>
        <div class="form-group">
          <label for="negara_${index}">Negara</label>
          <input type="text" id="negara_${index}" name="negara_${index}" required />
        </div>
      </div>

      <div class="form-group">
        <label for="alamat_${index}">Alamat</label>
        <textarea name="alamat_${index}" id="alamat_${index}" required></textarea>
      </div>

      <div class="form-section-title">Pemeriksaan Jaringan Tubuh</div>
      <div class="form-group">
        <label for="asal_${index}">Berasal dari</label>
        <input type="text" id="asal_${index}" name="asal_${index}" required />
      </div>
      <div class="form-group">
        <label for="perendaman_${index}">Direndam dalam</label>
        <div class="form-note-green">Umumnya digunakan formalin 10%</div>
        <input type="text" id="perendaman_${index}" name="perendaman_${index}" required />
      </div>
      <div class="form-group">
        <label for="diagKlinik_${index}">Diagnosis Klinik</label>
        <textarea name="diagKlinik_${index}" id="diagKlinik_${index}" required></textarea>
      </div>

      <div class="form-section-title">Penyakit Pasien</div>
      <div class="form-group">
        <label for="keterangan_${index}">Keterangan penyakit pasien</label>
        <div class="form-note-green">Jika mengirimkan kerokan rahim, hendaknya disebutkan tanggal haid terakhir</div>
        <textarea name="keterangan_${index}" id="keterangan_${index}" required></textarea>
      </div>

      <div class="form-group">
        <label for="patologi_${index}">Pemeriksaan Patologi</label>
        <input type="radio" name="patologi_${index}" value="sudah" required /> Sudah
        <input type="radio" name="patologi_${index}" value="belum" /> Belum
      </div>

      <div class="form-note-red">Data di bawah ini diisi ketika sudah dilakukan pemeriksaan patologi</div>
      <div class="form-group">
        <label for="noPemeriksa_${index}">Nomor Pemeriksaan</label>
        <input type="text" id="noPemeriksa_${index}" name="noPemeriksa_${index}" />
      </div>
      <div class="form-group">
        <label for="tglPeriksa_${index}">Tanggal Pemeriksaan</label>
        <input type="date" id="tglPeriksa_${index}" name="tglPeriksa_${index}" />
      </div>
      <div class="form-group">
        <label for="diagPeriksa_${index}">Diagnosis Pemeriksaan</label>
        <textarea name="diagPeriksa_${index}" id="diagPeriksa_${index}"></textarea>
      </div>

      <div class="inline-group">
        <div class="form-group">
          <label for="poliklinik_${index}">Poliklinik</label>
          <input type="text" id="poliklinik_${index}" name="poliklinik_${index}" />
        </div>
        <div class="form-group">
          <label for="klas_${index}">Klas</label>
          <input type="text" id="klas_${index}" name="klas_${index}" />
        </div>
      </div>
      `;
      return page;
    };

    const renderSteps = () => {
      stepsContainer.innerHTML = '';
      for (let i = 0; i < totalSteps; i++) {
        const step = document.createElement('div');
        step.classList.add('step');

        const header = document.createElement('div');
        header.classList.add('step-header');

        const circle = document.createElement('div');
        circle.classList.add('step-circle');
        circle.setAttribute('data-step', i + 1);
        if (i < currentStep) {
          circle.classList.add('completed');
        } else if (i === currentStep) {
          circle.classList.add('active');
        }
        header.appendChild(circle);

        const label = document.createElement('span');
        label.textContent = `Pengajuan ${i + 1}`;
        header.appendChild(label);

        step.appendChild(header);

        if (i < totalSteps - 1) {
          const line = document.createElement('div');
          line.classList.add('step-line');
          if (i < currentStep) {
            line.classList.add('active');
          }
          step.appendChild(line);
        }

        stepsContainer.appendChild(step);
      }
    };

    const updateNavigationButtons = () => {
      navigationButtons.innerHTML = '';
      if (totalSteps === 1) {
        const submitBtn = document.createElement('button');
        submitBtn.type = 'submit';
        submitBtn.className = 'btn';
        submitBtn.textContent = 'Kirim';
        navigationButtons.appendChild(submitBtn);
      } else {
        if (currentStep > 0) {
          const prevBtn = document.createElement('button');
          prevBtn.type = 'button';
          prevBtn.className = 'btn';
          prevBtn.textContent = 'Sebelumnya';
          prevBtn.onclick = () => changeStep(-1);
          navigationButtons.appendChild(prevBtn);
        }

        if (currentStep < totalSteps - 1) {
          const nextBtn = document.createElement('button');
          nextBtn.type = 'button';
          nextBtn.className = 'btn';
          nextBtn.textContent = 'Selanjutnya';
          nextBtn.onclick = () => {
            if (validateForm()) {
              changeStep(1);
            }
          };
          navigationButtons.appendChild(nextBtn);
        } else {
          const submitBtn = document.createElement('button');
          submitBtn.type = 'submit';
          submitBtn.className = 'btn';
          submitBtn.textContent = 'Kirim';
          navigationButtons.appendChild(submitBtn);
        }
      }
    };

    const changeStep = (direction) => {
      const pages = document.querySelectorAll('.form-page');
      pages[currentStep].style.display = 'none';
      currentStep += direction;
      pages[currentStep].style.display = 'block';
      halamanInput.value = currentStep + 1;
      currentStepDisplay.textContent = currentStep + 1;

      document.querySelectorAll('.step-circle').forEach((circle, index) => {
        circle.classList.remove('completed', 'active');
        if (index < currentStep) {
          circle.classList.add('completed');
        } else if (index === currentStep) {
          circle.classList.add('active');
        }
      });

      document.querySelectorAll('.step-line').forEach((line, index) => {
        line.classList.remove('active');
        if (index < currentStep) {
          line.classList.add('active');
        }
      });

      updateNavigationButtons();
    };

    for (let i = 0; i < totalSteps; i++) {
      formPagesContainer.appendChild(createFormPage(i));
    }
    renderSteps();
    currentStepDisplay.textContent = currentStep + 1;
    updateNavigationButtons();
  });
</script>
</body>
</html>