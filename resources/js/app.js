import './bootstrap';
// ==================== Language Toggle ==================== 
function initLanguageToggle() {
    const langButtons = document.querySelectorAll('.lang-btn');
    const body = document.body;
    
    langButtons.forEach(button => {
        button.addEventListener('click', function() {
            const lang = this.getAttribute('data-lang');
            updateLanguage(lang);
            localStorage.setItem('language', lang);
        });
    });
    
    // Load saved language or default to English
    const savedLang = localStorage.getItem('language') || 'en';
    updateLanguage(savedLang);
}

function updateLanguage(lang) {
    const body = document.body;
    // ALWAYS keep LTR direction - no RTL switching
    const newDir = 'ltr';
    
    body.setAttribute('lang', lang);
    body.setAttribute('dir', newDir);
    document.documentElement.setAttribute('dir', newDir);
    
    const langButtons = document.querySelectorAll('.lang-btn');
    langButtons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-lang') === lang) {
            btn.classList.add('active');
        }
    });
    
    // Keep search input text alignment as LTR
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) {
        searchInput.style.textAlign = 'left';
    }
    
    // Translate page content if needed
    translatePage(lang);
}

function translatePage(lang) {
    // This function will be called to translate page content
    // You can add translation logic here
    const translations = {
        en: {
            'search-placeholder': 'Search here...',
            'dashboard': 'Dashboard',
            'diets': 'Diets',
            'profile': 'Profile',
            'messages': 'Messages',
            'notifications': 'Notifications'
        },
        ar: {
            'search-placeholder': 'ابحث هنا...',
            'dashboard': 'لوحة التحكم',
            'diets': 'الحميات',
            'profile': 'الملف الشخصي',
            'messages': 'الرسائل',
            'notifications': 'الإشعارات'
        }
    };
    
    // Update placeholders and text
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput && translations[lang]) {
        searchInput.placeholder = translations[lang]['search-placeholder'];
    }
}

// ==================== Modal Functions ==================== 
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

function closeAllModals() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.classList.remove('show');
    });
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside of it
function setupModalClickHandlers() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal(this.id);
            }
        });
    });
    
    // Close button handlers
    const closeButtons = document.querySelectorAll('.modal-close');
    closeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                closeModal(modal.id);
            }
        });
    });
}

// ==================== Search Functions ==================== 
function initSearch() {
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            performSearch(searchTerm);
        });
    }
}

function performSearch(term) {
    // Filter diet cards
    const dietCards = document.querySelectorAll('.diet-card');
    dietCards.forEach(card => {
        const title = card.querySelector('.diet-card-title');
        if (title && title.textContent.toLowerCase().includes(term)) {
            card.style.display = 'block';
        } else if (title) {
            card.style.display = 'none';
        }
    });
    
    // Filter table rows
    const tableRows = document.querySelectorAll('.data-table tbody tr');
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(term)) {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    });
}

// ==================== Navigation Functions ==================== 
function setActiveNav(navItem) {
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.classList.remove('active');
    });
    if (navItem) {
        navItem.classList.add('active');
    }
}

function goBack() {
    window.history.back();
}

// ==================== Form Handling ==================== 
function handleFormSubmit(formId, callback) {
    const form = document.getElementById(formId);
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (callback) {
                callback(new FormData(form));
            }
        });
    }
}

function getFormData(formId) {
    const form = document.getElementById(formId);
    if (form) {
        return new FormData(form);
    }
    return null;
}

// ==================== Confirmation Dialog ==================== 
function showConfirmation(message, onConfirm, onCancel) {
    const confirmed = confirm(message);
    if (confirmed && onConfirm) {
        onConfirm();
    } else if (!confirmed && onCancel) {
        onCancel();
    }
    return confirmed;
}

// ==================== Notification Functions ==================== 
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: ${type === 'success' ? '#10B981' : type === 'error' ? '#EF4444' : '#3B82F6'};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// ==================== Data Management ==================== 
function saveDiet(dietData) {
    let diets = JSON.parse(localStorage.getItem('diets')) || [];
    const newDiet = {
        id: Date.now(),
        ...Object.fromEntries(dietData)
    };
    diets.push(newDiet);
    localStorage.setItem('diets', JSON.stringify(diets));
    return newDiet;
}

function updateDiet(dietId, dietData) {
    let diets = JSON.parse(localStorage.getItem('diets')) || [];
    const index = diets.findIndex(d => d.id === dietId);
    if (index !== -1) {
        diets[index] = { id: dietId, ...Object.fromEntries(dietData) };
        localStorage.setItem('diets', JSON.stringify(diets));
        return diets[index];
    }
    return null;
}

function deleteDiet(dietId) {
    let diets = JSON.parse(localStorage.getItem('diets')) || [];
    diets = diets.filter(d => d.id !== dietId);
    localStorage.setItem('diets', JSON.stringify(diets));
}

function getDiets() {
    return JSON.parse(localStorage.getItem('diets')) || [];
}

function getDietById(dietId) {
    const diets = getDiets();
    return diets.find(d => d.id === parseInt(dietId));
}

// ==================== Meal Management ==================== 
function saveMeal(mealData) {
    let meals = JSON.parse(localStorage.getItem('meals')) || [];
    const newMeal = {
        id: Date.now(),
        ...Object.fromEntries(mealData)
    };
    meals.push(newMeal);
    localStorage.setItem('meals', JSON.stringify(meals));
    return newMeal;
}

function getMeals() {
    return JSON.parse(localStorage.getItem('meals')) || [];
}

function deleteMeal(mealId) {
    let meals = JSON.parse(localStorage.getItem('meals')) || [];
    meals = meals.filter(m => m.id !== mealId);
    localStorage.setItem('meals', JSON.stringify(meals));
}

// ==================== Initialize on Page Load ==================== 
document.addEventListener('DOMContentLoaded', function() {
    initLanguageToggle();
    initSearch();
    setupModalClickHandlers();
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Close modals with Escape key
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
