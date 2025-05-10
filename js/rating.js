/**
 * 游戏评分系统
 * 处理游戏页面的评分功能
 */

document.addEventListener('DOMContentLoaded', function() {
    // 初始化评分系统
    initRatingSystem();
});

// 评分系统存储的键前缀
const RATING_KEY_PREFIX = 'sonice_game_rating_';
// 评分计数存储的键前缀
const RATING_COUNT_KEY_PREFIX = 'sonice_game_rating_count_';

/**
 * 初始化评分系统
 */
function initRatingSystem() {
    // 检查当前页面是否有评分容器
    const ratingContainer = document.querySelector('.rating-container');
    if (!ratingContainer) return;
    
    // 从URL获取游戏ID
    const gameId = getGameIdFromUrl();
    if (!gameId) return;
    
    // 获取星星元素
    const stars = document.querySelectorAll('.star');
    if (!stars.length) return;
    
    // 加载保存的评分数据
    loadRatingData(gameId, stars);
    
    // 为每个星星添加点击事件
    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
            const rating = index + 1; // 评分从1到5
            submitRating(gameId, rating, stars);
        });
        
        // 添加鼠标悬停效果
        star.addEventListener('mouseover', () => {
            // 悬停预览
            hoverRatingPreview(index, stars);
        });
        
        // 鼠标移出重置为当前评分
        star.addEventListener('mouseout', () => {
            // 重置为已保存的评分
            resetRatingDisplay(stars);
        });
    });
}

/**
 * 加载游戏评分数据
 * @param {string} gameId 游戏ID
 * @param {NodeList} stars 星星元素列表
 */
function loadRatingData(gameId, stars) {
    // 获取本地存储的评分
    const userRating = getUserRating(gameId);
    
    // 获取平均评分和评分计数
    const averageRating = getAverageRating(gameId);
    const ratingCount = getRatingCount(gameId);
    
    // 更新评分显示
    updateRatingDisplay(stars, userRating);
    
    // 更新评分统计
    updateRatingStats(averageRating, ratingCount);
}

/**
 * 提交评分
 * @param {string} gameId 游戏ID
 * @param {number} rating 评分值(1-5)
 * @param {NodeList} stars 星星元素列表
 */
function submitRating(gameId, rating, stars) {
    // 获取之前的评分
    const previousRating = getUserRating(gameId);
    
    // 保存用户评分
    saveUserRating(gameId, rating);
    
    // 更新评分显示
    updateRatingDisplay(stars, rating);
    
    // 更新服务器上的评分
    updateServerRating(gameId, rating, previousRating)
        .then(response => {
            // 更新本地评分统计
            updateRatingStats(response.averageRating, response.ratingCount);
            
            // 显示感谢信息
            showRatingThankYou();
        })
        .catch(error => {
            console.error('提交评分失败:', error);
            
            // 如果服务器请求失败，回退到本地模拟
            simulateRatingUpdate(gameId, rating, previousRating);
        });
    
    // 给已点击的星星添加动画
    stars.forEach((star, index) => {
        star.classList.remove('just-rated');
        if (index < rating) {
            star.classList.add('just-rated');
        }
    });
}

/**
 * 模拟服务器评分更新（在API不可用时使用）
 * @param {string} gameId 游戏ID
 * @param {number} rating 新评分
 * @param {number|null} previousRating 之前的评分
 */
function simulateRatingUpdate(gameId, rating, previousRating) {
    // 获取当前平均评分和计数
    let averageRating = getAverageRating(gameId);
    let ratingCount = getRatingCount(gameId);
    
    // 计算新的平均评分和计数
    if (previousRating) {
        // 更新现有评分
        const totalRating = averageRating * ratingCount;
        const newTotalRating = totalRating - previousRating + rating;
        averageRating = newTotalRating / ratingCount;
    } else {
        // 添加新评分
        const totalRating = averageRating * ratingCount;
        ratingCount++;
        averageRating = (totalRating + rating) / ratingCount;
    }
    
    // 保存更新后的平均评分和计数
    localStorage.setItem(`${RATING_KEY_PREFIX}avg_${gameId}`, averageRating.toFixed(1));
    localStorage.setItem(`${RATING_COUNT_KEY_PREFIX}${gameId}`, ratingCount.toString());
    
    // 更新评分统计显示
    updateRatingStats(averageRating, ratingCount);
    
    // 显示感谢信息
    showRatingThankYou();
}

/**
 * 悬停时预览评分
 * @param {number} hoveredIndex 悬停的星星索引
 * @param {NodeList} stars 星星元素列表
 */
