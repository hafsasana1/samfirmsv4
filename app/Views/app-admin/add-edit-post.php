<?php
$actionType = service('request')->getUri()->getSegment(3);
$auto_data  = getSiteMeta('postAutomation');

$postTitle      = $auto_data['title']          ?? '';
$postContent    = $auto_data['template']       ?? '';
$metaTitle      = $auto_data['metaTitle']      ?? '';
$metaDesription = $auto_data['metaDesription'] ?? '';
$metaTags       = $auto_data['metaTags']       ?? '';

if ($actionType == 'edit' && isset($post)) {
    $postTitle      = $post->postTitle;
    $postContent    = $post->postContent;
    $metaTitle      = $post->metaTitle;
    $metaDesription = $post->metaDesription;
    $metaTags       = $post->metaTags;
}
$post = $post ?? new stdClass();
?>

<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= ucfirst($actionType) ?> Firmware Post</h1>
            <p class="text-sm text-gray-600 mt-1">
                <?= $actionType == 'add' ? 'Create a new firmware post' : 'Update existing post details' ?>
            </p>
        </div>
        <a href="<?= base_url(ADMIN_PATH.'/posts') ?>" 
           class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Back to List</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-card overflow-hidden">
        <form method="post" class="post-form">
            <?= csrf_field() ?>
            <input type="hidden" name="postId" value="<?= $actionType == 'edit' ? ($post->postId ?? '') : '0' ?>">
            <input type="hidden" name="postSlug" value="<?= $actionType == 'edit' ? ($post->postSlug ?? '') : '' ?>">
            <textarea name="template" class="template" style="display: none;"></textarea>

            <!-- Basic Information -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Post Title <span class="text-red-600">*</span></label>
                    <input type="text" name="title" value="<?= $postTitle ?>" required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors post-title"
                           placeholder="Enter post title" onchange="updateSlug()">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Categories</label>
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                        <select class="form-control select-tags w-full" name="tags[]" multiple="">
                            <?php
                            $post_tags = @json_decode($post->tags ?? '[]', true) ?: [];
                            foreach ($getTags as $tag) {
                                $sel = in_array($tag, $post_tags) ? 'selected=""' : '';
                                echo '<option value="'.$tag.'" '.$sel.'>'.$tag.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Post Content -->
            <div class="px-6 py-4 border-t border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Post Content</h2>
            </div>
            <div class="p-6">
                <textarea name="content" id="ckedit" cols="30" rows="10"><?= $postContent ?></textarea>
            </div>

            <!-- Firmware Details -->
            <div class="px-6 py-4 border-t border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Firmware Details</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Version <span class="text-red-600">*</span></label>
                        <input type="text" name="version" value="<?= $post->version ?? '' ?>" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                               placeholder="e.g., A145FXXU5CVK4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">OS</label>
                        <input type="text" name="os" value="<?= $post->os ?? '' ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                               placeholder="e.g., Android 13">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bit (Binary/U)</label>
                        <input type="text" name="bit" value="<?= $post->bit ?? '' ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                               placeholder="e.g., 5">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Device <span class="text-red-600">*</span></label>
                        <input type="text" name="device" value="<?= $post->device ?? '' ?>" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                               placeholder="e.g., Galaxy A14 5G">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Model <span class="text-red-600">*</span></label>
                        <input type="text" name="model" value="<?= $post->model ?? '' ?>" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                               placeholder="e.g., SM-A145F">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CSC Version <span class="text-red-600">*</span></label>
                        <input type="text" name="cscversion" value="<?= $post->cscversion ?? '' ?>" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                               placeholder="e.g., A145FOXM5CVK3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <select class="form-control select2-country-flag w-full" name="country">
                            <?php
                            $defCountry = ($post->country ?? '') == '' ? 'USA' : $post->country;
                            foreach (worldCountries() as $cId => $pc) {
                                $sel = $cId == $defCountry ? 'selected=""' : '';
                                echo '<option country-iso2="'.$pc['code'].'" value="'.$cId.'" '.$sel.'>'.$pc['name'].' ('.$pc['code'].')</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CSC</label>
                        <input type="text" name="csc" value="<?= $post->csc ?? '' ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                               placeholder="e.g., OXM">
                    </div>
                </div>
            </div>

            <!-- Download Links -->
            <div class="px-6 py-4 border-t border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Download Links</h2>
            </div>
            <div class="p-6 space-y-6">
                <?php $downloadButton = @json_decode($post->downloadButton ?? '{}', true); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Button URL</label>
                        <input type="text" name="buttonUrl" value="<?= $downloadButton['buttonUrl'] ?? '' ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                               placeholder="https://example.com/download">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Button Text</label>
                        <input type="text" name="buttonText" value="<?= $downloadButton['buttonText'] ?? '' ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                               placeholder="Download Now">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">External Download Link</label>
                    <input type="text" name="externalFileLink" value="<?= $post->externalFileLink ?? '' ?>"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                           placeholder="https://example.com/file.zip">
                    <p class="mt-1 text-xs text-gray-500">External link for file automation</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Build Date</label>
                        <input type="text" name="buildDate" value="<?= $post->buildDate ?? '' ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                               placeholder="2023-12-15">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">File Size</label>
                        <input type="text" name="fileSize" value="<?= $post->fileSize ?? '' ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                               placeholder="e.g., 5.2 GB">
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="px-6 py-4 border-t border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">SEO Settings</h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                    <input type="text" name="metaTitle" value="<?= $metaTitle ?>"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                           placeholder="SEO title for search engines">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                    <textarea name="metaDesription" rows="3"
                              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors resize-y"
                              placeholder="Brief description for search results"><?= $metaDesription ?></textarea>
                </div>
            </div>

            <!-- Post Status -->
            <div class="px-6 py-4 border-t border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Post Status</h2>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="postStatus" value="Active" 
                               <?= ($post->postStatus ?? '') != 'Inactive' ? 'checked=""' : '' ?>
                               class="w-4 h-4 text-accent border-gray-300 focus:ring-accent">
                        <span class="ml-2 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                        </span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="postStatus" value="Inactive" 
                               <?= ($post->postStatus ?? '') == 'Inactive' ? 'checked=""' : '' ?>
                               class="w-4 h-4 text-accent border-gray-300 focus:ring-accent">
                        <span class="ml-2 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Form Footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-end gap-3">
                    <a href="<?= base_url(ADMIN_PATH.'/posts') ?>" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-accent hover:bg-accent-hover text-white font-semibold rounded-lg transition-colors shadow-sm hover:shadow-md flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Save Post</span>
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
    
    CKEDITOR.replace('ckedit', {height: 500});
});

function updateSlug(){
    // Slug update logic if needed
}

$('form.post-form').submit(function(e){
    e.preventDefault();
    
    var ckValue = CKEDITOR.instances['ckedit'].getData();
    $('.post-form .template').val(ckValue);
    
    var submitBtn = $('.post-form button[type="submit"]');
    var originalHtml = submitBtn.html();
    
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');
    
    $.ajax({
        type: 'post',
        data: $('.post-form').serialize(),
        url: '<?= base_url('app-admin/savePost') ?>',
        success: function(response){
            if($.trim(response) != 'success'){
                toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                submitBtn.prop('disabled', false).html(originalHtml);
            } else {
                toastr.success('Post saved successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                setTimeout(function(){
                    window.location.href = '<?= base_url(ADMIN_PATH.'/posts') ?>';
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
