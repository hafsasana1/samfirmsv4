<?php
$record = $record ?? null;
$webLogo = base_url().'resource/default/logo.png';
$favicon = base_url().'resource/default/favicon.ico';
if ($record && !empty($record->webLogo) && file_exists(RESOURCE_PATH.$record->webLogo)) {
    $webLogo = base_url().'resource/'.$record->webLogo;
}
if ($record && !empty($record->favicon) && file_exists(RESOURCE_PATH.$record->favicon)) {
    $favicon = base_url().'resource/'.$record->favicon;
}
$webLogo = $webLogo.'?v='.time();
$favicon = $favicon.'?v='.time();
?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Website Settings</h1>
    <p class="text-sm text-gray-600 mt-1">Configure your website branding and meta information</p>
</div>

<!-- Settings Card -->
<div class="bg-white rounded-lg shadow-card overflow-hidden">
        <form method="post" class="web-form" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="save" value="yes">

            <!-- Branding Section -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Branding</h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website Title <span class="text-red-600">*</span></label>
                    <input type="text" name="webTitle" value="<?= $record->webTitle ?? '' ?>" required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                           placeholder="Enter website title">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Logo Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Website Logo
                            <span class="text-xs text-gray-500 ml-2">(Header & Footer)</span>
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-accent transition-colors">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <i class="fas fa-upload"></i>
                                    <span class="text-sm font-medium w-image-text">Choose Logo</span>
                                </div>
                                <input type="file" name="webLogo" class="hidden w-image-input" accept="image/png,image/jpg,image/jpeg" onchange="previewImage(this,'w-image')">
                            </label>
                            
                            <!-- Size Recommendations -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs">
                                <div class="flex items-start gap-2 mb-2">
                                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold text-blue-900 mb-1">Recommendations:</p>
                                        <ul class="space-y-1 text-blue-800">
                                            <li>📐 <strong>Dimensions:</strong> 300×40px to 400×60px</li>
                                            <li>📦 <strong>File Size:</strong> Max 500KB</li>
                                            <li>✅ <strong>Format:</strong> PNG (transparent bg recommended), JPG, JPEG</li>
                                            <li>💡 <strong>Tip:</strong> Use transparent PNG for best results</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Current Logo Preview -->
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <p class="text-xs font-semibold text-gray-700 mb-2 text-center">Current Logo:</p>
                                <div class="text-center min-h-[60px] flex items-center justify-center">
                                    <img src="<?= $webLogo ?>" alt="Logo" class="max-h-[60px] w-auto w-image-preview">
                                </div>
                            </div>
                            
                            <!-- Fallback Preview -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-xs font-semibold text-yellow-900 mb-2 flex items-center gap-2">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    If no logo uploaded:
                                </p>
                                <div class="bg-white rounded border border-yellow-300 p-3 text-center">
                                    <span class="text-2xl font-bold" style="color: #1FBF8F; font-family: 'Inter', sans-serif;">
                                        <?= $record->webTitle ?? 'SamFirms' ?>
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">Website name displays as text</p>
                                </div>
                            </div>
                            
                            <!-- Remove Logo Button -->
                            <?php if ($record && !empty($record->webLogo) && file_exists(RESOURCE_PATH.$record->webLogo)): ?>
                            <button type="button" onclick="removeLogo()" 
                                    class="w-full px-3 py-2 text-sm bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-trash-alt"></i>
                                Remove Logo (Use Text Fallback)
                            </button>
                            <input type="hidden" name="removeLogo" id="removeLogo" value="">
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Favicon Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Favicon
                            <span class="text-xs text-gray-500 ml-2">(Browser Tab Icon)</span>
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-accent transition-colors">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <i class="fas fa-upload"></i>
                                    <span class="text-sm font-medium f-image-text">Choose Favicon</span>
                                </div>
                                <input type="file" name="favicon" class="hidden f-image-input" accept=".ico,.png" onchange="previewImage(this,'f-image')">
                            </label>
                            
                            <!-- Size Recommendations -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs">
                                <div class="flex items-start gap-2 mb-2">
                                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold text-blue-900 mb-1">Recommendations:</p>
                                        <ul class="space-y-1 text-blue-800">
                                            <li>📐 <strong>Dimensions:</strong> 32×32px or 16×16px</li>
                                            <li>📦 <strong>File Size:</strong> Max 100KB</li>
                                            <li>✅ <strong>Format:</strong> ICO (recommended) or PNG</li>
                                            <li>💡 <strong>Tip:</strong> Use <a href="https://favicon.io" target="_blank" class="text-blue-600 underline hover:text-blue-800">favicon.io</a> to convert</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Current Favicon Preview -->
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <p class="text-xs font-semibold text-gray-700 mb-2 text-center">Current Favicon:</p>
                                <div class="text-center min-h-[40px] flex items-center justify-center">
                                    <img src="<?= $favicon ?>" alt="Favicon" class="w-8 h-8 f-image-preview">
                                </div>
                                <p class="text-xs text-gray-500 mt-2 text-center">Appears in browser tab</p>
                            </div>
                            
                            <!-- Remove Favicon Button -->
                            <?php if ($record && !empty($record->favicon) && file_exists(RESOURCE_PATH.$record->favicon)): ?>
                            <button type="button" onclick="removeFavicon()" 
                                    class="w-full px-3 py-2 text-sm bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-trash-alt"></i>
                                Remove Favicon (Use Default)
                            </button>
                            <input type="hidden" name="removeFavicon" id="removeFavicon" value="">
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Open Graph Image
                            <span class="text-xs text-gray-500">(Social Media Preview)</span>
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-accent transition-colors">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <i class="fas fa-image"></i>
                                    <span class="text-sm font-medium og-image-text">Choose OG Image</span>
                                </div>
                                <input type="file" name="og_image" class="hidden og-image-input" accept="image/png,image/jpg,image/jpeg" onchange="previewImage(this,'og-image')">
                            </label>
                            <p class="text-xs text-gray-500">
                                📐 Recommended: 1200x630px (JPG/PNG) | Used for Facebook, Twitter, LinkedIn preview
                            </p>
                            <?php 
                            $ogImagePath = base_url().'resource/og-image-default.jpg';
                            if ($record && !empty($record->og_image) && file_exists(RESOURCE_PATH.$record->og_image)) {
                                $ogImagePath = base_url().'resource/'.$record->og_image;
                            }
                            ?>
                            <div class="p-4 bg-gray-50 rounded-lg text-center">
                                <img src="<?= $ogImagePath ?>?v=<?= time() ?>" alt="OG Image" class="max-w-full h-auto mx-auto og-image-preview" style="max-height: 200px;">
                                <p class="text-xs text-gray-500 mt-2">Preview: How your page appears on social media</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Section -->
            <div class="px-6 py-4 border-t border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">SEO Settings</h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                    <input type="text" name="metaTitle" value="<?= $record->metaTitle ?? '' ?>"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                    <textarea name="metaDesription" rows="3"
                              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors resize-y"><?= $record->metaDesription ?? '' ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Header Search Title</label>
                    <input type="text" name="headerSearchTitle" value="<?= $record->headerSearchTitle ?? '' ?>"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Header Search Description</label>
                    <textarea name="headerSearchDescription" rows="3"
                              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors resize-y"><?= $record->headerSearchDescription ?? '' ?></textarea>
                </div>
            </div>

            <!-- Social Links Section -->
            <div class="px-6 py-4 border-t border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Social Media Links</h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-twitter text-blue-400 mr-2"></i>Twitter Link
                    </label>
                    <input type="url" name="twitterLink" value="<?= $record->twitterLink ?? '' ?>"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                           placeholder="https://twitter.com/yourprofile">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-facebook text-blue-600 mr-2"></i>Facebook Link
                    </label>
                    <input type="url" name="facebookLink" value="<?= $record->facebookLink ?? '' ?>"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                           placeholder="https://facebook.com/yourpage">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-youtube text-red-600 mr-2"></i>Youtube Link
                    </label>
                    <input type="url" name="youtubeLink" value="<?= $record->youtubeLink ?? '' ?>"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                           placeholder="https://youtube.com/yourchannel">
                </div>
            </div>

            <!-- Form Footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <button type="submit" 
                        class="w-full md:w-auto px-6 py-3 bg-accent hover:bg-accent-hover text-white font-semibold rounded-lg transition-colors shadow-sm hover:shadow-md flex items-center justify-center gap-2 do-save">
                    <i class="fas fa-save"></i>
                    <span>Save Settings</span>
                </button>
            </div>
        </form>
    </div>

