/**
 * iframe Proxy Tester Script
 * Tests different proxy methods to solve CORS issues when loading games in iframes
 */

document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('iframe-url');
    const testBtn = document.getElementById('test-btn');
    const proxyBtn = document.getElementById('proxy-test-btn');
    const directBtn = document.getElementById('direct-test-btn');
    const clearBtn = document.getElementById('clear-btn');
    const iframeContainer = document.getElementById('iframe-container');
    const placeholder = document.getElementById('placeholder');
    const statusContainer = document.getElementById('status-container');
    const historyList = document.getElementById('history-list');
    
    // 从本地存储加载历史记录
    let urlHistory = JSON.parse(localStorage.getItem('iframeTestHistory') || '[]');
    
    // 显示历史记录
    updateHistoryList();
    
    // 测试按钮点击事件 - 直接模式
    if (directBtn) {
        directBtn.addEventListener('click', function() {
            const url = urlInput.value.trim();
            
            if (!url) {
                showStatus('请输入有效的URL', 'error');
                return;
            }
            
            testDirectIframe(url);
        });
    }
    
    // 测试按钮点击事件 - 代理模式
    if (proxyBtn) {
        proxyBtn.addEventListener('click', function() {
            const url = urlInput.value.trim();
            
            if (!url) {
                showStatus('请输入有效的URL', 'error');
                return;
            }
            
            testProxyIframe(url);
        });
    }
    
    // 兼容性处理 - 如果只有一个测试按钮
    if (testBtn) {
        testBtn.addEventListener('click', function() {
            const url = urlInput.value.trim();
            
            if (!url) {
                showStatus('请输入有效的URL', 'error');
                return;
            }
            
            // 默认使用代理模式
            testProxyIframe(url);
        });
    }
    
    // 清除按钮点击事件
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            urlInput.value = '';
            clearIframe();
            statusContainer.style.display = 'none';
        });
    }
    
    // 测试直接iframe加载
    function testDirectIframe(url) {
        // 清除之前的iframe
        clearIframe();
        
        // 创建新的iframe
        const iframe = document.createElement('iframe');
        iframe.className = 'test-iframe';
        iframe.src = url;
        
        // 显示加载状态
        showStatus('正在直接加载iframe...', 'info');
        
        // 处理iframe加载事件
        iframe.addEventListener('load', function() {
            showStatus(`iframe直接加载成功！URL可正常访问。`, 'success');
            addToHistory(url, true, 'direct');
        });
        
        // 处理iframe加载错误
        iframe.addEventListener('error', function() {
            showStatus(`iframe直接加载失败。URL可能不允许在iframe中嵌入，或者资源不存在。`, 'error');
            addToHistory(url, false, 'direct');
        });
        
        // 添加iframe到容器
        placeholder.style.display = 'none';
        iframeContainer.appendChild(iframe);
    }
    
    // 测试代理iframe加载
    function testProxyIframe(url) {
        // 清除之前的iframe
        clearIframe();
        
        // 显示加载状态
        showStatus('正在通过代理加载iframe...', 'info');
        
        // 创建游戏容器div
        const gameContainer = document.createElement('div');
        gameContainer.className = 'test-iframe';
        gameContainer.id = 'proxy-game-container';
        
        // 添加容器到页面
        placeholder.style.display = 'none';
        iframeContainer.appendChild(gameContainer);
        
        // 使用Javascript创建iframe并注入
        try {
            // 创建一个新的iframe元素
            const iframe = document.createElement('iframe');
            iframe.style.width = '100%';
            iframe.style.height = '100%';
            iframe.style.border = 'none';
            iframe.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture";
            iframe.allowFullscreen = true;
            
            // 设置iframe的src为真实游戏URL
            iframe.src = url;
            
            // 添加iframe到容器
            gameContainer.appendChild(iframe);
            
            // 处理iframe加载成功事件
            iframe.onload = function() {
                showStatus(`通过代理加载成功！URL可正常访问。推荐在生成游戏页面时使用代理模式。`, 'success');
                addToHistory(url, true, 'proxy');
            };
            
            // 处理iframe加载错误
            iframe.onerror = function() {
                showStatus(`通过代理加载失败。此URL可能不允许在任何站点中嵌入。`, 'error');
                addToHistory(url, false, 'proxy');
            };
        } catch (error) {
            showStatus(`代理加载出错: ${error.message}`, 'error');
            addToHistory(url, false, 'proxy');
        }
    }
    
    // 清除iframe
    function clearIframe() {
        iframeContainer.innerHTML = '';
        placeholder.style.display = 'flex';
        iframeContainer.appendChild(placeholder);
    }
    
    // 显示状态信息
    function showStatus(message, type) {
        statusContainer.innerHTML = message;
        statusContainer.style.display = 'block';
        statusContainer.className = 'status-container';
        
        if (type === 'success') {
            statusContainer.classList.add('status-success');
        } else if (type === 'error') {
            statusContainer.classList.add('status-error');
        }
    }
    
    // 添加到历史记录
    function addToHistory(url, success, mode) {
        // 检查是否已经存在这个URL
        const existingIndex = urlHistory.findIndex(item => item.url === url && item.mode === mode);
        
        if (existingIndex !== -1) {
            // 如果存在，更新状态和时间
            urlHistory[existingIndex].success = success;
            urlHistory[existingIndex].time = new Date().toISOString();
        } else {
            // 如果不存在，添加新记录
            urlHistory.unshift({
                url: url,
                success: success,
                mode: mode || 'direct',
                time: new Date().toISOString()
            });
            
            // 保持记录数量在15个以内
            if (urlHistory.length > 15) {
                urlHistory.pop();
            }
        }
        
        // 保存到本地存储
        localStorage.setItem('iframeTestHistory', JSON.stringify(urlHistory));
        
        // 更新显示
        updateHistoryList();
    }
    
    // 更新历史记录列表
    function updateHistoryList() {
        if (!historyList) return;
        
        historyList.innerHTML = '';
        
        if (urlHistory.length === 0) {
            const emptyItem = document.createElement('li');
            emptyItem.className = 'history-item';
            emptyItem.textContent = '无测试记录';
            historyList.appendChild(emptyItem);
            return;
        }
        
        urlHistory.forEach((item, index) => {
            const listItem = document.createElement('li');
            listItem.className = 'history-item';
            
            const urlSpan = document.createElement('span');
            urlSpan.className = 'history-url';
            urlSpan.textContent = item.url;
            listItem.appendChild(urlSpan);
            
            const statusSpan = document.createElement('span');
            statusSpan.className = 'history-status ' + (item.success ? 'success' : 'error');
            statusSpan.textContent = item.success ? '成功' : '失败';
            statusSpan.title = item.mode === 'proxy' ? '代理模式' : '直接加载';
            listItem.appendChild(statusSpan);
            
            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'history-actions';
            
            // 直接测试按钮
            const directTestBtn = document.createElement('button');
            directTestBtn.className = 'history-btn';
            directTestBtn.textContent = '直接测试';
            directTestBtn.addEventListener('click', () => {
                urlInput.value = item.url;
                testDirectIframe(item.url);
            });
            actionsDiv.appendChild(directTestBtn);
            
            // 代理测试按钮
            const proxyTestBtn = document.createElement('button');
            proxyTestBtn.className = 'history-btn';
            proxyTestBtn.textContent = '代理测试';
            proxyTestBtn.addEventListener('click', () => {
                urlInput.value = item.url;
                testProxyIframe(item.url);
            });
            actionsDiv.appendChild(proxyTestBtn);
            
            // 删除按钮
            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'history-btn';
            deleteBtn.textContent = '删除';
            deleteBtn.addEventListener('click', () => {
                urlHistory.splice(index, 1);
                localStorage.setItem('iframeTestHistory', JSON.stringify(urlHistory));
                updateHistoryList();
            });
            actionsDiv.appendChild(deleteBtn);
            
            listItem.appendChild(actionsDiv);
            
            historyList.appendChild(listItem);
        });
    }
});

// 生成随机字符串，用于避免缓存
function randomString(length = 8) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
} 