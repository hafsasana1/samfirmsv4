<?php
$request_obj = service('request');
$modalLogo   = base_url().'assets/samsung-preview.png';
$pagePath    = base_url('firmware');
?>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "<?= esc($web->webTitle ?? 'SamFirms') ?>",
  "url": "<?= base_url() ?>",
  "description": "<?= esc($meta_description ?? '') ?>",
  "potentialAction": {
    "@type": "SearchAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "<?= base_url('load/model') ?>?query={search_term_string}"
    },
    "query-input": "required name=search_term_string"
  }
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "<?= esc($web->webTitle ?? 'SamFirms') ?>",
  "url": "<?= base_url() ?>",
  "logo": "<?= base_url('resource/thq-logo.jpg') ?>",
  "sameAs": [
    "https://twitter.com/samfirms",
    "https://facebook.com/samfirms"
  ],
  "contactPoint": {
    "@type": "ContactPoint",
    "contactType": "Customer Service",
    "url": "<?= base_url('contact-us') ?>"
  }
}
</script>

<!-- Main Content Area -->
<div class="lg:col-span-9 lg:order-1 space-y-6">
    <h1 class="text-3xl lg:text-4xl font-bold text-ink mb-6 hidden">
        Download Samsung Firmware - Latest Official Stock ROMs Free
    </h1>
    
    <!-- Recently Added Models - Live Monitor Style -->
    <?php if (!empty($recentModels)) { ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Live Monitor Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-accent-soft to-white border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <span class="live-pulse-dot"></span>
                        <span class="live-pulse-ring"></span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-ink flex items-center space-x-2">
                            <span class="live-badge">LIVE</span>
                            <span>Recently Added Models</span>
                        </h2>
                        <p class="text-xs text-ink-muted mt-0.5">Monitoring server for new firmware updates</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs font-semibold text-accent"><?= count($recentModels) ?> New Models</div>
                    <div class="text-xs text-ink-muted">Last 7 days</div>
                </div>
            </div>
        </div>

        <!-- Model Badges Grid -->
        <div class="p-6 lg:p-8">
            <div class="model-badge-grid">
                <?php 
                $delay = 0;
                foreach ($recentModels as $model) { 
                    $modelUrl = base_url('firmware/'.$model->model);
                    $timestamp = strtotime($model->latest_time);
                    $now = time();
                    $diff = $now - $timestamp;
                    
                    // Calculate time ago
                    if ($diff < 3600) {
                        $timeAgo = floor($diff / 60) . ' min ago';
                    } elseif ($diff < 86400) {
                        $timeAgo = floor($diff / 3600) . ' hrs ago';
                    } else {
                        $timeAgo = floor($diff / 86400) . ' days ago';
                    }
                    
                    $delay += 0.1;
                ?>
                <a href="<?= $modelUrl ?>" class="model-badge-item" style="animation-delay: <?= $delay ?>s">
                    <div class="model-badge-content">
                        <div class="flex items-start justify-between mb-2">
                            <span class="new-indicator">NEW</span>
                            <span class="model-time"><?= $timeAgo ?></span>
                        </div>
                        <div class="model-number"><?= $model->model ?></div>
                        <div class="model-device"><?= $model->device ?></div>
                        <div class="model-badge-glow"></div>
                    </div>
                </a>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php } ?>

    <!-- Latest Model Ads Section -->
    <?php if (!empty($latest_model_ads) && trim(strip_tags($latest_model_ads)) != '') { ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <?= $latest_model_ads ?>
    </div>
    <?php } ?>

    <!-- Latest Blog Posts - COMMENTED OUT -->
    <?php /* if (!empty($blog_record)) { ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-ink flex items-center space-x-2">
                <span class="w-2 h-2 bg-highlight rounded-full animate-pulse"></span>
                <svg class="w-6 h-6 text-highlight" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
                <span>Latest News & Updates</span>
            </h2>
            <a href="<?= base_url('blog') ?>" class="text-sm font-medium text-accent hover:text-accent-hover flex items-center space-x-1 group">
                <span>View All</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($blog_record as $rec) {
                $doc = new DOMDocument();
                @$doc->loadHTML($rec->postContent);
                $tags   = $doc->getElementsByTagName('img');
                $imgUrl = '';
                foreach ($tags as $tag) { $imgUrl = $tag->getAttribute('src'); if ($imgUrl != '') break; }
                // ✅ FIX #4: REMOVED base64 encoding - Use default logo URL instead
            // Old method: Read file + base64 encode = 50KB HTML bloat + no caching
            // New method: Direct URL = cacheable, -50KB HTML, -100ms load time
            $imgUrl = $imgUrl == '' ? base_url('resource/logo.png') : $imgUrl;
            ?>
                <a href="<?= base_url('blog/'.$rec->postSlug) ?>" class="group block bg-gray-50 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="aspect-video overflow-hidden bg-canvas">
                        <img src="<?= $imgUrl ?>" 
                             loading="lazy" 
                             alt="<?= $rec->postTitle ?>" 
                             width="280" 
                             height="160"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h3 class="text-sm font-semibold text-ink line-clamp-2 group-hover:text-accent transition-colors">
                            <?= $rec->postTitle ?>
                        </h3>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>
    <?php } */ ?>

    <!-- Advanced Filter Section - COMMENTED OUT (Homepage Only) -->
    <!-- 
    <div class="bg-surface rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-accent-soft border-b border-accent/20">
            <h2 class="text-xl font-bold text-ink flex items-center space-x-2">
                <span class="w-2 h-2 bg-accent rounded-full animate-pulse"></span>
                <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <span>Filter Firmware</span>
            </h2>
        </div>
        <div class="p-6">
            <style>
                /* Custom Select Dropdown Styling - Light Branded Theme */
                .filter-area select {
                    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%231FBF8F' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
                    background-position: right 1rem center;
                    background-repeat: no-repeat;
                    background-size: 1.5em 1.5em;
                    padding-right: 3rem;
                    appearance: none;
                    -webkit-appearance: none;
                    -moz-appearance: none;
                }
                
                .filter-area select option {
                    background-color: #f0fdf9;
                    color: #0f172a;
                    padding: 12px;
                    font-weight: 500;
                }
                
                .filter-area select option:hover,
                .filter-area select option:checked {
                    background: linear-gradient(135deg, #e0f8f3 0%, #d0f4ec 100%);
                    color: #1FBF8F;
                    font-weight: 600;
                }
            </style>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-ink mb-3 flex items-center space-x-2">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        <span>BIT/SW REV.</span>
                    </label>
                    <select class="filter-bit-rate w-full px-5 py-3.5 rounded-xl border-2 border-accent/30 focus:border-accent focus:ring-4 focus:ring-accent-soft outline-none transition-all duration-200 bg-white text-ink shadow-sm hover:border-accent cursor-pointer font-semibold" onchange="setFilter()">
                        <option value="">All</option>
                        <?php for ($i = 1; $i <= 10; $i++) {
                            echo '<option value="'.$i.'" '.($request_obj->getGet('bit') == $i ? 'selected=""' : '').'>'.$i.'</option>';
                        } ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-ink mb-3 flex items-center space-x-2">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span>ANDROID VERSION</span>
                    </label>
                    <select class="filter-os w-full px-5 py-3.5 rounded-xl border-2 border-accent/30 focus:border-accent focus:ring-4 focus:ring-accent-soft outline-none transition-all duration-200 bg-white text-ink shadow-sm hover:border-accent cursor-pointer font-semibold" onchange="setFilter()">
                        <option value="">All</option>
                        <?php foreach ($osList as $osl) {
                            echo '<option value="'.$osl->os.'" '.($request_obj->getGet('os') == $osl->os ? 'selected=""' : '').'>'.$osl->os.'</option>';
                        } ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-ink mb-3 flex items-center space-x-2">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>COUNTRY/CSC</span>
                    </label>
                    <input type="text" class="filter-csc w-full px-5 py-3.5 rounded-xl border-2 border-accent/30 focus:border-accent focus:ring-4 focus:ring-accent-soft outline-none transition-all duration-200 bg-white text-ink placeholder-ink-muted shadow-sm hover:border-accent font-semibold" placeholder="Type country or CSC code" value="<?= $request_obj->getGet('csc') ?>" onkeyup="return submitFilter(event)">
                </div>
            </div>
        </div>
    </div>
    -->

    <!-- Latest Firmware Updates Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-accent-soft border-b border-gray-100">
            <h2 class="text-2xl font-bold text-ink flex items-center space-x-2">
                <span class="w-2 h-2 bg-accent rounded-full animate-pulse"></span>
                <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 11l3 3m0 0l3-3m-3 3V8"></path>
                </svg>
                <span>Latest Firmware Updates</span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-navy-900">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Model</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Device</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">CSC</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Version</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">OS</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Size</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <?php foreach ($record as $rec) {
                        $post_link = postUrl($rec);
                        echo '<tr class="hover:bg-accent-soft cursor-pointer transition-all duration-200 link-click" data-link="'.$post_link.'">';
                            echo '<td class="px-6 py-4"><a href="'.base_url('firmware/'.$rec->model).'" class="text-accent hover:text-accent-hover font-bold text-sm">'.$rec->model.'</a></td>';
                            echo '<td class="px-6 py-4 text-ink font-medium text-sm">'.($rec->device != '' ? $rec->device : $rec->postTitle).'</td>';
                            echo '<td class="px-6 py-4 text-center"><a href="'.base_url('firmware/'.$rec->model.'/'.$rec->csc).'" class="inline-flex flex-col items-center space-y-1 hover:scale-110 transition-transform"><img class="rounded shadow-sm border border-gray-200" loading="lazy" alt="'.ucfirst(worldCountries()[$rec->country]['name'] ?? 'us').' flag" src="'.base_url('assets/img/flags/4x3/'.(strtolower(worldCountries()[$rec->country]['code'] ?? 'us')).'.svg').'" width="28" height="21"><span class="text-xs font-bold text-ink mt-1">'.$rec->csc.'</span></a></td>';
                            echo '<td class="px-6 py-4 text-ink-muted font-mono text-sm">'.$rec->version.'</td>';
                            echo '<td class="px-6 py-4"><span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-accent-soft text-accent border border-accent/20">'.$rec->os.'</span></td>';
                            echo '<td class="px-6 py-4 text-ink-muted text-sm font-medium">'.($rec->fileSize != '' ? $rec->fileSize : '<span class="text-highlight font-semibold">Uploading...</span>').'</td>';
                            echo '<td class="px-6 py-4 text-ink-muted text-sm">'.date('Y-m-d', strtotime($rec->modifiedTime)).'</td>';
                        echo '</tr>';
                    } ?>
                </tbody>
            </table>
            
        </div>
    </div>

    <!-- Welcome Content Card - MOVED AFTER FIRMWARE TABLE -->
    <?php if (!empty($homePage->pageContent)) { ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
        <div class="px-6 py-4 bg-accent-soft border-b border-accent/20">
            <h2 class="text-xl font-bold text-ink flex items-center space-x-2">
                <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span>About <?= $web->webTitle ?? 'SamFirms' ?></span>
            </h2>
        </div>
        <div class="p-6 lg:p-8">
            <div class="max-h-72 overflow-y-auto prose prose-lg max-w-none pr-4 custom-scrollbar">
                <?= $homePage->pageContent ?>
            </div>
        </div>
        
        <!-- Custom Scrollbar Styles -->
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 8px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #ff767a;
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #ff5a5f;
            }
            /* Firefox */
            .custom-scrollbar {
                scrollbar-width: thin;
                scrollbar-color: #ff767a #f1f5f9;
            }
        </style>
    </div>
    <?php } ?>
