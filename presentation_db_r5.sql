-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2017 年 12 朁E22 日 15:34
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
-- テーブルの構造 `icon_table`
--

CREATE TABLE IF NOT EXISTS `icon_table` (
`icon_id` int(128) NOT NULL,
  `slide_group` int(128) NOT NULL,
  `slide_now_num` int(12) NOT NULL,
  `icon_start_time` int(128) NOT NULL,
  `icon_data` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(128) NOT NULL,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `icon_table`
--

INSERT INTO `icon_table` (`icon_id`, `slide_group`, `slide_now_num`, `icon_start_time`, `icon_data`, `user_id`, `create_date`) VALUES
(1, 0, 1, 2000, '20171222151459_slide_group0_slide_now_num1_icon_start_time2000_ae713146106ccf510ee3d9b0f1546efb.png', 1, '2017-12-22 23:14:59'),
(2, 0, 2, 1088, '20171222151517_slide_group0_slide_now_num2_icon_start_time1088_ae713146106ccf510ee3d9b0f1546efb.png', 1, '2017-12-22 23:15:17');

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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `slide_table`
--

INSERT INTO `slide_table` (`slide_id`, `slide_group`, `slide_name`, `slide_num`, `slide_now_num`, `slide_data`, `user_id`, `create_date`) VALUES
(33, 0, 'slide2', 3, 1, '20171222134323_slide_group0_slide_now_num1_ae713146106ccf510ee3d9b0f1546efb.png', 1, '2017-12-22 21:43:23'),
(34, 0, 'slide2', 3, 2, '20171221130942_slide_group0_slide_now_num2_ae713146106ccf510ee3d9b0f1546efb.png', 1, '2017-12-21 21:09:42'),
(35, 0, 'slide2', 3, 3, '20171222122820_slide_group0_slide_now_num3_ae713146106ccf510ee3d9b0f1546efb.png', 1, '2017-12-22 20:28:20');

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
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `voice_table`
--

INSERT INTO `voice_table` (`voice_id`, `slide_group`, `slide_now_num`, `voice_data`, `voice_time`, `user_id`, `create_date`) VALUES
(137, 0, 1, '20171222145856_slide_group0_slide_num1_ae713146106ccf510ee3d9b0f1546efb.wav', 8277, 1, '2017-12-22 22:58:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `icon_table`
--
ALTER TABLE `icon_table`
 ADD PRIMARY KEY (`icon_id`);

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
-- AUTO_INCREMENT for table `icon_table`
--
ALTER TABLE `icon_table`
MODIFY `icon_id` int(128) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `slide_table`
--
ALTER TABLE `slide_table`
MODIFY `slide_id` int(128) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=36;
--
-- AUTO_INCREMENT for table `voice_table`
--
ALTER TABLE `voice_table`
MODIFY `voice_id` int(128) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=138;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
