-- MySQL dump 10.13  Distrib 5.5.40, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: timesules
-- ------------------------------------------------------
-- Server version 5.5.40-0ubuntu0.12.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bugs`
--

DROP TABLE IF EXISTS `bugs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bugs` (
  `bug_id` int(10) NOT NULL AUTO_INCREMENT,
  `bug_page` varchar(50) NOT NULL,
  `bug_msg` text NOT NULL,
  `bug_user` int(10) NOT NULL,
  `bug_date` int(10) NOT NULL,
  PRIMARY KEY (`bug_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bugs`
--

LOCK TABLES `bugs` WRITE;
/*!40000 ALTER TABLE `bugs` DISABLE KEYS */;
/*!40000 ALTER TABLE `bugs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `com_id` int(10) NOT NULL AUTO_INCREMENT,
  `com_type` int(1) NOT NULL,
  `com_user` int(10) NOT NULL,
  `com_post` int(10) NOT NULL,
  `com_date` int(10) NOT NULL,
  `com_comment` varchar(500) NOT NULL,
  PRIMARY KEY (`com_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `debug_tracker`
--

DROP TABLE IF EXISTS `debug_tracker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `debug_tracker` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user` int(10) NOT NULL,
  `page` varchar(50) NOT NULL,
  `get_data` text NOT NULL,
  `post_data` text NOT NULL,
  `queries` text NOT NULL,
  `user_data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `debug_tracker`
--

LOCK TABLES `debug_tracker` WRITE;
/*!40000 ALTER TABLE `debug_tracker` DISABLE KEYS */;
/*!40000 ALTER TABLE `debug_tracker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_posts`
--

DROP TABLE IF EXISTS `group_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_posts` (
  `gpo_id` int(10) NOT NULL AUTO_INCREMENT,
  `gpo_pid` int(10) NOT NULL,
  `gpo_uid` int(10) NOT NULL,
  `gpo_msg` text NOT NULL,
  `gpo_attachments` text NOT NULL,
  `gpo_date` int(10) NOT NULL,
  PRIMARY KEY (`gpo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_posts`
--

LOCK TABLES `group_posts` WRITE;
/*!40000 ALTER TABLE `group_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `group_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_prompts`
--

DROP TABLE IF EXISTS `group_prompts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_prompts` (
  `gpr_id` int(10) NOT NULL AUTO_INCREMENT,
  `gpr_gid` int(10) NOT NULL,
  `gpr_prompt` varchar(200) NOT NULL,
  `gpr_des` varchar(200) NOT NULL,
  `gpr_lock` int(10) NOT NULL,
  `gpr_release` int(10) NOT NULL,
  `gpr_vis` tinyint(1) NOT NULL,
  PRIMARY KEY (`gpr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_prompts`
--

LOCK TABLES `group_prompts` WRITE;
/*!40000 ALTER TABLE `group_prompts` DISABLE KEYS */;
INSERT INTO `group_prompts` VALUES (1,3,'Memories','Hello World!',1415140200,1417647600,0),(2,4,'Memories','All the fun we had',1415314800,1417820400,0);
/*!40000 ALTER TABLE `group_prompts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `group_id` int(10) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(100) NOT NULL,
  `group_admin` int(10) NOT NULL,
  `group_users` text NOT NULL,
  `group_avatar` varchar(32) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'LBJ King',5,'',''),(2,'Urban',5,'',''),(3,'Timesule Groups',6,'',''),(4,'Capstone',9,'','');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `post_id` int(10) NOT NULL AUTO_INCREMENT,
  `post_user` int(10) NOT NULL,
  `post_to` text NOT NULL,
  `post_prompt` varchar(200) NOT NULL,
  `post_msg` text NOT NULL,
  `post_attachments` text NOT NULL,
  `post_lock` int(10) NOT NULL,
  `post_release` int(10) NOT NULL,
  `post_vis` tinyint(1) NOT NULL DEFAULT '1',
  `post_draft` tinyint(1) NOT NULL DEFAULT '0',
  `post_hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sendacapsule`
--

DROP TABLE IF EXISTS `sendacapsule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sendacapsule` (
  `cap_id` int(10) NOT NULL AUTO_INCREMENT,
  `cap_email` varchar(100) NOT NULL,
  `cap_subj` varchar(100) NOT NULL,
  `cap_msg` varchar(500) NOT NULL,
  `cap_time` int(10) NOT NULL,
  PRIMARY KEY (`cap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sendacapsule`
--

LOCK TABLES `sendacapsule` WRITE;
/*!40000 ALTER TABLE `sendacapsule` DISABLE KEYS */;
/*!40000 ALTER TABLE `sendacapsule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets` (
  `ticket_id` int(10) NOT NULL AUTO_INCREMENT,
  `ticket_name` varchar(200) NOT NULL,
  `ticket_email` varchar(200) NOT NULL,
  `ticket_area` varchar(50) NOT NULL,
  `ticket_msg` text NOT NULL,
  `ticket_user` int(10) NOT NULL,
  `ticket_date` int(10) NOT NULL,
  PRIMARY KEY (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(50) NOT NULL,
  `user_first` varchar(50) NOT NULL,
  `user_middle` varchar(50) NOT NULL,
  `user_last` varchar(50) NOT NULL,
  `user_age` date NOT NULL,
  `user_gender` tinyint(1) NOT NULL,
  `user_avatar` varchar(32) NOT NULL,
  `user_ip` varchar(50) NOT NULL,
  `user_ban` tinyint(1) NOT NULL DEFAULT '0',
  `user_conf` varchar(50) NOT NULL,
  `user_contacts` text NOT NULL,
  `user_groups` text NOT NULL,
  `user_notifications` text NOT NULL,
  `user_prefs` bit(5) NOT NULL DEFAULT b'11111',
  `hash` varchar(32) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'mbembac@gmail.com','7b91a70640f90b82507f4d9d9615fdb558f61fd0d560','Claudius','m','Mbemba','1973-11-22',1,'','192.168.56.1',0,'','','','','','',1),(2,'testuser@gmail.com','dec2c833ca6c01d2bf71fdace18ab4e99dd48d1ce597','test','','user','1980-07-12',1,'','192.168.56.1',0,'','','','','','',1),(3,'jane@doe.com','e07ccb9c9a8a3c70f6aef4a6722c83810ee44c10af25','Jane','','Doe','1992-07-21',2,'','192.168.56.1',0,'','','','','','',1),(4,'john@doe.com','4e57248fde36931fb672d7ef3a93a8837bdb06072bef','John','','Doe','1965-06-07',1,'','192.168.56.1',0,'','','','','','',1),(5,'jlturner829@gmail.com','d100d34a69fe039d1d6513404d3a5ae6c1aa2d60d2b6','Jennifer','','Turner','1991-08-29',2,'','192.168.56.1',0,'','','','','','',1),(6,'telljohann.9@buckeyemail.osu.edu','cb060b2972d25ceac06e665b097dea1d849d47dc29db','Daniel','','Telljohann','1992-11-12',1,'','192.168.56.1',0,'','','','','','091d584fced301b442654dd8c23b3fc9',1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-11-07 17:06:53
