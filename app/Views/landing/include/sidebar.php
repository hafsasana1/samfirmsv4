<!-- Modern Sidebar -->
<div class="lg:col-span-3 lg:order-2 space-y-6">
    <!-- Search Widget -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
        <div class="px-5 py-3 border-b border-gray-100">
            <h3 class="text-ink font-semibold flex items-center space-x-2">
                <span class="w-2 h-2 bg-accent rounded-full"></span>
                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Quick Search</span>
            </h3>
        </div>
        <div class="p-5">
            <div class="relative">
                <input type="text" 
                       class="w-full px-4 py-3 pr-12 rounded-xl border-2 border-gray-200 focus:border-accent focus:ring-4 focus:ring-accent-soft outline-none transition-all search-in-field" 
                       placeholder="Search..." 
                       onkeyup="return isFieldSearch(event,this)">
                <button class="absolute right-2 top-1/2 -translate-y-1/2 p-2 bg-accent text-white rounded-lg hover:bg-accent-hover transition-all" 
                        onclick="searchInField($('.search-in-field').val())">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Recently Added Widget - Compact Modern Design -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 bg-gradient-to-r from-red-50 to-pink-50 border-b border-red-100 flex items-center justify-between">
            <h3 class="text-gray-900 font-bold flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Recently Added</span>
            </h3>
            <span class="px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded animate-pulse">LIVE</span>
        </div>
        <div class="p-3">
            <ul class="space-y-2">
                <?php
                $currentPostId = isset($post) && is_object($post) && isset($post->postId) ? $post->postId : '';
                foreach (recentPosts(5, $currentPostId) as $pst) {
                    $post_url  = postUrl($pst);
                    $deviceName = !empty($pst->device) ? $pst->device : $pst->model;
                    
                    // Get country flag
                    $worldCountries = worldCountries();
                    $cntryRecord = $worldCountries[$pst->country] ?? null;
                    $cntryCode = is_array($cntryRecord) ? strtolower($cntryRecord['code']) : 'us';
                    $flagUrl = base_url('assets/img/flags/4x3/' . $cntryCode . '.svg');
                    
                    // Calculate time ago
                    $timestamp = strtotime($pst->publishedAt ?? $pst->createdTime);
                    $now = time();
                    $diff = $now - $timestamp;
                    
                    if ($diff < 3600) {
                        $timeAgo = floor($diff / 60) . 'min';
                        $timeClass = 'bg-green-100 text-green-700';
                    } elseif ($diff < 86400) {
                        $timeAgo = floor($diff / 3600) . 'h';
                        $timeClass = 'bg-blue-100 text-blue-700';
                    } else {
                        $timeAgo = floor($diff / 86400) . 'd';
                        $timeClass = 'bg-gray-100 text-gray-700';
                    }
                    
                    echo '<li>';
                    echo '<a href="' . $post_url . '" class="block p-2 rounded-lg hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-200">';
                    
                    // Device name and time
                    echo '<div class="flex items-start justify-between gap-2 mb-1.5">';
                    echo '<h4 class="text-sm font-bold text-gray-900 line-clamp-1 flex-1">' . esc(formatDeviceDisplay($deviceName)) . '</h4>';
                    echo '<span class="text-xs font-semibold px-1.5 py-0.5 rounded ' . $timeClass . ' flex-shrink-0">' . $timeAgo . '</span>';
                    echo '</div>';
                    
                    // Model, Flag+CSC, Android version
                    echo '<div class="flex items-center gap-1.5 text-xs flex-wrap">';
                    echo '<span class="font-mono text-red-600 font-semibold">' . esc($pst->model) . '</span>';
                    echo '<span class="text-gray-400">•</span>';
                    echo '<div class="flex items-center gap-1">';
                    echo '<img src="' . $flagUrl . '" class="w-4 h-3 inline-block" alt="flag">';
                    echo '<span class="font-medium text-gray-700">' . esc($pst->csc) . '</span>';
                    echo '</div>';
                    if (!empty($pst->os)) {
                        echo '<span class="text-gray-400">•</span>';
                        echo '<span class="text-red-600 font-semibold">Android ' . esc($pst->os) . '</span>';
                    }
                    echo '</div>';
                    
                    echo '</a>';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
    </div>

    <!-- Sidebar Ads -->
    <?php if (!empty($sidebar_ads)) { ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5">
            <?= $sidebar_ads ?>
        </div>
    </div>
    <?php } ?>
</div>