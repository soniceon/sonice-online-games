// User Authentication System
class Auth {
    constructor() {
        this.users = JSON.parse(localStorage.getItem('users') || '[]');
        this.currentUser = JSON.parse(localStorage.getItem('currentUser') || 'null');
        this.tokenExpiryTime = localStorage.getItem('tokenExpiryTime') || null;
        
        // Check if token is expired
        if (this.tokenExpiryTime && new Date().getTime() > parseInt(this.tokenExpiryTime)) {
            this.logout(false); // Silent logout if token expired
        }
        
        this.initAuthUI();
        this.updateAuthState();
    }

    initAuthUI() {
        // Add auth modal to body
        const authModal = document.createElement('div');
        authModal.id = 'auth-modal';
        authModal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50';
        authModal.innerHTML = `
            <div class="bg-dark-lighter rounded-lg p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-white" id="auth-modal-title">Sign In</h2>
                    <button class="text-gray-400 hover:text-white" id="close-auth-modal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div id="auth-forms">
                    <!-- Login Form -->
                    <form id="login-form" class="space-y-4">
                        <div>
                            <label class="block text-gray-400 mb-2" for="login-email">Email</label>
                            <input type="email" id="login-email" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required>
                            <p class="text-red-500 text-sm mt-1 hidden" id="login-email-error"></p>
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2" for="login-password">Password</label>
                            <input type="password" id="login-password" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required>
                            <p class="text-red-500 text-sm mt-1 hidden" id="login-password-error"></p>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="remember-me" class="mr-2 bg-dark border border-gray-700 rounded">
                            <label for="remember-me" class="text-gray-400">Remember me for 30 days</label>
                        </div>
                        <button type="submit" class="w-full py-2 px-4 bg-purple-primary text-white rounded hover:bg-purple-600 transition-colors">
                            Sign In
                        </button>
                        <div class="flex justify-between">
                            <button type="button" class="text-blue-primary hover:text-blue-secondary text-sm" id="show-forgot-password">Forgot Password?</button>
                            <button type="button" class="text-blue-primary hover:text-blue-secondary text-sm" id="show-register">Register</button>
                        </div>
                    </form>

                    <!-- Register Form -->
                    <form id="register-form" class="space-y-4 hidden">
                        <div>
                            <label class="block text-gray-400 mb-2" for="register-username">Username</label>
                            <input type="text" id="register-username" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required>
                            <p class="text-red-500 text-sm mt-1 hidden" id="register-username-error"></p>
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2" for="register-email">Email</label>
                            <input type="email" id="register-email" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required>
                            <p class="text-red-500 text-sm mt-1 hidden" id="register-email-error"></p>
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2" for="register-password">Password</label>
                            <input type="password" id="register-password" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required>
                            <p class="text-red-500 text-sm mt-1 hidden" id="register-password-error"></p>
                            <div class="mt-1 text-xs text-gray-500">
                                Password must be at least 8 characters and include a number and a special character
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2" for="register-confirm-password">Confirm Password</label>
                            <input type="password" id="register-confirm-password" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required>
                            <p class="text-red-500 text-sm mt-1 hidden" id="register-confirm-password-error"></p>
                        </div>
                        <button type="submit" class="w-full py-2 px-4 bg-purple-primary text-white rounded hover:bg-purple-600 transition-colors">
                            Register
                        </button>
                        <p class="text-center text-gray-400">
                            Already have an account? 
                            <button type="button" class="text-blue-primary hover:text-blue-secondary" id="show-login">Sign In</button>
                        </p>
                    </form>
                    
                    <!-- Forgot Password Form -->
                    <form id="forgot-password-form" class="space-y-4 hidden">
                        <div>
                            <label class="block text-gray-400 mb-2" for="forgot-email">Email</label>
                            <input type="email" id="forgot-email" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required>
                            <p class="text-red-500 text-sm mt-1 hidden" id="forgot-email-error"></p>
                        </div>
                        <button type="submit" class="w-full py-2 px-4 bg-purple-primary text-white rounded hover:bg-purple-600 transition-colors">
                            Reset Password
                        </button>
                        <p class="text-center text-gray-400">
                            Remember your password? 
                            <button type="button" class="text-blue-primary hover:text-blue-secondary" id="back-to-login">Back to Sign In</button>
                        </p>
                    </form>
                    
                    <!-- Reset Password Form -->
                    <form id="reset-password-form" class="space-y-4 hidden">
                        <div>
                            <label class="block text-gray-400 mb-2" for="security-question">Security Question: What is your favorite color?</label>
                            <input type="text" id="security-answer" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required>
                            <p class="text-red-500 text-sm mt-1 hidden" id="security-answer-error"></p>
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2" for="new-password">New Password</label>
                            <input type="password" id="new-password" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required>
                            <p class="text-red-500 text-sm mt-1 hidden" id="new-password-error"></p>
                            <div class="mt-1 text-xs text-gray-500">
                                Password must be at least 8 characters and include a number and a special character
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2" for="confirm-new-password">Confirm New Password</label>
                            <input type="password" id="confirm-new-password" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required>
                            <p class="text-red-500 text-sm mt-1 hidden" id="confirm-new-password-error"></p>
                        </div>
                        <input type="hidden" id="reset-email">
                        <button type="submit" class="w-full py-2 px-4 bg-purple-primary text-white rounded hover:bg-purple-600 transition-colors">
                            Reset Password
                        </button>
                    </form>
                </div>
            </div>
        `;
        document.body.appendChild(authModal);

        // Event Listeners
        document.getElementById('close-auth-modal').addEventListener('click', () => this.hideAuthModal());
        document.getElementById('show-register').addEventListener('click', () => this.toggleAuthForms('register'));
        document.getElementById('show-login').addEventListener('click', () => this.toggleAuthForms('login'));
        document.getElementById('show-forgot-password').addEventListener('click', () => this.toggleAuthForms('forgot-password'));
        document.getElementById('back-to-login').addEventListener('click', () => this.toggleAuthForms('login'));
        document.getElementById('login-form').addEventListener('submit', (e) => this.handleLogin(e));
        document.getElementById('register-form').addEventListener('submit', (e) => this.handleRegister(e));
        document.getElementById('forgot-password-form').addEventListener('submit', (e) => this.handleForgotPassword(e));
        document.getElementById('reset-password-form').addEventListener('submit', (e) => this.handleResetPassword(e));
    }

