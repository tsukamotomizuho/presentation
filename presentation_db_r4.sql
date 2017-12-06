-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2017 年 12 朁E06 日 14:43
-- サーバのバージョン： 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `presentation_db`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `slide_table`
--

CREATE TABLE IF NOT EXISTS `slide_table` (
`slide_id` int(128) NOT NULL,
  `slide_group` int(128) NOT NULL,
  `slide_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `slide_num` int(128) NOT NULL,
  `slide_now_num` int(12) NOT NULL,
  `slide_data` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(128) NOT NULL,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `slide_table`
--

INSERT INTO `slide_table` (`slide_id`, `slide_group`, `slide_name`, `slide_num`, `slide_now_num`, `slide_data`, `user_id`, `create_date`) VALUES
(1, 0, 'slide2', 3, 1, '20171205012155_slide_group0_slide_now_num1_ae713146106ccf510ee3d9b0f1546efb.png', 1, '2017-12-05 09:21:55'),
(2, 0, 'slide2', 3, 2, '20171205012155_slide_group0_slide_now_num2_ae713146106ccf510ee3d9b0f1546efb.png', 1, '2017-12-05 09:21:55'),
(3, 0, 'slide2', 3, 3, '20171205012155_slide_group0_slide_now_num3_ae713146106ccf510ee3d9b0f1546efb.png', 1, '2017-12-05 09:21:56'),
(4, 1, 'slide2', 3, 1, '20171205012251_slide_group1_slide_now_num1_ae713146106ccf510ee3d9b0f1546efb.png', 1, '2017-12-05 09:22:51'),
(5, 1, 'slide2', 3, 2, '20171205012251_slide_group1_slide_now_num2_ae713146106ccf510ee3d9b0f1546efb.png', 1, '2017-12-05 09:22:51'),
(6, 1, 'slide2', 3, 3, '20171205012251_slide_group1_slide_now_num3_ae713146106ccf510ee3d9b0f1546efb.png', 1, '2017-12-05 09:22:51'),
(7, 2, 'slide2', 3, 1, '20171205045210_slide_group2_slide_now_num1_ae713146106ccf510ee3d9b0f1546efb.png', 1, '2017-12-05 12:52:10'),
(8, 2, 'slide2', 3, 2, '20171205045210_slide_group2_slide_now_num2_ae713146106ccf510ee3d9b0f1546efb.png', 1, '2017-12-05 12:52:10'),
(9, 2, 'slide2', 3, 3, '20171205045210_slide_group2_slide_now_num3_ae713146106ccf510ee3d9b0f1546efb.png', 1, '2017-12-05 12:52:10');

-- --------------------------------------------------------

--
-- テーブルの構造 `voice_table`
--

CREATE TABLE IF NOT EXISTS `voice_table` (
`voice_id` int(128) NOT NULL,
  `slide_group` int(128) NOT NULL,
  `slide_now_num` int(12) NOT NULL,
  `voice_data` text COLLATE utf8_unicode_ci NOT NULL,
  `voice_time` int(128) NOT NULL,
  `user_id` int(128) NOT NULL,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `voice_table`
--

INSERT INTO `voice_table` (`voice_id`, `slide_group`, `slide_now_num`, `voice_data`, `voice_time`, `user_id`, `create_date`) VALUES
(1, 1, 1, '20171205012342_slide_group1_slide_num1_ae713146106ccf510ee3d9b0f1546efb.wav', 0, 1, '2017-12-05 09:23:42'),
(2, 1, 2, '20171205012349_slide_group1_slide_num2_ae713146106ccf510ee3d9b0f1546efb.wav', 0, 1, '2017-12-05 09:23:49'),
(3, 1, 3, '20171205012354_slide_group1_slide_num3_ae713146106ccf510ee3d9b0f1546efb.wav', 0, 1, '2017-12-05 09:23:54'),
(4, 2, 1, '20171205045216_slide_group2_slide_num1_ae713146106ccf510ee3d9b0f1546efb.wav', 0, 1, '2017-12-05 12:52:16'),
(5, 2, 2, '20171205045226_slide_group2_slide_num2_ae713146106ccf510ee3d9b0f1546efb.wav', 0, 1, '2017-12-05 12:52:26'),
(6, 2, 3, '20171205045230_slide_group2_slide_num3_ae713146106ccf510ee3d9b0f1546efb.wav', 0, 1, '2017-12-05 12:52:30'),
(7, 2, 1, '20171205085059_slide_group2_slide_num1_ae713146106ccf510ee3d9b0f1546efb.wav', 0, 1, '2017-12-05 16:50:59'),
(8, 2, 1, '20171206120141_slide_group2_slide_num1_ae713146106ccf510ee3d9b0f1546efb.wav', 0, 1, '2017-12-06 20:01:41'),
(9, 2, 1, '20171206120448_slide_group2_slide_num1_ae713146106ccf510ee3d9b0f1546efb.wav', 4, 1, '2017-12-06 20:04:48'),
(10, 2, 1, '20171206120556_slide_group2_slide_num1_ae713146106ccf510ee3d9b0f1546efb.wav', 3499, 1, '2017-12-06 20:05:56'),
(11, 2, 2, '20171206122100_slide_group2_slide_num2_ae713146106ccf510ee3d9b0f1546efb.wav', 3584, 1, '2017-12-06 20:21:00'),
(12, 2, 3, '20171206122105_slide_group2_slide_num3_ae713146106ccf510ee3d9b0f1546efb.wav', 2389, 1, '2017-12-06 20:21:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `slide_table`
--
ALTER TABLE `slide_table`
 ADD PRIMARY KEY (`slide_id`);

--
-- Indexes for table `voice_table`
--
ALTER TABLE `voice_table`
 ADD PRIMARY KEY (`voice_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `slide_table`
--
ALTER TABLE `slide_table`
MODIFY `slide_id` int(128) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `voice_table`
--
ALTER TABLE `voice_table`
MODIFY `voice_id` int(128) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
