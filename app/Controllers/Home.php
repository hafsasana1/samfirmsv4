<?php

namespace App\Controllers;

use App\Models\HomeModel;
use App\Libraries\InputSanitizer;

class Home extends BaseController
{
    protected object|null $app;
    protected HomeModel $homeModel;
    protected $db;

    public function __construct()
    {
        helper(['url', 'cookie', 'global_function', 'query_cache']);
        $this->app       = session()->get('fw');
        $this->homeModel = new HomeModel();
        $this->db        = \Config\Database::connect();
    }

    public function ptest(): string
    {
        echo '<pre>getMappingRoute removed — CI4 incompatible</pre>';
        return '';
    }

    public function fwIndex(): \CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('fw') || isLoggedIn()) {
            return redirect()->to(base_url(ADMIN_PATH . '/dashboard'));
        }
        return redirect()->to(base_url(ADMIN_PATH . '/login'));
    }

    public function doLogin(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (isLoggedIn()) {
            return redirect()->to(base_url(ADMIN_PATH . '/dashboard'));
        }

        // CI3 mein isset($_POST['submit']) tha — same logic
        if ($this->request->getMethod() === 'POST' && $this->request->getPost('submit')) {

            $username = trim($this->request->getPost('username'));
            $password = trim($this->request->getPost('password'));
            $sanswer  = trim($this->request->getPost('sanswer'));
            $ipAddress = $this->request->getIPAddress();

            // Bug 1 fix: compare against session-stored math answer, not date()
            $correctAnswer = session()->get('seq-ans');
            session()->remove('seq-ans');
            if ($correctAnswer === null || (int)$sanswer !== (int)$correctAnswer) {
                // ✅ SECURITY: Record failed attempt (security question failed)
                \App\Filters\RateLimiting::recordAttempt($ipAddress, $username, false);
                
                session()->set('error', 'Security answer is not valid');
                return view(ADMIN_PATH . '/login-page');
            }

            $record = $this->homeModel->doLogin($username, $password);

            if ($record !== '0') {
                // ✅ SECURITY: Record successful login
                \App\Filters\RateLimiting::recordAttempt($ipAddress, $username, true);
                
                // ✅ SECURITY: Clear failed attempts for this IP
                \App\Filters\RateLimiting::clearAttempts($ipAddress);
                
                // ✅ SECURITY FIX 1.4: Remove password from session storage
                // Don't store the password hash in session files (security risk)
                unset($record->password);
                session()->set('fw', $record);

                // ✅ REMEMBER ME FEATURE: Only set cookies if user checked the box
                $rememberMe = $this->request->getPost('remember_me');
                
                if ($rememberMe === '1') {
                    // ✅ SECURITY FIX 1.6: Use secure random token for remember-me instead of MD5
                    $tokenRaw = bin2hex(random_bytes(32));  // 64-char hex string
                    $tokenHash = hash_hmac('sha256', $tokenRaw, config('Encryption')->key);
                    
                    $this->db->table('fw_users')
                             ->where('userId', $record->userId)
                             ->update(['hashToken' => $tokenHash]);

                    // Bug 4 fix: chain cookies onto the redirect response so they are sent together
                    return redirect()->to(base_url(ADMIN_PATH . '/dashboard'))
                        ->setCookie('rememberme', $tokenRaw, 2592000)      // 30 days
                        ->setCookie('loginUsername', $record->username, 2592000);
                } else {
                    // User doesn't want to be remembered — clear any existing cookies
                    return redirect()->to(base_url(ADMIN_PATH . '/dashboard'))
                        ->deleteCookie('rememberme')
                        ->deleteCookie('loginUsername');
                }
            }

            // ✅ SECURITY: Record failed login attempt
            \App\Filters\RateLimiting::recordAttempt($ipAddress, $username, false);
            
            // ✅ SECURITY: Show remaining attempts
            $remainingAttempts = 5 - \App\Filters\RateLimiting::getAttemptCount($ipAddress);
            $errorMsg = 'Username / Password Invalid';
            if ($remainingAttempts > 0 && $remainingAttempts <= 3) {
                $errorMsg .= " ($remainingAttempts attempts remaining before lockout)";
            }
            
            session()->set('error', $errorMsg);
            return view(ADMIN_PATH . '/login-page');
        }

        // GET request — show login form
        return view(ADMIN_PATH . '/login-page');
    }

    public function doLogout(): \CodeIgniter\HTTP\RedirectResponse
    {
        delete_cookie('rememberme');
        session()->remove('fw');
        session()->destroy();
        return redirect()->to(base_url(ADMIN_PATH . '/login'));
    }

    public function notfound(): string
    {
        try {
            // Layer 1: Try to load custom styled 404 page
            return view('error-404');
        } catch (\Throwable $e) {
            // Layer 2: If custom view fails, load simple version
            try {
                return view('error-404-simple');
            } catch (\Throwable $e2) {
                // Layer 3: If all fails, return plain HTML fallback
                return $this->getFallback404();
            }
        }
    }

    /**
     * Ultimate fallback 404 page (plain HTML, no dependencies)
     * This ensures users NEVER see the CI4 "Whoops!" error page
     */
    private function getFallback404(): string
    {
        $baseUrl = base_url();
        $siteName = 'SamFirms';
        
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - {$siteName}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #1F2937 0%, #111827 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container { 
            text-align: center; 
            max-width: 600px;
            background: rgba(255,255,255,0.05);
            padding: 60px 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { 
            font-size: 120px; 
            font-weight: 700; 
            color: #FF6B35;
            margin-bottom: 20px;
            line-height: 1;
            text-shadow: 0 4px 20px rgba(255,107,53,0.3);
        }
        h2 { 
            font-size: 32px; 
            margin-bottom: 20px;
            color: #fff;
            font-weight: 600;
        }
        p { 
            font-size: 18px; 
            color: rgba(255,255,255,0.7);
            margin-bottom: 30px;
            line-height: 1.6;
        }
        a { 
            display: inline-block;
            background: #FF6B35;
            color: #fff;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(255,107,53,0.3);
        }
        a:hover { 
            background: #E65A28;
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(255,107,53,0.4);
        }
        @media (max-width: 600px) {
            h1 { font-size: 80px; }
            h2 { font-size: 24px; }
            p { font-size: 16px; }
            .container { padding: 40px 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>Sorry, the page you're looking for doesn't exist or has been moved.<br>Please check the URL or return to the homepage.</p>
        <a href="{$baseUrl}">Back to Homepage</a>
    </div>
</body>
</html>
HTML;
    }

    public function noRecordFound(): string
    {
        return view('page-notfound');
    }

    public function dashboardStates(): \CodeIgniter\HTTP\Response
    {
        $posts    = $this->db->table('fw_posts')->select('postId')->get()->getNumRows();
        $visitors = $this->db->table('fw_post_view')->select('viewId')->get()->getNumRows();

        $pendingFileUpload = $this->homeModel->viewPostPendingLink();
        $failedPost        = $this->homeModel->viewFailePendingPosts();

        $firmwares = $this->db->table('fw_posts')->select('postId')->groupBy('version')->get()->getNumRows();
        $models    = $this->db->table('fw_posts')->select('postId')->groupBy('model')->get()->getNumRows();
        $downloads = $this->db->table('fw_posts')->selectSum('downloadCount', 'dcoun')->get()->getRow()->dcoun ?? 0;

        $data = [
            'totalFirmware'     => number_format($posts),
            'visitors'          => number_format($visitors),
            'pendingUploads'    => number_format($pendingFileUpload),
            'firmwares'         => number_format($firmwares),
            'models'            => number_format($models),
            'downloads'         => number_format($downloads),
            'failedPost'        => number_format($failedPost),
        ];

        return $this->response->setContentType('application/json')
                              ->setBody(json_encode($data));
    }

    public function sitemap(): \CodeIgniter\HTTP\Response
    {
        // ⚡ PERFORMANCE: Cache sitemap for 1 hour (regenerates when new content added)
        $cache = \Config\Services::cache();
        $cacheKey = 'sitemap_master_v1';
        
        if ($cached = $cache->get($cacheKey)) {
            return $this->response->setContentType('application/xml')->setBody($cached);
        }
        
        $urls = [];
        
        // ✅ SEO OPTIMIZATION: Homepage - Priority 1.0 (Entry point, brand authority)
        $item = new \stdClass();
        $item->loc = base_url(); 
        $item->lastmod = date(DATE_ATOM, time());
        $item->changefreq = 'daily'; 
        $item->priority = '1.0';
        $urls[] = $item;
        
        // ✅ SEO BEST PRACTICE: Separated sitemaps for clear hierarchy
        // CMS pages (static content)
        $item = new \stdClass();
        $item->loc = base_url('sitemaps/cms');
        $urls[] = $item;
        
        // Model pages (browsing/navigation)
        $item = new \stdClass();
        $item->loc = base_url('sitemaps/models');
        $urls[] = $item;
        
        // CSC pages (filtering)
        $item = new \stdClass();
        $item->loc = base_url('sitemaps/csc');
        $urls[] = $item;
        
        // Blog posts
        $item = new \stdClass();
        $item->loc = base_url('sitemaps/blog');
        $urls[] = $item;

        // ✅ SECURITY FIX: Only include Active posts in sitemap, exclude Draft/Inactive
        // Performance: Optimized via database index on postStatus, postId
        $record = $this->db->table('fw_posts')
                           ->where('postStatus', 'Active')
                           ->orderBy('postId', 'asc')
                           ->get()
                           ->getResult();
        
        $gi = 1; $linkCount = 1;

        foreach ($record as $rec) {
            if ($rec->postStatus === 'Active') {
                $gi++;
                if ($gi == SITEMAP_LIMIT) {
                    $item = new \stdClass();
                    $item->loc = base_url('sitemaps/firmware' . $linkCount);
                    $urls[] = $item; $gi = 1; $linkCount++;
                }
            }
        }
        if ($gi != '1') { 
            $item = new \stdClass(); 
            $item->loc = base_url('sitemaps/firmware' . $linkCount); 
            $urls[] = $item; 
        }

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        foreach ($urls as $url) {
            $child = $xml->addChild('url');
            $child->addChild('loc', $url->loc);
            if (isset($url->lastmod))    $child->addChild('lastmod',    $url->lastmod);
            if (isset($url->changefreq)) $child->addChild('changefreq', $url->changefreq);
            if (isset($url->priority))   $child->addChild('priority',   number_format($url->priority, 2));
        }
        
        $xmlOutput = $xml->asXml();
        $cache->save($cacheKey, $xmlOutput, 3600); // Cache for 1 hour
        
        return $this->response->setContentType('application/xml')->setBody($xmlOutput);
    }

    public function sitemapBlog(): \CodeIgniter\HTTP\Response
    {
        // ⚡ PERFORMANCE: Cache sitemap for 1 hour
        $cache = \Config\Services::cache();
        $cacheKey = 'sitemap_blog_v2'; // v2: Added image support
        
        if ($cached = $cache->get($cacheKey)) {
            return $this->response->setContentType('application/xml')->setBody($cached);
        }
        
        $urls = [];
        
        // Performance: Optimized via database indexes
        $record = $this->db->table('fw_blogs')->orderBy('postId', 'asc')->get();
        
        if ($record->getNumRows() > 0) {
            foreach ($record->getResult() as $rec) {
                if ($rec->postStatus === 'Active') {
                    $item = new \stdClass();
                    $item->loc = base_url('blog/' . $rec->postSlug);
                    $item->lastmod = date(DATE_ATOM, strtotime($rec->modifiedTime) + rand(0, 100));
                    // ✅ SEO OPTIMIZATION: Blog posts - Priority 0.7 (Content marketing, supporting pages)
                    $item->changefreq = 'monthly'; 
                    $item->priority = '0.7';
                    
                    // 🖼️ IMAGE SEO: Extract first real image from content (skip base64 fallbacks)
                    $doc = new \DOMDocument();
                    @$doc->loadHTML($rec->postContent);
                    $tags = $doc->getElementsByTagName('img');
                    foreach ($tags as $tag) {
                        $imgSrc = $tag->getAttribute('src');
                        // Only include real images (URLs), skip base64 encoded fallbacks
                        if (!empty($imgSrc) && !str_starts_with($imgSrc, 'data:image')) {
                            $item->image = $imgSrc;
                            $item->imageTitle = $rec->postTitle;
                            // Create caption from content (first 100 chars, strip HTML)
                            $caption = strip_tags($rec->postContent);
                            $caption = preg_replace('/\s+/', ' ', $caption); // Remove extra whitespace
                            $item->imageCaption = mb_substr(trim($caption), 0, 100);
                            break; // Use only first image
                        }
                    }
                    
                    $urls[] = $item;
                }
            }
        }
        
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->addAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');
        $xml->addAttribute('xmlns:mobile', 'http://www.google.com/schemas/sitemap-mobile/1.0');
        
        foreach ($urls as $url) {
            $child = $xml->addChild('url'); 
            $child->addChild('loc', $url->loc);
            if (isset($url->lastmod))    $child->addChild('lastmod',    $url->lastmod);
            if (isset($url->changefreq)) $child->addChild('changefreq', $url->changefreq);
            if (isset($url->priority))   $child->addChild('priority',   number_format($url->priority, 2));
            
            // 📱 MOBILE SEO: Signal that page is mobile-optimized
            $child->addChild('mobile:mobile', '', 'http://www.google.com/schemas/sitemap-mobile/1.0');
            
            // 🖼️ IMAGE SEO: Add image tag if real image found (better Google Image Search)
            if (isset($url->image)) {
                $imageNode = $child->addChild('image:image', null, 'http://www.google.com/schemas/sitemap-image/1.1');
                $imageNode->addChild('image:loc', htmlspecialchars($url->image), 'http://www.google.com/schemas/sitemap-image/1.1');
                if (isset($url->imageTitle)) {
                    $imageNode->addChild('image:title', htmlspecialchars($url->imageTitle), 'http://www.google.com/schemas/sitemap-image/1.1');
                }
                if (isset($url->imageCaption)) {
                    $imageNode->addChild('image:caption', htmlspecialchars($url->imageCaption), 'http://www.google.com/schemas/sitemap-image/1.1');
                }
            }
        }
        
        $xmlOutput = $xml->asXml();
        $cache->save($cacheKey, $xmlOutput, 3600); // Cache for 1 hour
        
        return $this->response->setContentType('application/xml')->setBody($xmlOutput);
    }

    public function sitemapCMS(): \CodeIgniter\HTTP\Response
    {
        // ⚡ PERFORMANCE: Cache sitemap for 6 hours (static pages rarely change)
        $cache = \Config\Services::cache();
        $cacheKey = 'sitemap_cms_v1';
        
        if ($cached = $cache->get($cacheKey)) {
            return $this->response->setContentType('application/xml')->setBody($cached);
        }
        
        $urls = [];
        
        // ✅ SEO OPTIMIZATION: CMS/Static pages - Priority 0.6 (utility pages)
        $cmsPages = $this->db->table('fw_cms')
                             ->select('slugUrl, modifiedTime')
                             ->where('status', 'Active')
                             ->where('pageType', '1')
                             ->orderBy('pageId', 'asc')
                             ->get()
                             ->getResult();
        
        if (count($cmsPages) > 0) {
            foreach ($cmsPages as $page) {
                $item = new \stdClass();
                $item->loc = base_url($page->slugUrl);
                $item->lastmod = date(DATE_ATOM, strtotime($page->modifiedTime));
                $item->changefreq = 'yearly';
                $item->priority = '0.6';
                $urls[] = $item;
            }
        }
        
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        foreach ($urls as $url) {
            $child = $xml->addChild('url');
            $child->addChild('loc', $url->loc);
            if (isset($url->lastmod))    $child->addChild('lastmod',    $url->lastmod);
            if (isset($url->changefreq)) $child->addChild('changefreq', $url->changefreq);
            if (isset($url->priority))   $child->addChild('priority',   number_format($url->priority, 2));
        }
        
        $xmlOutput = $xml->asXml();
        $cache->save($cacheKey, $xmlOutput, 21600); // Cache for 6 hours
        
        return $this->response->setContentType('application/xml')->setBody($xmlOutput);
    }

    public function sitemapModels(): \CodeIgniter\HTTP\Response
    {
        // ⚡ PERFORMANCE: Cache sitemap for 2 hours
        $cache = \Config\Services::cache();
        $cacheKey = 'sitemap_models_v1';
        
        if ($cached = $cache->get($cacheKey)) {
            return $this->response->setContentType('application/xml')->setBody($cached);
        }
        
        $urls = [];
        
        // ✅ SEO OPTIMIZATION: Get all distinct models - Priority 0.85 (browsing pages)
        $models = $this->db->query("
            SELECT model, MAX(modifiedTime) as lastMod
            FROM fw_posts 
            WHERE postStatus = 'Active' 
            AND model IS NOT NULL 
            AND model != ''
            GROUP BY model
            ORDER BY model ASC
        ")->getResult();
        
        if (count($models) > 0) {
            foreach ($models as $modelData) {
                $item = new \stdClass();
                $item->loc = base_url('firmware/' . $modelData->model);
                $item->lastmod = date(DATE_ATOM, strtotime($modelData->lastMod));
                $item->changefreq = 'weekly';
                $item->priority = '0.85';
                $urls[] = $item;
            }
        }
        
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        foreach ($urls as $url) {
            $child = $xml->addChild('url');
            $child->addChild('loc', $url->loc);
            if (isset($url->lastmod))    $child->addChild('lastmod',    $url->lastmod);
            if (isset($url->changefreq)) $child->addChild('changefreq', $url->changefreq);
            if (isset($url->priority))   $child->addChild('priority',   number_format($url->priority, 2));
        }
        
        $xmlOutput = $xml->asXml();
        $cache->save($cacheKey, $xmlOutput, 7200); // Cache for 2 hours
        
        return $this->response->setContentType('application/xml')->setBody($xmlOutput);
    }

    public function sitemapCSC(): \CodeIgniter\HTTP\Response
    {
        // ⚡ PERFORMANCE: Cache sitemap for 2 hours
        $cache = \Config\Services::cache();
        $cacheKey = 'sitemap_csc_v1';
        
        if ($cached = $cache->get($cacheKey)) {
            return $this->response->setContentType('application/xml')->setBody($cached);
        }
        
        $urls = [];
        
        // ✅ SEO OPTIMIZATION: Get all CSC pages - Priority 0.80 (filtering pages)
        $cscs = $this->db->query("
            SELECT CONCAT(model, '/', csc) as urlPath, MAX(modifiedTime) as lastMod
            FROM fw_posts 
            WHERE postStatus = 'Active' 
            AND model IS NOT NULL 
            AND model != ''
            AND csc IS NOT NULL 
            AND csc != ''
            GROUP BY model, csc
            ORDER BY model ASC, csc ASC
        ")->getResult();
        
        if (count($cscs) > 0) {
            foreach ($cscs as $cscData) {
                $item = new \stdClass();
                $item->loc = base_url('firmware/' . $cscData->urlPath);
                $item->lastmod = date(DATE_ATOM, strtotime($cscData->lastMod));
                $item->changefreq = 'monthly';
                $item->priority = '0.80';
                $urls[] = $item;
            }
        }
        
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        foreach ($urls as $url) {
            $child = $xml->addChild('url');
            $child->addChild('loc', $url->loc);
            if (isset($url->lastmod))    $child->addChild('lastmod',    $url->lastmod);
            if (isset($url->changefreq)) $child->addChild('changefreq', $url->changefreq);
            if (isset($url->priority))   $child->addChild('priority',   number_format($url->priority, 2));
        }
        
        $xmlOutput = $xml->asXml();
        $cache->save($cacheKey, $xmlOutput, 7200); // Cache for 2 hours
        
        return $this->response->setContentType('application/xml')->setBody($xmlOutput);
    }

    public function sitemapFirmware(): \CodeIgniter\HTTP\Response
    {
        $urls = []; $limit = SITEMAP_LIMIT;
        $page = str_replace(['firmware', '.xml'], '', $this->request->getUri()->getSegment(2));
        $page = $page - 1; $pageRecord = $page * $limit;

        // ⚡ PERFORMANCE: Cache each firmware sitemap page for 1 hour
        $cache = \Config\Services::cache();
        $cacheKey = 'sitemap_firmware_page_' . ($page + 1) . '_v3'; // v3: Removed duplicate flag images
        
        if ($cached = $cache->get($cacheKey)) {
            return $this->response->setContentType('application/xml')->setBody($cached);
        }

        // ✅ SECURITY FIX: Only include Active posts in sitemap, exclude Draft/Inactive
        // Performance: Optimized via database indexes on postStatus, postId
        $record = $this->db->table('fw_posts')
                           ->where('postStatus', 'Active')
                           ->orderBy('postId', 'asc')
                           ->limit($limit, $pageRecord)
                           ->get();
        
        if ($record->getNumRows() > 0) {
            foreach ($record->getResult() as $rec) {
                if ($rec->postStatus === 'Active') {
                    $item = new \stdClass();
                    $item->loc = postUrl($rec);
                    $item->lastmod = date(DATE_ATOM, strtotime($rec->modifiedTime) + rand(0, 100));
                    // ✅ SEO OPTIMIZATION: Firmware posts - Priority 0.9 (Money pages - downloads happen here)
                    // changefreq = yearly because firmware files don't change after publishing
                    // New firmware discovered via lastmod date, not changefreq
                    $item->changefreq = 'yearly'; 
                    $item->priority = '0.9';
                    
                    // 🖼️ IMAGE SEO: Reserved for future unique device images
                    // Note: Flag images intentionally excluded to avoid duplicate content issues
                    // Only add unique, high-value images (device photos, screenshots, etc.)
                    
                    $urls[] = $item;
                }
            }
        }
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->addAttribute('xmlns:mobile', 'http://www.google.com/schemas/sitemap-mobile/1.0');
        // Note: xmlns:image namespace available for future use when unique images are added
        $alreadyIn = [];
        foreach ($urls as $url) {
            if (!in_array($url->loc, $alreadyIn)) {
                $child = $xml->addChild('url'); 
                $child->addChild('loc', $url->loc);
                if (isset($url->lastmod))    $child->addChild('lastmod',    $url->lastmod);
                if (isset($url->changefreq)) $child->addChild('changefreq', $url->changefreq);
                if (isset($url->priority))   $child->addChild('priority',   number_format($url->priority, 2));
                
                // 📱 MOBILE SEO: Signal that page is mobile-optimized
                $child->addChild('mobile:mobile', '', 'http://www.google.com/schemas/sitemap-mobile/1.0');
                
                // 🖼️ IMAGE SEO: Add unique images when available
                // Only include if image is unique to this specific firmware post
                if (isset($url->image) && isset($url->imageUnique) && $url->imageUnique === true) {
                    $imageNode = $child->addChild('image:image', null, 'http://www.google.com/schemas/sitemap-image/1.1');
                    $imageNode->addChild('image:loc', htmlspecialchars($url->image), 'http://www.google.com/schemas/sitemap-image/1.1');
                    if (isset($url->imageTitle)) {
                        $imageNode->addChild('image:title', htmlspecialchars($url->imageTitle), 'http://www.google.com/schemas/sitemap-image/1.1');
                    }
                }
                
                $alreadyIn[] = $url->loc;
            }
        }
        
        $xmlOutput = $xml->asXml();
        $cache->save($cacheKey, $xmlOutput, 3600); // Cache for 1 hour
        
        return $this->response->setContentType('application/xml')->setBody($xmlOutput);
    }

    public function contactUs(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');
        
        // ✅ SECURITY: Sanitize all inputs
        $name        = InputSanitizer::sanitizeString($this->request->getPost('name'));
        $email       = InputSanitizer::sanitizeEmail($this->request->getPost('email'));
        $website     = InputSanitizer::sanitizeUrl($this->request->getPost('website'));
        $description = InputSanitizer::sanitizeString($this->request->getPost('description'));
        $sanswer     = InputSanitizer::sanitizeString($this->request->getPost('sanswer'));
        $form_token  = base64_decode(trim($this->request->getPost('form_token')));
        
        $securityWords = ['CupCake', 'auToMobile', 'Samsung', 'Firmware', 'bOOt', 'MoBile'];
        
        // Validate required fields
        if (!InputSanitizer::validateRequired($name)) {
            exit('Please enter your name');
        }
        
        if (!InputSanitizer::validateEmail($email)) {
            exit('Please enter valid email address');
        }
        
        if (!InputSanitizer::validateRequired($description)) {
            exit('Please enter description');
        }
        
        if ($website !== '') {
            if (!InputSanitizer::validateUrl('https://' . $website)) {
                if (strpos($website, 'https://') === false && strpos($website, 'http://') === false) {
                    $website = 'https://' . $website;
                }
            }
            if (!InputSanitizer::validateUrl($website)) {
                exit('Please enter valid website address');
            }
        }
        
        if (!in_array($sanswer, $securityWords) || $form_token !== $sanswer) {
            exit('Security question answer is not valid');
        }
        
        $this->db->table('fw_contact_us')->insert([
            'fromName'    => $name,
            'fromEmail'   => $email,
            'website'     => $website,
            'description' => $description,
            'createdTime' => date('Y-m-d H:i:s'),
            'ipAddress'   => $this->request->getIPAddress(),
        ]);
        
        exit('success');
    }

    public function stawain(): void {}

    public function authDrive(): \CodeIgniter\HTTP\RedirectResponse|null
    {
        $filePath = RESOURCE_PATH . 'auth-resource.info';
        $client_id = '323365054618-1hudg6ejt8k33re2kbbaurvdvlc0udo1.apps.googleusercontent.com';
        $client_secret = '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';
        $redirect_uri = 'https://samfirms.com/home/authDrive';
        if ($this->request->getGet('code')) {
            if (!file_exists($filePath)) { $h = @fopen($filePath, 'w'); @fclose($h); }
            $oauth2 = ['client_id' => $client_id, 'client_secret' => $client_secret];
            try {
                $provider = new \League\OAuth2\Client\Provider\GenericProvider([
                    'clientId' => $client_id, 'clientSecret' => $client_secret, 'redirectUri' => $redirect_uri,
                    'urlAuthorize' => 'https://www.googleapis.com/oauth2/v4/authorize',
                    'urlAccessToken' => 'https://www.googleapis.com/oauth2/v4/token', 'urlResourceOwnerDetails' => '',
                ]);
                $accessToken = $provider->getAccessToken('authorization_code', ['code' => $this->request->getGet('code')]);
                $oauth2['hasError'] = 'false'; $oauth2['access_token'] = $accessToken->getToken();
                $oauth2['expires_in'] = $accessToken->getExpires(); $oauth2['timeIn'] = time();
                $oauth2['refresh_token'] = $accessToken->getRefreshToken();
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) { $oauth2['hasError'] = 'true'; }
            @file_put_contents($filePath, json_encode($oauth2));
            return null;
        } else {
            return redirect()->to('https://accounts.google.com/o/oauth2/v2/auth/oauthchooseaccount?redirect_uri=' . $redirect_uri . '&prompt=consent&response_type=code&client_id=' . $client_id . '&scope=https://www.googleapis.com/auth/drive&access_type=offline');
        }
    }

    public function ping(): void
    {
        $oauth2 = @json_decode(@file_get_contents(RESOURCE_PATH . 'auth-resource.info'), true);
        if (!$oauth2 || !isset($oauth2['expires_in']) || !isset($oauth2['timeIn'])) { echo 'false'; return; }
        $timeDiff = $oauth2['expires_in'] - $oauth2['timeIn'];
        $return = 'false';
        if (time() > ($oauth2['timeIn'] + $timeDiff)) { $this->refreshAuth(); $return = 'true'; }
        echo $return;
    }

    public function refreshAuth(): array
    {
        $filePath = RESOURCE_PATH . 'auth-resource.info';
        $oauth2 = @json_decode(@file_get_contents($filePath), true);
        if (!$oauth2) return [];
        try {
            $provider = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId' => $oauth2['client_id'] ?? '', 'clientSecret' => $oauth2['client_secret'] ?? '',
                'redirectUri' => 'https://samfirms.com/home/authDrive',
                'urlAuthorize' => 'https://www.googleapis.com/oauth2/v4/authorize',
                'urlAccessToken' => 'https://www.googleapis.com/oauth2/v4/token', 'urlResourceOwnerDetails' => '',
            ]);
            $accessToken = $provider->getAccessToken('refresh_token', ['refresh_token' => $oauth2['refresh_token'] ?? '']);
            $oauth2['hasError'] = 'false'; $oauth2['access_token'] = $accessToken->getToken();
            $oauth2['expires_in'] = $accessToken->getExpires(); $oauth2['timeIn'] = time();
            $oauth2['refresh_token'] = $accessToken->getRefreshToken() ?: ($oauth2['refresh_token'] ?? '');
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) { $oauth2['hasError'] = 'true'; }
        @file_put_contents($filePath, json_encode($oauth2));
        return $oauth2;
    }

    public function downloadnow(): void
    {
        $postId = trim($this->request->getGet('id') ?? $this->request->getPost('id') ?? '');
        $token  = trim($this->request->getGet('token') ?? $this->request->getPost('token') ?? '');
        $this->db->table('fw_posts')->where('postId', $postId)->set('downloadCount', 'downloadCount + 1', false)->update();
        $response = @json_decode(@file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=6LfFIOggAAAAANTrZmEG8MiC_cUvZLrkwhV60-xd&response=' . $token . '&remoteip=' . $this->request->getIPAddress()), true);
        if (empty($response['success'])) {
            $dataArr = ['code' => 604];
        } else {
            $postd = $this->homeModel->getSinglePost((int) $postId);
            $code = 404; $fileName = ''; $url = '';
            if ($postd !== '0') {
                $code = 200;
                $fileName = 'firmware-' . $postd->model . '-' . $postd->country . '-' . $postd->version . '.zip';
                $downloadButton = @json_decode($postd->downloadButton, true);
                $url = $downloadButton['buttonUrl'] ?? '';
                if (($url === '' || $url === null) && $postd->externalFileLink !== '') $url = $postd->externalFileLink;
            }
            $dataArr = ['code' => $code, 'url' => $url, 'filename' => $fileName];
        }
        $this->response->setContentType('application/json');
        echo json_encode($dataArr);
    }

    public function accessLink(): void
    {
        $data = @file_get_contents('https://samfirms.com/sitemaps');
        $links = []; $count = preg_match_all('@<loc>(.+?)<\/loc>@', $data, $matches);
        for ($i = 0; $i < $count; ++$i) $links[] = $matches[0][$i];
        if (is_array($links) && count($links) > 0) {
            $random_keys = array_rand($links, min(200, count($links)));
            foreach ($random_keys as $key) { try { @file_get_contents(str_replace(['<loc>', '</loc>'], '', $links[$key])); } catch (\Exception $e) {} }
        }
    }

    // ============================================================
    // SITEMAP ADMIN TOOLS
    // ============================================================

    public function refreshSitemaps(): \CodeIgniter\HTTP\Response
    {
        // 🔒 SECURITY: Only allow in development or with admin authentication
        if (ENVIRONMENT === 'production' && empty(session()->get('loggedIn'))) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Authentication required'
            ])->setStatusCode(401);
        }
        
        $result = autoInvalidateSitemapCache();
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Sitemap caches cleared and search engines notified',
            'data' => $result
        ]);
    }

    public function pingSitemaps(): \CodeIgniter\HTTP\Response
    {
        // 🔒 SECURITY: Only allow in development or with admin authentication
        if (ENVIRONMENT === 'production' && empty(session()->get('loggedIn'))) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Authentication required'
            ])->setStatusCode(401);
        }
        
        $result = pingSitemapToSearchEngines();
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Search engines notified about sitemap',
            'data' => $result
        ]);
    }

    /**
     * RSS Feed for Blog Posts
     * Critical for Bing and Yandex discoverability
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function rssFeed(): \CodeIgniter\HTTP\Response
    {
        // ⚡ PERFORMANCE: Cache RSS feed for 30 minutes
        $cache = \Config\Services::cache();
        $cacheKey = 'rss_feed_blog_v1';
        
        if ($cached = $cache->get($cacheKey)) {
            return $this->response->setContentType('application/rss+xml')->setBody($cached);
        }
        
        // Get web settings
        $web = @json_decode(@file_get_contents(RESOURCE_PATH . 'web-setting.info'));
        $webTitle = $web->webTitle ?? 'SamFirms';
        $webDescription = $web->metaDesription ?? 'Download Official Samsung Firmware - Latest Stock ROMs Free';
        
        // Get latest 20 blog posts
        $posts = $this->db->table('fw_blogs')
            ->where('postStatus', 'Active')
            ->orderBy('postId', 'DESC')
            ->limit(20)
            ->get()
            ->getResult();
        
        // Build RSS 2.0 feed
        $rss = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/"/>');
        
        $channel = $rss->addChild('channel');
        $channel->addChild('title', htmlspecialchars($webTitle));
        $channel->addChild('link', base_url());
        $channel->addChild('description', htmlspecialchars($webDescription));
        $channel->addChild('language', 'en-us');
        $channel->addChild('lastBuildDate', date(DATE_RSS));
        $channel->addChild('generator', 'SamFirms RSS Generator');
        
        // Add atom:link for RSS best practices
        $atomLink = $channel->addChild('atom:link', null, 'http://www.w3.org/2005/Atom');
        $atomLink->addAttribute('href', base_url('feed'));
        $atomLink->addAttribute('rel', 'self');
        $atomLink->addAttribute('type', 'application/rss+xml');
        
        foreach ($posts as $post) {
            $item = $channel->addChild('item');
            $item->addChild('title', htmlspecialchars($post->postTitle));
            $item->addChild('link', base_url('blog/' . $post->postSlug));
            $item->addChild('guid', base_url('blog/' . $post->postSlug));
            $item->addChild('pubDate', date(DATE_RSS, strtotime($post->modifiedTime)));
            
            // Extract description (first 300 chars of content, strip HTML)
            $description = strip_tags($post->postContent);
            $description = preg_replace('/\s+/', ' ', $description);
            $description = mb_substr(trim($description), 0, 300) . '...';
            $item->addChild('description', htmlspecialchars($description));
            
            // Add category if available
            if (!empty($post->postCategory)) {
                $item->addChild('category', htmlspecialchars($post->postCategory));
            }
            
            // Extract first image for enclosure (media RSS)
            $doc = new \DOMDocument();
            @$doc->loadHTML($post->postContent);
            $tags = $doc->getElementsByTagName('img');
            foreach ($tags as $tag) {
                $imgSrc = $tag->getAttribute('src');
                if (!empty($imgSrc) && !str_starts_with($imgSrc, 'data:image')) {
                    // Add enclosure for image
                    $enclosure = $item->addChild('enclosure');
                    $enclosure->addAttribute('url', $imgSrc);
                    $enclosure->addAttribute('type', 'image/jpeg');
                    break;
                }
            }
        }
        
        $xmlOutput = $rss->asXml();
        $cache->save($cacheKey, $xmlOutput, 1800); // Cache for 30 minutes
        
        return $this->response->setContentType('application/rss+xml')->setBody($xmlOutput);
    }
}
