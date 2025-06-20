-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: website
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `follows`
--

DROP TABLE IF EXISTS `follows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `follows` (
  `follower_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  `followed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`follower_id`,`following_id`),
  KEY `following_id` (`following_id`),
  CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `follows`
--

LOCK TABLES `follows` WRITE;
/*!40000 ALTER TABLE `follows` DISABLE KEYS */;
INSERT INTO `follows` VALUES (1,2,'2025-06-20 14:29:54');
/*!40000 ALTER TABLE `follows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `postreplies`
--

DROP TABLE IF EXISTS `postreplies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `postreplies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reply_text` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_postreplies_post` (`post_id`),
  KEY `fk_postreplies_user` (`user_id`),
  CONSTRAINT `fk_postreplies_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_postreplies_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `postreplies`
--

LOCK TABLES `postreplies` WRITE;
/*!40000 ALTER TABLE `postreplies` DISABLE KEYS */;
INSERT INTO `postreplies` VALUES (8,16,1,'This is a comment','2025-06-19 17:11:27'),(9,16,1,'This is epic lol\r\nhahahahahahahahaa','2025-06-19 17:11:39'),(10,16,1,'Noit nei','2025-06-19 17:11:51'),(15,17,1,'Sweeettt!!!','2025-06-20 13:25:01');
/*!40000 ALTER TABLE `postreplies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `media_url` varchar(100) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `vote` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_posts_user` (`user_id`),
  CONSTRAINT `fk_posts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (16,2,'asdf','uploads/Wilmer/1750341320_blank.jpg','asdf','2025-06-19 13:55:20',2),(17,2,'AFDS',NULL,'SDG','2025-06-19 14:13:11',2),(21,4,'Windows vs Linux','uploads/brogrammer/1750434607_4gb-ram-on-linux-vs-windows-v0-8dwpkl3smo6f1.webp','I choose linux lol','2025-06-20 15:50:07',0),(22,4,'Poor guy','uploads/brogrammer/1750434648_7oz1ep486i3f1.webp','So true!','2025-06-20 15:50:48',0),(23,4,'WHY?','uploads/brogrammer/1750434717_aka-vibe-coders-v0-0at05ai3z08f1.webp','WHY?','2025-06-20 15:51:57',0),(24,4,'He did what?????','uploads/brogrammer/1750434777_f1s1l7dl1blc1.webp','Imagine coding in Hebrew????!!!','2025-06-20 15:52:57',0),(25,4,'Ah yes...ooooo','uploads/brogrammer/1750434821_some-easy-code-v0-tpt4r7u9ewjc1.webp','oooooooo','2025-06-20 15:53:41',0),(26,4,'huh???','uploads/brogrammer/1750434864_i-guess-that-works-v0-0p86nc79himc1.webp','','2025-06-20 15:54:24',0),(27,5,'Thoughts?','uploads/MemerMannnnn/1750435099_8-years-old-me-v0-ko3l9d5p3k7f1.webp','An intrusive though if you will','2025-06-20 15:58:19',0),(28,5,'Parents do be cooking','uploads/MemerMannnnn/1750435127_good-olwd-days-v0-k0x37brtg28f1.webp','','2025-06-20 15:58:47',0),(29,5,'Doge','uploads/MemerMannnnn/1750435168_meme.webp','Dogs ammirite????','2025-06-20 15:59:28',0),(30,5,'Is this true?????','uploads/MemerMannnnn/1750435223_ka0npcwe238f1.mp4','','2025-06-20 16:00:23',0),(31,5,'Patriccc????','uploads/MemerMannnnn/1750435277_patric.jpg','Guilty lol','2025-06-20 16:01:17',0),(32,6,'Who\'s this???','uploads/MuscleCarDealer/1750435483_31o4kla09hj41.webp','Saw this beaut the other day','2025-06-20 16:04:43',0),(33,6,'Such a classic!!','uploads/MuscleCarDealer/1750435525_bniri8om7jt41.webp','Don\'t know what it is lol','2025-06-20 16:05:25',0),(34,6,'An old chevy?','uploads/MuscleCarDealer/1750435563_images.jpeg','What is this?','2025-06-20 16:06:03',0),(35,6,'Screaming eagle sound intensifies','uploads/MuscleCarDealer/1750435632_Untitled4.jpeg','','2025-06-20 16:07:12',0),(36,6,'Ford mustang','uploads/MuscleCarDealer/1750435681_imaasasges.jpeg','The blue on this car!!!!','2025-06-20 16:08:01',0),(37,4,'HAHA','uploads/brogrammer/1750435747_love-python-v0-4kjwejella7f1.webp','Python slow sometimes','2025-06-20 16:09:07',0);
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `session_id` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `timeLastSeen` int(10) unsigned NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('40d690c638261a251ee25c2ea3cb4a854dda6f63','Brogrammer',1750435747),('5de64d760aa6b2b15b613f3822672dcb3559983e','memerman@gmail.com',1750435277),('67011ef99bb4d15451fbcb2872f10f5f77ebb983','cars@gmail.com',1750435681),('d3d0ec22c441334e08defc9d17b8c7b3bf8e96ee','jw',1750347769),('fbf9f8d17cef264e15f9a0cd4537f867eccd70b3','Wilmer',1750428741);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `topicfollows`
--

