-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 23, 2025 at 05:50 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ayura_hampers`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `issue` varchar(255) NOT NULL,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `name`, `email`, `issue`, `submitted_at`) VALUES
(1, 'Ayush Vyas', 'ayush@gmail.com', 'Product not received', '2025-07-23 16:11:35'),
(2, 'Ayush Vyas', 'ayush@gmail.com', 'Damaged product', '2025-07-23 16:15:55');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `feedback` text NOT NULL,
  `submitted_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `replied` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `feedback`, `submitted_at`, `replied`) VALUES
(3, 12, 'hi', '2025-07-23 20:42:16', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `promo_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) NOT NULL DEFAULT 'Confirmed',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `email`, `name`, `address`, `payment_method`, `promo_code`, `discount_amount`, `total_amount`, `order_date`, `created_at`, `status`) VALUES
(1, 4, 'ayushvyas172@gmail.com', 'Ayush Vyas', 'J-404, avalon courtyard, ghodasar, 380050', 'COD', '', 0.00, 99.00, '2025-07-20 16:38:51', '2025-07-20 16:42:03', 'Confirmed'),
(2, 4, 'ayushvyas172@gmail.com', 'Ayush Vyas', 'J-404, avalon courtyard, ghodasar, 380050', 'COD', 'WELCOME100', 100.00, 890.00, '2025-07-20 16:43:21', '2025-07-20 16:43:21', 'Confirmed'),
(3, 3, 'ayushvyas172@gmail.com', 'Ayush Vyas', 'J-404, avalon courtyard, ghodasar, 380050', 'COD', '', 0.00, 99.00, '2025-07-21 20:12:21', '2025-07-21 20:12:21', 'Confirmed'),
(4, 15, 'ayushvyas1726@gmail.com', 'Ayush Ravin Vyas', 'J-404, AVALON COURTYARD-1, NEAR CADILA BRIDGE, GHODASAR, 380050', 'COD', 'WELCOME', 15.00, 84.00, '2025-07-23 23:09:48', '2025-07-23 23:09:48', 'Confirmed'),
(5, 15, 'ayushvyas1726@gmail.com', 'Ayush', 'J-404, AVALON COURTYARD-1, NEAR CADILA BRIDGE, GHODASAR, 380050', 'COD', 'WELCOME', 15.00, 183.00, '2025-07-23 23:12:31', '2025-07-23 23:12:31', 'Confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`, `total`) VALUES
(1, 1, 0, 'Chocolate Delight', 99.00, 1, 99.00),
(2, 2, 0, 'Chocolate Delight', 99.00, 5, 495.00),
(3, 2, 0, 'Luxury Festive Hamper', 99.00, 5, 495.00),
(4, 3, 0, 'Chocolate Delight', 99.00, 1, 99.00),
(5, 4, 0, 'Luxury Festive Hamper', 99.00, 1, 99.00),
(6, 5, 0, 'Luxury Festive Hamper', 99.00, 1, 99.00),
(7, 5, 0, 'Chocolate Delight', 99.00, 1, 99.00);

-- --------------------------------------------------------

--
-- Table structure for table `promo_codes`
--

DROP TABLE IF EXISTS `promo_codes`;
CREATE TABLE IF NOT EXISTS `promo_codes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `promo_code` varchar(50) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) NOT NULL,
  `expiry_date` date NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `promo_codes`
--

INSERT INTO `promo_codes` (`id`, `promo_code`, `discount_amount`, `min_order_amount`, `expiry_date`, `active`) VALUES
(1, 'WELCOME100', 100.00, 500.00, '2025-12-31', 1),
(2, 'FESTIVE50', 50.00, 300.00, '2025-08-15', 1),
(3, 'WELCOME', 15.00, 99.00, '2025-07-30', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `otp` varchar(6) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `is_verified`, `otp`, `otp_expires`) VALUES
(2, 'Ayush', 'ayushvyas172@gmail.com', '$2y$10$H5fDoGJ16JsfTGg6TCIqV.2XhxfykKBYOaB7Myv5rDPSq9hWnNQnS', 'admin', '2025-07-20 10:29:47', 0, NULL, NULL),
(12, 'Ayushh', 'vyasayush2601@gmail.com', '$2y$10$GjpZEyNmS0iyMNl8b1.dE.tzXy22hrGodfNP3/Ptz3it5Vtdssxh.', 'user', '2025-07-21 15:50:58', 0, NULL, NULL),
(13, 'Ayush', 'ayush@gmail.com', '$2y$10$/GBmlMQJmjXO7G5ONO4u9OPt9fmEqSNYkjkQnnrF.GzxmRa61QhSS', 'user', '2025-07-23 15:45:38', 0, NULL, NULL),
(15, 'Ayush', 'ayushvyas1726@gmail.com', '$2y$10$.jCVTiQCvpPvuwikrvsCz.GYCQUTtoOlNFJpgLqBXyn8uEOF8vcdu', 'user', '2025-07-23 17:38:40', 0, NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
