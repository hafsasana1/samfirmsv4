<?php
$postSearch = session()->get('postSearch');
$app        = session()->get('fw');
?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Firmware Posts</h1>
            <p class="text-sm text-gray-600 mt-1">Manage firmware downloads and updates</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <?php if ($app->type == 'Admin'): ?>
                <a href="<?= base_url(ADMIN_PATH.'/posts/failed') ?>" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg transition-colors shadow-sm">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Failed Posts</span>
                </a>
            <?php endif; ?>
            <a href="<?= base_url(ADMIN_PATH.'/posts/add') ?>" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white font-semibold rounded-lg transition-colors shadow-sm hover:shadow-md">
                <i class="fas fa-plus"></i>
                <span>Add Post</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white rounded-lg shadow-card mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Search & Filter</h2>
        </div>
        <form action="<?= base_url() ?>app-admin/posts" class="search-form" method="post">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search Input -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" 
                               name="searchIn" 
                               value="<?= $postSearch['searchIn'] ?? '' ?>"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                               placeholder="Search by title, model, device, version...">
                        <p class="mt-1 text-xs text-gray-500">Search in <strong>title | model | device | version</strong></p>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select name="type" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors">
                            <option value="">All Posts</option>
                            <option value="Uploaded" <?= ($postSearch['type'] ?? '') == 'Uploaded' ? 'selected' : '' ?>>Uploaded</option>
                            <option value="Pending"  <?= ($postSearch['type'] ?? '') == 'Pending'  ? 'selected' : '' ?>>Pending</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-end gap-2">
                        <button type="submit" 
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-search"></i>
                            <span>Search</span>
                        </button>
                        <?php if (session()->has('postSearch')): ?>
                            <a href="<?= base_url() ?>app-admin/posts?reset=true" 
                               class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors"
                               title="Reset Filters">
                                <i class="fas fa-undo"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="px-6 py-4 bg-gradient-to-r from-accent to-accent-hover border-t border-gray-200">
                <div class="flex items-center justify-between text-white">
                    <span class="font-medium">Total Posts</span>
                    <span class="text-2xl font-bold"><?= number_format($total_rows) ?></span>
                </div>
            </div>
        </form>
    </div>

    <!-- Posts Table Card -->
    <div class="bg-white rounded-lg shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[250px]">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20" title="Comments">
                            <i class="fas fa-comment"></i>
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20" title="Likes">
                            <i class="fas fa-heart"></i>
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20" title="Views">
                            <i class="fas fa-eye"></i>
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20" title="Downloads">
                            <i class="fas fa-download"></i>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $i = $page_record ?? 0;
                    foreach ($record as $rec) {
                        $statusClass = 'bg-green-100 text-green-800';
                        if ($rec->postStatus == 'Inactive') $statusClass = 'bg-red-100 text-red-800';
                        if ($rec->postStatus == 'Draft')    $statusClass = 'bg-blue-100 text-blue-800';
                        
                        $post_link = postUrl($rec);
                        echo '<tr id="post-'.$rec->postId.'" class="hover:bg-gray-50 transition-colors">';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">'.++$i.'</td>';
                            echo '<td class="px-4 py-3">
                                    <a href="'.$post_link.'" target="_blank" class="text-accent hover:text-accent-hover font-medium inline-flex items-center gap-1">
                                        '.replacePostToken($rec->postTitle,$rec).'
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                  </td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">'.$rec->model.'</td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">'.$rec->device.'</td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-center text-gray-900">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-cyan-50 text-cyan-600 font-medium">'.$rec->commentCount.'</span>
                                  </td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-center text-gray-900">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-pink-50 text-pink-600 font-medium">'.$rec->likesCount.'</span>
                                  </td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                    <a href="'.base_url(ADMIN_PATH.'/posts/views/'.$rec->postId).'" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-purple-50 text-purple-600 font-medium hover:bg-purple-100 transition-colors">
                                        '.$rec->viewsCount.'
                                    </a>
                                  </td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-center text-gray-900">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-50 text-green-600 font-medium">'.$rec->downloadCount.'</span>
                                  </td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">'.$rec->modifiedTime.'</td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium '.$statusClass.'">
                                        '.$rec->postStatus.'
                                    </span>
                                  </td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">';
                                        echo '<a href="'.base_url(ADMIN_PATH.'/posts/add?postId='.$rec->postId).'" 
                                                 class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-100 transition-colors"
                                                 title="Duplicate Post">
                                                <i class="fas fa-copy text-sm"></i>
                                              </a>';
                                        echo '<a href="'.base_url(ADMIN_PATH.'/posts/edit/'.$rec->postId).'" 
                                                 class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-cyan-50 text-cyan-600 hover:bg-cyan-100 transition-colors"
                                                 title="Edit Post">
                                                <i class="fas fa-edit text-sm"></i>
                                              </a>';
                                        echo '<button onclick="deletePost('.$rec->postId.')" 
                                                     class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                     title="Delete Post">
                                                <i class="fas fa-trash text-sm"></i>
                                              </button>';
                            echo '      </div>
                                  </td>';
                        echo '</tr>';
                    }
                    if(count($record) == 0){
                        echo '<tr>
                                <td colspan="11" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <i class="fas fa-inbox text-4xl mb-3"></i>
                                        <p class="text-sm font-medium">No posts found</p>
                                        <a href="'.base_url(ADMIN_PATH.'/posts/add').'" class="mt-3 text-accent hover:text-accent-hover text-sm">
                                            <i class="fas fa-plus mr-1"></i> Create your first post
                                        </a>
                                    </div>
                                </td>
                              </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if(isset($pager) && $pager->links()): ?>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-center">
                    <?= $pager->links() ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

<script type="text/javascript">
function deletePost(postId){
    swal({
        type: 'warning',
        title: 'Are you sure?',
        text: 'This post will be permanently deleted.',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#DC2626'
    }, function(){
        $.ajax({
            type: 'post',
            data: {postId: postId},
            url: '<?= base_url('app-admin/deletePost') ?>',
            success: function(response){
                if($.trim(response) != 'success'){
                    toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                } else {
                    toastr.success('Post deleted successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                    $('#post-'+postId).fadeOut(300, function(){
                        $(this).remove();
                    });
                }
            },
            error: function(err){
                toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            }
        });
    });
}
</script>
