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
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES
(1,19,5,1,'2026-01-05 11:40:21'),
(2,20,6,14,'2026-01-08 07:32:07'),
(3,21,6,2,'2026-01-08 10:30:03'),
(4,21,2,8,'2026-01-08 10:30:09');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
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
/*!40000 ALTER TABLE `contact_requests` DISABLE KEYS */;
INSERT INTO `contact_requests` VALUES
(0,'Emma','7075265807','emma.wilson6162@gmail.com','Hi,\r\n\r\nI was just checking out your website, territoriatepla.ru, and wanted to reach out personally.\r\n\r\nWe run an agency called Promote4U. We have access to a massive network of over 32 million subscribers, giving us the ability to target traffic specifically by Country and Language.\r\n\r\nMost marketing firms charge you for \"ads\" or \"email blasts\" and you just have to hope they work.\r\n\r\nWe operate differently. We don\'t charge for \"attempts\" or \"sends.\" We focus on Guaranteed Visitors.\r\n\r\nEssentially, we promote your site to our network—filtering by the countries and languages you need—and we keep working UNTIL a guaranteed number of people have actually visited your site.\r\n\r\nWe specialize in Local Marketing as well. If you need to target specific people in your area rather than globally, we can tailor a campaign specifically for that.\r\n\r\nIf this is of interest and you would like further information, please get back in touch.\r\n\r\nKind Regards,\r\nEmma','processed','2025-12-17 15:50:53',NULL,NULL),
(0,'Ian','734599272','info@domainsubmlt.info','List territoriatepla.ru in Google Search Index and it will be displayed in WebSearch Results.\r\n\r\nAdd territoriatepla.ru now at https://searchregister.org','processed','2025-12-17 18:34:39',NULL,NULL),
(0,'Emma','7075265807','emma.wilson6162@gmail.com','Hi,\r\n\r\nI was just checking out your website, territoriatepla.ru, and wanted to reach out personally.\r\n\r\nWe run an agency called Promote4U. We have access to a massive network of over 32 million subscribers, giving us the ability to target traffic specifically by Country and Language.\r\n\r\nMost marketing firms charge you for \"ads\" or \"email blasts\" and you just have to hope they work.\r\n\r\nWe operate differently. We don\'t charge for \"attempts\" or \"sends.\" We focus on Guaranteed Visitors.\r\n\r\nEssentially, we promote your site to our network—filtering by the countries and languages you need—and we keep working UNTIL a guaranteed number of people have actually visited your site.\r\n\r\nWe specialize in Local Marketing as well. If you need to target specific people in your area rather than globally, we can tailor a campaign specifically for that.\r\n\r\nIf this is of interest and you would like further information, please get back in touch.\r\n\r\nKind Regards,\r\nEmma','processed','2025-12-17 15:50:53',NULL,NULL),
(0,'Ian','734599272','info@domainsubmlt.info','List territoriatepla.ru in Google Search Index and it will be displayed in WebSearch Results.\r\n\r\nAdd territoriatepla.ru now at https://searchregister.org','processed','2025-12-17 18:34:39',NULL,NULL),
(0,'Mollie','7722181427','molliehanson.vgo@gmail.com','Hi there,\r\n\r\nAre you looking to grow your YouTube channel with real, engaged subscribers?\r\n\r\nWe offer a YouTube growth service designed to help you consistently expand your audience in a safe and effective way.\r\n\r\nHere’s what you can expect:\r\n\r\n- Gain approximately 300–500 new subscribers every month\r\n- Attract viewers who are genuinely interested in your content\r\n- Increase engagement with more likes, comments, and interactions\r\n- 100% manual promotion — no bots, no shortcuts\r\n\r\nOur service is simple and affordable at just $60/month, and we can get started right away.\r\n\r\nIf you’d like to see examples of our past results or learn more, just reply to this email — we’d be happy to share details.\r\n\r\nBest regards,\r\n\r\nMollie\r\n\r\nTo unsubscribe, simply reply with “unsubscribe” in the subject line.','new','2026-04-07 18:13:02',NULL,NULL),
(0,'Merlin','630165980','bidwell.merlin@gmail.com','Supercharge territoriatepla.ru website seo with trusted seo backlinks and google appearance!\r\nDaily Seo Links - we build daily backlinks and grow google rank every day:\r\n\r\n1,000 and more backlinks daily\r\nReal google visits\r\nPrice as low as $1\r\nUp to 85% deals:\r\n\r\nhttps://tiny.cc/daily-85saves\r\n\r\nDaily seo links - daily seo backlinks to improve your seo backlinks every day','new','2026-06-22 16:34:10',NULL,NULL),
(0,'Ashwani','','ashwani@rocketdigitaltech.com','Hello http://territoriatepla.ru,\r\n \r\nTraditional SEO alone isn\'t enough anymore.\r\n \r\nI can help your business rank on Google and get discovered through AI platforms like ChatGPT, Gemini, and Google AI Overviews using a complete SEO + GEO + AEO strategy.\r\n \r\nSend me your website and target market, and I\'ll share a customized growth plan.\r\n \r\nBest Regards,\r\nAshwani','new','2026-07-10 16:13:47',NULL,NULL),
(0,'Millie','888856204','millie.orme@gmail.com','Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','new','2026-07-13 12:25:23',NULL,NULL),
(0,'Gertrude','2991513916','gosse.gertrude24@yahoo.com','Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','new','2026-07-15 00:11:25',NULL,NULL),
(0,'Ardis','7727665013','ardis.sievier77@gmail.com','Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','new','2026-07-15 00:13:12',NULL,NULL),
(0,'Hubert','624165182','button.hubert@gmail.com','Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','new','2026-07-15 00:24:36',NULL,NULL),
(0,'Jade','4233553758','chacon.jade@msn.com','Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','new','2026-07-15 00:28:53',NULL,NULL),
(0,'Van','6821564063','van.andrzejewski61@gmail.com','Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','new','2026-07-15 00:29:41',NULL,NULL),
(0,'Bryce','3243495468','bays.bryce58@gmail.com','Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','new','2026-07-15 00:40:48',NULL,NULL),
(0,'Elliot','7965101591','elliot.toscano@gmail.com','Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','new','2026-07-15 00:43:18',NULL,NULL),
(0,'Damon','516995998','damon.hudgens10@gmail.com','Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','new','2026-07-15 00:46:32',NULL,NULL),
(0,'Carlota','712572630','shivers.carlota@hotmail.com','Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','new','2026-07-15 00:54:32',NULL,NULL),
(0,'Shani','3126308850','shani.schultz@msn.com','Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','new','2026-07-15 01:19:02',NULL,NULL),
(0,'Kenny','249899698','kellermann.kenny15@msn.com','Greetings from Dreamproxies\r\n\r\nHot proxy promotion: premium, fast speed and trusted private proxies now even cheaper price!\r\n50% Off - for all private proxies:\r\n\r\nhttps://tiny.cc/dreamproxies-discount\r\n\r\nFully Private, Top Quality, Blazing Speed, Unlimited Bandwidth, Strong Servers, Low Prices, Extra Discounts and many more\r\n\r\n50% Cheaper for all proxy plans - at Dreamproxies.com','new','2026-07-29 14:05:58',NULL,NULL),
(0,'figitaldv','+16479873671','oooraits@rambler.ru','Hi there, \r\n \r\nI believe territoriatepla.ru has a big potential. You\'re in an industry with stiff competition, but also has a lot of customers. \r\n \r\nMy company has been marketing sites for over 7 years and we would be glad to explain how you can improve the online visibility of territoriatepla.ru. \r\n \r\nThis is a free consultation and if you like our approach, but don\'t have time to perform these tasks yourself,  we can talk about potentially delegating some of the tasks to our team. \r\n \r\nterritoriatepla.ru is a great resource and we would love to help you grow it to it\'s full potential. \r\n \r\nSimply reply to this email and we can setup a call or continue by email. \r\n \r\nThank you \r\n \r\n \r\nAlexander \r\nqc-digital \r\n+16479873671','new','2026-08-02 15:20:40',NULL,NULL),
(0,'ofisnaokncb','88299987388','alverdievsusoi1975@rambler.ru','Здравствуйте! \r\n \r\nСдаётся: Light Industrial,Офис, склад, интернет-магазин, производство 185 м.кв., 600 метров от м.Черкизовская и ТПУ Локомотив. \r\nhttps://realty.yandex.ru/offer/2160899779330445057/ \r\n \r\nСдаётся под: Light Industrial,офис, тёплый склад, интернет-магазин, дата-центр производственное помещение категории \"В+\" 185 м.кв. \r\nМожно использовать как:Light Industrial, офисное помещение, тёплый склад, интернет-магазин, не грязное производство, центр обработки данных, помещение свободного назначения и.т.д. \r\nПервый этаж, одноэтажного офисно-складского здания, возможность выгрузки и загрузки товарной продукции через вход и окна. \r\nДанное помещение только после косметического ремонта, сделан профессиональный клининг. \r\n650 м. от метро Черкизовская и ТПУ Локомотив. \r\nОтдельный вход доступ 24 часа 7 дней в неделю. \r\nсемь мест для паркинга: 5 под легковые автомашины + 2 выделенных места под Газель/Портер. \r\nПериметр огорожен забором. \r\nОхрана периметра 24 часа + установлена дополнительная пультовая охрана (подключим при необходимости). \r\nЗдание под видеонаблюдением 24 часа. \r\nПротиво-взломные жалюзи на дверь и окна. \r\nВывоз мусора, ежедневная уборка террритории, электрик/сантехник, решение любых вопросов связанных с эксплуатацией помещения. \r\nМощности 76 Квт, возможно увеличение до двухсот и более Квт. \r\n14 подключённых рабочих мест в зале и комнатах. \r\nКаждая электрическая розетка подключена отдельным проводом (разделение на компьютерное и бытовое электричество). \r\nИнтернет/интернет-телефония, оптоволокно до 1Гигабитбита/сек. \r\nЯщик с патч-панелями под телефонию и интернет (отмаркированны 22 компьютерные и 22 телефонные розетки). \r\nПодведены 16 телефонных линий MTC (можем подключить). \r\nПотолок \"Armstrong\" и энергосберегающие светильники во всех комнатах. \r\nВысота до потолков \"Армстронг\" - 3,25 метр (Общая высота потолка в зале 5-6 метров). \r\nНовая беспроводная пожарная сигнализация \"Стрелец\", Пожарный кран, огнетушители. \r\nФальшпол, плитки из керамогранита 600х600, допустимая нагрузка 2500 Килограмм на квадратный метр. \r\nЗаправленные и чистые кондиционеры. \r\nЦентральное теплоснабжение. \r\nВентиляция. \r\nВсе коммуникации в здании обновлены. \r\nЕсть витрины с подсветкой (красного цвета в разобранном виде). \r\nБольшое кол-во видов разной мебели для офиса (- разобраны). \r\nХолодильник. \r\nЗал: 83 м.кв. высота потолков 3,25 метров (общая высота 5-6 метров) \r\nКомнаты: 20, 15, 24, 12, 20 м.кв. \r\nШумоизоляция во всех комнатах и зале. \r\nМеста для паркинга, эксплуатационные/коммунальные расходы, юридический адрес включены в стоимость. \r\nВода и Электричество оплачиваются по счётчикам. \r\nУпрощённая система налогообложения(УСН) \r\nКомиссия риэлторам от 50%. \r\nАрендные каникулы (Акция! - её действие огрангичено). \r\nДанное помещение не сдаётся под: общежитие/хостел/гостиницу, пищевое/грязное/шумное производство. \r\nhttps://realty.yandex.ru/offer/2160899779330445057/','new','2026-08-04 08:05:41',NULL,NULL),
(0,'Ириза','+7 (922) 672-02-73','iriza.dautaeva74@yandex.ru','Здравствуйте! Вопрос по теме: разводка водоснабжения. Хочу заменить всё разом. Подскажите, пожалуйста, стоимость и сроки.','new','2026-08-09 07:23:25',NULL,NULL),
(0,'Яна','+7 (978) 510-88-12','yana.ivkina63@gmail.com','Добрый день! Нужен расчёт: разводка водоснабжения. Перезвоните, пожалуйста, в рабочее время.','new','2026-08-10 17:05:04',NULL,NULL),
(0,'lzgdiymrzf','+1-019-358-5959','rruyrjiu@immenseignite.info','llffughnkduysfzfhypnhxsguwzref','new','2026-08-29 22:19:24',NULL,NULL);
/*!40000 ALTER TABLE `contact_requests` ENABLE KEYS */;
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
/*!40000 ALTER TABLE `installation_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `installation_requests` ENABLE KEYS */;
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
/*!40000 ALTER TABLE `installer_applications` DISABLE KEYS */;
INSERT INTO `installer_applications` VALUES
(1,'123123','123123','123123','123123',123123,'123123','123123','new',NULL,'2026-02-10 17:10:10'),
(2,'Rodolfo','De Garis','7089242420','Rodolfo',0,'Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','new',NULL,'2026-07-15 00:19:12'),
(3,'Rodrigo','Kilvington','7958812407','Rodrigo',0,'Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','Приглашаем на бесплатный вебинар, где Вы\r\n\r\nНаучитесь готовить настоящие «ресторанные» итальянские блюда: пасту, супы, второе, соусы, сыры и десерты без поварского опыта прямо у себя дома по проверенным традиционным рецептам\r\n\r\nРеальный результат сразу: участник не просто слушает теорию, а уже на практикуме готовит свой первый домашний итальянский сыр и нежные, полезные конфеты маскарпоне - настоящая альтернатива торту.\r\n\r\nПошаговая система для новичков: никаких сложных терминов и оборудования — всё делается из доступных продуктов на обычной кухне.\r\n\r\nАвторская методика и 10+ лет опыта: знания, собранные из практики и проверенные на более чем 1 000 000 учеников.\r\n\r\nФормат “включился и сделал”: максимум практики, минимум воды.\r\n\r\nhttps://u.to/kbGgIg\r\n[URL]https://u.to/kbGgIg[/URL]\r\n<a href=https://u.to/kbGgIg>https://u.to/kbGgIg</a>','new',NULL,'2026-07-15 00:34:24');
/*!40000 ALTER TABLE `installer_applications` ENABLE KEYS */;
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
(1,1,'Опытный специалист по монтажу отопительных систем. Работаю с любыми типами котлов и радиаторов.','МГСУ, факультет теплогазоснабжения','Сертификат Gazprom, Сертификат Viessmann','Монтаж котлов, установка радиаторов, прокладка трубопроводов',NULL,NULL,NULL,'Москва и Московская область в радиусе 50 км',NULL,'2025-11-21 05:30:12','2025-11-25 05:29:03','asdasdasd','12312312'),
(2,2,'Профессиональный сантехник с большим опытом работы. Специализируюсь на монтаже теплых полов и сантехнических работ.','Колледж сантехнического оборудования','Сертификат Rehau, Сертификат Uponor','Монтаж теплых полов, установка сантехники, ремонтные работы',NULL,NULL,NULL,'Москва, Зеленоград, Химки',NULL,'2025-11-21 05:30:12','2025-11-21 05:30:12',NULL,NULL),
(3,3,'Специалист по установке радиаторов и сантехническому оборудованию. Качественно и в срок выполняю работы любой сложности.','Техникум монтажного оборудования','Сертификат Global Radiatori, Сертификат Kermi','Установка радиаторов, замена сантехники, устранение протечек',NULL,NULL,NULL,'Московская область, ближнее Подмосковье',NULL,'2025-11-21 05:30:12','2025-11-21 05:30:12',NULL,NULL),
(4,1,'Опытный специалист по монтажу отопительных систем. Работаю с любыми типами котлов и радиаторов.','МГСУ, факультет теплогазоснабжения','Сертификат Gazprom, Сертификат Viessmann','Монтаж котлов, установка радиаторов, прокладка трубопроводов',NULL,NULL,NULL,'Москва и Московская область в радиусе 50 км',NULL,'2025-11-21 05:31:59','2025-11-25 05:29:03','asdasdasd','12312312'),
(5,2,'Профессиональный сантехник с большим опытом работы. Специализируюсь на монтаже теплых полов и сантехнических работ.','Колледж сантехнического оборудования','Сертификат Rehau, Сертификат Uponor','Монтаж теплых полов, установка сантехники, ремонтные работы',NULL,NULL,NULL,'Москва, Зеленоград, Химки',NULL,'2025-11-21 05:31:59','2025-11-21 05:31:59',NULL,NULL),
(6,3,'Специалист по установке радиаторов и сантехническому оборудованию. Качественно и в срок выполняю работы любой сложности.','Техникум монтажного оборудования','Сертификат Global Radiatori, Сертификат Kermi','Установка радиаторов, замена сантехники, устранение протечек',NULL,NULL,NULL,'Московская область, ближнее Подмосковье',NULL,'2025-11-21 05:31:59','2025-11-21 05:31:59',NULL,NULL),
(1,1,'Опытный специалист по монтажу отопительных систем. Работаю с любыми типами котлов и радиаторов.','МГСУ, факультет теплогазоснабжения','Сертификат Gazprom, Сертификат Viessmann','Монтаж котлов, установка радиаторов, прокладка трубопроводов',NULL,NULL,NULL,'Москва и Московская область в радиусе 50 км',NULL,'2025-11-21 05:30:12','2025-11-25 05:29:03','asdasdasd','12312312'),
(2,2,'Профессиональный сантехник с большим опытом работы. Специализируюсь на монтаже теплых полов и сантехнических работ.','Колледж сантехнического оборудования','Сертификат Rehau, Сертификат Uponor','Монтаж теплых полов, установка сантехники, ремонтные работы',NULL,NULL,NULL,'Москва, Зеленоград, Химки',NULL,'2025-11-21 05:30:12','2025-11-21 05:30:12',NULL,NULL),
(3,3,'Специалист по установке радиаторов и сантехническому оборудованию. Качественно и в срок выполняю работы любой сложности.','Техникум монтажного оборудования','Сертификат Global Radiatori, Сертификат Kermi','Установка радиаторов, замена сантехники, устранение протечек',NULL,NULL,NULL,'Московская область, ближнее Подмосковье',NULL,'2025-11-21 05:30:12','2025-11-21 05:30:12',NULL,NULL),
(4,1,'Опытный специалист по монтажу отопительных систем. Работаю с любыми типами котлов и радиаторов.','МГСУ, факультет теплогазоснабжения','Сертификат Gazprom, Сертификат Viessmann','Монтаж котлов, установка радиаторов, прокладка трубопроводов',NULL,NULL,NULL,'Москва и Московская область в радиусе 50 км',NULL,'2025-11-21 05:31:59','2025-11-25 05:29:03','asdasdasd','12312312'),
(5,2,'Профессиональный сантехник с большим опытом работы. Специализируюсь на монтаже теплых полов и сантехнических работ.','Колледж сантехнического оборудования','Сертификат Rehau, Сертификат Uponor','Монтаж теплых полов, установка сантехники, ремонтные работы',NULL,NULL,NULL,'Москва, Зеленоград, Химки',NULL,'2025-11-21 05:31:59','2025-11-21 05:31:59',NULL,NULL),
(6,3,'Специалист по установке радиаторов и сантехническому оборудованию. Качественно и в срок выполняю работы любой сложности.','Техникум монтажного оборудования','Сертификат Global Radiatori, Сертификат Kermi','Установка радиаторов, замена сантехники, устранение протечек',NULL,NULL,NULL,'Московская область, ближнее Подмосковье',NULL,'2025-11-21 05:31:59','2025-11-21 05:31:59',NULL,NULL);
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
(1,1,1,'maintenance','фыа','2025-12-13','12:12:00','аыва','Ывыв','+7 (222) 232-22-33','123123@123.com','pending',NULL,NULL,'2025-11-21 07:48:15','2025-11-21 07:48:15'),
(2,1,1,'installation','asfsd','2026-03-12','12:31:00','fsdfsdf','фвфывфыв','+7 (231) 231-23-12','W@asdfasdf.com','pending',NULL,NULL,'2025-11-21 07:49:45','2025-11-21 07:49:45'),
(1,1,1,'maintenance','фыа','2025-12-13','12:12:00','аыва','Ывыв','+7 (222) 232-22-33','123123@123.com','pending',NULL,NULL,'2025-11-21 07:48:15','2025-11-21 07:48:15'),
(2,1,1,'installation','asfsd','2026-03-12','12:31:00','fsdfsdf','фвфывфыв','+7 (231) 231-23-12','W@asdfasdf.com','pending',NULL,NULL,'2025-11-21 07:49:45','2025-11-21 07:49:45');
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
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES
(2,2,1,'Дровяная печь \"Теплодар\"',24500.00,1),
(3,2,2,'Стальной мангал \"Казань\"',8900.00,1),
(4,3,2,'Стальной мангал \"Казань\"',8900.00,52),
(5,4,3,'Душевая кабина \"Акватон\"',42300.00,1),
(6,5,4,'Комплекс барбекю \"Грильмастер\"',67800.00,1),
(7,8,4,'Комплекс барбекю \"Грильмастер\"',67800.00,1),
(8,10,4,'Комплекс барбекю \"Грильмастер\"',67800.00,1),
(9,11,3,'Душевая кабина \"Акватон\"',42300.00,1),
(2,2,1,'Дровяная печь \"Теплодар\"',24500.00,1),
(3,2,2,'Стальной мангал \"Казань\"',8900.00,1),
(4,3,2,'Стальной мангал \"Казань\"',8900.00,52),
(5,4,3,'Душевая кабина \"Акватон\"',42300.00,1),
(6,5,4,'Комплекс барбекю \"Грильмастер\"',67800.00,1),
(7,8,4,'Комплекс барбекю \"Грильмастер\"',67800.00,1),
(8,10,4,'Комплекс барбекю \"Грильмастер\"',67800.00,1),
(9,11,3,'Душевая кабина \"Акватон\"',42300.00,1);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
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
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES
(2,7,0.00,'иван иванов','103901293031','asoioaisdpoia@123.com','','pickup','cash','','cancelled','2025-11-24 14:33:56','2026-01-11 04:09:55'),
(3,7,0.00,'иван иванов','103901293031','asoioaisdpoia@123.com','','pickup','cash','','cancelled','2025-11-24 14:56:39','2025-12-03 03:17:34'),
(4,4,42300.00,'Иван Иванов','+7 (222) 322-23-3','123123@123.com','ваоулы','pickup','cash','','cancelled','2025-12-10 07:42:31','2026-01-11 04:09:58'),
(5,1,67800.00,'Администратор Системы','12312312','admin@store.com','','pickup','cash','','cancelled','2025-12-10 07:51:56','2026-01-11 04:10:01'),
(8,1,67800.00,'Администратор Системы','+7 (313) 123-12-12','admin@store.com','','pickup','cash','','cancelled','2025-12-10 08:06:08','2026-01-11 04:10:03'),
(10,1,67800.00,'Администратор Системы','+7 (131) 231-23-12','admin@store.com','','pickup','cash','','cancelled','2025-12-10 08:10:34','2026-01-11 04:10:06'),
(11,1,42300.00,'Администратор Системы','121311312123','admin@store.com','','pickup','cash','','cancelled','2025-12-10 08:18:25','2026-01-11 04:10:08'),
(2,7,0.00,'иван иванов','103901293031','asoioaisdpoia@123.com','','pickup','cash','','cancelled','2025-11-24 14:33:56','2026-01-11 04:09:55'),
(3,7,0.00,'иван иванов','103901293031','asoioaisdpoia@123.com','','pickup','cash','','cancelled','2025-11-24 14:56:39','2025-12-03 03:17:34'),
(4,4,42300.00,'Иван Иванов','+7 (222) 322-23-3','123123@123.com','ваоулы','pickup','cash','','cancelled','2025-12-10 07:42:31','2026-01-11 04:09:58'),
(5,1,67800.00,'Администратор Системы','12312312','admin@store.com','','pickup','cash','','cancelled','2025-12-10 07:51:56','2026-01-11 04:10:01'),
(8,1,67800.00,'Администратор Системы','+7 (313) 123-12-12','admin@store.com','','pickup','cash','','cancelled','2025-12-10 08:06:08','2026-01-11 04:10:03'),
(10,1,67800.00,'Администратор Системы','+7 (131) 231-23-12','admin@store.com','','pickup','cash','','cancelled','2025-12-10 08:10:34','2026-01-11 04:10:06'),
(11,1,42300.00,'Администратор Системы','121311312123','admin@store.com','','pickup','cash','','cancelled','2025-12-10 08:18:25','2026-01-11 04:10:08');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
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
(4,4,'/images/products/bbq1.jpg','Комплекс барбекю Грильмастер',1,0,'2025-11-21 07:20:53'),
(5,5,'/images/products/pech2.jpg','Чугунная печь Русская',1,0,'2025-11-21 07:20:53'),
(6,6,'/images/products/smes1.jpg','Смеситель для кухни Аква',1,0,'2025-11-21 07:20:53'),
(8,2,'/images/products/prod_2_6925410c87f1f.png',NULL,1,0,'2025-11-25 05:39:24'),
(9,3,'/images/products/prod_1764728378_692f9e3a5c83d.jpg',NULL,0,0,'2025-12-03 02:19:38'),
(4,4,'/images/products/bbq1.jpg','Комплекс барбекю Грильмастер',1,0,'2025-11-21 07:20:53'),
(5,5,'/images/products/pech2.jpg','Чугунная печь Русская',1,0,'2025-11-21 07:20:53'),
(6,6,'/images/products/smes1.jpg','Смеситель для кухни Аква',1,0,'2025-11-21 07:20:53'),
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
(2,3,8,5,'ывфывфыв','2025-12-10 06:48:10'),
(3,3,1,5,'фвфы','2025-12-10 06:52:09'),
(5,3,4,5,'1123','2025-12-10 08:56:45'),
(2,3,8,5,'ывфывфыв','2025-12-10 06:48:10'),
(3,3,1,5,'фвфы','2025-12-10 06:52:09'),
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
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Заголовок акции',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Полное описание',
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Путь к картинке',
  `discount_percent` int(11) DEFAULT NULL COMMENT 'Процент скидки (для бейджика)',
  `start_date` datetime DEFAULT current_timestamp() COMMENT 'Начало акции',
  `end_date` datetime DEFAULT NULL COMMENT 'Конец акции (NULL = бессрочно)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1 - активна, 0 - скрыта',
  `sort_order` int(11) DEFAULT 0 COMMENT 'Порядок сортировки',
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
(5,4,5,'фывыф','2025-12-10 06:53:54'),
(4,4,5,'123','2025-12-10 06:53:53'),
(5,4,5,'фывыф','2025-12-10 06:53:54'),
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
  `personal_data_agreed` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Согласие на обработку персональных данных (152-ФЗ)',
  `personal_data_agreed_at` timestamp NULL DEFAULT NULL COMMENT 'Дата и время выдачи согласия',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'admin','admin@store.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Администратор','Системы',NULL,NULL,NULL,NULL,NULL,'admin',1,'2025-11-21 05:30:12','2025-11-21 05:30:12',0,0,NULL),
