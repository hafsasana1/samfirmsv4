<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">CMS Pages</h1>
            <p class="text-sm text-gray-600 mt-1">Manage your website content pages</p>
        </div>
        <a href="<?= base_url(ADMIN_PATH.'/cms/add') ?>" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white font-semibold rounded-lg transition-colors shadow-sm hover:shadow-md">
            <i class="fas fa-plus"></i>
            <span>Add Page</span>
        </a>
    </div>

    <!-- CMS Table Card -->
    <div class="bg-white rounded-lg shadow-card overflow-hidden">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-accent-soft rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-lg text-accent"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">All Pages</h2>
                    <p class="text-sm text-gray-600">Total: <?= count($record) ?> pages</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $i = $page_record ?? 0;
                    foreach($record as $rec){
                        $statusClass = $rec->status == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        echo '<tr id="page-'.$rec->pageId.'" class="hover:bg-gray-50 transition-colors">';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.++$i.'</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">'.$rec->pageTitle.'</div>
                                    <div class="text-xs text-gray-500">'.$rec->navTitle.'</div>
                                  </td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm">';
                            if($rec->pageType == '1'){
                                echo '<a href="'.base_url($rec->slugUrl).'" target="_blank" class="text-accent hover:text-accent-hover inline-flex items-center gap-1">
                                        '.$rec->slugUrl.'
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                      </a>';
                            } else {
                                echo '<span class="text-gray-400">N/A</span>';
                            }
                            echo '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">'.$rec->createdTime.'</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium '.$statusClass.'">
                                        '.$rec->status.'
                                    </span>
                                  </td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">';
                                        echo '<a href="'.base_url(ADMIN_PATH.'/cms/edit/'.$rec->pageId).'" 
                                                 class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-cyan-50 text-cyan-600 hover:bg-cyan-100 transition-colors"
                                                 title="Edit Page">
                                                <i class="fas fa-edit"></i>
                                              </a>';
                                        if($rec->pageType == '1'){
                                            echo '<button onclick="deletePage('.$rec->pageId.')" 
                                                         class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                         title="Delete Page">
                                                    <i class="fas fa-trash"></i>
                                                  </button>';
                                        }
                            echo '      </div>
                                  </td>';
                        echo '</tr>';
                    }
                    if(count($record) == 0){
                        echo '<tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <i class="fas fa-file-alt text-4xl mb-3"></i>
                                        <p class="text-sm font-medium">No pages found</p>
                                        <a href="'.base_url(ADMIN_PATH.'/cms/add').'" class="mt-3 text-accent hover:text-accent-hover text-sm">
                                            <i class="fas fa-plus mr-1"></i> Create your first page
                                        </a>
                                    </div>
                                </td>
                              </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script type="text/javascript">
function deletePage(pageId){
    swal({
        type: 'warning',
        title: 'Are you sure?',
        text: 'This action cannot be undone. The page will be permanently deleted.',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#DC2626'
    }, function(){
        $.ajax({
            type: 'post',
            data: {pageId: pageId, <?= csrf_token() ?>: '<?= csrf_hash() ?>'},
            url: '<?= base_url(ADMIN_PATH.'/deletePage') ?>',
            success: function(response){
                if($.trim(response) != 'success'){
                    toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                } else {
                    toastr.success('Page deleted successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                    $('#page-'+pageId).fadeOut(300, function(){
                        $(this).remove();
                    });
                }
            },
            error: function(err){
                toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            }
        })
    });
}
</script>
