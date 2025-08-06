/**
 * 游戏评论系统
 * 处理游戏页面的评论功能
 */

document.addEventListener('DOMContentLoaded', function() {
    // 初始化评论系统
    initCommentSystem();
});

// 评论存储的键前缀
const COMMENT_KEY_PREFIX = 'sonice_game_comments_';

/**
 * 初始化评论系统
 */
function initCommentSystem() {
    // 检查当前页面是否有评论容器
    const commentContainer = document.querySelector('.comment-container');
    if (!commentContainer) return;
    
    // 从URL获取游戏ID
    const gameId = getGameIdFromUrl();
    if (!gameId) return;
    
    // 加载评论
    loadComments(gameId);
    
    // 设置评论提交处理
    setupCommentForm(gameId);
}

/**
 * 设置评论表单提交处理
 * @param {string} gameId 游戏ID
 */
function setupCommentForm(gameId) {
    const commentForm = document.querySelector('.comment-form');
    if (!commentForm) return;
    
    commentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // 获取评论内容
        const commentInput = commentForm.querySelector('.comment-input');
        const commentText = commentInput.value.trim();
        
        if (!commentText) {
            showCommentError('评论内容不能为空');
            return;
        }
        
        // 获取用户名（可以从localStorage获取或使用默认名）
        const username = getUserName();
        
        // 创建评论对象
        const comment = {
            id: generateCommentId(),
            username: username,
            gameId: gameId,
            content: commentText,
            timestamp: Date.now(),
            likes: 0,
            avatar: getRandomAvatar() // 随机头像或默认头像
        };
        
        // 提交评论
        submitComment(gameId, comment)
            .then(() => {
                // 清空输入框
                commentInput.value = '';
                
                // 重新加载评论列表
                loadComments(gameId);
                
                // 显示成功消息
                showCommentSuccess('评论已发布');
            })
            .catch(error => {
                showCommentError('评论发布失败，请稍后重试');
                console.error('提交评论失败:', error);
            });
    });
}

/**
 * 加载游戏评论
 * @param {string} gameId 游戏ID
 */
function loadComments(gameId) {
    // 获取评论列表容器
    const commentsList = document.querySelector('.comments-list');
    if (!commentsList) return;
    
    // 显示加载状态
    commentsList.innerHTML = '<div class="comments-loading">加载评论中...</div>';
    
    // 从服务器或本地存储获取评论
    getComments(gameId)
        .then(comments => {
            if (comments.length === 0) {
                commentsList.innerHTML = '<div class="no-comments">暂无评论，来发表第一条评论吧！</div>';
                return;
            }
            
            // 渲染评论列表
            renderComments(comments, commentsList);
        })
        .catch(error => {
            commentsList.innerHTML = '<div class="comments-error">加载评论失败，请刷新页面重试</div>';
            console.error('加载评论失败:', error);
        });
}

/**
 * 渲染评论列表
 * @param {Array} comments 评论数组
 * @param {Element} container 容器元素
 */
function renderComments(comments, container) {
    // 清空容器
    container.innerHTML = '';
    
    // 按时间排序（最新的在前面）
    comments.sort((a, b) => b.timestamp - a.timestamp);
    
    // 创建评论元素
    comments.forEach(comment => {
        const commentElement = createCommentElement(comment);
        container.appendChild(commentElement);
    });
}

/**
 * 创建单个评论元素
 * @param {Object} comment 评论对象
 * @returns {HTMLElement} 评论HTML元素
 */
function createCommentElement(comment) {
    const commentDiv = document.createElement('div');
    commentDiv.className = 'comment-item';
    commentDiv.dataset.id = comment.id;
    
    // 格式化日期
    const date = new Date(comment.timestamp);
    const formattedDate = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')} ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
    
    commentDiv.innerHTML = `
        <div class="comment-avatar">
            <img src="${comment.avatar}" alt="${comment.username}" />
        </div>
        <div class="comment-content">
            <div class="comment-header">
                <span class="comment-username">${escapeHTML(comment.username)}</span>
                <span class="comment-date">${formattedDate}</span>
            </div>
            <div class="comment-text">${escapeHTML(comment.content)}</div>
            <div class="comment-actions">
                <button class="like-button" data-id="${comment.id}">
                    <i class="icon-like"></i> <span class="like-count">${comment.likes}</span>
                </button>
                <button class="reply-button" data-id="${comment.id}">回复</button>
            </div>
        </div>
    `;
    
    // 添加点赞事件
    const likeButton = commentDiv.querySelector('.like-button');
    likeButton.addEventListener('click', () => {
        likeComment(comment.id);
    });
    
    // 添加回复事件（如果实现回复功能）
    const replyButton = commentDiv.querySelector('.reply-button');
    replyButton.addEventListener('click', () => {
        replyToComment(comment.id);
    });
    
    return commentDiv;
}

/**
 * 提交评论
 * @param {string} gameId 游戏ID
 * @param {Object} comment 评论对象
 * @returns {Promise} 提交结果Promise
 */
function submitComment(gameId, comment) {
    // 这里应该是一个API调用，如果没有后端，可以模拟一个Promise
    return new Promise((resolve, reject) => {
        try {
            // 获取现有评论
            const comments = getCommentsFromStorage(gameId);
            
            // 添加新评论
            comments.push(comment);
            
            // 保存回本地存储
            saveCommentsToStorage(gameId, comments);
            
            // 模拟网络延迟
            setTimeout(() => {
                resolve({ success: true });
            }, 500);
        } catch (error) {
            reject(error);
        }
    });
}

/**
 * 获取评论列表
 * @param {string} gameId 游戏ID
 * @returns {Promise<Array>} 评论数组Promise
 */
