<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateScraperTables extends Migration
{
    public function up()
    {
        // ✅ Table 1: Scraper Cache - Stores cached HTML/data to reduce external requests
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `fw_scraper_cache` (
              `cacheId` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `cacheKey` VARCHAR(255) NOT NULL,
              `cacheData` LONGTEXT NOT NULL,
              `createdTime` DATETIME NOT NULL,
              `expiryTime` DATETIME NOT NULL,
              `updatedTime` DATETIME DEFAULT NULL,
              PRIMARY KEY (`cacheId`),
              UNIQUE KEY `idx_cache_key` (`cacheKey`),
              KEY `idx_expiry` (`expiryTime`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // ✅ Table 2: Scraper Log - Logs all scraping activities for debugging and monitoring
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `fw_scraper_log` (
              `logId` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `logLevel` VARCHAR(20) NOT NULL DEFAULT 'info',
              `logMessage` TEXT NOT NULL,
              `logTime` DATETIME NOT NULL,
              PRIMARY KEY (`logId`),
              KEY `idx_log_time` (`logTime`),
              KEY `idx_log_level` (`logLevel`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // ✅ Table 3: Add new columns to existing fw_posts table (if not exist)
        try {
            $this->db->query("
                ALTER TABLE `fw_posts` 
                ADD COLUMN IF NOT EXISTS `scrapedFrom` VARCHAR(50) DEFAULT NULL COMMENT 'Source: sxrom, sammobile, etc.' AFTER `autoPost`,
                ADD COLUMN IF NOT EXISTS `pdaVersion` VARCHAR(100) DEFAULT NULL COMMENT 'Separate PDA version' AFTER `version`,
                ADD INDEX IF NOT EXISTS `idx_scraped_from` (`scrapedFrom`),
                ADD INDEX IF NOT EXISTS `idx_pda_version` (`pdaVersion`)
            ");
        } catch (\Exception $e) {
            // Silently handle if columns already exist
        }

        // ✅ Add composite indexes for better performance
        try {
            $this->db->query("
                ALTER TABLE `fw_posts`
                ADD INDEX IF NOT EXISTS `idx_duplicate_check` (`model`, `pdaVersion`, `csc`),
                ADD INDEX IF NOT EXISTS `idx_model_csc` (`model`, `csc`)
            ");
        } catch (\Exception $e) {
            // Silently handle if indexes already exist
        }
    }

    public function down()
    {
        // Drop tables in reverse order
        $this->db->query("DROP TABLE IF EXISTS `fw_scraper_log`");
        $this->db->query("DROP TABLE IF EXISTS `fw_scraper_cache`");

        // Drop columns from fw_posts (optional - be careful with this)
        try {
            $this->db->query("
                ALTER TABLE `fw_posts`
                DROP COLUMN IF EXISTS `scrapedFrom`,
                DROP COLUMN IF EXISTS `pdaVersion`,
                DROP INDEX IF EXISTS `idx_scraped_from`,
                DROP INDEX IF EXISTS `idx_pda_version`,
                DROP INDEX IF EXISTS `idx_duplicate_check`,
                DROP INDEX IF EXISTS `idx_model_csc`
            ");
        } catch (\Exception $e) {
            // Silently handle if columns don't exist
        }
    }
}
