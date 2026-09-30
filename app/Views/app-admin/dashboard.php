<?php
$app = session()->get('fw');
?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Welcome back, <?= $app->name ?? 'Admin' ?>!</p>
</div>

<?php if($app->type == 'Admin'){ ?>
<!-- Statistics Cards - Top Row -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Models Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Models</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2 models-count">
                            <i class="fas fa-spinner fa-spin text-accent"></i>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-accent-soft dark:bg-accent/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-mobile-alt text-2xl text-accent"></i>
                    </div>
                </div>
    </div>

    <!-- Firmwares Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Firmwares</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2 firmwares-count">
                            <i class="fas fa-spinner fa-spin text-cyan-600"></i>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-desktop text-2xl text-cyan-600 dark:text-cyan-400"></i>
                    </div>
                </div>
    </div>

    <!-- Downloads Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Downloads</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2 downloads-count">
                            <i class="fas fa-spinner fa-spin text-green-600"></i>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-download text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
    </div>

    <!-- Failed Posts Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 hover:shadow-lg transition-shadow">
                <a href="<?= base_url() ?>app-admin/posts/failed" class="block">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Failed Posts</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2 failed-posts-count">
                                <i class="fas fa-spinner fa-spin text-red-600"></i>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-2xl text-red-600 dark:text-red-400"></i>
                        </div>
                    </div>
        </a>
    </div>
</div>

<!-- Secondary Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Visitors Card -->
            <div class="bg-gradient-to-br from-accent to-accent-hover rounded-lg shadow-card p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-white opacity-90">Visitors</p>
                        <p class="text-3xl font-bold mt-2 visitor-count">
                            <i class="fas fa-spinner fa-spin"></i>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
                <div class="h-16 opacity-75">
                    <canvas data-chart="line" data-animation="false" 
                            data-labels='["Jun 21", "Jun 20", "Jun 19", "Jun 18", "Jun 17", "Jun 16", "Jun 15"]' 
                            data-values='[{"backgroundColor": "rgba(255, 255, 255, 0.3)", "borderColor": "#ffffff", "data": [8796, 11317, 8678, 9452, 8453, 11853, 9945]}]' 
                            data-scales='{"yAxes": [{ "ticks": {"max": 12742}}]}' 
                            data-hide='["legend", "points", "scalesX", "scalesY", "tooltips"]' 
                            height="35"></canvas>
                </div>
            </div>

            <!-- Total Firmware Card -->
            <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg shadow-card p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-white opacity-90">Total Firmware</p>
                        <p class="text-3xl font-bold mt-2 posts-count">
                            <i class="fas fa-spinner fa-spin"></i>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                </div>
                <div class="h-16 opacity-75">
                    <canvas data-chart="line" data-animation="false" 
                            data-labels='["Jun 21", "Jun 20", "Jun 19", "Jun 18", "Jun 17", "Jun 16", "Jun 15"]' 
                            data-values='[{"backgroundColor": "rgba(255, 255, 255, 0.3)", "borderColor": "#ffffff", "data": [8796, 11317, 8678, 9452, 8453, 11853, 9945]}]' 
                            data-scales='{"yAxes": [{ "ticks": {"max": 12742}}]}' 
                            data-hide='["legend", "points", "scalesX", "scalesY", "tooltips"]' 
                            height="35"></canvas>
                </div>
            </div>

            <!-- Pending Uploads Card -->
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-card p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-white opacity-90">Pending Uploads</p>
                        <p class="text-3xl font-bold mt-2 pending-file-upload-count">
                            <i class="fas fa-spinner fa-spin"></i>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
                <div class="h-16 opacity-75">
                    <canvas data-chart="line" data-animation="false" 
                            data-labels='["Jun 21", "Jun 20", "Jun 19", "Jun 18", "Jun 17", "Jun 16", "Jun 15"]' 
                            data-values='[{"backgroundColor": "rgba(255, 255, 255, 0.3)", "borderColor": "#ffffff", "data": [8796, 11317, 8678, 9452, 8453, 11853, 9945]}]' 
                            data-scales='{"yAxes": [{ "ticks": {"max": 12742}}]}' 
                            data-hide='["legend", "points", "scalesX", "scalesY", "tooltips"]' 
                            height="35"></canvas>
                </div>
        </div>
    </div>
</div>
<?php } ?>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Visitor Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Visitor Statistics</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-cyan-100 dark:bg-cyan-900/30 text-cyan-800 dark:text-cyan-400">
                        Today: <?= number_format(end($states['values'])) ?>
                    </span>
                </div>
            </div>
            <div class="p-6">
                <canvas id="search-engine" 
                        data-chart="bar" 
                        data-labels='["",<?= implode(',',$states['lables']) ?>]' 
                        data-values='[{"label": "Visitors", "backgroundColor": "#1FBF8F", "borderColor": "#1FBF8F", "data": ["0",<?= implode(',',$states['values']) ?>]}]' 
                        data-hide='["gridLinesX", "legend"]' 
                        height="150"></canvas>
            </div>
        </div>

        <!-- Search Engine / Referral Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Search Engine / Referral</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-accent-soft dark:bg-accent/20 text-accent">
                        Total: <?= number_format($referrerData['totalSearch']) ?>
                    </span>
                </div>
            </div>
            <div class="p-6">
                <canvas id="referrer-chart" 
                        data-chart="bar" 
                        data-labels='[<?= implode(',',$referrerData['lables']) ?>]' 
                        data-values='[{"label": "Visitors", "backgroundColor": "#FF8A1F", "borderColor": "#FF8A1F", "data": [<?= implode(',',$referrerData['values']) ?>]}]' 
                        data-hide='["gridLinesX", "legend"]' 
                        height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Top Countries Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Searching Countries</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-accent-soft dark:bg-accent/20 text-accent">
                        Total: <?= number_format($countryData['totalSearch']) ?>
                    </span>
                </div>
            </div>
            <div class="p-6">
                <canvas id="country-chart" 
                        data-chart="bar" 
                        data-labels='[<?= implode(',',$countryData['lables']) ?>]' 
                        data-values='[{"label": "Visitors", "backgroundColor": "#101C2C", "borderColor": "#101C2C", "data": [<?= implode(',',$countryData['values']) ?>]}]' 
                        data-hide='["gridLinesX", "legend"]' 
                        height="150"></canvas>
            </div>
        </div>

        <!-- Recent Comments -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Comments</h3>
                    <a href="<?= base_url(ADMIN_PATH.'/comments') ?>" 
                       class="text-sm font-medium text-accent hover:text-accent-hover">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Post</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">From</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Comment</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php
                        $i = 0;
                        foreach($comments as $rec){
                            $mpost = getPost($rec->postId);
                            $post_link = postUrl($mpost);
                            echo '<tr id="comment-'.$rec->commentId.'" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">';
                                echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">'.++$i.'</td>';
                                echo '<td class="px-4 py-3 text-sm"><a href="'.$post_link.'" target="_blank" class="text-accent hover:text-accent-hover font-medium">'.replacePostToken($rec->postTitle,$mpost).'</a></td>';
                                echo '<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400"><div class="space-y-1"><div>'.$rec->fromName.'</div><div class="text-xs text-gray-500 dark:text-gray-500">'.$rec->fromEmail.'</div><div class="text-xs text-gray-400 dark:text-gray-600">'.$rec->ipAddress.'</div></div></td>';
                                echo '<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">'.$rec->comment.'</td>';
                                echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-500">'.$rec->commentTime.'</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Post Automation File Uploads -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-card overflow-hidden mb-6 transition-all duration-300 ease-in-out relative"
     :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'"
     style="margin-top: 1.5rem;">
    <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Post Automation File Uploads</h3>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-accent-soft dark:bg-accent/20 text-accent">
                Total: <span id="automation-total-count"><?= $total_post_rows ?></span>
            </span>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div id="automation-loading" class="hidden absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-90 dark:bg-opacity-90 flex items-center justify-center z-10" style="min-height: 300px;">
        <div class="text-center">
            <i class="fas fa-spinner fa-spin text-4xl text-accent mb-3"></i>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Loading posts...</p>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table id="post-automation-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[200px]">Post</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">Last Updated</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[100px]">Action</th>
                </tr>
            </thead>
            <?= view(ADMIN_PATH . '/include/post-automation-table', [
                'post_records' => $post_records,
                'current_page' => $current_page,
                'per_page' => $per_page,
                'total_post_rows' => $total_post_rows
            ]) ?>
        </table>
    </div>
</div>

<!-- Post File Link Modal -->
<div x-data="{ automationModalOpen: false }" 
     @automation-modal.window="automationModalOpen = true">
    <div x-show="automationModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <!-- Background overlay -->
        <div x-show="automationModalOpen" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" 
             @click="automationModalOpen = false"></div>

        <!-- Modal panel -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="automationModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Post File Link</h3>
                        <button @click="automationModalOpen = false" 
                                class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-4 space-y-4">
                    <input type="hidden" class="automation-postId" value="">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">External File Link</label>
                        <div class="flex gap-2">
                            <input class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-accent focus:ring-accent" 
                                   type="text" 
                                   id="automation-link" 
                                   value="">
                            <button onclick="copyLink()" 
                                    class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors"
                                    title="Copy to clipboard">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Downloadable File Link</label>
                        <div class="flex gap-2">
                            <input class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-accent focus:ring-accent" 
                                   type="text" 
                                   id="automation-filelink" 
                                   value="">
                            <button onclick="savePostLink(this)" 
                                    class="px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-lg transition-colors"
                                    title="Save New File Link">
                                <i class="fas fa-save mr-2"></i> Save
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <button @click="automationModalOpen = false" 
                            class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    setTimeout(dashboardStates,3000);
});

