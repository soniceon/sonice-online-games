/**
 * Profile.js - Handles user profile functionality
 */
class ProfileManager {
    constructor() {
        this.currentUser = null;
        this.isEditMode = false;
        this.selectedAvatar = null;

        // Initialize tabs and panels
        this.tabs = document.querySelectorAll('.tab-button');
        this.panels = document.querySelectorAll('.tab-panel');

        // Initialize DOM elements
        this.profileContentEl = document.getElementById('profile-content');
        this.notLoggedInEl = document.getElementById('not-logged-in');
        
        // Tab elements
        this.tabInfo = document.getElementById('tab-info');
        this.tabSecurity = document.getElementById('tab-security');
        this.tabFavorites = document.getElementById('tab-favorites');
        this.tabHistory = document.getElementById('tab-history');
        
        // Panel elements
        this.panelInfo = document.getElementById('panel-info');
        this.panelSecurity = document.getElementById('panel-security');
        this.panelFavorites = document.getElementById('panel-favorites');
        this.panelHistory = document.getElementById('panel-history');
        
        // Profile elements
        this.profileViewMode = document.getElementById('profile-view-mode');
        this.profileEditMode = document.getElementById('profile-edit-mode');
        this.editProfileBtn = document.getElementById('edit-profile-btn');
        this.cancelEditBtn = document.getElementById('cancel-edit-btn');
        
        // Profile form elements
        this.editUsername = document.getElementById('edit-username');
        this.editEmail = document.getElementById('edit-email');
        this.editDisplayName = document.getElementById('edit-display-name');
        this.editCountry = document.getElementById('edit-country');
        this.editBio = document.getElementById('edit-bio');
        
        // Profile view elements
        this.viewUsername = document.getElementById('view-username');
        this.viewEmail = document.getElementById('view-email');
        this.viewDisplayName = document.getElementById('view-display-name');
        this.viewCountry = document.getElementById('view-country');
        this.viewBio = document.getElementById('view-bio');
        
        // Profile header elements
        this.profileUsername = document.getElementById('profile-username');
        this.profileMemberSince = document.getElementById('profile-member-since');
        this.profileGamesPlayed = document.getElementById('profile-games-played');
        this.profileAchievements = document.getElementById('profile-achievements');
        this.userAvatar = document.getElementById('user-avatar');
        
        // Avatar change elements
        this.changeAvatarBtn = document.getElementById('change-avatar-btn');
        this.avatarModal = document.getElementById('avatar-modal');
        this.closeAvatarModal = document.getElementById('close-avatar-modal');
        this.avatarOptions = document.querySelectorAll('.avatar-option');
        
        // Security elements
        this.changePasswordForm = document.getElementById('change-password-form');
        this.currentPassword = document.getElementById('current-password');
        this.newPassword = document.getElementById('new-password');
        this.confirmPassword = document.getElementById('confirm-password');
        this.logoutAllDevicesBtn = document.getElementById('logout-all-devices-btn');
        this.deleteAccountBtn = document.getElementById('delete-account-btn');
        
        // Delete account modal elements
        this.deleteAccountModal = document.getElementById('delete-account-modal');
        this.cancelDeleteBtn = document.getElementById('cancel-delete-btn');
        this.confirmDeleteBtn = document.getElementById('confirm-delete-btn');
        this.deleteConfirmPassword = document.getElementById('delete-confirm-password');
        
        // History elements
        this.historyFilter = document.getElementById('history-filter');
        this.playHistory = document.getElementById('play-history');
        
        // Login redirection
        this.loginRedirectBtn = document.getElementById('login-redirect-btn');

        // Initialize event listeners
        this.initEventListeners();
        
        // Check authentication status
        this.checkAuth();
    }

