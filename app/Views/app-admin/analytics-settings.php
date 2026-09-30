<div class="p-4 lg:p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Analytics Setting</h1>
        <p class="text-sm text-gray-500 mt-1">Configure tracking and verification scripts for your website</p>
    </div>

    <!-- Analytics Form Card -->
    <div class="bg-white rounded-lg shadow-card">
        <div class="p-6">
            <form method="post" class="ana-form">
                <?= csrf_field() ?>
                
                <!-- Analytics Scripts Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Analytics & Verification Scripts
                    </label>
                    <textarea 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors font-mono text-sm resize-y" 
                        name="analytics" 
                        rows="12"
                        placeholder="<!-- Google Analytics, Google Tag Manager, Facebook Pixel, etc. -->"><?= $record['analytics'] ?></textarea>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle text-accent mr-1"></i>
                        Place all your analytical and verification scripts here (Google Analytics, Tag Manager, Facebook Pixel, search console verification codes, etc.)
                    </p>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end pt-6 border-t border-gray-200 mt-6">
                    <button 
                        class="px-8 py-3 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                        type="submit">
                        <span class="btn-text">Save Changes</span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Info Card -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-lightbulb text-blue-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-900 mb-1">Usage Tips</h3>
                <ul class="text-xs text-blue-800 space-y-1">
                    <li>• Scripts will be automatically inserted into the &lt;head&gt; section of your website</li>
                    <li>• Make sure to include complete script tags (&lt;script&gt;...&lt;/script&gt;)</li>
                    <li>• Test tracking implementation after saving changes</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
// Analytics Form Submission
$("form.ana-form").submit(function(e){
    e.preventDefault();
    
    var btnObj = $('.ana-form button[type="submit"]');
    var btnText = btnObj.find('.btn-text');
    
    // Disable button and show loading state
    btnObj.prop('disabled', true);
    btnText.html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');

    $.ajax({
        url: window.location.href,
        type: 'POST',
        data: $('.ana-form').serialize(),
        success: function(response) {
            if(response != '1'){
                toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
            } else {
                toastr.success('Analytics settings saved successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
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