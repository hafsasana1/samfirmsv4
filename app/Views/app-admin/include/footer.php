<?php
$web = @json_decode(@file_get_contents(RESOURCE_PATH . 'web-setting.info'));
?>
        </div><!-- End max-w wrapper -->
    </div><!-- End padding wrapper -->
</div><!-- End main content wrapper -->
        
<!-- Footer -->
<footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 py-4 transition-all duration-300"
        :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                &copy; <?= date('Y') ?> 
                <a href="<?= base_url() ?>dashboard" class="text-accent hover:text-accent-hover font-medium">
                    <?= $web->webTitle ?? '' ?>
                </a>
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-500">
                Powered by <span class="font-semibold text-accent">Tailwind CSS</span>
            </p>
        </div>
    </div>
</footer>
        
        <!-- Loading Overlay -->
        <div class="top-loading hidden fixed top-0 left-0 right-0 h-1 z-50">
            <div class="h-full bg-accent animate-pulse"></div>
        </div>
        
        <!-- No Connection Alert -->
        <div class="no-connection hidden fixed top-20 left-1/2 transform -translate-x-1/2 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <span class="font-medium">No Internet Connection</span>
        </div>
        
        <!-- Scripts -->
        <script src="<?= base_url() ?>assets/js/vendor.min.js"></script>
        <script src="<?= base_url() ?>assets/js/compose.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.7.3/ckeditor.js"></script>
        
        <style type="text/css">
            .is-loading {cursor: wait;}
            span.is-req {color: #DC2626;}
            
            /* Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }
            ::-webkit-scrollbar-track {
                background: #F8FAF9;
            }
            ::-webkit-scrollbar-thumb {
                background: #9A9DA3;
                border-radius: 4px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #5A5D63;
            }
            
            /* Toastr overrides for Tailwind colors */
            .toast-success {
                background-color: #1FBF8F !important;
            }
            .toast-error {
                background-color: #DC2626 !important;
            }
            .toast-warning {
                background-color: #FF8A1F !important;
            }
            .toast-info {
                background-color: #0EA5E9 !important;
            }
            
            /* ============================================
               SELECT2 TAILWIND MODERN STYLING
               ============================================ */
            
            /* Container */
            .select2-container {
                width: 100% !important;
            }
            
            /* Selection Box - Single */
            .select2-container--default .select2-selection--single {
                border: 1px solid #D1D5DB !important;
                border-radius: 0.5rem !important;
                height: 48px !important;
                padding: 0 12px !important;
                background-color: #FFFFFF !important;
                transition: all 0.2s ease !important;
                display: flex !important;
                align-items: center !important;
            }
            
            /* Selection Box - Multiple (Tags Input) */
            .select2-container--default .select2-selection--multiple {
                border: 1px solid #D1D5DB !important;
                border-radius: 0.5rem !important;
                min-height: 48px !important;
                padding: 6px 8px !important;
                background-color: #FFFFFF !important;
                transition: all 0.2s ease !important;
                cursor: text !important;
                display: block !important;
            }
            
            /* Clear any background images or gradients */
            .select2-container--default .select2-selection--multiple {
                background-image: none !important;
            }
            
            /* Focus State */
            .select2-container--default.select2-container--focus .select2-selection--single,
            .select2-container--default.select2-container--focus .select2-selection--multiple,
            .select2-container--default.select2-container--open .select2-selection--single,
            .select2-container--default.select2-container--open .select2-selection--multiple {
                border-color: #1FBF8F !important;
                box-shadow: 0 0 0 3px rgba(31, 191, 143, 0.1) !important;
                outline: none !important;
            }
            
            /* Selected Value Display */
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 46px !important;
                padding: 0 !important;
                color: #1F2937 !important;
                font-size: 0.875rem !important;
            }
            
            /* Placeholder */
            .select2-container--default .select2-selection--single .select2-selection__placeholder,
            .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
                color: #9CA3AF !important;
            }
            
            /* Arrow */
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 46px !important;
                right: 12px !important;
                top: 1px !important;
            }
            
            .select2-container--default .select2-selection--single .select2-selection__arrow b {
                border-color: #6B7280 transparent transparent transparent !important;
                border-width: 5px 4px 0 4px !important;
                margin-left: -4px !important;
                margin-top: -2px !important;
            }
            
            .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
                border-color: transparent transparent #6B7280 transparent !important;
                border-width: 0 4px 5px 4px !important;
            }
            
            /* ===== TAGS STYLING (CRITICAL) ===== */
            
            /* Container for all selected tags */
            .select2-container--default .select2-selection--multiple .select2-selection__rendered {
                padding: 0 !important;
                margin: 0 !important;
                list-style: none !important;
                display: block !important;
                overflow: visible !important;
            }
            
            /* Individual Tag Badge */
            .select2-container--default .select2-selection--multiple .select2-selection__choice {
                background-color: #1FBF8F !important;
                background-image: none !important;
                border: none !important;
                border-radius: 4px !important;
                color: #FFFFFF !important;
                padding: 3px 8px !important;
                padding-left: 8px !important;
                margin: 2px 4px 2px 0 !important;
                font-size: 0.75rem !important;
                line-height: 1.4 !important;
                font-weight: 500 !important;
                display: inline-block !important;
                float: left !important;
                position: relative !important;
                white-space: nowrap !important;
                vertical-align: middle !important;
                max-width: 100% !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            
            /* Remove button (X) on tag */
            .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
                color: #FFFFFF !important;
                font-size: 14px !important;
                font-weight: bold !important;
                margin-right: 4px !important;
                padding: 0 !important;
                border: none !important;
                background: transparent !important;
                cursor: pointer !important;
                float: none !important;
                display: inline !important;
                opacity: 0.8 !important;
                transition: opacity 0.2s !important;
            }
            
            .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
                color: #FFFFFF !important;
                opacity: 1 !important;
                background: transparent !important;
            }
            
            /* Tag text */
            .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
                cursor: default !important;
                padding: 0 !important;
            }
            
            /* Search Input inside tags */
            .select2-container--default .select2-search--inline {
                float: left !important;
                display: inline-block !important;
            }
            
            .select2-container--default .select2-search--inline .select2-search__field {
                margin: 0 !important;
                padding: 4px 6px !important;
                font-size: 0.875rem !important;
                color: #1F2937 !important;
                border: none !important;
                outline: none !important;
                box-shadow: none !important;
                background: transparent !important;
                min-width: 80px !important;
                max-width: 100% !important;
            }
            
            .select2-container--default .select2-search--inline .select2-search__field::placeholder {
                color: #9CA3AF !important;
            }
            
            /* Clear floats */
            .select2-container--default .select2-selection--multiple:after {
                content: "" !important;
                display: block !important;
                clear: both !important;
            }
            
            /* Dropdown */
            .select2-dropdown {
                border: 1px solid #E5E7EB !important;
                border-radius: 0.5rem !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                overflow: hidden !important;
                margin-top: 4px !important;
                background: #FFFFFF !important;
            }
            
            /* Search in Dropdown */
            .select2-search--dropdown {
                padding: 8px !important;
                background: #FFFFFF !important;
            }
            
            .select2-container--default .select2-search--dropdown .select2-search__field {
                border: 1px solid #D1D5DB !important;
                border-radius: 0.375rem !important;
                padding: 8px 12px !important;
                font-size: 0.875rem !important;
                width: 100% !important;
                outline: none !important;
                color: #1F2937 !important;
            }
            
            .select2-container--default .select2-search--dropdown .select2-search__field:focus {
                border-color: #1FBF8F !important;
                box-shadow: 0 0 0 3px rgba(31, 191, 143, 0.1) !important;
            }
            
            /* Results Container */
            .select2-results {
                background: #FFFFFF !important;
            }
            
            .select2-results__options {
                max-height: 280px !important;
                overflow-y: auto !important;
            }
            
            /* Options */
            .select2-container--default .select2-results__option {
                padding: 10px 16px !important;
                font-size: 0.875rem !important;
                color: #374151 !important;
                background: #FFFFFF !important;
                cursor: pointer !important;
                transition: all 0.15s ease !important;
            }
            
            /* Hover State */
            .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background-color: #F0FDF9 !important;
                color: #047857 !important;
            }
            
            /* Selected State */
            .select2-container--default .select2-results__option[aria-selected=true] {
                background-color: #1FBF8F !important;
                color: #FFFFFF !important;
                font-weight: 500 !important;
            }
            
            /* Disabled State */
            .select2-container--default .select2-results__option[aria-disabled=true] {
                color: #9CA3AF !important;
                cursor: not-allowed !important;
            }
            
            /* Loading/No Results */
            .select2-results__message {
                color: #6B7280 !important;
                padding: 12px 16px !important;
                font-size: 0.875rem !important;
            }
            
            /* Clear Selection Button */
            .select2-container--default .select2-selection__clear {
                color: #6B7280 !important;
                font-size: 18px !important;
                font-weight: bold !important;
                margin-right: 8px !important;
                cursor: pointer !important;
            }
            
            .select2-container--default .select2-selection__clear:hover {
                color: #DC2626 !important;
            }
            
            /* Disabled Select2 */
            .select2-container--default .select2-selection--single.select2-selection--disabled,
            .select2-container--default .select2-selection--multiple.select2-selection--disabled {
                background-color: #F3F4F6 !important;
                cursor: not-allowed !important;
                opacity: 0.6 !important;
            }
        </style>
    </body>
