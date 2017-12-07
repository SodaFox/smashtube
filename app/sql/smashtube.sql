/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE IF NOT EXISTS `smashtube` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `smashtube`;

CREATE TABLE IF NOT EXISTS `admin_contact` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `e-mail` varchar(256) NOT NULL,
  `is_contact` tinyint(4) NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `admin_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_contact` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `genre` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `category` DISABLE KEYS */;
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
/*!40000 ALTER TABLE `category` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `design` (
  `design_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`design_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `design` DISABLE KEYS */;
INSERT INTO `design` (`design_id`, `description`) VALUES
	(1, 'white');
/*!40000 ALTER TABLE `design` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL,
  `thumbnail` varchar(256) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  `duration` time NOT NULL,
  `path` varchar(256) NOT NULL,
  `realtime` time DEFAULT NULL,
  `season` int(11) DEFAULT NULL,
  `description_id` int(11) NOT NULL,
  `episode_number` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_media_media_ov` (`description_id`),
  CONSTRAINT `media_media_description` FOREIGN KEY (`description_id`) REFERENCES `media_description` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` (`id`, `title`, `thumbnail`, `description`, `duration`, `path`, `realtime`, `season`, `description_id`, `episode_number`) VALUES
	(4, NULL, 'das/ist/ein/thubnail', NULL, '01:50:00', '/das/ist/der/pfad/zur/datei.mp4', '00:00:00', NULL, 6, NULL),
	(5, 'das ist eine folge', 'das/ist/ein/thubnail', 'die folge', '00:15:00', '/das/ist/der/pfad/zur/datei.mp4', '00:42:02', 1, 7, 1),
	(6, 'das ist eine zweite folge ', 'das/ist/ein/thubnail', 'die zweite folge', '00:52:22', '/das/ist/der/pfad/zur/datei.mp4', '01:10:04', 1, 7, 2),
	(8, 'das ist eine zweite folge ', 'das/ist/ein/thubnail', 'die zweite folge', '00:52:22', '/das/ist/der/pfad/zur/datei.mp4', '01:10:04', 2, 7, 1),
	(9, 'asddasd', NULL, NULL, '00:40:00', 'C:\\wamp64\\tmp\\phpE253.tmp', NULL, 3, 7, 2);
/*!40000 ALTER TABLE `media` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `media_category` (
  `description_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`description_id`,`category_id`),
  KEY `media_category_categoryId` (`category_id`),
  CONSTRAINT `media_category_categoryId` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON UPDATE NO ACTION,
  CONSTRAINT `media_category_mediaId` FOREIGN KEY (`description_id`) REFERENCES `media_description` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `media_category` DISABLE KEYS */;
INSERT INTO `media_category` (`description_id`, `category_id`) VALUES
	(5, 1),
	(7, 4),
	(6, 5),
	(5, 6);
/*!40000 ALTER TABLE `media_category` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `media_description` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `description` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `media_description` DISABLE KEYS */;
INSERT INTO `media_description` (`id`, `title`, `description`) VALUES
	(5, 'das ist eine folge', 'die fo'),
	(6, 'dango', 'das ist der dango'),
	(7, 'die serie', 'das ist die serieeea');
/*!40000 ALTER TABLE `media_description` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `question` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_text` varchar(256) NOT NULL DEFAULT '0',
  PRIMARY KEY (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `question` DISABLE KEYS */;
INSERT INTO `question` (`question_id`, `question_text`) VALUES
	(5, 'test');
/*!40000 ALTER TABLE `question` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `timestamp` (
  `timestamp_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `timestamp` time NOT NULL,
  PRIMARY KEY (`timestamp_id`),
  KEY `FK_timestamp_media` (`media_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `FK_timestamp_media` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`),
  CONSTRAINT `FK_timestamp_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `timestamp` DISABLE KEYS */;
/*!40000 ALTER TABLE `timestamp` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(256) NOT NULL,
  `birthday` date NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `password` varchar(256) NOT NULL,
  `answer` varchar(256) NOT NULL,
  `salt` varchar(50) DEFAULT NULL,
  `roles` varchar(100) DEFAULT NULL,
  `question_id` int(11) NOT NULL,
  `design_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `question_id` (`question_id`),
  KEY `design_id` (`design_id`),
  CONSTRAINT `FK_user_design` FOREIGN KEY (`design_id`) REFERENCES `design` (`design_id`),
  CONSTRAINT `question_id` FOREIGN KEY (`question_id`) REFERENCES `question` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`user_id`, `username`, `birthday`, `is_admin`, `password`, `answer`, `salt`, `roles`, `question_id`, `design_id`) VALUES
	(1, 'user', '2013-03-04', 1, '$2y$10$c0ZhUsNjZ.9Cihfgnz3HRuIXw8ft3.yMGLfkAEfNMp83RknosuRTG', 'test', NULL, NULL, 5, 1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `watch_history` (
  `watch_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `media_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` time DEFAULT NULL,
  PRIMARY KEY (`watch_history_id`),
  KEY `user_id` (`user_id`),
  KEY `media_id` (`media_id`),
  CONSTRAINT `FK_Verlauf_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `watch_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `watch_history` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
