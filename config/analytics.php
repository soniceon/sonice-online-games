<?php
/**
 * Google Analytics 配置
 * 请将 G-XXXXXXXXXX 替换为您的实际 GA4 测量 ID
 */

// Google Analytics 4 配置
$analytics_config = [
    'ga4_id' => 'G-C6DQJE930Z', // 您的 GA4 测量 ID
    'domain' => 'sonice.games',
    'debug_mode' => false, // 开发环境可设为 true
    'enhanced_ecommerce' => true,
    'user_id_tracking' => true,
    'custom_dimensions' => [
        'dimension1' => 'user_type', // 用户类型（游客/注册用户）
        'dimension2' => 'game_category', // 游戏分类
        'dimension3' => 'game_platform', // 游戏平台
        'dimension4' => 'user_country', // 用户国家
        'dimension5' => 'session_duration' // 会话时长
    ],
    'custom_metrics' => [
        'metric1' => 'games_played', // 游戏次数
        'metric2' => 'search_queries', // 搜索次数
        'metric3' => 'favorites_added' // 收藏次数
    ]
];

// 事件配置
$analytics_events = [
    'game_play' => [
        'category' => 'Games',
        'action' => 'play',
        'label' => 'game_title',
        'value' => 1
    ],
    'game_search' => [
        'category' => 'Search',
        'action' => 'search',
        'label' => 'search_term',
        'value' => 1
    ],
    'category_view' => [
        'category' => 'Navigation',
        'action' => 'view',
        'label' => 'category_name',
        'value' => 1
    ],
    'user_login' => [
        'category' => 'User',
        'action' => 'login',
        'label' => 'login_method',
        'value' => 1
    ],
    'user_register' => [
        'category' => 'User',
        'action' => 'register',
        'label' => 'registration_method',
        'value' => 1
    ],
    'favorite_add' => [
        'category' => 'User',
        'action' => 'favorite_add',
        'label' => 'game_title',
        'value' => 1
    ],
    'favorite_remove' => [
        'category' => 'User',
        'action' => 'favorite_remove',
        'label' => 'game_title',
        'value' => 1
    ]
];

// 获取当前用户类型
function getUserType() {
    return isset($_SESSION['user_id']) ? 'registered' : 'guest';
}

// 获取用户国家（简化版）
function getUserCountry() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    // 这里可以集成 IP 地理位置服务
    return 'unknown';
}

// 生成 GA4 配置脚本
function generateGA4Script($config) {
    $ga4_id = $config['ga4_id'];
    $domain = $config['domain'];
    
    return "
    <!-- Google Analytics 4 -->
    <script async src=\"https://www.googletagmanager.com/gtag/js?id=G-C6DQJE930Z\"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-C6DQJE930Z', {
            'page_title': '{{page_title|default(\"Welcome\")}}',
            'page_location': 'https://{$domain}{{page_url|default(\"/\")}}',
            'custom_map': {
                'dimension1': 'user_type',
                'dimension2': 'game_category',
                'dimension3': 'game_platform',
                'dimension4': 'user_country',
                'dimension5': 'session_duration'
            },
            'user_id': '{{user.id|default(\"\")}}',
            'user_properties': {
                'user_type': '" . getUserType() . "',
                'user_country': '" . getUserCountry() . "'
            }
        });
        
        // 自定义事件跟踪函数
        function trackGamePlay(gameTitle, gameCategory) {
            gtag('event', 'game_play', {
                'event_category': 'Games',
                'event_label': gameTitle,
                'custom_map': {
                    'dimension2': gameCategory
                },
                'value': 1
            });
        }
        
        function trackGameSearch(searchTerm) {
            gtag('event', 'search', {
                'search_term': searchTerm,
                'event_category': 'Search',
                'value': 1
            });
        }
        
        function trackCategoryView(categoryName) {
            gtag('event', 'category_view', {
                'event_category': 'Navigation',
                'event_label': categoryName,
                'value': 1
            });
        }
        
        function trackUserLogin(method) {
            gtag('event', 'login', {
                'method': method,
                'event_category': 'User',
                'value': 1
            });
        }
        
        function trackUserRegister(method) {
            gtag('event', 'sign_up', {
                'method': method,
                'event_category': 'User',
                'value': 1
            });
        }
        
        function trackFavoriteAdd(gameTitle) {
            gtag('event', 'favorite_add', {
                'event_category': 'User',
                'event_label': gameTitle,
                'value': 1
            });
        }
        
        function trackFavoriteRemove(gameTitle) {
            gtag('event', 'favorite_remove', {
                'event_category': 'User',
                'event_label': gameTitle,
                'value': 1
            });
        }
        
        // 页面加载完成后的跟踪
        document.addEventListener('DOMContentLoaded', function() {
            // 跟踪页面浏览
            gtag('event', 'page_view', {
                'page_title': document.title,
                'page_location': window.location.href
            });
        });
    </script>
    ";
}

// 导出配置
return [
    'config' => $analytics_config,
    'events' => $analytics_events,
    'generate_script' => 'generateGA4Script'
];
?> 