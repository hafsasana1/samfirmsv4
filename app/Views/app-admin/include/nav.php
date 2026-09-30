<?php
$request_obj = service('request');
$pagesArr = ['', 'dashboard', 'profile', 'settings', 'cms', 'posts', 'blog', 'comments', 'contact-us', 'imei'];

$uri_obj   = $request_obj->getUri();
$totalSegs = $uri_obj->getTotalSegments();
$nav       = ($totalSegs >= 2) ? trim($uri_obj->getSegment(2)) : '';
$page      = ($totalSegs >= 3) ? trim($uri_obj->getSegment(3)) : '';

// Special handling for firmware-scheduler route
$currentUrl = current_url();
$scheduler = '';
if (strpos($currentUrl, 'firmware-scheduler') !== false) {
    $scheduler = 'active';
    $nav = 'scheduler'; // Set nav but don't validate against pagesArr
}

if ($nav == '') {
    $nav  = 'dashboard';
    $page = 'dashboard';
}
if ($page == '') {
    $page = $nav;
}

// Only validate if not scheduler
if ($nav !== 'scheduler' && !in_array($nav, $pagesArr)) {
    echo '<script>window.location.href="' . base_url('error-404') . '";</script>';
    exit;
}
if ($nav == 'contact-us') {
    $nav = 'contact';
}
if ($nav == 'imei') {
    $imei = 'active';
} else {
    $imei = '';
}

// Variables for active states
$dashboard = ''; $profile  = ''; $settings  = ''; $cms      = '';
$posts     = ''; $blog     = ''; $comments  = ''; $contact  = '';
$web       = ''; $ads      = ''; $analytics = ''; $automation = '';
$countries = ''; $csc      = '';

switch ($nav) {
    case 'dashboard': $dashboard = 'active'; break;
    case 'profile':   $profile   = 'active'; break;
    case 'settings':  $settings  = 'active'; break;
    case 'cms':       $cms       = 'active'; break;
    case 'posts':     $posts     = 'active'; break;
    case 'blog':      $blog      = 'active'; break;
    case 'comments':  $comments  = 'active'; break;
    case 'contact':   $contact   = 'active'; break;
    // scheduler is already set above
}

$settingsOpen = '';
switch ($page) {
    case 'web':       $web       = 'active'; $settingsOpen = 'open'; break;
    case 'ads':       $ads       = 'active'; $settingsOpen = 'open'; break;
    case 'analytics': $analytics = 'active'; $settingsOpen = 'open'; break;
    case 'automation':$automation= 'active'; $settingsOpen = 'open'; break;
    case 'countries': $countries = 'active'; $settingsOpen = 'open'; break;
    case 'csc':       $csc       = 'active'; $settingsOpen = 'open'; break;
}

