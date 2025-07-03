-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 12, 2023 at 12:22 PM
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
-- Database: `cmslooks_builder`
--

-- --------------------------------------------------------

--
-- Table structure for table `page_builder_sections`
--
CREATE TABLE `page_builder_sections` (
  `id` int(11) NOT NULL,
  `page_id` bigint(20) UNSIGNED NOT NULL,
  `theme_id` int(11) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `page_builder_sections_layout_widget_properties`
--

CREATE TABLE `page_builder_sections_layout_widget_properties` (
  `id` int(11) NOT NULL,
  `layout_has_widget_id` int(11) DEFAULT NULL,
  `properties` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `page_builder_sections_properties`
--

CREATE TABLE `page_builder_sections_properties` (
  `id` int(11) NOT NULL,
  `section_id` int(11) DEFAULT NULL,
  `properties` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `page_builder_section_layouts`
--

CREATE TABLE `page_builder_section_layouts` (
  `id` int(11) NOT NULL,
  `section_id` int(11) DEFAULT NULL,
  `col_index` int(3) DEFAULT NULL,
  `col_value` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `page_builder_section_layout_widgets`
--

CREATE TABLE `page_builder_section_layout_widgets` (
  `id` int(11) NOT NULL,
  `section_layout_id` int(11) DEFAULT NULL,
  `page_widget_id` int(11) DEFAULT NULL,
  `serial` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `page_builder_widgets`
--

CREATE TABLE `page_builder_widgets` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `theme_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `page_builder_widget_translations`
--

CREATE TABLE `page_builder_widget_translations` (
  `id` int(11) NOT NULL,
  `lang` varchar(150) DEFAULT NULL,
  `layout_widget_properties_id` int(11) DEFAULT NULL,
  `properties` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `page_builder_sections`
--
ALTER TABLE `page_builder_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_id` (`page_id`);

--
-- Indexes for table `page_builder_sections_layout_widget_properties`
--
ALTER TABLE `page_builder_sections_layout_widget_properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_has_widget_id` (`layout_has_widget_id`);

--
-- Indexes for table `page_builder_sections_properties`
--
ALTER TABLE `page_builder_sections_properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `page_builder_section_layouts`
--
ALTER TABLE `page_builder_section_layouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `page_builder_section_layout_widgets`
--
ALTER TABLE `page_builder_section_layout_widgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `section_layout_id` (`section_layout_id`),
  ADD KEY `page_widget_id` (`page_widget_id`);

--
-- Indexes for table `page_builder_widgets`
--
ALTER TABLE `page_builder_widgets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_builder_widget_translations`
--
ALTER TABLE `page_builder_widget_translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `layout_widget_properties_id` (`layout_widget_properties_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `page_builder_sections`
--
ALTER TABLE `page_builder_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `page_builder_sections_layout_widget_properties`
--
ALTER TABLE `page_builder_sections_layout_widget_properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=212;

--
-- AUTO_INCREMENT for table `page_builder_sections_properties`
--
ALTER TABLE `page_builder_sections_properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `page_builder_section_layouts`
--
ALTER TABLE `page_builder_section_layouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=302;

--
-- AUTO_INCREMENT for table `page_builder_section_layout_widgets`
--
ALTER TABLE `page_builder_section_layout_widgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=317;

--
-- AUTO_INCREMENT for table `page_builder_widgets`
--
ALTER TABLE `page_builder_widgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `page_builder_widget_translations`
--
ALTER TABLE `page_builder_widget_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `page_builder_sections`
--
ALTER TABLE `page_builder_sections`
  ADD CONSTRAINT `page_builder_sections_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `tl_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `page_builder_sections_layout_widget_properties`
--
ALTER TABLE `page_builder_sections_layout_widget_properties`
  ADD CONSTRAINT `page_builder_sections_layout_widget_properties_ibfk_1` FOREIGN KEY (`layout_has_widget_id`) REFERENCES `page_builder_section_layout_widgets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `page_builder_sections_properties`
--
ALTER TABLE `page_builder_sections_properties`
  ADD CONSTRAINT `page_builder_sections_properties_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `page_builder_sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `page_builder_section_layouts`
--
ALTER TABLE `page_builder_section_layouts`
  ADD CONSTRAINT `page_builder_section_layouts_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `page_builder_sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `page_builder_section_layout_widgets`
--
ALTER TABLE `page_builder_section_layout_widgets`
  ADD CONSTRAINT `page_builder_section_layout_widgets_ibfk_1` FOREIGN KEY (`section_layout_id`) REFERENCES `page_builder_section_layouts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `page_builder_section_layout_widgets_ibfk_2` FOREIGN KEY (`page_widget_id`) REFERENCES `page_builder_widgets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `page_builder_widget_translations`
--
ALTER TABLE `page_builder_widget_translations`
  ADD CONSTRAINT `page_builder_widget_translations_ibfk_1` FOREIGN KEY (`layout_widget_properties_id`) REFERENCES `page_builder_sections_layout_widget_properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
