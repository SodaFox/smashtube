-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 01, 2017 at 07:11 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `admin_contact`
--

DROP TABLE IF EXISTS `admin_contact`;
CREATE TABLE IF NOT EXISTS `admin_contact` (
  `user_id` int(11) NOT NULL,
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

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `genre`) VALUES
(1, 'Comedy'),
(2, 'Action'),
(3, 'Horror'),
(4, 'Thriller'),
(5, 'Drama'),
(6, 'Adventure'),
(7, 'Documentary'),
(8, 'Erotic'),
(9, 'Educational'),
(10, 'Social guidance'),
(11, 'Epic'),
(12, 'Experimental'),
(13, 'Exploitation'),
(14, 'Fantasy'),
(15, 'Film noir'),
(16, 'Gothic'),
(17, 'Musical'),
(18, 'Mystery'),
(19, 'Propaganda'),
(20, 'Romantic'),
(21, 'Science fiction');

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
-- Table structure for table `episode`
--

DROP TABLE IF EXISTS `episode`;
CREATE TABLE IF NOT EXISTS `episode` (
  `episode_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `duration` time NOT NULL,
  PRIMARY KEY (`episode_id`,`media_id`),
  KEY `season_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE IF NOT EXISTS `media` (
  `media_id` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(256) NOT NULL DEFAULT '0',
  `description` varchar(256) DEFAULT '0',
  `duration` time NOT NULL DEFAULT '00:00:00',
  `media_typ` enum('M','S') NOT NULL,
  `season_id` int(11) DEFAULT NULL,
  `category_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`media_id`),
  KEY `category_id` (`category_id`),
  KEY `season_id` (`season_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

--
-- Dumping data for table `security`
--

INSERT INTO `security` (`question_id`, `question_text`) VALUES
(1, 'Wie war der erste Name deines Haustieres?'),
(2, 'In welcher Stadt wurdest du geboren?'),
(3, 'Wie lautet der Mädchenname deiner Mutter?'),
(4, 'Wie ist die Nummer deiner Kreditkarte + PIN?');

-- --------------------------------------------------------

--
-- Table structure for table `timestamp`
--

DROP TABLE IF EXISTS `timestamp`;
CREATE TABLE IF NOT EXISTS `timestamp` (
  `user_id` int(11) NOT NULL,
  `medien_id` int(11) NOT NULL,
  `timestamp` time DEFAULT NULL,
  `episode_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`,`medien_id`),
  KEY `FK_timestamp_media` (`medien_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(256) NOT NULL,
  `day_of_birth` date NOT NULL,
  `is_admin` enum('A','U') NOT NULL,
  `password` varchar(500) NOT NULL,
  `answer` varchar(256) NOT NULL,
  `salt` varchar(50),
  `roles` varchar(100),
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
  `medien_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` time DEFAULT NULL,
  `episode_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`medien_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE IF NOT EXISTS `wishlist` (
  `medien_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`medien_id`,`user_id`),
  KEY `user_id` (`user_id`)
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
-- Constraints for table `episode`
--
ALTER TABLE `episode`
  ADD CONSTRAINT `FK_episode_media` FOREIGN KEY (`media_id`) REFERENCES `media` (`media_id`);

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `FK_media_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- Constraints for table `timestamp`
--
ALTER TABLE `timestamp`
  ADD CONSTRAINT `FK_timestamp_media` FOREIGN KEY (`medien_id`) REFERENCES `media` (`media_id`),
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
  ADD CONSTRAINT `FK_Verlauf_media` FOREIGN KEY (`medien_id`) REFERENCES `media` (`media_id`),
  ADD CONSTRAINT `FK_Verlauf_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `FK_merkliste_media` FOREIGN KEY (`medien_id`) REFERENCES `media` (`media_id`),
  ADD CONSTRAINT `FK_merkliste_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
