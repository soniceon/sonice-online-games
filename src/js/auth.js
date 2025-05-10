// Auth System for Sonice Games
const auth = {
    currentUser: null,
    
    // Get user from localStorage on page load
    init() {
        const storedUser = localStorage.getItem('currentUser');
        if (storedUser) {
            this.currentUser = JSON.parse(storedUser);
            this.updateUIForUser();
        }
    },
    
    // Register a new user
    register(username, email, password) {
        // In a real system, you would make an API call here
        // For this demo, we'll just store in localStorage
        const userId = 'user_' + Date.now();
        const newUser = {
            id: userId,
            username,
            email,
            password, // Note: In a real system, never store plain text passwords
            createdAt: new Date().toISOString()
        };
        
        // Store user info
        localStorage.setItem(`user_${email}`, JSON.stringify(newUser));
        
        // Log in the user after registration
        this.login(email, password);
        
        return newUser;
    },
    
    // Log in a user
    login(email, password) {
        // In a real system, you would validate with an API call
        const storedUser = localStorage.getItem(`user_${email}`);
        
        if (!storedUser) {
            throw new Error('User not found');
        }
        
        const user = JSON.parse(storedUser);
        
        if (user.password !== password) {
            throw new Error('Invalid password');
        }
        
        // Store current user
        this.currentUser = {
            id: user.id,
            username: user.username,
            email: user.email,
            createdAt: user.createdAt
        };
        
        localStorage.setItem('currentUser', JSON.stringify(this.currentUser));
        
        // Update UI
        this.updateUIForUser();
        
        // Dispatch auth state change event
        const event = new CustomEvent('authStateChanged', { detail: { user: this.currentUser } });
        document.dispatchEvent(event);
        
        return this.currentUser;
    },
    
    // Log out the current user
    logout() {
        this.currentUser = null;
        localStorage.removeItem('currentUser');
        
        // Update UI
        this.updateUIForUser();
        
        // Dispatch auth state change event
        const event = new CustomEvent('authStateChanged', { detail: { user: null } });
        document.dispatchEvent(event);
    },
    
    // Update UI elements based on authentication state
    updateUIForUser() {
        const loginButton = document.getElementById('loginButton');
        const authButtonText = document.querySelector('#loginButton span');
        const authButtonAvatar = document.querySelector('#loginButton img');
        
        if (loginButton) {
            if (this.currentUser) {
                // User is logged in
                if (authButtonText) {
                    authButtonText.textContent = this.currentUser.username;
                }
                
                if (authButtonAvatar) {
                    // You could use user's profile pic if available
                    authButtonAvatar.src = '/assets/images/default-avatar.png';
                }
                
                // Add dropdown menu functionality
                loginButton.addEventListener('click', this.toggleUserMenu);
            } else {
                // User is logged out
                if (authButtonText) {
                    authButtonText.textContent = 'Login';
                }
                
                if (authButtonAvatar) {
                    authButtonAvatar.src = '/assets/images/default-avatar.png';
                }
                
                // Remove dropdown menu and add login modal functionality
                loginButton.removeEventListener('click', this.toggleUserMenu);
                loginButton.addEventListener('click', this.showAuthModal);
            }
        }
    },
    
    // Toggle user dropdown menu
    toggleUserMenu(e) {
        const userMenu = document.getElementById('userMenu');
        if (!userMenu) {
            // Create the menu if it doesn't exist
            const menu = document.createElement('div');
            menu.id = 'userMenu';
            menu.className = 'user-menu absolute top-full right-0 mt-2 w-48 bg-dark-lighter rounded-lg shadow-lg py-2 z-50';
            menu.innerHTML = `
                <a href="/profile.html" class="block px-4 py-2 text-white hover:bg-gray-700">Profile</a>
                <a href="/favorites.html" class="block px-4 py-2 text-white hover:bg-gray-700">Favorites</a>
                <a href="/recent.html" class="block px-4 py-2 text-white hover:bg-gray-700">Recently Played</a>
                <div class="border-t border-gray-700 my-1"></div>
                <a href="#" id="logoutButton" class="block px-4 py-2 text-white hover:bg-gray-700">Logout</a>
            `;
            document.body.appendChild(menu);
            
            // Position the menu
            const buttonRect = e.currentTarget.getBoundingClientRect();
            menu.style.top = (buttonRect.bottom + window.scrollY) + 'px';
            menu.style.right = (window.innerWidth - buttonRect.right) + 'px';
            
            // Add logout functionality
            document.getElementById('logoutButton').addEventListener('click', (e) => {
                e.preventDefault();
                auth.logout();
                menu.remove();
            });
            
            // Close menu when clicking outside
            const closeMenu = (e) => {
                if (!menu.contains(e.target) && e.target !== document.getElementById('loginButton')) {
                    menu.remove();
                    document.removeEventListener('click', closeMenu);
                }
            };
            
            // Use setTimeout to avoid immediate closure
            setTimeout(() => {
                document.addEventListener('click', closeMenu);
            }, 0);
        } else {
            userMenu.remove();
        }
    },
    
    // Show authentication modal
    showAuthModal() {
        const modal = document.createElement('div');
        modal.id = 'authModal';
        modal.className = 'fixed inset-0 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="fixed inset-0 bg-black opacity-70"></div>
            <div class="auth-modal bg-dark-lighter rounded-lg shadow-xl p-6 max-w-md w-full relative z-10">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-white">Sign In</h2>
                    <button id="closeAuthModal" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="auth-tabs flex border-b border-gray-700 mb-4">
                    <button id="loginTab" class="active-tab flex-1 py-2 text-center text-white font-medium">Login</button>
                    <button id="registerTab" class="flex-1 py-2 text-center text-gray-400 font-medium">Register</button>
                        </div>
                
                <div id="loginForm" class="auth-form">
                    <div class="mb-4">
                        <label class="block text-white text-sm font-medium mb-2" for="email">Email</label>
                        <input id="loginEmail" type="email" class="w-full px-3 py-2 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-primary" placeholder="Enter your email">
                        <p id="loginEmailError" class="text-red-500 text-xs mt-1 hidden">Invalid email format</p>
                        </div>
                    
                    <div class="mb-6">
                        <label class="block text-white text-sm font-medium mb-2" for="password">Password</label>
                        <input id="loginPassword" type="password" class="w-full px-3 py-2 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-primary" placeholder="Enter your password">
                        <p id="loginPasswordError" class="text-red-500 text-xs mt-1 hidden">Password is required</p>
                        </div>
                    
                    <p id="loginError" class="text-red-500 text-sm mb-4 hidden">Invalid email or password</p>
                    
                    <button id="loginSubmit" class="w-full bg-blue-primary hover:bg-blue-secondary text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Sign In
                        </button>
                        </div>
                
                <div id="registerForm" class="auth-form hidden">
                    <div class="mb-4">
                        <label class="block text-white text-sm font-medium mb-2" for="username">Username</label>
                        <input id="registerUsername" type="text" class="w-full px-3 py-2 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-primary" placeholder="Choose a username">
                        <p id="registerUsernameError" class="text-red-500 text-xs mt-1 hidden">Username is required</p>
                        </div>
                    
                    <div class="mb-4">
                        <label class="block text-white text-sm font-medium mb-2" for="email">Email</label>
                        <input id="registerEmail" type="email" class="w-full px-3 py-2 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-primary" placeholder="Enter your email">
                        <p id="registerEmailError" class="text-red-500 text-xs mt-1 hidden">Invalid email format</p>
                        </div>
                    
                    <div class="mb-6">
                        <label class="block text-white text-sm font-medium mb-2" for="password">Password</label>
                        <input id="registerPassword" type="password" class="w-full px-3 py-2 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-primary" placeholder="Create a password">
                        <p id="registerPasswordError" class="text-red-500 text-xs mt-1 hidden">Password must be at least 6 characters</p>
                        </div>
                    
                    <p id="registerError" class="text-red-500 text-sm mb-4 hidden">Error creating account</p>
                    
                    <button id="registerSubmit" class="w-full bg-blue-primary hover:bg-blue-secondary text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Create Account
                        </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);

        // Modal interactions
        document.getElementById('closeAuthModal').addEventListener('click', () => {
            modal.remove();
        });
        
        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
        
        // Tab switching
        document.getElementById('loginTab').addEventListener('click', () => {
            document.getElementById('loginTab').classList.add('active-tab');
            document.getElementById('loginTab').classList.remove('text-gray-400');
            document.getElementById('loginTab').classList.add('text-white');
            
            document.getElementById('registerTab').classList.remove('active-tab');
            document.getElementById('registerTab').classList.add('text-gray-400');
            document.getElementById('registerTab').classList.remove('text-white');
            
            document.getElementById('loginForm').classList.remove('hidden');
            document.getElementById('registerForm').classList.add('hidden');
        });
        
        document.getElementById('registerTab').addEventListener('click', () => {
            document.getElementById('registerTab').classList.add('active-tab');
            document.getElementById('registerTab').classList.remove('text-gray-400');
            document.getElementById('registerTab').classList.add('text-white');
            
            document.getElementById('loginTab').classList.remove('active-tab');
            document.getElementById('loginTab').classList.add('text-gray-400');
            document.getElementById('loginTab').classList.remove('text-white');
            
            document.getElementById('registerForm').classList.remove('hidden');
            document.getElementById('loginForm').classList.add('hidden');
        });
        
        // Login form submission
        document.getElementById('loginSubmit').addEventListener('click', () => {
            const email = document.getElementById('loginEmail').value.trim();
            const password = document.getElementById('loginPassword').value;
            
            // Reset errors
            document.getElementById('loginEmailError').classList.add('hidden');
            document.getElementById('loginPasswordError').classList.add('hidden');
            document.getElementById('loginError').classList.add('hidden');
        
            // Validate
        let isValid = true;
        
            if (!email || !email.includes('@')) {
                document.getElementById('loginEmailError').classList.remove('hidden');
            isValid = false;
        }
        
            if (!password) {
                document.getElementById('loginPasswordError').classList.remove('hidden');
            isValid = false;
        }
        
            if (isValid) {
                try {
                    auth.login(email, password);
                    modal.remove();
                    
                    // Show success notification
                    showToast('Successfully logged in!');
                } catch (error) {
                    document.getElementById('loginError').textContent = error.message;
                    document.getElementById('loginError').classList.remove('hidden');
                }
            }
        });
        
        // Register form submission
        document.getElementById('registerSubmit').addEventListener('click', () => {
            const username = document.getElementById('registerUsername').value.trim();
            const email = document.getElementById('registerEmail').value.trim();
            const password = document.getElementById('registerPassword').value;
            
            // Reset errors
            document.getElementById('registerUsernameError').classList.add('hidden');
            document.getElementById('registerEmailError').classList.add('hidden');
            document.getElementById('registerPasswordError').classList.add('hidden');
            document.getElementById('registerError').classList.add('hidden');
            
            // Validate
        let isValid = true;
        
            if (!username) {
                document.getElementById('registerUsernameError').classList.remove('hidden');
            isValid = false;
        }
        
            if (!email || !email.includes('@')) {
                document.getElementById('registerEmailError').classList.remove('hidden');
            isValid = false;
        }
        
            if (!password || password.length < 6) {
                document.getElementById('registerPasswordError').classList.remove('hidden');
            isValid = false;
        }
        
            if (isValid) {
                try {
                    // Check if email already exists
                    if (localStorage.getItem(`user_${email}`)) {
                        document.getElementById('registerError').textContent = 'Email already in use';
                        document.getElementById('registerError').classList.remove('hidden');
            return;
        }
        
                    auth.register(username, email, password);
                    modal.remove();
                    
                    // Show success notification
                    showToast('Account created successfully!');
                } catch (error) {
                    document.getElementById('registerError').textContent = error.message;
                    document.getElementById('registerError').classList.remove('hidden');
                }
            }
        });
    }
};

// Initialize auth system on page load
document.addEventListener('DOMContentLoaded', () => {
    auth.init();
    
    // Add styles for auth elements
    const style = document.createElement('style');
    style.textContent = `
        .active-tab {
            border-bottom: 2px solid #0EA5E9;
            color: white;
        }
        .user-menu {
            background: #1A1B1F;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
        }
    `;
    document.head.appendChild(style);
});

// Expose methods globally
window.auth = auth; 