if (!isLoggedIn()) {
    echo '<script>window.location.href="' . base_url(ADMIN_PATH) . '";</script>';
    exit;
}
?>
<!-- Desktop Sidebar -->
<aside x-show="sidebarOpen"
       x-transition:enter="transition-transform ease-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition-transform ease-in duration-300"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="hidden lg:block fixed left-0 top-16 bottom-0 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 overflow-y-auto z-40">
    <nav class="p-4 space-y-1">
        <!-- Dashboard -->
        <a href="<?= base_url(ADMIN_PATH.'/dashboard') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $dashboard == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100' ?>">
            <i class="fas fa-home text-lg w-5"></i>
            <span class="font-medium text-sm">Dashboard</span>
        </a>

        <!-- Profile -->
        <a href="<?= base_url(ADMIN_PATH.'/profile') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $profile == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100' ?>">
            <i class="fas fa-user text-lg w-5"></i>
            <span class="font-medium text-sm">Profile</span>
        </a>

        <!-- Settings (Collapsible) -->
        <div x-data="{ settingsOpen: <?= $settingsOpen == 'open' ? 'true' : 'false' ?> }">
            <button @click="settingsOpen = !settingsOpen" 
                    class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-lg transition-all <?= $settings == 'active' ? 'bg-gray-100 text-accent' : 'text-gray-700 hover:bg-gray-100' ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-cog text-lg w-5"></i>
                    <span class="font-medium text-sm">Settings</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="settingsOpen ? 'rotate-180' : ''"></i>
            </button>
            
            <!-- Settings Submenu -->
            <div x-show="settingsOpen" 
                 x-collapse
                 class="ml-9 mt-1 space-y-0.5">
                <a href="<?= base_url(ADMIN_PATH.'/settings/web') ?>" 
                   class="block px-4 py-2 rounded-lg text-sm transition-all <?= $web == 'active' ? 'bg-accent text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-accent' ?>">
                    Web Portal
                </a>
                <a href="<?= base_url(ADMIN_PATH.'/settings/ads') ?>" 
                   class="block px-4 py-2 rounded-lg text-sm transition-all <?= $ads == 'active' ? 'bg-accent text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-accent' ?>">
                    Ads Setting
                </a>
                <a href="<?= base_url(ADMIN_PATH.'/settings/analytics') ?>" 
                   class="block px-4 py-2 rounded-lg text-sm transition-all <?= $analytics == 'active' ? 'bg-accent text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-accent' ?>">
                    Analytics Setting
                </a>
                <a href="<?= base_url(ADMIN_PATH.'/settings/automation') ?>" 
                   class="block px-4 py-2 rounded-lg text-sm transition-all <?= $automation == 'active' ? 'bg-accent text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-accent' ?>">
                    Post Automation
                </a>
                <a href="<?= base_url(ADMIN_PATH.'/settings/countries') ?>" 
                   class="block px-4 py-2 rounded-lg text-sm transition-all <?= $countries == 'active' ? 'bg-accent text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-accent' ?>">
                    Country List
                </a>
                <a href="<?= base_url(ADMIN_PATH.'/settings/csc') ?>" 
                   class="block px-4 py-2 rounded-lg text-sm transition-all <?= $csc == 'active' ? 'bg-accent text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-accent' ?>">
                    CSC List
                </a>
            </div>
        </div>

        <!-- CMS -->
        <a href="<?= base_url(ADMIN_PATH.'/cms') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $cms == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100' ?>">
            <i class="fas fa-tasks text-lg w-5"></i>
            <span class="font-medium text-sm">CMS</span>
        </a>

        <!-- Post -->
        <a href="<?= base_url(ADMIN_PATH.'/posts') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $posts == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100' ?>">
            <i class="fas fa-sticky-note text-lg w-5"></i>
            <span class="font-medium text-sm">Post</span>
        </a>

        <!-- Blog -->
        <a href="<?= base_url(ADMIN_PATH.'/blog') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $blog == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100' ?>">
            <i class="fas fa-blog text-lg w-5"></i>
            <span class="font-medium text-sm">Blog</span>
        </a>

        <!-- Comments -->
        <a href="<?= base_url(ADMIN_PATH.'/comments') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $comments == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100' ?>">
            <i class="fas fa-comment text-lg w-5"></i>
            <span class="font-medium text-sm">Comments</span>
        </a>

        <!-- Scheduler (NEW) -->
        <a href="<?= base_url('firmware-scheduler/dashboard') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $scheduler == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100' ?>">
            <i class="fas fa-clock text-lg w-5"></i>
            <span class="font-medium text-sm">Scheduler</span>
            <span class="ml-auto px-2 py-0.5 text-xs font-semibold bg-purple-100 text-purple-600 rounded-full">NEW</span>
        </a>

        <!-- Contact US -->
        <a href="<?= base_url(ADMIN_PATH.'/contact-us') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $contact == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100' ?>">
            <i class="fas fa-envelope text-lg w-5"></i>
            <span class="font-medium text-sm">Contact US</span>
        </a>

        <!-- IMEI Data -->
        <a href="<?= base_url(ADMIN_PATH.'/imei') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $imei == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100' ?>">
            <i class="fas fa-mobile-alt text-lg w-5"></i>
            <span class="font-medium text-sm">IMEI Data</span>
        </a>

        <!-- Logout -->
        <a href="<?= base_url(ADMIN_PATH.'/logout') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all text-red-600 hover:bg-red-50">
            <i class="fas fa-sign-out-alt text-lg w-5"></i>
            <span class="font-medium text-sm">Logout</span>
        </a>
    </nav>
</aside>

<!-- Mobile Sidebar Overlay -->
<div x-show="mobileMenuOpen" 
     @click="mobileMenuOpen = false"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40">
</div>

