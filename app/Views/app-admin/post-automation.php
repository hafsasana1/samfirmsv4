<?php
$shortCodes = array('{post-device}', '{post-model}', '{post-size}', '{post-product}', '{post-filename}', '{post-apversion}', '{post-os}', '{post-cscversion}', '{post-csc}', '{post-csccode}', '{post-bit}', '{post-uploaddate}', '{post-country}', '{post-ads}', '{post-builddate}', '{shortcode-device-data}');
?>
<div class="p-4 lg:p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Post Automation</h1>
        <p class="text-sm text-gray-500 mt-1">Configure automatic post generation with dynamic content tokens</p>
    </div>

    <!-- Token Reference Card -->
    <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-code text-purple-600 text-xl"></i>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-purple-900 mb-2">Available Tokens</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach($shortCodes as $code): ?>
                        <span class="inline-flex items-center px-3 py-1 bg-white border border-purple-300 rounded-full text-xs font-mono text-purple-700 hover:bg-purple-50 transition-colors cursor-pointer" onclick="copyToClipboard('<?= $code ?>')">
                            <?= $code ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <p class="text-xs text-purple-700 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Click any token to copy. These will be automatically replaced with actual post data.
                </p>
            </div>
        </div>
    </div>

    <!-- Automation Form Card -->
    <div class="bg-white rounded-lg shadow-card">
        <div class="p-6">
            <form action="" class="automation-form">
                <input type="hidden" name="type" value="post">
                <textarea name="template" class="template" style="display: none;"></textarea>
                
                <div class="space-y-6">
                    
                    <!-- Post Title Section -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Post Title Template <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="postTitle" 
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors" 
                            value="<?= $auto_data['title'] ?>"
                            placeholder="e.g., Download {post-device} {post-model} - {post-csc} - {post-filename}"
                            required>
                        <p class="text-xs text-gray-500 mt-1">Use tokens to create dynamic titles</p>
                    </div>

                    <!-- Post Content Section -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Post Content Template <span class="text-red-500">*</span>
                        </label>
                        <div class="border border-gray-300 rounded-lg overflow-hidden">
                            <textarea name="content" id="ckedit" cols="30" rows="10"><?= $auto_data['template'] ?></textarea>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Rich text editor with token support. Use tokens anywhere in your content.</p>
                    </div>

                    <!-- SEO Settings Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-search text-accent mr-2"></i>
                            SEO Configuration
                        </h3>
                        
                        <div class="space-y-5">
                            
                            <!-- Meta Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Meta Title
                                </label>
                                <input 
                                    type="text" 
                                    name="metaTitle" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors" 
                                    value="<?= $auto_data['metaTitle'] ?>"
                                    placeholder="SEO optimized title with tokens">
                            </div>

                            <!-- Meta Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Meta Description
                                </label>
                                <textarea 
                                    name="metaDesription" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors resize-y" 
                                    rows="3"
                                    placeholder="SEO description with tokens..."><?= $auto_data['metaDesription'] ?></textarea>
                                <p class="text-xs text-gray-500 mt-1">Recommended length: 150-160 characters</p>
                            </div>

                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="flex justify-end pt-6 border-t border-gray-200">
                        <button 
                            class="px-8 py-3 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                            type="submit">
                            <span class="btn-text">Save Template</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
// Copy token to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        toastr.success('Token copied: ' + text, '', {timeOut: 2000, positionClass: 'toast-top-right'});
    }).catch(function() {
        toastr.error('Failed to copy token', '', {timeOut: 2000, positionClass: 'toast-top-center'});
    });
}

// Initialize CKEditor and Select2
$(document).ready(function(){
    var veditor = CKEDITOR.replace('ckedit', {
        height: 600,
        toolbar: [
            { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
            { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
            { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
            '/',
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
            '/',
            { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
        ]
    });
    
    // Destroy any existing Select2 instance on tags
    if ($('.select-tags').data('select2')) {
        $('.select-tags').select2('destroy');
    }
    
    // Initialize Select2 for tags with proper configuration
    $('.select-tags').select2({
        tags: true,
        tokenSeparators: [',', ';'],
        placeholder: 'Type and press Enter to add tags...',
        width: '100%',
        minimumResultsForSearch: -1,
        allowClear: false,
        closeOnSelect: false
    });
});

// Automation Form Submission
$('form.automation-form').submit(function(e){
    e.preventDefault();
    
    var vfrom = $(this);
    var ckValue = CKEDITOR.instances['ckedit'].getData();
    vfrom.find('.template').val(ckValue);

    var btnObj = vfrom.find('button[type="submit"]');
    var btnText = btnObj.find('.btn-text');
    
    // Disable button and show loading state
    btnObj.prop('disabled', true);
    btnText.html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');

    $.ajax({
        type: 'post',
        data: vfrom.serialize(),
        url: '<?= base_url('app-admin/saveAutoPost') ?>',
        success: function(response){
            if($.trim(response) != 'success'){
                toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
            } else {
                toastr.success('Automation template saved successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
            }
            btnObj.prop('disabled', false);
            btnText.html('Save Template');
        },
        error: function(err){
            toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            btnObj.prop('disabled', false);
            btnText.html('Save Template');
        }
    });
});
</script>