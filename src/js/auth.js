// User Authentication System
class Auth {
    constructor() {
        this.users = JSON.parse(localStorage.getItem('users') || '[]');
        this.currentUser = JSON.parse(localStorage.getItem('currentUser') || 'null');
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
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2" for="login-password">Password</label>
                            <input type="password" id="login-password" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required>
                        </div>
                        <button type="submit" class="w-full py-2 px-4 bg-purple-primary text-white rounded hover:bg-purple-600 transition-colors">
                            Sign In
                        </button>
                        <p class="text-center text-gray-400">
                            Don't have an account? 
                            <button type="button" class="text-blue-primary hover:text-blue-secondary" id="show-register">Register</button>
                        </p>
                    </form>

                    <!-- Register Form -->
                    <form id="register-form" class="space-y-4 hidden">
                        <div>
                            <label class="block text-gray-400 mb-2" for="register-username">Username</label>
                            <input type="text" id="register-username" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required>
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2" for="register-email">Email</label>
                            <input type="email" id="register-email" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required>
                        </div>
                        <div>
                            <label class="block text-gray-400 mb-2" for="register-password">Password</label>
                            <input type="password" id="register-password" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required>
                        </div>
                        <button type="submit" class="w-full py-2 px-4 bg-purple-primary text-white rounded hover:bg-purple-600 transition-colors">
                            Register
                        </button>
                        <p class="text-center text-gray-400">
                            Already have an account? 
                            <button type="button" class="text-blue-primary hover:text-blue-secondary" id="show-login">Sign In</button>
                        </p>
                    </form>
                </div>
            </div>
        `;
        document.body.appendChild(authModal);

        // Event Listeners
        document.getElementById('close-auth-modal').addEventListener('click', () => this.hideAuthModal());
        document.getElementById('show-register').addEventListener('click', () => this.toggleAuthForms('register'));
        document.getElementById('show-login').addEventListener('click', () => this.toggleAuthForms('login'));
        document.getElementById('login-form').addEventListener('submit', (e) => this.handleLogin(e));
        document.getElementById('register-form').addEventListener('submit', (e) => this.handleRegister(e));
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
        const modalTitle = document.getElementById('auth-modal-title');

        if (form === 'register') {
            loginForm.classList.add('hidden');
            registerForm.classList.remove('hidden');
            modalTitle.textContent = 'Register';
        } else {
            loginForm.classList.remove('hidden');
            registerForm.classList.add('hidden');
            modalTitle.textContent = 'Sign In';
        }
    }

    handleLogin(e) {
        e.preventDefault();
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;

        const user = this.users.find(u => u.email === email && u.password === password);
        if (user) {
            this.currentUser = user;
            localStorage.setItem('currentUser', JSON.stringify(user));
            this.hideAuthModal();
            this.updateAuthState();
            this.showToast('Successfully signed in!');
        } else {
            this.showToast('Invalid email or password', 'error');
        }
    }

    handleRegister(e) {
        e.preventDefault();
        const username = document.getElementById('register-username').value;
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;

        if (this.users.some(u => u.email === email)) {
            this.showToast('Email already registered', 'error');
            return;
        }

        const newUser = {
            id: Date.now().toString(),
            username,
            email,
            password,
            createdAt: new Date().toISOString()
        };

        this.users.push(newUser);
        localStorage.setItem('users', JSON.stringify(this.users));
        
        this.currentUser = newUser;
        localStorage.setItem('currentUser', JSON.stringify(newUser));
        
        this.hideAuthModal();
        this.updateAuthState();
        this.showToast('Successfully registered!');
    }

    updateAuthState() {
        const authButton = document.querySelector('.user-auth-button');
        if (this.currentUser) {
            authButton.innerHTML = `
                <div class="relative group">
                    <button class="flex items-center space-x-2 text-white">
                        <span class="w-8 h-8 bg-purple-primary rounded-full flex items-center justify-center text-sm font-medium">
                            ${this.currentUser.username.charAt(0).toUpperCase()}
                        </span>
                        <span class="hidden md:block">${this.currentUser.username}</span>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-dark-lighter rounded-lg shadow-lg py-2 hidden group-hover:block">
                        <button class="w-full px-4 py-2 text-left text-gray-400 hover:text-white hover:bg-gray-700" onclick="auth.logout()">
                            Sign Out
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

    logout() {
        this.currentUser = null;
        localStorage.removeItem('currentUser');
        this.updateAuthState();
        this.showToast('Successfully signed out!');
    }

    showToast(message, type = 'success') {
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

// Initialize auth system
const auth = new Auth(); 