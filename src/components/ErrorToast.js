class ErrorToast {
    constructor() {
        this.element = document.createElement('div');
        this.element.className = 'error-toast fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300 translate-x-full';
        document.body.appendChild(this.element);
    }

    show(message, duration = 3000) {
        this.element.textContent = message;
        this.element.classList.remove('translate-x-full');
        
        setTimeout(() => {
            this.hide();
        }, duration);
    }

    hide() {
        this.element.classList.add('translate-x-full');
    }
}

export default ErrorToast; 