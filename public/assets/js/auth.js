// Modal handling
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
        document.body.style.overflow = '';
    }
}

// Close modal when clicking outside
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('auth-modal')) {
        closeModal(e.target.id);
    }
});

// Form handling
async function handleLogin(event) {
    event.preventDefault();
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    fetch('login.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        credentials: 'same-origin',
        body: JSON.stringify({email, password})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showLoginError(data.message || 'Login failed');
        }
    })
    .catch(() => {
        showLoginError('Network error');
    });
    return false;
}

function handleRegister(event) {
    event.preventDefault();
    const username = document.getElementById('registerUsername').value;
    const email = document.getElementById('registerEmail').value;
    const password = document.getElementById('registerPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const agree = document.getElementById('agreeTerms') ? document.getElementById('agreeTerms').checked : true;
    if (password !== confirmPassword) {
        showRegisterError('Passwords do not match');
        return false;
    }
    if (!agree) {
        showRegisterError('You must agree to the terms');
        return false;
    }
    fetch('register.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({username, email, password})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showRegisterError(data.message || 'Register failed');
        }
    })
    .catch(() => {
        showRegisterError('Network error');
    });
    return false;
}

function showRegisterError(msg) {
    var err = document.getElementById('registerError');
    if (err) {
        err.innerText = msg;
        err.classList.remove('hidden');
    }
}

async function handleForgotPassword(e) {
    e.preventDefault();
    const form = e.target;
    const email = form.querySelector('[name="email"]').value;

    try {
        const response = await fetch('/auth/forgot-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email }),
        });

        const data = await response.json();

        if (response.ok) {
            showSuccess('forgotPasswordSuccess', 'Password reset instructions have been sent to your email');
            setTimeout(() => {
                closeModal('forgotPasswordModal');
            }, 3000);
        } else {
            showError('forgotPasswordError', data.message || 'Failed to send reset instructions');
        }
    } catch (error) {
        showError('forgotPasswordError', 'An error occurred. Please try again.');
    }
}

async function logout() {
    try {
        const response = await fetch('/auth/logout', {
            method: 'POST',
        });

        if (response.ok) {
            window.location.reload();
        }
    } catch (error) {
        console.error('Logout failed:', error);
    }
}

// Helper functions
function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add('show');
    }
}

function showSuccess(elementId, message) {
    const successElement = document.getElementById(elementId);
    if (successElement) {
        successElement.textContent = message;
        successElement.classList.add('show');
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');

    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }

    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
    }

    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', handleForgotPassword);
    }
});

function hideAllModals() {
    ['loginModal', 'registerModal', 'forgotPasswordModal'].forEach(id => {
        var m = document.getElementById(id);
        if (m) m.classList.add('hidden');
    });
}

function showAuthModal(tab = 'login') {
    var modal = document.getElementById('authModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    // 切换登录/注册tab
    var loginForm = document.getElementById('loginForm');
    var registerForm = document.getElementById('registerForm');
    if (loginForm && registerForm) {
        loginForm.classList.toggle('hidden', tab !== 'login');
        registerForm.classList.toggle('hidden', tab !== 'register');
    }
}
window.showAuthModal = showAuthModal;

// 关闭弹窗
const closeAuthModalBtn = document.getElementById('closeAuthModalBtn');
if (closeAuthModalBtn) {
    closeAuthModalBtn.addEventListener('click', function() {
        document.getElementById('authModal').classList.add('hidden');
    });
}

function changeAvatarRandom() {
    // 生成随机seed
    const seed = Math.random().toString(36).substring(2, 12);
    const avatar = `https://api.dicebear.com/7.x/adventurer/svg?seed=${seed}`;
    fetch('api/change_avatar.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({avatar})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || '换头像失败');
        }
    })
    .catch(() => {
        alert('网络错误，换头像失败');
    });
}

function showAvatarUploadModal() {
    // 创建文件选择框
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = function() {
        if (!input.files || !input.files[0]) return;
        const formData = new FormData();
        formData.append('avatar', input.files[0]);
        fetch('api/upload_avatar.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || '上传头像失败');
            }
        })
        .catch(() => {
            alert('网络错误，上传头像失败');
        });
    };
    input.click();
}

// 头像选择弹窗逻辑
window.showAvatarPickerModal = function() {
    var modal = document.getElementById('avatar-picker-modal');
    var dropdown = document.getElementById('user-menu-dropdown');
    if (modal && dropdown) {
        // 计算下拉菜单位置
        var rect = dropdown.getBoundingClientRect();
        modal.style.position = 'absolute';
        modal.style.left = rect.left + 'px';
        modal.style.top = (rect.bottom + window.scrollY + 8) + 'px'; // 下方8px间距
        modal.style.right = 'auto';
        modal.style.bottom = 'auto';
        modal.style.margin = '0';
        modal.style.zIndex = 99999;
        modal.style.display = 'block';
        modal.classList.remove('hidden');
        modal.style.background = 'transparent'; // 去掉遮罩
        modal.querySelector('.modal-content').style.boxShadow = '0 8px 32px rgba(0,0,0,0.25)';
        window.refreshAvatarPicker();
    } else if (modal) {
        modal.classList.remove('hidden');
        window.refreshAvatarPicker();
    } else {
        alert('弹窗HTML未渲染到页面！');
    }
};
window.hideAvatarPickerModal = function() {
    var modal = document.getElementById('avatar-picker-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = '';
        modal.style.position = '';
        modal.style.left = '';
        modal.style.top = '';
        modal.style.background = '';
    }
};
window.refreshAvatarPicker = function() {
    var list = document.getElementById('avatar-picker-list');
    if (!list) return;
    list.innerHTML = '';
    for (let i = 0; i < 9; i++) {
        const seed = Math.random().toString(36).substring(2, 12);
        const url = `https://api.dicebear.com/7.x/adventurer/svg?seed=${seed}`;
        const btn = document.createElement('button');
        btn.className = 'rounded-xl overflow-hidden border-2 border-transparent hover:border-blue-500 transition-all aspect-square bg-white';
        btn.style.padding = '0';
        btn.onclick = function() { window.selectAvatarFromPicker(url); };
        const img = document.createElement('img');
        img.src = url;
        img.alt = 'avatar';
        img.className = 'w-20 h-20 object-cover';
        btn.appendChild(img);
        list.appendChild(btn);
    }
};
window.selectAvatarFromPicker = function(avatar) {
    fetch('api/change_avatar.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        credentials: 'same-origin',
        body: JSON.stringify({avatar})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.hideAvatarPickerModal();
            location.reload();
        } else {
            alert(data.message || '换头像失败');
        }
    })
    .catch(() => {
        alert('网络错误，换头像失败');
    });
};

// 用户菜单下拉交互
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('user-menu-button');
        var dropdown = document.getElementById('user-menu-dropdown');
        if (btn && dropdown) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function() {
                dropdown.classList.add('hidden');
            });
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });
})(); 