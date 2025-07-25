-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th7 25, 2025 lúc 06:25 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `wireless_world`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bill`
--

CREATE TABLE `bill` (
  `id` int(11) NOT NULL,
  `orderid` int(11) NOT NULL,
  `totalprice` decimal(10,2) NOT NULL CHECK (`totalprice` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `bill`
--

INSERT INTO `bill` (`id`, `orderid`, `totalprice`) VALUES
(4, 6, 1299.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `logo_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`id`, `name`, `logo_url`) VALUES
(1, 'Samsung', '../public/assets/images/samsung.png'),
(2, 'Apple', '../public/assets/images/apple.jpg'),
(3, 'Nokia', '../public/assets/images/nokia.png'),
(4, 'Huawei', '../public/assets/images/huawei.jpg'),
(5, 'OtherDevice', '../public/assets/images/otherdevices.jpg'),
(6, 'Xiaomi', '../public/assets/images/xiaomi.png'),
(7, 'Vertu', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `dateoforder` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','done','canceled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order`
--

INSERT INTO `order` (`id`, `userid`, `dateoforder`, `status`) VALUES
(6, 26, '2025-07-25 11:04:27', 'done');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orderdetail`
--

CREATE TABLE `orderdetail` (
  `id` int(11) NOT NULL,
  `orderid` int(11) NOT NULL,
  `productid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orderdetail`
--

INSERT INTO `orderdetail` (`id`, `orderid`, `productid`, `quantity`) VALUES
(8, 6, 110, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `is_top_selling` tinyint(1) DEFAULT 0,
  `is_best_budget` tinyint(1) DEFAULT 0,
  `is_best_offer` tinyint(1) DEFAULT 0,
  `type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `brand_id`, `name`, `price`, `image_url`, `features`, `is_top_selling`, `is_best_budget`, `is_best_offer`, `type`) VALUES
(61, 2, 'iPhone 16 Pro Max 256GB', 1199.00, '../public/assets/images/apple/iphone/iphone_16_pro_max_white.jpg', 'A18 Pro chip, 6.9\" Super Retina XDR, 48MP Fusion camera, 5G', 1, 0, 0, NULL),
(62, 2, 'iPhone 16 Pro 128GB', 999.00, '../public/assets/images/apple/iphone/iphone_16_pro_white.jpg', 'A18 chip, 6.3\" Super Retina XDR, 48MP camera, 5G', 1, 0, 1, NULL),
(63, 2, 'iPhone 16 256GB', 899.00, '../public/assets/images/apple/iphone/iphone_16_purple.jpg', 'A18 chip, 6.1\" Super Retina XDR, 48MP camera, 5G', 0, 1, 0, NULL),
(64, 2, 'iPhone 16 Plus 128GB', 899.00, '../public/assets/images/iphone16_plus.jpg', 'A18 chip, 6.7\" Super Retina XDR, 48MP camera, 5G', 0, 0, 1, NULL),
(65, 2, 'iPhone 15 Pro Max 512GB', 1399.00, '../public/assets/images/iphone15_pro_max.jpg', 'A17 Pro chip, 6.7\" Super Retina XDR, 48MP camera, USB-C', 1, 0, 0, NULL),
(66, 2, 'iPhone 15 Pro 256GB', 1099.00, '../public/assets/images/iphone15_pro.jpg', 'A17 Pro chip, 6.1\" Super Retina XDR, 48MP camera, USB-C', 1, 0, 0, NULL),
(67, 2, 'iPhone 15 128GB', 799.00, '../public/assets/images/iphone15.jpg', 'A16 Bionic chip, 6.1\" Super Retina XDR, 48MP camera, USB-C', 0, 1, 0, NULL),
(68, 2, 'iPhone 15 Plus 256GB', 899.00, '../public/assets/images/iphone15_plus.jpg', 'A16 Bionic chip, 6.7\" Super Retina XDR, 48MP camera, USB-C', 0, 0, 1, NULL),
(69, 2, 'iPhone 14 Pro Max 1TB', 1599.00, '../public/assets/images/iphone14_pro_max.jpg', 'A16 Bionic chip, 6.7\" Super Retina XDR, 48MP camera, Dynamic Island', 1, 0, 0, NULL),
(70, 2, 'iPhone 14 Pro 256GB', 1099.00, '../public/assets/images/iphone14_pro.jpg', 'A16 Bionic chip, 6.1\" Super Retina XDR, 48MP camera, Dynamic Island', 1, 0, 0, NULL),
(71, 2, 'iPhone 14 128GB', 699.00, '../public/assets/images/iphone14.jpg', 'A15 Bionic chip, 6.1\" Super Retina XDR, 12MP camera, 5G', 0, 1, 0, NULL),
(72, 2, 'iPhone 14 Plus 256GB', 799.00, '../public/assets/images/iphone14_plus.jpg', 'A15 Bionic chip, 6.7\" Super Retina XDR, 12MP camera, 5G', 0, 0, 1, NULL),
(73, 2, 'iPhone 13 Pro Max 512GB', 1299.00, '../public/assets/images/iphone13_pro_max.jpg', 'A15 Bionic chip, 6.7\" Super Retina XDR, 12MP Pro camera, 5G', 0, 0, 0, NULL),
(74, 2, 'iPhone 13 Pro 256GB', 999.00, '../public/assets/images/iphone13_pro.jpg', 'A15 Bionic chip, 6.1\" Super Retina XDR, 12MP Pro camera, 5G', 0, 0, 0, NULL),
(75, 2, 'iPhone 13 128GB', 599.00, '../public/assets/images/iphone13.jpg', 'A15 Bionic chip, 6.1\" Super Retina XDR, 12MP camera, 5G', 0, 1, 0, NULL),
(76, 2, 'iPhone 13 Mini 256GB', 699.00, '../public/assets/images/iphone13_mini.jpg', 'A15 Bionic chip, 5.4\" Super Retina XDR, 12MP camera, 5G', 0, 0, 1, NULL),
(77, 2, 'iPhone 12 Pro Max 256GB', 1099.00, '../public/assets/images/iphone12_pro_max.jpg', 'A14 Bionic chip, 6.7\" Super Retina XDR, 12MP Pro camera, 5G', 0, 0, 0, NULL),
(78, 2, 'iPhone 12 Pro 128GB', 899.00, '../public/assets/images/iphone12_pro.jpg', 'A14 Bionic chip, 6.1\" Super Retina XDR, 12MP Pro camera, 5G', 0, 0, 0, NULL),
(79, 2, 'iPhone 12 64GB', 599.00, '../public/assets/images/iphone12.jpg', 'A14 Bionic chip, 6.1\" Super Retina XDR, 12MP camera, 5G', 0, 1, 0, NULL),
(80, 2, 'iPhone 12 Mini 128GB', 649.00, '../public/assets/images/iphone12_mini.jpg', 'A14 Bionic chip, 5.4\" Super Retina XDR, 12MP camera, 5G', 0, 0, 1, NULL),
(81, 2, 'iPad Pro 11\" M4 256GB', 999.00, '../public/assets/images/ipad_pro_11_m4.jpg', 'M4 chip, 11\" Ultra Retina XDR, Apple Pencil Pro support, Wi-Fi 6E', 1, 0, 0, NULL),
(82, 2, 'iPad Pro 13\" M4 512GB', 1499.00, '../public/assets/images/ipad_pro_13_m4.jpg', 'M4 chip, 13\" Ultra Retina XDR, Apple Pencil Pro support, Wi-Fi 6E', 1, 0, 0, NULL),
(83, 2, 'iPad Air 11\" M2 128GB', 599.00, '../public/assets/images/ipad_air_11_m2.jpg', 'M2 chip, 11\" Liquid Retina, Apple Pencil support, Wi-Fi 6', 0, 1, 0, NULL),
(84, 2, 'iPad Air 13\" M2 256GB', 799.00, '../public/assets/images/ipad_air_13_m2.jpg', 'M2 chip, 13\" Liquid Retina, Apple Pencil support, Wi-Fi 6', 0, 0, 1, NULL),
(85, 2, 'iPad 10th Gen 64GB', 349.00, '../public/assets/images/ipad_10th_gen.jpg', 'A14 Bionic chip, 10.9\" Liquid Retina, USB-C, Wi-Fi 6', 0, 1, 0, NULL),
(86, 2, 'iPad 10th Gen 256GB', 499.00, '../public/assets/images/ipad_10th_gen_256.jpg', 'A14 Bionic chip, 10.9\" Liquid Retina, USB-C, Wi-Fi 6', 0, 0, 1, NULL),
(87, 2, 'iPad Mini 6 64GB', 499.00, '../public/assets/images/ipad_mini_6.jpg', 'A15 Bionic chip, 8.3\" Liquid Retina, Apple Pencil 2 support, 5G', 0, 1, 0, NULL),
(88, 2, 'iPad Mini 6 256GB', 649.00, '../public/assets/images/ipad_mini_6_256.jpg', 'A15 Bionic chip, 8.3\" Liquid Retina, Apple Pencil 2 support, 5G', 0, 0, 1, NULL),
(89, 2, 'iPad Pro 12.9\" M2 128GB', 1099.00, '../public/assets/images/ipad_pro_12_9_m2.jpg', 'M2 chip, 12.9\" Liquid Retina XDR, Apple Pencil 2 support, Wi-Fi 6E', 1, 0, 0, NULL),
(90, 2, 'iPad Air 5th Gen 256GB', 749.00, '../public/assets/images/ipad_air_5th_gen.jpg', 'M1 chip, 10.9\" Liquid Retina, Apple Pencil 2 support, 5G', 0, 0, 1, NULL),
(91, 2, 'AirPods Pro 2 USB-C', 249.00, '../public/assets/images/airpods_pro_2.jpg', 'H2 chip, Active Noise Cancellation, USB-C charging, Spatial Audio', 1, 0, 0, NULL),
(92, 2, 'AirPods 4 ANC', 179.00, '../public/assets/images/airpods_4_anc.jpg', 'H2 chip, Active Noise Cancellation, Transparency Mode, USB-C', 0, 1, 0, NULL),
(93, 2, 'AirPods 4', 129.00, '../public/assets/images/airpods_4.jpg', 'H2 chip, Personalized Spatial Audio, USB-C charging', 0, 1, 1, NULL),
(94, 2, 'AirPods Max Space Gray', 549.00, '../public/assets/images/airpods_max_space_gray.jpg', 'H1 chip, Active Noise Cancellation, Spatial Audio, 20h battery', 1, 0, 0, NULL),
(95, 2, 'AirPods Max Pink', 549.00, '../public/assets/images/airpods_max_pink.jpg', 'H1 chip, Active Noise Cancellation, Spatial Audio, 20h battery', 0, 0, 0, NULL),
(97, 2, 'AirPods Pro 1st Gen', 229.00, '../public/assets/images/airpods_pro_1.jpg', 'H1 chip, Active Noise Cancellation, Spatial Audio, Lightning', 0, 0, 1, NULL),
(98, 2, 'AirPods Max Silver', 549.00, '../public/assets/images/airpods_max_silver.jpg', 'H1 chip, Active Noise Cancellation, Spatial Audio, 20h battery', 0, 0, 0, NULL),
(99, 2, 'Apple Watch Series 9 41mm', 399.00, '../public/assets/images/apple_watch_series_9_41mm.jpg', 'S9 chip, Retina Display, ECG, Blood Oxygen, 18h battery', 1, 0, 0, NULL),
(100, 2, 'Apple Watch Series 9 45mm', 429.00, '../public/assets/images/apple_watch_series_9_45mm.jpg', 'S9 chip, Retina Display, ECG, Blood Oxygen, 18h battery', 1, 0, 0, NULL),
(101, 2, 'Apple Watch Ultra 2', 799.00, '../public/assets/images/apple_watch_ultra_2.jpg', 'S9 chip, 49mm Retina Display, GPS + Cellular, 36h battery', 1, 0, 0, NULL),
(102, 2, 'Apple Watch SE 40mm', 249.00, '../public/assets/images/apple_watch_se_40mm.jpg', 'S8 chip, Retina Display, Crash Detection, 18h battery', 0, 1, 0, NULL),
(103, 2, 'Apple Watch SE 44mm', 279.00, '../public/assets/images/apple_watch_se_44mm.jpg', 'S8 chip, Retina Display, Crash Detection, 18h battery', 0, 1, 1, NULL),
(104, 2, 'Apple Watch Series 8 41mm', 349.00, '../public/assets/images/apple_watch_series_8_41mm.jpg', 'S8 chip, Retina Display, ECG, Blood Oxygen, 18h battery', 0, 0, 1, NULL),
(105, 2, 'Apple Watch Series 8 45mm', 379.00, '../public/assets/images/apple_watch_series_8_45mm.jpg', 'S8 chip, Retina Display, ECG, Blood Oxygen, 18h battery', 0, 0, 0, NULL),
(106, 2, 'MacBook Air M3 13\" 256GB', 1099.00, '../public/assets/images/macbook_air_m3_13.jpg', 'M3 chip, 8GB RAM, 256GB SSD, 13.6\" Liquid Retina', 1, 0, 0, NULL),
(107, 2, 'MacBook Air M2 13\" 256GB', 999.00, '../public/assets/images/macbook_air_m2_13.jpg', 'M2 chip, 8GB RAM, 256GB SSD, 13.6\" Liquid Retina', 0, 1, 0, NULL),
(108, 2, 'MacBook Pro M3 14\" 512GB', 1599.00, '../public/assets/images/macbook_pro_m3_14.jpg', 'M3 chip, 8GB RAM, 512GB SSD, 14.2\" Liquid Retina XDR', 1, 0, 0, NULL),
(109, 2, 'MacBook Pro M2 Pro 16\" 512GB', 2499.00, '../public/assets/images/macbook_pro_m2_pro_16.jpg', 'M2 Pro chip, 16GB RAM, 512GB SSD, 16.2\" Liquid Retina XDR', 1, 0, 0, NULL),
(110, 2, 'MacBook Air M2 15\" 256GB', 1299.00, '../public/assets/images/macbook_air_m2_15.jpg', 'M2 chip, 8GB RAM, 256GB SSD, 15.3\" Liquid Retina', 0, 0, 1, NULL),
(111, 7, 'Vertu1', 2000.00, '', NULL, 0, 0, 0, 'phone');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_registered` tinyint(1) DEFAULT 1,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `is_registered`, `email`, `phone`, `role`) VALUES
(10, 'aptech', '$2y$10$zPCxHqZm7j9rYOSZqaChweyOQFbLVWYfrsU38oGisfEM2dpiFQmoq', 1, '', '0845123698', 'admin'),
(25, 'user2', '$2y$10$MDRGKmABpxFoV0B73eo4N.u6YzIrH71ASec9jrlwv/aYhzdUWXvsq', 1, NULL, '0215487690', 'user'),
(26, 'user', '$2y$10$HOQczogby4ufGs.KmEsO4OrPbpNXzlLdL9uKnsCRTIgfWaj5PxF3a', 1, NULL, '0975678904', 'user');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `visitors`
--

CREATE TABLE `visitors` (
  `id` int(11) NOT NULL,
  `visit_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bill`
--
ALTER TABLE `bill`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orderid` (`orderid`);

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`);

--
-- Chỉ mục cho bảng `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orderid` (`orderid`),
  ADD KEY `productid` (`productid`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bill`
--
ALTER TABLE `bill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `orderdetail`
--
ALTER TABLE `orderdetail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bill`
--
ALTER TABLE `bill`
  ADD CONSTRAINT `bill_ibfk_1` FOREIGN KEY (`orderid`) REFERENCES `order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD CONSTRAINT `orderdetail_ibfk_1` FOREIGN KEY (`orderid`) REFERENCES `order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orderdetail_ibfk_2` FOREIGN KEY (`productid`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
