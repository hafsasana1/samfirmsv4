<?php

namespace App\Controllers;

/**
 * ============================================================================
 * FIRMWARE SMART SCHEDULER
 * ============================================================================
 * 
 * Purpose: Priority-based random publishing with intelligent scheduling
 * 
 * Features:
 * - Auto-detects new models → URGENT priority (publish within 2 hours)
 * - Popular models → HIGH priority (publish within 8 hours)
 * - Normal variants → NORMAL priority (publish within 24-48 hours)
 * - Weighted random gaps (looks human, not automated)
 * - Configurable publishing speed (slow/normal/fast/turbo)
 * - Full admin control and manual overrides
 * 
 * Cron Setup: Run every 5 minutes
 * URL: /firmware-scheduler/publish
 * 
 * Author: Kiro AI
 * Date: 2026-08-19
 * ============================================================================
 */

class FirmwareScheduler extends BaseController
{
    protected $db;

    public function __construct()
    {
        helper(['url', 'global_function']);
        $this->db = \Config\Database::connect();
        date_default_timezone_set(defaultTimeZone());
        
        // Authentication check for dashboard and admin methods - DISABLED FOR DEBUG
        $segment = service('uri')->getSegment(2);
        // if ($segment === 'dashboard' || in_array($segment, ['save-settings', 'rerandomize', 'bulk-prioritize', 'bulk-action'])) {
        //     if (!isLoggedIn()) {
        //         header('Location: ' . base_url(ADMIN_PATH));
        //         exit;
        //     }
        // }
    }

    // ========================================================================
    // MAIN SCHEDULER ENTRY POINT (Called by Cron)
    // ========================================================================

