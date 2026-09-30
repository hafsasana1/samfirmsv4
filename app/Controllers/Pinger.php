<?php

namespace App\Controllers;

class Pinger extends BaseController
{
    protected $db;
    protected $apiLink;

    public function __construct()
    {
        helper(['url', 'global_function']);
        $this->db      = \Config\Database::connect();
        $this->apiLink = 'https://samsf-19a82545d463.herokuapp.com/';
        date_default_timezone_set(defaultTimeZone());
    }

    public function vtest(): void
    {
        print_r($_SERVER['DOCUMENT_ROOT']);
        mail('mu.cp15@gmail.com', 'umair testing', 'umair testing ' . date('Y-m-d H:i:s'));
    }

    public function runQry(): void
    {
        exit;
        $auto_data   = getSiteMeta('postAutomation');
        $postTitle   = $auto_data['title'];
        $postContent = $auto_data['template'];
        $metaTitle   = $auto_data['metaTitle'];
        $metaDesription = $auto_data['metaDesription'];
        $metaTags    = $auto_data['metaTags'];

        $updateQry = [
            'postTitle'      => $postTitle,
            'postContent'    => $postContent,
            'metaTags'       => $metaTags,
            'metaTitle'      => $metaTitle,
            'metaDesription' => $metaDesription,
        ];
        $this->db->table('fw_posts')->update($updateQry);
    }

    public function ivTest(): void
    {
        $ulink = 'https://www.whatmobile.com.pk/Samsung_Galaxy-A30';
        $vhtml = file_get_contents($ulink);
        $doc   = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($vhtml);
        $finder = new \DOMXPath($doc);
        $node   = $finder->query("//*[contains(@class, 'specs')]");
        print_r($doc->saveHTML($node->item(0)));
    }

    public function samSpecs(): void
    {
        // OPTIMIZED: Fetch 30 posts that need specs syncing
        $record = $this->db->table('fw_posts')
                           ->where('specs', '')
                           ->where('specsSynced', 'No')
                           ->orderBy('modifiedTime', 'desc')
                           ->limit(30)
                           ->get();

        if ($record->getNumRows() === 0) {
            return;
        }

        // OPTIMIZED: Batch fetch - Get all device specs in ONE query instead of 30 queries
        $devices = array_map(fn($rec) => $rec->device, $record->getResult());
        $deviceSpecsMap = [];
        
        if (!empty($devices)) {
            $existingSpecs = $this->db->table('fw_posts')
                                      ->select('device, specs')
                                      ->whereIn('device', $devices)
                                      ->where('specs !=', '')
                                      ->groupBy('device')
                                      ->get();
            
            foreach ($existingSpecs->getResult() as $spec) {
                $deviceSpecsMap[$spec->device] = $spec->specs;
            }
        }

        $updateBatch = [];
        
        foreach ($record->getResult() as $rec) {
            $updateQry = ['specsSynced' => 'Yes'];

            // Use cached device specs or fetch from external source
            if (isset($deviceSpecsMap[$rec->device])) {
                $updateQry['specs'] = $deviceSpecsMap[$rec->device];
            } else {
                $device = str_replace(' ', '-', ucwords(strtolower($rec->device)));
                $ulink  = 'https://www.whatmobile.com.pk/Samsung_' . $device;
                $vhtml  = @file_get_contents($ulink);
                if ($vhtml != '') {
                    try {
                        $doc    = new \DOMDocument();
                        libxml_use_internal_errors(true);
                        $doc->loadHTML($vhtml);
                        $finder = new \DOMXPath($doc);
                        $node   = $finder->query("//*[contains(@class, 'specs')]");
                        $updateQry['specs'] = $doc->saveHTML($node->item(0));
                    } catch (\Exception $e) {}
                }
            }
            
            $updateQry['postId'] = $rec->postId;
            $updateBatch[] = $updateQry;
        }

        // OPTIMIZED: Batch update instead of 30 individual queries
        if (!empty($updateBatch)) {
            $this->db->table('fw_posts')->updateBatch($updateBatch, 'postId');
        }
    }

    public function updatePostToNew(): void
    {
        $auto_data = getSiteMeta('postAutomation');
        $record    = $this->db->table('fw_posts')->get();

        $updateQry = [];
        foreach ($record->getResult() as $rec) {
            $updateQry[] = [
                'postTitle'      => $auto_data['title'],
                'postContent'    => $auto_data['template'],
                'metaTags'       => $auto_data['metaTags'],
                'metaTitle'      => $auto_data['metaTitle'],
                'metaDesription' => $auto_data['metaDesription'],
                'postId'         => $rec->postId,
            ];
        }

        if (is_array($updateQry) && count($updateQry) > 0) {
            // CI4: updateBatch
            $this->db->table('fw_posts')->updateBatch($updateQry, 'postId');
        }
    }