<script type="text/javascript">
// Initialize Select2 for tags
$(document).ready(function(){
    // Destroy any existing Select2 instance
    if ($('.select-tags').data('select2')) {
        $('.select-tags').select2('destroy');
    }
    
    // Initialize with proper configuration
    $('.select-tags').select2({
        tags: true,
        tokenSeparators: [",", ";"],
        placeholder: "Type and press Enter to add tags...",
        width: '100%',
        minimumResultsForSearch: -1,
        allowClear: false,
        closeOnSelect: false
    });
    
    // File size validation
    $('.w-image-input').on('change', function() {
        validateFileSize(this, 500 * 1024, 'Logo'); // 500KB
    });
    
    $('.f-image-input').on('change', function() {
        validateFileSize(this, 100 * 1024, 'Favicon'); // 100KB
    });
    
    $('.og-image-input').on('change', function() {
        validateFileSize(this, 1024 * 1024, 'OG Image'); // 1MB
    });
});

function validateFileSize(input, maxSize, label) {
    if (input.files && input.files[0]) {
        const fileSize = input.files[0].size;
        const maxSizeMB = (maxSize / (1024 * 1024)).toFixed(1);
        
        if (fileSize > maxSize) {
            toastr.error(label + ' file size is ' + (fileSize / (1024 * 1024)).toFixed(2) + 'MB. Maximum allowed is ' + maxSizeMB + 'MB. Please choose a smaller file.', '', {
                timeOut: 6000,
                positionClass: 'toast-top-center'
            });
            input.value = ''; // Clear the input
            return false;
        }
    }
    return true;
}

