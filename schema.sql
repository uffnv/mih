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




/*!40101 SET character_set_client = @saved_cs_client */;





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




/*!40101 SET character_set_client = @saved_cs_client */;








/*!40101 SET character_set_client = @saved_cs_client */;





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




/*!40101 SET character_set_client = @saved_cs_client */;





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




/*!40101 SET character_set_client = @saved_cs_client */;





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

-- =============================================================================
-- БАЗОВЫЕ ДАННЫЕ ДЛЯ МАГАЗИНА «ТЕРРИТОРИЯ ТЕПЛА» (territoriatepla.ru)
-- ООО «МП ТЕРРИТОРИЯ ТЕПЛА», с. Майма, ул. Энергетиков, д. 24, Республика Алтай
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. КАТЕГОРИИ КАТАЛОГА
INSERT INTO `categories` (`id`, `name`, `description`, `parent_id`, `image_path`, `is_active`, `sort_order`, `created_at`) VALUES
(1, 'Котлы отопления', 'Твердотопливные, газовые, электрические и комбинированные котлы', NULL, 'images/categories/cat_boilers.jpg', 1, 1, NOW()),
(2, 'Печи для бани и дома', 'Дровяные и чугунные банные печи, отопительные печи длительного горения', NULL, 'images/categories/cat_stoves.jpg', 1, 2, NOW()),
(3, 'Радиаторы отопления', 'Биметаллические, алюминиевые и стальные панельные радиаторы', NULL, 'images/categories/cat_radiators.jpg', 1, 3, NOW()),
(4, 'Теплые полы и автоматика', 'Трубы PE-RT/PEX, коллекторные группы, смесительные узлы, терморегуляторы', NULL, 'images/categories/cat_floor.jpg', 1, 4, NOW()),
(5, 'Дымоходы и вентиляция', 'Сэндвич-дымоходы из нержавеющей стали AISI 430/304, баки для воды, фланцы', NULL, 'images/categories/cat_chimneys.jpg', 1, 5, NOW()),
(6, 'Водонагреватели и бойлеры', 'Бойлеры косвенного нагрева, накопительные и проточные водонагреватели', NULL, 'images/categories/cat_water_heaters.jpg', 1, 6, NOW()),
(7, 'Насосное оборудование', 'Циркуляционные насосы для отопления, скважинные насосы и гидроаккумуляторы', NULL, 'images/categories/cat_pumps.jpg', 1, 7, NOW()),
(8, 'Трубы и фитинги', 'Полипропилен, сшитый полиэтилен, металлопластик, запорная арматура', NULL, 'images/categories/cat_pipes.jpg', 1, 8, NOW()),
(9, 'Мангалы и барбекю', 'Стальные мангалы, чугунные казаны, коптильни и барбекю-комплексы', NULL, 'images/categories/cat_bbq.jpg', 1, 9, NOW()),
(10, 'Сантехника и смесители', 'Смесители, душевые стойки, сифоны и санфаянс', NULL, 'images/categories/cat_plumbing.jpg', 1, 10, NOW())
ON DUPLICATE KEY UPDATE 
    `name` = VALUES(`name`),
    `description` = VALUES(`description`),
    `is_active` = VALUES(`is_active`),
    `sort_order` = VALUES(`sort_order`);

