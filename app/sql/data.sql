-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 18, 2017 at 07:55 AM
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

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `genre`) VALUES
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

--
-- Dumping data for table `design`
--

INSERT INTO `design` (`id`, `description`) VALUES
(1, 'white');

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `title`, `thumbnail`, `description`, `duration`, `path`, `realtime`, `season`, `description_id`, `episode_number`) VALUES
(4, 'dingo', 'das/ist/ein/thubnail', 'aaaaaaaaaah', '01:50:00', '/das/ist/der/pfad/zur/datei.mp4', '00:00:00', NULL, 6, NULL),
(5, 'das ist eine folge', 'das/ist/ein/thubnail', 'die folge', '00:15:00', '/das/ist/der/pfad/zur/datei.mp4', '00:42:02', 1, 7, 1),
(6, 'das ist eine zweite folge ', 'das/ist/ein/thubnail', 'die zweite folge', '00:52:22', '/das/ist/der/pfad/zur/datei.mp4', '01:10:04', 1, 7, 2),
(8, 'folge 2', 'das/ist/ein/thubnail', 'die zweite folge', '00:52:22', '/das/ist/der/pfad/zur/datei.mp4', '01:10:04', 2, 8, 1),
(9, 'asddasd', NULL, 'aaaaaaaaashh', '00:40:00', 'C:\\wamp64\\tmp\\phpE253.tmp', NULL, 3, 7, 2);

--
-- Dumping data for table `media_category`
--

INSERT INTO `media_category` (`description_id`, `category_id`) VALUES
(5, 1),
(7, 4),
(6, 5),
(5, 6);

--
-- Dumping data for table `media_description`
--

INSERT INTO `media_description` (`id`, `title`, `description`, `thumbnail`, `path`) VALUES
(5, 'das ist eine folge', 'die fo', NULL, NULL),
(6, 'dango', 'das ist der dango', NULL, NULL),
(7, 'die serie', 'das ist die serieeea', NULL, NULL),
(8, 'der neue anime', 'der dude lebt jetzt hier', NULL, NULL);

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`id`, `text`) VALUES
(5, 'test');

--
-- Dumping data for table `timestamp`
--

INSERT INTO `timestamp` (`id`, `user_id`, `media_id`, `timestamp`) VALUES
(1, 2, 5, '00:00:26'),
(2, 2, 4, '00:00:10');

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `birthday`, `is_admin`, `password`, `answer`, `salt`, `roles`, `question_id`, `design_id`) VALUES
(1, 'user', '2013-03-04', 1, '$2y$10$c0ZhUsNjZ.9Cihfgnz3HRuIXw8ft3.yMGLfkAEfNMp83RknosuRTG', 'test', NULL, NULL, 5, 1),
(2, 'SodaFox', '2017-12-15', 0, '$2y$13$/AX.eEtPj4NKwQsh28r/U.N5o8trh39046hgPy/a2Ge/.3adGkpqu', '$2y$13$G8hEx.0KS0KWslmBcvVoZe8HH27ZR7mV2TcZHtCTYl0wWRliLf0Ye', NULL, NULL, 5, NULL);

--
-- Dumping data for table `watch_list`
--

INSERT INTO `watch_list` (`id`, `media_id`, `user_id`) VALUES
(1, 6, 1),
(2, 8, 1);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
