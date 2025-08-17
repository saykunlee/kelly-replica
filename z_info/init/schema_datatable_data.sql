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
-- Dumping data for table `datatable_settings`
--

INSERT INTO `datatable_settings` VALUES (1,24,'sidemenu','/api/menu-api/get-menu-list','pageLength,excelHtml5,pdfHtml5,print',100,0,1,'2024-08-26 14:02:43',0,'2024-08-30 20:16:05',0,1,0);
INSERT INTO `datatable_settings` VALUES (4,34,'topmenu','/api/menu-api/get-top-menu-list','pageLength,excelHtml5',100,0,1,'2024-08-26 14:02:43',1,'2025-01-03 17:21:24',0,1,0);
INSERT INTO `datatable_settings` VALUES (8,30,'member_list','/api/member-api/get-member-search-list','pageLength,excelHtml5',10,0,1,'2024-09-01 23:20:49',1,'2024-12-19 17:23:10',0,1,1);
INSERT INTO `datatable_settings` VALUES (9,36,'get-login-logs','/api/member-api/get-login-logs','pageLength,excelHtml5,pdfHtml5,print',10,0,1,'2024-09-10 15:32:27',0,'0000-00-00 00:00:00',0,1,0);
INSERT INTO `datatable_settings` VALUES (10,37,'SideCateList','/api/menu-api/get-Sidecate-List','pageLength,excelHtml5',10,NULL,1,'2024-09-24 10:02:59',1,'2024-09-24 12:00:43',0,1,1);
INSERT INTO `datatable_settings` VALUES (11,42,'topcatelist','/api/menu-api/get-Topcate-List','pageLength,print',100,NULL,1,'2025-01-03 16:53:50',NULL,'0000-00-00 00:00:00',0,1,0);
INSERT INTO `datatable_settings` VALUES (12,44,'get-board-group-list','/api/board-api/get-board-group-list','pageLength,excelHtml5',100,NULL,1,'2025-02-03 20:25:03',NULL,'0000-00-00 00:00:00',0,1,0);
INSERT INTO `datatable_settings` VALUES (13,45,'get-board-list','/api/board-api/get-board-list','pageLength,excelHtml5',50,NULL,1,'2025-02-03 22:12:42',1,'2025-02-03 23:40:02',0,1,0);
INSERT INTO `datatable_settings` VALUES (14,46,'get-post-admin-list','/api/board-api/get-post-admin-list','pageLength,excelHtml5',50,NULL,1,'2025-02-06 15:30:22',NULL,'0000-00-00 00:00:00',0,1,0);

--
-- Dumping data for table `datatable_columns`
--

