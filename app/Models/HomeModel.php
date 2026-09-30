<?php

namespace App\Models;

use CodeIgniter\Model;

class HomeModel extends Model
{
    // ✅ FIX: Add table and pagination config for CI4 Model pagination
    protected $table      = 'fw_posts';
    protected $primaryKey = 'postId';
    protected $returnType = 'object';
    
    public function doLogin(string $username, string $password): object|string
    {
        $builder = $this->db->table('fw_users')->where('status', 'Active')
                            ->where('username', trim($username));
        
        $rec = $builder->get();
        
        if ($rec->getNumRows() === 0) {
            return '0';
        }
        
        $record = $rec->getRow();
        
        // ✅ SECURITY FIX 1.2: Dual-auth mode - Support both MD5 (old) and bcrypt (new)
        // Check if password field contains bcrypt hash (starts with $2y$ or $2b$)
        if (str_starts_with($record->password, '$2y$') || str_starts_with($record->password, '$2b$')) {
            // New bcrypt password - use password_verify()
            if (password_verify($password, $record->password)) {
                return $record;
            }
        } else {
            // Old MD5 password - check with md5() for backward compatibility
            if (md5($password) === $record->password) {
                return $record;
            }
        }
        
        return '0';
    }

    public function getVisitorStates(): array
    {
        $lables       = [];
        $values       = [];
        $visitorCount = 0;

        for ($i = 7; $i >= 0; $i--) {
            $to_day  = date('Y-m-d', strtotime('-' . $i . ' Days'));
            $record  = $this->db->table('fw_post_view')
                                ->select('viewId')
                                ->like('viewTime', $to_day)
                                ->get();
            $lables[] = '"' . $to_day . '"';
            $vcount   = $record->getNumRows();
            $values[] = $vcount;
            $visitorCount += $vcount;
        }

        return ['lables' => $lables, 'values' => $values, 'visitorCount' => $visitorCount];
    }

    public function getReferrerData(): array
    {
        $selfUrlMe = explode('.', cleanBaseUrl(base_url()));
        $lengths   = array_map('strlen', $selfUrlMe);
        $selfUrl   = $selfUrlMe[array_search(max($lengths), $lengths)];

        $lables      = [];
        $values      = [];
        $totalSearch = 0;

        $record = $this->db->table('fw_post_view')
                           ->select('viewId, referrelUrl, count(viewId) as totalCount')
                           ->where('referrelUrl <>', $selfUrl)
                           ->where('referrelUrl <> ', '')
                           ->groupBy('referrelUrl')
                           ->orderBy('totalCount', 'desc')
                           ->get();

        $ii = 0;
        if ($record->getNumRows() > 0) {
            foreach ($record->getResult() as $rec) {
                if ($ii < 10) {
                    $refTitle = ($rec->referrelUrl == '' || $rec->referrelUrl === null)
                        ? 'others'
                        : $rec->referrelUrl;
                    $lables[] = '"' . ucfirst($refTitle) . '"';
                    $values[] = $rec->totalCount;
                }
                $totalSearch += $rec->totalCount;
                $ii++;
            }
        }

        return ['lables' => $lables, 'values' => $values, 'totalSearch' => $totalSearch];
    }

    public function getCountryData(): array
    {
        $worldCountries = worldCountries('2');

        $lables      = [];
        $values      = [];
        $totalSearch = 0;

        $record = $this->db->table('fw_post_view')
                           ->select('viewId, country, count(viewId) as totalCount')
                           ->where('country <>', '')
                           ->groupStart()
                               ->where('viewTime >= ', date('Y-m-d', strtotime('-8 Days')))
                               ->where('viewTime <= ', date('Y-m-d', strtotime('+1 Days')))
                           ->groupEnd()
                           ->groupBy('country')
                           ->orderBy('totalCount', 'desc')
                           ->get();

        $ii = 0;
        if ($record->getNumRows() > 0) {
            foreach ($record->getResult() as $rec) {
                if ($ii < 10) {
                    $refTitle = is_array($worldCountries[$rec->country])
                        ? $worldCountries[$rec->country]['name']
                        : $rec->country;
                    $lables[] = '"' . $refTitle . '"';
                    $values[] = $rec->totalCount;
                }
                $totalSearch += $rec->totalCount;
                $ii++;
            }
        }

        return ['lables' => $lables, 'values' => $values, 'totalSearch' => $totalSearch];
    }

