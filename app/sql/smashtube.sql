-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 04, 2017 at 07:53 AM
-- Server version: 5.7.19
-- PHP Version: 7.1.9

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smashtube`
--
CREATE DATABASE IF NOT EXISTS `smashtube` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `smashtube`;

-- --------------------------------------------------------

--
-- Table structure for table `admin_contact`
--

DROP TABLE IF EXISTS `admin_contact`;
CREATE TABLE IF NOT EXISTS `admin_contact` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `e-mail` varchar(256) NOT NULL,
  `is_contact` tinyint(4) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `genre` varchar(256) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `design`
--

DROP TABLE IF EXISTS `design`;
CREATE TABLE IF NOT EXISTS `design` (
  `design_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`design_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE IF NOT EXISTS `media` (
  `media_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  `duration` time NOT NULL,
  `realtime` time DEFAULT NULL,
  `season` int(11) DEFAULT NULL,
  `media_ov_id` int(11) NOT NULL,
  PRIMARY KEY (`media_id`),
  KEY `FK_media_media_ov` (`media_ov_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `media_ov`
--

DROP TABLE IF EXISTS `media_ov`;
CREATE TABLE IF NOT EXISTS `media_ov` (
  `media_ov_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `description` varchar(256) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`media_ov_id`),
  KEY `FK_media_ov_category` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `security`
--

DROP TABLE IF EXISTS `security`;
CREATE TABLE IF NOT EXISTS `security` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_text` varchar(256) NOT NULL DEFAULT '0',
  PRIMARY KEY (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `timestamp`
--

DROP TABLE IF EXISTS `timestamp`;
CREATE TABLE IF NOT EXISTS `timestamp` (
  `timestamp_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `timestamp` time NOT NULL,
  PRIMARY KEY (`timestamp_id`),
  KEY `FK_timestamp_media` (`media_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `day_of_birth` date NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `password` varchar(256) NOT NULL,
  `answer` varchar(256) NOT NULL,
  `salt` varchar(50) DEFAULT NULL,
  `roles` varchar(100) DEFAULT NULL,
  `question_id` int(11) NOT NULL,
  `design_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `question_id` (`question_id`),
  KEY `design_id` (`design_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `watch_history`
--

DROP TABLE IF EXISTS `watch_history`;
CREATE TABLE IF NOT EXISTS `watch_history` (
  `watch_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `media_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` time DEFAULT NULL,
  PRIMARY KEY (`watch_history_id`),
  KEY `user_id` (`user_id`),
  KEY `media_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_contact`
--
ALTER TABLE `admin_contact`
  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `media_ov`
--
ALTER TABLE `media_ov`
  ADD CONSTRAINT `FK_media_ov_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `timestamp`
--
ALTER TABLE `timestamp`
  ADD CONSTRAINT `FK_timestamp_media` FOREIGN KEY (`media_id`) REFERENCES `media` (`media_id`),
  ADD CONSTRAINT `FK_timestamp_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_user_design` FOREIGN KEY (`design_id`) REFERENCES `design` (`design_id`),
  ADD CONSTRAINT `question_id` FOREIGN KEY (`question_id`) REFERENCES `security` (`question_id`);

--
-- Constraints for table `watch_history`
--
ALTER TABLE `watch_history`
  ADD CONSTRAINT `FK_Verlauf_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