    /**
     * Initialize event listeners
     */
    initEventListeners() {
        // Tab switching
        this.tabs.forEach(tab => {
            tab.addEventListener('click', () => this.switchTab(tab.id));
        });
        
        // Profile editing
        this.editProfileBtn.addEventListener('click', () => this.toggleEditMode(true));
        this.cancelEditBtn.addEventListener('click', () => this.toggleEditMode(false));
        this.profileEditMode.addEventListener('submit', (e) => this.saveProfile(e));
        
        // Avatar change
        this.changeAvatarBtn.addEventListener('click', () => this.openAvatarModal());
        this.closeAvatarModal.addEventListener('click', () => this.closeAvatarModal());
        this.avatarOptions.forEach(option => {
            option.addEventListener('click', () => this.selectAvatar(option));
        });
        
        // Security actions
        this.changePasswordForm.addEventListener('submit', (e) => this.changePassword(e));
        this.logoutAllDevicesBtn.addEventListener('click', () => this.logoutAllDevices());
        this.deleteAccountBtn.addEventListener('click', () => this.openDeleteModal());
        this.cancelDeleteBtn.addEventListener('click', () => this.closeDeleteModal());
        this.confirmDeleteBtn.addEventListener('click', () => this.deleteAccount());
        
        // History filter
        this.historyFilter.addEventListener('change', () => this.loadPlayHistory());
        
        // Login redirect
        this.loginRedirectBtn.addEventListener('click', () => this.redirectToLogin());
        
        // Listen for authentication changes
        document.addEventListener('authStateChanged', (e) => {
            this.handleAuthChange(e.detail);
        });
    }

    /**
     * Switch between profile tabs
     * @param {string} tabId - The tab ID to switch to
     */
    switchTab(tabId) {
        // Reset all tabs and panels
        this.tabs.forEach(tab => {
            tab.classList.remove('active');
            tab.classList.remove('border-purple-primary');
            tab.classList.remove('text-purple-primary');
            tab.classList.add('border-transparent');
            tab.classList.add('text-gray-400');
        });
        
        this.panels.forEach(panel => {
            panel.classList.add('hidden');
        });
        
        // Activate selected tab
        const selectedTab = document.getElementById(tabId);
        selectedTab.classList.add('active');
        selectedTab.classList.add('border-purple-primary');
        selectedTab.classList.add('text-purple-primary');
        selectedTab.classList.remove('border-transparent');
        selectedTab.classList.remove('text-gray-400');
        
        // Show corresponding panel
        const panelId = 'panel-' + tabId.split('-')[1];
        const selectedPanel = document.getElementById(panelId);
        selectedPanel.classList.remove('hidden');
        
        // Load data for specific tabs if needed
        if (tabId === 'tab-favorites') {
            this.loadFavorites();
        } else if (tabId === 'tab-history') {
            this.loadPlayHistory();
        }
    }

    /**
     * Toggle between edit and view mode for profile
     * @param {boolean} isEdit - Whether to switch to edit mode
     */
    toggleEditMode(isEdit) {
        this.isEditMode = isEdit;
        
        if (isEdit) {
            // Switch to edit mode
            this.profileViewMode.classList.add('hidden');
            this.profileEditMode.classList.remove('hidden');
            
            // Fill form with current values
            this.editUsername.value = this.currentUser.username || '';
            this.editEmail.value = this.currentUser.email || '';
            this.editDisplayName.value = this.currentUser.displayName || '';
            this.editCountry.value = this.currentUser.country || '';
            this.editBio.value = this.currentUser.bio || '';
        } else {
            // Switch to view mode
            this.profileViewMode.classList.remove('hidden');
            this.profileEditMode.classList.add('hidden');
            
            // Reset any error messages
            document.getElementById('username-error').classList.add('hidden');
            document.getElementById('email-error').classList.add('hidden');
        }
    }

    /**
     * Save profile changes
     * @param {Event} e - Form submit event
     */
    saveProfile(e) {
        e.preventDefault();
        
        // Validate inputs
        let isValid = true;
        
        // Username validation
        if (!this.editUsername.value.trim()) {
            this.showError('username-error', 'Username cannot be empty');
            isValid = false;
        } else {
            document.getElementById('username-error').classList.add('hidden');
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!this.editEmail.value.trim() || !emailRegex.test(this.editEmail.value.trim())) {
            this.showError('email-error', 'Please enter a valid email address');
            isValid = false;
        } else {
            document.getElementById('email-error').classList.add('hidden');
        }
        
        if (!isValid) return;
        
        // Update user data
        const updatedUser = {
            ...this.currentUser,
            username: this.editUsername.value.trim(),
            email: this.editEmail.value.trim(),
            displayName: this.editDisplayName.value.trim(),
            country: this.editCountry.value,
            bio: this.editBio.value.trim()
        };
        
        // Here would be an API call to update the user profile
        // For now, we'll just simulate success
        setTimeout(() => {
            this.currentUser = updatedUser;
            this.updateProfileView();
            this.toggleEditMode(false);
            this.showToast('Profile updated successfully');
        }, 500);
    }

