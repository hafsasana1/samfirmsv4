<?php
// CI4: $this->uri->segment(3) → service('request')->getUri()->getSegment(3)
//      $this->page_record → $page_record
//      $this->pagination->create_links() → $pager->links()
$uri_segment3 = service('request')->getUri()->getSegment(3);
?>
<div class="p-4 lg:p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800"><?= ucfirst($uri_segment3) ?> Posts</h1>
            <p class="text-sm text-gray-500 mt-1">Total: <span class="font-semibold text-gray-700"><?= $total_rows ?></span> posts</p>
        </div>
        <button 
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors" 
            onclick="refreshAll(this)">
            <i class="fas fa-sync-alt mr-2"></i>Refresh All
        </button>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-lg shadow-card">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 w-12">#</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Post Data</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Error Details</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700 w-32">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php
                        $i = $page_record ?? 0;
                        foreach ($record as $rec) {
                            $crawlData = @json_decode($rec->crawlData, true);
                            echo '<tr id="crawl-'.$rec->crawlId.'" class="hover:bg-gray-50 transition-colors">';
                                echo '<td class="py-4 px-4 text-sm text-gray-600 align-top">'.++$i.'</td>';
                                echo '<td class="py-4 px-4 align-top">';
                                    echo '<div class="space-y-2">';
                                        echo '<a href="'.$rec->urlLink.'" target="_blank" class="text-accent hover:text-accent-dark font-medium text-sm break-all">'.$rec->urlLink.'</a>';
                                        if(!empty($crawlData)) {
                                            echo '<div class="bg-gray-50 rounded p-3 border border-gray-200">';
                                                echo '<pre class="text-xs text-gray-700 overflow-auto max-h-48">'.print_r($crawlData, true).'</pre>';
                                            echo '</div>';
                                        }
                                    echo '</div>';
                                echo '</td>';
                                echo '<td class="py-4 px-4 align-top">';
                                    echo '<div class="space-y-2">';
                                        echo '<div class="text-xs text-gray-500">';
                                            echo '<i class="fas fa-clock mr-1"></i>'.$rec->createdTime;
                                        echo '</div>';
                                        echo '<div class="text-sm text-red-600 bg-red-50 p-3 rounded border border-red-200">';
                                            echo $rec->processingError;
                                        echo '</div>';
                                    echo '</div>';
                                echo '</td>';
                                echo '<td class="py-4 px-4 align-top">';
                                    echo '<div class="flex items-start justify-center gap-2">';
                                        echo '<button onclick="updateStatus(\''.$rec->crawlId.'\')" class="w-8 h-8 flex items-center justify-center bg-cyan-50 hover:bg-cyan-100 text-cyan-600 rounded-lg transition-colors" title="Mark as Processed"><i class="fas fa-check"></i></button>';
                                        echo '<a href="'.base_url('pinger/autoPost?crawlId='.$rec->crawlId).'" target="_blank" class="w-8 h-8 flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors" title="Retry"><i class="fas fa-redo"></i></a>';
                                        echo '<button onclick="deletePost('.$rec->crawlId.')" class="w-8 h-8 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors" title="Delete"><i class="fas fa-trash"></i></button>';
                                    echo '</div>';
                                echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php if(isset($pager)): ?>
                <div class="mt-6 flex justify-end">
                    <div class="pagination-wrapper">
                        <?= $pager->links() ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script type="text/javascript">
function deletePost(crawlId){
    swal({
        type: 'warning',
        title: 'Are you sure to delete this?',
        text: 'This action cannot be undone',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        confirmButtonColor: '#EF4444'
    }, function(){
        $.ajax({
            type: 'post',
            data: {crawlId: crawlId},
            url: '<?= base_url('app-admin/deleteAutoPost') ?>',
            success: function(response){
                if($.trim(response) != 'success'){
                    toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                } else {
                    $('#crawl-' + crawlId).fadeOut(300, function(){ $(this).remove(); });
                    toastr.success('Post deleted successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                }
            },
            error: function(err){
                toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            }
        });
    });
}

function updateStatus(crawlId){
    swal({
        type: 'info',
        title: 'Mark as processed?',
        text: 'This will remove the post from the failed list',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Yes, Continue',
        confirmButtonColor: '#1FBF8F'
    }, function(){
        $.ajax({
            type: 'post',
            data: {crawlId: crawlId},
            url: '<?= base_url('app-admin/updateAutoPost') ?>',
            success: function(response){
                if($.trim(response) != 'success'){
                    toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                } else {
                    $('#crawl-' + crawlId).fadeOut(300, function(){ $(this).remove(); });
                    toastr.success('Status updated successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                }
            },
            error: function(err){
                toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            }
        });
    });
}

function refreshAll(obj){
    $(obj).prop('disabled', true);
    var originalHTML = $(obj).html();
    $(obj).html('<i class="fas fa-spinner fa-spin mr-2"></i>Refreshing...');
    
    $.ajax({
        url: '<?= base_url('app-admin/refreshFailedPost') ?>',
        success: function(response){
            if($.trim(response) != 'success'){
                toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                $(obj).prop('disabled', false);
                $(obj).html(originalHTML);
            } else {
                toastr.success('All posts refreshed successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                setTimeout(function(){ window.location.reload(); }, 1000);
            }
        },
        error: function(err){
            toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            $(obj).prop('disabled', false);
            $(obj).html(originalHTML);
        }
    });
}
</script>
