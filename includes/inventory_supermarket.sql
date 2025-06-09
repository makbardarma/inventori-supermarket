-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Jun 2025 pada 12.00
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
-- Database: `inventory_supermarket`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Makanan', 'Produk makanan kemasan', '2025-06-05 14:39:35'),
(2, 'Minuman', 'Minuman dalam kemasan', '2025-06-05 14:39:35'),
(3, 'Peralatan', 'Peralatan rumah tangga', '2025-06-05 14:39:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `kode_barang` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `location` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `location_rack` varchar(100) DEFAULT NULL,
  `rack_location` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_in` date DEFAULT curdate(),
  `date_out` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `user_id`, `kode_barang`, `name`, `description`, `price`, `stock`, `location`, `category_id`, `location_rack`, `rack_location`, `image`, `date_in`, `date_out`, `created_at`, `updated_at`, `status`, `is_deleted`) VALUES
(2, NULL, 'KB00002', 'Aqua Gelas', 'Air mineral kemasan gelas', 500.00, 200, '3', 2, '1', 'Rak B2', NULL, '2023-02-20', NULL, '2025-06-05 14:39:35', '2025-06-05 17:50:01', 'draft', 0),
(4, NULL, 'KB00004', 'Roti', 'sehat', 20000.00, 2, '2', 1, NULL, NULL, NULL, '2025-06-06', NULL, '2025-06-05 17:15:25', '2025-06-05 17:50:01', 'draft', 0),
(5, NULL, 'KB00005', 'Biskuat', 'welel', 10000.00, 4, '4', 1, NULL, NULL, NULL, '2025-06-05', NULL, '2025-06-05 17:28:45', '2025-06-05 17:50:01', 'draft', 0),
(6, NULL, 'KB00006', 'cucu', 'kiw', 30000.00, 100, '5', 2, NULL, NULL, NULL, '2025-06-05', NULL, '2025-06-05 17:29:09', '2025-06-05 17:50:01', 'draft', 0),
(7, NULL, 'KB00007', 'gandum', 'whwhwh', 292929.00, 20, '6', 2, NULL, NULL, NULL, '2025-06-05', NULL, '2025-06-05 17:32:10', '2025-06-05 17:50:01', 'draft', 0),
(8, NULL, 'KB00008', 'jamu', 'minumana sehat', 50000.00, 20, '7', 2, NULL, NULL, NULL, '2025-06-05', NULL, '2025-06-05 17:32:36', '2025-06-05 17:50:01', 'draft', 0),
(11, NULL, 'BC145', 'Marimas', 'uhuy', 1000.00, 500, '8', 2, NULL, NULL, NULL, '2025-06-05', NULL, '2025-06-05 17:50:48', '2025-06-05 17:53:04', 'draft', 0),
(15, 6, 'USR-1749340656-8404', 'Gunting', 'hshshhs', 0.00, 60, NULL, 3, NULL, NULL, NULL, '2025-06-08', NULL, '2025-06-07 23:57:36', '2025-06-08 02:02:50', 'submitted', 1),
(16, 6, 'USR-1749341195-8149', 'Keju Mozarella', 'manis gurih ', 0.00, 100, NULL, 1, NULL, NULL, NULL, '2025-06-08', NULL, '2025-06-08 00:06:35', '2025-06-09 07:16:59', 'accepted', 1),
(17, 6, 'USR-1749347998-1182', 'Keju Mayones', 'didjeke', 0.00, 100, NULL, 1, NULL, NULL, NULL, '2025-06-08', NULL, '2025-06-08 01:59:58', '2025-06-08 13:18:24', 'rejected', 1),
(18, 6, 'USR-1749348425-1574', 'Kecap Bangau', 'manis alami', 43000.00, 100, NULL, 1, NULL, NULL, NULL, '2025-06-08', NULL, '2025-06-08 02:07:05', '2025-06-08 13:18:43', 'accepted', 1),
(19, 6, 'USR-1749348491-2883', 'Extra Joss', 'EXTRA MINUMAN', 10000.00, 20, '10', 2, NULL, NULL, NULL, '2025-06-08', NULL, '2025-06-08 02:08:11', '2025-06-08 13:18:17', 'accepted', 0),
(20, 7, 'USR-1749441554-5446', 'KOPI TUBRUK', 'KOPI ENAK NIKMAT', 20000.00, 300, NULL, 2, NULL, NULL, NULL, '2025-06-09', NULL, '2025-06-09 03:59:14', '2025-06-09 07:16:53', 'accepted', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_history`
--

CREATE TABLE `stock_history` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `movement` enum('in','out') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `fullname`, `photo`, `password`, `role`, `created_at`) VALUES
(3, 'admin', 'admin@supermarket.com', NULL, NULL, 'HASIL_HASH_DISINI', 'admin', '2025-06-05 16:10:47'),
(4, 'Dewiku', 'dewiku@gmail.com', NULL, NULL, '$2y$10$khMJ7Wql7.nODgJYr1EAAOG0sssAvpdtAf5f9hLZds1/TYKlRZeji', 'admin', '2025-06-05 16:16:50'),
(5, '', '', NULL, 'admin_5_1749385973.jpg', '$2y$10$WzQHYywS2FiSkHcXi2Gx2ekozxBku2vSitmlg.Cuwxzyq3pedh2xO', 'admin', '2025-06-05 16:57:39'),
(6, 'user', 'user@gmail.com', 'Reisa Beiber', 'user_6_1749349438.jpg', '$2y$10$sIYWeSzd0ZujiNIR8VyKo.uuadzgV9ODL1SFfDIiSR4.bOMikBFFq', 'user', '2025-06-07 07:39:21'),
(7, 'user1', 'user1@gmail.com', 'nikmatul', 'user_7_1749436248.png', '$2y$10$4YSOVcbecsIcMPt0.q/dN.2aUH6uQOPiw6nZjY910rIOkFJdx1//O', 'user', '2025-06-08 12:29:04'),
(8, 'adminbaru', 'adminbaru@gmail.com', NULL, 'admin_8_1749391682.jpg', '$2y$10$r.ifNTSEAfkuKpJwxz978.Igy64VAQ/RxivJ5oHZhYar4KgjXUaje', 'admin', '2025-06-08 14:05:37');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_barang` (`kode_barang`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeks untuk tabel `stock_history`
--
ALTER TABLE `stock_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `stock_history`
--
ALTER TABLE `stock_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `stock_history`
--
ALTER TABLE `stock_history`
  ADD CONSTRAINT `stock_history_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
