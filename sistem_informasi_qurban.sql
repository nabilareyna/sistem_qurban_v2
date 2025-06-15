-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.39 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for sistem_informasi_qurban
CREATE DATABASE IF NOT EXISTS `sistem_informasi_qurban` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `sistem_informasi_qurban`;

-- Dumping structure for table sistem_informasi_qurban.distribusi
CREATE TABLE IF NOT EXISTS `distribusi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '0',
  `jumlah_daging` int NOT NULL,
  `status_ambil` enum('belum','diambil') NOT NULL DEFAULT 'belum',
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_distribusi_users` (`user_nik`),
  CONSTRAINT `FK_distribusi_users` FOREIGN KEY (`user_nik`) REFERENCES `users` (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table sistem_informasi_qurban.distribusi: ~60 rows (approximately)

-- Dumping structure for table sistem_informasi_qurban.hewans
CREATE TABLE IF NOT EXISTS `hewans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jenis` enum('sapi','kambing') NOT NULL,
  `harga` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table sistem_informasi_qurban.hewans: ~2 rows (approximately)
INSERT INTO `hewans` (`id`, `jenis`, `harga`, `created_at`) VALUES
	(7, 'kambing', 2750000, '2025-06-13 14:43:09'),
	(8, 'sapi', 21000000, '2025-06-13 14:57:56');

-- Dumping structure for table sistem_informasi_qurban.keuangan
CREATE TABLE IF NOT EXISTS `keuangan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipe` enum('masuk','keluar') NOT NULL,
  `kategori` varchar(50) NOT NULL DEFAULT '',
  `jumlah` int NOT NULL DEFAULT '0',
  `catatan` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table sistem_informasi_qurban.keuangan: ~3 rows (approximately)

-- Dumping structure for table sistem_informasi_qurban.qurbans
CREATE TABLE IF NOT EXISTS `qurbans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '0',
  `hewan_id` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `status_bayar` enum('sudah','belum') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK2_hewan` (`hewan_id`),
  KEY `FK_qurbans_users` (`user_nik`),
  CONSTRAINT `FK2_hewan` FOREIGN KEY (`hewan_id`) REFERENCES `hewans` (`id`),
  CONSTRAINT `FK_qurbans_users` FOREIGN KEY (`user_nik`) REFERENCES `users` (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table sistem_informasi_qurban.qurbans: ~1 rows (approximately)

-- Dumping structure for table sistem_informasi_qurban.users
CREATE TABLE IF NOT EXISTS `users` (
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `role` enum('warga','panitia','berqurban','admin') NOT NULL DEFAULT 'warga',
  `alamat` text,
  `no_hp` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table sistem_informasi_qurban.users: ~61 rows (approximately)
INSERT INTO `users` (`nik`, `name`, `username`, `password`, `role`, `alamat`, `no_hp`, `created_at`) VALUES
	('1234567890123456', 'Admin Satu', 'admin1', '$2y$10$/HIRnQiNQxUCYrFzgUglvOQ8ZLXJ5cqVoc1XbBRHK9i8Ny.cIhHuW', 'admin', 'jl. aa', '081234567811', NULL),
	('3171020100000001', 'Budi Speed', 'budi', '$2y$10$3eCK0OVAOYMn4ujkD8yY.eQTYU4N7KCHzAWO6eG1.FCUivTFsOilq', 'warga', 'Jl. Melati No. 2', '081234567891', NULL),
	('3171020100000002', 'Siti Aminah', 'siti', '$2y$10$0wyEbQls6qpP25/6ODo31uXqRyVRNuLfoVNPvTfyGNbmzRxXZSz.m', 'panitia', 'Jl. Melati No. 3', '081234567891', NULL),
	('3171020100000003', 'Rina Wulandari', 'rina', '$2y$10$XYRzsL.N/IlgRMCBQxVYA.fVphQAzup/bM1MNi9dXcJ0MjDyG/tBS', 'panitia', 'Jl. Anggrek No. 1', '081234567893', NULL),
	('3171020100000004', 'Doni Prasetyo', 'doni', '$2y$10$/wOZ6/YhZZEdfE1LzP8aLe1a4QFD0hV0w/OVI5CVJVF7FKdGDZ5tm', 'warga', 'Jl. Anggrek No. 2', '081234567894', NULL),
	('3171020100000005', 'Hendra Saputra', 'hendra', '$2y$10$bs86UXUzlB/Y2Khr/1Rre.MBacMl2Ju6.x.NaXo5yN78jk4hX/sjy', 'panitia', 'Jl. Dahlia No. 1', '081234567895', NULL),
	('3171020100000006', 'Nia Kurniawati', 'nia', '$2y$10$5h9LM6KEhp/JLKCtX4VCuO5Av9gKxS4JjIJXLf1gceWKkwevTxKRi', 'warga', 'Jl. Dahlia No. 2', '081234567896', NULL),
	('3171020100000007', 'Rudi Hartono', 'rudi', '$2y$10$8qf8TjpLP4RHXYXjN/LxSeFbma70PeG2xLkC9xx8o5I0ePHHKQltK', 'warga', 'Jl. Sakura No. 3', '081234567897', NULL),
	('3171020100000008', 'Dewi Lestari', 'dewi', '$2y$10$Czs01Mitp9fxqK7.hisxJe2sNx.vIYsavjZiCij0o.HdN7ds5kYF.', 'warga', 'Jl. Sakura No. 3', '081234567897', NULL),
	('3171020100000009', 'Yusuf Maulana', 'yusuf', '$2y$10$qP8yFlQPOyKdoOYHRJSnX.n/oeQ1n78l/QuCbaFYS6ugIjHzJhbWy', 'warga', 'Jl. Kenanga No. 1', '081234567899', NULL),
	('3171020100000010', 'Ahmad Fauzi', 'ahmad11', '$2y$10$rJwr5q4P71ujv4QtV4YLTuURxJ3oNhEvy9qFezL9/r7r/zA6qPGyS', 'warga', 'Jl. Kenanga No. 2', '081234567811', NULL),
	('317102010000011', 'Bella Putri', 'bella12', '$2y$10$ulnVJzHAjddqW35N7xnyeuai71vMNQZBM7S0fFyrJCAXoX82gktaq', 'warga', 'Jl. Flamboyan No. 1', '081234567812', NULL),
	('317102010000012', 'Candra Wijaya', 'candra13', '$2y$10$fncJN2fIQz8g/KXUUbne7e6KdH4D/.NSVPBPNFjI4P8xGAIYhX/r.', 'warga', 'Jl. Flamboyan No. 2', '081234567813', NULL),
	('317102010000013', 'Dina Marlina', 'dina14', '$2y$10$PQRSZGK/Kil/wrT/JoGVwuHyeAVt3aYyYlZ8aDAulsY3kHfhSbqm.', 'warga', 'Jl. Flamboyan No. 3', '081234567814', NULL),
	('317102010000014', 'Eko Prasetyo', 'eko15', '$2y$10$IPGkl3Jkg8CBXiEZVVC2BOtmrTT88cjYpmASP.hip.Auu4/fYRwhO', 'warga', 'Jl. Anggrek No. 4', '081234567815', NULL),
	('317102010000015', 'Fitriani', 'fitri16', '$2y$10$j4lOKVp0/0ykP.FsL7ye7eGEZnvPb6.q35NHt7NEg6RU.esC.v262', 'warga', 'Jl. Anggrek No. 5', '081234567816', NULL),
	('317102010000016', 'Galih Permana', 'galih17', '$2y$10$HVSGAReqCjRFOslG2Qv8Y.7TSm4y64ZX0tDAOIzc6BB7go9JXkscW', 'warga', 'Jl. Mawar No. 6', '081234567817', NULL),
	('317102010000017', 'Hesti Rahayu', 'hesti18', '$2y$10$mkzrDp5YohQdNiUMO60XQOiAOjTGq5T8jlZgaKWlblDTTqNxbniB.', 'warga', 'Jl. Mawar No. 7', '081234567818', NULL),
	('317102010000018', 'Irfan Setiawan', 'irfan19', '$2y$10$l5izKlerpK7Hx3jWvTBy4ufJ4RFRMyuJWPa9xUG3CC.SsGtOE.J5a', 'warga', 'Jl. Melati No. 8', '081234567819', NULL),
	('317102010000019', 'Joko Susilo', 'joko', '$2y$10$j7Hw7W4feAuQfvDU6IycnO6fjT9Yb8kwnxjjH18OWg7fKqsvZfuOm', 'warga', 'Jl. Melati No. 9', '081234567820', NULL),
	('317102010000020', 'Kurnia Dewi', 'kurnia21', '$2y$10$0KIftlArTE8y1ZeJVd6XJeD9odLvQgKwgXqU5mv2AAQR5QX2EI1sW', 'warga', 'Jl. Dahlia No. 10', '081234567821', NULL),
	('317102010000021', 'Lina Handayani', 'lina22', '$2y$10$1Ddqn3wQsy20sYF4fFGPcOnE6.MuNgSNSNwCs.xj0BruW9dlDWOm6', 'warga', 'Jl. Dahlia No. 11', '081234567822', NULL),
	('317102010000022', 'Maman Sutarman', 'maman23', '$2y$10$geb9PdiwnyObe3vOIjhAbOaCc90mYWT95zS/mDRRpp.WqsMPUxXlW', 'warga', 'Jl. Sakura No. 12', '081234567823', NULL),
	('317102010000023', 'Nia Kurniasih', 'nia24', '$2y$10$X/4SpIJGBjERNETokOOUk.jTd5KNii7dIUFPTMupSrwOqJsx3nu66', 'warga', 'Jl. Sakura No. 13', '081234567824', NULL),
	('317102010000024', 'Oki Setiawan', 'oki25', '$2y$10$L2qs3iqaLhm6dIVaZbbHl.FlIlkv9.aR.YszKGayuFw9JDQKqOgmy', 'warga', 'Jl. Kenanga No. 14', '081234567825', NULL),
	('317102010000025', 'Putri Ayu', 'putri26', '$2y$10$cWPmjoN4k90SVQZdsVtps.zmW4pPjhYJe2ObSqZ2JimgGv.fEWS.q', 'warga', 'Jl. Kenanga No. 15', '081234567826', NULL),
	('317102010000026', 'Rian Pratama', 'rian27', '$2y$10$vdRzucujXOS6b1IQyhSHHek35PpHP7CjhPBVn2/x7CIuTDYbWmMWa', 'warga', 'Jl. Flamboyan No. 16', '081234567827', NULL),
	('317102010000027', 'Santi Wijaya', 'santi28', '$2y$10$ljEayiHWYfImQM1yD8a4R.GDkK3pZMe6gm7cVT/1kis4YGIX42VFm', 'warga', 'Jl. Flamboyan No. 17', '081234567828', NULL),
	('317102010000028', 'Tono Gunawan', 'tono29', '$2y$10$J5eX65VXj130MggU58IM0OW8NcyAdF/37NAQkUgolAp/Z5SwGnIE.', 'warga', 'Jl. Anggrek No. 18', '081234567829', NULL),
	('317102010000029', 'Umi Kulsum', 'umi30', '$2y$10$3HsYq82WWuFT2igLhsql3uMO7X.QubNQshpGMU9GGFIgJKJmKmtGa', 'warga', 'Jl. Anggrek No. 19', '081234567830', NULL),
	('317102010000030', 'Vina Oktaviani', 'vina31', '$2y$10$1r5jZZa2kUWED8s0A59vXuwGE1nWvKNn5waiJ5R/toV0mv1eI6Eaa', 'warga', 'Jl. Mawar No. 20', '081234567831', NULL),
	('317102010000031', 'Wawan Kurniawan', 'wawan32', '$2y$10$PtDe359tJCSrRqeBuSMiP.Y4KHHnG871X9wQTMA7cKIl7FquoOqxK', 'warga', 'Jl. Mawar No. 21', '081234567832', NULL),
	('317102010000032', 'Xena Valentina', 'xena33', '$2y$10$MxEjwo84jkLfbn4JR07k8uWoilJ4mGN/OmfjSyjmhJOSW4OXK/75a', 'warga', 'Jl. Melati No. 23', '081234567833', NULL),
	('317102010000033', 'Yudi Hermawan', 'yudi34', '$2y$10$mPw0JCw1XILPYbu9AFtfKuZw4DiFBlJEq5GqXnpvDzDJjc6Pge4za', 'warga', 'Jl. Melati No. 23', '081234567834', NULL),
	('317102010000034', 'Zaskia Adya', 'zaskia35', '$2y$10$g0R2zz9yBSIAPRqDQW3zUOlFQKZ2bVPvVc1oTCrrcdx3zdWoWeZFG', 'warga', 'Jl. Dahlia No. 24', '081234567835', NULL),
	('317102010000035', 'Arif Budiman', 'arif36', '$2y$10$KkDIYDJmFpY/WmjUXunO2uU5bKYe1/Y9yaSxeofJcGGBfsX4cPFoK', 'warga', 'Jl. Dahlia No. 25', '081234567836', NULL),
	('317102010000036', 'Bunga Citra', 'bunga37', '$2y$10$qW1vSOdgCj.tdMIOIJ3aduEwwqpTE1YD11yO483AEuJZXXyOj5rt2', 'warga', 'Jl. Sakura No. 26', '081234567837', NULL),
	('317102010000037', 'Cahyo Purnomo', 'cahyo38', '$2y$10$J/xzsr80wZhgqZyYOn30VeeEA8Yi1ooHix5O2EnIMNacQ7eJArdIa', 'warga', 'Jl. Sakura No. 27', '081234567838', NULL),
	('317102010000038', 'Dedi Susanto', 'dedi39', '$2y$10$HDmnh0gC.mKXnA8TKxhfjOVvq4f79GAVoUQQZxtW7LtyGgBIkoi6q', 'warga', 'Jl. Kenanga No. 28', '081234567839', NULL),
	('317102010000039', 'Elsa Fitriani', 'elsa40', '$2y$10$UVZbdPqTadDtz5/.Ff1LS.DRF6axzP4sgFCMrQCu02VKiJKnqJp.S', 'warga', 'Jl. Kenanga No. 29', '081234567840', NULL),
	('317102010000040', 'Dedi Mulyadi', 'dedimul', '$2y$10$dmRdyL6AQ6EDgXi.ePVoIedFv5ZlPlB7/J0XHs89ueNxI/yXCvy9y', 'warga', 'Jl. Flamboyan No. 30', '081234567841', NULL),
	('317102010000041', 'Gita Permata', 'gita42', '$2y$10$fT0tnbmglMrorNzYuZiTWOcvS51n96xQ2z3VGjXKzpfZdjlx4LJoC', 'warga', 'Jl. Flamboyan No. 31', '081234567842', NULL),
	('317102010000042', 'Heru Santoso', 'heru43', '$2y$10$MaLA3Vmi.v4Lo1DtzN0LAeE.4q0O/W23fg8HWLCgVVBYNrDAMHldW', 'warga', 'Jl. Anggrek No. 32', '081234567843', NULL),
	('317102010000043', 'Intan Permata', 'intan44', '$2y$10$JfU9NpdclcHcrokN7EqhbeOSjyDysYR3D6D.pKihOi4yCXBC.n.G6', 'warga', 'Jl. Anggrek No. 33', '081234567844', NULL),
	('317102010000044', 'Jefri Alexander', 'jefri45', '$2y$10$AzCZDpyUNavTUwM01rheM.PjSiNaNNp3lxAY/B9RxfZZzz6/P3Jgu', 'warga', 'Jl. Mawar No. 34', '081234567845', NULL),
	('317102010000045', 'Kiki Amalia', 'kiki46', '$2y$10$xIxB7P//n/kU4JH5tlSF5.N5hJ4leXb0H66HSKkwXwD97/6yABCQa', 'warga', 'Jl. Mawar No. 35', '081234567846', NULL),
	('317102010000046', 'Luki Setiawan', 'luki47', '$2y$10$WvfmDyNNWuzz9lBODGTYBu/L.RLU/0azISWYKKoJ/qfP/ipgdMH2K', 'warga', 'Jl. Melati No. 36', '081234567847', NULL),
	('317102010000047', 'Maya Sari', 'maya48', '$2y$10$2T9Uh.ZqLBrO7TkYukcFMO0D/2dNw53EpaUv6QDA.HcW/LFQxNHTC', 'warga', 'Jl. Melati No. 37', '081234567848', NULL),
	('317102010000048', 'Nando Pratama', 'nando49', '$2y$10$4.GP0.lzAnK57adADft2TurFPpxsMg78bVIV8o1H2pT0zbTdgqd6O', 'warga', 'Jl. Dahlia No. 38', '081234567849', NULL),
	('317102010000049', 'Oki Maulana', 'oki50', '$2y$10$QtXuEl6q0W2Uxbw2NjfGGuT2NZEPo1fhlWqZYQ0NNdbXftyKeimKq', 'warga', 'Jl. Dahlia No. 39', '081234567850', NULL),
	('317102010000050', 'Putra Ramadhan', 'putra51', '$2y$10$uIDiLlb3x983xprQaNFsn.ryIj6nqjdea4VkmmYMHMgoRF4rhLpeu', 'warga', 'Jl. Sakura No. 40', '081234567851', NULL),
	('317102010000051', 'Queen Amelia', 'queen52', '$2y$10$4srR0.BXl6x48t58Hfhhg.7j4FBu1ekKgJBI7kAS4Idu0TBM6BD02', 'warga', 'Jl. Sakura No. 41', '081234567852', NULL),
	('317102010000052', 'Rizky Fadilah', 'rizky53', '$2y$10$8J3pbISSQR1pzsyy.uu.seYLad0PWXvV47aK6yXMC2uuA/HsNlpqm', 'warga', 'Jl. Kenanga No. 42', '081234567853', NULL),
	('317102010000053', 'Siska Nurhayati', 'siska54', '$2y$10$Y3aR5611MZYsomzoUjGpIes2oHAkUjFT/XcNQYEw/.Maur.OiJlzK', 'warga', 'Jl. Kenanga No. 43', '081234567854', NULL),
	('317102010000054', 'Taufik Hidayat', 'taufik55', '$2y$10$BCkJGc1/JZfDnF992jg1GuZYZZSVGhyHfDSM15Amlm9pqNZsoUPfK', 'warga', 'Jl. Flamboyan No. 44', '081234567855', NULL),
	('317102010000055', 'Ujang Suryana', 'ujang56', '$2y$10$OUYb4psU.lKx9vqrJN6nne2c0AOj2hQsroE2Do.aPtk7CUzEYkDI2', 'warga', 'Jl. Flamboyan No. 45', '081234567856', NULL),
	('317102010000056', 'Vino Bastian', 'vino57', '$2y$10$aqgPhaCE5yHg0wBZKflwuevILqK4j1ocJLmzW5jm8gFSlWdX4PQdW', 'warga', 'Jl. Anggrek No. 46', '081234567857', NULL),
	('317102010000057', 'Wulan Dari', 'wulan58', '$2y$10$faY6qU6St/4RMpz6sSLoxeDokCBLyfu/EbvRxCo3e1vtIZcsHGus2', 'warga', 'Jl. Anggrek No. 47', '081234567858', NULL),
	('317102010000058', 'Yani Susanti', 'yani59', '$2y$10$lJi3/pnaHNUawmN2d9wUfeRrBeVzosJSjUH.qiig8DAlkUFUM5NmG', 'warga', 'Jl. Mawar No. 48', '081234567859', NULL),
	('317102010000059', 'Zaki Ahmad', 'zaki60', '$2y$10$Zy1KSz79uBNxRYh3UFX3r.4nxGJWE4WnB9YwcIySEvO7v3xyK7Kve', 'warga', 'Jl. Mawar No. 49', '081234567860', NULL),
	('317102010000060', 'Adi Nugroho', 'adi61', '$2y$10$ImlWrISY8iGgNDuhKv725.2ZJxCONPaJtaGkoR3gsWN2kdwZaPuFm', 'warga', 'Jl. Melati No. 50', '081234567861', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
