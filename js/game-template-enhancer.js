/**
 * Sonice·Online Games - Game Template Enhancer
 * Add lazy loading and CDN features to game template pages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Ensure SoniceResources is loaded
    if (typeof SoniceResources === 'undefined') {
        console.error('SoniceResources not found, please ensure lazy-load-cdn.js is loaded');
        return;
    }
    
    // Enhance game frame loading
    enhanceGameFrame();
    
    // Enhance recommended game cards
    enhanceGameCards();
    
    // Enhance user avatars
    enhanceUserAvatars();
    
    // Lazy load background images
    enhanceBackgroundImages();
    
    // Preload critical game assets
    preloadCriticalAssets();
});

/**
 * Enhance game frame loading
 */
function enhanceGameFrame() {
    const gameFrame = document.getElementById('game-frame');
    const loadingOverlay = document.getElementById('game-loading-overlay');
    const loadingProgress = document.getElementById('loading-progress');
    
    if (!gameFrame || !loadingOverlay) return;
    
    // Listen for game frame load events
    gameFrame.addEventListener('load', function() {
        // Fade out loading overlay
        loadingOverlay.style.opacity = '0';
        setTimeout(() => {
            loadingOverlay.style.display = 'none';
        }, 500);
    });
    
    // Simulate loading progress (in real scenarios, this would update based on actual loading)
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress >= 100) {
            progress = 100;
            clearInterval(progressInterval);
            
            // If game still hasn't loaded after 3 seconds, force hide loading overlay
            setTimeout(() => {
                if (loadingOverlay.style.display !== 'none') {
                    loadingOverlay.style.opacity = '0';
                    setTimeout(() => {
                        loadingOverlay.style.display = 'none';
                    }, 500);
                }
            }, 3000);
        }
        
        if (loadingProgress) {
            loadingProgress.style.width = `${progress}%`;
        }
    }, 200);
}

/**
 * Enhance game cards, apply lazy loading
 */
function enhanceGameCards() {
    // Find all game card containers
    const gameContainers = [
        document.getElementById('related-games'),
        document.getElementById('popular-games'),
        document.getElementById('new-games')
    ].filter(container => container);
    
    gameContainers.forEach(container => {
        // Find all images and apply lazy loading
        const images = container.querySelectorAll('img:not(.lazy-load)');
        images.forEach(img => {
            // Save original src
            if (!img.dataset.src && img.src) {
                img.dataset.src = img.src;
                img.removeAttribute('src');
                img.classList.add('lazy-load', 'game-thumbnail');
                
                // Ensure parent container has relative positioning
                const parent = img.closest('.game-img') || img.parentElement;
                if (parent) {
                    if (!parent.style.position || parent.style.position === 'static') {
                        parent.style.position = 'relative';
                    }
                    
                    // Add placeholder if not already present
                    if (!parent.querySelector('.lazy-placeholder')) {
                        parent.appendChild(SoniceResources.createPlaceholder());
                    }
                }
            }
        });
        
        // Apply lazy loading
        SoniceResources.setupLazyLoad(container.querySelectorAll('.lazy-load'));
    });
    
    // Listen for card clicks, preload resources for games about to be visited
    document.querySelectorAll('.game-card a, [data-game-link="true"]').forEach(link => {
        link.addEventListener('click', function(e) {
            // No need to prevent default behavior, as preloading is parallel
            const href = this.getAttribute('href');
            if (href && href.includes('/games/')) {
                const gameSlug = href.split('/').pop().replace('.html', '');
                
                // Preload game thumbnails
                SoniceResources.preloadImages([
                    `assets/images/games/${gameSlug}-360-240.webp`,
                    `assets/images/games/${gameSlug}-thumbnail.webp`
                ]);
            }
        });
    });
}

/**
 * Enhance user avatars, apply lazy loading
 */
