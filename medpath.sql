-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Jun 2025 pada 13.58
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medpath`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pengujian` varchar(30) NOT NULL,
  `tanggal_pembayaran` date NOT NULL,
  `waktu` time NOT NULL,
  `jenis_pembayaran` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan`
--

CREATE TABLE `pengajuan` (
  `id_pengajuan` varchar(30) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `nama_dokter` varchar(100) NOT NULL,
  `alamat_rs` text NOT NULL,
  `nama_pasien` varchar(100) NOT NULL,
  `usia` int(11) NOT NULL,
  `jenis_kelamin` varchar(50) NOT NULL,
  `negara` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `tanggal_pengajuan` date DEFAULT NULL,
  `asal_jaringan` varchar(255) DEFAULT NULL,
  `perendaman` varchar(255) DEFAULT NULL,
  `pemeriksaan_patologi` varchar(50) DEFAULT NULL,
  `nomor_pemeriksaan` varchar(100) DEFAULT NULL,
  `tanggal_pemeriksaan` date DEFAULT NULL,
  `diagnosis_pemeriksaan` text DEFAULT NULL,
  `poliklinik` varchar(100) DEFAULT NULL,
  `klas` varchar(100) DEFAULT NULL,
  `bahan_tersedia` text DEFAULT NULL,
  `diambil_dengan` text DEFAULT NULL,
  `jumlah_sampel` int(11) DEFAULT NULL,
  `jenis_preparat` varchar(50) DEFAULT NULL,
  `fiksasi` text DEFAULT NULL,
  `status_diri` text DEFAULT NULL,
  `jumlah_anak` int(11) DEFAULT NULL,
  `kontrasepsi` text DEFAULT NULL,
  `keluhan` text DEFAULT NULL,
  `cairan_vagina` text DEFAULT NULL,
  `keadaan_servix` text DEFAULT NULL,
  `pemeriksaan_sitologi` varchar(50) DEFAULT NULL,
  `jumlah_rokok` int(11) DEFAULT NULL,
  `lain` varchar(255) DEFAULT NULL,
  `tumor` varchar(255) DEFAULT NULL,
  `kelenjar_regional` varchar(255) DEFAULT NULL,
  `jenis_lesi` varchar(50) DEFAULT NULL,
  `asal_lesi` varchar(255) DEFAULT NULL,
  `metastasis` varchar(255) DEFAULT NULL,
  `ro_foto` varchar(255) DEFAULT NULL,
  `tindakan_pemeriksaan` varchar(50) DEFAULT NULL,
  `status_tindakan` varchar(50) DEFAULT NULL,
  `diagnosis_klinik` text DEFAULT NULL,
  `keterangan_penyakit` text DEFAULT NULL,
  `status_pengajuan` varchar(50) DEFAULT 'Menunggu Verifikasi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengajuan`
--

