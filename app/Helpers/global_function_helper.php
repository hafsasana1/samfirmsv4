<?php

/**
 * global_function_helper.php — CI4 Fixed Version
 * Save to: SAMPRO/app/Helpers/global_function_helper.php
 *
 * KEY FIX: CI4 mein db_connect() ke baad ->select()->from() kaam nahi karta
 * Sahi tarika: $db->table('tablename')->select('...')->where()->get()
 *
 * URI FIX: $request->uri protected hai CI4 new versions mein
 * Sahi tarika: getTotalSegments() check karo pehle, phir getSegment() call karo
 */

// ============================================================
// PAGINATION
// ============================================================

if (!function_exists('pagination_initialize')) {
    function pagination_initialize() {
        $config['first_link']           = 'First';
        $config['last_link']            = 'Last';
        $config['page_query_string']    = true;
        $config['query_string_segment'] = 'record';
        $config['num_tag_open']         = '<li>';
        $config['num_tag_close']        = '</li>';
        $config['next_tag_open']        = '<li>';
        $config['next_tag_close']       = '</li>';
        $config['prev_tag_open']        = '<li>';
        $config['prev_tag_close']       = '</li>';
        $config['cur_tag_open']         = '<li class="active"><a href="javascript:;">';
        $config['cur_tag_close']        = '</a></li>';
        $config['first_tag_open']       = '<li>';
        $config['first_tag_close']      = '</li>';
        $config['last_tag_open']        = '<li>';
        $config['last_tag_close']       = '</li>';
        $config['num_links']            = 5;
        return $config;
    }
}

// ============================================================
// TIMEZONES
// ============================================================

if (!function_exists('time_zones')) {
    function time_zones() {
        return array(
            'Africa/Abidjan','Africa/Accra','Africa/Addis_Ababa','Africa/Algiers','Africa/Asmara',
            'Africa/Bamako','Africa/Bangui','Africa/Banjul','Africa/Bissau','Africa/Blantyre',
            'Africa/Brazzaville','Africa/Bujumbura','Africa/Cairo','Africa/Casablanca','Africa/Ceuta',
            'Africa/Conakry','Africa/Dakar','Africa/Dar_es_Salaam','Africa/Djibouti','Africa/Douala',
            'Africa/El_Aaiun','Africa/Freetown','Africa/Gaborone','Africa/Harare','Africa/Johannesburg',
            'Africa/Juba','Africa/Kampala','Africa/Khartoum','Africa/Kigali','Africa/Kinshasa',
            'Africa/Lagos','Africa/Libreville','Africa/Lome','Africa/Luanda','Africa/Lubumbashi',
            'Africa/Lusaka','Africa/Malabo','Africa/Maputo','Africa/Maseru','Africa/Mbabane',
            'Africa/Mogadishu','Africa/Monrovia','Africa/Nairobi','Africa/Ndjamena','Africa/Niamey',
            'Africa/Nouakchott','Africa/Ouagadougou','Africa/Porto-Novo','Africa/Sao_Tome',
            'Africa/Tripoli','Africa/Tunis','Africa/Windhoek',
            'America/Adak','America/Anchorage','America/Anguilla','America/Antigua','America/Araguaina',
            'America/Argentina/Buenos_Aires','America/Bogota','America/Boise','America/Chicago',
            'America/Denver','America/Detroit','America/Edmonton','America/Halifax','America/Havana',
            'America/Los_Angeles','America/Mexico_City','America/New_York','America/Phoenix',
            'America/Santiago','America/Sao_Paulo','America/Toronto','America/Vancouver',
            'America/Winnipeg',
            'Asia/Aden','Asia/Almaty','Asia/Amman','Asia/Baghdad','Asia/Bahrain','Asia/Baku',
            'Asia/Bangkok','Asia/Beirut','Asia/Colombo','Asia/Damascus','Asia/Dhaka','Asia/Dubai',
            'Asia/Ho_Chi_Minh','Asia/Hong_Kong','Asia/Istanbul','Asia/Jakarta','Asia/Jerusalem',
            'Asia/Kabul','Asia/Karachi','Asia/Kathmandu','Asia/Kolkata','Asia/Kuala_Lumpur',
            'Asia/Kuwait','Asia/Manila','Asia/Muscat','Asia/Qatar','Asia/Riyadh','Asia/Seoul',
            'Asia/Shanghai','Asia/Singapore','Asia/Taipei','Asia/Tashkent','Asia/Tehran','Asia/Tokyo',
            'Asia/Ulaanbaatar','Asia/Yerevan',
            'Atlantic/Azores','Atlantic/Bermuda','Atlantic/Canary','Atlantic/Cape_Verde',
            'Australia/Adelaide','Australia/Brisbane','Australia/Darwin','Australia/Melbourne',
            'Australia/Perth','Australia/Sydney',
            'Europe/Amsterdam','Europe/Athens','Europe/Belgrade','Europe/Berlin','Europe/Brussels',
            'Europe/Bucharest','Europe/Budapest','Europe/Copenhagen','Europe/Dublin','Europe/Helsinki',
            'Europe/Istanbul','Europe/Kiev','Europe/Lisbon','Europe/London','Europe/Madrid',
            'Europe/Moscow','Europe/Oslo','Europe/Paris','Europe/Prague','Europe/Rome',
            'Europe/Stockholm','Europe/Vienna','Europe/Warsaw','Europe/Zurich',
            'Indian/Maldives','Indian/Mauritius',
            'Pacific/Auckland','Pacific/Fiji','Pacific/Guam','Pacific/Honolulu',
            'UTC','GMT','EST','MST',
        );
    }
}