-- 2. ТОВАРЫ КАТАЛОГА (ХИТЫ И ОСНОВНОЙ АССОРТИМЕНТ)
INSERT INTO `products` (`id`, `name`, `description`, `short_description`, `price`, `old_price`, `sku`, `category_id`, `promotion_id`, `stock_quantity`, `is_active`, `is_featured`, `warranty_months`, `installation_required`, `created_at`, `updated_at`) VALUES
(1, 'Дровяная печь Теплодар Русь 18 Л', 'Надежная банная печь для парилок объемом от 10 до 18 куб.м. Изготовлена из жаростойкой нержавеющей стали с содержанием хрома до 13%. Обеспечивает мягкий влажный пар и легкий нагрев камней.', 'Банная печь из жаростойкой стали для парилок 10–18 м³', 26500.00, 29800.00, 'PCH-RUS-18', 2, NULL, 7, 1, 1, 36, 1, NOW(), NOW()),
(2, 'Твердотопливный котел ZOTA Тополь-М 20', 'Универсальный котел длительного горения мощностью 20 кВт. Отапливаемая площадь до 200 м². Работает на угле и дровах, возможность установки ТЭНа или пеллетной горелки.', 'Котел твердотопливный 20 кВт для домов до 200 м²', 58900.00, 64500.00, 'KTL-ZOTA-20', 1, NULL, 5, 1, 1, 24, 1, NOW(), NOW()),
(3, 'Биметаллический радиатор Royal Thermo Revolution Bimetall 500 (10 секций)', 'Сверхпрочный биметаллический секционный радиатор с волнообразным оребрением. Теплоотдача секции 168 Вт. Рабочее давление до 30 бар. Защита от гидроударов.', '10-секционный радиатор повышенной теплоотдачи, 1680 Вт', 9800.00, 11200.00, 'RAD-ROYAL-10', 3, NULL, 24, 1, 1, 120, 1, NOW(), NOW()),
(4, 'Чугунная печь для бани Везувий Скиф Ковка 16', 'Банная печь с топкой из специального литейного чугуна толщиной до 12 мм и открытой сеткой для 130 кг камней. Сверхдолгий срок службы и устойчивость к термоударам.', 'Чугунная топка, сетка-кожух для 130 кг банных камней', 34900.00, 38500.00, 'PCH-VEZ-16', 2, NULL, 4, 1, 1, 36, 1, NOW(), NOW()),
(5, 'Комплект теплого пола Valtec (коллектор 4 контура + труба PE-RT 200м)', 'Готовый комплект для монтажа теплого пола площадью до 40 м². Включает коллекторную группу с расходомерами, смесительный узел, насос и бухту трубы PE-RT Evoh 16x2.0.', 'Готовый комплект водяного теплого пола на 40 м²', 32400.00, 36000.00, 'FLR-VALTEC-4', 4, NULL, 8, 1, 1, 84, 1, NOW(), NOW()),
(6, 'Дымоход сэндвич AISI 430/0.8мм d115/200 (комплект 5м)', 'Комплект утепленного двухконтурного дымохода для банных и отопительных печей. Внутренняя труба 0.8 мм, негорючая базальтовая изоляция 40 мм. Полный монтажный набор.', 'Утепленный сэндвич-дымоход 5 метров с оголовком и разделкой', 18600.00, 21000.00, 'DMH-SND-115', 5, NULL, 12, 1, 1, 24, 1, NOW(), NOW()),
(7, 'Бойлер косвенного нагрева Drazice OKC 160 NTR', 'Напольный водонагреватель косвенного нагрева объемом 160 литров. Мощный спиральный теплообменник 24 кВт, магниевый анод, керамический нагреватель.', 'Бойлер косвенного нагрева 160 л с эмалированным баком', 49500.00, 54000.00, 'BLR-DRAZ-160', 6, NULL, 3, 1, 1, 60, 1, NOW(), NOW()),
(8, 'Циркуляционный насос Grundfos UPS 25-60 180', 'Классический трехскоростной циркуляционный насос с мокрым ротором для систем отопления частного дома. Чугунный корпус, бесшумная надежная работа.', 'Надежный циркуляционный насос для отопления, монтажная длина 180 мм', 7800.00, 8900.00, 'NSP-GRND-2560', 7, NULL, 15, 1, 1, 36, 0, NOW(), NOW()),
(9, 'Стальной мангал Казань 4мм с зольником и полкой', 'Прочный усиленный мангал из горячекатаной стали толщиной 4 мм. Ребра жесткости, съемный зольник, боковой столик и подставка под казан 8-12 л.', 'Усиленный стальной мангал 4 мм со столиком и подказанником', 8900.00, 10500.00, 'MNG-KZ-4MM', 9, NULL, 6, 1, 1, 24, 0, NOW(), NOW()),
(10, 'Душевая кабина Erlit Comfort 90x90 матовое стекло', 'Полноразмерная душевая кабина с акриловым поддоном на усиленном каркасе, безопасным закаленным стеклом 4 мм и смесителем с тропическим душем.', 'Угловая кабина 90x90 с глубоким поддоном и тропическим душем', 35900.00, 39900.00, 'DSH-ERL-90', 10, NULL, 4, 1, 0, 24, 1, NOW(), NOW()),
(11, 'Труба полипропиленовая армированная стекловолокном PN25 25x3.5 (4м)', 'Труба для высокотемпературного радиаторного отопления и горячего водоснабжения. Минимальное линейное удлинение, не требует зачистки при пайке.', 'Полипропилен армированный стекловолокном PN25, штанга 4 метра', 380.00, 420.00, 'TRB-PPR-25', 8, NULL, 180, 1, 0, 120, 0, NOW(), NOW()),
(12, 'Смеситель для ванны Lemark Comfort с длинным изливом', 'Латунный смеситель с поворотным изливом 350 мм, керамическим картриджем 35 мм и душевым гарнитуром в комплекте. Хромоникелевое покрытие.', 'Латунный смеситель с изливом 35 см и душевой лейкой', 5600.00, 6400.00, 'SMS-LMK-350', 10, NULL, 10, 1, 0, 48, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    `name` = VALUES(`name`),
    `description` = VALUES(`description`),
    `short_description` = VALUES(`short_description`),
    `price` = VALUES(`price`),
    `old_price` = VALUES(`old_price`),
    `stock_quantity` = VALUES(`stock_quantity`),
    `is_active` = VALUES(`is_active`),
    `is_featured` = VALUES(`is_featured`),
    `warranty_months` = VALUES(`warranty_months`),
    `installation_required` = VALUES(`installation_required`);

-- 3. МОНТАЖНИКИ (СЕРТИФИЦИРОВАННЫЕ МАСТЕРА РЕСПУБЛИКИ АЛТАЙ)
INSERT INTO `installers` (`id`, `first_name`, `last_name`, `photo_path`, `specialization`, `experience_years`, `rating`, `completed_orders`, `is_active`, `city`, `hourly_rate`, `created_at`, `languages`, `equipment_list`, `insurance_info`) VALUES
(1, 'Алексей', 'Смирнов', 'images/installers/installer1.jpg', 'Котлы отопления и обвязка котельных', 9, 4.90, 175, 1, 'с. Майма, Горно-Алтайск', 1400.00, NOW(), 'Русский', 'Пресс-клещи Rothenberger, лазерный нивелир Bosch, опрессовщик Rems', 'Страхование профессиональной ответственности на 3 000 000 руб.'),
(2, 'Иван', 'Кузнецов', 'images/installers/installer2.jpg', 'Водяные теплые полы и сантехника', 7, 4.85, 142, 1, 'Горно-Алтайск, Чемал', 1200.00, NOW(), 'Русский', 'Тепловизор Testo, размотчик труб, инструмент Rehau Rautool M1', 'Гарантия на все выполненные работы 24 месяца по договору'),
(3, 'Сергей', 'Васильев', 'images/installers/installer3.jpg', 'Банные печи, камины и дымоходы', 8, 4.80, 128, 1, 'с. Майма, Манжерок', 1300.00, NOW(), 'Русский', 'Алмазная коронка Hilti, леса строительные, гидравлический трубогиб', 'Официальный сертификат монтажника систем Теплодар и Везувий'),
(4, 'Михаил', 'Горбунов', 'images/installers/installer4.jpg', 'Твердотопливные и автоматические пеллетные котлы', 11, 4.95, 210, 1, 'Горно-Алтайск, Чоя, Шебалино', 1500.00, NOW(), 'Русский', 'Газоанализатор Testo, сварочный полуавтомат, трубонарезной станок', 'Сертифицированный сервисный инженер ZOTA и Viessmann'),
(5, 'Андрей', 'Новиков', 'images/installers/installer5.jpg', 'Водоснабжение, насосные станции и автоматика', 6, 4.75, 98, 1, 'с. Майма, Кызыл-Озек', 1100.00, NOW(), 'Русский', 'Электроинструмент DeWalt, паяльник для ППР Candan, тестер давления', 'Договор подряда, гарантия на герметичность соединений 3 года'),
(6, 'Дмитрий', 'Козлов', 'images/installers/installer6.jpg', 'Монтаж радиаторов и замена разводки отопления', 5, 4.70, 85, 1, 'Горно-Алтайск, Турочак', 1000.00, NOW(), 'Русский', 'Резьбонарезной клупп, перфоратор Makita, пресс-инструмент Valtec', 'Работа под ключ, закупка расходных материалов со скидкой магазина')
ON DUPLICATE KEY UPDATE 
    `first_name` = VALUES(`first_name`),
    `last_name` = VALUES(`last_name`),
    `specialization` = VALUES(`specialization`),
    `experience_years` = VALUES(`experience_years`),
    `rating` = VALUES(`rating`),
    `completed_orders` = VALUES(`completed_orders`),
    `is_active` = VALUES(`is_active`),
    `city` = VALUES(`city`),
    `hourly_rate` = VALUES(`hourly_rate`),
    `equipment_list` = VALUES(`equipment_list`),
    `insurance_info` = VALUES(`insurance_info`);

-- 4. ПОДРОБНОСТИ О МОНТАЖНИКАХ (КВАЛИФИКАЦИЯ, СЕРТИФИКАТЫ, ЗОНЫ ОБСЛУЖИВАНИЯ)
INSERT INTO `installer_details` (`id`, `installer_id`, `bio`, `education`, `certifications`, `skills`, `languages`, `work_schedule`, `equipment_list`, `service_areas`, `insurance_info`, `created_at`, `updated_at`, `description`, `services_list`) VALUES
(1, 1, 'Инженер-теплотехник с опытом более 9 лет. Специализируюсь на проектировании и комплексном монтаже котельных под ключ: твердотопливные котлы, электрокотлы, буферные емкости и гидрострелки. Провожу пусконаладочные работы и настройку погодозависимой автоматики.', 'Томский политехнический университет, Теплоэнергетика', 'Сертификат ZOTA Expert, Сертификат Viessmann Partner, Допуск Ростехнадзора', 'Монтаж котлов, гидравлический расчет, обвязка медью и сталью, наладка автоматики', 'Русский', 'Пн-Сб: 08:00 - 20:00', 'Пресс-клещи Rothenberger, лазерный нивелир, опрессовщик Rems', 'Майма, Горно-Алтайск, Чемал, Шебалино (выезд по всей Республике Алтай)', 'Страхование ответственности до 3 000 000 руб.', NOW(), NOW(), 'Комплексный монтаж систем отопления частных домов и турбаз.', 'Установка котла, Обвязка котельной, Монтаж гидрострелки, Пусконаладка'),
(2, 2, 'Профессиональный мастер по системам напольного отопления и водоснабжения. Смонтировал более 8 000 кв. метров теплого пола на объектах Республики Алтай. Работаю строго по строительным нормативам СП 60.13330.', 'Колледж архитектуры и строительства', 'Сертификат Rehau Rautool, Сертификат Valtec Pro, Uponor Academy', 'Укладка теплого пола, опрессовка воздухом/водой, балансировка контуров', 'Русский', 'Пн-Вс: 09:00 - 19:00', 'Тепловизор Testo, размотчик труб, инструмент Rehau Rautool M1', 'Горно-Алтайск, Майма, Манжерок, Чемал, Соузга', 'Гарантия на герметичность контуров 2 года', NOW(), NOW(), 'Монтаж водяных теплых полов с проверкой тепловизором.', 'Укладка теплого пола, Монтаж коллектора, Балансировка расходомеров'),
(3, 3, 'Специалист по банным комплексам и пожаробезопасным дымоходам. Устанавливаю чугунные и стальные печи, противопожарные разделки перекрытий и кровли, сэндвич-дымоходы из нержавейки.', 'Строительный техникум', 'Сертификат Теплодар Мастер, Допуск к огневым работам', 'Монтаж печей, противопожарная разделка, кирпичная кладка порталов, дымоходы', 'Русский', 'Вт-Вс: 09:00 - 18:00', 'Алмазная коронка Hilti, леса строительные, гидравлический трубогиб', 'Майма, Манжерок, Чемал, Усть-Сема, Горно-Алтайск', 'Строгое соблюдение противопожарных отступок по СНиП 41-01-2003', NOW(), NOW(), 'Безопасная установка банных печей и дымоходов под ключ.', 'Установка банной печи, Монтаж сэндвич-дымохода, Противопожарная разделка'),
(4, 4, 'Ведущий специалист по твердотопливным, комбинированным и полуавтоматическим котлам длительного горения. Опыт работы на частных и коммерческих объектах (гостиницы, турбазы).', 'Новосибирский государственный технический университет', 'Сертификат ZOTA Master, Сертификат Bosch Thermotechnik', 'Проектирование котельных большой мощности, монтаж пеллетных бункеров', 'Русский', 'Пн-Сб: 08:30 - 19:30', 'Газоанализатор Testo, сварочный инвертор, клупп резьбонарезной', 'Вся Республика Алтай: Майма, Чоя, Чемал, Шебалино, Онгудай', 'Гарантийное и сервисное обслуживание смонтированных котельных', NOW(), NOW(), 'Монтаж котельных любой сложности для коттеджей и баз отдыха.', 'Монтаж твердотопливного котла, Установка теплоаккумулятора, Сервисное ТО'),
(5, 5, 'Мастер систем автономного водоснабжения, скважинных насосов и водоочистки. Обеспечиваю стабильное давление в доме и правильную фильтрацию воды.', 'Алтайский государственный аграрный университет', 'Сертификат Grundfos Pump School, Сертификат Джилекс', 'Монтаж скважинных насосов, автоматика водоснабжения, монтаж сантехники', 'Русский', 'Пн-Сб: 09:00 - 18:00', 'Инструмент DeWalt, паяльник ППР Candan, тестер давления', 'Майма, Кызыл-Озек, Горно-Алтайск, Подгорное', 'Договор с гарантией 3 года', NOW(), NOW(), 'Автономное водоснабжение из скважины или колодца.', 'Монтаж погружного насоса, Установка гидроаккумулятора, Разводка водопровода'),
(6, 6, 'Квалифицированный монтажник отопительных приборов и трубной разводки. Быстрая и аккуратная замена радиаторов без грязи и пыли.', 'Технический лицей', 'Сертификат Royal Thermo, Сертификат Global Radiatori', 'Диагональное и нижнее подключение радиаторов, замена стояков', 'Русский', 'Пн-Пт: 09:00 - 18:00', 'Резьбонарезной клупп, перфоратор Makita, пресс-инструмент', 'Горно-Алтайск, Майма, Турочак', 'Гарантия отсутствия протечек', NOW(), NOW(), 'Замена и установка радиаторов отопления в домах и квартирах.', 'Монтаж радиатора, Замена кранов и термоклапанов, Опрессовка системы')
ON DUPLICATE KEY UPDATE 
    `bio` = VALUES(`bio`),
    `education` = VALUES(`education`),
    `certifications` = VALUES(`certifications`),
    `skills` = VALUES(`skills`),
    `service_areas` = VALUES(`service_areas`),
    `insurance_info` = VALUES(`insurance_info`),
    `description` = VALUES(`description`),
    `services_list` = VALUES(`services_list`);

-- 5. ОТЗЫВЫ О МОНТАЖНИКАХ (ЧТОБЫ РЕЙТИНГ И СЧЕТЧИКИ БЫЛИ ЖИВЫМИ)
INSERT INTO `installer_reviews` (`id`, `installer_id`, `user_id`, `client_name`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 1, 'Дмитрий В. (с. Майма)', 5, 'Алексей установил котел ZOTA Тополь-М 20 и смонтировал гидрострелку. Работа выполнена аккуратно, труба к трубе. Провел подробный инструктаж по растопке. Зиму перезимовали в тепле, спасибо!', NOW() - INTERVAL 25 DAY),
(2, 1, 1, 'Владимир (Манжерок)', 5, 'Обвязали котельную в гостевом доме на 8 номеров. Все работает как часы, термостаты держат заданную температуру.', NOW() - INTERVAL 12 DAY),
(3, 2, 1, 'Елена (Горно-Алтайск)', 5, 'Иван делал теплый пол на площади 75 кв.м. После укладки провели опрессовку и проверили тепловизором. Профессиональный подход!', NOW() - INTERVAL 35 DAY),
(4, 2, 1, 'Константин (Чемал)', 5, 'Отличный специалист. Помог рассчитать метраж трубы, посоветовал комплект Valtec. Сделал все за 2 дня.', NOW() - INTERVAL 18 DAY),
(5, 3, 1, 'Олег (с. Майма)', 5, 'Сергей установил чугунную банную печь Везувий и смонтировал сэндвич-дымоход. Особое внимание уделил разделке в перекрытии. Очень надежно!', NOW() - INTERVAL 40 DAY),
(6, 4, 1, 'Андрей П. (Турбаза Катунь)', 5, 'Михаил настраивал автоматический котел. Все узлы собраны качественно, сервис на высшем уровне.', NOW() - INTERVAL 10 DAY)
ON DUPLICATE KEY UPDATE 
    `client_name` = VALUES(`client_name`),
    `rating` = VALUES(`rating`),
    `comment` = VALUES(`comment`);

-- 6. ПОРТФОЛИО МОНТАЖНИКОВ
INSERT INTO `installer_portfolio` (`id`, `installer_id`, `image_path`, `title`, `description`, `created_at`) VALUES
(1, 1, 'images/portfolio/work1.jpg', 'Котельная коттеджа 180 м² в с. Майма', 'Монтаж котла ZOTA, расширительного бака и насосной группы', NOW() - INTERVAL 30 DAY),
(2, 1, 'images/portfolio/work2.jpg', 'Обвязка буферной емкости 500л', 'Установка гидрострелки и распределительного коллектора', NOW() - INTERVAL 20 DAY),
(3, 2, 'images/portfolio/work3.jpg', 'Теплый пол 90 м² в Чемале', 'Укладка трубы Valtec 16мм с шагом 150 мм', NOW() - INTERVAL 25 DAY),
(4, 3, 'images/portfolio/work4.jpg', 'Банная печь Везувий с дымоходом AISI 430', 'Монтаж печи с порталом из кирпича и выводом через кровлю', NOW() - INTERVAL 15 DAY)
ON DUPLICATE KEY UPDATE 
    `title` = VALUES(`title`),
    `description` = VALUES(`description`);

-- 7. ОТЗЫВЫ О МАГАЗИНЕ «ТЕРРИТОРИЯ ТЕПЛА»
INSERT INTO `site_reviews` (`id`, `user_id`, `author_name`, `rating`, `comment`, `is_approved`, `created_at`) VALUES
(1, 1, 'Сергей Николаевич', 5, 'Отличный магазин в Майме! Закупали полностью оборудование для отопления дома (котел, радиаторы, трубы). Проконсультировали грамотно, сделали скидку за объем, еще и монтажника сертифицированного подобрали.', 1, NOW() - INTERVAL 15 DAY),
(2, 1, 'Анна Васильевна', 5, 'Покупали дровяную печь Теплодар для бани. Привезли в тот же день прямо на участок в Кызыл-Озек. Печь отличная, пар легкий, вся семья довольна!', 1, NOW() - INTERVAL 8 DAY),
(3, 1, 'Игорь М.', 5, 'Грамотные инженеры на складе по ул. Энергетиков 24. Помогли посчитать количество секций радиаторов и подобрать циркуляционный насос. Цены адекватные, в наличии всё есть.', 1, NOW() - INTERVAL 3 DAY)
ON DUPLICATE KEY UPDATE 
    `author_name` = VALUES(`author_name`),
    `rating` = VALUES(`rating`),
    `comment` = VALUES(`comment`),
    `is_approved` = VALUES(`is_approved`);

-- 8. ДОКУМЕНТЫ И СЕРТИФИКАТЫ КОМПАНИИ (ДЛЯ СТРАНИЦЫ «О КОМПАНИИ»)
INSERT INTO `company_documents` (`id`, `title`, `file_path`, `document_type`, `created_at`) VALUES
(1, 'Свидетельство ОГРН ООО «МП ТЕРРИТОРИЯ ТЕПЛА»', 'images/documents/695ff4475033e.jpg', 'legal', NOW()),
(2, 'Сертификат официального дилера отопительного оборудования', 'images/documents/695ff4aeee405.jpg', 'certificate', NOW()),
(3, 'Сертификат соответствия продукции стандартам ГОСТ Р', 'images/documents/695ff50099a5e.png', 'certificate', NOW())
ON DUPLICATE KEY UPDATE 
    `title` = VALUES(`title`),
    `file_path` = VALUES(`file_path`),
    `document_type` = VALUES(`document_type`);


-- 9. ФОТОГРАФИИ ТОВАРОВ
INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `alt_text`, `is_main`, `sort_order`, `created_at`) VALUES
(1, 1, 'images/products/pech1.jpg', 'Дровяная печь Теплодар Русь 18', 1, 0, NOW()),
(2, 2, 'images/products/kotel1.jpg', 'Твердотопливный котел ZOTA Тополь-М 20', 1, 0, NOW()),
(3, 3, 'images/products/rad1.jpg', 'Биметаллический радиатор Royal Thermo Revolution 500', 1, 0, NOW()),
(4, 4, 'images/products/pech2.jpg', 'Банная печь Везувий Скиф Ковка 16', 1, 0, NOW()),
(5, 5, 'images/products/pol1.jpg', 'Комплект теплого пола Valtec', 1, 0, NOW()),
(6, 6, 'images/products/dym1.jpg', 'Дымоход сэндвич AISI 430 d115', 1, 0, NOW()),
(7, 7, 'images/products/boiler1.jpg', 'Бойлер косвенного нагрева Drazice OKC 160', 1, 0, NOW()),
(8, 8, 'images/products/nasos1.jpg', 'Циркуляционный насос Grundfos UPS 25-60', 1, 0, NOW()),
(9, 9, 'images/products/mangal1.jpg', 'Стальной мангал Казань 4мм', 1, 0, NOW()),
(10, 10, 'images/products/dush1.jpg', 'Душевая кабина Erlit Comfort 90', 1, 0, NOW()),
(11, 11, 'images/products/truba1.jpg', 'Труба полипропиленовая армированная PN25', 1, 0, NOW()),
(12, 12, 'images/products/smes1.jpg', 'Смеситель для ванны Lemark Comfort', 1, 0, NOW())
ON DUPLICATE KEY UPDATE 
    `product_id` = VALUES(`product_id`),
    `image_path` = VALUES(`image_path`),
    `alt_text` = VALUES(`alt_text`),
    `is_main` = VALUES(`is_main`);

SET FOREIGN_KEY_CHECKS = 1;