function dashboardStates(){
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '<?= base_url('app-admin/dashboardStates') ?>',
        success: function(vobj){
            $('.visitor-count').text(vobj.visitors);
            $('.posts-count').text(vobj.totalFirmware);
            $('.pending-file-upload-count').text(vobj.pendingUploads);
            $('.models-count').text(vobj.models);
            $('.firmwares-count').text(vobj.firmwares);
            $('.downloads-count').text(vobj.downloads);
            $('.failed-posts-count').text(vobj.failedPost);
        },
        error: function(err){
            toastr.error('Unable to process your request','',{timeOut: 5000, positionClass: 'toast-top-center'});
            $('.visitor-count, .posts-count, .pending-file-upload-count, .models-count, .firmwares-count, .downloads-count, .failed-posts-count').text('Failed to load');
        }
    })
}

function postDownload(postId,externalFileLink){
    $('.automation-postId').val(postId);
    $('#automation-link').val(externalFileLink);
    window.dispatchEvent(new CustomEvent('automation-modal'));
}

// ----------------------------------------------------------------
// AJAX Pagination for Post Automation Table
// ----------------------------------------------------------------
function loadPostAutomationPage(page){
    // Show loading overlay
    $('#automation-loading').removeClass('hidden');
    
    // Smooth scroll to table
    $('html, body').animate({
        scrollTop: $("#post-automation-table").offset().top - 100
    }, 300);
    
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '<?= base_url('app-admin/loadPostAutomationTable') ?>',
        data: { page: page },
        success: function(response){
            if(response.success){
                // Fade out current content
                $('#post-automation-table tbody, #post-automation-table tfoot').fadeOut(200, function(){
                    // Replace with new content
                    $(this).remove();
                    $('#post-automation-table thead').after(response.html);
                    
                    // Fade in new content
                    $('#post-automation-table tbody, #post-automation-table tfoot').hide().fadeIn(300);
                    
                    // Hide loading overlay
                    $('#automation-loading').addClass('hidden');
                    
                    // Update URL without page reload (for browser back button)
                    if(history.pushState) {
                        const newUrl = '<?= base_url(ADMIN_PATH . '/dashboard') ?>?page=' + page;
                        history.pushState({page: page}, '', newUrl);
                    }
                });
            }else{
                $('#automation-loading').addClass('hidden');
                toastr.error('Unable to load page','',{timeOut: 3000, positionClass: 'toast-top-center'});
            }
        },
        error: function(err){
            $('#automation-loading').addClass('hidden');
            toastr.error('Unable to load page','',{timeOut: 3000, positionClass: 'toast-top-center'});
        }
    });
}

