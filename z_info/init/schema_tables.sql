/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.21-MariaDB, for osx10.20 (arm64)
--
-- Host: localhost    Database: kelly
-- ------------------------------------------------------
-- Server version	10.6.21-MariaDB

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
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance` (
  `att_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `att_point` int(11) NOT NULL DEFAULT 0,
  `att_memo` varchar(255) DEFAULT NULL,
  `att_continuity` int(11) unsigned NOT NULL DEFAULT 0,
  `att_ranking` int(11) unsigned NOT NULL DEFAULT 0,
  `att_date` date DEFAULT NULL,
  `att_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`att_id`),
  UNIQUE KEY `att_date_mem_id` (`att_date`,`mem_id`),
  KEY `att_datetime_mem_id` (`att_datetime`,`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `autologin`
--

DROP TABLE IF EXISTS `autologin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `autologin` (
  `aul_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `aul_key` varchar(255) NOT NULL DEFAULT '',
  `aul_ip` varchar(50) NOT NULL DEFAULT '',
  `aul_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`aul_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banner`
--

DROP TABLE IF EXISTS `banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `banner` (
  `ban_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ban_start_date` date DEFAULT NULL,
  `ban_end_date` date DEFAULT NULL,
  `bng_name` varchar(100) NOT NULL DEFAULT '',
  `ban_title` varchar(255) NOT NULL DEFAULT '',
  `ban_url` varchar(255) NOT NULL DEFAULT '',
  `ban_target` varchar(255) NOT NULL DEFAULT '',
  `ban_device` varchar(255) NOT NULL DEFAULT '',
  `ban_width` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `ban_height` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `ban_hit` int(11) unsigned NOT NULL DEFAULT 0,
  `ban_order` int(11) unsigned NOT NULL DEFAULT 0,
  `ban_image` varchar(255) NOT NULL DEFAULT '',
  `ban_activated` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `ban_datetime` datetime DEFAULT NULL,
  `ban_ip` varchar(50) NOT NULL DEFAULT '',
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`ban_id`),
  KEY `bng_name` (`bng_name`),
  KEY `ban_start_date` (`ban_start_date`),
  KEY `ban_end_date` (`ban_end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banner_click_log`
--

DROP TABLE IF EXISTS `banner_click_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `banner_click_log` (
  `bcl_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ban_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned DEFAULT NULL,
  `bcl_datetime` datetime DEFAULT NULL,
  `bcl_ip` varchar(50) NOT NULL DEFAULT '',
  `bcl_referer` varchar(255) NOT NULL DEFAULT '',
  `bcl_url` varchar(255) NOT NULL DEFAULT '',
  `bcl_useragent` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`bcl_id`),
  KEY `ban_id` (`ban_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banner_group`
--

DROP TABLE IF EXISTS `banner_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `banner_group` (
  `bng_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bng_name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`bng_id`),
  KEY `bng_name` (`bng_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blame`
--

DROP TABLE IF EXISTS `blame`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blame` (
  `bla_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `target_id` int(11) unsigned NOT NULL DEFAULT 0,
  `target_type` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `target_mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `bla_datetime` datetime DEFAULT NULL,
  `bla_ip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`bla_id`),
  KEY `mem_id` (`mem_id`),
  KEY `target_mem_id` (`target_mem_id`),
  KEY `target_id` (`target_id`),
  KEY `brd_id` (`brd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `board`
--

DROP TABLE IF EXISTS `board`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `board` (
  `brd_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bgr_id` int(11) unsigned DEFAULT 0,
  `brd_key` varchar(100) NOT NULL DEFAULT '',
  `brd_type` varchar(100) DEFAULT 'inform' COMMENT 'board type inform , review',
  `brd_form` int(11) DEFAULT NULL,
  `brd_name` varchar(255) NOT NULL DEFAULT '',
  `brd_mobile_name` varchar(255) NOT NULL DEFAULT '',
  `brd_order` int(11) NOT NULL DEFAULT 0,
  `brd_search` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `update_date` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `insert_date` datetime NOT NULL DEFAULT sysdate(),
  `insert_id` int(11) DEFAULT NULL,
  `update_id` int(11) DEFAULT NULL,
  `useing` int(11) NOT NULL DEFAULT 1,
  `is_deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`brd_id`),
  UNIQUE KEY `brd_key` (`brd_key`),
  KEY `bgr_id` (`bgr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `board_admin`
--

DROP TABLE IF EXISTS `board_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `board_admin` (
  `bam_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`bam_id`),
  KEY `brd_id` (`brd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `board_category`
--

DROP TABLE IF EXISTS `board_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `board_category` (
  `bca_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `bca_key` varchar(255) NOT NULL DEFAULT '',
  `bca_value` varchar(255) NOT NULL DEFAULT '',
  `bca_parent` varchar(255) NOT NULL DEFAULT '',
  `bca_order` int(11) unsigned NOT NULL DEFAULT 0,
  `insert_date` datetime NOT NULL DEFAULT sysdate(),
  `insert_id` int(11) DEFAULT NULL,
  `update_date` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `update_id` int(11) DEFAULT NULL,
  `useing` int(11) NOT NULL DEFAULT 1,
  `is_deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`bca_id`),
  KEY `brd_id` (`brd_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `board_group`
--

DROP TABLE IF EXISTS `board_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `board_group` (
  `bgr_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bgr_key` varchar(100) NOT NULL DEFAULT '',
  `bgr_name` varchar(255) NOT NULL DEFAULT '',
  `bgr_order` int(11) NOT NULL DEFAULT 0,
  `update_date` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `insert_date` datetime NOT NULL DEFAULT sysdate(),
  `insert_id` int(11) DEFAULT NULL,
  `update_id` int(11) DEFAULT NULL,
  `useing` int(11) NOT NULL DEFAULT 1,
  `is_deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`bgr_id`),
  UNIQUE KEY `bgr_key` (`bgr_key`),
  KEY `bgr_order` (`bgr_order`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `board_group_admin`
--

DROP TABLE IF EXISTS `board_group_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `board_group_admin` (
  `bga_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bgr_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`bga_id`),
  KEY `bgr_id` (`bgr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `board_group_meta`
--

DROP TABLE IF EXISTS `board_group_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `board_group_meta` (
  `bgr_id` int(11) unsigned NOT NULL DEFAULT 0,
  `bgm_key` varchar(100) NOT NULL DEFAULT '',
  `bgm_value` text DEFAULT NULL,
  UNIQUE KEY `bgr_id_bgm_key` (`bgr_id`,`bgm_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `board_meta`
--

DROP TABLE IF EXISTS `board_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `board_meta` (
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `bmt_key` varchar(100) NOT NULL DEFAULT '',
  `bmt_value` text DEFAULT NULL,
  UNIQUE KEY `brd_id_bmt_key` (`brd_id`,`bmt_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cmall_cart`
--

DROP TABLE IF EXISTS `cmall_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmall_cart` (
  `cct_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cde_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cct_count` int(11) unsigned NOT NULL DEFAULT 0,
  `cct_cart` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cct_order` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cct_datetime` datetime DEFAULT NULL,
  `cct_ip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`cct_id`),
  KEY `mem_id` (`mem_id`),
  KEY `cit_id` (`cit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cmall_category`
--

DROP TABLE IF EXISTS `cmall_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmall_category` (
  `cca_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cca_value` varchar(255) NOT NULL DEFAULT '',
  `cca_parent` varchar(255) NOT NULL DEFAULT '',
  `cca_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`cca_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cmall_category_rel`
--

DROP TABLE IF EXISTS `cmall_category_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmall_category_rel` (
  `ccr_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cca_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`ccr_id`),
  KEY `cit_id` (`cit_id`),
  KEY `cca_id` (`cca_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cmall_demo_click_log`
--

DROP TABLE IF EXISTS `cmall_demo_click_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmall_demo_click_log` (
  `cdc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cdc_type` varchar(50) NOT NULL DEFAULT '',
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cdc_datetime` datetime DEFAULT NULL,
  `cdc_ip` varchar(50) NOT NULL DEFAULT '',
  `cdc_useragent` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`cdc_id`),
  KEY `cit_id` (`cit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cmall_download_log`
--

DROP TABLE IF EXISTS `cmall_download_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmall_download_log` (
  `cdo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cde_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cdo_datetime` datetime DEFAULT NULL,
  `cdo_ip` varchar(50) NOT NULL DEFAULT '',
  `cdo_useragent` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`cdo_id`),
  KEY `cde_id` (`cde_id`),
  KEY `cit_id` (`cit_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cmall_item`
--

DROP TABLE IF EXISTS `cmall_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmall_item` (
  `cit_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cit_key` varchar(100) NOT NULL DEFAULT '',
  `cit_name` varchar(255) NOT NULL DEFAULT '',
  `cit_order` int(11) NOT NULL DEFAULT 0,
  `cit_type1` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cit_type2` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cit_type3` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cit_type4` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cit_status` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cit_summary` text DEFAULT NULL,
  `cit_content` mediumtext DEFAULT NULL,
  `cit_mobile_content` mediumtext DEFAULT NULL,
  `cit_content_html_type` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cit_price` int(11) unsigned NOT NULL DEFAULT 0,
  `cit_file_1` varchar(255) NOT NULL DEFAULT '',
  `cit_file_2` varchar(255) NOT NULL DEFAULT '',
  `cit_file_3` varchar(255) NOT NULL DEFAULT '',
  `cit_file_4` varchar(255) NOT NULL DEFAULT '',
  `cit_file_5` varchar(255) NOT NULL DEFAULT '',
  `cit_file_6` varchar(255) NOT NULL DEFAULT '',
  `cit_file_7` varchar(255) NOT NULL DEFAULT '',
  `cit_file_8` varchar(255) NOT NULL DEFAULT '',
  `cit_file_9` varchar(255) NOT NULL DEFAULT '',
  `cit_file_10` varchar(255) NOT NULL DEFAULT '',
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cit_hit` int(11) unsigned NOT NULL DEFAULT 0,
  `cit_datetime` datetime DEFAULT NULL,
  `cit_updated_datetime` datetime DEFAULT NULL,
  `cit_sell_count` int(11) unsigned NOT NULL DEFAULT 0,
  `cit_wish_count` int(11) unsigned NOT NULL DEFAULT 0,
  `cit_download_days` int(11) unsigned NOT NULL DEFAULT 0,
  `cit_review_count` int(11) unsigned NOT NULL DEFAULT 0,
  `cit_review_average` decimal(2,1) NOT NULL DEFAULT 0.0,
  `cit_qna_count` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`cit_id`),
  UNIQUE KEY `cit_key` (`cit_key`),
  KEY `cit_order` (`cit_order`),
  KEY `cit_price` (`cit_price`),
  KEY `cit_sell_count` (`cit_sell_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cmall_item_detail`
--

DROP TABLE IF EXISTS `cmall_item_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmall_item_detail` (
  `cde_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cde_title` varchar(255) NOT NULL DEFAULT '',
  `cde_price` int(11) unsigned NOT NULL DEFAULT 0,
  `cde_originname` varchar(255) NOT NULL DEFAULT '',
  `cde_filename` varchar(255) NOT NULL DEFAULT '',
  `cde_download` int(11) unsigned NOT NULL DEFAULT 0,
  `cde_filesize` int(11) unsigned NOT NULL DEFAULT 0,
  `cde_type` varchar(10) NOT NULL DEFAULT '',
  `cde_is_image` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cde_datetime` datetime DEFAULT NULL,
  `cde_ip` varchar(50) NOT NULL DEFAULT '',
  `cde_status` tinyint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`cde_id`),
  KEY `cit_id` (`cit_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cmall_item_history`
--

DROP TABLE IF EXISTS `cmall_item_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmall_item_history` (
  `chi_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `chi_title` varchar(255) NOT NULL DEFAULT '',
  `chi_content` mediumtext DEFAULT NULL,
  `chi_content_html_type` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `chi_ip` varchar(50) NOT NULL DEFAULT '',
  `chi_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`chi_id`),
  KEY `cit_id` (`cit_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cmall_item_meta`
--

DROP TABLE IF EXISTS `cmall_item_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmall_item_meta` (
  `cit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cim_key` varchar(100) NOT NULL DEFAULT '',
  `cim_value` text DEFAULT NULL,
  UNIQUE KEY `cit_id_cim_key` (`cit_id`,`cim_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cmall_order`
--

DROP TABLE IF EXISTS `cmall_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmall_order` (
  `cor_id` bigint(20) unsigned NOT NULL,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_nickname` varchar(100) NOT NULL DEFAULT '',
  `mem_realname` varchar(100) NOT NULL DEFAULT '',
  `mem_email` varchar(100) NOT NULL DEFAULT '',
  `mem_phone` varchar(255) NOT NULL DEFAULT '',
  `cor_memo` text DEFAULT NULL,
  `cor_total_money` int(11) NOT NULL DEFAULT 0,
  `cor_deposit` int(11) NOT NULL DEFAULT 0,
  `cor_cash_request` int(11) NOT NULL DEFAULT 0,
  `cor_cash` int(11) NOT NULL DEFAULT 0,
  `cor_content` text DEFAULT NULL,
  `cor_pay_type` varchar(100) NOT NULL DEFAULT '',
  `cor_pg` varchar(255) NOT NULL DEFAULT '',
  `cor_tno` varchar(255) NOT NULL DEFAULT '',
  `cor_app_no` varchar(255) NOT NULL DEFAULT '',
  `cor_bank_info` varchar(255) NOT NULL DEFAULT '',
  `cor_admin_memo` text DEFAULT NULL,
  `cor_datetime` datetime DEFAULT NULL,
  `cor_approve_datetime` datetime DEFAULT NULL,
  `cor_ip` varchar(50) NOT NULL DEFAULT '',
  `cor_useragent` varchar(255) NOT NULL DEFAULT '',
  `cor_status` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cor_vbank_expire` datetime DEFAULT NULL,
  `is_test` char(1) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT '',
  `cor_refund_price` int(11) NOT NULL DEFAULT 0,
  `cor_order_history` text DEFAULT NULL,
  PRIMARY KEY (`cor_id`),
  KEY `mem_id` (`mem_id`),
  KEY `cor_pay_type` (`cor_pay_type`),
  KEY `cor_datetime` (`cor_datetime`),
  KEY `cor_approve_datetime` (`cor_approve_datetime`),
  KEY `cor_status` (`cor_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cmall_order_detail`
--

DROP TABLE IF EXISTS `cmall_order_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmall_order_detail` (
  `cod_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cor_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cde_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cod_download_days` int(11) NOT NULL DEFAULT 0,
  `cod_count` int(11) unsigned NOT NULL DEFAULT 0,
  `cod_status` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`cod_id`),
  KEY `cor_id` (`cor_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cmall_qna`
--

DROP TABLE IF EXISTS `cmall_qna`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmall_qna` (
  `cqa_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cqa_title` varchar(255) NOT NULL DEFAULT '',
  `cqa_content` mediumtext DEFAULT NULL,
  `cqa_content_html_type` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cqa_reply_content` mediumtext DEFAULT NULL,
  `cqa_reply_html_type` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cqa_secret` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cqa_receive_email` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cqa_receive_sms` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cqa_datetime` datetime DEFAULT NULL,
  `cqa_ip` varchar(50) NOT NULL DEFAULT '',
  `cqa_reply_datetime` datetime DEFAULT NULL,
  `cqa_reply_mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cqa_reply_ip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`cqa_id`),
  KEY `cit_id` (`cit_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cmall_review`
--

DROP TABLE IF EXISTS `cmall_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmall_review` (
  `cre_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cre_title` varchar(255) NOT NULL DEFAULT '',
  `cre_content` mediumtext DEFAULT NULL,
  `cre_content_html_type` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cre_score` tinyint(4) NOT NULL DEFAULT 0,
  `cre_datetime` datetime DEFAULT NULL,
  `cre_ip` varchar(50) NOT NULL DEFAULT '',
  `cre_status` tinyint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`cre_id`),
  KEY `cit_id` (`cit_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cmall_wishlist`
--

DROP TABLE IF EXISTS `cmall_wishlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmall_wishlist` (
  `cwi_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cwi_datetime` datetime DEFAULT NULL,
  `cwi_ip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`cwi_id`),
  UNIQUE KEY `mem_id_cit_id` (`mem_id`,`cit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `comment` (
  `cmt_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL DEFAULT 0,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cmt_num` int(11) NOT NULL DEFAULT 0,
  `cmt_reply` varchar(20) NOT NULL DEFAULT '',
  `cmt_html` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cmt_secret` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `cmt_content` text DEFAULT NULL,
  `mem_id` int(11) NOT NULL DEFAULT 0,
  `cmt_password` varchar(255) NOT NULL DEFAULT '',
  `cmt_userid` varchar(100) NOT NULL DEFAULT '',
  `cmt_username` varchar(100) NOT NULL DEFAULT '',
  `cmt_nickname` varchar(100) NOT NULL DEFAULT '',
  `cmt_email` varchar(255) NOT NULL DEFAULT '',
  `cmt_homepage` text DEFAULT NULL,
  `cmt_datetime` datetime DEFAULT NULL,
  `cmt_updated_datetime` datetime DEFAULT NULL,
  `cmt_ip` varchar(50) NOT NULL DEFAULT '',
  `cmt_like` int(11) unsigned NOT NULL DEFAULT 0,
  `cmt_dislike` int(11) unsigned NOT NULL DEFAULT 0,
  `cmt_blame` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `cmt_device` varchar(10) NOT NULL DEFAULT '',
  `cmt_del` tinyint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`cmt_id`),
  KEY `post_id_cmt_num_cmt_reply` (`post_id`,`cmt_num`,`cmt_reply`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comment_meta`
--

DROP TABLE IF EXISTS `comment_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `comment_meta` (
  `cmt_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cme_key` varchar(100) NOT NULL DEFAULT '',
  `cme_value` text DEFAULT NULL,
  UNIQUE KEY `cmt_id_cme_key` (`cmt_id`,`cme_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `config` (
  `cfg_key` varchar(100) NOT NULL DEFAULT '',
  `cfg_value` text DEFAULT NULL,
  UNIQUE KEY `cfg_key` (`cfg_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `currentvisitor`
--

DROP TABLE IF EXISTS `currentvisitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `currentvisitor` (
  `cur_ip` varchar(50) NOT NULL DEFAULT '',
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cur_mem_name` varchar(255) NOT NULL DEFAULT '',
  `cur_datetime` datetime DEFAULT NULL,
  `cur_page` text DEFAULT NULL,
  `cur_url` text DEFAULT NULL,
  `cur_referer` text DEFAULT NULL,
  `cur_useragent` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`cur_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datatable_columns`
--

DROP TABLE IF EXISTS `datatable_columns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `datatable_columns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_id` int(11) NOT NULL,
  `order` int(11) DEFAULT NULL,
  `data` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `width` varchar(50) DEFAULT NULL,
  `className` varchar(255) DEFAULT NULL,
  `orderable` tinyint(1) DEFAULT 1,
  `render` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT sysdate(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `visible` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datatable_settings`
--

DROP TABLE IF EXISTS `datatable_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `datatable_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(100) DEFAULT NULL,
  `dt_buttons` varchar(255) DEFAULT NULL,
  `pageLength` int(10) DEFAULT 10,
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT sysdate(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `showCheckbox` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deposit`
--

DROP TABLE IF EXISTS `deposit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `deposit` (
  `dep_id` bigint(20) unsigned NOT NULL,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_nickname` varchar(100) NOT NULL DEFAULT '',
  `mem_realname` varchar(100) NOT NULL DEFAULT '',
  `mem_email` varchar(100) NOT NULL DEFAULT '',
  `mem_phone` varchar(255) NOT NULL DEFAULT '',
  `dep_from_type` varchar(100) NOT NULL DEFAULT '',
  `dep_to_type` varchar(100) NOT NULL DEFAULT '',
  `dep_deposit_request` int(11) NOT NULL DEFAULT 0,
  `dep_deposit` int(11) NOT NULL DEFAULT 0,
  `dep_deposit_sum` int(11) NOT NULL DEFAULT 0,
  `dep_cash_request` int(11) NOT NULL DEFAULT 0,
  `dep_cash` int(11) NOT NULL DEFAULT 0,
  `dep_point` int(11) NOT NULL DEFAULT 0,
  `dep_content` varchar(255) NOT NULL DEFAULT '',
  `dep_pay_type` varchar(100) NOT NULL DEFAULT '',
  `dep_pg` varchar(255) NOT NULL DEFAULT '',
  `dep_tno` varchar(255) NOT NULL DEFAULT '',
  `dep_app_no` varchar(255) NOT NULL DEFAULT '',
  `dep_bank_info` varchar(255) NOT NULL DEFAULT '',
  `dep_admin_memo` text DEFAULT NULL,
  `dep_datetime` datetime DEFAULT NULL,
  `dep_deposit_datetime` datetime DEFAULT NULL,
  `dep_ip` varchar(50) NOT NULL DEFAULT '',
  `dep_useragent` varchar(255) NOT NULL DEFAULT '',
  `dep_status` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `dep_vbank_expire` datetime DEFAULT NULL,
  `is_test` char(1) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT '',
  `dep_refund_price` int(11) NOT NULL DEFAULT 0,
  `dep_order_history` text DEFAULT NULL,
  PRIMARY KEY (`dep_id`),
  KEY `mem_id` (`mem_id`),
  KEY `dep_pay_type` (`dep_pay_type`),
  KEY `dep_datetime` (`dep_datetime`),
  KEY `dep_deposit_datetime` (`dep_deposit_datetime`),
  KEY `dep_status` (`dep_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document`
--

DROP TABLE IF EXISTS `document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `document` (
  `doc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `doc_key` varchar(100) NOT NULL DEFAULT '',
  `doc_title` varchar(255) NOT NULL DEFAULT '',
  `doc_content` mediumtext DEFAULT NULL,
  `doc_mobile_content` mediumtext DEFAULT NULL,
  `doc_content_html_type` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `doc_layout` varchar(255) NOT NULL DEFAULT '',
  `doc_mobile_layout` varchar(255) NOT NULL DEFAULT '',
  `doc_sidebar` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `doc_mobile_sidebar` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `doc_skin` varchar(255) NOT NULL DEFAULT '',
  `doc_mobile_skin` varchar(255) NOT NULL DEFAULT '',
  `doc_hit` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `doc_datetime` datetime DEFAULT NULL,
  `doc_updated_mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `doc_updated_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  UNIQUE KEY `doc_key` (`doc_key`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `editor_image`
--

DROP TABLE IF EXISTS `editor_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `editor_image` (
  `eim_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `eim_originname` varchar(255) NOT NULL DEFAULT '',
  `eim_filename` varchar(255) NOT NULL DEFAULT '',
  `eim_filesize` int(11) unsigned NOT NULL DEFAULT 0,
  `eim_width` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `eim_height` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `eim_type` varchar(10) NOT NULL DEFAULT '',
  `eim_datetime` datetime DEFAULT NULL,
  `eim_ip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`eim_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faq`
--

DROP TABLE IF EXISTS `faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `faq` (
  `faq_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fgr_id` int(11) unsigned NOT NULL DEFAULT 0,
  `faq_title` text DEFAULT NULL,
  `faq_content` mediumtext DEFAULT NULL,
  `faq_mobile_content` mediumtext DEFAULT NULL,
  `faq_content_html_type` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `faq_order` int(11) NOT NULL DEFAULT 0,
  `faq_datetime` datetime DEFAULT NULL,
  `faq_ip` varchar(50) NOT NULL DEFAULT '',
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`faq_id`),
  KEY `fgr_id` (`fgr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faq_group`
--

DROP TABLE IF EXISTS `faq_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `faq_group` (
  `fgr_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fgr_title` varchar(255) NOT NULL DEFAULT '',
  `fgr_key` varchar(100) NOT NULL DEFAULT '',
  `fgr_layout` varchar(255) NOT NULL DEFAULT '',
  `fgr_mobile_layout` varchar(255) NOT NULL DEFAULT '',
  `fgr_sidebar` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `fgr_mobile_sidebar` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `fgr_skin` varchar(255) NOT NULL DEFAULT '',
  `fgr_mobile_skin` varchar(255) NOT NULL DEFAULT '',
  `fgr_datetime` datetime DEFAULT NULL,
  `fgr_ip` varchar(50) NOT NULL DEFAULT '',
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`fgr_id`),
  UNIQUE KEY `fgr_key` (`fgr_key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `follow`
--

DROP TABLE IF EXISTS `follow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `follow` (
  `fol_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `target_mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `fol_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`fol_id`),
  KEY `mem_id` (`mem_id`),
  KEY `target_mem_id` (`target_mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `like`
--

DROP TABLE IF EXISTS `like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `like` (
  `lik_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `target_id` int(11) unsigned NOT NULL DEFAULT 0,
  `target_type` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `target_mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `lik_type` tinyint(4) unsigned NOT NULL,
  `lik_datetime` datetime DEFAULT NULL,
  `lik_ip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`lik_id`),
  KEY `target_id` (`target_id`),
  KEY `mem_id` (`mem_id`),
  KEY `target_mem_id` (`target_mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member` (
  `mem_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_userid` varchar(100) NOT NULL DEFAULT '',
  `mem_email` varchar(100) NOT NULL DEFAULT '',
  `mem_password` varchar(255) NOT NULL DEFAULT '',
  `mem_username` varchar(100) NOT NULL DEFAULT '',
  `mem_nickname` varchar(100) NOT NULL DEFAULT '',
  `mem_level` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `mem_point` int(11) NOT NULL DEFAULT 0,
  `mem_homepage` text DEFAULT NULL,
  `mem_phone` varchar(255) NOT NULL DEFAULT '',
  `mem_birthday` char(10) NOT NULL DEFAULT '',
  `mem_sex` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_zipcode` varchar(7) NOT NULL DEFAULT '',
  `mem_address1` varchar(255) NOT NULL DEFAULT '',
  `mem_address2` varchar(255) NOT NULL DEFAULT '',
  `mem_address3` varchar(255) NOT NULL DEFAULT '',
  `mem_address4` varchar(255) NOT NULL DEFAULT '',
  `mem_receive_email` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_use_note` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_receive_sms` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_open_profile` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_denied` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_email_cert` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_register_datetime` datetime NOT NULL DEFAULT sysdate(),
  `mem_register_ip` varchar(50) NOT NULL DEFAULT '',
  `mem_lastlogin_datetime` datetime DEFAULT NULL,
  `mem_lastlogin_ip` varchar(50) NOT NULL DEFAULT '',
  `mem_is_admin` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_is_setupable` int(11) NOT NULL DEFAULT 0,
  `mem_is_developer` int(11) DEFAULT 0,
  `mem_is_superadmin` int(11) DEFAULT NULL,
  `mem_profile_content` text DEFAULT NULL,
  `mem_adminmemo` text DEFAULT NULL,
  `mem_following` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_followed` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_icon` varchar(255) NOT NULL DEFAULT '',
  `mem_photo` varchar(255) NOT NULL DEFAULT '',
  `mem_updated_by` int(11) DEFAULT NULL,
  `mem_updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`mem_id`),
  UNIQUE KEY `mem_userid` (`mem_userid`),
  KEY `mem_email` (`mem_email`),
  KEY `mem_lastlogin_datetime` (`mem_lastlogin_datetime`),
  KEY `mem_register_datetime` (`mem_register_datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_auth_email`
--

DROP TABLE IF EXISTS `member_auth_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_auth_email` (
  `mae_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mae_key` varchar(100) NOT NULL DEFAULT '',
  `mae_type` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mae_generate_datetime` datetime DEFAULT NULL,
  `mae_use_datetime` datetime DEFAULT NULL,
  `mae_expired` tinyint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`mae_id`),
  KEY `mae_key_mem_id` (`mae_key`,`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_certify`
--

DROP TABLE IF EXISTS `member_certify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_certify` (
  `mce_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mce_type` varchar(50) NOT NULL DEFAULT '',
  `mce_datetime` datetime DEFAULT NULL,
  `mce_ip` varchar(50) NOT NULL DEFAULT '',
  `mce_content` text DEFAULT NULL,
  PRIMARY KEY (`mce_id`),
  KEY `mem_id` (`mem_id`),
  KEY `mce_type` (`mce_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_dormant`
--

DROP TABLE IF EXISTS `member_dormant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_dormant` (
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_userid` varchar(100) NOT NULL DEFAULT '',
  `mem_email` varchar(100) NOT NULL DEFAULT '',
  `mem_password` varchar(255) NOT NULL DEFAULT '',
  `mem_username` varchar(100) NOT NULL DEFAULT '',
  `mem_nickname` varchar(100) NOT NULL DEFAULT '',
  `mem_level` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `mem_point` int(11) NOT NULL DEFAULT 0,
  `mem_homepage` text DEFAULT NULL,
  `mem_phone` varchar(255) NOT NULL DEFAULT '',
  `mem_birthday` char(10) NOT NULL DEFAULT '',
  `mem_sex` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_zipcode` varchar(7) NOT NULL DEFAULT '',
  `mem_address1` varchar(255) NOT NULL DEFAULT '',
  `mem_address2` varchar(255) NOT NULL DEFAULT '',
  `mem_address3` varchar(255) NOT NULL DEFAULT '',
  `mem_address4` varchar(255) NOT NULL DEFAULT '',
  `mem_receive_email` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_use_note` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_receive_sms` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_open_profile` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_denied` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_email_cert` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_register_datetime` datetime DEFAULT NULL,
  `mem_register_ip` varchar(50) NOT NULL DEFAULT '',
  `mem_lastlogin_datetime` datetime DEFAULT NULL,
  `mem_lastlogin_ip` varchar(50) NOT NULL DEFAULT '',
  `mem_is_admin` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_profile_content` text DEFAULT NULL,
  `mem_adminmemo` text DEFAULT NULL,
  `mem_following` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_followed` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_icon` varchar(255) NOT NULL DEFAULT '',
  `mem_photo` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `mem_userid` (`mem_userid`),
  KEY `mem_id` (`mem_id`),
  KEY `mem_email` (`mem_email`),
  KEY `mem_lastlogin_datetime` (`mem_lastlogin_datetime`),
  KEY `mem_register_datetime` (`mem_register_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_dormant_notify`
--

DROP TABLE IF EXISTS `member_dormant_notify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_dormant_notify` (
  `mdn_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_userid` varchar(100) NOT NULL DEFAULT '',
  `mem_email` varchar(100) NOT NULL DEFAULT '',
  `mem_username` varchar(100) NOT NULL DEFAULT '',
  `mem_nickname` varchar(100) NOT NULL DEFAULT '',
  `mem_register_datetime` datetime DEFAULT NULL,
  `mem_lastlogin_datetime` datetime DEFAULT NULL,
  `mdn_dormant_datetime` datetime DEFAULT NULL,
  `mdn_dormant_notify_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`mdn_id`),
  KEY `mem_id` (`mem_id`),
  KEY `mem_email` (`mem_email`),
  KEY `mem_register_datetime` (`mem_register_datetime`),
  KEY `mem_lastlogin_datetime` (`mem_lastlogin_datetime`),
  KEY `mdn_dormant_datetime` (`mdn_dormant_datetime`),
  KEY `mdn_dormant_notify_datetime` (`mdn_dormant_notify_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_extra_vars`
--

DROP TABLE IF EXISTS `member_extra_vars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_extra_vars` (
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mev_key` varchar(100) NOT NULL DEFAULT '',
  `mev_value` text DEFAULT NULL,
  UNIQUE KEY `mem_id_mev_key` (`mem_id`,`mev_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_group`
--

DROP TABLE IF EXISTS `member_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_group` (
  `mgr_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mgr_title` varchar(255) NOT NULL DEFAULT '',
  `mgr_is_default` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mgr_datetime` datetime DEFAULT NULL,
  `mgr_order` int(11) NOT NULL DEFAULT 0,
  `mgr_description` text DEFAULT NULL,
  PRIMARY KEY (`mgr_id`),
  KEY `mgr_order` (`mgr_order`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_group_member`
--

DROP TABLE IF EXISTS `member_group_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_group_member` (
  `mgm_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mgr_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mgm_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`mgm_id`),
  KEY `mgr_id` (`mgr_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2262 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_level_history`
--

DROP TABLE IF EXISTS `member_level_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_level_history` (
  `mlh_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mlh_from` int(11) unsigned NOT NULL DEFAULT 0,
  `mlh_to` int(11) unsigned NOT NULL DEFAULT 0,
  `mlh_datetime` datetime DEFAULT NULL,
  `mlh_reason` varchar(255) NOT NULL DEFAULT '',
  `mlh_ip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`mlh_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_login_log`
--

DROP TABLE IF EXISTS `member_login_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_login_log` (
  `mll_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mll_success` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mll_userid` varchar(255) NOT NULL DEFAULT '',
  `mll_datetime` datetime DEFAULT NULL,
  `mll_ip` varchar(50) NOT NULL DEFAULT '',
  `mll_reason` varchar(255) NOT NULL DEFAULT '',
  `mll_fail_reason` varchar(255) DEFAULT NULL,
  `mll_useragent` varchar(255) NOT NULL DEFAULT '',
  `mll_url` text DEFAULT NULL,
  `mll_referer` text DEFAULT NULL,
  `mll_device_type` varchar(50) DEFAULT NULL,
  `mll_os` varchar(100) DEFAULT NULL,
  `mll_browser` varchar(100) DEFAULT NULL,
  `mll_device_fingerprint` varchar(255) DEFAULT NULL,
  `mll_location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`mll_id`),
  KEY `mem_id` (`mem_id`),
  KEY `mll_success` (`mll_success`),
  KEY `mll_datetime` (`mll_datetime`),
  KEY `mll_userid` (`mll_userid`)
) ENGINE=InnoDB AUTO_INCREMENT=140 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_meta`
--

DROP TABLE IF EXISTS `member_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_meta` (
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mmt_key` varchar(100) NOT NULL DEFAULT '',
  `mmt_value` text DEFAULT NULL,
  UNIQUE KEY `mem_id_mmt_key` (`mem_id`,`mmt_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_nickname`
--

DROP TABLE IF EXISTS `member_nickname`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_nickname` (
  `mni_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mni_nickname` varchar(100) NOT NULL DEFAULT '',
  `mni_start_datetime` datetime DEFAULT sysdate(),
  `mni_end_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`mni_id`),
  KEY `mem_id` (`mem_id`),
  KEY `mni_nickname` (`mni_nickname`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_register`
--

DROP TABLE IF EXISTS `member_register`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_register` (
  `mrg_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mrg_ip` varchar(50) NOT NULL DEFAULT '',
  `mrg_datetime` datetime DEFAULT NULL,
  `mrg_recommend_mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mrg_useragent` varchar(255) NOT NULL DEFAULT '',
  `mrg_referer` text DEFAULT NULL,
  PRIMARY KEY (`mrg_id`),
  UNIQUE KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_selfcert_history`
--

DROP TABLE IF EXISTS `member_selfcert_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_selfcert_history` (
  `msh_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `msh_company` varchar(255) NOT NULL DEFAULT '',
  `msh_certtype` varchar(255) NOT NULL DEFAULT '',
  `msh_cert_key` varchar(255) NOT NULL DEFAULT '',
  `msh_datetime` datetime DEFAULT NULL,
  `msh_ip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`msh_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_userid`
--

DROP TABLE IF EXISTS `member_userid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_userid` (
  `mem_id` int(11) unsigned NOT NULL,
  `mem_userid` varchar(100) NOT NULL DEFAULT '',
  `mem_status` int(11) unsigned NOT NULL DEFAULT 0,
  UNIQUE KEY `mem_userid` (`mem_userid`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `men_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `men_parent` int(11) unsigned NOT NULL DEFAULT 0,
  `men_name` varchar(255) NOT NULL DEFAULT '',
  `men_link` text DEFAULT NULL,
  `men_target` varchar(255) NOT NULL DEFAULT '',
  `men_desktop` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `men_mobile` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `men_custom` varchar(255) NOT NULL DEFAULT '',
  `men_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`men_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu_categories`
--

DROP TABLE IF EXISTS `menu_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_categories` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  `type` varchar(100) NOT NULL DEFAULT 'admin',
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`no`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menus` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `route` varchar(255) DEFAULT '',
  `icon` varchar(255) DEFAULT 'settings',
  `parent_id` int(11) DEFAULT 0,
  `order` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT sysdate(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_show` int(11) NOT NULL DEFAULT 1 COMMENT 'hide ',
  `dt_mode` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`no`),
  KEY `idx_category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `version` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `note`
--

DROP TABLE IF EXISTS `note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `note` (
  `nte_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `send_mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `recv_mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `nte_type` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `related_note_id` int(11) unsigned NOT NULL DEFAULT 0,
  `nte_title` varchar(255) NOT NULL DEFAULT '',
  `nte_content` text DEFAULT NULL,
  `nte_content_html_type` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `nte_datetime` datetime DEFAULT NULL,
  `nte_read_datetime` datetime DEFAULT NULL,
  `nte_originname` varchar(255) NOT NULL DEFAULT '',
  `nte_filename` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`nte_id`),
  KEY `send_mem_id` (`send_mem_id`),
  KEY `recv_mem_id` (`recv_mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification` (
  `not_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `target_mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `not_type` varchar(255) NOT NULL DEFAULT '',
  `not_content_id` int(11) unsigned NOT NULL DEFAULT 0,
  `not_message` varchar(255) NOT NULL DEFAULT '',
  `not_url` varchar(255) NOT NULL DEFAULT '',
  `not_datetime` datetime DEFAULT NULL,
  `not_read_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`not_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_inicis_log`
--

DROP TABLE IF EXISTS `payment_inicis_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_inicis_log` (
  `pil_id` bigint(11) unsigned NOT NULL,
  `pil_type` varchar(255) NOT NULL DEFAULT '',
  `P_TID` varchar(255) NOT NULL DEFAULT '',
  `P_MID` varchar(255) NOT NULL DEFAULT '',
  `P_AUTH_DT` varchar(255) NOT NULL DEFAULT '',
  `P_STATUS` varchar(255) NOT NULL DEFAULT '',
  `P_TYPE` varchar(255) NOT NULL DEFAULT '',
  `P_OID` varchar(255) NOT NULL DEFAULT '',
  `P_FN_NM` varchar(255) NOT NULL DEFAULT '',
  `P_AMT` int(11) unsigned NOT NULL DEFAULT 0,
  `P_AUTH_NO` varchar(255) NOT NULL DEFAULT '',
  `P_RMESG1` varchar(255) NOT NULL DEFAULT '',
  KEY `pil_id` (`pil_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_order_data`
--

DROP TABLE IF EXISTS `payment_order_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_order_data` (
  `pod_id` bigint(11) unsigned NOT NULL,
  `pod_pg` varchar(255) NOT NULL DEFAULT '',
  `pod_type` varchar(255) NOT NULL DEFAULT '',
  `pod_data` text DEFAULT NULL,
  `pod_datetime` datetime DEFAULT NULL,
  `pod_ip` varchar(50) NOT NULL DEFAULT '',
  `mem_id` int(11) NOT NULL DEFAULT 0,
  `cart_id` varchar(255) NOT NULL DEFAULT '0',
  KEY `pod_id` (`pod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `point`
--

DROP TABLE IF EXISTS `point`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `point` (
  `poi_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `poi_datetime` datetime DEFAULT NULL,
  `poi_content` varchar(255) NOT NULL DEFAULT '',
  `poi_point` int(11) NOT NULL DEFAULT 0,
  `poi_type` varchar(20) NOT NULL DEFAULT '',
  `poi_related_id` varchar(20) NOT NULL DEFAULT '',
  `poi_action` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`poi_id`),
  KEY `mem_id_poi_type_poi_related_id_poi_action` (`mem_id`,`poi_type`,`poi_related_id`,`poi_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `popup`
--

DROP TABLE IF EXISTS `popup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `popup` (
  `pop_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pop_start_date` date DEFAULT NULL,
  `pop_end_date` date DEFAULT NULL,
  `pop_is_center` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `pop_left` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `pop_top` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `pop_width` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `pop_height` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `pop_device` varchar(10) NOT NULL DEFAULT '',
  `pop_title` varchar(255) NOT NULL DEFAULT '',
  `pop_content` text DEFAULT NULL,
  `pop_content_html_type` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `pop_disable_hours` int(11) unsigned NOT NULL DEFAULT 0,
  `pop_activated` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `pop_page` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `pop_datetime` datetime DEFAULT NULL,
  `pop_ip` varchar(50) NOT NULL DEFAULT '',
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`pop_id`),
  KEY `pop_start_date` (`pop_start_date`),
  KEY `pop_end_date` (`pop_end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post`
--

DROP TABLE IF EXISTS `post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post` (
  `post_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_num` int(11) NOT NULL DEFAULT 0,
  `post_sex` int(11) DEFAULT NULL,
  `post_age` int(11) DEFAULT NULL,
  `post_travel_year` varchar(100) DEFAULT NULL,
  `post_travel_team` varchar(100) DEFAULT NULL,
  `post_travel_month` varchar(10) DEFAULT NULL,
  `post_travel_chasu` varchar(10) DEFAULT NULL,
  `post_reply` varchar(10) NOT NULL DEFAULT '',
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `post_title` varchar(255) NOT NULL DEFAULT '',
  `post_content` longtext DEFAULT NULL,
  `post_category` varchar(255) NOT NULL DEFAULT '',
  `mem_id` int(11) NOT NULL DEFAULT 0,
  `post_userid` varchar(100) NOT NULL DEFAULT '',
  `post_username` varchar(100) NOT NULL DEFAULT '',
  `post_nickname` varchar(100) NOT NULL DEFAULT '',
  `post_email` varchar(255) NOT NULL DEFAULT '',
  `post_homepage` text DEFAULT NULL,
  `post_datetime` datetime NOT NULL DEFAULT sysdate(),
  `post_password` varchar(255) NOT NULL DEFAULT '',
  `post_updated_datetime` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `post_update_mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `post_comment_count` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `post_comment_updated_datetime` datetime DEFAULT NULL,
  `post_link_count` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `post_secret` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `post_html` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `post_hide_comment` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `post_notice` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `post_recommand` int(11) NOT NULL DEFAULT 0,
  `post_receive_email` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `post_hit` int(11) unsigned NOT NULL DEFAULT 0,
  `post_hash` varchar(1000) DEFAULT NULL,
  `post_like` int(11) unsigned NOT NULL DEFAULT 0,
  `post_dislike` int(11) unsigned NOT NULL DEFAULT 0,
  `post_ip` varchar(50) NOT NULL DEFAULT '',
  `post_blame` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `post_device` varchar(10) NOT NULL DEFAULT '',
  `post_file` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `post_image` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `post_del` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `useing` int(11) NOT NULL DEFAULT 1,
  `is_temp` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`post_id`),
  UNIQUE KEY `post__index_contents` (`post_id`),
  KEY `brd_id` (`brd_id`),
  KEY `idx_partial_text_column` (`post_content`(100)),
  KEY `post__index_1` (`post_num`),
  KEY `post__index_2` (`post_content`(768)),
  KEY `post_comment_updated_datetime` (`post_comment_updated_datetime`),
  KEY `post_datetime` (`post_datetime`),
  KEY `post_mem_id_index` (`mem_id`),
  KEY `post_num_post_reply` (`post_num`,`post_reply`),
  KEY `post_updated_datetime` (`post_updated_datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_extra_vars`
--

DROP TABLE IF EXISTS `post_extra_vars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_extra_vars` (
  `post_id` int(11) unsigned NOT NULL DEFAULT 0,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `pev_key` varchar(100) NOT NULL DEFAULT '',
  `pev_value` text DEFAULT NULL,
  UNIQUE KEY `post_id_pev_key` (`post_id`,`pev_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_file`
--

DROP TABLE IF EXISTS `post_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_file` (
  `pfi_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL DEFAULT 0,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `pfi_originname` varchar(255) NOT NULL DEFAULT '',
  `pfi_filename` varchar(255) NOT NULL DEFAULT '',
  `pfi_download` int(11) unsigned NOT NULL DEFAULT 0,
  `pfi_filesize` int(11) unsigned NOT NULL DEFAULT 0,
  `pfi_width` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `pfi_height` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `pfi_type` varchar(10) NOT NULL DEFAULT '',
  `pfi_is_image` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `pfi_datetime` datetime DEFAULT NULL,
  `pfi_ip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`pfi_id`),
  KEY `post_id` (`post_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_file_download_log`
--

DROP TABLE IF EXISTS `post_file_download_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_file_download_log` (
  `pfd_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pfi_id` int(11) unsigned NOT NULL DEFAULT 0,
  `post_id` int(11) unsigned NOT NULL DEFAULT 0,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `pfd_datetime` datetime DEFAULT NULL,
  `pfd_ip` varchar(50) NOT NULL DEFAULT '',
  `pfd_useragent` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`pfd_id`),
  KEY `pfi_id` (`pfi_id`),
  KEY `post_id` (`post_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_history`
--

DROP TABLE IF EXISTS `post_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_history` (
  `phi_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL DEFAULT 0,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `phi_title` varchar(255) NOT NULL DEFAULT '',
  `phi_content` mediumtext DEFAULT NULL,
  `phi_content_html_type` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `phi_ip` varchar(50) NOT NULL DEFAULT '',
  `phi_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`phi_id`),
  KEY `post_id` (`post_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_link`
--

DROP TABLE IF EXISTS `post_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_link` (
  `pln_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL DEFAULT 0,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `pln_url` text DEFAULT NULL,
  `pln_hit` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`pln_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_link_click_log`
--

DROP TABLE IF EXISTS `post_link_click_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_link_click_log` (
  `plc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pln_id` int(11) unsigned NOT NULL DEFAULT 0,
  `post_id` int(11) unsigned NOT NULL DEFAULT 0,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `plc_datetime` datetime DEFAULT NULL,
  `plc_ip` varchar(50) NOT NULL DEFAULT '',
  `plc_useragent` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`plc_id`),
  KEY `pln_id` (`pln_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_meta`
--

DROP TABLE IF EXISTS `post_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_meta` (
  `post_id` int(11) unsigned NOT NULL DEFAULT 0,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `pmt_key` varchar(100) NOT NULL DEFAULT '',
  `pmt_value` text DEFAULT NULL,
  UNIQUE KEY `post_id_pmt_key` (`post_id`,`pmt_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_naver_syndi_log`
--

DROP TABLE IF EXISTS `post_naver_syndi_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_naver_syndi_log` (
  `pns_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `pns_status` varchar(255) NOT NULL DEFAULT '',
  `pns_return_code` varchar(255) NOT NULL DEFAULT '',
  `pns_return_message` varchar(255) NOT NULL DEFAULT '',
  `pns_receipt_number` varchar(255) NOT NULL DEFAULT '',
  `pns_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`pns_id`),
  KEY `post_id` (`post_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_poll`
--

DROP TABLE IF EXISTS `post_poll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_poll` (
  `ppo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL DEFAULT 0,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ppo_start_datetime` datetime DEFAULT NULL,
  `ppo_end_datetime` datetime DEFAULT NULL,
  `ppo_title` varchar(255) NOT NULL DEFAULT '',
  `ppo_count` int(11) unsigned NOT NULL DEFAULT 0,
  `ppo_choose_count` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `ppo_after_comment` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `ppo_point` int(11) NOT NULL DEFAULT 0,
  `ppo_datetime` datetime DEFAULT NULL,
  `ppo_ip` varchar(50) NOT NULL DEFAULT '',
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`ppo_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_poll_item`
--

DROP TABLE IF EXISTS `post_poll_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_poll_item` (
  `ppi_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ppo_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ppi_item` varchar(255) NOT NULL DEFAULT '',
  `ppi_count` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`ppi_id`),
  KEY `ppo_id` (`ppo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_poll_item_poll`
--

DROP TABLE IF EXISTS `post_poll_item_poll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_poll_item_poll` (
  `ppp_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ppo_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ppi_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ppp_datetime` datetime DEFAULT NULL,
  `ppp_ip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`ppp_id`),
  KEY `ppo_id` (`ppo_id`),
  KEY `ppi_id` (`ppi_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_tag`
--

DROP TABLE IF EXISTS `post_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_tag` (
  `pta_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL DEFAULT 0,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `pta_tag` varchar(100) NOT NULL DEFAULT '',
  `is_show_to_search` int(11) DEFAULT 1,
  `insert_id` int(11) DEFAULT NULL,
  `update_id` int(11) DEFAULT NULL,
  `update_date` datetime DEFAULT current_timestamp(),
  `insert_date` datetime DEFAULT sysdate(),
  PRIMARY KEY (`pta_id`),
  KEY `post_id` (`post_id`),
  KEY `pta_tag` (`pta_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrap`
--

DROP TABLE IF EXISTS `scrap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrap` (
  `scr_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `post_id` int(11) unsigned NOT NULL DEFAULT 0,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `target_mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `scr_datetime` datetime DEFAULT NULL,
  `scr_title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`scr_id`),
  KEY `mem_id` (`mem_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `search_keyword`
--

DROP TABLE IF EXISTS `search_keyword`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `search_keyword` (
  `sek_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sek_keyword` varchar(100) NOT NULL DEFAULT '',
  `sek_datetime` datetime DEFAULT NULL,
  `sek_ip` varchar(50) NOT NULL DEFAULT '',
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`sek_id`),
  KEY `sek_keyword_sek_datetime_sek_ip` (`sek_keyword`,`sek_datetime`,`sek_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `session` (
  `id` varchar(120) NOT NULL DEFAULT '',
  `ip_address` varchar(45) NOT NULL DEFAULT '',
  `timestamp` int(10) NOT NULL DEFAULT 0,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_favorite`
--

DROP TABLE IF EXISTS `sms_favorite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_favorite` (
  `sfa_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sfa_title` varchar(255) NOT NULL DEFAULT '',
  `sfa_content` text DEFAULT NULL,
  `sfa_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`sfa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_member`
--

DROP TABLE IF EXISTS `sms_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_member` (
  `sme_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `smg_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `sme_name` varchar(255) NOT NULL DEFAULT '',
  `sme_phone` varchar(255) NOT NULL DEFAULT '',
  `sme_receive` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `sme_datetime` datetime DEFAULT NULL,
  `sme_memo` text DEFAULT NULL,
  PRIMARY KEY (`sme_id`),
  KEY `smg_id` (`smg_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_member_group`
--

DROP TABLE IF EXISTS `sms_member_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_member_group` (
  `smg_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `smg_name` varchar(255) NOT NULL DEFAULT '',
  `smg_order` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `smg_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`smg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_send_content`
--

DROP TABLE IF EXISTS `sms_send_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_send_content` (
  `ssc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ssc_content` text DEFAULT NULL,
  `send_mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ssc_send_phone` varchar(255) NOT NULL DEFAULT '',
  `ssc_booking` datetime DEFAULT NULL,
  `ssc_total` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `ssc_success` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `ssc_fail` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `ssc_datetime` datetime DEFAULT NULL,
  `ssc_memo` text DEFAULT NULL,
  PRIMARY KEY (`ssc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_send_history`
--

DROP TABLE IF EXISTS `sms_send_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_send_history` (
  `ssh_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ssc_id` int(11) unsigned NOT NULL DEFAULT 0,
  `send_mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `recv_mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ssh_name` varchar(255) NOT NULL DEFAULT '',
  `ssh_phone` varchar(255) NOT NULL DEFAULT '',
  `ssh_success` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `ssh_datetime` datetime DEFAULT NULL,
  `ssh_memo` text DEFAULT NULL,
  `ssh_log` text DEFAULT NULL,
  PRIMARY KEY (`ssh_id`),
  KEY `ssc_id` (`ssc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `social`
--

DROP TABLE IF EXISTS `social`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `social` (
  `soc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `soc_type` varchar(255) NOT NULL DEFAULT '',
  `soc_account_id` varchar(100) NOT NULL DEFAULT '',
  `soc_key` varchar(100) NOT NULL DEFAULT '',
  `soc_value` text DEFAULT NULL,
  PRIMARY KEY (`soc_id`),
  KEY `soc_account_id` (`soc_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `social_meta`
--

DROP TABLE IF EXISTS `social_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_meta` (
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `smt_key` varchar(100) NOT NULL DEFAULT '',
  `smt_value` varchar(100) NOT NULL DEFAULT '',
  UNIQUE KEY `mem_id_smt_key` (`mem_id`,`smt_key`),
  KEY `smt_value` (`smt_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stat_count`
--

DROP TABLE IF EXISTS `stat_count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stat_count` (
  `sco_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sco_ip` varchar(50) NOT NULL DEFAULT '',
  `sco_date` date NOT NULL,
  `sco_time` time NOT NULL,
  `sco_referer` text DEFAULT NULL,
  `sco_current` text DEFAULT NULL,
  `sco_agent` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`sco_id`),
  KEY `sco_date` (`sco_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stat_count_board`
--

DROP TABLE IF EXISTS `stat_count_board`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stat_count_board` (
  `scb_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `scb_date` date NOT NULL,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `scb_count` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`scb_id`),
  KEY `scb_date_brd_id` (`scb_date`,`brd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stat_count_date`
--

DROP TABLE IF EXISTS `stat_count_date`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stat_count_date` (
  `scd_date` date NOT NULL,
  `scd_count` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`scd_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tempsave`
--

DROP TABLE IF EXISTS `tempsave`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tempsave` (
  `tmp_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `brd_id` int(11) unsigned NOT NULL DEFAULT 0,
  `tmp_title` varchar(255) NOT NULL DEFAULT '',
  `tmp_content` mediumtext DEFAULT NULL,
  `mem_id` int(11) unsigned NOT NULL DEFAULT 0,
  `tmp_ip` varchar(50) NOT NULL DEFAULT '',
  `tmp_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`tmp_id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `top_menu_categories`
--

DROP TABLE IF EXISTS `top_menu_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `top_menu_categories` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT sysdate(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `type` enum('user','admin') NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`no`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `top_menus`
--

DROP TABLE IF EXISTS `top_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `top_menus` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `route` varchar(255) DEFAULT '',
  `icon` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT sysdate(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `type` enum('user','admin') NOT NULL DEFAULT 'admin',
  `dt_mode` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`no`),
  KEY `idx_top_category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unique_id`
--

DROP TABLE IF EXISTS `unique_id`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `unique_id` (
  `unq_id` bigint(20) unsigned NOT NULL,
  `unq_ip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`unq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `v_board`
--

DROP TABLE IF EXISTS `v_board`;
/*!50001 DROP VIEW IF EXISTS `v_board`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_board` AS SELECT
 1 AS `brd_id`,
  1 AS `bgr_id`,
  1 AS `brd_key`,
  1 AS `brd_type`,
  1 AS `brd_form`,
  1 AS `brd_name`,
  1 AS `brd_mobile_name`,
  1 AS `brd_order`,
  1 AS `brd_search`,
  1 AS `update_date`,
  1 AS `insert_date`,
  1 AS `insert_id`,
  1 AS `update_id`,
  1 AS `useing`,
  1 AS `is_deleted`,
  1 AS `no`,
  1 AS `pkey`,
  1 AS `tags`,
  1 AS `all_tags`,
  1 AS `post_cnt`,
  1 AS `bgr_name`,
  1 AS `insert_member`,
  1 AS `update_member`,
  1 AS `insert_date_ymd`,
  1 AS `update_date_ymd`,
  1 AS `useing_text` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_board_category`
--

DROP TABLE IF EXISTS `v_board_category`;
/*!50001 DROP VIEW IF EXISTS `v_board_category`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_board_category` AS SELECT
 1 AS `no`,
  1 AS `pkey`,
  1 AS `brd_name`,
  1 AS `bca_id`,
  1 AS `brd_id`,
  1 AS `bca_key`,
  1 AS `bca_value`,
  1 AS `bca_parent`,
  1 AS `bca_order`,
  1 AS `insert_date`,
  1 AS `insert_id`,
  1 AS `update_date`,
  1 AS `update_id`,
  1 AS `useing`,
  1 AS `is_deleted`,
  1 AS `insert_member`,
  1 AS `update_member`,
  1 AS `insert_date_ymd`,
  1 AS `update_date_ymd`,
  1 AS `useing_text` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_board_group`
--

DROP TABLE IF EXISTS `v_board_group`;
/*!50001 DROP VIEW IF EXISTS `v_board_group`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_board_group` AS SELECT
 1 AS `bgr_id`,
  1 AS `bgr_key`,
  1 AS `bgr_name`,
  1 AS `bg_count`,
  1 AS `bgr_order`,
  1 AS `update_date`,
  1 AS `insert_date`,
  1 AS `insert_id`,
  1 AS `update_id`,
  1 AS `useing`,
  1 AS `is_deleted`,
  1 AS `no`,
  1 AS `pkey`,
  1 AS `insert_member`,
  1 AS `update_member`,
  1 AS `insert_date_ymd`,
  1 AS `update_date_ymd`,
  1 AS `useing_text` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_post`
--

DROP TABLE IF EXISTS `v_post`;
/*!50001 DROP VIEW IF EXISTS `v_post`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_post` AS SELECT
 1 AS `no`,
  1 AS `pkey`,
  1 AS `post_id`,
  1 AS `post_num`,
  1 AS `post_reply`,
  1 AS `brd_id`,
  1 AS `bgr_id`,
  1 AS `brd_key`,
  1 AS `brd_type`,
  1 AS `brd_name`,
  1 AS `post_title`,
  1 AS `post_content`,
  1 AS `post_content_full`,
  1 AS `post_category`,
  1 AS `mem_id`,
  1 AS `post_userid`,
  1 AS `post_username`,
  1 AS `post_nickname`,
  1 AS `masked_post_nickname`,
  1 AS `post_hash`,
  1 AS `post_sex_text`,
  1 AS `post_sex`,
  1 AS `post_travel_team`,
  1 AS `post_travel_team_name`,
  1 AS `post_travel_year`,
  1 AS `post_travel_month`,
  1 AS `post_travel_chasu`,
  1 AS `post_age`,
  1 AS `post_tags`,
  1 AS `post_email`,
  1 AS `post_homepage`,
  1 AS `post_datetime`,
  1 AS `post_password`,
  1 AS `post_updated_datetime`,
  1 AS `post_update_mem_id`,
  1 AS `post_comment_count`,
  1 AS `post_comment_updated_datetime`,
  1 AS `post_link_count`,
  1 AS `post_secret`,
  1 AS `post_html`,
  1 AS `post_hide_comment`,
  1 AS `post_notice`,
  1 AS `post_receive_email`,
  1 AS `post_comment_cnt`,
  1 AS `post_hit`,
  1 AS `post_like`,
  1 AS `post_dislike`,
  1 AS `post_ip`,
  1 AS `post_blame`,
  1 AS `post_device`,
  1 AS `post_file`,
  1 AS `post_image`,
  1 AS `post_del`,
  1 AS `post_recommand`,
  1 AS `is_temp`,
  1 AS `is_recommend_display_manual`,
  1 AS `useing`,
  1 AS `insert_id`,
  1 AS `insert_member`,
  1 AS `update_id`,
  1 AS `update_member`,
  1 AS `insert_date`,
  1 AS `insert_date_ymd`,
  1 AS `update_date`,
  1 AS `update_date_ymd`,
  1 AS `useing_text`,
  1 AS `is_deleted` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_post_short`
--

DROP TABLE IF EXISTS `v_post_short`;
/*!50001 DROP VIEW IF EXISTS `v_post_short`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_post_short` AS SELECT
 1 AS `no`,
  1 AS `pkey`,
  1 AS `post_id`,
  1 AS `post_num`,
  1 AS `post_reply`,
  1 AS `brd_id`,
  1 AS `bgr_id`,
  1 AS `brd_key`,
  1 AS `brd_type`,
  1 AS `brd_name`,
  1 AS `post_title`,
  1 AS `post_content_short`,
  1 AS `post_category`,
  1 AS `mem_id`,
  1 AS `post_userid`,
  1 AS `post_username`,
  1 AS `post_nickname`,
  1 AS `masked_post_nickname`,
  1 AS `post_hash`,
  1 AS `post_sex_text`,
  1 AS `post_sex`,
  1 AS `post_travel_team`,
  1 AS `post_travel_year`,
  1 AS `post_travel_month`,
  1 AS `post_travel_chasu`,
  1 AS `post_age`,
  1 AS `post_tags`,
  1 AS `post_email`,
  1 AS `post_homepage`,
  1 AS `post_datetime`,
  1 AS `post_date`,
  1 AS `post_password`,
  1 AS `post_updated_datetime`,
  1 AS `post_update_mem_id`,
  1 AS `post_comment_count`,
  1 AS `post_comment_updated_datetime`,
  1 AS `post_link_count`,
  1 AS `post_secret`,
  1 AS `post_html`,
  1 AS `post_hide_comment`,
  1 AS `post_notice`,
  1 AS `post_receive_email`,
  1 AS `post_comment_cnt`,
  1 AS `post_hit`,
  1 AS `post_like`,
  1 AS `post_dislike`,
  1 AS `post_ip`,
  1 AS `post_blame`,
  1 AS `post_device`,
  1 AS `post_file`,
  1 AS `post_image`,
  1 AS `post_del`,
  1 AS `post_recommand`,
  1 AS `is_temp`,
  1 AS `is_recommend_display_manual`,
  1 AS `useing`,
  1 AS `insert_id`,
  1 AS `insert_member`,
  1 AS `update_id`,
  1 AS `update_member`,
  1 AS `insert_date`,
  1 AS `insert_date_ymd`,
  1 AS `update_date`,
  1 AS `update_date_ymd`,
  1 AS `useing_text`,
  1 AS `is_deleted` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_post_tag`
--

DROP TABLE IF EXISTS `v_post_tag`;
/*!50001 DROP VIEW IF EXISTS `v_post_tag`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_post_tag` AS SELECT
 1 AS `pta_id`,
  1 AS `post_id`,
  1 AS `brd_id`,
  1 AS `pta_tag`,
  1 AS `post_title`,
  1 AS `is_show_to_search`,
  1 AS `no`,
  1 AS `pkey` */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_board`
--

/*!50001 DROP VIEW IF EXISTS `v_board`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 SQL SECURITY DEFINER */
/*!50001 VIEW `v_board` AS select `bd`.`brd_id` AS `brd_id`,`bd`.`bgr_id` AS `bgr_id`,`bd`.`brd_key` AS `brd_key`,`bd`.`brd_type` AS `brd_type`,`bd`.`brd_form` AS `brd_form`,`bd`.`brd_name` AS `brd_name`,`bd`.`brd_mobile_name` AS `brd_mobile_name`,`bd`.`brd_order` AS `brd_order`,`bd`.`brd_search` AS `brd_search`,`bd`.`update_date` AS `update_date`,`bd`.`insert_date` AS `insert_date`,`bd`.`insert_id` AS `insert_id`,`bd`.`update_id` AS `update_id`,`bd`.`useing` AS `useing`,`bd`.`is_deleted` AS `is_deleted`,`bd`.`brd_id` AS `no`,`bd`.`brd_id` AS `pkey`,`FN_GET_ALL_TAGS_FOR_BOARD`(`bd`.`brd_id`,'1') AS `tags`,`FN_GET_ALL_TAGS_FOR_BOARD`(`bd`.`brd_id`,'all') AS `all_tags`,`FN_GET_POST_CNT`(`bd`.`brd_id`,'brd_id') AS `post_cnt`,`FN_get_bordgroup_name`(`bd`.`bgr_id`) AS `bgr_name`,`FN_get_memname`(`bd`.`insert_id`) AS `insert_member`,`FN_get_memname`(`bd`.`update_id`) AS `update_member`,date_format(`bd`.`insert_date`,'%Y-%m-%d') AS `insert_date_ymd`,date_format(`bd`.`update_date`,'%Y-%m-%d') AS `update_date_ymd`,if(`bd`.`useing` = 1,'사용','미사용') AS `useing_text` from `kelly`.`board` `bd` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_board_category`
--

/*!50001 DROP VIEW IF EXISTS `v_board_category`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 SQL SECURITY DEFINER */
/*!50001 VIEW `v_board_category` AS select `bc`.`bca_id` AS `no`,`bc`.`bca_id` AS `pkey`,`FN_get_bord_name`(`bc`.`brd_id`) AS `brd_name`,`bc`.`bca_id` AS `bca_id`,`bc`.`brd_id` AS `brd_id`,`bc`.`bca_key` AS `bca_key`,`bc`.`bca_value` AS `bca_value`,`bc`.`bca_parent` AS `bca_parent`,`bc`.`bca_order` AS `bca_order`,`bc`.`insert_date` AS `insert_date`,`bc`.`insert_id` AS `insert_id`,`bc`.`update_date` AS `update_date`,`bc`.`update_id` AS `update_id`,`bc`.`useing` AS `useing`,`bc`.`is_deleted` AS `is_deleted`,`FN_get_memname`(`bc`.`insert_id`) AS `insert_member`,`FN_get_memname`(`bc`.`update_id`) AS `update_member`,date_format(`bc`.`insert_date`,'%Y-%m-%d') AS `insert_date_ymd`,date_format(`bc`.`update_date`,'%Y-%m-%d') AS `update_date_ymd`,if(`bc`.`useing` = 1,'사용','미사용') AS `useing_text` from `board_category` `bc` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_board_group`
--

/*!50001 DROP VIEW IF EXISTS `v_board_group`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 SQL SECURITY DEFINER */
/*!50001 VIEW `v_board_group` AS select `bg`.`bgr_id` AS `bgr_id`,`bg`.`bgr_key` AS `bgr_key`,`bg`.`bgr_name` AS `bgr_name`,`FN_GET_BOARD_COUNT_BY_GROUP`(`bg`.`bgr_id`) AS `bg_count`,`bg`.`bgr_order` AS `bgr_order`,`bg`.`update_date` AS `update_date`,`bg`.`insert_date` AS `insert_date`,`bg`.`insert_id` AS `insert_id`,`bg`.`update_id` AS `update_id`,`bg`.`useing` AS `useing`,`bg`.`is_deleted` AS `is_deleted`,`bg`.`bgr_id` AS `no`,`bg`.`bgr_id` AS `pkey`,`FN_get_memname`(`bg`.`insert_id`) AS `insert_member`,`FN_get_memname`(`bg`.`update_id`) AS `update_member`,date_format(`bg`.`insert_date`,'%Y-%m-%d') AS `insert_date_ymd`,date_format(`bg`.`update_date`,'%Y-%m-%d') AS `update_date_ymd`,if(`bg`.`useing` = 1,'사용','미사용') AS `useing_text` from `kelly`.`board_group` `bg` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_post`
--

/*!50001 DROP VIEW IF EXISTS `v_post`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 SQL SECURITY DEFINER */
/*!50001 VIEW `v_post` AS select `p`.`post_id` AS `no`,`p`.`post_id` AS `pkey`,`p`.`post_id` AS `post_id`,`p`.`post_num` AS `post_num`,`p`.`post_reply` AS `post_reply`,`p`.`brd_id` AS `brd_id`,`FN_get_bordgroup_id`(`p`.`brd_id`) AS `bgr_id`,`FN_get_bord_key`(`p`.`brd_id`) AS `brd_key`,`FN_get_bord_type`(`p`.`brd_id`) AS `brd_type`,`FN_get_bord_name`(`p`.`brd_id`) AS `brd_name`,`p`.`post_title` AS `post_title`,left(`p`.`post_content`,133333) AS `post_content`,`p`.`post_content` AS `post_content_full`,`p`.`post_category` AS `post_category`,`p`.`mem_id` AS `mem_id`,`p`.`post_userid` AS `post_userid`,`p`.`post_username` AS `post_username`,`p`.`post_nickname` AS `post_nickname`,case when char_length(`p`.`post_nickname`) > 2 then concat(substr(`p`.`post_nickname`,1,1),repeat('*',char_length(`p`.`post_nickname`) - 2),substr(`p`.`post_nickname`,char_length(`p`.`post_nickname`),1)) when char_length(`p`.`post_nickname`) = 2 then concat(substr(`p`.`post_nickname`,1,1),'*') else `p`.`post_nickname` end AS `masked_post_nickname`,`p`.`post_hash` AS `post_hash`,case when `p`.`post_sex` = 0 then '남' when `p`.`post_sex` = 1 then '여' else '' end AS `post_sex_text`,`p`.`post_sex` AS `post_sex`,`p`.`post_travel_team` AS `post_travel_team`,if(`p`.`post_travel_chasu` <> '' and `p`.`post_travel_team` <> '' and `p`.`post_travel_month` <> '',concat(`p`.`post_travel_team`,' ',`p`.`post_travel_month`,'월 ',`p`.`post_travel_chasu`,'차'),'') AS `post_travel_team_name`,`p`.`post_travel_year` AS `post_travel_year`,`p`.`post_travel_month` AS `post_travel_month`,`p`.`post_travel_chasu` AS `post_travel_chasu`,`p`.`post_age` AS `post_age`,`FN_GET_TAGS_FOR_POST`(`p`.`post_id`) AS `post_tags`,`p`.`post_email` AS `post_email`,`p`.`post_homepage` AS `post_homepage`,`p`.`post_datetime` AS `post_datetime`,`p`.`post_password` AS `post_password`,`p`.`post_updated_datetime` AS `post_updated_datetime`,`p`.`post_update_mem_id` AS `post_update_mem_id`,`p`.`post_comment_count` AS `post_comment_count`,`p`.`post_comment_updated_datetime` AS `post_comment_updated_datetime`,`p`.`post_link_count` AS `post_link_count`,`p`.`post_secret` AS `post_secret`,`p`.`post_html` AS `post_html`,`p`.`post_hide_comment` AS `post_hide_comment`,`p`.`post_notice` AS `post_notice`,`p`.`post_receive_email` AS `post_receive_email`,`FN_GET_COMMENT_CNT`(`p`.`post_id`) AS `post_comment_cnt`,`p`.`post_hit` AS `post_hit`,`p`.`post_like` AS `post_like`,`p`.`post_dislike` AS `post_dislike`,`p`.`post_ip` AS `post_ip`,`p`.`post_blame` AS `post_blame`,`p`.`post_device` AS `post_device`,`p`.`post_file` AS `post_file`,`p`.`post_image` AS `post_image`,`p`.`post_del` AS `post_del`,`p`.`post_recommand` AS `post_recommand`,`p`.`is_temp` AS `is_temp`,`FN_IS_RECOMMEND_DISPLAY_MANUAL`() AS `is_recommend_display_manual`,`p`.`useing` AS `useing`,`p`.`mem_id` AS `insert_id`,`FN_get_memname`(`p`.`mem_id`) AS `insert_member`,`p`.`post_update_mem_id` AS `update_id`,`FN_get_memname`(`p`.`post_update_mem_id`) AS `update_member`,`p`.`post_datetime` AS `insert_date`,date_format(`p`.`post_datetime`,'%Y-%m-%d') AS `insert_date_ymd`,`p`.`post_updated_datetime` AS `update_date`,date_format(`p`.`post_updated_datetime`,'%Y-%m-%d') AS `update_date_ymd`,if(`p`.`useing` = 1,'사용','미사용') AS `useing_text`,`p`.`post_del` AS `is_deleted` from `kelly`.`post` `p` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_post_short`
--

/*!50001 DROP VIEW IF EXISTS `v_post_short`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 SQL SECURITY DEFINER */
/*!50001 VIEW `v_post_short` AS select `p`.`post_id` AS `no`,`p`.`post_id` AS `pkey`,`p`.`post_id` AS `post_id`,`p`.`post_num` AS `post_num`,`p`.`post_reply` AS `post_reply`,`p`.`brd_id` AS `brd_id`,`FN_get_bordgroup_id`(`p`.`brd_id`) AS `bgr_id`,`FN_get_bord_key`(`p`.`brd_id`) AS `brd_key`,`FN_get_bord_type`(`p`.`brd_id`) AS `brd_type`,`FN_get_bord_name`(`p`.`brd_id`) AS `brd_name`,`p`.`post_title` AS `post_title`,case when char_length(`p`.`post_content`) > 100 then concat(substr(`p`.`post_content`,1,1000),'...') else `p`.`post_content` end AS `post_content_short`,`p`.`post_category` AS `post_category`,`p`.`mem_id` AS `mem_id`,`p`.`post_userid` AS `post_userid`,`p`.`post_username` AS `post_username`,`p`.`post_nickname` AS `post_nickname`,case when char_length(`p`.`post_nickname`) > 2 then concat(substr(`p`.`post_nickname`,1,1),repeat('*',char_length(`p`.`post_nickname`) - 2),substr(`p`.`post_nickname`,char_length(`p`.`post_nickname`),1)) when char_length(`p`.`post_nickname`) = 2 then concat(substr(`p`.`post_nickname`,1,1),'*') else `p`.`post_nickname` end AS `masked_post_nickname`,`p`.`post_hash` AS `post_hash`,if(`p`.`post_sex` = 0,'남성','여성') AS `post_sex_text`,`p`.`post_sex` AS `post_sex`,`p`.`post_travel_team` AS `post_travel_team`,`p`.`post_travel_year` AS `post_travel_year`,`p`.`post_travel_month` AS `post_travel_month`,`p`.`post_travel_chasu` AS `post_travel_chasu`,`p`.`post_age` AS `post_age`,`FN_GET_TAGS_FOR_POST`(`p`.`post_id`) AS `post_tags`,`p`.`post_email` AS `post_email`,`p`.`post_homepage` AS `post_homepage`,`p`.`post_datetime` AS `post_datetime`,date_format(`p`.`post_datetime`,'%Y-%m-%d') AS `post_date`,`p`.`post_password` AS `post_password`,`p`.`post_updated_datetime` AS `post_updated_datetime`,`p`.`post_update_mem_id` AS `post_update_mem_id`,`p`.`post_comment_count` AS `post_comment_count`,`p`.`post_comment_updated_datetime` AS `post_comment_updated_datetime`,`p`.`post_link_count` AS `post_link_count`,`p`.`post_secret` AS `post_secret`,`p`.`post_html` AS `post_html`,`p`.`post_hide_comment` AS `post_hide_comment`,`p`.`post_notice` AS `post_notice`,`p`.`post_receive_email` AS `post_receive_email`,`FN_GET_COMMENT_CNT`(`p`.`post_id`) AS `post_comment_cnt`,`p`.`post_hit` AS `post_hit`,`p`.`post_like` AS `post_like`,`p`.`post_dislike` AS `post_dislike`,`p`.`post_ip` AS `post_ip`,`p`.`post_blame` AS `post_blame`,`p`.`post_device` AS `post_device`,`p`.`post_file` AS `post_file`,`p`.`post_image` AS `post_image`,`p`.`post_del` AS `post_del`,`p`.`post_recommand` AS `post_recommand`,`p`.`is_temp` AS `is_temp`,`FN_IS_RECOMMEND_DISPLAY_MANUAL`() AS `is_recommend_display_manual`,`p`.`useing` AS `useing`,`p`.`mem_id` AS `insert_id`,`FN_get_memname`(`p`.`mem_id`) AS `insert_member`,`p`.`post_update_mem_id` AS `update_id`,`FN_get_memname`(`p`.`post_update_mem_id`) AS `update_member`,`p`.`post_datetime` AS `insert_date`,date_format(`p`.`post_datetime`,'%Y-%m-%d') AS `insert_date_ymd`,`p`.`post_updated_datetime` AS `update_date`,date_format(`p`.`post_updated_datetime`,'%Y-%m-%d') AS `update_date_ymd`,if(`p`.`useing` = 1,'사용','미사용') AS `useing_text`,`p`.`post_del` AS `is_deleted` from `kelly`.`post` `p` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_post_tag`
--

/*!50001 DROP VIEW IF EXISTS `v_post_tag`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 SQL SECURITY DEFINER */
/*!50001 VIEW `v_post_tag` AS select `pt`.`pta_id` AS `pta_id`,`pt`.`post_id` AS `post_id`,`pt`.`brd_id` AS `brd_id`,`pt`.`pta_tag` AS `pta_tag`,`FN_GET_POST_TITLE`(`pt`.`post_id`) AS `post_title`,`pt`.`is_show_to_search` AS `is_show_to_search`,`pt`.`pta_id` AS `no`,`pt`.`pta_id` AS `pkey` from `herehub`.`post_tag` `pt` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-16 14:00:24