    public function isValidUsername(string $username, int $userId): bool
    {
        $record = $this->db->table('fw_users')
                           ->select('userId')
                           ->where('username', $username)
                           ->where('userId <> ', $userId)
                           ->get();
        return $record->getNumRows() === 0;
    }

    public function isPageSlugExist(string $slugUrl, int $pageId): bool
    {
        $record = $this->db->table('fw_cms')
                           ->select('pageId')
                           ->where('slugUrl', $slugUrl)
                           ->where('pageId <> ', $pageId)
                           ->get();
        return $record->getNumRows() > 0;
    }

    public function isPostSlugExist(string $slugUrl, int $postId): bool
    {
        $record = $this->db->table('fw_posts')
                           ->select('postId')
                           ->where('postSlug', $slugUrl)
                           ->where('postId <> ', $postId)
                           ->get();
        return $record->getNumRows() > 0;
    }

    public function getCMSRecord(): array
    {
        return $this->db->table('fw_cms')
                        ->select('*')
                        ->orderBy('pageId', 'desc')
                        ->get()->getResult();
    }

    public function getCMSPage(int $pageId): object|int
    {
        $record = $this->db->table('fw_cms')
                           ->select('*')
                           ->where('pageId', $pageId)
                           ->get();
        return ($record->getNumRows() > 0) ? $record->getRow() : 0;
    }

    public function viewPosts(int $perPage = 50): array
    {
        $session    = session();
        $postSearch = $session->get('postSearch');

        // ✅ FIX: Use Model-based pagination, not query builder pagination
        $this->select('*, COALESCE(publishedAt, modifiedTime) as sortTime')->orderBy('sortTime', 'desc');

        if ($session->has('postSearch')) {
            if ($postSearch['searchIn'] != '') {
                $this->groupStart();
                    $this->orLike('postTitle', $postSearch['searchIn']);
                    $this->orLike('version',   $postSearch['searchIn']);
                    $this->orLike('country',   $postSearch['searchIn']);
                    $this->orLike('device',    $postSearch['searchIn']);
                    $this->orLike('model',     $postSearch['searchIn']);
                $this->groupEnd();
            }
            if (isset($postSearch['type'])) {
                if ($postSearch['type'] === 'Uploaded') {
                    $this->like('downloadButton', '"buttonUrl"');
                } elseif ($postSearch['type'] === 'Pending') {
                    $this->notLike('downloadButton', '"buttonUrl"');
                }
            }
        }

        // Use Model's paginate() method
        return $this->paginate($perPage, 'default');
    }

    public function getSinglePost(int $postId): object|string
    {
        $record = $this->db->table('fw_posts')
                           ->select('*')
                           ->where('postId', $postId)
                           ->get();
        return ($record->getNumRows() > 0) ? $record->getRow() : '0';
    }

    public function getRecentPostsOld(int|string $limit = '', int|string $record = ''): array|int
    {
        $builder = $this->db->table('fw_posts')->select('*, COALESCE(publishedAt, modifiedTime) as sortTime')->orderBy('sortTime', 'desc');

        if ($limit === '') {
            return $builder->get()->getNumRows();
        }

        return $builder->limit($limit, $record)->get()->getResult();
    }

