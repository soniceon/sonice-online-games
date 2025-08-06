/**
 * Sonice·Online Games - 全局资源优化脚本
 * 用于非游戏页面的资源优化，如首页、分类页面、搜索结果页等
 */

document.addEventListener('DOMContentLoaded', function() {
    // 确保SoniceResources已加载
    if (typeof SoniceResources === 'undefined') {
        console.error('SoniceResources未找到，请确保已加载lazy-load-cdn.js');
        return;
    }
    
    // 预加载全局关键资源
    preloadGlobalResources();
    
    // 优化页面顶部的元素
    optimizeHeaderElements();
    
    // 优化游戏卡片和网格
    optimizeGameGrids();
    
    // 优化轮播图和横幅
    optimizeCarousels();
    
    // 优化搜索和筛选功能
    optimizeSearchElements();
    
    // 监听动态加载内容
    observeContentChanges();
});

/**
 * 预加载全局关键资源
 */
function preloadGlobalResources() {
    SoniceResources.preloadImages([
        'assets/images/icons/logo.png',
        'assets/images/user/default-avatar.png',
        'assets/images/placeholder.png'
    ]);
}

/**
 * 优化页面顶部的元素（头部、导航等）
 */
function optimizeHeaderElements() {
    // 优化用户头像
    const userAvatars = document.querySelectorAll('#userAvatar, .user-avatar');
    userAvatars.forEach(avatar => {
        if (!avatar.classList.contains('lazy-load')) {
            // 保存原始src
            if (!avatar.dataset.src && avatar.src) {
                avatar.dataset.src = avatar.src;
                avatar.removeAttribute('src');
                avatar.classList.add('lazy-load', 'user-avatar');
                
                const parent = avatar.parentElement;
                if (parent) {
                    if (!parent.style.position || parent.style.position === 'static') {
                        parent.style.position = 'relative';
                    }
                    
                    // 添加占位符，如果还没有
                    if (!parent.querySelector('.lazy-placeholder')) {
                        parent.appendChild(SoniceResources.createPlaceholder('fas fa-user'));
                    }
                }
            }
        }
    });
    
    // 优化站点Logo
    const logoImages = document.querySelectorAll('.site-logo, header img[alt*="logo" i], header img[alt*="Logo" i]');
    logoImages.forEach(img => {
        // 直接设置src，不使用懒加载（Logo是关键资源）
        if (img.getAttribute('src') && !img.getAttribute('src').startsWith('http')) {
            img.src = SoniceResources.buildUrl(img.getAttribute('src'), { optimizeExtension: false });
        }
    });
    
    // 应用懒加载到用户头像
    SoniceResources.setupLazyLoad(userAvatars);
}

/**
 * 优化游戏卡片和网格
 */
function optimizeGameGrids() {
    // 查找所有游戏卡片容器
    const gameGrids = document.querySelectorAll('.game-grid, .game-container, .games-list');
    
    gameGrids.forEach(grid => {
        // 查找所有普通图片并转换为懒加载
        const normalImages = grid.querySelectorAll('img:not(.lazy-load)');
        normalImages.forEach(img => {
            // 保存原始src
            if (!img.dataset.src && img.src) {
                const originalSrc = img.getAttribute('src');
                img.dataset.src = originalSrc;
                img.removeAttribute('src');
                img.classList.add('lazy-load', 'game-thumbnail');
                
                // 确保父容器有相对定位
                const parent = img.closest('.game-img') || img.parentElement;
                if (parent) {
                    if (!parent.style.position || parent.style.position === 'static') {
                        parent.style.position = 'relative';
                    }
                    
                    // 添加占位符，如果还没有
                    if (!parent.querySelector('.lazy-placeholder')) {
                        parent.appendChild(SoniceResources.createPlaceholder());
                    }
                }
            }
        });
        
        // 应用懒加载
        SoniceResources.setupLazyLoad(grid.querySelectorAll('.lazy-load'));
    });
}

/**
 * 优化轮播图和横幅
 */
