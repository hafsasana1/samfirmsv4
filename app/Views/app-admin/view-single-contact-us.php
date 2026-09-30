<div class="p-4 lg:p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Contact Request Details</h1>
            <p class="text-sm text-gray-500 mt-1">View complete contact form submission</p>
        </div>
        <a href="<?= base_url(ADMIN_PATH.'/contact-us') ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <!-- Contact Details Card -->
    <div class="bg-white rounded-lg shadow-card">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Name -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Name</label>
                    <p class="mt-1 text-sm font-medium text-gray-800"><?= $cus->fromName ?></p>
                </div>

                <!-- Email -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</label>
                    <p class="mt-1">
                        <a href="mailto:<?= $cus->fromEmail.'?subject=Re: '.substr(strip_tags($cus->description), 0, 100) ?>" class="text-sm font-medium text-accent hover:text-accent-dark">
                            <i class="fas fa-envelope mr-1"></i><?= $cus->fromEmail ?>
                        </a>
                    </p>
                </div>

                <!-- Phone Number -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Phone Number</label>
                    <p class="mt-1 text-sm font-medium text-gray-800"><?= $cus->phoneNumber ?></p>
                </div>

                <!-- Website -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Website</label>
                    <p class="mt-1">
                        <a href="<?= $cus->website ?>" target="_blank" class="text-sm font-medium text-accent hover:text-accent-dark break-all">
                            <i class="fas fa-external-link-alt mr-1"></i><?= $cus->website ?>
                        </a>
                    </p>
                </div>

                <!-- IP Address -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">IP Address</label>
                    <p class="mt-1 text-sm font-mono text-gray-800"><?= $cus->ipAddress ?></p>
                </div>

                <!-- Created Time -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Submitted At</label>
                    <p class="mt-1 text-sm text-gray-800">
                        <i class="fas fa-clock text-gray-400 mr-1"></i><?= $cus->createdTime ?>
                    </p>
                </div>

            </div>

            <!-- Description - Full Width -->
            <div class="mt-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide block mb-2">Message</label>
                <div class="prose prose-sm max-w-none text-gray-800">
                    <?= $cus->description ?>
                </div>
            </div>

        </div>
    </div>
</div>
