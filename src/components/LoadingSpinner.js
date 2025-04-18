class LoadingSpinner {
    constructor() {
        this.element = document.createElement('div');
        this.element.className = 'loading-spinner fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        this.element.innerHTML = `
            <div class="bg-white p-4 rounded-lg flex flex-col items-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-apple-blue"></div>
                <p class="mt-2 text-gray-600">Loading...</p>
            </div>
        `;
        document.body.appendChild(this.element);
        this.hide(); // 默认隐藏
    }

    show() {
        this.element.classList.remove('hidden');
    }

    hide() {
        this.element.classList.add('hidden');
    }
}

export default LoadingSpinner; 