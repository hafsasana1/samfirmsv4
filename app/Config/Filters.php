<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>>
     *
     * [filter_name => classname]
     * or [filter_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        'ratelimit'     => \App\Filters\RateLimiting::class,
        'security'      => \App\Filters\SecurityHeaders::class,
    ];

    /**
     * List of special required filters.
     *
     * The filters listed here are special. They are applied before and after
     * other kinds of filters, and always applied even if a route does not exist.
     *
     * Filters set by default provide framework functionality. If removed,
     * those functions will no longer work.
     *
     * @see https://codeigniter.com/user_guide/incoming/filters.html#provided-filters
     *
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            'forcehttps', // Force Global Secure Requests
            'pagecache',  // Web Page Caching
        ],
        'after' => [
            'pagecache',   // Web Page Caching
            'performance', // Performance Metrics
            'toolbar',     // Debug Toolbar
        ],
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array{
     *     before: array<string, array{except: list<string>|string}>|list<string>,
     *     after: array<string, array{except: list<string>|string}>|list<string>
     * }
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            // ✅ SECURITY: CSRF disabled globally - will be enabled per route in $filters
            // 'csrf',
            'invalidchars',
        ],
        'after' => [
            // 'honeypot',
            'secureheaders',
            // ✅ ENABLED: Custom SecurityHeaders with ad-friendly CSP
            'security',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'POST' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [
        // ✅ SECURITY: Apply CSRF protection to admin panel (except cache management)
        'csrf' => [
            'before' => [
                'app-admin/login',
                'app-admin/logout',
                'app-admin/dashboard',
                'app-admin/dashboardStates',
                'app-admin/loadPostAutomationTable',
                'app-admin/savePage',
                'app-admin/deletePage',
                'app-admin/savePost',
                'app-admin/deletePost',
                //'app-admin/saveBlogPost',
                'app-admin/deleteBlogPost',
                'app-admin/approveComment',
                'app-admin/postComment',
                'app-admin/deleteComment',
                'app-admin/deleteContactUS',
                'app-admin/saveAutoPost',
                'app-admin/saveDownloadableLink',
                'app-admin/deleteAutoPost',
                'app-admin/updateAutoPost',
                'app-admin/refreshFailedPost',
                'app-admin/saveCountry',
                'app-admin/deleteCountry',
                'app-admin/saveCSC',
                'app-admin/deleteCSC',
                'app-admin/settings/*',
                'app-admin/profile',
                'app-admin/cms',
                'app-admin/cms/*',
                'app-admin/posts',
                'app-admin/posts/*',
                'app-admin/blog',
                'app-admin/blog/*',
                'app-admin/comments',
                'app-admin/comments/*',
                'app-admin/contact-us',
                'app-admin/contact-us/*',
            ]
        ],
        
        // ✅ SECURITY: Apply rate limiting to login attempts
        'ratelimit' => ['before' => ['app-admin/login']],
    ];
}
