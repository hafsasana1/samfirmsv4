<?php
$num1    = rand(0, 19);
$num2    = rand(0, 19);
$qanswer = $num1 + $num2;
session()->set('seq-ans', $qanswer);

$downloadButton = @json_decode($post->downloadButton, true);
$pageLike       = getvisitorLike($post->postId, service('request')->getIPAddress(), 'post');
$post_link      = postUrl($post);
$gSitekey       = '6LfFIOggAAAAAG2Rse1QdKSAWH8ibnW2kPEe9x0x';
$postTitle      = replacePostToken($post->postTitle, $post);
$fileSizeByte   = toByteSize($post->fileSize);

// ✅ SEO: Prepare structured data variables
$worldCountries = worldCountries();
$countryName = $worldCountries[$post->country ?? 'US']['name'] ?? 'Unknown';
$publishDate = $post->publishedAt ?? $post->createdTime ?? date('Y-m-d');
$modifiedDate = $post->modifiedTime ?? $publishDate;
?>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "<?= esc($post->device) ?> Firmware <?= esc($post->version) ?>",
  "operatingSystem": "Android <?= esc($post->os) ?>",
  "applicationCategory": "Firmware",
  "description": "<?= esc(strip_tags($meta_description ?? '')) ?>",
  "softwareVersion": "<?= esc($post->version) ?>",
  "releaseNotes": "Official Samsung firmware for <?= esc($post->model) ?> (<?= esc($post->device) ?>)",
  "datePublished": "<?= date('c', strtotime($publishDate)) ?>",
  "dateModified": "<?= date('c', strtotime($modifiedDate)) ?>",
  "author": {
    "@type": "Organization",
    "name": "Samsung Electronics"
  },
  "publisher": {
    "@type": "Organization",
    "name": "<?= esc($web->webTitle ?? 'SamFirms') ?>",
    "url": "<?= base_url() ?>"
  },
  "offers": {
    "@type": "Offer",
    "price": "0",
    "priceCurrency": "USD",
    "availability": "https://schema.org/InStock"
  }<?php if (!empty($post->fileSize)): ?>,
  "fileSize": "<?= esc($post->fileSize) ?>"<?php endif; ?><?php if (!empty($fileSizeByte)): ?>,
  "contentSize": "<?= intval($fileSizeByte) ?>"<?php endif; ?>
}
</script>

