import os
import glob
import re

# 游戏页面模板 - 根据提供的截图布局调整
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

    <style>
        body {{
            background: linear-gradient(135deg, #2a4cad 0%, #3b62d9 100%);
            color: #fff;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }}
        
        .header {{
            background-color: #1A1F35;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }}
        
        .logo {{
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 20px;
        }}
        
        .logo img {{
            width: 32px;
            height: 32px;
        }}
        
        .search-bar {{
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            padding: 8px 16px;
            width: 300px;
            border: none;
            color: white;
        }}
        
        .container {{
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }}
        
        .game-title {{
            background-color: #38bdf8;
            color: white;
            display: inline-block;
            padding: 10px 30px;
            border-radius: 8px;
            font-size: 32px;
            font-weight: bold;
            margin: 20px auto;
            text-align: center;
        }}
        
        .game-title-container {{
            text-align: center;
            position: relative;
        }}
        
        .purple-dot {{
            width: 10px;
            height: 10px;
            background-color: #a855f7;
            border-radius: 50%;
            margin: 8px auto;
        }}
        
        .game-description {{
            background-color: rgba(30, 41, 59, 0.8);
            padding: 15px;
            border-radius: 8px;
            margin: 20px auto;
            text-align: center;
            max-width: 800px;
        }}
        
        .game-frame-container {{
            background-color: #1e293b;
            border-radius: 12px;
            overflow: hidden;
            margin: 30px auto;
            width: 100%;
            aspect-ratio: 16/9;
            position: relative;
        }}
        
        .game-frame {{
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }}
    </style>
</head>
<body>
    <header class="header">
        <a href="/" class="logo">
            <img src="/src/assets/icons/logo.png" alt="Sonice Games">
            Sonice Games
        </a>
        <input type="text" class="search-bar" placeholder="Search games...">
        <div></div>
    </header>

    <div class="container">
        <div class="game-title-container">
            <div class="game-title">{title}</div>
            <div class="purple-dot"></div>
        </div>
        
        <div class="game-description">
            {description}
        </div>
        
        <div class="game-frame-container">
            <iframe class="game-frame" src="" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>

    <script>
        // 设置iframe的src属性，确保游戏能正确加载
        document.addEventListener('DOMContentLoaded', function() {{
            // 由于无法直接访问/games/{slug}/，使用相对路径
            const gameFrame = document.querySelector('.game-frame');
            gameFrame.src = './';
        }});
    </script>
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