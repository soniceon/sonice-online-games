# Google Analytics 设置指南

## 🎯 概述
本指南将帮助您正确设置Google Analytics 4 (GA4) 来跟踪Sonice Online Games网站的访问数据。

## 📋 设置步骤

### 1. 创建Google Analytics账户

1. 访问 [Google Analytics](https://analytics.google.com/)
2. 点击"开始衡量"
3. 创建账户（Sonice Games）
4. 创建数据流（网站）
5. 获取测量ID（格式：G-XXXXXXXXXX）

### 2. 配置数据流

**基本信息**：
- 网站名称：Sonice Online Games
- 网站URL：https://sonice.games
- 行业类别：游戏
- 时区：根据您的服务器位置设置

**增强型衡量**：
- ✅ 页面浏览
- ✅ 滚动
- ✅ 点击
- ✅ 网站搜索
- ✅ 视频互动
- ✅ 文件下载

### 3. 更新代码

将您的GA4测量ID替换到以下文件中：

#### 更新 `templates/layouts/base.twig`
```twig
<!-- 将 G-XXXXXXXXXX 替换为您的实际测量ID -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
```

#### 更新 `config/analytics.php`
```php
$analytics_config = [
    'ga4_id' => 'G-XXXXXXXXXX', // 替换为您的测量ID
    // ... 其他配置
];
```

### 4. 自定义维度设置

在GA4中创建以下自定义维度：

| 维度名称 | 维度ID | 范围 | 描述 |
|---------|--------|------|------|
| 用户类型 | user_type | 用户 | 游客/注册用户 |
| 游戏分类 | game_category | 事件 | 游戏所属分类 |
| 游戏平台 | game_platform | 事件 | 游戏运行平台 |
| 用户国家 | user_country | 用户 | 用户所在国家 |
| 会话时长 | session_duration | 会话 | 用户会话时长 |

### 5. 自定义指标设置

在GA4中创建以下自定义指标：

| 指标名称 | 指标ID | 范围 | 描述 |
|---------|--------|------|------|
| 游戏次数 | games_played | 事件 | 用户玩游戏的总次数 |
| 搜索次数 | search_queries | 事件 | 用户搜索的总次数 |
| 收藏次数 | favorites_added | 事件 | 用户添加收藏的总次数 |

## 🔍 事件跟踪

### 自动跟踪的事件
- ✅ 页面浏览 (page_view)
- ✅ 用户登录 (login)
- ✅ 用户注册 (sign_up)
- ✅ 搜索 (search)
- ✅ 游戏播放 (game_play)
- ✅ 分类浏览 (category_view)
- ✅ 收藏添加 (favorite_add)
- ✅ 收藏移除 (favorite_remove)

### 手动触发事件
```javascript
// 游戏播放跟踪
trackGamePlay('游戏名称', '游戏分类');

// 搜索跟踪
trackGameSearch('搜索关键词');

// 分类浏览跟踪
trackCategoryView('分类名称');

// 用户登录跟踪
trackUserLogin('login_method');

// 收藏操作跟踪
trackFavoriteAdd('游戏名称');
trackFavoriteRemove('游戏名称');
```

## 📊 报告设置

### 1. 实时报告
- 当前活跃用户
- 实时事件
- 用户地理位置
- 流量来源

### 2. 获取报告
- 用户获取
- 用户参与度
- 用户留存
- 用户属性

### 3. 探索报告
- 用户路径分析
- 漏斗分析
- 队列分析
- 用户生命周期

## 🛠️ 验证设置

### 1. 使用Google Tag Assistant
1. 安装 [Google Tag Assistant](https://chrome.google.com/webstore/detail/tag-assistant-legacy-by-g/kejbdjndbnbjgmefkgdddjlbokphdefk)
2. 访问您的网站
3. 检查GA4标签是否正确加载
4. 验证事件是否正确触发

### 2. 使用GA4调试模式
```javascript
// 在开发环境中启用调试模式
gtag('config', 'G-XXXXXXXXXX', {
    'debug_mode': true
});
```

### 3. 检查实时报告
1. 登录GA4
2. 查看实时报告
3. 确认数据正在收集

## 📈 高级配置

### 1. 增强型电子商务
```javascript
// 游戏购买事件
gtag('event', 'purchase', {
    'transaction_id': 'T_12345',
    'value': 9.99,
    'currency': 'USD',
    'items': [{
        'item_id': 'game_id',
        'item_name': 'Game Name',
        'item_category': 'Action',
        'price': 9.99,
        'quantity': 1
    }]
});
```

### 2. 用户ID跟踪
```javascript
// 设置用户ID
gtag('config', 'G-XXXXXXXXXX', {
    'user_id': 'USER_ID_HERE'
});
```

### 3. 自定义用户属性
```javascript
// 设置用户属性
gtag('set', 'user_properties', {
    'user_type': 'registered',
    'subscription_level': 'premium',
    'favorite_category': 'action'
});
```

## 🔒 隐私合规

### 1. 数据保护
- 实施GDPR合规措施
- 添加Cookie同意横幅
- 提供数据删除选项

### 2. 隐私政策更新
确保您的隐私政策包含：
- Google Analytics使用说明
- 数据收集目的
- 用户权利说明
- 联系信息

## 📞 故障排除

### 常见问题

**Q: 数据没有显示在GA4中**
A: 检查测量ID是否正确，等待24-48小时数据延迟

**Q: 事件没有触发**
A: 检查JavaScript控制台错误，验证gtag函数是否加载

**Q: 实时数据不准确**
A: 清除浏览器缓存，检查广告拦截器设置

### 联系支持
- Google Analytics帮助中心
- Google Analytics社区论坛
- 开发者文档

## ✅ 检查清单

- [ ] 创建GA4账户
- [ ] 获取测量ID
- [ ] 更新代码中的测量ID
- [ ] 设置自定义维度
- [ ] 设置自定义指标
- [ ] 验证代码安装
- [ ] 测试事件跟踪
- [ ] 检查实时数据
- [ ] 配置隐私设置
- [ ] 更新隐私政策

完成这些步骤后，您就可以开始收集和分析Sonice Online Games的访问数据了！ 