<?php
?>
<!-- Single Post Page -->
<div class="lg:col-span-9 lg:order-1">
    <h1 class="text-2xl lg:text-3xl font-bold text-ink mb-6 hidden">
        <?= esc($post->device ?? 'Samsung Device') ?> (<?= esc($post->model) ?>) Firmware <?= esc($post->version) ?> - Android <?= esc($post->os) ?>
    </h1>
    
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 bg-white rounded-xl shadow-sm border border-gray-100 px-4 py-3">
            <li class="inline-flex items-center">
                <a href="<?= base_url() ?>" class="inline-flex items-center text-sm font-semibold text-ink hover:text-accent transition-all hover:scale-105 group">
                    <svg class="w-4 h-4 mr-2 text-accent group-hover:animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    <span>Home</span>
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-accent/40" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="<?= base_url().'firmware/'.$post->model ?>" class="ml-1 inline-flex items-center space-x-1.5 text-sm font-bold text-white bg-accent hover:bg-accent-hover px-3 py-1.5 rounded-lg transition-all hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span><?= $post->model ?></span>
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-accent/40" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="<?= base_url().'firmware/'.$post->model.'/'.$post->csc ?>" class="ml-1 inline-flex items-center space-x-1.5 text-sm font-bold text-white bg-highlight hover:bg-highlight/90 px-3 py-1.5 rounded-lg transition-all hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                        </svg>
                        <span><?= $post->csc ?></span>
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-accent/40" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 inline-flex items-center space-x-1.5 text-sm font-bold text-ink bg-canvas px-3 py-1.5 rounded-lg border-2 border-accent/20">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <span><?= $post->version ?></span>
                    </span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- ✅ FIX #2: Duplicate BreadcrumbList schema removed - keeping only one at top -->

    <div class="space-y-6 move-to-area">
        <!-- Device Specifications -->
        <?php if ($post->specs != '') { ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-header-specs">
            <div class="px-6 py-4 bg-highlight-soft border-b border-highlight/20">
                <h3 class="text-xl font-bold text-ink flex items-center space-x-2">
                    <span class="w-2 h-2 bg-highlight rounded-full animate-pulse"></span>
                    <svg class="w-6 h-6 text-highlight" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>Samsung <?= formatDeviceDisplay($post->device) ?> Detailed Specifications</span>
                </h3>
            </div>
            <div class="p-6">
                <div class="max-h-96 overflow-y-auto prose prose-sm max-w-none sm-specifications">
                    <?= $post->specs ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- Main Content Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-accent-soft border-b border-gray-100">
                <h1 class="text-lg font-bold text-ink"><?= $postTitle ?></h1>
            </div>

            <div class="p-6 lg:p-8">
                <!-- Post Content -->
                <div class="prose prose-lg max-w-none mb-0">
                    <?php
                    $cntryRecord = ($countries ?? [])[$post->country] ?? [];
                    $cntryCode   = strtolower($cntryRecord['code'] ?? 'us');
                    $cntryName   = strtolower($cntryRecord['name'] ?? 'unknown');

                    $postContent = $post->postContent;
                    $contentData = innerTableContent();
                    $contentData = replacePostToken($contentData, $post);
                    $postContent = str_replace('{shortcode-device-data}', $contentData, $postContent);
                    $postContent = replacePostToken($postContent, $post);

                    $ext_countries = extractCountry($postContent);
                    if (count($ext_countries[1]) > 0) {
                        foreach ($ext_countries[1] as $cnt) {
                            $countryName = ucfirst($cnt);
                            $postContent = str_replace('{'.strtolower($cnt).'}', '<img class="inline-block rounded shadow-sm" loading="lazy" alt="'.$countryName.' flag" src="'.base_url('assets/img/flags/4x3/'.(strtolower($cnt)).'.svg').'" width="24" height="18">', $postContent);
                            $postContent = str_replace('{'.strtoupper($cnt).'}', '<img class="inline-block rounded shadow-sm" loading="lazy" alt="'.$countryName.' flag" src="'.base_url('assets/img/flags/4x3/'.(strtolower($cnt)).'.svg').'" width="24" height="18">', $postContent);
                        }
                    }
                    
                    // ✅ FIX: Remove empty HTML elements that create blank lines (AFTER shortcode replacement)
                    $postContent = preg_replace('/<pre[^>]*>(\s|&nbsp;|<br\s*\/?>)*<\/pre>/i', '', $postContent);
                    $postContent = preg_replace('/<p[^>]*>(\s|&nbsp;|<br\s*\/?>)*<\/p>/i', '', $postContent);
                    $postContent = preg_replace('/<div[^>]*>(\s|&nbsp;|<br\s*\/?>)*<\/div>/i', '', $postContent);
                    $postContent = preg_replace('/<span[^>]*>(\s|&nbsp;|<br\s*\/?>)*<\/span>/i', '', $postContent);
                    
                    echo $postContent;
                    ?>
                </div>

                <!-- Ads Sections -->
                <?php if ($post->separateAds == 'Yes' && $post->adsContent != '' && trim(strip_tags($post->adsContent)) != '') { ?>
                <div class="my-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                    <?= $post->adsContent ?>
                </div>
                <?php } ?>
                
                <?php if (!empty($post_ads) && trim(strip_tags($post_ads)) != '') { ?>
                <div class="my-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                    <?= $post_ads ?>
                </div>
                <?php } ?>

                <!-- Enhanced 2-Button Download Section -->
                <div class="mt-4">
                    <div class="download-section bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-8">
                        <h2 class="text-2xl font-bold text-center mb-2 text-ink flex items-center justify-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                            Download Firmware
                        </h2>
                        <p class="text-center text-sm text-gray-600 mb-6">Choose your preferred download method</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- BUTTON 1: Direct Download (ydfile.com) -->
                            <div class="download-option bg-white rounded-xl p-6 shadow-lg border-2 transition-all hover:shadow-xl <?= (strpos($post->externalFileLink ?? '', 'ydfile.com') !== false) ? 'border-green-500' : 'border-gray-300' ?>">
                                
                                <?php if (!empty($post->externalFileLink) && strpos($post->externalFileLink, 'ydfile.com') !== false): ?>
                                    <!-- ydfile link AVAILABLE -->
                                    <div class="text-center">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                        <h3 class="font-bold text-lg mb-2 text-green-600 flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            Direct Download
                                        </h3>
                                        <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded mb-3">
                                            RECOMMENDED
                                        </span>
                                        <p class="text-sm text-gray-600 mb-4">
                                            Fast • Easy • No tools needed
                                        </p>
                                        
                                        <form action="<?= base_url('download-now') ?>" method="post">
                                            <input type="hidden" name="postId" value="<?= $post->postId ?>">
                                            <button type="submit" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 6px !important; background-color: #22c55e !important; color: #ffffff !important; padding: 8px 14px !important; border-radius: 6px !important; font-weight: 600 !important; font-size: 13px !important; text-align: center !important; text-decoration: none !important; transition: all 0.2s !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important; white-space: nowrap !important; border: none !important; cursor: pointer !important; width: auto !important;"
                                                   onmouseover="this.style.backgroundColor='#16a34a'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)'" 
                                                   onmouseout="this.style.backgroundColor='#22c55e'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'">
                                                <svg style="width: 16px !important; height: 16px !important; flex-shrink: 0 !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                <span style="color: #ffffff !important;">Download Now</span>
                                            </button>
                                        </form>
                                        
                                        <div class="mt-4 text-xs text-gray-600 space-y-1">
                                            <?php if (!empty($post->fileSize)): ?>
                                            <p class="flex items-center justify-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/></svg>
                                                File size: <span class="font-semibold"><?= $post->fileSize ?></span>
                                            </p>
                                            <?php endif; ?>
                                            <p class="flex items-center justify-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                                Downloaded <span class="font-bold text-highlight"><?= number_format($post->downloadCount + 129) ?></span> times
                                            </p>
                                        </div>
                                    </div>
                                    
                                <?php else: ?>
                                    <!-- ydfile link NOT AVAILABLE -->
                                    <div class="text-center opacity-70">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 animate-spin" style="animation-duration: 3s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <h3 class="font-bold text-lg mb-2 text-gray-600">
                                            Direct Download
                                        </h3>
                                        <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded mb-3">
                                            COMING SOON
                                        </span>
                                        <p class="text-sm text-gray-600 mb-4">
                                            Firmware uploading...
                                        </p>
                                        
                                        <button disabled style="display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 6px !important; background-color: #d1d5db !important; color: #6b7280 !important; padding: 8px 14px !important; border-radius: 6px !important; font-weight: 600 !important; font-size: 13px !important; text-align: center !important; text-decoration: none !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important; white-space: nowrap !important; border: none !important; cursor: not-allowed !important; width: auto !important; opacity: 0.7 !important;">
                                            <svg style="width: 16px !important; height: 16px !important; flex-shrink: 0 !important;" class="animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            <span style="color: #6b7280 !important;">Uploading...</span>
                                        </button>
                                        
                                        <p class="text-xs text-gray-500 mt-4">
                                            Check back in 24-48 hours<br>
                                            <span class="text-blue-600">→ Use download tool option for now</span>
                                        </p>
                                    </div>
                                <?php endif; ?>
                                
                            </div>

                            <!-- BUTTON 2: Tool Download (ALWAYS AVAILABLE) -->
                            <div class="download-option bg-white rounded-xl p-6 shadow-lg border-2 border-blue-500 transition-all hover:shadow-xl">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <h3 class="font-bold text-lg mb-2 text-blue-600">
                                        Download via Tool
                                    </h3>
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded mb-3">
                                        ALWAYS AVAILABLE
                                    </span>
                                    <p class="text-sm text-gray-600 mb-4">
                                        For advanced users • Direct from Samsung
                                    </p>
                                    
                                    <button onclick="toggleToolInstructions()" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 6px !important; background-color: #3b82f6 !important; color: #ffffff !important; padding: 8px 14px !important; border-radius: 6px !important; font-weight: 600 !important; font-size: 13px !important; text-align: center !important; text-decoration: none !important; transition: all 0.2s !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important; white-space: nowrap !important; border: none !important; cursor: pointer !important; width: auto !important;"
                                           onmouseover="this.style.backgroundColor='#2563eb'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)'" 
                                           onmouseout="this.style.backgroundColor='#3b82f6'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'">
                                        <svg style="width: 16px !important; height: 16px !important; flex-shrink: 0 !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                        <span style="color: #ffffff !important;">Use SamFirms Tool</span>
                                    </button>
                                    
                                    <p class="text-xs text-gray-600 mt-4">
                                        Official Samsung servers<br>
                                        100% Safe & Legal
                                    </p>
                                </div>
                            </div>
                            
                        </div>

                        <!-- Tool Instructions (Hidden by default) -->
                        <div id="toolInstructions" class="hidden mt-6 bg-blue-50 rounded-xl p-6 border-2 border-blue-200" style="animation: fadeIn 0.3s ease-out;">
                            <button onclick="toggleToolInstructions()" class="float-right text-gray-500 hover:text-gray-700 w-6 h-6 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            
                            <h4 class="font-bold text-lg mb-3 text-blue-800 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                How to Download Using Tools
                            </h4>
                            
                            <ol class="list-decimal list-inside space-y-2 mb-4 text-sm text-gray-700">
                                <li>Download and install <strong>Bifrost</strong> or <strong>SamFirm</strong> tool (links below)</li>
                                <li>Open the tool and enter the firmware details shown below</li>
                                <li>Click download - tool will fetch directly from Samsung servers</li>
                                <li>Flash using Odin tool (download separately)</li>
                            </ol>
                            
                            <div class="bg-white rounded-lg p-4 mb-4 border border-blue-200">
                                <p class="font-bold text-sm mb-3 text-blue-800 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Copy these firmware details into the tool:
                                </p>
                                <table class="w-full text-sm">
                                    <tr class="border-b">
                                        <td class="font-semibold py-2 pr-4 text-gray-700">Model:</td>
                                        <td class="py-2">
                                            <code class="bg-gray-100 px-3 py-1 rounded font-mono text-accent"><?= $post->model ?></code>
                                            <button onclick="copyToClipboard('<?= $post->model ?>')" class="ml-2 text-blue-600 hover:text-blue-800 inline-flex items-center" title="Copy">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="font-semibold py-2 pr-4 text-gray-700">Region (CSC):</td>
                                        <td class="py-2">
                                            <code class="bg-gray-100 px-3 py-1 rounded font-mono text-accent"><?= $post->csc ?></code>
                                            <button onclick="copyToClipboard('<?= $post->csc ?>')" class="ml-2 text-blue-600 hover:text-blue-800 inline-flex items-center" title="Copy">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-semibold py-2 pr-4 text-gray-700">Version (PDA):</td>
                                        <td class="py-2">
                                            <code class="bg-gray-100 px-3 py-1 rounded font-mono text-accent"><?= $post->pdaVersion ?? $post->version ?></code>
                                            <button onclick="copyToClipboard('<?= $post->pdaVersion ?? $post->version ?>')" class="ml-2 text-blue-600 hover:text-blue-800 inline-flex items-center" title="Copy">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3" style="display: grid !important; grid-template-columns: repeat(1, minmax(0, 1fr)) !important; gap: 12px !important;">
                            <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
                                <a href="https://github.com/zacharee/SamloaderKotlin/releases" target="_blank" 
                                   style="display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 6px !important; background-color: #7c3aed !important; color: #ffffff !important; padding: 8px 14px !important; border-radius: 6px !important; font-weight: 600 !important; font-size: 13px !important; text-align: center !important; text-decoration: none !important; transition: all 0.2s !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important; white-space: nowrap !important;"
                                   onmouseover="this.style.backgroundColor='#6d28d9'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)'" 
                                   onmouseout="this.style.backgroundColor='#7c3aed'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'">
                                    <svg style="width: 16px !important; height: 16px !important; flex-shrink: 0 !important; fill: currentColor !important;" viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                                    <span style="color: #ffffff !important;">Download Bifrost</span>
                                </a>
                                <a href="https://github.com/DavidArsene/samfirm.js/releases" target="_blank" 
                                   style="display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 6px !important; background-color: #4f46e5 !important; color: #ffffff !important; padding: 8px 14px !important; border-radius: 6px !important; font-weight: 600 !important; font-size: 13px !important; text-align: center !important; text-decoration: none !important; transition: all 0.2s !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important; white-space: nowrap !important;"
                                   onmouseover="this.style.backgroundColor='#4338ca'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)'" 
                                   onmouseout="this.style.backgroundColor='#4f46e5'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'">
                                    <svg style="width: 16px !important; height: 16px !important; flex-shrink: 0 !important; fill: currentColor !important;" viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                                    <span style="color: #ffffff !important;">Download SamFirm</span>
                                </a>
                                <a href="https://www.youtube.com/results?search_query=bifrost+samsung+firmware+download" target="_blank" 
                                   style="display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 6px !important; background-color: #dc2626 !important; color: #ffffff !important; padding: 8px 14px !important; border-radius: 6px !important; font-weight: 600 !important; font-size: 13px !important; text-align: center !important; text-decoration: none !important; transition: all 0.2s !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important; white-space: nowrap !important;"
                                   onmouseover="this.style.backgroundColor='#b91c1c'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)'" 
                                   onmouseout="this.style.backgroundColor='#dc2626'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'">
                                    <svg style="width: 16px !important; height: 16px !important; flex-shrink: 0 !important; fill: currentColor !important;" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    <span style="color: #ffffff !important;">Watch Tutorial</span>
                                </a>
                            </div>
                            </div>
                            
                            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3 flex gap-2">
                                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <p class="text-xs text-yellow-800">
                                    <strong>Note:</strong> These tools download firmware directly from Samsung's official servers. Always backup your data before flashing firmware.
                                </p>
                            </div>
                        </div>

                        <!-- Download count -->
                        <div class="text-center mt-6">
                            <p class="text-sm text-gray-600 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                This firmware has been downloaded <span class="font-bold text-highlight"><?= number_format($post->downloadCount + 129) ?></span> times
                            </p>
                        </div>
                    </div>
                </div>

                <script>
                function toggleToolInstructions() {
                    document.getElementById('toolInstructions').classList.toggle('hidden');
                }

                function copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(function() {
                        const btn = event.target.closest('button');
                        const original = btn.innerHTML;
                        btn.innerHTML = '<svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
                        setTimeout(function() { btn.innerHTML = original; }, 2000);
                    });
                }
                </script>

                <style>
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(-10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                </style>

                <!-- Social Share Buttons -->
                <div class="mt-8 flex items-center justify-center space-x-4">
                    <a class="btn-post-action flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 hover:bg-red-500 hover:text-white transition-all <?= $pageLike == 'dislike' ? 'bg-red-500 text-white' : 'text-gray-700' ?>" 
                       href="javascript:;" 
                       onclick="postAction(this,'post','dislike',<?= $post->postId ?>)">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-1.105-1.79l-.05-.025A4 4 0 0011.055 2H5.64a2 2 0 00-1.962 1.608l-1.2 6A2 2 0 004.44 12H8v4a2 2 0 002 2 1 1 0 001-1v-.667a4 4 0 01.8-2.4l1.4-1.866a4 4 0 00.8-2.4z"></path>
                        </svg>
                    </a>

                    <a class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-all hover:scale-110" 
                       href="http://www.facebook.com/sharer.php?u=<?= $post_link ?>" 
                       target="_blank">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>

                    <a class="flex items-center justify-center w-12 h-12 rounded-full bg-sky-500 text-white hover:bg-sky-600 transition-all hover:scale-110" 
                       href="http://twitter.com/share?url=<?= $post_link ?>&text=<?= $postTitle ?>&hashtags=<?= $web->webTitle ?? '' ?>" 
                       target="_blank">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>

                    <a class="flex items-center justify-center w-12 h-12 rounded-full bg-red-600 text-white hover:bg-red-700 transition-all hover:scale-110" 
                       href="http://pinterest.com/pin/create/button/?url=<?= $post_link ?>" 
                       target="_blank">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0a12 12 0 00-4.37 23.172c-.175-1.588-.033-3.498.392-5.232l2.943-12.47s-.736-1.473-.736-3.651c0-3.422 1.983-5.982 4.452-5.982 2.099 0 3.112 1.575 3.112 3.463 0 2.108-1.342 5.258-2.033 8.178-.578 2.447 1.226 4.441 3.64 4.441 4.37 0 7.314-5.592 7.314-12.204 0-5.026-3.387-8.79-9.516-8.79-6.93 0-11.222 5.177-11.222 10.933 0 1.988.589 3.391 1.516 4.476a.686.686 0 01.196.66c-.083.32-.265 1.059-.302 1.207-.05.196-.163.237-.378.143-2.651-1.082-3.876-4.002-3.876-7.276 0-5.404 4.536-11.919 13.491-11.919 7.193 0 11.91 5.227 11.91 10.838 0 7.419-4.131 12.99-10.186 12.99-2.038 0-3.956-1.097-4.613-2.35 0 0-1.096 4.312-1.333 5.138-.406 1.418-1.199 2.861-1.902 3.944A12.002 12.002 0 0024 12c0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </a>

                    <a class="btn-post-action flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 hover:bg-green-500 hover:text-white transition-all <?= $pageLike == 'like' ? 'bg-green-500 text-white' : 'text-gray-700' ?>" 
                       href="javascript:;" 
                       onclick="postAction(this,'post','like',<?= $post->postId ?>)">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <?php if ($post->commentStatus == 'Enabled') { ?>
        <div id="comments" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-accent-soft border-b border-accent/20">
                <h3 class="text-xl font-bold text-ink flex items-center space-x-2">
                    <span class="w-2 h-2 bg-accent rounded-full animate-pulse"></span>
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span>Comments</span>
                </h3>
            </div>

            <div class="p-6">
                <?php
                $comments = getPostComments($post->postId);
                if (count($comments) > 0) {
                    echo '<div class="space-y-4 mb-8">';
                    foreach ($comments as $comm) {
                        $comTitle = $comm->userId != '0' ? ($web->webTitle ?? '') : $comm->fromName;
                        echo '<div class="bg-gray-50 rounded-xl p-4 border border-gray-200">';
                        echo '<div class="flex items-start justify-between mb-2">';
                        echo '<p class="font-semibold text-gray-900">'.$comTitle.'</p>';
                        echo '<span class="text-sm text-gray-500">'.timeAgo($comm->commentTime).'</span>';
                        echo '</div>';
                        echo '<p class="text-gray-700">'.$comm->comment.'</p>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
                ?>

                <!-- Comment Form -->
                <div class="bg-canvas rounded-xl p-6 border border-gray-200">
                    <div class="alert alert-success success-comment hidden mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                        Thank you for commenting. Your review will be posted after approval.
                    </div>

                    <form action="" class="comment-form space-y-4" method="post">
                        <input type="hidden" name="postId" value="<?= $post->postId ?>">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-ink-muted mb-2">Name *</label>
                                <input type="text" name="fromName" class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-accent focus:ring-4 focus:ring-accent-soft outline-none transition-all comment-fields" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-ink-muted mb-2">Email *</label>
                                <input type="email" name="fromEmail" class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-accent focus:ring-4 focus:ring-accent-soft outline-none transition-all comment-fields" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-ink-muted mb-2">Comment *</label>
                            <textarea name="comment" rows="4" class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-accent focus:ring-4 focus:ring-accent-soft outline-none transition-all resize-none comment-fields" required></textarea>
                        </div>

                        <div class="bg-gray-100 rounded-lg p-4">
                            <label class="block text-sm font-semibold text-ink-muted mb-3">Security Question *</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 bg-navy-900 text-white px-6 py-3 rounded-lg font-mono text-lg font-bold">
                                    <?= $num1.' + '.$num2 ?> =
                                </div>
                                <input type="text" name="sanswer" class="flex-1 px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-accent focus:ring-4 focus:ring-accent-soft outline-none transition-all text-center font-semibold comment-fields" required>
                            </div>
                        </div>

                        <button type="submit" class="w-full px-6 py-3 bg-accent text-white rounded-xl font-semibold hover:bg-accent-hover hover:scale-105 transition-all duration-200">
                            Submit Comment
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
function requestDownload(dType){
    const download_type = dType || 'downloadweb';
    grecaptcha.ready(function() {
        grecaptcha.execute('<?= $gSitekey ?>').then(function(token) {
            const formData = new FormData();
            formData.append('id', '<?= $post->postId ?>');
            formData.append('token', token);
            
            fetch('<?= base_url('home/downloadnow') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                const result = JSON.parse(data);
                const button = document.getElementById(download_type + "_button");
                
                if (result.code == 200) {
                    window.location.href = result.url;
                } else if (button) {
                    button.innerHTML = "<svg class='inline w-4 h-4 mr-1' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'></path></svg> [" + result.code + "] Error";
                }
            })
            .catch(error => {
                const button = document.getElementById(download_type + "_button");
                if (button) {
                    button.innerHTML = "<svg class='inline w-4 h-4 mr-1' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'></path></svg> Download Error";
                }
            });
        });
    });
}
</script>
