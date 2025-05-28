-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Bulan Mei 2025 pada 17.20
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `alleycoffe`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `customers`
--

INSERT INTO `customers` (`id`, `full_name`, `email`, `phone`) VALUES
(1, 'egi Virgi', 'adityavirgi78@gmail.com', '0856398273333');

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `category` enum('coffee','non-coffee','pastry','food','dessert') NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `menu`
--

INSERT INTO `menu` (`id`, `name`, `description`, `price`, `category`, `image`) VALUES
(1, 'Chocolate Croissant', 'Buttery, flaky pastry filled with rich chocolate.', 22000, 'pastry', 'Chocolate-Croissant.jpg'),
(2, 'Almond Croissant', 'Flaky croissant filled with almond cream and topped with sliced almonds.', 25000, 'pastry', 'almond-croissant.jpg'),
(3, 'Espresso', 'Single shot of espresso with rich crema', 18000, 'coffee', 'espresso.jpg'),
(4, 'Cappuccino', 'Espresso with steamed milk and foam', 25000, 'coffee', 'cappuccino.jpg'),
(5, 'Latte', 'Espresso with lots of milk and light foam', 27000, 'coffee', 'latte.jpg'),
(6, 'Americano', 'Espresso with hot water', 20000, 'coffee', 'americano.jpg'),
(7, 'Mocha', 'Espresso with chocolate and steamed milk', 29000, 'coffee', 'mocha.jpg'),
(8, 'Caramel Macchiato', 'Espresso with milk and caramel drizzle', 32000, 'coffee', 'caramel_macchiato.jpg'),
(9, 'Matcha Latte', 'Premium matcha with steamed milk', 30000, 'non-coffee', 'matcha_latte.jpg'),
(10, 'Red Velvet Latte', 'Steamed milk with red velvet flavor', 29000, 'non-coffee', 'red_velvet.jpg'),
(11, 'Lemon Tea', 'Refreshing iced or hot lemon tea', 20000, 'non-coffee', 'lemon_tea.jpg'),
(12, 'Strawberry Milk', 'Chilled strawberry milk blend', 25000, 'non-coffee', 'strawberry_milk.jpg'),
(13, 'Croissant', 'Buttery flaky croissant', 15000, 'pastry', 'croissant.jpg'),
(14, 'Pain au Chocolat', 'Chocolate-filled croissant', 18000, 'pastry', 'pain_au_chocolat.jpg'),
(15, 'Cheese Danish', 'Pastry filled with cheese and sweet glaze', 17000, 'pastry', 'cheese_danish.jpg'),
(16, 'Beef Sandwich', 'Grilled beef sandwich with fresh veggies', 35000, 'food', 'beef_sandwich.jpg'),
(17, 'Chicken Wrap', 'Tortilla wrap filled with chicken & sauce', 34000, 'food', 'chicken_wrap.jpg'),
(18, 'Spaghetti Carbonara', 'Classic creamy spaghetti with bacon', 42000, 'food', 'carbonara.jpg'),
(19, 'Nasi Goreng Alley', 'Indonesian fried rice with fried egg', 30000, 'food', 'nasi_goreng.jpg'),
(20, 'Chocolate Lava Cake', 'Warm cake with melting chocolate center', 28000, 'dessert', 'lava_cake.jpg'),
(21, 'Tiramisu', 'Coffee-flavored Italian dessert', 30000, 'dessert', 'tiramisu.jpg'),
(22, 'Cheesecake', 'Creamy cheesecake slice', 29000, 'dessert', 'cheesecake.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `status` enum('pending','paid','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `reservation_id`, `total`, `status`, `created_at`) VALUES
(1, 2, 158000, 'pending', '2025-05-25 07:43:10'),
(2, 3, 147000, 'pending', '2025-05-25 07:49:13'),
(3, 4, 125000, 'pending', '2025-05-25 08:13:11'),
(4, 5, 75000, 'pending', '2025-05-25 15:04:28'),
(5, 6, 142000, 'pending', '2025-05-25 15:07:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `total_price` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_name`, `quantity`, `price`, `total_price`) VALUES
(1, 1, 'Espresso', 2, 18000, 36000),
(2, 1, 'Cappuccino', 4, 25000, 100000),
(3, 1, 'Chocolate Croissant', 1, 22000, 22000),
(4, 2, 'Almond Croissant', 3, 25000, 75000),
(5, 2, 'Espresso', 4, 18000, 72000),
(6, 3, 'Almond Croissant', 2, 25000, 50000),
(7, 3, 'Cappuccino', 3, 25000, 75000),
(8, 4, 'Almond Croissant', 3, 25000, 75000),
(9, 5, 'Spaghetti Carbonara', 2, 42000, 84000),
(10, 5, 'Red Velvet Latte', 2, 29000, 58000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `people` int(11) DEFAULT NULL,
  `reservation_date` date DEFAULT NULL,
  `checkin` time DEFAULT NULL,
  `checkout` time DEFAULT NULL,
  `dp_amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `reservations`
--

INSERT INTO `reservations` (`id`, `guest_name`, `people`, `reservation_date`, `checkin`, `checkout`, `dp_amount`, `created_at`) VALUES
(1, 'Nabilah Putri Cantika', 4, '2025-05-24', '20:00:00', '22:00:00', 100000.00, '2025-05-24 10:19:38'),
(2, 'Bilah', 3, '2025-05-25', '10:50:00', '11:50:00', 50000.00, '2025-05-25 03:51:03'),
(3, 'Bilah', 2, '2025-05-25', '14:48:00', '14:48:00', 1000000.00, '2025-05-25 07:48:46'),
(4, 'Virgi', 7, '2025-05-25', '15:12:00', '17:12:00', 50000.00, '2025-05-25 08:12:44'),
(5, 'NABILAH PUTRI CANTIKA', 3, '2025-05-22', '21:52:00', '23:52:00', 100000.00, '2025-05-25 14:52:38'),
(6, 'Virgi', 2, '2025-05-25', '22:05:00', '23:05:00', 100000.00, '2025-05-25 15:07:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reservation_tables`
--

CREATE TABLE `reservation_tables` (
  `reservation_id` int(11) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `reservation_tables`
--

INSERT INTO `reservation_tables` (`reservation_id`, `table_id`) VALUES
(1, 3),
(2, 2),
(3, 7),
(4, 1),
(4, 8),
(5, 5),
(6, 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tables`
--

CREATE TABLE `tables` (
  `id` int(11) NOT NULL,
  `seats` int(11) NOT NULL,
  `zone` varchar(10) NOT NULL,
  `status` enum('available','reserved') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tables`
--

INSERT INTO `tables` (`id`, `seats`, `zone`, `status`) VALUES
(1, 6, 'A', 'reserved'),
(2, 4, 'A', 'reserved'),
(3, 4, 'A', 'reserved'),
(4, 2, 'A', 'reserved'),
(5, 6, 'B', 'reserved'),
(6, 2, 'B', 'available'),
(7, 2, 'B', 'reserved'),
(8, 3, 'A', 'reserved');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `reservation_tables`
--
ALTER TABLE `reservation_tables`
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `table_id` (`table_id`);

--
-- Indeks untuk tabel `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tables`
--
ALTER TABLE `tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `reservation_tables`
--
ALTER TABLE `reservation_tables`
  ADD CONSTRAINT `reservation_tables_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`),
  ADD CONSTRAINT `reservation_tables_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