function getComments(gameId) {
    // 这里应该是一个API调用，如果没有后端，可以模拟一个Promise
    return new Promise((resolve, reject) => {
        try {
            // 从本地存储获取评论
            const comments = getCommentsFromStorage(gameId);
            
            // 模拟网络延迟
            setTimeout(() => {
                resolve(comments);
            }, 500);
        } catch (error) {
            reject(error);
        }
    });
}

/**
 * 从本地存储获取评论
 * @param {string} gameId 游戏ID
 * @returns {Array} 评论数组
 */
function getCommentsFromStorage(gameId) {
    const commentsJson = localStorage.getItem(`${COMMENT_KEY_PREFIX}${gameId}`);
    return commentsJson ? JSON.parse(commentsJson) : [];
}

/**
 * 保存评论到本地存储
 * @param {string} gameId 游戏ID
 * @param {Array} comments 评论数组
 */
function saveCommentsToStorage(gameId, comments) {
    localStorage.setItem(`${COMMENT_KEY_PREFIX}${gameId}`, JSON.stringify(comments));
}

/**
 * 点赞评论
 * @param {string} commentId 评论ID
 */
function likeComment(commentId) {
    // 获取游戏ID
    const gameId = getGameIdFromUrl();
    if (!gameId) return;
    
    // 获取当前点赞状态，防止重复点赞
    const likedComments = getLikedComments();
    if (likedComments.includes(commentId)) {
        showCommentError('您已经赞过这条评论');
        return;
    }
    
    // 获取所有评论
    const comments = getCommentsFromStorage(gameId);
    
    // 找到要点赞的评论
    const comment = comments.find(c => c.id === commentId);
    if (!comment) return;
    
    // 更新点赞数
    comment.likes = (comment.likes || 0) + 1;
    
    // 保存评论
    saveCommentsToStorage(gameId, comments);
    
    // 更新点赞记录
    likedComments.push(commentId);
    localStorage.setItem('sonice_liked_comments', JSON.stringify(likedComments));
    
    // 更新UI
    const likeCountElement = document.querySelector(`.like-button[data-id="${commentId}"] .like-count`);
    if (likeCountElement) {
        likeCountElement.textContent = comment.likes;
    }
    
    // 添加点赞动画
    const likeButton = document.querySelector(`.like-button[data-id="${commentId}"]`);
    if (likeButton) {
        likeButton.classList.add('liked');
        likeButton.disabled = true;
    }
}

/**
 * 获取已点赞的评论ID列表
 * @returns {Array} 已点赞评论ID数组
 */
function getLikedComments() {
    const likedJson = localStorage.getItem('sonice_liked_comments');
    return likedJson ? JSON.parse(likedJson) : [];
}

/**
 * 回复评论（功能预留）
 * @param {string} commentId 评论ID
 */
function replyToComment(commentId) {
    // 在这里实现回复功能
    const commentInput = document.querySelector('.comment-input');
    if (commentInput) {
        commentInput.focus();
        commentInput.value = `@评论#${commentId.substr(0, 5)} `;
    }
}

/**
 * 获取用户名
 * @returns {string} 用户名
 */
function getUserName() {
    // 从本地存储获取用户名
    let username = localStorage.getItem('sonice_username');
    
    // 如果没有用户名，使用匿名用户或生成随机名称
    if (!username) {
        username = `游客${Math.floor(Math.random() * 10000).toString().padStart(4, '0')}`;
        localStorage.setItem('sonice_username', username);
    }
    
    return username;
}

/**
 * 获取随机头像
 * @returns {string} 头像URL
 */
function getRandomAvatar() {
    // 这里可以使用一些默认头像
    const avatars = [
        'https://api.dicebear.com/6.x/identicon/svg?seed=1',
        'https://api.dicebear.com/6.x/identicon/svg?seed=2',
        'https://api.dicebear.com/6.x/identicon/svg?seed=3',
        'https://api.dicebear.com/6.x/identicon/svg?seed=4',
        'https://api.dicebear.com/6.x/identicon/svg?seed=5'
    ];
    
    // 使用存储的头像或随机选择一个
    let avatarUrl = localStorage.getItem('sonice_avatar');
    
    if (!avatarUrl) {
        avatarUrl = avatars[Math.floor(Math.random() * avatars.length)];
        localStorage.setItem('sonice_avatar', avatarUrl);
    }
    
    return avatarUrl;
}

/**
 * 生成唯一评论ID
 * @returns {string} 唯一ID
 */
function generateCommentId() {
    return Date.now().toString(36) + Math.random().toString(36).substr(2, 5);
}

/**
 * 显示评论错误信息
 * @param {string} message 错误信息
 */
function showCommentError(message) {
    showCommentMessage(message, 'error');
}

/**
 * 显示评论成功信息
 * @param {string} message 成功信息
 */
function showCommentSuccess(message) {
    showCommentMessage(message, 'success');
}

/**
 * 显示评论提示信息
 * @param {string} message 消息内容
 * @param {string} type 消息类型（error/success）
 */
function showCommentMessage(message, type) {
    // 移除可能已存在的消息
    const existingMessage = document.querySelector('.comment-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // 创建新消息
    const messageElement = document.createElement('div');
    messageElement.className = `comment-message ${type}`;
    messageElement.textContent = message;
    
    // 添加到表单附近
    const commentForm = document.querySelector('.comment-form');
    if (commentForm) {
        commentForm.appendChild(messageElement);
    } else {
        document.body.appendChild(messageElement);
    }
    
    // 设置自动消失
    setTimeout(() => {
        messageElement.classList.add('fade-out');
        setTimeout(() => {
            messageElement.remove();
        }, 300);
    }, 2000);
}

/**
 * 转义HTML字符
 * @param {string} text 要转义的文本
 * @returns {string} 转义后的文本
 */
function escapeHTML(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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