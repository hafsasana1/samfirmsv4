<?php
$request_obj = service('request');
$modalLogo   = base_url().'assets/samsung-preview.png';
?>
<!-- Modern Blog Page -->
<div class="lg:col-span-9 lg:order-1">
    <!-- Colorful Breadcrumb -->
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
            <?php
            if ($request_obj->getUri()->getSegment(2) == 'category') {
                echo '<li><div class="flex items-center"><svg class="w-5 h-5 text-accent/40" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg><a href="'.base_url().'blog" class="ml-1 inline-flex items-center space-x-1.5 text-sm font-bold text-white bg-accent hover:bg-accent-hover px-3 py-1.5 rounded-lg transition-all hover:scale-105"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg><span>News</span></a></div></li>';
                echo '<li><div class="flex items-center"><svg class="w-5 h-5 text-accent/40" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg><span class="ml-1 inline-flex items-center space-x-1.5 text-sm font-bold text-ink bg-highlight-soft px-3 py-1.5 rounded-lg border-2 border-highlight/30"><svg class="w-4 h-4 text-highlight" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg><span>'.$request_obj->getUri()->getSegment(3).'</span></span></div></li>';
            } else {
                echo '<li><div class="flex items-center"><svg class="w-5 h-5 text-accent/40" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg><span class="ml-1 inline-flex items-center space-x-1.5 text-sm font-bold text-white bg-accent px-3 py-1.5 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg><span>News</span></span></div></li>';
            }
            ?>
        </ol>
    </nav>

    <!-- Blog Posts Grid -->
    <div class="space-y-6">
        <?php foreach ($record as $rec) {
            $doc = new DOMDocument();
            @$doc->loadHTML($rec->postContent);
            $tags   = $doc->getElementsByTagName('img');
            $imgUrl = '';
            foreach ($tags as $tag) { $imgUrl = $tag->getAttribute('src'); if ($imgUrl != '') break; }
            // ✅ FIX #4: REMOVED base64 encoding
            $imgUrl = $imgUrl == '' ? base_url('resource/logo.png') : $imgUrl;
        ?>
            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 group">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                    <!-- Image -->
                    <div class="md:col-span-1">
                        <a href="<?= base_url('blog/'.$rec->postSlug) ?>" class="block overflow-hidden rounded-xl bg-canvas aspect-video">
                            <img src="<?= $imgUrl ?>" 
                                 loading="lazy"
                                 alt="<?= $rec->postTitle ?>"
                                 width="280"
                                 height="160"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </a>
                    </div>
                    
                    <!-- Content -->
                    <div class="md:col-span-2 flex flex-col justify-between">
                        <div>
                            <a href="<?= base_url('blog/'.$rec->postSlug) ?>" class="block">
                                <h2 class="text-xl font-bold text-ink mb-3 group-hover:text-accent transition-colors line-clamp-2">
                                    <?= $rec->postTitle ?>
                                </h2>
                            </a>
                            <div class="prose prose-sm text-ink-muted mb-4 line-clamp-3">
                                <?= word_limiter(preg_replace("/<img[^>]+\>/i", "", $rec->postContent), 30) ?>
                            </div>
                        </div>
                        
                        <!-- Meta Info -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>@SamFirms</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span><?= timeAgo($rec->modifiedTime) ?></span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <span><?= number_format($rec->viewsCount + 5) ?> views</span>
                                </div>
                            </div>
                            
                            <a href="<?= base_url('blog/'.$rec->postSlug) ?>" 
                               class="inline-flex items-center space-x-2 text-sm font-semibold text-accent hover:text-accent-hover transition-colors group/btn">
                                <span>Read More</span>
                                <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </article>
        <?php } ?>
        
        <!-- Pagination -->
        <?php if (is_array($record) && count($record) > 0 && isset($pager)) { ?>
        <div class="flex justify-center mt-8">
            <nav class="inline-flex rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <?= str_replace(
                    ['<ul class="pagination', '<li', '<a', '<span', 'active'],
                    ['<ul class="pagination flex divide-x divide-gray-200', '<li class="', '<a class="px-4 py-2 text-sm font-medium text-ink hover:bg-gray-50 transition-colors', '<span class="px-4 py-2 text-sm font-medium', 'bg-accent text-white'],
                    $pager->links()
                ) ?>
            </nav>
        </div>
        <?php } ?>
        
        <!-- Empty State -->
        <?php if (empty($record)) { ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No posts found</h3>
            <p class="text-gray-600">Check back later for new content!</p>
        </div>
        <?php } ?>
    </div>
</div>