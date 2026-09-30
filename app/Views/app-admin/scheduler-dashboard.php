<?php
$app = session()->get('fw');

// DEBUG: Output which file is being loaded
echo "<!-- LOADED FROM: " . __FILE__ . " -->";

// Helper function for human-readable time difference
if (!function_exists('human_time_diff')) {
    function human_time_diff($from, $to = 0) {
        if (empty($to)) $to = time();
        $diff = (int) abs($to - $from);
        
        if ($diff < 60) return $diff . ' seconds';
        if ($diff < 3600) return round($diff / 60) . ' minutes';
        if ($diff < 86400) return round($diff / 3600) . ' hours';
        return round($diff / 86400) . ' days';
    }
}
?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Firmware Scheduler</h1>
    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Smart priority-based random publishing with automatic new model detection</p>
</div>

<?php if (empty($stats) || !isset($stats->scheduler_status)): ?>
    <!-- Database Not Setup -->
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 rounded-lg p-6 mb-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 dark:bg-yellow-800 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-2xl text-yellow-600 dark:text-yellow-400"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Database Setup Required</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    The scheduler database tables have not been installed yet. Please run the SQL setup file to continue.
                </p>
                <div class="bg-gray-800 rounded-lg p-3 mb-4">
                    <p class="text-xs text-gray-400 mb-1">SQL File Location:</p>
                    <p class="text-sm text-green-400 font-mono">c:\xampp\htdocs\sampro\database_scheduler_complete.sql</p>
                </div>
                <div class="flex gap-3">
                    <a href="http://localhost/phpmyadmin" target="_blank" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-database mr-2"></i>Open phpMyAdmin
                    </a>
                    <button onclick="location.reload()" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh After Setup
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>

<!-- Statistics Cards - Top Row -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    <!-- Scraped Today -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Scraped Today</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2"><?= $stats->today_scraped ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-cloud-download-alt text-2xl text-blue-600 dark:text-blue-400"></i>
            </div>
        </div>
    </div>

    <!-- Pending Drafts -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Drafts</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2"><?= $stats->pending_drafts ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                <i class="fas fa-file-alt text-2xl text-gray-600 dark:text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Urgent Queue -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Urgent Priority</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-2"><?= $stats->urgent_queue ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-circle text-2xl text-red-600 dark:text-red-400"></i>
            </div>
        </div>
    </div>

    <!-- High Priority -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">High Priority</p>
                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-2"><?= $stats->high_queue ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-arrow-circle-up text-2xl text-orange-600 dark:text-orange-400"></i>
            </div>
        </div>
    </div>

    <!-- Normal Priority -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Normal Priority</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-2"><?= $stats->normal_queue ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-2xl text-green-600 dark:text-green-400"></i>
            </div>
        </div>
    </div>

    <!-- Published Today -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Published Today</p>
                <p class="text-2xl font-bold text-accent dark:text-accent mt-2"><?= $stats->published_today ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-accent-soft dark:bg-accent/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-double text-2xl text-accent"></i>
            </div>
        </div>
    </div>
</div>

<!-- Smart Queue Manager -->
<?php
$totalPending = ($stats->pending_drafts ?? 0);
$currentSpeed = $stats->publishing_speed ?? 'normal';
$dailyRates = ['slow' => 15, 'normal' => 25, 'fast' => 40, 'turbo' => 60];
$currentRate = $dailyRates[$currentSpeed] ?? 25;
$daysToComplete = $totalPending > 0 ? round($totalPending / $currentRate, 1) : 0;
$newModelsCount = ($stats->urgent_queue ?? 0);

// ✅ FIX: Always show Smart Queue Manager and Quick Actions - they should be visible even when queue is empty
?>

