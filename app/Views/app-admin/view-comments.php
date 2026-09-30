<?php
$comments = session()->get('comments');
?>

<!-- Page Header -->
<div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Comments Management</h1>
        <p class="text-sm text-gray-600 mt-1">Moderate and manage user comments</p>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-lg shadow-card mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Filter Comments</h2>
        </div>
        <form action="<?= base_url(ADMIN_PATH.'/comments') ?>" method="post">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Post</label>
                        <select class="form-control ajax-select w-full" name="postId">
                            <?php
                            if (($comments['postId'] ?? '') != '' && ($comments['postId'] ?? '0') != '0') {
                                echo '<option value="'.$comments['postId'].'" selected="">'.getPost($comments['postId'])->postTitle.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors" name="status">
                            <option value="All" <?= ($comments['status'] ?? '') == 'All' ? 'selected' : '' ?>>All</option>
                            <option value="Pending" <?= ($comments['status'] ?? '') == 'Pending' || ($comments['status'] ?? '') == '' ? 'selected' : '' ?>>Pending</option>
                            <option value="Approved" <?= ($comments['status'] ?? '') == 'Approved' ? 'selected' : '' ?>>Approved</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" 
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-filter"></i>
                            <span>Filter</span>
                        </button>
                        <?php if (session()->has('comments')): ?>
                            <a href="<?= base_url(ADMIN_PATH.'/comments?reset=true') ?>" 
                               class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors"
                               title="Reset">
                                <i class="fas fa-undo"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Comments Table -->
    <div class="bg-white rounded-lg shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[200px]">Post</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[250px]">Comment</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $i = $page_record ?? 0;
                    foreach ($record as $rec) {
                        $post_link = postUrl($rec);
                        echo '<tr id="comment-'.$rec->commentId.'" class="hover:bg-gray-50 transition-colors">';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">'.++$i.'</td>';
                            echo '<td class="px-4 py-3">
                                    <a href="'.$post_link.'" target="_blank" class="text-accent hover:text-accent-hover font-medium inline-flex items-center gap-1">
                                        '.replacePostToken($rec->postTitle,$rec).'
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                  </td>';
                            echo '<td class="px-4 py-3 text-sm">
                                    <div class="space-y-1">
                                        <div class="font-medium text-gray-900">'.$rec->fromName.'</div>
                                        <div class="text-gray-600">'.$rec->fromEmail.'</div>
                                        <div class="text-xs text-gray-400">'.$rec->ipAddress.'</div>
                                    </div>
                                  </td>';
                            echo '<td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">'.$rec->comment.'</td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">'.$rec->commentTime.'</td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap">';
                            if($rec->status == 'Pending'){
                                echo '<button onclick="approveComment(this,'.$rec->commentId.','.$rec->postId.')" 
                                             class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition-colors cursor-pointer">
                                        Pending
                                      </button>';
                            } else {
                                echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Approved
                                      </span>';
                            }
                            echo '</td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">';
                                        if ($rec->status == 'Approved') {
                                            echo '<a href="'.base_url(ADMIN_PATH.'/comments/'.$rec->commentId).'" 
                                                     class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-cyan-50 text-cyan-600 hover:bg-cyan-100 transition-colors"
                                                     title="Reply">
                                                    <i class="fas fa-reply text-sm"></i>
                                                  </a>';
                                        }
                                        echo '<button onclick="deleteComment('.$rec->commentId.')" 
                                                     class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                     title="Delete">
                                                <i class="fas fa-trash text-sm"></i>
                                              </button>';
                            echo '      </div>
                                  </td>';
                        echo '</tr>';
                    }
                    if(count($record) == 0){
                        echo '<tr><td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-comments text-4xl mb-3"></i>
                                    <p class="text-sm font-medium">No comments found</p>
                                </div>
                              </td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php if(isset($pager) && $pager->links()): ?>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-center">
                    <?= $pager->links() ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script type="text/javascript">
$(document).ready(function(){
    $('.ajax-select').select2({
        allowClear: true,
        placeholder: "Search in posts",
        minimumInputLength: 3,
        delay: 250,
        ajax: {
            url: '<?= base_url('app-admin/getPosts') ?>',
            dataType: 'json',
            data: function(params){ return {search: params.term}; },
            processResults: function(data){ return {results: data}; }
        }
    });
});

function approveComment(obj, commentId, postId){
    swal({
        type: 'info',
        title: 'Approve this comment?',
        text: 'The comment will be visible to all users.',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel'
    }, function(){
        $.ajax({
            type: 'post',
            data: {commentId: commentId, postId: postId},
            url: '<?= base_url('app-admin/approveComment') ?>',
            success: function(response){
                if($.trim(response) != 'success'){
                    toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                } else {
                    toastr.success('Comment approved successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                    $(obj).replaceWith('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>');
                }
            },
            error: function(err){
                toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            }
        });
    });
}

function deleteComment(commentId){
    swal({
        type: 'warning',
        title: 'Are you sure?',
        text: 'This comment will be permanently deleted.',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#DC2626'
    }, function(){
        $.ajax({
            type: 'post',
            data: {commentId: commentId},
            url: '<?= base_url('app-admin/deleteComment') ?>',
            success: function(response){
                if($.trim(response) != 'success'){
                    toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                } else {
                    toastr.success('Comment deleted successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                    $('#comment-'+commentId).fadeOut(300, function(){ $(this).remove(); });
                }
            },
            error: function(err){
                toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            }
        });
    });
}
</script>
