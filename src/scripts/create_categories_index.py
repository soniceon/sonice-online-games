#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import json
import codecs

def main():
    """生成分类首页"""
    base_dir = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
    data_path = os.path.join(base_dir, 'data', 'categories.json')
    output_path = os.path.join(base_dir, 'categories.html')
    
    # 检查分类数据文件是否存在
    if not os.path.exists(data_path):
        print(f"Error: Categories data file not found at {data_path}")
        return
    
    # 读取分类数据
    try:
        with open(data_path, 'r', encoding='utf-8') as f:
            data = json.load(f)
    except Exception as e:
        print(f"Error reading categories data: {e}")
        return
    
    # 确保数据格式正确
    if not isinstance(data, dict) or 'categories' not in data:
        print("Error: Invalid categories data format")
        return
    
    categories = data['categories']
    
    # 按数量排序分类
    sorted_categories = sorted(
        categories.values(), 
        key=lambda x: x['count'], 
        reverse=True
    )
    
    # 生成HTML页面
    html = """<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Categories - Sonice Online Games</title>
    <meta name="description" content="Browse all game categories at sonice.online. Find your favorite game categories including action, puzzle, racing and more!">
    <link rel="icon" type="image/png" href="/src/assets/icons/logo.png">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sonice.online/categories.html">
    <meta property="og:title" content="Game Categories - Sonice Online Games">
    <meta property="og:description" content="Browse all game categories at sonice.online. Find your favorite game categories including action, puzzle, racing and more!">
    <meta property="og:image" content="/src/assets/images/og-image.jpg">

    <link rel="stylesheet" href="/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'dark': '#0F1012',
                        'dark-lighter': '#1A1B1F',
                        'blue-primary': '#0EA5E9',
                        'blue-secondary': '#38BDF8',
                        'blue-bright': '#7DD3FC',
                        'purple-primary': '#7C3AED',
                        'gray-custom': '#2A2B31'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: 
                linear-gradient(135deg, rgba(30, 58, 138, 0.95) 0%, rgba(30, 64, 175, 0.95) 25%, rgba(37, 99, 235, 0.95) 50%, rgba(59, 130, 246, 0.95) 75%, rgba(96, 165, 250, 0.95) 100%),
                url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239BA3EB' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            color: #fff;
            min-height: 100vh;
        }
        .header-bg {
            background-color: #1A1B1F;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar {
            width: 64px;
            background: rgba(0, 43, 135, 0.95);
            backdrop-filter: blur(12px);
            position: fixed;
            top: 64px;
            left: 0;
            height: calc(100vh - 64px);
            transition: width 0.3s ease;
            overflow: hidden;
            z-index: 40;
        }
        .sidebar:hover {
            width: 240px;
        }
        .sidebar-item {
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .sidebar:hover .sidebar-item {
            opacity: 1;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
        }
        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .sidebar-icon {
            min-width: 24px;
            width: 24px;
            height: 24px;
            margin-right: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .category-label {
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            white-space: nowrap;
        }
        .search-bar {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .search-bar:focus-within {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* Category styles */
        .categories-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1400px;
        }

        .category-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
            text-align: center;
            margin-bottom: 2rem;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .category-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .category-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .category-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
            margin-bottom: 0.5rem;
        }

        .category-count {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .category-button {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .category-button:hover {
            background: rgba(255, 255, 255, 0.25);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header-bg fixed top-0 left-0 right-0 h-16 z-50">
        <div class="container mx-auto px-4 h-full">
            <div class="flex items-center justify-between h-full">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-2">
                    <img src="/src/assets/icons/logo.png" alt="Sonice Games" class="w-8 h-8">
                    <span class="text-xl font-bold text-white">Sonice Games</span>
                </a>

                <!-- Search Bar -->
                <div class="flex-1 max-w-2xl mx-8">
                    <div class="search-bar flex items-center px-4 py-2">
                        <i class="fas fa-search text-gray-400 mr-2"></i>
                        <input type="text" placeholder="Search games..." class="bg-transparent w-full text-white focus:outline-none">
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <button class="user-button flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-700 transition-colors relative">
                        <img src="/assets/images/default-avatar.png" alt="User Avatar" class="w-8 h-8 rounded-full">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="content-wrapper" style="margin-top: 104px;">
        <div class="flex">
            <!-- Sidebar -->
            <aside class="sidebar">
                <nav class="py-2" role="navigation" aria-label="Main navigation">
                    <div class="space-y-1">
                        <a href="/index.html" class="sidebar-link">
                            <div class="sidebar-icon icon-home">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L2 9.5V22h20V9.5L12 2zm7 18H5v-9.5l7-5.25 7 5.25V20z"/>
                                </svg>
                            </div>
                            <span class="sidebar-item">Home</span>
                        </a>
                        
                        <a href="/favorites.html" class="sidebar-link">
                            <div class="sidebar-icon icon-favorites">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </div>
                            <span class="sidebar-item">Favorites</span>
                            <span class="ml-auto bg-purple-primary px-2 py-0.5 rounded-full text-xs text-white favorites-count">0</span>
                        </a>
                        
                        <a href="/recent.html" class="sidebar-link">
                            <div class="sidebar-icon icon-recent">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                </svg>
                            </div>
                            <span class="sidebar-item">Recently Played</span>
                        </a>

                        <a href="/new-games.html" class="sidebar-link">
                            <div class="sidebar-icon icon-new">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 4v12h-1.34l1.91 1.91A2.01 2.01 0 0022 16V4c0-1.1-.9-2-2-2H4c-1.1 0-1.99.9-1.99 2L2 16c0 1.1.9 2 2 2h11.17l-.83-.83V18H4V6h16v10h-1v2h1zm-7 6l-4 4h3v6h2v-6h3l-4-4z"/>
                                </svg>
                            </div>
                            <span class="sidebar-item">New Games</span>
                            <span class="ml-auto bg-blue-primary px-2 py-0.5 rounded-full text-xs text-white">12</span>
                        </a>

                        <div class="py-2">
                            <div class="category-label">
                                <span class="sidebar-item">Categories</span>
                            </div>

                            <a href="/categories.html" class="sidebar-link active">
                                <div class="sidebar-icon icon-action">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
                                    </svg>
                                </div>
                                <span class="sidebar-item">All Categories</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="main-content flex-1 p-6">
                <div class="categories-container">
                    <h1 class="category-title">Game Categories</h1>
                    
                    <div class="categories-grid">
"""
    
    # 添加分类卡片
    for category in sorted_categories:
        html += f"""
                        <div class="category-card">
                            <div class="category-icon">{category.get('icon', '🎮')}</div>
                            <h2 class="category-name">{category.get('name', 'Unknown')}</h2>
                            <div class="category-count">{category.get('count', 0)} Games</div>
                            <a href="{category.get('url', '#')}" class="category-button">View Games</a>
                        </div>
"""
    
    # 完成HTML页面
    html += """
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>About Us</h3>
                <p>sonice.online is your premier destination for free online games. We offer a wide variety of games across different genres.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="/games.html">All Games</a></li>
                    <li><a href="/categories.html">Categories</a></li>
                    <li><a href="/about.html">About Us</a></li>
                    <li><a href="/contact.html">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Categories</h3>
                <ul class="footer-links">
                    <li><a href="/category/action.html">Action</a></li>
                    <li><a href="/category/puzzle.html">Puzzle</a></li>
                    <li><a href="/category/racing.html">Racing</a></li>
                    <li><a href="/category/sports.html">Sports</a></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2024 sonice.online. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Sidebar toggle functionality
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        const toggleBtn = document.querySelector('.toggle-sidebar');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });
        }

        // Keyboard shortcut (Alt + S) to toggle sidebar
        document.addEventListener('keydown', (e) => {
            if (e.altKey && e.key.toLowerCase() === 's') {
                e.preventDefault();
                if (toggleBtn) toggleBtn.click();
            }
        });
    </script>
</body>
</html>
"""
    
    # 写入文件
    try:
        with open(output_path, 'w', encoding='utf-8') as f:
            f.write(html)
        print(f"Categories index page created at: {output_path}")
    except Exception as e:
        print(f"Error writing categories index page: {e}")

if __name__ == "__main__":
    main() 