<div class="bg-purple-600 dark:bg-purple-700 rounded-lg shadow-lg p-6 mb-6 border border-purple-500 dark:border-purple-600" style="background-color: #7c3aed; border: 2px solid #6d28d9;">
    <h2 class="text-xl font-bold mb-4 flex items-center text-white" style="color: white;">
        <i class="fas fa-brain mr-2" style="color: white;"></i> Smart Queue Manager
    </h2>
    
    <?php if ($totalPending > 0 || $newModelsCount > 0): ?>
    <!-- Show queue stats when we have pending posts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Left: Queue Overview -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm text-white font-medium">Total Pending</span>
                <span class="text-2xl font-bold text-white"><?= $totalPending ?> posts</span>
            </div>
            
            <div class="flex items-center justify-between">
                <span class="text-sm text-white font-medium">Current Speed</span>
                <span class="text-lg font-semibold text-white"><?= ucfirst($currentSpeed) ?> (<?= $currentRate ?>/day)</span>
            </div>
            
            <div class="flex items-center justify-between">
                <span class="text-sm text-white font-medium">Estimated Clear Time</span>
                <span class="text-lg font-semibold text-white"><?= $daysToComplete ?> days</span>
            </div>
            
            <div class="h-px bg-white opacity-30 my-2"></div>
            
            <?php if ($newModelsCount > 0): ?>
            <div class="bg-yellow-500 dark:bg-yellow-600 rounded-lg p-3 border-2 border-yellow-400 dark:border-yellow-500">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-rocket text-white text-lg"></i>
                    <span class="font-semibold text-white"><?= $newModelsCount ?> New Models Detected!</span>
                </div>
                <p class="text-xs text-white font-medium">Auto-boosted to urgent priority</p>
                <p class="text-xs text-white font-medium">Will publish in next 30min-2hrs</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Right: Speed Adjustment Preview -->
        <div class="space-y-3">
            <p class="text-sm font-semibold text-white mb-3">Speed Change Impact:</p>
            
            <?php foreach ($dailyRates as $speed => $rate): ?>
                <?php if ($speed !== $currentSpeed): ?>
                    <?php 
                    $newDays = $totalPending > 0 ? round($totalPending / $rate, 1) : 0;
                    $daysSaved = round($daysToComplete - $newDays, 1);
                    ?>
                    <button onclick="changeSpeedPreview('<?= $speed ?>')" 
                            style="background-color: #8b5cf6; color: white;"
                            class="w-full text-left bg-purple-500 dark:bg-purple-600 hover:bg-purple-400 dark:hover:bg-purple-500 rounded-lg p-4 transition-all border-2 border-purple-400 dark:border-purple-500 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="font-bold text-white text-base"><?= ucfirst($speed) ?> (<?= $rate ?>/day)</p>
                                <p class="text-sm text-white font-medium mt-1">Clears in <?= $newDays ?> days</p>
                            </div>
                            <div class="text-right ml-4">
                                <?php if ($daysSaved > 0): ?>
                                    <span class="text-green-200 text-sm font-bold whitespace-nowrap">
                                        <i class="fas fa-arrow-down"></i> <?= abs($daysSaved) ?> days faster
                                    </span>
                                <?php elseif ($daysSaved < 0): ?>
                                    <span class="text-red-200 text-sm font-bold whitespace-nowrap">
                                        <i class="fas fa-arrow-up"></i> <?= abs($daysSaved) ?> days slower
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <!-- Show message when queue is empty -->
    <div class="bg-purple-500 dark:bg-purple-600 rounded-lg p-4 mb-4 border-2 border-purple-400 dark:border-purple-500">
        <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-white text-2xl"></i>
            <div>
                <p class="text-white font-semibold">Queue is Empty</p>
                <p class="text-white text-sm">All posts have been published. Run scraper to add new firmwares.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t-2 border-white border-opacity-30">
        <button onclick="publishNextNow(10)" style="background-color: #8b5cf6; color: white; border: 2px solid #a78bfa;" class="bg-purple-500 dark:bg-purple-600 hover:bg-purple-400 dark:hover:bg-purple-500 rounded-lg py-3 px-4 font-bold transition-all text-white">
            <i class="fas fa-bolt mr-2 text-white"></i><span class="text-white">Publish Next 10 Now</span>
        </button>
        
        <button onclick="rerandomizeAll()" style="background-color: #8b5cf6; color: white; border: 2px solid #a78bfa;" class="bg-purple-500 dark:bg-purple-600 hover:bg-purple-400 dark:hover:bg-purple-500 rounded-lg py-3 px-4 font-bold transition-all text-white">
            <i class="fas fa-random mr-2 text-white"></i><span class="text-white">Re-randomize Times</span>
        </button>
        
        <button onclick="showModelSearch()" style="background-color: #8b5cf6; color: white; border: 2px solid #a78bfa;" class="bg-purple-500 dark:bg-purple-600 hover:bg-purple-400 dark:hover:bg-purple-500 rounded-lg py-3 px-4 font-bold transition-all text-white">
            <i class="fas fa-search mr-2 text-white"></i><span class="text-white">Find & Prioritize</span>
        </button>
        
        <button onclick="showBulkActions()" style="background-color: #8b5cf6; color: white; border: 2px solid #a78bfa;" class="bg-purple-500 dark:bg-purple-600 hover:bg-purple-400 dark:hover:bg-purple-500 rounded-lg py-3 px-4 font-bold transition-all text-white">
            <i class="fas fa-tasks mr-2 text-white"></i><span class="text-white">Bulk Actions</span>
        </button>
    </div>
