-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2026 at 11:52 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webgis_rumah_makan_tataaran`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`) VALUES
(1, 'admin', '$2y$12$OG5eMX.qZHu6oUHA01XtbeeRy92OYqlww5EdvR3bXSZMl/L0iKyqi');

-- --------------------------------------------------------

--
-- Table structure for table `rumah_makan`
--

CREATE TABLE `rumah_makan` (
  `id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `alamat` text NOT NULL,
  `kategori` varchar(100) DEFAULT 'Rumah Makan',
  `deskripsi` text DEFAULT NULL,
  `jam_buka` time DEFAULT '08:00:00',
  `jam_tutup` time DEFAULT '21:00:00',
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rumah_makan`
--

INSERT INTO `rumah_makan` (`id`, `nama`, `alamat`, `kategori`, `deskripsi`, `jam_buka`, `jam_tutup`, `latitude`, `longitude`, `foto`, `created_at`) VALUES
(2, 'Pawon Selaras', 'Perum Jl. Unima, Tataaran Patar, Tondano Selatan, Minahasa Regency, North Sulawesi', '', '', '08:00:00', '21:00:00', 1.2678947, 124.8757875, '', '2026-10-26 10:21:33'),
(3, 'Rm Nani', 'SMA N 2 tondano, Lorong, Tataaran Patar, Tondano Selatan,  Minahasa Regency, North Sulawesi', 'Restoran', '', '09:00:00', '18:00:00', 1.2782133, 124.8790270, '', '2026-10-26 10:27:14'),
(4, 'TERRACE SKY', 'Lrg. SMA N 2 Tondano, Tataaran Patar, Kec. Tondano Sel., Kabupaten Minahasa, Sulawesi Utara 95619', 'Rumah Makan', '', '09:00:00', '21:00:00', 1.2784439, 124.8788774, '', '2026-10-26 10:30:51'),
(5, 'Kuliner GEMOY  tataaran patar', 'Tataaran Patar, kec. Tondano Sel., Kabupaten Minahasa, Sulawesi Utara', 'Restoran Indonesia', '', '08:00:00', '22:00:00', 1.2782562, 124.8776382, '', '2026-10-26 10:35:51'),
(6, 'RM. TOP PAS OMA OPA CABANG PASAR SOVENIR TATAARAN  PATAR', 'Pasar Sovenir, Tataaran Patar, Kec. Tondano Sel., Kabupaten Minahasa, Sulawesi Utara', 'Rumah Makan', '', '08:00:00', '00:00:00', 1.2780685, 124.8775900, '', '2026-10-26 10:38:54'),
(7, 'Berkat RN', 'Tataaran Patar, Kec. Tondano Sel, Kabupaten Minahasa, Sulawesi Utara', 'Restoran Masakan Ayam', '', '08:00:00', '23:00:00', 1.2780363, 124.8772627, '', '2026-10-26 10:41:23'),
(8, 'KATSUI Warung Makanan dan Minuman', 'Ruko dekat halte, Jl. Kampus Unima, Tataaran Patar, Kec. Tondano Sel., Kabupaten Minahasa, Sulawesi Utara 95619', 'Rumah Makan', '', '08:00:00', '21:00:00', 1.2779130, 124.8769888, '', '2026-10-26 10:43:53'),
(9, 'RM Ragey 21 tataaran patar', 'Tataaran Dua, Kec. Tondano Selatan., Kaabupaten Minahasa, Sulawesi Utara', 'Restoran', '', '09:00:00', '22:00:00', 1.2811308, 124.8769784, '', '2026-10-26 10:46:27');

-- --------------------------------------------------------

--
-- Table structure for table `ulasan`
--

CREATE TABLE `ulasan` (
  `id` int(11) NOT NULL,
  `rumah_makan_id` int(11) NOT NULL,
  `nama_pengunjung` varchar(100) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `komentar` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `ulasan`
--

INSERT INTO `ulasan` (`id`, `rumah_makan_id`, `nama_pengunjung`, `rating`, `komentar`, `created_at`) VALUES
(3, 3, 'naufal', 5, 'enak dan sangat bersih tempat makan nya', '2026-10-26 10:47:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `rumah_makan`
--
ALTER TABLE `rumah_makan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ulasan_rumah_makan` (`rumah_makan_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rumah_makan`
--
ALTER TABLE `rumah_makan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `fk_ulasan_rumah_makan` FOREIGN KEY (`rumah_makan_id`) REFERENCES `rumah_makan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
