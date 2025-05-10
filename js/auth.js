/**
 * Authentication JavaScript for Sonice Online Games
 * Handles login, registration, and user state
 */

// Auth System for Sonice Games
document.addEventListener('DOMContentLoaded', () => {
    // Migrate legacy user data if exists
    migrateUserData();
    
    // Initialize authentication system
    initAuth();
    
    // Setup UI interaction
    setupAuthUI();
});

// Migrate old format user data to new format
function migrateUserData() {
    try {
        // Check for legacy current user
        const legacyUser = localStorage.getItem('currentUser');
        if (legacyUser) {
            // Convert to new format
            const userData = JSON.parse(legacyUser);
            
            // Only migrate if not already migrated
            if (!localStorage.getItem(`sonice_user_${userData.email}`)) {
                console.log('Migrating legacy user data...');
                
                // Generate a password hash if missing
                if (!userData.passwordHash && userData.password) {
                    userData.passwordHash = CryptoJS.SHA256(userData.password).toString();
                }
                
                // Create proper user structure
                const migratedUser = {
                    id: userData.id || ('user_' + Date.now()),
                    username: userData.username,
                    email: userData.email,
                    passwordHash: userData.passwordHash || userData.password, // Fallback for legacy data
                    avatar: userData.avatar || generateAvatar(userData.username),
                    createdAt: userData.createdAt || new Date().toISOString(),
                    favorites: userData.favorites || [],
                    recentlyPlayed: userData.recentlyPlayed || []
                };
                
                // Store in new format
                localStorage.setItem(`sonice_user_${userData.email}`, JSON.stringify(migratedUser));
                localStorage.setItem('sonice_currentUser', JSON.stringify(migratedUser));
                
                // Add to users index
                let usersIndex = JSON.parse(localStorage.getItem('sonice_users_index') || '[]');
                if (!usersIndex.some(u => u.id === migratedUser.id)) {
                    usersIndex.push({
                        id: migratedUser.id,
                        email: migratedUser.email,
                        username: migratedUser.username
                    });
                    localStorage.setItem('sonice_users_index', JSON.stringify(usersIndex));
                }
                
                console.log('Legacy user data migration complete.');
            }
        }
        
        // Check for all legacy users in localStorage
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key && key.startsWith('user_') && !key.startsWith('sonice_user_')) {
                try {
                    const userData = JSON.parse(localStorage.getItem(key));
                    if (userData && userData.email && !localStorage.getItem(`sonice_user_${userData.email}`)) {
                        // This is a legacy user that hasn't been migrated
                        
                        // Generate a password hash if missing
                        if (!userData.passwordHash && userData.password) {
                            userData.passwordHash = CryptoJS.SHA256(userData.password).toString();
                        }
                        
                        // Create proper user structure
                        const migratedUser = {
                            id: userData.id || key,
                            username: userData.username,
                            email: userData.email,
                            passwordHash: userData.passwordHash || userData.password, // Fallback for legacy data
                            avatar: userData.avatar || generateAvatar(userData.username),
                            createdAt: userData.createdAt || new Date().toISOString(),
                            favorites: userData.favorites || [],
                            recentlyPlayed: userData.recentlyPlayed || []
                        };
                        
                        // Store in new format
                        localStorage.setItem(`sonice_user_${userData.email}`, JSON.stringify(migratedUser));
                        
                        // Add to users index
                        let usersIndex = JSON.parse(localStorage.getItem('sonice_users_index') || '[]');
                        if (!usersIndex.some(u => u.id === migratedUser.id)) {
                            usersIndex.push({
                                id: migratedUser.id,
                                email: migratedUser.email,
                                username: migratedUser.username
                            });
                            localStorage.setItem('sonice_users_index', JSON.stringify(usersIndex));
                        }
                    }
                } catch (err) {
                    console.error(`Error migrating legacy user: ${key}`, err);
                }
            }
        }
    } catch (err) {
        console.error('Error during user data migration:', err);
    }
}