</div>

<!-- Scheduler Status Card -->
<!-- DEBUG: Stats object check -->
<?php 
// Debug: Show what stats we have
if (false) { // Set to true to enable debug
    echo "<div class='bg-yellow-100 p-4 mb-4 rounded'>";
    echo "<h3 class='font-bold'>DEBUG - Stats Object:</h3>";
    echo "<pre>" . print_r($stats, true) . "</pre>";
    echo "</div>";
}
?>
<!-- ALWAYS SHOW Scheduler Status Card (if $stats exists) -->
<?php if (isset($stats)): ?>
<div class="bg-teal-600 dark:bg-teal-700 rounded-lg shadow-lg p-6 mb-6 border border-teal-500 dark:border-teal-600">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-sm font-semibold text-white mb-3">Scheduler Status</p>
            <div class="flex items-center gap-4 mt-3">
                <?php if ($stats->scheduler_status === 'Yes'): ?>
                    <span class="px-3 py-1.5 bg-teal-500 dark:bg-teal-600 text-white text-sm font-semibold rounded-full border-2 border-teal-400 dark:border-teal-500">
                        <i class="fas fa-circle text-xs mr-1"></i> Active
                    </span>
                <?php else: ?>
                    <span class="px-3 py-1.5 bg-red-500 dark:bg-red-600 text-white text-sm font-semibold rounded-full border-2 border-red-400 dark:border-red-500">
                        <i class="fas fa-circle text-xs mr-1"></i> Disabled
                    </span>
                <?php endif; ?>
                
                <span class="px-3 py-1.5 bg-teal-500 dark:bg-teal-600 text-white text-sm font-semibold rounded-full border-2 border-teal-400 dark:border-teal-500">
                    <?php
                    $speedLabels = [
                        'slow' => 'Slow (15/day)',
                        'normal' => 'Normal (25/day)',
                        'fast' => 'Fast (40/day)',
                        'turbo' => 'Turbo (60/day)'
                    ];
                    echo $speedLabels[$stats->publishing_speed] ?? 'Normal (25/day)';
                    ?>
                </span>
            </div>
        </div>
        <div class="w-12 h-12 bg-teal-500 dark:bg-teal-600 rounded-lg flex items-center justify-center border-2 border-teal-400 dark:border-teal-500">
            <i class="fas fa-clock text-2xl text-white"></i>
        </div>
    </div>
    
    <?php if (!empty($stats->next_publish_time)): ?>
    <div class="bg-teal-500 dark:bg-teal-600 rounded-lg p-3 mt-4 border-2 border-teal-400 dark:border-teal-500">
        <p class="text-xs text-white font-semibold mb-1">Next Scheduled Post</p>
        <p class="text-sm font-bold text-white">
            <?= date('l, F j, Y \a\t g:i A', strtotime($stats->next_publish_time)) ?>
        </p>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Scheduler Settings -->
