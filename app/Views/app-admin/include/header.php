<?php
$app     = session()->get('fw');
$filePath = RESOURCE_PATH . 'web-setting.info';
$webtrAsc = @json_decode(@file_get_contents($filePath));

// Avatar fallback system: profile.png → avatar-default.svg → inline SVG
$userInitial = strtoupper(substr($app->name ?? 'U', 0, 1));

// Check for valid profile images
if (file_exists(FCPATH . 'resource/profile.png') && filesize(FCPATH . 'resource/profile.png') > 1024) {
    $profileImage = base_url('resource/profile.png') . '?v=' . filemtime(FCPATH . 'resource/profile.png');
} elseif (file_exists(FCPATH . 'resource/avatar.png') && filesize(FCPATH . 'resource/avatar.png') > 1024) {
    $profileImage = base_url('resource/avatar.png') . '?v=' . filemtime(FCPATH . 'resource/avatar.png');
} elseif (file_exists(FCPATH . 'resource/avatar-default.svg')) {
    $profileImage = base_url('resource/avatar-default.svg') . '?v=' . time();
} else {
    // Inline SVG with user's initial as final fallback
    $profileImage = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22 viewBox=%220 0 100 100%22%3E%3Ccircle cx=%2250%22 cy=%2250%22 r=%2250%22 fill=%22%23ff767a%22/%3E%3Ctext x=%2250%22 y=%2250%22 font-size=%2240%22 fill=%22white%22 text-anchor=%22middle%22 dy=%22.35em%22 font-family=%22Arial, sans-serif%22 font-weight=%22bold%22%3E' . $userInitial . '%3C/text%3E%3C/svg%3E';
}
$webLogo      = base_url() . 'resource/logo.png';
$favicon      = base_url() . 'resource/favicon.ico';

