-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2026 at 05:02 PM
-- Server version: 10.11.16-MariaDB
-- PHP Version: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `security-project`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `initiative_id` int(10) UNSIGNED DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `comment_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `initiative_id`, `username`, `role`, `comment_text`, `created_at`) VALUES
(1, 6, 'amna', 'admin', 'مبادرة جميلة أشجع للاشتراك فيها . قم بزيارة الموقع الرسمي للاشتراك', '2026-11-21 11:19:54'),
(7, 6, 'nora', 'user', 'جميل', '2026-11-21 11:32:47'),
(9, 6, 'amna', 'admin', 'sdff', '2026-11-21 13:48:11');

-- --------------------------------------------------------

--
-- Table structure for table `initiatives`
--

CREATE TABLE `initiatives` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `how_to_contribute` text NOT NULL,
  `source_text` text NOT NULL,
  `source_link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `initiatives`
--

INSERT INTO `initiatives` (`id`, `category`, `title`, `description`, `how_to_contribute`, `source_text`, `source_link`) VALUES
(1, 'البيئة', 'التشجير وزيادة المساحات الخضراء', 'تعزيز الغطاء النباتي والمحافظة على الأشجار في الأحياء والمساحات العامة.', 'المحافظة على الأشجار القائمة وعدم إتلافها.\r\nزراعة النباتات المناسبة للمناخ المحلي.\r\nالعناية بالأشجار والمساحات الخضراء.\r\nنشر الوعي بأهمية التشجير وحماية البيئة.', 'يمكنك التعرف على برامج التشجير من خلال المركز الوطني لتنمية الغطاء النباتي ومكافحة التصحر.', 'https://ncvc.gov.sa/ar/Pages/default.aspx'),
(5, 'المياه', 'ترشيد استهلاك المياه', 'ترشيد المياه سلوك مستدام يساهم في المحافظة على الموارد المائية وتقليل الهدر.', 'إغلاق الصنبور عند عدم الحاجة.\r\nإصلاح التسريبات والإبلاغ عنها عند ملاحظتها.\r\nاستخدام الأدوات والأجهزة الموفرة للمياه.\r\nتقليل استهلاك المياه أثناء غسل الأدوات والري.\r\nنشر الوعي بأهمية المحافظة على المياه.', 'قدم شركة المياه الوطنية خدمات ومعلومات تساعد على متابعة الاستهلاك والتعامل مع تسريبات المياه.', 'https://www.nwc.com.sa/'),
(6, 'النفايات', 'إعادة التدوير وفرز النفايات', 'يساعد فرز المواد وإعادة تدويرها على تقليل النفايات والاستفادة من الموارد.', 'فصل الورق والبلاستيك والمعادن عن النفايات الأخرى.\r\nاستخدام الحاويات المخصصة لإعادة التدوير.\r\nإعادة استخدام المواد التي ما زالت صالحة.\r\nتنظيف العبوات القابلة للتدوير عند الحاجة.\r\nتسليم المواد القابلة للتدوير إلى نقاط الجمع المتاحة.', 'يمكنك التعرف على إدارة النفايات وإعادة التدوير من خلال المركز الوطني لإدارة النفايات (موان)', 'https://mwan.gov.sa/'),
(9, 'البيئة', 'jjh', 'drtfyguhij', 'xrcytv\r\ndfghjkl', 'dtfyguhijok', 'https://www.nwc.com.sa/');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'amna', '$2y$12$Omv.r0lUZaSypak1yZAFK.9eUaLd9rq4OI8AnOdyffiZxWVJ08PJq', 'admin'),
(2, 'nora', '$2y$12$FF//91dH4tbYICV4mK/b8OzJqnsf8M36dR8G3PYa/MF4xo2f.NYDO', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_comments_initiative_id` (`initiative_id`);

--
-- Indexes for table `initiatives`
--
ALTER TABLE `initiatives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `initiatives`
--
ALTER TABLE `initiatives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
