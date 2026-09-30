<?php
$request_obj = service('request');
$modalLogo   = base_url().'assets/samsung-preview.png';
$pagePath    = base_url('firmware');

// ✅ SEO: Prepare breadcrumb data
$uri = $request_obj->getUri();
$model = $uri->getSegment(2) ?? '';
$csc = $uri->getSegment(3) ?? '';
$worldCountries = worldCountries();
$firstRec = $record[0] ?? null;
$countryName = '';
if ($firstRec && isset($firstRec->country)) {
    $countryName = $worldCountries[$firstRec->country]['name'] ?? '';
}
?>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "<?= base_url() ?>"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "<?= esc($model) ?>",
      "item": "<?= base_url('firmware/' . $model) ?>"
    }<?php if (!empty($csc)): ?>,
    {
      "@type": "ListItem",
      "position": 3,
      "name": "<?= esc($csc) ?><?= $countryName ? ' (' . esc($countryName) . ')' : '' ?>",
      "item": "<?= current_url() ?>"
    }<?php endif; ?>
  ]
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "<?= esc($meta_title ?? '') ?>",
  "description": "<?= esc($meta_description ?? '') ?>",
  "url": "<?= current_url() ?>",
  "mainEntity": {
    "@type": "ItemList",
    "numberOfItems": <?= count($record) ?>,
    "itemListElement": [
      <?php foreach ($record as $index => $rec): ?>
      {
        "@type": "SoftwareApplication",
        "position": <?= $index + 1 ?>,
        "name": "<?= esc($rec->device) ?> Firmware <?= esc($rec->version) ?>",
        "operatingSystem": "Android <?= esc($rec->os) ?>",
        "url": "<?= postUrl($rec) ?>"
      }<?= $index < count($record) - 1 ? ',' : '' ?>
      <?php endforeach; ?>
    ]
  }
}
</script>

<!-- Model Posts Page -->
<div class="lg:col-span-9 lg:order-1">
    <h1 class="text-3xl lg:text-4xl font-bold text-ink mb-6 hidden">
        <?= esc($meta_title ?? 'Samsung Firmware Download') ?>
    </h1>
    
    <!-- Colorful Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 bg-white rounded-xl shadow-sm border border-gray-100 px-4 py-3 flex-wrap">
            <li class="inline-flex items-center">
                <a href="<?= base_url() ?>" class="inline-flex items-center text-sm font-semibold text-ink hover:text-accent transition-all hover:scale-105 group">
                    <svg class="w-4 h-4 mr-2 text-accent group-hover:animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    <span>Home</span>
                </a>
            </li>
            <?php
            if ($request_obj->getUri()->getSegment(3) == '' || $request_obj->getUri()->getSegment(2) == null) {
                echo '<li><div class="flex items-center"><svg class="w-5 h-5 text-accent/40" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg><span class="ml-1 inline-flex items-center space-x-1.5 text-sm font-bold text-white bg-accent px-3 py-1.5 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg><span>'.$request_obj->getUri()->getSegment(2).'</span></span></div></li>';
                $pagePath .= '/'.$request_obj->getUri()->getSegment(2);
            } else {
                echo '<li><div class="flex items-center"><svg class="w-5 h-5 text-accent/40" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg><a href="'.base_url().'firmware/'.$request_obj->getUri()->getSegment(2).'" class="ml-1 inline-flex items-center space-x-1.5 text-sm font-bold text-white bg-accent hover:bg-accent-hover px-3 py-1.5 rounded-lg transition-all hover:scale-105"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg><span>'.$request_obj->getUri()->getSegment(2).'</span></a></div></li>';
                echo '<li><div class="flex items-center"><svg class="w-5 h-5 text-accent/40" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg><span class="ml-1 inline-flex items-center space-x-1.5 text-sm font-bold text-white bg-highlight px-3 py-1.5 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg><span>'.$request_obj->getUri()->getSegment(3).'</span></span></div></li>';
                $pagePath .= '/'.$request_obj->getUri()->getSegment(2).'/'.$request_obj->getUri()->getSegment(3);
            }
            ?>
        </ol>
    </nav>

    <div class="space-y-6">
        <!-- Specifications Card -->
        <?php if (!empty($record[0]->specs)) { ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-highlight-soft border-b border-highlight/20">
                <h3 class="text-xl font-bold text-ink flex items-center space-x-2">
                    <span class="w-2 h-2 bg-highlight rounded-full animate-pulse"></span>
                    <svg class="w-6 h-6 text-highlight" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>Samsung <?= $record[0]->device ?> Detailed Specifications</span>
                </h3>
            </div>
            <div class="p-6">
                <div class="max-h-96 overflow-y-auto prose prose-sm max-w-none">
                    <?= $record[0]->specs ?>
                </div>
            </div>
        </div>
        <?php } ?>
        
        <?php /* Welcome Content - COMMENTED OUT (Only show on homepage)
        else if (!empty($homePage->pageContent)) { ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="max-h-72 overflow-y-auto prose max-w-none">
                <?= $homePage->pageContent ?>
            </div>
        </div>
        <?php } */ ?>

        <!-- Filter Section -->
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 filter-area">
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

        <!-- Firmware Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-accent-soft border-b border-gray-100">
                <h2 class="text-2xl font-bold text-ink flex items-center space-x-2">
                    <span class="w-2 h-2 bg-accent rounded-full animate-pulse"></span>
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 11l3 3m0 0l3-3m-3 3V8"></path>
                    </svg>
                    <span><?= esc($page_title ?? 'Firmware List') ?></span>
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
                
                <!-- Pagination -->
                <?php if (isset($pager)) { ?>
                <div class="px-6 py-4 bg-canvas border-t border-gray-200">
                    <nav class="flex justify-center">
                        <?= str_replace(
                            ['<ul class="pagination', '<li', '<a', '<span', 'active'],
                            ['<ul class="pagination flex space-x-2', '<li class="', '<a class="px-4 py-2.5 text-sm font-semibold text-ink bg-white border-2 border-gray-200 rounded-lg hover:border-accent hover:text-accent transition-all', '<span class="px-4 py-2.5 text-sm font-semibold', 'bg-accent text-white border-accent shadow-sm'],
                            $pager->links()
                        ) ?>
                    </nav>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
/* ========================================
   TIER 5 OPTIMIZATION: jQuery Removed - Fetch API Filter
   Page: model-posts.php
   Purpose: AJAX filtering without URL changes (SEO-friendly)
   ======================================== */

function submitFilter(e){if(e.which=='13'){setFilter();}}

function setFilter(){
    const bit = document.querySelector('.filter-area .filter-bit-rate').value;
    const os = document.querySelector('.filter-area .filter-os').value;
    const csc = document.querySelector('.filter-area .filter-csc').value;
    const model = '<?= $model ?? $request_obj->getUri()->getSegment(2) ?>';
    const cscSegment = '<?= $csc ?? "" ?>';
    
    const tbody = document.querySelector('tbody');
    
    // Show loading state
    tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center"><div class="inline-flex items-center space-x-3"><svg class="animate-spin h-5 w-5 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="text-ink-muted font-medium">Filtering firmware...</span></div></td></tr>';
    
    // Build query string
    const params = new URLSearchParams({
        model: model,
        csc: cscSegment,
        bit: bit,
        os: os,
        filter_csc: csc
    });
    
    // Fetch API request - URL stays clean!
    fetch('<?= base_url("firmware/ajax-filter") ?>?' + params.toString(), {
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
