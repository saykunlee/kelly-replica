/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.21-MariaDB, for osx10.20 (arm64)
--
-- Host: localhost    Database: kelly
-- ------------------------------------------------------
-- Server version	10.6.21-MariaDB
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `board`
--

INSERT INTO `board` VALUES (1,1,'fantasy','review',9,'유럽 판타지','',1,1,'2024-06-26 16:19:23','2023-10-03 20:51:46',NULL,11,1,0);
INSERT INTO `board` VALUES (2,1,'joyful','review',9,'유럽 조이풀','',3,1,'2024-06-26 16:23:38','2023-10-03 20:51:46',NULL,7,1,0);
INSERT INTO `board` VALUES (3,1,'perfect','review',9,'유럽 퍼펙트','',2,1,'2024-06-26 16:23:38','2023-10-03 20:51:46',NULL,7,1,0);
INSERT INTO `board` VALUES (4,1,'october','review',9,'옥토버','',4,1,'2024-06-26 16:23:38','2023-10-03 20:51:46',NULL,7,1,0);
INSERT INTO `board` VALUES (5,1,'christmas','review',9,'크리스마스','',5,1,'2024-06-26 16:23:38','2023-10-03 20:51:46',NULL,7,1,0);
INSERT INTO `board` VALUES (6,1,'eastern_europe','review',9,'동유럽','',6,1,'2024-06-26 16:23:38','2023-10-03 20:51:46',NULL,7,1,0);
INSERT INTO `board` VALUES (7,1,'spain','review',9,'스페인','',7,1,'2024-06-26 16:23:38','2023-10-03 20:51:46',NULL,7,1,0);
INSERT INTO `board` VALUES (8,1,'croatia','review',9,'크로아티아','',8,1,'2024-06-26 16:23:38','2023-10-03 20:51:46',NULL,7,1,0);
INSERT INTO `board` VALUES (9,1,'swiss','review',9,'스위스','',9,1,'2024-06-26 16:23:38','2023-10-03 20:51:46',NULL,7,1,0);
INSERT INTO `board` VALUES (18,1,'island','review',9,'아이슬란드','',10,1,'2024-06-26 16:23:38','2023-10-07 13:09:15',NULL,7,1,0);
INSERT INTO `board` VALUES (19,1,'italy','review',9,'이탈리아','',11,1,'2024-06-26 16:23:38','2023-10-07 13:10:12',NULL,7,1,0);
INSERT INTO `board` VALUES (20,1,'portugal_spain','review',9,'포루투갈 스페인','',12,1,'2024-06-26 16:23:38','2023-10-07 13:11:06',NULL,7,1,0);
INSERT INTO `board` VALUES (21,1,'north_europe','review',9,'북유럽','',13,1,'2024-06-26 16:23:38','2023-10-07 13:12:32',NULL,7,1,0);
INSERT INTO `board` VALUES (22,1,'western_us','review',9,'미서부','',14,1,'2024-06-26 16:23:38','2023-10-07 13:13:18',NULL,7,1,0);
INSERT INTO `board` VALUES (23,1,'hokkaido','review',9,'홋카이도','',15,1,'2024-06-26 16:23:38','2023-10-07 13:13:48',NULL,7,1,0);
INSERT INTO `board` VALUES (24,1,'mediterranean','review',9,'지중해','',16,1,'2024-06-26 16:23:38','2023-10-07 13:14:11',NULL,7,1,0);
INSERT INTO `board` VALUES (25,1,'mongolia','review',9,'몽골','',17,1,'2024-06-26 16:23:38','2023-10-07 13:14:36',NULL,7,1,0);
INSERT INTO `board` VALUES (26,1,'western_europe','review',9,'서유럽','',18,1,'2024-06-26 16:23:38','2023-10-07 13:14:58',NULL,7,1,0);
INSERT INTO `board` VALUES (27,2,'america','inform',3,'미국여행','',3,1,'2024-06-14 16:38:34','2023-10-07 13:18:29',NULL,7,1,0);
INSERT INTO `board` VALUES (28,1,'europe','inform',NULL,'유럽여행','',2,1,'2024-06-14 16:38:23','2023-10-07 13:18:51',NULL,7,1,0);
INSERT INTO `board` VALUES (29,NULL,'test_board_1','inform',3,'테스트 게시판','',30,1,'2023-10-19 10:57:16','2023-10-18 15:23:51',1,1,1,1);
INSERT INTO `board` VALUES (30,1,'southamerica','review',9,'남미','',19,1,'2024-06-26 16:23:38','2024-01-25 14:46:41',7,7,1,0);
INSERT INTO `board` VALUES (31,1,'newyork','review',9,'뉴욕','',20,1,'2024-06-26 16:23:38','2024-01-25 14:47:28',7,NULL,1,0);
INSERT INTO `board` VALUES (32,2,'traveltips','inform',3,'해외여행꿀팁','',1,1,'2024-06-14 17:42:17','2024-03-29 11:42:24',7,7,1,0);
INSERT INTO `board` VALUES (33,2,'japan','inform',NULL,'일본여행','',6,1,'2024-06-14 16:42:50','2024-05-17 14:44:10',7,7,1,1);
INSERT INTO `board` VALUES (34,2,'asia','inform',3,'일본/아시아','',4,1,'2024-06-14 16:39:00','2024-05-17 14:45:09',7,7,1,0);
INSERT INTO `board` VALUES (35,2,'influencer','inform',3,'인플루언서','',2,1,'2024-06-04 15:47:06','2024-06-04 15:39:26',7,7,1,1);
INSERT INTO `board` VALUES (36,1,'southern_europe','review',9,'남유럽','',21,1,'2024-06-26 16:23:38','2024-06-24 11:12:00',7,NULL,1,0);
INSERT INTO `board` VALUES (37,1,'Sydney','review',9,'시드니','',22,1,NULL,'2024-07-26 13:45:19',7,NULL,1,0);
INSERT INTO `board` VALUES (38,1,'aaa','',0,'testestestes','',111,1,'2025-02-05 15:22:19','2025-02-05 14:53:05',NULL,NULL,0,1);
INSERT INTO `board` VALUES (39,1,'ttt','',0,'ttt','',5,1,'2025-02-05 15:22:14','2025-02-05 14:55:33',NULL,NULL,1,1);
INSERT INTO `board` VALUES (40,1,'nnn','',0,'nnnn','',122,0,'2025-02-05 15:22:21','2025-02-05 14:56:20',NULL,NULL,1,1);
INSERT INTO `board` VALUES (41,1,'mmm','',0,'mmm','',0,0,'2025-02-05 15:19:40','2025-02-05 14:57:27',NULL,NULL,1,1);
INSERT INTO `board` VALUES (42,0,'qwer','',0,'qwer','',1,1,'2025-02-07 16:22:46','2025-02-07 02:15:05',NULL,NULL,1,1);
INSERT INTO `board` VALUES (45,0,'123213213213213','',0,'qwer','',1,1,'2025-02-07 17:13:37','2025-02-07 02:17:17',NULL,NULL,1,1);
INSERT INTO `board` VALUES (47,1,'poiu','',0,'poiu','',0,0,'2025-02-07 08:40:01','2025-02-07 02:17:50',NULL,NULL,1,1);
INSERT INTO `board` VALUES (48,0,'bbbb','',0,'bbbb','',0,0,'2025-02-07 08:40:05','2025-02-07 02:18:43',NULL,NULL,1,1);
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-16 14:14:42