(2,'moderator','moderator@store.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Модератор','Отдела',NULL,NULL,NULL,NULL,NULL,'moderator',1,'2025-11-21 05:30:12','2025-11-21 05:30:12',0,0,NULL),
(23,'uffnv','uffnv@yandex.ru','$2y$12$JiTHsvX69dFhYQ44UvYA8eW8mBPitDgf52sadKUajlORmg0y7Xm.K','влаовлоал','ывадывдалдлыдва','+7 (013) 409-10-29',NULL,NULL,NULL,NULL,'customer',1,'2026-02-01 12:56:16','2026-02-01 12:56:16',0,0,NULL),
(24,'123123','123123@123.ru','$2y$12$SzIEYEtRfhYsU1Hxidz4l.6ZR8N04Ngcn5p/EWWUeCPmyI0spE1PO','фывафыв','фывфыв','+7 (123) 123-12-3',NULL,NULL,NULL,NULL,'customer',1,'2026-02-10 16:49:45','2026-02-10 16:49:45',0,0,NULL),
(25,'noqnvden','noqnvden@immenseignite.info','$2y$12$SBNTwXUpKa30PaHRyktfFO1LYow/ZGL1ICYfTEUhj9kNC/Bl8Eq3a','omfosxlskr','pzdzhoiest','+1-213-126-0420',NULL,NULL,NULL,NULL,'customer',1,'2026-08-29 22:19:26','2026-08-29 22:19:26',0,0,NULL),
(26,'sdafs','sdafs@asdf.ru','$2y$12$2KNX/dZqDFDQCDf80d5sZ.pE4wVh3Qd.S/9eum3LDwUnqoJuY8jFO','sdf','asdfasf','+7 (343) 423-42-34',NULL,NULL,NULL,NULL,'customer',1,'2026-09-02 08:11:07','2026-09-02 08:11:07',0,0,NULL),
(27,'1231','1231@123.ru','$2y$10$Iyc839uWZiMIbb2G9NUlHOI9IP7hwIS9v7rhBztJ/ohcPu0/msU1C','123','123','+7 (123) 123-12-31',NULL,NULL,NULL,NULL,'customer',1,'2026-09-02 08:37:02','2026-09-02 08:37:02',0,1,'2026-09-02 08:37:02');
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
