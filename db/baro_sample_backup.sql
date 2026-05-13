/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.4.10-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: baro
-- ------------------------------------------------------
-- Server version	11.4.10-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `badge_seen_t`
--

DROP TABLE IF EXISTS `badge_seen_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `badge_seen_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `sh_idx` int(11) NOT NULL,
  `mt_idx` int(11) NOT NULL,
  `badge_type` enum('TABLE','PACK','RESERVE') NOT NULL,
  `last_seen_at` datetime DEFAULT NULL,
  `b_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  `b_udate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idx`),
  UNIQUE KEY `uq_badge_seen` (`sh_idx`,`mt_idx`,`badge_type`),
  KEY `idx_mt` (`mt_idx`),
  KEY `idx_sh` (`sh_idx`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='가맹점주 메뉴별 뱃지 마지막 확인시각';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `badge_seen_t`
--
-- WHERE:  true limit 10

LOCK TABLES `badge_seen_t` WRITE;
/*!40000 ALTER TABLE `badge_seen_t` DISABLE KEYS */;
INSERT INTO `badge_seen_t` VALUES
(1,1,2,'TABLE','2026-03-30 18:20:49','2026-03-30 09:46:49','2026-03-30 18:20:49'),
(2,1,2,'PACK','2026-03-30 18:16:17','2026-03-30 09:55:40','2026-03-30 18:16:17'),
(3,1,2,'RESERVE','2026-03-30 12:00:58','2026-03-30 11:55:39','2026-03-30 12:00:58'),
(4,14,23,'TABLE','2026-03-31 14:11:11','2026-03-31 13:46:48','2026-03-31 14:11:11'),
(5,14,23,'PACK','2026-03-31 14:06:59','2026-03-31 13:58:38','2026-03-31 14:06:59'),
(6,14,23,'RESERVE','2026-03-31 14:06:04','2026-03-31 14:06:04','2026-03-31 14:06:04'),
(7,45,73,'TABLE','2026-03-31 14:18:52','2026-03-31 14:18:52','2026-03-31 14:18:52'),
(8,46,74,'TABLE','2026-04-17 15:40:45','2026-03-31 14:39:47','2026-04-17 15:40:45'),
(9,47,78,'TABLE','2026-04-03 09:43:39','2026-03-31 15:25:11','2026-04-03 09:43:39'),
(10,46,74,'PACK','2026-04-03 09:59:21','2026-04-02 11:09:17','2026-04-03 09:59:21');
/*!40000 ALTER TABLE `badge_seen_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_options_t`
--

DROP TABLE IF EXISTS `cart_options_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_options_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `ct_idx` int(11) DEFAULT NULL,
  `om_idx` int(11) DEFAULT NULL,
  `oc_idx` int(11) DEFAULT NULL,
  `co_option_name` varchar(255) DEFAULT NULL,
  `co_option_price` int(11) DEFAULT NULL,
  `co_wdate` datetime DEFAULT current_timestamp(),
  `co_udate` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  KEY `cart_options_t_cart_t_idx_fk` (`ct_idx`),
  KEY `cart_options_t_menu_option_category_t_idx_fk` (`oc_idx`),
  KEY `cart_options_t_option_menu_t_idx_fk` (`om_idx`),
  CONSTRAINT `cart_options_t_cart_t_idx_fk` FOREIGN KEY (`ct_idx`) REFERENCES `cart_t` (`idx`),
  CONSTRAINT `cart_options_t_menu_option_category_t_idx_fk` FOREIGN KEY (`oc_idx`) REFERENCES `menu_option_category_t` (`idx`),
  CONSTRAINT `cart_options_t_option_menu_t_idx_fk` FOREIGN KEY (`om_idx`) REFERENCES `option_menu_t` (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_options_t`
--
-- WHERE:  true limit 10

LOCK TABLES `cart_options_t` WRITE;
/*!40000 ALTER TABLE `cart_options_t` DISABLE KEYS */;
INSERT INTO `cart_options_t` VALUES
(1,64,152,86,'칼칼하게',0,'2026-04-07 14:20:12',NULL),
(2,64,155,87,'곱배기',2000,'2026-04-07 14:20:12',NULL),
(3,70,152,86,'칼칼하게',0,'2026-04-08 13:39:50',NULL);
/*!40000 ALTER TABLE `cart_options_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_t`
--

DROP TABLE IF EXISTS `cart_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `mt_idx` int(11) DEFAULT NULL COMMENT '회원 키',
  `st_id` int(11) DEFAULT NULL COMMENT '매장 키',
  `sm_id` int(11) DEFAULT NULL COMMENT '메뉴 키',
  `ct_quantity` int(11) NOT NULL,
  `ct_price` int(11) NOT NULL,
  `ct_total_price` int(11) NOT NULL,
  `ct_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  `ct_udate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idx`),
  KEY `cart_t_shop_menu_t_idx_fk` (`sm_id`),
  KEY `cart_t_shop_t_idx_fk` (`st_id`),
  CONSTRAINT `cart_t_shop_menu_t_idx_fk` FOREIGN KEY (`sm_id`) REFERENCES `shop_menu_t` (`idx`),
  CONSTRAINT `cart_t_shop_t_idx_fk` FOREIGN KEY (`st_id`) REFERENCES `shop_t` (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_t`
--
-- WHERE:  true limit 10

LOCK TABLES `cart_t` WRITE;
/*!40000 ALTER TABLE `cart_t` DISABLE KEYS */;
INSERT INTO `cart_t` VALUES
(62,77,46,36,1,9000,9000,'2026-04-03 09:21:48','2026-04-03 09:21:48'),
(63,NULL,46,36,1,9000,9000,'2026-04-03 11:02:17','2026-04-03 11:02:17'),
(64,NULL,46,32,1,12000,12000,'2026-04-07 14:20:12','2026-04-07 14:20:12'),
(65,84,48,42,3,12000,36000,'2026-04-07 14:37:55','2026-04-13 11:09:28'),
(70,NULL,46,32,1,10000,10000,'2026-04-08 13:39:50','2026-04-08 13:39:50'),
(78,81,46,36,1,9000,9000,'2026-04-10 23:02:06','2026-04-10 23:02:06');
/*!40000 ALTER TABLE `cart_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupon_log_t`
--

DROP TABLE IF EXISTS `coupon_log_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_log_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `ct_idx` int(11) DEFAULT NULL COMMENT '쿠폰 키',
  `mt_idx` int(11) DEFAULT NULL COMMENT '회원 키',
  `cl_view` enum('Y','N') DEFAULT 'N' COMMENT '사용 여부',
  `cl_wdate` datetime DEFAULT NULL,
  `cl_udate` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  KEY `coupon_log_t_coupon_t_idx_fk` (`ct_idx`),
  KEY `coupon_log_t_member_t_idx_fk` (`mt_idx`),
  CONSTRAINT `coupon_log_t_coupon_t_idx_fk` FOREIGN KEY (`ct_idx`) REFERENCES `coupon_t` (`idx`),
  CONSTRAINT `coupon_log_t_member_t_idx_fk` FOREIGN KEY (`mt_idx`) REFERENCES `member_t` (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='쿠폰 내역';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupon_log_t`
--
-- WHERE:  true limit 10

LOCK TABLES `coupon_log_t` WRITE;
/*!40000 ALTER TABLE `coupon_log_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupon_log_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupon_t`
--

DROP TABLE IF EXISTS `coupon_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `ct_code` varchar(50) NOT NULL COMMENT '쿠폰 코드',
  `sh_idx` int(11) NOT NULL DEFAULT 0 COMMENT '매장 키(0=전체)',
  `ct_title` varchar(255) NOT NULL COMMENT '쿠폰 이름',
  `ct_type1` tinyint(1) NOT NULL DEFAULT 1 COMMENT '유효기간 타입(1:기간설정, 2:발급일+N일)',
  `ct_type2` tinyint(1) NOT NULL DEFAULT 1 COMMENT '할인 타입(1:정액, 2:정율)',
  `ct_discount1` int(11) NOT NULL DEFAULT 0 COMMENT '할인 금액(정액/정율)',
  `ct_discount3` int(11) NOT NULL DEFAULT 0 COMMENT '최소 주문금액',
  `ct_sdate` date DEFAULT NULL COMMENT '유효 시작일',
  `ct_edate` date DEFAULT NULL COMMENT '유효 종료일',
  `ct_days` int(11) DEFAULT NULL COMMENT '발급일 기준 N일',
  `ct_show` char(1) NOT NULL DEFAULT 'Y' COMMENT '사용 여부(Y/N)',
  `ct_target_scope` enum('ALL','MEMBER') NOT NULL DEFAULT 'ALL' COMMENT '발급 대상 범위',
  `ct_target_members` text DEFAULT NULL COMMENT '발급 대상 회원 목록(mt_idx CSV)',
  `ct_memo` text DEFAULT NULL COMMENT '관리자 메모',
  `ct_order` int(11) NOT NULL DEFAULT 0 COMMENT '정렬 순서',
  `ct_wdate` datetime DEFAULT NULL,
  `ct_udate` datetime DEFAULT NULL,
  `ct_del_yn` char(1) NOT NULL DEFAULT 'N' COMMENT '삭제 여부(Y/N)',
  `ct_del_date` datetime DEFAULT NULL COMMENT '삭제 일시',
  PRIMARY KEY (`idx`),
  UNIQUE KEY `uk_coupon_code` (`ct_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='쿠폰';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupon_t`
--
-- WHERE:  true limit 10

LOCK TABLES `coupon_t` WRITE;
/*!40000 ALTER TABLE `coupon_t` DISABLE KEYS */;
INSERT INTO `coupon_t` VALUES
(1,'N37U4W7G2U',0,'테스트',1,1,1111,1,'2026-03-30','2026-03-31',NULL,'Y','ALL',NULL,'테스트',1,'2026-03-30 09:40:51',NULL,'Y','2026-03-30 09:41:13');
/*!40000 ALTER TABLE `coupon_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_t`
--

DROP TABLE IF EXISTS `member_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `mt_type` tinyint(4) DEFAULT NULL COMMENT '회원가입구분\n1: 일반 2:카카오, 3:네이버, 4:구글, 5:애플, 6:페이스북',
  `mt_grade` varchar(20) DEFAULT 'rookie' COMMENT '회원 등급 코드 (member_grade_t.w_code와 연동)',
  `mt_id` varchar(255) DEFAULT NULL COMMENT '아이디(이메일)',
  `mt_sns_id` varchar(255) DEFAULT NULL COMMENT 'sns계정연동',
  `mt_pwd_key` varchar(255) DEFAULT NULL COMMENT '비밀번호키',
  `mt_pwd` varchar(255) DEFAULT NULL COMMENT '비밀번호',
  `mt_level` tinyint(1) DEFAULT NULL COMMENT '회원구분\n1:탈퇴, 2:회원,\n5:딜러회원, 7:폴리스관리자,  8: 서브관리자, 9:관리자',
  `mt_rank` tinyint(1) DEFAULT NULL COMMENT '회원등급 1:실버, 2:GOLD, 3:ROYAL, 4:VIP, 5:VVIP',
  `mt_appr` enum('','Y','N','D','T') DEFAULT NULL COMMENT '승인 여부 (Y: 승인 N: 대기 D: 거절 T: 임시',
  `mt_auth` enum('Y','N') DEFAULT 'N' COMMENT '회원본인인증 여부',
  `mt_auto_login` enum('Y','N') DEFAULT 'N' COMMENT '자동 로그인 여부',
  `mt_agency_code` varchar(50) DEFAULT NULL COMMENT '에이전시코드',
  `mt_promo_code` varchar(10) DEFAULT NULL COMMENT '홍보회원코드',
  `mt_promo_rank` tinyint(1) DEFAULT NULL COMMENT '홍보회원등급 1:일반, 2:인플루언서',
  `mt_promo_rate` float DEFAULT NULL COMMENT '홍보성사율 전체',
  `mt_promo_rate_week` float DEFAULT NULL COMMENT '홍보성사율 주간',
  `mt_promo_rate_month` float DEFAULT NULL COMMENT '홍보성사율 월간',
  `mt_name` varchar(50) DEFAULT NULL COMMENT '성명',
  `mt_nickname` varchar(50) DEFAULT NULL COMMENT '닉네임',
  `mt_nickname_date` datetime DEFAULT NULL COMMENT '닉네임 변경일',
  `mt_image1` varchar(255) DEFAULT NULL COMMENT '프로필이미지',
  `mt_hp` varchar(50) DEFAULT NULL COMMENT '연락처',
  `mt_tel` varchar(50) DEFAULT NULL COMMENT '연락처',
  `mt_birth` varchar(10) DEFAULT NULL COMMENT '생년월일',
  `mt_gender` enum('F','M') DEFAULT NULL COMMENT '성별',
  `mt_email` varchar(50) DEFAULT NULL COMMENT 'Email',
  `mt_zip` varchar(10) DEFAULT NULL COMMENT '우편번호',
  `mt_add1` varchar(255) DEFAULT NULL COMMENT '주소',
  `mt_add2` varchar(255) DEFAULT NULL COMMENT '상세주소',
  `mt_sale_price` double DEFAULT NULL COMMENT '판매대금 전체',
  `mt_sale_price_week` double DEFAULT NULL COMMENT '판매대금 주간',
  `mt_sale_price_month` double DEFAULT NULL COMMENT '판매대금 월간',
  `mt_language` tinyint(1) DEFAULT 2 COMMENT '회원지정언어 1:ko, 2:en',
  `mt_bank` varchar(255) DEFAULT NULL COMMENT '정산은행',
  `mt_bank_account` varchar(255) DEFAULT NULL COMMENT '정산계좌',
  `mt_bank_name` varchar(255) DEFAULT NULL COMMENT '정산예금주',
  `mt_image2` varchar(255) DEFAULT NULL COMMENT '통장사본',
  `mt_point` double(22,0) DEFAULT NULL COMMENT '현적립금',
  `mt_profile_memo` mediumtext DEFAULT NULL COMMENT '프로필 소개내용',
  `mt_following_cnt` int(11) DEFAULT 0 COMMENT '내가 팔로우한 수',
  `mt_follower_cnt` int(11) DEFAULT 0 COMMENT '나를 팔로우한 수',
  `mt_membership_title` varchar(50) DEFAULT NULL COMMENT '멤버십 제목',
  `mt_membership_memo` varchar(255) DEFAULT NULL COMMENT '멤버십 내용',
  `mt_membership_price` double DEFAULT NULL COMMENT '멤버십 가격',
  `mt_membership_status` tinyint(1) DEFAULT NULL COMMENT '활성화 여부 1:활성화, 2:비활성화',
  `mt_store_title` varchar(50) DEFAULT NULL COMMENT '스토어명',
  `mt_sns1` varchar(255) DEFAULT NULL COMMENT 'SNS주소',
  `mt_sns2` varchar(255) DEFAULT NULL COMMENT 'SNS주소',
  `mt_sns3` varchar(255) DEFAULT NULL COMMENT 'SNS주소',
  `mt_sns4` varchar(255) DEFAULT NULL COMMENT 'SNS주소',
  `mt_sns5` varchar(255) DEFAULT NULL COMMENT 'SNS주소',
  `mt_marketing` enum('Y','N') DEFAULT 'N' COMMENT '마케팅 정보 수신동의',
  `mt_pcc_code` varchar(20) DEFAULT NULL COMMENT '개인통관번호',
  `mt_app_token` varchar(255) DEFAULT NULL COMMENT '앱토큰',
  `mt_web_token` varchar(255) DEFAULT NULL,
  `mt_app_join` enum('Y','N') DEFAULT 'N' COMMENT '회원가입(앱 : Y, 웹 : N)',
  `mt_smsing` enum('Y','N') DEFAULT 'Y' COMMENT '문자수신여부',
  `mt_mailing` enum('Y','N') DEFAULT 'Y' COMMENT '이메일수신여부',
  `mt_authority1` enum('Y','N') DEFAULT 'N' COMMENT '쿠폰 관리 권한',
  `mt_authority2` enum('Y','N') DEFAULT 'N' COMMENT '포인트 관리 권한',
  `mt_authority3` enum('Y','N') DEFAULT 'N' COMMENT '리뷰 관리 권한',
  `mt_authority4` enum('Y','N') DEFAULT 'N' COMMENT '신고 관리 권한',
  `mt_authority5` enum('Y','N') DEFAULT 'N' COMMENT '문의 관리 권한',
  `mt_pushing1` enum('Y','N') DEFAULT 'Y' COMMENT '알림(앱푸쉬)',
  `mt_pushing2` enum('Y','N') DEFAULT 'Y' COMMENT '알림2(카카오톡)',
  `mt_pushing3` enum('Y','N') DEFAULT 'Y' COMMENT '알림3',
  `mt_pushing4` enum('Y','N') DEFAULT 'Y' COMMENT '알림4',
  `mt_pushing5` enum('Y','N') DEFAULT 'Y' COMMENT '알림5',
  `mt_push` enum('Y','N') DEFAULT 'Y' COMMENT '즐겨찾기 알림',
  `mt_notice_push` enum('Y','N') DEFAULT 'Y' COMMENT '공지사항 알림',
  `mt_status` enum('Y','N') DEFAULT 'N' COMMENT '로그인상태 Y:로그인, N:불가능',
  `del_status` enum('Y','N') DEFAULT 'N' COMMENT '회원상태: Y:정상, N:정지',
  `mt_show_influ` enum('Y','N') DEFAULT 'Y' COMMENT '인플루언서 노출여부 선택 Y:노출, N:미노출',
  `mt_seller` enum('Y','N','D','R') DEFAULT 'N' COMMENT '판매자 승인여부 Y:승인, N:미승인, D:요청, R:거절',
  `mt_influencer` enum('Y','N','D','R') DEFAULT 'N' COMMENT '인플루언서 승인여부 Y:승인, N:미승인, D:요청',
  `mt_agency` enum('Y','N','D','R') DEFAULT 'N' COMMENT '에이전시 승인여부 Y:승인, N:미승인, D:요청, R:거절',
  `mt_mng` enum('Y','N','D','R') DEFAULT 'N' COMMENT '부관리자 승인여부 Y:승인, N:미승인, D:요청',
  `mt_sldate` datetime DEFAULT NULL COMMENT '셀러승인일시',
  `mt_fdate` datetime DEFAULT NULL COMMENT '인플루언서 승인일시',
  `mt_wdate` datetime DEFAULT NULL COMMENT '회원가입일시',
  `mt_udate` datetime DEFAULT NULL COMMENT '승인일시',
  `mt_ldate` datetime DEFAULT NULL COMMENT '로그인일시',
  `mt_lgdate` datetime DEFAULT NULL COMMENT '로그아웃일시',
  `mt_rdate` datetime DEFAULT NULL COMMENT '탈퇴일시',
  `mt_retire_level` int(11) DEFAULT 0,
  `mt_retire_memo` varchar(255) DEFAULT NULL COMMENT '회원탈퇴사유',
  `mt_wine_like_cnt` int(11) DEFAULT 0 COMMENT '와인찜 갯수',
  `mt_wine_keep_cnt` int(11) DEFAULT 0 COMMENT '와인보관 갯수',
  `mt_review_bookmark_cnt` int(11) DEFAULT 0,
  `mt_device_type` varchar(50) DEFAULT NULL COMMENT '디바이스',
  `mt_position` varchar(45) DEFAULT NULL COMMENT '관리자직책',
  `mt_app_version` varchar(45) DEFAULT NULL COMMENT '내 앱 버전',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=493849 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='회원 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_t`
--
-- WHERE:  true limit 10

LOCK TABLES `member_t` WRITE;
/*!40000 ALTER TABLE `member_t` DISABLE KEYS */;
INSERT INTO `member_t` VALUES
(1,1,'rookie','admin',NULL,NULL,'$2y$10$MmVgeloevIPbbxlI6kCv9.L1/zAwHwPQ7Gl6FwrjSak8/hiqLVtVC',9,NULL,NULL,'Y','N',NULL,NULL,NULL,NULL,NULL,NULL,'최고관리자','관리자',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N',NULL,NULL,NULL,'N','Y','Y','N','N','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N','N',NULL,NULL,'2025-11-13 10:38:14',NULL,'2026-04-28 10:54:39',NULL,NULL,0,NULL,0,0,0,NULL,NULL,NULL),
(74,1,'rookie','test',NULL,NULL,'$2y$12$gVCI7C9P2gqgPPeortDM..N5Nf/XxtKnZFUGpMiDYGiaixKZIVmvq',5,NULL,'Y','Y','N',NULL,NULL,NULL,NULL,NULL,NULL,'테스트','테스트',NULL,NULL,'01000000000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N',NULL,NULL,NULL,'N','Y','Y','N','N','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N','N',NULL,NULL,'2026-03-31 14:39:05',NULL,'2026-04-17 15:40:44',NULL,NULL,0,NULL,0,0,0,NULL,NULL,NULL),
(75,2,'rookie','kakao_4790687671','kakao_4790687671',NULL,NULL,2,NULL,NULL,'N','N',NULL,NULL,NULL,NULL,NULL,NULL,'kakao47906876','kakao47906876','2026-03-31 14:41:13','','',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N',NULL,'',NULL,'N','Y','Y','N','N','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N','N',NULL,NULL,'2026-03-31 14:41:13',NULL,'2026-04-20 12:27:25',NULL,NULL,0,NULL,0,0,0,NULL,NULL,NULL),
(76,2,'rookie','kakao_4790687671','kakao_4790687671',NULL,NULL,2,NULL,NULL,'N','N',NULL,NULL,NULL,NULL,NULL,NULL,'kakao47906876','kakao47906876','2026-03-31 14:41:13','','',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N',NULL,NULL,NULL,'N','Y','Y','N','N','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N','N',NULL,NULL,'2026-03-31 14:41:13',NULL,'2026-03-31 14:41:13',NULL,NULL,0,NULL,0,0,0,NULL,NULL,NULL),
(77,1,'rookie','test11',NULL,NULL,'$2y$12$K9mkwpD3hsS0XcpPomjbKO3Ci88oXlaZuDdMzgEghk3tLp8h4PTOW',2,NULL,NULL,'N','N',NULL,NULL,NULL,NULL,NULL,NULL,'테스트','테스트',NULL,NULL,'01000000000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N',NULL,'c_f90TJFE07uga9e-hilzf:APA91bGS8n0TamcaAP-hoCpRQdiddePoJAj_EeKMuh7BAdhpik8BOeL8jXIluYufR38-W5xRV_LJGCiLOY0OGAbuZ3mvbzYj_EPZSIJLJgnGQWTGUL4LxDA',NULL,'N','Y','Y','N','N','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N','N',NULL,NULL,'2026-03-31 14:42:50',NULL,NULL,NULL,NULL,0,NULL,0,0,0,NULL,NULL,NULL),
(78,1,'rookie','test2',NULL,NULL,'$2y$12$SYneK73fMGImrHacpD3rE.e1Mik9g1I4HOPI6q5yGUY/NCxXo6KaW',5,NULL,'T','Y','N',NULL,NULL,NULL,NULL,NULL,NULL,'테스트2','테스트2',NULL,NULL,'01000000001',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N',NULL,NULL,NULL,'N','Y','Y','N','N','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N','N',NULL,NULL,'2026-03-31 15:24:11',NULL,'2026-04-03 09:25:16',NULL,NULL,0,NULL,0,0,0,NULL,NULL,NULL),
(79,5,'rookie','apple_000659.1c7e0c5e7a5c41f2a59fc2d55b347ec7.0705','apple_000659.1c7e0c5e7a5c41f2a59fc2d55b347ec7.0705',NULL,NULL,2,NULL,NULL,'N','N',NULL,NULL,NULL,NULL,NULL,NULL,'AppleUser_000659.1','AppleUser_000659.1','2026-04-01 09:15:24',NULL,'',NULL,NULL,NULL,'8vz2rzsw56@privaterelay.appleid.com',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N',NULL,'cCe70eG7a0Huhj8GDKaTez:APA91bFWkFSoIxJfDMry5a2f4bWRvMwDbfIAtJsu9A61npCFnLnBQ0eMY0Aj1TYZrgAODXK5aqwCXpC4dpkD6JKUPUiGXDmBf_P2goLMAc__FUsZthBzMlI',NULL,'N','Y','Y','N','N','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N','N',NULL,NULL,'2026-04-01 09:15:24',NULL,'2026-04-01 09:15:24',NULL,NULL,0,NULL,0,0,0,NULL,NULL,NULL),
(80,5,'rookie','apple_000870.cd6b68e367e54cc094783e4b28e6cebc.0205','apple_000870.cd6b68e367e54cc094783e4b28e6cebc.0205',NULL,NULL,2,NULL,NULL,'N','N',NULL,NULL,NULL,NULL,NULL,NULL,'AppleUser_000870.c','AppleUser_000870.c','2026-04-01 11:05:23',NULL,'',NULL,NULL,NULL,'swr8sjzm7b@privaterelay.appleid.com',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N',NULL,NULL,NULL,'N','Y','Y','N','N','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N','N',NULL,NULL,'2026-04-01 11:05:23',NULL,'2026-04-01 11:05:23',NULL,NULL,0,NULL,0,0,0,NULL,NULL,NULL),
(81,3,'rookie','naver_jJqPFhqdMj6cML3MwmfZZ8HkK1Of4XKNcx3NQnIrZLM','naver_jJqPFhqdMj6cML3MwmfZZ8HkK1Of4XKNcx3NQnIrZLM',NULL,NULL,2,NULL,NULL,'N','N',NULL,NULL,NULL,NULL,NULL,NULL,'임재득','임재득','2026-04-01 20:45:53','','',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N',NULL,'dcldjQb5TZaTJwZyiDNuX-:APA91bHsRmAXWNznMO_4Q2tc33N1wV5I7Ygc6Qd7XYlHfC7ZzYUSt5GtPwQbjdtOHSRSdPlbilNeCM_ZYqyU5uWgZojg_puz78gpEofw68Ik2hsnO4BpiIo',NULL,'N','Y','Y','N','N','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N','N',NULL,NULL,'2026-04-01 20:45:53',NULL,'2026-04-10 10:30:17',NULL,NULL,0,NULL,0,0,0,NULL,NULL,NULL),
(82,3,'rookie','naver_UL8QcOJJqqpLiJtVsg-w71W7oDhjHSnruPxCV8jKhuM','naver_UL8QcOJJqqpLiJtVsg-w71W7oDhjHSnruPxCV8jKhuM',NULL,NULL,2,NULL,NULL,'N','N',NULL,NULL,NULL,NULL,NULL,NULL,'이슬','이슬','2026-04-03 09:21:52','','',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N',NULL,NULL,NULL,'N','Y','Y','N','N','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','N','N','N','N',NULL,NULL,'2026-04-03 09:21:52',NULL,'2026-04-03 09:21:52',NULL,NULL,0,NULL,0,0,0,NULL,NULL,NULL);
/*!40000 ALTER TABLE `member_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_option_category_t`
--

DROP TABLE IF EXISTS `menu_option_category_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_option_category_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `sm_idx` int(11) DEFAULT NULL COMMENT '메뉴 키',
  `oc_title` varchar(255) DEFAULT NULL COMMENT '카테고리 명',
  `oc_check` enum('Y','N') DEFAULT 'Y' COMMENT '필수: Y, 선택: N',
  `oc_su` int(11) DEFAULT NULL COMMENT '최대 개수',
  `oc_show` enum('Y','N') DEFAULT 'Y' COMMENT '노출여부',
  `oc_order` int(11) DEFAULT NULL COMMENT '순서',
  `oc_wdate` datetime DEFAULT NULL,
  `oc_udate` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  KEY `menu_option_category_t_shop_menu_t_idx_fk` (`sm_idx`),
  CONSTRAINT `menu_option_category_t_shop_menu_t_idx_fk` FOREIGN KEY (`sm_idx`) REFERENCES `shop_menu_t` (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='메뉴 옵션 카테고리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_option_category_t`
--
-- WHERE:  true limit 10

LOCK TABLES `menu_option_category_t` WRITE;
/*!40000 ALTER TABLE `menu_option_category_t` DISABLE KEYS */;
INSERT INTO `menu_option_category_t` VALUES
(38,36,'오이고명','N',1,'Y',1,'2026-04-03 09:08:06','2026-04-03 09:08:06'),
(39,37,'빼주세요','N',1,'Y',1,'2026-04-03 09:10:07','2026-04-03 09:10:07'),
(40,37,'사이즈업','N',1,'Y',2,'2026-04-03 09:10:07','2026-04-03 09:10:07'),
(41,38,'추가','N',1,'Y',1,'2026-04-03 09:32:49','2026-04-03 09:32:49'),
(44,39,'맵기 선택','N',1,'Y',1,'2026-04-03 09:34:36','2026-04-03 09:34:36'),
(45,39,'추가','N',1,'Y',2,'2026-04-03 09:34:36','2026-04-03 09:34:36'),
(48,40,'사이즈','Y',1,'Y',1,'2026-04-03 09:44:02','2026-04-03 09:44:02'),
(49,40,'토핑 추가','N',1,'Y',2,'2026-04-03 09:44:02','2026-04-03 09:44:02'),
(86,32,'맵기','Y',1,'Y',1,'2026-04-06 17:40:40','2026-04-06 17:40:40'),
(87,32,'사이즈','N',1,'Y',2,'2026-04-06 17:40:40','2026-04-06 17:40:40');
/*!40000 ALTER TABLE `menu_option_category_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notice_t`
--

DROP TABLE IF EXISTS `notice_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notice_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `mt_idx` int(11) DEFAULT NULL COMMENT '회원 IDX',
  `mt_id` varchar(255) DEFAULT NULL COMMENT '회원 ID',
  `mt_name` varchar(255) DEFAULT NULL COMMENT '회원명',
  `nt_title` varchar(255) DEFAULT NULL COMMENT '공지사항 제목',
  `nt_content` longtext DEFAULT NULL COMMENT '공지사항 내용',
  `nt_order` int(11) DEFAULT 0 COMMENT '순서',
  `nt_show` enum('Y','N') DEFAULT NULL COMMENT '공지사항 노출여부 Y:노출, N:노출안함',
  `nt_hit` int(11) DEFAULT 0 COMMENT '조회수',
  `nt_wdate` datetime DEFAULT NULL COMMENT '등록일시',
  `nt_udate` datetime DEFAULT NULL COMMENT '등록일시',
  `del_date` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='C 공지사항 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notice_t`
--
-- WHERE:  true limit 10

LOCK TABLES `notice_t` WRITE;
/*!40000 ALTER TABLE `notice_t` DISABLE KEYS */;
INSERT INTO `notice_t` VALUES
(1,1,'admin','최고관리자','테스트','<p>ㅂㅈㄷㅈㅂㄷ</p>',1,'Y',0,'2026-03-30 09:41:35',NULL,NULL);
/*!40000 ALTER TABLE `notice_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `option_menu_t`
--

DROP TABLE IF EXISTS `option_menu_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `option_menu_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `oc_idx` int(11) DEFAULT NULL COMMENT '옵션 카테고리 키',
  `om_title` varchar(255) DEFAULT NULL COMMENT '옵션 명',
  `om_price` int(11) DEFAULT NULL COMMENT '가격',
  `om_show` enum('Y','N') DEFAULT 'Y' COMMENT '노출여부',
  `om_order` int(11) DEFAULT NULL COMMENT '순서',
  `om_wdate` datetime DEFAULT NULL,
  `om_udate` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  KEY `option_menu_t_menu_option_category_t_idx_fk` (`oc_idx`),
  CONSTRAINT `option_menu_t_menu_option_category_t_idx_fk` FOREIGN KEY (`oc_idx`) REFERENCES `menu_option_category_t` (`idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='메뉴 옵션';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `option_menu_t`
--
-- WHERE:  true limit 10

LOCK TABLES `option_menu_t` WRITE;
/*!40000 ALTER TABLE `option_menu_t` DISABLE KEYS */;
INSERT INTO `option_menu_t` VALUES
(58,38,'빼주세요',0,'Y',1,'2026-04-03 09:08:06','2026-04-03 09:08:06'),
(59,39,'오이',0,'Y',1,'2026-04-03 09:10:07','2026-04-03 09:10:07'),
(60,39,'배',0,'Y',2,'2026-04-03 09:10:07','2026-04-03 09:10:07'),
(61,39,'새싹채소',0,'Y',3,'2026-04-03 09:10:07','2026-04-03 09:10:07'),
(62,40,'면곱배기',10000,'Y',1,'2026-04-03 09:10:07','2026-04-03 09:10:07'),
(63,40,'낙지1마리 추가',3000,'Y',2,'2026-04-03 09:10:07','2026-04-03 09:10:07'),
(64,41,'식전빵(3개)',2000,'Y',1,'2026-04-03 09:32:49','2026-04-03 09:32:49'),
(65,41,'부라타 치즈 1개',4000,'Y',2,'2026-04-03 09:32:49','2026-04-03 09:32:49'),
(68,44,'맵게해주세요',0,'Y',1,'2026-04-03 09:34:36','2026-04-03 09:34:36'),
(69,45,'조개',2000,'Y',1,'2026-04-03 09:34:36','2026-04-03 09:34:36');
/*!40000 ALTER TABLE `option_menu_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders_t`
--

DROP TABLE IF EXISTS `orders_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `mt_idx` int(11) DEFAULT NULL COMMENT '구매자 키',
  `sh_idx` int(11) DEFAULT NULL COMMENT '매장 키',
  `rv_idx` int(11) DEFAULT NULL COMMENT '예약 키',
  `tv_idx` int(11) DEFAULT NULL COMMENT '방문 세션 키(table_visit_t.idx)',
  `ot_number` varchar(255) DEFAULT NULL COMMENT '주문번호',
  `ot_status` enum('PENDING','CONFIRMED','PREPARING','COMPLETED','CANCELLED') DEFAULT 'PENDING' COMMENT '주문상태',
  `ot_table` varchar(255) DEFAULT NULL COMMENT '주문 테이블 번호(없으면 포장) ',
  `ot_total_price` decimal(10,2) DEFAULT NULL COMMENT '총 금액',
  `cl_idx` int(11) DEFAULT NULL COMMENT '쿠폰 키',
  `ot_discount_amount` decimal(10,2) DEFAULT 0.00 COMMENT '쿠폰 금액',
  `ct_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '장바구니 데이터 JSON',
  `ot_notes` text DEFAULT NULL COMMENT '요청사항',
  `ot_cancel` datetime DEFAULT NULL COMMENT '취소일자',
  `ot_cancel_reason` text DEFAULT NULL COMMENT '취소 사유',
  `ot_wdate` datetime DEFAULT NULL,
  `ot_udate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `ot_pay_type` enum('PREPAID','POSTPAID') NOT NULL DEFAULT 'PREPAID' COMMENT '결제 방식(선결제/후결제)',
  `ot_pay_status` enum('UNPAID','PAID','REFUND') NOT NULL DEFAULT 'UNPAID' COMMENT '결제 상태',
  `ot_pay_date` datetime DEFAULT NULL COMMENT '결제 완료 시각',
  `ot_settle_yn` enum('N','Y') NOT NULL DEFAULT 'N' COMMENT '정산 여부',
  `ot_settle_date` datetime DEFAULT NULL COMMENT '정산 완료 시각',
  `st_idx` int(11) DEFAULT NULL COMMENT '정산 키',
  `ot_prep_min` int(11) DEFAULT NULL COMMENT '접수 시 선택 준비시간(분)',
  `ot_prep_set_at` datetime DEFAULT NULL COMMENT '준비시간 설정 시각',
  `ot_completed_at` datetime DEFAULT NULL COMMENT '전달완료 시각',
  PRIMARY KEY (`idx`),
  UNIQUE KEY `ot_number` (`ot_number`),
  KEY `orders_t_reservation_t_idx_fk` (`rv_idx`),
  KEY `orders_t_settle_t_idx_fk` (`st_idx`),
  KEY `orders_t_shop_t_idx_fk` (`sh_idx`),
  KEY `ix_orders_tv` (`tv_idx`),
  CONSTRAINT `fk_orders_tv` FOREIGN KEY (`tv_idx`) REFERENCES `table_visit_t` (`idx`),
  CONSTRAINT `orders_t_reservation_t_idx_fk` FOREIGN KEY (`rv_idx`) REFERENCES `reservation_t` (`idx`) ON DELETE SET NULL,
  CONSTRAINT `orders_t_settle_t_idx_fk` FOREIGN KEY (`st_idx`) REFERENCES `settle_t` (`idx`) ON DELETE SET NULL,
  CONSTRAINT `orders_t_shop_t_idx_fk` FOREIGN KEY (`sh_idx`) REFERENCES `shop_t` (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='결재';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders_t`
--
-- WHERE:  true limit 10

LOCK TABLES `orders_t` WRITE;
/*!40000 ALTER TABLE `orders_t` DISABLE KEYS */;
INSERT INTO `orders_t` VALUES
(18,77,46,14,NULL,'OR-20260402-0001','CANCELLED',NULL,1000.00,NULL,0.00,'{\"items\":[{\"sm_id\":32,\"menu_name\":\"김치찌개\",\"quantity\":1,\"unit_price\":1000,\"total_price\":1000,\"options\":[]}],\"summary\":{\"sub_total\":1000,\"discount\":0,\"total\":1000}}',NULL,'2026-04-02 15:08:07',NULL,'2026-04-02 14:14:02','2026-04-02 15:08:07','PREPAID','PAID','2026-04-02 14:14:02','N',NULL,NULL,NULL,NULL,NULL),
(19,77,46,15,NULL,'OR-20260402-0002','CANCELLED',NULL,1000.00,NULL,0.00,'{\"items\":[{\"sm_id\":32,\"menu_name\":\"김치찌개\",\"quantity\":1,\"unit_price\":1000,\"total_price\":1000,\"options\":[]}],\"summary\":{\"sub_total\":1000,\"discount\":0,\"total\":1000}}',NULL,'2026-04-02 15:11:59',NULL,'2026-04-02 15:11:46','2026-04-02 15:11:59','PREPAID','PAID','2026-04-02 15:11:46','N',NULL,NULL,NULL,NULL,NULL),
(20,77,46,16,NULL,'OR-20260402-0003','CANCELLED',NULL,1000.00,NULL,0.00,'{\"items\":[{\"sm_id\":32,\"menu_name\":\"김치찌개\",\"quantity\":1,\"unit_price\":1000,\"total_price\":1000,\"options\":[]}],\"summary\":{\"sub_total\":1000,\"discount\":0,\"total\":1000}}',NULL,'2026-04-02 15:13:37',NULL,'2026-04-02 15:13:28','2026-04-02 15:13:37','PREPAID','PAID','2026-04-02 15:13:28','N',NULL,NULL,NULL,NULL,NULL),
(21,77,46,17,NULL,'OR-20260402-0004','CANCELLED',NULL,2000.00,NULL,0.00,'{\"items\":[{\"sm_id\":32,\"menu_name\":\"김치찌개\",\"quantity\":2,\"unit_price\":1000,\"total_price\":2000,\"options\":[]}],\"summary\":{\"sub_total\":2000,\"discount\":0,\"total\":2000}}',NULL,'2026-04-02 15:15:49',NULL,'2026-04-02 15:15:43','2026-04-02 15:15:49','PREPAID','PAID','2026-04-02 15:15:43','N',NULL,NULL,NULL,NULL,NULL),
(22,77,46,18,NULL,'OR-20260402-0005','CANCELLED',NULL,2000.00,NULL,0.00,'{\"items\":[{\"sm_id\":32,\"menu_name\":\"김치찌개\",\"quantity\":2,\"unit_price\":1000,\"total_price\":2000,\"options\":[]}],\"summary\":{\"sub_total\":2000,\"discount\":0,\"total\":2000}}',NULL,'2026-04-02 15:25:44',NULL,'2026-04-02 15:25:16','2026-04-02 15:25:44','PREPAID','PAID','2026-04-02 15:25:16','N',NULL,NULL,NULL,NULL,NULL),
(23,77,46,19,NULL,'OR-20260402-0006','CANCELLED',NULL,2000.00,NULL,0.00,'{\"items\":[{\"sm_id\":32,\"menu_name\":\"김치찌개\",\"quantity\":2,\"unit_price\":1000,\"total_price\":2000,\"options\":[]}],\"summary\":{\"sub_total\":2000,\"discount\":0,\"total\":2000}}',NULL,'2026-04-02 15:27:00',NULL,'2026-04-02 15:26:44','2026-04-02 15:27:00','PREPAID','PAID','2026-04-02 15:26:44','N',NULL,NULL,NULL,NULL,NULL),
(24,77,46,20,NULL,'OR-20260403-0001','PENDING',NULL,4000.00,NULL,0.00,'{\"items\":[{\"sm_id\":32,\"menu_name\":\"1996년 보양사골 대치칼국수\",\"quantity\":4,\"unit_price\":1000,\"total_price\":4000,\"options\":[]}],\"summary\":{\"sub_total\":4000,\"discount\":0,\"total\":4000}}',NULL,NULL,NULL,'2026-04-03 09:09:41','2026-04-03 09:09:41','PREPAID','PAID','2026-04-03 09:09:41','N',NULL,NULL,NULL,NULL,NULL),
(25,NULL,48,NULL,35,'OR-20260407-0001','CANCELLED','4',10000.00,NULL,0.00,'{\"items\":[{\"sm_id\":45,\"menu_name\":\"왕돈까스\",\"quantity\":1,\"unit_price\":10000,\"total_price\":10000,\"options\":[]}],\"summary\":{\"sub_total\":10000,\"discount\":0,\"total\":10000}}',NULL,'2026-04-07 15:19:19','재고가 부족합니다.','2026-04-07 15:18:38','2026-04-07 15:19:19','PREPAID','PAID','2026-04-07 15:18:38','N',NULL,NULL,NULL,NULL,NULL),
(26,NULL,48,NULL,37,'OR-20260407-0002','COMPLETED','1',12000.00,NULL,0.00,'{\"items\":[{\"sm_id\":42,\"menu_name\":\"제육볶음\",\"quantity\":1,\"unit_price\":12000,\"total_price\":12000,\"options\":[]}],\"summary\":{\"sub_total\":12000,\"discount\":0,\"total\":12000}}',NULL,NULL,NULL,'2026-04-07 16:40:35','2026-04-07 16:41:07','PREPAID','PAID','2026-04-07 16:40:35','N',NULL,NULL,NULL,NULL,NULL),
(27,75,46,21,NULL,'OR-20260408-0001','COMPLETED',NULL,1000.00,NULL,0.00,'{\"items\":[{\"sm_id\":32,\"menu_name\":\"1996년 보양사골 대치칼국수\",\"quantity\":1,\"unit_price\":1000,\"total_price\":1000,\"options\":[]}],\"summary\":{\"sub_total\":1000,\"discount\":0,\"total\":1000}}',NULL,NULL,NULL,'2026-04-08 11:12:27','2026-04-08 12:00:50','PREPAID','PAID','2026-04-08 11:12:27','N',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `orders_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_refunds_t`
--

DROP TABLE IF EXISTS `payment_refunds_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_refunds_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `pay_idx` int(11) NOT NULL COMMENT 'payments_t.idx',
  `ot_idx` int(11) NOT NULL COMMENT 'orders_t.idx',
  `sh_idx` int(11) DEFAULT NULL COMMENT 'shop_t.idx',
  `refund_type` enum('PARTIAL','FULL') NOT NULL DEFAULT 'PARTIAL',
  `request_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '요청 환불 금액',
  `approved_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '승인(성공) 환불 금액',
  `reason` varchar(255) DEFAULT NULL COMMENT '환불 사유',
  `requested_by` int(11) DEFAULT NULL COMMENT '관리자/가맹점주 mt_idx',
  `requested_ip` varchar(45) DEFAULT NULL,
  `imp_uid` varchar(255) DEFAULT NULL COMMENT '포트원 결제 고유번호(중복 저장: 조회 편의)',
  `cancel_receipt_id` varchar(255) DEFAULT NULL COMMENT '취소(환불) 식별값(있으면 저장)',
  `result_code` varchar(50) DEFAULT NULL,
  `result_msg` varchar(255) DEFAULT NULL,
  `status` enum('REQUESTED','APPROVED','FAILED') NOT NULL DEFAULT 'REQUESTED',
  `requested_at` datetime NOT NULL,
  `processed_at` datetime DEFAULT NULL,
  `pg_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '환불 요청/응답 원문' CHECK (json_valid(`pg_payload`)),
  PRIMARY KEY (`idx`),
  KEY `ix_imp_uid` (`imp_uid`),
  KEY `ix_ot_idx` (`ot_idx`),
  KEY `ix_pay_idx` (`pay_idx`),
  KEY `ix_sh_idx` (`sh_idx`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='환불(부분취소/전체취소) 이력';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_refunds_t`
--
-- WHERE:  true limit 10

LOCK TABLES `payment_refunds_t` WRITE;
/*!40000 ALTER TABLE `payment_refunds_t` DISABLE KEYS */;
INSERT INTO `payment_refunds_t` VALUES
(3,25,25,48,'FULL',10000.00,10000.00,NULL,NULL,'104.23.251.109',NULL,NULL,'OK','TEST APPROVED','APPROVED','2026-04-07 15:19:19','2026-04-07 15:19:20',NULL);
/*!40000 ALTER TABLE `payment_refunds_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments_t`
--

DROP TABLE IF EXISTS `payments_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `ot_idx` int(11) NOT NULL COMMENT 'orders_t.idx',
  `sh_idx` int(11) DEFAULT NULL COMMENT 'shop_t.idx',
  `sh_name` varchar(255) DEFAULT NULL COMMENT '결제매장 명칭',
  `mt_idx` int(11) DEFAULT NULL COMMENT 'member_t.idx (결제자)',
  `mt_name` varchar(255) DEFAULT NULL COMMENT '결제자명',
  `mt_hp` varchar(255) DEFAULT NULL COMMENT '결제자 전화번호',
  `mt_email` varchar(255) DEFAULT NULL,
  `merchant_uid` varchar(255) NOT NULL COMMENT '가맹점 주문번호(보통 orders_t.ot_number 사용)',
  `imp_uid` varchar(255) DEFAULT NULL COMMENT '포트원 결제 고유번호',
  `pg_provider` varchar(50) DEFAULT NULL COMMENT 'kcp, nice, inicis 등',
  `pay_method` varchar(50) DEFAULT NULL COMMENT 'card, trans, vbank, phone 등',
  `currency` varchar(10) DEFAULT 'KRW',
  `amount_total` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '결제 요청/승인 총액',
  `amount_paid` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '실제 승인액(대부분 total과 동일)',
  `amount_refunded` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '누적 환불액',
  `amount_remain` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '남은 결제 잔액(= paid - refunded)',
  `status` enum('READY','PAID','FAILED','CANCELLED','PARTIAL_CANCELLED','REFUNDED') NOT NULL DEFAULT 'READY' COMMENT '결제 상태',
  `paid_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `pg_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '포트원 결제 조회 원문/주요 필드' CHECK (json_valid(`pg_payload`)),
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `ux_merchant_uid` (`merchant_uid`),
  KEY `ix_imp_uid` (`imp_uid`),
  KEY `ix_ot_idx` (`ot_idx`),
  KEY `ix_sh_idx` (`sh_idx`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='결제(포트원)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments_t`
--
-- WHERE:  true limit 10

LOCK TABLES `payments_t` WRITE;
/*!40000 ALTER TABLE `payments_t` DISABLE KEYS */;
INSERT INTO `payments_t` VALUES
(9,18,46,'샘플1',77,'테스트','01000000000','guest@qrorder.com','OR-20260402-0001','pay_OR-20260402-0001_1775106799','inicis','card','KRW',1000.00,1000.00,0.00,1000.00,'PAID',NULL,NULL,'{\"status\":\"PAID\",\"id\":\"pay_OR-20260402-0001_1775106799\",\"transactionId\":\"019d4c9c-38e1-00c5-7e96-8911d2345eff\",\"merchantId\":\"merchant-864d696c-9d23-4747-b01d-e2fc12dcf76d\",\"storeId\":\"store-63ecd6c6-7024-40a1-87d1-a616e1dc63d6\",\"method\":{\"type\":\"PaymentMethodCard\",\"card\":{\"publisher\":\"KOOKMIN_CARD\",\"issuer\":\"KOOKMIN_CARD\",\"brand\":\"MASTER\",\"type\":\"DEBIT\",\"ownerType\":\"PERSONAL\",\"bin\":\"527289\",\"name\":\"국민카드\",\"number\":\"527289\"},\"approvalNumber\":\"30029356\",\"installment\":{\"month\":0,\"isInterestFree\":false},\"pointUsed\":false},\"channel\":{\"type\":\"TEST\",\"id\":\"channel-id-6dd28f85-ace8-4dd2-bb5d-8246f02dd92a\",\"key\":\"channel-key-c1dca7d8-3431-4413-adb8-6e00c19b9b5b\",\"name\":\"QR 오더 테스트 결제\",\"pgProvider\":\"INICIS_V2\",\"pgMerchantId\":\"INIpayTest\"},\"version\":\"V2\",\"requestedAt\":\"2026-04-02T05:13:19.887805101Z\",\"updatedAt\":\"2026-04-02T05:14:01.854086621Z\",\"statusChangedAt\":\"2026-04-02T05:14:01Z\",\"orderName\":\"김치찌개\",\"amount\":{\"total\":1000,\"taxFree\":0,\"vat\":91,\"supply\":909,\"discount\":0,\"paid\":1000,\"cancelled\":0,\"cancelledTaxFree\":0},\"currency\":\"KRW\",\"customer\":{\"id\":\"port-customer-id-019d4c9c-38e7-e9c1-dc50-1d5d76477d28\",\"name\":\"테스트\",\"email\":\"guest@qrorder.com\",\"phoneNumber\":\"01000000000\"},\"promotionId\":\"\",\"isCulturalExpense\":false,\"paidAt\":\"2026-04-02T05:14:01Z\",\"pgTxId\":\"StdpayCARDINIpayTest20260402141401248092\",\"pgResponse\":\"{\\n  \\\"CARD_Quota\\\": \\\"00\\\",\\n  \\\"CARD_ClEvent\\\": \\\"\\\",\\n  \\\"CARD_CorpFlag\\\": \\\"0\\\",\\n  \\\"buyerTel\\\": \\\"01000000000\\\",\\n  \\\"parentEmail\\\": \\\"\\\",\\n  \\\"applDate\\\": \\\"20260402\\\",\\n  \\\"CARD_IssuerName\\\": \\\"국민카드\\\",\\n  \\\"buyerEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"OrgPrice\\\": \\\"\\\",\\n  \\\"p_Sub\\\": \\\"\\\",\\n  \\\"resultCode\\\": \\\"0000\\\",\\n  \\\"mid\\\": \\\"INIpayTest\\\",\\n  \\\"CARD_UsePoint\\\": \\\"\\\",\\n  \\\"CARD_Num\\\": \\\"527289*********3\\\",\\n  \\\"authSignature\\\": \\\"6524357cc675c9cbdcdbe00fe69f44bca771fb3d485acdd8d63dad822e16d4d8\\\",\\n  \\\"tid\\\": \\\"StdpayCARDINIpayTest20260402141401248092\\\",\\n  \\\"EventCode\\\": \\\"\\\",\\n  \\\"goodName\\\": \\\"김치찌개\\\",\\n  \\\"TotPrice\\\": \\\"1000\\\",\\n  \\\"payMethod\\\": \\\"Card\\\",\\n  \\\"CARD_MemberNum\\\": \\\"\\\",\\n  \\\"MOID\\\": \\\"pay_OR-20260402-0001_1775106799\\\",\\n  \\\"CARD_Point\\\": \\\"\\\",\\n  \\\"currency\\\": \\\"WON\\\",\\n  \\\"CARD_PurchaseCode\\\": \\\"\\\",\\n  \\\"CARD_PrtcCode\\\": \\\"1\\\",\\n  \\\"applTime\\\": \\\"141401\\\",\\n  \\\"goodsName\\\": \\\"김치찌개\\\",\\n  \\\"CARD_CheckFlag\\\": \\\"1\\\",\\n  \\\"FlgNotiSendChk\\\": \\\"\\\",\\n  \\\"CARD_Code\\\": \\\"06\\\",\\n  \\\"CARD_BankCode\\\": \\\"04\\\",\\n  \\\"CARD_TerminalNum\\\": \\\"019058I000\\\",\\n  \\\"P_FN_NM\\\": \\\"국민계열\\\",\\n  \\\"buyerName\\\": \\\"테스트\\\",\\n  \\\"p_SubCnt\\\": \\\"\\\",\\n  \\\"applNum\\\": \\\"30029356\\\",\\n  \\\"resultMsg\\\": \\\"정상처리되었습니다.\\\",\\n  \\\"CARD_Interest\\\": \\\"0\\\",\\n  \\\"CARD_SrcCode\\\": \\\"K\\\",\\n  \\\"CARD_ApplPrice\\\": \\\"1000\\\",\\n  \\\"CARD_GWCode\\\": \\\"G\\\",\\n  \\\"custEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"CARD_Expire\\\": \\\"\\\",\\n  \\\"CARD_PurchaseName\\\": \\\"국민계열\\\",\\n  \\\"CARD_PRTC_CODE\\\": \\\"1\\\",\\n  \\\"payDevice\\\": \\\"PC\\\"\\n}\",\"receiptUrl\":\"https:\\/\\/iniweb.inicis.com\\/DefaultWebApp\\/mall\\/cr\\/cm\\/mCmReceipt_head.jsp?noTid=StdpayCARDINIpayTest20260402141401248092&noMethod=1\",\"disputes\":[]}','2026-04-02 14:14:02','2026-04-02 14:14:02'),
(10,19,46,'샘플1',77,'테스트','01000000000','guest@qrorder.com','OR-20260402-0002','pay_OR-20260402-0002_1775110257','inicis','card','KRW',1000.00,1000.00,0.00,1000.00,'PAID',NULL,NULL,'{\"status\":\"PAID\",\"id\":\"pay_OR-20260402-0002_1775110257\",\"transactionId\":\"019d4cd0-fdb5-ca90-d0d4-84f47a2bb2b3\",\"merchantId\":\"merchant-864d696c-9d23-4747-b01d-e2fc12dcf76d\",\"storeId\":\"store-63ecd6c6-7024-40a1-87d1-a616e1dc63d6\",\"method\":{\"type\":\"PaymentMethodCard\",\"card\":{\"publisher\":\"KOOKMIN_CARD\",\"issuer\":\"KOOKMIN_CARD\",\"brand\":\"MASTER\",\"type\":\"DEBIT\",\"ownerType\":\"PERSONAL\",\"bin\":\"527289\",\"name\":\"국민카드\",\"number\":\"527289\"},\"approvalNumber\":\"30029367\",\"installment\":{\"month\":0,\"isInterestFree\":false},\"pointUsed\":false},\"channel\":{\"type\":\"TEST\",\"id\":\"channel-id-6dd28f85-ace8-4dd2-bb5d-8246f02dd92a\",\"key\":\"channel-key-c1dca7d8-3431-4413-adb8-6e00c19b9b5b\",\"name\":\"QR 오더 테스트 결제\",\"pgProvider\":\"INICIS_V2\",\"pgMerchantId\":\"INIpayTest\"},\"version\":\"V2\",\"requestedAt\":\"2026-04-02T06:10:58.155662524Z\",\"updatedAt\":\"2026-04-02T06:11:46.533693634Z\",\"statusChangedAt\":\"2026-04-02T06:11:46Z\",\"orderName\":\"김치찌개\",\"amount\":{\"total\":1000,\"taxFree\":0,\"vat\":91,\"supply\":909,\"discount\":0,\"paid\":1000,\"cancelled\":0,\"cancelledTaxFree\":0},\"currency\":\"KRW\",\"customer\":{\"id\":\"port-customer-id-019d4cd0-fdbd-d9e7-e74b-97a9db5bd8be\",\"name\":\"테스트\",\"email\":\"guest@qrorder.com\",\"phoneNumber\":\"01000000000\"},\"promotionId\":\"\",\"isCulturalExpense\":false,\"paidAt\":\"2026-04-02T06:11:46Z\",\"pgTxId\":\"StdpayCARDINIpayTest20260402151145932367\",\"pgResponse\":\"{\\n  \\\"CARD_Quota\\\": \\\"00\\\",\\n  \\\"CARD_ClEvent\\\": \\\"\\\",\\n  \\\"CARD_CorpFlag\\\": \\\"0\\\",\\n  \\\"buyerTel\\\": \\\"01000000000\\\",\\n  \\\"parentEmail\\\": \\\"\\\",\\n  \\\"applDate\\\": \\\"20260402\\\",\\n  \\\"CARD_IssuerName\\\": \\\"국민카드\\\",\\n  \\\"buyerEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"OrgPrice\\\": \\\"\\\",\\n  \\\"p_Sub\\\": \\\"\\\",\\n  \\\"resultCode\\\": \\\"0000\\\",\\n  \\\"mid\\\": \\\"INIpayTest\\\",\\n  \\\"CARD_UsePoint\\\": \\\"\\\",\\n  \\\"CARD_Num\\\": \\\"527289*********9\\\",\\n  \\\"authSignature\\\": \\\"630531f2c0f9d4141d205523c0b57ed43e7e0e3f7b191aa364cecdbe408edb37\\\",\\n  \\\"tid\\\": \\\"StdpayCARDINIpayTest20260402151145932367\\\",\\n  \\\"EventCode\\\": \\\"\\\",\\n  \\\"goodName\\\": \\\"김치찌개\\\",\\n  \\\"TotPrice\\\": \\\"1000\\\",\\n  \\\"payMethod\\\": \\\"Card\\\",\\n  \\\"CARD_MemberNum\\\": \\\"\\\",\\n  \\\"MOID\\\": \\\"pay_OR-20260402-0002_1775110257\\\",\\n  \\\"CARD_Point\\\": \\\"\\\",\\n  \\\"currency\\\": \\\"WON\\\",\\n  \\\"CARD_PurchaseCode\\\": \\\"\\\",\\n  \\\"CARD_PrtcCode\\\": \\\"1\\\",\\n  \\\"applTime\\\": \\\"151146\\\",\\n  \\\"goodsName\\\": \\\"김치찌개\\\",\\n  \\\"CARD_CheckFlag\\\": \\\"1\\\",\\n  \\\"FlgNotiSendChk\\\": \\\"\\\",\\n  \\\"CARD_Code\\\": \\\"06\\\",\\n  \\\"CARD_BankCode\\\": \\\"04\\\",\\n  \\\"CARD_TerminalNum\\\": \\\"019058I000\\\",\\n  \\\"P_FN_NM\\\": \\\"국민계열\\\",\\n  \\\"buyerName\\\": \\\"테스트\\\",\\n  \\\"p_SubCnt\\\": \\\"\\\",\\n  \\\"applNum\\\": \\\"30029367\\\",\\n  \\\"resultMsg\\\": \\\"정상처리되었습니다.\\\",\\n  \\\"CARD_Interest\\\": \\\"0\\\",\\n  \\\"CARD_SrcCode\\\": \\\"K\\\",\\n  \\\"CARD_ApplPrice\\\": \\\"1000\\\",\\n  \\\"CARD_GWCode\\\": \\\"G\\\",\\n  \\\"custEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"CARD_Expire\\\": \\\"\\\",\\n  \\\"CARD_PurchaseName\\\": \\\"국민계열\\\",\\n  \\\"CARD_PRTC_CODE\\\": \\\"1\\\",\\n  \\\"payDevice\\\": \\\"PC\\\"\\n}\",\"receiptUrl\":\"https:\\/\\/iniweb.inicis.com\\/DefaultWebApp\\/mall\\/cr\\/cm\\/mCmReceipt_head.jsp?noTid=StdpayCARDINIpayTest20260402151145932367&noMethod=1\",\"disputes\":[]}','2026-04-02 15:11:46','2026-04-02 15:11:46'),
(11,20,46,'샘플1',77,'테스트','01000000000','guest@qrorder.com','OR-20260402-0003','pay_OR-20260402-0003_1775110377','inicis','card','KRW',1000.00,1000.00,0.00,1000.00,'PAID',NULL,NULL,'{\"status\":\"PAID\",\"id\":\"pay_OR-20260402-0003_1775110377\",\"transactionId\":\"019d4cd2-d10d-a785-968b-f10633063585\",\"merchantId\":\"merchant-864d696c-9d23-4747-b01d-e2fc12dcf76d\",\"storeId\":\"store-63ecd6c6-7024-40a1-87d1-a616e1dc63d6\",\"method\":{\"type\":\"PaymentMethodCard\",\"card\":{\"publisher\":\"KOOKMIN_CARD\",\"issuer\":\"KOOKMIN_CARD\",\"brand\":\"MASTER\",\"type\":\"DEBIT\",\"ownerType\":\"PERSONAL\",\"bin\":\"527289\",\"name\":\"국민카드\",\"number\":\"527289\"},\"approvalNumber\":\"30029378\",\"installment\":{\"month\":0,\"isInterestFree\":false},\"pointUsed\":false},\"channel\":{\"type\":\"TEST\",\"id\":\"channel-id-6dd28f85-ace8-4dd2-bb5d-8246f02dd92a\",\"key\":\"channel-key-c1dca7d8-3431-4413-adb8-6e00c19b9b5b\",\"name\":\"QR 오더 테스트 결제\",\"pgProvider\":\"INICIS_V2\",\"pgMerchantId\":\"INIpayTest\"},\"version\":\"V2\",\"requestedAt\":\"2026-04-02T06:12:57.790805232Z\",\"updatedAt\":\"2026-04-02T06:13:27.456821048Z\",\"statusChangedAt\":\"2026-04-02T06:13:27Z\",\"orderName\":\"김치찌개\",\"amount\":{\"total\":1000,\"taxFree\":0,\"vat\":91,\"supply\":909,\"discount\":0,\"paid\":1000,\"cancelled\":0,\"cancelledTaxFree\":0},\"currency\":\"KRW\",\"customer\":{\"id\":\"port-customer-id-019d4cd2-d114-838e-688e-7d57172409c2\",\"name\":\"테스트\",\"email\":\"guest@qrorder.com\",\"phoneNumber\":\"01000000000\"},\"promotionId\":\"\",\"isCulturalExpense\":false,\"paidAt\":\"2026-04-02T06:13:27Z\",\"pgTxId\":\"StdpayCARDINIpayTest20260402151326692782\",\"pgResponse\":\"{\\n  \\\"CARD_Quota\\\": \\\"00\\\",\\n  \\\"CARD_ClEvent\\\": \\\"\\\",\\n  \\\"CARD_CorpFlag\\\": \\\"0\\\",\\n  \\\"buyerTel\\\": \\\"01000000000\\\",\\n  \\\"parentEmail\\\": \\\"\\\",\\n  \\\"applDate\\\": \\\"20260402\\\",\\n  \\\"CARD_IssuerName\\\": \\\"국민카드\\\",\\n  \\\"buyerEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"OrgPrice\\\": \\\"\\\",\\n  \\\"p_Sub\\\": \\\"\\\",\\n  \\\"resultCode\\\": \\\"0000\\\",\\n  \\\"mid\\\": \\\"INIpayTest\\\",\\n  \\\"CARD_UsePoint\\\": \\\"\\\",\\n  \\\"CARD_Num\\\": \\\"527289*********6\\\",\\n  \\\"authSignature\\\": \\\"01426b2770b903c401c5f790d076f4e7df6a3a98801949b739b958098faad363\\\",\\n  \\\"tid\\\": \\\"StdpayCARDINIpayTest20260402151326692782\\\",\\n  \\\"EventCode\\\": \\\"\\\",\\n  \\\"goodName\\\": \\\"김치찌개\\\",\\n  \\\"TotPrice\\\": \\\"1000\\\",\\n  \\\"payMethod\\\": \\\"Card\\\",\\n  \\\"CARD_MemberNum\\\": \\\"\\\",\\n  \\\"MOID\\\": \\\"pay_OR-20260402-0003_1775110377\\\",\\n  \\\"CARD_Point\\\": \\\"\\\",\\n  \\\"currency\\\": \\\"WON\\\",\\n  \\\"CARD_PurchaseCode\\\": \\\"\\\",\\n  \\\"CARD_PrtcCode\\\": \\\"1\\\",\\n  \\\"applTime\\\": \\\"151327\\\",\\n  \\\"goodsName\\\": \\\"김치찌개\\\",\\n  \\\"CARD_CheckFlag\\\": \\\"1\\\",\\n  \\\"FlgNotiSendChk\\\": \\\"\\\",\\n  \\\"CARD_Code\\\": \\\"06\\\",\\n  \\\"CARD_BankCode\\\": \\\"04\\\",\\n  \\\"CARD_TerminalNum\\\": \\\"019058I000\\\",\\n  \\\"P_FN_NM\\\": \\\"국민계열\\\",\\n  \\\"buyerName\\\": \\\"테스트\\\",\\n  \\\"p_SubCnt\\\": \\\"\\\",\\n  \\\"applNum\\\": \\\"30029378\\\",\\n  \\\"resultMsg\\\": \\\"정상처리되었습니다.\\\",\\n  \\\"CARD_Interest\\\": \\\"0\\\",\\n  \\\"CARD_SrcCode\\\": \\\"K\\\",\\n  \\\"CARD_ApplPrice\\\": \\\"1000\\\",\\n  \\\"CARD_GWCode\\\": \\\"G\\\",\\n  \\\"custEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"CARD_Expire\\\": \\\"\\\",\\n  \\\"CARD_PurchaseName\\\": \\\"국민계열\\\",\\n  \\\"CARD_PRTC_CODE\\\": \\\"1\\\",\\n  \\\"payDevice\\\": \\\"PC\\\"\\n}\",\"receiptUrl\":\"https:\\/\\/iniweb.inicis.com\\/DefaultWebApp\\/mall\\/cr\\/cm\\/mCmReceipt_head.jsp?noTid=StdpayCARDINIpayTest20260402151326692782&noMethod=1\",\"disputes\":[]}','2026-04-02 15:13:28','2026-04-02 15:13:28'),
(12,21,46,'샘플1',77,'테스트','01000000000','guest@qrorder.com','OR-20260402-0004','pay_OR-20260402-0004_1775110520','inicis','card','KRW',2000.00,2000.00,0.00,2000.00,'PAID',NULL,NULL,'{\"status\":\"PAID\",\"id\":\"pay_OR-20260402-0004_1775110520\",\"transactionId\":\"019d4cd4-fe10-647a-2347-96f31d9e6a4a\",\"merchantId\":\"merchant-864d696c-9d23-4747-b01d-e2fc12dcf76d\",\"storeId\":\"store-63ecd6c6-7024-40a1-87d1-a616e1dc63d6\",\"method\":{\"type\":\"PaymentMethodCard\",\"card\":{\"publisher\":\"KOOKMIN_CARD\",\"issuer\":\"KOOKMIN_CARD\",\"brand\":\"MASTER\",\"type\":\"DEBIT\",\"ownerType\":\"PERSONAL\",\"bin\":\"527289\",\"name\":\"국민카드\",\"number\":\"527289\"},\"approvalNumber\":\"30029389\",\"installment\":{\"month\":0,\"isInterestFree\":false},\"pointUsed\":false},\"channel\":{\"type\":\"TEST\",\"id\":\"channel-id-6dd28f85-ace8-4dd2-bb5d-8246f02dd92a\",\"key\":\"channel-key-c1dca7d8-3431-4413-adb8-6e00c19b9b5b\",\"name\":\"QR 오더 테스트 결제\",\"pgProvider\":\"INICIS_V2\",\"pgMerchantId\":\"INIpayTest\"},\"version\":\"V2\",\"requestedAt\":\"2026-04-02T06:15:20.38728032Z\",\"updatedAt\":\"2026-04-02T06:15:43.171876983Z\",\"statusChangedAt\":\"2026-04-02T06:15:43Z\",\"orderName\":\"김치찌개\",\"amount\":{\"total\":2000,\"taxFree\":0,\"vat\":182,\"supply\":1818,\"discount\":0,\"paid\":2000,\"cancelled\":0,\"cancelledTaxFree\":0},\"currency\":\"KRW\",\"customer\":{\"id\":\"port-customer-id-019d4cd4-fe1a-59d5-06d2-0a4d259be4fd\",\"name\":\"테스트\",\"email\":\"guest@qrorder.com\",\"phoneNumber\":\"01000000000\"},\"promotionId\":\"\",\"isCulturalExpense\":false,\"paidAt\":\"2026-04-02T06:15:43Z\",\"pgTxId\":\"StdpayCARDINIpayTest20260402151542447326\",\"pgResponse\":\"{\\n  \\\"CARD_Quota\\\": \\\"00\\\",\\n  \\\"CARD_ClEvent\\\": \\\"\\\",\\n  \\\"CARD_CorpFlag\\\": \\\"0\\\",\\n  \\\"buyerTel\\\": \\\"01000000000\\\",\\n  \\\"parentEmail\\\": \\\"\\\",\\n  \\\"applDate\\\": \\\"20260402\\\",\\n  \\\"CARD_IssuerName\\\": \\\"국민카드\\\",\\n  \\\"buyerEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"OrgPrice\\\": \\\"\\\",\\n  \\\"p_Sub\\\": \\\"\\\",\\n  \\\"resultCode\\\": \\\"0000\\\",\\n  \\\"mid\\\": \\\"INIpayTest\\\",\\n  \\\"CARD_UsePoint\\\": \\\"\\\",\\n  \\\"CARD_Num\\\": \\\"527289*********2\\\",\\n  \\\"authSignature\\\": \\\"04b10eaad881163670ea5c9f7f88f5188fa20bb2fb7484f95ffd7befc98355d1\\\",\\n  \\\"tid\\\": \\\"StdpayCARDINIpayTest20260402151542447326\\\",\\n  \\\"EventCode\\\": \\\"\\\",\\n  \\\"goodName\\\": \\\"김치찌개\\\",\\n  \\\"TotPrice\\\": \\\"2000\\\",\\n  \\\"payMethod\\\": \\\"Card\\\",\\n  \\\"CARD_MemberNum\\\": \\\"\\\",\\n  \\\"MOID\\\": \\\"pay_OR-20260402-0004_1775110520\\\",\\n  \\\"CARD_Point\\\": \\\"\\\",\\n  \\\"currency\\\": \\\"WON\\\",\\n  \\\"CARD_PurchaseCode\\\": \\\"\\\",\\n  \\\"CARD_PrtcCode\\\": \\\"1\\\",\\n  \\\"applTime\\\": \\\"151543\\\",\\n  \\\"goodsName\\\": \\\"김치찌개\\\",\\n  \\\"CARD_CheckFlag\\\": \\\"1\\\",\\n  \\\"FlgNotiSendChk\\\": \\\"\\\",\\n  \\\"CARD_Code\\\": \\\"06\\\",\\n  \\\"CARD_BankCode\\\": \\\"04\\\",\\n  \\\"CARD_TerminalNum\\\": \\\"019058I000\\\",\\n  \\\"P_FN_NM\\\": \\\"국민계열\\\",\\n  \\\"buyerName\\\": \\\"테스트\\\",\\n  \\\"p_SubCnt\\\": \\\"\\\",\\n  \\\"applNum\\\": \\\"30029389\\\",\\n  \\\"resultMsg\\\": \\\"정상처리되었습니다.\\\",\\n  \\\"CARD_Interest\\\": \\\"0\\\",\\n  \\\"CARD_SrcCode\\\": \\\"K\\\",\\n  \\\"CARD_ApplPrice\\\": \\\"2000\\\",\\n  \\\"CARD_GWCode\\\": \\\"G\\\",\\n  \\\"custEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"CARD_Expire\\\": \\\"\\\",\\n  \\\"CARD_PurchaseName\\\": \\\"국민계열\\\",\\n  \\\"CARD_PRTC_CODE\\\": \\\"1\\\",\\n  \\\"payDevice\\\": \\\"PC\\\"\\n}\",\"receiptUrl\":\"https:\\/\\/iniweb.inicis.com\\/DefaultWebApp\\/mall\\/cr\\/cm\\/mCmReceipt_head.jsp?noTid=StdpayCARDINIpayTest20260402151542447326&noMethod=1\",\"disputes\":[]}','2026-04-02 15:15:43','2026-04-02 15:15:43'),
(13,22,46,'샘플1',77,'테스트','01000000000','guest@qrorder.com','OR-20260402-0005','pay_OR-20260402-0005_1775111091','inicis','card','KRW',2000.00,2000.00,0.00,2000.00,'PAID',NULL,NULL,'{\"status\":\"PAID\",\"id\":\"pay_OR-20260402-0005_1775111091\",\"transactionId\":\"019d4cdd-b662-ea74-9f01-a4c50d3bac77\",\"merchantId\":\"merchant-864d696c-9d23-4747-b01d-e2fc12dcf76d\",\"storeId\":\"store-63ecd6c6-7024-40a1-87d1-a616e1dc63d6\",\"method\":{\"type\":\"PaymentMethodCard\",\"card\":{\"publisher\":\"KOOKMIN_CARD\",\"issuer\":\"KOOKMIN_CARD\",\"brand\":\"MASTER\",\"type\":\"DEBIT\",\"ownerType\":\"PERSONAL\",\"bin\":\"527289\",\"name\":\"국민카드\",\"number\":\"527289\"},\"approvalNumber\":\"30029390\",\"installment\":{\"month\":0,\"isInterestFree\":false},\"pointUsed\":false},\"channel\":{\"type\":\"TEST\",\"id\":\"channel-id-6dd28f85-ace8-4dd2-bb5d-8246f02dd92a\",\"key\":\"channel-key-c1dca7d8-3431-4413-adb8-6e00c19b9b5b\",\"name\":\"QR 오더 테스트 결제\",\"pgProvider\":\"INICIS_V2\",\"pgMerchantId\":\"INIpayTest\"},\"version\":\"V2\",\"requestedAt\":\"2026-04-02T06:24:51.857844719Z\",\"updatedAt\":\"2026-04-02T06:25:15.966990778Z\",\"statusChangedAt\":\"2026-04-02T06:25:15Z\",\"orderName\":\"김치찌개\",\"amount\":{\"total\":2000,\"taxFree\":0,\"vat\":182,\"supply\":1818,\"discount\":0,\"paid\":2000,\"cancelled\":0,\"cancelledTaxFree\":0},\"currency\":\"KRW\",\"customer\":{\"id\":\"port-customer-id-019d4cdd-b66a-3cc5-2349-d9b9c84a057c\",\"name\":\"테스트\",\"email\":\"guest@qrorder.com\",\"phoneNumber\":\"01000000000\"},\"promotionId\":\"\",\"isCulturalExpense\":false,\"paidAt\":\"2026-04-02T06:25:15Z\",\"pgTxId\":\"StdpayCARDINIpayTest20260402152515164076\",\"pgResponse\":\"{\\n  \\\"CARD_Quota\\\": \\\"00\\\",\\n  \\\"CARD_ClEvent\\\": \\\"\\\",\\n  \\\"CARD_CorpFlag\\\": \\\"0\\\",\\n  \\\"buyerTel\\\": \\\"01000000000\\\",\\n  \\\"parentEmail\\\": \\\"\\\",\\n  \\\"applDate\\\": \\\"20260402\\\",\\n  \\\"CARD_IssuerName\\\": \\\"국민카드\\\",\\n  \\\"buyerEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"OrgPrice\\\": \\\"\\\",\\n  \\\"p_Sub\\\": \\\"\\\",\\n  \\\"resultCode\\\": \\\"0000\\\",\\n  \\\"mid\\\": \\\"INIpayTest\\\",\\n  \\\"CARD_UsePoint\\\": \\\"\\\",\\n  \\\"CARD_Num\\\": \\\"527289*********8\\\",\\n  \\\"authSignature\\\": \\\"af37116ea5ed9b1da453ab0087871f293b985a2c7e5c6e6bbfbfb0cd8ae67af1\\\",\\n  \\\"tid\\\": \\\"StdpayCARDINIpayTest20260402152515164076\\\",\\n  \\\"EventCode\\\": \\\"\\\",\\n  \\\"goodName\\\": \\\"김치찌개\\\",\\n  \\\"TotPrice\\\": \\\"2000\\\",\\n  \\\"payMethod\\\": \\\"Card\\\",\\n  \\\"CARD_MemberNum\\\": \\\"\\\",\\n  \\\"MOID\\\": \\\"pay_OR-20260402-0005_1775111091\\\",\\n  \\\"CARD_Point\\\": \\\"\\\",\\n  \\\"currency\\\": \\\"WON\\\",\\n  \\\"CARD_PurchaseCode\\\": \\\"\\\",\\n  \\\"CARD_PrtcCode\\\": \\\"1\\\",\\n  \\\"applTime\\\": \\\"152515\\\",\\n  \\\"goodsName\\\": \\\"김치찌개\\\",\\n  \\\"CARD_CheckFlag\\\": \\\"1\\\",\\n  \\\"FlgNotiSendChk\\\": \\\"\\\",\\n  \\\"CARD_Code\\\": \\\"06\\\",\\n  \\\"CARD_BankCode\\\": \\\"04\\\",\\n  \\\"CARD_TerminalNum\\\": \\\"019058I000\\\",\\n  \\\"P_FN_NM\\\": \\\"국민계열\\\",\\n  \\\"buyerName\\\": \\\"테스트\\\",\\n  \\\"p_SubCnt\\\": \\\"\\\",\\n  \\\"applNum\\\": \\\"30029390\\\",\\n  \\\"resultMsg\\\": \\\"정상처리되었습니다.\\\",\\n  \\\"CARD_Interest\\\": \\\"0\\\",\\n  \\\"CARD_SrcCode\\\": \\\"K\\\",\\n  \\\"CARD_ApplPrice\\\": \\\"2000\\\",\\n  \\\"CARD_GWCode\\\": \\\"G\\\",\\n  \\\"custEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"CARD_Expire\\\": \\\"\\\",\\n  \\\"CARD_PurchaseName\\\": \\\"국민계열\\\",\\n  \\\"CARD_PRTC_CODE\\\": \\\"1\\\",\\n  \\\"payDevice\\\": \\\"PC\\\"\\n}\",\"receiptUrl\":\"https:\\/\\/iniweb.inicis.com\\/DefaultWebApp\\/mall\\/cr\\/cm\\/mCmReceipt_head.jsp?noTid=StdpayCARDINIpayTest20260402152515164076&noMethod=1\",\"disputes\":[]}','2026-04-02 15:25:16','2026-04-02 15:25:16'),
(14,23,46,'샘플1',77,'테스트','01000000000','guest@qrorder.com','OR-20260402-0006','pay_OR-20260402-0006_1775111179','inicis','card','KRW',2000.00,2000.00,0.00,2000.00,'PAID',NULL,NULL,'{\"status\":\"PAID\",\"id\":\"pay_OR-20260402-0006_1775111179\",\"transactionId\":\"019d4cdf-0d1b-9c43-d727-e0c70fb9e57c\",\"merchantId\":\"merchant-864d696c-9d23-4747-b01d-e2fc12dcf76d\",\"storeId\":\"store-63ecd6c6-7024-40a1-87d1-a616e1dc63d6\",\"method\":{\"type\":\"PaymentMethodCard\",\"card\":{\"publisher\":\"KOOKMIN_CARD\",\"issuer\":\"KOOKMIN_CARD\",\"brand\":\"MASTER\",\"type\":\"DEBIT\",\"ownerType\":\"PERSONAL\",\"bin\":\"527289\",\"name\":\"국민카드\",\"number\":\"527289\"},\"approvalNumber\":\"30029402\",\"installment\":{\"month\":0,\"isInterestFree\":false},\"pointUsed\":false},\"channel\":{\"type\":\"TEST\",\"id\":\"channel-id-6dd28f85-ace8-4dd2-bb5d-8246f02dd92a\",\"key\":\"channel-key-c1dca7d8-3431-4413-adb8-6e00c19b9b5b\",\"name\":\"QR 오더 테스트 결제\",\"pgProvider\":\"INICIS_V2\",\"pgMerchantId\":\"INIpayTest\"},\"version\":\"V2\",\"requestedAt\":\"2026-04-02T06:26:19.595214578Z\",\"updatedAt\":\"2026-04-02T06:26:43.580540528Z\",\"statusChangedAt\":\"2026-04-02T06:26:43Z\",\"orderName\":\"김치찌개\",\"amount\":{\"total\":2000,\"taxFree\":0,\"vat\":182,\"supply\":1818,\"discount\":0,\"paid\":2000,\"cancelled\":0,\"cancelledTaxFree\":0},\"currency\":\"KRW\",\"customer\":{\"id\":\"port-customer-id-019d4cdf-0d22-6996-dd2f-679b4b255df5\",\"name\":\"테스트\",\"email\":\"guest@qrorder.com\",\"phoneNumber\":\"01000000000\"},\"promotionId\":\"\",\"isCulturalExpense\":false,\"paidAt\":\"2026-04-02T06:26:43Z\",\"pgTxId\":\"StdpayCARDINIpayTest20260402152642824428\",\"pgResponse\":\"{\\n  \\\"CARD_Quota\\\": \\\"00\\\",\\n  \\\"CARD_ClEvent\\\": \\\"\\\",\\n  \\\"CARD_CorpFlag\\\": \\\"0\\\",\\n  \\\"buyerTel\\\": \\\"01000000000\\\",\\n  \\\"parentEmail\\\": \\\"\\\",\\n  \\\"applDate\\\": \\\"20260402\\\",\\n  \\\"CARD_IssuerName\\\": \\\"국민카드\\\",\\n  \\\"buyerEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"OrgPrice\\\": \\\"\\\",\\n  \\\"p_Sub\\\": \\\"\\\",\\n  \\\"resultCode\\\": \\\"0000\\\",\\n  \\\"mid\\\": \\\"INIpayTest\\\",\\n  \\\"CARD_UsePoint\\\": \\\"\\\",\\n  \\\"CARD_Num\\\": \\\"527289*********7\\\",\\n  \\\"authSignature\\\": \\\"0c6f4cec4b91f622b94ef370fdc09d3a35939e0ebd1cd3b2b0dd3e3b0437f4f1\\\",\\n  \\\"tid\\\": \\\"StdpayCARDINIpayTest20260402152642824428\\\",\\n  \\\"EventCode\\\": \\\"\\\",\\n  \\\"goodName\\\": \\\"김치찌개\\\",\\n  \\\"TotPrice\\\": \\\"2000\\\",\\n  \\\"payMethod\\\": \\\"Card\\\",\\n  \\\"CARD_MemberNum\\\": \\\"\\\",\\n  \\\"MOID\\\": \\\"pay_OR-20260402-0006_1775111179\\\",\\n  \\\"CARD_Point\\\": \\\"\\\",\\n  \\\"currency\\\": \\\"WON\\\",\\n  \\\"CARD_PurchaseCode\\\": \\\"\\\",\\n  \\\"CARD_PrtcCode\\\": \\\"1\\\",\\n  \\\"applTime\\\": \\\"152643\\\",\\n  \\\"goodsName\\\": \\\"김치찌개\\\",\\n  \\\"CARD_CheckFlag\\\": \\\"1\\\",\\n  \\\"FlgNotiSendChk\\\": \\\"\\\",\\n  \\\"CARD_Code\\\": \\\"06\\\",\\n  \\\"CARD_BankCode\\\": \\\"04\\\",\\n  \\\"CARD_TerminalNum\\\": \\\"019058I000\\\",\\n  \\\"P_FN_NM\\\": \\\"국민계열\\\",\\n  \\\"buyerName\\\": \\\"테스트\\\",\\n  \\\"p_SubCnt\\\": \\\"\\\",\\n  \\\"applNum\\\": \\\"30029402\\\",\\n  \\\"resultMsg\\\": \\\"정상처리되었습니다.\\\",\\n  \\\"CARD_Interest\\\": \\\"0\\\",\\n  \\\"CARD_SrcCode\\\": \\\"K\\\",\\n  \\\"CARD_ApplPrice\\\": \\\"2000\\\",\\n  \\\"CARD_GWCode\\\": \\\"G\\\",\\n  \\\"custEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"CARD_Expire\\\": \\\"\\\",\\n  \\\"CARD_PurchaseName\\\": \\\"국민계열\\\",\\n  \\\"CARD_PRTC_CODE\\\": \\\"1\\\",\\n  \\\"payDevice\\\": \\\"PC\\\"\\n}\",\"receiptUrl\":\"https:\\/\\/iniweb.inicis.com\\/DefaultWebApp\\/mall\\/cr\\/cm\\/mCmReceipt_head.jsp?noTid=StdpayCARDINIpayTest20260402152642824428&noMethod=1\",\"disputes\":[]}','2026-04-02 15:26:44','2026-04-02 15:26:44'),
(15,24,46,'서울원조집',77,'테스트','01000000000','guest@qrorder.com','OR-20260403-0001','pay_OR-20260403-0001_1775174739','inicis','card','KRW',4000.00,4000.00,0.00,4000.00,'PAID',NULL,NULL,'{\"status\":\"PAID\",\"id\":\"pay_OR-20260403-0001_1775174739\",\"transactionId\":\"019d50a8-e786-3dd1-3032-f05a97e8d3f1\",\"merchantId\":\"merchant-864d696c-9d23-4747-b01d-e2fc12dcf76d\",\"storeId\":\"store-63ecd6c6-7024-40a1-87d1-a616e1dc63d6\",\"method\":{\"type\":\"PaymentMethodEasyPay\",\"provider\":\"KAKAOPAY\",\"easyPayMethod\":{\"type\":\"PaymentMethodEasyPayMethodCharge\"}},\"channel\":{\"type\":\"TEST\",\"id\":\"channel-id-6dd28f85-ace8-4dd2-bb5d-8246f02dd92a\",\"key\":\"channel-key-c1dca7d8-3431-4413-adb8-6e00c19b9b5b\",\"name\":\"QR 오더 테스트 결제\",\"pgProvider\":\"INICIS_V2\",\"pgMerchantId\":\"INIpayTest\"},\"version\":\"V2\",\"requestedAt\":\"2026-04-03T00:05:39.897025227Z\",\"updatedAt\":\"2026-04-03T00:09:40.841501798Z\",\"statusChangedAt\":\"2026-04-03T00:09:40Z\",\"orderName\":\"김치찌개\",\"amount\":{\"total\":4000,\"taxFree\":0,\"vat\":364,\"supply\":3636,\"discount\":0,\"paid\":4000,\"cancelled\":0,\"cancelledTaxFree\":0},\"currency\":\"KRW\",\"customer\":{\"id\":\"port-customer-id-019d50a8-e78d-e748-f072-85f25a672d37\",\"name\":\"테스트\",\"email\":\"guest@qrorder.com\",\"phoneNumber\":\"01000000000\"},\"promotionId\":\"\",\"isCulturalExpense\":false,\"paidAt\":\"2026-04-03T00:09:40Z\",\"pgTxId\":\"StdpayCARDINIpayTest20260403090939814718\",\"pgResponse\":\"{\\n  \\\"CARD_Quota\\\": \\\"00\\\",\\n  \\\"CARD_ClEvent\\\": \\\"\\\",\\n  \\\"CARD_CorpFlag\\\": \\\"9\\\",\\n  \\\"buyerTel\\\": \\\"01000000000\\\",\\n  \\\"parentEmail\\\": \\\"\\\",\\n  \\\"applDate\\\": \\\"20260403\\\",\\n  \\\"CARD_IssuerName\\\": \\\"카카오머니\\\",\\n  \\\"buyerEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"OrgPrice\\\": \\\"\\\",\\n  \\\"p_Sub\\\": \\\"\\\",\\n  \\\"resultCode\\\": \\\"0000\\\",\\n  \\\"mid\\\": \\\"INIpayTest\\\",\\n  \\\"CARD_UsePoint\\\": \\\"\\\",\\n  \\\"CARD_Num\\\": \\\"*********\\\",\\n  \\\"authSignature\\\": \\\"f333a5d29550517da4905c5b6ff289898246768aea03f26dee8cfc207dbb2ae6\\\",\\n  \\\"tid\\\": \\\"StdpayCARDINIpayTest20260403090939814718\\\",\\n  \\\"EventCode\\\": \\\"\\\",\\n  \\\"goodName\\\": \\\"김치찌개\\\",\\n  \\\"TotPrice\\\": \\\"4000\\\",\\n  \\\"payMethod\\\": \\\"Card\\\",\\n  \\\"CARD_MemberNum\\\": \\\"\\\",\\n  \\\"MOID\\\": \\\"pay_OR-20260403-0001_1775174739\\\",\\n  \\\"CARD_Point\\\": \\\"\\\",\\n  \\\"currency\\\": \\\"WON\\\",\\n  \\\"CARD_PurchaseCode\\\": \\\"\\\",\\n  \\\"CARD_PrtcCode\\\": \\\"1\\\",\\n  \\\"applTime\\\": \\\"090940\\\",\\n  \\\"goodsName\\\": \\\"김치찌개\\\",\\n  \\\"CARD_CheckFlag\\\": \\\"1\\\",\\n  \\\"FlgNotiSendChk\\\": \\\"\\\",\\n  \\\"CARD_Code\\\": \\\"97\\\",\\n  \\\"CARD_BankCode\\\": \\\"97\\\",\\n  \\\"CARD_TerminalNum\\\": \\\"\\\",\\n  \\\"P_FN_NM\\\": \\\"카카오머니\\\",\\n  \\\"buyerName\\\": \\\"테스트\\\",\\n  \\\"p_SubCnt\\\": \\\"\\\",\\n  \\\"applNum\\\": \\\"\\\",\\n  \\\"resultMsg\\\": \\\"정상처리되었습니다.\\\",\\n  \\\"CARD_Interest\\\": \\\"0\\\",\\n  \\\"CARD_SrcCode\\\": \\\"O\\\",\\n  \\\"CARD_ApplPrice\\\": \\\"4000\\\",\\n  \\\"CARD_GWCode\\\": \\\"K\\\",\\n  \\\"custEmail\\\": \\\"\\\",\\n  \\\"CARD_Expire\\\": \\\"\\\",\\n  \\\"CARD_PurchaseName\\\": \\\"카카오머니\\\",\\n  \\\"CARD_PRTC_CODE\\\": \\\"1\\\",\\n  \\\"payDevice\\\": \\\"PC\\\"\\n}\",\"receiptUrl\":\"https:\\/\\/iniweb.inicis.com\\/DefaultWebApp\\/mall\\/cr\\/cm\\/mCmReceipt_head.jsp?noTid=StdpayCARDINIpayTest20260403090939814718&noMethod=1\",\"disputes\":[]}','2026-04-03 09:09:41','2026-04-03 09:09:41'),
(16,25,48,'소담한하루',NULL,'비회원','010-0000-0000','guest@qrorder.com','OR-20260407-0001','pay_OR-20260407-0001_1775542659','inicis','card','KRW',10000.00,10000.00,0.00,10000.00,'PAID',NULL,NULL,'{\"status\":\"PAID\",\"id\":\"pay_OR-20260407-0001_1775542659\",\"transactionId\":\"019d6696-ea2d-521f-cbfd-8522bec86701\",\"merchantId\":\"merchant-864d696c-9d23-4747-b01d-e2fc12dcf76d\",\"storeId\":\"store-63ecd6c6-7024-40a1-87d1-a616e1dc63d6\",\"method\":{\"type\":\"PaymentMethodEasyPay\",\"provider\":\"NAVERPAY\",\"easyPayMethod\":{\"type\":\"PaymentMethodEasyPayMethodCharge\"}},\"channel\":{\"type\":\"TEST\",\"id\":\"channel-id-6dd28f85-ace8-4dd2-bb5d-8246f02dd92a\",\"key\":\"channel-key-c1dca7d8-3431-4413-adb8-6e00c19b9b5b\",\"name\":\"QR 오더 테스트 결제\",\"pgProvider\":\"INICIS_V2\",\"pgMerchantId\":\"INIpayTest\"},\"version\":\"V2\",\"requestedAt\":\"2026-04-07T06:17:39.67535433Z\",\"updatedAt\":\"2026-04-07T06:18:37.379575092Z\",\"statusChangedAt\":\"2026-04-07T06:18:36Z\",\"orderName\":\"왕돈까스\",\"amount\":{\"total\":10000,\"taxFree\":0,\"vat\":909,\"supply\":9091,\"discount\":0,\"paid\":10000,\"cancelled\":0,\"cancelledTaxFree\":0},\"currency\":\"KRW\",\"customer\":{\"id\":\"port-customer-id-019d6696-ea35-a7e4-39b9-2e1c0364db41\",\"name\":\"고객\",\"email\":\"guest@qrorder.com\",\"phoneNumber\":\"01000000000\"},\"promotionId\":\"\",\"isCulturalExpense\":false,\"paidAt\":\"2026-04-07T06:18:36Z\",\"pgTxId\":\"INIMX_CARDINIpayTest20260407151836666204\",\"pgResponse\":\"P_STATUS=00&P_AUTH_DT=20260407151836&P_AUTH_NO=&P_RMESG1=성공적으로 처리 하였습니다.&P_RMESG2=00&P_TID=INIMX_CARDINIpayTest20260407151836666204&P_FN_CD1=91&P_AMT=10000&P_TYPE=CARD&P_UNAME=고객&P_MID=INIpayTest&P_OID=pay_OR-20260407-0001_1775542659&P_NOTI={\\\"P_OID\\\":\\\"pay_OR-20260407-0001_1775542659\\\"}&P_NEXT_URL=https:\\/\\/checkout-service.prod.iamport.co\\/api\\/complete\\/PG_PROVIDER_INICIS_V2\\/v2?paymentId=pay_OR-20260407-0001_1775542659&pgProvider=PG_PROVIDER_INICIS_V2&redirectUrl=https%253A%252F%252Fbarorez.com%252Fapp%252Fcallback%252Fportone_redirect.php&storeId=store-63ecd6c6-7024-40a1-87d1-a616e1dc63d6&transactionType=PAYMENT&txId=019d6696-ea2d-521f-cbfd-8522bec86701&windowType=REDIRECTION&P_MNAME=&P_NOTEURL=&P_CARD_MEMBER_NUM=&P_CARD_NUM=*********&P_CARD_ISSUER_CODE=91&P_CARD_PURCHASE_CODE=91&P_CARD_PRTC_CODE=1&P_CARD_INTEREST=0&P_CARD_CHECKFLAG=&P_CARD_ISSUER_NAME=&P_CARD_PURCHASE_NAME=네이버포인트&P_FN_NM=네이버포인트&CARD_CorpFlag=9&P_SRC_CODE=I&P_MERCHANT_RESERVED=dXNlcG9pbnQ9MTAwMDAm&P_CARD_APPLPRICE=10000&P_CARD_USEPOINT=10000&NAVERPOINT_CSHRApplYN=Y&NAVERPOINT_UseFreePoint=0&NAVERPOINT_CSHRApplAmt=10000&EventCode=\",\"receiptUrl\":\"https:\\/\\/iniweb.inicis.com\\/DefaultWebApp\\/mall\\/cr\\/cm\\/mCmReceipt_head.jsp?noTid=INIMX_CARDINIpayTest20260407151836666204&noMethod=1\",\"disputes\":[]}','2026-04-07 15:18:38','2026-04-07 15:18:38'),
(17,26,48,'소담한하루',NULL,'비회원','010-0000-0000','guest@qrorder.com','OR-20260407-0002','pay_OR-20260407-0002_1775547614','inicis','card','KRW',12000.00,12000.00,0.00,12000.00,'PAID',NULL,NULL,'{\"status\":\"PAID\",\"id\":\"pay_OR-20260407-0002_1775547614\",\"transactionId\":\"019d66e2-87e2-cb3b-5987-d9a81ca7e17c\",\"merchantId\":\"merchant-864d696c-9d23-4747-b01d-e2fc12dcf76d\",\"storeId\":\"store-63ecd6c6-7024-40a1-87d1-a616e1dc63d6\",\"method\":{\"type\":\"PaymentMethodEasyPay\",\"provider\":\"NAVERPAY\",\"easyPayMethod\":{\"type\":\"PaymentMethodEasyPayMethodCharge\"}},\"channel\":{\"type\":\"TEST\",\"id\":\"channel-id-6dd28f85-ace8-4dd2-bb5d-8246f02dd92a\",\"key\":\"channel-key-c1dca7d8-3431-4413-adb8-6e00c19b9b5b\",\"name\":\"QR 오더 테스트 결제\",\"pgProvider\":\"INICIS_V2\",\"pgMerchantId\":\"INIpayTest\"},\"version\":\"V2\",\"requestedAt\":\"2026-04-07T07:40:15.254074242Z\",\"updatedAt\":\"2026-04-07T07:40:34.935044832Z\",\"statusChangedAt\":\"2026-04-07T07:40:34Z\",\"orderName\":\"제육볶음\",\"amount\":{\"total\":12000,\"taxFree\":0,\"vat\":1091,\"supply\":10909,\"discount\":0,\"paid\":12000,\"cancelled\":0,\"cancelledTaxFree\":0},\"currency\":\"KRW\",\"customer\":{\"id\":\"port-customer-id-019d66e2-87eb-2925-c4d2-2d7ac643797f\",\"name\":\"고객\",\"email\":\"guest@qrorder.com\",\"phoneNumber\":\"01000000000\"},\"promotionId\":\"\",\"isCulturalExpense\":false,\"paidAt\":\"2026-04-07T07:40:34Z\",\"pgTxId\":\"INIMX_CARDINIpayTest20260407164034256109\",\"pgResponse\":\"P_STATUS=00&P_AUTH_DT=20260407164034&P_AUTH_NO=&P_RMESG1=성공적으로 처리 하였습니다.&P_RMESG2=00&P_TID=INIMX_CARDINIpayTest20260407164034256109&P_FN_CD1=91&P_AMT=12000&P_TYPE=CARD&P_UNAME=고객&P_MID=INIpayTest&P_OID=pay_OR-20260407-0002_1775547614&P_NOTI={\\\"P_OID\\\":\\\"pay_OR-20260407-0002_1775547614\\\"}&P_NEXT_URL=https:\\/\\/checkout-service.prod.iamport.co\\/api\\/complete\\/PG_PROVIDER_INICIS_V2\\/v2?paymentId=pay_OR-20260407-0002_1775547614&pgProvider=PG_PROVIDER_INICIS_V2&redirectUrl=https%253A%252F%252Fbarorez.com%252Fapp%252Fcallback%252Fportone_redirect.php&storeId=store-63ecd6c6-7024-40a1-87d1-a616e1dc63d6&transactionType=PAYMENT&txId=019d66e2-87e2-cb3b-5987-d9a81ca7e17c&windowType=REDIRECTION&P_MNAME=&P_NOTEURL=&P_CARD_MEMBER_NUM=&P_CARD_NUM=*********&P_CARD_ISSUER_CODE=91&P_CARD_PURCHASE_CODE=91&P_CARD_PRTC_CODE=1&P_CARD_INTEREST=0&P_CARD_CHECKFLAG=&P_CARD_ISSUER_NAME=&P_CARD_PURCHASE_NAME=네이버포인트&P_FN_NM=네이버포인트&CARD_CorpFlag=9&P_SRC_CODE=I&P_MERCHANT_RESERVED=dXNlcG9pbnQ9MTIwMDAm&P_CARD_APPLPRICE=12000&P_CARD_USEPOINT=12000&NAVERPOINT_CSHRApplYN=Y&NAVERPOINT_UseFreePoint=0&NAVERPOINT_CSHRApplAmt=12000&EventCode=\",\"receiptUrl\":\"https:\\/\\/iniweb.inicis.com\\/DefaultWebApp\\/mall\\/cr\\/cm\\/mCmReceipt_head.jsp?noTid=INIMX_CARDINIpayTest20260407164034256109&noMethod=1\",\"disputes\":[]}','2026-04-07 16:40:35','2026-04-07 16:40:35'),
(18,27,46,'서울원조집',75,'kakao47906876','010-0000-0000','guest@qrorder.com','OR-20260408-0001','pay_OR-20260408-0001_1775614307','inicis','card','KRW',1000.00,1000.00,0.00,1000.00,'PAID',NULL,NULL,'{\"status\":\"PAID\",\"id\":\"pay_OR-20260408-0001_1775614307\",\"transactionId\":\"019d6adc-2f74-282f-7647-5461a7f2ae99\",\"merchantId\":\"merchant-864d696c-9d23-4747-b01d-e2fc12dcf76d\",\"storeId\":\"store-63ecd6c6-7024-40a1-87d1-a616e1dc63d6\",\"method\":{\"type\":\"PaymentMethodCard\",\"card\":{\"publisher\":\"KOOKMIN_CARD\",\"issuer\":\"KOOKMIN_CARD\",\"brand\":\"MASTER\",\"type\":\"DEBIT\",\"ownerType\":\"PERSONAL\",\"bin\":\"527289\",\"name\":\"국민카드\",\"number\":\"527289\"},\"approvalNumber\":\"30029716\",\"installment\":{\"month\":0,\"isInterestFree\":false},\"pointUsed\":false},\"channel\":{\"type\":\"TEST\",\"id\":\"channel-id-6dd28f85-ace8-4dd2-bb5d-8246f02dd92a\",\"key\":\"channel-key-c1dca7d8-3431-4413-adb8-6e00c19b9b5b\",\"name\":\"QR 오더 테스트 결제\",\"pgProvider\":\"INICIS_V2\",\"pgMerchantId\":\"INIpayTest\"},\"version\":\"V2\",\"requestedAt\":\"2026-04-08T02:11:48.271471824Z\",\"updatedAt\":\"2026-04-08T02:12:26.655822068Z\",\"statusChangedAt\":\"2026-04-08T02:12:26Z\",\"orderName\":\"1996년 보양사골 대치칼국수\",\"amount\":{\"total\":1000,\"taxFree\":0,\"vat\":91,\"supply\":909,\"discount\":0,\"paid\":1000,\"cancelled\":0,\"cancelledTaxFree\":0},\"currency\":\"KRW\",\"customer\":{\"id\":\"port-customer-id-019d6adc-2f7b-f257-5d7b-36b6e009c14d\",\"name\":\"kakao47906876\",\"email\":\"guest@qrorder.com\",\"phoneNumber\":\"01000000000\"},\"promotionId\":\"\",\"isCulturalExpense\":false,\"paidAt\":\"2026-04-08T02:12:26Z\",\"pgTxId\":\"StdpayCARDINIpayTest20260408111225859328\",\"pgResponse\":\"{\\n  \\\"CARD_Quota\\\": \\\"00\\\",\\n  \\\"CARD_ClEvent\\\": \\\"\\\",\\n  \\\"CARD_CorpFlag\\\": \\\"0\\\",\\n  \\\"buyerTel\\\": \\\"01000000000\\\",\\n  \\\"parentEmail\\\": \\\"\\\",\\n  \\\"applDate\\\": \\\"20260408\\\",\\n  \\\"CARD_IssuerName\\\": \\\"국민카드\\\",\\n  \\\"buyerEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"OrgPrice\\\": \\\"\\\",\\n  \\\"p_Sub\\\": \\\"\\\",\\n  \\\"resultCode\\\": \\\"0000\\\",\\n  \\\"mid\\\": \\\"INIpayTest\\\",\\n  \\\"CARD_UsePoint\\\": \\\"\\\",\\n  \\\"CARD_Num\\\": \\\"527289*********3\\\",\\n  \\\"authSignature\\\": \\\"0b005e626ac69ecfb42a676c1fae1c4b603baa15df3177993cf4e04e82fa96b2\\\",\\n  \\\"tid\\\": \\\"StdpayCARDINIpayTest20260408111225859328\\\",\\n  \\\"EventCode\\\": \\\"\\\",\\n  \\\"goodName\\\": \\\"1996년 보양사골 대치칼국수\\\",\\n  \\\"TotPrice\\\": \\\"1000\\\",\\n  \\\"payMethod\\\": \\\"Card\\\",\\n  \\\"CARD_MemberNum\\\": \\\"\\\",\\n  \\\"MOID\\\": \\\"pay_OR-20260408-0001_1775614307\\\",\\n  \\\"CARD_Point\\\": \\\"\\\",\\n  \\\"currency\\\": \\\"WON\\\",\\n  \\\"CARD_PurchaseCode\\\": \\\"\\\",\\n  \\\"CARD_PrtcCode\\\": \\\"1\\\",\\n  \\\"applTime\\\": \\\"111226\\\",\\n  \\\"goodsName\\\": \\\"1996년 보양사골 대치칼국수\\\",\\n  \\\"CARD_CheckFlag\\\": \\\"1\\\",\\n  \\\"FlgNotiSendChk\\\": \\\"\\\",\\n  \\\"CARD_Code\\\": \\\"06\\\",\\n  \\\"CARD_BankCode\\\": \\\"04\\\",\\n  \\\"CARD_TerminalNum\\\": \\\"019058I000\\\",\\n  \\\"P_FN_NM\\\": \\\"국민계열\\\",\\n  \\\"buyerName\\\": \\\"kakao47906876\\\",\\n  \\\"p_SubCnt\\\": \\\"\\\",\\n  \\\"applNum\\\": \\\"30029716\\\",\\n  \\\"resultMsg\\\": \\\"정상처리되었습니다.\\\",\\n  \\\"CARD_Interest\\\": \\\"0\\\",\\n  \\\"CARD_SrcCode\\\": \\\"K\\\",\\n  \\\"CARD_ApplPrice\\\": \\\"1000\\\",\\n  \\\"CARD_GWCode\\\": \\\"G\\\",\\n  \\\"custEmail\\\": \\\"guest@qrorder.com\\\",\\n  \\\"CARD_Expire\\\": \\\"\\\",\\n  \\\"CARD_PurchaseName\\\": \\\"국민계열\\\",\\n  \\\"CARD_PRTC_CODE\\\": \\\"1\\\",\\n  \\\"payDevice\\\": \\\"PC\\\"\\n}\",\"receiptUrl\":\"https:\\/\\/iniweb.inicis.com\\/DefaultWebApp\\/mall\\/cr\\/cm\\/mCmReceipt_head.jsp?noTid=StdpayCARDINIpayTest20260408111225859328&noMethod=1\",\"disputes\":[]}','2026-04-08 11:12:27','2026-04-08 11:12:27');
/*!40000 ALTER TABLE `payments_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qa_t`
--

DROP TABLE IF EXISTS `qa_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `qa_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '기본키',
  `mt_idx` int(11) NOT NULL DEFAULT 0 COMMENT '요청한 사용자 (회원 ID)',
  `rt_title` varchar(255) NOT NULL DEFAULT '' COMMENT '제목',
  `rt_description` text DEFAULT NULL COMMENT '자세한 설명',
  `rt_img1` varchar(255) NOT NULL DEFAULT '' COMMENT '이미지 1',
  `rt_img2` varchar(255) NOT NULL DEFAULT '' COMMENT '이미지 2',
  `rt_img3` varchar(255) NOT NULL DEFAULT '' COMMENT '이미지 3',
  `rt_img4` varchar(255) NOT NULL DEFAULT '' COMMENT '이미지 4',
  `rt_img5` varchar(255) NOT NULL DEFAULT '' COMMENT '이미지 5',
  `rt_response_text` text DEFAULT NULL COMMENT '최고관리자 답변',
  `rt_status` enum('pending','answered') DEFAULT 'pending' COMMENT '요청 상태',
  `rt_show` enum('Y','N') DEFAULT 'Y',
  `rt_order` int(11) DEFAULT 0,
  `rt_name` varchar(255) DEFAULT NULL COMMENT '답변자',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '요청 등록 시간',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '요청 수정 시간',
  `del_date` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qa_t`
--
-- WHERE:  true limit 10

LOCK TABLES `qa_t` WRITE;
/*!40000 ALTER TABLE `qa_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `qa_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation_t`
--

DROP TABLE IF EXISTS `reservation_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservation_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `sh_idx` int(11) DEFAULT NULL COMMENT '매장 키',
  `mt_idx` int(11) DEFAULT NULL COMMENT '회원 키 (비회원이면 NULL)',
  `rv_number` varchar(255) DEFAULT NULL COMMENT '예약번호',
  `rv_name` varchar(100) NOT NULL COMMENT '예약자 이름',
  `rv_hp` varchar(20) NOT NULL COMMENT '예약자 휴대폰',
  `rv_date` date NOT NULL COMMENT '예약 일자',
  `rv_time` time NOT NULL COMMENT '예약 시간',
  `rv_people` int(11) NOT NULL DEFAULT 1 COMMENT '예약 인원 수',
  `rv_type` enum('VISIT','PREPAID') NOT NULL DEFAULT 'VISIT' COMMENT 'VISIT: 방문예약(현장결제), PREPAID: 선결제 예약',
  `rv_status` enum('PENDING','CONFIRMED','ARRIVED','CANCELLED','REJECTED') NOT NULL DEFAULT 'PENDING' COMMENT '예약 상태',
  `rv_table` varchar(20) DEFAULT NULL COMMENT '배정 테이블 번호',
  `rv_memo` text DEFAULT NULL COMMENT '요청 사항',
  `rv_cancel_reason` varchar(255) DEFAULT NULL COMMENT '취소/거절 사유',
  `rv_cancel_at` datetime DEFAULT NULL COMMENT '취소/거절 처리 시각',
  `ot_idx` int(11) DEFAULT NULL COMMENT '선결제 주문 키 (orders_t.idx)',
  `rv_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  `rv_udate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idx`),
  KEY `reservation_t_member_t_idx_fk` (`mt_idx`),
  KEY `reservation_t_shop_t_idx_fk` (`sh_idx`),
  KEY `reservation_t_orders_t_idx_fk` (`ot_idx`),
  CONSTRAINT `reservation_t_member_t_idx_fk` FOREIGN KEY (`mt_idx`) REFERENCES `member_t` (`idx`),
  CONSTRAINT `reservation_t_shop_t_idx_fk` FOREIGN KEY (`sh_idx`) REFERENCES `shop_t` (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='예약 정보';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation_t`
--
-- WHERE:  true limit 10

LOCK TABLES `reservation_t` WRITE;
/*!40000 ALTER TABLE `reservation_t` DISABLE KEYS */;
INSERT INTO `reservation_t` VALUES
(8,46,77,'2026033146770','wqewq','010192923923','2026-03-31','17:00:00',1,'VISIT','PENDING',NULL,NULL,NULL,NULL,NULL,'2026-03-31 15:48:31','2026-03-31 15:48:31'),
(9,46,NULL,'202603314600','어어엉','0102929393','2026-03-31','18:00:00',1,'VISIT','PENDING',NULL,NULL,NULL,NULL,NULL,'2026-03-31 15:49:02','2026-03-31 15:49:02'),
(10,47,NULL,'202603314700','테스트','01029383832','2026-04-01','07:00:00',1,'VISIT','PENDING',NULL,NULL,NULL,NULL,NULL,'2026-03-31 15:50:41','2026-03-31 15:50:41'),
(11,47,NULL,'202603314700','테라릉','0102938383','2026-04-01','07:30:00',1,'VISIT','PENDING',NULL,NULL,NULL,NULL,NULL,'2026-03-31 15:51:08','2026-03-31 15:51:08'),
(12,47,NULL,'202603314700','테스트','01019162343','2026-04-02','07:00:00',1,'VISIT','PENDING',NULL,NULL,NULL,NULL,NULL,'2026-03-31 15:52:53','2026-03-31 15:52:53'),
(13,46,75,'2026040146750','테스트','01012342345','2026-04-01','18:00:00',1,'VISIT','CANCELLED',NULL,NULL,NULL,NULL,NULL,'2026-04-01 09:20:28','2026-04-01 09:20:36'),
(14,46,77,'2026040246771','ㅂㅈㄷ','010123213','2026-04-02','17:00:00',1,'PREPAID','CANCELLED',NULL,NULL,NULL,NULL,18,'2026-04-02 14:14:02','2026-04-02 15:08:07'),
(15,46,77,'2026040246772','wqe','010123213','2026-04-02','18:00:00',1,'PREPAID','CANCELLED',NULL,NULL,NULL,NULL,19,'2026-04-02 15:11:46','2026-04-02 15:11:59'),
(16,46,77,'2026040246773','wew','01012312312','2026-04-02','17:00:00',1,'PREPAID','CANCELLED',NULL,NULL,NULL,NULL,20,'2026-04-02 15:13:28','2026-04-02 15:13:37'),
(17,46,77,'2026040246774','qweqw','010123213213','2026-04-02','17:00:00',1,'PREPAID','CANCELLED',NULL,NULL,NULL,NULL,21,'2026-04-02 15:15:43','2026-04-02 15:15:49');
/*!40000 ALTER TABLE `reservation_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review_image_t`
--

DROP TABLE IF EXISTS `review_image_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `review_image_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `rv_idx` int(11) NOT NULL COMMENT '리뷰 키',
  `ri_file` varchar(255) NOT NULL COMMENT '저장 파일명',
  `ri_origin_name` varchar(255) DEFAULT NULL COMMENT '원본 파일명',
  `ri_mime` varchar(100) DEFAULT NULL COMMENT 'MIME 타입',
  `ri_size` int(11) DEFAULT NULL COMMENT '파일 크기(byte)',
  `ri_order` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '정렬 순서',
  `ri_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idx`),
  KEY `ix_review_image_review_order` (`rv_idx`,`ri_order`),
  CONSTRAINT `fk_review_image_review` FOREIGN KEY (`rv_idx`) REFERENCES `review_t` (`idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='리뷰 이미지';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_image_t`
--
-- WHERE:  true limit 10

LOCK TABLES `review_image_t` WRITE;
/*!40000 ALTER TABLE `review_image_t` DISABLE KEYS */;
INSERT INTO `review_image_t` VALUES
(1,1,'20260408120720_1_9f075ea4.png','다운로드.png','image/png',2144146,1,'2026-04-08 12:07:20'),
(2,1,'20260408120720_2_6b069906.png','다운로드 (1).png','image/png',1892245,2,'2026-04-08 12:07:20'),
(3,1,'20260408120720_3_519e6a8e.png','다운로드 (2).png','image/png',2397963,3,'2026-04-08 12:07:20');
/*!40000 ALTER TABLE `review_image_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review_menu_t`
--

DROP TABLE IF EXISTS `review_menu_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `review_menu_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `rv_idx` int(11) NOT NULL COMMENT '리뷰 키',
  `sm_idx` int(11) DEFAULT NULL COMMENT '메뉴 키',
  `rm_menu_name` varchar(255) NOT NULL COMMENT '리뷰 작성 시점 메뉴명',
  `rm_option_text` varchar(255) DEFAULT NULL COMMENT '옵션 표시 텍스트',
  `rm_option_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '옵션 JSON',
  `rm_quantity` int(11) NOT NULL DEFAULT 1 COMMENT '수량',
  `rm_unit_price` decimal(10,2) DEFAULT NULL COMMENT '단가 스냅샷',
  `rm_total_price` decimal(10,2) DEFAULT NULL COMMENT '합계 스냅샷',
  `rm_order` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '정렬 순서',
  `rm_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idx`),
  KEY `ix_review_menu_review_order` (`rv_idx`,`rm_order`),
  KEY `ix_review_menu_sm_idx` (`sm_idx`),
  CONSTRAINT `fk_review_menu_review` FOREIGN KEY (`rv_idx`) REFERENCES `review_t` (`idx`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_review_menu_shop_menu` FOREIGN KEY (`sm_idx`) REFERENCES `shop_menu_t` (`idx`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='리뷰 체크 메뉴';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_menu_t`
--
-- WHERE:  true limit 10

LOCK TABLES `review_menu_t` WRITE;
/*!40000 ALTER TABLE `review_menu_t` DISABLE KEYS */;
INSERT INTO `review_menu_t` VALUES
(1,1,32,'1996년 보양사골 대치칼국수',NULL,NULL,1,1000.00,1000.00,1,'2026-04-08 12:07:20');
/*!40000 ALTER TABLE `review_menu_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review_t`
--

DROP TABLE IF EXISTS `review_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `review_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `mt_idx` int(11) NOT NULL COMMENT '작성자 키',
  `sh_idx` int(11) NOT NULL COMMENT '매장 키',
  `ot_idx` int(11) NOT NULL COMMENT '주문 키',
  `rv_food_score` tinyint(3) unsigned NOT NULL COMMENT '음식 평점(1~5)',
  `rv_contents` text NOT NULL COMMENT '리뷰 내용',
  `rv_photo_cnt` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '첨부 사진 수',
  `rv_show` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '노출 여부',
  `del_date` datetime DEFAULT NULL COMMENT '삭제일',
  `rv_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  `rv_udate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idx`),
  UNIQUE KEY `uq_review_order` (`ot_idx`),
  KEY `ix_review_member_wdate` (`mt_idx`,`rv_wdate`),
  KEY `ix_review_shop_show_wdate` (`sh_idx`,`rv_show`,`rv_wdate`),
  CONSTRAINT `fk_review_member` FOREIGN KEY (`mt_idx`) REFERENCES `member_t` (`idx`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_review_order` FOREIGN KEY (`ot_idx`) REFERENCES `orders_t` (`idx`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_review_shop` FOREIGN KEY (`sh_idx`) REFERENCES `shop_t` (`idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='리뷰';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_t`
--
-- WHERE:  true limit 10

LOCK TABLES `review_t` WRITE;
/*!40000 ALTER TABLE `review_t` DISABLE KEYS */;
INSERT INTO `review_t` VALUES
(1,75,46,27,4,'테스트입니다 20자 까지 작성을 해야합니다!',3,'Y',NULL,'2026-04-08 12:07:20','2026-04-08 12:07:20');
/*!40000 ALTER TABLE `review_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settle_t`
--

DROP TABLE IF EXISTS `settle_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settle_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `st_number` varchar(50) NOT NULL COMMENT '정산번호(고유코드)',
  `sh_idx` int(11) NOT NULL COMMENT '매장 키',
  `st_start_date` date NOT NULL COMMENT '정산기간 시작일',
  `st_end_date` date NOT NULL COMMENT '정산기간 종료일',
  `st_order_count` int(11) NOT NULL DEFAULT 0 COMMENT '정산에 포함된 주문 건수',
  `st_total_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '총 정산 금액(주문 합계)',
  `st_service_fee` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '서비스 수수료',
  `st_final_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '실 지급 금액(정산예정금액)',
  `st_plan_date` date DEFAULT NULL COMMENT '정산 예정일',
  `st_done_date` datetime DEFAULT NULL COMMENT '정산 완료일시',
  `st_status` enum('READY','DONE','PLANNED') NOT NULL DEFAULT 'PLANNED' COMMENT '정산 상태',
  `st_admin_memo` text DEFAULT NULL COMMENT '관리자 메모',
  `st_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  `st_udate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idx`),
  UNIQUE KEY `settle_t_uk_number` (`st_number`),
  KEY `settle_t_shop_t_idx_fk` (`sh_idx`),
  CONSTRAINT `settle_t_shop_t_idx_fk` FOREIGN KEY (`sh_idx`) REFERENCES `shop_t` (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='정산 내역';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settle_t`
--
-- WHERE:  true limit 10

LOCK TABLES `settle_t` WRITE;
/*!40000 ALTER TABLE `settle_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `settle_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `setup_t`
--

DROP TABLE IF EXISTS `setup_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `setup_t` (
  `idx` int(11) NOT NULL,
  `st_agree1` longtext DEFAULT NULL COMMENT '이용약관',
  `st_agree2` longtext DEFAULT NULL COMMENT '개인정보처리방침',
  `st_agree3` longtext DEFAULT NULL COMMENT '개인정보 제 3자 정보 제공 동의',
  `st_agree4` longtext DEFAULT NULL COMMENT '탈퇴약관',
  `st_agree5` longtext DEFAULT NULL COMMENT '배송안내',
  `st_agree6` longtext DEFAULT NULL COMMENT '주문취소/변경 안내',
  `st_agree7` longtext DEFAULT NULL COMMENT '교환/반품 안내',
  `st_agree8` longtext DEFAULT NULL COMMENT '위조상품 판매금지',
  `st_agree9` longtext DEFAULT NULL COMMENT '지적 재산권 침해 금지',
  `st_agree10` longtext DEFAULT NULL COMMENT '직거래 금지',
  `st_agree11` longtext DEFAULT NULL COMMENT '판매규정준수',
  `st_commission1` tinyint(4) DEFAULT NULL COMMENT '판로인마진 %',
  `st_commission2` tinyint(4) DEFAULT NULL COMMENT '홍보회원 수수료 MAX %',
  `st_commission3` tinyint(4) DEFAULT NULL COMMENT '인플루언서 회원 수수료 MAX %',
  `st_commission4` tinyint(4) DEFAULT NULL COMMENT '판로인 홍보회원 마진 %',
  `st_commission5` tinyint(4) DEFAULT NULL,
  `st_commission6` tinyint(4) DEFAULT NULL,
  `st_point` tinyint(4) DEFAULT NULL COMMENT '결제적립%',
  `st_logis` mediumtext DEFAULT NULL COMMENT '택배사 '',''로 구분',
  `st_purchase_cdate` tinyint(4) DEFAULT NULL COMMENT '송장입력수 자동 배송완료일',
  `st_purchase_rdate` tinyint(4) DEFAULT NULL COMMENT '배송완료후 자동 구매확정일',
  `st_customer_tel` varchar(100) DEFAULT NULL COMMENT '고객센터 전화번호',
  `st_customer_time` varchar(100) DEFAULT NULL COMMENT '고객센터 시간',
  `st_company_zipcode` varchar(10) DEFAULT NULL COMMENT '우편번호',
  `st_company_add` varchar(255) DEFAULT NULL COMMENT '회사주소',
  `st_company_num1` varchar(50) DEFAULT NULL COMMENT '사업자등록번호',
  `st_company_boss` varchar(50) DEFAULT NULL COMMENT '대표자',
  `st_company_num2` varchar(50) DEFAULT NULL COMMENT '통신판매업 신고번호',
  `st_privacy_admin` varchar(50) DEFAULT NULL COMMENT '개인정보책임관리자',
  `st_company_name` varchar(50) DEFAULT NULL COMMENT '회사명',
  `st_customer_email` varchar(50) DEFAULT NULL COMMENT '고객센터 이메일',
  `st_sns_link1` varchar(255) DEFAULT NULL COMMENT '페이스북',
  `st_sns_link2` varchar(255) DEFAULT NULL COMMENT '트위터',
  `st_sns_link3` varchar(255) DEFAULT NULL COMMENT '인스타그램',
  `st_sns_link4` varchar(255) DEFAULT NULL COMMENT '유튜브',
  `st_sns_link5` varchar(255) DEFAULT NULL COMMENT '링크드인',
  `st_sns_link6` varchar(255) DEFAULT NULL COMMENT '카카오 채널',
  `st_sns_link7` varchar(255) DEFAULT NULL COMMENT '네이버블로그',
  `st_google_link` varchar(255) DEFAULT NULL COMMENT '구글스토어 링크',
  `st_apple_link` varchar(255) DEFAULT NULL COMMENT '앱스토어 링크',
  `st_sweettrack_key` varchar(255) DEFAULT NULL COMMENT '스마트택배 KEY',
  `st_sweettrack_date` varchar(50) DEFAULT NULL COMMENT '스마트택배 갱신일',
  `st_coupon_use` enum('Y','N') DEFAULT NULL COMMENT '신규회원 쿠폰발행 여부',
  `st_coupon_price` int(11) DEFAULT NULL COMMENT '쿠폰할인금액',
  `st_coupon_minimum` int(11) DEFAULT NULL COMMENT '주문최소금액',
  `st_coupon_term` int(11) DEFAULT NULL COMMENT '쿠폰유효기간',
  `st_grade_coupon_use` enum('Y','N') DEFAULT NULL COMMENT '등급 혜택 쿠폰 발생',
  `st_grade_coupon_price` int(11) DEFAULT NULL COMMENT '쿠폰할인금액',
  `st_grade_coupon_minimum` int(11) DEFAULT NULL COMMENT '주문최소금액',
  `st_grade_coupon_term` int(11) DEFAULT NULL COMMENT '쿠폰유효기간',
  `st_point_join` int(11) DEFAULT 0 COMMENT '회원가입 포인트',
  `st_point_od_confirm` int(11) DEFAULT 1 COMMENT '구매확정 포인트',
  `st_point_od_confirm_chk` enum('1','2') DEFAULT '1' COMMENT '1: 원, 2: %',
  `st_point_review_text` int(11) DEFAULT 0 COMMENT '텍스트 리뷰 포이트',
  `st_point_review_photo` int(11) DEFAULT 0 COMMENT '포토 리뷰 포인트',
  `st_prohibit_id` mediumtext DEFAULT NULL COMMENT '아이디,닉네임 금지단어',
  `st_meta_title` mediumtext DEFAULT NULL COMMENT '메타태그 브라우저 타이틀',
  `st_meta_author` mediumtext DEFAULT NULL COMMENT '메타태그 Author',
  `st_meta_description` mediumtext DEFAULT NULL COMMENT '메타태그 Description',
  `st_meta_keywords` mediumtext DEFAULT NULL COMMENT '메타태그 Keywords',
  `st_add_meta` mediumtext DEFAULT NULL COMMENT '추가 메타태그',
  `st_id_filter` mediumtext DEFAULT NULL COMMENT '금지 아이디 목록',
  `st_filter` mediumtext DEFAULT NULL COMMENT '단어 필터링',
  `st_possible_ip` mediumtext DEFAULT NULL COMMENT '접근가능 IP',
  `st_intercept_ip` mediumtext DEFAULT NULL COMMENT '접근차단 IP',
  `st_analytics` mediumtext DEFAULT NULL COMMENT '방문자분석 스크립트',
  `st_portone` varchar(45) DEFAULT 'test' COMMENT '포트원 결제모드',
  `st_bank_name` varchar(45) DEFAULT NULL COMMENT '은행',
  `st_bank_num` varchar(45) DEFAULT NULL COMMENT '계좌번호',
  `st_bank_user` varchar(45) DEFAULT NULL COMMENT '계좌주',
  `st_aos_version` varchar(45) DEFAULT NULL COMMENT '안드로이드 앱버전',
  `st_aos_update` varchar(45) DEFAULT NULL COMMENT '앱업데이트 1선택 2강제',
  `st_ios_version` varchar(45) DEFAULT NULL COMMENT ' ios 앱버전',
  `st_ios_update` varchar(45) DEFAULT NULL COMMENT '앱업데이트 1선택 2강제'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='기본설정 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `setup_t`
--
-- WHERE:  true limit 10

LOCK TABLES `setup_t` WRITE;
/*!40000 ALTER TABLE `setup_t` DISABLE KEYS */;
INSERT INTO `setup_t` VALUES
(1,'<p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>맛집바로 서비스 이용약관</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">최종 업데이트: 2026년 3월</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">본 이용약관은 **코로코 주식회사(이하 \"회사\")**가 제공하는 **맛집바로 서비스(이하 \"서비스\")**의 이용과 관련하여 회사와 이용자 간의 권리, 의무 및 책임사항을 규정함을 목적으로 합니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>제1조 (목적)</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">본 약관은 회사가 제공하는 맛집바로 서비스의 이용조건 및 절차, 회사와 이용자의 권리와 의무, 책임사항 및 기타 필요한 사항을 규정함을 목적으로 합니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>제2조 (정의)</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">본 약관에서 사용하는 용어의 정의는 다음과 같습니다.</span></p><ol><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>서비스</strong></span><br><span style=\"color:rgb(0,0,0);font-family:Arial;\">회사가 제공하는 지도 기반 맛집 검색, 메뉴 확인, 주문 및 결제 서비스를 의미합니다.</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>이용자</strong></span><br><span style=\"color:rgb(0,0,0);font-family:Arial;\">본 약관에 따라 회사가 제공하는 서비스를 이용하는 회원 및 비회원을 말합니다.</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>회원</strong></span><br><span style=\"color:rgb(0,0,0);font-family:Arial;\">서비스에 가입하여 회사가 제공하는 서비스를 지속적으로 이용할 수 있는 자를 말합니다.</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>가맹점</strong></span><br><span style=\"color:rgb(0,0,0);font-family:Arial;\">회사의 플랫폼에 입점하여 메뉴 판매 및 서비스를 제공하는 음식점 또는 사업자를 의미합니다.</span><br>&nbsp;</p></li></ol><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>제3조 (약관의 효력 및 변경)</strong></span></p><ol><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">본 약관은 서비스 화면에 게시하거나 기타 방법으로 이용자에게 공지함으로써 효력이 발생합니다.</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">회사는 관련 법령을 위반하지 않는 범위에서 본 약관을 변경할 수 있습니다.</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">약관이 변경될 경우 적용일자 및 변경내용을 사전에 공지합니다.</span><br>&nbsp;</p></li></ol><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>제4조 (서비스의 제공)</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">회사는 다음과 같은 서비스를 제공합니다.</span></p><ol><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">지도 기반 맛집 검색 서비스</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">음식점 메뉴 정보 제공 서비스</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">메뉴 주문 서비스</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">결제 서비스</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">기타 회사가 추가로 개발하거나 제공하는 서비스</span><br>&nbsp;</p></li></ol><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>제5조 (서비스 이용)</strong></span></p><ol><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">이용자는 본 약관에 따라 서비스를 이용할 수 있습니다.</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">서비스 이용은 회사의 업무상 또는 기술상 특별한 지장이 없는 한 연중무휴 24시간 제공됩니다.</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">회사는 시스템 점검, 유지보수 등의 경우 서비스 제공을 일시적으로 중단할 수 있습니다.</span><br>&nbsp;</p></li></ol><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>제6조 (회원가입)</strong></span></p><ol><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">이용자는 회사가 정한 가입 절차에 따라 회원가입을 할 수 있습니다.</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">회사는 다음의 경우 회원가입을 제한할 수 있습니다.</span><br>&nbsp;</p></li></ol><ul><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">타인의 정보를 도용한 경우</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">허위 정보를 입력한 경우</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">기타 회사가 판단하기에 부적절한 경우</span><br>&nbsp;</p></li></ul><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>제7조 (주문 및 결제)</strong></span></p><ol><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">이용자는 서비스 내에서 가맹점의 메뉴를 주문할 수 있습니다.</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">결제는 서비스에서 제공하는 결제수단을 통해 이루어집니다.</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">주문의 취소 및 환불은 가맹점의 정책 및 관련 법령에 따릅니다.</span></p><p style=\"margin-left:0pt;\">&nbsp;</p></li></ol><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>제8조 (이용자의 의무)</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">이용자는 다음 행위를 하여서는 안 됩니다.</span></p><ol><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">타인의 계정을 사용하는 행위</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">서비스 운영을 방해하는 행위</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">허위 주문 또는 부정한 결제 행위</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">기타 법령 또는 공서양속에 반하는 행위</span><br>&nbsp;</p></li></ol><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>제9조 (회사의 책임 제한)</strong></span></p><ol><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">회사는 가맹점이 제공하는 음식 및 서비스의 품질에 대해 직접적인 책임을 지지 않습니다.</span><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">회사는 천재지변, 시스템 장애 등 불가항력적 사유로 인한 서비스 중단에 대해 책임을 지지 않습니다.</span><br>&nbsp;</p></li></ol><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>제10조 (서비스 변경 및 중단)</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">회사는 서비스의 일부 또는 전부를 변경하거나 중단할 수 있으며, 이 경우 사전에 공지합니다.</span><br>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>제11조 (지적재산권)</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">서비스에 포함된 모든 콘텐츠, 디자인, 로고, 프로그램 등에 대한 권리는 회사 또는 정당한 권리자에게 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">이용자는 회사의 사전 동의 없이 이를 사용할 수 없습니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>제12조 (분쟁 해결)</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">본 약관과 관련된 분쟁에 대해서는 대한민국 법률을 적용하며, 관할 법원은 회사 본점 소재지 관할 법원으로 합니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>제13조 (회사 정보)</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">회사명: 코로코 주식회사</span><br><span style=\"color:rgb(0,0,0);font-family:Arial;\">서비스명: 맛집바로</span><br><span style=\"color:rgb(0,0,0);font-family:Arial;\">문의: support@koroco.co.kr</span></p>','<p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\"><strong>개인정보처리방침</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">코로코는 [개인정보보호법] 등 관련 법령규정을 준수하며, 회사가 운영하고 있는 코로코 정보망에서 취급하는 모든 이용자의 개인정보는 관련 법령에 근거하거나 이용자의 동의에 의하여 수집, 보유 및 처리되고 있습니다.코로코는 [개인정보보호법] 제30조에 따라 이용자의 개인정보 보호 및 권익을 보호하고 개인정보와 관련한 이용자의 고충을 원활하게 처리할 수 있도록 아래와 같은 개인정보처리방침을 두고 있습니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제1조【개인정보의 수집 및 이용목적】</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">코로코는 아래 항의 목적을 위해 개인정보를 수집 ․ 처리하고, 아래의 이용목적 외의 용도로는 사용하지 않습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 회원등록 및 관리</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">회원 가입의사 확인, 유료 회원제 서비스 제공에 따른 본인확인 및 인증, 회원자격 유지 및 관리, 서비스 부정이용 방지, 각종 고지 및 통지, 고충처리 등 민원처리, 분쟁조정을 위한 기록보존 등을 목적으로 개인정보를 처리합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 민원사무 처리에의 활용</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">회원의 신원확인, 민원사항 확인, 사실조사를 위한 연락 및 통지, 처리결과 통보 등을 목적으로 개인정보를 처리합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">③ 정보이용 계약이행 및 요금정산에의 활용</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">금융거래 본인인증, 요금결제 및 정산, 청구서 발송, 연령확인, 콘텐츠 및 서비스 제공 등을 목적으로 개인정보를 처리합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">④ 마케팅 및 광고에의 활용</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">이벤트 및 광고성 정보 제공 및 참여기회 제공, 통계학적 특성에 따른 서비스 제공 및 광고게재, 서비스의 유효성 확인, 신규 서비스 개발 및 맞춤 서비스 제공, 접속빈도 파악 또는 회원의 서비스 이용에 대한 통계 등을 목적으로 개인정보를 처리합니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제2조【개인정보의 수집 항목】</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 코로코는 회원가입 당시 본인확인 및 서비스 제공을 위해 아래와 같은 항목을 수집합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">- 로그인 계정, 연락처 주소, 성명, 주민등록번호, 주소, 연락처, 이메일주소, 사업자등록번호, 차량정보, 계좌번호 등</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 위 제1항의 항목 외 서비스 이용과정 및 사업 처리과정에서</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">- 서비스 이용기록, 이용정지 기록, 접속로그, 쿠키, 방문일시, 결제기록, 운송실적정보 등의 항목이 생성되어 수집될 수 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">③ 앱에서 액세스, 수집, 사용, 공유하는 \"개인 정보\" 및 \"민감한 사용자 데이터\" 유형</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">- 본 앱에서 사용되는 개인정보는 구글 계정과 상관없는 로그인계정, 연락처주소가 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">- 본 앱은 위 기술한 \"개인 정보\", \"민감한 사용자 데이터\" 제외하고 이외 데이터를 수집, 사용, 공유하지 않습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">- 본 앱은 위 기술한 \"개인 정보\", \"민감한 사용자 데이터\"를 서버로 전송하여 사용하는데 동의하는것으로 간주됩니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">- 이 정보는 귀하가 앱을 통해 데이터를 공개적으로 공유하지 않는 한 본사에서만 접속이 가능합니다</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">④ 개인 정보 및 민감한 사용자 데이터를 안전하게 처리하는 절차 및 사용 목적</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">- \"개인 정보\"의 로그인계정, 연락처 주소는 불법적인 중복 사용자를 걸러내는데 사용하거나 고객의 문의사항에 대응하기 위한 자료로 활용됩니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">- 연락처 주소는 이 서비스를 사용하고 있는 상대방이 있는지 여부를&nbsp; 서버에서 API로 판가름 하는 데에만 사용되며 별도로 서버에 저장되거나 보관하지 않습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">- \"민감한 사용자 데이터\"는 앱을 사용하기 위한 기본적인 앱 데이터 값이며 사용자가 언제든 삭제할 수 있습니다</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제3조【개인정보의 보유 및 이용기간】</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">이용자의 개인정보는 원칙적으로 개인정보의 수집 및 이용목적이 달성되면 지체 없이 파기합니다.단, 다음의 정보에 대해서는 아래의 이유로 명시한 기간 동안 보존합니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 회사내부 방침에 의한 정보보유 사유</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">부정이용기록</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">보존이유 : 부정가입 및 부정이용 방지</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">보존기간 : 5년</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 관련법령에 의한 정보보유 사유</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">상법, 전자상거래 등에서의 소비자보호에 관한 법률 등 관계법령의 규정에 의하여 보존할 필요가 있는 경우 회사의 관계법령에서 정한 일정한 기간 동안 회원정보를 보관합니다. 이 경우 회사는 보관하는 정보를 그 보관의 목적으로만 이용하며 보존기간은 아래와 같습니다.</span><br>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">1. 계약 또는 청약철회 등에 관한 기록</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">보존이유 : 전자상거래 등에서의 소비자보호에 관한법률</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">보존기간 : 5년</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">2. 대금결제 및 전자금융거래 등에 관한 기록</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">보존 이유 : 전자상거래 등에서의 소비자보호에 관한 법률</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">보존 기간 : 5년</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">3. 소비자의 불만 또는 분쟁처리에 관한 기록</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">보존 이유 : 전자상거래 등에서의 소비자보호에 관한 법률</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">보존 기간 : 3년</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">4. 본인확인에 관한 기록</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">보존 이유 : 정보통신 이용촉진 및 정보보호 등에 관한 법률</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">보존 기간 : 6개월</span></p><p style=\"margin-left:0px;\">&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">5. 방문에 관한 기록</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">보존 이유 : 통신비밀보호법</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">보존 기간 : 3개월</span></p><p style=\"margin-left:0px;\">&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">6. 통계조사 자료에 관한 기록</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">보존 이유 : 고객만족과 서비스 개선을 위한 통계조사 자료</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">보존 기간 : 3년</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제4조【개인정보의 제3자 제공에 관한 사항】</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 코로코는 원칙적으로 이용자의 개인정보를 제1조에서 명시한 목적 범위 내에서만 주의를 기울여 처리합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 코로코는 아래의 경우를 제외하고는 이용자의 사전 동의 없이 제1조에서 명시한 목적 범위를 초과하여 이용자의 개인정보를 이용하거나 제3자에게 제공하지 않습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">1. 정보주체로부터 별도의 동의를 받은 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">2. 다른 법률에 특별한 규정이 있는 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">3. 정보주체 또는 그 법정대리인이 의사표시를 할 수 없는 상태에 있거나 주소불명 등으로 사전 동의를 받을 수 없는 경우로서 명백히 정보주체 또는 제3자의 급박한 생명, 신체, 재산의 이익을 위하여 필요하다고 인정되는 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">4. 통계작성 및 학술연구 등의 목적을 위하여 필요한 경우로서 특정 개인을 알아볼 수 없는 형태로 개인정보를 제공하는 경우</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제5조【개인정보처리 위탁】</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">코로코는 개인정보처리를 위탁하지 않고 있으나, 제3자에게 개인정보의 처리 업무를 위탁하는 경우에는 다음 각 호의 내용이 포함된 문서에 의하도록 하고, 방침이 변경되면 본 개인정보처리방침에 고지하도록 하겠습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">1. 위탁업무 수행 목적 외 개인정보의 처리 금지에 관한 사항</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">2. 개인정보의 기술적·관리적 보호조치에 관한 사항</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">3. 그 밖에 개인정보의 안전한 관리를 위하여 대통령령으로 정한 사항</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제6조【정보주체의 권리,의무 및 그 행사방법】</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">이용자는 개인정보주체로서 다음과 같은 권리를 행사할 수 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 정보주체는 코로코에 대해 언제든지 다음 각 호의 개인정보 보호 관련 권리를 행사할 수 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">1. 개인정보 열람요구</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">이용자는 개인정보 열람을 요구할 수 있으나, 코로코는 다음 각 호의 어느 하나에 해당하는 경우에는 정보주체에게 그 사유를 알리고 열람을 제한하거나 거절할 수 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">가. 법률에 따라 열람이 금지되거나 제한되는 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">나. 다른 사람의 생명·신체를 해할 우려가 있거나 다른 사람의 재산과 그 밖의 이익을 부당하게 침해할 우려가 있는 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">다. 공공기관이 다음 각 목의 어느 하나에 해당하는 업무를 수행할 때 중대한 지장을 초래하는 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">(1) 조세의 부과·징수 또는 환급에 관한 업무</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">(2) 「초·중등교육법」 및 「고등교육법」에 따른 각급 학교, 「평생교육법」에 따른 평생교육시설, 그 밖의 다른 법률에 따라 설치된 고등교육기관에서의 성적 평가 또는 입학자 선발에 관한 업무</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">(3) 학력·기능 및 채용에 관한 시험, 자격 심사에 관한 업무</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">(4) 보상금·급부금 산정 등에 대하여 진행 중인 평가 또는 판단에 관한 업무</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">(5) 다른 법률에 따라 진행 중인 감사 및 조사에 관한 업무</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">2. 개인정보의 정정 ․ 삭제 요구</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">이용자는 코로코에게 그 개인정보의 정정 또는 삭제를 요구할 수 있습니다. 다만, 다른 법령에서 그 개인정보가 수집 대상으로 명시되어 있는 경우에는 그 삭제를 요구할 수 없습니다. 정정 또는 삭제요구를 받은 코로코는 개인정보의 정정 또는 삭제에 관하여 다른 법령에 특별한 절차가 규정되어 있는 경우를 제외하고는 지체 없이 그 개인정보를 조사하여 정보주체의 요구에 따라 정정 ․ 삭제 등 필요한 조치를 한 후 그 결과를 정보주체에게 알려야 합니다.</span></p><p style=\"margin-left:0px;\"><br><span style=\"color:rgb(0,0,0);font-family:;\">3. 개인정보의 처리정지 요구</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">이용자는 코로코에 대하여 자신의 개인정보 처리의 정지를 요구할 수 있습니다. 그러면, 코로코는 지체 없이 정보주체의 요구에 따라 개인정보 처리의 전부를 정지하거나 일부를 정지하여야 합니다. 다만, 다음 각 호의 어느 하나에 해당하는 경우에는 정보주체의 처리정지 요구를 거절할 수 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">가. 법률에 특별한 규정이 있거나 법령상 의무를 준수하기 위하여 불가피한 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">나. 다른 사람의 생명·신체를 해할 우려가 있거나 다른 사람의 재산과 그 밖의 이익을 부당하게 침해할 우려가 있는 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">다. 공공기관이 개인정보를 처리하지 아니하면 다른 법률에서 정하는 소관 업무를 수행할 수 없는 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">라. 개인정보를 처리하지 아니하면 정보주체와 약정한 서비스를 제공하지 못하는 등 계약의 이행이 곤란한 경우로서 정보주체가 그 계약의 해지 의사를 명확하게 밝히지 아니한 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 제1항에 따른 권리 행사는 코로코에 대해 개인정보보호법시행규칙 별지 제8호 서식에 따라 서면, 전자우편, 팩스 등으로 하실 수 있으며, 코로코는 이에 대해 지체 없이 조치하겠습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">③ 정보주체가 개인정보의 오류 등에 대한 정정 또는 삭제를 요구한 경우, 코로코는 정정 또는 삭제를 완료할 때까지 당해 개인정보를 이용하거나 제공하지 않습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">④ 제1항에 따른 권리 행사는 정보주체의 법정대리인이나 위임을 받은 자 등 대리인을 통하여 하실 수 있으며, 이 경우 위임장을 제출하셔야 합니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제7조【개인정보의 파기】</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">코로코는 원칙적으로 개인정보의 수집 및 이용목적이 달성되면 지체 없이 해당 개인정보를 파기하며, 파기의 절차, 기한 및 방법은 다음과 같습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 파기절차</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">이용자가 입력한 정보는 목적 달성 후 별도의 DB에 옮겨져(종이의 경우 별도의 서류) 내부 방침 및 기타 관련 법령에 따라 일정기간 저장된 후 혹은 즉시 파기됩니다. 이 때, DB로 옮겨진 개인정보는 법률에 의한 경우가 아니고서는 다른 목적으로 이용되지 않습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 파기기한 및 방법</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">개인정보의 보유기간이 만료되었거나, 개인정보의 처리 목적 달성, 해당 서비스의 폐지, 사업의 종료 등 그 개인정보가 불필요하게 되었을 때에는 조속한 시일 내에 종이에 출력된 개인정보는 분쇄기로 분쇄하거나 소각을 하고, 전자적 파일 형태로 저장된 개인정보는 기록을 재생할 수 없는 기술적 방법을 사용하여 삭제하는 방법으로 파기합니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제8조【개인정보의 안전성 확보 조치】</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">코로코는 개인정보보호법 제29조에 따라 다음과 같이 개인정보의 안전성 확보에 필요한 기술적/관리적 및 물리적 조치를 하고 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 정기적인 자체 감사 실시</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">개인정보 취급 관련 안정성 확보를 위해 정기적(분기 1회)으로 자체 감사를 실시하고 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 개인정보 취급 직원의 최소화 및 교육</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">개인정보를 취급하는 직원을 지정하고 담당자에 한정시켜 최소화 하여 개인정보를 관리하는 대책을 시행하고 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">③ 내부관리계획의 수립 및 시행</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">개인정보의 안전한 처리를 위하여 내부관리계획을 수립하고 시행하고 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">④ 보안프로그램 설치 및 주기적 점검과 갱신</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">해킹이나 컴퓨터 바이러스 등에 의한 개인정보 유출 및 훼손을 막기 위하여 보안프로그램을 설치하고 주기적으로 점검하며 갱신하고 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">⑤ 개인정보의 암호화</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">개인정보 중 주민등록번호 및 중요한 자료는 암호화 등을 통하여 안전하게 관리하고 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">⑥ 접속기록 보관 및 위 ․ 변조 방지</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">개인정보를 처리하는 시스템에 접속한 기록을 6개월 이상 보관, 관리하면서 접속 기록이 위 ․ 변조되거나 분실도지 않도록 보안기능을 사용하고 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">⑦ 개인정보에 대한 접근 제한</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">개인정보를 처리하는 데이터베이스시스템에 대한 접근권한의 부여, 변경, 말소를 통하여 개인정보에 대한 접근통제를 위하여 필요한 조치를 하고 있으며, 침입차단시스템을 이용하여 외부로부터의 무단 접근을 통제하고 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">⑧ 비인가자에 대한 출입 통제</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">개인정보를 보관하고 있는 물리적 보관 장소를 별도로 두고 이에 대해 출입통제 절차를 수립, 운영하고 있습니다.</span></p><p style=\"margin-left:0px;\">&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제9조【인터넷 접속정보파일 등 개인정보를 자동으로 수집하는 장치의 설치⋅운영 및 그 거부에 관한 사항】</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 코로코는 이용자의 정보를 수시로 저장하고 찾아내는 ‘쿠키’를 운용합니다. 쿠키란 코로코 홈페이지 운영을 위해 사용하는 서버가 사용자의 브라우저에 보내는 아주 작은 텍스트 파일로서 사용자의 컴퓨터 하드디스크에 저장됩니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 쿠키의 운용 목적은 코로코 홈페이지 이용자의 접속빈도나 방문시간 등을 분석하고 맞춤화된 개인화 서비스의 제공 등에 사용하기 위함입니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">③ 이용자는 쿠키 설치에 대한 선택권을 가지고 있습니다. 따라서 이용자는 웹브라우저에서 옵션을 설정함으로써 모든 쿠키를 허용하거나, 쿠키가 저장될 때마다 확인을 거치거나, 아니면 모든 쿠키의 저장을 거부할 수도 있습니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제10조【개인정보 보호책임자 작성】</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">코로코는 개인정보 처리에 관한 업무를 총괄해서 책임지고, 개인정보 처리와 관련한 정보주체의 불만처리 및 피해구제 등을 위하여 아래와 같이 개인정보 보호책임자를 지정하고 있습니다.</span><br><span style=\"color:rgb(0,0,0);font-family:;\">① 개인정보 보호책임자</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">성 명 : 코로코 김기준 대표</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">연락처 : 개인정보 보호 담당부서로 연결됩니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 개인정보 보호담당자</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">성 명 : 코로코 김기준 대표</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">연락처 : 1660-0404</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">③ 정보주체께서는 코로코의 서비스를 이용하시면서 발생한 모든 개인정보 보호 관련 문의, 불만처리, 피해구제 등에 관한 사항을 개인정보 보호책임자 및 담당부서로 문의하실 수 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">코로코는 정보주체의 문의에 대해 지체 없이 답변 및 처리해드릴 것입니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제11조【개인정보 열람청구】</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">정보주체께서는 코로코의 자체적인 개인정보 불만처리, 피해구제 결과에 만족하지 못하시거나, 피해를 구제받기 위하여 보다 자세한 도움이 필요하시면 아래 기관으로 문의하여 주시기 바랍니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">▶ 개인정보 침해신고센터 (한국인터넷 진흥원 운영 http://privacy.kisa.or.kr) 국번없이 118</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">▶ 개인정보 분쟁조정위원회 (한국인터넷 진흥원 운영 http://www.kopico.or.kr) 02-405-5150</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">▶ 경찰청 사이버안전국 (http://cyberbureau.police.go.kr) 국번없이 182</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">▶ 대검찰청 사이버수사과 (http://www.spo.go.kr) 국번없이 1301</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제12조【고지의 의무】</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">이 개인정보처리방침은 시행일로부터 적용되며, 법령 및 방침에 따른 변경내용의 추가, 삭제 및 정정이 있는 경우에는 변경사항의 시행 7일 전부터 공지사항을 통하여 고지할 것입니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">▶ 공고날짜: 2026년 03월 12일</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">▶ 시행일자: 2026년 03월 12일</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">▶ 최초 시행일자: 2026년 03월 12일</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">1. 회 사 : 코로코</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">2. 성 명 : 김 기 준</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">3. 앱이름 : 맛집바로</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">4. 개발자 : 김기준</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">위치기반서비스 이용약관</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제1장 총 칙</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 1 조 (목적)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">본 약관은 코로코 (이하 “회사”라 합니다.)가 운영, 제공하는 코로코(이하 “서비스”)를 이용함에 있어 회사와 고객 및 개인위치정보주체의 권리, 의무 및 책임사항에 따른 이용조건 및 절차 등 기본적인 사항을 규정함을 목적으로 합니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 2 조 (이용약관의 효력 및 변경)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 본 약관은 이용자가 본 약관에 동의하고 회사가 정한 절차에 따라 위치기반서비스의 이용자로 등록됨으로써 효력이 발생합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 회원이 오프라인에서 위치정보 활용동의서에 서명하여 회사로부터 이용자번호(ID), 비밀번호(P/W)를 부여받았을 때부터 본 약관의 내용을 모두 읽고 충분히 이해하였으며, 그 적용에 동의한 것으로 봅니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">③ 회사는 서비스에 새로운 업무 적용, 정부에 의한 시정명령의 이행 및 기타 회사의 업무상 약관을 변경해야 할 중요한 사유가 있다고 판단될 경우 본 약관을 변경할 수 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">④ 회사는 본 약관을 변경할 경우에는 변경된 약관과 사유를 적용일자 1주일 전까지 서비스홈페이지 등 기타 공지사항 페이지에 게시하거나 회원에게 전자적 형태(전자우편, SMS 등)로 공지하여, 회원이 그 기간 안에 이의제기가 없거나, 본 서비스를 이용할 경우 이를 승인한 것으로 봅니다.</span><br>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 3 조 (관계법령의 적용)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">본 약관은 신의성실의 원칙에 따라 공정하게 적용하며, 본 약관에 명시되지 아니한 사항에 대하여는 관계법령 또는 상관례에 따릅니다.</span><br>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 4 조 (서비스의 내용)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">회사가 제공하는 서비스는 아래와 같습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">서비스 명 서비스 내용</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">코로코</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">o 화물물동량 제공</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">o 물동량과 관련한 화물차량 위치제공, 위치/지역에 따른 알림, 경로 안내</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">o 해당 대상에게 물동량 실시간 노출</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">o 위치정보를 활용한 정보 검색결과 및 콘텐츠를 제공하거나 추천</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">o 위치기반의 맞춤형 광고</span></p><p style=\"margin-left:0px;\">&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 5 조 (서비스 이용요금)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">회사가 제공하는 위치기반서비스는 무료입니다. 단, 무선 서비스 이용 시 발생하는 데이터 통신료는 별도이며, 이용자가 가입한 각 이동통신사의 정책에 따릅니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 6 조 (서비스이용의 제한 및 중지)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 회사는 위치기반서비스사업자의 정책변경 등과 같이 회사의 제반사정 또는 법률상의 이유로 위치기반서비스를 유지할 수 없는 경우 위치기반서비스의 전부 또는 일부를 제한·중지할 수 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 단, 위 항에 의한 위치기반서비스 중단의 경우 회사는 사전에 회사 홈페이지 등 기타 공지사항 페이지를 통해 공지하거나 이용자에게 통지합니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 7 조 (개인위치정보의 이용 또는 제공)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">“회사”는 개인위치정보를 이용하여 서비스를 제공하고자 하는 경우에는 미리 다음 각호의 내용을 이용약관에 명시한 후 “개인위치정보주체”의 동의를 얻어야 합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 회사는 상호, 주소, 전화번호 그 밖의 연락처를 이용자가 쉽게 알 수 있도록 서비스화면에 게시하며 약관의 내용은 이용자가 연결화면을 통하여 볼 수 있도록 합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② “이용자” 및 법정대리인의 권리와 그 행사방법은 제소 당시의 이용자의 주소에 의하며, 주소가 없는 경우에는 거소를 관할하는 지방법원의 전속관할로 합니다. 다만, 제소 당시 이용자의 주소 또는 거소가 분명하지 않거나 외국 거주자의 경우에는 민사소송법상의 관할법원에 제기합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">③ 회사는 타사업자 또는 이용 고객과의 요금정산 및 민원처리를 위해 위치정보 이용·제공․사실 확인 자료를 자동 기록·보존하며, 해당 자료는 1년간 보관합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">④ 개인위치정보를 개인위치정보주체가 지정하는 제3자에게 제공하려는 경우에는 제공 받는자 및 제공목적을 개인위치정보주체에게 고지하고 미리 동의를 얻으며, 개인위치정보를 개인위치정보주체가 지정하는 제3자에게 제공하는 경우에는 개인위치정보를 수집한 당해통신단말장치로 매회 개인위치정보주체에게 제공받는 자, 제공일시 및 제공목적을 즉시 통보합니다. 아래 각호에 해당하는 경우 개인위치정보주체가 미리 지정한 전자우편 또는 온라인 게시 등의 방법으로 통보할 수 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">1. 개인위치정보를 수집한 당해 통신단말장치가 문자, 음성 또는 영상의 수신기능을 갖추지 아니한 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">2. 회원이 온라인 게시 등의 방법으로 통보할 것을 미리 요청한 경우</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 8 조 (개인위치정보주체의 권리)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 개인 위치정보주체는 개인위치정보의 이용·제공목적, 제공받는 자의 범위 및 위치기반서비스의 일부에 대해 동의를 유보하거나 언제든지 동의의 전부 또는 일부를 철회할 수 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 개인 위치정보주체는 언제든지 개인위치정보의 이용 또는 제공의 일시적인 중지를 요구할 수 있습니다. 이 경우 회사는 요구를 거절하지 아니하며, 이를 위한 기술적 조치를 취합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">③ 개인 위치정보주체는 회사에 대해 아래 자료의 열람 또는 고지를 요구할 수 있고, 당해 자료에 오류가 있는 경우에는 그 정정을 요구할 수 있습니다. 이 경우 회사는 정당한 이유없이 요구를 거절하지 아니합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">1. 개인 위치정보주체에 대한 위치정보 이용·제공사실 확인자료</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">2. 개인 위치정보주체의 개인위치정보가 위치정보의 보호 및 이용 등에 관한 법률 또는 다른 법률의 규정에 의해 제3자에게 제공된 이유 및 내용</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">④ 회사는 개인 위치정보주체가 위치정보 이용·제공에 대한 동의의 전부 또는 일부를 철회한 경우에는 지체 없이 개인 위치정보 및 위치정보 이용·제공사실 확인자료(동의의 일부를 철회한 경우에는 철회하는 부분의 확인자료)를 파기합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">⑤ 개인 위치정보주체는 제①항 내지 제④항의 권리행사를 위해 회사 소정의 절차를 통해 회사에 요구할 수 있습니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 9 조 (법정대리인의 권리)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 회원은 원칙상 성인으로만 가입한다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 회사는 14세 미만의 회원에 대해서는 개인위치정보를 이용한 위치기반서비스 제공 및 개인위치정보의 제3자 제공에 대한 동의를 당해 회원과 당해 회원의 법정대리인으로부터 동의를 받아야 합니다. 이 경우 법정대리인은 제9조에 의한 회원의 권리를 모두 가집니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">③ 회사는 14세 미만의 아동의 개인위치정보 또는 위치정보 이용․제공사실 확인 자료를 이용약관에 명시 또는 고지한 범위를 넘어 이용하거나 제3자에게 제공하고자 하는 경우에는 14세미만의 아동과 그 법정대리인의 동의를 받아야 합니다. 단, 아래의 경우는 제외합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">1. 위치정보 및 위치기반서비스 제공에 따른 요금정산을 위하여 위치정보 이용, 제공사실 확인 자료가 필요한 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">2. 통계작성, 학술연구 또는 시장조사를 위하여 특정 개인을 알아볼 수 없는 형태로 가공하여 제공하는 경우</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 10 조 (8세 이하의 아동 등의 보호의무자의 권리)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 회사는 아래의 경우에 해당하는 자(이하 “8세 이하의 아동”등이라 한다)의 보호의무자가 8세이하의 아동 등의 생명 또는 신체보호를 위하여 개인위치정보의 이용 또는 제공에 동의하는경우에는 본인의 동의가 있는 것으로 봅니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">1. 8세 이하의 아동</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">2. 피성년후견인</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">3. 장애인복지법 제2조제2항제2호의 규정에 의한 정신적 장애를 가진 자로서 장애인고용촉진및 직업재활법 제2조제2호의 규정에 의한 중증장애인에 해당하는 자(장애인복지법 32조 의 규정에 의하여 장애인등록을 한 자에 한한다)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 8세 이하의 아동 등의 생명 또는 신체의 보호를 위하여 개인위치정보의 이용 또는 제공에 동의를 하고자 하는 보호의무자는 서면동의서에 보호의무자임을 증명하는 서면을 첨부하여 회사에 제출하여야 합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">③ 보호의무자는 8세 이하의 아동 등의 개인위치정보 이용 또는 제공에 동의하는 경우 개인위치정보주체 권리의 전부를 행사할 수 있습니다.</span></p><p style=\"margin-left:0px;\">&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 11 조 (위치정보관리책임자의 지정)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 회사는 위치정보를 적절히 관리·보호하고 개인위치정보주체의 불만을 원활히 처리할 수 있도록 실질적인 책임을 질 수 있는 지위에 있는 자를 위치정보관리책임자로 지정해 운영합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 위치정보관리책임자는 위치기반서비스를 제공하는 부서의 부서장으로서 구체적인 사항은 본 약관의 부칙에 따릅니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 12 조 (손해배상)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 회사가 위치정보의 보호 및 이용 등에 관한 법률 제15조 내지 제26조의 규정을 위반한 행위로 회원에게 손해가 발생한 경우 회원은 회사에 대하여 손해배상 청구를 할 수 있습니다. 이 경우 회사는 고의, 과실이 없음을 입증하지 못하는 경우 책임을 면할 수 없습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 회원이 본 약관의 규정을 위반하여 회사에 손해가 발생한 경우 회사는 회원에 대하여 손해배상을 청구할 수 있습니다. 이 경우 회원은 고의, 과실이 없음을 입증하지 못하는 경우 책임을 면할 수 없습니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 13 조 (면책)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 회사는 다음 각 호의 경우로 서비스를 제공할 수 없는 경우 이로 인하여 회원에게 발생한 손해에 대해서는 책임을 부담하지 않습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">1. 천재지변 또는 이에 준하는 불가항력의 상태가 있는 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">2. 서비스 제공을 위하여 회사와 서비스 제휴계약을 체결한 제3자의 고의적인 서비스 방해가 있는 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">3. 회원의 귀책사유로 서비스 이용에 장애가 있는 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">4. 제1호 내지 제3호를 제외한 기타 회사의 고의·과실이 없는 사유로 인한 경우</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 회사는 서비스 및 서비스에 게재된 정보, 자료, 사실의 신뢰도, 정확성 등에 대해서는 보증을하지 않으며 이로 인해 발생한 회원의 손해에 대하여는 책임을 부담하지 아니합니다.</span><br>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 14 조 (규정의 준용)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 본 약관은 대한민국법령에 의하여 규정되고 이행됩니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 본 약관에 규정되지 않은 사항에 대해서는 관련법령 및 상관습에 의합니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 15 조 (분쟁의 조정 및 기타)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 회사는 위치정보와 관련된 분쟁에 대해 당사자 간 협의가 이루어지지 아니하거나 협의를할 수 없는 경우에는 위치정보의 보호 및 이용 등에 관한 법률 제28조의 규정에 의한 방송통신위원회에 재정을 신청할 수 있습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 회사 또는 고객은 위치정보와 관련된 분쟁에 대해 당사자 간 협의가 이루어지지 아니하거나협의를 할 수 없는 경우에는 개인정보보호법 제43조의 규정에 의한 개인정보분쟁조정위원회에 조정을 신청할 수 있습니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제 16 조 (회사의 연락처)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">회사의 상호 및 주소 등은 다음과 같습니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">1. 상 호 : 코로코</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">2. 대 표 자 : 김&nbsp; 기&nbsp; 준</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">3. 주 소 : 경기도 광주시 장지1길 90 1층</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">4. 대표전화 : 1660-0404</span></p><p style=\"margin-left:0px;\">&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">부 칙 제1조 (시행일)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 이 약관은 2024년 07월 08일부터 시행한다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">제2조 (위치정보관리책임자 지정)</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">① 위치정보관리책임자는 2024년 07월을 기준으로 다음과 같이 지정합니다.</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">② 위치정보관리책임자는 2024년 07월 08일을 기준으로 다음과 같이 지정합니다.</span></p><p>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">1. 회 사 : 코로코</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">2. 성 명 : 김 기 준</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">3. 앱이름 : 맛집바로</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">4. 개발자 : 김기준</span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:;\">5. 대표전화 : 1660-0404</span></p>','<p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>위치정보 이용약관</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">코로코 주식회사(이하 \"회사\")는 위치정보를 활용하여 다음과 같은 서비스를 제공합니다.</span></p><p><br>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>1. 위치정보 이용 목적</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">회사는 다음 목적으로 위치정보를 이용합니다.</span></p><ul><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">주변 맛집 검색</span><br><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">위치 기반 서비스 제공</span><br><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">서비스 개선</span><br><br>&nbsp;</p></li></ul><p><br>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>2. 위치정보 수집 방법</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">회사는 다음 방법으로 위치정보를 수집할 수 있습니다.</span></p><ul><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">GPS</span><br><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">WiFi</span><br><br>&nbsp;</p></li><li><p style=\"margin-left:0pt;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">이동통신 네트워크</span><br><br>&nbsp;</p></li></ul><p><br>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>3. 위치정보 보관 기간</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">회사는 위치정보를 서비스 제공 목적 범위 내에서만 이용하며 목적 달성 시 파기합니다.</span></p><p><br>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>4. 이용자의 권리</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">이용자는 언제든지 위치정보 제공을 거부하거나 설정을 변경할 수 있습니다.</span></p><p><br>&nbsp;</p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\"><strong>5. 책임</strong></span></p><p style=\"margin-left:0px;\"><span style=\"color:rgb(0,0,0);font-family:Arial;\">회사는 위치정보 관련 법령을 준수합니다.</span></p>',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','','','','','','','','(주)맛집바로','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,1,'1',0,0,NULL,NULL,NULL,NULL,NULL,'','admin,super,master,root,administrator,system,sysadmin,guest,user,test,operator,manager,owner,webmaster,moderator,service,account,accounts,login,logout,auth,authentication,info,contact,faq,qna,notice,news,mail,email,ceo,cto,cfo,staff,board,official,office,security,member,event\r\n','18아,18놈,18새끼,18년,18뇬,18노,18것,18넘,개년,개놈,개뇬,개새,개색끼,개세끼,개세이,개쉐이,개쉑,개쉽,개시키,개자식,개좆,게색기,게색끼,광뇬,뇬,눈깔,뉘미럴,니귀미,니기미,니미,도촬,되질래,뒈져라,뒈진다,디져라,디진다,디질래,병쉰,병신,뻐큐,뻑큐,뽁큐,삐리넷,새꺄,쉬발,쉬밸,쉬팔,쉽알,스패킹,스팽,시벌,시부랄,시부럴,시부리,시불,시브랄,시팍,시팔,시펄,실밸,십8,십쌔,십창,싶알,쌉년,썅놈,쌔끼,쌩쑈,썅,써벌,썩을년,쎄꺄,쎄엑,쓰바,쓰발,쓰벌,쓰팔,씨8,씨댕,씨바,씨발,씨뱅,씨봉알,씨부랄,씨부럴,씨부렁,씨부리,씨불,씨브랄,씨빠,씨빨,씨뽀랄,씨팍,씨팔,씨펄,씹,아가리,아갈이,엄창,접년,잡놈,재랄,저주글,조까,조빠,조쟁이,조지냐,조진다,조질래,존나,존니,좀물,좁년,좃,좆,좇,쥐랄,쥐롤,쥬디,지랄,지럴,지롤,지미랄,쫍빱,凸,퍽큐,뻑큐,빠큐,ㅅㅂㄹㅁ,해외선물,선물옵션,해외파생','','','','no','','','','0.0.1','1','0.0.1','1');
/*!40000 ALTER TABLE `setup_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_break_t`
--

DROP TABLE IF EXISTS `shop_break_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_break_t` (
  `sh_idx` int(11) NOT NULL COMMENT '매장 키(shop_t.idx)',
  `start_time` time DEFAULT NULL COMMENT 'NULL이면 브레이크 사용안함',
  `end_time` time DEFAULT NULL COMMENT 'NULL이면 브레이크 사용안함',
  `bk_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  `bk_udate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`sh_idx`),
  CONSTRAINT `fk_shop_break_shop` FOREIGN KEY (`sh_idx`) REFERENCES `shop_t` (`idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='매장 공통 브레이크타임(1행)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_break_t`
--
-- WHERE:  true limit 10

LOCK TABLES `shop_break_t` WRITE;
/*!40000 ALTER TABLE `shop_break_t` DISABLE KEYS */;
INSERT INTO `shop_break_t` VALUES
(46,'15:00:00','16:00:00','2026-03-31 14:40:22','2026-03-31 14:40:22'),
(47,'15:00:00','16:00:00','2026-03-31 15:28:00','2026-03-31 15:28:16'),
(48,NULL,NULL,'2026-04-07 14:27:16','2026-04-07 14:27:16');
/*!40000 ALTER TABLE `shop_break_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_category_t`
--

DROP TABLE IF EXISTS `shop_category_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_category_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `sh_idx` int(11) DEFAULT NULL COMMENT '매장 id',
  `sc_title` varchar(255) DEFAULT NULL,
  `sc_memo` varchar(255) DEFAULT NULL COMMENT '메모',
  `sc_show` enum('Y','N') DEFAULT 'Y',
  `sc_order` int(11) DEFAULT NULL,
  `sc_wdate` datetime DEFAULT NULL,
  `sc_udate` datetime DEFAULT NULL,
  `sc_del` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  KEY `shop_category_t_shop_t_idx_fk` (`sh_idx`),
  CONSTRAINT `shop_category_t_shop_t_idx_fk` FOREIGN KEY (`sh_idx`) REFERENCES `shop_t` (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_category_t`
--
-- WHERE:  true limit 10

LOCK TABLES `shop_category_t` WRITE;
/*!40000 ALTER TABLE `shop_category_t` DISABLE KEYS */;
INSERT INTO `shop_category_t` VALUES
(25,46,'식사메뉴',NULL,'Y',2,'2026-03-31 15:11:42','2026-03-31 15:18:37',NULL),
(26,46,'음료',NULL,'Y',1,'2026-03-31 15:18:32','2026-03-31 15:18:43',NULL),
(27,47,'식사',NULL,'Y',1,'2026-04-01 10:04:05','2026-04-03 09:35:20',NULL),
(28,47,'피자',NULL,'Y',2,'2026-04-03 09:35:12','2026-04-03 09:35:28',NULL),
(29,47,'세트메뉴',NULL,'Y',3,'2026-04-03 09:37:40','2026-04-03 09:37:40',NULL),
(30,48,'한식',NULL,'Y',1,'2026-04-07 13:29:45','2026-04-07 13:29:45',NULL);
/*!40000 ALTER TABLE `shop_category_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_hours_t`
--

DROP TABLE IF EXISTS `shop_hours_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_hours_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `sh_idx` int(11) NOT NULL COMMENT '매장 키(shop_t.idx)',
  `dow` tinyint(4) NOT NULL COMMENT '요일(0~6) 0=일,1=월..6=토',
  `bt_type` enum('OPEN','CLOSE') NOT NULL COMMENT 'OPEN=영업, CLOSE=휴무',
  `start_time` time DEFAULT NULL COMMENT 'OPEN일 때만 사용',
  `end_time` time DEFAULT NULL COMMENT 'OPEN일 때만 사용',
  `bt_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  `bt_udate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idx`),
  UNIQUE KEY `uq_shop_dow` (`sh_idx`,`dow`),
  CONSTRAINT `fk_shop_hours_shop` FOREIGN KEY (`sh_idx`) REFERENCES `shop_t` (`idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='매장 요일별 영업/휴무(요일당 1행)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_hours_t`
--
-- WHERE:  true limit 10

LOCK TABLES `shop_hours_t` WRITE;
/*!40000 ALTER TABLE `shop_hours_t` DISABLE KEYS */;
INSERT INTO `shop_hours_t` VALUES
(36,46,0,'CLOSE',NULL,NULL,'2026-03-31 14:40:22','2026-03-31 14:40:22'),
(37,46,1,'OPEN','09:00:00','20:00:00','2026-03-31 14:40:22','2026-03-31 14:40:22'),
(38,46,2,'OPEN','09:00:00','20:00:00','2026-03-31 14:40:22','2026-03-31 14:40:22'),
(39,46,3,'OPEN','09:00:00','20:00:00','2026-03-31 14:40:22','2026-03-31 14:40:22'),
(40,46,4,'OPEN','09:00:00','20:00:00','2026-03-31 14:40:22','2026-03-31 14:40:22'),
(41,46,5,'OPEN','09:00:00','20:00:00','2026-03-31 14:40:22','2026-03-31 14:40:22'),
(42,46,6,'OPEN','09:00:00','18:00:00','2026-03-31 14:40:22','2026-03-31 15:10:20'),
(43,47,0,'OPEN','09:00:00','18:00:00','2026-03-31 15:28:00','2026-03-31 15:28:16'),
(44,47,1,'CLOSE',NULL,NULL,'2026-03-31 15:28:00','2026-03-31 15:28:00'),
(45,47,2,'OPEN','09:00:00','18:00:00','2026-03-31 15:28:00','2026-03-31 15:28:16');
/*!40000 ALTER TABLE `shop_hours_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_menu_t`
--

DROP TABLE IF EXISTS `shop_menu_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_menu_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `sc_idx` int(11) DEFAULT NULL COMMENT '메뉴 카테고리 키',
  `sm_title` varchar(255) DEFAULT NULL COMMENT '메뉴 명',
  `sm_image` varchar(255) DEFAULT NULL COMMENT '메뉴 이미지',
  `sm_contents` text DEFAULT NULL COMMENT '메뉴 상세설명',
  `sm_price` int(11) DEFAULT NULL COMMENT '메뉴 금액',
  `sm_su` int(11) DEFAULT NULL COMMENT '재고 수량',
  `sm_type` enum('Y','N') DEFAULT 'Y' COMMENT '판매 중지',
  `sm_age_show` enum('N','Y') DEFAULT NULL COMMENT '나이제한 N: 전체, Y: 19세 이상',
  `sm_main` enum('N','Y') DEFAULT 'N' COMMENT '추천메뉴',
  `sm_show` enum('Y','N') DEFAULT 'Y' COMMENT '노출여부',
  `sm_order` int(11) DEFAULT NULL COMMENT '순서',
  `sm_wdate` datetime DEFAULT NULL,
  `sm_udate` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  KEY `shop_menu_t_shop_category_t_idx_fk` (`sc_idx`),
  CONSTRAINT `shop_menu_t_shop_category_t_idx_fk` FOREIGN KEY (`sc_idx`) REFERENCES `shop_category_t` (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_menu_t`
--
-- WHERE:  true limit 10

LOCK TABLES `shop_menu_t` WRITE;
/*!40000 ALTER TABLE `shop_menu_t` DISABLE KEYS */;
INSERT INTO `shop_menu_t` VALUES
(32,25,'1996년 보양사골 대치칼국수','menu_1775174822_23724.jpg','쫄깃한 면발과 보양 사골육수가 일품인 메뉴',10000,NULL,'Y','N','Y','Y',1,'2026-03-31 15:18:23','2026-04-06 17:40:40'),
(33,26,'콜라','menu_1774937943_43873.png','',1000,NULL,'Y','N','N','Y',1,'2026-03-31 15:19:03','2026-03-31 15:19:03'),
(34,26,'맥주','menu_1774937967_30218.png','',10000,NULL,'Y','Y','N','Y',1,'2026-03-31 15:19:27','2026-03-31 15:19:27'),
(35,27,'비프웰링턴(한정수량20)','menu_1775176283_82987.jpg','소안심을 페스츄리생지에 말아 오븐에 구운 음식 (살짝 매콤한 트러플향 소스) *25분 소요',62000,NULL,'Y','N','N','Y',1,'2026-04-01 10:04:28','2026-04-03 09:31:23'),
(36,25,'콩국수(국산콩)','menu_1775174886_62205.png','한식명인 대표 고향 안동에서 가족이 직접 농사지은 콩을 정성껏 갈아 만든 콩국수',9000,NULL,'Y','N','N','Y',1,'2026-04-03 09:08:06','2026-04-03 09:08:06'),
(37,25,'낙지비빔칼국수','menu_1775175007_83891.jpg','[신메뉴]신선한 낙지와 특제소스를 넣고 탱글한 칼국수 면발로 식감을 살린 비빔칼국수',12000,NULL,'Y','N','N','Y',1,'2026-04-03 09:10:07','2026-04-03 09:10:07'),
(38,27,'바질냉파스타(여름한정)','menu_1775176369_25037.jpg','수제바질페스토와 부라타치즈가 올라간 바질냉파스타 (숏파스타) - 여름한정메뉴',23500,NULL,'Y','N','N','Y',1,'2026-04-03 09:32:49','2026-04-03 09:32:49'),
(39,27,'왕문어먹물리조토','menu_1775176476_16603.jpg','왕문어다리를 올린 먹물 크림 리조토로 깊고 진한 풍미를 즐길 수 있는 메뉴',22500,NULL,'Y','N','N','Y',1,'2026-04-03 09:34:17','2026-04-03 09:34:36'),
(40,28,'가든씬피자','menu_1775176624_35605.jpg','토마토소스에 시금치와 견과류, 프로슈토와 까망베르치즈를 올린 씬피자',20500,NULL,'Y','N','N','Y',1,'2026-04-03 09:37:04','2026-04-03 09:44:02'),
(41,29,'2인세트메뉴 A','menu_1775176718_61608.png','식전빵 + 샐러드 or 씬피자 + 비프웰링턴 + 파스타 or 리조토 + 음료2잔',118000,NULL,'Y','N','N','Y',1,'2026-04-03 09:38:38','2026-04-03 09:38:38');
/*!40000 ALTER TABLE `shop_menu_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_reserve_penalty_t`
--

DROP TABLE IF EXISTS `shop_reserve_penalty_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_reserve_penalty_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `rs_idx` int(11) NOT NULL COMMENT 'shop_reserve_setting_t.idx',
  `rp_use` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '위약금 사용 여부',
  `rp_type` enum('FIXED','PERCENT') NOT NULL DEFAULT 'FIXED' COMMENT 'FIXED=고정금액, PERCENT=%',
  `rp_value` int(11) NOT NULL DEFAULT 0 COMMENT '위약금 값(FIXED:원, PERCENT:%)',
  `rp_free_cancel_before_min` int(11) NOT NULL DEFAULT 0 COMMENT '무료취소 가능 시간(분) ex) 24시간=1440',
  `rp_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  `rp_udate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idx`),
  UNIQUE KEY `uq_srp` (`rs_idx`),
  KEY `ix_srp_rs` (`rs_idx`),
  CONSTRAINT `fk_srp_setting` FOREIGN KEY (`rs_idx`) REFERENCES `shop_reserve_setting_t` (`idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='매장 예약 위약금 설정';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_reserve_penalty_t`
--
-- WHERE:  true limit 10

LOCK TABLES `shop_reserve_penalty_t` WRITE;
/*!40000 ALTER TABLE `shop_reserve_penalty_t` DISABLE KEYS */;
INSERT INTO `shop_reserve_penalty_t` VALUES
(9,5,'Y','FIXED',1000,1440,'2026-03-31 14:40:50','2026-04-02 15:25:32'),
(10,6,'Y','FIXED',0,1440,'2026-03-31 15:50:24','2026-03-31 15:50:24');
/*!40000 ALTER TABLE `shop_reserve_penalty_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_reserve_setting_t`
--

DROP TABLE IF EXISTS `shop_reserve_setting_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_reserve_setting_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `sh_idx` int(11) NOT NULL COMMENT '매장 키(shop_t.idx)',
  `rs_notice` varchar(300) DEFAULT NULL COMMENT '예약 안내글(최대 300자 권장)',
  `rs_allow_same_day` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '당일 예약 허용 여부',
  `rs_max_reserve_days` smallint(6) NOT NULL DEFAULT 0 COMMENT '최대 예약 가능 일수(0=제한없음)',
  `rs_min_person` smallint(6) NOT NULL DEFAULT 1 COMMENT '최소 예약 인원',
  `rs_max_person` smallint(6) NOT NULL DEFAULT 1 COMMENT '최대 예약 인원',
  `rs_slot_unit_min` smallint(6) NOT NULL DEFAULT 30 COMMENT '예약 슬롯 단위(분) (현재 UI엔 없지만 운영상 유용)',
  `rs_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  `rs_udate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idx`),
  UNIQUE KEY `uq_srs_shop` (`sh_idx`),
  KEY `ix_srs_shop` (`sh_idx`),
  CONSTRAINT `fk_srs_shop` FOREIGN KEY (`sh_idx`) REFERENCES `shop_t` (`idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='매장 예약 기본 설정';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_reserve_setting_t`
--
-- WHERE:  true limit 10

LOCK TABLES `shop_reserve_setting_t` WRITE;
/*!40000 ALTER TABLE `shop_reserve_setting_t` DISABLE KEYS */;
INSERT INTO `shop_reserve_setting_t` VALUES
(5,46,'예약시 착수금으로 진행이 되며 하루전 예약 취소는 전액 환불이 가능합니다.','Y',0,1,2,30,'2026-03-31 14:40:50','2026-03-31 15:11:14'),
(6,47,'','Y',0,1,1,30,'2026-03-31 15:50:24','2026-03-31 15:50:24');
/*!40000 ALTER TABLE `shop_reserve_setting_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_reserve_slot_t`
--

DROP TABLE IF EXISTS `shop_reserve_slot_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_reserve_slot_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `rs_idx` int(11) NOT NULL COMMENT 'shop_reserve_setting_t.idx',
  `slot_use` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '해당 시간대 사용 여부(스위치)',
  `slot_day_type` enum('WEEKDAY','SAT','SUN') NOT NULL COMMENT 'WEEKDAY=평일, SAT=토, SUN=일',
  `slot_hour` tinyint(4) NOT NULL COMMENT '1~24',
  `slot_minute` tinyint(4) NOT NULL COMMENT '0~60',
  `slot_max_count` int(11) NOT NULL DEFAULT 1 COMMENT '예약건수(기본 1)',
  `slot_sort` int(11) NOT NULL DEFAULT 0,
  `slot_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  `slot_udate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idx`),
  UNIQUE KEY `uq_slot` (`rs_idx`,`slot_day_type`,`slot_hour`,`slot_minute`),
  KEY `ix_slot_day_time` (`rs_idx`,`slot_day_type`,`slot_hour`,`slot_minute`),
  KEY `ix_slot_rs` (`rs_idx`),
  CONSTRAINT `fk_slot_setting` FOREIGN KEY (`rs_idx`) REFERENCES `shop_reserve_setting_t` (`idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='매장 예약 가능 시간대(슬롯)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_reserve_slot_t`
--
-- WHERE:  true limit 10

LOCK TABLES `shop_reserve_slot_t` WRITE;
/*!40000 ALTER TABLE `shop_reserve_slot_t` DISABLE KEYS */;
INSERT INTO `shop_reserve_slot_t` VALUES
(41,6,'Y','WEEKDAY',7,0,1,1,'2026-03-31 15:50:24','2026-03-31 15:50:24'),
(42,6,'Y','WEEKDAY',7,30,1,2,'2026-03-31 15:50:24','2026-03-31 15:50:24'),
(78,5,'Y','WEEKDAY',7,0,1,1,'2026-04-02 15:25:32','2026-04-02 15:25:32'),
(79,5,'Y','WEEKDAY',7,30,1,2,'2026-04-02 15:25:32','2026-04-02 15:25:32'),
(80,5,'Y','WEEKDAY',17,0,1,3,'2026-04-02 15:25:32','2026-04-02 15:25:32'),
(81,5,'Y','WEEKDAY',18,0,1,4,'2026-04-02 15:25:32','2026-04-02 15:25:32'),
(82,5,'Y','SAT',10,0,2,5,'2026-04-02 15:25:32','2026-04-02 15:25:32');
/*!40000 ALTER TABLE `shop_reserve_slot_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_t`
--

DROP TABLE IF EXISTS `shop_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `mb_idx` int(11) NOT NULL COMMENT '작성자 키',
  `sh_title` varchar(150) NOT NULL COMMENT '매장 이름',
  `sh_category` varchar(50) DEFAULT NULL COMMENT '업종 (예: 한식, 카페, 스파게티 파스타전문)',
  `sh_contents` text DEFAULT NULL COMMENT '매장 소개',
  `sh_corp_nm` varchar(255) DEFAULT NULL COMMENT '법인명',
  `sh_biz_no` varchar(255) DEFAULT NULL COMMENT '사업자번호',
  `sh_ceo_nm` varchar(255) DEFAULT NULL COMMENT '대표자명',
  `sh_biz_file` varchar(255) DEFAULT NULL COMMENT '사업자등록증',
  `sh_branch_nm` varchar(255) DEFAULT NULL COMMENT '지점명',
  `sh_zip` varchar(255) DEFAULT NULL COMMENT '우편번호',
  `sh_addr1` varchar(255) DEFAULT NULL COMMENT '주소',
  `sh_addr2` varchar(255) DEFAULT NULL COMMENT '상세주소',
  `sh_lat` varchar(255) DEFAULT NULL COMMENT '위도',
  `sh_lng` varchar(255) DEFAULT NULL COMMENT '경도',
  `sh_img1` varchar(255) DEFAULT NULL COMMENT '매장이미지1',
  `sh_img2` varchar(255) DEFAULT NULL COMMENT '매장이미지2',
  `sh_img3` varchar(255) DEFAULT NULL COMMENT '매장이미지3',
  `sh_img4` varchar(255) DEFAULT NULL,
  `sh_img5` varchar(255) DEFAULT NULL,
  `sh_bank` varchar(255) DEFAULT NULL COMMENT '은행명',
  `sh_bank_holder` varchar(255) DEFAULT NULL COMMENT '예금주',
  `sh_bank_account` varchar(255) DEFAULT NULL COMMENT '계좌번호',
  `sh_bankbook` varchar(255) DEFAULT NULL COMMENT '통장사본',
  `sh_show` enum('Y','N') DEFAULT 'Y' COMMENT '영업 상태 Y: 영업중, N: 문닫음',
  `del_date` datetime DEFAULT NULL,
  `sh_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  `sh_udate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `sh_tel` varchar(30) DEFAULT NULL COMMENT '매장 전화번호',
  `sh_qr_yn` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '테이블 QR 주문 사용 여부',
  `sh_qr_pay_type` enum('PREPAY','POSTPAY') NOT NULL DEFAULT 'PREPAY' COMMENT 'QR 주문 결제방식(선결제/후불결제)',
  `sh_reserve_yn` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '예약 기능 사용 여부',
  `sh_reserve_pay_type` enum('PREPAY','POSTPAY') DEFAULT 'PREPAY' COMMENT '예약 주문 결제방식(선결제/후불결제)',
  `sh_takeout_yn` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '포장 주문 가능여부',
  `sh_lat_num` double DEFAULT NULL,
  `sh_lng_num` double DEFAULT NULL,
  PRIMARY KEY (`idx`),
  KEY `ix_sh_mb` (`mb_idx`),
  KEY `idx_shop_map_bounds` (`sh_show`,`del_date`,`sh_lat_num`,`sh_lng_num`),
  KEY `idx_shop_addr1` (`sh_addr1`(32)),
  KEY `idx_shop_t_category` (`sh_category`),
  CONSTRAINT `fk_sh_member` FOREIGN KEY (`mb_idx`) REFERENCES `member_t` (`idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=493809 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='매장';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_t`
--
-- WHERE:  true limit 10

LOCK TABLES `shop_t` WRITE;
/*!40000 ALTER TABLE `shop_t` DISABLE KEYS */;
INSERT INTO `shop_t` VALUES
(46,74,'서울원조집',NULL,'','서울원조집','010123455678','테스트','biz_20260331053905_5953.png','','03175','서울 종로구 경희궁길 4','10-10','37.5705863977953','126.972155304519','img1_sh46_20260403091954_91a4fce192c0.png','img2_sh46_20260403091954_8dd2ce589689.jpg','img3_sh46_20260403091954_4359c7052e3a.png','img4_sh46_20260403091954_1454d9c15733.jpg','','kbbank','테스트','010000000000','20260331053905_4768.png','Y',NULL,'2026-03-31 14:39:05','2026-04-17 14:19:36','01000000000','Y','POSTPAY','Y','POSTPAY','Y',37.5705863977953,126.972155304519),
(47,78,'성수파스타리아',NULL,'','성수파스타리아','101023213213','테스트2','biz_20260331152411_8475.png','','04111','서울 마포구 서강대길 3','10-10','37.5494698710378','126.939344606101','img1_sh47_20260403093037_e1bd1406a010.png','img2_sh47_20260403093037_c311502e4d67.png','img3_sh47_20260403093037_fca82f58b90e.jpg','img4_sh47_20260403093037_69729e62e2fa.jpg','','kbbank','테스트2','120120321031203','20260331152411_3994.png','Y',NULL,'2026-03-31 15:24:11','2026-04-17 14:19:36','01000000001','Y','POSTPAY','Y','PREPAY','Y',37.5494698710378,126.939344606101),
(48,83,'소담한하루',NULL,NULL,'(주)코로코','1268664275','임재득,임영묵','biz_20260407132221_7958.png',NULL,'12777','경기 광주시 장지1길 90','B1층','37.4007300169504','127.244103947596',NULL,NULL,NULL,NULL,NULL,'shinhan','(주)코로코','100030285904','20260407132221_5372.jpeg','Y',NULL,'2026-04-07 13:22:21','2026-04-17 14:19:36','0310000000','Y','PREPAY','Y','PREPAY','Y',37.4007300169504,127.244103947596),
(49,89,'삽다리식당','한식',NULL,NULL,NULL,NULL,NULL,NULL,'18567','경기도 화성시 우정읍 조암로 17',NULL,'37.0847243544353','126.819295531922',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Y',NULL,'2024-12-22 00:00:00','2024-12-22 00:00:00','031-358-6424','Y','PREPAY','Y','PREPAY','Y',37.0847243544353,126.819295531922),
(50,90,'뚜레쥬르 판교파미어스몰점','베이커리',NULL,NULL,NULL,NULL,NULL,NULL,'13449','경기도 성남시 수정구 창업로 17 B동 103-2호 (시흥동  판교아이스퀘어)',NULL,'37.4120131608179','127.097830310006',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Y',NULL,'2024-12-22 00:00:00','2024-12-22 00:00:00','031-8039-6424','Y','PREPAY','Y','PREPAY','Y',37.4120131608179,127.097830310006),
(51,91,'위스키볼트','바(BAR)',NULL,NULL,NULL,NULL,NULL,NULL,'16862','경기도 용인시 수지구 수지로 124 지하1층 B03호',NULL,'37.3126839307817','127.079741790382',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Y',NULL,'2024-12-22 00:00:00','2024-12-22 00:00:00','031-264-6424','Y','PREPAY','Y','PREPAY','Y',37.3126839307817,127.079741790382),
(52,92,'발리맥주 본점','요리주점',NULL,NULL,NULL,NULL,NULL,NULL,'14566','경기도 부천시 원미구 조마루로297번길 33',NULL,'37.4991772389382','126.776228325111',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Y',NULL,'2024-12-22 00:00:00','2024-12-22 00:00:00','0507-1338-6424','Y','PREPAY','Y','PREPAY','Y',37.4991772389382,126.776228325111),
(53,93,'후토루 정자점','분식',NULL,NULL,NULL,NULL,NULL,NULL,'13616','경기도 성남시 분당구 정자일로 135 정자3차푸르지오시티 d114호',NULL,'37.3622215565015','127.105270741711',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Y',NULL,'2024-12-22 00:00:00','2024-12-22 00:00:00','0507-1389-6424','Y','PREPAY','Y','PREPAY','Y',37.3622215565015,127.105270741711),
(54,94,'빵에갸또','베이커리',NULL,NULL,NULL,NULL,NULL,NULL,'12105','경기도 남양주시 두물로39번길 25 1층',NULL,'37.6580852251838','127.127937957506',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Y',NULL,'2024-12-22 00:00:00','2024-12-22 00:00:00','031-528-6424','Y','PREPAY','Y','PREPAY','Y',37.6580852251838,127.127937957506),
(55,95,'카리스커피','카페',NULL,NULL,NULL,NULL,NULL,NULL,'14120','경기도 안양시 동안구 엘에스로 76 가동 240호',NULL,'37.3700483081148','126.953128673007',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Y',NULL,'2024-12-22 00:00:00','2024-12-22 00:00:00','031-479-6688','Y','PREPAY','Y','PREPAY','Y',37.3700483081148,126.953128673007);
/*!40000 ALTER TABLE `shop_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_table_qr_t`
--

DROP TABLE IF EXISTS `shop_table_qr_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_table_qr_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `sh_idx` int(11) NOT NULL COMMENT '매장 키 (shop_t.idx)',
  `tb_idx` int(11) NOT NULL COMMENT '테이블 키 (shop_table_t.idx)',
  `qr_token` varchar(64) DEFAULT NULL,
  `qr_text` varchar(500) NOT NULL COMMENT 'QR에 인코딩된 URL',
  `qr_file` varchar(255) NOT NULL COMMENT 'QR 이미지 경로(/data/qr/...)',
  `qr_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  `qr_udate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idx`),
  UNIQUE KEY `uq_qr_table` (`tb_idx`),
  UNIQUE KEY `uq_qr_token` (`qr_token`),
  KEY `idx_sh_tb` (`sh_idx`,`tb_idx`),
  CONSTRAINT `fk_qr_shop` FOREIGN KEY (`sh_idx`) REFERENCES `shop_t` (`idx`) ON DELETE CASCADE,
  CONSTRAINT `fk_qr_table` FOREIGN KEY (`tb_idx`) REFERENCES `shop_table_t` (`idx`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='테이블별 QR 코드';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_table_qr_t`
--
-- WHERE:  true limit 10

LOCK TABLES `shop_table_qr_t` WRITE;
/*!40000 ALTER TABLE `shop_table_qr_t` DISABLE KEYS */;
INSERT INTO `shop_table_qr_t` VALUES
(3,47,3,'1dee4e626442719d497884976e6e55cd7e3cdc32e7707d1b484b7ee6166fd5c4','https://barorez.com/app?tk=1dee4e626442719d497884976e6e55cd7e3cdc32e7707d1b484b7ee6166fd5c4','/data/qr/47/table_3.png','2026-03-31 15:25:22','2026-03-31 15:25:22'),
(4,47,4,'d77e34b044958286c36134707ed736019390fe9322f2816768a0e8ef941be04d','https://barorez.com/app?tk=d77e34b044958286c36134707ed736019390fe9322f2816768a0e8ef941be04d','/data/qr/47/table_4.png','2026-03-31 15:25:32','2026-03-31 15:25:32'),
(5,46,5,'498cf89d145df9aca91b6d581d81f1f32b0a0ea7cc3263970fb815d99a3462ae','https://barorez.com/app?tk=498cf89d145df9aca91b6d581d81f1f32b0a0ea7cc3263970fb815d99a3462ae','/data/qr/46/table_5.png','2026-04-02 15:43:20','2026-04-02 15:43:20'),
(6,48,6,'1b0cbc095dce2f7a1ac5c97a6acfc22b8b853cf20e9830152e73106c40cf0134','https://barorez.com/app?tk=1b0cbc095dce2f7a1ac5c97a6acfc22b8b853cf20e9830152e73106c40cf0134','/data/qr/48/table_6.png','2026-04-07 13:28:07','2026-04-07 13:28:07'),
(7,48,7,'c62d2a89ed592052f0ee1ab65a1d6fce6d2aac227de8485f57dc808b78ac1ef2','https://barorez.com/app?tk=c62d2a89ed592052f0ee1ab65a1d6fce6d2aac227de8485f57dc808b78ac1ef2','/data/qr/48/table_7.png','2026-04-07 13:28:36','2026-04-07 13:28:36'),
(8,48,8,'cd47f752f376ae39dfac9a0b6fef342bc4518244e9fa26482adff4601d0326d7','https://barorez.com/app?tk=cd47f752f376ae39dfac9a0b6fef342bc4518244e9fa26482adff4601d0326d7','/data/qr/48/table_8.png','2026-04-07 13:28:53','2026-04-07 13:28:53'),
(9,48,9,'4d2e064f66328eb656a2cdff095effa0b154dd7ebc61e40b7a3a0b0019b8b630','https://barorez.com/app?tk=4d2e064f66328eb656a2cdff095effa0b154dd7ebc61e40b7a3a0b0019b8b630','/data/qr/48/table_9.png','2026-04-07 13:29:09','2026-04-07 13:29:09'),
(10,46,10,'4779735761174e8fd42de7f457791fec694a84655df5767036c788ef35721e5d','https://barorez.com/app?tk=4779735761174e8fd42de7f457791fec694a84655df5767036c788ef35721e5d','/data/qr/46/table_10.png','2026-04-08 17:10:35','2026-04-08 17:10:35');
/*!40000 ALTER TABLE `shop_table_qr_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_table_t`
--

DROP TABLE IF EXISTS `shop_table_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_table_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `sh_idx` int(11) NOT NULL COMMENT '매장 키 (shop_t.idx)',
  `tb_name` varchar(100) NOT NULL COMMENT '테이블명',
  `tb_no` int(11) DEFAULT NULL COMMENT '정렬용 번호',
  `tb_seats` int(11) NOT NULL DEFAULT 2 COMMENT '좌석 수',
  `use_yn` enum('Y','N') NOT NULL DEFAULT 'Y',
  `tb_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  `tb_udate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idx`),
  KEY `fk_shop_table3_shop` (`sh_idx`),
  CONSTRAINT `fk_shop_table3_shop` FOREIGN KEY (`sh_idx`) REFERENCES `shop_t` (`idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='매장 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_table_t`
--
-- WHERE:  true limit 10

LOCK TABLES `shop_table_t` WRITE;
/*!40000 ALTER TABLE `shop_table_t` DISABLE KEYS */;
INSERT INTO `shop_table_t` VALUES
(3,47,'a',1,2,'Y','2026-03-31 15:25:22','2026-03-31 15:25:22'),
(4,47,'b',2,2,'Y','2026-03-31 15:25:32','2026-03-31 15:25:32'),
(5,46,'1',1,2,'Y','2026-04-02 15:43:20','2026-04-02 15:43:20'),
(6,48,'1',1,4,'Y','2026-04-07 13:28:07','2026-04-07 13:28:07'),
(7,48,'2',2,4,'Y','2026-04-07 13:28:36','2026-04-07 13:28:36'),
(8,48,'3',3,4,'Y','2026-04-07 13:28:53','2026-04-07 13:28:53'),
(9,48,'4',4,4,'Y','2026-04-07 13:29:09','2026-04-07 13:29:09'),
(10,46,'a',2,4,'Y','2026-04-08 17:10:35','2026-04-08 17:10:35');
/*!40000 ALTER TABLE `shop_table_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_temp_holiday_t`
--

DROP TABLE IF EXISTS `shop_temp_holiday_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_temp_holiday_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `sh_idx` int(11) NOT NULL COMMENT '매장 키(shop_t.idx)',
  `start_date` date NOT NULL COMMENT '휴무 시작일',
  `end_date` date NOT NULL COMMENT '휴무 종료일',
  `memo` varchar(255) DEFAULT NULL COMMENT '메모(선택)',
  `use_yn` enum('Y','N') NOT NULL DEFAULT 'Y',
  `th_wdate` datetime NOT NULL DEFAULT current_timestamp(),
  `th_udate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idx`),
  KEY `ix_sth_range` (`sh_idx`,`start_date`,`end_date`),
  KEY `ix_sth_shop` (`sh_idx`),
  CONSTRAINT `fk_sth_shop` FOREIGN KEY (`sh_idx`) REFERENCES `shop_t` (`idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='매장 임시휴무(기간)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_temp_holiday_t`
--
-- WHERE:  true limit 10

LOCK TABLES `shop_temp_holiday_t` WRITE;
/*!40000 ALTER TABLE `shop_temp_holiday_t` DISABLE KEYS */;
INSERT INTO `shop_temp_holiday_t` VALUES
(3,46,'2026-04-02','2026-04-03',NULL,'N','2026-03-31 15:10:08','2026-04-03 09:15:39'),
(4,47,'2026-03-31','2026-04-02',NULL,'N','2026-03-31 15:27:58','2026-04-03 09:43:05');
/*!40000 ALTER TABLE `shop_temp_holiday_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `table_visit_t`
--

DROP TABLE IF EXISTS `table_visit_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `table_visit_t` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `visit_key` varchar(64) NOT NULL,
  `sh_idx` int(11) NOT NULL COMMENT '매장 키',
  `tv_table` varchar(255) NOT NULL COMMENT '테이블 번호(문자열 허용)',
  `mt_idx` int(11) DEFAULT NULL COMMENT '회원 키(비회원이면 NULL)',
  `tv_status` enum('ACTIVE','CLOSED') NOT NULL DEFAULT 'ACTIVE',
  `tv_started` datetime NOT NULL,
  `tv_last_active` datetime NOT NULL,
  `tv_ended` datetime DEFAULT NULL,
  `tv_ip` varchar(45) DEFAULT NULL,
  `tv_ua` varchar(255) DEFAULT NULL,
  `tv_last_seen_order_idx` int(11) NOT NULL DEFAULT 0 COMMENT '마지막 확인(접수 처리 시점) 주문 idx',
  `tv_udate` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  KEY `ix_member_status` (`mt_idx`,`tv_status`),
  KEY `ix_shop_table_status` (`sh_idx`,`tv_table`,`tv_status`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='QR 방문(테이블 이용) 세션';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `table_visit_t`
--
-- WHERE:  true limit 10

LOCK TABLES `table_visit_t` WRITE;
/*!40000 ALTER TABLE `table_visit_t` DISABLE KEYS */;
INSERT INTO `table_visit_t` VALUES
(5,'a9cfbb3a127bbdc61a6a48304db984e0e63e705904852dd6fcabfb6ce8f1e9bc',46,'1',NULL,'CLOSED','2026-04-02 15:43:34','2026-04-02 15:48:55','2026-04-02 17:20:44','162.158.193.117','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36',0,NULL),
(6,'3b115337397eba2c7a617d5f3157515cda7adfa249f378ffb5e65a6755e61d57',46,'1',NULL,'ACTIVE','2026-04-02 15:49:03','2026-04-02 15:49:03',NULL,'172.70.49.222','facebookexternalhit/1.1; kakaotalk-scrap/1.0; +https://devtalk.kakao.com/t/scrap/33984',0,NULL),
(7,'7439b3fea38b63551e8f356ff503778d5cb366135a897b85fe98ceaa7db8b3fb',46,'1',NULL,'ACTIVE','2026-04-02 17:20:44','2026-04-02 17:20:44',NULL,'162.158.114.18','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36',0,NULL),
(8,'1b919099cf6671703fab6f2e17097f18d19448e29eaea5ee66cdc3c380daec54',46,'1',NULL,'ACTIVE','2026-04-02 17:20:58','2026-04-02 17:20:58',NULL,'162.158.114.18','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36',0,NULL),
(9,'4c3e95c7e420c3102735e57dd274f40aca68f9832607e91238dca63ff7d79f6d',46,'1',NULL,'ACTIVE','2026-04-02 17:21:15','2026-04-02 17:21:15',NULL,'162.158.179.202','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36',0,NULL),
(10,'b6f4674714294d38b995e25a4b4a53186b870c8dc468b679fdf3ff8ddee63337',46,'1',NULL,'ACTIVE','2026-04-02 22:03:25','2026-04-02 22:03:25',NULL,'162.159.108.48','facebookexternalhit/1.1; kakaotalk-scrap/1.0; +https://devtalk.kakao.com/t/scrap/33984',0,NULL),
(11,'82fa704f415810ca85d1bf9d414db95fa50dd340b723c7e0b5519edd90230483',46,'1',NULL,'ACTIVE','2026-04-03 08:49:50','2026-04-03 08:51:42',NULL,'108.162.246.182','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36',0,NULL),
(12,'64fc0184d38201d5979b8872003ccc55e8a53f646c976afa04bf5c3284e9ec8a',46,'1',NULL,'ACTIVE','2026-04-03 10:23:33','2026-04-03 11:13:25',NULL,'162.158.90.107','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36',0,NULL),
(13,'d449203e4edab34c5607154b9773321b24a5c5e00c0d110c9b25dfae4b801cee',46,'1',NULL,'ACTIVE','2026-04-03 11:05:30','2026-04-03 11:05:43',NULL,'172.70.206.118','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Safari/537.36',0,NULL),
(14,'6832c68f1cedaf66f790ee28fa02fe95c36efb19de6257e7d6a49c24548c9803',46,'1',NULL,'ACTIVE','2026-04-03 11:05:41','2026-04-03 11:05:41',NULL,'172.64.217.36','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36',0,NULL);
/*!40000 ALTER TABLE `table_visit_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visit_sum_t`
--

DROP TABLE IF EXISTS `visit_sum_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `visit_sum_t` (
  `vs_date` date NOT NULL,
  `vs_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visit_sum_t`
--
-- WHERE:  true limit 10

LOCK TABLES `visit_sum_t` WRITE;
/*!40000 ALTER TABLE `visit_sum_t` DISABLE KEYS */;
INSERT INTO `visit_sum_t` VALUES
('2026-03-27',3),
('2026-03-30',1123),
('2026-03-31',4697),
('2026-04-01',809),
('2026-04-02',5568),
('2026-04-03',3170),
('2026-04-04',16),
('2026-04-05',5),
('2026-04-06',1614),
('2026-04-07',3035);
/*!40000 ALTER TABLE `visit_sum_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visit_t`
--

DROP TABLE IF EXISTS `visit_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `visit_t` (
  `vi_id` int(11) NOT NULL,
  `vi_mt_idx` int(11) DEFAULT NULL COMMENT '회원idx',
  `vi_ip` varchar(255) NOT NULL DEFAULT '',
  `vi_date` date NOT NULL,
  `vi_time` time NOT NULL,
  `vi_referer` mediumtext NOT NULL,
  `vi_agent` varchar(255) DEFAULT NULL COMMENT '접속구분(app/web)',
  `vi_os` varchar(255) NOT NULL DEFAULT '',
  `vi_browser` varchar(255) DEFAULT NULL,
  `vi_device` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visit_t`
--
-- WHERE:  true limit 10

LOCK TABLES `visit_t` WRITE;
/*!40000 ALTER TABLE `visit_t` DISABLE KEYS */;
INSERT INTO `visit_t` VALUES
(1,0,'162.158.179.34','2026-03-27','08:32:08','','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','macOS','Chrome Generic','Desktop'),
(2,0,'162.158.179.33','2026-03-27','08:32:16','https://barorez.com/market/login.php','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','macOS','Chrome Generic','Desktop'),
(3,0,'172.71.215.123','2026-03-27','08:35:18','https://barorez.com/market/join.php','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','macOS','Chrome Generic','Desktop'),
(4,0,'172.68.211.147','2026-03-30','00:22:34','https://barorez.com/mng/','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','macOS','Chrome Generic','Desktop'),
(5,0,'172.68.211.146','2026-03-30','00:22:42','https://barorez.com/mng/manager/list.php','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','macOS','Chrome Generic','Desktop'),
(6,0,'172.68.211.147','2026-03-30','00:22:43','https://barorez.com/mng/manager/list.php','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','macOS','Chrome Generic','Desktop'),
(7,0,'172.68.211.146','2026-03-30','00:22:44','https://barorez.com/mng/manager/list.php?type=approval','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','macOS','Chrome Generic','Desktop'),
(8,0,'172.68.211.147','2026-03-30','00:22:46','https://barorez.com/mng/manager/list.php?type=secession','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','macOS','Chrome Generic','Desktop'),
(9,0,'172.68.211.146','2026-03-30','00:22:48','https://barorez.com/mng/shop/list.php','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','macOS','Chrome Generic','Desktop'),
(10,0,'172.68.211.147','2026-03-30','00:22:50','https://barorez.com/mng/adjustment/list.php','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','macOS','Chrome Generic','Desktop');
/*!40000 ALTER TABLE `visit_t` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-05-12 10:18:12
