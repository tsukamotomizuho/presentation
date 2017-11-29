-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2017 年 11 朁E29 日 11:53
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
`slide_id` int(12) NOT NULL,
  `slide_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `slide_num` int(128) NOT NULL,
  `slide_data` text COLLATE utf8_unicode_ci NOT NULL,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `slide_table`
--

INSERT INTO `slide_table` (`slide_id`, `slide_name`, `slide_num`, `slide_data`, `create_date`) VALUES
(55, 'テストスライド', 1, '/201711281258130_ae713146106ccf510ee3d9b0f1546efb.', '2017-11-28 20:58:13'),
(56, 'テストスライド', 4, '/201711281300090_ae713146106ccf510ee3d9b0f1546efb.jpg/201711281300091_ae713146106ccf510ee3d9b0f1546efb.jpg/201711281300092_ae713146106ccf510ee3d9b0f1546efb.jpg/201711281300093_ae713146106ccf510ee3d9b0f1546efb.jpg', '2017-11-28 21:00:09'),
(57, 'テストスライド', 3, '/201711281301060_ae713146106ccf510ee3d9b0f1546efb.png/201711281301061_ae713146106ccf510ee3d9b0f1546efb.png/201711281301062_ae713146106ccf510ee3d9b0f1546efb.png', '2017-11-28 21:01:06');

-- --------------------------------------------------------

--
-- テーブルの構造 `voice_table`
--

CREATE TABLE IF NOT EXISTS `voice_table` (
`voice_id` int(12) NOT NULL,
  `slide_id` int(12) NOT NULL,
  `slide_now_num` int(12) NOT NULL,
  `voice_data` text COLLATE utf8_unicode_ci NOT NULL,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `voice_table`
--

INSERT INTO `voice_table` (`voice_id`, `slide_id`, `slide_now_num`, `voice_data`, `create_date`) VALUES
(132, 56, 1, '20171128130113_slide_id56_slide_num1_ae713146106ccf510ee3d9b0f1546efb.wav', '2017-11-28 21:01:13'),
(133, 56, 2, '20171128130117_slide_id56_slide_num2_ae713146106ccf510ee3d9b0f1546efb.wav', '2017-11-28 21:01:17'),
(134, 56, 3, '20171128130120_slide_id56_slide_num3_ae713146106ccf510ee3d9b0f1546efb.wav', '2017-11-28 21:01:20');

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
MODIFY `slide_id` int(12) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=58;
--
-- AUTO_INCREMENT for table `voice_table`
--
ALTER TABLE `voice_table`
MODIFY `voice_id` int(12) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=135;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