DROP TABLE IF EXISTS `topicfollows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `topicfollows` (
  `follower_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `followed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`follower_id`,`topic_id`),
  KEY `fk_topicfollows_topic` (`topic_id`),
  CONSTRAINT `fk_topicfollows_follower` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_topicfollows_topic` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `topicfollows`
--

LOCK TABLES `topicfollows` WRITE;
/*!40000 ALTER TABLE `topicfollows` DISABLE KEYS */;
/*!40000 ALTER TABLE `topicfollows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `topicmessages`
--

DROP TABLE IF EXISTS `topicmessages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `topicmessages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `time_created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_topicmessages_topic` (`topic_id`),
  KEY `fk_topicmessages_post` (`post_id`),
  CONSTRAINT `fk_topicmessages_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_topicmessages_topic` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `topicmessages`
--

LOCK TABLES `topicmessages` WRITE;
/*!40000 ALTER TABLE `topicmessages` DISABLE KEYS */;
INSERT INTO `topicmessages` VALUES (9,14,17,'2025-06-19 14:13:11'),(16,23,21,'2025-06-20 15:50:07'),(17,12,21,'2025-06-20 15:50:07'),(18,24,21,'2025-06-20 15:50:07'),(19,24,22,'2025-06-20 15:50:48'),(20,12,22,'2025-06-20 15:50:48'),(21,12,23,'2025-06-20 15:51:57'),(22,24,23,'2025-06-20 15:51:57'),(23,25,23,'2025-06-20 15:51:57'),(24,24,24,'2025-06-20 15:52:57'),(25,23,24,'2025-06-20 15:52:57'),(26,23,25,'2025-06-20 15:53:41'),(27,26,25,'2025-06-20 15:53:41'),(28,12,26,'2025-06-20 15:54:24'),(29,23,26,'2025-06-20 15:54:24'),(30,25,26,'2025-06-20 15:54:24'),(31,24,27,'2025-06-20 15:58:19'),(32,27,27,'2025-06-20 15:58:19'),(33,26,27,'2025-06-20 15:58:19'),(34,24,28,'2025-06-20 15:58:47'),(35,26,28,'2025-06-20 15:58:47'),(36,28,28,'2025-06-20 15:58:47'),(37,29,29,'2025-06-20 15:59:28'),(38,27,29,'2025-06-20 15:59:28'),(39,26,29,'2025-06-20 15:59:28'),(40,24,29,'2025-06-20 15:59:28'),(41,27,30,'2025-06-20 16:00:23'),(42,30,30,'2025-06-20 16:00:23'),(43,24,30,'2025-06-20 16:00:23'),(44,21,31,'2025-06-20 16:01:17'),(45,31,31,'2025-06-20 16:01:17'),(46,26,31,'2025-06-20 16:01:17'),(47,27,31,'2025-06-20 16:01:17'),(48,32,32,'2025-06-20 16:04:43'),(49,33,32,'2025-06-20 16:04:43'),(50,32,33,'2025-06-20 16:05:25'),(51,34,33,'2025-06-20 16:05:25'),(52,32,34,'2025-06-20 16:06:03'),(53,35,34,'2025-06-20 16:06:03'),(54,33,34,'2025-06-20 16:06:03'),(55,32,35,'2025-06-20 16:07:12'),(56,36,35,'2025-06-20 16:07:12'),(57,32,36,'2025-06-20 16:08:01'),(58,34,36,'2025-06-20 16:08:01'),(59,32,37,'2025-06-20 16:09:07'),(60,24,37,'2025-06-20 16:09:07'),(61,12,37,'2025-06-20 16:09:07');
/*!40000 ALTER TABLE `topicmessages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `topics`
--

DROP TABLE IF EXISTS `topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `topics`
--

LOCK TABLES `topics` WRITE;
/*!40000 ALTER TABLE `topics` DISABLE KEYS */;
INSERT INTO `topics` VALUES (12,'coding','2025-06-19 07:30:45'),(13,'networks','2025-06-19 07:30:45'),(14,'','2025-06-19 13:33:44'),(15,'Music','2025-06-19 15:21:44'),(16,'Technology','2025-06-19 15:21:44'),(17,'Toets','2025-06-19 15:55:20'),(18,'leke','2025-06-19 15:55:20'),(19,'REII414','2025-06-19 18:43:19'),(20,'fun','2025-06-19 18:43:19'),(21,'spongebob','2025-06-20 15:22:26'),(22,'bakinibottom','2025-06-20 15:22:26'),(23,'programming','2025-06-20 17:50:07'),(24,'memes','2025-06-20 17:50:07'),(25,'fullstackdev','2025-06-20 17:51:57'),(26,'funny','2025-06-20 17:53:41'),(27,'humor','2025-06-20 17:58:19'),(28,'lol','2025-06-20 17:58:47'),(29,'dogs','2025-06-20 17:59:28'),(30,'confused','2025-06-20 18:00:23'),(31,'meme','2025-06-20 18:01:17'),(32,'cars','2025-06-20 18:04:43'),(33,'americanmuscle','2025-06-20 18:04:43'),(34,'cool','2025-06-20 18:05:25'),(35,'old','2025-06-20 18:06:03'),(36,'trucks','2025-06-20 18:07:12');
/*!40000 ALTER TABLE `topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `bio` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `salt` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Enrico','taljaardenrico798@gmail.com','8b816221dbd85ef93069845defd29def6cf67d0c','Enrico Taljaard','Leedle leedle leedle','profilePictures/1750274562_spongebob.jpg','2025-06-17 16:48:38','b9035ac304'),(2,'Wilmer','wilmerkluever@gmail.com','0ec19503d109c1da33b34de3d482ab2003551272','Wilmer Kluever','Champ!','profilePictures/1750428741_patric.jpg','2025-06-17 16:53:27','27b0ddd95f'),(3,'jw','jwkluever.patria@gmail.com','ed411d7041d8b2e2fd0524ab4d09c28d8ea10cc3',NULL,NULL,NULL,'2025-06-19 13:46:28','df833e09c1'),(4,'brogrammer','brogrammer@gmail.com','22b4075e54809ba11f8ca2b7b7ef26b153e63de0','Ewan','I do programming warcrimes','profilePictures/1750434557_Untitledsdsd.jpeg','2025-06-20 15:47:24','87b9dd5f02'),(5,'MemerMannnnn','memerman@gmail.com','432a78391051e2a4561e439d1ef2a58403a083ab','I am memerman','posting memes since yesterday','profilePictures/1750435021_sdsds.jpeg','2025-06-20 15:55:43','7923b13061'),(6,'MuscleCarDealer','cars@gmail.com','e208028ddcb9c0950fd2c9b81481659db4ac95d7','Hill Billy Dan','I am the American muscle car dealer','profilePictures/1750435437_sdsdsdsdsddd.jpeg','2025-06-20 16:02:28','c079b0976b');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votes` (
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `vote_value` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `votes`
--

LOCK TABLES `votes` WRITE;
/*!40000 ALTER TABLE `votes` DISABLE KEYS */;
INSERT INTO `votes` VALUES (0,2,-1),(0,3,-1),(1,16,1),(1,17,1),(1,18,1),(2,13,1),(2,14,-1),(2,15,-1),(2,16,1),(2,17,1);
/*!40000 ALTER TABLE `votes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-20 18:10:48
