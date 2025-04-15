class PageLoader {
    static async init() {
        await this.loadTemplates();
        this.initAnalytics();
        this.setupEventTracking();
    }

    static async loadTemplates() {
        try {
            await Promise.all([
                this.loadComponent('header-container', '../templates/header.html'),
                this.loadComponent('footer-container', '../templates/footer.html')
            ]);
        } catch (error) {
            console.error('Error loading templates:', error);
        }
    }

    static async loadComponent(elementId, templatePath) {
        try {
            const response = await fetch(templatePath);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const html = await response.text();
            const element = document.getElementById(elementId);
            if (element) {
                element.innerHTML = html;
            }
        } catch (error) {
            console.error(`Error loading ${templatePath}:`, error);
        }
    }

    static initAnalytics() {
        const pageName = document.title.split(' - ')[0];
        if (pageName) {
            this.trackPageView(pageName);
        }
    }

    static trackPageView(pageName) {
        if (typeof gtag === 'function') {
            gtag('event', 'page_view', {
                page_title: pageName,
                page_location: window.location.href,
                page_path: window.location.pathname
            });
        }
    }

    static setupEventTracking() {
        document.addEventListener('click', (event) => {
            const target = event.target.closest('[data-track]');
            if (target) {
                const eventName = target.dataset.track;
                const eventCategory = target.dataset.category || 'engagement';
                this.trackEvent(eventName, eventCategory);
            }
        });
    }

    static trackEvent(eventName, eventCategory) {
        if (typeof gtag === 'function') {
            gtag('event', eventName, {
                event_category: eventCategory,
                event_label: document.title
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => PageLoader.init());