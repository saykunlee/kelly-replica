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
-- Dumping data for table `menus`
--

INSERT INTO `menus` VALUES (1,'User Pages','#','','user',NULL,1,3,1,'2024-08-19 16:59:55',NULL,NULL,0,1,1,0);
INSERT INTO `menus` VALUES (2,'Other Pages','#','','file',NULL,6,3,1,'2024-08-19 16:59:55',NULL,NULL,0,1,1,0);
INSERT INTO `menus` VALUES (3,'View Profile','page-profile-view','admin\\UserPagesController::viewProfile','file',1,2,3,1,'2024-08-19 16:59:55',0,'2024-08-25 13:31:54',0,1,1,0);
INSERT INTO `menus` VALUES (4,'Connections','page-connections','admin\\UserPagesController::connections','file',1,3,3,1,'2024-08-19 16:59:55',NULL,'2024-08-25 13:31:54',0,1,1,0);
INSERT INTO `menus` VALUES (5,'Groups','page-groups','admin\\UserPagesController::groups','file',1,4,3,1,'2024-08-19 16:59:55',NULL,'2024-08-25 13:31:54',0,1,1,0);
INSERT INTO `menus` VALUES (6,'Events','page-events','admin\\UserPagesController::events','file',1,5,3,1,'2024-08-19 16:59:55',0,'2024-08-25 13:31:54',0,1,1,0);
INSERT INTO `menus` VALUES (7,'Timeline','page-timeline','admin\\OtherPagesController::timeline','file',2,7,3,1,'2024-08-19 16:59:55',NULL,'2024-08-25 13:31:54',0,1,1,0);
INSERT INTO `menus` VALUES (8,'Sales Monitorings','dashboard-one','admin\\DashboardController::salesMonitoring','shopping-bag',0,1,1,1,'2024-08-19 16:59:55',0,'2024-08-25 00:50:31',0,1,1,0);
INSERT INTO `menus` VALUES (9,'Website Analytics','dashboard-two','admin\\DashboardController::websiteAnalytics','globe',0,2,1,1,'2024-08-19 16:59:55',0,'2024-08-25 00:50:31',0,1,1,0);
INSERT INTO `menus` VALUES (10,'Cryptocurrency','dashboard-three','admin\\DashboardController::cryptocurrency','pie-chart',0,3,1,1,'2024-08-19 16:59:55',0,'2024-08-19 16:59:55',0,1,1,0);
INSERT INTO `menus` VALUES (11,'Helpdesk Management','dashboard-four','admin\\DashboardController::helpdeskManagement','life-buoy',0,4,1,1,'2024-08-19 16:59:55',0,'2024-08-19 16:59:55',0,1,1,0);
INSERT INTO `menus` VALUES (12,'Calendar','app-calendar','admin\\WebAppsController::calendar','calendar',0,1,2,1,'2024-08-19 16:59:55',0,'2024-08-19 16:59:55',0,1,1,0);
INSERT INTO `menus` VALUES (13,'Chat','app-chat','admin\\WebAppsController::chat','message-square',NULL,2,2,1,'2024-08-19 16:59:55',NULL,'2024-08-19 16:59:55',0,1,1,0);
INSERT INTO `menus` VALUES (14,'Contacts','app-contacts','admin\\WebAppsController::contacts','users',0,3,2,1,'2024-08-19 16:59:55',0,'2024-08-19 16:59:55',0,1,1,0);
INSERT INTO `menus` VALUES (15,'File Manager','app-file-manager','admin\\WebAppsController::fileManager','file-text',NULL,4,2,1,'2024-08-19 16:59:55',NULL,'2024-08-19 16:59:55',0,1,1,0);
INSERT INTO `menus` VALUES (16,'Mail','app-mail','admin\\WebAppsController::mail','mail',NULL,5,2,1,'2024-08-19 16:59:55',NULL,'2024-08-19 16:59:55',0,1,1,0);
INSERT INTO `menus` VALUES (17,'Components','components','admin\\UserInterfaceController::components','layers',0,1,4,1,'2024-08-19 16:59:55',0,'2024-08-19 16:59:55',0,1,1,0);
INSERT INTO `menus` VALUES (18,'Collections','collections','admin\\UserInterfaceController::collections','box',0,2,4,1,'2024-08-19 16:59:55',0,'2024-08-19 16:59:55',0,1,1,0);
INSERT INTO `menus` VALUES (19,'Sales Monitoring','/reservation-check','user\\ReservationController::checkReser','shopping-bag',0,1,5,1,'2024-08-19 16:59:55',0,'2024-08-20 18:17:27',0,1,1,0);
INSERT INTO `menus` VALUES (20,'Website Analytics','dashboard-two','admin\\DashboardController::websiteAnalytics','globe',0,2,5,1,'2024-08-19 16:59:55',0,'2024-08-19 16:59:55',0,1,1,0);
INSERT INTO `menus` VALUES (21,'Cryptocurrency','dashboard-three','admin\\DashboardController::cryptocurrency','pie-chart',0,3,5,1,'2024-08-19 16:59:55',0,'2024-08-19 16:59:55',0,1,1,0);
INSERT INTO `menus` VALUES (22,'Helpdesk Management','/reservation-check2','user\\ReservationController::checkReser','life-buoy',0,4,5,1,'2024-08-19 16:59:55',0,'2024-08-20 18:18:03',0,1,1,0);
INSERT INTO `menus` VALUES (23,'메뉴관리','#','','settings',0,40,6,1,'2024-08-19 16:59:55',NULL,'2024-09-05 16:37:27',0,1,1,0);
INSERT INTO `menus` VALUES (24,'사이드메뉴관리','/admin/sidemenu','Admin\\MenuManageController::sidemenu','',23,10,6,1,'2024-08-19 16:59:55',0,'2024-09-14 16:13:28',0,1,1,1);
INSERT INTO `menus` VALUES (29,'사용자','#','','user',0,10,6,0,'2024-09-01 22:29:57',0,'2024-09-01 22:34:30',0,1,1,0);
INSERT INTO `menus` VALUES (30,'사용자관리','/admin/members','Admin\\MemberManageController::members','',29,10,6,0,'2024-09-01 22:36:40',0,'2024-09-14 16:13:28',0,1,1,1);
INSERT INTO `menus` VALUES (33,'Sign-up','/login','Admin\\LoginController::index','',29,10,6,0,'2024-09-05 16:38:47',0,'2024-09-10 16:59:18',0,1,1,0);
INSERT INTO `menus` VALUES (34,'탑메뉴관리','/admin/topmenu','Admin\\MenuManageController::topmenu','',23,20,6,1,'2024-08-19 16:59:55',0,'2024-09-14 16:13:28',0,1,1,1);
INSERT INTO `menus` VALUES (35,'Log-out','/logout','Admin\\LoginController::logout','',29,20,6,0,'2024-09-09 17:03:05',NULL,NULL,0,1,1,0);
INSERT INTO `menus` VALUES (36,'로그인기록','/loginlog','Admin\\LoginController::loginlog','',29,40,6,0,'2024-09-10 15:10:52',0,'0000-00-00 00:00:00',0,1,1,0);
INSERT INTO `menus` VALUES (37,'사이트메뉴카테고리','/admin/sidecate','Admin\\MenuManageController::sidecate','',23,30,6,0,'2024-09-24 09:35:17',0,'0000-00-00 00:00:00',0,1,1,1);
INSERT INTO `menus` VALUES (42,'탑메뉴카테고리','/admin/topcate','Admin\\MenuManageController::topcate','',23,40,6,0,'2025-01-03 15:58:23',NULL,NULL,0,1,1,1);
INSERT INTO `menus` VALUES (43,'게시판관리','#','','user',0,30,10,0,'2025-02-03 13:20:18',NULL,'2025-02-15 17:37:41',0,1,1,1);
INSERT INTO `menus` VALUES (44,'게시판그룹','/admin/board/board-gorups','Admin\\BoardManageController::manageboardgroups','',43,10,10,0,'2025-02-03 13:24:48',NULL,'2025-02-03 13:25:28',0,1,1,1);
INSERT INTO `menus` VALUES (45,'게시판관리','/admin/board/manage-boards','Admin\\BoardManageController::manageboard','',43,20,10,0,'2025-02-03 13:26:31',NULL,'2025-02-03 13:27:26',0,1,1,1);
INSERT INTO `menus` VALUES (46,'게시글관리','/admin/board/manage-posts','Admin\\BoardManageController::manageposts','',43,30,10,0,'2025-02-03 13:28:17',NULL,'2025-02-15 17:41:39',0,1,1,1);
INSERT INTO `menus` VALUES (47,'게시글상세','/admin/board/detail-post','Admin\\BoardManageController::detailpost','',43,40,10,0,'2025-02-06 17:56:52',NULL,'2025-02-06 17:57:43',0,1,0,1);
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-16 14:15:55