// Generate avatar URL based on username
function generateAvatar(username) {
    // Get a consistent but random number from the username
    const getNumberFromString = (str) => {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            hash = ((hash << 5) - hash) + str.charCodeAt(i);
            hash |= 0; // Convert to 32bit integer
        }
        return Math.abs(hash);
    };
    
    // Available avatar APIs and styles
    const avatarOptions = [
        // DiceBear Avatars - Updated API URLs for v7
        {
            type: 'dicebear',
            styles: ['initials', 'shapes', 'thumbs', 'bottts', 'pixel-art', 'lorelei']
        },
        // Boring Avatars - https://boringavatars.com
        {
            type: 'boring',
            styles: ['beam', 'marble', 'sunset', 'bauhaus', 'ring']
        }
    ];
    
    // Color palettes for avatars
    const colorPalettes = [
        ['4f46e5', '7c3aed', '0ea5e9', '3b82f6'], // Blues & Purples
        ['10b981', '059669', '047857', '064e3b'], // Greens
        ['f59e0b', 'fbbf24', 'f97316', 'fb923c'], // Oranges
        ['ef4444', 'f87171', 'dc2626', 'b91c1c'], // Reds
        ['8b5cf6', 'a78bfa', 'c4b5fd', '7c3aed'], // Purples
        ['3b82f6', '60a5fa', '93c5fd', '2563eb']  // Blues
    ];
    
    try {
        // Get consistent random values for this username
        const hash = getNumberFromString(username);
        const apiIndex = hash % avatarOptions.length;
        const api = avatarOptions[apiIndex];
        const styleIndex = (hash >> 4) % api.styles.length;
        const style = api.styles[styleIndex];
        const paletteIndex = (hash >> 8) % colorPalettes.length;
        const colorPalette = colorPalettes[paletteIndex];
        
        // Generate avatar URL based on the selected API and style
        if (api.type === 'dicebear') {
            const bgColor = colorPalette[hash % colorPalette.length];
            // 使用最新的 DiceBear API v7 地址
            return `https://api.dicebear.com/7.x/${style}/svg?seed=${encodeURIComponent(username)}&backgroundColor=${bgColor}`;
        } else if (api.type === 'boring') {
            // Convert colors to boring avatars format (no # prefix)
            const colors = colorPalette.join(',');
            return `https://source.boringavatars.com/${style}/120/${encodeURIComponent(username)}?colors=${colors}`;
        }
    } catch (error) {
        console.error("Error generating avatar:", error);
    }
    
    // Fallback to UI Avatars if other APIs fail
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(username)}&background=random&color=fff&bold=true&format=svg`;
}

// Auth system core functionality
const auth = {
    currentUser: null,
    
    // Get user from localStorage on page load
    init() {
        const storedUser = localStorage.getItem('sonice_currentUser');
        if (storedUser) {
            try {
                this.currentUser = JSON.parse(storedUser);
                this.updateUIForUser();
                return true;
            } catch (e) {
                console.error('Error parsing stored user data:', e);
                localStorage.removeItem('sonice_currentUser');
            }
        }
        return false;
    },
    
    // Register a new user
    register(username, email, password) {
        // Hash password for basic security
        const hashedPassword = CryptoJS.SHA256(password).toString();
        
        const userId = 'user_' + Date.now();
        
        // Generate avatar based on username with added randomness
        const randomSeed = username + '_' + Math.random().toString(36).substring(2, 10);
        const avatarUrl = generateAvatar(randomSeed);
        
        // Ensure we got a valid avatar URL, fallback if not
        let finalAvatarUrl = avatarUrl;
        if (!avatarUrl || avatarUrl.trim() === '') {
            console.warn("Avatar generation failed, using fallback");
            finalAvatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(username)}&background=random&color=fff`;
        }
        
        const newUser = {
            id: userId,
            username,
            email,
            passwordHash: hashedPassword,
            avatar: finalAvatarUrl,
            createdAt: new Date().toISOString(),
            favorites: [],
            recentlyPlayed: []
        };
        
        // Check if user already exists
        if (localStorage.getItem(`sonice_user_${email}`)) {
            throw new Error('User with this email already exists');
        }
        
        // Store user info
        localStorage.setItem(`sonice_user_${email}`, JSON.stringify(newUser));
        
        // Store in users index for searching
        let usersIndex = JSON.parse(localStorage.getItem('sonice_users_index') || '[]');
        usersIndex.push({id: userId, email, username});
        localStorage.setItem('sonice_users_index', JSON.stringify(usersIndex));
        
        // Log in the user after registration
        return this.login(email, password);
    },
    
    // Log in a user
    login(email, password) {
        // Hash password to check against stored hash
        const hashedPassword = CryptoJS.SHA256(password).toString();
        
        // Get user data
        const storedUserData = localStorage.getItem(`sonice_user_${email}`);
        
        if (!storedUserData) {
            // Check if legacy user exists
            const legacyUser = this.checkLegacyUser(email, password);
            if (legacyUser) {
                return legacyUser; // Legacy user login successful
            }
            throw new Error('User not found');
        }
        
        const userData = JSON.parse(storedUserData);
        
        // Check password - first try hashed password
        if (userData.passwordHash !== hashedPassword) {
            // If that fails, try checking against plain password (legacy format)
            if (userData.password === password) {
                // Update to secure format if plain password matched
                userData.passwordHash = hashedPassword;
                delete userData.password;
                localStorage.setItem(`sonice_user_${email}`, JSON.stringify(userData));
            } else {
                throw new Error('Invalid password');
            }
        }
        
        // Ensure avatar exists, generate one if not
        if (!userData.avatar) {
            userData.avatar = generateAvatar(userData.username);
            // Update user data with the new avatar
            localStorage.setItem(`sonice_user_${email}`, JSON.stringify(userData));
        }
        
        // Create currentUser without sensitive data
        this.currentUser = {
            id: userData.id,
            username: userData.username,
            email: userData.email,
            avatar: userData.avatar,
            createdAt: userData.createdAt,
            favorites: userData.favorites || [],
            recentlyPlayed: userData.recentlyPlayed || []
        };
        
        // Store current user in session
        localStorage.setItem('sonice_currentUser', JSON.stringify(this.currentUser));
        
        // Update UI for logged in state
        this.updateUIForUser();
        
        // Dispatch auth state change event
        const event = new CustomEvent('authStateChanged', { 
            detail: { user: this.currentUser } 
        });
        document.dispatchEvent(event);
        
        // Update favorites count in UI
        this.updateFavoritesCount();
        
        return this.currentUser;
    },
    
    // Check for legacy user login
    checkLegacyUser(email, password) {
        try {
            // Try to find a legacy user by email
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key && key.startsWith('user_') && !key.startsWith('sonice_user_')) {
                    const userData = JSON.parse(localStorage.getItem(key));
                    if (userData && userData.email === email) {
                        // Found legacy user, check password
                        if (userData.password === password) {
                            console.log('Legacy user login successful, migrating...');
                            
                            // Create migrated user
                            const hashedPassword = CryptoJS.SHA256(password).toString();
                            const migratedUser = {
                                id: userData.id || key,
                                username: userData.username,
                                email: userData.email,
                                passwordHash: hashedPassword,
                                avatar: userData.avatar || generateAvatar(userData.username),
                                createdAt: userData.createdAt || new Date().toISOString(),
                                favorites: userData.favorites || [],
                                recentlyPlayed: userData.recentlyPlayed || []
                            };
                            
                            // Save in new format
                            localStorage.setItem(`sonice_user_${email}`, JSON.stringify(migratedUser));
                            
                            // Add to users index
                            let usersIndex = JSON.parse(localStorage.getItem('sonice_users_index') || '[]');
                            if (!usersIndex.some(u => u.id === migratedUser.id)) {
                                usersIndex.push({
                                    id: migratedUser.id,
                                    email: migratedUser.email,
                                    username: migratedUser.username
                                });
                                localStorage.setItem('sonice_users_index', JSON.stringify(usersIndex));
                            }
                            
                            // Set as current user
                            this.currentUser = {
                                id: migratedUser.id,
                                username: migratedUser.username,
                                email: migratedUser.email,
                                avatar: migratedUser.avatar,
                                createdAt: migratedUser.createdAt,
                                favorites: migratedUser.favorites,
                                recentlyPlayed: migratedUser.recentlyPlayed
                            };
                            
                            // Store current user in session
                            localStorage.setItem('sonice_currentUser', JSON.stringify(this.currentUser));
                            
                            // Update UI and dispatch event
                            this.updateUIForUser();
                            const event = new CustomEvent('authStateChanged', { 
                                detail: { user: this.currentUser } 
                            });
                            document.dispatchEvent(event);
                            
                            // Update favorites count
                            this.updateFavoritesCount();
                            
                            return this.currentUser;
                        }
                    }
                }
            }
        } catch (err) {
            console.error('Error checking legacy user:', err);
        }
        
        return null;
    },
    
    // Log out the current user
    logout() {
        this.currentUser = null;
        localStorage.removeItem('sonice_currentUser');
        
        // Update UI to logged out state
        this.updateUIForUser();
        
        // Dispatch auth state change event
        const event = new CustomEvent('authStateChanged', { 
            detail: { user: null } 
        });
        document.dispatchEvent(event);
        
        // Clear any user-specific UI
        this.updateFavoritesCount();
        
        // Show toast notification
        showToast('Successfully logged out', 'success');
    },
    
    // Update UI elements based on authentication state
    updateUIForUser() {
        const userMenuButton = document.getElementById('userMenuButton');
        const userDisplayName = document.getElementById('userDisplayName');
        const userAvatar = document.getElementById('userAvatar');
        const guestMenuItems = document.getElementById('guestMenuItems');
        const userMenuItems = document.getElementById('userMenuItems');
        
        if (!userMenuButton || !userDisplayName || !userAvatar || !guestMenuItems || !userMenuItems) {
            return; // UI elements not found
        }
        
        if (this.currentUser) {
            // User is logged in
            userDisplayName.textContent = this.currentUser.username;
            userAvatar.src = this.currentUser.avatar;
            
            // Show user menu items, hide guest menu items
            guestMenuItems.classList.add('hidden');
            userMenuItems.classList.remove('hidden');
        } else {
            // User is logged out
            userDisplayName.textContent = 'Login';
            userAvatar.src = '/assets/images/user/default-avatar.png';
            
            // Show guest menu items, hide user menu items
            guestMenuItems.classList.remove('hidden');
            userMenuItems.classList.add('hidden');
        }
    },
    
    // Update the favorites count in the UI
    updateFavoritesCount() {
        const favoritesCountElements = document.querySelectorAll('.favorites-count');
        const count = this.currentUser ? this.currentUser.favorites.length : 0;
        
        favoritesCountElements.forEach(element => {
            element.textContent = count.toString();
        });
    },
    
    // Add a game to user favorites
    addToFavorites(gameId) {
        if (!this.currentUser) {
            showAuthModal('login');
            return false;
        }
        
        if (!this.currentUser.favorites.includes(gameId)) {
            this.currentUser.favorites.push(gameId);
            this.updateUserData();
            this.updateFavoritesCount();
            return true;
        }
        
        return false;
    },
    
    // Remove a game from user favorites
    removeFromFavorites(gameId) {
        if (!this.currentUser) return false;
        
        const index = this.currentUser.favorites.indexOf(gameId);
        if (index !== -1) {
            this.currentUser.favorites.splice(index, 1);
            this.updateUserData();
            this.updateFavoritesCount();
            return true;
        }
        
        return false;
    },
    
    // Check if a game is in user favorites
    isInFavorites(gameId) {
        return this.currentUser && this.currentUser.favorites.includes(gameId);
    },
    
    // Add a game to recently played
    addToRecentlyPlayed(gameData) {
        if (!this.currentUser) return false;
        
        // Remove game if it already exists in recently played
        const existingIndex = this.currentUser.recentlyPlayed.findIndex(
            game => game.id === gameData.id
        );
        
        if (existingIndex !== -1) {
            this.currentUser.recentlyPlayed.splice(existingIndex, 1);
        }
        
        // Add game to the beginning of the array
        this.currentUser.recentlyPlayed.unshift({
            id: gameData.id,
            title: gameData.title,
            thumbnail: gameData.thumbnail,
            url: gameData.url,
            playedAt: new Date().toISOString()
        });
        
        // Keep only the latest 20 games
        if (this.currentUser.recentlyPlayed.length > 20) {
            this.currentUser.recentlyPlayed = this.currentUser.recentlyPlayed.slice(0, 20);
        }
        
        this.updateUserData();
        return true;
    },
    
    // Update user data in localStorage
    updateUserData() {
        if (!this.currentUser) return;
        
        // Update current user in session storage
        localStorage.setItem('sonice_currentUser', JSON.stringify(this.currentUser));
        
        // Get full user data
        const userData = JSON.parse(localStorage.getItem(`sonice_user_${this.currentUser.email}`));
        
        // Update specific fields
        userData.favorites = this.currentUser.favorites;
        userData.recentlyPlayed = this.currentUser.recentlyPlayed;
        userData.avatar = this.currentUser.avatar;
        
        // Save back to storage
        localStorage.setItem(`sonice_user_${this.currentUser.email}`, JSON.stringify(userData));
    },
    
    // Update user profile
    updateProfile(profileData) {
        if (!this.currentUser) return false;
        
        // Get full user data
        const userData = JSON.parse(localStorage.getItem(`sonice_user_${this.currentUser.email}`));
        
        // Update username if provided
        if (profileData.username) {
            this.currentUser.username = profileData.username;
            userData.username = profileData.username;
            
            // Update in users index
            let usersIndex = JSON.parse(localStorage.getItem('sonice_users_index') || '[]');
            const userIndex = usersIndex.findIndex(u => u.id === this.currentUser.id);
            if (userIndex !== -1) {
                usersIndex[userIndex].username = profileData.username;
                localStorage.setItem('sonice_users_index', JSON.stringify(usersIndex));
            }
            
            // Generate new avatar if username changed and no custom avatar provided
            if (!profileData.avatar) {
                const newAvatar = generateAvatar(profileData.username);
                this.currentUser.avatar = newAvatar;
                userData.avatar = newAvatar;
            }
        }
        
        // Update avatar if provided
        if (profileData.avatar) {
            this.currentUser.avatar = profileData.avatar;
            userData.avatar = profileData.avatar;
        }
        
        // Save changes
        localStorage.setItem(`sonice_user_${this.currentUser.email}`, JSON.stringify(userData));
        localStorage.setItem('sonice_currentUser', JSON.stringify(this.currentUser));
        
        // Update UI
        this.updateUIForUser();
        
        return true;
    },
    
    // Change password
    changePassword(currentPassword, newPassword) {
        if (!this.currentUser) return false;
        
        // Get full user data
        const userData = JSON.parse(localStorage.getItem(`sonice_user_${this.currentUser.email}`));
        
        // Verify current password
        const currentPasswordHash = CryptoJS.SHA256(currentPassword).toString();
        if (userData.passwordHash !== currentPasswordHash) {
            throw new Error('Current password is incorrect');
        }
        
        // Update password
        const newPasswordHash = CryptoJS.SHA256(newPassword).toString();
        userData.passwordHash = newPasswordHash;
        
        // Save changes
        localStorage.setItem(`sonice_user_${this.currentUser.email}`, JSON.stringify(userData));
        
        return true;
    },
    
    // Generate a new random avatar for current user
    regenerateAvatar() {
        if (!this.currentUser) return false;
        
        try {
            // Create a randomized string to ensure unique avatar
            const randomSeed = this.currentUser.username + '_' + Date.now() + '_' + Math.random().toString(36).substring(2, 15);
            const newAvatar = generateAvatar(randomSeed);
            
            // Ensure we got a valid avatar URL
            if (!newAvatar || newAvatar.trim() === '') {
                console.warn("Avatar regeneration failed, using fallback");
                throw new Error("Failed to generate avatar");
            }
            
            // Update avatar in user data
            this.currentUser.avatar = newAvatar;
            
            // Get full user data
            const userData = JSON.parse(localStorage.getItem(`sonice_user_${this.currentUser.email}`));
            userData.avatar = newAvatar;
            
            // Save changes
            localStorage.setItem(`sonice_user_${this.currentUser.email}`, JSON.stringify(userData));
            localStorage.setItem('sonice_currentUser', JSON.stringify(this.currentUser));
            
            // Update UI
            this.updateUIForUser();
            
            // Show toast notification
            showToast('Avatar updated successfully', 'success');
            
            return newAvatar;
        } catch (error) {
            console.error("Error regenerating avatar:", error);
            showToast('Failed to generate avatar. Please try again.', 'error');
            return false;
        }
    },
    
    // Generate multiple avatar options for selection
    generateAvatarOptions(count = 6) {
        if (!this.currentUser) return [];
        
        const options = [];
        try {
            for (let i = 0; i < count; i++) {
                // Create different random seeds for each avatar, using timestamp to ensure uniqueness
                const randomSeed = this.currentUser.username + '_option_' + i + '_' + Date.now() + '_' + Math.random().toString(36).substring(2, 8);
                const avatar = generateAvatar(randomSeed);
                
                // Ensure we got a valid avatar URL
                if (avatar && avatar.trim() !== '') {
                    options.push(avatar);
                } else {
                    // Fallback to UI Avatars if generation fails
                    const randomColor = Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
                    const fallbackAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(this.currentUser.username)}&background=${randomColor}&color=fff`;
                    options.push(fallbackAvatar);
                }
            }
        } catch (error) {
            console.error("Error generating avatar options:", error);
            // Fill with fallback avatars if needed
            const remaining = count - options.length;
            if (remaining > 0) {
                for (let i = 0; i < remaining; i++) {
                    const randomColor = Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
                    const fallbackAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(this.currentUser.username)}&background=${randomColor}&color=fff`;
                    options.push(fallbackAvatar);
                }
            }
        }
        
        return options;
    },
    
    // Show avatar selection modal
    showAvatarSelector() {
        if (!this.currentUser) return false;
        
        // Generate avatar options
        const avatarOptions = this.generateAvatarOptions();
        
        // Create modal for avatar selection
        const modal = document.createElement('div');
        modal.id = 'avatarSelectorModal';
        modal.className = 'fixed inset-0 flex items-center justify-center z-50';
        
        modal.innerHTML = `
            <div class="fixed inset-0 bg-black bg-opacity-70" id="avatarModalOverlay"></div>
            <div class="bg-dark-lighter rounded-lg shadow-xl p-6 max-w-md w-full relative z-10">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-white">Choose an Avatar</h2>
                    <button id="closeAvatarModal" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-3 gap-4 mb-6">
                    ${avatarOptions.map((avatar, index) => `
                        <div class="avatar-option cursor-pointer flex flex-col items-center">
                            <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-transparent hover:border-blue-primary transition-colors">
                                <img src="${avatar}" alt="Avatar option ${index + 1}" class="w-full h-full" data-avatar-url="${avatar}">
                            </div>
                        </div>
                    `).join('')}
                </div>
                
                <div class="text-center">
                    <button id="generateMoreAvatars" class="bg-gray-700 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition-colors">
                        Generate More Options
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Avatar selection event handlers
        setupAvatarSelectorHandlers();
        
        return true;
    }
};

// Initialize auth system
function initAuth() {
    auth.init();
}

// Setup UI interaction for user dropdown and auth modals
function setupAuthUI() {
    // User dropdown toggle
    const userMenuButton = document.getElementById('userMenuButton');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    
    if (userMenuButton && userDropdownMenu) {
        userMenuButton.addEventListener('click', () => {
            userDropdownMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (!userMenuButton.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                userDropdownMenu.classList.add('hidden');
            }
        });
    }
    // Login button click handler
    const loginBtn = document.getElementById('loginBtn');
    if (loginBtn) {
        loginBtn.onclick = null; // 移除 HTML onclick
        loginBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showAuthModal('login');
        });
    }
    
    // Register button click handler
    const registerBtn = document.getElementById('registerBtn');
    if (registerBtn) {
        registerBtn.addEventListener('click', () => {
            showAuthModal('register');
        });
    }
    
    // Change avatar button click handler
    const changeAvatarBtn = document.getElementById('changeAvatarBtn');
    if (changeAvatarBtn) {
        changeAvatarBtn.addEventListener('click', () => {
            userDropdownMenu.classList.add('hidden');
            auth.showAvatarSelector();
        });
    }
    
    // Logout button click handler
    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) {
        logoutButton.addEventListener('click', (e) => {
            e.preventDefault();
            auth.logout();
            userDropdownMenu.classList.add('hidden');
        });
    }
}

// Show authentication modal
function showAuthModal(tab = 'login') {
    var modal = document.getElementById('authModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    var loginForm = document.getElementById('loginForm');
    var registerForm = document.getElementById('registerForm');
    if (loginForm && registerForm) {
        loginForm.classList.toggle('hidden', tab !== 'login');
        registerForm.classList.toggle('hidden', tab !== 'register');
    }
}
window.showAuthModal = showAuthModal;

// 关闭弹窗按钮
const closeAuthModalBtn = document.getElementById('closeAuthModalBtn');
if (closeAuthModalBtn) {
    closeAuthModalBtn.addEventListener('click', function() {
        document.getElementById('authModal').classList.add('hidden');
    });
}

// Setup avatar selector event handlers
function setupAvatarSelectorHandlers() {
    // Close modal
    const closeBtn = document.getElementById('closeAvatarModal');
    const overlay = document.getElementById('avatarModalOverlay');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', closeAvatarModal);
    }
    
    if (overlay) {
        overlay.addEventListener('click', closeAvatarModal);
    }
    
    // Handle avatar selection
    const avatarImages = document.querySelectorAll('.avatar-option img');
    avatarImages.forEach(img => {
        img.addEventListener('click', (e) => {
            const avatarUrl = e.target.getAttribute('data-avatar-url');
            if (avatarUrl) {
                // Update user avatar
                auth.currentUser.avatar = avatarUrl;
                
                // Get full user data
                const userData = JSON.parse(localStorage.getItem(`sonice_user_${auth.currentUser.email}`));
                userData.avatar = avatarUrl;
                
                // Save changes
                localStorage.setItem(`sonice_user_${auth.currentUser.email}`, JSON.stringify(userData));
                localStorage.setItem('sonice_currentUser', JSON.stringify(auth.currentUser));
                
                // Update UI
                auth.updateUIForUser();
                
                // Close modal
                closeAvatarModal();
                
                // Show success notification
                showToast('Avatar updated successfully', 'success');
            }
        });
        
        // Add visual feedback on hover
        const avatarContainer = img.parentElement;
        img.addEventListener('mouseenter', () => {
            avatarContainer.classList.add('border-blue-primary');
        });
        
        img.addEventListener('mouseleave', () => {
            avatarContainer.classList.remove('border-blue-primary');
        });
    });
    
    // Generate more avatars
    const generateMoreBtn = document.getElementById('generateMoreAvatars');
    if (generateMoreBtn) {
        generateMoreBtn.addEventListener('click', () => {
            closeAvatarModal();
            setTimeout(() => {
                auth.showAvatarSelector();
            }, 300);
        });
    }
}

// Close avatar selection modal
function closeAvatarModal() {
    const modal = document.getElementById('avatarSelectorModal');
    if (modal) {
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Validate email format
function validateEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

// Display toast message
function showToast(message, type = 'info') {
    // Remove any existing toasts
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Create new toast
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    // Create icon based on type
    let icon = '';
    if (type === 'success') {
        icon = '<i class="fas fa-check-circle mr-2 text-green-500"></i>';
    } else if (type === 'error') {
        icon = '<i class="fas fa-exclamation-circle mr-2 text-red-500"></i>';
    } else {
        icon = '<i class="fas fa-info-circle mr-2 text-blue-500"></i>';
    }
    
    // Set toast content
    toast.innerHTML = `
        ${icon}
        <p>${message}</p>
        <button class="ml-auto text-gray-400 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to the document
    document.body.appendChild(toast);
    
    // Show the toast (small delay to allow for animation)
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    // Add click event to close button
    const closeButton = toast.querySelector('button');
    closeButton.addEventListener('click', () => {
        hideToast(toast);
    });
    
    // Automatically hide after 5 seconds
    setTimeout(() => {
        hideToast(toast);
    }, 5000);
    
    return toast;
}

function hideToast(toast) {
    toast.classList.remove('show');
    setTimeout(() => {
        toast.remove();
    }, 300); // Allow time for the transition to complete
}

// Helper to check if the user is authenticated
function isAuthenticated() {
    return auth.currentUser !== null;
}