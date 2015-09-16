# ************************************************************
# Sequel Pro SQL dump
# Version 4135
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.34)
# Database: asl1407
# Generation Time: 2014-07-31 16:30:50 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table framework
# ------------------------------------------------------------

DROP TABLE IF EXISTS `framework`;

CREATE TABLE `framework` (
  `frameId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `language` int(11) DEFAULT NULL,
  `frameName` varchar(75) DEFAULT NULL,
  `frameNote` text NOT NULL,
  `frameLink` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`frameId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `framework` WRITE;
/*!40000 ALTER TABLE `framework` DISABLE KEYS */;

INSERT INTO `framework` (`frameId`, `language`, `frameName`, `frameNote`, `frameLink`)
VALUES
	(2,2,'Flask','No instructor notes exist','aol.com'),
	(3,3,'node.JS','No instructor notes exist',''),
	(4,4,'Rails','No instructor notes exist',''),
	(5,6,'SLIM','No instructor notes exist','');

/*!40000 ALTER TABLE `framework` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table language
# ------------------------------------------------------------

DROP TABLE IF EXISTS `language`;

CREATE TABLE `language` (
  `langId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `langName` varchar(75) DEFAULT NULL,
  `langNote` text NOT NULL,
  `langLink` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`langId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `language` WRITE;
/*!40000 ALTER TABLE `language` DISABLE KEYS */;

INSERT INTO `language` (`langId`, `langName`, `langNote`, `langLink`)
VALUES
	(2,'Python','<pre>&nbsp;<a style=\"font-family: Verdana; font-size: medium;\" name=\"Python\"></a></pre>\r\n<pre style=\"display: inline !important;\"># Hello World in Python</pre>\r\n<pre><a style=\"font-family: Verdana; font-size: medium;\" name=\"Python\"></a></pre>\r\n<pre>print \"Hello World\"</pre>','www.google.com'),
	(3,'Javascript','<pre>&lt;html&gt;\r\n&lt;body&gt;\r\n&lt;script language=\"JavaScript\" type=\"text/javascript\"&gt;\r\n// Hello World in JavaScript\r\ndocument.write(\'Hello World\');\r\n&lt;/script&gt;\r\n&lt;/body&gt;\r\n&lt;/html&gt;</pre>',''),
	(4,'Ruby','<pre># Hello World in Ruby\r\nputs \"Hello World!\"</pre>',''),
	(6,'PHP','&lt;?php <br /><br />// Hello World in PHP <br /><br />echo \'Hello World!\'; <br /><br />?&gt;',''),
	(7,'Cold Fusion','some code','');

/*!40000 ALTER TABLE `language` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tutorial
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tutorial`;

CREATE TABLE `tutorial` (
  `tutorId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `language` int(11) DEFAULT NULL,
  `framework` int(11) DEFAULT NULL,
  `tutorialName` varchar(75) DEFAULT NULL,
  `tutorNote` text NOT NULL,
  PRIMARY KEY (`tutorId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `tutorial` WRITE;
/*!40000 ALTER TABLE `tutorial` DISABLE KEYS */;

INSERT INTO `tutorial` (`tutorId`, `language`, `framework`, `tutorialName`, `tutorNote`)
VALUES
	(2,2,2,'tutotrial','No instructor notes exist'),
	(3,3,3,'Hello World!','No instructor notes exist'),
	(4,4,4,'Hello World!','No instructor notes exist'),
	(5,6,5,'install','No instructor notes exist'),
	(6,7,2,'','No instructor notes exist');

/*!40000 ALTER TABLE `tutorial` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;

INSERT INTO `user` (`id`, `username`, `password`)
VALUES
	(1,'user','password');

/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
