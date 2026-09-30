<div class="p-4 lg:p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Ads Setting</h1>
        <p class="text-sm text-gray-500 mt-1">Configure advertisement placements across your website</p>
    </div>

    <!-- Ads Form Card -->
    <div class="bg-white rounded-lg shadow-card">
        <div class="p-6">
            <form method="post" class="ads-form space-y-6">
                <?= csrf_field() ?>
                
                <!-- Header Ads Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Header Ads
                    </label>
                    <textarea 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors resize-y" 
                        name="header_ads" 
                        rows="5"
                        placeholder="Paste your header ad code here..."><?= $ads['header_ads'] ?></textarea>
                </div>

                <!-- Sidebar Ads Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Sidebar Ads
                    </label>
                    <textarea 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors resize-y" 
                        name="sidebar_ads" 
                        rows="5"
                        placeholder="Paste your sidebar ad code here..."><?= $ads['sidebar_ads'] ?></textarea>
                </div>

                <!-- Footer Ads Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Footer Ads
                    </label>
                    <textarea 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors resize-y" 
                        name="footer_ads" 
                        rows="5"
                        placeholder="Paste your footer ad code here..."><?= $ads['footer_ads'] ?></textarea>
                </div>

                <!-- Post Ads Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Post Ads
                    </label>
                    <textarea 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors resize-y" 
                        name="post_ads" 
                        rows="5"
                        placeholder="Paste your post ad code here..."><?= $ads['post_ads'] ?></textarea>
                    <p class="text-xs text-gray-500 mt-1">Will be shown above the download button</p>
                </div>

                <!-- Latest Model Ads Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Latest Model Ads
                    </label>
                    <textarea 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors resize-y" 
                        name="latest_model_ads" 
                        rows="5"
                        placeholder="Paste your latest model ad code here..."><?= $ads['latest_model_ads'] ?></textarea>
                    <p class="text-xs text-gray-500 mt-1">Will be shown below the latest model update panel on home page</p>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button 
                        class="px-8 py-3 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                        type="submit">
                        <span class="btn-text">Save Changes</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
// Ads Form Submission
$("form.ads-form").submit(function(e){
    e.preventDefault();
    
    var btnObj = $('.ads-form button[type="submit"]');
    var btnText = btnObj.find('.btn-text');
    
    // Disable button and show loading state
    btnObj.prop('disabled', true);
    btnText.html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');

    $.ajax({
        url: window.location.href,
        type: 'POST',
        data: $('.ads-form').serialize(),
        success: function(response) {
            if(response != '1'){
                toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
            } else {
                toastr.success('Ads settings saved successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            }
            btnObj.prop('disabled', false);
            btnText.html('Save Changes');
        },
        error: function(err){
            toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            btnObj.prop('disabled', false);
            btnText.html('Save Changes');
        }
    });
});
</script>