    /**
     * Load user's favorite games
     */
    loadFavorites() {
        const favoritesContainer = document.getElementById('favorites-container');
        
        // Check if the favorites manager is available
        if (window.favoriteManager) {
            const favorites = window.favoriteManager.getFavoritesData();
            
            if (favorites && favorites.length > 0) {
                // Clear the container
                favoritesContainer.innerHTML = '';
                
                // Add each favorite game
                favorites.forEach(game => {
                    const gameCard = this.createGameCard(game);
                    favoritesContainer.appendChild(gameCard);
                });
            } else {
                // No favorites
                favoritesContainer.innerHTML = `
                    <div class="col-span-full text-center py-10">
                        <i class="far fa-heart text-4xl mb-2 text-gray-400"></i>
                        <p class="text-gray-400">You haven't added any favorites yet</p>
                        <a href="games.html" class="mt-4 inline-block px-4 py-2 bg-purple-primary text-white rounded hover:bg-purple-600 transition-colors">
                            Browse Games
                        </a>
                    </div>
                `;
            }
        } else {
            // Favorites manager not available
            favoritesContainer.innerHTML = `
                <div class="col-span-full text-center py-10">
                    <i class="fas fa-exclamation-triangle text-4xl mb-2 text-gray-400"></i>
                    <p class="text-gray-400">Unable to load favorites</p>
                </div>
            `;
        }
    }