    public function updateExistingPost(): void
    {
        $record = $this->db->table('fw_posts')
                           ->where('externalFileLink', '')
                           ->where('isProcessed', 'No')
                           ->limit(20)
                           ->get();

        if ($record->getNumRows() === 0) {
            return;
        }

        $updateBatch = [];

        foreach ($record->getResult() as $rec) {
            $file_link = $this->apiLink . 'list/' . $rec->csc . '/' . $rec->model;
            $data      = @file_get_contents($file_link);
            $afound    = ($data != '' && $data != null);

            $fileData  = [];
            $fileData2 = [];
            if ($afound) {
                $fileData  = @json_decode($data, true);
                $file_link2 = $this->apiLink . 'binary/' . $rec->csc . '/' . $rec->model . '/' . $fileData['latest'];
                $data2     = @file_get_contents($file_link2);
                if ($data2 == '' || $data2 == null) $afound = false;
                else $fileData2 = @json_decode($data2, true);
            }

            $dataArr = ['isProcessed' => 'Yes', 'postId' => $rec->postId];
            if ($afound) {
                $dataArr['externalFileLink']     = $fileData2['download_path_decrypt'] ?? '';
                $dataArr['externalFileUploaded'] = 'No';
                $dataArr['fileSize']             = $fileData2['size_readable'] ?? '';
                $dataArr['buildDate']            = $fileData2['last_modified'] ?? '';
            }

            // OPTIMIZED: Accumulate updates for batch operation
            $updateBatch[] = $dataArr;
        }

        // OPTIMIZED: Single batch update instead of 20 individual queries
        if (!empty($updateBatch)) {
            $this->db->table('fw_posts')->updateBatch($updateBatch, 'postId');
        }
    }

    public function pageCrawl(): void
    {
        // CI4: FCPATH fix
        $webInfo  = @json_decode(@file_get_contents(RESOURCE_PATH . 'web-setting.info'), true);
        $dataArr  = [];
        $link     = 'https://www.sammobile.com/firmwares';
        $vhtml    = @file_get_contents($link);

        if ($vhtml != '' && $vhtml != null) {
            $links    = [];
            $document = new \DOMDocument();
            $document->loadHTML($vhtml);
            $xPath      = new \DOMXPath($document);
            $anchorTags = $xPath->evaluate("//div[@class=\"main-content-item__content main-content-item__content-md\"]//tr//td//a/@href");

            foreach ($anchorTags as $anchorTag) {
                $links[] = $anchorTag->nodeValue;
            }

            foreach ($links as $lnk) {
                $llink = trim(str_replace('https://www.sammobile.com/samsung/', '', $lnk), '/');
                $dArr  = @explode('/', $llink);
                $dArr['ttype'] = '1';

                // CI4: table()->where()->countAllResults()
                $numRows = $this->db->table('fw_page_crawler')->where('urlLink', $llink)->countAllResults();
                if ($numRows == 0) {
                    $dataArr[] = [
                        'urlLink'     => $llink,
                        'crawlData'   => @json_encode($dArr),
                        'createdTime' => date('Y-m-d H:i:s'),
                    ];
                }
            }
        }

        if (is_array($dataArr) && count($dataArr) > 0) {
            // CI4: insertBatch
            $this->db->table('fw_page_crawler')->insertBatch($dataArr);
        }
    }

