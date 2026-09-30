<?php
$categories = '';
foreach (@json_decode($pagee->category, true) as $cat) {
    $categories .= '<a class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-highlight-soft text-highlight hover:bg-highlight hover:text-white transition-colors mr-2 mb-2" href="'.base_url('blog/category/'.$cat).'">'.$cat.'</a>';
}
$pageLike  = getvisitorLike($pagee->postId, service('request')->getIPAddress(), 'blog');
$post_link = base_url().'blog/'.$pagee->postSlug;
?>
<!-- Single Blog Page -->
<div class="lg:col-span-9 lg:order-1">
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
            <li><div class="flex items-center"><svg class="w-5 h-5 text-accent/40" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg><a href="<?= base_url('blog') ?>" class="ml-1 inline-flex items-center space-x-1.5 text-sm font-bold text-white bg-accent hover:bg-accent-hover px-3 py-1.5 rounded-lg transition-all hover:scale-105"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg><span>News</span></a></div></li>
            <li><div class="flex items-center"><svg class="w-5 h-5 text-accent/40" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg><span class="ml-1 inline-flex items-center space-x-1.5 text-sm font-bold text-ink bg-canvas px-3 py-1.5 rounded-lg border-2 border-accent/20 line-clamp-1 max-w-xs"><svg class="w-4 h-4 text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg><span class="truncate"><?= $pagee->postTitle ?></span></span></div></li>
        </ol>
    </nav>

    <div class="space-y-6 move-to-area">
        <!-- Article Header -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 lg:px-8 py-6 bg-accent-soft border-b border-accent/20">
                <h1 class="text-2xl font-bold text-accent mb-0"><?= $pagee->postTitle ?></h1>
            </div>
            
            <div class="px-6 lg:px-8 py-4 border-b border-gray-100">
                <!-- Meta Info -->
                <div class="flex flex-wrap items-center gap-4 text-ink-muted">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-medium">@SamFirms</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span><?= timeAgo($pagee->modifiedTime) ?></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span><?= number_format($pagee->viewsCount + 5) ?> views</span>
                    </div>
                </div>
            </div>

            <!-- Article Content -->
            <div class="p-6 lg:p-8">
                <!-- Categories -->
                <?php if (!empty($categories)) { ?>
                <div class="mb-6">
                    <?= $categories ?>
                </div>
                <?php } ?>

                <!-- Blog Content -->
                <div class="prose prose-lg max-w-none blog-content">
                    <?= $pagee->postContent ?>
                </div>
                <div class="mt-8 pt-8 border-t-2 border-gray-200">
                    <div class="flex items-center justify-center space-x-4">
                        <span class="text-sm font-semibold text-gray-700">Share this article:</span>
                        
                        <a class="btn-post-action flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-red-500 hover:text-white transition-all <?= $pageLike == 'dislike' ? 'bg-red-500 text-white' : 'text-gray-700' ?>" 
                           href="javascript:;" 
                           onclick="postAction(this,'blog','dislike',<?= $pagee->postId ?>)">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-1.105-1.79l-.05-.025A4 4 0 0011.055 2H5.64a2 2 0 00-1.962 1.608l-1.2 6A2 2 0 004.44 12H8v4a2 2 0 002 2 1 1 0 001-1v-.667a4 4 0 01.8-2.4l1.4-1.866a4 4 0 00.8-2.4z"></path>
                            </svg>
                        </a>

                        <a class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-all hover:scale-110" 
                           href="http://www.facebook.com/sharer.php?u=<?= $post_link ?>" 
                           target="_blank">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>

                        <a class="flex items-center justify-center w-10 h-10 rounded-full bg-sky-500 text-white hover:bg-sky-600 transition-all hover:scale-110" 
                           href="http://twitter.com/share?url=<?= $post_link ?>&text=<?= $pagee->postTitle ?>&hashtags=<?= $web->webTitle ?? '' ?>" 
                           target="_blank">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>

                        <a class="flex items-center justify-center w-10 h-10 rounded-full bg-red-600 text-white hover:bg-red-700 transition-all hover:scale-110" 
                           href="http://pinterest.com/pin/create/button/?url=<?= $post_link ?>" 
                           target="_blank">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0a12 12 0 00-4.37 23.172c-.175-1.588-.033-3.498.392-5.232l2.943-12.47s-.736-1.473-.736-3.651c0-3.422 1.983-5.982 4.452-5.982 2.099 0 3.112 1.575 3.112 3.463 0 2.108-1.342 5.258-2.033 8.178-.578 2.447 1.226 4.441 3.64 4.441 4.37 0 7.314-5.592 7.314-12.204 0-5.026-3.387-8.79-9.516-8.79-6.93 0-11.222 5.177-11.222 10.933 0 1.988.589 3.391 1.516 4.476a.686.686 0 01.196.66c-.083.32-.265 1.059-.302 1.207-.05.196-.163.237-.378.143-2.651-1.082-3.876-4.002-3.876-7.276 0-5.404 4.536-11.919 13.491-11.919 7.193 0 11.91 5.227 11.91 10.838 0 7.419-4.131 12.99-10.186 12.99-2.038 0-3.956-1.097-4.613-2.35 0 0-1.096 4.312-1.333 5.138-.406 1.418-1.199 2.861-1.902 3.944A12.002 12.002 0 0024 12c0-6.627-5.373-12-12-12z"/>
                            </svg>
                        </a>

                        <a class="btn-post-action flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-green-500 hover:text-white transition-all <?= $pageLike == 'like' ? 'bg-green-500 text-white' : 'text-gray-700' ?>" 
                           href="javascript:;" 
                           onclick="postAction(this,'blog','like',<?= $pagee->postId ?>)">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