<!-- ALWAYS SHOW Scheduler Settings (if $stats exists) -->
<?php if (isset($stats)): ?>
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        <i class="fas fa-cog mr-2"></i>Scheduler Settings
    </h2>
    
    <form id="schedulerSettingsForm" class="space-y-6">
        <!-- Publishing Speed -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Publishing Speed</label>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <label class="relative flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border-2 border-transparent has-[:checked]:border-accent has-[:checked]:bg-accent-soft dark:has-[:checked]:bg-accent/20">
                    <input type="radio" name="speed" value="slow" <?= ($stats->publishing_speed === 'slow') ? 'checked' : '' ?> class="sr-only">
                    <div class="flex-1">
                        <p class="font-semibold text-sm text-gray-900 dark:text-white">Slow</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">15 posts/day</p>
                    </div>
                    <i class="fas fa-check-circle text-accent opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                </label>
                
                <label class="relative flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border-2 border-transparent has-[:checked]:border-accent has-[:checked]:bg-accent-soft dark:has-[:checked]:bg-accent/20">
                    <input type="radio" name="speed" value="normal" <?= ($stats->publishing_speed === 'normal') ? 'checked' : '' ?> class="sr-only">
                    <div class="flex-1">
                        <p class="font-semibold text-sm text-gray-900 dark:text-white">Normal</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">25 posts/day</p>
                    </div>
                    <i class="fas fa-check-circle text-accent opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                </label>
                
                <label class="relative flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border-2 border-transparent has-[:checked]:border-accent has-[:checked]:bg-accent-soft dark:has-[:checked]:bg-accent/20">
                    <input type="radio" name="speed" value="fast" <?= ($stats->publishing_speed === 'fast') ? 'checked' : '' ?> class="sr-only">
                    <div class="flex-1">
                        <p class="font-semibold text-sm text-gray-900 dark:text-white">Fast</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">40 posts/day</p>
                    </div>
                    <i class="fas fa-check-circle text-accent opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                </label>
                
                <label class="relative flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border-2 border-transparent has-[:checked]:border-accent has-[:checked]:bg-accent-soft dark:has-[:checked]:bg-accent/20">
                    <input type="radio" name="speed" value="turbo" <?= ($stats->publishing_speed === 'turbo') ? 'checked' : '' ?> class="sr-only">
                    <div class="flex-1">
                        <p class="font-semibold text-sm text-gray-900 dark:text-white">Turbo</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">60 posts/day</p>
                    </div>
                    <i class="fas fa-check-circle text-accent opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                </label>
            </div>
        </div>

        <!-- Time Settings -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Min Gap (minutes)</label>
                <input type="number" name="min_gap" value="<?= $stats->daily_budget ?? 10 ?>" min="5" max="60"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-accent">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Minimum time between posts</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Gap (minutes)</label>
                <input type="number" name="max_gap" value="<?= $stats->daily_budget ?? 90 ?>" min="30" max="180"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-accent">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maximum time between posts</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Urgent Window (hours)</label>
                <input type="number" name="urgent_hours" value="2" min="1" max="6" step="0.5"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-accent">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">New models publish within</p>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button type="button" onclick="resetSettings()" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg font-medium transition-colors">
                <i class="fas fa-undo mr-2"></i>Reset to Default
            </button>
            <button type="submit" class="px-6 py-2 bg-accent hover:bg-accent-hover text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Publishing Timeline -->