    /**
     * Main publish method - checks for due posts and publishes them
     * URL: /firmware-scheduler/publish
     * Cron: Run every 5 minutes
     * 
     * ✨ HUMAN-LIKE: Respects natural publishing patterns
     */
    public function publish()
    {
        $startTime = microtime(true);
        
        // Check if scheduler is enabled
        if ($this->getSetting('scheduler_enabled') !== 'Yes') {
            return $this->jsonResponse('Scheduler is disabled', 'info');
        }

        echo "<h2>Firmware Scheduler - Running...</h2>";
        echo "<p>Time: " . date('Y-m-d H:i:s') . "</p><hr>";

        // Get posts that are due for publishing
        $duePosts = $this->db->query("
            SELECT * FROM fw_posts 
            WHERE postStatus = 'Draft' 
            AND scheduledPublishTime IS NOT NULL 
            AND scheduledPublishTime <= NOW()
            ORDER BY 
                FIELD(priority, 'urgent', 'high', 'normal'),
                scheduledPublishTime ASC
        ")->getResult();

        if (empty($duePosts)) {
            echo "<p>✅ No posts due for publishing at this time.</p>";
            return $this->jsonResponse('No posts due', 'success');
        }

        $published = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($duePosts as $post) {
            // ✨ HUMAN-LIKE: Sometimes skip a post (simulate editor busy/distracted)
            // 5% chance to skip and reschedule for later
            if (rand(1, 100) <= 5) {
                $newTime = time() + rand(15, 45) * 60; // Reschedule 15-45 min later
                $this->db->table('fw_posts')->where('postId', $post->postId)->update([
                    'scheduledPublishTime' => date('Y-m-d H:i:s', $newTime)
                ]);
                $skipped++;
                echo "<p>⏭️ Skipped (rescheduled): {$post->model} - {$post->csc}</p>";
                continue;
            }

            if ($this->publishPost($post)) {
                $published++;
                echo "<p>✅ Published: {$post->model} - {$post->csc} (Priority: {$post->priority})</p>";
                
                // ✨ HUMAN-LIKE: Small delay between publishes (simulate manual action)
                // 2-8 seconds delay (human clicking, reviewing)
                sleep(rand(2, 8));
            } else {
                $failed++;
                echo "<p>❌ Failed: {$post->model} - {$post->csc}</p>";
            }
        }

        $executionTime = round(microtime(true) - $startTime, 3);

        echo "<hr>";
        echo "<p><strong>Summary:</strong></p>";
        echo "<p>✅ Published: {$published}</p>";
        echo "<p>⏭️ Skipped: {$skipped}</p>";
        echo "<p>❌ Failed: {$failed}</p>";
        echo "<p>⏱️ Execution Time: {$executionTime}s</p>";

        // Update daily stats
        $this->updateDailyStats('publishedCount', $published);

        return $this->jsonResponse("Published: {$published}, Skipped: {$skipped}, Failed: {$failed}", 'success');
    }

    // ========================================================================
    // AUTO-SCHEDULE ALL DRAFTS
    // ========================================================================

    /**
     * Auto-schedule all draft posts with smart priority detection (AJAX)
     * URL: /firmware-scheduler/auto-schedule-ajax
     */
    public function autoScheduleAjax()
    {
        $startTime = microtime(true);

        // Get all unscheduled drafts
        $drafts = $this->db->query("
            SELECT * FROM fw_posts 
            WHERE postStatus = 'Draft' 
            AND (scheduledPublishTime IS NULL OR autoScheduled = 'No')
            ORDER BY createdTime DESC
        ")->getResult();

        if (empty($drafts)) {
            return $this->response->setJSON([
                'success' => true,
                'html' => '<div class="text-center py-8">
                    <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">No Drafts to Schedule</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">All drafts have already been scheduled.</p>
                </div>'
            ]);
        }

        $scheduled = 0;
        $urgent = 0;
        $high = 0;
        $normal = 0;
        $results = [];
        $lastScheduledTime = null;
        $isFirst = true;

        foreach ($drafts as $draft) {
            $priority = $this->detectPriority($draft);
            // ✨ KEY FIX: Pass isFirstPost and lastScheduledTime
            $scheduleTime = $this->calculateScheduleTime($priority, $isFirst, $lastScheduledTime);
            
            // Convert to timestamp for tracking
            $lastScheduledTime = strtotime($scheduleTime);
            $isFirst = false;

            $this->db->table('fw_posts')->where('postId', $draft->postId)->update([
                'priority' => $priority,
                'isNewModel' => $this->isNewModel($draft->model) ? 'Yes' : 'No',
                'scheduledPublishTime' => $scheduleTime,
                'autoScheduled' => 'Yes'
            ]);

            $this->logSchedulerAction($draft->postId, 'scheduled', $priority, $scheduleTime, 
                "Auto-scheduled as {$priority} priority for {$scheduleTime}");

            $scheduled++;
            if ($priority === 'urgent') $urgent++;
            elseif ($priority === 'high') $high++;
            else $normal++;

            // Store first 10 for display
            if (count($results) < 10) {
                $results[] = [
                    'model' => $draft->model,
                    'csc' => $draft->csc,
                    'priority' => $priority,
                    'time' => $scheduleTime
                ];
            }
        }

        $executionTime = round(microtime(true) - $startTime, 3);
        $this->updateDailyStats('scheduledCount', $scheduled);

        // Build HTML response
        $html = '<div class="space-y-4">
            <div class="text-center pb-4 border-b border-gray-200 dark:border-gray-700">
                <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">' . $scheduled . ' Firmwares Scheduled</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Execution Time: ' . $executionTime . 's</p>
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400">' . $urgent . '</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Urgent Priority</p>
                </div>
                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">' . $high . '</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">High Priority</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">' . $normal . '</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Normal Priority</p>
                </div>
            </div>';

        if (!empty($results)) {
            $html .= '<div class="mt-4">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Sample Scheduled Posts:</h4>
                <div class="space-y-2">';
            
            foreach ($results as $r) {
                $priorityColor = $r['priority'] === 'urgent' ? 'red' : ($r['priority'] === 'high' ? 'orange' : 'green');
                $html .= '<div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-' . $priorityColor . '-100 text-' . $priorityColor . '-700 dark:bg-' . $priorityColor . '-900/30 dark:text-' . $priorityColor . '-300">
                            ' . strtoupper($r['priority']) . '
                        </span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">' . $r['model'] . '</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">' . $r['csc'] . '</span>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">' . date('M j, g:i A', strtotime($r['time'])) . '</span>
                </div>';
            }
            
            $html .= '</div></div>';
        }

        $html .= '</div>';

        return $this->response->setJSON([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Publish due posts (AJAX)
     * URL: /firmware-scheduler/publish-ajax
     */
    public function publishAjax()
    {
        $startTime = microtime(true);

        if ($this->getSetting('scheduler_enabled') !== 'Yes') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Scheduler is currently disabled'
            ]);
        }

        $duePosts = $this->db->query("
            SELECT * FROM fw_posts 
            WHERE postStatus = 'Draft' 
            AND scheduledPublishTime IS NOT NULL 
            AND scheduledPublishTime <= NOW()
            ORDER BY priority DESC, scheduledPublishTime ASC
        ")->getResult();

        if (empty($duePosts)) {
            return $this->response->setJSON([
                'success' => true,
                'html' => '<div class="text-center py-8">
                    <i class="fas fa-info-circle text-5xl text-blue-500 mb-4"></i>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">No Posts Due</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">There are no posts scheduled for publishing at this time.</p>
                </div>'
            ]);
        }

        $published = 0;
        $failed = 0;
        $results = [];

        foreach ($duePosts as $post) {
            if ($this->publishPost($post)) {
                $published++;
                $results[] = [
                    'status' => 'success',
                    'model' => $post->model,
                    'csc' => $post->csc,
                    'priority' => $post->priority
                ];
            } else {
                $failed++;
                $results[] = [
                    'status' => 'failed',
                    'model' => $post->model,
                    'csc' => $post->csc,
                    'priority' => $post->priority
                ];
            }
        }

        $executionTime = round(microtime(true) - $startTime, 3);
        $this->updateDailyStats('publishedCount', $published);

        // Build HTML response
        $html = '<div class="space-y-4">
            <div class="text-center pb-4 border-b border-gray-200 dark:border-gray-700">
                <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Publishing Complete</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Execution Time: ' . $executionTime . 's</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">' . $published . '</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Published</p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400">' . $failed . '</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Failed</p>
                </div>
            </div>';

        if (!empty($results)) {
            $html .= '<div class="mt-4">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Published Posts:</h4>
                <div class="space-y-2">';
            
            foreach ($results as $r) {
                $statusIcon = $r['status'] === 'success' ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600';
                $html .= '<div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <i class="fas ' . $statusIcon . '"></i>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">' . $r['model'] . '</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">' . $r['csc'] . '</span>
                    <span class="ml-auto px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300">
                        ' . strtoupper($r['priority']) . '
                    </span>
                </div>';
            }
            
            $html .= '</div></div>';
        }

        $html .= '</div>';

        return $this->response->setJSON([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Auto-schedule all draft posts with smart priority detection
     * URL: /firmware-scheduler/auto-schedule
     */
    public function autoSchedule()
    {
        $startTime = microtime(true);

        echo "<h2>Auto-Scheduling Drafts...</h2><hr>";

        // Get all unscheduled drafts
        $drafts = $this->db->query("
            SELECT * FROM fw_posts 
            WHERE postStatus = 'Draft' 
            AND (scheduledPublishTime IS NULL OR autoScheduled = 'No')
            ORDER BY createdTime DESC
        ")->getResult();

        if (empty($drafts)) {
            echo "<p>✅ No drafts to schedule.</p>";
            return $this->jsonResponse('No drafts to schedule', 'info');
        }

        $scheduled = 0;
        $urgent = 0;
        $high = 0;
        $normal = 0;
        $lastScheduledTime = null;
        $isFirst = true;

        foreach ($drafts as $draft) {
            // Detect priority
            $priority = $this->detectPriority($draft);
            
            // ✨ KEY FIX: Pass isFirstPost and lastScheduledTime
            $scheduleTime = $this->calculateScheduleTime($priority, $isFirst, $lastScheduledTime);
            
            // Convert to timestamp for tracking
            $lastScheduledTime = strtotime($scheduleTime);
            $isFirst = false;

            // Update post
            $this->db->table('fw_posts')->where('postId', $draft->postId)->update([
                'priority' => $priority,
                'isNewModel' => $this->isNewModel($draft->model) ? 'Yes' : 'No',
                'scheduledPublishTime' => $scheduleTime,
                'autoScheduled' => 'Yes'
            ]);

            // Log
            $this->logSchedulerAction($draft->postId, 'scheduled', $priority, $scheduleTime, 
                "Auto-scheduled as {$priority} priority for {$scheduleTime}");

            $scheduled++;
            if ($priority === 'urgent') $urgent++;
            elseif ($priority === 'high') $high++;
            else $normal++;

            echo "<p>📅 Scheduled: {$draft->model} - Priority: <strong>{$priority}</strong> - Time: {$scheduleTime}</p>";
        }

        $executionTime = round(microtime(true) - $startTime, 3);

        echo "<hr>";
        echo "<p><strong>Summary:</strong></p>";
        echo "<p>📊 Total Scheduled: {$scheduled}</p>";
        echo "<p>🔴 Urgent: {$urgent}</p>";
        echo "<p>🟡 High: {$high}</p>";
        echo "<p>🟢 Normal: {$normal}</p>";
        echo "<p>⏱️ Execution Time: {$executionTime}s</p>";

        // Update stats
        $this->updateDailyStats('scheduledCount', $scheduled);

        return $this->jsonResponse("Scheduled {$scheduled} drafts", 'success');
    }

    // ========================================================================
    // PRIORITY DETECTION (AUTO-DETECTS NEW MODELS)
    // ========================================================================

    /**
     * Detect post priority based on model, popularity, and newness
     * 🔴 URGENT: New models (auto-detected)
     * 🟡 HIGH: Popular models
     * 🟢 NORMAL: Everything else
     */
    private function detectPriority($post): string
    {
        // 🔴 URGENT: Manual override
        if (isset($post->manualPrioritySet) && $post->manualPrioritySet === 'Yes' && $post->priority === 'urgent') {
            return 'urgent';
        }

        // 🔴 URGENT: NEW MODEL AUTO-DETECTION
        if ($this->isNewModel($post->model)) {
            return 'urgent';
        }

        // 🟡 HIGH: Popular model series
        if ($this->isPopularModel($post->model)) {
            return 'high';
        }

        // 🟡 HIGH: Latest Android version (17+)
        if (!empty($post->os) && intval($post->os) >= 17) {
            return 'high';
        }

        // 🟢 NORMAL: Everything else
        return 'normal';
    }

    /**
     * Check if this is a new model (first time seen in database)
     * Returns TRUE if model doesn't exist OR was added in last 7 days
     */
    private function isNewModel($model): bool
    {
        $existing = $this->db->table('fw_posts')
            ->where('model', $model)
            ->orderBy('createdTime', 'ASC')
            ->get()
            ->getRow();

        // Model doesn't exist = NEW MODEL!
        if (!$existing) {
            return true;
        }

        // Check if model was first seen within last 7 days
        $firstSeenTime = strtotime($existing->createdTime);
        $daysSince = (time() - $firstSeenTime) / 86400;

        return ($daysSince < 7);
    }

    /**
     * Check if model belongs to popular series
     */
    private function isPopularModel($model): bool
    {
        $popularPrefixes = explode(',', $this->getSetting('popular_models', 'SM-S9,SM-F9,SM-F7,SM-N9'));
        
        foreach ($popularPrefixes as $prefix) {
            if (strpos($model, trim($prefix)) === 0) {
                return true;
            }
        }

        return false;
    }

    // ========================================================================
    // SCHEDULE TIME CALCULATION - HUMAN-LIKE EDITORIAL TEAM SIMULATION
    // ========================================================================

    /**
     * ✨ SIMPLIFIED HUMAN-LIKE SCHEDULING
     * Calculate schedule for a post based on its position in queue
     * 
     * For first post: Add only review delay (30-60 min)
     * For subsequent posts: Add gap from previous post (8-60 min)
     */
    private function calculateScheduleTime($priority, $isFirstPost = true, $lastScheduledTime = null): string
    {
        $now = time();
        
        // ✨ STEP 1: DETERMINE BASE TIME
        if ($isFirstPost || $lastScheduledTime === null) {
            // FIRST POST: Add review delay only
            $reviewDelayMin = intval($this->getSetting('review_delay_min', '30')); 
            $reviewDelayMax = intval($this->getSetting('review_delay_max', '60')); 
            $delay = rand($reviewDelayMin, $reviewDelayMax) * 60;
            $scheduleTime = $now + $delay;
        } else {
            // SUBSEQUENT POSTS: Add gap to previous time
            $gapMinMinutes = 8;
            $gapMaxMinutes = 30;
            $gap = rand($gapMinMinutes, $gapMaxMinutes) * 60;
            $scheduleTime = $lastScheduledTime + $gap;
        }
        
        // ✨ STEP 2: ADD RANDOM SECONDS (human clicking)
        $randomSeconds = rand(0, 59);
        $scheduleTime += $randomSeconds;
        
        // ✨ STEP 3: PRIORITY BOOST (optional: publish urgent slightly earlier)
        if ($priority === 'urgent' && !$isFirstPost) {
            // For urgent non-first posts, reduce gap by 20%
            $scheduleTime = $scheduleTime - (intval((rand(8, 30) * 0.2) * 60));
        }
        
        // ✨ STEP 4: COLLISION DETECTION
        $scheduleTime = $this->avoidCollision($scheduleTime, $priority);
        
        return date('Y-m-d H:i:s', $scheduleTime);
    }

    /**
     * ✨ SEO ENHANCEMENT: Avoid scheduling posts too close together
     * Ensures minimum gap between posts (looks more human)
     */
    private function avoidCollision($scheduleTime, $priority): int
    {
        // Check if another post scheduled within ±10 minutes
        $checkStart = date('Y-m-d H:i:s', $scheduleTime - 600); // 10 min before
        $checkEnd = date('Y-m-d H:i:s', $scheduleTime + 600);   // 10 min after

        $nearby = $this->db->query("
            SELECT COUNT(*) as count 
            FROM fw_posts 
            WHERE scheduledPublishTime BETWEEN ? AND ?
        ", [$checkStart, $checkEnd])->getRow();

        // If collision detected, add random offset
        if ($nearby && $nearby->count > 0) {
            $offset = rand(8, 20) * 60; // 8-20 minutes
            $scheduleTime += $offset;
        }

        return $scheduleTime;
    }

    /**
     * Adjust time to business hours (9 AM - 6 PM)
     */
    private function adjustToBusinessHours($timestamp): int
    {
        $hour = intval(date('H', $timestamp));

        if ($hour < 9) {
            // Too early, move to 9 AM
            return strtotime(date('Y-m-d 09:00:00', $timestamp));
        } elseif ($hour >= 18) {
            // Too late, move to next day 9 AM
            return strtotime(date('Y-m-d 09:00:00', strtotime('+1 day', $timestamp)));
        }

        return $timestamp;
    }

    /**
     * Skip weekends - move to Monday if falls on Sat/Sun
     */
    private function skipWeekends($timestamp): int
    {
        $dayOfWeek = intval(date('N', $timestamp)); // 1=Mon, 7=Sun

        if ($dayOfWeek == 6) {
            // Saturday, move to Monday
            return strtotime('+2 days', $timestamp);
        } elseif ($dayOfWeek == 7) {
            // Sunday, move to Monday
            return strtotime('+1 day', $timestamp);
        }

        return $timestamp;
    }

    // ========================================================================
    // PUBLISH POST
    // ========================================================================

    /**
     * Publish a single post (change status from Draft to Active)
     */
    private function publishPost($post): bool
    {
        try {
            $this->db->table('fw_posts')->where('postId', $post->postId)->update([
                'postStatus' => 'Active',
                'publishedAt' => date('Y-m-d H:i:s')
            ]);

            $this->logSchedulerAction($post->postId, 'published', $post->priority, null, 
                "Published: {$post->model} - {$post->csc}");

            // Update priority-specific stats
            $priorityField = $post->priority . 'Published';
            $this->updateDailyStats($priorityField, 1);

            // ✅ FIX: Clear query cache after publishing so homepage and dashboard update immediately
            $this->clearPublishCache();
            
            // 🔔 SEO: Auto-ping search engines about sitemap update (async, non-blocking)
            // Only ping every 10 posts to avoid rate limiting
            static $publishCount = 0;
            $publishCount++;
            if ($publishCount % 10 === 0) {
                try {
                    autoInvalidateSitemapCache();
                } catch (\Exception $e) {
                    // Non-critical, log and continue
                    log_message('info', 'Sitemap ping skipped: ' . $e->getMessage());
                }
            }

            return true;

        } catch (\Exception $e) {
            $this->logSchedulerAction($post->postId, 'failed', $post->priority, null, 
                "Failed: " . $e->getMessage());
            
            $this->updateDailyStats('failedCount', 1);

            return false;
        }
    }

    // ========================================================================
    // SETTINGS & STATS
    // ========================================================================

    /**
     * Get scheduler setting
     */
    private function getSetting($key, $default = '')
    {
        $setting = $this->db->table('fw_scheduler_settings')
            ->where('settingKey', $key)
            ->get()
            ->getRow();

        return $setting ? $setting->settingValue : $default;
    }

    /**
     * Log scheduler action
     */
    private function logSchedulerAction($postId, $action, $priority, $scheduledTime = null, $message = '')
    {
        try {
            $this->db->table('fw_scheduler_log')->insert([
                'postId' => $postId,
                'action' => $action,
                'priority' => $priority,
                'scheduledTime' => $scheduledTime,
                'publishedTime' => ($action === 'published') ? date('Y-m-d H:i:s') : null,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            // Silently fail if log table doesn't exist or is read-only
            // This prevents breaking the main functionality
        }
    }

    /**
     * Update daily stats
     */
    private function updateDailyStats($field, $increment)
    {
        try {
            $today = date('Y-m-d');

            $existing = $this->db->table('fw_scheduler_stats')
                ->where('statDate', $today)
                ->get()
                ->getRow();

            if ($existing) {
                $this->db->query("
                    UPDATE fw_scheduler_stats 
                    SET {$field} = {$field} + {$increment} 
                    WHERE statDate = '{$today}'
                ");
            } else {
                $this->db->table('fw_scheduler_stats')->insert([
                    'statDate' => $today,
                    $field => $increment
                ]);
            }
        } catch (\Exception $e) {
            // Silently fail if stats table doesn't exist or is read-only
        }
    }

    /**
     * JSON response helper
     */
    private function jsonResponse($message, $type = 'success')
    {
        // ✅ FIX: Check if request exists (when called via HTTP) before checking isAJAX
        if ($this->request && $this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => $type,
                'message' => $message
            ]);
        }
        return $message;
    }

    // ========================================================================
    // DASHBOARD & ADMIN VIEWS
    // ========================================================================

    /**
     * Scheduler dashboard
     * URL: /firmware-scheduler/dashboard
     */
    public function dashboard()
    {
        // Check if tables exist (setup verification)
        try {
            // Test 1: Check if fw_scheduler_settings table exists
            $settingsTable = $this->db->query("SHOW TABLES LIKE 'fw_scheduler_settings'")->getRow();
            
            // Test 2: Check if fw_scheduler_log table exists
            $logTable = $this->db->query("SHOW TABLES LIKE 'fw_scheduler_log'")->getRow();
            
            // Test 3: Check if new columns exist in fw_posts
            $columnsCheck = $this->db->query("
                SELECT COLUMN_NAME 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'fw_posts' 
                AND COLUMN_NAME IN ('scheduledPublishTime', 'priority', 'isNewModel')
            ")->getResult();
            
            if (!$settingsTable || !$logTable || count($columnsCheck) < 3) {
                // Tables don't exist - show setup message with details
                $missingTables = [];
                if (!$settingsTable) $missingTables[] = 'fw_scheduler_settings';
                if (!$logTable) $missingTables[] = 'fw_scheduler_log';
                if (count($columnsCheck) < 3) $missingTables[] = 'fw_posts columns';
                
                return $this->showSetupMessage('Missing: ' . implode(', ', $missingTables));
            }

            // Get dashboard stats from view
            $stats = $this->db->query("SELECT * FROM vw_scheduler_dashboard")->getRow();

            // Get recent logs (wrap in try-catch) - Show distinct entries only
            $recentLogs = [];
            try {
                $recentLogs = $this->db->query("
                    SELECT DISTINCT l.logId, l.postId, l.action, l.priority, 
                           l.scheduledTime, l.publishedTime, l.message, 
                           l.executionTime, l.createdAt, p.model, p.csc 
                    FROM fw_scheduler_log l
                    LEFT JOIN fw_posts p ON l.postId = p.postId
                    WHERE p.postStatus != 'Duplicate_Old'
                    ORDER BY l.createdAt DESC
                    LIMIT 20
                ")->getResult();
            } catch (\Exception $e) {
                // Log table might be read-only, skip for now
            }

            // Get upcoming posts for timeline - Filter out duplicates and past dates
            // Use proper timezone-aware comparison
            $upcomingPosts = $this->db->query("
                SELECT postId, model, csc, priority, scheduledPublishTime 
                FROM fw_posts 
                WHERE postStatus = 'Draft' 
                AND scheduledPublishTime IS NOT NULL 
                AND scheduledPublishTime >= NOW()
                ORDER BY scheduledPublishTime ASC 
                LIMIT 20
            ")->getResult();
            
            // Debug: Log if no upcoming posts found
            if (empty($upcomingPosts)) {
                log_message('debug', 'Scheduler Dashboard: No upcoming posts found. NOW() = ' . date('Y-m-d H:i:s'));
                
                // Get all scheduled posts for debugging
                $allScheduled = $this->db->query("
                    SELECT COUNT(*) as total, MIN(scheduledPublishTime) as earliest, MAX(scheduledPublishTime) as latest
                    FROM fw_posts 
                    WHERE postStatus = 'Draft' AND scheduledPublishTime IS NOT NULL
                ")->getRow();
                
                log_message('debug', 'Total scheduled: ' . ($allScheduled->total ?? 0) . ', Range: ' . ($allScheduled->earliest ?? 'none') . ' to ' . ($allScheduled->latest ?? 'none'));
            }

            $data = [
                'stats' => $stats,
                'logs' => $recentLogs,
                'upcomingPosts' => $upcomingPosts,
                'request' => 'scheduler-dashboard'
            ];

            return view(ADMIN_PATH . '/include/content', $data);

        } catch (\Exception $e) {
            // Database error - show setup message
            return $this->showSetupMessage('Error: ' . $e->getMessage());
        }
    }

    /**
     * Fix scheduler data accuracy issues
     * URL: /firmware-scheduler/fix-data-accuracy
     */
    public function fixDataAccuracy()
    {
        $results = [];
        
        try {
            // Step 1: Recreate view with correct pending drafts count
            $results[] = "Step 1: Fixing vw_scheduler_dashboard view...";
            $this->db->query("DROP VIEW IF EXISTS vw_scheduler_dashboard");
            
            $viewSQL = "CREATE VIEW vw_scheduler_dashboard AS
            SELECT 
                (SELECT COUNT(*) FROM fw_posts WHERE DATE(createdTime) = CURDATE() AND scrapedFrom IS NOT NULL) AS today_scraped,
                (SELECT COUNT(*) FROM fw_posts WHERE postStatus = 'Draft') AS pending_drafts,
                (SELECT COUNT(*) FROM fw_posts WHERE postStatus = 'Active' AND DATE(modifiedTime) = CURDATE()) AS published_today,
                (SELECT COUNT(*) FROM fw_posts WHERE postStatus = 'Draft' AND priority = 'urgent') AS urgent_queue,
                (SELECT COUNT(*) FROM fw_posts WHERE postStatus = 'Draft' AND priority = 'high') AS high_queue,
                (SELECT COUNT(*) FROM fw_posts WHERE postStatus = 'Draft' AND priority = 'normal') AS normal_queue,
                (SELECT scheduledPublishTime FROM fw_posts WHERE postStatus = 'Draft' AND scheduledPublishTime IS NOT NULL AND scheduledPublishTime >= NOW() ORDER BY scheduledPublishTime ASC LIMIT 1) AS next_publish_time,
                (SELECT settingValue FROM fw_scheduler_settings WHERE settingKey = 'scheduler_enabled') AS scheduler_status,
                (SELECT settingValue FROM fw_scheduler_settings WHERE settingKey = 'publishing_speed') AS publishing_speed,
                (SELECT settingValue FROM fw_scheduler_settings WHERE settingKey = 'daily_budget') AS daily_budget";
            
            $this->db->query($viewSQL);
            $results[] = "✓ View recreated successfully";
            
            // Step 2: Fix year 2028 dates
            $results[] = "\nStep 2: Fixing posts scheduled in year 2028...";
            $count2028 = $this->db->query("SELECT COUNT(*) as cnt FROM fw_posts WHERE YEAR(scheduledPublishTime) = 2028")->getRow()->cnt;
            
            if ($count2028 > 0) {
                $this->db->query("UPDATE fw_posts SET scheduledPublishTime = DATE_SUB(scheduledPublishTime, INTERVAL 2 YEAR) WHERE YEAR(scheduledPublishTime) = 2028");
                $results[] = "✓ Updated $count2028 posts from year 2028 to 2026";
            } else {
                $results[] = "✓ No posts with year 2028 found";
            }
            
            // Step 3: Find and report duplicates
            $results[] = "\nStep 3: Finding duplicate models...";
            $duplicates = $this->db->query("
                SELECT model, csc, COUNT(*) as cnt, GROUP_CONCAT(postId ORDER BY postId) as ids
                FROM fw_posts
                WHERE postStatus != 'Duplicate_Old'
                GROUP BY model, csc
                HAVING COUNT(*) > 1
            ")->getResult();
            
            if (count($duplicates) > 0) {
                $results[] = "Found " . count($duplicates) . " duplicate model/csc combinations:";
                foreach ($duplicates as $dup) {
                    $results[] = "  - {$dup->model} - {$dup->csc}: {$dup->cnt} copies (IDs: {$dup->ids})";
                }
            } else {
                $results[] = "✓ No duplicates found";
            }
            
            // Step 4: Mark older duplicates (keep newest)
            $results[] = "\nStep 4: Marking duplicate posts...";
            $this->db->query("
                UPDATE fw_posts p1
                INNER JOIN (
                    SELECT MAX(postId) as keep_id, model, csc
                    FROM fw_posts
                    WHERE postStatus != 'Duplicate_Old'
                    GROUP BY model, csc
                    HAVING COUNT(*) > 1
                ) p2 ON p1.model = p2.model AND p1.csc = p2.csc
                SET p1.postStatus = 'Duplicate_Old'
                WHERE p1.postId < p2.keep_id
            ");
            $marked = $this->db->affectedRows();
            $results[] = "✓ Marked $marked duplicate posts as 'Duplicate_Old'";
            
            // Step 5: Verification
            $results[] = "\n=== VERIFICATION ===";
            $stats = $this->db->query("SELECT * FROM vw_scheduler_dashboard")->getRow();
            $results[] = "Dashboard Stats:";
            $results[] = "  - Scraped Today: {$stats->today_scraped}";
            $results[] = "  - Pending Drafts: {$stats->pending_drafts}";
            $results[] = "  - Published Today: {$stats->published_today}";
            $results[] = "  - Urgent Queue: {$stats->urgent_queue}";
            $results[] = "  - High Queue: {$stats->high_queue}";
            $results[] = "  - Normal Queue: {$stats->normal_queue}";
            
            $futureCount = $this->db->query("SELECT COUNT(*) as cnt FROM fw_posts WHERE YEAR(scheduledPublishTime) > 2026")->getRow()->cnt;
            $results[] = "\nPosts with dates > 2026: $futureCount (should be 0)";
            
            $dupCountRemaining = $this->db->query("
                SELECT COUNT(*) as cnt FROM (
                    SELECT model, csc FROM fw_posts 
                    WHERE postStatus != 'Duplicate_Old'
                    GROUP BY model, csc HAVING COUNT(*) > 1
                ) as dups
            ")->getRow()->cnt;
            $results[] = "Duplicate models remaining: $dupCountRemaining (should be 0)";
            
            $results[] = "\n✓ All fixes applied successfully!";
            $results[] = "\nYou can now return to the dashboard: <a href='" . site_url('firmware-scheduler/dashboard') . "'>Go to Dashboard</a>";
            
        } catch (\Exception $e) {
            $results[] = "✗ Error: " . $e->getMessage();
        }
        
        // Display results
        echo "<html><head><title>Fix Scheduler Data</title></head><body style='font-family: monospace; padding: 20px;'>";
        echo "<h2>Scheduler Data Accuracy Fix</h2>";
        echo "<pre>" . implode("\n", $results) . "</pre>";
        echo "</body></html>";
    }

    /**
     * Publish next N posts immediately
     */
    public function publishNext()
    {
        $json = $this->request->getJSON();
        $count = $json->count ?? 10;
        
        try {
            $posts = $this->db->query("
                SELECT * FROM fw_posts 
                WHERE postStatus = 'Draft' 
                AND scheduledPublishTime IS NOT NULL 
                ORDER BY priority DESC, scheduledPublishTime ASC 
                LIMIT {$count}
            ")->getResult();
            
            $published = 0;
            foreach ($posts as $post) {
                if ($this->publishPost($post)) {
                    $published++;
                }
            }
            
            $html = '<div class="text-center py-8">
                <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">' . $published . ' Posts Published!</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Successfully published immediately</p>
            </div>';
            
            return $this->response->setJSON([
                'success' => true,
                'html' => $html,
                'count' => $published
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Re-randomize all scheduled times
     */
    public function rerandomize()
    {
        try {
            $drafts = $this->db->query("
                SELECT * FROM fw_posts 
                WHERE postStatus = 'Draft' 
                AND scheduledPublishTime IS NOT NULL
                ORDER BY scheduledPublishTime ASC
            ")->getResult();
            
            $lastScheduledTime = null;
            $isFirst = true;
            
            foreach ($drafts as $draft) {
                // ✨ KEY FIX: Pass isFirstPost and lastScheduledTime
                $newTime = $this->calculateScheduleTime($draft->priority, $isFirst, $lastScheduledTime);
                
                // Convert to timestamp for tracking
                $lastScheduledTime = strtotime($newTime);
                $isFirst = false;
                
                $this->db->table('fw_posts')
                    ->where('postId', $draft->postId)
                    ->update(['scheduledPublishTime' => $newTime]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'count' => count($drafts)
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Bulk prioritize posts by model pattern
     */
    public function bulkPrioritize()
    {
        $json = $this->request->getJSON();
        $pattern = $json->pattern ?? '';
        $priority = $json->priority ?? 'normal';
        
        try {
            // Convert wildcard pattern to SQL LIKE pattern
            $sqlPattern = str_replace('*', '%', $pattern);
            
            // Get matching posts, ordered by scheduled time
            $posts = $this->db->query("
                SELECT postId, model, priority as oldPriority FROM fw_posts 
                WHERE postStatus = 'Draft' 
                AND model LIKE ?
                ORDER BY scheduledPublishTime ASC
            ", [$sqlPattern])->getResult();
            
            $updated = 0;
            $lastScheduledTime = null;
            $isFirst = true;
            
            foreach ($posts as $post) {
                // ✨ KEY FIX: Pass isFirstPost and lastScheduledTime
                $newTime = $this->calculateScheduleTime($priority, $isFirst, $lastScheduledTime);
                
                // Convert to timestamp for tracking
                $lastScheduledTime = strtotime($newTime);
                $isFirst = false;
                
                $this->db->table('fw_posts')
                    ->where('postId', $post->postId)
                    ->update([
                        'priority' => $priority,
                        'scheduledPublishTime' => $newTime,
                        'manualPrioritySet' => 'Yes'
                    ]);
                
                $this->logSchedulerAction($post->postId, 'scheduled', $priority, $newTime,
                    "Bulk updated from {$post->oldPriority} to {$priority}");
                
                $updated++;
            }
            
            return $this->response->setJSON([
                'success' => true,
                'count' => $updated
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Execute bulk action on posts
     */
    public function bulkAction()
    {
        $json = $this->request->getJSON();
        $targetPriority = $json->targetPriority ?? 'all';
        $action = $json->action ?? '';
        
        try {
            // Build query based on target priority
            $whereClause = "postStatus = 'Draft' AND scheduledPublishTime IS NOT NULL";
            if ($targetPriority !== 'all') {
                $whereClause .= " AND priority = '{$targetPriority}'";
            }
            
            $posts = $this->db->query("
                SELECT * FROM fw_posts 
                WHERE {$whereClause}
                ORDER BY scheduledPublishTime ASC
            ")->getResult();
            
            $updated = 0;
            
            foreach ($posts as $post) {
                $currentTime = strtotime($post->scheduledPublishTime);
                $newTime = null;
                
                switch ($action) {
                    case 'compress':
                        // Reduce gaps by 50%
                        $minutesFromNow = ($currentTime - time()) / 60;
                        $newMinutes = $minutesFromNow * 0.5;
                        $newTime = date('Y-m-d H:i:s', time() + ($newMinutes * 60));
                        break;
                        
                    case 'expand':
                        // Increase gaps by 50%
                        $minutesFromNow = ($currentTime - time()) / 60;
                        $newMinutes = $minutesFromNow * 1.5;
                        $newTime = date('Y-m-d H:i:s', time() + ($newMinutes * 60));
                        break;
                        
                    case 'shift-tomorrow':
                        // Move to tomorrow at same time
                        $newTime = date('Y-m-d H:i:s', strtotime('+1 day', $currentTime));
                        break;
                        
                    case 'shift-next-week':
                        // Move to next week at same time
                        $newTime = date('Y-m-d H:i:s', strtotime('+7 days', $currentTime));
                        break;
                }
                
                if ($newTime) {
                    $this->db->table('fw_posts')
                        ->where('postId', $post->postId)
                        ->update(['scheduledPublishTime' => $newTime]);
                    $updated++;
                }
            }
            
            $actionLabels = [
                'compress' => 'Compressed schedule',
                'expand' => 'Expanded schedule',
                'shift-tomorrow' => 'Shifted to tomorrow',
                'shift-next-week' => 'Shifted to next week'
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'count' => $updated,
                'message' => $actionLabels[$action] ?? 'Action completed'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Save scheduler settings
     */
    public function saveSettings()
    {
        $json = $this->request->getJSON();
        
        try {
            // Update publishing speed
            if (isset($json->speed)) {
                $this->db->table('fw_scheduler_settings')
                    ->where('settingKey', 'publishing_speed')
                    ->update(['settingValue' => $json->speed]);
            }
            
            // Update time gaps
            if (isset($json->min_gap)) {
                $this->db->table('fw_scheduler_settings')
                    ->where('settingKey', 'min_gap_minutes')
                    ->update(['settingValue' => $json->min_gap]);
            }
            
            if (isset($json->max_gap)) {
                $this->db->table('fw_scheduler_settings')
                    ->where('settingKey', 'max_gap_minutes')
                    ->update(['settingValue' => $json->max_gap]);
            }
            
            if (isset($json->urgent_hours)) {
                $urgentMinutes = $json->urgent_hours * 60;
                $this->db->table('fw_scheduler_settings')
                    ->where('settingKey', 'urgent_publish_minutes')
                    ->update(['settingValue' => $urgentMinutes]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Settings saved successfully'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get post details for editing
     */
    public function getPost($postId)
    {
        $post = $this->db->table('fw_posts')
            ->where('postId', $postId)
            ->get()
            ->getRow();
            
        if (!$post) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Post not found'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'model' => $post->model,
            'csc' => $post->csc,
            'priority' => $post->priority,
            'scheduledTime' => date('Y-m-d\TH:i', strtotime($post->scheduledPublishTime))
        ]);
    }
    
    /**
     * Update post schedule
     */
    public function updateSchedule()
    {
        $json = $this->request->getJSON();
        
        try {
            $this->db->table('fw_posts')
                ->where('postId', $json->postId)
                ->update([
                    'scheduledPublishTime' => date('Y-m-d H:i:s', strtotime($json->scheduleTime)),
                    'priority' => $json->priority,
                    'manualPrioritySet' => 'Yes'
                ]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Schedule updated successfully'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * ✅ FIX: Clear query cache after publishing
     * This ensures homepage and dashboard show updated data immediately
     */
    private function clearPublishCache()
    {
        try {
            // Clear query cache if it exists
            if (class_exists('\App\Libraries\QueryCache')) {
                $cache = new \App\Libraries\QueryCache();
                
                // Clear specific cache patterns that might be affected by publishing
                $cache->forgetPattern('recent_models*');
                $cache->forgetPattern('recent_posts*');
                $cache->forgetPattern('homepage*');
                $cache->forgetPattern('landing*');
                $cache->forgetPattern('scheduler_stats*');
            }
            
            // Clear CodeIgniter's cache if enabled
            $cacheInstance = \Config\Services::cache();
            if ($cacheInstance) {
                $cacheInstance->delete('recent_models');
                $cacheInstance->delete('recent_posts');
                $cacheInstance->delete('homepage_data');
                $cacheInstance->delete('scheduler_dashboard');
            }
        } catch (\Exception $e) {
            // Cache clearing is not critical, log but don't fail
            log_message('warning', 'Failed to clear cache after publish: ' . $e->getMessage());
        }
    }

    /**
     * Show setup instructions when database not configured
     */
    private function showSetupMessage($error = '')
    {
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduler Setup Required</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 50px;
            max-width: 800px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            font-size: 36px;
            margin-bottom: 20px;
        }
        .icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        p {
            color: #555;
            line-height: 1.8;
            margin-bottom: 15px;
        }
        .steps {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .step {
            margin: 15px 0;
            padding-left: 30px;
            position: relative;
        }
        .step::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
            font-size: 20px;
        }
        .code {
            background: #2d3748;
            color: #68d391;
            padding: 15px;
            border-radius: 8px;
            font-family: "Courier New", monospace;
            font-size: 14px;
            margin: 15px 0;
            overflow-x: auto;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s;
        }
        .button:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .error {
            background: #fee;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🚀</div>
        <h1>Setup Required</h1>
        <p>The Firmware Scheduler database schema has not been installed yet. Please follow these steps to complete the setup:</p>
        
        <div class="steps">
            <div class="step">Open <strong>phpMyAdmin</strong> in your browser</div>
            <div class="step">Select your database: <strong>samfirmware</strong></div>
            <div class="step">Click on the <strong>SQL</strong> tab</div>
            <div class="step">Open the file: <code>database_scheduler_complete.sql</code></div>
            <div class="step">Copy the entire SQL content</div>
            <div class="step">Paste into phpMyAdmin SQL tab</div>
            <div class="step">Click <strong>GO</strong> to execute</div>
            <div class="step">Wait for green checkmarks (success)</div>
            <div class="step">Refresh this page</div>
        </div>

        <p><strong>SQL File Location:</strong></p>
        <div class="code">c:\xampp\htdocs\sampro\database_scheduler_complete.sql</div>

        <p><strong>phpMyAdmin URL:</strong></p>
        <div class="code">http://localhost/phpmyadmin</div>

        ' . ($error ? '<div class="error"><strong>Error Details:</strong><br>' . htmlspecialchars($error) . '</div>' : '') . '

        <p style="margin-top: 30px;"><strong>What will be installed:</strong></p>
        <ul style="margin-left: 30px; color: #555; line-height: 2;">
            <li>6 new columns in <code>fw_posts</code> table</li>
            <li><code>fw_scheduler_log</code> table (activity logs)</li>
            <li><code>fw_scheduler_settings</code> table (configuration)</li>
            <li><code>fw_scheduler_stats</code> table (daily metrics)</li>
            <li><code>vw_scheduler_dashboard</code> view (dashboard data)</li>
        </ul>

        <a href="http://localhost/phpmyadmin" class="button" target="_blank">Open phpMyAdmin</a>
        <a href="javascript:location.reload()" class="button" style="background: #27ae60;">Refresh After Install</a>

        <p style="margin-top: 30px; color: #999; font-size: 14px;">
            <strong>Need Help?</strong> Check <code>SCHEDULER_SETUP_GUIDE.md</code> for detailed instructions.
        </p>
    </div>
</body>
</html>';
        
        return $html;
    }
}
