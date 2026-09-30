<?php
// CI4: $this->session->* → session()->*
//      $this->page_record → $page_record
//      $this->pagination->create_links() → $pager->links()
$cscSearch      = session()->get('cscSearch');
$worldCountries = worldCountries();
?>
<div class="p-4 lg:p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">CSC List</h1>
            <p class="text-sm text-gray-500 mt-1">Manage CSC codes database</p>
        </div>
        <button 
            class="px-4 py-2 bg-accent hover:bg-accent-dark text-white rounded-lg transition-colors" 
            type="button" 
            onclick="addCSC()">
            <i class="fas fa-plus mr-2"></i>Add CSC
        </button>
    </div>

    <!-- Search Card -->
    <div class="bg-white rounded-lg shadow-card mb-6">
        <div class="p-6">
            <form action="<?= base_url() ?>app-admin/settings/csc" class="search-form" method="post">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input 
                            type="text" 
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors" 
                            name="searchIn" 
                            value="<?= $cscSearch['searchIn'] ?? '' ?>"
                            placeholder="Search by CSC code or country...">
                    </div>
                    <div class="md:col-span-4 flex items-end gap-2">
                        <button class="px-6 py-3 bg-accent hover:bg-accent-dark text-white rounded-lg transition-colors" type="submit">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                        <?php if (session()->has('cscSearch')) { ?>
                            <a href="<?= base_url() ?>app-admin/settings/csc?reset=true" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                                <i class="fas fa-redo mr-2"></i>Reset
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-lg shadow-card">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">#</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">CSC</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Country</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php
                        $i = $page_record ?? 0;
                        foreach ($record as $rec) {
                            echo '<tr id="csc-'.$rec->cscId.'" class="hover:bg-gray-50 transition-colors">';
                                echo '<td class="py-3 px-4 text-sm text-gray-600">'.++$i.'</td>';
                                echo '<td class="py-3 px-4 text-sm text-gray-800"><span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full font-mono text-xs font-semibold">'.$rec->csc.'</span></td>';
                                echo '<td class="py-3 px-4 text-sm text-gray-600">'.($worldCountries[$rec->country]['name'] ?? $rec->country).'</td>';
                                echo '<td class="py-3 px-4">';
                                    echo '<div class="flex items-center justify-center gap-2">';
                                        echo '<button onclick="editCSC(\''.$rec->cscId.'\',\''.$rec->country.'\',\''.$rec->csc.'\')" class="w-8 h-8 flex items-center justify-center bg-cyan-50 hover:bg-cyan-100 text-cyan-600 rounded-lg transition-colors"><i class="fas fa-edit"></i></button>';
                                        echo '<button onclick="deleteCSC(\''.$rec->cscId.'\')" class="w-8 h-8 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors"><i class="fas fa-trash"></i></button>';
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

<!-- CSC Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="csc-modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-lg">
            <div class="modal-header bg-gray-50 border-b border-gray-200">
                <h4 class="modal-title text-lg font-semibold text-gray-800">CSC</h4>
                <button type="button" class="close text-gray-400 hover:text-gray-600" data-dismiss="modal">&times;</button>
            </div>
            <form action="" class="csc-form" method="post">
                <input type="hidden" name="cscId" value="">
                <div class="modal-body p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country <span class="text-red-500">*</span></label>
                            <select class="form-control select2 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20" name="country">
                                <?php foreach ($worldCountries as $iso3 => $cdata) {
                                    echo '<option value="'.$iso3.'">'.$cdata['name'].'</option>';
                                } ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">CSC <span class="text-red-500">*</span></label>
                            <input type="text" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 uppercase font-mono" name="csc" maxlength="3" required="" placeholder="XSG">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 border-t border-gray-200 flex justify-between">
                    <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors" data-dismiss="modal">Close</button>
                    <button type="submit" class="px-6 py-2 bg-accent hover:bg-accent-dark text-white rounded-lg transition-colors">
                        <span class="btn-text">Submit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $('.select2:not(.normal)').each(function(){
        $(this).select2({dropdownParent: $(this).parent()});
    });
});

function addCSC() {
    $('#csc-modal .modal-title').text('Add CSC');
    $('.csc-form input[type="text"]').val('');
    $('.csc-form input[name="cscId"]').val('0');
    $('#csc-modal').modal();
}

function editCSC(cscId, country, csc) {
    $('#csc-modal .modal-title').text('Edit CSC');
    $('.csc-form input[name="cscId"]').val(cscId);
    $('.csc-form .select2[name="country"]').val(country).trigger('change');
    $('.csc-form input[name="csc"]').val(csc);
    $('#csc-modal').modal();
}

$("form.csc-form").submit(function(e){
    e.preventDefault();
    
    var btnObj = $('.csc-form button[type="submit"]');
    var btnText = btnObj.find('.btn-text');
    
    btnObj.prop('disabled', true);
    btnText.html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');
    
    $.ajax({
        url: '<?= base_url('app-admin/saveCSC') ?>',
        type: 'POST',
        data: $('.csc-form').serialize(),
        success: function(response){
            if(response != 'success'){
                toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                btnObj.prop('disabled', false);
                btnText.html('Submit');
            } else {
                toastr.success('CSC saved successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                setTimeout(function(){ window.location.reload(); }, 1000);
            }
        },
        error: function(err){
            toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            btnObj.prop('disabled', false);
            btnText.html('Submit');
        }
    });
});

function deleteCSC(cscId){
    swal({
        type: 'warning',
        title: 'Are you sure?',
        text: 'This action cannot be undone',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        confirmButtonColor: '#EF4444'
    }, function(){
        $.ajax({
            type: 'post',
            data: {cscId: cscId},
            url: '<?= base_url('app-admin/deleteCSC') ?>',
            success: function(response){
                $('#csc-' + cscId).fadeOut(300, function(){ $(this).remove(); });
                toastr.success('CSC deleted successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
            }
        });
    });
}
</script>
