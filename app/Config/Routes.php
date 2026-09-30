<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->setDefaultController('LandingPages');
$routes->set404Override('App\Controllers\Home::notfound');
$routes->setTranslateURIDashes(false);

// ------------------------------------------------------------
// Static files
// ------------------------------------------------------------
$routes->get('favicon.ico',    function() { return ''; });
$routes->get('resource/(:any)', function() { return ''; });
$routes->get('assets/(:any)',   function() { return ''; });
$routes->get('home/ping',      'Home::ping');

// ------------------------------------------------------------
// Homepage
// ------------------------------------------------------------
$routes->get('/', 'LandingPages::index');

// ------------------------------------------------------------
// Error page
// ------------------------------------------------------------
$routes->get('error-404', 'Home::notfound');

// ------------------------------------------------------------
// Service worker
// ------------------------------------------------------------
$routes->get('sw.js', 'Home::stawain');

// ------------------------------------------------------------
// Admin — auth
// ------------------------------------------------------------
$routes->get('app-admin/login',  'Home::doLogin');
$routes->post('app-admin/login', 'Home::doLogin');
$routes->get('app-admin/logout', 'Home::doLogout');
$routes->get('app-admin',        'Home::fwIndex');

// ------------------------------------------------------------
// Admin — dashboard + AJAX
// ------------------------------------------------------------
$routes->get('app-admin/dashboard',          'AdminPanel::dashboard');
$routes->post('app-admin/dashboardStates',   'Home::dashboardStates');
$routes->post('app-admin/loadPostAutomationTable', 'AdminPanel::loadPostAutomationTable');

// ------------------------------------------------------------
// Admin — AJAX actions (POST only)
// ------------------------------------------------------------
$routes->post('app-admin/savePage',             'AdminPanel::savePage');
$routes->post('app-admin/deletePage',           'AdminPanel::deletePage');
$routes->post('app-admin/savePost',             'AdminPanel::savePost');
$routes->post('app-admin/deletePost',           'AdminPanel::deletePost');
$routes->post('app-admin/saveBlogPost',         'AdminPanel::saveBlogPost');
$routes->post('app-admin/deleteBlogPost',       'AdminPanel::deleteBlogPost');
$routes->post('app-admin/approveComment',       'AdminPanel::approveComment');
$routes->post('app-admin/postComment',          'AdminPanel::postComment');
$routes->post('app-admin/deleteComment',        'AdminPanel::deleteComment');
$routes->post('app-admin/deleteContactUS',      'AdminPanel::deleteContactUS');
$routes->post('app-admin/saveAutoPost',         'AdminPanel::saveAutoPost');
$routes->post('app-admin/saveDownloadableLink', 'AdminPanel::saveDownloadableLink');
$routes->post('app-admin/deleteAutoPost',       'AdminPanel::deleteAutoPost');
$routes->post('app-admin/updateAutoPost',       'AdminPanel::updateAutoPost');
$routes->post('app-admin/refreshFailedPost',    'AdminPanel::refreshFailedPost');
$routes->post('app-admin/saveCountry',          'AdminPanel::saveCountry');
$routes->post('app-admin/deleteCountry',        'AdminPanel::deleteCountry');
$routes->post('app-admin/saveCSC',              'AdminPanel::saveCSC');
$routes->post('app-admin/deleteCSC',            'AdminPanel::deleteCSC');
$routes->get('app-admin/getPosts',              'AdminPanel::getPosts');

// ------------------------------------------------------------
// Admin — cache management (Admin only)
// ------------------------------------------------------------
$routes->post('app-admin/clearCache',           'AdminPanel::clearCache');
$routes->get('app-admin/getCacheInfo',          'AdminPanel::getCacheInfo');

// ------------------------------------------------------------
// Admin — settings
// ------------------------------------------------------------
$routes->get('app-admin/settings/web',         'AdminPanel::web');
$routes->post('app-admin/settings/web',        'AdminPanel::web');
$routes->get('app-admin/settings/ads',         'AdminPanel::adsSetting');
$routes->post('app-admin/settings/ads',        'AdminPanel::adsSetting');
$routes->get('app-admin/settings/analytics',   'AdminPanel::analyticsSetting');
$routes->post('app-admin/settings/analytics',  'AdminPanel::analyticsSetting');
$routes->get('app-admin/settings/automation',  'AdminPanel::postAutomation');
$routes->post('app-admin/settings/automation', 'AdminPanel::postAutomation');

// ------------------------------------------------------------
// Admin — countries & CSC
// ------------------------------------------------------------
$routes->get('app-admin/settings/countries',   'AdminPanel::countriesList');
$routes->post('app-admin/settings/countries',  'AdminPanel::countriesList');
$routes->get('app-admin/settings/csc',         'AdminPanel::cscList');
$routes->post('app-admin/settings/csc',        'AdminPanel::cscList');

