-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2026 at 06:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u409227338_samfirms`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` enum('unread','read','replied') NOT NULL DEFAULT 'unread',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_access_logs`
--

CREATE TABLE `fw_access_logs` (
  `log_id` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `reason` varchar(200) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_blogs`
--

CREATE TABLE `fw_blogs` (
  `postId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `postTitle` varchar(255) NOT NULL,
  `postSlug` varchar(255) NOT NULL,
  `postContent` longblob NOT NULL,
  `isRecentModel` enum('Yes','No') NOT NULL DEFAULT 'No',
  `sharePost` enum('Yes','No') NOT NULL DEFAULT 'No',
  `commentStatus` enum('Enabled','Disabled') NOT NULL DEFAULT 'Enabled',
  `postStatus` enum('Active','Inactive','Draft') NOT NULL DEFAULT 'Active',
  `featuredPost` enum('Yes','No') NOT NULL DEFAULT 'No',
  `separateAds` enum('Yes','No') NOT NULL DEFAULT 'No',
  `adsContent` longtext NOT NULL,
  `category` varchar(255) NOT NULL,
  `commentCount` int(11) NOT NULL,
  `likesCount` int(11) NOT NULL,
  `viewsCount` int(11) NOT NULL,
  `metaTags` tinytext NOT NULL,
  `metaTitle` varchar(255) NOT NULL,
  `metaDesription` text NOT NULL,
  `createdTime` datetime NOT NULL,
  `modifiedTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_categoreis`
--

CREATE TABLE `fw_categoreis` (
  `catId` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `catSlug` varchar(255) NOT NULL,
  `createdTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_cms`
--

CREATE TABLE `fw_cms` (
  `pageId` int(11) NOT NULL,
  `pageTitle` varchar(512) NOT NULL,
  `navTitle` varchar(255) DEFAULT NULL,
  `slugUrl` varchar(255) NOT NULL,
  `pageContent` longblob NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `position` varchar(50) NOT NULL DEFAULT 'footer',
  `isButton` varchar(10) DEFAULT 'No',
  `metaTags` tinytext NOT NULL,
  `metaTitle` varchar(255) NOT NULL,
  `metaDesription` text NOT NULL,
  `pageType` enum('1','0') NOT NULL DEFAULT '1',
  `createdTime` datetime NOT NULL,
  `modifiedTime` datetime NOT NULL,
  `page_area` enum('home','download','imei','') NOT NULL DEFAULT '',
  `adsContent` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_contact_us`
--

CREATE TABLE `fw_contact_us` (
  `contactUsId` int(11) NOT NULL,
  `fromName` varchar(255) NOT NULL,
  `fromEmail` varchar(255) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `website` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `ipAddress` varchar(50) NOT NULL,
  `createdTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_devices`
--

CREATE TABLE `fw_devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `Network` longtext DEFAULT NULL,
  `Launch` longtext DEFAULT NULL,
  `Body` longtext DEFAULT NULL,
  `Display` longtext DEFAULT NULL,
  `Platform` longtext DEFAULT NULL,
  `Memory` longtext DEFAULT NULL,
  `MainCamera` longtext DEFAULT NULL,
  `SelfieCamera` longtext DEFAULT NULL,
  `Sound` longtext DEFAULT NULL,
  `Communication` longtext DEFAULT NULL,
  `Features` longtext DEFAULT NULL,
  `Battery` longtext DEFAULT NULL,
  `Misc` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_devices_crawler`
--

CREATE TABLE `fw_devices_crawler` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_error_logs`
--

CREATE TABLE `fw_error_logs` (
  `log_id` int(11) NOT NULL,
  `error_code` int(3) NOT NULL DEFAULT 404,
  `requested_url` varchar(500) NOT NULL,
  `referrer_url` varchar(500) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `hit_count` int(11) NOT NULL DEFAULT 1,
  `first_seen` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_resolved` enum('Yes','No') NOT NULL DEFAULT 'No',
  `resolution_type` enum('Redirect','Ignore','Manual') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_imei_checked_ip`
--

CREATE TABLE `fw_imei_checked_ip` (
  `recordId` bigint(20) NOT NULL,
  `imei` varchar(50) NOT NULL,
  `ip_address` varchar(25) NOT NULL,
  `dated` date NOT NULL,
  `createdTime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_imei_checks`
--

CREATE TABLE `fw_imei_checks` (
  `id` int(11) UNSIGNED NOT NULL,
  `imei` varchar(20) NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_imei_info`
--

CREATE TABLE `fw_imei_info` (
  `recordId` bigint(20) NOT NULL,
  `orderId` varchar(15) NOT NULL,
  `price` varchar(10) NOT NULL,
  `imei` varchar(75) NOT NULL,
  `modelInfo` varchar(50) NOT NULL,
  `serial` varchar(50) NOT NULL,
  `modelDesc` varchar(100) NOT NULL,
  `modelName` varchar(50) NOT NULL,
  `modelNumber` varchar(50) NOT NULL,
  `color` varchar(20) NOT NULL,
  `warrantyStatus` varchar(50) NOT NULL,
  `estWarrantyEnd` varchar(20) NOT NULL,
  `productionLocation` varchar(100) NOT NULL,
  `productionDate` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL,
  `carrier` varchar(50) NOT NULL,
  `result` text NOT NULL,
  `visitorIP` varchar(50) NOT NULL,
  `createdTime` datetime NOT NULL,
  `updatedTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_imei_list`
--

CREATE TABLE `fw_imei_list` (
  `id` int(11) NOT NULL,
  `model` varchar(100) NOT NULL,
  `imei` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_login_attempts`
--

CREATE TABLE `fw_login_attempts` (
  `attemptId` int(11) UNSIGNED NOT NULL,
  `ipAddress` varchar(45) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `attemptTime` datetime NOT NULL,
  `success` enum('Yes','No') NOT NULL DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_page_crawler`
--

CREATE TABLE `fw_page_crawler` (
  `crawlId` bigint(20) NOT NULL,
  `urlLink` varchar(512) NOT NULL,
  `crawlData` longtext NOT NULL,
  `createdTime` datetime NOT NULL,
  `processTime` datetime DEFAULT NULL,
  `status` enum('Pending','Processed','Failed') NOT NULL DEFAULT 'Pending',
  `processingError` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_posts`
--

CREATE TABLE `fw_posts` (
  `postId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `postTitle` varchar(255) NOT NULL,
  `postSlug` varchar(255) NOT NULL,
  `postContent` longblob NOT NULL,
  `version` varchar(255) NOT NULL,
  `pdaVersion` varchar(100) DEFAULT NULL COMMENT 'Separate PDA version',
  `cscversion` varchar(100) NOT NULL,
  `bit` varchar(255) NOT NULL,
  `os` varchar(255) NOT NULL,
  `country` varchar(3) NOT NULL DEFAULT 'USA',
  `csc` varchar(3) NOT NULL,
  `device` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `isRecentModel` enum('Yes','No') NOT NULL DEFAULT 'No',
  `fileType` varchar(255) NOT NULL,
  `fileSize` varchar(50) NOT NULL,
  `buildDate` varchar(255) NOT NULL,
  `sharePost` enum('Yes','No') NOT NULL DEFAULT 'No',
  `commentStatus` enum('Enabled','Disabled') NOT NULL DEFAULT 'Enabled',
  `postStatus` enum('Active','Inactive','Draft') NOT NULL DEFAULT 'Active',
  `scheduledPublishTime` datetime DEFAULT NULL,
  `priority` enum('urgent','high','normal') DEFAULT 'normal',
  `isNewModel` enum('Yes','No') DEFAULT 'No' COMMENT 'First time this model seen?',
  `autoScheduled` enum('Yes','No') DEFAULT 'No',
  `publishedAt` datetime DEFAULT NULL,
  `manualPrioritySet` enum('Yes','No') DEFAULT 'No',
  `featuredPost` enum('Yes','No') NOT NULL DEFAULT 'No',
  `separateAds` enum('Yes','No') NOT NULL DEFAULT 'No',
  `adsContent` longtext NOT NULL,
  `category` varchar(255) NOT NULL,
  `tags` text NOT NULL,
  `downloadButton` text NOT NULL,
  `commentCount` int(11) NOT NULL,
  `likesCount` int(11) NOT NULL,
  `viewsCount` int(11) NOT NULL,
  `downloadCount` int(11) NOT NULL,
  `metaTags` tinytext NOT NULL,
  `metaTitle` varchar(255) NOT NULL,
  `metaDesription` text NOT NULL,
  `createdTime` datetime NOT NULL,
  `modifiedTime` datetime NOT NULL,
  `externalFileLink` varchar(512) NOT NULL,
  `externalFileLinkOld` varchar(512) NOT NULL,
  `externalFileUploaded` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `autoPost` enum('Yes','No') NOT NULL DEFAULT 'No',
  `scrapedFrom` varchar(50) DEFAULT NULL COMMENT 'Source: sxrom, sammobile, etc.',
  `specs` longtext NOT NULL,
  `specsSynced` enum('Yes','No') DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_posts2`
--

CREATE TABLE `fw_posts2` (
  `postId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `postTitle` varchar(255) NOT NULL,
  `postSlug` varchar(255) NOT NULL,
  `postContent` longblob NOT NULL,
  `version` varchar(255) NOT NULL,
  `cscversion` varchar(100) NOT NULL,
  `bit` varchar(255) NOT NULL,
  `os` varchar(255) NOT NULL,
  `country` varchar(3) NOT NULL DEFAULT 'USA',
  `csc` varchar(3) NOT NULL,
  `device` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `isRecentModel` enum('Yes','No') NOT NULL DEFAULT 'No',
  `fileType` varchar(255) NOT NULL,
  `sharePost` enum('Yes','No') NOT NULL DEFAULT 'No',
  `commentStatus` enum('Enabled','Disabled') NOT NULL DEFAULT 'Enabled',
  `postStatus` enum('Active','Inactive','Draft') NOT NULL DEFAULT 'Active',
  `featuredPost` enum('Yes','No') NOT NULL DEFAULT 'No',
  `separateAds` enum('Yes','No') NOT NULL DEFAULT 'No',
  `adsContent` longtext NOT NULL,
  `category` varchar(255) NOT NULL,
  `tags` text NOT NULL,
  `downloadButton` text NOT NULL,
  `commentCount` int(11) NOT NULL,
  `likesCount` int(11) NOT NULL,
  `viewsCount` int(11) NOT NULL,
  `downloadCount` int(11) NOT NULL,
  `metaTags` tinytext NOT NULL,
  `metaTitle` varchar(255) NOT NULL,
  `metaDesription` text NOT NULL,
  `createdTime` datetime NOT NULL,
  `modifiedTime` datetime NOT NULL,
  `externalFileLink` varchar(512) NOT NULL,
  `externalFileUploaded` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `autoPost` enum('Yes','No') NOT NULL DEFAULT 'No',
  `specs` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_posts_backup_20250618`
--

CREATE TABLE `fw_posts_backup_20250618` (
  `postId` int(11) NOT NULL DEFAULT 0,
  `userId` int(11) NOT NULL,
  `postTitle` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `postSlug` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `postContent` longblob NOT NULL,
  `version` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `cscversion` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `bit` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `os` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `country` varchar(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'USA',
  `csc` varchar(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `device` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `model` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `isRecentModel` enum('Yes','No') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'No',
  `fileType` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `fileSize` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `buildDate` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `sharePost` enum('Yes','No') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'No',
  `commentStatus` enum('Enabled','Disabled') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Enabled',
  `postStatus` enum('Active','Inactive','Draft') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Active',
  `featuredPost` enum('Yes','No') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'No',
  `separateAds` enum('Yes','No') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'No',
  `adsContent` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `category` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `tags` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `downloadButton` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `commentCount` int(11) NOT NULL,
  `likesCount` int(11) NOT NULL,
  `viewsCount` int(11) NOT NULL,
  `downloadCount` int(11) NOT NULL,
  `metaTags` tinytext CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `metaTitle` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `metaDesription` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `createdTime` datetime NOT NULL,
  `modifiedTime` datetime NOT NULL,
  `externalFileLink` varchar(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `externalFileLinkOld` varchar(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `externalFileUploaded` enum('Yes','No') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Yes',
  `autoPost` enum('Yes','No') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'No',
  `specs` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_post_comments`
--

CREATE TABLE `fw_post_comments` (
  `commentId` bigint(20) NOT NULL,
  `userId` int(11) NOT NULL,
  `postId` int(11) NOT NULL,
  `fromComment` bigint(20) NOT NULL,
  `ipAddress` varchar(20) NOT NULL,
  `fromName` varchar(255) NOT NULL,
  `fromEmail` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `status` enum('Approved','Pending') NOT NULL DEFAULT 'Pending',
  `replied` enum('true','false') NOT NULL DEFAULT 'false',
  `commentTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_post_likes`
--

CREATE TABLE `fw_post_likes` (
  `likeId` bigint(20) NOT NULL,
  `postId` int(11) NOT NULL,
  `area` enum('post','blog') DEFAULT 'post',
  `ipAddress` varchar(255) NOT NULL,
  `type` enum('like','dislike') NOT NULL DEFAULT 'like',
  `likeTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_post_view`
--

CREATE TABLE `fw_post_view` (
  `viewId` bigint(20) NOT NULL,
  `postId` int(11) NOT NULL,
  `ipAddress` varchar(255) NOT NULL,
  `country` varchar(5) NOT NULL,
  `visitingUrl` varchar(500) DEFAULT NULL,
  `referrelUrl` varchar(200) NOT NULL,
  `referrerFrom` varchar(500) DEFAULT NULL,
  `userAgent` text DEFAULT NULL,
  `viewTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_redirect_rules`
--

CREATE TABLE `fw_redirect_rules` (
  `rule_id` int(11) NOT NULL,
  `source_url` varchar(500) NOT NULL,
  `destination_url` varchar(500) NOT NULL,
  `redirect_type` enum('301','302','404_to_home','404_to_relevant') NOT NULL DEFAULT '301',
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `hit_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_scheduler_log`
--

CREATE TABLE `fw_scheduler_log` (
  `logId` int(11) NOT NULL,
  `postId` int(11) DEFAULT NULL,
  `action` enum('scheduled','published','failed','rescheduled') NOT NULL,
  `priority` enum('urgent','high','normal') DEFAULT NULL,
  `scheduledTime` datetime DEFAULT NULL,
  `publishedTime` datetime DEFAULT NULL,
  `message` text DEFAULT NULL,
  `executionTime` decimal(10,3) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_scheduler_settings`
--

CREATE TABLE `fw_scheduler_settings` (
  `settingId` int(11) NOT NULL,
  `settingKey` varchar(100) NOT NULL,
  `settingValue` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_scheduler_stats`
--

CREATE TABLE `fw_scheduler_stats` (
  `statId` int(11) NOT NULL,
  `statDate` date NOT NULL,
  `scheduledCount` int(11) DEFAULT 0,
  `publishedCount` int(11) DEFAULT 0,
  `failedCount` int(11) DEFAULT 0,
  `urgentPublished` int(11) DEFAULT 0,
  `highPublished` int(11) DEFAULT 0,
  `normalPublished` int(11) DEFAULT 0,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_scraper_cache`
--

CREATE TABLE `fw_scraper_cache` (
  `cacheId` int(11) UNSIGNED NOT NULL,
  `cacheKey` varchar(255) NOT NULL,
  `cacheData` longtext NOT NULL,
  `createdTime` datetime NOT NULL,
  `expiryTime` datetime NOT NULL,
  `updatedTime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_scraper_log`
--

CREATE TABLE `fw_scraper_log` (
  `logId` int(11) NOT NULL,
  `logLevel` enum('info','success','warning','error') DEFAULT 'info',
  `logMessage` text NOT NULL,
  `logTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_site_meta`
--

CREATE TABLE `fw_site_meta` (
  `metaId` int(11) NOT NULL,
  `metaType` varchar(100) NOT NULL,
  `metaValue` longblob NOT NULL,
  `updatedTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_tags`
--

CREATE TABLE `fw_tags` (
  `tagId` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `createdTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_users`
--

CREATE TABLE `fw_users` (
  `userId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cnicNumber` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `type` enum('Admin','User') NOT NULL DEFAULT 'User',
  `hashToken` varchar(255) NOT NULL,
  `createdTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fw_world_country`
--

CREATE TABLE `fw_world_country` (
  `id` int(11) NOT NULL,
  `iso2` char(50) DEFAULT NULL,
  `iso3` char(50) DEFAULT NULL,
  `continent` int(11) NOT NULL DEFAULT 0 COMMENT '1=Africa, 2=Asia, 3=Europe, 4=North America, 5=South America, 6= Australia',
  `country` char(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `currency_id` int(11) NOT NULL,
  `latitude` varchar(30) NOT NULL,
  `longitude` varchar(30) NOT NULL,
  `call_countrycode` varchar(15) DEFAULT NULL,
  `sales_tax` double(10,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `fw_world_csc`
--

CREATE TABLE `fw_world_csc` (
  `cscId` int(11) NOT NULL,
  `country` varchar(3) NOT NULL,
  `csc` varchar(3) NOT NULL,
  `createdTime` datetime NOT NULL,
  `updatedTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_scheduler_dashboard`
-- (See below for the actual view)
--
CREATE TABLE `vw_scheduler_dashboard` (
`today_scraped` bigint(21)
,`pending_drafts` bigint(21)
,`published_today` bigint(21)
,`urgent_queue` bigint(21)
,`high_queue` bigint(21)
,`normal_queue` bigint(21)
,`next_publish_time` datetime
,`scheduler_status` mediumtext
,`publishing_speed` mediumtext
,`daily_budget` mediumtext
);

-- --------------------------------------------------------

--
-- Structure for view `vw_scheduler_dashboard`
--
DROP TABLE IF EXISTS `vw_scheduler_dashboard`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_scheduler_dashboard`  AS SELECT (select count(0) from `fw_posts` where cast(`fw_posts`.`createdTime` as date) = curdate() and `fw_posts`.`scrapedFrom` is not null) AS `today_scraped`, (select count(0) from `fw_posts` where `fw_posts`.`postStatus` = 'Draft' and `fw_posts`.`autoScheduled` = 'Yes') AS `pending_drafts`, (select count(0) from `fw_posts` where cast(`fw_posts`.`publishedAt` as date) = curdate()) AS `published_today`, (select count(0) from `fw_posts` where `fw_posts`.`postStatus` = 'Draft' and `fw_posts`.`priority` = 'urgent') AS `urgent_queue`, (select count(0) from `fw_posts` where `fw_posts`.`postStatus` = 'Draft' and `fw_posts`.`priority` = 'high') AS `high_queue`, (select count(0) from `fw_posts` where `fw_posts`.`postStatus` = 'Draft' and `fw_posts`.`priority` = 'normal') AS `normal_queue`, (select `fw_posts`.`scheduledPublishTime` from `fw_posts` where `fw_posts`.`postStatus` = 'Draft' and `fw_posts`.`scheduledPublishTime` is not null order by `fw_posts`.`scheduledPublishTime` limit 1) AS `next_publish_time`, (select `fw_scheduler_settings`.`settingValue` from `fw_scheduler_settings` where `fw_scheduler_settings`.`settingKey` = 'scheduler_enabled') AS `scheduler_status`, (select `fw_scheduler_settings`.`settingValue` from `fw_scheduler_settings` where `fw_scheduler_settings`.`settingKey` = 'publishing_speed') AS `publishing_speed`, (select `fw_scheduler_settings`.`settingValue` from `fw_scheduler_settings` where `fw_scheduler_settings`.`settingKey` = 'daily_budget') AS `daily_budget` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `fw_access_logs`
--
ALTER TABLE `fw_access_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `url` (`url`),
  ADD KEY `timestamp` (`timestamp`);

--
-- Indexes for table `fw_blogs`
--
ALTER TABLE `fw_blogs`
  ADD PRIMARY KEY (`postId`),
  ADD KEY `idx_postSlug` (`postSlug`),
  ADD KEY `idx_postStatus_blogs` (`postStatus`),
  ADD KEY `idx_createdTime_blogs` (`createdTime`);

--
-- Indexes for table `fw_categoreis`
--
ALTER TABLE `fw_categoreis`
  ADD PRIMARY KEY (`catId`);

--
-- Indexes for table `fw_cms`
--
ALTER TABLE `fw_cms`
  ADD PRIMARY KEY (`pageId`),
  ADD KEY `idx_slugUrl` (`slugUrl`),
  ADD KEY `idx_status_cms` (`status`);

--
-- Indexes for table `fw_contact_us`
--
ALTER TABLE `fw_contact_us`
  ADD PRIMARY KEY (`contactUsId`),
  ADD KEY `idx_status_contact` (`status`),
  ADD KEY `idx_createdTime_contact` (`createdTime`);

--
-- Indexes for table `fw_devices`
--
ALTER TABLE `fw_devices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fw_devices_crawler`
--
ALTER TABLE `fw_devices_crawler`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fw_error_logs`
--
ALTER TABLE `fw_error_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD UNIQUE KEY `requested_url` (`requested_url`),
  ADD KEY `error_code` (`error_code`),
  ADD KEY `is_resolved` (`is_resolved`),
  ADD KEY `last_seen` (`last_seen`);

--
-- Indexes for table `fw_imei_checked_ip`
--
ALTER TABLE `fw_imei_checked_ip`
  ADD PRIMARY KEY (`recordId`);

--
-- Indexes for table `fw_imei_checks`
--
ALTER TABLE `fw_imei_checks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imei` (`imei`),
  ADD KEY `created_time` (`created_time`),
  ADD KEY `ip_address` (`ip_address`);

--
-- Indexes for table `fw_imei_info`
--
ALTER TABLE `fw_imei_info`
  ADD PRIMARY KEY (`recordId`);

--
-- Indexes for table `fw_imei_list`
--
ALTER TABLE `fw_imei_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fw_login_attempts`
--
ALTER TABLE `fw_login_attempts`
  ADD PRIMARY KEY (`attemptId`),
  ADD KEY `ipAddress` (`ipAddress`),
  ADD KEY `attemptTime` (`attemptTime`);

--
-- Indexes for table `fw_page_crawler`
--
ALTER TABLE `fw_page_crawler`
  ADD PRIMARY KEY (`crawlId`),
  ADD KEY `idx_status_crawler` (`status`);

--
-- Indexes for table `fw_posts`
--
ALTER TABLE `fw_posts`
  ADD PRIMARY KEY (`postId`),
  ADD KEY `idx_scraped_from` (`scrapedFrom`),
  ADD KEY `idx_pda_version` (`pdaVersion`),
  ADD KEY `idx_duplicate_check` (`model`,`pdaVersion`,`csc`),
  ADD KEY `idx_model_csc` (`model`,`csc`),
  ADD KEY `idx_scheduled` (`scheduledPublishTime`,`postStatus`,`priority`),
  ADD KEY `idx_priority` (`priority`,`postStatus`),
  ADD KEY `idx_new_model` (`isNewModel`,`model`),
  ADD KEY `wdbi_postslug_poststatus_f73641963d77` (`postSlug`,`postStatus`),
  ADD KEY `wdbi_modifiedtime_4e0578d37f37` (`modifiedTime`),
  ADD KEY `wdbi_model_csc_version_32_35d877b2f9b6` (`model`,`csc`,`version`(32)),
  ADD KEY `idx_model` (`model`),
  ADD KEY `idx_device` (`device`),
  ADD KEY `idx_version` (`version`),
  ADD KEY `idx_csc` (`csc`),
  ADD KEY `idx_createdTime` (`createdTime`),
  ADD KEY `idx_postStatus` (`postStatus`),
  ADD KEY `idx_userId` (`userId`),
  ADD KEY `idx_model_version_csc` (`model`,`version`,`csc`),
  ADD KEY `idx_model_device` (`model`,`device`),
  ADD KEY `idx_createdTime_status` (`createdTime`,`postStatus`),
  ADD KEY `idx_postStatus_model` (`postStatus`,`model`),
  ADD KEY `idx_postStatus_modifiedTime` (`postStatus`,`modifiedTime`);

--
-- Indexes for table `fw_posts2`
--
ALTER TABLE `fw_posts2`
  ADD PRIMARY KEY (`postId`);

--
-- Indexes for table `fw_post_comments`
--
ALTER TABLE `fw_post_comments`
  ADD PRIMARY KEY (`commentId`),
  ADD KEY `idx_postId` (`postId`),
  ADD KEY `idx_userId_comments` (`userId`),
  ADD KEY `idx_postId_status` (`postId`,`status`);

--
-- Indexes for table `fw_post_likes`
--
ALTER TABLE `fw_post_likes`
  ADD PRIMARY KEY (`likeId`),
  ADD KEY `idx_post_area` (`postId`,`area`,`ipAddress`);

--
-- Indexes for table `fw_post_view`
--
ALTER TABLE `fw_post_view`
  ADD PRIMARY KEY (`viewId`),
  ADD KEY `idx_postId_ipAddress_viewTime` (`postId`,`ipAddress`,`viewTime`),
  ADD KEY `idx_ipAddress` (`ipAddress`),
  ADD KEY `idx_country` (`country`),
  ADD KEY `idx_viewTime` (`viewTime`),
  ADD KEY `idx_referrelUrl` (`referrelUrl`),
  ADD KEY `idx_postId` (`postId`),
  ADD KEY `idx_duplicate_check` (`postId`,`ipAddress`,`viewTime`);

--
-- Indexes for table `fw_redirect_rules`
--
ALTER TABLE `fw_redirect_rules`
  ADD PRIMARY KEY (`rule_id`),
  ADD UNIQUE KEY `source_url` (`source_url`),
  ADD KEY `status` (`status`),
  ADD KEY `redirect_type` (`redirect_type`);

--
-- Indexes for table `fw_scheduler_log`
--
ALTER TABLE `fw_scheduler_log`
  ADD PRIMARY KEY (`logId`),
  ADD KEY `idx_postId` (`postId`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_createdAt` (`createdAt`);

--
-- Indexes for table `fw_scheduler_settings`
--
ALTER TABLE `fw_scheduler_settings`
  ADD PRIMARY KEY (`settingId`),
  ADD UNIQUE KEY `settingKey` (`settingKey`),
  ADD KEY `idx_key` (`settingKey`);

--
-- Indexes for table `fw_scheduler_stats`
--
ALTER TABLE `fw_scheduler_stats`
  ADD PRIMARY KEY (`statId`),
  ADD UNIQUE KEY `statDate` (`statDate`),
  ADD KEY `idx_date` (`statDate`);

--
-- Indexes for table `fw_scraper_cache`
--
ALTER TABLE `fw_scraper_cache`
  ADD PRIMARY KEY (`cacheId`),
  ADD UNIQUE KEY `idx_cache_key` (`cacheKey`),
  ADD KEY `idx_expiry` (`expiryTime`);

--
-- Indexes for table `fw_scraper_log`
--
ALTER TABLE `fw_scraper_log`
  ADD PRIMARY KEY (`logId`),
  ADD KEY `idx_level` (`logLevel`),
  ADD KEY `idx_time` (`logTime`);

--
-- Indexes for table `fw_site_meta`
--
ALTER TABLE `fw_site_meta`
  ADD PRIMARY KEY (`metaId`);

--
-- Indexes for table `fw_tags`
--
ALTER TABLE `fw_tags`
  ADD PRIMARY KEY (`tagId`);

--
-- Indexes for table `fw_users`
--
ALTER TABLE `fw_users`
  ADD PRIMARY KEY (`userId`);

--
-- Indexes for table `fw_world_country`
--
ALTER TABLE `fw_world_country`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `country` (`country`),
  ADD KEY `idx_country` (`country`);

--
-- Indexes for table `fw_world_csc`
--
ALTER TABLE `fw_world_csc`
  ADD PRIMARY KEY (`cscId`),
  ADD KEY `idx_csc_code` (`csc`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_access_logs`
--
ALTER TABLE `fw_access_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_blogs`
--
ALTER TABLE `fw_blogs`
  MODIFY `postId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_categoreis`
--
ALTER TABLE `fw_categoreis`
  MODIFY `catId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_cms`
--
ALTER TABLE `fw_cms`
  MODIFY `pageId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_contact_us`
--
ALTER TABLE `fw_contact_us`
  MODIFY `contactUsId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_devices`
--
ALTER TABLE `fw_devices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_devices_crawler`
--
ALTER TABLE `fw_devices_crawler`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_error_logs`
--
ALTER TABLE `fw_error_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_imei_checked_ip`
--
ALTER TABLE `fw_imei_checked_ip`
  MODIFY `recordId` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_imei_checks`
--
ALTER TABLE `fw_imei_checks`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_imei_info`
--
ALTER TABLE `fw_imei_info`
  MODIFY `recordId` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_imei_list`
--
ALTER TABLE `fw_imei_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_login_attempts`
--
ALTER TABLE `fw_login_attempts`
  MODIFY `attemptId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_page_crawler`
--
ALTER TABLE `fw_page_crawler`
  MODIFY `crawlId` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_posts`
--
ALTER TABLE `fw_posts`
  MODIFY `postId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_posts2`
--
ALTER TABLE `fw_posts2`
  MODIFY `postId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_post_comments`
--
ALTER TABLE `fw_post_comments`
  MODIFY `commentId` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_post_likes`
--
ALTER TABLE `fw_post_likes`
  MODIFY `likeId` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_post_view`
--
ALTER TABLE `fw_post_view`
  MODIFY `viewId` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_redirect_rules`
--
ALTER TABLE `fw_redirect_rules`
  MODIFY `rule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_scheduler_log`
--
ALTER TABLE `fw_scheduler_log`
  MODIFY `logId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_scheduler_settings`
--
ALTER TABLE `fw_scheduler_settings`
  MODIFY `settingId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_scheduler_stats`
--
ALTER TABLE `fw_scheduler_stats`
  MODIFY `statId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_scraper_cache`
--
ALTER TABLE `fw_scraper_cache`
  MODIFY `cacheId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_scraper_log`
--
ALTER TABLE `fw_scraper_log`
  MODIFY `logId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_site_meta`
--
ALTER TABLE `fw_site_meta`
  MODIFY `metaId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_tags`
--
ALTER TABLE `fw_tags`
  MODIFY `tagId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_users`
--
ALTER TABLE `fw_users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_world_country`
--
ALTER TABLE `fw_world_country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fw_world_csc`
--
ALTER TABLE `fw_world_csc`
  MODIFY `cscId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
