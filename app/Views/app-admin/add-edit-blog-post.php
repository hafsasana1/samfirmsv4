<?php
// CI4: $this->uri->segment(3) → service('request')->getUri()->getSegment(3)
$actionType = service('request')->getUri()->getSegment(3);

$postTitle     = '';
$postContent   = '';
$metaTitle     = '';
$metaDesription = '';
$metaTags      = 'samsung firmware,samsung firmware download,samfirmware,firmware samsung,firmware download,samsung update,samsung firmware update,galaxy firmware,one ui';

$post = $post ?? new stdClass();

if (isset($post->postTitle) && $post->postTitle != '') {
    $postTitle      = $post->postTitle;
    $postContent    = $post->postContent;
    $metaTitle      = $post->metaTitle;
    $metaDesription = $post->metaDesription;
    $metaTags       = $post->metaTags;
}
?>
<div class="p-4 lg:p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800"><?= ucfirst($actionType) ?> Blog Post</h1>
            <p class="text-sm text-gray-500 mt-1"><?= $actionType == 'add' ? 'Create a new blog post' : 'Edit existing blog post' ?></p>
        </div>
        <a href="<?= base_url(ADMIN_PATH.'/blog') ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <!-- Blog Post Form Card -->
    <div class="bg-white rounded-lg shadow-card">
        <div class="p-6">
            <form method="post" class="post-form">
                <?= csrf_field() ?>
                <input type="hidden" name="postId"   value="<?= $actionType != 'add' ? ($post->postId ?? '') : '' ?>">
                <input type="hidden" name="postSlug" value="<?= $post->postSlug ?? '' ?>">
                <textarea name="template" class="template" style="display: none;"></textarea>
                
                <div class="space-y-6">
                    
                    <!-- Post Title & Slug Section -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Post Details</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Post Title <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors post-title" 
                                    name="title" 
                                    value="<?= $postTitle ?>" 
                                    onchange="updateSlug()"
                                    required>
                                <?php if(!empty($post->postSlug)): ?>
                                    <p class="text-xs text-gray-500 mt-2 post-permalink">
                                        <i class="fas fa-link text-accent mr-1"></i>
                                        <span class="font-mono"><?= base_url($post->postSlug) ?></span>
                                        <a href="javascript:;" class="text-accent hover:text-accent-dark ml-2" onclick="changeSlug(this)">
                                            <i class="fas fa-edit"></i> Edit Slug
                                        </a>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Categories
                                </label>
                                <select class="form-control select-tags w-full" name="category[]" multiple="">
                                    <?php
                                    $post_categories = @json_decode($post->category ?? '[]', true) ?: [];
                                    foreach ($getCategoreis as $category) {
                                        $sel = in_array($category, $post_categories) ? 'selected=""' : '';
                                        echo '<option value="'.$category.'" '.$sel.'>'.$category.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Post Content Section -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Post Content <span class="text-red-500">*</span>
                        </label>
                        <div class="border border-gray-300 rounded-lg overflow-hidden">
                            <textarea name="content" id="ckedit" cols="30" rows="10"><?= $postContent ?></textarea>
                        </div>
                    </div>

                    <!-- SEO Settings Section -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-search text-accent mr-2"></i>
                            SEO Settings
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Meta Title
                                </label>
                                <input 
                                    type="text" 
                                    name="metaTitle" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors" 
                                    value="<?= $metaTitle ?>">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Meta Description
                                </label>
                                <textarea 
                                    name="metaDesription" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors resize-y" 
                                    rows="3"><?= $metaDesription ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Post Status Section -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Publication Status</h3>
                        
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center cursor-pointer">
                                <input 
                                    name="postStatus" 
                                    type="radio" 
                                    value="Active" 
                                    <?= ($post->postStatus ?? '') != 'Inactive' ? 'checked=""' : '' ?>
                                    class="w-4 h-4 text-accent focus:ring-accent">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input 
                                    name="postStatus" 
                                    type="radio" 
                                    value="Inactive" 
                                    <?= ($post->postStatus ?? '') == 'Inactive' ? 'checked=""' : '' ?>
                                    class="w-4 h-4 text-red-500 focus:ring-red-500">
                                <span class="ml-2 text-sm text-gray-700">Inactive</span>
                            </label>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="flex justify-end pt-4 border-t border-gray-200">
                        <button 
                            class="px-8 py-3 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                            type="submit">
                            <span class="btn-text">
                                <i class="fas fa-save mr-2"></i><?= $actionType == 'add' ? 'Create Post' : 'Update Post' ?>
                            </span>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
var actionType = '<?= $actionType ?>';

$(document).ready(function(){
    // Initialize CKEditor
    CKEDITOR.replace('ckedit', {height: 600});
    
    // Initialize Select2 for tags and categories
    $('.select-tags').select2({
        tags: true,
        tokenSeparators: [','],
        placeholder: 'Select or type to add...',
        width: '100%'
    });
});

// Update slug from title
function updateSlug() {
    var title = $('.post-title').val();
    var vslug = $('.post-form input[name="postSlug"]').val();
    
    if(vslug == '' || vslug == undefined) {
        var slugUrl = getUrl(title);
        $('.post-form input[name="postSlug"]').val(slugUrl);
        var slugUpdate = '<a href="javascript:;" class="text-accent hover:text-accent-dark ml-2" onclick="changeSlug(this)"><i class="fas fa-edit"></i> Edit Slug</a>';
        $('.post-permalink').html('<i class="fas fa-link text-accent mr-1"></i><span class="font-mono"><?= base_url() ?>blog/' + slugUrl + '</span>' + slugUpdate);
    }
}

// Generate URL-friendly slug
function getUrl(link) {
    return $.trim(link.toLowerCase().replace(/ /g, '-').replace(/_/g, '-').replace(/[^\w-]+/g, '')).replace(/--/g, '-');
}

// Change slug functionality
function changeSlug(elem) {
    var currentSlug = $('.post-form input[name="postSlug"]').val();
    swal({
        title: "Edit Slug",
        text: "Enter new slug:",
        type: "input",
        showCancelButton: true,
        inputValue: currentSlug,
        confirmButtonText: "Update",
        closeOnConfirm: false
    }, function(inputValue) {
        if (inputValue === false) return false;
        if (inputValue === "") {
            swal.showInputError("Slug cannot be empty");
            return false;
        }
        var slugUrl = getUrl(inputValue);
        $('.post-form input[name="postSlug"]').val(slugUrl);
        var slugUpdate = '<a href="javascript:;" class="text-accent hover:text-accent-dark ml-2" onclick="changeSlug(this)"><i class="fas fa-edit"></i> Edit Slug</a>';
        $('.post-permalink').html('<i class="fas fa-link text-accent mr-1"></i><span class="font-mono"><?= base_url() ?>blog/' + slugUrl + '</span>' + slugUpdate);
        swal("Success!", "Slug updated successfully", "success");
    });
}

// Blog Post Form Submission
$('form.post-form').submit(function(e){
    e.preventDefault();
    
    var ckValue = CKEDITOR.instances['ckedit'].getData();
    $('.post-form .template').val(ckValue);
    
    var btnObj = $('.post-form button[type="submit"]');
    var btnText = btnObj.find('.btn-text');
    
    // Disable button and show loading state
    btnObj.prop('disabled', true);
    btnText.html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');

    $.ajax({
        type: 'post',
        data: $('.post-form').serialize(),
        url: '<?= base_url('app-admin/saveBlogPost') ?>',
        success: function(response) {
            if($.trim(response) != 'success') {
                toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                btnObj.prop('disabled', false);
                btnText.html('<i class="fas fa-save mr-2"></i>' + (actionType == 'add' ? 'Create Post' : 'Update Post'));
            } else {
                toastr.success('Blog post saved successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                setTimeout(function() {
                    window.location.href = '<?= base_url(ADMIN_PATH.'/blog') ?>';
                }, 1000);
            }
        },
        error: function(err) {
            toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            btnObj.prop('disabled', false);
            btnText.html('<i class="fas fa-save mr-2"></i>' + (actionType == 'add' ? 'Create Post' : 'Update Post'));
        }
    });
});
</script>