    // ⚠️ DEPRECATED: Old filtering method - replaced by AJAX filtering
    // This method is kept for backward compatibility but should not be used
    // Use ajaxFilterHome() in LandingPages controller instead
    public function getRecentPostsOld_Deprecated(int $perPage = 15): array
    {
        $request = service('request');
        $page = $request->getGet('page') ?? 1;
        $offset = ($page - 1) * $perPage;
        
        $builder = $this->db->table('fw_posts')
                            ->select('*')
                            ->where('postStatus', 'Active') // ✅ SECURITY FIX: Only show Active posts
                            ->select('COALESCE(publishedAt, modifiedTime) as sortTime')->orderBy('sortTime', 'desc');

        if ($request->getGet('bit') != '') {
            $builder->where('bit', $request->getGet('bit'));
        }
        if ($request->getGet('os') != '') {
            $builder->where('os', $request->getGet('os'));
        }
        if ($request->getGet('csc') != '') {
            $builder->groupStart();
                $builder->where('csc', strtoupper($request->getGet('csc')));
                $builder->orWhere('country', strtoupper($request->getGet('csc')));
            $builder->groupEnd();
        }

        // ✅ FIX: Manual pagination for complex queries with filters
        return $builder->limit($perPage, $offset)->get()->getResult();
    }

    // ✅ NEW: Clean method without URL filtering (for home page)
    public function getRecentPosts(int $perPage = 15, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;
        
        return $this->db->table('fw_posts')
                       ->select('*')
                       ->where('postStatus', 'Active')
                       ->select('COALESCE(publishedAt, modifiedTime) as sortTime')->orderBy('sortTime', 'desc')
                       ->limit($perPage, $offset)
                       ->get()
                       ->getResult();
    }

    public function viewComments(string $area = '', int|string $limit = '', int|string $record = ''): array|int
    {
        $session  = session();
        $comments = $session->get('comments');

        $builder = $this->db->table('fw_post_comments')
                            ->select('fw_post_comments.*, fw_posts.*')
                            ->join('fw_posts', 'fw_posts.postId = fw_post_comments.postId', 'left')
                            ->where('fw_post_comments.userId', '0');

        if ($session->has('comments') && $area === '') {
            if ($comments['postId'] != '' && $comments['postId'] != '0' && $comments['postId'] !== null) {
                $builder->where('fw_post_comments.postId', $comments['postId']);
            }
            if ($comments['status'] !== 'All') {
                $builder->where('fw_post_comments.status', $comments['status']);
            }
        } else {
            $builder->where('fw_post_comments.status', 'Pending');
        }

        $builder->orderBy('fw_post_comments.commentId', 'desc');

        if ($limit === '') {
            return $builder->get()->getNumRows();
        }

        return $builder->limit($limit, $record)->get()->getResult();
    }

    public function getSingleComment(int $commentId): object|string
    {
        $record = $this->db->table('fw_post_comments')
                           ->select('fw_post_comments.*, fw_posts.postTitle, fw_posts.postSlug')
                           ->join('fw_posts', 'fw_posts.postId = fw_post_comments.postId', 'left')
                           ->where('fw_post_comments.commentId', $commentId)
                           ->get();
        return ($record->getNumRows() > 0) ? $record->getRow() : '0';
    }

    public function getCommentThreads(int $commentId): array
    {
        return $this->db->table('fw_post_comments')
                        ->select('*')
                        ->where('fromComment', $commentId)
                        ->orderBy('commentId', 'asc')
                        ->get()->getResult();
    }

    public function getCategoryPosts(string $category, int|string $limit = '', int|string $record = ''): array|int
    {
        $builder = $this->db->table('fw_posts')
                            ->select('*')
                            ->like('category', $category)
                            ->select('COALESCE(publishedAt, modifiedTime) as sortTime')->orderBy('sortTime', 'desc');

        if ($limit === '') {
            return $builder->get()->getNumRows();
        }

        return $builder->limit($limit, $record)->get()->getResult();
    }

