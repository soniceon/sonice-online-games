/**
 * Sonice·Online Games - Global Lazy Loading and CDN Management Script
 * Provides site-wide image lazy loading and CDN resource management
 */

// Immediately Invoked Function Expression (IIFE) to create a closure
const SoniceResources = (function() {
    // CDN configuration
    const config = {
        // CDN base URL
        cdnBase: 'https://cdn.sonice.online',
        
        // Whether to use local images (set to true for development, false for production)
        useLocalImages: false,
        
        // Lazy loading configuration
        lazyLoad: {
            // How far from the viewport to start loading
            rootMargin: '200px 0px',
            // Placeholder icon style
            placeholderIcon: 'fas fa-gamepad',
            // Default placeholder image
            defaultPlaceholder: '/assets/images/placeholder.png',
            // Default game thumbnail
            defaultGameThumbnail: '/assets/images/games/default-game-thumbnail.webp',
            // Default user avatar
            defaultUserAvatar: '/assets/images/user/default-avatar.png'
        },
        
        // File extension optimization
        fileExtensions: {
            // Whether to use WebP based on browser support
            useWebP: true,
            // Mapping of original image formats to WebP format
            webpMap: {
                '.jpg': '.webp',
                '.jpeg': '.webp',
                '.png': '.webp'
            }
        }
    };
    
    /**
     * Check if the browser supports WebP format
     * @return {Promise<boolean>} Returns a Promise that resolves to a boolean indicating WebP support
     */
    function checkWebPSupport() {
        return new Promise(resolve => {
            const webP = new Image();
            webP.onload = function() { resolve(true); };
            webP.onerror = function() { resolve(false); };
            webP.src = 'data:image/webp;base64,UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA==';
        });
    }
    
    /**
     * Build the final resource URL (CDN or local)
     * @param {string} path - Resource path
     * @param {Object} options - Configuration options
     * @param {boolean} options.forceLocal - Force using local path
     * @param {boolean} options.optimizeExtension - Whether to optimize file extension (e.g. use WebP)
     * @return {string} Processed URL
     */
    function buildUrl(path, options = {}) {
        if (!path) return config.lazyLoad.defaultPlaceholder;
        
        // If it's already a complete URL, return it directly
        if (path.startsWith('http://') || path.startsWith('https://')) {
            return path;
        }
        
        // Remove leading slash
        const cleanPath = path.startsWith('/') ? path.substring(1) : path;
        
        // Process file extension (if optimization is enabled and browser supports WebP)
        let finalPath = cleanPath;
        if (options.optimizeExtension && config.fileExtensions.useWebP && window.supportWebP) {
            const extension = '.' + finalPath.split('.').pop().toLowerCase();
            if (config.fileExtensions.webpMap[extension]) {
                // Replace original extension with WebP
                finalPath = finalPath.replace(
                    new RegExp(extension + '$'), 
                    config.fileExtensions.webpMap[extension]
                );
            }
        }
        
        // Build final URL (CDN or local)
        return (config.useLocalImages || options.forceLocal) 
            ? `/${finalPath}` 
            : `${config.cdnBase}/${finalPath}`;
    }
    
    /**
     * Create image lazy loading placeholder element
     * @param {string} iconClass - Icon class to use
     * @return {HTMLElement} Created placeholder element
     */
    function createPlaceholder(iconClass = config.lazyLoad.placeholderIcon) {
        const placeholder = document.createElement('div');
        placeholder.className = 'lazy-placeholder';
        placeholder.innerHTML = `<i class="${iconClass}"></i>`;
        return placeholder;
    }
    
    /**
     * Set up lazy loading for elements
     * @param {HTMLElement|NodeList|Array} elements - Element(s) to enable lazy loading for
     * @param {Object} options - Lazy loading configuration options
     */
    function setupLazyLoad(elements, options = {}) {
        // If a single element is passed, convert to array
        const items = elements instanceof Element ? [elements] : elements;
        
        // If browser supports IntersectionObserver
        if ('IntersectionObserver' in window) {
            const imgObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;
                        
                        // If it's an image element
                        if (target.tagName === 'IMG') {
                            const src = target.dataset.src;
                            if (src) {
                                // Build final URL
                                const finalSrc = buildUrl(src, { 
                                    optimizeExtension: true,
                                    forceLocal: options.forceLocal || false
                                });
                                
                                // Set image source
                                target.src = finalSrc;
                                
                                // When image is loaded
                                target.addEventListener('load', () => {
                                    // Add loaded class
                                    target.classList.add('loaded');
                                    
                                    // Remove placeholder
                                    const placeholder = target.parentElement.querySelector('.lazy-placeholder');
                                    if (placeholder) {
                                        placeholder.style.opacity = '0';
                                        setTimeout(() => placeholder.remove(), 300);
                                    }
                                });
                                
                                // When image fails to load, use default image
                                target.addEventListener('error', () => {
                                    console.warn(`Image failed to load: ${finalSrc}`);
                                    // Use different default images based on image type
                                    if (target.classList.contains('game-thumbnail')) {
                                        target.src = config.lazyLoad.defaultGameThumbnail;
                                    } else if (target.classList.contains('user-avatar')) {
                                        target.src = config.lazyLoad.defaultUserAvatar;
                                    } else {
                                        target.src = config.lazyLoad.defaultPlaceholder;
                                    }
                                });
                                
                                // Remove data-src attribute
                                target.removeAttribute('data-src');
                            }
                        } 
                        // If it's a background image container
                        else if (target.dataset.bgSrc) {
                            const src = target.dataset.bgSrc;
                            if (src) {
                                // Build final URL
                                const finalSrc = buildUrl(src, { 
                                    optimizeExtension: true,
                                    forceLocal: options.forceLocal || false
                                });
                                
                                // Set background image
                                target.style.backgroundImage = `url('${finalSrc}')`;
                                
                                // Remove placeholder
                                const placeholder = target.querySelector('.lazy-placeholder');
                                if (placeholder) {
                                    placeholder.style.opacity = '0';
                                    setTimeout(() => placeholder.remove(), 300);
                                }
                                
                                // Add loaded class
                                target.classList.add('bg-loaded');
                                
                                // Remove data-bg-src attribute
                                target.removeAttribute('data-bg-src');
                            }
                        }
                        
                        // Stop observing this element
                        observer.unobserve(target);
                    }
                });
            }, {
                rootMargin: options.rootMargin || config.lazyLoad.rootMargin,
                threshold: options.threshold || 0
            });
            
            // Start observing each element
            Array.from(items).forEach(item => {
                imgObserver.observe(item);
            });
        } else {
            // Fallback for browsers that don't support IntersectionObserver
            Array.from(items).forEach(item => {
                // If it's an image
                if (item.tagName === 'IMG' && item.dataset.src) {
                    item.src = buildUrl(item.dataset.src, { 
                        optimizeExtension: true,
                        forceLocal: options.forceLocal || false
                    });
                    item.classList.add('loaded');
                } 
                // If it's a background image container
                else if (item.dataset.bgSrc) {
                    item.style.backgroundImage = `url('${buildUrl(item.dataset.bgSrc, { 
                        optimizeExtension: true,
                        forceLocal: options.forceLocal || false
                    })}')`;
                    item.classList.add('bg-loaded');
                }
                
                // Remove placeholder
                const placeholder = item.tagName === 'IMG' 
                    ? item.parentElement.querySelector('.lazy-placeholder')
                    : item.querySelector('.lazy-placeholder');
                
                if (placeholder) {
                    placeholder.remove();
                }
            });
        }
    }
    
    /**
     * Preload critical image resources
     * @param {Array<string>} paths - Array of image paths to preload
     * @return {Promise<Array>} Promise that resolves when all images are loaded
     */
    function preloadImages(paths) {
        return Promise.all(paths.map(path => {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => resolve(path);
                img.onerror = () => {
                    console.warn(`Failed to preload image: ${path}`);
                    resolve(null); // Resolve promise even on failure to avoid blocking other images
                };
                img.src = buildUrl(path, { optimizeExtension: true });
            });
        }));
    }
    
    /**
     * Initialize the library
     * @param {Object} customConfig - Custom configuration
     */
    async function init(customConfig = {}) {
        // Merge configurations
        Object.assign(config, customConfig);
        
        // Check WebP support
        window.supportWebP = await checkWebPSupport();
        
        // Add global CSS styles (if not already added)
        if (!document.getElementById('sonice-lazyload-styles')) {
            const style = document.createElement('style');
            style.id = 'sonice-lazyload-styles';
            style.textContent = `
                .lazy-placeholder {
                    background-color: #e5e7eb;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: absolute;
                    inset: 0;
                    transition: opacity 0.3s;
                }
                
                .dark .lazy-placeholder {
                    background-color: #374151;
                }
                
                .lazy-placeholder i {
                    font-size: 2rem;
                    color: #9ca3af;
                }
                
                img.lazy-load {
                    opacity: 0;
                    transition: opacity 0.5s;
                }
                
                img.lazy-load.loaded {
                    opacity: 1;
                }
                
                .bg-lazy-load {
                    background-size: cover;
                    background-position: center;
                    transition: opacity 0.5s;
                }
                
                .bg-lazy-load:not(.bg-loaded) {
                    opacity: 0;
                }
                
                .bg-lazy-load.bg-loaded {
                    opacity: 1;
                }
            `;
            document.head.appendChild(style);
        }
        
        // Auto-process all images with data-src attribute on the page
        document.querySelectorAll('img[data-src]').forEach(img => {
            // If parent doesn't have relative positioning, add it
            if (!img.parentElement.classList.contains('lazy-container')) {
                img.parentElement.classList.add('lazy-container');
                img.parentElement.style.position = 'relative';
            }
            
            // If lazy-load class is not already added, add it
            if (!img.classList.contains('lazy-load')) {
                img.classList.add('lazy-load');
            }
            
            // If placeholder doesn't exist, add it
            if (!img.parentElement.querySelector('.lazy-placeholder')) {
                img.parentElement.appendChild(createPlaceholder());
            }
        });
        
        // Auto-process all elements with data-bg-src attribute on the page
        document.querySelectorAll('[data-bg-src]').forEach(el => {
            // Add bg-lazy-load class
            if (!el.classList.contains('bg-lazy-load')) {
                el.classList.add('bg-lazy-load');
            }
            
            // Ensure element has relative positioning
            const position = window.getComputedStyle(el).position;
            if (position === 'static') {
                el.style.position = 'relative';
            }
            
            // If placeholder doesn't exist, add it
            if (!el.querySelector('.lazy-placeholder')) {
                el.appendChild(createPlaceholder());
            }
        });
        
        // Set up lazy loading
        setupLazyLoad(document.querySelectorAll('img.lazy-load, .bg-lazy-load'));
        
        console.log('SoniceResources initialized', {
            useWebP: window.supportWebP,
            cdnEnabled: !config.useLocalImages
        });
    }
    
    // Expose public API
    return {
        init,
        buildUrl,
        setupLazyLoad,
        createPlaceholder,
        preloadImages,
        // Allow users to get and set configuration
        getConfig: () => ({ ...config }), // Return a copy of config to prevent direct modification
        setConfig: (newConfig) => Object.assign(config, newConfig)
    };
})();

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get configuration from <script> tag if available
    const scriptTag = document.querySelector('script[data-sonice-config]');
    let config = {};
    
    if (scriptTag) {
        try {
            config = JSON.parse(scriptTag.dataset.soniceConfig);
        } catch (e) {
            console.error('Failed to parse SoniceResources configuration', e);
        }
    }
    
    // Check if running in local environment (development mode)
    const isLocalhost = window.location.hostname === 'localhost' || 
                       window.location.hostname === '127.0.0.1' ||
                       window.location.hostname.includes('192.168.');
    
    // If in local environment, default to using local images
    if (isLocalhost && config.useLocalImages === undefined) {
        config.useLocalImages = true;
    }
    
    // Initialize
    SoniceResources.init(config);
}); 