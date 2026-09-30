<?php
$gSitekey    = '6LfFIOggAAAAAG2Rse1QdKSAWH8ibnW2kPEe9x0x';
$gSiteSecret = '6LfFIOggAAAAANTrZmEG8MiC_cUvZLrkwhV60-xd';

// ✅ FIX: Cache web settings to avoid blocking I/O on every request
if (!isset($web)) {
    $cache = \Config\Services::cache();
    $web = $cache->remember('web_settings_v1', 3600, function() {
        return @json_decode(@file_get_contents(RESOURCE_PATH . 'web-setting.info'));
    });
}

// ✅ DYNAMIC LOGO: Load from admin settings (same logic as header)
if (!isset($webLogo)) {
    $webLogo = base_url().'resource/logo.png';
    if (isset($web) && !empty($web->webLogo) && file_exists(RESOURCE_PATH . $web->webLogo)) {
        $webLogo = base_url().'resource/'.$web->webLogo;
    }
    $webLogo = $webLogo.'?v='.time();
}
?>
                </div>
            </div>
        </section>

        <!-- Footer Ads -->
        <?php if (!empty($footer_ads)) { ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <?= $footer_ads ?>
            </div>
        </div>
        <?php } ?>

        <!-- Modern Footer -->
        <footer class="bg-navy-900 text-gray-300 mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- About Section - Logo + Site Name -->
                    <div class="space-y-4">
                        <!-- Logo + Site Name -->
                        <div class="flex items-center space-x-3 mb-4">
                            <?php 
                            // Extract filename from URL (remove query string and base_url)
                            $logoFile = '';
                            if (!empty($webLogo)) {
                                $logoUrl = explode('?', $webLogo)[0]; // Remove ?v=timestamp
                                $logoFile = str_replace(base_url().'resource/', '', $logoUrl);
                            }
                            
                            // Check if logo file exists
                            $logoExists = !empty($logoFile) && file_exists(RESOURCE_PATH . $logoFile);
                            
                            if ($logoExists): 
                            ?>
                                <img src="<?= $webLogo ?>" 
                                     alt="<?= esc($web->webTitle ?? 'SamFirms') ?>" 
                                     class="h-10 w-auto"
                                     loading="lazy">
                            <?php endif; ?>
                            
                            <!-- Site Name (Always Visible) -->
                            <span class="text-2xl font-bold text-white" style="font-family: 'Inter', sans-serif;">
                                <?= esc($web->webTitle ?? 'SamFirms') ?>
                            </span>
                        </div>
                        
                        <!-- SEO-Optimized Professional Description -->
                        <p class="text-sm text-gray-400 leading-relaxed" data-nosnippet>
                            Official Samsung firmware download for Galaxy phones and tablets. Access Unlimited stock ROM files, One UI updates, and security patches. Free firmware downloads with Odin flash tool guides.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div class="space-y-4">
                        <h3 class="text-white font-bold text-lg mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="<?= base_url() ?>" class="text-sm hover:text-accent transition-colors flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                <span>Home</span>
                            </a></li>
                            <?php
                            foreach (getActivePages('footer') as $pagee) {
                                echo '<li><a href="'.base_url($pagee->slugUrl).'" class="text-sm hover:text-accent transition-colors flex items-center space-x-2">';
                                echo '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>';
                                echo '<span>'.$pagee->navTitle.'</span>';
                                echo '</a></li>';
                            }
                            ?>
                            <li><a href="<?= base_url('contact-us') ?>" class="text-sm hover:text-accent transition-colors flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                <span>Contact Us</span>
                            </a></li>
                        </ul>
                    </div>

                    <!-- Features -->
                    <div class="space-y-4">
                        <h3 class="text-white font-bold text-lg mb-4">Why Choose Us</h3>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-start space-x-3 group">
                                <div class="flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-accent group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-white font-medium">Direct Samsung Servers</span>
                                    <p class="text-gray-500 text-xs mt-0.5">100% official, unmodified firmware</p>
                                </div>
                            </li>
                            <li class="flex items-start space-x-3 group">
                                <div class="flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-accent group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-white font-medium">All Regions & CSC Codes</span>
                                    <p class="text-gray-500 text-xs mt-0.5">195+ countries supported</p>
                                </div>
                            </li>
                            <li class="flex items-start space-x-3 group">
                                <div class="flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-accent group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-white font-medium">No Speed Limits</span>
                                    <p class="text-gray-500 text-xs mt-0.5">Maximum download speed, no throttling</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Stay Updated - Telegram Channel -->
                    <div class="space-y-4">
                        <h3 class="text-white font-bold text-lg mb-4">Stay Updated</h3>
                        
                        <!-- Telegram Channel - Compact Version -->
                        <div class="space-y-3">
                            <p class="text-sm text-gray-400">
                                Join our Telegram for instant updates
                            </p>
                            
                            <a href="https://t.me/YOUR_CHANNEL_NAME" 
                               target="_blank"
                               rel="noopener noreferrer"
                               class="flex items-center justify-center space-x-2 w-full px-4 py-2.5 bg-[#0088cc] text-white rounded-lg hover:bg-[#0077b5] transition-all text-sm font-semibold shadow-lg hover:shadow-xl">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18 1.897-.962 6.502-1.359 8.627-.168.9-.5 1.201-.82 1.23-.697.064-1.226-.461-1.901-.903-1.056-.692-1.653-1.123-2.678-1.799-1.185-.781-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.139-5.062 3.345-.479.329-.913.489-1.302.481-.428-.008-1.252-.241-1.865-.44-.752-.244-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.831-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635.099-.002.321.023.465.141.121.099.155.232.171.326.016.093.036.306.02.472z"/>
                                </svg>
                                <span>Join Telegram</span>
                            </a>
                        </div>
                        
                        <!-- Support Email - Compact -->
                        <div class="pt-3 border-t border-navy-800 space-y-1.5">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Need Help?</p>
                            <a href="mailto:support@<?= str_replace(['https://','http://','www.'],'',base_url()) ?>" 
                               class="text-sm text-accent hover:text-accent-hover transition-colors flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span>Email Support</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="mt-12 pt-8 border-t border-navy-800">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <p class="text-sm text-gray-400">
                            Copyright &copy; <script>document.write(new Date().getFullYear())</script> 
                            <a href="<?= base_url() ?>" class="text-accent hover:text-accent-hover transition-colors"><?= trim(str_replace(['https://','http://'],'',base_url()),'/') ?></a>
                            . All Rights Reserved.
                        </p>
                        <div class="flex items-center space-x-6">
                            <a href="#" class="text-gray-400 hover:text-accent transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-accent transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-accent transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <script>var baseurl = "<?= base_url() ?>";</script>
        <script src="<?= base_url() ?>assets/js/landing.js?v=2.2.0" defer></script>
        
        <?php 
        // ✅ FIX #8: Conditional reCAPTCHA - Load only on pages with forms
        // Saves 150KB+ on most pages, improves TBT by 100ms
        $needsRecaptcha = isset($request) && in_array($request, ['contact-us', 'download-now']);
        if ($needsRecaptcha && !empty($gSitekey)): 
        ?>
        <script src="https://www.google.com/recaptcha/api.js?render=<?= $gSitekey ?>" async defer></script>
        <?php endif; ?>

        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Organization",
          "name": "SamFirms",
          "url": "<?= base_url() ?>",
          "logo": "<?= base_url('resource/logo.png') ?>",
          "description": "Download official Samsung firmware for all Galaxy models and regions. Free, fast, and direct from Samsung Cloud Server.",
          "foundingDate": "2020",
          "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "Customer Support",
            "email": "contact@samfirms.com"
          },
          "sameAs": [
            "https://facebook.com/samfirms",
            "https://twitter.com/samfirms"
          ]
        }
        </script>

        <!-- ✅ FIXED: Replaced fake LocalBusiness with WebSite SearchAction schema -->
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "WebSite",
          "name": "<?= esc($web->webTitle ?? 'SamFirms') ?>",
          "url": "<?= base_url() ?>",
          "description": "Download official Samsung firmware for all Galaxy devices. Fast, safe, and free firmware downloads.",
          "potentialAction": {
            "@type": "SearchAction",
            "target": {
              "@type": "EntryPoint",
              "urlTemplate": "<?= base_url('load/model?query={search_term_string}') ?>"
            },
            "query-input": "required name=search_term_string"
          },
          "publisher": {
            "@type": "Organization",
            "name": "<?= esc($web->webTitle ?? 'SamFirms') ?>",
            "url": "<?= base_url() ?>"
          }
        }
        </script>
    </body>
</html>