    public function getModelPosts_Old_Deprecated(string $model, int|string $limit = '', int|string $record = ''): array|int
    {
        $request = service('request');
        $uri     = $request->getUri();

        $csc = ($uri->getTotalSegments() >= 3) ? trim($uri->getSegment(3)) : '';

        $builder = $this->db->table('fw_posts')->select('*, COALESCE(publishedAt, modifiedTime) as sortTime')->where('model', $model);

        if ($csc !== '' && $csc !== null) {
            $builder->groupStart();
                $builder->where('csc', strtoupper($csc));
                $builder->orWhere('country', $csc);
            $builder->groupEnd();
        }
        if ($request->getGet('bit') != '') {
            $builder->where('bit', $request->getGet('bit'));
        }
        if ($request->getGet('os') != '') {
            $builder->where('os', $request->getGet('os'));
        }
        if ($request->getGet('csc') != '') {
            $builder->groupStart();
                $builder->where('csc', strtoupper($request->getGet('csc')));
                $builder->orWhere('country', strtoupper($request->getGet('csc')));
            $builder->groupEnd();
        }

        $builder->orderBy('sortTime', 'desc');

        if ($limit === '') {
            return $builder->get()->getNumRows();
        }

        return $builder->limit($limit, $record)->get()->getResult();
    }

    // ✅ NEW: Clean method without URL filtering (for model pages)
    public function getModelPosts(string $model, string $csc = '', int|string $limit = '', int|string $record = ''): array|int
    {
        $builder = $this->db->table('fw_posts')
                            ->select('*, COALESCE(publishedAt, modifiedTime) as sortTime')
                            ->where('postStatus', 'Active')
                            ->where('model', $model);

        // Filter by CSC if provided (from URL segment, not query parameter)
        if ($csc !== '' && $csc !== null) {
            $builder->groupStart();
                $builder->where('csc', strtoupper($csc));
                $builder->orWhere('country', $csc);
            $builder->groupEnd();
        }

        $builder->orderBy('sortTime', 'desc');

        if ($limit === '') {
            return $builder->get()->getNumRows();
        }

        return $builder->limit($limit, $record)->get()->getResult();
    }

    public function viewContactUS(int|string $limit = '', int|string $record = ''): array|int
    {
        $builder = $this->db->table('fw_contact_us')->select('*')->orderBy('contactUsId', 'desc');

        if ($limit === '') {
            return $builder->get()->getNumRows();
        }

        return $builder->limit($limit, $record)->get()->getResult();
    }

    public function getContactUS(int $contactUsId): object|int
    {
        $record = $this->db->table('fw_contact_us')
                           ->select('*')
                           ->where('contactUsId', $contactUsId)
                           ->get();
        return ($record->getNumRows() > 0) ? $record->getRow() : 0;
    }

    public function viewCountries(int|string $limit = '', int|string $record = ''): array|int
    {
        $session       = session();
        $countrySearch = $session->get('countrySearch');

        $builder = $this->db->table('fw_world_country')->select('*')->orderBy('country', 'asc');

        if ($session->has('countrySearch')) {
            if ($countrySearch['searchIn'] != '') {
                $builder->groupStart();
                    $builder->orLike('country', $countrySearch['searchIn']);
                    $builder->orLike('iso2',    $countrySearch['searchIn']);
                    $builder->orLike('iso3',    $countrySearch['searchIn']);
                $builder->groupEnd();
            }
        }

        if ($limit === '') {
            return $builder->get()->getNumRows();
        }

        return $builder->limit($limit, $record)->get()->getResult();
    }

    public function viewCSCList(int|string $limit = '', int|string $record = ''): array|int
    {
        $session   = session();
        $cscSearch = $session->get('cscSearch');

        $builder = $this->db->table('fw_world_csc')->select('*')->orderBy('csc', 'asc');

        if ($session->has('cscSearch')) {
            if ($cscSearch['searchIn'] != '') {
                $builder->groupStart();
                    $builder->orLike('country', $cscSearch['searchIn']);
                    $builder->orLike('csc',     $cscSearch['searchIn']);
                $builder->groupEnd();
            }
        }

        if ($limit === '') {
            return $builder->get()->getNumRows();
        }

        return $builder->limit($limit, $record)->get()->getResult();
    }