    /**
     * Create a game card element
     * @param {Object} game - The game data
     * @returns {HTMLElement} - The game card element
     */
    createGameCard(game) {
        const div = document.createElement('div');
        div.className = 'bg-dark rounded-lg overflow-hidden';
        div.innerHTML = `
            <a href="game.html?id=${game.id}" class="block">
                <img src="${game.thumbnail}" alt="${game.title}" class="w-full h-36 object-cover">
                <div class="p-4">
                    <h4 class="font-bold mb-1">${game.title}</h4>
                    <p class="text-gray-400 text-sm mb-3">${game.category}</p>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <i class="fas fa-gamepad mr-1 text-gray-400"></i>
                            <span class="text-sm text-gray-400">${game.plays || 0} plays</span>
                        </div>
                        <button class="favorite-button active" data-game-id="${game.id}">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </a>
        `;
        
        // Add click event for the favorite button
        const favoriteButton = div.querySelector('.favorite-button');
        favoriteButton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (window.favoriteManager) {
                window.favoriteManager.toggleFavorite(game.id);
                this.loadFavorites(); // Reload favorites after toggle
            }
        });
        
        return div;
    }

    /**
     * Load user's play history
     */
    loadPlayHistory() {
        const days = parseInt(this.historyFilter.value);
        
        // Here would be an API call to get the user's play history
        // For now, we'll just simulate some data
        const mockHistory = [
            { id: 'game1', title: 'Tetris', thumbnail: 'assets/images/games/tetris.jpg', lastPlayed: '2023-06-01T14:30:00', playTime: 45, score: 5280 },
            { id: 'game2', title: 'Snake', thumbnail: 'assets/images/games/snake.jpg', lastPlayed: '2023-05-28T20:15:00', playTime: 30, score: 120 },
            { id: 'game3', title: 'Pac-Man', thumbnail: 'assets/images/games/pacman.jpg', lastPlayed: '2023-05-25T10:45:00', playTime: 60, score: 8750 }
        ];
        
        const filteredHistory = days === 'all' ? mockHistory : mockHistory.filter(item => {
            const lastPlayed = new Date(item.lastPlayed);
            const now = new Date();
            const diffTime = Math.abs(now - lastPlayed);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays <= days;
        });
        
        if (filteredHistory.length > 0) {
            this.playHistory.innerHTML = '';
            
            filteredHistory.forEach(item => {
                const historyItem = document.createElement('div');
                historyItem.className = 'bg-dark rounded-lg overflow-hidden';
                historyItem.innerHTML = `
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-1/4">
                            <img src="${item.thumbnail}" alt="${item.title}" class="w-full h-full md:h-28 object-cover">
                        </div>
                        <div class="p-4 flex-grow">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold">${item.title}</h4>
                                <span class="text-sm text-gray-400">${this.formatDate(item.lastPlayed)}</span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div>
                                    <span class="text-gray-400 text-sm">Play Time:</span>
                                    <p>${this.formatPlayTime(item.playTime)}</p>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-sm">Score:</span>
                                    <p>${item.score.toLocaleString()}</p>
                                </div>
                                <div class="col-span-2 md:col-span-1 mt-2 md:mt-0 text-right md:text-left">
                                    <a href="game.html?id=${item.id}" class="inline-block px-3 py-1 bg-purple-primary hover:bg-purple-600 rounded text-white text-sm transition-colors">
                                        Play Again
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                this.playHistory.appendChild(historyItem);
            });
        } else {
            this.playHistory.innerHTML = `
                <div class="text-center py-10">
                    <i class="fas fa-history text-4xl mb-2 text-gray-400"></i>
                    <p class="text-gray-400">No play history found for the selected period</p>
                    <a href="games.html" class="mt-4 inline-block px-4 py-2 bg-purple-primary text-white rounded hover:bg-purple-600 transition-colors">
                        Start Playing
                    </a>
                </div>
            `;
        }
    }

    /**
     * Format date for display
     * @param {string} dateString - ISO date string
     * @returns {string} - Formatted date
     */
    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 1) {
            return 'Yesterday';
        } else if (diffDays < 7) {
            return `${diffDays} days ago`;
        } else {
            return date.toLocaleDateString();
        }
    }

    /**
     * Format play time for display
     * @param {number} minutes - Play time in minutes
     * @returns {string} - Formatted play time
     */
    formatPlayTime(minutes) {
        if (minutes < 60) {
            return `${minutes} min`;
        } else {
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            return mins > 0 ? `${hours}h ${mins}m` : `${hours}h`;
        }
    }

    /**
     * Open the avatar selection modal
     */
    openAvatarModal() {
        this.avatarModal.classList.remove('hidden');
    }

    /**
     * Close the avatar selection modal
     */
    closeAvatarModal() {
        this.avatarModal.classList.add('hidden');
        this.avatarOptions.forEach(option => {
            option.classList.remove('border-purple-primary');
        });
    }

    /**
     * Select an avatar from the modal
     * @param {HTMLElement} option - The selected avatar option
     */
    selectAvatar(option) {
        // Clear previous selection
        this.avatarOptions.forEach(opt => {
            opt.classList.remove('border-purple-primary');
        });
        
        // Highlight selected avatar
        option.classList.add('border-purple-primary');
        
        // Get the image src
        const imgSrc = option.querySelector('img').src;
        
        // Update the user's avatar
        this.userAvatar.src = imgSrc;
        
        // Update the user data
        this.currentUser.avatar = imgSrc;
        
        // Here would be an API call to update the user's avatar
        // For now, we'll just simulate success
        setTimeout(() => {
            this.closeAvatarModal();
            this.showToast('Profile picture updated');
        }, 500);
    }

    /**
     * Change the user's password
     * @param {Event} e - Form submit event
     */
    changePassword(e) {
        e.preventDefault();
        
        // Validate inputs
        let isValid = true;
        
        // Current password validation
        if (!this.currentPassword.value.trim()) {
            this.showError('current-password-error', 'Please enter your current password');
            isValid = false;
        } else {
            document.getElementById('current-password-error').classList.add('hidden');
        }
        
        // New password validation
        if (!this.newPassword.value.trim() || this.newPassword.value.length < 8) {
            this.showError('new-password-error', 'Password must be at least 8 characters');
            isValid = false;
        } else {
            document.getElementById('new-password-error').classList.add('hidden');
        }
        
        // Confirm password validation
        if (this.newPassword.value !== this.confirmPassword.value) {
            this.showError('confirm-password-error', 'Passwords do not match');
            isValid = false;
        } else {
            document.getElementById('confirm-password-error').classList.add('hidden');
        }
        
        if (!isValid) return;
        
        // Here would be an API call to change the password
        // For now, we'll just simulate success
        setTimeout(() => {
            // Reset form
            this.changePasswordForm.reset();
            this.showToast('Password changed successfully');
        }, 500);
    }

    /**
     * Logout from all devices
     */
    logoutAllDevices() {
        // Here would be an API call to logout from all devices
        // For now, we'll just simulate success
        setTimeout(() => {
            this.showToast('Logged out from all devices');
        }, 500);
    }

    /**
     * Open the delete account confirmation modal
     */
    openDeleteModal() {
        this.deleteAccountModal.classList.remove('hidden');
    }

    /**
     * Close the delete account confirmation modal
     */
    closeDeleteModal() {
        this.deleteAccountModal.classList.add('hidden');
        this.deleteConfirmPassword.value = '';
        document.getElementById('delete-password-error').classList.add('hidden');
    }

    /**
     * Delete the user's account
     */
    deleteAccount() {
        // Validate password
        if (!this.deleteConfirmPassword.value.trim()) {
            this.showError('delete-password-error', 'Please enter your password to confirm');
            return;
        }
        
        // Here would be an API call to delete the account
        // For now, we'll just simulate success and redirect to home
        setTimeout(() => {
            window.location.href = 'index.html';
        }, 500);
    }

    /**
     * Show an error message
     * @param {string} elementId - The ID of the error element
     * @param {string} message - The error message
     */
    showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }

    /**
     * Show a toast notification
     * @param {string} message - The notification message
     */
    showToast(message) {
        const toast = document.getElementById('toast-notification');
        toast.textContent = message;
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    /**
     * Redirect to login
     */
    redirectToLogin() {
        // Trigger the auth modal to open
        const authStateEvent = new CustomEvent('openAuthModal', {
            detail: { mode: 'login' }
        });
        document.dispatchEvent(authStateEvent);
    }

    /**
     * Check if user is authenticated
     */
    checkAuth() {
        // For demo purposes, we'll simulate a user
        // In a real app, this would check with the auth system
        this.simulateUser();
    }

    /**
     * Handle authentication state changes
     * @param {Object} user - The authenticated user data
     */
    handleAuthChange(user) {
        if (user) {
            this.currentUser = user;
            this.showProfileContent();
            this.updateProfileView();
        } else {
            this.currentUser = null;
            this.hideProfileContent();
        }
    }

    /**
     * Show the profile content
     */
    showProfileContent() {
        this.profileContentEl.classList.remove('hidden');
        this.notLoggedInEl.classList.add('hidden');
    }

    /**
     * Hide the profile content
     */
    hideProfileContent() {
        this.profileContentEl.classList.add('hidden');
        this.notLoggedInEl.classList.remove('hidden');
    }

    /**
     * Update the profile view with user data
     */
    updateProfileView() {
        // Update profile header
        this.profileUsername.textContent = this.currentUser.username || 'Username';
        this.profileMemberSince.textContent = `Member since: ${this.formatMemberSince(this.currentUser.createdAt)}`;
        this.profileGamesPlayed.textContent = this.currentUser.gamesPlayed || 0;
        this.profileAchievements.textContent = this.currentUser.achievements || 0;
        
        if (this.currentUser.avatar) {
            this.userAvatar.src = this.currentUser.avatar;
        }
        
        // Update profile info
        this.viewUsername.textContent = this.currentUser.username || 'Username';
        this.viewEmail.textContent = this.currentUser.email || 'email@example.com';
        this.viewDisplayName.textContent = this.currentUser.displayName || 'Display Name';
        this.viewCountry.textContent = this.currentUser.country || 'Not specified';
        this.viewBio.textContent = this.currentUser.bio || 'No bio added yet.';
    }

    /**
     * Format the member since date
     * @param {string} dateString - ISO date string
     * @returns {string} - Formatted date
     */
    formatMemberSince(dateString) {
        if (!dateString) return 'Jan 2023';
        
        const date = new Date(dateString);
        const month = date.toLocaleString('default', { month: 'short' });
        const year = date.getFullYear();
        
        return `${month} ${year}`;
    }

    /**
     * Simulate a user for demo purposes
     */
    simulateUser() {
        // Simulate an authenticated user
        const mockUser = {
            id: '12345',
            username: 'gamerlover',
            email: 'gamer@example.com',
            displayName: 'Gamer Lover',
            country: 'US',
            bio: 'I love playing browser games in my free time. My favorites are puzzle and strategy games.',
            avatar: 'assets/images/avatars/avatar1.png',
            createdAt: '2023-01-15T00:00:00',
            gamesPlayed: 42,
            achievements: 15
        };
        
        // Dispatch auth state change event
        const authStateEvent = new CustomEvent('authStateChanged', {
            detail: mockUser
        });
        document.dispatchEvent(authStateEvent);
    }
}

// Initialize profile manager when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.profileManager = new ProfileManager();
}); 