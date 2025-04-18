import { Toast } from './Toast.js';

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