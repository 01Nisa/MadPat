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
      width: 1200px;
      height: 780px;
      background-color: var(--green6);
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .close-button {
      position: absolute;
      top: 15px;
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
      height: 710px;
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
      font-size: 1.2em;
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
      height: 710px;
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
      font-size: 1.5em;
    }

    .form-section-title {
      font-size: 20px;
      font-weight: bold;
      color: var(--green1);
      margin-top: 16px;
      margin-bottom: 1px;
    }

     .form-section-subTitle {
      font-size: 16px;
      font-weight: bold;
      color: var(--black1);
      margin-top: 5px;
      margin-bottom: 1px;
    }

    .form-note-red {
      color: red;
      font-weight: bold;
      margin-top: 16px;
      margin-bottom: 2px;
      font-size: 0.9em;
    }

    .form-note-green {
      color: var(--green1);
      font-size: 0.9em;
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

    .checkbox-group {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 6px;
    }

    .checkbox-group label {
      margin: 0;
      display: inline-flex;
      align-items: center;
      font-size: 0.9em;
      color: #333;
      font-weight: 500;
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
      font-size: 0.9em;
    }

    input[type="checkbox"] {
      margin-right: 5px;
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
      font-size: 0.9em;
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

      .checkbox-group {
        flex-direction: column;
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
    <h2>Formulir Pengujian Sitologi Non Ginekologi <span id="currentStepDisplay"></span></h2>
    <form id="formPengajuan" action="../process/proSitologiNonGinekologi.php" method="post">
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
          <div class="checkbox-group">
            <input type="radio" name="jk_${index}" value="perempuan" required /> <label>Perempuan</label>
            <input type="radio" name="jk_${index}" value="laki-laki" /> <label>Laki-laki</label>
          </div>
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

      <div class="form-section-title">Keterangan Sampel</div>
      <div class="form-group">
        <label for="bahan_${index}">Bahan tersedia</label>
        <div class="checkbox-group">
          <input type="checkbox" id="sputum_${index}" name="bahan_${index}[]" value="sputum" />
          <label for="sputum_${index}">Sputum</label>
          <input type="checkbox" id="ascites_${index}" name="bahan_${index}[]" value="ascites" />
          <label for="ascites_${index}">Ascites</label>
          <input type="checkbox" id="pleuralEffusion_${index}" name="bahan_${index}[]" value="Pleural effusion" />
          <label for="pleuralEffusion_${index}">Pleural effusion</label>
          <input type="checkbox" id="voidedUrine_${index}" name="bahan_${index}[]" value="Voided urine" />
          <label for="voidedUrine_${index}">Voided urine</label>
          <input type="checkbox" id="urineCatheter_${index}" name="bahan_${index}[]" value="Urine catheter" />
          <label for="urineCatheter_${index}">Urine catheter</label>
          <input type="checkbox" id="liquorCs_${index}" name="bahan_${index}[]" value="Liquor cs." />
          <label for="liquorCs_${index}">Liquor cs.</label>
          <input type="checkbox" id="bronchialWashing${index}" name="bahan_${index}[]" value="Bronchial washing" />
          <label for="bronchialWashing_${index}">Bronchial washing</label>
          <input type="checkbox" id="gastricLavage_${index}" name="bahan_${index}[]" value="Gastric lavage" />
          <label for="gastricLavage_${index}">Gastric lavage</label>
          <input type="checkbox" id="nasopharynxSwab_${index}" name="bahan_${index}[]" value="Nasopharynx swab" />
          <label for="nasopharynxSwab_${index}">Nasopharynx swab</label>
          <input type="checkbox" id="rectalSwab_${index}" name="bahan_${index}[]" value="Rectal swab" />
          <label for="rectalSwab_${index}">Rectal swab</label>
          <input type="checkbox" id="prostateMassage_${index}" name="bahan_${index}[]" value="Prostate massage" />
          <label for="prostateMassage_${index}">Prostate massage</label>
          <input type="checkbox" id="nippleDischarge_${index}" name="bahan_${index}[]" value="Nipple discharge" />
          <label for="nippleDischarge_${index}">Nipple discharge</label>
          <input type="checkbox" id="lainnya_${index}" name="bahan_${index}[]" value="lainnya" />
          <label for="lainnya_${index}">Lainnya</label>
        </div>
      </div>

      <div class="inline-group">
        <div class="form-group">
          <label for="jumlahSampel_${index}">Jumlah sampel dikirim</label>
          <input type="number" id="jumlahSampel_${index}" name="jumlahSampel_${index}" required />
        </div>
        <div class="form-group">
          <label for="jenis_${index}">Jenis Preparat</label>
          <div class="checkbox-group">
            <input type="radio" name="jenis_${index}" value="basah" required /> <label>Basah</label>
            <input type="radio" name="jenis_${index}" value="kering" /> <label>Kering</label>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="perendaman_${index}">Fiksasi</label>
        <div class="form-note-green">Ketentuan fiksasi:</div>
        <div class="form-note-green">1. Preparat apus, dipakai alcohol 95% atau alcohol arther aa rata-rata selama 2 jam</div>
         <div class="form-note-green">2. Sputum, dipakai alcohol 70% secukupnya</div>
        <div class="form-note-green">3. Cairan, dipakai alcohol 50% dengan cairannya</div>
        <div class="checkbox-group">
          <input type="checkbox" id="alkohol95%_${index}" name="perendaman_${index}[]" value="Alkohol 95%" />
          <label for="alkohol95%_${index}">Alkohol 95%</label>
          <input type="checkbox" id="alkoholAetherAA_${index}" name="perendaman_${index}[]" value="Alkohol aether aa" />
          <label for="alkoholAetherAA_${index}">Alkohol aether aa</label>
          <input type="checkbox" id="lainnya_${index}" name="perendaman_${index}[]" value="lainnya" />
          <label for="lainnya_${index}">Lainnya</label>
        </div>
      </div>

      <div class="inline-group">
        <div class="form-group">
          <label for="sitologi_${index}">Pemeriksaan Sitologi</label>
          <div class="checkbox-group">
            <input type="radio" name="sitologi_${index}" value="baru" required /> <label>Baru</label>
            <input type="radio" name="sitologi_${index}" value="ulangan" /> <label>Ulangan</label>
          </div>
        </div>
        <div class="form-group">
          <label for="noPemeriksa_${index}">Nomor Pemeriksaan</label>
          <input type="text" id="noPemeriksa_${index}" name="noPemeriksa_${index}" />
        </div>
      </div>

      <div class="form-section-title">Keterangan Klinik</div>
      <div class="form-section-subTitle">Anamnese</div>
      <div class="inline-group">
        <div class="form-group">
          <label for="jumlahRokok_${index}">Jumlah rokok per hari</label>
          <input type="number" id="jumlahRokok_${index}" name="jumlahRokok_${index}"/>
        </div>
        <div class="form-group">
          <label for="lain_${index}">Lain-lain</label>
          <input type="text" id="lain_${index}" name="lain_${index}"/>
        </div>
      </div>

      <div class="form-section-subTitle">Status lokalis</div>
      <div class="inline-group">
        <div class="form-group">
          <label for="tumor_${index}">Tumor</label>
          <input type="text" id="tumor_${index}" name="tumor_${index}"/>
        </div>
        <div class="form-group">
          <label for="kelenjarRegional_${index}">Kelenjar regional</label>
          <input type="text" id="kelenjarRegional_${index}" name="kelenjarRegional_${index}"/>
        </div>
      </div>

      <div class="form-section-subTitle">Status lokalis</div>
      <div class="inline-group">
        <div class="form-group">
          <label for="jenisLesi_${index}">Jenis Lesi</label>
          <div class="checkbox-group">
            <input type="radio" name="jenisLesi_${index}" value="primer" required /> <label>Primer</label>
            <input type="radio" name="jenisLesi_${index}" value="sekunder" /> <label>Sekunder</label>
          </div>
        </div>
        <div class="form-group">
          <label for="asalLesi_${index}">Asal Lesi</label>
          <input type="text" id="asalLesi_${index}" name="asalLesi_${index}"/>
        </div>
      </div>

      <div class="form-group">
        <label for="metastasis_${index}">Metastasis</label>
        <input type="text" id="metastasis_${index}" name="metastasis_${index}"/>
      </div>

      <div class="form-group">
        <label for="roFoto_${index}">Ro foto</label>
        <input type="text" id="roFoto_${index}" name="roFoto_${index}"/>
      </div>

      <div class="inline-group">
        <div class="form-group">
          <label for="tindakanPemeriksaan_${index}">Tindakan Pemeriksaan</label>
          <div class="checkbox-group">
            <input type="radio" name="tindakanPemeriksaan_${index}" value="operasi" required /> <label>Operasi</label>
            <input type="radio" name="tindakanPemeriksaan_${index}" value="biopsi" /> <label>Biopsi</label>
          </div>
        </div>

        <div class="form-group">
          <label for="statusTindakan_${index}">Status Tindakan Pemeriksaan</label>
          <div class="checkbox-group">
            <input type="radio" name="statusTindakan_${index}" value="belum" required /> <label>Belum</label>
            <input type="radio" name="statusTindakan_${index}" value="pernah" /> <label>Pernah</label>
          </div>
        </div>
      </div>
      
      <div class="form-group">
        <label for="diagKlinik_${index}">Diagnosis Klinik</label>
        <textarea name="diagKlinik_${index}" id="diagKlinik_${index}"></textarea>
      </div>

      <div class="form-group">
        <label for="keterangan_${index}">Keterangan penyakit pasien</label>
        <textarea name="keterangan_${index}" id="keterangan_${index}"></textarea>
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