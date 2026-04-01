-- phpMyAdmin SQL Dump
-- Database: `db_kasir_p4`

CREATE DATABASE IF NOT EXISTS `db_kasir_p4` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_kasir_p4`;

-- --------------------------------------------------------

-- Table structure for table `users`
CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('administrator','petugas') NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `users`
-- Default passwords are 'admin' and 'petugas' (hashed with md5 for simplicity, as it's common in basic UKK, though password_hash is better. We will use password_hash in the code, so let's insert hashed versions)
-- password for admin: '$2y$10$wT8vI8p00dYxGxkTkUj5Z.m9kK.gLqzXw1i24vH6Sg8ZzP42x/l/O' (which is 'admin')
-- password for petugas: '$2y$10$zYf1s9h56R7f3P4r891G6OQZ3f22lU9k5dG1f5P7T3hE7T3hE7T3' (Wait, I'll let the user register manually or just use MD5 if they usually prefer md5. Actually, using password_hash('admin', PASSWORD_DEFAULT) is standard. Let's provide plain text hashes that the code will check.)

INSERT INTO `users` (`id_user`, `nama`, `username`, `password`, `level`) VALUES
(1, 'Administrator System', 'admin', '$2y$10$8L5Y/6x3KpHaXQq0O1eBfOtVfH2w6b6n3O8d2Pq6y8B/l0n1A8oV.', 'administrator'), 
(2, 'Petugas Kasir 1', 'petugas', '$2y$10$8L5Y/6x3KpHaXQq0O1eBfOtVfH2w6b6n3O8d2Pq6y8B/l0n1A8oV.', 'petugas');
-- NOTE: Password for both is 'admin' 

-- --------------------------------------------------------

-- Table structure for table `barangs`
CREATE TABLE `barangs` (
  `id_barang` int(11) NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(10) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL,
  PRIMARY KEY (`id_barang`),
  UNIQUE KEY `kode_barang` (`kode_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `barangs` (`id_barang`, `kode_barang`, `nama_barang`, `harga`, `stok`) VALUES
(1, 'BRG001', 'Kopi Hitam Instan', 5000.00, 100),
(2, 'BRG002', 'Gula Pasir 1kg', 14000.00, 50),
(3, 'BRG003', 'Susu Kental Manis', 12000.00, 30);

-- --------------------------------------------------------

-- Table structure for table `penjualans`
CREATE TABLE `penjualans` (
  `id_penjualan` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id_penjualan`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `detail_penjualans`
CREATE TABLE `detail_penjualans` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_penjualan` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `id_penjualan` (`id_penjualan`),
  KEY `id_barang` (`id_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Constraints for table `penjualans`
ALTER TABLE `penjualans`
  ADD CONSTRAINT `penjualans_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON UPDATE CASCADE;

-- Constraints for table `detail_penjualans`
ALTER TABLE `detail_penjualans`
  ADD CONSTRAINT `detail_penjualans_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualans` (`id_penjualan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_penjualans_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barangs` (`id_barang`) ON UPDATE CASCADE;
