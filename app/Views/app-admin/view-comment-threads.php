<?php
$mpost = getPost($comment->postId);
$post_link = postUrl($mpost);
?>

<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Comment Threads</h1>
            <p class="text-sm text-gray-600 mt-1">View and reply to comment conversations</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= base_url(ADMIN_PATH.'/comments') ?>" 
               class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
            <button onclick="replyComment(<?= $comment->commentId ?>)" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white font-semibold rounded-lg transition-colors shadow-sm">
                <i class="fas fa-reply"></i>
                <span>Add Reply</span>
            </button>
        </div>
    </div>

    <!-- Post Info Card -->
    <div class="bg-white rounded-lg shadow-card mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-accent-soft to-cyan-50">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Post</p>
                    <a href="<?= $post_link ?>#comments" target="_blank" 
                       class="text-lg font-semibold text-accent hover:text-accent-hover inline-flex items-center gap-2">
                        <?= replacePostToken($comment->postTitle,$mpost) ?>
                        <i class="fas fa-external-link-alt text-sm"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Original Comment -->
    <div class="bg-white rounded-lg shadow-card mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Original Comment</h2>
        </div>
        <div class="p-6">
            <div class="mb-4 pb-4 border-b border-gray-200">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-accent to-cyan-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            <?= strtoupper(substr($comment->fromName, 0, 1)) ?>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="font-semibold text-gray-900"><?= $comment->fromName ?></h3>
                            <span class="text-sm text-gray-500"><?= $comment->commentTime ?></span>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600 mb-3">
                            <div><i class="fas fa-envelope text-gray-400 mr-2"></i><?= $comment->fromEmail ?></div>
                            <div><i class="fas fa-globe text-gray-400 mr-2"></i><?= $comment->ipAddress ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="prose max-w-none text-gray-700">
                <?= $comment->comment ?>
            </div>
        </div>
    </div>

    <!-- Replies -->
    <div class="bg-white rounded-lg shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Replies (<?= count($commentrecord) ?>)</h2>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            <?php
            if(count($commentrecord) == 0){
                echo '<div class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <i class="fas fa-comments text-4xl mb-3"></i>
                            <p class="text-sm font-medium">No replies yet</p>
                            <p class="text-xs text-gray-500 mt-1">Be the first to reply to this comment</p>
                        </div>
                      </div>';
            }
            
            foreach($commentrecord as $srec){
                $isAdmin = $srec->userId != '0';
                $bgClass = $isAdmin ? 'bg-accent-soft' : 'bg-white';
                echo '<div id="comment-'.$srec->commentId.'" class="p-6 '.$bgClass.'">';
                    echo '<div class="flex items-start gap-4">';
                        echo '<div class="flex-shrink-0">';
                            if($isAdmin){
                                echo '<div class="w-10 h-10 bg-accent rounded-full flex items-center justify-center text-white font-bold">
                                        <i class="fas fa-user-shield"></i>
                                      </div>';
                            } else {
                                $initial = strtoupper(substr($srec->fromName, 0, 1));
                                echo '<div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                                        '.$initial.'
                                      </div>';
                            }
                        echo '</div>';
                        echo '<div class="flex-1">';
                            echo '<div class="flex items-center justify-between mb-2">';
                                echo '<div class="flex items-center gap-3">';
                                    if($isAdmin){
                                        echo '<h4 class="font-semibold text-gray-900">'.userName($srec->userId).'</h4>';
                                        echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-accent text-white">
                                                <i class="fas fa-shield-alt mr-1"></i>Admin
                                              </span>';
                                    } else {
                                        echo '<h4 class="font-semibold text-gray-900">'.$srec->fromName.'</h4>';
                                    }
                                    echo '<span class="text-sm text-gray-500">'.$srec->commentTime.'</span>';
                                echo '</div>';
                                echo '<button onclick="deleteComment('.$srec->commentId.')" 
                                             class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                             title="Delete Reply">
                                        <i class="fas fa-trash text-sm"></i>
                                      </button>';
                            echo '</div>';
                            if(!$isAdmin){
                                echo '<div class="space-y-1 text-sm text-gray-600 mb-3">
                                        <div><i class="fas fa-envelope text-gray-400 mr-2"></i>'.$srec->fromEmail.'</div>
                                        <div><i class="fas fa-globe text-gray-400 mr-2"></i>'.$srec->ipAddress.'</div>
                                      </div>';
                            }
                            echo '<div class="text-gray-700 mt-2">'.$srec->comment.'</div>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</main>

<!-- Reply Modal -->
<div x-data="{ commentModalOpen: false }" 
     @comment-modal.window="commentModalOpen = true">
    <div x-show="commentModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto">
        <div x-show="commentModalOpen" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="commentModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="commentModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <form action="" class="comment-form" method="post">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Add Reply</h3>
                            <button @click="commentModalOpen = false" type="button"
                                    class="text-gray-400 hover:text-gray-500 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <input type="hidden" name="commentId" value="">
                        <textarea name="comment" rows="6" required
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors resize-y"
                                  placeholder="Write your reply..."></textarea>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-end gap-3">
                            <button @click="commentModalOpen = false" type="button"
                                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                            <button type="submit" 
                                    class="px-6 py-3 bg-accent hover:bg-accent-hover text-white font-semibold rounded-lg transition-colors shadow-sm hover:shadow-md flex items-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                <span>Submit Reply</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function replyComment(commentId){
    $('.comment-form input[name="commentId"]').val(commentId);
    $('.comment-form textarea[name="comment"]').val('');
    window.dispatchEvent(new CustomEvent('comment-modal'));
}

$('.comment-form').submit(function(e){
    e.preventDefault();
    
    var submitBtn = $(this).find('button[type="submit"]');
    var originalHtml = submitBtn.html();
    
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...');

    $.ajax({
        type: 'post',
        data: $(this).serialize(),
        url: '<?= base_url('app-admin/postComment') ?>',
        success: function(response){
            if($.trim(response) != 'success'){
                toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                submitBtn.prop('disabled', false).html(originalHtml);
            } else {
                toastr.success('Reply posted successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                setTimeout(function(){ window.location.reload(); }, 1500);
            }
        },
        error: function(err){
            toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            submitBtn.prop('disabled', false).html(originalHtml);
        }
    });
});

function deleteComment(commentId){
    swal({
        type: 'warning',
        title: 'Are you sure?',
        text: 'This reply will be permanently deleted.',
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
                    toastr.success('Reply deleted successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
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
