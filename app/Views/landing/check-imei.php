<?php
$requestKey = md5('samfirms::' . date('Ymd'));
?>

<!-- Hide Sidebar for Full Width -->
<style>
body.imei-page .lg\:col-span-3 { display: none !important; }
body.imei-page .lg\:col-span-9 { grid-column: span 12 / span 12 !important; }
</style>
<script>document.body.classList.add('imei-page');</script>

<!-- Main Content - Compact Minimal Design with Alpine.js -->
<div class="lg:col-span-9 lg:order-1" x-data="imeiChecker()">
    
    <!-- IMEI Check Section -->
    <section class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 bg-accent-soft border-b border-gray-200">
                <h1 class="text-xl font-bold text-navy-900">IMEI Device Checker</h1>
                <p class="text-sm text-gray-600 mt-1">Get instant firmware and device information</p>
            </div>
            
            <div class="p-6 lg:p-8">
                <!-- Search Form -->
                <div class="max-w-2xl mx-auto mb-8">
                    <div class="flex flex-col sm:flex-row gap-3 mb-4">
                        <!-- Input -->
                        <div class="relative flex-1">
                            <input type="text" 
                                   x-model="imei"
                                   @input="formatIMEI()"
                                   @keydown.enter="checkIMEI()"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-accent focus:ring-2 focus:ring-accent-soft outline-none transition-all font-mono"
                                   placeholder="Enter IMEI (15 digits)"
                                   maxlength="19"
                                   autocomplete="off">
                        </div>
                        
                        <!-- Search Button -->
                        <button type="button"
                                @click="checkIMEI()"
                                :disabled="loading"
                                class="px-6 py-3 bg-accent text-white font-semibold rounded-lg hover:bg-accent-hover transition-all disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                            <span x-show="!loading">Search</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Checking...
                            </span>
                        </button>
                    </div>
                    
                    <!-- Helper Text -->
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span x-text="validationMessage"></span>
                        <button type="button" @click="fillSample()" class="text-accent hover:text-accent-hover font-medium">
                            Try Sample
                        </button>
                    </div>
                </div>
                
                <!-- Results Area -->
                <div x-show="showResults" x-transition class="border-t border-gray-200 pt-6">
                    <!-- Loading State -->
                    <div x-show="loading" class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-accent-soft rounded-full mb-4">
                            <svg class="w-8 h-8 text-accent animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600">Analyzing device information...</p>
                    </div>
                    
                    <!-- Error State -->
                    <div x-show="error && !loading" class="text-center py-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-50 rounded-full mb-4">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Error</h3>
                        <p class="text-red-600 mb-4" x-text="errorMessage"></p>
                        <button @click="reset()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                            Try Again
                        </button>
                    </div>
                    
                    <!-- Success State -->
                    <div x-show="success && !loading">
                        <div class="bg-accent-soft rounded-lg p-6 mb-4">
                            <div class="prose max-w-none" x-html="resultHtml"></div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3">
                            <button @click="window.print()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                                Print
                            </button>
                            <button @click="copyResults()" class="px-4 py-2 text-white rounded-lg transition-colors"
                                    :class="copied ? 'bg-green-600' : 'bg-green-500 hover:bg-green-600'">
                                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                            </button>
                            <button @click="reset()" class="px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-lg transition-colors">
                                Check Another
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CMS Content -->
    <?php if (!empty($pagee->pageContent)): ?>
    <section class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-navy-900">IMEI Check Information</h2>
            </div>
            <div class="p-6 lg:p-8">
                <div class="prose max-w-none">
                    <?php
                    $pageContent = $pagee->pageContent;
                    $ext_countries = extractCountry($pageContent);
                    if (count($ext_countries[1]) > 0) {
                        foreach ($ext_countries[1] as $cnt) {
                            $countryName = ucfirst($cnt);
                            $pageContent = str_replace('{' . strtolower($cnt) . '}', '<img class="inline-block w-6 h-4 rounded shadow-sm" loading="lazy" alt="' . $countryName . ' flag" src="' . base_url('assets/img/flags/4x3/' . $cnt . '.svg') . '">', $pageContent);
                        }
                    }
                    echo $pageContent;
                    ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<!-- Alpine.js Component -->
<script>
function imeiChecker() {
    return {
        imei: '',
        loading: false,
        showResults: false,
        error: false,
        success: false,
        errorMessage: '',
        resultHtml: '',
        copied: false,
        
        get validationMessage() {
            const clean = this.imei.replace(/\s/g, '');
            if (clean.length === 0) return 'Enter at least 15 digits';
            if (clean.length < 15) return `Need ${15 - clean.length} more digits`;
            return 'Valid format - Ready to check';
        },
        
        formatIMEI() {
            let value = this.imei.replace(/\s/g, '').replace(/[^0-9]/g, '');
            if (value.length > 0) {
                this.imei = value.match(/.{1,4}/g)?.join(' ') || value;
            }
        },
        
        fillSample() {
            this.imei = '3524 4951 2229 538';
        },
        
        async checkIMEI() {
            const cleanIMEI = this.imei.replace(/\s/g, '');
            
            if (cleanIMEI.length < 14) {
                this.errorMessage = 'IMEI must be at least 14 digits';
                this.error = true;
                this.showResults = true;
                return;
            }
            
            this.loading = true;
            this.showResults = true;
            this.error = false;
            this.success = false;
            
            const formData = new FormData();
            formData.append('deviceImei', cleanIMEI);
            formData.append('requestKey', '<?= $requestKey ?>');
            
            try {
                const response = await fetch('<?= base_url("imei") ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                const data = await response.json();
                this.loading = false;
                
                if (data.status === 'success') {
                    this.success = true;
                    this.resultHtml = data.response;
                    setTimeout(() => {
                        this.$el.querySelector('[x-show="success"]')?.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'start' 
                        });
                    }, 100);
                } else {
                    this.error = true;
                    this.errorMessage = data.response || 'Unable to process your request';
                }
            } catch (err) {
                this.loading = false;
                this.error = true;
                this.errorMessage = 'Network error occurred. Please try again.';
            }
        },
        
        copyResults() {
            const content = this.$el.querySelector('.prose')?.innerText || '';
            navigator.clipboard.writeText(content).then(() => {
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 2000);
            });
        },
        
        reset() {
            this.imei = '';
            this.showResults = false;
            this.error = false;
            this.success = false;
            this.resultHtml = '';
        }
    }
}
</script>
