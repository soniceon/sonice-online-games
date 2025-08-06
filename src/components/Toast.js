export class Toast {
    static container = null;
    static queue = [];
    static isProcessing = false;

    static init() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'fixed top-4 right-4 z-50 flex flex-col items-end space-y-2';
            document.body.appendChild(this.container);
        }
    }

    static show(message, type = 'info', duration = 3000) {
        this.init();
        this.queue.push({ message, type, duration });
        if (!this.isProcessing) {
            this.processQueue();
        }
    }

    static async processQueue() {
        if (this.queue.length === 0) {
            this.isProcessing = false;
            return;
        }

        this.isProcessing = true;
        const { message, type, duration } = this.queue.shift();
        
        const toast = document.createElement('div');
        toast.className = `transform transition-all duration-300 ease-in-out translate-x-full
            flex items-center p-4 mb-2 text-sm rounded-lg shadow-lg max-w-xs ${this.getTypeStyles(type)}`;
        
        const icon = document.createElement('span');
        icon.className = 'mr-2';
        icon.innerHTML = this.getTypeIcon(type);
        
        const text = document.createElement('span');
        text.textContent = message;
        
        toast.appendChild(icon);
        toast.appendChild(text);
        this.container.appendChild(toast);

        // 动画显示
        await new Promise(resolve => setTimeout(resolve, 100));
        toast.classList.remove('translate-x-full');

        // 等待显示时间
        await new Promise(resolve => setTimeout(resolve, duration));

        // 动画隐藏
        toast.classList.add('translate-x-full');
        await new Promise(resolve => setTimeout(resolve, 300));
        
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }

        // 处理队列中的下一个
        this.processQueue();
    }

    static getTypeStyles(type) {
        const styles = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-white',
            info: 'bg-blue-500 text-white'
        };
        return styles[type] || styles.info;
    }

    static getTypeIcon(type) {
        const icons = {
            success: '<i class="fas fa-check-circle"></i>',
            error: '<i class="fas fa-exclamation-circle"></i>',
            warning: '<i class="fas fa-exclamation-triangle"></i>',
            info: '<i class="fas fa-info-circle"></i>'
        };
        return icons[type] || icons.info;
    }
}

// 创建一个全局实例
export const toast = new Toast(); 