INSERT INTO `pengajuan` (`id_pengajuan`, `id_pengguna`, `nama_dokter`, `alamat_rs`, `nama_pasien`, `usia`, `jenis_kelamin`, `negara`, `alamat`, `tanggal_pengajuan`, `asal_jaringan`, `perendaman`, `pemeriksaan_patologi`, `nomor_pemeriksaan`, `tanggal_pemeriksaan`, `diagnosis_pemeriksaan`, `poliklinik`, `klas`, `bahan_tersedia`, `diambil_dengan`, `jumlah_sampel`, `jenis_preparat`, `fiksasi`, `status_diri`, `jumlah_anak`, `kontrasepsi`, `keluhan`, `cairan_vagina`, `keadaan_servix`, `pemeriksaan_sitologi`, `jumlah_rokok`, `lain`, `tumor`, `kelenjar_regional`, `jenis_lesi`, `asal_lesi`, `metastasis`, `ro_foto`, `tindakan_pemeriksaan`, `status_tindakan`, `diagnosis_klinik`, `keterangan_penyakit`, `status_pengajuan`) VALUES
('JRM-2025-001', NULL, 'dr. Herawati Puspita Sp.PA', 'RS Puspita ', 'Bunga Lestari', 56, 'perempuan', 'Indonesia', 'Jl. Godean No.67', '2025-06-25', 'Anthrum gaster', '20%', 'sudah', '226071', '2025-06-01', 'Sakitt', 'Dalam', 'BPJS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Sakit', 'tidak', 'Verifikasi'),
('SNRM-2025-001', NULL, 'dr. Aliya Putri Sp.GA ', 'RS Puspita', 'Bunga Andini', 65, 'perempuan', 'Indonesia', 'Jl. Kertanegara No.08', '2025-06-25', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, 'sputum', NULL, 1, 'basah', 'Alkohol 95%', NULL, NULL, NULL, NULL, NULL, NULL, 'baru', 0, '', '', '', 'primer', 'mana aja', '', '', 'operasi', 'belum', '', '', 'Menunggu Verifikasi'),
('SRM-2025-001', NULL, 'dr. Bayu Purnomo Sp.PD ', 'RS Puspita', 'Bagas Andikara', 24, 'laki-laki', 'Indonesia', 'Jl. Caturtunggal Km 09', '2025-06-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'endometrium,endoservix', 'Y.Scaper', 1, 'basah', 'alkohol95%', 'belumKawin', 0, '', 'fluor', 'putih', 'tenang', 'baru', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'Menunggu Verifikasi'),
('SRM-2025-002', NULL, 'dr. Bayu Purnomo Sp.PD ', 'RS Puspita', 'Lulu Indah', 36, 'perempuan', 'Indonesia', 'Jl. Sudirman Km 10', '2025-06-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'endometrium', 'Y.Scaper', 1, 'kering', 'alkoholAetherAA', 'kawin', 2, 'iud', 'fluor', 'putih', 'tenang', 'baru', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'Menunggu Verifikasi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengambilan`
--

CREATE TABLE `pengambilan` (
  `id_pengambilan` int(11) NOT NULL,
  `id_pengajuan` varchar(30) NOT NULL,
  `tanggal_pengambilan` date NOT NULL,
  `tanggal_pengambilan_ulang` date DEFAULT NULL,
  `waktu` time NOT NULL,
  `status_pengambilan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengambilan`
--

INSERT INTO `pengambilan` (`id_pengambilan`, `id_pengajuan`, `tanggal_pengambilan`, `tanggal_pengambilan_ulang`, `waktu`, `status_pengambilan`) VALUES
(1, 'JRM-2025-001', '2025-06-02', '2025-06-04', '15:11:00', 'Berhasil');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(20) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `alamat` varchar(50) NOT NULL,
  `nomortlp` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `email`, `password`, `nama`, `alamat`, `nomortlp`) VALUES
(1, 'kurirmadpat123@gmail.com', 'kurir123**', 'Handoko Budiman', 'Jl. Bantul km 08', '0988812000'),
(2, 'petugasmadpat123@gmail.com', 'petugas123**', 'Suci Lestari', 'Jl. Semail No.09', '0895602271802'),
(3, 'pemilikmadpat123@gmail.com', 'pemilik123**', 'Agus Handoko', 'Jl. Cepit No.020', '089671000'),
(4, 'muhammadrizki@gmail.com', '123', 'Muhammad Rizki', 'Jl. Godean No.78 Sleman', '2147483647'),
(5, 'Dian@gmail.com', '1234', 'Dian Puspita', 'Jl. Kusuma Negara No.9', '08897777666');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengujian`
--

CREATE TABLE `pengujian` (
  `id_pengujian` varchar(30) NOT NULL,
  `id_pengambilan` int(11) NOT NULL,
  `nomor_pemeriksaan` varchar(35) DEFAULT NULL,
  `nama_pasien` varchar(35) NOT NULL,
  `usia` int(11) NOT NULL,
  `alamat` varchar(50) NOT NULL,
  `tanggal_terima` date NOT NULL,
  `tanggal_jadi` date NOT NULL,
  `asal_sediaan` varchar(450) DEFAULT NULL,
  `diagnosa_klinik` varchar(450) DEFAULT NULL,
  `keterangan_klinik` varchar(50) NOT NULL,
  `mikroskopis` varchar(450) NOT NULL,
  `makroskopis` varchar(450) NOT NULL,
  `kesimpulan` varchar(300) NOT NULL,
  `status_pengujian` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`);

--
-- Indeks untuk tabel `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD PRIMARY KEY (`id_pengajuan`);

--
-- Indeks untuk tabel `pengambilan`
--
ALTER TABLE `pengambilan`
  ADD PRIMARY KEY (`id_pengambilan`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`);

--
-- Indeks untuk tabel `pengujian`
--
ALTER TABLE `pengujian`
  ADD PRIMARY KEY (`id_pengujian`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pengambilan`
--
ALTER TABLE `pengambilan`
  MODIFY `id_pengambilan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
