import React, { useState } from 'react';

const UserMenu = ({ userId }) => {
    const [showAvatarSelector, setShowAvatarSelector] = useState(false);
    const [avatarOptions, setAvatarOptions] = useState([]);
    const [selectedAvatar, setSelectedAvatar] = useState('');

    // 生成随机头像选项
    const generateAvatarOptions = () => {
        const styles = ['adventurer', 'avataaars', 'bottts', 'pixel-art', 'fun-emoji'];
        const newOptions = styles.map(style => {
            const seed = Math.random().toString(36).substring(7);
            return `https://api.dicebear.com/7.x/${style}/svg?seed=${seed}`;
        });
        setAvatarOptions(newOptions);
    };

    // 打开头像选择器时生成选项
    const handleOpenAvatarSelector = () => {
        generateAvatarOptions();
        setShowAvatarSelector(true);
    };

    // 更新头像
    const handleUpdateAvatar = async (avatarUrl) => {
        try {
            const response = await fetch('/api/update_avatar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `user_id=${userId}&avatar=${encodeURIComponent(avatarUrl)}`
            });

            const data = await response.json();
            if (data.success) {
                setSelectedAvatar(avatarUrl);
                setShowAvatarSelector(false);
                window.location.reload(); // 刷新页面以更新头像
            } else {
                alert(data.msg || '更新头像失败');
            }
        } catch (error) {
            console.error('更新头像错误:', error);
            alert('更新头像失败，请重试');
        }
    };

    // 处理自定义头像上传
    const handleCustomAvatarUpload = (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onloadend = () => {
                handleUpdateAvatar(reader.result);
            };
            reader.readAsDataURL(file);
        }
    };

    // 处理登出
    const handleLogout = async () => {
        try {
            const response = await fetch('/api/logout.php', {
                method: 'POST'
            });
            const data = await response.json();
            if (data.success) {
                window.location.reload();
            }
        } catch (error) {
            console.error('登出错误:', error);
            alert('登出失败，请重试');
        }
    };

    return (
        <div className="relative">
            {/* 用户菜单选项 */}
            <div className="py-1">
                {/* Profile */}
                <a href="/profile" 
                   className="block px-4 py-2 text-white hover:bg-gray-700 transition-colors">
                    <i className="fas fa-user mr-2"></i>
                    Profile
                </a>

                {/* Settings */}
                <a href="/settings" 
                   className="block px-4 py-2 text-white hover:bg-gray-700 transition-colors">
                    <i className="fas fa-cog mr-2"></i>
                    Settings
                </a>

                {/* Change Avatar */}
                <button
                    onClick={handleOpenAvatarSelector}
                    className="w-full text-left px-4 py-2 text-white hover:bg-gray-700 transition-colors"
                >
                    <i className="fas fa-user-circle mr-2"></i>
                    更换头像
                </button>

                {/* Favorites */}
                <a href="/favorites" 
                   className="block px-4 py-2 text-white hover:bg-gray-700 transition-colors">
                    <i className="fas fa-heart mr-2"></i>
                    Favorites
                </a>

                {/* Divider */}
                <hr className="my-1 border-gray-700" />

                {/* Logout */}
                <button
                    onClick={handleLogout}
                    className="w-full text-left px-4 py-2 text-white hover:bg-red-600 transition-colors"
                >
                    <i className="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </button>
            </div>

            {/* 头像选择器弹窗 */}
            {showAvatarSelector && (
                <div className="fixed inset-0 z-50 flex items-center justify-center">
                    <div className="fixed inset-0 bg-black opacity-50" onClick={() => setShowAvatarSelector(false)}></div>
                    <div className="relative bg-dark-lighter rounded-lg p-6 max-w-lg w-full mx-4">
                        <h3 className="text-xl font-bold text-white mb-4">选择头像</h3>
                        
                        {/* 随机头像选项 */}
                        <div className="grid grid-cols-3 gap-4 mb-6">
                            {avatarOptions.map((avatar, index) => (
                                <div 
                                    key={index} 
                                    className="cursor-pointer p-2 rounded-lg hover:bg-gray-700 transition-colors"
                                    onClick={() => handleUpdateAvatar(avatar)}
                                >
                                    <img 
                                        src={avatar} 
                                        alt={`Avatar option ${index + 1}`}
                                        className="w-full h-auto rounded-lg"
                                    />
                                </div>
                            ))}
                        </div>

                        {/* 操作按钮 */}
                        <div className="flex flex-wrap gap-4">
                            <button
                                onClick={generateAvatarOptions}
                                className="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                            >
                                换一批
                            </button>
                            
                            <label className="flex-1">
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleCustomAvatarUpload}
                                    className="hidden"
                                />
                                <div className="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors text-center cursor-pointer">
                                    上传图片
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default UserMenu; 
class UserMenu {
    constructor() {
        this.button = document.querySelector('.user-menu-button');
        this.container = document.querySelector('.user-menu-container');
        this.menu = null;
        this.isOpen = false;
        
        this.init();
    }

    init() {
        this.createMenu();
        this.addEventListeners();
    }

    createMenu() {
        this.menu = document.createElement('div');
        this.menu.className = 'absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-dark-lighter ring-1 ring-black ring-opacity-5 focus:outline-none hidden';
        this.menu.setAttribute('role', 'menu');
        this.menu.setAttribute('aria-orientation', 'vertical');
        this.menu.setAttribute('aria-labelledby', 'user-menu-button');
        this.menu.setAttribute('tabindex', '-1');

        const menuItems = [
            { text: '个人资料', icon: '<i class="fas fa-user"></i>', action: () => console.log('Profile clicked') },
            { text: '我的收藏', icon: '<i class="fas fa-heart"></i>', action: () => console.log('Favorites clicked') },
            { text: '最近游戏', icon: '<i class="fas fa-clock"></i>', action: () => console.log('Recent clicked') },
            { text: '设置', icon: '<i class="fas fa-cog"></i>', action: () => console.log('Settings clicked') },
            { text: '退出', icon: '<i class="fas fa-sign-out-alt"></i>', action: () => this.handleLogout() }
        ];

        menuItems.forEach(item => {
            const menuItem = document.createElement('a');
            menuItem.href = '#';
            menuItem.className = 'group flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-dark-purple hover:text-white transition-colors duration-150';
            menuItem.setAttribute('role', 'menuitem');
            menuItem.innerHTML = `
                <span class="mr-3 text-gray-400 group-hover:text-white">${item.icon}</span>
                ${item.text}
            `;
            menuItem.addEventListener('click', (e) => {
                e.preventDefault();
                item.action();
                this.closeMenu();
            });
            this.menu.appendChild(menuItem);
        });

        this.container.appendChild(this.menu);
    }

    addEventListeners() {
        this.button.addEventListener('click', () => this.toggleMenu());
        
        document.addEventListener('click', (e) => {
            if (!this.button.contains(e.target) && !this.menu.contains(e.target)) {
                this.closeMenu();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeMenu();
            }
        });
    }

    toggleMenu() {
        this.isOpen ? this.closeMenu() : this.openMenu();
    }

    openMenu() {
        this.menu.classList.remove('hidden');
        this.button.setAttribute('aria-expanded', 'true');
        this.isOpen = true;
    }

    closeMenu() {
        this.menu.classList.add('hidden');
        this.button.setAttribute('aria-expanded', 'false');
        this.isOpen = false;
    }

    handleLogout() {
        console.log('Logging out...');
        Toast.show('成功退出登录', 'success');
        // 这里添加实际的退出登录逻辑
    }
}

const userMenu = new UserMenu();
export default userMenu; 
                {/* Change Avatar */}
                <button
                    onClick={handleOpenAvatarSelector}
                    className="w-full text-left px-4 py-2 text-white hover:bg-gray-700 transition-colors"
                >
                    <i className="fas fa-user-circle mr-2"></i>
                    更换头像
                </button>

                {/* Favorites */}
                <a href="/favorites" 
                   className="block px-4 py-2 text-white hover:bg-gray-700 transition-colors">
                    <i className="fas fa-heart mr-2"></i>
                    Favorites
                </a>

                {/* Divider */}
                <hr className="my-1 border-gray-700" />

                {/* Logout */}
                <button
                    onClick={handleLogout}
                    className="w-full text-left px-4 py-2 text-white hover:bg-red-600 transition-colors"
                >
                    <i className="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </button>
            </div>

            {/* 头像选择器弹窗 */}
            {showAvatarSelector && (
                <div className="fixed inset-0 z-50 flex items-center justify-center">
                    <div className="fixed inset-0 bg-black opacity-50" onClick={() => setShowAvatarSelector(false)}></div>
                    <div className="relative bg-dark-lighter rounded-lg p-6 max-w-lg w-full mx-4">
                        <h3 className="text-xl font-bold text-white mb-4">选择头像</h3>
                        
                        {/* 随机头像选项 */}
                        <div className="grid grid-cols-3 gap-4 mb-6">
                            {avatarOptions.map((avatar, index) => (
                                <div 
                                    key={index} 
                                    className="cursor-pointer p-2 rounded-lg hover:bg-gray-700 transition-colors"
                                    onClick={() => handleUpdateAvatar(avatar)}
                                >
                                    <img 
                                        src={avatar} 
                                        alt={`Avatar option ${index + 1}`}
                                        className="w-full h-auto rounded-lg"
                                    />
                                </div>
                            ))}
                        </div>

                        {/* 操作按钮 */}
                        <div className="flex flex-wrap gap-4">
                            <button
                                onClick={generateAvatarOptions}
                                className="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                            >
                                换一批
                            </button>
                            
                            <label className="flex-1">
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleCustomAvatarUpload}
                                    className="hidden"
                                />
                                <div className="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors text-center cursor-pointer">
                                    上传图片
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default UserMenu; 
class UserMenu {
    constructor() {
        this.button = document.querySelector('.user-menu-button');
        this.container = document.querySelector('.user-menu-container');
        this.menu = null;
        this.isOpen = false;
        
        this.init();
    }

    init() {
        this.createMenu();
        this.addEventListeners();
    }

    createMenu() {
        this.menu = document.createElement('div');
        this.menu.className = 'absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-dark-lighter ring-1 ring-black ring-opacity-5 focus:outline-none hidden';
        this.menu.setAttribute('role', 'menu');
        this.menu.setAttribute('aria-orientation', 'vertical');
        this.menu.setAttribute('aria-labelledby', 'user-menu-button');
        this.menu.setAttribute('tabindex', '-1');

        const menuItems = [
            { text: '个人资料', icon: '<i class="fas fa-user"></i>', action: () => console.log('Profile clicked') },
            { text: '我的收藏', icon: '<i class="fas fa-heart"></i>', action: () => console.log('Favorites clicked') },
            { text: '最近游戏', icon: '<i class="fas fa-clock"></i>', action: () => console.log('Recent clicked') },
            { text: '设置', icon: '<i class="fas fa-cog"></i>', action: () => console.log('Settings clicked') },
            { text: '退出', icon: '<i class="fas fa-sign-out-alt"></i>', action: () => this.handleLogout() }
        ];

        menuItems.forEach(item => {
            const menuItem = document.createElement('a');
            menuItem.href = '#';
            menuItem.className = 'group flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-dark-purple hover:text-white transition-colors duration-150';
            menuItem.setAttribute('role', 'menuitem');
            menuItem.innerHTML = `
                <span class="mr-3 text-gray-400 group-hover:text-white">${item.icon}</span>
                ${item.text}
            `;
            menuItem.addEventListener('click', (e) => {
                e.preventDefault();
                item.action();
                this.closeMenu();
            });
            this.menu.appendChild(menuItem);
        });

        this.container.appendChild(this.menu);
    }

    addEventListeners() {
        this.button.addEventListener('click', () => this.toggleMenu());
        
        document.addEventListener('click', (e) => {
            if (!this.button.contains(e.target) && !this.menu.contains(e.target)) {
                this.closeMenu();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeMenu();
            }
        });
    }

    toggleMenu() {
        this.isOpen ? this.closeMenu() : this.openMenu();
    }

    openMenu() {
        this.menu.classList.remove('hidden');
        this.button.setAttribute('aria-expanded', 'true');
        this.isOpen = true;
    }

    closeMenu() {
        this.menu.classList.add('hidden');
        this.button.setAttribute('aria-expanded', 'false');
        this.isOpen = false;
    }

    handleLogout() {
        console.log('Logging out...');
        Toast.show('成功退出登录', 'success');
        // 这里添加实际的退出登录逻辑
    }
}

const userMenu = new UserMenu();
export default userMenu; 
                {/* Change Avatar */}
                <button
                    onClick={handleOpenAvatarSelector}
                    className="w-full text-left px-4 py-2 text-white hover:bg-gray-700 transition-colors"
                >
                    <i className="fas fa-user-circle mr-2"></i>
                    更换头像
                </button>

                {/* Favorites */}
                <a href="/favorites" 
                   className="block px-4 py-2 text-white hover:bg-gray-700 transition-colors">
                    <i className="fas fa-heart mr-2"></i>
                    Favorites
                </a>

                {/* Divider */}
                <hr className="my-1 border-gray-700" />

                {/* Logout */}
                <button
                    onClick={handleLogout}
                    className="w-full text-left px-4 py-2 text-white hover:bg-red-600 transition-colors"
                >
                    <i className="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </button>
            </div>

            {/* 头像选择器弹窗 */}
            {showAvatarSelector && (
                <div className="fixed inset-0 z-50 flex items-center justify-center">
                    <div className="fixed inset-0 bg-black opacity-50" onClick={() => setShowAvatarSelector(false)}></div>
                    <div className="relative bg-dark-lighter rounded-lg p-6 max-w-lg w-full mx-4">
                        <h3 className="text-xl font-bold text-white mb-4">选择头像</h3>
                        
                        {/* 随机头像选项 */}
                        <div className="grid grid-cols-3 gap-4 mb-6">
                            {avatarOptions.map((avatar, index) => (
                                <div 
                                    key={index} 
                                    className="cursor-pointer p-2 rounded-lg hover:bg-gray-700 transition-colors"
                                    onClick={() => handleUpdateAvatar(avatar)}
                                >
                                    <img 
                                        src={avatar} 
                                        alt={`Avatar option ${index + 1}`}
                                        className="w-full h-auto rounded-lg"
                                    />
                                </div>
                            ))}
                        </div>

                        {/* 操作按钮 */}
                        <div className="flex flex-wrap gap-4">
                            <button
                                onClick={generateAvatarOptions}
                                className="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                            >
                                换一批
                            </button>
                            
                            <label className="flex-1">
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleCustomAvatarUpload}
                                    className="hidden"
                                />
                                <div className="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors text-center cursor-pointer">
                                    上传图片
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default UserMenu; 
class UserMenu {
    constructor() {
        this.button = document.querySelector('.user-menu-button');
        this.container = document.querySelector('.user-menu-container');
        this.menu = null;
        this.isOpen = false;
        
        this.init();
    }

    init() {
        this.createMenu();
        this.addEventListeners();
    }

    createMenu() {
        this.menu = document.createElement('div');
        this.menu.className = 'absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-dark-lighter ring-1 ring-black ring-opacity-5 focus:outline-none hidden';
        this.menu.setAttribute('role', 'menu');
        this.menu.setAttribute('aria-orientation', 'vertical');
        this.menu.setAttribute('aria-labelledby', 'user-menu-button');
        this.menu.setAttribute('tabindex', '-1');

        const menuItems = [
            { text: '个人资料', icon: '<i class="fas fa-user"></i>', action: () => console.log('Profile clicked') },
            { text: '我的收藏', icon: '<i class="fas fa-heart"></i>', action: () => console.log('Favorites clicked') },
            { text: '最近游戏', icon: '<i class="fas fa-clock"></i>', action: () => console.log('Recent clicked') },
            { text: '设置', icon: '<i class="fas fa-cog"></i>', action: () => console.log('Settings clicked') },
            { text: '退出', icon: '<i class="fas fa-sign-out-alt"></i>', action: () => this.handleLogout() }
        ];

        menuItems.forEach(item => {
            const menuItem = document.createElement('a');
            menuItem.href = '#';
            menuItem.className = 'group flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-dark-purple hover:text-white transition-colors duration-150';
            menuItem.setAttribute('role', 'menuitem');
            menuItem.innerHTML = `
                <span class="mr-3 text-gray-400 group-hover:text-white">${item.icon}</span>
                ${item.text}
            `;
            menuItem.addEventListener('click', (e) => {
                e.preventDefault();
                item.action();
                this.closeMenu();
            });
            this.menu.appendChild(menuItem);
        });

        this.container.appendChild(this.menu);
    }

    addEventListeners() {
        this.button.addEventListener('click', () => this.toggleMenu());
        
        document.addEventListener('click', (e) => {
            if (!this.button.contains(e.target) && !this.menu.contains(e.target)) {
                this.closeMenu();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeMenu();
            }
        });
    }

    toggleMenu() {
        this.isOpen ? this.closeMenu() : this.openMenu();
    }

    openMenu() {
        this.menu.classList.remove('hidden');
        this.button.setAttribute('aria-expanded', 'true');
        this.isOpen = true;
    }

    closeMenu() {
        this.menu.classList.add('hidden');
        this.button.setAttribute('aria-expanded', 'false');
        this.isOpen = false;
    }

    handleLogout() {
        console.log('Logging out...');
        Toast.show('成功退出登录', 'success');
        // 这里添加实际的退出登录逻辑
    }
}

const userMenu = new UserMenu();
export default userMenu; 
                {/* Change Avatar */}
                <button
                    onClick={handleOpenAvatarSelector}
                    className="w-full text-left px-4 py-2 text-white hover:bg-gray-700 transition-colors"
                >
                    <i className="fas fa-user-circle mr-2"></i>
                    更换头像
                </button>

                {/* Favorites */}
                <a href="/favorites" 
                   className="block px-4 py-2 text-white hover:bg-gray-700 transition-colors">
                    <i className="fas fa-heart mr-2"></i>
                    Favorites
                </a>

                {/* Divider */}
                <hr className="my-1 border-gray-700" />

                {/* Logout */}
                <button
                    onClick={handleLogout}
                    className="w-full text-left px-4 py-2 text-white hover:bg-red-600 transition-colors"
                >
                    <i className="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </button>
            </div>

            {/* 头像选择器弹窗 */}
            {showAvatarSelector && (
                <div className="fixed inset-0 z-50 flex items-center justify-center">
                    <div className="fixed inset-0 bg-black opacity-50" onClick={() => setShowAvatarSelector(false)}></div>
                    <div className="relative bg-dark-lighter rounded-lg p-6 max-w-lg w-full mx-4">
                        <h3 className="text-xl font-bold text-white mb-4">选择头像</h3>
                        
                        {/* 随机头像选项 */}
                        <div className="grid grid-cols-3 gap-4 mb-6">
                            {avatarOptions.map((avatar, index) => (
                                <div 
                                    key={index} 
                                    className="cursor-pointer p-2 rounded-lg hover:bg-gray-700 transition-colors"
                                    onClick={() => handleUpdateAvatar(avatar)}
                                >
                                    <img 
                                        src={avatar} 
                                        alt={`Avatar option ${index + 1}`}
                                        className="w-full h-auto rounded-lg"
                                    />
                                </div>
                            ))}
                        </div>

                        {/* 操作按钮 */}
                        <div className="flex flex-wrap gap-4">
                            <button
                                onClick={generateAvatarOptions}
                                className="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                            >
                                换一批
                            </button>
                            
                            <label className="flex-1">
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleCustomAvatarUpload}
                                    className="hidden"
                                />
                                <div className="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors text-center cursor-pointer">
                                    上传图片
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default UserMenu; 
class UserMenu {
    constructor() {
        this.button = document.querySelector('.user-menu-button');
        this.container = document.querySelector('.user-menu-container');
        this.menu = null;
        this.isOpen = false;
        
        this.init();
    }

    init() {
        this.createMenu();
        this.addEventListeners();
    }

    createMenu() {
        this.menu = document.createElement('div');
        this.menu.className = 'absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-dark-lighter ring-1 ring-black ring-opacity-5 focus:outline-none hidden';
        this.menu.setAttribute('role', 'menu');
        this.menu.setAttribute('aria-orientation', 'vertical');
        this.menu.setAttribute('aria-labelledby', 'user-menu-button');
        this.menu.setAttribute('tabindex', '-1');

        const menuItems = [
            { text: '个人资料', icon: '<i class="fas fa-user"></i>', action: () => console.log('Profile clicked') },
            { text: '我的收藏', icon: '<i class="fas fa-heart"></i>', action: () => console.log('Favorites clicked') },
            { text: '最近游戏', icon: '<i class="fas fa-clock"></i>', action: () => console.log('Recent clicked') },
            { text: '设置', icon: '<i class="fas fa-cog"></i>', action: () => console.log('Settings clicked') },
            { text: '退出', icon: '<i class="fas fa-sign-out-alt"></i>', action: () => this.handleLogout() }
        ];

        menuItems.forEach(item => {
            const menuItem = document.createElement('a');
            menuItem.href = '#';
            menuItem.className = 'group flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-dark-purple hover:text-white transition-colors duration-150';
            menuItem.setAttribute('role', 'menuitem');
            menuItem.innerHTML = `
                <span class="mr-3 text-gray-400 group-hover:text-white">${item.icon}</span>
                ${item.text}
            `;
            menuItem.addEventListener('click', (e) => {
                e.preventDefault();
                item.action();
                this.closeMenu();
            });
            this.menu.appendChild(menuItem);
        });

        this.container.appendChild(this.menu);
    }

    addEventListeners() {
        this.button.addEventListener('click', () => this.toggleMenu());
        
        document.addEventListener('click', (e) => {
            if (!this.button.contains(e.target) && !this.menu.contains(e.target)) {
                this.closeMenu();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeMenu();
            }
        });
    }

    toggleMenu() {
        this.isOpen ? this.closeMenu() : this.openMenu();
    }

    openMenu() {
        this.menu.classList.remove('hidden');
        this.button.setAttribute('aria-expanded', 'true');
        this.isOpen = true;
    }

    closeMenu() {
        this.menu.classList.add('hidden');
        this.button.setAttribute('aria-expanded', 'false');
        this.isOpen = false;
    }

    handleLogout() {
        console.log('Logging out...');
        Toast.show('成功退出登录', 'success');
        // 这里添加实际的退出登录逻辑
    }
}

const userMenu = new UserMenu();
export default userMenu; 
                {/* Change Avatar */}
                <button
                    onClick={handleOpenAvatarSelector}
                    className="w-full text-left px-4 py-2 text-white hover:bg-gray-700 transition-colors"
                >
                    <i className="fas fa-user-circle mr-2"></i>
                    更换头像
                </button>

                {/* Favorites */}
                <a href="/favorites" 
                   className="block px-4 py-2 text-white hover:bg-gray-700 transition-colors">
                    <i className="fas fa-heart mr-2"></i>
                    Favorites
                </a>

                {/* Divider */}
                <hr className="my-1 border-gray-700" />

                {/* Logout */}
                <button
                    onClick={handleLogout}
                    className="w-full text-left px-4 py-2 text-white hover:bg-red-600 transition-colors"
                >
                    <i className="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </button>
            </div>

            {/* 头像选择器弹窗 */}
            {showAvatarSelector && (
                <div className="fixed inset-0 z-50 flex items-center justify-center">
                    <div className="fixed inset-0 bg-black opacity-50" onClick={() => setShowAvatarSelector(false)}></div>
                    <div className="relative bg-dark-lighter rounded-lg p-6 max-w-lg w-full mx-4">
                        <h3 className="text-xl font-bold text-white mb-4">选择头像</h3>
                        
                        {/* 随机头像选项 */}
                        <div className="grid grid-cols-3 gap-4 mb-6">
                            {avatarOptions.map((avatar, index) => (
                                <div 
                                    key={index} 
                                    className="cursor-pointer p-2 rounded-lg hover:bg-gray-700 transition-colors"
                                    onClick={() => handleUpdateAvatar(avatar)}
                                >
                                    <img 
                                        src={avatar} 
                                        alt={`Avatar option ${index + 1}`}
                                        className="w-full h-auto rounded-lg"
                                    />
                                </div>
                            ))}
                        </div>

                        {/* 操作按钮 */}
                        <div className="flex flex-wrap gap-4">
                            <button
                                onClick={generateAvatarOptions}
                                className="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                            >
                                换一批
                            </button>
                            
                            <label className="flex-1">
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleCustomAvatarUpload}
                                    className="hidden"
                                />
                                <div className="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors text-center cursor-pointer">
                                    上传图片
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default UserMenu; 
class UserMenu {
    constructor() {
        this.button = document.querySelector('.user-menu-button');
        this.container = document.querySelector('.user-menu-container');
        this.menu = null;
        this.isOpen = false;
        
        this.init();
    }

    init() {
        this.createMenu();
        this.addEventListeners();
    }

    createMenu() {
        this.menu = document.createElement('div');
        this.menu.className = 'absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-dark-lighter ring-1 ring-black ring-opacity-5 focus:outline-none hidden';
        this.menu.setAttribute('role', 'menu');
        this.menu.setAttribute('aria-orientation', 'vertical');
        this.menu.setAttribute('aria-labelledby', 'user-menu-button');
        this.menu.setAttribute('tabindex', '-1');

        const menuItems = [
            { text: '个人资料', icon: '<i class="fas fa-user"></i>', action: () => console.log('Profile clicked') },
            { text: '我的收藏', icon: '<i class="fas fa-heart"></i>', action: () => console.log('Favorites clicked') },
            { text: '最近游戏', icon: '<i class="fas fa-clock"></i>', action: () => console.log('Recent clicked') },
            { text: '设置', icon: '<i class="fas fa-cog"></i>', action: () => console.log('Settings clicked') },
            { text: '退出', icon: '<i class="fas fa-sign-out-alt"></i>', action: () => this.handleLogout() }
        ];

        menuItems.forEach(item => {
            const menuItem = document.createElement('a');
            menuItem.href = '#';
            menuItem.className = 'group flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-dark-purple hover:text-white transition-colors duration-150';
            menuItem.setAttribute('role', 'menuitem');
            menuItem.innerHTML = `
                <span class="mr-3 text-gray-400 group-hover:text-white">${item.icon}</span>
                ${item.text}
            `;
            menuItem.addEventListener('click', (e) => {
                e.preventDefault();
                item.action();
                this.closeMenu();
            });
            this.menu.appendChild(menuItem);
        });

        this.container.appendChild(this.menu);
    }

    addEventListeners() {
        this.button.addEventListener('click', () => this.toggleMenu());
        
        document.addEventListener('click', (e) => {
            if (!this.button.contains(e.target) && !this.menu.contains(e.target)) {
                this.closeMenu();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeMenu();
            }
        });
    }

    toggleMenu() {
        this.isOpen ? this.closeMenu() : this.openMenu();
    }

    openMenu() {
        this.menu.classList.remove('hidden');
        this.button.setAttribute('aria-expanded', 'true');
        this.isOpen = true;
    }

    closeMenu() {
        this.menu.classList.add('hidden');
        this.button.setAttribute('aria-expanded', 'false');
        this.isOpen = false;
    }

    handleLogout() {
        console.log('Logging out...');
        Toast.show('成功退出登录', 'success');
        // 这里添加实际的退出登录逻辑
    }
}

const userMenu = new UserMenu();
export default userMenu; 
                {/* Change Avatar */}
                <button
                    onClick={handleOpenAvatarSelector}
                    className="w-full text-left px-4 py-2 text-white hover:bg-gray-700 transition-colors"
                >
                    <i className="fas fa-user-circle mr-2"></i>
                    更换头像
                </button>

                {/* Favorites */}
                <a href="/favorites" 
                   className="block px-4 py-2 text-white hover:bg-gray-700 transition-colors">
                    <i className="fas fa-heart mr-2"></i>
                    Favorites
                </a>

                {/* Divider */}
                <hr className="my-1 border-gray-700" />

                {/* Logout */}
                <button
                    onClick={handleLogout}
                    className="w-full text-left px-4 py-2 text-white hover:bg-red-600 transition-colors"
                >
                    <i className="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </button>
            </div>

            {/* 头像选择器弹窗 */}
            {showAvatarSelector && (
                <div className="fixed inset-0 z-50 flex items-center justify-center">
                    <div className="fixed inset-0 bg-black opacity-50" onClick={() => setShowAvatarSelector(false)}></div>
                    <div className="relative bg-dark-lighter rounded-lg p-6 max-w-lg w-full mx-4">
                        <h3 className="text-xl font-bold text-white mb-4">选择头像</h3>
                        
                        {/* 随机头像选项 */}
                        <div className="grid grid-cols-3 gap-4 mb-6">
                            {avatarOptions.map((avatar, index) => (
                                <div 
                                    key={index} 
                                    className="cursor-pointer p-2 rounded-lg hover:bg-gray-700 transition-colors"
                                    onClick={() => handleUpdateAvatar(avatar)}
                                >
                                    <img 
                                        src={avatar} 
                                        alt={`Avatar option ${index + 1}`}
                                        className="w-full h-auto rounded-lg"
                                    />
                                </div>
                            ))}
                        </div>

                        {/* 操作按钮 */}
                        <div className="flex flex-wrap gap-4">
                            <button
                                onClick={generateAvatarOptions}
                                className="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                            >
                                换一批
                            </button>
                            
                            <label className="flex-1">
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleCustomAvatarUpload}
                                    className="hidden"
                                />
                                <div className="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors text-center cursor-pointer">
                                    上传图片
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default UserMenu; 
class UserMenu {
    constructor() {
        this.button = document.querySelector('.user-menu-button');
        this.container = document.querySelector('.user-menu-container');
        this.menu = null;
        this.isOpen = false;
        
        this.init();
    }

    init() {
        this.createMenu();
        this.addEventListeners();
    }

    createMenu() {
        this.menu = document.createElement('div');
        this.menu.className = 'absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-dark-lighter ring-1 ring-black ring-opacity-5 focus:outline-none hidden';
        this.menu.setAttribute('role', 'menu');
        this.menu.setAttribute('aria-orientation', 'vertical');
        this.menu.setAttribute('aria-labelledby', 'user-menu-button');
        this.menu.setAttribute('tabindex', '-1');

        const menuItems = [
            { text: '个人资料', icon: '<i class="fas fa-user"></i>', action: () => console.log('Profile clicked') },
            { text: '我的收藏', icon: '<i class="fas fa-heart"></i>', action: () => console.log('Favorites clicked') },
            { text: '最近游戏', icon: '<i class="fas fa-clock"></i>', action: () => console.log('Recent clicked') },
            { text: '设置', icon: '<i class="fas fa-cog"></i>', action: () => console.log('Settings clicked') },
            { text: '退出', icon: '<i class="fas fa-sign-out-alt"></i>', action: () => this.handleLogout() }
        ];

        menuItems.forEach(item => {
            const menuItem = document.createElement('a');
            menuItem.href = '#';
            menuItem.className = 'group flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-dark-purple hover:text-white transition-colors duration-150';
            menuItem.setAttribute('role', 'menuitem');
            menuItem.innerHTML = `
                <span class="mr-3 text-gray-400 group-hover:text-white">${item.icon}</span>
                ${item.text}
            `;
            menuItem.addEventListener('click', (e) => {
                e.preventDefault();
                item.action();
                this.closeMenu();
            });
            this.menu.appendChild(menuItem);
        });

        this.container.appendChild(this.menu);
    }

    addEventListeners() {
        this.button.addEventListener('click', () => this.toggleMenu());
        
        document.addEventListener('click', (e) => {
            if (!this.button.contains(e.target) && !this.menu.contains(e.target)) {
                this.closeMenu();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeMenu();
            }
        });
    }

    toggleMenu() {
        this.isOpen ? this.closeMenu() : this.openMenu();
    }

    openMenu() {
        this.menu.classList.remove('hidden');
        this.button.setAttribute('aria-expanded', 'true');
        this.isOpen = true;
    }

    closeMenu() {
        this.menu.classList.add('hidden');
        this.button.setAttribute('aria-expanded', 'false');
        this.isOpen = false;
    }

    handleLogout() {
        console.log('Logging out...');
        Toast.show('成功退出登录', 'success');
        // 这里添加实际的退出登录逻辑
    }
}

const userMenu = new UserMenu();
export default userMenu; 
                {/* Change Avatar */}
                <button
                    onClick={handleOpenAvatarSelector}
                    className="w-full text-left px-4 py-2 text-white hover:bg-gray-700 transition-colors"
                >
                    <i className="fas fa-user-circle mr-2"></i>
                    更换头像
                </button>

                {/* Favorites */}
                <a href="/favorites" 
                   className="block px-4 py-2 text-white hover:bg-gray-700 transition-colors">
                    <i className="fas fa-heart mr-2"></i>
                    Favorites
                </a>

                {/* Divider */}
                <hr className="my-1 border-gray-700" />

                {/* Logout */}
                <button
                    onClick={handleLogout}
                    className="w-full text-left px-4 py-2 text-white hover:bg-red-600 transition-colors"
                >
                    <i className="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </button>
            </div>

            {/* 头像选择器弹窗 */}
            {showAvatarSelector && (
                <div className="fixed inset-0 z-50 flex items-center justify-center">
                    <div className="fixed inset-0 bg-black opacity-50" onClick={() => setShowAvatarSelector(false)}></div>
                    <div className="relative bg-dark-lighter rounded-lg p-6 max-w-lg w-full mx-4">
                        <h3 className="text-xl font-bold text-white mb-4">选择头像</h3>
                        
                        {/* 随机头像选项 */}
                        <div className="grid grid-cols-3 gap-4 mb-6">
                            {avatarOptions.map((avatar, index) => (
                                <div 
                                    key={index} 
                                    className="cursor-pointer p-2 rounded-lg hover:bg-gray-700 transition-colors"
                                    onClick={() => handleUpdateAvatar(avatar)}
                                >
                                    <img 
                                        src={avatar} 
                                        alt={`Avatar option ${index + 1}`}
                                        className="w-full h-auto rounded-lg"
                                    />
                                </div>
                            ))}
                        </div>

                        {/* 操作按钮 */}
                        <div className="flex flex-wrap gap-4">
                            <button
                                onClick={generateAvatarOptions}
                                className="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                            >
                                换一批
                            </button>
                            
                            <label className="flex-1">
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleCustomAvatarUpload}
                                    className="hidden"
                                />
                                <div className="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors text-center cursor-pointer">
                                    上传图片
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default UserMenu; 
class UserMenu {
    constructor() {
        this.button = document.querySelector('.user-menu-button');
        this.container = document.querySelector('.user-menu-container');
        this.menu = null;
        this.isOpen = false;
        
        this.init();
    }

    init() {
        this.createMenu();
        this.addEventListeners();
    }

    createMenu() {
        this.menu = document.createElement('div');
        this.menu.className = 'absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-dark-lighter ring-1 ring-black ring-opacity-5 focus:outline-none hidden';
        this.menu.setAttribute('role', 'menu');
        this.menu.setAttribute('aria-orientation', 'vertical');
        this.menu.setAttribute('aria-labelledby', 'user-menu-button');
        this.menu.setAttribute('tabindex', '-1');

        const menuItems = [
            { text: '个人资料', icon: '<i class="fas fa-user"></i>', action: () => console.log('Profile clicked') },
            { text: '我的收藏', icon: '<i class="fas fa-heart"></i>', action: () => console.log('Favorites clicked') },
            { text: '最近游戏', icon: '<i class="fas fa-clock"></i>', action: () => console.log('Recent clicked') },
            { text: '设置', icon: '<i class="fas fa-cog"></i>', action: () => console.log('Settings clicked') },
            { text: '退出', icon: '<i class="fas fa-sign-out-alt"></i>', action: () => this.handleLogout() }
        ];

        menuItems.forEach(item => {
            const menuItem = document.createElement('a');
            menuItem.href = '#';
            menuItem.className = 'group flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-dark-purple hover:text-white transition-colors duration-150';
            menuItem.setAttribute('role', 'menuitem');
            menuItem.innerHTML = `
                <span class="mr-3 text-gray-400 group-hover:text-white">${item.icon}</span>
                ${item.text}
            `;
            menuItem.addEventListener('click', (e) => {
                e.preventDefault();
                item.action();
                this.closeMenu();
            });
            this.menu.appendChild(menuItem);
        });

        this.container.appendChild(this.menu);
    }

    addEventListeners() {
        this.button.addEventListener('click', () => this.toggleMenu());
        
        document.addEventListener('click', (e) => {
            if (!this.button.contains(e.target) && !this.menu.contains(e.target)) {
                this.closeMenu();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeMenu();
            }
        });
    }

    toggleMenu() {
        this.isOpen ? this.closeMenu() : this.openMenu();
    }

    openMenu() {
        this.menu.classList.remove('hidden');
        this.button.setAttribute('aria-expanded', 'true');
        this.isOpen = true;
    }

    closeMenu() {
        this.menu.classList.add('hidden');
        this.button.setAttribute('aria-expanded', 'false');
        this.isOpen = false;
    }

    handleLogout() {
        console.log('Logging out...');
        Toast.show('成功退出登录', 'success');
        // 这里添加实际的退出登录逻辑
    }
}

const userMenu = new UserMenu();
export default userMenu; 
                {/* Change Avatar */}
                <button
                    onClick={handleOpenAvatarSelector}
                    className="w-full text-left px-4 py-2 text-white hover:bg-gray-700 transition-colors"
                >
                    <i className="fas fa-user-circle mr-2"></i>
                    更换头像
                </button>

                {/* Favorites */}
                <a href="/favorites" 
                   className="block px-4 py-2 text-white hover:bg-gray-700 transition-colors">
                    <i className="fas fa-heart mr-2"></i>
                    Favorites
                </a>

                {/* Divider */}
                <hr className="my-1 border-gray-700" />

                {/* Logout */}
                <button
                    onClick={handleLogout}
                    className="w-full text-left px-4 py-2 text-white hover:bg-red-600 transition-colors"
                >
                    <i className="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </button>
            </div>

            {/* 头像选择器弹窗 */}
            {showAvatarSelector && (
                <div className="fixed inset-0 z-50 flex items-center justify-center">
                    <div className="fixed inset-0 bg-black opacity-50" onClick={() => setShowAvatarSelector(false)}></div>
                    <div className="relative bg-dark-lighter rounded-lg p-6 max-w-lg w-full mx-4">
                        <h3 className="text-xl font-bold text-white mb-4">选择头像</h3>
                        
                        {/* 随机头像选项 */}
                        <div className="grid grid-cols-3 gap-4 mb-6">
                            {avatarOptions.map((avatar, index) => (
                                <div 
                                    key={index} 
                                    className="cursor-pointer p-2 rounded-lg hover:bg-gray-700 transition-colors"
                                    onClick={() => handleUpdateAvatar(avatar)}
                                >
                                    <img 
                                        src={avatar} 
                                        alt={`Avatar option ${index + 1}`}
                                        className="w-full h-auto rounded-lg"
                                    />
                                </div>
                            ))}
                        </div>

                        {/* 操作按钮 */}
                        <div className="flex flex-wrap gap-4">
                            <button
                                onClick={generateAvatarOptions}
                                className="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                            >
                                换一批
                            </button>
                            
                            <label className="flex-1">
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleCustomAvatarUpload}
                                    className="hidden"
                                />
                                <div className="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors text-center cursor-pointer">
                                    上传图片
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default UserMenu; 
class UserMenu {
    constructor() {
        this.button = document.querySelector('.user-menu-button');
        this.container = document.querySelector('.user-menu-container');
        this.menu = null;
        this.isOpen = false;
        
        this.init();
    }

    init() {
        this.createMenu();
        this.addEventListeners();
    }

    createMenu() {
        this.menu = document.createElement('div');
        this.menu.className = 'absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-dark-lighter ring-1 ring-black ring-opacity-5 focus:outline-none hidden';
        this.menu.setAttribute('role', 'menu');
        this.menu.setAttribute('aria-orientation', 'vertical');
        this.menu.setAttribute('aria-labelledby', 'user-menu-button');
        this.menu.setAttribute('tabindex', '-1');

        const menuItems = [
            { text: '个人资料', icon: '<i class="fas fa-user"></i>', action: () => console.log('Profile clicked') },
            { text: '我的收藏', icon: '<i class="fas fa-heart"></i>', action: () => console.log('Favorites clicked') },
            { text: '最近游戏', icon: '<i class="fas fa-clock"></i>', action: () => console.log('Recent clicked') },
            { text: '设置', icon: '<i class="fas fa-cog"></i>', action: () => console.log('Settings clicked') },
            { text: '退出', icon: '<i class="fas fa-sign-out-alt"></i>', action: () => this.handleLogout() }
        ];

        menuItems.forEach(item => {
            const menuItem = document.createElement('a');
            menuItem.href = '#';
            menuItem.className = 'group flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-dark-purple hover:text-white transition-colors duration-150';
            menuItem.setAttribute('role', 'menuitem');
            menuItem.innerHTML = `
                <span class="mr-3 text-gray-400 group-hover:text-white">${item.icon}</span>
                ${item.text}
            `;
            menuItem.addEventListener('click', (e) => {
                e.preventDefault();
                item.action();
                this.closeMenu();
            });
            this.menu.appendChild(menuItem);
        });

        this.container.appendChild(this.menu);
    }

    addEventListeners() {
        this.button.addEventListener('click', () => this.toggleMenu());
        
        document.addEventListener('click', (e) => {
            if (!this.button.contains(e.target) && !this.menu.contains(e.target)) {
                this.closeMenu();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeMenu();
            }
        });
    }

    toggleMenu() {
        this.isOpen ? this.closeMenu() : this.openMenu();
    }

    openMenu() {
        this.menu.classList.remove('hidden');
        this.button.setAttribute('aria-expanded', 'true');
        this.isOpen = true;
    }

    closeMenu() {
        this.menu.classList.add('hidden');
        this.button.setAttribute('aria-expanded', 'false');
        this.isOpen = false;
    }

    handleLogout() {
        console.log('Logging out...');
        Toast.show('成功退出登录', 'success');
        // 这里添加实际的退出登录逻辑
    }
}

const userMenu = new UserMenu();
export default userMenu; 