// Handle browser back/forward buttons
window.addEventListener('popstate', function(e) {
    if(e.state && e.state.page) {
        loadPostAutomationPage(e.state.page);
    }
});


function copyLink(){
    var copyText = document.getElementById("automation-link");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    toastr.success('Copied to clipboard','',{timeOut: 3000, positionClass: 'toast-top-center'});
}

function savePostLink(obj){
    var vobj = $(obj);
    var postId = $('.automation-postId').val();
    var downloadableFile = $('#automation-filelink').val();
    
    if(downloadableFile == '' || downloadableFile == null || downloadableFile == undefined){
        toastr.error('New downloadable file link required','',{timeOut: 5000, positionClass: 'toast-top-center'});
    }else{
        vobj.prop('disabled',true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        $.ajax({
            type: 'post',
            data: {postId:postId,downloadableFile:downloadableFile},
            url: '<?= base_url('app-admin/saveDownloadableLink') ?>',
            success: function(response){
                vobj.prop('disabled',false).html('<i class="fas fa-save mr-2"></i> Save');
                if($.trim(response) != 'success'){
                    toastr.error(response,'',{timeOut: 5000, positionClass: 'toast-top-center'});
                }else{
                    $('#autopost-'+postId).remove();
                    toastr.success('Action performed successfully','',{timeOut: 5000, positionClass: 'toast-top-center'});
                    window.dispatchEvent(new CustomEvent('automation-modal'));
                }
            },
            error: function(err){
                toastr.error('Unable to process your request','',{timeOut: 5000, positionClass: 'toast-top-center'});
                vobj.prop('disabled',false).html('<i class="fas fa-save mr-2"></i> Save');
            }
        })
    }
}
</script>
