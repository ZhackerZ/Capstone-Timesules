-- MySQL dump 10.13  Distrib 5.5.40, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: timesules
-- ------------------------------------------------------
-- Server version	5.5.40-0ubuntu0.12.04.1

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
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attachments` (
  `attachment_id` int(11) NOT NULL AUTO_INCREMENT,
  `attachment_image_link` varchar(100) NOT NULL,
  `attachment_media_link` varchar(100) NOT NULL,
  `attachment_text` text NOT NULL,
  `cap_id` int(11) NOT NULL,
  `user_id` int(10) NOT NULL,
  `attach_time` datetime NOT NULL,
  PRIMARY KEY (`attachment_id`,`cap_id`),
  KEY `fk_attachments_capsules1_idx` (`cap_id`),
  CONSTRAINT `fk_attachments_capsules1` FOREIGN KEY (`cap_id`) REFERENCES `capsules` (`cap_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachments`
--

LOCK TABLES `attachments` WRITE;
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
INSERT INTO `attachments` VALUES (1,'','','<p>Hello world!</p>',10,6,'2014-11-23 06:11:46'),(2,'','','<p>This is awesome!</p>',10,7,'2014-11-24 12:11:04');
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Table structure for table `capsules`
--

DROP TABLE IF EXISTS `capsules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `capsules` (
  `cap_id` int(11) NOT NULL AUTO_INCREMENT,
  `cap_email_to` varchar(100) NOT NULL,
  `cap_email_from` varchar(100) NOT NULL,
  `cap_title` varchar(200) NOT NULL,
  `cap_msg` text NOT NULL,
  `cap_lock` datetime NOT NULL,
  `cap_release` datetime NOT NULL,
  `cap_hidden` tinyint(4) NOT NULL DEFAULT '0',
  `cap_draft` tinyint(4) NOT NULL DEFAULT '0',
  `cap_vis` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`cap_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `capsules`
--

LOCK TABLES `capsules` WRITE;
/*!40000 ALTER TABLE `capsules` DISABLE KEYS */;
INSERT INTO `capsules` VALUES (1,'mbembac@gmail.com','telljohann.9@buckeyemail.osu.edu','1st Capsule','WOW this is cool','2014-11-10 00:00:00','2014-11-13 00:00:00',0,0,1),(2,'mbembac@gmail.com','testuser@gmail.com','Cap # 2','Dude you\'re so popular on this netowrk!','2014-11-15 00:00:00','2014-11-17 00:00:00',0,0,1),(8,'Group','telljohann.9@buckeyemail.osu.edu','Memories','All the fun we had','2014-11-21 01:00:00','2014-12-20 01:00:00',0,0,0),(9,'Group','telljohann.9@buckeyemail.osu.edu','Everyone','Hello guys!','2014-11-21 01:00:00','2014-12-20 01:00:00',0,0,1),(10,'Group','telljohann.9@buckeyemail.osu.edu','Test','All the fun we had','2014-11-23 01:00:00','2014-11-23 03:00:00',0,0,0),(11,'Group','mbembac@gmail.com','Memories','Test memories','2014-11-25 09:00:00','2014-12-24 09:00:00',0,0,1),(12,'Array','1','test','<p>test</p>','2014-01-01 02:00:00','2015-01-02 01:00:00',0,0,1),(13,'testuser@gmail.com','mbembac@gmail.com','test','<p>WOW Man you have a timesule</p>','2014-11-25 08:00:00','2014-11-25 09:00:00',0,0,1),(14,'Array','mbembac@gmail.com','test','<p>Hey</p>','2014-11-25 08:00:00','2014-11-25 09:15:00',0,0,1),(15,'Array','mbembac@gmail.com','test','<p>test</p>','2014-11-25 08:00:00','2014-11-25 09:20:00',0,0,1),(16,'testuser@gmail.com','mbembac@gmail.com','test','<p>hey</p>','2014-11-25 09:08:00','2014-11-25 10:00:00',0,0,1),(17,'mbembac@gmail.com','testuser@gmail.com','Hey Claude','<p>Goodluck</p>','2014-11-25 08:01:00','2014-11-25 09:21:00',0,0,1),(18,'mbembac@gmail.com','testuser@gmail.com','Hey Man','<p>YOU are amazing</p>','2014-11-25 08:00:00','2014-11-25 09:35:00',0,0,1),(19,'mbembac@gmail.com','testuser@gmail.com','test','<p>COOL</p>','2014-11-01 02:00:00','2014-11-25 09:36:00',0,0,1),(20,'mbembac@gmail.com','testuser@gmail.com','Test Locked','<p>THis is locked for 1 year</p>','2014-01-01 01:00:00','2015-01-01 01:00:00',0,0,1),(21,'testuser@gmail.com','mbembac@gmail.com','Memories','<p>Good luck with this release</p>','0000-00-00 00:00:00','2014-11-30 18:12:00',0,0,1),(22,'mbembac@gmail.com','mbembac@gmail.com','TEST TO ME','<p>Testing</p>','0000-00-00 00:00:00','2014-11-30 19:00:00',0,0,1),(23,'testuser@gmail.com','mbembac@gmail.com','test time','<p>hey</p>','0000-00-00 00:00:00','2014-12-31 01:00:00',0,0,1),(24,'testuser@gmail.com','mbembac@gmail.com','test time','<p>test</p>','0000-00-00 00:00:00','2015-01-01 13:00:00',0,0,0),(25,'testuser@gmail.com','mbembac@gmail.com','test time','<p>test</p>','0000-00-00 00:00:00','2015-01-01 13:00:00',0,0,0),(26,'mbembac@gmail.com','mbembac@gmail.com','test','<p>test</p>','0000-00-00 00:00:00','2014-12-02 13:59:00',0,0,1),(27,'testuser@gmail.com','mbembac@gmail.com','test hey','<p>hey</p>','0000-00-00 00:00:00','2015-01-01 13:00:00',0,0,1),(28,'testuser@gmail.com','mbembac@gmail.com','test error','<p>test</p>','2014-12-02 06:51:25','2014-12-31 00:59:00',0,0,0),(29,'testuser@gmail.com','mbembac@gmail.com','testing','<p>HEY</p>','2014-12-02 07:02:51','2014-12-02 19:05:00',0,0,0),(30,'mbembac@gmail.com','mbembac@gmail.com','test draft','<p>test draft</p>','2014-12-02 22:07:42','2015-01-31 13:00:00',0,1,1),(31,'mbembac@gmail.com','mbembac@gmail.com','test draft 2','<p>draft2</p>','2014-12-02 22:11:46','2016-01-31 01:00:00',0,1,1);
/*!40000 ALTER TABLE `capsules` ENABLE KEYS */;
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
-- Table structure for table `group_has_capsules`
--

DROP TABLE IF EXISTS `group_has_capsules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_has_capsules` (
  `cap_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`cap_id`,`group_id`),
  KEY `fk_group_has_capsules_groups1_idx` (`group_id`),
  KEY `fk_group_has_capsules_capsules1_idx` (`cap_id`),
  CONSTRAINT `fk_group_has_capsules_cap` FOREIGN KEY (`cap_id`) REFERENCES `capsules` (`cap_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_group_has_capsules_group` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_has_capsules`
--

LOCK TABLES `group_has_capsules` WRITE;
/*!40000 ALTER TABLE `group_has_capsules` DISABLE KEYS */;
INSERT INTO `group_has_capsules` VALUES (8,2),(9,2),(10,2),(11,3);
/*!40000 ALTER TABLE `group_has_capsules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(45) NOT NULL,
  `group_avatar` varchar(50) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'Capstone',''),(2,'Capstone',''),(3,'testGroup','');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `friend_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `cap_id` int(11) NOT NULL DEFAULT '0',
  `type` int(4) NOT NULL,
  `message` text NOT NULL,
  `viewed` tinyint(1) NOT NULL,
  PRIMARY KEY (`notification_id`,`user_id`),
  KEY `fk_notifications_users1_idx` (`user_id`),
  CONSTRAINT `fk_notifications_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,1,6,0,0,1,'Hey You have a notification',1),(2,1,0,1,0,2,'New capsule available',0),(3,1,0,0,1,3,'',1),(4,2,1,0,0,1,'Claudius Mbemba wants to be friends',1),(5,5,1,0,0,1,'Claudius Mbemba wants to be friends',0),(6,1,0,0,19,3,'',0),(7,1,0,0,20,3,'',0),(8,2,0,0,21,3,'',0),(9,1,0,0,22,3,'',0),(10,2,0,0,23,3,'',0),(11,2,0,0,24,3,'',0),(12,2,0,0,25,3,'',0),(13,1,0,0,1,3,'New notification',0),(14,1,0,0,2,3,'hey',0),(15,2,0,0,27,3,'',0),(16,2,0,0,28,3,'',0),(17,2,0,0,29,3,'',0),(18,1,0,0,30,3,'',0),(19,1,0,0,31,3,'',0);
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
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
-- Table structure for table `user_has_capsules`
--

