import os
import glob
import re

# 游戏页面模板
TEMPLATE = '''<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{title} - Play Free Online Game</title>
    <meta name="description" content="{description}">
    <link rel="icon" type="image/png" href="/src/assets/icons/logo.png">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sonice.online/games/{slug}.html">
    <meta property="og:title" content="{title} - Play Free Online Game">
    <meta property="og:description" content="{description}">
    <meta property="og:image" content="https://sonice.online/src/assets/images/games/{slug}.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://sonice.online/games/{slug}.html">
    <meta property="twitter:title" content="{title} - Play Free Online Game">
    <meta property="twitter:description" content="{description}">
    <meta property="twitter:image" content="https://sonice.online/src/assets/images/games/{slug}.jpg">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {{
        "@context": "https://schema.org",
        "@type": "VideoGame",
        "name": "{title}",
        "description": "{description}",
        "image": "https://sonice.online/src/assets/images/games/{slug}.jpg",
        "genre": "{category}",
        "url": "https://sonice.online/games/{slug}.html"
    }}
    </script>

    <link rel="stylesheet" href="/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {{
            theme: {{
                extend: {{
                    colors: {{
                        'dark': '#0F1012',
                        'dark-lighter': '#1A1B1F',
                        'blue-primary': '#0EA5E9',
                        'blue-secondary': '#38BDF8',
                        'blue-bright': '#7DD3FC',
                        'purple-primary': '#7C3AED',
                        'gray-custom': '#2A2B31'
                    }}
                }}
            }}
        }}
    </script>
    <style>
        body {{
            background: 
                linear-gradient(135deg, rgba(30, 58, 138, 0.95) 0%, rgba(30, 64, 175, 0.95) 25%, rgba(37, 99, 235, 0.95) 50%, rgba(59, 130, 246, 0.95) 75%, rgba(96, 165, 250, 0.95) 100%),
                url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239BA3EB' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            color: #fff;
            min-height: 100vh;
        }}
        .header-bg {{
            background-color: rgba(26, 27, 31, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }}
        .sidebar {{
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
        }}
        .sidebar:hover {{
            width: 240px;
        }}
        .sidebar-item {{
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.2s ease;
        }}
        .sidebar:hover .sidebar-item {{
            opacity: 1;
        }}
        .sidebar-link {{
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
        }}
        .sidebar-link:hover {{
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }}
        .sidebar-icon {{
            min-width: 24px;
            width: 24px;
            height: 24px;
            margin-right: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }}
        .category-label {{
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            white-space: nowrap;
        }}
        .search-bar {{
            background: rgba(255, 255, 255, 0.1);
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }}
        .search-bar:focus-within {{
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
        }}

        /* Icon colors */
        .icon-home {{ color: #7C3AED; }}
        .icon-favorites {{ color: #EF4444; }}
        .icon-recent {{ color: #3B82F6; }}
        .icon-new {{ color: #10B981; }}
        .icon-action {{ color: #F59E0B; }}
        .icon-racing {{ color: #EC4899; }}
        .icon-sports {{ color: #06B6D4; }}
        .icon-shooter {{ color: #6366F1; }}
        .icon-cards {{ color: #8B5CF6; }}

        /* Game container styles */
        .game-container {{
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1400px;
        }}

        /* Main Content Styles */
        .main-content {{
            margin-left: 64px;
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }}

        .content-wrapper {{
            margin-top: 64px !important;
        }}

        .game-wrapper {{
            background: rgba(0, 0, 0, 0.5);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            width: 100%;
            padding-bottom: 56.25%;
            margin: 2rem 0;
        }}

        .game-wrapper iframe {{
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }}

        /* Title Section Styles */
        .title-section {{
            text-align: center;
            margin-bottom: 1rem;
        }}

        .title-section h1 {{
            font-size: 2rem;
            font-weight: bold;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            display: inline-block;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.8), rgba(56, 189, 248, 0.8));
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }}

        .categories-section {{
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }}

        .description-section {{
            max-width: 3xl;
            margin: 0 auto;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 12px;
            line-height: 1.2;
        }}
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
                        <input type="text" placeholder="Search games..." class="bg-transparent w-full text-white focus:outline-none">
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <img src="/src/assets/icons/user.png" alt="User" class="w-8 h-8 rounded-full">
                </div>
            </div>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="container mx-auto px-4 py-6">
            <div class="game-container">
                <div class="title-section">
                    <h1>{title}</h1>
                    
                    <div class="categories-section">
                        <span class="category-tag" style="
                            background: linear-gradient(135deg, rgba(124, 58, 237, 0.8), rgba(139, 92, 246, 0.8));
                            box-shadow: 0 2px 8px rgba(124, 58, 237, 0.2);
                            border: 1px solid rgba(255, 255, 255, 0.1);
                            padding: 0.5rem 1rem;
                            border-radius: 20px;
                            font-weight: 500;
                        ">{category}</span>
                    </div>
                    
                    <div class="description-section">
                        <p class="text-white text-lg">{description}</p>
                    </div>
                </div>
                
                <div class="game-wrapper">
                    <iframe src="/games/{slug}/" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</body>
</html>'''

def get_game_info(file_path):
    """从现有的游戏页面中提取游戏信息"""
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    # 提取标题
    title_match = re.search(r'<title>(.*?) -', content)
    title = title_match.group(1) if title_match else "Game"
    
    # 提取描述
    desc_match = re.search(r'<meta name="description" content="([^"]*)"', content)
    description = desc_match.group(1) if desc_match else f"Play {title} at sonice.online - Free browser game, no download required!"
    
    # 提取分类
    category_match = re.search(r'"genre":\s*"([^"]*)"', content)
    category = category_match.group(1) if category_match else "Action"
    
    # 获取slug（从文件名）
    slug = os.path.splitext(os.path.basename(file_path))[0]
    
    return {
        'title': title,
        'description': description,
        'category': category,
        'slug': slug
    }

def update_game_page(file_path):
    """更新单个游戏页面"""
    try:
        game_info = get_game_info(file_path)
        new_content = TEMPLATE.format(**game_info)
        
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
            
        print(f"Updated: {file_path}")
        return True
    except Exception as e:
        print(f"Error updating {file_path}: {str(e)}")
        return False

def main():
    """主函数"""
    # 获取所有游戏页面
    game_pages = glob.glob('pages/games/*.html')
    
    success_count = 0
    error_count = 0
    
    for page in game_pages:
        if update_game_page(page):
            success_count += 1
        else:
            error_count += 1
    
    print(f"\nUpdate completed!")
    print(f"Successfully updated: {success_count} pages")
    print(f"Failed to update: {error_count} pages")

if __name__ == '__main__':
    main() 