if ($webtrAsc && !empty($webtrAsc->webLogo) && file_exists(RESOURCE_PATH . '' . $webtrAsc->webLogo)) {
    $webLogo = base_url() . 'resource/' . $webtrAsc->webLogo;
}
if ($webtrAsc && !empty($webtrAsc->favicon) && file_exists(RESOURCE_PATH . '' . $webtrAsc->favicon)) {
    $favicon = base_url() . 'resource/' . $webtrAsc->favicon;
}
$webLogo = $webLogo . '?v=' . time();
$favicon = $favicon . '?v=' . time();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?= $webtrAsc->webTitle ?? 'Admin' ?></title>
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
        <meta name="theme-color" content="#1FBF8F">
        <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>">
        <link rel="shortcut icon" href="<?= $favicon ?>">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/tailwind-output.css?v=<?= time() ?>">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <link rel="stylesheet" href="<?= base_url() ?>assets/sweetalert/sweetalert.css">
        <script src="<?= base_url() ?>assets/sweetalert/sweetalert-dev.js"></script>
        <style>
            [x-cloak] { display: none !important; }
        </style>
        
        <!-- Dark Mode Script - Runs before Alpine.js to prevent flash -->
        <script>
            // Apply dark mode immediately on page load
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        </script>
    </head>
    <body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased" 
          x-data="{ 
              sidebarOpen: true, 
              mobileMenuOpen: false, 
              darkMode: localStorage.getItem('darkMode') === 'true' 
          }" 
          x-init="$watch('darkMode', value => localStorage.setItem('darkMode', value))"
          :class="darkMode ? 'dark' : ''">
        <!-- Top Header -->
        <header class="fixed top-0 left-0 right-0 h-16 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 z-50">
            <div class="h-full flex items-center justify-between px-4 lg:px-6">
                <!-- Left: Logo & Sidebar Toggle -->
                <div class="flex items-center gap-4">
                    <!-- Mobile Menu Toggle -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    
                    <!-- Desktop Sidebar Toggle -->
                    <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:block p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Toggle Sidebar">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    
                    <!-- Logo -->
                    <a href="<?= base_url(ADMIN_PATH) ?>/dashboard" class="flex items-center">
                        <img src="<?= $webLogo . '?u=' . time() ?>" alt="<?= $webtrAsc->webTitle ?? '' ?>" class="h-10 w-auto">
                    </a>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-3">
                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" 
                            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            title="Toggle Dark Mode">
                        <i x-show="!darkMode" x-cloak class="fas fa-moon text-gray-600 dark:text-gray-300"></i>
                        <i x-show="darkMode" x-cloak class="fas fa-sun text-yellow-500"></i>
                    </button>

                    <!-- Cache Management Dropdown (Admin Only) -->
                    <?php if ($app->type == 'Admin'): ?>
                    <div x-data="{ cacheOpen: false }" class="relative">
                        <button @click="cacheOpen = !cacheOpen" 
                                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-accent-soft dark:hover:bg-accent/20 text-gray-700 dark:text-gray-300 hover:text-accent transition-colors"
                                title="Cache Management">
                            <i class="fas fa-bolt text-sm"></i>
                            <span class="text-sm font-medium hidden lg:inline">Cache</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        
                        <!-- Cache Dropdown Menu -->
                        <div x-show="cacheOpen" 
                             @click.away="cacheOpen = false"
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
                            
                            <!-- Cache Options -->
                            <div class="py-1">
                                <button onclick="clearCache('all')" 
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left">
                                    <i class="fas fa-sync-alt w-4 text-center text-accent"></i>
                                    <span>Clear All Cache</span>
                                </button>
                                
                                <button onclick="clearCache('stats')" 
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left">
                                    <i class="fas fa-chart-bar w-4 text-center text-cyan-500"></i>
                                    <span>Clear Site Stats</span>
                                </button>
                                
                                <button onclick="clearCache('query')" 
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left">
                                    <i class="fas fa-database w-4 text-center text-green-500"></i>
                                    <span>Clear Query Cache</span>
                                </button>
                                
                                <button onclick="clearCache('page')" 
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left">
                                    <i class="fas fa-file-alt w-4 text-center text-blue-500"></i>
                                    <span>Clear Page Cache</span>
                                </button>
                                
                                <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                                
                                <button onclick="viewCacheInfo()" 
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left">
                                    <i class="fas fa-info-circle w-4 text-center text-gray-500"></i>
                                    <span>View Cache Info</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Website Link -->
                    <a href="<?= base_url() ?>" target="_blank" 
                       class="hidden sm:flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-accent-soft dark:hover:bg-accent/20 text-gray-700 dark:text-gray-300 hover:text-accent transition-colors"
                       title="<?= $webtrAsc->webTitle ?? '' ?>">
                        <i class="fas fa-external-link-alt text-sm"></i>
                        <span class="text-sm font-medium hidden md:inline">Website</span>
                    </a>

                    <!-- Notifications Dropdown -->
                    <?php $contactUsArr = getContactUSHeader(); ?>
                    <div x-data="{ notifOpen: false }" class="relative">
                        <button @click="notifOpen = !notifOpen" 
                                class="relative p-2 rounded-lg hover:bg-accent-soft dark:hover:bg-accent/20 transition-colors">
                            <i class="fas fa-bell text-lg text-gray-600 dark:text-gray-300"></i>
                            <?php if ($contactUsArr['ecount'] != '0'): ?>
                                <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                                    <?= $contactUsArr['ecount'] ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        
                        <!-- Notifications Dropdown Menu -->
                        <div x-show="notifOpen" 
                             @click.away="notifOpen = false"
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <!-- Header -->
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Recent Contact Us</h3>
                            </div>
                            
                            <!-- Notifications List -->
                            <div class="max-h-96 overflow-y-auto">
                                <?php foreach ($contactUsArr['record'] as $rec): ?>
                                    <a href="<?= base_url(ADMIN_PATH . '/contact-us/' . $rec->contactUsId) ?>" 
                                       class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-b border-gray-100 dark:border-gray-700 <?= $rec->status == '0' ? 'bg-accent-soft dark:bg-accent/20' : '' ?>">
                                        <div class="flex gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                                                    <i class="fas fa-exclamation-triangle text-cyan-600 dark:text-cyan-400"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between mb-1">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-white"><?= $rec->fromName ?></p>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400"><?= timeAgo($rec->createdTime) ?></span>
                                                </div>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 truncate"><?= substr($rec->description, 0, 100) ?></p>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Footer -->
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                                <a href="<?= base_url(ADMIN_PATH) ?>/contact-us" 
                                   class="block text-center text-sm font-medium text-accent hover:text-accent-hover">
                                    See All
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div x-data="{ profileOpen: false }" class="relative">
                        <button @click="profileOpen = !profileOpen" 
                                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-accent-soft dark:hover:bg-accent/20 transition-colors">
                            <img src="<?= $profileImage ?>" 
                                 alt="<?= esc($app->name ?? 'User') ?>" 
                                 onerror="this.onerror=null; this.src='<?= base_url('resource/avatar-default.svg') ?>?v=<?= time() ?>'"
                                 class="w-8 h-8 rounded-full border-2 border-accent object-cover bg-gray-100 dark:bg-gray-700">
                            <span class="text-sm font-medium text-gray-900 dark:text-white hidden md:inline"><?= $app->name ?? '' ?></span>
                            <i class="fas fa-chevron-down text-xs text-gray-500 dark:text-gray-400 hidden md:inline"></i>
                        </button>
                        
                        <!-- Profile Dropdown Menu -->
                        <div x-show="profileOpen" 
                             @click.away="profileOpen = false"
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <a href="<?= base_url(ADMIN_PATH) ?>/profile" 
                               class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-accent-soft dark:hover:bg-accent/20 hover:text-accent transition-colors">
                                <i class="fas fa-user w-5"></i> Profile
                            </a>
                            <a href="<?= base_url(ADMIN_PATH) ?>/logout" 
                               class="block px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors border-t border-gray-200 dark:border-gray-700">
                                <i class="fas fa-sign-out-alt w-5"></i> Sign Out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>


