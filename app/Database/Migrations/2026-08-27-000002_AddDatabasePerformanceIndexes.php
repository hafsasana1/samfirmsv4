<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDatabasePerformanceIndexes extends Migration
{
    public function up()
    {
        $this->db->disableForeignKeyChecks();

        // ✅ fw_posts table - Single column indexes
        try { $this->db->query("ALTER TABLE fw_posts ADD INDEX idx_model (model)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_posts ADD INDEX idx_device (device)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_posts ADD INDEX idx_version (version)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_posts ADD INDEX idx_csc (csc)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_posts ADD INDEX idx_createdTime (createdTime)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_posts ADD INDEX idx_isProcessed (isProcessed)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_posts ADD INDEX idx_postStatus (postStatus)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_posts ADD INDEX idx_userId (userId)"); } catch (\Exception $e) {}

        // ✅ Composite indexes for common query patterns
        try { $this->db->query("ALTER TABLE fw_posts ADD INDEX idx_model_version_csc (model, version, csc)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_posts ADD INDEX idx_model_device (model, device)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_posts ADD INDEX idx_createdTime_status (createdTime, postStatus)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_posts ADD INDEX idx_isProcessed_createdTime (isProcessed, createdTime)"); } catch (\Exception $e) {}

        // ✅ fw_post_comments table
        try { $this->db->query("ALTER TABLE fw_post_comments ADD INDEX idx_postId (postId)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_post_comments ADD INDEX idx_userId_comments (userId)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_post_comments ADD INDEX idx_postId_status (postId, status)"); } catch (\Exception $e) {}

        // ✅ fw_blogs table
        try { $this->db->query("ALTER TABLE fw_blogs ADD INDEX idx_postSlug (postSlug)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_blogs ADD INDEX idx_postStatus_blogs (postStatus)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_blogs ADD INDEX idx_createdTime_blogs (createdTime)"); } catch (\Exception $e) {}

        // ✅ fw_cms table
        try { $this->db->query("ALTER TABLE fw_cms ADD INDEX idx_slugUrl (slugUrl)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_cms ADD INDEX idx_status_cms (status)"); } catch (\Exception $e) {}

        // ✅ fw_world_country table
        try { $this->db->query("ALTER TABLE fw_world_country ADD INDEX idx_country (country)"); } catch (\Exception $e) {}

        // ✅ fw_world_csc table
        try { $this->db->query("ALTER TABLE fw_world_csc ADD INDEX idx_csc_code (csc)"); } catch (\Exception $e) {}

        // ✅ fw_contact_us table
        try { $this->db->query("ALTER TABLE fw_contact_us ADD INDEX idx_status_contact (status)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_contact_us ADD INDEX idx_createdTime_contact (createdTime)"); } catch (\Exception $e) {}

        // ✅ fw_page_crawler table
        try { $this->db->query("ALTER TABLE fw_page_crawler ADD INDEX idx_status_crawler (status)"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE fw_page_crawler ADD INDEX idx_model_crawler (model)"); } catch (\Exception $e) {}

        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->db->disableForeignKeyChecks();

        // Drop all added indexes
        $this->db->query("ALTER TABLE fw_posts DROP INDEX IF EXISTS idx_model");
        $this->db->query("ALTER TABLE fw_posts DROP INDEX IF EXISTS idx_device");
        $this->db->query("ALTER TABLE fw_posts DROP INDEX IF EXISTS idx_version");
        $this->db->query("ALTER TABLE fw_posts DROP INDEX IF EXISTS idx_csc");
        $this->db->query("ALTER TABLE fw_posts DROP INDEX IF EXISTS idx_createdTime");
        $this->db->query("ALTER TABLE fw_posts DROP INDEX IF EXISTS idx_isProcessed");
        $this->db->query("ALTER TABLE fw_posts DROP INDEX IF EXISTS idx_postStatus");
        $this->db->query("ALTER TABLE fw_posts DROP INDEX IF EXISTS idx_userId");
        $this->db->query("ALTER TABLE fw_posts DROP INDEX IF EXISTS idx_model_version_csc");
        $this->db->query("ALTER TABLE fw_posts DROP INDEX IF EXISTS idx_model_device");
        $this->db->query("ALTER TABLE fw_posts DROP INDEX IF EXISTS idx_createdTime_status");
        $this->db->query("ALTER TABLE fw_posts DROP INDEX IF EXISTS idx_isProcessed_createdTime");

        $this->db->query("ALTER TABLE fw_post_comments DROP INDEX IF EXISTS idx_postId");
        $this->db->query("ALTER TABLE fw_post_comments DROP INDEX IF EXISTS idx_userId_comments");
        $this->db->query("ALTER TABLE fw_post_comments DROP INDEX IF EXISTS idx_postId_status");

        $this->db->query("ALTER TABLE fw_blogs DROP INDEX IF EXISTS idx_postSlug");
        $this->db->query("ALTER TABLE fw_blogs DROP INDEX IF EXISTS idx_postStatus_blogs");
        $this->db->query("ALTER TABLE fw_blogs DROP INDEX IF EXISTS idx_createdTime_blogs");

        $this->db->query("ALTER TABLE fw_cms DROP INDEX IF EXISTS idx_slugUrl");
        $this->db->query("ALTER TABLE fw_cms DROP INDEX IF EXISTS idx_status_cms");

        $this->db->query("ALTER TABLE fw_world_country DROP INDEX IF EXISTS idx_country");
        $this->db->query("ALTER TABLE fw_world_csc DROP INDEX IF EXISTS idx_csc_code");

        $this->db->query("ALTER TABLE fw_contact_us DROP INDEX IF EXISTS idx_status_contact");
        $this->db->query("ALTER TABLE fw_contact_us DROP INDEX IF EXISTS idx_createdTime_contact");

        $this->db->query("ALTER TABLE fw_page_crawler DROP INDEX IF EXISTS idx_status_crawler");
        $this->db->query("ALTER TABLE fw_page_crawler DROP INDEX IF EXISTS idx_model_crawler");

        $this->db->enableForeignKeyChecks();
    }
}
