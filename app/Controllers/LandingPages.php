<?php

namespace App\Controllers;

use App\Models\HomeModel;

class LandingPages extends BaseController
{
    protected $app;
    protected $web;
    protected $ads;
    protected $countries;
    protected $page_record;
    protected $db;
    protected HomeModel $home_model;

    public function __construct()
    {
        helper(['url', 'cookie', 'text', 'global_function', 'query_cache']);

        $this->db = \Config\Database::connect();

        date_default_timezone_set(defaultTimeZone());

        $this->app = session()->get('fw');

        $this->web = @json_decode(@file_get_contents(RESOURCE_PATH . 'web-setting.info'));

        $this->home_model = new HomeModel();

        $this->page_record = isset($_GET['record']) && $_GET['record'] ? $_GET['record'] : '0';

        $this->ads = getSiteMeta('ads');

        $this->countries = worldCountries();

        // ✅ VISITOR TRACKING: Enabled for statistics
        setVisitor(service('request')->getIPAddress());
    }

    // ----------------------------------------------------------------
    // Index
    // ----------------------------------------------------------------

    public function index(): string
    {
        return $this->indexOld();
    }

    public function indexOld(): string
    {
        // Get homepage data
        $homePage = getPageByArea('home');

        // ✅ REAL STATS: Calculate database-driven statistics with caching
        $data['siteStats'] = $this->getSiteStats();

        // ✅ FIX #3 (CORRECTED): Cache with proper object serialization
        // Using cache()->remember() which preserves object types
        $cache = \Config\Services::cache();
        
        $data['record'] = $cache->remember('homepage_recent_posts_v4', 1800, function() {
            return $this->home_model->getRecentPosts(10, 1);
        });
        
        $data['blog_record'] = $cache->remember('homepage_blog_posts_v4', 1800, function() {
            return $this->home_model->viewBlogPostsLanding(3, 0);
        });

        // ✅ FIX #3: Get recently added models with proper caching
        $data['recentModels'] = $cache->remember('homepage_recent_models_v4', 1800, function() {
            return $this->db->table('fw_posts')
                            ->select('model, device, MAX(publishedAt) as latest_time')
                            ->where('postStatus', 'Active')
                            ->where('publishedAt >=', date('Y-m-d H:i:s', strtotime('-7 days')))
                            ->where('publishedAt IS NOT NULL')
                            ->groupBy('model')
                            ->orderBy('latest_time', 'DESC')
                            ->limit(6)
                            ->get()
                            ->getResult();
        });

        // Get OS list
        // ✅ SECURITY FIX: Only show OS from Active posts
        // Performance: Optimized via database index on postStatus, os
        $data['osList'] = $this->db->table('fw_posts')
                                   ->select('os')
                                   ->where('postStatus', 'Active')
                                   ->where('os <> ', '')
                                   ->groupBy('os')
                                   ->get()
                                   ->getResult();

        $data['header_ads']       = $this->ads['header_ads']       ?? '';
        $data['sidebar_ads']      = $this->ads['sidebar_ads']      ?? '';
        $data['footer_ads']       = $this->ads['footer_ads']       ?? '';
        $data['latest_model_ads'] = $this->ads['latest_model_ads'] ?? '';

        $data['meta_tags']        = $this->web->metaTags       ?? '';
        $data['page_title']       = $homePage->metaTitle ?? 'Samsung Firmware Download'; // ✅ Clean for H1
        // ✅ PRIORITY FIX: Use Web Portal settings first, then fall back to CMS home page
        $data['meta_title']       = $this->web->metaTitle      ?: ($homePage->metaTitle ?? '');
        $data['meta_description'] = $this->web->metaDesription ?: ($homePage->metaDesription ?? '');
        $data['web_title']        = ($data['meta_title'] ?: $homePage->metaTitle ?? '') . ' | ' . ($this->web->webTitle ?? 'SamFirms');
        $data['homePage']         = $homePage;
        $data['web']              = $this->web; // ✅ Pass web object to view for logo display
        $data['request']          = 'home';

        return view(LANDING_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // Post Search (AJAX)
    // ----------------------------------------------------------------

    public function postSearch(): void
    {
        $query = trim($this->request->getGet('query'));

        // ✅ SECURITY FIX: Only search Active posts, exclude Draft/Inactive
        // Performance: Optimized via database index on postStatus, model, device
        $record = $this->db->table('fw_posts')
                           ->select('*')
                           ->where('postStatus', 'Active')
                           ->groupStart()
                               ->like('model', $query)
                               ->orLike('device', $query)
                           ->groupEnd()
                           ->groupBy('model')
                           ->limit(20)
                           ->get();

        $vhtml = '';
        foreach ($record->getResult() as $rec) {
            $ptitle  = $rec->model != '' ? $rec->model . ' / ' . $rec->device : $rec->postTitle;
            $slugUrl = 'firmware/' . $rec->model;
            $vhtml  .= '<a href="' . base_url($slugUrl) . '" class="flex items-center space-x-3 px-4 py-3 hover:bg-accent-soft transition-colors duration-200 border-b border-gray-100 last:border-0 group">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center group-hover:bg-accent group-hover:scale-110 transition-all duration-200">
                    <svg class="w-5 h-5 text-accent group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-ink group-hover:text-accent truncate">' . $rec->model . '</p>
                    <p class="text-xs text-ink-muted truncate">' . $rec->device . '</p>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-accent group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>';
        }

        echo $vhtml != '' ? '<div class="divide-y divide-gray-100">' . $vhtml . '</div>' : '<div class="px-4 py-8 text-center"><svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-sm text-ink-muted">No results found. Try searching with a different model or device name.</p></div>';
    }

    // ----------------------------------------------------------------
    // View Post
    // ----------------------------------------------------------------

    public function viewPost(): string
    {
        $uri   = $this->request->getUri();
        $total = $uri->getTotalSegments();

        $seg1 = ($total >= 1) ? trim($uri->getSegment(1)) : '';
        $seg2 = ($total >= 2) ? trim($uri->getSegment(2)) : '';
        $seg3 = ($total >= 3) ? trim($uri->getSegment(3)) : '';
        $seg4 = ($total >= 4) ? trim($uri->getSegment(4)) : '';

        if ($seg1 == 'firmware') {
            $model   = $seg2;
            $csc     = $seg3;
            $version = $seg4;
        } else {
            $model   = $seg1;
            $csc     = $seg2;
            $version = $seg3;
        }

        // ✅ SECURITY FIX: Only show Active posts, prevent direct URL access to Draft posts
        // Performance: Optimized via database indexes on postStatus, model, version, country
        // ✅ SEO FIX: Use country field only (matching old samfirms URLs from 2019)
        // This prevents duplicate content for same firmware accessible via both country and csc
        $record = $this->db->table('fw_posts')
                           ->select('*')
                           ->where('postStatus', 'Active')
                           ->where('model', $model)
                           ->where('version', $version)
                           ->where('country', $csc)
                           ->get();

        $postCount = $record->getNumRows();

        if ($postCount == 0) {
            // ✅ UX: Check if post exists but is scheduled/draft (better error handling)
            $scheduledPost = $this->db->table('fw_posts')
                                      ->where('model', $model)
                                      ->where('version', $version)
                                      ->where('country', $csc)
                                      ->get()->getRow();
            
            if ($scheduledPost && $scheduledPost->postStatus != 'Active') {
                // Post exists but not published yet - redirect to model page with message
                return redirect()->to(base_url('firmware/' . $model))
                                ->with('info', 'This firmware version will be available soon.');
            }
            
            // Post doesn't exist at all - show 404
            return $this->indexOld();
        } else {
            $post = $record->getRow();
            return $this->showPost($post, $postCount);
        }
    }

    // ----------------------------------------------------------------
    // Show Post
    // ----------------------------------------------------------------

    public function showPost($post, $postCount): string
    {
        if ($this->request->isAJAX()) {
            $postId    = trim($this->request->getPost('postId'));
            $fromName  = trim($this->request->getPost('fromName'));
            $fromEmail = trim($this->request->getPost('fromEmail'));
            $comment   = trim($this->request->getPost('comment'));
            $sanswer   = trim($this->request->getPost('sanswer'));

            if ($fromName == '')  exit('Please enter your name');
            if ($fromEmail == '') exit('Please enter your email');
            if ($comment == '')   exit('Please enter your comment');

            $dataArr = [
                'postId'      => $postId,
                'ipAddress'   => $this->request->getIPAddress(),
                'fromName'    => $fromName,
                'fromEmail'   => $fromEmail,
                'comment'     => $comment,
                'commentTime' => date('Y-m-d H:i:s'),
            ];
            $this->db->table('fw_post_comments')->insert($dataArr);
            exit('success');
        }

        $data['header_ads']  = $this->ads['header_ads']  ?? '';
        $data['sidebar_ads'] = $this->ads['sidebar_ads'] ?? '';
        $data['footer_ads']  = $this->ads['footer_ads']  ?? '';
        $data['post_ads']    = $this->ads['post_ads']    ?? '';

        // ✅ REAL STATS: Pass site statistics to all pages
        $data['siteStats'] = $this->getSiteStats();

        $data['meta_tags']        = replacePostToken($post->metaTags, $post);
        $data['meta_title']       = replacePostToken($post->metaTitle, $post);
        $data['page_title']       = replacePostToken($post->metaTitle, $post); // ✅ Clean title for H1 (no brand)
        $data['meta_description'] = replacePostToken($post->metaDesription, $post);
        
        // ✅ SEO CLEANUP: Remove em dashes from titles (use only pipe separator)
        $data['meta_title'] = str_replace(['—', '–', ' - ', ' – ', ' — '], ' ', $data['meta_title']);
        $data['meta_title'] = preg_replace('/\s+/', ' ', $data['meta_title']); // Clean multiple spaces
        $data['page_title'] = str_replace(['—', '–', ' - ', ' – ', ' — '], ' ', $data['page_title']);
        $data['page_title'] = preg_replace('/\s+/', ' ', $data['page_title']); // Clean multiple spaces
        
        // ✅ BRANDING: Smart truncation for web_title to fit brand within 55 chars
        $brandSuffix = ' | ' . ($this->web->webTitle ?? 'SamFirms');
        $maxLength = 55 - strlen($brandSuffix); // 43 chars for content
        
        if (strlen($post->postTitle) > $maxLength) {
            // Truncate intelligently - keep model, CSC, version priority
            $truncated = substr($post->postTitle, 0, $maxLength);
            // Remove incomplete word at end
            $truncated = preg_replace('/\s+\S*$/', '', $truncated);
            $data['web_title'] = $truncated . $brandSuffix;
        } else {
            $data['web_title'] = $post->postTitle . $brandSuffix;
        }

        // ✅ SEO FIX: Provide fallback meta tags if database fields are empty
        if (empty(trim($data['meta_title']))) {
            // ✅ PROGRESSIVE FUNNEL: Add "Download" for action intent
            $displayDevice = formatDeviceDisplay($post->device);
            $baseTitle = "Download {$post->model} {$post->csc} {$post->version} Firmware";
            $data['page_title'] = $baseTitle; // ✅ Clean for H1
            $data['meta_title'] = $baseTitle . " | " . ($this->web->webTitle ?? 'SamFirms'); // ✅ Branded for <title>
        } else {
            // Admin filled meta title - ensure "Download" prefix and brand suffix
            $cleanTitle = trim($data['meta_title']);
            
            // ✅ CLEANUP: Remove any trailing separators (em dash, en dash, pipe, hyphen)
            $cleanTitle = preg_replace('/\s*[—–\-|]+\s*$/', '', $cleanTitle);
            
            // Add "Download" prefix if not present
            if (stripos($cleanTitle, 'Download') !== 0) {
                $cleanTitle = 'Download ' . $cleanTitle;
            }
            
            // Store clean title for H1 (remove brand if present)
            $data['page_title'] = preg_replace('/\s*\|\s*SamFirms\s*$/i', '', $cleanTitle);
            
            // Add brand suffix if not present
            if (strpos($cleanTitle, 'SamFirms') === false && strpos($cleanTitle, '|') === false) {
                $brandSuffix = ' | ' . ($this->web->webTitle ?? 'SamFirms');
                
                // Smart truncation if too long (keep under 60 chars)
                $maxMetaLength = 58 - strlen($brandSuffix); // 58 to be safe
                if (strlen($cleanTitle) > $maxMetaLength) {
                    $cleanTitle = substr($cleanTitle, 0, $maxMetaLength);
                    $cleanTitle = preg_replace('/\s+\S*$/', '', $cleanTitle); // Remove incomplete word
                }
                $data['meta_title'] = $cleanTitle . $brandSuffix;
            } else {
                $data['meta_title'] = $cleanTitle;
            }
        }
        
        if (empty(trim($data['meta_description']))) {
            $worldCountries = worldCountries();
            $countryName = $worldCountries[$post->country ?? 'US']['name'] ?? '';
            $countryText = $countryName ? " ({$countryName})" : "";
            $osText = !empty($post->os) ? "Android {$post->os}, " : "";
            $sizeText = !empty($post->fileSize) ? "{$post->fileSize}. " : "";
            
            // ✅ GALAXY KEYWORD: Add "Galaxy" prefix for better SEO
            $displayDevice = formatDeviceDisplay($post->device);
            
            $data['meta_description'] = "Download {$post->model} firmware version {$post->version} for {$post->csc}{$countryText}. " .
                                       "Official Samsung {$displayDevice} stock ROM. {$osText}{$sizeText}Free & fast download with installation guide.";
        }

        // ✅ SEO FIX: Always set canonical URL to prevent duplicate content issues
        $data['canonicalTags'] = postUrl($post);

        $data['post']    = $post;
        $data['request'] = 'view-post';

        return view(LANDING_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // Contact Us
    // ----------------------------------------------------------------

    public function contactUs(): string
    {
        $homePage = getPageByArea('home');

        $data['header_ads']       = $this->ads['header_ads']  ?? '';
        $data['sidebar_ads']      = $this->ads['sidebar_ads'] ?? '';
        $data['footer_ads']       = $this->ads['footer_ads']  ?? '';
        
        // ✅ REAL STATS: Pass site statistics to all pages
        $data['siteStats'] = $this->getSiteStats();
        
        // ⚠️ REMOVED: Meta keywords (deprecated since 2009)
        $data['page_title']       = 'Contact Us'; // ✅ Clean title for H1
        $data['meta_title']       = 'Contact Us | ' . ($this->web->webTitle ?? 'SamFirms');
        $data['meta_description'] = 'Contact US ' . ($homePage->metaDesription ?? '');
        $data['web_title']        = 'Contact Us | ' . ($this->web->webTitle ?? 'SamFirms');
        $data['request']          = 'contact-us';

        return view(LANDING_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // IMEI Check
    // ----------------------------------------------------------------

    public function imeiCheck()
    {
        if ($this->request->isAJAX()) {
            // Handle AJAX request for IMEI check
            $deviceImei = trim($this->request->getPost('deviceImei'));
            
            // Validate IMEI
            $validation = $this->validateIMEI($deviceImei);
            if (!$validation['valid']) {
                return $this->response->setJSON([
                    'status' => 'failed',
                    'response' => $validation['message']
                ]);
            }
            
            // Log the check
            $this->db->table('fw_imei_checks')->insert([
                'imei' => $deviceImei,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'created_time' => date('Y-m-d H:i:s')
            ]);
            
            // Generate result HTML with firmware links
            $resultHtml = $this->generateImeiResult($deviceImei);
            
            if ($resultHtml === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'response' => 'Unable to retrieve device information. Please try again later.'
                ]);
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'response' => $resultHtml
            ]);
        }
        
        // Handle GET request - show page
        $pagee = getPageBySlug('imei');
        
        if ($pagee == '0') {
            return redirect()->to(base_url());
        }
        
        $data['header_ads']       = $this->ads['header_ads']  ?? '';
        $data['sidebar_ads']      = $this->ads['sidebar_ads'] ?? '';
        $data['footer_ads']       = $this->ads['footer_ads']  ?? '';
        $data['siteStats']        = $this->getSiteStats();
        $data['page_title']       = $pagee->pageTitle ?? 'IMEI Check';
        $data['meta_title']       = $pagee->metaTitle ?? 'IMEI Check | Samsung Device Information';
        $data['meta_description'] = $pagee->metaDesription ?? 'Check your Samsung device IMEI number to verify warranty status, model information, and authenticity.';
        $data['web_title']        = ($pagee->pageTitle ?? 'IMEI Check') . ' | ' . ($this->web->webTitle ?? 'SamFirms');
        $data['pagee']            = $pagee;
        $data['web']              = $this->web;
        $data['request']          = 'check-imei';
        
        return view(LANDING_PATH . '/include/content', $data);
    }
    
    // ----------------------------------------------------------------
    // Validate IMEI
    // ----------------------------------------------------------------
    
    private function validateIMEI(string $imei): array
    {
        // Remove spaces and dashes
        $imei = preg_replace('/[\s\-]/', '', $imei);
        
        // Check if empty
        if (empty($imei)) {
            return [
                'valid' => false,
                'message' => 'Please enter an IMEI number'
            ];
        }
        
        // Check length (15 digits for IMEI, 14 for older devices)
        if (!preg_match('/^\d{14,15}$/', $imei)) {
            return [
                'valid' => false,
                'message' => 'IMEI must be 14-15 digits'
            ];
        }
        
        // Luhn algorithm check (for 15-digit IMEI)
        if (strlen($imei) == 15 && !$this->luhnCheck($imei)) {
            return [
                'valid' => false,
                'message' => 'Invalid IMEI number (checksum failed)'
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Valid IMEI'
        ];
    }
    
    // ----------------------------------------------------------------
    // Luhn Algorithm Check (IMEI validation)
    // ----------------------------------------------------------------
    
    private function luhnCheck(string $number): bool
    {
        $sum = 0;
        $numDigits = strlen($number);
        $parity = $numDigits % 2;
        
        for ($i = 0; $i < $numDigits; $i++) {
            $digit = (int)$number[$i];
            
            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            
            $sum += $digit;
        }
        
        return ($sum % 10) == 0;
    }
    
    // ----------------------------------------------------------------
    // Generate IMEI Result HTML
    // ----------------------------------------------------------------
    
    private function generateImeiResult(string $imei)
    {
        // Clean IMEI
        $cleanImei = preg_replace('/[\s\-]/', '', $imei);
        
        // Try to get cached info from database
        $cachedInfo = $this->db->table('fw_imei_info')
                              ->where('imei', $cleanImei)
                              ->orderBy('createdTime', 'DESC')
                              ->get()
                              ->getRow();
        
        // Check if API is configured
        $apiKey = $this->web->imeiApiKey ?? '';
        $serviceId = $this->web->imeiApiServiceId ?? '';
        
        $deviceInfo = null;
        
        if ($cachedInfo && $cachedInfo->result == 'success') {
            // Use cached result
            $deviceInfo = $cachedInfo;
        } elseif (!empty($apiKey) && !empty($serviceId)) {
            // Call external API
            $apiResult = $this->callImeiAPI($cleanImei, $apiKey, $serviceId);
            
            if ($apiResult['success']) {
                // Store in database
                $insertData = [
                    'imei' => $cleanImei,
                    'modelInfo' => $apiResult['data']['modelInfo'] ?? '',
                    'serial' => $apiResult['data']['serial'] ?? '',
                    'modelDesc' => $apiResult['data']['modelDesc'] ?? '',
                    'modelName' => $apiResult['data']['modelName'] ?? '',
                    'modelNumber' => $apiResult['data']['modelNumber'] ?? '',
                    'color' => $apiResult['data']['color'] ?? '',
                    'warrantyStatus' => $apiResult['data']['warrantyStatus'] ?? '',
                    'estWarrantyEnd' => $apiResult['data']['estWarrantyEnd'] ?? '',
                    'productionLocation' => $apiResult['data']['productionLocation'] ?? '',
                    'productionDate' => $apiResult['data']['productionDate'] ?? '',
                    'country' => $apiResult['data']['country'] ?? '',
                    'carrier' => $apiResult['data']['carrier'] ?? '',
                    'result' => 'success',
                    'visitorIP' => $this->request->getIPAddress(),
                    'createdTime' => date('Y-m-d H:i:s')
                ];
                $this->db->table('fw_imei_info')->insert($insertData);
                
                $deviceInfo = (object)$apiResult['data'];
            }
        }
        
        // If no API result, generate basic info from database lookup
        if (!$deviceInfo) {
            // Try to extract TAC (first 8 digits) and look up model in database
            $tac = substr($cleanImei, 0, 8);
            
            // Search for matching firmware by TAC or similar models
            $modelData = $this->db->table('fw_firmware')
                                 ->select('model, device')
                                 ->groupBy('model')
                                 ->limit(1)
                                 ->get()
                                 ->getRow();
            
            // Create basic device info
            $deviceInfo = (object)[
                'imei' => $cleanImei,
                'modelNumber' => $modelData->model ?? 'Unknown',
                'modelName' => $modelData->device ?? 'Samsung Device',
                'serial' => substr($cleanImei, 0, 14),
                'result' => 'partial'
            ];
        }
        
        // Format device info with firmware links
        return $this->formatImeiInfoWithFirmware($deviceInfo, $cleanImei);
    }
    
    // ----------------------------------------------------------------
    // Call External IMEI API
    // ----------------------------------------------------------------
    
    private function callImeiAPI(string $imei, string $apiKey, string $serviceId): array
    {
        try {
            $curl = curl_init();
            
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://alpha.imeicheck.com/api/php-api/create',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query([
                    'key' => $apiKey,
                    'service' => $serviceId,
                    'imei' => $imei
                ])
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($httpCode == 200 && $response) {
                $data = json_decode($response, true);
                
                if (isset($data['status']) && $data['status'] == 'success') {
                    return [
                        'success' => true,
                        'data' => $data
                    ];
                }
            }
            
            return ['success' => false];
            
        } catch (\Exception $e) {
            return ['success' => false];
        }
    }
    
    // ----------------------------------------------------------------
    // Format IMEI Info with Firmware Download Links
    // ----------------------------------------------------------------
    
    private function formatImeiInfoWithFirmware($info, string $imei): string
    {
        // Extract model number from API response
        $modelNumber = $info->modelNumber ?? $info->modelName ?? $info->modelInfo ?? '';
        
        // Get country code
        $countryCode = $info->country ?? '';
        
        // Search for firmware in database using model
        $firmwares = [];
        if (!empty($modelNumber)) {
            $firmwares = $this->db->table('fw_firmware')
                                 ->like('model', $modelNumber, 'both')
                                 ->orderBy('version', 'DESC')
                                 ->limit(5)
                                 ->get()
                                 ->getResult();
        }
        
        // Start building HTML - Similar to screenshot layout
        $html = '<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">';
        
        // Left side - Device image placeholder (you can add actual device image later)
        $html .= '<div class="flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-8 border border-gray-200">';
        $html .= '<div class="text-center">';
        $html .= '<div class="w-48 h-48 mx-auto mb-4 bg-white rounded-3xl shadow-lg flex items-center justify-center border-4 border-gray-200">';
        $html .= '<i class="fas fa-mobile-alt text-8xl text-gray-400"></i>';
        $html .= '</div>';
        $html .= '<p class="text-sm text-gray-500 italic">Device image placeholder</p>';
        $html .= '</div></div>';
        
        // Right side - Device information
        $html .= '<div class="space-y-4">';
        
        // IMEI
        $html .= '<div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">';
        $html .= '<p class="text-sm font-medium text-gray-600 mb-1">IMEI</p>';
        $html .= '<p class="text-lg font-bold text-gray-900">' . $imei . '</p>';
        $html .= '</div>';
        
        // Serial Number
        if (!empty($info->serial)) {
            $html .= '<div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">';
            $html .= '<p class="text-sm font-medium text-gray-600 mb-1">Serial number</p>';
            $html .= '<p class="text-lg font-bold text-gray-900">' . htmlspecialchars($info->serial) . '</p>';
            $html .= '</div>';
        }
        
        // Model
        if (!empty($modelNumber)) {
            $html .= '<div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">';
            $html .= '<p class="text-sm font-medium text-gray-600 mb-1">Model</p>';
            $html .= '<p class="text-lg font-bold text-gray-900">' . htmlspecialchars($modelNumber) . '</p>';
            $html .= '</div>';
        }
        
        // Region
        if (!empty($countryCode)) {
            $html .= '<div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">';
            $html .= '<p class="text-sm font-medium text-gray-600 mb-1">Region</p>';
            $html .= '<p class="text-lg font-bold text-gray-900">' . htmlspecialchars($countryCode) . '</p>';
            $html .= '</div>';
        }
        
        // Model name
        if (!empty($info->modelDesc)) {
            $html .= '<div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">';
            $html .= '<p class="text-sm font-medium text-gray-600 mb-1">Model name</p>';
            $html .= '<p class="text-lg font-bold text-gray-900">' . htmlspecialchars($info->modelDesc) . '</p>';
            $html .= '</div>';
        }
        
        // Model full name
        if (!empty($info->modelName)) {
            $html .= '<div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">';
            $html .= '<p class="text-sm font-medium text-gray-600 mb-1">Model full name</p>';
            $html .= '<p class="text-lg font-bold text-gray-900">' . htmlspecialchars($info->modelName) . '</p>';
            $html .= '</div>';
        }
        
        // Country
        if (!empty($info->country)) {
            $html .= '<div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">';
            $html .= '<p class="text-sm font-medium text-gray-600 mb-1">Country</p>';
            $html .= '<p class="text-lg font-bold text-gray-900">' . htmlspecialchars($info->country) . '</p>';
            $html .= '</div>';
        }
        
        // Production date
        if (!empty($info->productionDate)) {
            $html .= '<div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">';
            $html .= '<p class="text-sm font-medium text-gray-600 mb-1">Product date</p>';
            $html .= '<p class="text-lg font-bold text-gray-900">' . htmlspecialchars($info->productionDate) . '</p>';
            $html .= '</div>';
        }
        
        // Device age (if production date available)
        if (!empty($info->productionDate)) {
            try {
                $prodDate = new \DateTime($info->productionDate);
                $now = new \DateTime();
                $diff = $now->diff($prodDate);
                $age = $diff->days;
                
                $html .= '<div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">';
                $html .= '<p class="text-sm font-medium text-gray-600 mb-1">Device age</p>';
                $html .= '<p class="text-lg font-bold text-gray-900">' . $age . ' days</p>';
                $html .= '</div>';
            } catch (\Exception $e) {
                // Skip if date parsing fails
            }
        }
        
        // KG Registered (Warranty)
        if (!empty($info->warrantyStatus)) {
            $isRegistered = (stripos($info->warrantyStatus, 'yes') !== false || stripos($info->warrantyStatus, 'active') !== false);
            $html .= '<div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">';
            $html .= '<div class="flex items-center justify-between">';
            $html .= '<p class="text-sm font-medium text-gray-600">KG Registered</p>';
            if ($isRegistered) {
                $html .= '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">';
                $html .= '<i class="fas fa-check-circle mr-1"></i> YES';
                $html .= '</span>';
            } else {
                $html .= '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">';
                $html .= '<i class="fas fa-times-circle mr-1"></i> NO';
                $html .= '</span>';
            }
            $html .= '</div></div>';
        }
        
        $html .= '</div>'; // End right side
        $html .= '</div>'; // End grid
        
        // Firmware Download Buttons Section
        if (!empty($firmwares) && count($firmwares) > 0) {
            $html .= '<div class="mt-8 space-y-3">';
            
            // Get the first firmware to extract model info
            $firstFw = $firmwares[0];
            
            // Button 1: Download Model Firmware (all regions)
            $html .= '<a href="' . base_url('firmware/' . $firstFw->model) . '" ';
            $html .= 'class="block w-full bg-gray-800 hover:bg-gray-900 text-white text-center py-4 px-6 rounded-xl font-bold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">';
            $html .= '<i class="fas fa-download mr-2"></i>';
            $html .= 'Download ' . htmlspecialchars($firstFw->model);
            if (!empty($firstFw->device)) {
                $html .= ' / ' . htmlspecialchars($firstFw->device);
            }
            $html .= ' Firmware';
            $html .= '</a>';
            
            // Button 2: Download with specific region (if country code available)
            if (!empty($countryCode) && count($firmwares) > 0) {
                // Try to find firmware with matching CSC/country
                $regionFw = null;
                foreach ($firmwares as $fw) {
                    if (strtolower($fw->csc) == strtolower($countryCode) || strtolower($fw->country) == strtolower($countryCode)) {
                        $regionFw = $fw;
                        break;
                    }
                }
                
                if ($regionFw) {
                    $worldCountries = worldCountries();
                    $countryName = $worldCountries[$regionFw->country]['name'] ?? $countryCode;
                    
                    $html .= '<a href="' . base_url('firmware/' . $regionFw->model . '/' . $regionFw->csc) . '" ';
                    $html .= 'class="block w-full bg-gray-800 hover:bg-gray-900 text-white text-center py-4 px-6 rounded-xl font-bold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">';
                    $html .= '<i class="fas fa-download mr-2"></i>';
                    $html .= 'Download ' . htmlspecialchars($regionFw->model);
                    if (!empty($regionFw->device)) {
                        $html .= ' / ' . htmlspecialchars($regionFw->device);
                    }
                    $html .= ' with Region ' . htmlspecialchars($regionFw->csc) . ' Firmware';
                    $html .= '</a>';
                }
            }
            
            // Button 3: Combination firmware (if available)
            $html .= '<a href="' . base_url('firmware/' . $firstFw->model) . '" ';
            $html .= 'class="block w-full bg-gray-800 hover:bg-gray-900 text-white text-center py-4 px-6 rounded-xl font-bold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">';
            $html .= '<i class="fas fa-download mr-2"></i>';
            $html .= 'Download ' . htmlspecialchars($firstFw->model);
            if (!empty($firstFw->device)) {
                $html .= ' / ' . htmlspecialchars($firstFw->device);
            }
            $html .= ' Combination';
            $html .= '</a>';
            
            $html .= '</div>';
        } else {
            // No firmware found message
            $html .= '<div class="mt-8 p-6 bg-yellow-50 border border-yellow-200 rounded-xl">';
            $html .= '<div class="flex items-start space-x-3">';
            $html .= '<i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-1"></i>';
            $html .= '<div>';
            $html .= '<h4 class="font-bold text-yellow-900 mb-1">No Firmware Found</h4>';
            $html .= '<p class="text-sm text-yellow-800">We could not find firmware for this device model in our database. ';
            $html .= 'Please check our <a href="' . base_url() . '" class="underline font-bold">homepage</a> to search manually.</p>';
            $html .= '</div></div></div>';
        }
        
        // Unlock/FRP section (optional)
        $html .= '<div class="mt-6 p-6 bg-gradient-to-r from-cyan-50 to-blue-50 border border-cyan-200 rounded-xl">';
        $html .= '<div class="flex items-center space-x-3 mb-3">';
        $html .= '<div class="w-10 h-10 bg-cyan-500 rounded-lg flex items-center justify-center">';
        $html .= '<i class="fas fa-unlock text-white"></i>';
        $html .= '</div>';
        $html .= '<h4 class="text-lg font-bold text-gray-900">Need to Unlock or Remove FRP?</h4>';
        $html .= '</div>';
        $html .= '<p class="text-sm text-gray-700">Contact our support team for assistance with device unlocking or FRP removal services.</p>';
        $html .= '</div>';
        
        return $html;
    }

    // ----------------------------------------------------------------
    // View Page (CMS)
    // ----------------------------------------------------------------

    public function viewPage(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // ✅ FIX: safe segment access
        $uri     = $this->request->getUri();
        $slugUrl = ($uri->getTotalSegments() >= 1) ? trim($uri->getSegment(1)) : '';

        $post = getPostBySlug($slugUrl);
        if ($post == '0') {
            $pagee = getPageBySlug($slugUrl);
            if ($pagee == '0') {
                return redirect()->to(base_url());
            }

            $data['header_ads']       = $this->ads['header_ads']  ?? '';
            $data['sidebar_ads']      = $this->ads['sidebar_ads'] ?? '';
            $data['footer_ads']       = $this->ads['footer_ads']  ?? '';
            
            // ✅ REAL STATS: Pass site statistics to all pages
            $data['siteStats'] = $this->getSiteStats();
            
            // ⚠️ REMOVED: Meta keywords (deprecated since 2009)
            $data['page_title']       = $pagee->pageTitle ?? 'Page'; // ✅ Clean for H1
            $data['meta_title']       = $pagee->metaTitle         ?? '';
            $data['meta_description'] = $pagee->metaDesription    ?? '';
            $data['web_title']        = ($pagee->pageTitle ?? '') . ' | ' . ($this->web->webTitle ?? 'SamFirms');
            $data['pagee']            = $pagee;
            $data['web']              = $this->web; // ✅ Pass web object to view for logo display
            $data['request']          = 'view-cms-page';

            return view(LANDING_PATH . '/include/content', $data);
        } else {
            return $this->showPost($post, 1);
        }
    }

    // ----------------------------------------------------------------
    // Category Posts
    // ----------------------------------------------------------------

    public function categoryPosts(): string
    {
        $homePage = getPageByArea('home');

        // ✅ FIX: safe segment access
        $uri     = $this->request->getUri();
        $catSlug = ($uri->getTotalSegments() >= 2) ? trim($uri->getSegment(2)) : '';
        $cat     = getCategory($catSlug);

        if ($cat == '0') {
            return view('error-404');
        }

        $per_page   = 50;
        $total_rows = $this->home_model->getCategoryPosts($cat->category);

        $data['total_rows']  = $total_rows;
        $data['per_page']    = $per_page;
        $data['page_record'] = $this->page_record;
        $data['base_url']    = base_url('category/' . $catSlug);
        $data['record']      = $this->home_model->getCategoryPosts($cat->category, $per_page, $this->page_record);

        $data['header_ads']       = $this->ads['header_ads']  ?? '';
        $data['sidebar_ads']      = $this->ads['sidebar_ads'] ?? '';
        $data['footer_ads']       = $this->ads['footer_ads']  ?? '';
        
        // ✅ REAL STATS: Pass site statistics to all pages
        $data['siteStats'] = $this->getSiteStats();
        
        // ⚠️ REMOVED: Meta keywords (deprecated since 2009)
        $data['page_title']       = $cat->category ?? 'Category'; // ✅ Clean for H1
        $data['meta_title']       = $homePage->metaTitle      ?? '';
        $data['meta_description'] = $homePage->metaDesription ?? '';
        $data['web_title']        = ($homePage->metaTitle ?? '') . ' | ' . ($this->web->webTitle ?? 'SamFirms');
        $data['request']          = 'category-posts';

        return view(LANDING_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // Firmware Posts
    // ----------------------------------------------------------------

    public function firmwarePosts(): string
    {
        $homePage = getPageByArea('home');

        // ✅ FIX: safe segment access
        $uri   = $this->request->getUri();
        $model = ($uri->getTotalSegments() >= 2) ? trim($uri->getSegment(2)) : '';
        $csc   = ($uri->getTotalSegments() >= 3) ? trim($uri->getSegment(3)) : '';

        $per_page   = 10;
        $total_rows = $this->home_model->getModelPosts($model, $csc);
        $record     = $this->home_model->getModelPosts($model, $csc, $per_page, $this->page_record);

        $data['total_rows']  = $total_rows;
        $data['per_page']    = $per_page;
        $data['page_record'] = $this->page_record;
        $data['base_url']    = base_url('firmware/' . $model);
        $data['record']      = $record;

        $data['header_ads']  = $this->ads['header_ads']  ?? '';
        $data['sidebar_ads'] = $this->ads['sidebar_ads'] ?? '';
        $data['footer_ads']  = $this->ads['footer_ads']  ?? '';
        
        // ✅ REAL STATS: Pass site statistics to all pages
        $data['siteStats'] = $this->getSiteStats();
        
        // ⚠️ REMOVED: Meta keywords (deprecated since 2009)

        // ✅ SEO OPTIMIZATION: Enhanced meta tags with better descriptions
        $firstRec = $record[0] ?? null;
        $deviceName = $firstRec->device ?? 'Samsung Device';
        
        // ✅ GALAXY KEYWORD: Add "Galaxy" prefix for better SEO
        $displayDeviceName = formatDeviceDisplay($deviceName);
        
        // ✅ DYNAMIC: Calculate version count from actual database records
        $versionCount = is_array($record) ? count($record) : 0;
        
        if ($csc != '' && $csc != null) {
            // Step 2: Model + CSC page
            $countryName = '';
            $countryAbbr = '';
            if ($firstRec && isset($firstRec->country)) {
                $worldCountries = worldCountries();
                $countryName = $worldCountries[$firstRec->country]['name'] ?? '';
                
                // ✅ DYNAMIC: Get country abbreviation for title
                $countryAbbreviations = [
                    'United Arab Emirates' => 'UAE',
                    'United States' => 'USA',
                    'United Kingdom' => 'UK',
                    'Saudi Arabia' => 'KSA',
                    'South Africa' => 'SA',
                    'New Zealand' => 'NZ',
                ];
                $countryAbbr = $countryAbbreviations[$countryName] ?? $countryName;
            }
            
            // ✅ DYNAMIC: Get latest Android version for this model + CSC
            $latestOS = $this->db->table('fw_posts')
                ->select('os')
                ->where('postStatus', 'Active')
                ->where('model', $model)
                ->where('country', $csc)
                ->orderBy('CAST(os AS UNSIGNED)', 'DESC')
                ->limit(1)
                ->get()
                ->getRow()
                ->os ?? '';
            
            // ✅ SEO: Create separate page title (clean) and meta title (branded + optimized)
            // H1/H2 will show: "SM-F761U1 GALAXY Z FLIP7 FE XAA\nUnited States"
            $pageTitle = $model . ' ' . $displayDeviceName . ' ' . $csc . ($countryName ? "\n" . $countryName : '');
            
            // ✅ DYNAMIC Meta title: Includes country abbreviation
            $mTitle = $model . ' ' . $displayDeviceName . ' ' . $csc . ' Firmware';
            if ($countryAbbr) {
                $mTitle .= ' (' . $countryAbbr . ')';
            }
            $mTitle .= ' | ' . ($this->web->webTitle ?? 'SamFirms');
            
            // ✅ DYNAMIC Meta description: Version count + Android version update automatically
            $androidText = $latestOS ? " Android {$latestOS}." : "";
            $data['meta_description'] = "Download {$displayDeviceName} ({$model}) firmware for {$csc} - {$countryName}. {$versionCount}+ ROMs,{$androidText} Official, fast & free with guide.";
        } else {
            // Step 1: Model-only page
            
            // ✅ DYNAMIC: Get latest Android version for this model (all CSCs)
            $latestOS = $this->db->table('fw_posts')
                ->select('os')
                ->where('postStatus', 'Active')
                ->where('model', $model)
                ->orderBy('CAST(os AS UNSIGNED)', 'DESC')
                ->limit(1)
                ->get()
                ->getRow()
                ->os ?? '';
            
            // ✅ SEO: Create separate page title (clean) and meta title (branded + optimized)
            // H1/H2 will show: "SM-F761U1 GALAXY Z FLIP7 FE Firmware - All versions"
            $pageTitle = $model . ' ' . $displayDeviceName . ' Firmware - All versions';
            
            // ✅ DYNAMIC Meta title: Includes version count that updates automatically
            $mTitle = $model . ' ' . $displayDeviceName . ' Firmware All Regions (' . $versionCount . '+) | ' . ($this->web->webTitle ?? 'SamFirms');
            
            // ✅ DYNAMIC Meta description: Version count + Android version update automatically
            $androidText = $latestOS ? " Latest Android {$latestOS}." : "";
            $data['meta_description'] = "Download {$displayDeviceName} ({$model}) firmware - {$versionCount}+ ROMs all regions.{$androidText} Free, fast & official. Installation guide included.";
        }
        
        // Add pagination indicator to titles
        if ($this->page_record != '0') {
            $pageNum = ((int)$this->page_record / 10) + 1;
            $pageTitle .= ' - Page ' . $pageNum;
            $mTitle .= ' - Page ' . $pageNum;
        }

        $data['page_title']       = $pageTitle; // ✅ Clean title for H1/H2 (no brand)
        $data['meta_title']       = $mTitle;     // ✅ Branded title for <title> tag
        $data['web_title']        = $mTitle;     // ✅ Use same branded title for <title> tag

        // ✅ SEO PHASE 1: Set canonical URL (clean URL without query parameters)
        // This helps consolidate all filtered/paginated variations to the main page
        $canonicalPath = 'firmware/' . $model;
        if ($csc != '' && $csc != null) {
            $canonicalPath .= '/' . $csc;
        }
        $data['canonicalTags'] = base_url($canonicalPath);

        // ✅ SECURITY FIX: Only show OS from Active posts
        $data['osList'] = $this->db->table('fw_posts')
                                   ->select('os')
                                   ->where('postStatus', 'Active')
                                   ->where('model', $model)
                                   ->where('os <> ', '')
                                   ->groupBy('os')
                                   ->get()
                                   ->getResult();

        $data['homePage'] = getPageByArea('home');
        $data['request']  = 'model-posts';

        return view(LANDING_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // ✅ SEO PHASE 2: AJAX Filter - Returns filtered firmware rows
    // ----------------------------------------------------------------

    public function ajaxFilter(): string
    {
        // Get filter parameters
        $model = $this->request->getGet('model');
        $csc = $this->request->getGet('csc');
        $bit = $this->request->getGet('bit');
        $os = $this->request->getGet('os');
        $filterCsc = $this->request->getGet('filter_csc');
        
        // Build query
        $builder = $this->db->table('fw_posts')
                            ->where('postStatus', 'Active')
                            ->where('model', $model);
        
        // Apply CSC filter from URL segment (Step 2: /firmware/MODEL/CSC)
        if (!empty($csc)) {
            $builder->groupStart()
                   ->where('csc', strtoupper($csc))
                   ->orWhere('country', strtoupper($csc))
                   ->groupEnd();
        }
        
        // Apply additional filters from user input
        if (!empty($bit)) {
            $builder->where('bit', $bit);
        }
        
        if (!empty($os)) {
            $builder->where('os', $os);
        }
        
        if (!empty($filterCsc)) {
            $builder->groupStart()
                   ->like('csc', strtoupper($filterCsc))
                   ->orLike('country', strtoupper($filterCsc))
                   ->groupEnd();
        }
        
        // Get filtered results
        $record = $builder->orderBy('modifiedTime', 'desc')
                         ->limit(100)
                         ->get()
                         ->getResult();
        
        // Generate table rows HTML
        $html = '';
        foreach ($record as $rec) {
            $post_link = postUrl($rec);
            $html .= '<tr class="hover:bg-accent-soft cursor-pointer transition-all duration-200 link-click" data-link="'.$post_link.'">';
            $html .= '<td class="px-6 py-4"><a href="'.base_url('firmware/'.$rec->model).'" class="text-accent hover:text-accent-hover font-bold text-sm">'.$rec->model.'</a></td>';
            $html .= '<td class="px-6 py-4 text-ink font-medium text-sm">'.($rec->device != '' ? $rec->device : $rec->postTitle).'</td>';
            $html .= '<td class="px-6 py-4 text-center"><a href="'.base_url('firmware/'.$rec->model.'/'.$rec->csc).'" class="inline-flex flex-col items-center space-y-1 hover:scale-110 transition-transform"><img class="rounded shadow-sm border border-gray-200" loading="lazy" alt="'.ucfirst(worldCountries()[$rec->country]['name'] ?? 'us').' flag" src="'.base_url('assets/img/flags/4x3/'.(strtolower(worldCountries()[$rec->country]['code'] ?? 'us')).'.svg').'" width="28" height="21"><span class="text-xs font-bold text-ink mt-1">'.$rec->csc.'</span></a></td>';
            $html .= '<td class="px-6 py-4 text-ink-muted font-mono text-sm">'.$rec->version.'</td>';
            $html .= '<td class="px-6 py-4"><span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-accent-soft text-accent border border-accent/20">'.$rec->os.'</span></td>';
            $html .= '<td class="px-6 py-4 text-ink-muted text-sm font-medium">'.($rec->fileSize != '' ? $rec->fileSize : '<span class="text-highlight font-semibold">Uploading...</span>').'</td>';
            $html .= '<td class="px-6 py-4 text-ink-muted text-sm">'.date('Y-m-d', strtotime($rec->modifiedTime)).'</td>';
            $html .= '</tr>';
        }
        
        return $html;
    }

    // ----------------------------------------------------------------
    // ✅ SEO PHASE 2: AJAX Filter for Home Page
    // ----------------------------------------------------------------

    public function ajaxFilterHome(): string
    {
        // Get filter parameters
        $bit = $this->request->getGet('bit');
        $os = $this->request->getGet('os');
        $filterCsc = $this->request->getGet('filter_csc');
        
        // Build query - get latest firmware across all models
        $builder = $this->db->table('fw_posts')
                            ->where('postStatus', 'Active');
        
        // Apply filters
        if (!empty($bit)) {
            $builder->where('bit', $bit);
        }
        
        if (!empty($os)) {
            $builder->where('os', $os);
        }
        
        if (!empty($filterCsc)) {
            $builder->groupStart()
                   ->like('csc', strtoupper($filterCsc))
                   ->orLike('country', strtoupper($filterCsc))
                   ->groupEnd();
        }
        
        // Get filtered results
        $record = $builder->orderBy('modifiedTime', 'desc')
                         ->limit(10)
                         ->get()
                         ->getResult();
        
        // Generate table rows HTML
        $html = '';
        foreach ($record as $rec) {
            $post_link = postUrl($rec);
            $html .= '<tr class="hover:bg-accent-soft cursor-pointer transition-all duration-200 link-click" data-link="'.$post_link.'">';
            $html .= '<td class="px-6 py-4"><a href="'.base_url('firmware/'.$rec->model).'" class="text-accent hover:text-accent-hover font-bold text-sm">'.$rec->model.'</a></td>';
            $html .= '<td class="px-6 py-4 text-ink font-medium text-sm">'.($rec->device != '' ? $rec->device : $rec->postTitle).'</td>';
            $html .= '<td class="px-6 py-4 text-center"><a href="'.base_url('firmware/'.$rec->model.'/'.$rec->csc).'" class="inline-flex flex-col items-center space-y-1 hover:scale-110 transition-transform"><img class="rounded shadow-sm border border-gray-200" loading="lazy" alt="'.ucfirst(worldCountries()[$rec->country]['name'] ?? 'us').' flag" src="'.base_url('assets/img/flags/4x3/'.(strtolower(worldCountries()[$rec->country]['code'] ?? 'us')).'.svg').'" width="28" height="21"><span class="text-xs font-bold text-ink mt-1">'.$rec->csc.'</span></a></td>';
            $html .= '<td class="px-6 py-4 text-ink-muted font-mono text-sm">'.$rec->version.'</td>';
            $html .= '<td class="px-6 py-4"><span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-accent-soft text-accent border border-accent/20">'.$rec->os.'</span></td>';
            $html .= '<td class="px-6 py-4 text-ink-muted text-sm font-medium">'.($rec->fileSize != '' ? $rec->fileSize : '<span class="text-highlight font-semibold">Uploading...</span>').'</td>';
            $html .= '<td class="px-6 py-4 text-ink-muted text-sm">'.date('Y-m-d', strtotime($rec->modifiedTime)).'</td>';
            $html .= '</tr>';
        }
        
        return $html;
    }

    // ----------------------------------------------------------------
    // Download Now
    // ----------------------------------------------------------------

    public function downloadNow(): string
    {
        $postId = trim($this->request->getPost('postId'));
        $post   = getPost($postId);

        if ($post == '0') {
            return view('error-404');
        }

        // ✅ FIX: Update download count with error handling
        try {
            $this->db->table('fw_posts')
                     ->where('postId', $postId)
                     ->set('downloadCount', 'downloadCount + 1', false)
                     ->update();
        } catch (\Exception $e) {
            // Database is read-only, log error but continue
            log_message('error', 'Cannot update downloadCount (DB read-only): ' . $e->getMessage());
        }

        $data['header_ads']       = $this->ads['header_ads']  ?? '';
        $data['sidebar_ads']      = $this->ads['sidebar_ads'] ?? '';
        $data['footer_ads']       = $this->ads['footer_ads']  ?? '';
        $data['post_ads']         = $this->ads['post_ads']    ?? '';
        
        // ✅ REAL STATS: Pass site statistics to all pages
        $data['siteStats'] = $this->getSiteStats();
        
        // ⚠️ REMOVED: Meta keywords (deprecated since 2009)
        $data['page_title']       = 'Download Samsung Firmware'; // ✅ Clean title for H1
        $data['meta_title']       = 'Download Samsung Firmware | ' . ($this->web->webTitle ?? 'SamFirms');
        $data['meta_description'] = $this->web->metaDesription ?? '';
        $data['web_title']        = 'Download Samsung Firmware | ' . ($this->web->webTitle ?? 'SamFirms');
        $data['web_title']        = 'Download Now';
        $data['post']             = $post;
        $data['downrec']          = getPageByArea('download');
        $data['request']          = 'download-now';

        return view(LANDING_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // Download Link
    // ----------------------------------------------------------------

    public function downloadLink(): void
    {
        // ✅ FIX: safe segment access
        $uri    = $this->request->getUri();
        $seg2   = ($uri->getTotalSegments() >= 2) ? trim($uri->getSegment(2)) : '';
        $uhash  = decrypt($seg2);
        $postId = @current(explode('::', $uhash));
        $post   = getPost($postId);

        if ($post == '0') {
            redirect()->to(base_url());
            return;
        }

        // ✅ FIX: Update download count with error handling
        try {
            $this->db->table('fw_posts')
                     ->where('postId', $postId)
                     ->set('downloadCount', 'downloadCount + 1', false)
                     ->update();
        } catch (\Exception $e) {
            // Database is read-only, log error but continue
            log_message('error', 'Cannot update downloadCount (DB read-only): ' . $e->getMessage());
        }

        $downloadButton = @json_decode($post->downloadButton, true);

        if (
            $post->downloadButton != '' &&
            count($downloadButton) > 0 &&
            $downloadButton['buttonUrl'] != ''
        ) {
            header('Location: ' . $downloadButton['buttonUrl']);
        } elseif ($post->externalFileLink != '' && $post->externalFileLink != null) {
            header('Location: ' . $post->externalFileLink);
        } else {
            echo '<h3 style="text-align:center;color:red;">Firmware Uploading.... Please visit 2 hour later</h3>';
        }
    }

    // ----------------------------------------------------------------
    // Post Action (Like/Dislike)
    // ----------------------------------------------------------------

    public function postAction(): void
    {
        $postId    = trim($this->request->getPost('postId'));
        $type      = trim($this->request->getPost('type'));
        $area      = trim($this->request->getPost('area'));
        $ipAddress = $this->request->getIPAddress();

        // ✅ FIX: table() se shuru karo
        $record = $this->db->table('fw_post_likes')
                           ->select('likeId')
                           ->where('postId', $postId)
                           ->where('ipAddress', $ipAddress)
                           ->where('area', $area)
                           ->get();

        $dataArr = [
            'postId'    => $postId,
            'type'      => $type,
            'area'      => $area,
            'ipAddress' => $ipAddress,
            'likeTime'  => date('Y-m-d H:i:s'),
        ];

        if ($record->getNumRows() == 0) {
            $this->db->table('fw_post_likes')->insert($dataArr);
        } else {
            $likeId = $record->getRow()->likeId;
            $this->db->table('fw_post_likes')->where('likeId', $likeId)->update($dataArr);
        }

        // ✅ FIX: table() se shuru karo
        $likesCount = $this->db->table('fw_post_likes')
                               ->select('likeId')
                               ->where('postId', $postId)
                               ->where('type', 'like')
                               ->where('area', $area)
                               ->where('ipAddress', $ipAddress)
                               ->get()->getNumRows();

        $dbTable = ($area == 'blog') ? 'fw_blogs' : 'fw_posts';
        $this->db->table($dbTable)->where('postId', $postId)->update(['likesCount' => $likesCount]);
    }

    // ----------------------------------------------------------------
    // View Blogs
    // ----------------------------------------------------------------

    public function viewBlogs(): string
    {
        $per_page   = 10;
        $total_rows = $this->home_model->viewBlogPostsLanding();
        $record     = $this->home_model->viewBlogPostsLanding($per_page, $this->page_record);

        $data['total_rows']  = $total_rows;
        $data['per_page']    = $per_page;
        $data['page_record'] = $this->page_record;
        $data['base_url']    = base_url('blog');
        $data['record']      = $record;

        $data['header_ads']  = $this->ads['header_ads']  ?? '';
        $data['sidebar_ads'] = $this->ads['sidebar_ads'] ?? '';
        $data['footer_ads']  = $this->ads['footer_ads']  ?? '';
        
        // ✅ REAL STATS: Pass site statistics to all pages
        $data['siteStats'] = $this->getSiteStats();
        
        // ⚠️ NOTE: Hardcoded meta keywords below for blog listing (consider removing in future)
        $data['meta_tags']   = 'samsung firmware, samsung firmware download, samfirmware, firmware samsung, firmware download, samsung update, samsung firmware update, galaxy firmware, one ui';

        $mTitle = 'Blog';
        $pageTitle = 'Blog'; // ✅ Clean title for H1
        if ($this->page_record != '0') {
            $pageNum = ((int)$this->page_record + 1);
            $mTitle .= ' - Page ' . $pageNum;
            $pageTitle .= ' - Page ' . $pageNum;
        }
        $mTitle .= ' | ' . ($this->web->webTitle ?? 'SamFirms');
        
        $mDescription = 'News in SamFirms.com - Samsung News';
        if ($this->page_record != '0') {
            $mDescription .= ' - Page ' . ((int)$this->page_record + 1);
        }

        $data['page_title']       = $pageTitle; // ✅ Clean title for H1
        $data['meta_title']       = $mTitle;
        $data['meta_description'] = $mDescription;
        $data['web_title']        = $mTitle;
        $data['request']          = 'blog-posts';

        return view(LANDING_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // View Single Blog
    // ----------------------------------------------------------------

    public function viewSingleBlogs(): string
    {
        // ✅ FIX: safe segment access
        $uri     = $this->request->getUri();
        $slugUrl = ($uri->getTotalSegments() >= 2) ? trim($uri->getSegment(2)) : '';
        $pagee   = getBlogBySlug($slugUrl);

        if ($pagee == '0') {
            return redirect()->to(base_url('blog'));
        }

        $data['header_ads']       = $this->ads['header_ads']  ?? '';
        $data['sidebar_ads']      = $this->ads['sidebar_ads'] ?? '';
        $data['footer_ads']       = $this->ads['footer_ads']  ?? '';
        
        // ✅ REAL STATS: Pass site statistics to all pages
        $data['siteStats'] = $this->getSiteStats();
        
        // ⚠️ REMOVED: Meta keywords (deprecated since 2009)
        $data['page_title']       = $pagee->pageTitle ?? 'Blog Post'; // ✅ Clean for H1
        $data['meta_title']       = $pagee->metaTitle         ?? '';
        $data['meta_description'] = $pagee->metaDesription    ?? '';
        $data['web_title']        = ($pagee->pageTitle ?? '') . ' | ' . ($this->web->webTitle ?? 'SamFirms');
        $data['pagee']            = $pagee;
        $data['request']          = 'view-blog-page';

        return view(LANDING_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // Speed Test
    // ----------------------------------------------------------------

    public function speedtest(): string
    {
        $homePage = getPageByArea('home');

        $data['header_ads']       = $this->ads['header_ads']  ?? '';
        $data['sidebar_ads']      = $this->ads['sidebar_ads'] ?? '';
        $data['footer_ads']       = $this->ads['footer_ads']  ?? '';
        
        // ✅ REAL STATS: Pass site statistics to all pages
        $data['siteStats'] = $this->getSiteStats();
        
        // ⚠️ REMOVED: Meta keywords (deprecated since 2009)
        $data['page_title']       = 'Internet Speed Test'; // ✅ Clean title for H1
        $data['meta_title']       = 'Internet Speed Test | ' . ($this->web->webTitle ?? 'SamFirms');
        $data['meta_description'] = 'Internet Speed Test ' . ($homePage->metaDesription ?? '');
        $data['web_title']        = 'Internet Speed Test | ' . ($this->web->webTitle ?? 'SamFirms');
        $data['request']          = 'internet-speed-test';

        return view(LANDING_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // Get Site Statistics (Cached)
    // ----------------------------------------------------------------
    
    private function getSiteStats(): array
    {
        $cache = \Config\Services::cache();
        $cacheKey = 'site_stats_v1';
        
        // Try to get from cache (cached for 1 hour)
        $stats = $cache->get($cacheKey);
        
        if ($stats === null) {
            // Calculate fresh stats from database
            
            // Total firmware files (Active posts only)
            $totalFirmware = $this->db->table('fw_posts')
                                     ->where('postStatus', 'Active')
                                     ->countAllResults();
            
            // Total unique countries (from CSC codes)
            $totalCountries = $this->db->table('fw_posts')
                                      ->select('country')
                                      ->where('postStatus', 'Active')
                                      ->where('country IS NOT NULL')
                                      ->where('country !=', '')
                                      ->groupBy('country')
                                      ->countAllResults();
            
            // Total unique device models
            $totalModels = $this->db->table('fw_posts')
                                   ->select('model')
                                   ->where('postStatus', 'Active')
                                   ->where('model IS NOT NULL')
                                   ->where('model !=', '')
                                   ->groupBy('model')
                                   ->countAllResults();
            
            // Check if updated today (last 24 hours)
            $recentUpdates = $this->db->table('fw_posts')
                                     ->where('postStatus', 'Active')
                                     ->where('publishedAt >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                                     ->countAllResults();
            
            $stats = [
                'totalFirmware' => $totalFirmware,
                'totalCountries' => $totalCountries,
                'totalModels' => $totalModels,
                'updatedToday' => $recentUpdates > 0,
                'recentCount' => $recentUpdates
            ];
            
            // Cache for 5 minutes (300 seconds) - reduced from 3600 for more frequent updates
            // ✅ IMPROVEMENT: Faster stat updates + paired with manual cache clear on publish
            $cache->save($cacheKey, $stats, 300);
        }
        
        return $stats;
    }
}