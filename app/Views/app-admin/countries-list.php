<?php
// CI4: $this->session->* → session()->*
//      $this->page_record → $page_record
//      $this->pagination->create_links() → $pager->links()
$countrySearch = session()->get('countrySearch');
?>
<div class="p-4 lg:p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Country List</h1>
            <p class="text-sm text-gray-500 mt-1">Manage countries database</p>
        </div>
        <button 
            class="px-4 py-2 bg-accent hover:bg-accent-dark text-white rounded-lg transition-colors" 
            type="button" 
            onclick="addCountry()">
            <i class="fas fa-plus mr-2"></i>Add Country
        </button>
    </div>

    <!-- Search Card -->
    <div class="bg-white rounded-lg shadow-card mb-6">
        <div class="p-6">
            <form action="<?= base_url() ?>app-admin/settings/countries" class="search-form" method="post">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input 
                            type="text" 
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors" 
                            name="searchIn" 
                            value="<?= $countrySearch['searchIn'] ?? '' ?>"
                            placeholder="Search by Country, ISO2, or ISO3...">
                        <p class="text-xs text-gray-500 mt-1">Search in <b>Country | ISO2 | ISO3</b></p>
                    </div>
                    <div class="md:col-span-4 flex items-end gap-2">
                        <button class="px-6 py-3 bg-accent hover:bg-accent-dark text-white rounded-lg transition-colors" type="submit">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                        <?php if (session()->has('countrySearch')) { ?>
                            <a href="<?= base_url() ?>app-admin/settings/countries?reset=true" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
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
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Country</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">ISO 2</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">ISO 3</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Dialing Code</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php
                        $i = $page_record ?? 0;
                        foreach ($record as $rec) {
                            echo '<tr id="country-'.$rec->id.'" class="hover:bg-gray-50 transition-colors">';
                                echo '<td class="py-3 px-4 text-sm text-gray-600">'.++$i.'</td>';
                                echo '<td class="py-3 px-4 text-sm text-gray-800 font-medium">'.$rec->country.'</td>';
                                echo '<td class="py-3 px-4 text-sm text-gray-600"><span class="px-2 py-1 bg-blue-50 text-blue-700 rounded font-mono text-xs">'.$rec->iso2.'</span></td>';
                                echo '<td class="py-3 px-4 text-sm text-gray-600"><span class="px-2 py-1 bg-purple-50 text-purple-700 rounded font-mono text-xs">'.$rec->iso3.'</span></td>';
                                echo '<td class="py-3 px-4 text-sm text-gray-600">'.$rec->call_countrycode.'</td>';
                                echo '<td class="py-3 px-4">';
                                    echo '<div class="flex items-center justify-center gap-2">';
                                        echo '<button onclick="editCountry(\''.$rec->id.'\',\''.$rec->continent.'\',\''.addslashes($rec->country).'\',\''.$rec->iso2.'\',\''.$rec->iso3.'\',\''.$rec->call_countrycode.'\')" class="w-8 h-8 flex items-center justify-center bg-cyan-50 hover:bg-cyan-100 text-cyan-600 rounded-lg transition-colors"><i class="fas fa-edit"></i></button>';
                                        echo '<button onclick="deleteCountry(\''.$rec->id.'\')" class="w-8 h-8 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors"><i class="fas fa-trash"></i></button>';
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
<!-- Country Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="country-modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-lg">
            <div class="modal-header bg-gray-50 border-b border-gray-200">
                <h4 class="modal-title text-lg font-semibold text-gray-800">Country</h4>
                <button type="button" class="close text-gray-400 hover:text-gray-600" data-dismiss="modal">&times;</button>
            </div>
            <form action="" class="country-form" method="post">
                <input type="hidden" name="id" value="">
                <div class="modal-body p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Continent <span class="text-red-500">*</span></label>
                            <select class="form-control select2 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20" name="continent">
                                <?php
                                $d = ['1'=>'Africa','2'=>'Asia','3'=>'Europe','4'=>'North America','5'=>'South America','6'=>'Australia'];
                                foreach ($d as $cid => $ctext) {
                                    echo '<option value="'.$cid.'">'.$ctext.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country Name <span class="text-red-500">*</span></label>
                            <input type="text" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20" name="country" required="">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">ISO2 <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 uppercase" name="iso2" maxlength="2" required="" placeholder="US">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">ISO3 <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 uppercase" name="iso3" maxlength="3" required="" placeholder="USA">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dialing Code <span class="text-red-500">*</span></label>
                            <input type="text" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20" name="call_countrycode" required="" placeholder="+1">
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

function addCountry() {
    $('#country-modal .modal-title').text('Add Country');
    $('.country-form input[type="text"]').val('');
    $('.country-form input[name="id"]').val('0');
    $('#country-modal').modal();
}

function editCountry(id, continent, country, iso2, iso3, call_countrycode) {
    $('#country-modal .modal-title').text('Edit Country');
    $('.country-form input[name="id"]').val(id);
    $('.country-form .select2[name="continent"]').val(continent).trigger('change');
    $('.country-form input[name="country"]').val(country);
    $('.country-form input[name="iso2"]').val(iso2);
    $('.country-form input[name="iso3"]').val(iso3);
    $('.country-form input[name="call_countrycode"]').val(call_countrycode);
    $('#country-modal').modal();
}

$("form.country-form").submit(function(e){
    e.preventDefault();
    
    var btnObj = $('.country-form button[type="submit"]');
    var btnText = btnObj.find('.btn-text');
    
    btnObj.prop('disabled', true);
    btnText.html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');
    
    $.ajax({
        url: '<?= base_url('app-admin/saveCountry') ?>',
        type: 'POST',
        data: $('.country-form').serialize(),
        success: function(response){
            if(response != 'success'){
                toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                btnObj.prop('disabled', false);
                btnText.html('Submit');
            } else {
                toastr.success('Country saved successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
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

function deleteCountry(id){
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
            data: {id: id},
            url: '<?= base_url('app-admin/deleteCountry') ?>',
            success: function(response){
                $('#country-' + id).fadeOut(300, function(){ $(this).remove(); });
                toastr.success('Country deleted successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
            }
        });
    });
}
</script>
