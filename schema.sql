-- MariaDB dump 10.19  Distrib 10.8.4-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: heating_equipment_store
-- ------------------------------------------------------
-- Server version	10.8.4-MariaDB

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
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(600) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories_new`
--

DROP TABLE IF EXISTS `categories_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories_new` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories_new`
--

LOCK TABLES `categories_new` WRITE;
/*!40000 ALTER TABLE `categories_new` DISABLE KEYS */;
/*!40000 ALTER TABLE `categories_new` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_documents`
--

DROP TABLE IF EXISTS `company_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_documents`
--

LOCK TABLES `company_documents` WRITE;
/*!40000 ALTER TABLE `company_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_requests`
--

DROP TABLE IF EXISTS `contact_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('new','processed','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `admin_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_requests`
--

LOCK TABLES `contact_requests` WRITE;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `installation_requests`
--

DROP TABLE IF EXISTS `installation_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `installation_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `installer_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `request_type` enum('consultation','installation','repair','maintenance') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` time DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','confirmed','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `total_cost` decimal(10,2) DEFAULT NULL,
  `admin_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `installation_requests`
--

LOCK TABLES `installation_requests` WRITE;
UNLOCK TABLES;

--
-- Table structure for table `installer_applications`
--

DROP TABLE IF EXISTS `installer_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `installer_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialization` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `experience_years` int(11) DEFAULT 0,
  `skills` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('new','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `admin_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `installer_applications`
--

LOCK TABLES `installer_applications` WRITE;
UNLOCK TABLES;

--
-- Table structure for table `installer_details`
--

DROP TABLE IF EXISTS `installer_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `installer_details` (
  `id` int(11) NOT NULL,
  `installer_id` int(11) NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `education` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certifications` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skills` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `languages` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_schedule` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equipment_list` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_areas` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance_info` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `services_list` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `installer_details`
--

LOCK TABLES `installer_details` WRITE;
/*!40000 ALTER TABLE `installer_details` DISABLE KEYS */;
INSERT INTO `installer_details` VALUES
(1,1,'РћРїС‹С‚РЅС‹Р№ СЃРїРµС†РёР°Р»РёСЃС‚ РїРѕ РјРѕРЅС‚Р°Р¶Сѓ РѕС‚РѕРїРёС‚РµР»СЊРЅС‹С… СЃРёСЃС‚РµРј. Р Р°Р±РѕС‚Р°СЋ СЃ Р»СЋР±С‹РјРё С‚РёРїР°РјРё РєРѕС‚Р»РѕРІ Рё СЂР°РґРёР°С‚РѕСЂРѕРІ.','РњР“РЎРЈ, С„Р°РєСѓР»СЊС‚РµС‚ С‚РµРїР»РѕРіР°Р·РѕСЃРЅР°Р±Р¶РµРЅРёСЏ','РЎРµСЂС‚РёС„РёРєР°С‚ Gazprom, РЎРµСЂС‚РёС„РёРєР°С‚ Viessmann','РњРѕРЅС‚Р°Р¶ РєРѕС‚Р»РѕРІ, СѓСЃС‚Р°РЅРѕРІРєР° СЂР°РґРёР°С‚РѕСЂРѕРІ, РїСЂРѕРєР»Р°РґРєР° С‚СЂСѓР±РѕРїСЂРѕРІРѕРґРѕРІ',NULL,NULL,NULL,'РњРѕСЃРєРІР° Рё РњРѕСЃРєРѕРІСЃРєР°СЏ РѕР±Р»Р°СЃС‚СЊ РІ СЂР°РґРёСѓСЃРµ 50 РєРј',NULL,'2025-11-21 05:30:12','2025-11-25 05:29:03','asdasdasd','12312312'),
(2,2,'РџСЂРѕС„РµСЃСЃРёРѕРЅР°Р»СЊРЅС‹Р№ СЃР°РЅС‚РµС…РЅРёРє СЃ Р±РѕР»СЊС€РёРј РѕРїС‹С‚РѕРј СЂР°Р±РѕС‚С‹. РЎРїРµС†РёР°Р»РёР·РёСЂСѓСЋСЃСЊ РЅР° РјРѕРЅС‚Р°Р¶Рµ С‚РµРїР»С‹С… РїРѕР»РѕРІ Рё СЃР°РЅС‚РµС…РЅРёС‡РµСЃРєРёС… СЂР°Р±РѕС‚.','РљРѕР»Р»РµРґР¶ СЃР°РЅС‚РµС…РЅРёС‡РµСЃРєРѕРіРѕ РѕР±РѕСЂСѓРґРѕРІР°РЅРёСЏ','РЎРµСЂС‚РёС„РёРєР°С‚ Rehau, РЎРµСЂС‚РёС„РёРєР°С‚ Uponor','РњРѕРЅС‚Р°Р¶ С‚РµРїР»С‹С… РїРѕР»РѕРІ, СѓСЃС‚Р°РЅРѕРІРєР° СЃР°РЅС‚РµС…РЅРёРєРё, СЂРµРјРѕРЅС‚РЅС‹Рµ СЂР°Р±РѕС‚С‹',NULL,NULL,NULL,'РњРѕСЃРєРІР°, Р—РµР»РµРЅРѕРіСЂР°Рґ, РҐРёРјРєРё',NULL,'2025-11-21 05:30:12','2025-11-21 05:30:12',NULL,NULL),
(3,3,'РЎРїРµС†РёР°Р»РёСЃС‚ РїРѕ СѓСЃС‚Р°РЅРѕРІРєРµ СЂР°РґРёР°С‚РѕСЂРѕРІ Рё СЃР°РЅС‚РµС…РЅРёС‡РµСЃРєРѕРјСѓ РѕР±РѕСЂСѓРґРѕРІР°РЅРёСЋ. РљР°С‡РµСЃС‚РІРµРЅРЅРѕ Рё РІ СЃСЂРѕРє РІС‹РїРѕР»РЅСЏСЋ СЂР°Р±РѕС‚С‹ Р»СЋР±РѕР№ СЃР»РѕР¶РЅРѕСЃС‚Рё.','РўРµС…РЅРёРєСѓРј РјРѕРЅС‚Р°Р¶РЅРѕРіРѕ РѕР±РѕСЂСѓРґРѕРІР°РЅРёСЏ','РЎРµСЂС‚РёС„РёРєР°С‚ Global Radiatori, РЎРµСЂС‚РёС„РёРєР°С‚ Kermi','РЈСЃС‚Р°РЅРѕРІРєР° СЂР°РґРёР°С‚РѕСЂРѕРІ, Р·Р°РјРµРЅР° СЃР°РЅС‚РµС…РЅРёРєРё, СѓСЃС‚СЂР°РЅРµРЅРёРµ РїСЂРѕС‚РµС‡РµРє',NULL,NULL,NULL,'РњРѕСЃРєРѕРІСЃРєР°СЏ РѕР±Р»Р°СЃС‚СЊ, Р±Р»РёР¶РЅРµРµ РџРѕРґРјРѕСЃРєРѕРІСЊРµ',NULL,'2025-11-21 05:30:12','2025-11-21 05:30:12',NULL,NULL),
(4,1,'РћРїС‹С‚РЅС‹Р№ СЃРїРµС†РёР°Р»РёСЃС‚ РїРѕ РјРѕРЅС‚Р°Р¶Сѓ РѕС‚РѕРїРёС‚РµР»СЊРЅС‹С… СЃРёСЃС‚РµРј. Р Р°Р±РѕС‚Р°СЋ СЃ Р»СЋР±С‹РјРё С‚РёРїР°РјРё РєРѕС‚Р»РѕРІ Рё СЂР°РґРёР°С‚РѕСЂРѕРІ.','РњР“РЎРЈ, С„Р°РєСѓР»СЊС‚РµС‚ С‚РµРїР»РѕРіР°Р·РѕСЃРЅР°Р±Р¶РµРЅРёСЏ','РЎРµСЂС‚РёС„РёРєР°С‚ Gazprom, РЎРµСЂС‚РёС„РёРєР°С‚ Viessmann','РњРѕРЅС‚Р°Р¶ РєРѕС‚Р»РѕРІ, СѓСЃС‚Р°РЅРѕРІРєР° СЂР°РґРёР°С‚РѕСЂРѕРІ, РїСЂРѕРєР»Р°РґРєР° С‚СЂСѓР±РѕРїСЂРѕРІРѕРґРѕРІ',NULL,NULL,NULL,'РњРѕСЃРєРІР° Рё РњРѕСЃРєРѕРІСЃРєР°СЏ РѕР±Р»Р°СЃС‚СЊ РІ СЂР°РґРёСѓСЃРµ 50 РєРј',NULL,'2025-11-21 05:31:59','2025-11-25 05:29:03','asdasdasd','12312312'),
(5,2,'РџСЂРѕС„РµСЃСЃРёРѕРЅР°Р»СЊРЅС‹Р№ СЃР°РЅС‚РµС…РЅРёРє СЃ Р±РѕР»СЊС€РёРј РѕРїС‹С‚РѕРј СЂР°Р±РѕС‚С‹. РЎРїРµС†РёР°Р»РёР·РёСЂСѓСЋСЃСЊ РЅР° РјРѕРЅС‚Р°Р¶Рµ С‚РµРїР»С‹С… РїРѕР»РѕРІ Рё СЃР°РЅС‚РµС…РЅРёС‡РµСЃРєРёС… СЂР°Р±РѕС‚.','РљРѕР»Р»РµРґР¶ СЃР°РЅС‚РµС…РЅРёС‡РµСЃРєРѕРіРѕ РѕР±РѕСЂСѓРґРѕРІР°РЅРёСЏ','РЎРµСЂС‚РёС„РёРєР°С‚ Rehau, РЎРµСЂС‚РёС„РёРєР°С‚ Uponor','РњРѕРЅС‚Р°Р¶ С‚РµРїР»С‹С… РїРѕР»РѕРІ, СѓСЃС‚Р°РЅРѕРІРєР° СЃР°РЅС‚РµС…РЅРёРєРё, СЂРµРјРѕРЅС‚РЅС‹Рµ СЂР°Р±РѕС‚С‹',NULL,NULL,NULL,'РњРѕСЃРєРІР°, Р—РµР»РµРЅРѕРіСЂР°Рґ, РҐРёРјРєРё',NULL,'2025-11-21 05:31:59','2025-11-21 05:31:59',NULL,NULL),
(6,3,'РЎРїРµС†РёР°Р»РёСЃС‚ РїРѕ СѓСЃС‚Р°РЅРѕРІРєРµ СЂР°РґРёР°С‚РѕСЂРѕРІ Рё СЃР°РЅС‚РµС…РЅРёС‡РµСЃРєРѕРјСѓ РѕР±РѕСЂСѓРґРѕРІР°РЅРёСЋ. РљР°С‡РµСЃС‚РІРµРЅРЅРѕ Рё РІ СЃСЂРѕРє РІС‹РїРѕР»РЅСЏСЋ СЂР°Р±РѕС‚С‹ Р»СЋР±РѕР№ СЃР»РѕР¶РЅРѕСЃС‚Рё.','РўРµС…РЅРёРєСѓРј РјРѕРЅС‚Р°Р¶РЅРѕРіРѕ РѕР±РѕСЂСѓРґРѕРІР°РЅРёСЏ','РЎРµСЂС‚РёС„РёРєР°С‚ Global Radiatori, РЎРµСЂС‚РёС„РёРєР°С‚ Kermi','РЈСЃС‚Р°РЅРѕРІРєР° СЂР°РґРёР°С‚РѕСЂРѕРІ, Р·Р°РјРµРЅР° СЃР°РЅС‚РµС…РЅРёРєРё, СѓСЃС‚СЂР°РЅРµРЅРёРµ РїСЂРѕС‚РµС‡РµРє',NULL,NULL,NULL,'РњРѕСЃРєРѕРІСЃРєР°СЏ РѕР±Р»Р°СЃС‚СЊ, Р±Р»РёР¶РЅРµРµ РџРѕРґРјРѕСЃРєРѕРІСЊРµ',NULL,'2025-11-21 05:31:59','2025-11-21 05:31:59',NULL,NULL),
(1,1,'РћРїС‹С‚РЅС‹Р№ СЃРїРµС†РёР°Р»РёСЃС‚ РїРѕ РјРѕРЅС‚Р°Р¶Сѓ РѕС‚РѕРїРёС‚РµР»СЊРЅС‹С… СЃРёСЃС‚РµРј. Р Р°Р±РѕС‚Р°СЋ СЃ Р»СЋР±С‹РјРё С‚РёРїР°РјРё РєРѕС‚Р»РѕРІ Рё СЂР°РґРёР°С‚РѕСЂРѕРІ.','РњР“РЎРЈ, С„Р°РєСѓР»СЊС‚РµС‚ С‚РµРїР»РѕРіР°Р·РѕСЃРЅР°Р±Р¶РµРЅРёСЏ','РЎРµСЂС‚РёС„РёРєР°С‚ Gazprom, РЎРµСЂС‚РёС„РёРєР°С‚ Viessmann','РњРѕРЅС‚Р°Р¶ РєРѕС‚Р»РѕРІ, СѓСЃС‚Р°РЅРѕРІРєР° СЂР°РґРёР°С‚РѕСЂРѕРІ, РїСЂРѕРєР»Р°РґРєР° С‚СЂСѓР±РѕРїСЂРѕРІРѕРґРѕРІ',NULL,NULL,NULL,'РњРѕСЃРєРІР° Рё РњРѕСЃРєРѕРІСЃРєР°СЏ РѕР±Р»Р°СЃС‚СЊ РІ СЂР°РґРёСѓСЃРµ 50 РєРј',NULL,'2025-11-21 05:30:12','2025-11-25 05:29:03','asdasdasd','12312312'),
(2,2,'РџСЂРѕС„РµСЃСЃРёРѕРЅР°Р»СЊРЅС‹Р№ СЃР°РЅС‚РµС…РЅРёРє СЃ Р±РѕР»СЊС€РёРј РѕРїС‹С‚РѕРј СЂР°Р±РѕС‚С‹. РЎРїРµС†РёР°Р»РёР·РёСЂСѓСЋСЃСЊ РЅР° РјРѕРЅС‚Р°Р¶Рµ С‚РµРїР»С‹С… РїРѕР»РѕРІ Рё СЃР°РЅС‚РµС…РЅРёС‡РµСЃРєРёС… СЂР°Р±РѕС‚.','РљРѕР»Р»РµРґР¶ СЃР°РЅС‚РµС…РЅРёС‡РµСЃРєРѕРіРѕ РѕР±РѕСЂСѓРґРѕРІР°РЅРёСЏ','РЎРµСЂС‚РёС„РёРєР°С‚ Rehau, РЎРµСЂС‚РёС„РёРєР°С‚ Uponor','РњРѕРЅС‚Р°Р¶ С‚РµРїР»С‹С… РїРѕР»РѕРІ, СѓСЃС‚Р°РЅРѕРІРєР° СЃР°РЅС‚РµС…РЅРёРєРё, СЂРµРјРѕРЅС‚РЅС‹Рµ СЂР°Р±РѕС‚С‹',NULL,NULL,NULL,'РњРѕСЃРєРІР°, Р—РµР»РµРЅРѕРіСЂР°Рґ, РҐРёРјРєРё',NULL,'2025-11-21 05:30:12','2025-11-21 05:30:12',NULL,NULL),
(3,3,'РЎРїРµС†РёР°Р»РёСЃС‚ РїРѕ СѓСЃС‚Р°РЅРѕРІРєРµ СЂР°РґРёР°С‚РѕСЂРѕРІ Рё СЃР°РЅС‚РµС…РЅРёС‡РµСЃРєРѕРјСѓ РѕР±РѕСЂСѓРґРѕРІР°РЅРёСЋ. РљР°С‡РµСЃС‚РІРµРЅРЅРѕ Рё РІ СЃСЂРѕРє РІС‹РїРѕР»РЅСЏСЋ СЂР°Р±РѕС‚С‹ Р»СЋР±РѕР№ СЃР»РѕР¶РЅРѕСЃС‚Рё.','РўРµС…РЅРёРєСѓРј РјРѕРЅС‚Р°Р¶РЅРѕРіРѕ РѕР±РѕСЂСѓРґРѕРІР°РЅРёСЏ','РЎРµСЂС‚РёС„РёРєР°С‚ Global Radiatori, РЎРµСЂС‚РёС„РёРєР°С‚ Kermi','РЈСЃС‚Р°РЅРѕРІРєР° СЂР°РґРёР°С‚РѕСЂРѕРІ, Р·Р°РјРµРЅР° СЃР°РЅС‚РµС…РЅРёРєРё, СѓСЃС‚СЂР°РЅРµРЅРёРµ РїСЂРѕС‚РµС‡РµРє',NULL,NULL,NULL,'РњРѕСЃРєРѕРІСЃРєР°СЏ РѕР±Р»Р°СЃС‚СЊ, Р±Р»РёР¶РЅРµРµ РџРѕРґРјРѕСЃРєРѕРІСЊРµ',NULL,'2025-11-21 05:30:12','2025-11-21 05:30:12',NULL,NULL),
(4,1,'РћРїС‹С‚РЅС‹Р№ СЃРїРµС†РёР°Р»РёСЃС‚ РїРѕ РјРѕРЅС‚Р°Р¶Сѓ РѕС‚РѕРїРёС‚РµР»СЊРЅС‹С… СЃРёСЃС‚РµРј. Р Р°Р±РѕС‚Р°СЋ СЃ Р»СЋР±С‹РјРё С‚РёРїР°РјРё РєРѕС‚Р»РѕРІ Рё СЂР°РґРёР°С‚РѕСЂРѕРІ.','РњР“РЎРЈ, С„Р°РєСѓР»СЊС‚РµС‚ С‚РµРїР»РѕРіР°Р·РѕСЃРЅР°Р±Р¶РµРЅРёСЏ','РЎРµСЂС‚РёС„РёРєР°С‚ Gazprom, РЎРµСЂС‚РёС„РёРєР°С‚ Viessmann','РњРѕРЅС‚Р°Р¶ РєРѕС‚Р»РѕРІ, СѓСЃС‚Р°РЅРѕРІРєР° СЂР°РґРёР°С‚РѕСЂРѕРІ, РїСЂРѕРєР»Р°РґРєР° С‚СЂСѓР±РѕРїСЂРѕРІРѕРґРѕРІ',NULL,NULL,NULL,'РњРѕСЃРєРІР° Рё РњРѕСЃРєРѕРІСЃРєР°СЏ РѕР±Р»Р°СЃС‚СЊ РІ СЂР°РґРёСѓСЃРµ 50 РєРј',NULL,'2025-11-21 05:31:59','2025-11-25 05:29:03','asdasdasd','12312312'),
(5,2,'РџСЂРѕС„РµСЃСЃРёРѕРЅР°Р»СЊРЅС‹Р№ СЃР°РЅС‚РµС…РЅРёРє СЃ Р±РѕР»СЊС€РёРј РѕРїС‹С‚РѕРј СЂР°Р±РѕС‚С‹. РЎРїРµС†РёР°Р»РёР·РёСЂСѓСЋСЃСЊ РЅР° РјРѕРЅС‚Р°Р¶Рµ С‚РµРїР»С‹С… РїРѕР»РѕРІ Рё СЃР°РЅС‚РµС…РЅРёС‡РµСЃРєРёС… СЂР°Р±РѕС‚.','РљРѕР»Р»РµРґР¶ СЃР°РЅС‚РµС…РЅРёС‡РµСЃРєРѕРіРѕ РѕР±РѕСЂСѓРґРѕРІР°РЅРёСЏ','РЎРµСЂС‚РёС„РёРєР°С‚ Rehau, РЎРµСЂС‚РёС„РёРєР°С‚ Uponor','РњРѕРЅС‚Р°Р¶ С‚РµРїР»С‹С… РїРѕР»РѕРІ, СѓСЃС‚Р°РЅРѕРІРєР° СЃР°РЅС‚РµС…РЅРёРєРё, СЂРµРјРѕРЅС‚РЅС‹Рµ СЂР°Р±РѕС‚С‹',NULL,NULL,NULL,'РњРѕСЃРєРІР°, Р—РµР»РµРЅРѕРіСЂР°Рґ, РҐРёРјРєРё',NULL,'2025-11-21 05:31:59','2025-11-21 05:31:59',NULL,NULL),
(6,3,'РЎРїРµС†РёР°Р»РёСЃС‚ РїРѕ СѓСЃС‚Р°РЅРѕРІРєРµ СЂР°РґРёР°С‚РѕСЂРѕРІ Рё СЃР°РЅС‚РµС…РЅРёС‡РµСЃРєРѕРјСѓ РѕР±РѕСЂСѓРґРѕРІР°РЅРёСЋ. РљР°С‡РµСЃС‚РІРµРЅРЅРѕ Рё РІ СЃСЂРѕРє РІС‹РїРѕР»РЅСЏСЋ СЂР°Р±РѕС‚С‹ Р»СЋР±РѕР№ СЃР»РѕР¶РЅРѕСЃС‚Рё.','РўРµС…РЅРёРєСѓРј РјРѕРЅС‚Р°Р¶РЅРѕРіРѕ РѕР±РѕСЂСѓРґРѕРІР°РЅРёСЏ','РЎРµСЂС‚РёС„РёРєР°С‚ Global Radiatori, РЎРµСЂС‚РёС„РёРєР°С‚ Kermi','РЈСЃС‚Р°РЅРѕРІРєР° СЂР°РґРёР°С‚РѕСЂРѕРІ, Р·Р°РјРµРЅР° СЃР°РЅС‚РµС…РЅРёРєРё, СѓСЃС‚СЂР°РЅРµРЅРёРµ РїСЂРѕС‚РµС‡РµРє',NULL,NULL,NULL,'РњРѕСЃРєРѕРІСЃРєР°СЏ РѕР±Р»Р°СЃС‚СЊ, Р±Р»РёР¶РЅРµРµ РџРѕРґРјРѕСЃРєРѕРІСЊРµ',NULL,'2025-11-21 05:31:59','2025-11-21 05:31:59',NULL,NULL);
/*!40000 ALTER TABLE `installer_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `installer_portfolio`
--

DROP TABLE IF EXISTS `installer_portfolio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `installer_portfolio` (
  `id` int(11) NOT NULL,
  `installer_id` int(11) NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `installer_portfolio`
--

LOCK TABLES `installer_portfolio` WRITE;
/*!40000 ALTER TABLE `installer_portfolio` DISABLE KEYS */;
INSERT INTO `installer_portfolio` VALUES
(1,1,'/images/installers/portfolio/work_1764728345_692f9e19ce652.jpg','2025-12-03 02:19:05'),
(1,1,'/images/installers/portfolio/work_1764728345_692f9e19ce652.jpg','2025-12-03 02:19:05');
/*!40000 ALTER TABLE `installer_portfolio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `installer_requests`
--

DROP TABLE IF EXISTS `installer_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `installer_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `installer_id` int(11) NOT NULL,
  `request_type` enum('consultation','installation','repair','maintenance') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` time DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','confirmed','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `total_cost` decimal(10,2) DEFAULT NULL,
  `admin_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `installer_requests`
--

LOCK TABLES `installer_requests` WRITE;
/*!40000 ALTER TABLE `installer_requests` DISABLE KEYS */;
INSERT INTO `installer_requests` VALUES
(1,1,1,'maintenance','С„С‹Р°','2025-12-13','12:12:00','Р°С‹РІР°','Р«РІС‹РІ','+7 (222) 232-22-33','123123@123.com','pending',NULL,NULL,'2025-11-21 07:48:15','2025-11-21 07:48:15'),
(2,1,1,'installation','asfsd','2026-03-12','12:31:00','fsdfsdf','С„РІС„С‹РІС„С‹РІ','+7 (231) 231-23-12','W@asdfasdf.com','pending',NULL,NULL,'2025-11-21 07:49:45','2025-11-21 07:49:45'),
(1,1,1,'maintenance','С„С‹Р°','2025-12-13','12:12:00','Р°С‹РІР°','Р«РІС‹РІ','+7 (222) 232-22-33','123123@123.com','pending',NULL,NULL,'2025-11-21 07:48:15','2025-11-21 07:48:15'),
(2,1,1,'installation','asfsd','2026-03-12','12:31:00','fsdfsdf','С„РІС„С‹РІС„С‹РІ','+7 (231) 231-23-12','W@asdfasdf.com','pending',NULL,NULL,'2025-11-21 07:49:45','2025-11-21 07:49:45');
/*!40000 ALTER TABLE `installer_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `installer_reviews`
--

DROP TABLE IF EXISTS `installer_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `installer_reviews` (
  `id` int(11) NOT NULL,
  `installer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `installer_reviews`
--

LOCK TABLES `installer_reviews` WRITE;
/*!40000 ALTER TABLE `installer_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `installer_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `installers`
--

DROP TABLE IF EXISTS `installers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `installers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialization` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `completed_orders` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hourly_rate` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `languages` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equipment_list` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance_info` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `average_rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `installers`
--

LOCK TABLES `installers` WRITE;
/*!40000 ALTER TABLE `installers` DISABLE KEYS */;
/*!40000 ALTER TABLE `installers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loyalty_transactions`
--

DROP TABLE IF EXISTS `loyalty_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loyalty_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `points_change` int(11) NOT NULL,
  `transaction_type` enum('earn','spend','adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loyalty_transactions`
--

LOCK TABLES `loyalty_transactions` WRITE;
/*!40000 ALTER TABLE `loyalty_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `loyalty_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_method` enum('pickup','courier') COLLATE utf8mb4_unicode_ci DEFAULT 'pickup',
  `payment_method` enum('cash','card') COLLATE utf8mb4_unicode_ci DEFAULT 'cash',
  `comment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','confirmed','processing','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES
(4,4,'/images/products/bbq1.jpg','РљРѕРјРїР»РµРєСЃ Р±Р°СЂР±РµРєСЋ Р“СЂРёР»СЊРјР°СЃС‚РµСЂ',1,0,'2025-11-21 07:20:53'),
(5,5,'/images/products/pech2.jpg','Р§СѓРіСѓРЅРЅР°СЏ РїРµС‡СЊ Р СѓСЃСЃРєР°СЏ',1,0,'2025-11-21 07:20:53'),
(6,6,'/images/products/smes1.jpg','РЎРјРµСЃРёС‚РµР»СЊ РґР»СЏ РєСѓС…РЅРё РђРєРІР°',1,0,'2025-11-21 07:20:53'),
(8,2,'/images/products/prod_2_6925410c87f1f.png',NULL,1,0,'2025-11-25 05:39:24'),
(9,3,'/images/products/prod_1764728378_692f9e3a5c83d.jpg',NULL,0,0,'2025-12-03 02:19:38'),
(4,4,'/images/products/bbq1.jpg','РљРѕРјРїР»РµРєСЃ Р±Р°СЂР±РµРєСЋ Р“СЂРёР»СЊРјР°СЃС‚РµСЂ',1,0,'2025-11-21 07:20:53'),
(5,5,'/images/products/pech2.jpg','Р§СѓРіСѓРЅРЅР°СЏ РїРµС‡СЊ Р СѓСЃСЃРєР°СЏ',1,0,'2025-11-21 07:20:53'),
(6,6,'/images/products/smes1.jpg','РЎРјРµСЃРёС‚РµР»СЊ РґР»СЏ РєСѓС…РЅРё РђРєРІР°',1,0,'2025-11-21 07:20:53'),
(8,2,'/images/products/prod_2_6925410c87f1f.png',NULL,1,0,'2025-11-25 05:39:24'),
(9,3,'/images/products/prod_1764728378_692f9e3a5c83d.jpg',NULL,0,0,'2025-12-03 02:19:38');
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5,
  `comment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
INSERT INTO `product_reviews` VALUES
(2,3,8,5,'С‹РІС„С‹РІС„С‹РІ','2025-12-10 06:48:10'),
(3,3,1,5,'С„РІС„С‹','2025-12-10 06:52:09'),
(5,3,4,5,'1123','2025-12-10 08:56:45'),
(2,3,8,5,'С‹РІС„С‹РІС„С‹РІ','2025-12-10 06:48:10'),
(3,3,1,5,'С„РІС„С‹','2025-12-10 06:52:09'),
(5,3,4,5,'1123','2025-12-10 08:56:45');
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `warranty_months` int(11) DEFAULT 12,
  `installation_required` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotions`
--

DROP TABLE IF EXISTS `promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promotions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Р—Р°РіРѕР»РѕРІРѕРє Р°РєС†РёРё',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'РџРѕР»РЅРѕРµ РѕРїРёСЃР°РЅРёРµ',
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'РџСѓС‚СЊ Рє РєР°СЂС‚РёРЅРєРµ',
  `discount_percent` int(11) DEFAULT NULL COMMENT 'РџСЂРѕС†РµРЅС‚ СЃРєРёРґРєРё (РґР»СЏ Р±РµР№РґР¶РёРєР°)',
  `start_date` datetime DEFAULT current_timestamp() COMMENT 'РќР°С‡Р°Р»Рѕ Р°РєС†РёРё',
  `end_date` datetime DEFAULT NULL COMMENT 'РљРѕРЅРµС† Р°РєС†РёРё (NULL = Р±РµСЃСЃСЂРѕС‡РЅРѕ)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1 - Р°РєС‚РёРІРЅР°, 0 - СЃРєСЂС‹С‚Р°',
  `sort_order` int(11) DEFAULT 0 COMMENT 'РџРѕСЂСЏРґРѕРє СЃРѕСЂС‚РёСЂРѕРІРєРё',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotions`
--

LOCK TABLES `promotions` WRITE;
/*!40000 ALTER TABLE `promotions` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review_stats`
--

DROP TABLE IF EXISTS `review_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review_stats` (
  `id` int(11) NOT NULL,
  `total_reviews` int(11) DEFAULT 0,
  `average_rating` decimal(3,2) DEFAULT 0.00,
  `last_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_stats`
--

LOCK TABLES `review_stats` WRITE;
/*!40000 ALTER TABLE `review_stats` DISABLE KEYS */;
INSERT INTO `review_stats` VALUES
(1,0,0.00,'2025-11-24 09:42:06'),
(1,0,0.00,'2025-11-24 09:42:06');
/*!40000 ALTER TABLE `review_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `installer_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_reviews`
--

DROP TABLE IF EXISTS `site_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5,
  `comment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_reviews`
--

LOCK TABLES `site_reviews` WRITE;
/*!40000 ALTER TABLE `site_reviews` DISABLE KEYS */;
INSERT INTO `site_reviews` VALUES
(4,4,5,'123','2025-12-10 06:53:53'),
(5,4,5,'С„С‹РІС‹С„','2025-12-10 06:53:54'),
(4,4,5,'123','2025-12-10 06:53:53'),
(5,4,5,'С„С‹РІС‹С„','2025-12-10 06:53:54'),
(0,24,3,'asdas','2026-02-10 16:49:53');
/*!40000 ALTER TABLE `site_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','moderator','customer','installer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `loyalty_points` int(11) DEFAULT 0,
  `personal_data_agreed` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'РЎРѕРіР»Р°СЃРёРµ РЅР° РѕР±СЂР°Р±РѕС‚РєСѓ РїРµСЂСЃРѕРЅР°Р»СЊРЅС‹С… РґР°РЅРЅС‹С… (152-Р¤Р—)',
  `personal_data_agreed_at` timestamp NULL DEFAULT NULL COMMENT 'Р”Р°С‚Р° Рё РІСЂРµРјСЏ РІС‹РґР°С‡Рё СЃРѕРіР»Р°СЃРёСЏ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (id, username, email, password, first_name, last_name, role, is_active, created_at, personal_data_agreed) VALUES
(1,'admin','admin@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Администратор','Системы','admin',1,NOW(),1),
(2,'moderator','moderator@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Модератор','Контента','moderator',1,NOW(),1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `show_on_home` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `videos`
--

LOCK TABLES `videos` WRITE;
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;
INSERT INTO `videos` VALUES
(5,'123','','',1,0,0,'2026-01-11 04:12:56');
/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-02 16:11:48

