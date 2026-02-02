/**
 * Dark Mode System
 * File: assets/js/darkmode.js
 * Toggle dan simpan preferensi dark mode
 */

class DarkMode {
    constructor() {
        this.theme = localStorage.getItem('theme') || 'light';
        this.init();
    }
    
    init() {
        // Apply theme on page load
        this.applyTheme(this.theme);
        
        // Create toggle button if not exists
        this.createToggleButton();
        
        // Listen for toggle events
        this.attachEventListeners();
    }
    
    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        this.theme = theme;
        
        // Update toggle button icon
        this.updateToggleIcon();
    }
    
    toggleTheme() {
        const newTheme = this.theme === 'light' ? 'dark' : 'light';
        this.applyTheme(newTheme);
    }
    
    createToggleButton() {
        // Check if button already exists
        if (document.getElementById('darkModeToggle')) return;
        
        // Find navbar or create container
        const navbar = document.querySelector('.top-navbar, .navbar');
        if (!navbar) return;
        
        // Create toggle button
        const toggleBtn = document.createElement('button');
        toggleBtn.id = 'darkModeToggle';
        toggleBtn.className = 'btn btn-sm btn-outline-secondary ms-2';
        toggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
        toggleBtn.title = 'Toggle Dark Mode';
        toggleBtn.style.cssText = 'border-radius: 50%; width: 40px; height: 40px; padding: 0;';
        
        // Insert button
        const userInfo = navbar.querySelector('.user-info');
        if (userInfo) {
            userInfo.insertBefore(toggleBtn, userInfo.firstChild);
        }
    }
    
    updateToggleIcon() {
        const toggleBtn = document.getElementById('darkModeToggle');
        if (!toggleBtn) return;
        
        const icon = toggleBtn.querySelector('i');
        if (this.theme === 'dark') {
            icon.className = 'fas fa-sun';
        } else {
            icon.className = 'fas fa-moon';
        }
    }
    
    attachEventListeners() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('#darkModeToggle')) {
                this.toggleTheme();
            }
        });
    }
}

// Initialize dark mode when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.darkMode = new DarkMode();
});