</html>

<script type="text/javascript">
var isLoading = false;

// ✅ SECURITY: Setup CSRF token for all AJAX requests
$.ajaxSetup({
    headers: {
        '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
    },
    data: {
        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
    }
});

// Update CSRF token after each AJAX request
$(document).ajaxComplete(function(event, xhr, settings) {
    var newToken = xhr.getResponseHeader('<?= csrf_header() ?>');
    if (newToken) {
        // Update the token for future requests
        $.ajaxSetup({
            headers: {
                '<?= csrf_header() ?>': newToken
            },
            data: {
                '<?= csrf_token() ?>': newToken
            }
        });
    }
});

// AJAX Loading Indicators
$(document).ajaxStart(function() {
    $('.top-loading').removeClass('hidden').addClass('block');
    isLoading = true;
    $('body').addClass('is-loading');
});

$(document).ajaxStop(function() {
    isLoading = false;
    $('body').removeClass('is-loading');
    $('.top-loading').removeClass('block').addClass('hidden');
});

// Document Ready
$(document).ready(function(){
    // Date Picker
    if (typeof $.fn.datepicker !== 'undefined') {
        $('.date-picker').datepicker({
            format: "yyyy-mm-dd", 
            autoclose: true, 
            todayHighlight: true
        });
    }
    
    // Select2
    if (typeof $.fn.select2 !== 'undefined') {
        // Initialize regular select2 dropdowns
        $('.select2:not(.normal):not(.select-tags)').each(function () {
            if (!$(this).data('select2')) {
                $(this).select2({ 
                    dropdownParent: $(this).parent(),
                    theme: 'default',
                    width: '100%',
                    minimumResultsForSearch: 10
                });
            }
        });
        
        // Initialize tags select2 with tagging enabled (only if not already initialized on page)
        $('.select-tags').each(function() {
            if (!$(this).data('select2')) {
                $(this).select2({
                    tags: true,
                    tokenSeparators: [",", ";"],
                    dir: "ltr",
                    placeholder: "Type and press Enter to add tags...",
                    width: '100%',
                    minimumResultsForSearch: -1,
                    allowClear: false,
                    closeOnSelect: false
                });
            }
        });
    }
    
    // Input Mask
    if (typeof $.fn.inputmask !== 'undefined') {
        $('.input-mask').inputmask();
    }
    
    // Tooltips
    if (typeof $.fn.tooltip !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
    }
});