// ============================================================
// defaultTimeZone
// ============================================================

if (!function_exists('defaultTimeZone')) {
    function defaultTimeZone() {
        // Pakistan Time (UTC+5)
        return 'Asia/Karachi';
    }
}

// ============================================================
// AUTH — isLoggedIn
// ============================================================

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        if (session()->get('fw')) {
            return true;
        }

        $request   = service('request');
        $cookieVal = $request->getCookie('rememberme');

        if ($cookieVal && $cookieVal != '') {
            // ✅ SECURITY FIX 1.6: Use secure token validation instead of encrypted cookies
            // Hash the raw token from cookie using same method as generation
            $tokenHash = hash_hmac('sha256', $cookieVal, config('Encryption')->key);
            
            $db  = db_connect();
            $rec = $db->table('fw_users')
                      ->where('status', 'Active')
                      ->where('hashToken', $tokenHash)
                      ->get();

            if ($rec->getNumRows() > 0) {
                $record    = $rec->getRow();
                // ✅ SECURITY FIX 1.4: Remove password from session storage
                unset($record->password);
                session()->set('fw', $record);
                
                // Regenerate token for next time
                $newTokenRaw = bin2hex(random_bytes(32));
                $newTokenHash = hash_hmac('sha256', $newTokenRaw, config('Encryption')->key);
                service('response')->setCookie('rememberme', $newTokenRaw, 2592000);
                $db->table('fw_users')
                   ->where('userId', $record->userId)
                   ->update(['hashToken' => $newTokenHash]);
                return true;
            }
            return false;
        }
        return false;
    }
}

// ============================================================
// ENCRYPT / DECRYPT
// ============================================================

if (!function_exists('encrypt')) {
    function encrypt($token) {
        $cipher_method = 'aes-128-ctr';
        $key           = config('App')->encryptionKey ?? env('encryption.key', 'default_key');
        $enc_key       = openssl_digest($key, 'SHA256', TRUE);
        $enc_iv        = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_method));
        $crypted_token = openssl_encrypt($token, $cipher_method, $enc_key, 0, $enc_iv) . '::' . bin2hex($enc_iv);
        unset($token, $cipher_method, $enc_key, $enc_iv);
        return @base64_encode($crypted_token);
    }
}

if (!function_exists('decrypt')) {
    function decrypt($crypted_token) {
        $crypted_token = @base64_decode($crypted_token);
        $cipher_method = 'aes-128-ctr';
        $key           = config('App')->encryptionKey ?? env('encryption.key', 'default_key');
        $parts         = explode('::', $crypted_token);
        if (count($parts) < 2) return '';
        [$crypted_token, $enc_iv] = $parts;
        $enc_key = openssl_digest($key, 'SHA256', TRUE);
        $token   = openssl_decrypt($crypted_token, $cipher_method, $enc_key, 0, hex2bin($enc_iv));
        unset($cipher_method, $enc_key, $enc_iv);
        return $token;
    }
}

// ============================================================
// USER HELPERS
// ============================================================

if (!function_exists('userName')) {
    function userName($userId) {
        $db  = db_connect();
        $row = $db->table('fw_users')->select('name')->where('userId', $userId)->get()->getRow();
        return $row->name ?? '';
    }
}

if (!function_exists('userEmail')) {
    function userEmail($userId) {
        $db  = db_connect();
        $row = $db->table('fw_users')->select('email')->where('userId', $userId)->get()->getRow();
        return $row->email ?? '';
    }
}

if (!function_exists('getUser')) {
    function getUser($userId) {
        $db = db_connect();
        return $db->table('fw_users')->where('userId', $userId)->get()->getRow();
    }
}

// ============================================================
// STRING / VALIDATION HELPERS
// ============================================================

if (!function_exists('cleanNumber')) {
    function cleanNumber($number) {
        $number = str_replace([' ', ')', '(', '_', '-', '+', '.'], '', $number);
        if (strlen($number) == 10) $number = '1' . $number;
        return $number;
    }
}

if (!function_exists('cleanEmail')) {
    function cleanEmail($email) {
        return strtolower(str_replace([' ', ')', '(', ',', '"', '`', '!', '|', '#', '&', '%', '^'], '', $email));
    }
}

if (!function_exists('validEmail')) {
    function validEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false;
    }
}

// ============================================================
// DATE / TIME HELPERS
// ============================================================

if (!function_exists('reportTime')) {
    function reportTime($time) {
        return date('M d Y, h:i A', strtotime($time));
    }
}

if (!function_exists('reportDate')) {
    function reportDate($time) {
        return date('M d, Y', strtotime($time));
    }
}

if (!function_exists('timeAgo')) {
    function timeAgo($ptime) {
        if ($ptime == '0000-00-00 00:00:00') return 'x days ago';
        $etime = time() - strtotime($ptime);
        if ($etime < 1) return 'just now';
        $interval = [
            12 * 30 * 24 * 60 * 60 => 'years',
            30 * 24 * 60 * 60       => 'months',
            24 * 60 * 60            => 'days',
            60 * 60                 => 'hours',
            60                      => 'minutes',
            1                       => 'seconds',
        ];
        foreach ($interval as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return ($str == 'seconds' || $r == 0) ? 'just now' : $r . ' ' . $str . ' ago';
            }
        }
    }
}

// ============================================================
// EMOJIS
// ============================================================

