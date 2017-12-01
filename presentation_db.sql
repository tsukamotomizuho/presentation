-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2017 年 12 朁E01 日 12:51
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
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `slide_table`
--

INSERT INTO `slide_table` (`slide_id`, `slide_name`, `slide_num`, `slide_data`, `create_date`) VALUES
(65, 'テストスライド', 3, '/201712011244430_ae713146106ccf510ee3d9b0f1546efb.png/201712011244431_ae713146106ccf510ee3d9b0f1546efb.png/201712011244432_ae713146106ccf510ee3d9b0f1546efb.png', '2017-12-01 20:44:43');

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
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `voice_table`
--

INSERT INTO `voice_table` (`voice_id`, `slide_id`, `slide_now_num`, `voice_data`, `create_date`) VALUES
(182, 65, 3, '20171201124926_slide_id65_slide_num3_ae713146106ccf510ee3d9b0f1546efb.wav', '2017-12-01 20:49:26'),
(183, 65, 1, '20171201124932_slide_id65_slide_num1_ae713146106ccf510ee3d9b0f1546efb.wav', '2017-12-01 20:49:32'),
(184, 65, 2, '20171201124938_slide_id65_slide_num2_ae713146106ccf510ee3d9b0f1546efb.wav', '2017-12-01 20:49:38');

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
MODIFY `slide_id` int(12) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=66;
--
-- AUTO_INCREMENT for table `voice_table`
--
ALTER TABLE `voice_table`
MODIFY `voice_id` int(12) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=185;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
