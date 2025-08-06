// Auth state management
let currentUser = null;

// Tab 切换逻辑
function switchTab(tab) {
    if (tab === 'login') {
        document.getElementById('loginForm').style.display = 'block';
        document.getElementById('registerForm').style.display = 'none';
        document.getElementById('loginTab').classList.add('bg-blue-600', 'active');
        document.getElementById('loginTab').classList.remove('bg-gray-700');
        document.getElementById('registerTab').classList.remove('bg-blue-600', 'active');
        document.getElementById('registerTab').classList.add('bg-gray-700');
    } else {
        document.getElementById('loginForm').style.display = 'none';
        document.getElementById('registerForm').style.display = 'block';
        document.getElementById('loginTab').classList.remove('bg-blue-600', 'active');
        document.getElementById('loginTab').classList.add('bg-gray-700');
        document.getElementById('registerTab').classList.add('bg-blue-600', 'active');
        document.getElementById('registerTab').classList.remove('bg-gray-700');
    }
}

// 打开合并弹窗
function showAuthModal(defaultTab = 'login') {
    document.getElementById('authModal').classList.remove('hidden');
    switchTab(defaultTab);
    document.body.style.overflow = 'hidden';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// 登录表单提交
async function handleLogin(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('loginForm'));
    const email = formData.get('email');
    const password = formData.get('password');
    try {
        const response = await fetch('/api/auth/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        const data = await response.json();
        if (data.success) {
            showToast('登录成功！', 'success');
            closeModal('authModal');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || '登录失败', 'error');
        }
    } catch (error) {
        showToast('登录请求出错', 'error');
    }
}

// 注册表单提交
async function handleRegister(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('registerForm'));
    const username = formData.get('username');
    const email = formData.get('email');
    const password = formData.get('password');
    const confirmPassword = formData.get('confirm_password');
    if (password !== confirmPassword) {
        showToast('两次密码不一致', 'error');
        return;
    }
    try {
        const response = await fetch('/api/auth/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, email, password })
        });
        const data = await response.json();
        if (data.success) {
            showToast('注册成功，请查收激活邮件！', 'success');
            switchTab('login');
        } else {
            showToast(data.message || '注册失败', 'error');
        }
    } catch (error) {
        showToast('注册请求出错', 'error');
    }
}

// 找回密码表单提交（如有实现）
async function handleForgotPassword(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('forgotPasswordForm'));
    const email = formData.get('email');
    try {
        const response = await fetch('/api/auth/forgot-password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email })
        });
        const data = await response.json();
        if (data.success) {
            showToast('重置邮件已发送，请查收邮箱', 'success');
            closeModal('forgotPasswordModal');
        } else {
            showToast(data.message || '发送失败', 'error');
        }
    } catch (error) {
        showToast('请求出错', 'error');
    }
}

// Toast 通知
function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = message;
    toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 ${
        type === 'error' ? 'bg-red-500' : 
        type === 'success' ? 'bg-green-500' : 
        'bg-blue-500'
    } opacity-100`;
    toast.style.transform = 'translateY(0)';
    toast.style.opacity = '1';
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-100%)';
    }, 3000);
} 

// 绑定Tab和按钮事件
window.addEventListener('DOMContentLoaded', () => {
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    if (loginTab && registerTab) {
        loginTab.onclick = () => switchTab('login');
        registerTab.onclick = () => switchTab('register');
    }
    // 绑定全局登录/注册按钮（如导航栏）
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');
    if (loginBtn) loginBtn.onclick = (e) => { e.preventDefault(); showAuthModal('login'); };
    if (registerBtn) registerBtn.onclick = (e) => { e.preventDefault(); showAuthModal('register'); };
    // 表单事件
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (loginForm) {
        loginForm.onsubmit = handleLogin;
        loginForm.setAttribute('autocomplete', 'off');
    }
    if (registerForm) {
        registerForm.onsubmit = handleRegister;
        registerForm.setAttribute('autocomplete', 'off');
    }
    if (forgotPasswordForm) forgotPasswordForm.onsubmit = handleForgotPassword;
});

window.showAuthModal = showAuthModal; 