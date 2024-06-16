-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2024 at 06:22 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sub4sub`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_content`
--

CREATE TABLE `about_content` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `about_content`
--

INSERT INTO `about_content` (`id`, `content`, `updated_at`) VALUES
(1, '<h1>About Us</h1>\r\n\r\n<p>Welcome to our website. Here is some information about us. New About us</p>\r\n', '2024-06-15 17:53:43');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$2jsbwERFHcEXqpK23ZN/l..bbsSB0Udoj6BOLAP9S1TyEbChDE6h6', '2024-06-15 17:05:22');

-- --------------------------------------------------------

--
-- Table structure for table `contact_content`
--

CREATE TABLE `contact_content` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contact_content`
--

INSERT INTO `contact_content` (`id`, `content`, `updated_at`) VALUES
(1, '<h1>Contact Us</h1>\r\n\r\n<p>Here is how you can contact us. New details!!</p>\r\n', '2024-06-15 17:54:22');

-- --------------------------------------------------------

--
-- Table structure for table `faq_content`
--

CREATE TABLE `faq_content` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `privacy_content`
--

CREATE TABLE `privacy_content` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `privacy_content`
--

INSERT INTO `privacy_content` (`id`, `content`, `updated_at`) VALUES
(1, '<h1>Privacy Policy</h1>\r\n\r\n<p>Here is our privacy policy. new details loaded!!</p>\r\n', '2024-06-15 17:55:01');

-- --------------------------------------------------------

--
-- Table structure for table `tos_content`
--

CREATE TABLE `tos_content` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tos_content`
--

INSERT INTO `tos_content` (`id`, `content`, `updated_at`) VALUES
(1, '<h1>Terms of Service</h1>\r\n\r\n<p>Here are our terms of service loading..</p>\r\n', '2024-06-15 17:55:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `location_address` varchar(255) NOT NULL,
  `youtube_channel` varchar(255) NOT NULL,
  `youtube_channel_name` varchar(255) NOT NULL,
  `youtube_channel_changed` tinyint(1) DEFAULT '0',
  `subscription_urls` text,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `full_name`, `location_address`, `youtube_channel`, `youtube_channel_name`, `youtube_channel_changed`, `subscription_urls`, `profile_picture`, `created_at`) VALUES
(1, 'hiranmayroy@proton.me', '$2y$10$vd1mdOMM/9h9yZ/tZl8qw.tGWgwwe.eZlsPs54h5zLXr90R0FhZpm', '', '', '', 'Zendria_X', 0, '{\"0\":\"https:\\/\\/roydigitalnexus.com\\/admin\\/uploads\\/guests\",\"1\":\"https:\\/\\/roydigitalnexus.com\\/admin\\/\",\"2\":\"https:\\/\\/roydigitalnexus.com\\/admin\\/dashboard\\/auoload\",\"3\":\"1. 1https:\\/\\/roydigitalnexus.com\\/admin\\/uploads\\/guests\",\"4\":\"2. https:\\/\\/roydigitalnexus.com\\/admin\\/\",\"5\":\"3. https:\\/\\/roydigitalnexus.com\\/admin\\/dashboard\\/auoload\",\"6\":\"4. 1https:\\/\\/roydigitalnexus.com\\/admin\\/uploads\\/guests\",\"7\":\"5. https:\\/\\/roydigitalnexus.com\\/admin\\/\",\"8\":\"6. https:\\/\\/roydigitalnexus.com\\/admin\\/dashboard\\/auoload\",\"9\":\"1. https:\\/\\/roydigitalnexus.com\\/admin\\/uploads\\/guests\",\"15\":\"7. https:\\/\\/roydigitalnexus.com\\/admin\\/uploads\\/guests\",\"16\":\"8. https:\\/\\/roydigitalnexus.com\\/admin\\/\",\"17\":\"9. https:\\/\\/roydigitalnexus.com\\/admin\\/dashboard\\/auoload\"}', NULL, '2024-06-15 10:01:45'),
(2, 'user2@gmail.com', '$2y$10$4FJzGtubQRFyE7XV8hPntO.m5alNhOeotDPwLZUrstdTdJWJn9eAO', '', '', '', 'HelloWorld', 0, '[\"1. https:\\/\\/roydigitalnexus.com\\/1.png\",\"2. https:\\/\\/roydigitalnexus.com\\/2.png\",\"3. https:\\/\\/roydigitalnexus.com\\/4.png\"]', NULL, '2024-06-15 16:39:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_content`
--
ALTER TABLE `about_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `contact_content`
--
ALTER TABLE `contact_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq_content`
--
ALTER TABLE `faq_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `privacy_content`
--
ALTER TABLE `privacy_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tos_content`
--
ALTER TABLE `tos_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_content`
--
ALTER TABLE `about_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_content`
--
ALTER TABLE `contact_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faq_content`
--
ALTER TABLE `faq_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `privacy_content`
--
ALTER TABLE `privacy_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tos_content`
--
ALTER TABLE `tos_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
