/**
 * Alpine.js Initialization
 * Version: 3.13.5
 */

// Import Alpine.js
import Alpine from 'alpinejs';

// Alpine.js Global Store for App State
Alpine.store('app', {
    sidebarOpen: false,
    modalOpen: false,
    notificationCount: 0,
    
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
    },
    
    openModal() {
        this.modalOpen = true;
    },
    
    closeModal() {
        this.modalOpen = false;
    },
    
    incrementNotification() {
        this.notificationCount++;
    },
});

// Alpine.js Components

// Dropdown Component
Alpine.data('dropdown', () => ({
    open: false,
    
    toggle() {
        this.open = !this.open;
    },
    
    close() {
        this.open = false;
    },
}));

// Tab Component
Alpine.data('tabs', (defaultTab = 1) => ({
    activeTab: defaultTab,
    
    switchTab(tab) {
        this.activeTab = tab;
    },
    
    isActive(tab) {
        return this.activeTab === tab;
    },
}));

// Modal Component
Alpine.data('modal', (initialOpen = false) => ({
    open: initialOpen,
    
    show() {
        this.open = true;
        document.body.style.overflow = 'hidden';
    },
    
    hide() {
        this.open = false;
        document.body.style.overflow = '';
    },
    
    toggle() {
        this.open ? this.hide() : this.show();
    },
}));

// Toast/Notification Component
Alpine.data('toast', () => ({
    visible: false,
    message: '',
    type: 'info', // info, success, warning, error
    
    show(message, type = 'info', duration = 3000) {
        this.message = message;
        this.type = type;
        this.visible = true;
        
        setTimeout(() => {
            this.hide();
        }, duration);
    },
    
    hide() {
        this.visible = false;
        setTimeout(() => {
            this.message = '';
        }, 300);
    },
}));

// Form Validation Component
Alpine.data('formValidation', () => ({
    errors: {},
    
    validateField(fieldName, value, rules) {
        if (rules.required && !value) {
            this.errors[fieldName] = 'This field is required';
            return false;
        }
        
        if (rules.email && value && !this.isValidEmail(value)) {
            this.errors[fieldName] = 'Please enter a valid email';
            return false;
        }
        
        if (rules.minLength && value.length < rules.minLength) {
            this.errors[fieldName] = `Minimum ${rules.minLength} characters required`;
            return false;
        }
        
        delete this.errors[fieldName];
        return true;
    },
    
    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },
    
    hasError(fieldName) {
        return !!this.errors[fieldName];
    },
    
    getError(fieldName) {
        return this.errors[fieldName] || '';
    },
}));

// Sidebar Component
Alpine.data('sidebar', () => ({
    open: window.innerWidth >= 1024,
    collapsed: false,
    
    toggle() {
        this.open = !this.open;
    },
    
    collapse() {
        this.collapsed = !this.collapsed;
    },
    
    close() {
        if (window.innerWidth < 1024) {
            this.open = false;
        }
    },
}));

// Search Component
Alpine.data('search', () => ({
    query: '',
    results: [],
    loading: false,
    
    async search() {
        if (this.query.length < 2) {
            this.results = [];
            return;
        }
        
        this.loading = true;
        // Implement your search logic here
        // Example: await fetch(`/api/search?q=${this.query}`)
        this.loading = false;
    },
    
    clear() {
        this.query = '';
        this.results = [];
    },
}));

// Table Component with Sorting
Alpine.data('dataTable', (initialData = []) => ({
    data: initialData,
    sortColumn: null,
    sortDirection: 'asc',
    
    sort(column) {
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = column;
            this.sortDirection = 'asc';
        }
        
        this.data.sort((a, b) => {
            let aVal = a[column];
            let bVal = b[column];
            
            if (typeof aVal === 'string') {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }
            
            if (this.sortDirection === 'asc') {
                return aVal > bVal ? 1 : -1;
            } else {
                return aVal < bVal ? 1 : -1;
            }
        });
    },
}));

// Accordion Component
Alpine.data('accordion', (initialOpen = null) => ({
    openItem: initialOpen,
    
    toggle(item) {
        this.openItem = this.openItem === item ? null : item;
    },
    
    isOpen(item) {
        return this.openItem === item;
    },
}));

// Make Alpine available globally
window.Alpine = Alpine;

// Start Alpine
Alpine.start();

// Export for module usage
export default Alpine;