function optimizeCarousels() {
    // 查找所有轮播图和横幅
    const carousels = document.querySelectorAll('.carousel, .banner, .slider, .hero-section');
    
    carousels.forEach(carousel => {
        // 查找所有背景图片的元素
        const elementsWithBackground = carousel.querySelectorAll('[style*="background-image"]');
        elementsWithBackground.forEach(el => {
            // 获取背景图片URL
            const style = window.getComputedStyle(el);
            const bgImage = style.backgroundImage;
            
            if (bgImage && bgImage !== 'none' && !el.dataset.bgSrc) {
                // 提取URL
                const urlMatch = bgImage.match(/url\(['"]?([^'"]+)['"]?\)/);
                if (urlMatch && urlMatch[1]) {
                    // 保存原始URL
                    el.dataset.bgSrc = urlMatch[1];
                    
                    // 移除内联样式中的背景图片
                    el.style.backgroundImage = 'none';
                    
                    // 添加懒加载类
                    el.classList.add('bg-lazy-load');
                    
                    // 确保有相对定位
                    if (!el.style.position || el.style.position === 'static') {
                        el.style.position = 'relative';
                    }
                    
                    // 添加占位符
                    if (!el.querySelector('.lazy-placeholder')) {
                        el.appendChild(SoniceResources.createPlaceholder());
                    }
                }
            }
        });
        
        // 应用懒加载
        SoniceResources.setupLazyLoad(carousel.querySelectorAll('.bg-lazy-load'));
        
        // 优化轮播图中的普通图片
        const images = carousel.querySelectorAll('img:not(.lazy-load)');
        images.forEach(img => {
            // 首个轮播图项目的图片应该立即加载
            const isFirstSlide = img.closest('.carousel-item:first-child, .slider-item:first-child, .slide:first-child');
            
            if (isFirstSlide) {
                // 对于首个轮播项，直接设置CDN路径但不使用懒加载
                if (img.getAttribute('src')) {
                    img.src = SoniceResources.buildUrl(img.getAttribute('src'), { optimizeExtension: true });
                }
            } else {
                // 其他轮播项使用懒加载
                if (!img.dataset.src && img.src) {
                    img.dataset.src = img.getAttribute('src');
                    img.removeAttribute('src');
                    img.classList.add('lazy-load');
                    
                    const parent = img.parentElement;
                    if (parent) {
                        if (!parent.style.position || parent.style.position === 'static') {
                            parent.style.position = 'relative';
                        }
                        
                        if (!parent.querySelector('.lazy-placeholder')) {
                            parent.appendChild(SoniceResources.createPlaceholder());
                        }
                    }
                }
            }
        });
        
        // 为非首个轮播项图片应用懒加载
        SoniceResources.setupLazyLoad(carousel.querySelectorAll('img.lazy-load'));
    });
}

/**
 * 优化搜索和筛选功能
 */
function optimizeSearchElements() {
    // 查找所有搜索建议
    const searchSuggestionsContainers = document.querySelectorAll('#search-suggestions, .search-suggestions');
    
    // 为动态生成的搜索建议添加一个MutationObserver
    searchSuggestionsContainers.forEach(container => {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                // 如果有节点添加
                if (mutation.addedNodes.length > 0) {
                    // 查找新添加的图片并应用懒加载
                    const newImages = container.querySelectorAll('img:not(.lazy-load)');
                    newImages.forEach(img => {
                        if (!img.dataset.src && img.src) {
                            img.dataset.src = img.getAttribute('src');
                            img.removeAttribute('src');
                            img.classList.add('lazy-load', 'game-thumbnail');
                            
                            const parent = img.parentElement;
                            if (parent) {
                                if (!parent.style.position || parent.style.position === 'static') {
                                    parent.style.position = 'relative';
                                }
                                
                                if (!parent.querySelector('.lazy-placeholder')) {
                                    parent.appendChild(SoniceResources.createPlaceholder());
                                }
                            }
                        }
                    });
                    
                    // 应用懒加载
                    SoniceResources.setupLazyLoad(container.querySelectorAll('.lazy-load'));
                }
            });
        });
        
        // 观察容器内的变化，包括子树的变化
        observer.observe(container, { childList: true, subtree: true });
    });
}

/**
 * 监听动态加载内容
 */
function observeContentChanges() {
    // 监听主内容区域的变化
    const mainContent = document.querySelector('main, #main-content, .main-content');
    
    if (mainContent) {
        const observer = new MutationObserver((mutations) => {
            let hasNewImages = false;
            
            mutations.forEach(mutation => {
                if (mutation.addedNodes.length > 0) {
                    // 查看是否有添加新的图片
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            // 如果添加的是元素节点
                            if (node.tagName === 'IMG' && !node.classList.contains('lazy-load')) {
                                hasNewImages = true;
                            } else {
                                // 检查添加的元素内部是否有图片
                                const images = node.querySelectorAll('img:not(.lazy-load)');
                                if (images.length > 0) {
                                    hasNewImages = true;
                                }
                            }
                        }
                    });
                }
            });
            
            // 如果检测到新图片，应用优化
            if (hasNewImages) {
                // 延迟一小段时间确保DOM已完全更新
                setTimeout(() => {
                    optimizeGameGrids();
                    optimizeCarousels();
                }, 50);
            }
        });
        
        // 开始观察
        observer.observe(mainContent, { childList: true, subtree: true });
    }
    
    // 监听动态加载更多内容的按钮
    const loadMoreButtons = document.querySelectorAll('.load-more, [data-action="load-more"]');
    loadMoreButtons.forEach(button => {
        button.addEventListener('click', () => {
            // 延迟执行优化，等待新内容加载
            setTimeout(() => {
                optimizeGameGrids();
            }, 500);
        });
    });
    
    // 监听无限滚动事件
    let lastScrollY = window.scrollY;
    let scrollTimer;
    
    window.addEventListener('scroll', () => {
        // 如果已经滚动到接近底部
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 500) {
            // 如果滚动方向向下且与上次滚动事件相差较大
            if (window.scrollY > lastScrollY + 100) {
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(() => {
                    // 检查是否有新加载的图片
                    const newImages = document.querySelectorAll('img:not(.lazy-load):not([data-ignored])');
                    if (newImages.length > 0) {
                        optimizeGameGrids();
                    }
                }, 300);
            }
        }
        lastScrollY = window.scrollY;
    });
} 