<?php if (!empty($upcomingPosts) && count($upcomingPosts) > 0): ?>
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        <i class="fas fa-calendar-alt mr-2"></i>Publishing Timeline (Next 20 Posts)
    </h2>
    
    <div class="space-y-3">
        <?php 
        $prevTime = null;
        foreach ($upcomingPosts as $index => $post): 
            // ✨ FIX: Don't add UTC - use local timezone from database
            $currentTime = strtotime($post->scheduledPublishTime);
            $gap = null;
            if ($prevTime) {
                $gapMinutes = round(($currentTime - $prevTime) / 60);
                $gap = $gapMinutes;
            }
            $prevTime = $currentTime;
            
            $priorityColors = [
                'urgent' => 'border-red-500 bg-red-50 dark:bg-red-900/20',
                'high' => 'border-orange-500 bg-orange-50 dark:bg-orange-900/20',
                'normal' => 'border-green-500 bg-green-50 dark:bg-green-900/20'
            ];
            $borderClass = $priorityColors[$post->priority] ?? 'border-gray-300';
        ?>
        
        <div class="relative">
            <?php if ($gap !== null): ?>
            <div class="flex items-center justify-center py-2">
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <div class="h-px w-8 bg-gray-300 dark:bg-gray-600"></div>
                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-full">
                        <i class="fas fa-clock mr-1"></i><?= $gap ?> min gap
                    </span>
                    <div class="h-px w-8 bg-gray-300 dark:bg-gray-600"></div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="flex items-center gap-4 p-4 border-l-4 <?= $borderClass ?> rounded-lg">
                <div class="flex-shrink-0 w-12 h-12 bg-white dark:bg-gray-700 rounded-lg flex items-center justify-center border border-gray-200 dark:border-gray-600">
                    <span class="text-sm font-bold text-gray-600 dark:text-gray-400">#<?= $index + 1 ?></span>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white"><?= esc($post->model) ?></span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">•</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400"><?= esc($post->csc) ?></span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $post->priority === 'urgent' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : ($post->priority === 'high' ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300') ?>">
                            <?= strtoupper($post->priority) ?>
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-clock mr-1"></i><?= date('l, M j, Y \a\t g:i A', $currentTime) ?>
                        <span class="ml-2 text-gray-400">
                            (<?= $currentTime > time() ? 'in ' . human_time_diff(time(), $currentTime) : 'overdue' ?>)
                        </span>
                    </p>
                </div>
                
                <button onclick="editSchedule(<?= $post->postId ?>)" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Quick Actions -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <button onclick="runAutoSchedule()" 
       class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-4 hover:shadow-lg transition-all group text-left">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-magic text-lg text-purple-600 dark:text-purple-400"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Auto-Schedule</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Randomize all drafts</p>
            </div>
        </div>
    </button>
    
    <button onclick="runPublisher()"
       class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-4 hover:shadow-lg transition-all group text-left">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-play text-lg text-green-600 dark:text-green-400"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Run Publisher</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Publish due posts now</p>
            </div>
        </div>
    </button>
    
    <a href="<?= site_url('firmware-scraper/scrape') ?>" target="_blank"
       class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-4 hover:shadow-lg transition-all group">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-download text-lg text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Run Scraper</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Fetch new firmwares</p>
            </div>
        </div>
    </a>
    
    <button onclick="location.reload()" 
            class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-4 hover:shadow-lg transition-all group text-left">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-sync-alt text-lg text-gray-600 dark:text-gray-400"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Refresh Stats</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Update dashboard</p>
            </div>
        </div>
    </button>
</div>

<?php endif; // End main database check ?>

<!-- Activity Modal -->
<div id="activityModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeModal()">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modalTitle">Processing...</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors hover:scale-110" title="Close (Esc)">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1" id="modalContent">
            <div class="flex items-center justify-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-accent"></i>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex-shrink-0">
            <button onclick="closeModal()" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors shadow-sm hover:shadow">
                <i class="fas fa-times mr-2"></i>Close
            </button>
            <button onclick="location.reload()" class="px-5 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg font-medium transition-colors shadow-sm hover:shadow-md">
                <i class="fas fa-sync-alt mr-2"></i>Refresh Dashboard
            </button>
        </div>
    </div>