function removeLogo() {
    if (confirm('Are you sure you want to remove the logo? Website name will be displayed as text instead.')) {
        $('#removeLogo').val('yes');
        toastr.info('Logo will be removed when you save settings.', '', {
            timeOut: 3000,
            positionClass: 'toast-top-center'
        });
    }
}

function removeFavicon() {
    if (confirm('Are you sure you want to remove the favicon? Default favicon will be used instead.')) {
        $('#removeFavicon').val('yes');
        toastr.info('Favicon will be removed when you save settings.', '', {
            timeOut: 3000,
            positionClass: 'toast-top-center'
        });
    }
}

function previewImage(input, prefix){
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('.'+prefix+'-preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
        $('.'+prefix+'-text').text(input.files[0].name);
    }
}

$("form.web-form").submit(function(e){
    e.preventDefault();
    
    var formData = new FormData($(this)[0]);
    var btnObj = $('.web-form .do-save');
    var originalHtml = btnObj.html();
    
    btnObj.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');
    
    $.ajax({
        url: window.location.href,
        type: 'POST',
        data: formData,
        success: function(response) {
            if(response != '1'){
                toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                btnObj.prop('disabled', false).html(originalHtml);
            } else {
                toastr.success('Settings saved successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                setTimeout(function(){ window.location.reload(); }, 1500);
            }
        },
        error: function(err){
            toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            btnObj.prop('disabled', false).html(originalHtml);
        },
        cache: false,
        contentType: false,
        processData: false
    });
});
</script>