    showAuthModal() {
        document.getElementById('auth-modal').classList.remove('hidden');
    }

    hideAuthModal() {
        document.getElementById('auth-modal').classList.add('hidden');
    }

    toggleAuthForms(form) {
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const forgotPasswordForm = document.getElementById('forgot-password-form');
        const resetPasswordForm = document.getElementById('reset-password-form');
        const modalTitle = document.getElementById('auth-modal-title');

        // Hide all forms first
        loginForm.classList.add('hidden');
        registerForm.classList.add('hidden');
        forgotPasswordForm.classList.add('hidden');
        resetPasswordForm.classList.add('hidden');
        
        // Show selected form
        if (form === 'register') {
            registerForm.classList.remove('hidden');
            modalTitle.textContent = 'Register';
        } else if (form === 'forgot-password') {
            forgotPasswordForm.classList.remove('hidden');
            modalTitle.textContent = 'Forgot Password';
        } else if (form === 'reset-password') {
            resetPasswordForm.classList.remove('hidden');
            modalTitle.textContent = 'Reset Password';
        } else {
            loginForm.classList.remove('hidden');
            modalTitle.textContent = 'Sign In';
        }
    }
    
    // Simple password hash function (for demo purposes)
    // In production, use a proper hashing library or API
    hashPassword(password) {
        // This is a simple hash for demonstration
        // Do NOT use this in production!
        let hash = 0;
        for (let i = 0; i < password.length; i++) {
            const char = password.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32bit integer
        }
        return hash.toString(16); // Convert to hex string
    }
    
    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    validatePassword(password) {
        // At least 8 characters, 1 number, and 1 special character
        const re = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,}$/;
        return re.test(password);
    }
    
    validateUsername(username) {
        // At least 3 characters, only alphanumeric and underscore
        const re = /^[a-zA-Z0-9_]{3,}$/;
        return re.test(username);
    }
    
    showFieldError(fieldId, message) {
        const errorElement = document.getElementById(`${fieldId}-error`);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
            document.getElementById(fieldId).classList.add('border-red-500');
        }
    }
    
    clearFieldError(fieldId) {
        const errorElement = document.getElementById(`${fieldId}-error`);
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.classList.add('hidden');
            document.getElementById(fieldId).classList.remove('border-red-500');
        }
    }
    
    clearAllErrors(formId) {
        const errorElements = document.querySelectorAll(`#${formId} [id$="-error"]`);
        errorElements.forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
        
        const inputElements = document.querySelectorAll(`#${formId} input`);
        inputElements.forEach(el => {
            el.classList.remove('border-red-500');
        });
    }

    handleLogin(e) {
        e.preventDefault();
        this.clearAllErrors('login-form');
        
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        const rememberMe = document.getElementById('remember-me').checked;
        
        // Validate inputs
        let isValid = true;
        
        if (!this.validateEmail(email)) {
            this.showFieldError('login-email', 'Please enter a valid email address');
            isValid = false;
        }
        
        if (!password) {
            this.showFieldError('login-password', 'Please enter your password');
            isValid = false;
        }
        
        if (!isValid) return;
        
        // Hash password for comparison
        const hashedPassword = this.hashPassword(password);
        
        const user = this.users.find(u => u.email === email && u.password === hashedPassword);
        if (user) {
            this.currentUser = Object.assign({}, user, { password: undefined }); // Don't store password in memory
            localStorage.setItem('currentUser', JSON.stringify(this.currentUser));
            
            // Set token expiry time
            const expiryTime = rememberMe 
                ? new Date().getTime() + (30 * 24 * 60 * 60 * 1000) // 30 days
                : new Date().getTime() + (24 * 60 * 60 * 1000); // 24 hours
            
            localStorage.setItem('tokenExpiryTime', expiryTime.toString());
            this.tokenExpiryTime = expiryTime;
            
            this.hideAuthModal();
            this.updateAuthState();
            this.showToast('Successfully signed in!');
            
            // Dispatch event for other components
            document.dispatchEvent(new CustomEvent('authStateChanged'));
        } else {
            this.showToast('Invalid email or password', 'error');
        }
    }

    handleRegister(e) {
        e.preventDefault();
        this.clearAllErrors('register-form');
        
        const username = document.getElementById('register-username').value;
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;
        const confirmPassword = document.getElementById('register-confirm-password').value;
        
        // Validate inputs
        let isValid = true;
        
        if (!this.validateUsername(username)) {
            this.showFieldError('register-username', 'Username must be at least 3 characters and only contain letters, numbers, and underscores');
            isValid = false;
        }
        
        if (!this.validateEmail(email)) {
            this.showFieldError('register-email', 'Please enter a valid email address');
            isValid = false;
        }
        
        if (this.users.some(u => u.email === email)) {
            this.showFieldError('register-email', 'This email is already registered');
            isValid = false;
        }
        
        if (!this.validatePassword(password)) {
            this.showFieldError('register-password', 'Password must be at least 8 characters with at least one number and one special character');
            isValid = false;
        }
        
        if (password !== confirmPassword) {
            this.showFieldError('register-confirm-password', 'Passwords do not match');
            isValid = false;
        }
        
        if (!isValid) return;
        
        // Hash password before storing
        const hashedPassword = this.hashPassword(password);

        const newUser = {
            id: Date.now().toString(),
            username,
            email,
            password: hashedPassword,
            securityAnswer: '', // Will be set during password reset
            createdAt: new Date().toISOString(),
            profilePicture: null,
            preferences: {
                theme: 'dark',
                notifications: true
            }
        };

        this.users.push(newUser);
        localStorage.setItem('users', JSON.stringify(this.users));
        
        this.currentUser = Object.assign({}, newUser, { password: undefined }); // Don't store password in memory
        localStorage.setItem('currentUser', JSON.stringify(this.currentUser));
        
        // Set token expiry time (24 hours by default)
        const expiryTime = new Date().getTime() + (24 * 60 * 60 * 1000);
        localStorage.setItem('tokenExpiryTime', expiryTime.toString());
        this.tokenExpiryTime = expiryTime;
        
        this.hideAuthModal();
        this.updateAuthState();
        this.showToast('Successfully registered!');
        
        // Dispatch event for other components
        document.dispatchEvent(new CustomEvent('authStateChanged'));
    }
    
    handleForgotPassword(e) {
        e.preventDefault();
        this.clearAllErrors('forgot-password-form');
        
        const email = document.getElementById('forgot-email').value;
        
        if (!this.validateEmail(email)) {
            this.showFieldError('forgot-email', 'Please enter a valid email address');
            return;
        }
        
        const user = this.users.find(u => u.email === email);
        if (!user) {
            this.showFieldError('forgot-email', 'No account found with this email address');
            return;
        }
        
        // Set up reset password form
        document.getElementById('reset-email').value = email;
        this.toggleAuthForms('reset-password');
    }
    
    handleResetPassword(e) {
        e.preventDefault();
        this.clearAllErrors('reset-password-form');
        
        const securityAnswer = document.getElementById('security-answer').value;
        const newPassword = document.getElementById('new-password').value;
        const confirmNewPassword = document.getElementById('confirm-new-password').value;
        const email = document.getElementById('reset-email').value;
        
        // Find user
        const userIndex = this.users.findIndex(u => u.email === email);
        if (userIndex === -1) {
            this.showToast('User not found', 'error');
            return;
        }
        
        let isValid = true;
        
        // Simple security check (in real app, this would be more sophisticated)
        if (!securityAnswer) {
            this.showFieldError('security-answer', 'Please provide an answer');
            isValid = false;
        }
        
        if (!this.validatePassword(newPassword)) {
            this.showFieldError('new-password', 'Password must be at least 8 characters with at least one number and one special character');
            isValid = false;
        }
        
        if (newPassword !== confirmNewPassword) {
            this.showFieldError('confirm-new-password', 'Passwords do not match');
            isValid = false;
        }
        
        if (!isValid) return;
        
        // Update user in the array
        const user = this.users[userIndex];
        
        // If this is the first time setting security answer
        if (!user.securityAnswer) {
            user.securityAnswer = securityAnswer;
        } 
        // Check security answer (case insensitive)
        else if (user.securityAnswer.toLowerCase() !== securityAnswer.toLowerCase()) {
            this.showFieldError('security-answer', 'Incorrect answer');
            return;
        }
        
        // Update password
        user.password = this.hashPassword(newPassword);
        
        // Update users array
        this.users[userIndex] = user;
        localStorage.setItem('users', JSON.stringify(this.users));
        
        this.showToast('Password successfully reset!');
        this.toggleAuthForms('login');
    }

    updateAuthState() {
        const authButton = document.querySelector('.user-auth-button');
        if (!authButton) return;
        
        if (this.currentUser) {
            authButton.innerHTML = `
                <div class="relative group">
                    <button class="flex items-center space-x-2 text-white">
                        <span class="w-8 h-8 bg-purple-primary rounded-full flex items-center justify-center text-sm font-medium">
                            ${this.currentUser.username.charAt(0).toUpperCase()}
                        </span>
                        <span class="hidden md:block">${this.currentUser.username}</span>
                        <i class="fas fa-chevron-down text-gray-400 ml-1"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-dark-lighter rounded-lg shadow-lg py-2 hidden group-hover:block">
                        <a href="/profile.html" class="block w-full px-4 py-2 text-left text-gray-400 hover:text-white hover:bg-gray-700">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <a href="/settings.html" class="block w-full px-4 py-2 text-left text-gray-400 hover:text-white hover:bg-gray-700">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </a>
                        <button onclick="auth.logout()" class="block w-full px-4 py-2 text-left text-gray-400 hover:text-white hover:bg-gray-700">
                            <i class="fas fa-sign-out-alt mr-2"></i> Sign Out
                        </button>
                    </div>
                </div>
            `;
        } else {
            authButton.innerHTML = `
                <button onclick="auth.showAuthModal()" class="flex items-center space-x-2 text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="hidden md:block">Sign In</span>
                </button>
            `;
        }
    }

    logout(showToast = true) {
        this.currentUser = null;
        localStorage.removeItem('currentUser');
        localStorage.removeItem('tokenExpiryTime');
        this.tokenExpiryTime = null;
        this.updateAuthState();
        
        if (showToast) {
            this.showToast('Successfully signed out!');
        }
        
        // Dispatch event for other components
        document.dispatchEvent(new CustomEvent('authStateChanged'));
    }

    showToast(message, type = 'success') {
        const toastElement = document.getElementById('toast-notification');
        
        if (toastElement) {
            // Use existing toast element
            toastElement.textContent = message;
            toastElement.className = `toast ${type}`;
            toastElement.classList.add('show');
            
            setTimeout(() => {
                toastElement.classList.remove('show');
            }, 3000);
        } else {
            // Create new toast element
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } transition-opacity duration-300`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    }
}

// Initialize auth system
const auth = new Auth(); 