if (!function_exists('emojis')) {
    function emojis() {
        return ["&#128512","&#128513","&#128514","&#128515","&#128516","&#128517","&#128518","&#128519",
            "&#128520","&#128521","&#128522","&#128523","&#128524","&#128525","&#128526","&#128527",
            "&#128528","&#128529","&#128530","&#128531","&#128532","&#128533","&#128534","&#128535",
            "&#128536","&#128537","&#128538","&#128539","&#128540","&#128541","&#128542","&#128543",
            "&#128544","&#128545","&#128546","&#128547","&#128548","&#128549","&#128550","&#128551",
            "&#128552","&#128553","&#128554","&#128555","&#128556","&#128557","&#128558","&#128559",
            "&#128560","&#128561","&#128562","&#128563","&#128564","&#128565","&#128566","&#128567",
            "&#128568","&#128569","&#128570","&#128571","&#128572","&#128573","&#128574","&#128575",
            "&#128576","&#128577","&#128578","&#128579","&#128580","&#129296","&#129297","&#129298",
            "&#129299","&#129300","&#129301","&#129302","&#129303","&#129304","&#129305","&#129306",
            "&#129307","&#129308","&#129309","&#129310","&#129311","&#129312","&#129313","&#129314",
            "&#129315","&#129316","&#129317","&#129318","&#129319","&#129320","&#129321","&#129322",
            "&#129323","&#129324","&#129325","&#129326","&#129327","&#129488"];
    }
}

// ============================================================
// TAGS
// ============================================================

if (!function_exists('getTags')) {
    function getTags($tagId = 0) {
        $db      = db_connect();
        $builder = $db->table('fw_tags')->select('tag')->orderBy('tag', 'asc');
        if ($tagId != 0) $builder->where('tagId', $tagId);
        $return = [];
        foreach ($builder->get()->getResult() as $rec) {
            $return[] = $rec->tag;
        }
        return $return;
    }
}

if (!function_exists('setTag')) {
    function setTag($tag) {
        $db     = db_connect();
        $exists = $db->table('fw_tags')->where('tag', $tag)->countAllResults();
        if ($exists == 0) {
            $db->table('fw_tags')->insert(['tag' => $tag, 'createdTime' => date('Y-m-d H:i:s')]);
        }
    }
}

// ============================================================
// CATEGORIES
// ============================================================

if (!function_exists('getCategoreis')) {
    function getCategoreis($catId = 0) {
        $db      = db_connect();
        $builder = $db->table('fw_categoreis')->select('category')->orderBy('category', 'asc');
        if ($catId != 0) $builder->where('catId', $catId);
        $return = [];
        foreach ($builder->get()->getResult() as $rec) {
            $return[] = $rec->category;
        }
        return $return;
    }
}

if (!function_exists('getCategoreisWithSlug')) {
    function getCategoreisWithSlug() {
        $db = db_connect();
        return $db->table('fw_categoreis')->select('category,catSlug')->orderBy('category', 'asc')->get()->getResult();
    }
}

if (!function_exists('getCategory')) {
    function getCategory($category) {
        $db     = db_connect();
        $record = $db->table('fw_categoreis')
                     ->groupStart()
                         ->where('catSlug', $category)
                         ->orWhere('category', $category)
                     ->groupEnd()
                     ->get();
        return ($record->getNumRows() > 0) ? $record->getRow() : '0';
    }
}

