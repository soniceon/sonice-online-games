class TemplateLoader {
    static async load(elementId, templatePath) {
        try {
            const response = await fetch(templatePath);
            if (!response.ok) {
                throw new Error(`Failed to load template: ${response.status}`);
            }
            const html = await response.text();
            const element = document.getElementById(elementId);
            if (element) {
                element.innerHTML = html;
            }
        } catch (error) {
            console.error('Template loading failed:', error);
        }
    }

    static init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.load('header-container', '../templates/header.html');
            this.load('footer-container', '../templates/footer.html');
        });
    }
}

TemplateLoader.init();