</div>

<script>
// Close modal function
function closeModal() {
    document.getElementById('activityModal').classList.add('hidden');
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});

function showModal(title, content) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalContent').innerHTML = content;
    document.getElementById('activityModal').classList.remove('hidden');
}

function runAutoSchedule() {
    showModal('Auto-Scheduling Drafts...', '<div class="flex items-center justify-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-accent"></i><span class="ml-4 text-gray-600 dark:text-gray-400">Please wait...</span></div>');
    
    fetch('<?= site_url('firmware-scheduler/auto-schedule-ajax') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showModal('Auto-Schedule Complete', data.html);
            } else {
                showModal('Error', '<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>' + data.message + '</div>');
            }
        })
        .catch(error => {
            showModal('Error', '<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>Failed to execute: ' + error + '</div>');
        });
}

function runPublisher() {
    showModal('Publishing Due Posts...', '<div class="flex items-center justify-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-accent"></i><span class="ml-4 text-gray-600 dark:text-gray-400">Please wait...</span></div>');
    
    fetch('<?= site_url('firmware-scheduler/publish-ajax') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showModal('Publisher Complete', data.html);
            } else {
                showModal('Error', '<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>' + data.message + '</div>');
            }
        })
        .catch(error => {
            showModal('Error', '<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>Failed to execute: ' + error + '</div>');
        });
}

// Save scheduler settings (only if form exists)
const schedulerForm = document.getElementById('schedulerSettingsForm');
if (schedulerForm) {
    schedulerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        fetch('<?= site_url('firmware-scheduler/save-settings') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showModal('Settings Saved', '<div class="text-center py-8"><i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i><p class="text-lg font-semibold text-gray-900 dark:text-white">Settings updated successfully!</p></div>');
                setTimeout(() => location.reload(), 2000);
            } else {
                showModal('Error', '<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>' + data.message + '</div>');
            }
        });
    });
}

function resetSettings() {
    if (confirm('Reset all settings to default values?')) {
        document.querySelector('[name="speed"][value="normal"]').checked = true;
        document.querySelector('[name="min_gap"]').value = 10;
        document.querySelector('[name="max_gap"]').value = 90;
        document.querySelector('[name="urgent_hours"]').value = 2;
    }
}

function editSchedule(postId) {
    showModal('Edit Schedule', '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-accent"></i></div>');
    
    // TODO: Load edit form
    fetch('<?= site_url('firmware-scheduler/get-post/') ?>' + postId)
        .then(response => response.json())
        .then(data => {
            const html = `
                <div class="space-y-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Model: ${data.model}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">CSC: ${data.csc}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Scheduled Time</label>
                        <input type="datetime-local" id="editScheduleTime" value="${data.scheduledTime}" 
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                        <select id="editPriority" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="urgent" ${data.priority === 'urgent' ? 'selected' : ''}>Urgent</option>
                            <option value="high" ${data.priority === 'high' ? 'selected' : ''}>High</option>
                            <option value="normal" ${data.priority === 'normal' ? 'selected' : ''}>Normal</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-3 pt-4">
                        <button onclick="saveScheduleEdit(${postId})" class="flex-1 px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-lg font-medium">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                        <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium">
                            Cancel
                        </button>
                    </div>
                </div>
            `;
            showModal('Edit Schedule', html);
        });
}

function saveScheduleEdit(postId) {
    const scheduleTime = document.getElementById('editScheduleTime').value;
    const priority = document.getElementById('editPriority').value;
    
    fetch('<?= site_url('firmware-scheduler/update-schedule') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ postId, scheduleTime, priority })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showModal('Success', '<div class="text-center py-8"><i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i><p class="text-lg font-semibold">Schedule updated!</p></div>');
            setTimeout(() => location.reload(), 1500);
        } else {
            showModal('Error', '<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>' + data.message + '</div>');
        }
    });
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

