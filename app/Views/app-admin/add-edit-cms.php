<?php
$actionType = service('request')->getUri()->getSegment(3);
?>

<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= ucfirst($actionType) ?> Page</h1>
            <p class="text-sm text-gray-600 mt-1">
                <?= $actionType == 'add' ? 'Create a new CMS page' : 'Update existing page details' ?>
            </p>
        </div>
        <a href="<?= base_url(ADMIN_PATH.'/cms') ?>" 
           class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Back to List</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-card overflow-hidden">
        <form method="post" class="page-form">
            <?= csrf_field() ?>
            <input type="hidden" name="pageId" value="<?= $page->pageId ?? '' ?>">
            <textarea name="template" class="template" style="display: none;"><?= $page->pageContent ?? '' ?></textarea>

            <!-- Basic Information Section -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
                <p class="text-sm text-gray-600 mt-1">Page title and navigation settings</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Page Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Page Title <span class="text-red-600">*</span>
                    </label>
                    <input type="text" 
                           id="title"
                           name="title" 
                           value="<?= $page->pageTitle ?? '' ?>"
                           onchange="updateSlug(this.value)"
                           required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                           placeholder="Enter page title">
                    <p class="mt-2 text-sm page-slug">
                        <?= isset($page->slugUrl) && $page->slugUrl != '' ? '<a href="'.base_url($page->slugUrl).'" target="_blank" class="text-accent hover:text-accent-hover inline-flex items-center gap-1">'.base_url($page->slugUrl).' <i class="fas fa-external-link-alt text-xs"></i></a>' : '' ?>
                    </p>
                </div>

                <!-- Nav Title -->
                <div>
                    <label for="navTitle" class="block text-sm font-medium text-gray-700 mb-2">
                        Navigation Title
                    </label>
                    <input type="text" 
                           id="navTitle"
                           name="navTitle" 
                           value="<?= $page->navTitle ?? '' ?>"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                           placeholder="Title shown in navigation menu">
                    <p class="mt-1 text-xs text-gray-500">Leave blank to use page title</p>
                </div>
            </div>

            <!-- Content Section -->
            <div class="px-6 py-4 border-t border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Page Content</h2>
                <p class="text-sm text-gray-600 mt-1">Main content of the page</p>
            </div>

            <div class="p-6">
                <textarea name="content" id="ckedit" cols="30" rows="10"><?= $page->pageContent ?? '' ?></textarea>
            </div>

            <!-- SEO Settings Section -->
            <div class="px-6 py-4 border-t border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">SEO Settings</h2>
                <p class="text-sm text-gray-600 mt-1">Optimize your page for search engines</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Meta Title -->
                <div>
                    <label for="metaTitle" class="block text-sm font-medium text-gray-700 mb-2">
                        Meta Title
                    </label>
                    <input type="text" 
                           id="metaTitle"
                           name="metaTitle" 
                           value="<?= $page->metaTitle ?? '' ?>"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                           placeholder="SEO title for search engines">
                </div>

                <!-- Meta Description -->
                <div>
                    <label for="metaDesription" class="block text-sm font-medium text-gray-700 mb-2">
                        Meta Description
                    </label>
                    <textarea id="metaDesription"
                              name="metaDesription" 
                              rows="3"
                              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors resize-y"
                              placeholder="Brief description for search results"><?= $page->metaDesription ?? '' ?></textarea>
                    <p class="mt-1 text-xs text-gray-500">Recommended: 150-160 characters</p>
                </div>
            </div>

            <!-- Display Settings Section -->
            <div class="px-6 py-4 border-t border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Display Settings</h2>
                <p class="text-sm text-gray-600 mt-1">Configure where this page appears</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Position -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Navigation Position
                    </label>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" 
                                   name="position" 
                                   value="header" 
                                   <?= ($page->position ?? '') == 'header' ? 'checked=""' : '' ?>
                                   class="w-4 h-4 text-accent border-gray-300 focus:ring-accent">
                            <span class="ml-2 text-sm text-gray-700">Header Navigation</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" 
                                   name="position" 
                                   value="footer" 
                                   <?= ($page->position ?? '') != 'header' ? 'checked=""' : '' ?>
                                   class="w-4 h-4 text-accent border-gray-300 focus:ring-accent">
                            <span class="ml-2 text-sm text-gray-700">Footer Navigation</span>
                        </label>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Page Status
                    </label>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" 
                                   name="status" 
                                   value="Active" 
                                   <?= ($page->status ?? '') != 'Inactive' ? 'checked=""' : '' ?>
                                   class="w-4 h-4 text-accent border-gray-300 focus:ring-accent">
                            <span class="ml-2 text-sm text-gray-700">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            </span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" 
                                   name="status" 
                                   value="Inactive" 
                                   <?= ($page->status ?? '') == 'Inactive' ? 'checked=""' : '' ?>
                                   class="w-4 h-4 text-accent border-gray-300 focus:ring-accent">
                            <span class="ml-2 text-sm text-gray-700">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            </span>
                        </label>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Inactive pages won't be visible on the website</p>
                </div>
            </div>

            <!-- Form Footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-end gap-3">
                    <a href="<?= base_url(ADMIN_PATH.'/cms') ?>" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-accent hover:bg-accent-hover text-white font-semibold rounded-lg transition-colors shadow-sm hover:shadow-md flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Save Page</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

<script type="text/javascript">
var actionType = '<?= $actionType ?>';

$(document).ready(function(){
    if (typeof $.fn.inputmask !== 'undefined') {
        $('[data-inputmask]').inputmask();
    }
    
    // Initialize CKEditor
    CKEDITOR.replace('ckedit', {
        height: 500,
        toolbar: [
            { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print' ] },
            { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
            '/',
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
            '/',
            { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
        ]
    });
});

function updateSlug(title){
    var slugUrl = $.trim(title.toLowerCase().replace(/ /g,'-').replace(/_/g,'-').replace(/[^\w-]+/g,'')).replace(/--/g,'-');
    if(actionType == 'add'){
        $('.page-slug').html('<span class="text-gray-600">Preview URL: </span><span class="text-accent font-medium"><?= base_url() ?>'+slugUrl+'</span>');
    }
}

$('form.page-form').submit(function(e){
    e.preventDefault();
    
    var ckValue = CKEDITOR.instances['ckedit'].getData();
    $('.page-form .template').val(ckValue);
    
    var submitBtn = $('.page-form button[type="submit"]');
    var originalHtml = submitBtn.html();
    
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');
    
    $.ajax({
        type: 'post',
        data: $('.page-form').serialize(),
        url: '<?= base_url('app-admin/savePage') ?>',
        success: function(response){
            if($.trim(response) != 'success'){
                toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                submitBtn.prop('disabled', false).html(originalHtml);
            } else {
                toastr.success('Page saved successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                setTimeout(function(){
                    window.location.href = '<?= base_url(ADMIN_PATH.'/cms') ?>';
                }, 1500);
            }
        },
        error: function(err){
            toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            submitBtn.prop('disabled', false).html(originalHtml);
        }
    });
});
</script>
