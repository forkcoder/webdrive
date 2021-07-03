-- MySQL dump 10.13  Distrib 8.0.23, for osx10.16 (x86_64)
--
-- Host: localhost    Database: fcoder_dev
-- ------------------------------------------------------
-- Server version	8.0.23

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `fcoder_access_log`
--

DROP TABLE IF EXISTS `fcoder_access_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fcoder_access_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `u_id` varchar(30) NOT NULL,
  `genid` varchar(8) NOT NULL,
  `login_at` datetime NOT NULL,
  `logout_at` datetime DEFAULT NULL,
  `access_status` varchar(10) NOT NULL,
  `client_browser` varchar(45) NOT NULL DEFAULT '',
  `client_version` varchar(45) NOT NULL DEFAULT '',
  `client_ipaddress` varchar(15) NOT NULL DEFAULT '',
  `client_hostname` varchar(45) NOT NULL DEFAULT '',
  `client_platform` varchar(45) NOT NULL DEFAULT '',
  `geo_gspace` varchar(20) DEFAULT NULL,
  `geo_country` varchar(100) DEFAULT NULL,
  `geo_city` varchar(100) DEFAULT NULL,
  `geo_latitude` decimal(11,8) DEFAULT NULL,
  `geo_longitude` decimal(11,8) DEFAULT NULL,
  `geo_currency` varchar(15) DEFAULT NULL,
  `geo_currencycode` varchar(45) DEFAULT NULL,
  `geo_timezone` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fcoder_access_log`
--

LOCK TABLES `fcoder_access_log` WRITE;
/*!40000 ALTER TABLE `fcoder_access_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `fcoder_access_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fcoder_users`
--

DROP TABLE IF EXISTS `fcoder_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fcoder_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `genid` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `gspace` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `lspace` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `actype` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `accountno` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `contact_no` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `remember_token` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_hash` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `userid` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `iwau_event_id` bigint DEFAULT NULL,
  `upload_limit` int NOT NULL DEFAULT '20',
  `wstorage_limit` int NOT NULL DEFAULT '1024',
  `total_uploads` int NOT NULL DEFAULT '5',
  `total_recipients` int NOT NULL DEFAULT '5',
  `file_livetime` int NOT NULL DEFAULT '86400',
  `wshare_limit` int NOT NULL DEFAULT '100',
  `wstorage_data_bytes` bigint NOT NULL DEFAULT '0',
  `wshare_data_bytes` bigint NOT NULL DEFAULT '0',
  `wshare_access` tinyint NOT NULL DEFAULT '0',
  `wdrive_access` tinyint NOT NULL DEFAULT '0',
  `ftransfer_access` tinyint NOT NULL DEFAULT '0',
  `uassets_access` tinyint NOT NULL DEFAULT '0',
  `cschedule_access` tinyint NOT NULL DEFAULT '0',
  `hadmin_access` tinyint NOT NULL DEFAULT '0',
  `amgmt_access` tinyint NOT NULL DEFAULT '0',
  `mdesk_access` tinyint NOT NULL DEFAULT '1',
  `avater_count` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fcoder_users`
--

LOCK TABLES `fcoder_users` WRITE;
/*!40000 ALTER TABLE `fcoder_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `fcoder_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fcoder_webdrive_log`
--

DROP TABLE IF EXISTS `fcoder_webdrive_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fcoder_webdrive_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `wdl_action` varchar(20) NOT NULL,
  `wdl_src` varchar(255) NOT NULL,
  `wdl_dest` varchar(255) DEFAULT NULL,
  `wdl_share_id` int DEFAULT NULL,
  `wdl_status` tinyint NOT NULL,
  `wdl_msg` int NOT NULL,
  `wdl_datetime` datetime NOT NULL,
  `wdl_iuser_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fcoder_webdrive_log`
--

LOCK TABLES `fcoder_webdrive_log` WRITE;
/*!40000 ALTER TABLE `fcoder_webdrive_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `fcoder_webdrive_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fcoder_webdrive_share`
--

DROP TABLE IF EXISTS `fcoder_webdrive_share`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fcoder_webdrive_share` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `wds_name` varchar(100) NOT NULL,
  `wds_created_at` datetime NOT NULL,
  `wds_owner` varchar(8) NOT NULL,
  `wds_title` varchar(100) NOT NULL,
  `wds_base` varchar(45) NOT NULL,
  `wds_path` varchar(1000) NOT NULL,
  `wds_status` tinyint NOT NULL,
  `wds_count` int NOT NULL,
  `wds_size` bigint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fcoder_webdrive_share`
--

LOCK TABLES `fcoder_webdrive_share` WRITE;
/*!40000 ALTER TABLE `fcoder_webdrive_share` DISABLE KEYS */;
/*!40000 ALTER TABLE `fcoder_webdrive_share` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fcoder_webdrive_sharemap`
--

DROP TABLE IF EXISTS `fcoder_webdrive_sharemap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fcoder_webdrive_sharemap` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `wdsm_share_id` int NOT NULL,
  `wdsm_iuser_id` varchar(8) NOT NULL,
  `wdsm_shared_at` datetime DEFAULT NULL,
  `wdsm_readonly` tinyint NOT NULL DEFAULT '1',
  `wdsm_status` tinyint NOT NULL DEFAULT '0',
  `wdsm_removed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fcoder_webdrive_sharemap`
--

LOCK TABLES `fcoder_webdrive_sharemap` WRITE;
/*!40000 ALTER TABLE `fcoder_webdrive_sharemap` DISABLE KEYS */;
/*!40000 ALTER TABLE `fcoder_webdrive_sharemap` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-07-03 21:39:45
