<?php
$securityWords = array('CupCake','auToMobile','Samsung','Firmware','bOOt','MoBile');
shuffle($securityWords);
$qanswer = $securityWords[0];
?>
<!-- Modern Contact Page -->
<div class="lg:col-span-9 lg:order-1">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-navy-900 rounded-2xl shadow-lg p-6 text-white">
            <h1 class="text-2xl font-bold mb-2">Get In Touch</h1>
            <p class="text-white/80">Have questions? We'd love to hear from you. Send us a message!</p>
        </div>

        <!-- Contact Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-accent-soft rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-ink mb-2">Email Us</h3>
                <p class="text-sm text-ink-muted">Get in touch via email</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-highlight-soft rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-highlight" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-ink mb-2">Quick Response</h3>
                <p class="text-sm text-ink-muted">24-48 hours response time</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-accent-soft rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-ink mb-2">Support</h3>
                <p class="text-sm text-ink-muted">We're here to help you</p>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-8 py-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-ink flex items-center space-x-2">
                    <span class="w-2 h-2 bg-accent rounded-full"></span>
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    <span>Send us a Message</span>
                </h2>
            </div>
            
            <div class="p-8">
                <form action="" class="contact-us-form space-y-6" method="post">
                    <input type="hidden" name="form_token" value="<?= base64_encode($qanswer) ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-ink-muted mb-2">
                                Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-accent focus:ring-4 focus:ring-accent-soft outline-none transition-all" 
                                   placeholder="Your name" 
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-ink-muted mb-2">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-accent focus:ring-4 focus:ring-accent-soft outline-none transition-all" 
                                   placeholder="your.email@example.com" 
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-ink-muted mb-2">Website</label>
                            <input type="text" 
                                   name="website" 
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-accent focus:ring-4 focus:ring-accent-soft outline-none transition-all" 
                                   placeholder="https://yoursite.com">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-ink-muted mb-2">
                            Message <span class="text-danger">*</span>
                        </label>
                        <textarea name="description" 
                                  rows="6" 
                                  class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-accent focus:ring-4 focus:ring-accent-soft outline-none transition-all resize-none" 
                                  placeholder="Tell us how we can help you..." 
                                  required></textarea>
                    </div>
                    
                    <div class="bg-canvas rounded-xl p-6 border border-gray-200">
                        <label class="block text-sm font-semibold text-ink-muted mb-3">
                            Security Check <span class="text-danger">*</span>
                        </label>
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 bg-navy-900 text-white px-6 py-3 rounded-lg font-mono text-lg font-bold">
                                <?= $qanswer ?>
                            </div>
                            <input type="text" 
                                   name="sanswer" 
                                   class="flex-1 px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-accent focus:ring-4 focus:ring-accent-soft outline-none transition-all text-center font-semibold comment-fields" 
                                   placeholder="Enter the text shown" 
                                   required>
                        </div>
                        <p class="text-sm text-ink-faint mt-2">Please enter the security key shown above</p>
                    </div>
                    
                    <div class="flex items-center justify-end">
                        <button type="submit" 
                                class="px-8 py-3 bg-accent text-white rounded-xl font-semibold hover:bg-accent-hover hover:scale-105 transition-all duration-200 flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            <span>Send Message</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-ink mb-6">Frequently Asked Questions</h3>
            <div class="space-y-4" x-data="accordion()">
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="toggle(1)" class="w-full px-6 py-4 text-left bg-gray-50 hover:bg-canvas transition-colors flex items-center justify-between">
                        <span class="font-semibold text-ink">How do I download firmware?</span>
                        <svg class="w-5 h-5 text-ink-faint transform transition-transform" :class="isOpen(1) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="isOpen(1)" x-collapse class="px-6 py-4 bg-white">
                        <p class="text-ink-muted">Simply search for your device model, select the firmware version you need, and click the download button. All downloads are free and at maximum speed.</p>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="toggle(2)" class="w-full px-6 py-4 text-left bg-gray-50 hover:bg-canvas transition-colors flex items-center justify-between">
                        <span class="font-semibold text-ink">Are these official Samsung firmwares?</span>
                        <svg class="w-5 h-5 text-ink-faint transform transition-transform" :class="isOpen(2) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="isOpen(2)" x-collapse class="px-6 py-4 bg-white">
                        <p class="text-ink-muted">Yes, all firmware files are 100% official Samsung stock firmware directly from Samsung servers. We do not modify any files.</p>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="toggle(3)" class="w-full px-6 py-4 text-left bg-gray-50 hover:bg-canvas transition-colors flex items-center justify-between">
                        <span class="font-semibold text-ink">How long does it take to get a response?</span>
                        <svg class="w-5 h-5 text-ink-faint transform transition-transform" :class="isOpen(3) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="isOpen(3)" x-collapse class="px-6 py-4 bg-white">
                        <p class="text-ink-muted">We typically respond within 24-48 hours. For urgent matters, please mention "URGENT" in your message subject.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
