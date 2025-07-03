-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 06, 2023 at 06:00 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tl_multi_vendor`
--

-- --------------------------------------------------------

--
-- Table structure for table `tl_com_category_has_commission`
--

CREATE TABLE `tl_com_category_has_commission` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `rate` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tl_com_seller_earning`
--

CREATE TABLE `tl_com_seller_earning` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_package_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `earning` double NOT NULL DEFAULT 0,
  `admin_commission` double NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 2,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tl_com_seller_followers`
--

CREATE TABLE `tl_com_seller_followers` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tl_com_seller_payout_info`
--

CREATE TABLE `tl_com_seller_payout_info` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `bank_name` varchar(150) NOT NULL,
  `bank_code` varchar(150) DEFAULT NULL,
  `account_name` varchar(150) NOT NULL,
  `account_holder_name` varchar(150) DEFAULT NULL,
  `account_number` varchar(150) DEFAULT NULL,
  `bank_routing_number` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tl_com_seller_payout_request`
--

CREATE TABLE `tl_com_seller_payout_request` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `amount` double DEFAULT NULL,
  `message` text DEFAULT NULL,
  `payment_method` int(11) DEFAULT NULL,
  `transaction_number` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 2,
  `payment_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tl_com_seller_shop`
--

CREATE TABLE `tl_com_seller_shop` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `seller_phone` varchar(100) DEFAULT NULL,
  `shop_slug` varchar(100) NOT NULL,
  `shop_phone` varchar(100) DEFAULT NULL,
  `shop_name` varchar(150) NOT NULL,
  `logo` int(11) DEFAULT NULL,
  `shop_banner` int(11) DEFAULT NULL,
  `shop_address` text DEFAULT NULL,
  `meta_title` varchar(220) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_image` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `tl_plugins` (`name`, `location`, `author`, `description`, `version`, `unique_indentifier`, `is_activated`, `namespace`, `url`) VALUES
('Multivendor', 'multivendor', 'Themelooks', 'Multivendor for Tlcommerce  Saas', '1.0.0', UUID(), 0, 'Plugin\\Multivendor\\', 'http://www.themelooks.com/');

--
-- Indexes for table `tl_com_category_has_commission`
--
ALTER TABLE `tl_com_category_has_commission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tl_com_seller_earning`
--
ALTER TABLE `tl_com_seller_earning`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_package_id` (`order_package_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `tl_com_seller_followers`
--
ALTER TABLE `tl_com_seller_followers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tl_com_seller_payout_info`
--
ALTER TABLE `tl_com_seller_payout_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tl_com_seller_payout_request`
--
ALTER TABLE `tl_com_seller_payout_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tl_com_seller_shop`
--
ALTER TABLE `tl_com_seller_shop`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tl_com_category_has_commission`
--
ALTER TABLE `tl_com_category_has_commission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `tl_com_seller_earning`
--
ALTER TABLE `tl_com_seller_earning`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tl_com_seller_followers`
--
ALTER TABLE `tl_com_seller_followers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tl_com_seller_payout_info`
--
ALTER TABLE `tl_com_seller_payout_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tl_com_seller_payout_request`
--
ALTER TABLE `tl_com_seller_payout_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tl_com_seller_shop`
--
ALTER TABLE `tl_com_seller_shop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tl_com_category_has_commission`
--
ALTER TABLE `tl_com_category_has_commission`
  ADD CONSTRAINT `tl_com_category_has_commission_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `tl_com_categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `tl_com_seller_earning`
--
ALTER TABLE `tl_com_seller_earning`
  ADD CONSTRAINT `tl_com_seller_earning_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `tl_com_orders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tl_com_seller_earning_ibfk_2` FOREIGN KEY (`order_package_id`) REFERENCES `tl_com_ordered_products` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tl_com_seller_earning_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `tl_com_products` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tl_com_seller_earning_ibfk_4` FOREIGN KEY (`seller_id`) REFERENCES `tl_users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tl_com_seller_shop`
--
ALTER TABLE `tl_com_seller_shop`
  ADD CONSTRAINT `tl_com_seller_shop_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `tl_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
