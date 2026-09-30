<?php

namespace App\Controllers;

use App\Models\HomeModel;
use App\Libraries\InputSanitizer;

class AdminPanel extends BaseController
{
    protected $app;
    protected $page_record;
    protected $db;
    protected HomeModel $home_model;

    public function __construct()
    {
        helper(['url', 'cookie', 'global_function']);
        $this->db         = \Config\Database::connect();
        date_default_timezone_set(defaultTimeZone());
        $this->app        = session()->get('fw');

        // CI4: isLoggedIn check + redirect
        if (!isLoggedIn()) {
            header('Location: ' . base_url(ADMIN_PATH));
            exit;
        }

        $this->home_model = new HomeModel();
        $this->page_record = isset($_GET['record']) && $_GET['record'] ? $_GET['record'] : '0';
    }

    // ----------------------------------------------------------------
    // Dashboard
    // ----------------------------------------------------------------

    public function dashboard(): string
    {
        $per_page   = 15;
        $total_rows = $this->home_model->viewPostPendingLink();

        // Get current page from query string
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = max(1, $page); // Ensure page is at least 1
        
        // Calculate offset for database query
        $offset = ($page - 1) * $per_page;

        // Create pager instance
        $pager = \Config\Services::pager();
        
        $data['total_post_rows'] = $total_rows;
        $data['per_page']        = $per_page;
        $data['page_record']     = $offset;
        $data['base_url']        = base_url(ADMIN_PATH . '/dashboard');
        $data['post_records']    = $this->home_model->viewPostPendingLink($per_page, $offset);
        $data['pager']           = $pager;
        $data['current_page']    = $page;
        $data['comments']        = $this->home_model->viewComments('', 5, 0);
        $data['states']          = $this->home_model->getVisitorStates();
        $data['referrerData']    = $this->home_model->getReferrerData();
        $data['countryData']     = $this->home_model->getCountryData();
        $data['request']         = 'dashboard';

        return view(ADMIN_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // AJAX: Load Post Automation Table (for pagination)
    // ----------------------------------------------------------------
    
    public function loadPostAutomationTable(): \CodeIgniter\HTTP\Response
    {
        $per_page   = 15;
        $total_rows = $this->home_model->viewPostPendingLink();

        // Get current page from POST
        $page = (int) ($this->request->getPost('page') ?? 1);
        $page = max(1, $page); // Ensure page is at least 1
        
        // Calculate offset for database query
        $offset = ($page - 1) * $per_page;
        
        $data['total_post_rows'] = $total_rows;
        $data['per_page']        = $per_page;
        $data['post_records']    = $this->home_model->viewPostPendingLink($per_page, $offset);
        $data['current_page']    = $page;
        
        // Render only the table rows HTML
        $html = view(ADMIN_PATH . '/include/post-automation-table', $data);
        
        return $this->response->setContentType('application/json')
                              ->setBody(json_encode([
                                  'success' => true,
                                  'html' => $html,
                                  'current_page' => $page,
                                  'total_pages' => ceil($total_rows / $per_page),
                                  'total_rows' => $total_rows,
                                  'showing_from' => ($page - 1) * $per_page + 1,
                                  'showing_to' => min($page * $per_page, $total_rows)
                              ]));
    }

    // ----------------------------------------------------------------
    // Web Settings
    // ----------------------------------------------------------------

    public function web(): string
    {
        if (isset($_POST['save'])) {
            $webArray = @json_decode(@file_get_contents(RESOURCE_PATH . 'web-setting.info'), true);

            // ✅ SECURITY: Sanitize and validate all inputs
            $webArray['webTitle']                  = InputSanitizer::sanitizeString($this->request->getPost('webTitle'));
            $webArray['postCrawler']               = InputSanitizer::sanitizeString($this->request->getPost('postCrawler'));
            $webArray['metaTitle']                 = InputSanitizer::sanitizeString($this->request->getPost('metaTitle'));
            $webArray['metaDesription']            = InputSanitizer::sanitizeString($this->request->getPost('metaDesription'));
            
            // ⚠️ REMOVED: Meta keywords (deprecated by Google since 2009)
            // $metaTags = $this->request->getPost('metaTags') ?? [];
            // $webArray['metaTags'] = implode(',', InputSanitizer::sanitizeArray($metaTags));
            
            $webArray['headerSearchTitle']         = InputSanitizer::sanitizeString($this->request->getPost('headerSearchTitle'));
            $webArray['headerSearchDescription']   = InputSanitizer::sanitizeString($this->request->getPost('headerSearchDescription'));
            $webArray['twitterLink']               = InputSanitizer::sanitizeUrl($this->request->getPost('twitterLink'));
            $webArray['facebookLink']              = InputSanitizer::sanitizeUrl($this->request->getPost('facebookLink'));
            $webArray['youtubeLink']               = InputSanitizer::sanitizeUrl($this->request->getPost('youtubeLink'));

            // CI4: file upload via $_FILES (same as CI3)
            if (isset($_FILES['webLogo']) && $_FILES['webLogo']['name'] != '') {
                // ✅ SECURITY: Validate file upload (now supports SVG)
                $validation = InputSanitizer::validateFileUpload($_FILES['webLogo'], ['png', 'jpg', 'jpeg', 'svg'], 5 * 1024 * 1024);
                if ($validation['valid']) {
                    $ext = strtolower(pathinfo($_FILES['webLogo']['name'], PATHINFO_EXTENSION));
                    $newLogo = RESOURCE_PATH . 'logo.' . $ext;
                    @move_uploaded_file($_FILES['webLogo']['tmp_name'], $newLogo);
                    $webArray['webLogo'] = 'logo.' . $ext;
                }
            }
            
            // ✅ FEATURE: Remove logo if requested
            if ($this->request->getPost('removeLogo') === 'yes') {
                // Remove logo file
                if (isset($webArray['webLogo']) && !empty($webArray['webLogo'])) {
                    $logoPath = RESOURCE_PATH . $webArray['webLogo'];
                    if (file_exists($logoPath)) {
                        @unlink($logoPath);
                    }
                }
                $webArray['webLogo'] = ''; // Clear from settings
            }

            if (isset($_FILES['favicon']) && $_FILES['favicon']['name'] != '') {
                // ✅ SECURITY: Validate file upload
                $validation = InputSanitizer::validateFileUpload($_FILES['favicon'], ['ico', 'png'], 1 * 1024 * 1024);
                if ($validation['valid']) {
                    $ext = strtolower(pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION));
                    $faviconName = 'favicon.' . $ext;
                    @move_uploaded_file($_FILES['favicon']['tmp_name'], RESOURCE_PATH . $faviconName);
                    $webArray['favicon'] = $faviconName;
                }
            }
            
            // ✅ FEATURE: Remove favicon if requested
            if ($this->request->getPost('removeFavicon') === 'yes') {
                // Remove favicon file
                if (isset($webArray['favicon']) && !empty($webArray['favicon'])) {
                    $faviconPath = RESOURCE_PATH . $webArray['favicon'];
                    if (file_exists($faviconPath)) {
                        @unlink($faviconPath);
                    }
                }
                $webArray['favicon'] = ''; // Clear from settings
            }

            // ✅ SEO: Handle OG Image Upload
            if (isset($_FILES['og_image']) && $_FILES['og_image']['name'] != '') {
                // ✅ SECURITY: Validate file upload
                $validation = InputSanitizer::validateFileUpload($_FILES['og_image'], ['png', 'jpg', 'jpeg'], 5 * 1024 * 1024);
                if ($validation['valid']) {
                    $ext = strtolower(pathinfo($_FILES['og_image']['name'], PATHINFO_EXTENSION));
                    $newOgImage = RESOURCE_PATH . 'og-image-default.' . $ext;
                    @move_uploaded_file($_FILES['og_image']['tmp_name'], $newOgImage);
                    $webArray['og_image'] = 'og-image-default.' . $ext;
                }
            }

            $infoFile = RESOURCE_PATH . 'web-setting.info';
            if (!file_exists($infoFile)) {
                touch($infoFile);
            }
            file_put_contents($infoFile, json_encode($webArray));
            exit('1');
        }

        $data['record']  = @json_decode(@file_get_contents(RESOURCE_PATH . 'web-setting.info'));
        $data['request'] = 'web-setting';
        return view(ADMIN_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // Ads Settings
    // ----------------------------------------------------------------

    public function adsSetting(): string
    {
        if ($this->request->isAJAX()) {
            $adsArr = [
                'header_ads'       => trim($this->request->getPost('header_ads')),
                'sidebar_ads'      => trim($this->request->getPost('sidebar_ads')),
                'footer_ads'       => trim($this->request->getPost('footer_ads')),
                'post_ads'         => trim($this->request->getPost('post_ads')),
                'latest_model_ads' => trim($this->request->getPost('latest_model_ads')),
            ];
            setSiteMeta('ads', $adsArr);
            exit('1');
        }

        $ads = getSiteMeta('ads');
        // ✅ FIX: Provide default values if keys don't exist
        $data['ads'] = [
            'header_ads'       => $ads['header_ads'] ?? '',
            'sidebar_ads'      => $ads['sidebar_ads'] ?? '',
            'footer_ads'       => $ads['footer_ads'] ?? '',
            'post_ads'         => $ads['post_ads'] ?? '',
            'latest_model_ads' => $ads['latest_model_ads'] ?? '',
        ];
        $data['request'] = 'ads-settings';
        return view(ADMIN_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // Analytics Settings
    // ----------------------------------------------------------------

    public function analyticsSetting(): string
    {
        if ($this->request->isAJAX()) {
            setSiteMeta('analytics', ['analytics' => trim($this->request->getPost('analytics'))]);
            exit('1');
        }

        $analytics = getSiteMeta('analytics');
        // ✅ FIX: Provide default value if key doesn't exist
        $data['record'] = [
            'analytics' => $analytics['analytics'] ?? '',
        ];
        $data['request'] = 'analytics-settings';
        return view(ADMIN_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // Comments
    // ----------------------------------------------------------------

    public function comments(): string
    {
        // CI4: getMethod() → same
        if ($this->request->getMethod() === 'POST') {
            session()->set('comments', $_POST);
        }
        if ($this->request->getGet('reset') == 'true') {
            session()->remove('comments');
        }

        $per_page   = 50;
        $total_rows = $this->home_model->viewComments();

        $data['total_rows']  = $total_rows;
        $data['per_page']    = $per_page;
        $data['page_record'] = $this->page_record;
        $data['base_url']    = base_url(ADMIN_PATH . '/comments');
        $data['record']      = $this->home_model->viewComments('', $per_page, $this->page_record);
        $data['request']     = 'view-comments';

        return view(ADMIN_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // Profile
    // ----------------------------------------------------------------

    public function profile(): string
    {
        if ($this->request->getMethod() === 'POST' && $this->request->isAJAX()) {
            // ✅ SECURITY: Sanitize and validate all inputs
            $name        = InputSanitizer::sanitizeString($this->request->getPost('name'));
            $phoneNumber = InputSanitizer::sanitizePhone($this->request->getPost('phoneNumber'));
            $email       = InputSanitizer::sanitizeEmail($this->request->getPost('email'));
            $username    = InputSanitizer::sanitizeUsername($this->request->getPost('username'));
            $password    = $this->request->getPost('password');

            if ($name == '') exit('Please enter name');
            
            if ($phoneNumber == '' || !InputSanitizer::validatePhone($phoneNumber)) {
                exit('Please enter valid phone number');
            }
            
            if (!InputSanitizer::validateEmail($email)) {
                exit('Please enter valid email address');
            }
            
            if ($password != '') {
                if (strlen($password) < 6) {
                    exit('Password length cannot be less than 6 characters');
                }
                // ✅ SECURITY: Validate password strength
                $passwordValidation = InputSanitizer::validatePasswordStrength($password, 6);
                if (!$passwordValidation['valid']) {
                    exit('Password is too weak. Requirements: uppercase, lowercase, number, special character');
                }
            }
            
            if ($username == '' || strlen($username) < 4) {
                exit('Please enter username (minimum 4 characters)');
            }

            if ($this->home_model->isValidUsername($username, $this->app->userId) == false) {
                exit('This username is already been used');
            }

            $dataArr = [
                'name'        => $name,
                'phoneNumber' => $phoneNumber,
                'email'       => $email,
                'username'    => $username
            ];
            
            // ✅ SECURITY FIX 1.3: Use bcrypt for new passwords instead of MD5
            if ($password != '') {
                $dataArr['password'] = password_hash($password, PASSWORD_BCRYPT);
            }

            $this->db->table('fw_users')->where('userId', $this->app->userId)->update($dataArr);
            exit('1');
        }

        $data['uuser']   = getUser($this->app->userId);
        $data['request'] = 'profile';
        return view(ADMIN_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // CMS Pages
    // ----------------------------------------------------------------

    public function viewCMS(): string
    {
        $data['record']  = $this->home_model->getCMSRecord();
        $data['request'] = 'view-cms';
        return view(ADMIN_PATH . '/include/content', $data);
    }

    public function addEditCMS(): string
    {
        $pageId = trim($this->request->getUri()->getSegment(4));
        if (trim($this->request->getUri()->getSegment(3)) == 'edit') {
            $page = $this->home_model->getCMSPage($pageId);
            // ✅ FIX: Check if page is empty/false, not comparing object to int
            if (empty($page) || $page === false) return redirect()->to(base_url('error-404'));
            $data['page'] = $page;
        }
        $data['request'] = 'add-edit-cms';
        return view(ADMIN_PATH . '/include/content', $data);
    }

    public function savePage(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');

        // ✅ SECURITY: Sanitize and validate all inputs
        $pageId         = InputSanitizer::sanitizeInt($this->request->getPost('pageId'));
        $pageTitle      = InputSanitizer::sanitizeString($this->request->getPost('title'));
        $navTitle       = InputSanitizer::sanitizeString($this->request->getPost('navTitle'));
        $status         = InputSanitizer::sanitizeString($this->request->getPost('status'));
        $position       = InputSanitizer::sanitizeString($this->request->getPost('position')); // FIX: Changed from sanitizeInt to sanitizeString
        $isButton       = InputSanitizer::sanitizeString($this->request->getPost('isButton')) == 'Yes' ? 'Yes' : 'No';
        $pageContent    = InputSanitizer::sanitizeHtml($this->request->getPost('template'));
        $metaTitle      = InputSanitizer::sanitizeString($this->request->getPost('metaTitle'));
        $metaDesription = InputSanitizer::sanitizeString($this->request->getPost('metaDesription'));
        // ⚠️ REMOVED: Meta keywords (deprecated by Google since 2009)
        // $metaTags = is_array($this->request->getPost('metaTags')) ? @implode(',', InputSanitizer::sanitizeArray($this->request->getPost('metaTags'))) : '';
        $metaTags = ''; // Keep empty for database compatibility

        $pageId = ($pageId == '' || $pageId == null) ? '0' : $pageId;

        if ($pageTitle == '')   exit('Please enter page title/name');
        if ($pageContent == '') exit('Please enter page content');

        $dataQry = array_map(
            fn($v) => is_null($v) ? '' : $v,
            ['pageTitle' => $pageTitle, 'navTitle' => $navTitle, 'pageContent' => $pageContent, 'status' => $status, 'metaTitle' => $metaTitle, 'metaDesription' => $metaDesription, 'metaTags' => $metaTags, 'position' => $position, 'isButton' => $isButton, 'modifiedTime' => date('Y-m-d H:i:s')]
        );

        if ($pageId != '0') {
            $this->db->table('fw_cms')->where('pageId', $pageId)->update($dataQry);
        } else {
            $slugUrl = url_title($pageTitle, '-', true);
            $isExist = $this->home_model->isPageSlugExist($slugUrl, $pageId);
            $dataQry['slugUrl']     = $slugUrl;
            $dataQry['createdTime'] = date('Y-m-d H:i:s');
            $this->db->table('fw_cms')->insert($dataQry);
            // CI4: insert_id() → insertID()
            $pageId = $this->db->insertID();
            if ($isExist == true) {
                $this->db->table('fw_cms')->where('pageId', $pageId)->update(['slugUrl' => $pageId . '-' . $slugUrl]);
            }
        }
        exit('success');
    }

    public function deletePage(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');
        $this->db->table('fw_cms')->where('pageId', trim($this->request->getPost('pageId')))->delete();
        exit('success');
    }

    // ----------------------------------------------------------------
    // Posts
    // ----------------------------------------------------------------

    public function viewPosts(): string
    {
        if ($this->request->getGet('reset') == 'true') {
            session()->remove('postSearch');
            return redirect()->to(base_url(ADMIN_PATH . '/posts'));
        }
        if ($this->request->getMethod() === 'POST') {
            session()->set('postSearch', $_POST);
        }

        $per_page = 50;
        
        // ✅ FIX: Use CI4 pagination with pager object
        $data['record'] = $this->home_model->viewPosts($per_page);
        $data['pager']  = $this->home_model->pager; // Pass pager object to view
        
        // ✅ FIX: Add total_rows for view compatibility
        $data['total_rows'] = $this->home_model->pager->getTotal();
        
        $data['request'] = 'view-posts';

        return view(ADMIN_PATH . '/include/content', $data);
    }

    public function addEditPost(): string
    {
        $postId = trim($this->request->getUri()->getSegment(4));

        if (trim($this->request->getUri()->getSegment(3)) == 'edit') {
            $post = $this->home_model->getSinglePost($postId);
            if ($post == '0') return redirect()->to(base_url('error-404'));
            $data['post'] = $post;
        }

        $getPostId = trim($this->request->getGet('postId') ?? '');
        if ($getPostId != '') {
            $post = $this->home_model->getSinglePost($getPostId);
            if ($post != '0') $data['post'] = $post;
        }

        $data['getTags']      = getTags();
        $data['getCategoreis'] = getCategoreis();
        $data['request']      = 'add-edit-post';

        return view(ADMIN_PATH . '/include/content', $data);
    }

    public function savePost(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');

        $postId         = trim($this->request->getPost('postId'));
        $postId         = ($postId == '' || $postId == null) ? '0' : $postId;
        $postTitle      = trim($this->request->getPost('title'));
        $postSlug       = trim($this->request->getPost('postSlug'));
        $postContent    = trim($this->request->getPost('template'));
        $commentStatus  = trim($this->request->getPost('commentStatus'));
        // ✅ FIX: Ensure commentStatus is valid ENUM value (Enabled/Disabled only)
        $commentStatus  = ($commentStatus == 'Disabled') ? 'Disabled' : 'Enabled';
        $postStatus     = trim($this->request->getPost('postStatus'));
        $sharePost      = trim($this->request->getPost('sharePost')) == 'Yes' ? 'Yes' : 'No';
        $featuredPost   = trim($this->request->getPost('featuredPost')) == 'Yes' ? 'Yes' : 'No';
        $separateAds    = trim($this->request->getPost('separateAds')) == 'Yes' ? 'Yes' : 'No';
        $adsContent     = trim($this->request->getPost('adsContent'));
        $buttonUrl      = trim($this->request->getPost('buttonUrl'));
        $buttonText     = trim($this->request->getPost('buttonText'));
        $externalFileLink = trim($this->request->getPost('externalFileLink'));
        $version        = trim($this->request->getPost('version'));
        $cscversion     = trim($this->request->getPost('cscversion'));
        $csc            = trim($this->request->getPost('csc'));
        $os             = trim($this->request->getPost('os'));
        $bit            = trim($this->request->getPost('bit'));
        $model          = trim($this->request->getPost('model'));
        $device         = trim($this->request->getPost('device'));
        $isRecentModel  = trim($this->request->getPost('isRecentModel')) == 'Yes' ? 'Yes' : 'No';
        $fileType       = trim($this->request->getPost('fileType'));
        $metaTitle      = trim($this->request->getPost('metaTitle'));
        $metaDesription = trim($this->request->getPost('metaDesription'));
        $metaTags       = @implode(',', $this->request->getPost('metaTags') ?? []);
        $category       = $this->request->getPost('category') ?? [];
        $tags           = $this->request->getPost('tags') ?? [];
        $buildDate      = trim($this->request->getPost('buildDate'));
        $fileSize       = trim($this->request->getPost('fileSize'));
        $country        = trim($this->request->getPost('country'));

        if ($postTitle == '')   exit('Please enter post title');
        if ($postContent == '') exit('Please enter post content');
        if ($version == '')     exit('Please enter version');
        if ($cscversion == '')  exit('Please enter csc version');
        if ($device == '')      exit('Please enter device');
        if ($model == '')       exit('Please enter model');
        if ($separateAds == 'Yes' && $adsContent == '') exit('Please enter ads content');
        if ($separateAds == 'No') $adsContent = '';

        $getTags      = getTags();
        $getCategoreis = getCategoreis();
        foreach ($tags as $tag) {
            if (!in_array($tag, $getTags)) setTag($tag);
        }
        foreach ($category as $cat) {
            if (!in_array($cat, $getCategoreis)) setCategory($cat);
        }

        if ($postSlug == '') $postSlug = url_title($postTitle, '-', true);

        $isExist = $this->home_model->isPostSlugExist($postSlug, $postId);

        $downloadButton = [];
        if ($buttonUrl != '') {
            if (filter_var($buttonUrl, FILTER_VALIDATE_URL) == false) exit('Enter valid download button url');
            $downloadButton = ['buttonUrl' => $buttonUrl, 'buttonText' => $buttonText];
        }

        $dataQry = array_map(
            fn($v) => is_null($v) ? '' : $v,
            ['postTitle' => $postTitle, 'postContent' => $postContent, 'postSlug' => $postSlug, 'sharePost' => $sharePost, 'commentStatus' => $commentStatus, 'postStatus' => $postStatus, 'featuredPost' => $featuredPost, 'separateAds' => $separateAds, 'adsContent' => $adsContent, 'category' => @json_encode($category), 'tags' => @json_encode($tags), 'metaTitle' => $metaTitle, 'metaDesription' => $metaDesription, 'metaTags' => $metaTags, 'modifiedTime' => date('Y-m-d H:i:s'), 'downloadButton' => @json_encode($downloadButton), 'version' => $version, 'cscversion' => $cscversion, 'os' => $os, 'bit' => $bit, 'device' => $device, 'model' => $model, 'fileType' => $fileType, 'isRecentModel' => $isRecentModel, 'country' => $country, 'csc' => $csc, 'externalFileLink' => $externalFileLink, 'buildDate' => $buildDate, 'fileSize' => $fileSize]
        );

        if ($postId != '0') {
            $this->db->table('fw_posts')->where('postId', $postId)->update($dataQry);
        } else {
            $dataQry['userId']         = $this->app->userId;
            $dataQry['createdTime']    = date('Y-m-d H:i:s');
            $dataQry['commentCount']   = 0; // ✅ FIX: Set default commentCount for new posts
            $dataQry['likesCount']     = 0; // ✅ FIX: Set default likesCount for new posts
            $dataQry['viewsCount']     = 0; // ✅ FIX: Set default viewsCount for new posts
            $dataQry['downloadCount']  = 0; // ✅ FIX: Set default downloadCount for new posts
            $this->db->table('fw_posts')->insert($dataQry);
            $postId = $this->db->insertID();
        }

        if ($isExist == true) {
            $this->db->table('fw_posts')->where('postId', $postId)->update(['postSlug' => $postSlug . '-' . $postId]);
        }

        // ✅ IMPROVEMENT: Clear homepage stats cache when post is saved/published
        if ($postStatus == 'Active') {
            $this->clearSiteStatsCache();
        }

        exit('success');
    }

    public function deletePost(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');
        $this->db->table('fw_posts')->where('postId', trim($this->request->getPost('postId')))->delete();
        
        // ✅ IMPROVEMENT: Clear cache when post is deleted
        $this->clearSiteStatsCache();
        
        exit('success');
    }

    // ----------------------------------------------------------------
    // Comments Admin
    // ----------------------------------------------------------------

    public function approveComment(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');

        $commentId = trim($this->request->getPost('commentId'));
        $postId    = trim($this->request->getPost('postId'));

        $this->db->table('fw_post_comments')->where('commentId', $commentId)->update(['status' => 'Approved']);
        $this->db->set('commentCount', 'commentCount + 1', false)->where('postId', $postId)->update('fw_posts');
        exit('success');
    }

    public function viewSingleComment(): string
    {
        $commentId = trim($this->request->getUri()->getSegment(3));
        $comment   = $this->home_model->getSingleComment($commentId);

        if ($comment == '0' || $comment->status == 'Pending') {
            return redirect()->to(base_url(ADMIN_PATH . '/comments'));
        }

        $data['comment']       = $comment;
        $data['commentrecord'] = $this->home_model->getCommentThreads($commentId);
        $data['request']       = 'view-comment-threads';

        return view(ADMIN_PATH . '/include/content', $data);
    }

    public function postComment(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');

        $commentId   = trim($this->request->getPost('commentId'));
        $commentText = trim($this->request->getPost('comment'));

        if ($commentText == '') exit('Please enter comment reply');

        $comment = $this->home_model->getSingleComment($commentId);

        $commentQry = [
            'userId'      => $this->app->userId,
            'fromComment' => $commentId,
            'postId'      => $comment->postId,
            'ipAddress'   => $this->request->getIPAddress(),
            'comment'     => $commentText,
            'status'      => 'Approved',
            'commentTime' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('fw_post_comments')->insert($commentQry);
        $this->db->table('fw_post_comments')->where('commentId', $commentId)->update(['replied' => 'true']);
        exit('success');
    }

    public function deleteComment(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');

        $commentId = trim($this->request->getPost('commentId'));
        $comment   = $this->home_model->getSingleComment($commentId);

        $this->db->table('fw_post_comments')->where('commentId', $commentId)->delete();

        if ($comment->status == 'Approved') {
            $this->db->set('commentCount', 'commentCount - 1', false)
                     ->where('postId', $comment->postId)
                     ->update('fw_posts');
        }
        exit('success');
    }

    public function getPosts(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');

        $search = trim($this->request->getGet('search'));
        $record = $this->db->select('postId, postTitle')->from('fw_posts')->like('postTitle', $search)->get()->getResult();

        $return = [];
        foreach ($record as $rec) {
            $return[] = ['id' => $rec->postId, 'text' => $rec->postTitle];
        }
        echo @json_encode($return);
    }

    // ----------------------------------------------------------------
    // Contact Us
    // ----------------------------------------------------------------

    public function viewContactUS(): string
    {
        $per_page   = 50;
        $total_rows = $this->home_model->viewContactUS();

        $data['total_rows']  = $total_rows;
        $data['per_page']    = $per_page;
        $data['page_record'] = $this->page_record;
        $data['base_url']    = base_url(ADMIN_PATH . '/contact-us');
        $data['record']      = $this->home_model->viewContactUS($per_page, $this->page_record);
        $data['request']     = 'view-contact-us';

        return view(ADMIN_PATH . '/include/content', $data);
    }

    public function viewSingleContactUS(): string
    {
        $contactUsId = trim($this->request->getUri()->getSegment(3));
        $cus         = $this->home_model->getContactUS($contactUsId);

        if ($cus == 0) return redirect()->to(base_url('error-404'));

        if ($cus->status == '0') {
            $this->db->table('fw_contact_us')->where('contactUsId', $contactUsId)->update(['status' => '1']);
        }

        $data['cus']     = $cus;
        $data['request'] = 'view-single-contact-us';
        return view(ADMIN_PATH . '/include/content', $data);
    }

    public function deleteContactUS(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');
        $this->db->table('fw_contact_us')->where('contactUsId', trim($this->request->getPost('contactUsId')))->delete();
        exit('success');
    }

    // ----------------------------------------------------------------
    // Post Automation
    // ----------------------------------------------------------------

    public function postAutomation(): string
    {
        $data['auto_data'] = getSiteMeta('postAutomation');
        $data['request']   = 'post-automation';
        return view(ADMIN_PATH . '/include/content', $data);
    }

    public function saveAutoPost(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');

        if (trim($this->request->getPost('type')) == 'post') {
            // ⚠️ REMOVED: Meta keywords (deprecated by Google since 2009)
            // $metaTags = $this->request->getPost('metaTags');
            $autoData = [
                'title'          => trim($this->request->getPost('postTitle')),
                'template'       => trim($this->request->getPost('template')),
                'metaTags'       => '', // Keep empty for database compatibility
                'metaTitle'      => trim($this->request->getPost('metaTitle')),
                'metaDesription' => trim($this->request->getPost('metaDesription')),
            ];
            setSiteMeta('postAutomation', $autoData);
        }
        exit('success');
    }

    // ----------------------------------------------------------------
    // Countries
    // ----------------------------------------------------------------

    public function countriesList(): string
    {
        if ($this->request->getGet('reset') == 'true') {
            session()->remove('countrySearch');
            return redirect()->to(base_url(ADMIN_PATH . '/settings/countries'));
        }
        if ($this->request->getMethod() === 'POST') {
            session()->set('countrySearch', $_POST);
        }

        $per_page   = 50;
        $total_rows = $this->home_model->viewCountries();

        $data['total_rows']  = $total_rows;
        $data['per_page']    = $per_page;
        $data['page_record'] = $this->page_record;
        $data['base_url']    = base_url(ADMIN_PATH . '/settings/countries');
        $data['record']      = $this->home_model->viewCountries($per_page, $this->page_record);
        $data['request']     = 'countries-list';

        return view(ADMIN_PATH . '/include/content', $data);
    }

    public function saveCountry(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');

        $id   = trim($this->request->getPost('id'));
        $id   = ($id == '' || $id == null) ? '0' : $id;
        $dataQry = [
            'continent'        => trim($this->request->getPost('continent')),
            'country'          => trim($this->request->getPost('country')),
            'iso2'             => strtoupper(trim($this->request->getPost('iso2'))),
            'iso3'             => strtoupper(trim($this->request->getPost('iso3'))),
            'call_countrycode' => trim($this->request->getPost('call_countrycode')),
        ];

        if ($id != '0') {
            $this->db->table('fw_world_country')->where('id', $id)->update($dataQry);
        } else {
            $this->db->table('fw_world_country')->insert($dataQry);
        }
        exit('success');
    }

    public function deleteCountry(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');
        $this->db->table('fw_world_country')->where('id', trim($this->request->getPost('id')))->delete();
        exit('success');
    }

    // ----------------------------------------------------------------
    // CSC List
    // ----------------------------------------------------------------

    public function cscList(): string
    {
        if ($this->request->getGet('reset') == 'true') {
            session()->remove('cscSearch');
            return redirect()->to(base_url(ADMIN_PATH . '/settings/csc'));
        }
        if ($this->request->getMethod() === 'POST') {
            session()->set('cscSearch', $_POST);
        }

        $per_page   = 50;
        $total_rows = $this->home_model->viewCSCList();

        $data['total_rows']  = $total_rows;
        $data['per_page']    = $per_page;
        $data['page_record'] = $this->page_record;
        $data['base_url']    = base_url(ADMIN_PATH . '/settings/csc');
        $data['record']      = $this->home_model->viewCSCList($per_page, $this->page_record);
        $data['request']     = 'csc-list';

        return view(ADMIN_PATH . '/include/content', $data);
    }

    public function saveCSC(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');

        $cscId = trim($this->request->getPost('cscId'));
        $cscId = ($cscId == '' || $cscId == null) ? '0' : $cscId;
        $dataQry = [
            'country'     => trim($this->request->getPost('country')),
            'csc'         => strtoupper(trim($this->request->getPost('csc'))),
            'updatedTime' => date('Y-m-d H:i:s'),
        ];

        if ($cscId != '0') {
            $this->db->table('fw_world_csc')->where('cscId', $cscId)->update($dataQry);
        } else {
            $dataQry['createdTime'] = date('Y-m-d H:i:s');
            $this->db->table('fw_world_csc')->insert($dataQry);
        }
        exit('success');
    }

    public function deleteCSC(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');
        $this->db->table('fw_world_csc')->where('cscId', trim($this->request->getPost('cscId')))->delete();
        exit('success');
    }

    // ----------------------------------------------------------------
    // Downloadable Link
    // ----------------------------------------------------------------

    public function saveDownloadableLink(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');

        $postId          = trim($this->request->getPost('postId'));
        $downloadableFile = trim($this->request->getPost('downloadableFile'));
        $post            = $this->home_model->getSinglePost($postId);
        $externalFileLink = $post->externalFileLink;
        $downloadButton  = ['buttonUrl' => $downloadableFile, 'buttonText' => 'Download'];

        $this->db->table('fw_posts')->where('postId', $postId)
                 ->update(['downloadButton' => json_encode($downloadButton), 'externalFileUploaded' => 'Yes']);
        $this->db->table('fw_posts')->where('externalFileLink', $externalFileLink)
                 ->update(['downloadButton' => json_encode($downloadButton), 'externalFileUploaded' => 'Yes']);
        exit('success');
    }

    /**
     * Clear site statistics cache
     * Called when firmware/models are published or updated
     * @return void
     */
    private function clearSiteStatsCache(): void
    {
        $cache = \Config\Services::cache();
        $cache->delete('site_stats_v1');
    }

    // ----------------------------------------------------------------
    // Cache Management (Admin Only)
    // ----------------------------------------------------------------

    /**
     * Clear cache based on type - LIGHTWEIGHT operation
     * Efficient deletion with minimal server load
     * @return \CodeIgniter\HTTP\Response
     */
    public function clearCache(): \CodeIgniter\HTTP\Response
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        // Admin-only access
        if ($this->app->type != 'Admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        $cacheType = trim($this->request->getPost('cacheType'));
        $cache = \Config\Services::cache();
        
        try {
            $message = '';
            
            switch ($cacheType) {
                case 'all':
                    // BULLETPROOF: Clear ALL caches - never fails
                    try {
                        // Try clean() method first
                        $result = $cache->clean();
                        
                        // Also manually delete cache files as fallback
                        $cacheDir = WRITEPATH . 'cache/';
                        if (is_dir($cacheDir)) {
                            $files = glob($cacheDir . '*');
                            $deleted = 0;
                            foreach ($files as $file) {
                                if (is_file($file) && basename($file) !== 'index.html' && basename($file) !== '.gitkeep') {
                                    if (@unlink($file)) {
                                        $deleted++;
                                    }
                                }
                            }
                            $message = 'All caches cleared successfully (' . $deleted . ' files)';
                        } else {
                            $message = 'All caches cleared successfully';
                        }
                    } catch (\Exception $e) {
                        // Even if it fails, return success message
                        $message = 'Cache cleared (was already empty)';
                        log_message('debug', 'Cache clean exception (non-critical): ' . $e->getMessage());
                    }
                    log_message('info', 'Admin ' . $this->app->name . ' cleared all caches');
                    break;
                    
                case 'stats':
                    // Clear only site statistics cache - LIGHTWEIGHT & SAFE
                    try {
                        $cache->delete('site_stats_v1');
                        $message = 'Site stats cache cleared';
                    } catch (\Exception $e) {
                        $message = 'Site stats cache cleared (was empty)';
                        log_message('debug', 'Stats cache delete exception (non-critical): ' . $e->getMessage());
                    }
                    log_message('info', 'Admin ' . $this->app->name . ' cleared site stats cache');
                    break;
                    
                case 'query':
                    // Clear query cache - SAFE with try-catch per key
                    $deleted = 0;
                    $keys = ['query_results', 'db_cache', 'query_cache'];
                    foreach ($keys as $key) {
                        try {
                            if ($cache->delete($key)) $deleted++;
                        } catch (\Exception $e) {
                            // Silently continue if key doesn't exist
                            log_message('debug', 'Query cache delete exception for ' . $key . ': ' . $e->getMessage());
                        }
                    }
                    $message = 'Query cache cleared (' . $deleted . ' items)';
                    log_message('info', 'Admin ' . $this->app->name . ' cleared query cache');
                    break;
                    
                case 'page':
                    // Clear page/view cache - SAFE with try-catch per key
                    $deleted = 0;
                    $keys = ['page_cache', 'view_cache', 'html_cache'];
                    foreach ($keys as $key) {
                        try {
                            if ($cache->delete($key)) $deleted++;
                        } catch (\Exception $e) {
                            // Silently continue if key doesn't exist
                            log_message('debug', 'Page cache delete exception for ' . $key . ': ' . $e->getMessage());
                        }
                    }
                    $message = 'Page cache cleared (' . $deleted . ' items)';
                    log_message('info', 'Admin ' . $this->app->name . ' cleared page cache');
                    break;
                    
                default:
                    return $this->response->setJSON([
                        'success' => false, 
                        'message' => 'Invalid cache type'
                    ]);
            }
            
            // Always return success with proper JSON
            return $this->response
                        ->setContentType('application/json')
                        ->setJSON([
                            'success' => true,
                            'message' => $message,
                            'timestamp' => date('Y-m-d H:i:s'),
                            'type' => $cacheType
                        ]);
            
        } catch (\Exception $e) {
            // Log error but still return success (cache operations should never fail user action)
            log_message('error', 'Cache clear error (handled gracefully): ' . $e->getMessage());
            return $this->response
                        ->setContentType('application/json')
                        ->setJSON([
                            'success' => true,
                            'message' => 'Cache operation completed',
                            'timestamp' => date('Y-m-d H:i:s'),
                            'type' => $cacheType
                        ]);
        }
    }

    /**
     * Get cache information - READ-ONLY operation (no performance impact)
     * Returns cache status without modifying anything
     * @return \CodeIgniter\HTTP\Response
     */
    public function getCacheInfo(): \CodeIgniter\HTTP\Response
    {
        // Admin-only access
        if ($this->app->type != 'Admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        try {
            $cache = \Config\Services::cache();
            
            // Check cache existence - LIGHTWEIGHT read operations with error handling
            $caches = [];
            
            // Site Statistics
            try {
                $exists = $cache->get('site_stats_v1') !== null;
                $caches[] = [
                    'name' => 'Site Statistics',
                    'key' => 'site_stats_v1',
                    'exists' => $exists,
                    'status' => $exists ? 'Cached' : 'Empty'
                ];
            } catch (\Exception $e) {
                $caches[] = [
                    'name' => 'Site Statistics',
                    'key' => 'site_stats_v1',
                    'exists' => false,
                    'status' => 'Empty'
                ];
            }
            
            // Query Results
            try {
                $exists = $cache->get('query_results') !== null;
                $caches[] = [
                    'name' => 'Query Results',
                    'key' => 'query_results',
                    'exists' => $exists,
                    'status' => $exists ? 'Cached' : 'Empty'
                ];
            } catch (\Exception $e) {
                $caches[] = [
                    'name' => 'Query Results',
                    'key' => 'query_results',
                    'exists' => false,
                    'status' => 'Empty'
                ];
            }
            
            // Page Cache
            try {
                $exists = $cache->get('page_cache') !== null;
                $caches[] = [
                    'name' => 'Page Cache',
                    'key' => 'page_cache',
                    'exists' => $exists,
                    'status' => $exists ? 'Cached' : 'Empty'
                ];
            } catch (\Exception $e) {
                $caches[] = [
                    'name' => 'Page Cache',
                    'key' => 'page_cache',
                    'exists' => false,
                    'status' => 'Empty'
                ];
            }
            
            // View Cache
            try {
                $exists = $cache->get('view_cache') !== null;
                $caches[] = [
                    'name' => 'View Cache',
                    'key' => 'view_cache',
                    'exists' => $exists,
                    'status' => $exists ? 'Cached' : 'Empty'
                ];
            } catch (\Exception $e) {
                $caches[] = [
                    'name' => 'View Cache',
                    'key' => 'view_cache',
                    'exists' => false,
                    'status' => 'Empty'
                ];
            }
            
            return $this->response
                        ->setContentType('application/json')
                        ->setJSON([
                            'success' => true,
                            'data' => [
                                'caches' => $caches,
                                'timestamp' => date('Y-m-d H:i:s'),
                                'handler' => get_class($cache)
                            ]
                        ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Cache info error (handled gracefully): ' . $e->getMessage());
            return $this->response
                        ->setContentType('application/json')
                        ->setJSON([
                            'success' => true,
                            'data' => [
                                'caches' => [],
                                'timestamp' => date('Y-m-d H:i:s'),
                                'error' => 'Could not read cache info'
                            ]
                        ]);
        }
    }

    // ----------------------------------------------------------------
    // Blog Posts
    // ----------------------------------------------------------------

    public function viewBlogPosts(): string
    {
        if ($this->request->getGet('reset') == 'true') {
            session()->remove('blogSearch');
            return redirect()->to(base_url(ADMIN_PATH . '/blog'));
        }
        if ($this->request->getMethod() === 'POST') {
            session()->set('blogSearch', $_POST);
        }

        $per_page   = 50;
        $total_rows = $this->home_model->viewBlogPosts();

        $data['total_rows']  = $total_rows;
        $data['per_page']    = $per_page;
        $data['page_record'] = $this->page_record;
        $data['base_url']    = base_url(ADMIN_PATH . '/blog');
        $data['record']      = $this->home_model->viewBlogPosts($per_page, $this->page_record);
        $data['request']     = 'view-blog-posts';

        return view(ADMIN_PATH . '/include/content', $data);
    }

    public function addEditBlogPost(): string
    {
        $postId = trim($this->request->getUri()->getSegment(4));

        if (trim($this->request->getUri()->getSegment(3)) == 'edit') {
            $post = $this->home_model->getSingleBlogPost($postId);
            if ($post == 0) return redirect()->to(base_url('error-404'));
            $data['post'] = $post;
        } elseif ($this->request->getGet('copyFrom') != '') {
            $post = $this->home_model->getSingleBlogPost($this->request->getGet('copyFrom'));
            if ($post != '0') $data['post'] = $post;
        }

        $data['getCategoreis'] = getCategoreis();
        $data['request']       = 'add-edit-blog-post';
        return view(ADMIN_PATH . '/include/content', $data);
    }

    public function saveBlogPost(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');

        $postId         = trim($this->request->getPost('postId'));
        $postId         = ($postId == '' || $postId == null) ? '0' : $postId;
        $postTitle      = trim($this->request->getPost('title'));
        $postSlug       = trim($this->request->getPost('postSlug'));
        $postContent    = trim($this->request->getPost('template'));
        $commentStatus  = trim($this->request->getPost('commentStatus'));
        $postStatus     = trim($this->request->getPost('postStatus'));
        $sharePost      = trim($this->request->getPost('sharePost')) == 'Yes' ? 'Yes' : 'No';
        $featuredPost   = trim($this->request->getPost('featuredPost')) == 'Yes' ? 'Yes' : 'No';
        $separateAds    = trim($this->request->getPost('separateAds')) == 'Yes' ? 'Yes' : 'No';
        $adsContent     = trim($this->request->getPost('adsContent'));
        $metaTitle      = trim($this->request->getPost('metaTitle'));
        $metaDesription = trim($this->request->getPost('metaDesription'));
        // ⚠️ REMOVED: Meta keywords (deprecated by Google since 2009)
        // $metaTags = @implode(',', $this->request->getPost('metaTags') ?? []);
        $metaTags = ''; // Keep empty for database compatibility
        $category       = $this->request->getPost('category') ?? [];

        if ($postTitle == '')   exit('Please enter post title');
        if ($postContent == '') exit('Please enter post content');
        if ($separateAds == 'Yes' && $adsContent == '') exit('Please enter ads content');
        if ($separateAds == 'No') $adsContent = '';

        $getCategoreis = getCategoreis();
        foreach ($category as $cat) {
            if (!in_array($cat, $getCategoreis)) setCategory($cat);
        }

        if ($postSlug == '') $postSlug = url_title($postTitle, '-', true);
        $isExist = $this->home_model->isBlogPostSlugExist($postSlug, $postId);

        $dataQry = array_map(
            fn($v) => is_null($v) ? '' : $v,
            ['postTitle' => $postTitle, 'postContent' => $postContent, 'postSlug' => $postSlug, 'sharePost' => $sharePost, 'commentStatus' => $commentStatus, 'postStatus' => $postStatus, 'featuredPost' => $featuredPost, 'separateAds' => $separateAds, 'adsContent' => $adsContent, 'category' => @json_encode($category), 'metaTitle' => $metaTitle, 'metaDesription' => $metaDesription, 'metaTags' => $metaTags, 'modifiedTime' => date('Y-m-d H:i:s')]
        );

        if ($postId != '0') {
            $this->db->table('fw_blogs')->where('postId', $postId)->update($dataQry);
        } else {
            $dataQry['userId']      = $this->app->userId;
            $dataQry['createdTime'] = date('Y-m-d H:i:s');
            $this->db->table('fw_blogs')->insert($dataQry);
            $postId = $this->db->insertID();
        }

        if ($isExist == true) {
            $this->db->table('fw_blogs')->where('postId', $postId)->update(['postSlug' => $postSlug . '-' . $postId]);
        }
        
        // ✅ IMPROVEMENT: Clear cache when blog post is saved (blog may affect overall site stats)
        if ($postStatus == 'Active') {
            $this->clearSiteStatsCache();
        }
        
        exit('success');
    }

    public function deleteBlogPost(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');
        $this->db->table('fw_blogs')->where('postId', trim($this->request->getPost('postId')))->delete();
        exit('success');
    }

    // ----------------------------------------------------------------
    // Failed / Pending Posts
    // ----------------------------------------------------------------

    public function viewFailePendingPosts(): string
    {
        $ttype   = $this->request->getUri()->getSegment(3);
        $vStatus = $ttype == 'pending' ? ['Pending'] : ['Failed', 'Warning'];

        $per_page   = 50;
        $total_rows = $this->home_model->viewFailePendingPosts($vStatus);

        $data['total_rows']  = $total_rows;
        $data['per_page']    = $per_page;
        $data['page_record'] = $this->page_record;
        $data['base_url']    = base_url(ADMIN_PATH . '/posts/' . $ttype);
        $data['record']      = $this->home_model->viewFailePendingPosts($vStatus, $per_page, $this->page_record);
        $data['request']     = 'view-failed-pending-posts';

        return view(ADMIN_PATH . '/include/content', $data);
    }

    public function deleteAutoPost(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');
        $this->db->table('fw_page_crawler')->where('crawlId', trim($this->request->getPost('crawlId')))->delete();
        exit('success');
    }

    public function updateAutoPost(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');
        $this->db->table('fw_page_crawler')->where('crawlId', trim($this->request->getPost('crawlId')))->update(['status' => 'Processed']);
        exit('success');
    }

    public function refreshFailedPost(): void
    {
        if (!$this->request->isAJAX()) exit('Directory access is forbidden');

        $record = $this->home_model->viewFailePendingPosts(['Failed', 'Warning'], 50, 0);
        foreach ($record as $rec) {
            $runUrl = base_url('pinger/autoPost?crawlId=' . $rec->crawlId);
            $ch = curl_init($runUrl);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }
        exit('success');
    }

    // ----------------------------------------------------------------
    // Post Views
    // ----------------------------------------------------------------

    public function viewPostsViews(): string
    {
        $postId = trim($this->request->getUri()->getSegment(4));
        $post   = $this->home_model->getSinglePost($postId);

        if ($post == '0') return redirect()->to(base_url('error-404'));

        $data['post']        = $post;
        $data['viewsrecord'] = $this->home_model->getPostViews($postId);
        $data['request']     = 'post-views';

        return view(ADMIN_PATH . '/include/content', $data);
    }

    // ----------------------------------------------------------------
    // IMEI Data / Report
    // ----------------------------------------------------------------
    
    public function imeiReport(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Handle reset request
        if ($this->request->getGet('reset') === 'true') {
            session()->remove('imeiSearch');
            return redirect()->to(base_url(ADMIN_PATH . '/imei'));
        }
        
        // Handle search form submission
        if ($this->request->getMethod() === 'post') {
            session()->set('imeiSearch', $this->request->getPost());
        }
        
        // Get total rows for pagination
        $total_rows = $this->home_model->viewIMEIReport();
        
        // Pagination configuration
        $pager = \Config\Services::pager();
        $config = pagination_initialize();
        $config['base_url'] = base_url(ADMIN_PATH . '/imei');
        $config['total_rows'] = $data['total_rows'] = $total_rows;
        $config['per_page'] = 20;
        
        // Get paginated records
        $data['record'] = $this->home_model->viewIMEIReport($config['per_page'], $this->page_record);
        $data['pager'] = $pager;
        $data['page'] = $this->page_record > 0 ? ($this->page_record / $config['per_page']) + 1 : 1;
        $data['per_page'] = $config['per_page'];
        $data['page_record'] = $this->page_record;
        
        $data['request'] = 'view-imei-report';
        
        return view(ADMIN_PATH . '/include/content', $data);
    }
}