function hoverRatingPreview(hoveredIndex, stars) {
    stars.forEach((star, index) => {
        if (index <= hoveredIndex) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

/**
 * 重置为已保存的评分显示
 * @param {NodeList} stars 星星元素列表
 */
function resetRatingDisplay(stars) {
    const gameId = getGameIdFromUrl();
    if (!gameId) return;
    
    const userRating = getUserRating(gameId);
    updateRatingDisplay(stars, userRating);
}

/**
 * 更新评分显示
 * @param {NodeList} stars 星星元素列表
 * @param {number|null} rating 评分值
 */
function updateRatingDisplay(stars, rating) {
    stars.forEach((star, index) => {
        if (rating && index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

/**
 * 更新评分统计显示
 * @param {number} averageRating 平均评分
 * @param {number} ratingCount 评分计数
 */
function updateRatingStats(averageRating, ratingCount) {
    const avgElement = document.querySelector('.average-rating');
    const countElement = document.querySelector('.rating-count');
    
    if (avgElement) {
        avgElement.textContent = parseFloat(averageRating).toFixed(1);
    }
    
    if (countElement) {
        countElement.textContent = `(${ratingCount} 评分)`;
    }
}

/**
 * 显示感谢评分的信息
 */
function showRatingThankYou() {
    // 移除可能已存在的感谢信息
    const existingThankYou = document.querySelector('.rating-thank-you');
    if (existingThankYou) {
        existingThankYou.remove();
    }
    
    // 创建新的感谢信息
    const thankYouElement = document.createElement('div');
    thankYouElement.className = 'rating-thank-you';
    thankYouElement.textContent = '感谢您的评分！';
    
    // 添加到页面
    document.body.appendChild(thankYouElement);
    
    // 设置自动消失计时器
    setTimeout(() => {
        thankYouElement.remove();
    }, 2500);
}

/**
 * 从URL获取游戏ID
 * @returns {string|null} 游戏ID或null
 */
function getGameIdFromUrl() {
    // 尝试从查询参数获取
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    
    if (id) return id;
    
    // 尝试从路径获取 (/games/game-id.html)
    const pathMatch = window.location.pathname.match(/\/games\/([^\/]+)\.html$/);
    return pathMatch ? pathMatch[1] : null;
}

/**
 * 获取用户对游戏的评分
 * @param {string} gameId 游戏ID
 * @returns {number|null} 用户评分或null
 */
function getUserRating(gameId) {
    const rating = localStorage.getItem(`${RATING_KEY_PREFIX}${gameId}`);
    return rating ? parseInt(rating) : null;
}

/**
 * 保存用户评分
 * @param {string} gameId 游戏ID
 * @param {number} rating 评分值
 */
function saveUserRating(gameId, rating) {
    localStorage.setItem(`${RATING_KEY_PREFIX}${gameId}`, rating.toString());
}

/**
 * 获取游戏的平均评分
 * @param {string} gameId 游戏ID
 * @returns {number} 平均评分
 */
function getAverageRating(gameId) {
    const rating = localStorage.getItem(`${RATING_KEY_PREFIX}avg_${gameId}`);
    return rating ? parseFloat(rating) : 0;
}

/**
 * 获取游戏的评分计数
 * @param {string} gameId 游戏ID
 * @returns {number} 评分计数
 */
function getRatingCount(gameId) {
    const count = localStorage.getItem(`${RATING_COUNT_KEY_PREFIX}${gameId}`);
    return count ? parseInt(count) : 0;
}

/**
 * 更新服务器上的评分（向服务器发送评分请求）
 * @param {string} gameId 游戏ID
 * @param {number} rating 评分值
 * @param {number|null} previousRating 之前的评分
 * @returns {Promise} 包含平均评分和计数的Promise
 */
function updateServerRating(gameId, rating, previousRating) {
    // 这里应该是一个API调用，如果没有后端，可以模拟一个Promise
    // 在实际项目中，这里应该与后端API通信
    
    // 模拟API响应
    return new Promise((resolve, reject) => {
        // 如果有API，可以替换为实际的fetch调用
        // 例如：
        // fetch('/api/ratings', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ gameId, rating, previousRating })
        // })
        
        // 模拟网络延迟
        setTimeout(() => {
            // 模拟评分更新计算
            let averageRating = getAverageRating(gameId);
            let ratingCount = getRatingCount(gameId);
            
            if (previousRating) {
                // 更新现有评分
                const totalRating = averageRating * ratingCount;
                const newTotalRating = totalRating - previousRating + rating;
                averageRating = newTotalRating / ratingCount;
            } else {
                // 添加新评分
                const totalRating = averageRating * ratingCount;
                ratingCount++;
                averageRating = (totalRating + rating) / ratingCount;
            }
            
            // 保存更新后的平均评分和计数
            localStorage.setItem(`${RATING_KEY_PREFIX}avg_${gameId}`, averageRating.toFixed(1));
            localStorage.setItem(`${RATING_COUNT_KEY_PREFIX}${gameId}`, ratingCount.toString());
            
            resolve({
                success: true,
                averageRating,
                ratingCount
            });
        }, 500);
    });
} 