<!-- Cache Management JavaScript -->
<script>
// Lightweight cache clearing function with minimal server load
function clearCache(type) {
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    
    // Disable button and show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin w-4 text-center"></i><span>Clearing...</span>';
    
    // AJAX call with minimal payload (CSRF exempted for this route)
    $.ajax({
        url: '<?= base_url("app-admin/clearCache") ?>',
        type: 'POST',
        data: { 
            cacheType: type
        },
        dataType: 'json',
        timeout: 5000, // 5 second timeout - lightweight operation
        success: function(response) {
            if (response.success) {
                // Success - show green checkmark briefly
                btn.innerHTML = '<i class="fas fa-check w-4 text-center text-green-500"></i><span class="text-green-500">Cleared!</span>';
                toastr.success(response.message, '', { timeOut: 3000, positionClass: 'toast-top-right' });
                
                // Restore button after 2 seconds
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }, 2000);
            } else {
                // Error
                btn.innerHTML = originalHTML;
                btn.disabled = false;
                toastr.error(response.message || 'Failed to clear cache', '', { timeOut: 3000 });
            }
        },
        error: function(xhr, status, error) {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            toastr.error('Error clearing cache', '', { timeOut: 3000 });
        }
    });
}

// View cache info (read-only, no performance impact)
function viewCacheInfo() {
    $.ajax({
        url: '<?= base_url("app-admin/getCacheInfo") ?>',
        type: 'GET',
        dataType: 'json',
        timeout: 3000, // Quick read operation
        success: function(response) {
            if (response.success) {
                // Build info HTML
                let infoHTML = '<div class="space-y-3">';
                
                if (response.data && response.data.caches) {
                    response.data.caches.forEach(cache => {
                        const statusColor = cache.exists ? 'text-green-600' : 'text-gray-400';
                        const statusIcon = cache.exists ? 'fa-check-circle' : 'fa-times-circle';
                        infoHTML += `
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-2">
                                    <i class="fas ${statusIcon} ${statusColor}"></i>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">${cache.name}</span>
                                </div>
                                <span class="text-xs text-gray-500">${cache.status}</span>
                            </div>
                        `;
                    });
                }
                
                infoHTML += '</div>';
                infoHTML += '<div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-500">';
                infoHTML += 'Last checked: ' + (response.data.timestamp || 'Now');
                infoHTML += '</div>';
                
                // Show in SweetAlert
                swal({
                    title: "Cache Status",
                    text: infoHTML,
                    html: true,
                    type: "info"
                });
            } else {
                toastr.error('Failed to load cache info', '', { timeOut: 3000 });
            }
        },
        error: function() {
            toastr.error('Error loading cache info', '', { timeOut: 3000 });
        }
    });
}
</script>
