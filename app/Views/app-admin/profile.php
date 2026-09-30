<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Profile Settings</h1>
    <p class="text-sm text-gray-600 mt-1">Manage your account information and security</p>
</div>

<!-- Profile Card -->
<div class="w-full max-w-4xl">
    <div class="bg-white rounded-lg shadow-card overflow-hidden">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-accent-soft rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-circle text-2xl text-accent"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Account Information</h2>
                        <p class="text-sm text-gray-600">Update your personal details and password</p>
                    </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
                <form method="post" class="profile-form space-y-6">
                <?= csrf_field() ?>
                    <input type="hidden" name="save" value="yes">

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-600">*</span>
                        </label>
                        <input type="text" 
                               id="name"
                               name="name" 
                               value="<?= $uuser->name ?>"
                               required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                               placeholder="Enter your full name">
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phoneNumber" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   id="phoneNumber"
                                   name="phoneNumber" 
                                   value="<?= $uuser->phoneNumber ?>"
                                   required
                                   onkeypress="return isNumber(event)"
                                   class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                                   placeholder="Enter your phone number">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Minimum 10 digits required</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" 
                                   id="email"
                                   name="email" 
                                   value="<?= $uuser->email ?>"
                                   class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                                   placeholder="your.email@example.com"
                                   data-inputmask="'alias': 'email'">
                        </div>
                    </div>

                    <!-- Username (Readonly) -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Username <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   id="username"
                                   name="username" 
                                   value="<?= $uuser->username ?>"
                                   maxlength="10"
                                   readonly
                                   class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 bg-gray-50 text-gray-500 cursor-not-allowed">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-lock text-xs mr-1"></i>Username cannot be changed
                        </p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            New Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                            <input type="password" 
                                   id="password"
                                   name="password"
                                   class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-20 transition-colors"
                                   placeholder="Leave blank to keep current password">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Minimum 6 characters required</p>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 pt-6">
                        <!-- Save Button -->
                        <div class="flex items-center justify-end gap-3">
                            <button type="button" 
                                    onclick="window.location.reload()"
                                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                                <i class="fas fa-undo mr-2"></i>Reset
                            </button>
                            <button type="submit" 
                                    class="do-save px-6 py-3 bg-accent hover:bg-accent-hover text-white font-semibold rounded-lg transition-colors shadow-sm hover:shadow-md flex items-center gap-2">
                                <i class="fas fa-save"></i>
                                <span>Save Changes</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Info Card -->
        <div class="mt-6 bg-gradient-to-r from-cyan-50 to-accent-soft border border-cyan-200 rounded-lg p-6">
            <div class="flex gap-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-xl text-cyan-600"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-1">Security Tips</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><i class="fas fa-check text-accent mr-2"></i>Use a strong password with at least 6 characters</li>
                        <li><i class="fas fa-check text-accent mr-2"></i>Never share your login credentials with anyone</li>
                        <li><i class="fas fa-check text-accent mr-2"></i>Update your password regularly for better security</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    if (typeof $.fn.inputmask !== 'undefined') {
        $('[data-inputmask]').inputmask();
    }
});

// Profile Form Submit
$("form.profile-form").submit(function(e){
    e.preventDefault();
    
    var btnObj = $('.profile-form .do-save');
    var originalHtml = btnObj.html();
    
    btnObj.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');

    $.ajax({
        url: window.location.href,
        type: 'POST',
        data: $('.profile-form').serialize(),
        success: function(response) {
            if(response != '1'){
                toastr.error(response, '', {timeOut: 5000, positionClass: 'toast-top-center'});
                btnObj.prop('disabled', false).html(originalHtml);
            } else {
                toastr.success('Profile updated successfully!', '', {timeOut: 3000, positionClass: 'toast-top-center'});
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            }
        },
        error: function(err){
            toastr.error('Unable to process your request', '', {timeOut: 5000, positionClass: 'toast-top-center'});
            btnObj.prop('disabled', false).html(originalHtml);
        }
    });
});

// Allow only numbers for phone input
function isNumber(e){
    var numberArr = [8,48,49,50,51,52,53,54,55,56,57];
    if($.inArray(e.which, numberArr) != '-1'){
        return true;
    } else {
        return false;
    }
}
</script>
