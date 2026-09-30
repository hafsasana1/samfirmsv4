<!-- CMS Page -->
<div class="lg:col-span-9 lg:order-1">
    <div class="space-y-6 move-to-area">
        <?php 
        // Check if this is the About Us page for special layout
        $isAboutPage = (strtolower($pagee->pageTitle) === 'about us' || strtolower($pagee->pageSlug ?? '') === 'about-us');
        
        if ($isAboutPage): 
            // Get site stats from database
            $db = \Config\Database::connect();
            
            // Wrap in try-catch to prevent errors if tables don't exist
            try {
                $totalFirmware = $db->table('fw_posts')->where('postStatus', 'Active')->countAllResults();
                $totalModels = $db->table('fw_posts')->distinct()->where('postStatus', 'Active')->countAllResults('model');
                $totalCountries = $db->table('fw_country')->countAllResults();
            } catch (\Exception $e) {
                // Fallback values if database query fails
                $totalFirmware = 21;
                $totalModels = 17;
                $totalCountries = 4;
            }
        ?>
        
        <!-- About Us Page - Matching Site Design -->
        
        <!-- Stats Header (Like Homepage Live Monitor) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-accent-soft to-white border-b border-gray-100">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="page-content">
                        <h1>About SamFirms</h1>
                        <p class="text-sm text-ink-muted mt-3">Free Samsung Firmware Downloads for Everyone</p>
                    </div>
                    <div class="flex items-center space-x-6 text-sm">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-accent"><?= $totalFirmware ?>+</div>
                            <div class="text-xs text-ink-muted">Firmware Files</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-accent"><?= $totalModels ?>+</div>
                            <div class="text-xs text-ink-muted">Models</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-accent"><?= $totalCountries ?>+</div>
                            <div class="text-xs text-ink-muted">Countries</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="p-6 lg:p-10">
                <div class="prose prose-lg max-w-none">
                    <!-- Intro -->
                    <div class="mb-8 text-center">
                        <p class="text-xl text-ink-muted leading-relaxed">
                            SamFirms is your premier destination for <span class="font-semibold text-accent">official Samsung firmware downloads</span>. We provide authentic, verified stock ROMs directly sourced from Samsung servers—completely free, with no registration required and no download speed restrictions.
                        </p>
                    </div>
                    
                    <!-- Mission -->
                    <div class="bg-gradient-to-br from-accent-soft to-white rounded-2xl p-6 lg:p-8 mb-8 border-l-4 border-accent">
                        <h2 class="text-2xl font-bold text-ink mb-4 flex items-center">
                            <svg class="w-7 h-7 text-accent mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Our Mission
                        </h2>
                        <p class="text-lg text-ink-muted mb-0">
                            To democratize access to official Samsung firmware by providing a reliable, high-performance platform that eliminates the barriers of cost, speed restrictions, and complex registration processes. We believe every Samsung device owner deserves free, immediate access to the latest firmware files to maintain, upgrade, or restore their Galaxy devices.
                        </p>
                    </div>
                    
                    <!-- What We Offer -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-ink mb-6 flex items-center">
                            <svg class="w-7 h-7 text-accent mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                            Why Choose SamFirms
                        </h2>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-xl hover:bg-accent-soft transition-colors">
                                <svg class="w-6 h-6 text-accent flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-ink mb-1">Zero-Cost Access</h3>
                                    <p class="text-sm text-ink-muted mb-0">Completely free firmware downloads with no premium tiers, subscription fees, or hidden charges. Full access to our entire firmware library at no cost.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-xl hover:bg-accent-soft transition-colors">
                                <svg class="w-6 h-6 text-accent flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-ink mb-1">Enterprise-Grade Infrastructure</h3>
                                    <p class="text-sm text-ink-muted mb-0">High-performance servers delivering maximum download speeds without throttling or bandwidth caps. Download multi-gigabyte files in minutes.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-xl hover:bg-accent-soft transition-colors">
                                <svg class="w-6 h-6 text-accent flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-ink mb-1">Instant Access, No Barriers</h3>
                                    <p class="text-sm text-ink-muted mb-0">Download immediately without account creation, email verification, or personal information. Privacy-first approach with no user tracking or data collection.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-xl hover:bg-accent-soft transition-colors">
                                <svg class="w-6 h-6 text-accent flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-ink mb-1">Verified Authenticity</h3>
                                    <p class="text-sm text-ink-muted mb-0">All firmware files are original, unmodified stock ROMs sourced directly from Samsung's official servers. Cryptographically verified for authenticity and safety.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-xl hover:bg-accent-soft transition-colors">
                                <svg class="w-6 h-6 text-accent flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-ink mb-1">Automated Updates</h3>
                                    <p class="text-sm text-ink-muted mb-0">Our systems monitor Samsung's release channels 24/7, automatically adding new firmware files within hours of official release. Always current, always complete.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-xl hover:bg-accent-soft transition-colors">
                                <svg class="w-6 h-6 text-accent flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-ink mb-1">Comprehensive Toolkit</h3>
                                    <p class="text-sm text-ink-muted mb-0">Professional flashing tools including Odin, SamFirm Tool, FRP bypass utilities, and detailed firmware installation guides—all free and regularly updated.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Our Story -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-ink mb-4 flex items-center">
                            <svg class="w-7 h-7 text-accent mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Our Story
                        </h2>
                        <p class="text-ink-muted">
                            SamFirms was founded by a team of experienced developers and Samsung device enthusiasts who recognized a critical gap in the firmware distribution ecosystem. Users worldwide were struggling with unreliable download sources, artificially limited speeds, mandatory account registrations, and questionable file authenticity.
                        </p>
                        <p class="text-ink-muted">
                            We built SamFirms as the solution: a professional-grade platform engineered for reliability, performance, and accessibility. Today, our infrastructure serves <?= number_format($totalFirmware) ?>+ verified firmware files across <?= $totalModels ?>+ Samsung device models, supporting users in <?= $totalCountries ?>+ countries with millions of successful downloads.
                        </p>
                        <p class="text-ink-muted mb-0">
                            Our platform is built on enterprise-grade infrastructure with automated monitoring, integrity verification, and high-availability architecture to ensure 99.9% uptime and maximum download performance.
                        </p>
                    </div>
                    
                    <!-- Commitment -->
                    <div class="bg-gray-50 rounded-2xl p-6 lg:p-8 border border-gray-200">
                        <h2 class="text-2xl font-bold text-ink mb-4 flex items-center">
                            <svg class="w-7 h-7 text-accent mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Our Commitment
                        </h2>
                        <p class="text-ink-muted mb-4">
                            SamFirms will remain free, fast, and accessible forever. We are committed to continuous platform improvement, expanding our firmware library, enhancing server infrastructure, and developing advanced tools to streamline the firmware flashing process for both novice and expert users.
                        </p>
                        <p class="text-ink-muted mb-0">
                            Whether you're a professional technician servicing hundreds of devices or a home user performing your first firmware update, our platform provides the reliability and performance you need. For technical support, firmware requests, or general inquiries, please <a href="<?= base_url('contact-us') ?>" class="text-accent hover:text-accent-hover font-medium">contact us</a>.
                        </p>
                    </div>
                    
                    <!-- Team Signature -->
                    <div class="text-center mt-10 pt-8 border-t border-gray-200">
                        <p class="text-lg text-ink-muted mb-2">Sincerely,</p>
                        <p class="text-2xl font-bold text-accent">SamFirms Team</p>
                    </div>
                </div>
            </div>
        </div>

        <?php else: ?>
        
        <!-- Regular CMS Page Layout -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 page-content">
                <h1><?= $pagee->pageTitle ?></h1>
            </div>

            <div class="p-6 lg:p-8">
                <div class="prose prose-lg max-w-none page-content">
                    <?php
                    $pageContent = $pagee->pageContent; 
                    $ext_countries = extractCountry($pageContent);
                    if(count($ext_countries[1]) > 0){
                        foreach($ext_countries[1] as $cnt){
                            $countryName = ucfirst($cnt);
                            $pageContent = str_replace('{'.strtolower($cnt).'}', '<img class="inline-block rounded shadow-sm" loading="lazy" alt="'.$countryName.' flag" src="'.base_url('assets/img/flags/4x3/'.($cnt).'.svg').'" width="24" height="18">', $pageContent);
                        }
                    }
                    echo $pageContent;
                    ?>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
    </div>
</div>