// Helper function for human readable time
function human_time_diff(from, to) {
    const diff = Math.abs(to - from);
    if (diff < 60) return diff + ' seconds';
    if (diff < 3600) return Math.round(diff / 60) + ' minutes';
    if (diff < 86400) return Math.round(diff / 3600) + ' hours';
    return Math.round(diff / 86400) + ' days';
}

// Speed change preview
function changeSpeedPreview(speed) {
    if (confirm(`Switch to ${speed.toUpperCase()} speed? This will recalculate all scheduled times.`)) {
        document.querySelector(`[name="speed"][value="${speed}"]`).checked = true;
        document.getElementById('schedulerSettingsForm').dispatchEvent(new Event('submit'));
    }
}

// Publish next N posts immediately
function publishNextNow(count) {
    if (!confirm(`Publish the next ${count} scheduled posts immediately?`)) return;
    
    showModal('Publishing Posts...', '<div class="flex items-center justify-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-accent"></i><span class="ml-4 text-gray-600 dark:text-gray-400">Publishing ' + count + ' posts...</span></div>');
    
    fetch('<?= site_url('firmware-scheduler/publish-next') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ count: count })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showModal('Success', data.html);
            setTimeout(() => location.reload(), 2000);
        } else {
            showModal('Error', '<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>' + data.message + '</div>');
        }
    });
}

// Re-randomize all scheduled times
function rerandomizeAll() {
    if (!confirm('Re-randomize all scheduled times? This will generate new random gaps between posts.')) return;
    
    showModal('Re-randomizing...', '<div class="flex items-center justify-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-accent"></i><span class="ml-4 text-gray-600 dark:text-gray-400">Recalculating schedules...</span></div>');
    
    fetch('<?= site_url('firmware-scheduler/rerandomize') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showModal('Success', '<div class="text-center py-8"><i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i><p class="text-lg font-semibold">All schedules re-randomized!</p></div>');
                setTimeout(() => location.reload(), 1500);
            } else {
                showModal('Error', '<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>' + data.message + '</div>');
            }
        });
}

// Show model search interface
function showModelSearch() {
    const html = `
        <div class="space-y-4">
            <p class="text-gray-600 dark:text-gray-400">Search for specific models and change their priority:</p>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Model Pattern</label>
                <input type="text" id="modelSearchInput" placeholder="e.g., SM-F7, SM-S9, SM-A54" 
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Use * as wildcard (e.g., SM-F7* matches SM-F756B, SM-F761B, etc.)</p>
            </div>
            
            <div class="flex gap-3">
                <button onclick="searchAndPrioritize('urgent')" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
                    <i class="fas fa-exclamation-circle mr-2"></i>Make Urgent
                </button>
                <button onclick="searchAndPrioritize('high')" class="flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium">
                    <i class="fas fa-arrow-up mr-2"></i>Make High
                </button>
                <button onclick="searchAndPrioritize('normal')" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                    <i class="fas fa-check mr-2"></i>Make Normal
                </button>
            </div>
        </div>
    `;
    
    showModal('Find & Prioritize Models', html);
}

function searchAndPrioritize(priority) {
    const pattern = document.getElementById('modelSearchInput').value.trim();
    
    if (!pattern) {
        alert('Please enter a model pattern to search');
        return;
    }
    
    if (!confirm(`Change all posts matching "${pattern}" to ${priority.toUpperCase()} priority?`)) return;
    
    showModal('Processing...', '<div class="flex items-center justify-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-accent"></i></div>');
    
    fetch('<?= site_url('firmware-scheduler/bulk-prioritize') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pattern: pattern, priority: priority })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showModal('Success', `<div class="text-center py-8"><i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i><p class="text-lg font-semibold">Updated ${data.count} posts!</p></div>`);
            setTimeout(() => location.reload(), 1500);
        } else {
            showModal('Error', '<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>' + data.message + '</div>');
        }
    });
}