// ------------------------------------------------------------
// Admin — profile
// ------------------------------------------------------------
$routes->get('app-admin/profile',  'AdminPanel::profile');
$routes->post('app-admin/profile', 'AdminPanel::profile');

// ------------------------------------------------------------
// Admin — CMS pages
// ------------------------------------------------------------
$routes->get('app-admin/cms',             'AdminPanel::viewCMS');
$routes->get('app-admin/cms/add',         'AdminPanel::addEditCMS');
$routes->get('app-admin/cms/edit/(:num)', 'AdminPanel::addEditCMS/$1');

// ------------------------------------------------------------
// Admin — firmware posts
// ------------------------------------------------------------
$routes->get('app-admin/posts',              'AdminPanel::viewPosts');
$routes->post('app-admin/posts',             'AdminPanel::viewPosts');
$routes->get('app-admin/posts/add',          'AdminPanel::addEditPost');
$routes->get('app-admin/posts/edit/(:num)',  'AdminPanel::addEditPost/$1');
$routes->get('app-admin/posts/failed',       'AdminPanel::viewFailePendingPosts');
$routes->get('app-admin/posts/pending',      'AdminPanel::viewFailePendingPosts');
$routes->get('app-admin/posts/views/(:num)', 'AdminPanel::viewPostsViews/$1');

// ------------------------------------------------------------
// Admin — blog posts
// ------------------------------------------------------------
$routes->get('app-admin/blog',             'AdminPanel::viewBlogPosts');
$routes->post('app-admin/blog',            'AdminPanel::viewBlogPosts');
$routes->get('app-admin/blog/add',         'AdminPanel::addEditBlogPost');
$routes->get('app-admin/blog/edit/(:num)', 'AdminPanel::addEditBlogPost/$1');

// ------------------------------------------------------------
// Admin — comments
// ------------------------------------------------------------
$routes->get('app-admin/comments',        'AdminPanel::comments');
$routes->post('app-admin/comments',       'AdminPanel::comments');
$routes->get('app-admin/comments/(:num)', 'AdminPanel::viewSingleComment/$1');

// ------------------------------------------------------------
// Admin — contact us
// ------------------------------------------------------------
$routes->get('app-admin/contact-us',        'AdminPanel::viewContactUS');
$routes->get('app-admin/contact-us/(:num)', 'AdminPanel::viewSingleContactUS/$1');

// ------------------------------------------------------------
// Admin — IMEI Data
// ------------------------------------------------------------
$routes->get('app-admin/imei',  'AdminPanel::imeiReport');
$routes->post('app-admin/imei', 'AdminPanel::imeiReport');

// ------------------------------------------------------------
// Sitemaps
// ------------------------------------------------------------
$routes->get('sitemaps',         'Home::sitemap');
$routes->get('sitemaps.xml',     'Home::sitemap');
$routes->get('sitemaps/cms',     'Home::sitemapCMS');
$routes->get('sitemaps/models',  'Home::sitemapModels');
$routes->get('sitemaps/csc',     'Home::sitemapCSC');
$routes->get('sitemaps/blog',    'Home::sitemapBlog');
$routes->get('sitemaps/firmware(:num)', 'Home::sitemapFirmware/$1');
// Legacy support for old sitemap URLs
$routes->get('sitemaps/sitemappage(:num)', 'Home::sitemapFirmware/$1');
// Sitemap admin tools
$routes->get('sitemaps/refresh',         'Home::refreshSitemaps');
$routes->get('sitemaps/ping',            'Home::pingSitemaps');

// ------------------------------------------------------------
// RSS Feed (Critical for Bing & Yandex)
// ------------------------------------------------------------
$routes->get('feed',     'Home::rssFeed');
$routes->get('rss',      'Home::rssFeed');
$routes->get('feed.xml', 'Home::rssFeed');
$routes->get('rss.xml',  'Home::rssFeed');

// ------------------------------------------------------------
// Public — search / utility
// ------------------------------------------------------------
$routes->get('load/model', 'LandingPages::postSearch');
$routes->get('speedtest',  'LandingPages::speedtest');

// ------------------------------------------------------------
// Public — downloads
// Public — downloads
$routes->get('download-now',    'LandingPages::downloadNow');
$routes->post('download-now',   'LandingPages::downloadNow');
$routes->get('home/downloadnow', 'Home::downloadnow');
$routes->post('home/downloadnow', 'Home::downloadnow');
$routes->get('download/(:any)', 'LandingPages::downloadLink/$1');