<!-- Mobile Sidebar -->
<aside x-show="mobileMenuOpen" 
       @click.away="mobileMenuOpen = false"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-300"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="lg:hidden fixed left-0 top-0 bottom-0 w-64 bg-white dark:bg-gray-900 shadow-lg overflow-y-auto z-50">
    <!-- Mobile Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <span class="text-lg font-semibold text-gray-900 dark:text-white">Menu</span>
        <button @click="mobileMenuOpen = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
            <i class="fas fa-times text-gray-600 dark:text-gray-300"></i>
        </button>
    </div>
    <!-- Mobile Navigation (duplicate of desktop for mobile) -->
    <nav class="p-4 space-y-1">
        <!-- Dashboard -->
        <a href="<?= base_url(ADMIN_PATH.'/dashboard') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $dashboard == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' ?>">
            <i class="fas fa-home text-lg w-5"></i>
            <span class="font-medium text-sm">Dashboard</span>
        </a>

        <!-- Profile -->
        <a href="<?= base_url(ADMIN_PATH.'/profile') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $profile == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' ?>">
            <i class="fas fa-user text-lg w-5"></i>
            <span class="font-medium text-sm">Profile</span>
        </a>

        <!-- Settings (Collapsible) -->
        <div x-data="{ settingsOpen: <?= $settingsOpen == 'open' ? 'true' : 'false' ?> }">
            <button @click="settingsOpen = !settingsOpen" 
                    class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-lg transition-all <?= $settings == 'active' ? 'bg-gray-100 dark:bg-gray-800 text-accent' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-cog text-lg w-5"></i>
                    <span class="font-medium text-sm">Settings</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="settingsOpen ? 'rotate-180' : ''"></i>
            </button>
            
            <!-- Settings Submenu -->
            <div x-show="settingsOpen" 
                 x-collapse
                 class="ml-9 mt-1 space-y-0.5">
                <a href="<?= base_url(ADMIN_PATH.'/settings/web') ?>" 
                   class="block px-4 py-2 rounded-lg text-sm transition-all <?= $web == 'active' ? 'bg-accent text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-accent' ?>">
                    Web Portal
                </a>
                <a href="<?= base_url(ADMIN_PATH.'/settings/ads') ?>" 
                   class="block px-4 py-2 rounded-lg text-sm transition-all <?= $ads == 'active' ? 'bg-accent text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-accent' ?>">
                    Ads Setting
                </a>
                <a href="<?= base_url(ADMIN_PATH.'/settings/analytics') ?>" 
                   class="block px-4 py-2 rounded-lg text-sm transition-all <?= $analytics == 'active' ? 'bg-accent text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-accent' ?>">
                    Analytics Setting
                </a>
                <a href="<?= base_url(ADMIN_PATH.'/settings/automation') ?>" 
                   class="block px-4 py-2 rounded-lg text-sm transition-all <?= $automation == 'active' ? 'bg-accent text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-accent' ?>">
                    Post Automation
                </a>
                <a href="<?= base_url(ADMIN_PATH.'/settings/countries') ?>" 
                   class="block px-4 py-2 rounded-lg text-sm transition-all <?= $countries == 'active' ? 'bg-accent text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-accent' ?>">
                    Country List
                </a>
                <a href="<?= base_url(ADMIN_PATH.'/settings/csc') ?>" 
                   class="block px-4 py-2 rounded-lg text-sm transition-all <?= $csc == 'active' ? 'bg-accent text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-accent' ?>">
                    CSC List
                </a>
            </div>
        </div>

        <!-- CMS -->
        <a href="<?= base_url(ADMIN_PATH.'/cms') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $cms == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' ?>">
            <i class="fas fa-tasks text-lg w-5"></i>
            <span class="font-medium text-sm">CMS</span>
        </a>

        <!-- Post -->
        <a href="<?= base_url(ADMIN_PATH.'/posts') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $posts == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' ?>">
            <i class="fas fa-sticky-note text-lg w-5"></i>
            <span class="font-medium text-sm">Post</span>
        </a>

        <!-- Blog -->
        <a href="<?= base_url(ADMIN_PATH.'/blog') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $blog == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' ?>">
            <i class="fas fa-blog text-lg w-5"></i>
            <span class="font-medium text-sm">Blog</span>
        </a>

        <!-- Comments -->
        <a href="<?= base_url(ADMIN_PATH.'/comments') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $comments == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' ?>">
            <i class="fas fa-comment text-lg w-5"></i>
            <span class="font-medium text-sm">Comments</span>
        </a>

        <!-- Scheduler (NEW) -->
        <a href="<?= base_url('firmware-scheduler/dashboard') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $scheduler == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' ?>">
            <i class="fas fa-clock text-lg w-5"></i>
            <span class="font-medium text-sm">Scheduler</span>
            <span class="ml-auto px-2 py-0.5 text-xs font-semibold bg-purple-100 text-purple-600 rounded-full">NEW</span>
        </a>

        <!-- Contact US -->
        <a href="<?= base_url(ADMIN_PATH.'/contact-us') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $contact == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' ?>">
            <i class="fas fa-envelope text-lg w-5"></i>
            <span class="font-medium text-sm">Contact US</span>
        </a>

        <!-- IMEI Data -->
        <a href="<?= base_url(ADMIN_PATH.'/imei') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all <?= $imei == 'active' ? 'bg-accent text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' ?>">
            <i class="fas fa-mobile-alt text-lg w-5"></i>
            <span class="font-medium text-sm">IMEI Data</span>
        </a>

        <!-- Logout -->
        <a href="<?= base_url(ADMIN_PATH.'/logout') ?>" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
            <i class="fas fa-sign-out-alt text-lg w-5"></i>
            <span class="font-medium text-sm">Logout</span>
        </a>
    </nav>
</aside>

<!-- Main Content Wrapper -->
<div class="min-h-screen pt-16 bg-gray-50 dark:bg-gray-900 transition-all duration-300 ease-in-out"
     :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'">
    <div class="p-4 lg:p-6">
        <div class="max-w-7xl mx-auto">