// Keyboard Shortcuts
document.addEventListener('keydown', function(e) {
    // Toggle sidebar with '[' key
    if (e.key === '[' && !e.ctrlKey && !e.altKey) {
        const sidebarToggle = document.querySelector('[\\@click="sidebarOpen = !sidebarOpen"]');
        if (sidebarToggle) sidebarToggle.click();
    }
});

// Chart.js Initialization
document.addEventListener('DOMContentLoaded', function() {
    const chartElements = document.querySelectorAll('[data-chart]');
    
    chartElements.forEach(function(canvas) {
        const chartType = canvas.getAttribute('data-chart');
        const labels = JSON.parse(canvas.getAttribute('data-labels') || '[]');
        const datasets = JSON.parse(canvas.getAttribute('data-values') || '[]');
        const hide = JSON.parse(canvas.getAttribute('data-hide') || '[]');
        const scales = JSON.parse(canvas.getAttribute('data-scales') || '{}');
        
        const config = {
            type: chartType,
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: !hide.includes('legend')
                    },
                    tooltip: {
                        enabled: !hide.includes('tooltips')
                    }
                },
                scales: scales
            }
        };
        
        new Chart(canvas, config);
    });
});

// Network Status
window.addEventListener('offline', function() {
    $('.no-connection').removeClass('hidden').addClass('block');
});

window.addEventListener('online', function() {
    $('.no-connection').removeClass('block').addClass('hidden');
});
</script>