    public function viewPostPendingLink(int|string $limit = '', int|string $record = '0'): array|int
    {
        $builder = $this->db->table('fw_posts')
                            ->select('*')
                            ->where('externalFileLink <> ', '')
                            ->where('externalFileUploaded', 'No')
                            ->orderBy('postId', 'desc');

        if ($limit === '') {
            return $builder->get()->getNumRows();
        }

        return $builder->limit($limit, $record)->get()->getResult();
    }

    public function viewBlogPosts(int|string $limit = '', int|string $record = '0'): array|int
    {
        $session    = session();
        $postSearch = $session->get('blogSearch');

        $builder = $this->db->table('fw_blogs')->select('*')->orderBy('modifiedTime', 'desc');

        if ($session->has('blogSearch')) {
            if ($postSearch['searchIn'] != '') {
                $builder->like('postTitle', $postSearch['searchIn']);
            }
        }

        if ($limit === '') {
            return $builder->get()->getNumRows();
        }

        return $builder->limit($limit, $record)->get()->getResult();
    }

    public function getSingleBlogPost(int $postId): object|string
    {
        $record = $this->db->table('fw_blogs')
                           ->select('*')
                           ->where('postId', $postId)
                           ->get();
        return ($record->getNumRows() > 0) ? $record->getRow() : '0';
    }

    public function isBlogPostSlugExist(string $slugUrl, int $postId): bool
    {
        $record = $this->db->table('fw_blogs')
                           ->select('postId')
                           ->where('postSlug', $slugUrl)
                           ->where('postId <> ', $postId)
                           ->get();
        return $record->getNumRows() > 0;
    }

    public function viewBlogPostsLanding(int|string $limit = '', int|string $record = '0'): array|int
    {
        $request = service('request');
        $uri     = $request->getUri();

        $seg2 = ($uri->getTotalSegments() >= 2) ? $uri->getSegment(2) : '';
        $seg3 = ($uri->getTotalSegments() >= 3) ? $uri->getSegment(3) : '';

        $builder = $this->db->table('fw_blogs')
                            ->select('*')
                            ->where('postStatus', 'Active')
                            ->orderBy('modifiedTime', 'desc');

        if ($seg2 === 'category' && $seg3 != '') {
            $builder->like('category', $seg3);
        }

        if ($limit === '') {
            return $builder->get()->getNumRows();
        }

        return $builder->limit($limit, $record)->get()->getResult();
    }

    public function viewFailePendingPosts(array $statusArr = ['Failed', 'Warning'], int|string $limit = '', int|string $record = '0'): array|int
    {
        $builder = $this->db->table('fw_page_crawler')
                            ->select('*')
                            ->whereIn('status', $statusArr)
                            ->orderBy('RAND()');

        if ($limit === '') {
            return $builder->get()->getNumRows();
        }

        return $builder->limit($limit, $record)->get()->getResult();
    }

    public function getPostViews(int $postId): array
    {
        return $this->db->table('fw_post_view')
                        ->select('*')
                        ->where('postId', $postId)
                        ->orderBy('viewTime', 'desc')
                        ->get()->getResult();
    }

    // ----------------------------------------------------------------
    // IMEI Data / Report
    // ----------------------------------------------------------------
    
    public function viewIMEIReport($limit = '', $record = 0)
    {
        $session = session();
        $imeiSearch = $session->get('imeiSearch');
        
        $builder = $this->db->table('fw_imei_info')
                           ->select('*')
                           ->orderBy('createdTime', 'DESC');
        
        if ($session->has('imeiSearch') && !empty($imeiSearch['searchIn'])) {
            $builder->groupStart()
                   ->like('imei', $imeiSearch['searchIn'])
                   ->orLike('serial', $imeiSearch['searchIn'])
                   ->orLike('modelName', $imeiSearch['searchIn'])
                   ->orLike('modelNumber', $imeiSearch['searchIn'])
                   ->groupEnd();
        }
        
        if ($limit === '') {
            return $builder->countAllResults();
        } else {
            $builder->limit($limit, $record);
            return $builder->get()->getResult();
        }
    }
}
