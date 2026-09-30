<?php
$downloadButton = @json_decode($post->downloadButton, true);
$goToPostLink   = postUrl($post);

$downloadUrl = '';
if ($post->downloadButton != '' && count($downloadButton) > 0 && $downloadButton['buttonUrl'] != '') {
    $downloadUrl = $downloadButton['buttonUrl'];
} elseif ($post->externalFileLink != '' && $post->externalFileLink != null) {
    $downloadUrl = $post->externalFileLink;
}
?>
<!-- Download Page -->
<div class="lg:col-span-9 lg:order-1">
    <div class="space-y-4 move-to-area">
        <!-- Compact Timer Card -->
        <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
            <div class="p-6 text-center">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-2 main-head">Preparing Your Download</h2>
                    <p class="text-sm text-gray-500">Please wait while we generate your download link</p>
                </div>

                <!-- Circular Timer -->
                <div id="timer-container" class="flex flex-col items-center justify-center py-6">
                    <div class="relative w-32 h-32">
                        <!-- Background Circle -->
                        <svg class="transform -rotate-90 w-32 h-32">
                            <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-200" />
                            <circle id="timer-circle" cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="none" 
                                    class="text-accent transition-all duration-1000 ease-linear"
                                    stroke-dasharray="351.858" 
                                    stroke-dashoffset="0"
                                    stroke-linecap="round" />
                        </svg>
                        <!-- Timer Number -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span id="timer-display" class="text-4xl font-bold text-accent">10</span>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-gray-600">seconds remaining</p>
                </div>

                <!-- Success Message (hidden initially) -->
                <div id="success-message" class="hidden py-6">
                    <div class="inline-flex items-center gap-2 px-6 py-3 bg-green-50 text-green-600 rounded-xl border border-green-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="font-bold">Link Ready! Scroll down to download</span>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($post->separateAds == 'Yes' && $post->adsContent != '') { ?>
        <!-- Ads Section -->
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
            <?= $post->adsContent ?>
        </div>
        <?php } ?>

        <!-- Download Instructions -->
        <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
            <div class="p-6">
                <div class="prose max-w-none f-desc">
                    <?php
                    $pageContent   = $downrec->pageContent ?? '';
                    $ext_countries = extractCountry($pageContent);
                    if (count($ext_countries[1]) > 0) {
                        foreach ($ext_countries[1] as $cnt) {
                            $countryName = ucfirst($cnt);
                            $pageContent = str_replace('{'.strtolower($cnt).'}', '<img class="inline-block rounded shadow-sm" loading="lazy" alt="'.$countryName.' flag" src="'.base_url('assets/img/flags/4x3/'.($cnt).'.svg').'" width="24" height="18">', $pageContent);
                        }
                    }
                    echo $pageContent;
                    ?>
                </div>

                <?php if (!empty($post_ads)) { ?>
                <div class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <?= $post_ads ?>
                </div>
                <?php } ?>

                <!-- Download Button at End of Content (hidden initially) -->
                <?php if ($downloadUrl != '') { ?>
                <div id="download-button-area" class="hidden mt-6 text-center p-6 bg-gradient-to-r from-accent/5 to-accent/10 rounded-xl border-2 border-accent/20">
                    <a href="<?= $downloadUrl ?>" 
                       class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-accent hover:bg-accent-hover text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-300" 
                       onclick="proceedDownload()" 
                       target="_blank">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 11l3 3m0 0l3-3m-3 3V8"></path>
                        </svg>
                        <span>Download Now</span>
                    </a>
                    <p class="mt-3 text-sm text-gray-600">Click the button above to start your download</p>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
// 10-second countdown timer
let timeRemaining = 10;
const timerDisplay = document.getElementById('timer-display');
const timerCircle = document.getElementById('timer-circle');
const timerContainer = document.getElementById('timer-container');
const successMessage = document.getElementById('success-message');
const downloadButtonArea = document.getElementById('download-button-area');
const mainHead = document.querySelector('.main-head');
const circumference = 2 * Math.PI * 56; // 2πr where r=56

// Set initial state
timerCircle.style.strokeDashoffset = circumference;

// Start countdown after 1 second
setTimeout(function() {
    const timer = setInterval(function() {
        if (timeRemaining <= 0) {
            clearInterval(timer);
            
            // Update heading
            if (mainHead) {
                mainHead.innerHTML = '<span class="text-green-600 font-bold">Download Ready!</span>';
            }
            
            // Hide timer, show success message
            if (timerContainer) {
                timerContainer.classList.add('hidden');
            }
            
            if (successMessage) {
                successMessage.classList.remove('hidden');
            }
            
            // Show download button at end of content
            if (downloadButtonArea) {
                downloadButtonArea.classList.remove('hidden');
                
                // Smooth scroll to download button
                setTimeout(function() {
                    downloadButtonArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        } else {
            timeRemaining--;
            timerDisplay.textContent = timeRemaining;
            
            // Update circle progress
            const progress = timeRemaining / 10;
            const offset = circumference * (1 - progress);
            timerCircle.style.strokeDashoffset = offset;
        }
    }, 1000);
}, 1000);

function proceedDownload() {
    setTimeout(function() {
        window.location.href = '<?= $goToPostLink ?>';
    }, 1000);
}
</script>