function enhanceUserAvatars() {
    const userAvatars = document.querySelectorAll('#userAvatar, .user-avatar');
    userAvatars.forEach(avatar => {
        if (!avatar.classList.contains('lazy-load')) {
            // Save original src
            if (!avatar.dataset.src && avatar.src) {
                avatar.dataset.src = avatar.src;
                avatar.removeAttribute('src');
                avatar.classList.add('lazy-load', 'user-avatar');
                
                const parent = avatar.parentElement;
                if (parent) {
                    if (!parent.style.position || parent.style.position === 'static') {
                        parent.style.position = 'relative';
                    }
                    
                    // Add placeholder if not already present
                    if (!parent.querySelector('.lazy-placeholder')) {
                        // Use user icon for user avatars
                        parent.appendChild(SoniceResources.createPlaceholder('fas fa-user'));
                    }
                }
            }
        }
    });
    
    // Apply lazy loading
    SoniceResources.setupLazyLoad(document.querySelectorAll('.user-avatar.lazy-load'));
}

/**
 * Enhance background images, apply lazy loading
 */
function enhanceBackgroundImages() {
    // Find elements with background images
    const elementsWithBgImage = Array.from(document.querySelectorAll('[style*="background-image"]'));
    
    elementsWithBgImage.forEach(el => {
        // Get background image URL
        const style = window.getComputedStyle(el);
        const bgImage = style.backgroundImage;
        
        if (bgImage && bgImage !== 'none' && !el.dataset.bgSrc) {
            // Extract URL
            const urlMatch = bgImage.match(/url\(['"]?([^'"]+)['"]?\)/);
            if (urlMatch && urlMatch[1]) {
                // Save original URL
                el.dataset.bgSrc = urlMatch[1];
                // Remove background image from inline style
                el.style.backgroundImage = 'none';
                // Add lazy loading class
                el.classList.add('bg-lazy-load');
                
                // Ensure relative positioning
                if (!el.style.position || el.style.position === 'static') {
                    el.style.position = 'relative';
                }
                
                // Add placeholder
                if (!el.querySelector('.lazy-placeholder')) {
                    el.appendChild(SoniceResources.createPlaceholder());
                }
            }
        }
    });
    
    // Apply lazy loading
    SoniceResources.setupLazyLoad(document.querySelectorAll('.bg-lazy-load'));
}

/**
 * Preload critical game assets
 */
function preloadCriticalAssets() {
    // Get current game slug
    const gameSlug = getCurrentGameSlug();
    
    if (gameSlug) {
        // Preload main assets
        SoniceResources.preloadImages([
            `assets/images/games/${gameSlug}-thumbnail.webp`,
            `assets/images/games/${gameSlug}-banner.webp`
        ]);
    }
    
    // Preload common assets
    SoniceResources.preloadImages([
        'assets/images/icons/logo.png',
        'assets/images/user/default-avatar.png'
    ]);
}

/**
 * Get current game slug
 * @return {string|null} Game slug or null
 */
function getCurrentGameSlug() {
    // Try to get from URL
    const path = window.location.pathname;
    if (path.includes('/games/')) {
        return path.split('/games/')[1].replace('.html', '');
    }
    
    // Try to get from JSON-LD script
    const jsonLdScript = document.querySelector('script[type="application/ld+json"]');
    if (jsonLdScript) {
        try {
            const data = JSON.parse(jsonLdScript.textContent);
            if (data && data.url) {
                const urlParts = data.url.split('/');
                return urlParts[urlParts.length - 1].replace('.html', '');
            }
        } catch (e) {
            console.warn('Failed to parse JSON-LD data', e);
        }
    }
    
    // Try to get from Open Graph meta tag
    const ogUrlMeta = document.querySelector('meta[property="og:url"]');
    if (ogUrlMeta) {
        const url = ogUrlMeta.getAttribute('content');
        if (url && url.includes('/games/')) {
            return url.split('/games/')[1].replace('.html', '');
        }
    }
    
    return null;
} 