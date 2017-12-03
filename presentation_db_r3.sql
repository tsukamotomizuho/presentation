-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2017 年 12 朁E03 日 12:40
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
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `voice_table`
--

CREATE TABLE IF NOT EXISTS `voice_table` (
`voice_id` int(128) NOT NULL,
  `slide_group` int(128) NOT NULL,
  `slide_now_num` int(12) NOT NULL,
  `voice_data` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(128) NOT NULL,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=214 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
MODIFY `slide_id` int(128) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=53;
--
-- AUTO_INCREMENT for table `voice_table`
--
ALTER TABLE `voice_table`
MODIFY `voice_id` int(128) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=214;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