// ------------------------------------------------------------
// Public — firmware
// ------------------------------------------------------------
$routes->get('firmware/ajax-filter',                   'LandingPages::ajaxFilter'); // ✅ SEO Phase 2: AJAX filtering (model pages)
$routes->get('firmware/ajax-filter-home',              'LandingPages::ajaxFilterHome'); // ✅ SEO Phase 2: AJAX filtering (home)
// Note: /firmware route removed - homepage is the main firmware page
$routes->get('firmware/(:any)/(:any)/(:any)/(:any)',  'LandingPages::viewPost/$1/$2/$3/$4');
$routes->get('firmware/(:any)/(:any)/(:any)',          'LandingPages::viewPost/$1/$2/$3');
$routes->get('firmware/(:any)/(:any)',                 'LandingPages::firmwarePosts/$1/$2');
$routes->get('firmware/(:any)',                        'LandingPages::firmwarePosts/$1');

// ------------------------------------------------------------
// Public — categories
// ------------------------------------------------------------
$routes->get('category/(:any)', 'LandingPages::categoryPosts/$1');

// ------------------------------------------------------------
// Public — blog
// ------------------------------------------------------------
$routes->get('blog',                 'LandingPages::viewBlogs');
$routes->get('blog/category/(:any)', 'LandingPages::viewBlogs/$1');
$routes->get('blog/(:any)',          'LandingPages::viewSingleBlogs/$1');

// ------------------------------------------------------------
// Public — contact us
// ------------------------------------------------------------
$routes->get('contact-us',  'LandingPages::contactUs');
$routes->post('contact-us', 'LandingPages::contactUs');

// ------------------------------------------------------------
// Firmware Scraper (NEW)
// ------------------------------------------------------------
$routes->get('firmware-scraper/test',            'FirmwareScraper::test');
$routes->get('firmware-scraper/test-odinrom',    'FirmwareScraper::testOdinrom');
$routes->get('firmware-scraper/scrape',          'FirmwareScraper::scrape');
$routes->get('firmware-scraper/logs',            'FirmwareScraper::logs');
$routes->get('firmware-scraper/clear-cache',     'FirmwareScraper::clearCache');
$routes->get('firmware-scraper/update-existing', 'FirmwareScraper::updateExisting');
$routes->get('firmware-scraper',                 'FirmwareScraper::scrape');

// ------------------------------------------------------------
// Firmware Scheduler (NEW)
// ------------------------------------------------------------
$routes->get('firmware-scheduler/publish',           'FirmwareScheduler::publish');
$routes->get('firmware-scheduler/auto-schedule',     'FirmwareScheduler::autoSchedule');
$routes->get('firmware-scheduler/dashboard',         'FirmwareScheduler::dashboard');
$routes->get('firmware-scheduler/fix-data-accuracy', 'FirmwareScheduler::fixDataAccuracy');
$routes->get('firmware-scheduler/auto-schedule-ajax', 'FirmwareScheduler::autoScheduleAjax');
$routes->get('firmware-scheduler/publish-ajax',      'FirmwareScheduler::publishAjax');
$routes->post('firmware-scheduler/save-settings',    'FirmwareScheduler::saveSettings');
$routes->get('firmware-scheduler/get-post/(:num)',   'FirmwareScheduler::getPost/$1');
$routes->post('firmware-scheduler/update-schedule',  'FirmwareScheduler::updateSchedule');
$routes->post('firmware-scheduler/publish-next',     'FirmwareScheduler::publishNext');
$routes->get('firmware-scheduler/rerandomize',       'FirmwareScheduler::rerandomize');
$routes->post('firmware-scheduler/bulk-prioritize',  'FirmwareScheduler::bulkPrioritize');
$routes->post('firmware-scheduler/bulk-action',      'FirmwareScheduler::bulkAction');
$routes->get('firmware-scheduler',                   'FirmwareScheduler::dashboard');

// ------------------------------------------------------------
// One UI Scraper Test (NEW)
// ------------------------------------------------------------
$routes->get('oneui-scraper-test/test/(:any)',    'OneUIScraperTest::test/$1');
$routes->get('oneui-scraper-test/test-batch',     'OneUIScraperTest::testBatch');
$routes->get('oneui-scraper-test',                'OneUIScraperTest::index');

// ------------------------------------------------------------
// IMEI Check Tool
// ------------------------------------------------------------
$routes->match(['get', 'post'], 'imei', 'LandingPages::imeiCheck');

// ------------------------------------------------------------
// Catch-all (MUST be last)
// ------------------------------------------------------------
$routes->get('(:any)/(:any)/(:any)', 'LandingPages::viewPost/$1/$2/$3');
$routes->get('(:any)',               'LandingPages::viewPage/$1');