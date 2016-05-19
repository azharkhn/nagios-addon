-- MySQL dump 10.13  Distrib 5.6.25, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: monitoring_panels
-- ------------------------------------------------------
-- Server version	5.5.44-0ubuntu0.14.04.1

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
-- Table structure for table `base_cost_scheduler`
--


DROP TABLE IF EXISTS `monitoring_db`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monitoring_db` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `hostname` text,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `database` varchar(100) DEFAULT NULL,
  `active` enum('Y','N') DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `updated_by` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monitoring_db`
--

--
-- Table structure for table `monitoring_login_edrs`
--

DROP TABLE IF EXISTS `monitoring_login_edrs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monitoring_login_edrs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session-id` varchar(50) DEFAULT NULL,
  `ip_address` varchar(20) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `country_code` varchar(2) DEFAULT NULL,
  `user_agent` text,
  `host` varchar(25) DEFAULT NULL,
  `method` varchar(5) DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `status` enum('SUCCESS','FAILED') DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monitoring_login_edrs`
--



--
-- Table structure for table `monitoring_thresholds`
--

DROP TABLE IF EXISTS `monitoring_thresholds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monitoring_thresholds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `db_id` varchar(45) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `vendor` varchar(50) DEFAULT NULL,
  `alert` int(11) DEFAULT NULL,
  `warning` int(11) DEFAULT NULL,
  `critical` int(11) DEFAULT NULL,
  `query` text,
  `unit` varchar(25) DEFAULT NULL,
  `description` text,
  `active` enum('Y','N') DEFAULT 'Y',
  `notification` enum('Y','N') DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `updated_by` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_UNIQUE` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `monitoring_user_profile`
--

DROP TABLE IF EXISTS `monitoring_user_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monitoring_user_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(20) DEFAULT NULL,
  `middle_name` varchar(20) DEFAULT NULL,
  `last_name` varchar(20) DEFAULT NULL,
  `contact_no1` varchar(20) DEFAULT NULL,
  `contact_no2` varchar(20) DEFAULT NULL,
  `primary_email` varchar(30) DEFAULT NULL,
  `secondary_email` varchar(30) DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `updated_by` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monitoring_user_profile`
--

LOCK TABLES `monitoring_user_profile` WRITE;
/*!40000 ALTER TABLE `monitoring_user_profile` DISABLE KEYS */;
INSERT INTO `monitoring_user_profile` VALUES (1,1,'ADMIN ','','','','','','',NULL,NULL,NULL,'');
/*!40000 ALTER TABLE `monitoring_user_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monitoring_users`
--

DROP TABLE IF EXISTS `monitoring_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monitoring_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `user_type` enum('ADMIN','USER') DEFAULT 'USER',
  `active` enum('Y','N') DEFAULT 'Y',
  `created_datetime` datetime DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `updated_by` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monitoring_users`
--

LOCK TABLES `monitoring_users` WRITE;
/*!40000 ALTER TABLE `monitoring_users` DISABLE KEYS */;
INSERT INTO `monitoring_users` VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3','ADMIN','Y',NULL,'',NULL,'admin@127.0.0.1');
/*!40000 ALTER TABLE `monitoring_users` ENABLE KEYS */;
UNLOCK TABLES;

