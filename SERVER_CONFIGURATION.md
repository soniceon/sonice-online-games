# 🚀 服务器配置指南

## 📋 问题诊断

**当前问题**: `https://sonice.online/sitemap.xml` 返回HTML而不是XML

**错误信息**: "error on line 75 at column 19: Specification mandates value for attribute async"

## 🔧 解决方案

### 方案1: 使用PHP版本的sitemap

1. **上传文件**: 将 `public/sitemap.php` 上传到服务器根目录
2. **访问地址**: `https://sonice.online/sitemap.php`
3. **提交到Google**: 使用 `https://sonice.online/sitemap.php`

### 方案2: 配置服务器路由

#### Apache (.htaccess)
```apache
# 确保sitemap.xml返回正确的Content-Type
<Files "sitemap.xml">
    Header set Content-Type "application/xml; charset=utf-8"
</Files>

# 或者重定向到PHP版本
RewriteRule ^sitemap\.xml$ sitemap.php [L]
```

#### Nginx
```nginx
# 确保sitemap.xml返回正确的Content-Type
location ~* \.xml$ {
    add_header Content-Type "application/xml; charset=utf-8";
}
```

### 方案3: 使用独立PHP文件

1. **上传**: `public/standalone-sitemap.php` 到服务器
2. **访问**: `https://sonice.online/standalone-sitemap.php`
3. **提交**: 使用这个地址到Google Search Console

## 📝 部署检查清单

- [ ] 上传 `sitemap.xml` 到服务器根目录
- [ ] 上传 `sitemap.php` 到服务器根目录
- [ ] 配置服务器返回正确的Content-Type
- [ ] 测试访问 `https://sonice.online/sitemap.xml`
- [ ] 确认返回XML格式（不是HTML）
- [ ] 提交到Google Search Console

## 🎯 推荐方案

**使用PHP版本** (`sitemap.php`) 是最可靠的解决方案，因为它：
- ✅ 强制设置正确的Content-Type
- ✅ 不依赖服务器配置
- ✅ 确保返回XML格式

## 📞 技术支持

如果问题持续存在，请检查：
1. 服务器配置
2. 文件权限
3. 路由规则
4. 缓存设置 