// Show bulk actions interface
function showBulkActions() {
    const html = `
        <div class="space-y-4">
            <p class="text-gray-600 dark:text-gray-400">Select priority level and action:</p>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Target Priority</label>
                <select id="bulkTargetPriority" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="all">All Priorities</option>
                    <option value="urgent">Urgent Only</option>
                    <option value="high">High Only</option>
                    <option value="normal">Normal Only</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Action</label>
                <select id="bulkAction" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="compress">Compress Schedule (reduce gaps)</option>
                    <option value="expand">Expand Schedule (increase gaps)</option>
                    <option value="shift-tomorrow">Shift All to Tomorrow</option>
                    <option value="shift-next-week">Shift All to Next Week</option>
                </select>
            </div>
            
            <button onclick="executeBulkAction()" class="w-full px-4 py-3 bg-accent hover:bg-accent-hover text-white rounded-lg font-medium">
                <i class="fas fa-bolt mr-2"></i>Execute Bulk Action
            </button>
        </div>
    `;
    
    showModal('Bulk Actions', html);
}

function executeBulkAction() {
    const targetPriority = document.getElementById('bulkTargetPriority').value;
    const action = document.getElementById('bulkAction').value;
    
    if (!confirm(`Execute "${action}" on "${targetPriority}" posts?`)) return;
    
    showModal('Processing...', '<div class="flex items-center justify-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-accent"></i><span class="ml-4 text-gray-600 dark:text-gray-400">Processing bulk action...</span></div>');
    
    fetch('<?= site_url('firmware-scheduler/bulk-action') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ targetPriority: targetPriority, action: action })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showModal('Success', `<div class="text-center py-8"><i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i><p class="text-lg font-semibold">${data.message}</p><p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Affected ${data.count} posts</p></div>`);
            setTimeout(() => location.reload(), 2000);
        } else {
            showModal('Error', '<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>' + data.message + '</div>');
        }
    });
}
</script>

<!-- Recent Activity Log -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-card overflow-hidden">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Latest scheduler actions and events</p>
    </div>
    
    <?php if (!empty($logs)): ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Time</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Model</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">CSC</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Message</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($logs as $log): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                        <?= date('M j, g:i A', strtotime($log->createdAt)) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <?php
                        $actionConfig = [
                            'scheduled' => ['color' => 'blue', 'icon' => 'fa-calendar-check'],
                            'published' => ['color' => 'green', 'icon' => 'fa-check-circle'],
                            'failed' => ['color' => 'red', 'icon' => 'fa-times-circle'],
                            'skipped' => ['color' => 'yellow', 'icon' => 'fa-forward']
                        ];
                        $config = $actionConfig[$log->action] ?? ['color' => 'gray', 'icon' => 'fa-circle'];
                        ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?= $config['color'] ?>-100 text-<?= $config['color'] ?>-800 dark:bg-<?= $config['color'] ?>-900/30 dark:text-<?= $config['color'] ?>-300">
                            <i class="fas <?= $config['icon'] ?> mr-1.5"></i>
                            <?= ucfirst($log->action) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        <?= $log->model ?? 'N/A' ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                        <?= $log->csc ?? 'N/A' ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <?php
                        $priorityConfig = [
                            'urgent' => 'red',
                            'high' => 'orange',
                            'normal' => 'green'
                        ];
                        $pColor = $priorityConfig[$log->priority] ?? 'gray';
                        ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?= $pColor ?>-100 text-<?= $pColor ?>-800 dark:bg-<?= $pColor ?>-900/30 dark:text-<?= $pColor ?>-300">
                            <?= strtoupper($log->priority) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                        <?= esc($log->message) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="text-center py-12">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
            <i class="fas fa-inbox text-2xl text-gray-400 dark:text-gray-500"></i>
        </div>
        <p class="text-gray-500 dark:text-gray-400">No activity yet</p>
        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Run the scheduler to see logs here</p>
    </div>
    <?php endif; ?>
</div>