INSERT INTO `datatable_columns` VALUES (1,1,10,'null','','25px','tx-center-f',0,'function(data, type, row, meta) {\r\n    return meta.settings._iDisplayStart + meta.row + 1;\r\n}',0,1,'2024-08-26 14:03:49',0,'2024-08-27 00:54:09',0,1,1);
INSERT INTO `datatable_columns` VALUES (2,1,20,'type','타입','50px','tx-center',0,'function(data, type, row) {\r\n    return data == \"admin\" ? \"관리자\" : \"사용자\";\r\n}',0,1,'2024-08-26 14:03:49',0,'2024-08-27 00:54:09',0,1,1);
INSERT INTO `datatable_columns` VALUES (3,1,30,'category_name','카테고리','80px','tx-left',0,'null',0,1,'2024-08-26 14:03:49',0,'2024-08-27 00:55:03',0,1,1);
INSERT INTO `datatable_columns` VALUES (4,1,40,'parent_title','상위 메뉴','80px','tx-left',0,'null',0,1,'2024-08-26 14:03:49',0,'2024-08-27 00:54:09',0,1,1);
INSERT INTO `datatable_columns` VALUES (5,1,50,'icon','','20px','tx-center-f',0,'function(data, type, row) {\r\n    const iconName = data ? data : \"corner-down-right\";\r\n    return \'<i class=\"tx-color-03\" data-feather=\"\' + iconName + \'\"></i>\';\r\n}',0,1,'2024-08-26 14:03:49',0,'2024-08-27 00:54:09',0,1,1);
INSERT INTO `datatable_columns` VALUES (6,1,60,'title','제목','120px','tx-left',0,'null',0,1,'2024-08-26 14:03:49',0,'2024-08-27 00:54:09',0,1,1);
INSERT INTO `datatable_columns` VALUES (7,1,70,'order','순서','30px','tx-center-f',0,NULL,NULL,1,'2024-08-26 14:03:49',NULL,'2024-08-27 00:54:09',0,1,1);
INSERT INTO `datatable_columns` VALUES (8,1,80,'url','URL','110px','tx-left',0,'function (data, type, row, meta) {\r\n                if (type === \'display\') {\r\n                    // \'data\'는 해당 셀의 값을 나타냅니다.\r\n                    // 이 경우, data는 링크가 됩니다.\r\n                    return \'<a href=\"\' + data + \'\" target=\"_blank\">\' + data + \'</a>\';\r\n                }\r\n                return data;\r\n            }',0,1,'2024-08-26 14:03:49',0,'2024-08-27 00:54:09',0,1,1);
INSERT INTO `datatable_columns` VALUES (9,1,90,'route','Route','300px','tx-left text-wrap-break-all',0,NULL,NULL,1,'2024-08-26 14:03:49',NULL,'2024-08-27 00:54:09',0,1,1);
INSERT INTO `datatable_columns` VALUES (10,1,100,'is_active','상태','40px','tx-center-f',0,'function(data, type, row) {\n    return data == 1 ? \"활성\" : \"비활성\";\n}',NULL,1,'2024-08-26 14:03:49',NULL,'2024-08-27 00:54:09',0,1,1);
INSERT INTO `datatable_columns` VALUES (11,1,110,'null','Actions','50px','tx-center',0,'function(data, type, row) {\r\n    return \'<a style=\"color: #596882;\" href=\"javascript:void(0);\" onclick=\"openModal(\' + row.no + \');\"><i data-feather=\"more-vertical\"></i></a>\';\r\n}',0,1,'2024-08-26 14:03:49',0,'2024-08-27 00:54:09',0,1,1);
INSERT INTO `datatable_columns` VALUES (12,0,120,'','이청훈천재','50px','tx-center',0,'function(data, type, row) {\r\n    return \'<a style=\"color: #596882;\" href=\"javascript:void(0);\" onclick=\"openModal(\' + row.no + \');\"><i data-feather=\"more-vertical\"></i></a>\';\r\n}',NULL,1,'2024-08-30 12:17:15',NULL,NULL,0,1,0);
INSERT INTO `datatable_columns` VALUES (13,0,0,'','Actions','','',0,'function(data, type, row) {\r\n    return \'<a style=\"color: #596882;\" href=\"javascript:void(0);\" onclick=\"openModal(\' + row.no + \');\"><i data-feather=\"more-vertical\"></i></a>\';\r\n}',NULL,1,'2024-08-30 16:34:23',NULL,NULL,0,1,0);
INSERT INTO `datatable_columns` VALUES (14,0,0,'','이청훈 천채','','',0,'',NULL,1,'2024-08-30 16:35:25',NULL,NULL,0,1,0);
INSERT INTO `datatable_columns` VALUES (15,0,0,'','lch','','',0,'',NULL,1,'2024-08-30 16:39:17',NULL,NULL,0,1,0);
INSERT INTO `datatable_columns` VALUES (21,4,10,'null','','25px','tx-center',0,'function(data, type, row, meta) {\r\n                    return meta.settings._iDisplayStart + meta.row + 1;\r\n                }',0,1,'2024-08-30 11:44:12',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (22,4,50,'title','메뉴명','120px','tx-left',0,'',0,1,'2024-08-30 20:44:52',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (23,4,70,'url','URL','130px','tx-left',0,'function (data, type, row, meta) {\r\n                if (type === \'display\') {\r\n                    // \'data\'는 해당 셀의 값을 나타냅니다.\r\n                    // 이 경우, data는 링크가 됩니다.\r\n                    return \'<a href=\"\' + data + \'\" target=\"_blank\">\' + data + \'</a>\';\r\n                }\r\n                return data;\r\n            }',0,1,'2024-08-30 20:46:51',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (24,4,40,'icon','','20px','tx-center',0,'function(data, type, row) {\n    const iconName = data ? data : \"corner-down-right\";\n    return \'<i class=\"tx-color-03\" data-feather=\"\' + iconName + \'\"></i>\';\n}',0,1,'2024-08-30 20:47:59',0,'2024-09-14 16:15:11',0,1,1);
INSERT INTO `datatable_columns` VALUES (25,4,15,'type','타입','50px','tx-center',0,'function(data, type, row) {\r\n    return data == \"admin\" ? \"관리자\" : \"사용자\";\r\n}',0,1,'2024-08-30 20:48:34',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (26,4,20,'category_name','카테고리','80px','tx-center',0,'',0,1,'2024-08-30 20:48:49',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (27,4,30,'parent_title','상위메뉴','80px','tx-center',0,'',0,1,'2024-08-30 20:49:01',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (28,4,60,'order','순서','30px','tx-center',0,'',NULL,1,'2024-08-30 21:01:35',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (29,4,80,'route','Route','300px','tx-left',0,'',0,1,'2024-08-30 21:02:59',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (30,4,90,'is_active','상태','40px','tx-center-f',0,'function(data, type, row) {\r\n    return data == 1 ? \"활성\" : \"비활성\";\r\n}',0,1,'2024-08-30 21:03:28',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (32,4,100,'','','30px','tx-center',0,'function(data, type, row) {\r\n    return \'<a style=\"color: #596882;\" href=\"javascript:void(0);\" onclick=\"openModal(\' + row.no + \');\"><i data-feather=\"more-vertical\"></i></a>\';\r\n}',0,1,'2024-08-30 21:34:22',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (41,6,NULL,NULL,'#','25px','tx-center',0,'function(data, type, row, meta) {\n                    return meta.settings._iDisplayStart + meta.row + 1;\n                }',NULL,1,'2024-09-01 14:00:05',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (42,7,NULL,NULL,'#','25px','tx-center',0,'function(data, type, row, meta) {\n                    return meta.settings._iDisplayStart + meta.row + 1;\n                }',NULL,1,'2024-09-01 14:08:15',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (43,8,1,'null','#','20px','tx-center',0,'function(data, type, row, meta) {\r\n                    return meta.settings._iDisplayStart + meta.row + 1;\r\n                }',0,1,'2024-09-01 14:20:49',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (44,8,10,'mem_userid','아이디','80px','tx-center',1,'',0,1,'2024-09-01 23:23:34',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (45,8,20,'mem_username','이름','60px','tx-center',1,'',0,1,'2024-09-01 23:23:35',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (46,8,30,'mem_nickname','닉네임','60px','tx-center',1,'',0,1,'2024-09-01 23:23:49',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (47,8,40,'mem_email','E-mail','150px','tx-center',1,'',0,1,'2024-09-01 23:24:05',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (48,8,50,'mem_point','포인트','100px','tx-center',1,'',NULL,1,'2024-09-01 23:24:27',NULL,NULL,0,1,0);
INSERT INTO `datatable_columns` VALUES (49,8,60,'mem_register_datetime','등록일','90px','tx-center',1,'function (data, type, row, meta) {\r\n    if (type === \'display\' || type === \'filter\') {\r\n        // \"0000-00-00 00:00:00\"인지 확인\r\n        if (data === \"0000-00-00 00:00:00\") {\r\n            return \'-\';\r\n        }\r\n        \r\n        var date = new Date(data);\r\n        \r\n        // 날짜 객체가 유효한지 확인\r\n        if (isNaN(date.getTime())) {\r\n            return \'-\';\r\n        }\r\n        \r\n        var year = date.getFullYear();\r\n        var month = (\'0\' + (date.getMonth() + 1)).slice(-2);  // 월은 0부터 시작하므로 +1 필요\r\n        var day = (\'0\' + date.getDate()).slice(-2);\r\n        var hours = (\'0\' + date.getHours()).slice(-2);\r\n        var minutes = (\'0\' + date.getMinutes()).slice(-2);\r\n        \r\n        return year + \'/\' + month + \'/\' + day + \' <br>\' + hours + \':\' + minutes;\r\n    }\r\n    return data;\r\n}\r\n',0,1,'2024-09-01 23:24:57',0,'2024-09-02 10:44:58',0,1,1);
INSERT INTO `datatable_columns` VALUES (50,8,70,'mem_lastlogin_datetime','최근로그인','90px','tx-center',1,'function (data, type, row, meta) {\r\n    if (type === \'display\' || type === \'filter\') {\r\n        // \"0000-00-00 00:00:00\"인지 확인\r\n        if (data === \"0000-00-00 00:00:00\") {\r\n            return \'-\';\r\n        }\r\n        \r\n        var date = new Date(data);\r\n        \r\n        // 날짜 객체가 유효한지 확인\r\n        if (isNaN(date.getTime())) {\r\n            return \'-\';\r\n        }\r\n        \r\n        var year = date.getFullYear();\r\n        var month = (\'0\' + (date.getMonth() + 1)).slice(-2);  // 월은 0부터 시작하므로 +1 필요\r\n        var day = (\'0\' + date.getDate()).slice(-2);\r\n        var hours = (\'0\' + date.getHours()).slice(-2);\r\n        var minutes = (\'0\' + date.getMinutes()).slice(-2);\r\n        \r\n        return year + \'/\' + month + \'/\' + day + \' <br>\' + hours + \':\' + minutes;\r\n    }\r\n    return data;\r\n}\r\n',0,1,'2024-09-01 23:25:18',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (51,8,80,'mem_level','레벨','40px','tx-center',1,'',0,1,'2024-09-01 23:25:48',0,'2024-09-01 23:25:51',0,1,1);
INSERT INTO `datatable_columns` VALUES (52,8,90,'mem_denied','승인','40px','tx-center',1,'function(data, type, row) {\r\n    return data == 1 ? \"차단\" : \"승인\";\r\n}',0,1,'2024-09-01 23:26:17',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (53,8,100,'','','20px','tx-center',0,'function(data, type, row) {\r\n    return \'<a style=\"color: #596882;\" href=\"javascript:void(0);\" onclick=\"openModal(\' + row.mem_id + \');\"><i data-feather=\"more-vertical\"></i></a>\';\r\n}',0,1,'2024-09-01 23:27:12',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (54,8,45,'attend_groups','회원그룹','100px','tx-center',0,'',NULL,1,'2024-09-01 23:55:58',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (55,9,NULL,NULL,'#','25px','tx-center',0,'function(data, type, row, meta) {\n                    return meta.settings._iDisplayStart + meta.row + 1;\n                }',NULL,1,'2024-09-10 06:32:27',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (56,9,100,'','','30px','tx-center',0,'function(data, type, row) {\r\n                        return \'<a style=\"color: #596882;\" href=\"javascript:void(0);\" onclick=\"openModal(\' + row.mll_id + \');\"><i data-feather=\"more-vertical\"></i></a>\';\r\n                    }',0,1,'2024-09-10 06:32:27',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (57,9,30,'mll_success','상태','40px','tx-center',1,'function (data, type, row, meta) {\r\n    if (type === \'display\') {\r\n        if (data === \'1\') {\r\n            return \'<span class=\"badge badge-success\">성공</span>\';\r\n        } else if (data === \'0\') {\r\n            return \'<span class=\"badge badge-danger\">실패</span>\';\r\n        }\r\n    }\r\n    return data;  // 다른 타입 처리 (예: sort, type 등)\r\n}',0,1,'2024-09-10 15:33:53',0,'2024-09-10 15:33:56',0,1,1);
INSERT INTO `datatable_columns` VALUES (58,9,20,'mll_userid','아이디','80px','tx-center',1,'',0,1,'2024-09-10 15:37:38',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (59,9,40,'mll_reason','상태','50px','tx-center',0,'',0,1,'2024-09-10 15:38:40',0,'0000-00-00 00:00:00',0,1,0);
INSERT INTO `datatable_columns` VALUES (60,9,50,'mll_fail_reason','메시지','100px','tx-left',0,'',0,1,'2024-09-10 15:39:06',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (61,9,60,'mll_location','지역','30px','tx-center',1,'function (data, type, row, meta) {\r\n                    if (type === \'display\') {\r\n                        if (!data || typeof data !== \'string\') {\r\n                            return \'-\';\r\n                        }\r\n                        \r\n                        var parts = data.split(\',\');\r\n                        var firstPart = parts[0] ? parts[0].trim() : \'-\';\r\n                        \r\n                        return firstPart;\r\n                    }\r\n                    return data;\r\n                }',0,1,'2024-09-10 15:39:58',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (62,9,70,'mll_datetime','일시','80px','tx-center',1,'function (data, type, row, meta) {\r\n                    if (type === \'display\') {\r\n                        var dateTimeParts = data.split(\' \');\r\n                        var datePart = dateTimeParts[0]; // 날짜 부분\r\n                        var timePart = dateTimeParts[1]; // 시간 부분\r\n                        return datePart + \' \' + timePart;\r\n                    }\r\n                    return data;\r\n                }',0,1,'2024-09-10 15:41:58',0,'2024-09-10 15:47:18',0,1,1);
INSERT INTO `datatable_columns` VALUES (63,9,55,'mll_ip','IP','50px','tx-center',1,'',0,1,'2024-09-10 15:52:55',0,'2024-09-10 15:52:57',0,1,1);
INSERT INTO `datatable_columns` VALUES (65,8,0,'mem_id',' <input type=\"checkbox\" id=\"select-all\">','20px','tx-center-f',0,'function(data, type, row, meta) {\r\n    return \'<input type=\"checkbox\" class=\"row-select\" data-row-id=\"\' + row.mem_id + \'\">\';\r\n}',0,1,'2024-08-26 14:03:49',0,'2024-09-14 00:52:28',0,1,1);
INSERT INTO `datatable_columns` VALUES (66,10,10,'null','#','25px','tx-center',0,'function(data, type, row, meta) {\r\n                    return meta.settings._iDisplayStart + meta.row + 1;\r\n                }',0,1,'2024-09-24 01:02:59',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (67,10,100,'','','30px','tx-center',0,'function(data, type, row) {\n                        return \'<a style=\"color: #596882;\" href=\"javascript:void(0);\" onclick=\"openModal(\' + row.no + \');\"><i data-feather=\"more-vertical\"></i></a>\';\n                    }',0,1,'2024-09-24 01:02:59',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (68,10,20,'name','카테고리명','100px','tx-center',1,'',NULL,1,'2024-09-24 10:03:26',NULL,'2024-09-24 10:03:30',0,1,1);
INSERT INTO `datatable_columns` VALUES (69,10,30,'order','순서','100px','tx-center',1,'',NULL,1,'2024-09-24 10:03:47',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (70,10,15,'type','타입','100px','tx-center',1,'',NULL,1,'2024-09-24 10:04:00',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (71,11,NULL,NULL,'#','25px','tx-center',0,'function(data, type, row, meta) {\n                    return meta.settings._iDisplayStart + meta.row + 1;\n                }',NULL,1,'2025-01-03 07:53:50',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (72,11,100,'','','30px','tx-center',0,'function(data, type, row) {\n                        return \'<a style=\"color: #596882;\" href=\"javascript:void(0);\" onclick=\"openModal(\' + row.no + \');\"><i data-feather=\"more-vertical\"></i></a>\';\n                    }',0,1,'2025-01-03 07:53:50',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (73,11,15,'type','타입','100px','tx-center',1,'',NULL,1,'2025-01-03 16:54:36',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (74,11,20,'name','카테고리명','200px','tx-center',1,'',0,1,'2025-01-03 16:54:55',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (75,11,30,'order','순서','80px','tx-center',1,'',0,1,'2025-01-03 16:55:11',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (76,11,35,'is_active','상태','100px','tx-center-f',1,'function(data, type, row) {\r\n    return data == 1 ? \"활성\" : \"비활성\";\r\n}',0,1,'2025-01-03 16:56:09',0,'2025-01-03 16:56:16',0,1,1);
INSERT INTO `datatable_columns` VALUES (77,12,NULL,NULL,'#','25px','tx-center',0,'function(data, type, row, meta) {\n                    return meta.settings._iDisplayStart + meta.row + 1;\n                }',NULL,1,'2025-02-03 11:25:03',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (78,12,100,'','','30px','tx-center',0,'function(data, type, row) {\r\n                        return \'<a style=\"color: #596882;\" href=\"javascript:void(0);\" onclick=\"openModal(\' + row.no + \');\"><i data-feather=\"more-vertical\"></i></a>\';\r\n                    }',0,1,'2025-02-03 11:25:03',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (79,12,10,'bgr_key','그룹코드','100px','tx-center',1,'',NULL,1,'2025-02-03 20:29:44',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (80,12,20,'bgr_name','그룹명','100px','tx-center',1,'',NULL,1,'2025-02-03 20:29:54',NULL,'2025-02-03 20:31:51',0,1,1);
INSERT INTO `datatable_columns` VALUES (81,12,50,'useing_text','사용','100px','tx-center',1,'',NULL,1,'2025-02-03 20:31:48',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (82,12,40,'bgr_order','정렬순서','100px','tx-center',1,'',NULL,1,'2025-02-03 20:31:49',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (83,12,30,'bg_count','게시판수','100px','tx-center',1,'',NULL,1,'2025-02-03 20:31:50',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (84,13,NULL,NULL,'#','25px','tx-center',0,'function(data, type, row, meta) {\n                    return meta.settings._iDisplayStart + meta.row + 1;\n                }',NULL,1,'2025-02-03 13:12:42',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (85,13,100,'','','30px','tx-center',0,'function(data, type, row) {\n                        return \'<a style=\"color: #596882;\" href=\"javascript:void(0);\" onclick=\"openModal(\' + row.no + \');\"><i data-feather=\"more-vertical\"></i></a>\';\n                    }',0,1,'2025-02-03 13:12:42',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (86,13,10,'bgr_name','게시판그룹','100px','tx-center',1,'',NULL,1,'2025-02-03 23:45:01',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (87,13,30,'brd_key','게시판주소','100px','tx-center',0,'',NULL,1,'2025-02-03 23:45:19',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (88,13,40,'brd_name','게시판명','200px','tx-center',1,'',0,1,'2025-02-03 23:45:33',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (89,13,50,'brd_order','순서','50px','tx-center',1,'',0,1,'2025-02-03 23:45:49',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (90,13,60,'useing_text','사용','50px','tx-center',1,'',0,1,'2025-02-03 23:46:05',0,'2025-02-03 23:46:07',0,1,1);
INSERT INTO `datatable_columns` VALUES (91,14,NULL,NULL,'#','25px','tx-center',0,'function(data, type, row, meta) {\n                    return meta.settings._iDisplayStart + meta.row + 1;\n                }',NULL,1,'2025-02-06 06:30:22',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (92,14,100,'','','30px','tx-center',0,'function(data, type, row) {\n                        return \'<a style=\"color: #596882;\" href=\"javascript:void(0);\" onclick=\"openModal(\' + row.no + \');\"><i data-feather=\"more-vertical\"></i></a>\';\n                    }',0,1,'2025-02-06 06:30:22',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (93,14,10,'brd_name','게시판','100px','tx-center',1,'',NULL,1,'2025-02-06 16:24:45',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (94,14,20,'post_title','글제목','300px','tx-left',1,'function(data, type, row, meta) {\r\n    if (type === \'display\') {\r\n        return `\r\n            <a style=\"color: black; text-decoration: none; cursor: pointer;\"\r\n               onmouseover=\"this.style.textDecoration=\'underline\'\"\r\n               onmouseout=\"this.style.textDecoration=\'none\'\"\r\n               onclick=\"datatableApp.go_detail(\'${row.post_id}\', ${meta.row}, \'/admin/board/detail-post\')\">\r\n                ${row.post_title}\r\n            </a>\r\n        `;\r\n    }\r\n    return data;\r\n}\r\n',0,1,'2025-02-06 16:25:46',0,'0000-00-00 00:00:00',0,1,1);
INSERT INTO `datatable_columns` VALUES (95,14,30,'post_username','작성자','100px','tx-center',0,'',NULL,1,'2025-02-06 16:25:47',NULL,NULL,0,1,1);
INSERT INTO `datatable_columns` VALUES (96,14,40,'post_datetime','작성일','100px','tx-left',1,'',0,1,'2025-02-06 16:26:27',0,'0000-00-00 00:00:00',0,1,1);
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-16 14:14:42