DROP TABLE IF EXISTS `user_has_capsules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_has_capsules` (
  `cap_id` int(11) NOT NULL,
  `user_id` int(10) NOT NULL,
  `role` varchar(200) NOT NULL DEFAULT 'recipient',
  PRIMARY KEY (`cap_id`,`user_id`),
  KEY `fk_user_has_capsules_users1_idx` (`user_id`),
  KEY `fk_user_has_capsules_capsules1_idx` (`cap_id`),
  CONSTRAINT `fk_user_has_capsules_cap` FOREIGN KEY (`cap_id`) REFERENCES `capsules` (`cap_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_capsules_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_has_capsules`
--

LOCK TABLES `user_has_capsules` WRITE;
/*!40000 ALTER TABLE `user_has_capsules` DISABLE KEYS */;
INSERT INTO `user_has_capsules` VALUES (1,6,'owner'),(14,1,'owner'),(14,2,'recipient'),(15,1,'owner'),(15,2,'recipient'),(16,1,'owner'),(16,2,'recipient'),(17,1,'recipient'),(17,2,'owner'),(18,2,'owner'),(19,1,'recipient'),(19,2,'owner'),(20,1,'recipient'),(20,2,'owner'),(21,1,'owner'),(21,2,'recipient'),(22,1,'owner'),(23,1,'owner'),(23,2,'recipient'),(24,1,'owner'),(24,2,'recipient'),(25,1,'owner'),(25,2,'recipient'),(26,1,'owner'),(27,1,'owner'),(27,2,'recipient'),(28,1,'owner'),(28,2,'recipient'),(29,1,'owner'),(29,2,'recipient'),(30,1,'owner'),(31,1,'owner');
/*!40000 ALTER TABLE `user_has_capsules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_has_contacts`
--

DROP TABLE IF EXISTS `user_has_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_has_contacts` (
  `user_id` int(10) NOT NULL,
  `contact_id` int(10) NOT NULL,
  PRIMARY KEY (`user_id`,`contact_id`),
  KEY `fk_user_has_user_user2_idx` (`contact_id`),
  KEY `fk_user_has_user_user1_idx` (`user_id`),
  CONSTRAINT `fk_user_has_contacts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_contacts_contact` FOREIGN KEY (`contact_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_has_contacts`
--

LOCK TABLES `user_has_contacts` WRITE;
/*!40000 ALTER TABLE `user_has_contacts` DISABLE KEYS */;
INSERT INTO `user_has_contacts` VALUES (2,1),(5,1),(6,1),(1,6);
/*!40000 ALTER TABLE `user_has_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_has_notifcations`
--

DROP TABLE IF EXISTS `user_has_notifcations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_has_notifcations` (
  `user_id` int(10) NOT NULL,
  `notification_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`notification_id`),
  KEY `fk_notifications_has_users_users1_idx` (`user_id`),
  KEY `fk_notifications_has_users_notifications1_idx` (`notification_id`),
  CONSTRAINT `fk_user_has_notifcations_notification` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`notification_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_notifcations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_has_notifcations`
--

LOCK TABLES `user_has_notifcations` WRITE;
/*!40000 ALTER TABLE `user_has_notifcations` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_has_notifcations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_owns_groups`
--

DROP TABLE IF EXISTS `user_owns_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_owns_groups` (
  `group_id` int(11) NOT NULL,
  `user_id` int(10) NOT NULL,
  `user_role` varchar(150) NOT NULL DEFAULT 'member',
  PRIMARY KEY (`group_id`,`user_id`),
  KEY `fk_user_owns_group_user1_idx` (`user_id`),
  KEY `fk_user_owns_group_group1_idx` (`group_id`),
  CONSTRAINT `fk_user_owns_group_group` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_owns_group_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_owns_groups`
--

LOCK TABLES `user_owns_groups` WRITE;
/*!40000 ALTER TABLE `user_owns_groups` DISABLE KEYS */;
INSERT INTO `user_owns_groups` VALUES (2,6,'owner'),(2,7,'member'),(3,1,'owner');
/*!40000 ALTER TABLE `user_owns_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(50) NOT NULL,
  `user_first` varchar(50) NOT NULL,
  `user_middle` varchar(50) NOT NULL,
  `user_last` varchar(50) NOT NULL,
  `user_age` date NOT NULL,
  `user_gender` tinyint(1) NOT NULL,
  `user_avatar` varchar(45) NOT NULL,
  `user_ip` varchar(50) NOT NULL,
  `user_ban` tinyint(1) NOT NULL DEFAULT '0',
  `user_conf` varchar(50) NOT NULL,
  `user_prefs` bit(5) NOT NULL DEFAULT b'11111',
  `hash` varchar(32) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_email_UNIQUE` (`user_email`),
  KEY `email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'mbembac@gmail.com','7b91a70640f90b82507f4d9d9615fdb558f61fd0d560','Claudius','m','Mbemba','1973-11-22',1,'','192.168.56.1',0,'','','',1),(2,'testuser@gmail.com','dec2c833ca6c01d2bf71fdace18ab4e99dd48d1ce597','test','','user','1980-07-12',1,'','192.168.56.1',0,'','','',1),(3,'jane@doe.com','e07ccb9c9a8a3c70f6aef4a6722c83810ee44c10af25','Jane','','Doe','1992-07-21',2,'','192.168.56.1',0,'','','',1),(4,'john@doe.com','4e57248fde36931fb672d7ef3a93a8837bdb06072bef','John','','Doe','1965-06-07',1,'','192.168.56.1',0,'','','',1),(5,'jlturner829@gmail.com','d100d34a69fe039d1d6513404d3a5ae6c1aa2d60d2b6','Jennifer','','Turner','1991-08-29',2,'','192.168.56.1',0,'','','',1),(6,'telljohann.9@buckeyemail.osu.edu','cb060b2972d25ceac06e665b097dea1d849d47dc29db','Daniel','','Telljohann','1992-11-12',1,'','192.168.56.1',0,'','','091d584fced301b442654dd8c23b3fc9',1),(7,'buckeyes@gmail.com','f57df5f4f75f99dc42c80b2f7d670d95d8abce18d864','Urban','','Meyer','1990-04-02',1,'','192.168.56.1',0,'','','bca82e41ee7b0833588399b1fcd177c7',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-12-02 23:52:24