</div>

<script type="text/javascript">
/* ========================================
   TIER 5 OPTIMIZATION: jQuery Removed - Fetch API Filter
   Page: home.php
   Purpose: AJAX filtering without URL changes (SEO-friendly)
   ======================================== */

function submitFilter(e){if(e.which=='13'){setFilter();}}

function setFilter(){
    const bit = document.querySelector('.filter-bit-rate').value;
    const os = document.querySelector('.filter-os').value;
    const csc = document.querySelector('.filter-csc').value;
    
    const tbody = document.querySelector('tbody');
    
    // Show loading state
    tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center"><div class="inline-flex items-center space-x-3"><svg class="animate-spin h-5 w-5 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="text-ink-muted font-medium">Filtering firmware...</span></div></td></tr>';
    
    // Build query string
    const params = new URLSearchParams({
        bit: bit,
        os: os,
        filter_csc: csc
    });
    
    // Fetch API request - URL stays clean!
    fetch('<?= base_url("firmware/ajax-filter-home") ?>?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(response => {
        if (response.trim() === '') {
            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center"><div class="text-ink-muted"><svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-lg font-semibold text-ink mb-2">No firmware found</p><p class="text-sm">Try adjusting your filters</p></div></td></tr>';
        } else {
            tbody.innerHTML = response;
            // Re-attach click handlers for new rows
            initTableRowClicks();
        }
    })
    .catch(error => {
        tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center text-red-500 font-medium">Error loading firmware. Please try again.</td></tr>';
    });
}

// Enhanced table row click
function initTableRowClicks() {
    document.querySelectorAll('.link-click').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.tagName !== 'A') {
                window.location.href = this.dataset.link;
            }
        });
    });
}

// Initialize on page load
initTableRowClicks();
</script>
