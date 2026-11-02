-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 01 Nov 2026 pada 16.15
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pengaduan_mts`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `deskripsi`) VALUES
(1, 'Infrastruktur', 'Jalan rusak, lampu jalan mati, jembatan, saluran air, dll'),
(2, 'Kebersihan', 'Sampah menumpuk, saluran tersumbat, lingkungan kotor'),
(3, 'Keamanan', 'Ketertiban, keamanan lingkungan, konflik warga'),
(4, 'Sosial', 'Bantuan sosial, administrasi kependudukan, layanan warga lainnya'),
(8, 'Kegiatan Belajar Mengajar', NULL),
(12, 'Lainnya', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaduan`
--

CREATE TABLE `pengaduan` (
  `id` int(11) NOT NULL,
  `no_tiket` varchar(30) NOT NULL,
  `nama_pelapor` varchar(100) NOT NULL,
  `kelas` varchar(10) DEFAULT NULL,
  `lokasi_kejadian` varchar(255) DEFAULT NULL,
  `kategori_id` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('baru','diverifikasi','diproses','selesai','ditolak') NOT NULL DEFAULT 'baru',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaduan`
--

INSERT INTO `pengaduan` (`id`, `no_tiket`, `nama_pelapor`, `kelas`, `lokasi_kejadian`, `kategori_id`, `deskripsi`, `foto`, `status`, `created_at`, `updated_at`) VALUES
(2, 'PGD-20261031-0001', 'Nikma', '8', 'didalam ruang kelas 8', 3, 'ada perkelahian didalam kelas', 'bukti_6ae5c15373ef5.jpg', 'diproses', '2026-10-31 10:22:11', '2026-10-31 10:46:49'),
(3, 'PGD-20261031-0002', 'abdillah', '9', 'diluar kelas depan ruang kelas 9', 2, 'tong sampah pecah kena bola', 'bukti_6ae5cc73b319f.jpg', 'diverifikasi', '2026-10-31 11:09:39', '2026-10-31 11:11:00'),
(4, 'PGD-20261101-0001', 'latip', '7', 'didalam kelas', 8, 'ruang kelas kurang kondusif', 'bukti_6ae74fa88fed7.jpg', 'baru', '2026-11-01 14:41:44', '2026-11-01 14:41:44'),
(5, 'PGD-20261101-0002', 'puput', '9', 'lapangan', 3, 'ada perkelahian', 'bukti_6ae7500d93b47.jpg', 'baru', '2026-11-01 14:43:25', '2026-11-01 14:43:25');

-- --------------------------------------------------------

--
-- Struktur dari tabel `status_log`
--

CREATE TABLE `status_log` (
  `id` int(11) NOT NULL,
  `pengaduan_id` int(11) NOT NULL,
  `status_baru` enum('baru','diverifikasi','diproses','selesai','ditolak') NOT NULL,
  `catatan` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `status_log`
--

INSERT INTO `status_log` (`id`, `pengaduan_id`, `status_baru`, `catatan`, `updated_by`, `created_at`) VALUES
(1, 2, 'baru', 'Pengaduan diterima sistem', NULL, '2026-10-31 10:22:11'),
(2, 2, 'diproses', '', 7, '2026-10-31 10:46:49'),
(3, 3, 'baru', 'Pengaduan diterima sistem', NULL, '2026-10-31 11:09:39'),
(4, 3, 'diverifikasi', '', 7, '2026-10-31 11:11:00'),
(5, 4, 'baru', 'Pengaduan diterima sistem', NULL, '2026-11-01 14:41:44'),
(6, 5, 'baru', 'Pengaduan diterima sistem', NULL, '2026-11-01 14:43:25');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `created_at`) VALUES
(3, 'admin_sekolah', 'admin-sekolah', 'Administrator MTs', '2026-10-01 19:03:39'),
(5, 'guru_mts', '$2y$10$xOyaSwsJy89YRZYCROoWYuD5ifnU0I2GDgM/FErMpxJTwgxDthAX6', 'Guru MTs', '2026-10-01 19:03:39'),
(6, 'tu_mts', '$2y$10$t6xcUsj85TTvFeThfPil..3i3EaljJRDuER4wFNuZEttnqMDZtY2a', 'Tata Usaha', '2026-10-01 19:03:40'),
(7, 'bk_mts_ypm1', '$2y$10$GzhIoIUTEuF1XEGHAYHaV.j1KP.fqfwifZOJoNpeXn3o5QvboymMu', 'Guru BK', '2026-10-01 19:25:30');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_tiket` (`no_tiket`),
  ADD KEY `kategori_id` (`kategori_id`),
  ADD KEY `idx_pengaduan_status` (`status`),
  ADD KEY `idx_pengaduan_tiket` (`no_tiket`);

--
-- Indeks untuk tabel `status_log`
--
ALTER TABLE `status_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_status_log_pengaduan` (`pengaduan_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `pengaduan`
--
ALTER TABLE `pengaduan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `status_log`
--
ALTER TABLE `status_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD CONSTRAINT `pengaduan_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`);

--
-- Ketidakleluasaan untuk tabel `status_log`
--
ALTER TABLE `status_log`
  ADD CONSTRAINT `fk_status_log_pengaduan` FOREIGN KEY (`pengaduan_id`) REFERENCES `pengaduan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