    public function autoPost(): void
    {
        $auto_data      = getSiteMeta('postAutomation');
        $cscList        = getCSCList();
        $worldCountries = worldCountries();

        $isSingle = false;

        // CI4: service('request')->getGet()
        $crawlIdGet = trim($this->request->getGet('crawlId') ?? '');

        $builder = $this->db->table('fw_page_crawler');
        if ($crawlIdGet != '' && $crawlIdGet != null) {
            $builder->where('crawlId', $crawlIdGet);
            $isSingle = true;
        } else {
            $builder->where('status', 'Pending');
        }
        $record = $builder->limit(4)->get();

        if ($record->getNumRows() > 0) {
            // OPTIMIZED: Pre-fetch all device specs once instead of inside loop
            $allDeviceSpecs = $this->db->table('fw_posts')
                                       ->select('device, specs')
                                       ->where('specs !=', '')
                                       ->groupBy('device')
                                       ->get();
            
            $deviceSpecsCache = [];
            foreach ($allDeviceSpecs->getResult() as $spec) {
                $deviceSpecsCache[$spec->device] = $spec->specs;
            }

            foreach ($record->getResult() as $rec) {
                $crawlData = @json_decode($rec->crawlData, true);
                $urlLink   = $rec->urlLink;

                if ($crawlData['ttype'] == '2') {
                    $urlLinkArr = @explode('/', $urlLink);
                    $device     = strtoupper(str_replace('-', ' ', trim($urlLinkArr[2], '/')));
                    $model      = $urlLinkArr[0];
                    $csc        = $urlLinkArr[1];
                } else {
                    $urlLinkArr = @explode('firmware/', $urlLink);
                    $device     = strtoupper(str_replace('-', ' ', trim($urlLinkArr[0], '/')));
                    $urlLinkArr2 = @explode('/', $urlLinkArr[1]);
                    $model      = $urlLinkArr2[0];
                    $csc        = $urlLinkArr2[1];
                }

                $file_link = $this->apiLink . $csc . '/' . $model . '/list';
                $data      = @file_get_contents($file_link);

                if ($data == '' || $data == null) {
                    $this->db->table('fw_page_crawler')
                             ->where('crawlId', $rec->crawlId)
                             ->update(['status' => 'Failed', 'processingError' => 'Stage 1 : ' . $file_link]);
                    continue;
                }

                $fileData   = @json_decode($data, true);
                $flatestArr = @explode('/', $fileData[0]['firmware']);
                $bit        = $flatestArr[2][-5] ?? '';
                $vversion   = $flatestArr[0];
                $cscversion = $flatestArr[1];

                // Check if post exists
                $p_rec = $this->db->table('fw_posts')
                                  ->select('postId')
                                  ->where('version', $vversion)
                                  ->where('csc', $csc)
                                  ->where('model', $model)
                                  ->get();

                $isPostExits = false;
                $postId      = 0;
                if ($p_rec->getNumRows() > 0) {
                    $isPostExits = true;
                    $postId      = $p_rec->getRow()->postId;
                }

                if ($isPostExits == false) {
                    $file_link2 = $this->apiLink . $csc . '/' . $model . '/' . $fileData[0]['firmware'];
                    $data2      = @file_get_contents($file_link2);

                    if ($data2 == '' || $data2 == null) {
                        $this->db->table('fw_page_crawler')
                                 ->where('crawlId', $rec->crawlId)
                                 ->update(['status' => 'Failed', 'processingError' => 'Stage 2 : ' . $file_link2]);
                        continue;
                    }

                    $fileData2      = @json_decode($data2, true);
                    $downloadButton = [];
                    $countryISo     = $cscList[$csc] ?? '';

                    $postQry = array_map(
                        fn($v) => is_null($v) ? '' : $v,
                        [
                            'postTitle'           => $auto_data['title'],
                            'postContent'         => $auto_data['template'],
                            'metaTitle'           => $auto_data['metaTitle'],
                            'metaDesription'      => $auto_data['metaDesription'],
                            'metaTags'            => $auto_data['metaTags'],
                            'modifiedTime'        => date('Y-m-d H:i:s'),
                            'downloadButton'      => @json_encode($downloadButton),
                            'version'             => $vversion,
                            'cscversion'          => $cscversion,
                            'os'                  => $fileData2['version'] ?? '',
                            'bit'                 => $bit,
                            'device'              => $device,
                            'model'               => $model,
                            'fileType'            => 'Firmware',
                            'country'             => $countryISo,
                            'csc'                 => $csc,
                            'autoPost'            => 'Yes',
                            'createdTime'         => date('Y-m-d H:i:s'),
                            'externalFileLink'    => $fileData2['download_path_decrypt'] ?? '',
                            'externalFileUploaded' => 'No',
                            'fileSize'            => $fileData2['size_readable'] ?? '',
                            'buildDate'           => $fileData2['last_modified'] ?? '',
                            'specsSynced'         => 'Yes',
                        ]
                    );

                    // OPTIMIZED: Use pre-cached device specs instead of querying inside loop
                    if (isset($deviceSpecsCache[$device])) {
                        $postQry['specs'] = $deviceSpecsCache[$device];
                    } else {
                        $deviceClean = str_replace(' ', '-', ucwords(strtolower($device)));
                        $ulink       = 'https://www.whatmobile.com.pk/Samsung_' . $deviceClean;
                        $vhtml       = @file_get_contents($ulink);
                        if ($vhtml != '') {
                            try {
                                $doc    = new \DOMDocument();
                                libxml_use_internal_errors(true);
                                $doc->loadHTML($vhtml);
                                $finder = new \DOMXPath($doc);
                                $node   = $finder->query("//*[contains(@class, 'specs')]");
                                $postQry['specs'] = $doc->saveHTML($node->item(0));
                            } catch (\Exception $e) {}
                        }
                    }

                    $this->db->table('fw_posts')->insert($postQry);
                    // CI4: insertID()
                    $postId  = $this->db->insertID();
                    $mpost   = getPost($postId);
                    $postSlug = url_title(replacePostToken($mpost->postTitle, $mpost), '-', true);
                    $this->db->table('fw_posts')
                             ->where('postId', $postId)
                             ->update(['postSlug' => $postSlug . '-' . $postId]);

                } else {
                    // Firmware already exists - skip, no need to update
                    // Only update when actual data changes
                }

                $this->db->table('fw_page_crawler')
                         ->where('crawlId', $rec->crawlId)
                         ->update([
                             'status'      => 'Processed',
                             'postId'      => $postId,
                             'processTime' => date('Y-m-d H:i:s'),
                         ]);
            }
        }
    }

    public function updatePath(): void
    {
        exit;
        $record = $this->db->table('fw_posts')->where('externalFileLink !=', '')->get();
        foreach ($record->getResult() as $rec) {
            $link = str_replace(
                'https://samfware.herokuapp.com/download/',
                'http://samf.herokuapp.com/file/',
                $rec->externalFileLink
            );
            $this->db->table('fw_posts')->where('postId', $rec->postId)->update(['externalFileLink' => $link]);
        }
    }
}