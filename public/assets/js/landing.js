/* ========================================
   TIER 5 OPTIMIZATION: jQuery Removed - Alpine.js + Fetch API
   File: landing.js
   Date: September 2026
   Purpose: Modern vanilla JS implementation replacing jQuery for better performance
   ======================================== */

var baseurl = "";  // Set via PHP in footer before script load
var searchAbortController = null;
var searchDebounceTimer = null;

/**
 * ✅ TIER 5: Debounced firmware model data search via Fetch API
 * Prevents excessive AJAX calls while user is still typing
 * @param {string} data - Search query
 * @param {number} delay - Debounce delay in milliseconds (default: 300ms)
 */
function loadAjaxData(data, delay) {
    delay = delay || 300;  // Default 300ms debounce
    
    // Clear previous debounce timer
    if (searchDebounceTimer) {
        clearTimeout(searchDebounceTimer);
    }
    
    // Set new debounce timer
    searchDebounceTimer = setTimeout(function() {
        if (data.length > 2) {
            // Abort previous fetch request if still pending
            if (searchAbortController) {
                const resultsDiv = document.querySelector('.load-ajax-data');
                if (resultsDiv) resultsDiv.style.display = 'none';
                searchAbortController.abort();
            }
            
            // Create new AbortController for this request
            searchAbortController = new AbortController();
            
            const searchIcon = document.querySelector('.search-icon');
            if (searchIcon) searchIcon.classList.add('animate-spin');
            
            // Create timeout for fetch
            const timeoutId = setTimeout(() => searchAbortController.abort(), 5000);
            
            // Use GET request with query parameter (matching route configuration)
            fetch(baseurl + 'load/model?query=' + encodeURIComponent(data), {
                method: 'GET',
                signal: searchAbortController.signal
            })
            .then(response => {
                clearTimeout(timeoutId);
                return response.text();
            })
            .then(response => {
                if (searchIcon) searchIcon.classList.remove('animate-spin');
                const resultsDiv = document.querySelector('.load-ajax-data');
                if (resultsDiv) {
                    resultsDiv.style.display = 'block';
                    resultsDiv.innerHTML = '<div class="bg-white rounded-xl shadow-2xl border border-gray-200" style="max-height: 240px; overflow-y: auto;">' + response + '</div>';
                }
            })
            .catch(error => {
                clearTimeout(timeoutId);
                if (searchIcon) searchIcon.classList.remove('animate-spin');
                if (error.name !== 'AbortError') {
                    const resultsDiv = document.querySelector('.load-ajax-data');
                    if (resultsDiv) {
                        resultsDiv.style.display = 'none';
                    }
                }
            });
        } else {
            const searchIcon = document.querySelector('.search-icon');
            const resultsDiv = document.querySelector('.load-ajax-data');
            if (searchIcon) searchIcon.classList.remove('animate-spin');
            if (resultsDiv) {
                resultsDiv.style.display = 'none';
                resultsDiv.innerHTML = '';
            }
        }
    }, delay);
}

/**
 * Hide dropdown when clicking outside
 */
document.addEventListener('click', function(e) {
    const resultsDiv = document.querySelector('.load-ajax-data');
    const searchInput = document.querySelector('.ajax-model-load');
    
    if (resultsDiv && !resultsDiv.contains(e.target) && e.target !== searchInput) {
        resultsDiv.style.display = 'none';
    }
});

/**
 * Send ping to keep session alive
 */
function setPing() {
    fetch(baseurl + 'home/ping')
        .then(response => response.text())
        .catch(error => console.log('Ping failed:', error));
}

/**
 * Initialize ping on document ready and every 60 seconds
 */
document.addEventListener('DOMContentLoaded', function() {
    setPing();
    setInterval(setPing, 60000);
});

/**
 * Handle comment form submission
 */
document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.querySelector('.comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btnObj = this.querySelector('button[type="submit"]');
            const originalText = btnObj.textContent;
            btnObj.disabled = true;
            btnObj.textContent = 'Please wait....';
            
            const formData = new FormData(this);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(response => {
                btnObj.disabled = false;
                btnObj.textContent = 'Submit';
                
                if (response.trim() !== 'success') {
                    alert(response);
                } else {
                    // Clear form fields
                    const fields = this.querySelectorAll('.comment-fields');
                    fields.forEach(field => {
                        field.value = '';
                        if (field.tagName === 'TEXTAREA') field.textContent = '';
                    });
                    
                    // Show success message
                    const successMsg = document.querySelector('.success-comment');
                    if (successMsg) {
                        successMsg.style.display = 'block';
                        setTimeout(() => {
                            successMsg.style.display = 'none';
                        }, 5000);
                    }
                }
            })
            .catch(error => {
                btnObj.disabled = false;
                btnObj.textContent = originalText;
                alert('Unable to process your request');
            });
        });
    }
});

/**
 * Handle contact form submission
 */
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.querySelector('.contact-us-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btnObj = this.querySelector('button[type="submit"]');
            btnObj.disabled = true;
            
            const formData = new FormData(this);
            
            fetch(baseurl + 'home/contactUs', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(response => {
                btnObj.disabled = false;
                
                if (response.trim() !== 'success') {
                    alert(response);
                } else {
                    alert("Thank you for reaching us, we'll contact you ASAP.");
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            })
            .catch(error => {
                btnObj.disabled = false;
                alert('Unable to process your request');
            });
        });
    }
});

/**
 * Handle post action (like, share, etc)
 */
function postAction(obj, area, type, postId) {
    if (obj.classList.contains('active')) {
        return false;
    }
    
    // Remove active class from all buttons
    const buttons = document.querySelectorAll('.btn-post-action');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    // Add active class to clicked button
    obj.classList.add('active');
    
    const params = 'postId=' + postId + '&type=' + type + '&area=' + area;
    
    fetch(baseurl + 'landingPages/postAction', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: params
    })
    .catch(error => console.log('Post action failed:', error));
}

/**
 * Handle search field Enter key
 */
function isFieldSearch(e, obj) {
    if (e.which == 13 || e.keyCode == 13) {
        searchInField(obj.value);
    }
}

/**
 * Search in site using Google search operator
 */
function searchInField(search) {
    if (search !== '') {
        const siteSearchDomain = window.location.hostname;
        const url = 'https://www.google.com/search?q=' + encodeURIComponent(search) + '&sitesearch=' + siteSearchDomain;
        window.open(url, '_blank').focus();
    }
}
