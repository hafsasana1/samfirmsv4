<?php
$blogSearch = session()->get('blogSearch');
?>

<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Blog Posts</h1>
            <p class="text-sm text-gray-600 mt-1">Manage your blog articles and content</p>
        </div>
        <a href="<?= base_url(ADMIN_PATH.'/blog/add') ?>" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white font-semibold rounded-lg transition-colors shadow-sm hover:shadow-md">
            <i class="fas fa-plus"></i>
            <span>Add Post</span>
        </a>
    </div>

    <!-- Search Card -->
    <div class="bg-white rounded-lg shadow-card mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Search Blog Posts</h2>
        </div>
        <form action="<?= base_url() ?>app-admin/blog" class="search-form" method="post">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="searchIn" value="<?= $blogSearch['searchIn'] ?? '' ?>"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                               placeholder="Search by title...">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" 
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-search"></i>
                            <span>Search</span>
                        </button>
                        <?php if (session()->has('blogSearch')): ?>
                            <a href="<?= base_url() ?>app-admin/blog?reset=true" 
                               class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors"
                               title="Reset">
                                <i class="fas fa-undo"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gradient-to-r from-accent to-accent-hover border-t border-gray-200">
                <div class="flex items-center justify-between text-white">
                    <span class="font-medium">Total Posts</span>
                    <span class="text-2xl font-bold"><?= number_format($total_rows) ?></span>
                </div>
            </div>
        </form>
    </div>

    <!-- Posts Table -->
    <div class="bg-white rounded-lg shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[250px]">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20"><i class="fas fa-comment"></i></th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20"><i class="fas fa-heart"></i></th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20"><i class="fas fa-eye"></i></th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $i = $page_record ?? 0;
                    foreach ($record as $rec) {
                        $statusClass = 'bg-green-100 text-green-800';
                        if ($rec->postStatus == 'Inactive') $statusClass = 'bg-red-100 text-red-800';
                        if ($rec->postStatus == 'Draft') $statusClass = 'bg-blue-100 text-blue-800';
                        
                        $categories = @json_decode($rec->category, true) ?: [];
                        $post_link = base_url().'blog/'.$rec->postSlug;
                        
                        echo '<tr id="post-'.$rec->postId.'" class="hover:bg-gray-50 transition-colors">';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">'.++$i.'</td>';
                            echo '<td class="px-4 py-3">
                                    <a href="'.$post_link.'" target="_blank" class="text-accent hover:text-accent-hover font-medium inline-flex items-center gap-1">
                                        '.$rec->postTitle.'
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                  </td>';
                            echo '<td class="px-4 py-3"><div class="flex flex-wrap gap-1">';
                            foreach($categories as $cat) {
                                echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-accent-soft text-accent">'.$cat.'</span>';
                            }
                            echo '</div></td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-cyan-50 text-cyan-600 font-medium">'.$rec->commentCount.'</span>
                                  </td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-pink-50 text-pink-600 font-medium">'.$rec->likesCount.'</span>
                                  </td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-purple-50 text-purple-600 font-medium">'.$rec->viewsCount.'</span>
                                  </td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">'.$rec->modifiedTime.'</td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium '.$statusClass.'">
                                        '.$rec->postStatus.'
                                    </span>
                                  </td>';
                            echo '<td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">';
                                        echo '<a href="'.base_url(ADMIN_PATH.'/blog/edit/'.$rec->postId).'" 
                                                 class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-cyan-50 text-cyan-600 hover:bg-cyan-100 transition-colors"
                                                 title="Edit">
                                                <i class="fas fa-edit text-sm"></i>
                                              </a>';
                                        echo '<button onclick="deletePost('.$rec->postId.')" 
                                                     class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                     title="Delete">
                                                <i class="fas fa-trash text-sm"></i>
                                              </button>';
                            echo '      </div>
                                  </td>';
                        echo '</tr>';
                    }
                    if(count($record) == 0){
                        echo '<tr><td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-blog text-4xl mb-3"></i>
                                    <p class="text-sm font-medium">No blog posts found</p>
                                    <a href="'.base_url(ADMIN_PATH.'/blog/add').'" class="mt-3 text-accent hover:text-accent-hover text-sm">
                                        <i class="fas fa-plus mr-1"></i> Create your first post
                                    </a>
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
function deletePost(postId){
    swal({
        type: 'warning',
        title: 'Are you sure?',
        text: 'This blog post will be permanently deleted.',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#DC2626'
    }, function(){
        $.ajax({
            type: 'post',
            data: {postId: postId},
            url: '<?= base_url('app-admin/deleteBlogPost') ?>',
            success: function(response){
                if($.trim(response) != 'success'){
                    toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                } else {
                    toastr.success('Blog post deleted successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                    $('#post-'+postId).fadeOut(300, function(){ $(this).remove(); });
                }
            },
            error: function(err){
                toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            }
        });
    });
}
</script>