if (!function_exists('setCategory')) {
    function setCategory($category) {
        $db     = db_connect();
        $exists = $db->table('fw_categoreis')->where('category', $category)->countAllResults();
        if ($exists == 0) {
            $db->table('fw_categoreis')->insert([
                'category'    => $category,
                'catSlug'     => url_title($category, '-', true),
                'createdTime' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}

// ============================================================
// PAGES / CMS
// ============================================================

if (!function_exists('getActivePages')) {
    function getActivePages($position = 'all') {
        $db      = db_connect();
        $builder = $db->table('fw_cms')->where('status', 'Active')->where('pageType', '1');
        if ($position != 'all') $builder->where('position', $position);
        return $builder->get()->getResult();
    }
}

if (!function_exists('getPage')) {
    function getPage($pageId) {
        $db = db_connect();
        return $db->table('fw_cms')->where('pageId', $pageId)->get()->getRow();
    }
}

if (!function_exists('getPageByArea')) {
    function getPageByArea($page_area) {
        $db = db_connect();
        return $db->table('fw_cms')->where('page_area', $page_area)->get()->getRow();
    }
}

if (!function_exists('getPageBySlug')) {
    function getPageBySlug($slugUrl) {
        $db     = db_connect();
        $record = $db->table('fw_cms')
                     ->where('slugUrl', $slugUrl)
                     ->where('status', 'Active')
                     ->where('pageType', '1')
                     ->get();
        return ($record->getNumRows() > 0) ? $record->getRow() : '0';
    }
}

// ============================================================
// POSTS / BLOGS
// ============================================================

if (!function_exists('getPostBySlug')) {
    function getPostBySlug($postSlug) {
        $db     = db_connect();
        $record = $db->table('fw_posts')
                     ->where('postSlug', $postSlug)
                     ->where('postStatus', 'Active')
                     ->get();
        return ($record->getNumRows() > 0) ? $record->getRow() : '0';
    }
}

if (!function_exists('getBlogBySlug')) {
    function getBlogBySlug($postSlug) {
        $db     = db_connect();
        $record = $db->table('fw_blogs')
                     ->where('postSlug', $postSlug)
                     ->where('postStatus', 'Active')
                     ->get();
        return ($record->getNumRows() > 0) ? $record->getRow() : '0';
    }
}

if (!function_exists('getPost')) {
    function getPost($postId) {
        $db     = db_connect();
        $record = $db->table('fw_posts')->where('postId', $postId)->get();
        return ($record->getNumRows() > 0) ? $record->getRow() : '0';
    }
}

if (!function_exists('recentPosts')) {
    function recentPosts($limit = 10, $postId = '') {
        $db      = db_connect();
        $builder = $db->table('fw_posts');
        
        // ✅ SECURITY: Only show Active (published) posts to public
        $builder->where('postStatus', 'Active');
        
        if ($postId != '') $builder->where('postId !=', $postId);
        
        // ✅ SEO: Order by publishedAt (when post went live) for accurate "Recently Added"
        return $builder->orderBy('publishedAt', 'desc')->limit($limit)->get()->getResult();
    }
}

if (!function_exists('recentModels')) {
    function recentModels($limit = 5) {
        $db = db_connect();
        return $db->table('fw_posts')
                  ->where('postStatus', 'Active')  // ✅ SECURITY: Only show published posts
                  ->where('model !=', '')
                  ->where('isRecentModel', 'Yes')
                  ->groupBy('model')
                  ->orderBy('publishedAt', 'desc')  // ✅ SEO: Order by publish date
                  ->limit($limit)
                  ->get()->getResult();
    }
}

if (!function_exists('getPostComments')) {
    function getPostComments($postId, $limit = 20) {
        $db = db_connect();
        return $db->table('fw_post_comments')
                  ->where('status', 'Approved')
                  ->where('postId', $postId)
                  ->orderBy('commentTime', 'asc')
                  ->limit($limit)
                  ->get()->getResult();
    }
}

// ============================================================
// SITE META
// ============================================================

if (!function_exists('setSiteMeta')) {
    function setSiteMeta($metaType, $metaValue) {
        $db     = db_connect();
        $exists = $db->table('fw_site_meta')->where('metaType', $metaType)->countAllResults();
        if ($exists == 0) {
            $db->table('fw_site_meta')->insert([
                'metaType'    => $metaType,
                'metaValue'   => @json_encode($metaValue),
                'updatedTime' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $db->table('fw_site_meta')->where('metaType', $metaType)->update([
                'metaValue'   => @json_encode($metaValue),
                'updatedTime' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}

if (!function_exists('getSiteMeta')) {
    function getSiteMeta($metaType = '') {
        $db      = db_connect();
        $builder = $db->table('fw_site_meta')->select('metaType,metaValue');
        if ($metaType != '') {
            $row = $builder->where('metaType', $metaType)->get()->getRow();
            return @json_decode($row->metaValue ?? '[]', true);
        } else {
            $return = [];
            foreach ($builder->get()->getResult() as $rec) {
                $return[$rec->metaType] = @json_decode($rec->metaValue, true);
            }
            return $return;
        }
    }
}

// ============================================================
// VISITOR / BOT TRACKING
// ============================================================
// FIX SUMMARY:
//   1. $request->uri  -->  $request->getUri()           (protected property fix)
//   2. getSegment(n)  -->  getTotalSegments() check      (out of range fix)
//   3. Last blog check mein bhi pehla wala bug tha       (ab $seg2 se fix)
// ============================================================

if (!function_exists('setVisitor')) {
    function setVisitor($ipAddress = '', $postId = 0) {
        $db      = db_connect();
        $request = service('request');
        $uri     = $request->getUri(); // ✅ FIX 1: protected property ki jagah public method

        // ✅ FIX 2: har segment access se pehle getTotalSegments() check
        $totalSegs = $uri->getTotalSegments();
        $seg1 = ($totalSegs >= 1) ? trim($uri->getSegment(1)) : '';
        $seg2 = ($totalSegs >= 2) ? trim($uri->getSegment(2)) : '';
        $seg3 = ($totalSegs >= 3) ? trim($uri->getSegment(3)) : '';
        $seg4 = ($totalSegs >= 4) ? trim($uri->getSegment(4)) : '';

        $keySub  = $seg1;
        $model   = '';
        $csc     = '';
        $version = '';

        if ($keySub == 'firmware') {
            $model   = $seg2;
            $csc     = $seg3;
            $version = $seg4;
        } else {
            $model   = $seg1;
            $csc     = $seg2;
            $version = $seg3;
        }

        if ($version != '' && $csc != '' && $model != '') {
            $rec = $db->table('fw_posts')
                      ->select('postId')
                      ->where('model', $model)
                      ->where('version', $version)
                      ->groupStart()
                          ->where('csc', strtoupper($csc))
                          ->orWhere('country', $csc)
                      ->groupEnd()
                      ->get();
            $postId = $rec->getRow()->postId ?? 0;
        }

        if ($postId == '' || $postId == null) $postId = '0';

        $dateTime = date('Y-m-d H:i:s', strtotime('-10 Seconds'));
        $recent   = $db->table('fw_post_view')
                       ->select('viewId')
                       ->where('postId', $postId)
                       ->where('ipAddress', $ipAddress)
                       ->where('viewTime >=', $dateTime)
                       ->get();

        if ($recent->getNumRows() == 0) {
            $referrerFrom = isset($_SERVER['HTTP_REFERER']) ? trim($_SERVER['HTTP_REFERER']) : '';
            $referrelUrl  = '';
            if ($referrerFrom != '') {
                $rdomain     = parse_url($referrerFrom);
                $referrelUrl = $rdomain['host'] ?? '';
                if (!filter_var($referrelUrl, FILTER_VALIDATE_IP)) {
                    $refUrl      = explode('.', str_ireplace('www.', '', $referrelUrl));
                    $lengths     = array_map('strlen', $refUrl);
                    $referrelUrl = $refUrl[array_search(max($lengths), $lengths)];
                    if (substr_count($referrerFrom, 'google') > 0) $referrelUrl = 'google';
                }
                $referrelUrl = $referrelUrl ?: '';
            }

            $country     = ipInfo($ipAddress, 'country_code') ?: '';
            $visitingUrl = current_url() ?: '';
            $userAgent   = $_SERVER['HTTP_USER_AGENT'] ?? '';

            if (isBot($userAgent) == false) {
                $db->table('fw_post_view')->insert([
                    'country'      => $country,
                    'visitingUrl'  => $visitingUrl,
                    'postId'       => $postId,
                    'ipAddress'    => $ipAddress,
                    'viewTime'     => date('Y-m-d H:i:s'),
                    'referrelUrl'  => $referrelUrl,
                    'referrerFrom' => $referrerFrom,
                    'userAgent'    => $userAgent,
                ]);

                if ($postId != 0) {
                    $db->table('fw_posts')
                       ->where('postId', $postId)
                       ->set('viewsCount', 'viewsCount + 1', false)
                       ->update();
                }

                // ✅ FIX 3: $request->uri->getSegment(2) ki jagah already-safe $seg2 use karo
                if ($keySub == 'blog' && $seg2 != '') {
                    $db->table('fw_blogs')
                       ->where('postSlug', $seg2)
                       ->set('viewsCount', 'viewsCount + 1', false)
                       ->update();
                }
            }
        }
    }
}

if (!function_exists('isBot')) {
    function isBot($userAgent = '') {
        if ($userAgent == '') $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $userAgent = strtolower($userAgent);
        foreach (['bot', 'crawler'] as $utype) {
            if (substr_count($userAgent, $utype) > 0) return true;
        }
        return false;
    }
}

// ============================================================
// VISITOR LIKE
// ============================================================

if (!function_exists('getvisitorLike')) {
    function getvisitorLike($postId, $ipAddress, $area = 'post') {
        $db     = db_connect();
        $record = $db->table('fw_post_likes')
                     ->select('type')
                     ->where('ipAddress', $ipAddress)
                     ->where('postId', $postId)
                     // ->where('area', $area) // Removed: column 'area' doesn't exist in fw_post_likes table
                     ->get();
        return $record->getRow()->type ?? null;
    }
}

// ============================================================
// CONTACT US HEADER
// ============================================================

if (!function_exists('getContactUSHeader')) {
    function getContactUSHeader() {
        $db     = db_connect();
        $record = $db->table('fw_contact_us')->orderBy('contactUsId', 'desc')->limit(20)->get();
        $return = ['ecount' => '0', 'record' => []];
        foreach ($record->getResult() as $rec) {
            $return['ecount'] += ($rec->status == '0' ? 1 : 0);
            $return['record'][] = $rec;
        }
        return $return;
    }
}

// ============================================================
// WORLD COUNTRIES
// ============================================================

if (!function_exists('worldCountries')) {
    function worldCountries($type = '3') {
        $db     = db_connect();
        $record = $db->table('fw_world_country')
                     ->select('iso2,iso3,country,call_countrycode')
                     ->orderBy('country', 'asc')
                     ->get()->getResult();
        $return = [];
        foreach ($record as $rec) {
            if ($type == '2') {
                $return[$rec->iso2] = ['code' => $rec->iso2, 'iso3' => $rec->iso3, 'name' => $rec->country, 'callingCode' => $rec->call_countrycode];
            } else {
                $return[$rec->iso3] = ['code' => $rec->iso2, 'name' => $rec->country, 'callingCode' => $rec->call_countrycode];
            }
        }
        return $return;
    }
}

if (!function_exists('extractCountry')) {
    function extractCountry($str) {
        preg_match_all('/\{([^}]+)\}/', $str, $matches);
        return $matches;
    }
}

// ============================================================
// POST URL
// ============================================================

if (!function_exists('postUrl')) {
    function postUrl($post) {
        // ✅ COMPATIBILITY FIX: Match samfirmsold URL structure
        // Use country field first (primary), fallback to csc if country is empty
        // This ensures URLs match the old site's indexed URLs in Google
        
        // Validate that the post object has required fields
        if (!isset($post->model) || !isset($post->version)) {
            return base_url('firmware');
        }
        
        // Determine which field to use for URL (country or csc)
        $countryOrCSC = '';
        
        if (isset($post->country) && $post->country != '' && $post->country != null) {
            $countryOrCSC = $post->country;  // Primary: country field
        } else if (isset($post->csc) && $post->csc != '' && $post->csc != null) {
            $countryOrCSC = $post->csc;      // Fallback: csc field
        } else {
            // If neither field is available, return firmware list page
            return base_url('firmware');
        }
        
        // Build firmware URL: firmware/MODEL/COUNTRY/VERSION
        // Example: firmware/SM-G998B/XSG/G998BXXU1AUH1
        return base_url('firmware/' . $post->model . '/' . $countryOrCSC . '/' . $post->version);
    }
}

// ============================================================
// CSC LIST
// ============================================================

if (!function_exists('getCSCList')) {
    function getCSCList() {
        $db     = db_connect();
        $record = $db->table('fw_world_csc')->select('csc,country')->orderBy('country', 'asc')->get()->getResult();
        $return = [];
        foreach ($record as $rec) {
            $return[$rec->csc] = $rec->country;
        }
        return $return;
    }
}

// ============================================================
// HTML CONTENT HELPERS
// ============================================================

if (!function_exists('innerTableContent')) {
    function innerTableContent() {
        return '<div class="not-prose my-4">
            <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-base font-bold text-ink flex items-center space-x-2">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Samsung Firmware Details & Specifications</span>
                    </h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Device Name -->
                        <div class="bg-white rounded-lg p-3 border border-gray-100 hover:shadow transition-shadow">
                            <div class="flex items-start space-x-2">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-ink-muted uppercase tracking-wide mb-0.5">Device Name</p>
                                    <p class="text-sm font-bold text-ink truncate">{post-device}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Model Number -->
                        <div class="bg-white rounded-lg p-3 border border-gray-100 hover:shadow transition-shadow">
                            <div class="flex items-start space-x-2">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-ink-muted uppercase tracking-wide mb-0.5">Model Number</p>
                                    <p class="text-sm font-bold text-ink truncate">{post-model}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Product Brand -->
                        <div class="bg-white rounded-lg p-3 border border-gray-100 hover:shadow transition-shadow">
                            <div class="flex items-start space-x-2">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-ink-muted uppercase tracking-wide mb-0.5">Product Brand</p>
                                    <p class="text-sm font-bold text-ink truncate">{post-product}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Android OS -->
                        <div class="bg-white rounded-lg p-3 border border-gray-100 hover:shadow transition-shadow">
                            <div class="flex items-start space-x-2">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-ink-muted uppercase tracking-wide mb-0.5">Android OS</p>
                                    <p class="text-sm font-bold text-ink truncate">{post-os}</p>
                                </div>
                            </div>
                        </div>

                        <!-- AP Version -->
                        <div class="bg-white rounded-lg p-3 border border-gray-100 hover:shadow transition-shadow">
                            <div class="flex items-start space-x-2">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-ink-muted uppercase tracking-wide mb-0.5">AP Version (PDA)</p>
                                    <p class="text-xs font-mono font-bold text-ink truncate">{post-apversion}</p>
                                </div>
                            </div>
                        </div>

                        <!-- CSC Version -->
                        <div class="bg-white rounded-lg p-3 border border-gray-100 hover:shadow transition-shadow">
                            <div class="flex items-start space-x-2">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-ink-muted uppercase tracking-wide mb-0.5">CSC Version</p>
                                    <p class="text-xs font-mono font-bold text-ink truncate">{post-cscversion}</p>
                                </div>
                            </div>
                        </div>

                        <!-- CSC Country -->
                        <div class="bg-white rounded-lg p-3 border border-gray-100 hover:shadow transition-shadow">
                            <div class="flex items-start space-x-2">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-ink-muted uppercase tracking-wide mb-0.5">CSC Country Code</p>
                                    <p class="text-sm font-bold text-ink truncate">{post-flag} {post-csccode} <span class="text-xs text-gray-600 font-normal">({post-countryname})</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Binary Level -->
                        <div class="bg-white rounded-lg p-3 border border-gray-100 hover:shadow transition-shadow">
                            <div class="flex items-start space-x-2">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-ink-muted uppercase tracking-wide mb-0.5">Binary Level (BIT/U)</p>
                                    <p class="text-sm font-bold text-ink truncate">{post-bit}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Build Date -->
                        <div class="bg-white rounded-lg p-3 border border-gray-100 hover:shadow transition-shadow">
                            <div class="flex items-start space-x-2">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-ink-muted uppercase tracking-wide mb-0.5">Build Date</p>
                                    <p class="text-sm font-bold text-ink truncate">{post-builddate}</p>
                                </div>
                            </div>
                        </div>

                        <!-- File Size -->
                        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-ink-muted uppercase tracking-wide mb-1">File Size</p>
                                    <p class="text-base font-bold text-ink truncate">{post-size}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SEO Keywords Info -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="bg-accent-soft rounded-xl p-4 border border-accent/20">
                            <p class="text-xs text-ink-muted leading-relaxed">
                                <span class="font-bold text-accent">Download Official Samsung Firmware:</span> This firmware package includes the latest Android OS update, security patches, and system improvements for <strong>{post-device}</strong> model <strong>{post-model}</strong>. Compatible with CSC code <strong>{post-csc}</strong> region. Flash using Odin tool for Samsung devices. <em>Always backup your data before updating firmware.</em>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
}

if (!function_exists('replacePostToken')) {
    function replacePostToken($contentData, $post, $image = true) {
        $worldCountries = worldCountries();
        $cntryRecord    = $worldCountries[$post->country] ?? null;
        $cntryCode      = 'us';
        $cntryName      = 'unknown';
        if (is_array($cntryRecord)) {
            $cntryCode = strtolower($cntryRecord['code']);
            $cntryName = strtolower($cntryRecord['name']);
        }
        $cntryCodeCSC = '<img class="inline-block shadow-sm border border-gray-200 align-middle mr-1" src="' . base_url('assets/img/flags/4x3/' . $cntryCode . '.svg') . '" width="20" height="15"><strong class="text-ink">' . $post->csc . '</strong>';
        $flagOnly = '<img class="inline-block shadow-sm" src="' . base_url('assets/img/flags/4x3/' . $cntryCode . '.svg') . '" width="24" height="18" alt="' . $cntryName . ' flag">';
        
        $contentData  = str_replace('{post-device}',      formatDeviceDisplay(trim($post->device)), $contentData);
        $contentData  = str_replace('{post-model}',       trim($post->model),                $contentData);
        $contentData  = str_replace('{post-product}',     'SAMSUNG',                         $contentData);
        $contentData  = str_replace('{post-filename}',    trim($post->fileType),             $contentData);
        $contentData  = str_replace('{post-apversion}',   trim($post->version),              $contentData);
        $contentData  = str_replace('{post-os}',          trim($post->os),                   $contentData);
        $contentData  = str_replace('{post-cscversion}',  trim($post->cscversion),           $contentData);
        $contentData  = str_replace('{post-country}',     trim($post->country),              $contentData);
        $contentData  = str_replace('{post-builddate}',   trim($post->buildDate),            $contentData);
        $contentData  = str_replace('{post-countryname}', ucfirst(trim($cntryName)),         $contentData);
        $contentData  = str_replace('{post-countrycode}', trim($cntryCode),                  $contentData);
        $contentData  = str_replace('{post-flag}',        $flagOnly,                         $contentData);
        $contentData  = str_replace('{post-csc}',         $cntryCodeCSC,                     $contentData);
        $contentData  = str_replace('{post-csccode}',     $post->csc,                        $contentData);
        $contentData  = str_replace('{post-bit}',         trim($post->bit),                  $contentData);
        $contentData  = str_replace('{post-uploaddate}',  substr($post->createdTime, 0, 10), $contentData);
        $contentData  = str_replace('{post-size}',        trim($post->fileSize),             $contentData);
        $contentData  = str_replace('{post-ads}',         trim($post->adsContent),           $contentData);
        return $contentData;
    }
}

// ============================================================
// CLEAN BASE URL
// ============================================================

if (!function_exists('cleanBaseUrl')) {
    function cleanBaseUrl() {
        return trim(str_replace(['https://', 'http://', 'www.'], '', base_url()), '/');
    }
}

// ============================================================
// IP INFO
// ============================================================

if (!function_exists('ipInfo')) {
    function ipInfo($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
        $output = NULL;
        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            $ip = $_SERVER["REMOTE_ADDR"] ?? '127.0.0.1';
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
        $purpose    = str_replace(["name", "\n", "\t", " ", "-", "_"], NULL, strtolower(trim($purpose)));
        $support    = ["country", "countrycode", "state", "region", "city", "location", "address"];
        $continents = ["AF" => "Africa", "AN" => "Antarctica", "AS" => "Asia", "EU" => "Europe", "OC" => "Australia (Oceania)", "NA" => "North America", "SA" => "South America"];
        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode(@file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                switch ($purpose) {
                    case "location":
                        $output = ["city" => @$ipdat->geoplugin_city, "state" => @$ipdat->geoplugin_regionName, "country" => @$ipdat->geoplugin_countryName, "country_code" => @$ipdat->geoplugin_countryCode, "continent" => @$continents[strtoupper($ipdat->geoplugin_continentCode)], "continent_code" => @$ipdat->geoplugin_continentCode];
                        break;
                    case "address":
                        $address = [$ipdat->geoplugin_countryName];
                        if (@strlen($ipdat->geoplugin_regionName) >= 1) $address[] = $ipdat->geoplugin_regionName;
                        if (@strlen($ipdat->geoplugin_city) >= 1) $address[] = $ipdat->geoplugin_city;
                        $output = implode(", ", array_reverse($address));
                        break;
                    case "city":        $output = @$ipdat->geoplugin_city;        break;
                    case "state":
                    case "region":      $output = @$ipdat->geoplugin_regionName;  break;
                    case "country":     $output = @$ipdat->geoplugin_countryName; break;
                    case "countrycode": $output = @$ipdat->geoplugin_countryCode; break;
                }
            }
        }
        return $output;
    }
}

// ============================================================
// BYTE SIZE
// ============================================================

if (!function_exists('toByteSize')) {
    function toByteSize($p_sFormatted) {
        $aUnits = ['B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8];
        $sUnit  = strtoupper(trim(substr($p_sFormatted, -2)));
        if (intval($sUnit) !== 0) $sUnit = 'B';
        if (!in_array($sUnit, array_keys($aUnits))) return false;
        $iUnits = trim(substr($p_sFormatted, 0, strlen($p_sFormatted) - 2));
        if (!intval($iUnits) == $iUnits) return false;
        return $iUnits * pow(1024, $aUnits[$sUnit]);
    }
}


// ============================================================
// FIRMWARE SCRAPER - DISPLAY HELPERS
// ============================================================

if (!function_exists('formatDeviceDisplay')) {
    /**
     * Format device name for display with "GALAXY" prefix
     * @param string $device Device code from database (e.g., "A35", "WATCH5")
     * @return string Formatted display name (e.g., "GALAXY A35", "GALAXY WATCH5")
     */
    function formatDeviceDisplay(string $device): string {
        if (empty($device)) {
            return '';
        }
        
        // Don't add prefix if already present or if UNKNOWN
        if ($device === 'UNKNOWN' || stripos($device, 'GALAXY') === 0) {
            return $device;
        }
        
        // Add GALAXY prefix for all Samsung devices
        return 'GALAXY ' . $device;
    }
}


// ============================================================
// QUERY CACHE HELPERS - NEW
// ============================================================

/**
 * Get query cache instance
 * Lazy loads on first use
 */
if (!function_exists('qcache')) {
    function qcache(): \App\Libraries\QueryCache
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new \App\Libraries\QueryCache();
        }
        return $instance;
    }
}

/**
 * Remember a query result in cache
 * 
 * Usage:
 *   qremember('posts_recent', 3600, fn() => $this->db->table('fw_posts')->get()->getResult())
 *   qremember('post_' . $id, fn() => $this->db->table('fw_posts')->where('postId', $id)->get()->getRow())
 */
if (!function_exists('qremember')) {
    function qremember(string $key, $ttlOrCallback = 3600, ?callable $callback = null): mixed
    {
        return qcache()->remember($key, $ttlOrCallback, $callback);
    }
}

/**
 * Get cached value
 */
if (!function_exists('qget')) {
    function qget(string $key): mixed
    {
        return qcache()->get($key);
    }
}

/**
 * Store value in cache
 */
if (!function_exists('qput')) {
    function qput(string $key, mixed $value, int $ttl = 3600): bool
    {
        return qcache()->put($key, $value, $ttl);
    }
}

/**
 * Forget cached key
 */
if (!function_exists('qforget')) {
    function qforget(string $key): bool
    {
        return qcache()->forget($key);
    }
}

/**
 * Forget by pattern (e.g., 'site_meta_*')
 */
if (!function_exists('qforgetPattern')) {
    function qforgetPattern(string $pattern): int
    {
        return qcache()->forgetPattern($pattern);
    }
}

/**
 * Clear all cache
 */
if (!function_exists('qflush')) {
    function qflush(): int
    {
        return qcache()->flush();
    }
}

/**
 * Check if cache exists and is valid
 */
if (!function_exists('qhas')) {
    function qhas(string $key): bool
    {
        return qcache()->has($key);
    }
}

/**
 * Get cache statistics
 */
if (!function_exists('qstats')) {
    function qstats(): array
    {
        return qcache()->stats();
    }
}


// ============================================================
// SITEMAP PING SERVICE
// ============================================================

if (!function_exists('pingSitemapToSearchEngines')) {
    /**
     * 🔔 Notify search engines about sitemap updates
     * Call this after publishing new content to speed up indexing
     * 
     * @param string|null $sitemapUrl Optional specific sitemap URL (defaults to master sitemap)
     * @return array Status of ping attempts
     */
    function pingSitemapToSearchEngines(?string $sitemapUrl = null): array {
        if (empty($sitemapUrl)) {
            $sitemapUrl = base_url('sitemaps.xml');
        }
        
        $results = [
            'google' => ['status' => 'pending', 'message' => ''],
            'bing' => ['status' => 'pending', 'message' => '']
        ];
        
        // Only ping in production environment
        if (ENVIRONMENT !== 'production') {
            $results['google']['status'] = 'skipped';
            $results['google']['message'] = 'Skipped in ' . ENVIRONMENT . ' environment';
            $results['bing']['status'] = 'skipped';
            $results['bing']['message'] = 'Skipped in ' . ENVIRONMENT . ' environment';
            return $results;
        }
        
        $encodedUrl = urlencode($sitemapUrl);
        
        // 1. Ping Google
        try {
            $googlePingUrl = "https://www.google.com/ping?sitemap=" . $encodedUrl;
            $client = \Config\Services::curlrequest();
            $response = $client->get($googlePingUrl, ['timeout' => 5, 'http_errors' => false]);
            
            if ($response->getStatusCode() === 200) {
                $results['google']['status'] = 'success';
                $results['google']['message'] = 'Sitemap pinged successfully';
            } else {
                $results['google']['status'] = 'error';
                $results['google']['message'] = 'HTTP ' . $response->getStatusCode();
            }
        } catch (\Exception $e) {
            $results['google']['status'] = 'error';
            $results['google']['message'] = $e->getMessage();
        }
        
        // 2. Ping Bing (also covers Yahoo)
        try {
            $bingPingUrl = "https://www.bing.com/ping?sitemap=" . $encodedUrl;
            $client = \Config\Services::curlrequest();
            $response = $client->get($bingPingUrl, ['timeout' => 5, 'http_errors' => false]);
            
            if ($response->getStatusCode() === 200) {
                $results['bing']['status'] = 'success';
                $results['bing']['message'] = 'Sitemap pinged successfully';
            } else {
                $results['bing']['status'] = 'error';
                $results['bing']['message'] = 'HTTP ' . $response->getStatusCode();
            }
        } catch (\Exception $e) {
            $results['bing']['status'] = 'error';
            $results['bing']['message'] = $e->getMessage();
        }
        
        return $results;
    }
}

if (!function_exists('autoInvalidateSitemapCache')) {
    /**
     * 🔄 Clear sitemap caches and notify search engines
     * Call this after publishing new firmware or blog posts
     * 
     * @return array Results of cache clear and ping operations
     */
    function autoInvalidateSitemapCache(): array {
        $cache = \Config\Services::cache();
        
        // Clear all sitemap caches
        $cacheKeys = [
            'sitemap_master_v1',
            'sitemap_blog_v2', // v2: current version with images
            'sitemap_blog_v1', // v1: cleanup old cache
            'sitemap_cms_v1',
            'sitemap_models_v1',
            'sitemap_csc_v1'
        ];
        
        $clearedCount = 0;
        foreach ($cacheKeys as $key) {
            if ($cache->delete($key)) {
                $clearedCount++;
            }
        }
        
        // Clear firmware sitemap pages (up to 20 pages = 160,000 posts)
        for ($i = 1; $i <= 20; $i++) {
            $cache->delete('sitemap_firmware_page_' . $i . '_v3'); // v3: current version
            $cache->delete('sitemap_firmware_page_' . $i . '_v2'); // v2: cleanup old cache
        }
        
        // Ping search engines about the update
        $pingResults = pingSitemapToSearchEngines();
        
        return [
            'caches_cleared' => $clearedCount,
            'ping_results' => $pingResults,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}
