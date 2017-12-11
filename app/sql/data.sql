/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*!40000 ALTER TABLE `admin_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_contact` ENABLE KEYS */;

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

/*!40000 ALTER TABLE `design` DISABLE KEYS */;
INSERT INTO `design` (`id`, `description`) VALUES
	(1, 'white');
/*!40000 ALTER TABLE `design` ENABLE KEYS */;

/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` (`id`, `title`, `thumbnail`, `description`, `duration`, `path`, `realtime`, `season`, `description_id`, `episode_number`) VALUES
	(4, 'dingo', 'das/ist/ein/thubnail', 'aaaaaaaaaah', '01:50:00', '/das/ist/der/pfad/zur/datei.mp4', '00:00:00', NULL, 6, NULL),
	(5, 'das ist eine folge', 'das/ist/ein/thubnail', 'die folge', '00:15:00', '/das/ist/der/pfad/zur/datei.mp4', '00:42:02', 1, 7, 1),
	(6, 'das ist eine zweite folge ', 'das/ist/ein/thubnail', 'die zweite folge', '00:52:22', '/das/ist/der/pfad/zur/datei.mp4', '01:10:04', 1, 7, 2),
	(8, 'folge 2', 'das/ist/ein/thubnail', 'die zweite folge', '00:52:22', '/das/ist/der/pfad/zur/datei.mp4', '01:10:04', 2, 8, 1),
	(9, 'asddasd', NULL, 'aaaaaaaaashh', '00:40:00', 'C:\\wamp64\\tmp\\phpE253.tmp', NULL, 3, 7, 2);
/*!40000 ALTER TABLE `media` ENABLE KEYS */;

/*!40000 ALTER TABLE `media_category` DISABLE KEYS */;
INSERT INTO `media_category` (`description_id`, `category_id`) VALUES
	(5, 1),
	(7, 4),
	(6, 5),
	(5, 6);
/*!40000 ALTER TABLE `media_category` ENABLE KEYS */;

/*!40000 ALTER TABLE `media_description` DISABLE KEYS */;
INSERT INTO `media_description` (`id`, `title`, `description`) VALUES
	(5, 'das ist eine folge', 'die fo'),
	(6, 'dango', 'das ist der dango'),
	(7, 'die serie', 'das ist die serieeea'),
	(8, 'der neue anime', 'der dude lebt jetzt hier');
/*!40000 ALTER TABLE `media_description` ENABLE KEYS */;

/*!40000 ALTER TABLE `question` DISABLE KEYS */;
INSERT INTO `question` (`id`, `text`) VALUES
	(5, 'test');
/*!40000 ALTER TABLE `question` ENABLE KEYS */;

/*!40000 ALTER TABLE `timestamp` DISABLE KEYS */;
/*!40000 ALTER TABLE `timestamp` ENABLE KEYS */;

/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `username`, `birthday`, `is_admin`, `password`, `answer`, `salt`, `roles`, `question_id`, `design_id`) VALUES
	(1, 'user', '2013-03-04', 1, '$2y$10$c0ZhUsNjZ.9Cihfgnz3HRuIXw8ft3.yMGLfkAEfNMp83RknosuRTG', 'test', NULL, NULL, 5, 1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40000 ALTER TABLE `watch_list` DISABLE KEYS */;
INSERT INTO `watch_list` (`id`, `media_id`, `user_id`) VALUES
	(1, 6, 1),
	(2, 8, 1);
/*!40000 ALTER TABLE `watch_list` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
