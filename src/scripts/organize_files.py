import os
import shutil

def ensure_dir(directory):
    if not os.path.exists(directory):
        os.makedirs(directory)

def move_file(src, dst_dir):
    if os.path.exists(src):
        try:
            ensure_dir(dst_dir)
            dst = os.path.join(dst_dir, os.path.basename(src))
            shutil.move(src, dst)
            print(f"Moved {src} to {dst}")
        except (PermissionError, OSError) as e:
            print(f"Could not move {src}: {str(e)}")

# 创建必要的目录
directories = [
    'src/data',
    'src/scripts',
    'src/templates',
    'src/styles',
    'src/assets/images',
    'src/assets/icons',
    'src/components',
    'src/utils',
    'src/js',
    'docs'
]

for directory in directories:
    ensure_dir(directory)

# 移动 JavaScript 文件
js_files = [
    'ratings.js',
    'auth.js'
]

for file in js_files:
    move_file(file, 'src/js')

# 移动数据文件
data_files = [
    'games_data.json',
    'categorized_games.json',
    'crazygames2.csv',
    'iframe_urls.txt',
    'games_catalog.xlsx',
    'inaccessible_local_games.xlsx'
]

for file in data_files:
    move_file(file, 'src/data')

# 移动脚本文件
script_files = [
    'generate_pages.py',
    'check_local_games.py',
    'check_games.py',
    'process_games.py',
    'scraper.py',
    'add_ga_code.py'
]

for file in script_files:
    move_file(file, 'src/scripts')

# 移动模板文件
if os.path.exists('templates'):
    for file in os.listdir('templates'):
        src = os.path.join('templates', file)
        move_file(src, 'src/templates')

# 移动样式文件
style_files = [
    'styles.css'
]

for file in style_files:
    move_file(file, 'src/styles')

# 移动文档文件
doc_files = [
    'README.md',
    '统一的优化模板',
    '游戏页面模板使用指南',
    'debug_response.html'
]

for file in doc_files:
    move_file(file, 'docs')

# 移动组件文件
if os.path.exists('components'):
    for file in os.listdir('components'):
        src = os.path.join('components', file)
        move_file(src, 'src/components')

# 移动工具文件
if os.path.exists('utils'):
    for file in os.listdir('utils'):
        src = os.path.join('utils', file)
        move_file(src, 'src/utils')

# 移动资源文件
if os.path.exists('assets'):
    for item in os.listdir('assets'):
        src = os.path.join('assets', item)
        if os.path.isfile(src):
            if item.lower().endswith(('.png', '.jpg', '.jpeg', '.gif')):
                move_file(src, 'src/assets/images')
            elif item.lower().endswith(('.ico', '.svg')):
                move_file(src, 'src/assets/icons')
            else:
                move_file(src, 'src/assets')
        else:
            try:
                shutil.move(src, os.path.join('src/assets', item))
                print(f"Moved directory {src} to src/assets/{item}")
            except (PermissionError, OSError) as e:
                print(f"Could not move directory {src}: {str(e)}")

# 移动游戏页面到 pages/games
ensure_dir('pages/games')
if os.path.exists('games'):
    for file in os.listdir('games'):
        if file.endswith('.html'):
            src = os.path.join('games', file)
            move_file(src, 'pages/games')

# 移动分类页面到 pages/categories
ensure_dir('pages/categories')
if os.path.exists('categories'):
    for file in os.listdir('categories'):
        if file.endswith('.html'):
            src = os.path.join('categories', file)
            move_file(src, 'pages/categories')

# 移动 HTML 文件到 pages
html_files = [
    'index.html',
    'games.html',
    'categories.html',
    'about.html',
    'search-results.html',
    '404.html',
    '500.html'
]

for file in html_files:
    move_file(file, 'pages')

# 移动其他配置文件到根目录
config_files = [
    'package.json',
    'package-lock.json',
    'manifest.json',
    'robots.txt',
    '.htaccess',
    'sitemap.xml',
    'requirements.txt'
]

root_dir = '.'
for file in config_files:
    if os.path.exists(file) and os.path.dirname(file) != root_dir:
        move_file(file, root_dir)

print("File organization completed!") 