<!-- Page Header -->
<div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Contact Us Requests</h1>
        <p class="text-sm text-gray-600 mt-1">View and manage customer inquiries</p>
    </div>

    <!-- Contact Requests Table -->
    <div class="bg-white rounded-lg shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-envelope text-lg text-cyan-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">All Requests</h2>
                    <p class="text-sm text-gray-600">Total: <?= count($record) ?> inquiries</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Website</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $i = $page_record ?? 0;
                    foreach ($record as $rec) {
                        $bgClass = $rec->status == '0' ? 'bg-cyan-50' : '';
                        $badgeClass = $rec->status == '0' ? 'bg-cyan-100 text-cyan-800' : 'bg-gray-100 text-gray-600';
                        
                        echo '<tr id="contact-us-'.$rec->contactUsId.'" class="hover:bg-gray-50 transition-colors '.$bgClass.'">';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">'.++$i.'</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gradient-to-br from-accent to-cyan-600 rounded-full flex items-center justify-center text-white font-bold">
                                                '.strtoupper(substr($rec->fromName, 0, 1)).'
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">'.$rec->fromName.'</div>
                                            <div class="text-xs text-gray-500">'.$rec->ipAddress.'</div>
                                        </div>';
                            if($rec->status == '0'){
                                echo '          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium '.$badgeClass.'">
                                                    <i class="fas fa-circle text-[6px] mr-1"></i>New
                                                </span>';
                            }
                            echo '      </div>
                                  </td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap">
                                    <a href="mailto:'.$rec->fromEmail.'?subject=Re: '.substr(strip_tags($rec->description),0,100).'" 
                                       class="text-accent hover:text-accent-hover inline-flex items-center gap-1">
                                        '.$rec->fromEmail.'
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                  </td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">';
                            if($rec->website != ''){
                                echo '<a href="'.$rec->website.'" target="_blank" class="text-accent hover:text-accent-hover inline-flex items-center gap-1">
                                        Website <i class="fas fa-external-link-alt text-xs"></i>
                                      </a>';
                            } else {
                                echo '<span class="text-gray-400">—</span>';
                            }
                            echo '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">'.$rec->createdTime.'</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">';
                                        echo '<a href="'.base_url(ADMIN_PATH.'/contact-us/'.$rec->contactUsId).'" 
                                                 class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-cyan-50 text-cyan-600 hover:bg-cyan-100 transition-colors"
                                                 title="View Details">
                                                <i class="fas fa-eye text-sm"></i>
                                              </a>';
                                        echo '<button onclick="deleteContactUS('.$rec->contactUsId.')" 
                                                     class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                     title="Delete">
                                                <i class="fas fa-trash text-sm"></i>
                                              </button>';
                            echo '      </div>
                                  </td>';
                        echo '</tr>';
                    }
                    if(count($record) == 0){
                        echo '<tr><td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p class="text-sm font-medium">No contact requests yet</p>
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
function deleteContactUS(contactUsId){
    swal({
        type: 'warning',
        title: 'Are you sure?',
        text: 'This contact request will be permanently deleted.',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#DC2626'
    }, function(){
        $.ajax({
            type: 'post',
            data: {contactUsId: contactUsId},
            url: '<?= base_url('app-admin/deleteContactUS') ?>',
            success: function(response){
                if($.trim(response) != 'success'){
                    toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                } else {
                    toastr.success('Contact request deleted successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                    $('#contact-us-'+contactUsId).fadeOut(300, function(){ $(this).remove(); });
                }
            },
            error: function(err){
                toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            }
        });
    });
}
</script>
