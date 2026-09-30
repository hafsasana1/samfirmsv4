<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchedulerTables extends Migration
{
    public function up()
    {
        // ============================================================================
        // Step 1: Add scheduler columns to fw_posts table
        // ============================================================================
        try {
            $this->db->query("
                ALTER TABLE `fw_posts` 
                ADD COLUMN IF NOT EXISTS `scheduledPublishTime` DATETIME NULL DEFAULT NULL COMMENT 'Random scheduled publish time' AFTER `postStatus`,
                ADD COLUMN IF NOT EXISTS `priority` ENUM('urgent','high','normal') DEFAULT 'normal' COMMENT 'Publishing priority level' AFTER `scheduledPublishTime`,
                ADD COLUMN IF NOT EXISTS `isNewModel` ENUM('Yes','No') DEFAULT 'No' COMMENT 'First time this model seen?' AFTER `priority`,
                ADD COLUMN IF NOT EXISTS `autoScheduled` ENUM('Yes','No') DEFAULT 'No' COMMENT 'Auto-scheduled by system?' AFTER `isNewModel`,
                ADD COLUMN IF NOT EXISTS `publishedAt` DATETIME NULL DEFAULT NULL COMMENT 'Actual publish timestamp' AFTER `autoScheduled`,
                ADD COLUMN IF NOT EXISTS `manualPrioritySet` ENUM('Yes','No') DEFAULT 'No' COMMENT 'Admin manually set priority?' AFTER `publishedAt`,
                ADD INDEX IF NOT EXISTS `idx_scheduled` (`scheduledPublishTime`, `postStatus`, `priority`),
                ADD INDEX IF NOT EXISTS `idx_priority` (`priority`, `postStatus`),
                ADD INDEX IF NOT EXISTS `idx_new_model` (`isNewModel`, `model`)
            ");
        } catch (\Exception $e) {
            // Columns might already exist, continue
        }

        // ============================================================================
        // Step 2: Create scheduler log table
        // ============================================================================
        try {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `fw_scheduler_log` (
                  `logId` INT(11) NOT NULL AUTO_INCREMENT,
                  `postId` INT(11) NOT NULL,
                  `action` VARCHAR(50) NOT NULL COMMENT 'scheduled, published, failed, skipped',
                  `priority` ENUM('urgent','high','normal') DEFAULT 'normal',
                  `scheduledTime` DATETIME NULL,
                  `publishedTime` DATETIME NULL,
                  `message` TEXT NULL,
                  `executionTime` DECIMAL(10,3) NULL COMMENT 'Seconds taken',
                  `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`logId`),
                  INDEX `idx_post` (`postId`),
                  INDEX `idx_action` (`action`),
                  INDEX `idx_priority` (`priority`),
                  INDEX `idx_created` (`createdAt`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        } catch (\Exception $e) {
            // Table might already exist, continue
        }

        // ============================================================================
        // Step 3: Create scheduler settings table (if doesn't exist)
        // ============================================================================
        try {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `fw_scheduler_settings` (
                  `settingId` INT(11) NOT NULL AUTO_INCREMENT,
                  `settingKey` VARCHAR(100) NOT NULL UNIQUE,
                  `settingValue` TEXT NOT NULL,
                  `description` VARCHAR(255) NULL,
                  `updatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`settingId`),
                  UNIQUE KEY `uk_setting_key` (`settingKey`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        } catch (\Exception $e) {
            // Table might already exist, continue
        }

        // ============================================================================
        // Step 4: Insert default scheduler settings (use INSERT IGNORE to not override existing)
        // ============================================================================
        try {
            $this->db->query("
                INSERT IGNORE INTO `fw_scheduler_settings` (`settingKey`, `settingValue`, `description`) VALUES
                ('scheduler_enabled', 'Yes', 'Enable/Disable random scheduler (Yes/No)'),
                ('publishing_speed', 'normal', 'Publishing speed: slow, normal, fast, turbo'),
                ('daily_budget', '40', 'Maximum posts to publish per day'),
                ('min_gap_minutes', '10', 'Minimum gap between posts (minutes)'),
                ('max_gap_minutes', '90', 'Maximum gap between posts (minutes)'),
                ('schedule_window_hours', '48', 'Schedule drafts within X hours'),
                ('business_hours_only', 'No', 'Publish only 9 AM - 6 PM (Yes/No)'),
                ('exclude_weekends', 'No', 'Skip publishing on weekends (Yes/No)'),
                ('urgent_publish_minutes', '120', 'Publish urgent priority within X minutes'),
                ('high_publish_hours', '8', 'Publish high priority within X hours'),
                ('new_model_auto_urgent', 'Yes', 'Auto-set new models as urgent (Yes/No)'),
                ('popular_models', 'SM-S9,SM-F9,SM-F7,SM-N9,SM-G99', 'Popular model prefixes (comma separated)')
            ");
        } catch (\Exception $e) {
            // Might already exist, continue
        }

        // ============================================================================
        // Step 5: Create daily stats tracking table
        // ============================================================================
        try {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `fw_scheduler_stats` (
                  `statId` INT(11) NOT NULL AUTO_INCREMENT,
                  `statDate` DATE NOT NULL,
                  `scrapedCount` INT(11) DEFAULT 0,
                  `scheduledCount` INT(11) DEFAULT 0,
                  `publishedCount` INT(11) DEFAULT 0,
                  `urgentPublished` INT(11) DEFAULT 0,
                  `highPublished` INT(11) DEFAULT 0,
                  `normalPublished` INT(11) DEFAULT 0,
                  `failedCount` INT(11) DEFAULT 0,
                  `updatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`statId`),
                  UNIQUE KEY `uk_stat_date` (`statDate`),
                  INDEX `idx_date` (`statDate`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        } catch (\Exception $e) {
            // Table might already exist, continue
        }

        // ============================================================================
        // Step 6: Create view for dashboard stats
        // ============================================================================
        try {
            $this->db->query("DROP VIEW IF EXISTS `vw_scheduler_dashboard`");
        } catch (\Exception $e) {
            // View might not exist
        }

        try {
            $this->db->query("
                CREATE VIEW `vw_scheduler_dashboard` AS
                SELECT 
                    (SELECT COUNT(*) FROM fw_posts WHERE DATE(createdTime) = CURDATE() AND scrapedFrom IS NOT NULL) AS today_scraped,
                    (SELECT COUNT(*) FROM fw_posts WHERE postStatus = 'Draft' AND autoScheduled = 'Yes') AS pending_drafts,
                    (SELECT COUNT(*) FROM fw_posts WHERE DATE(publishedAt) = CURDATE()) AS published_today,
                    (SELECT COUNT(*) FROM fw_posts WHERE postStatus = 'Draft' AND priority = 'urgent') AS urgent_queue,
                    (SELECT COUNT(*) FROM fw_posts WHERE postStatus = 'Draft' AND priority = 'high') AS high_queue,
                    (SELECT COUNT(*) FROM fw_posts WHERE postStatus = 'Draft' AND priority = 'normal') AS normal_queue,
                    (SELECT scheduledPublishTime FROM fw_posts WHERE postStatus = 'Draft' AND scheduledPublishTime IS NOT NULL ORDER BY scheduledPublishTime ASC LIMIT 1) AS next_publish_time,
                    (SELECT settingValue FROM fw_scheduler_settings WHERE settingKey = 'scheduler_enabled') AS scheduler_status,
                    (SELECT settingValue FROM fw_scheduler_settings WHERE settingKey = 'publishing_speed') AS publishing_speed,
                    (SELECT settingValue FROM fw_scheduler_settings WHERE settingKey = 'daily_budget') AS daily_budget
            ");
        } catch (\Exception $e) {
            // View creation failed
            log_message('error', 'Failed to create vw_scheduler_dashboard: ' . $e->getMessage());
        }
    }

    public function down()
    {
        // ============================================================================
        // Rollback: Drop view and tables
        // ============================================================================
        try {
            $this->db->query("DROP VIEW IF EXISTS `vw_scheduler_dashboard`");
        } catch (\Exception $e) {
            // View might not exist
        }

        try {
            $this->db->query("DROP TABLE IF EXISTS `fw_scheduler_stats`");
        } catch (\Exception $e) {
            // Table might not exist
        }

        try {
            $this->db->query("DROP TABLE IF EXISTS `fw_scheduler_log`");
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Drop columns from fw_posts
        try {
            $this->db->query("
                ALTER TABLE `fw_posts`
                DROP COLUMN IF EXISTS `scheduledPublishTime`,
                DROP COLUMN IF EXISTS `priority`,
                DROP COLUMN IF EXISTS `isNewModel`,
                DROP COLUMN IF EXISTS `autoScheduled`,
                DROP COLUMN IF EXISTS `publishedAt`,
                DROP COLUMN IF EXISTS `manualPrioritySet`,
                DROP INDEX IF EXISTS `idx_scheduled`,
                DROP INDEX IF EXISTS `idx_priority`,
                DROP INDEX IF EXISTS `idx_new_model`
            ");
        } catch (\Exception $e) {
            // Columns might not exist, continue
        }
    }
}
