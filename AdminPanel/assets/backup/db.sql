-- MariaDB dump 10.19  Distrib 10.6.12-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: cyber_panel
-- ------------------------------------------------------
-- Server version	10.6.12-MariaDB-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cp_admins`
--

DROP TABLE IF EXISTS `cp_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cp_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `credit` float NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `ctime` int(11) NOT NULL,
  `utime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cp_admins`
--

LOCK TABLES `cp_admins` WRITE;
/*!40000 ALTER TABLE `cp_admins` DISABLE KEYS */;
INSERT INTO `cp_admins` VALUES (1,'admin','$2y$10$ZiEM0Flg8aEcAwv5Cb5v3.fhReAk8L1knxn5uKE0VsIdghIYKSZqO','مدیر پنل','admin',0,1,1691287888,1691525834);
/*!40000 ALTER TABLE `cp_admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cp_public_apis`
--

DROP TABLE IF EXISTS `cp_public_apis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cp_public_apis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(110) NOT NULL,
  `api_key` varchar(100) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `api_key` (`api_key`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cp_public_apis`
--

LOCK TABLES `cp_public_apis` WRITE;
/*!40000 ALTER TABLE `cp_public_apis` DISABLE KEYS */;
/*!40000 ALTER TABLE `cp_public_apis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cp_settings`
--

DROP TABLE IF EXISTS `cp_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cp_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cp_settings`
--

LOCK TABLES `cp_settings` WRITE;
/*!40000 ALTER TABLE `cp_settings` DISABLE KEYS */;
INSERT INTO `cp_settings` VALUES (1,'ssh_port','22'),(2,'udp_port','7300'),(3,'multiuser','1'),(4,'fake_url','https://google.com'),(5,'app_version','1');
/*!40000 ALTER TABLE `cp_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cp_traffics`
--

DROP TABLE IF EXISTS `cp_traffics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cp_traffics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `download` float NOT NULL,
  `upload` float NOT NULL,
  `total` float NOT NULL,
  `ctime` int(11) NOT NULL,
  `utime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cp_traffics`
--

LOCK TABLES `cp_traffics` WRITE;
/*!40000 ALTER TABLE `cp_traffics` DISABLE KEYS */;
/*!40000 ALTER TABLE `cp_traffics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cp_users`
--

DROP TABLE IF EXISTS `cp_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cp_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `admin_uname` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `desc` text NOT NULL,
  `limit_users` int(11) NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `expiry_days` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `traffic` float NOT NULL,
  `ctime` int(11) NOT NULL,
  `utime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `admin_uname` (`admin_uname`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cp_users`
--

LOCK TABLES `cp_users` WRITE;
/*!40000 ALTER TABLE `cp_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `cp_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-08-13 15:20:45
