import os
import glob
import re
from collections import defaultdict

def extract_game_categories(file_path):
    """从游戏页面提取游戏分类信息"""
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 从JSON-LD中提取分类
    category_match = re.search(r'"genre":\s*"([^"]*)"', content)
    categories = []
    if category_match:
        category_str = category_match.group(1).strip()
        categories = [cat.strip() for cat in category_str.split(',')]
    
    # 获取标题和slug
    title_match = re.search(r'<title>(.*?)(?:\s*-|\s*\|)', content)
    title = title_match.group(1).strip() if title_match else "Game"
    slug = os.path.splitext(os.path.basename(file_path))[0]
    
    return {
        'title': title,
        'slug': slug,
        'categories': categories,
        'file_path': file_path
    }

def fix_category_display(file_path, game_info):
    """修复游戏页面中的分类显示问题"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # 查找并替换所有Jinja2模板格式的分类展示代码
        
        # 1. 检查标题下方的分类部分
        title_categories_pattern = r'<div class="categories-section">.*?{{% for category in game\.categories %}}.*?{{% endfor %}}.*?</div>'
        title_categories_match = re.search(title_categories_pattern, content, re.DOTALL)
        
        if title_categories_match:
            # 提取原始HTML模板结构
            original_html = title_categories_match.group(0)
            # 创建新的HTML
            new_html = '<div class="categories-section">\n'
            for category in game_info['categories']:
                new_html += f'<span class="category-tag" style="\
                    background: linear-gradient(135deg, rgba(124, 58, 237, 0.8), rgba(139, 92, 246, 0.8));\
                    box-shadow: 0 2px 8px rgba(124, 58, 237, 0.2);\
                    border: 1px solid rgba(255, 255, 255, 0.1);\
                    padding: 0.5rem 1rem;\
                    border-radius: 20px;\
                    font-weight: 500;\
                ">{category}</span>\n'
            new_html += '</div>'
            
            # 替换内容
            content = content.replace(original_html, new_html)
        
        # 2. 检查游戏概述部分的分类
        overview_pattern = r'<div class="game-categories">.*?{{% for category in game\.categories %}}.*?{{% endfor %}}.*?</div>'
        overview_match = re.search(overview_pattern, content, re.DOTALL)
        
        if overview_match:
            original_html = overview_match.group(0)
            new_html = '<div class="game-categories">\n'
            for category in game_info['categories']:
                new_html += f'    <span class="category-tag">{category}</span>\n'
            new_html += '</div>'
            
            content = content.replace(original_html, new_html)
        
        # 3. 检查其他可能的Jinja2模板变量格式
        other_templates = [
            r'{{% for category in game\.categories %}}(.*?){{% endfor %}}',
            r'{{\s*category\s*}}',
            r'\[{{\s*category\s*}}\]'
        ]
        
        for pattern in other_templates:
            matches = re.findall(pattern, content)
            if matches:
                for match in matches:
                    # 对于每个匹配到的模板代码，都替换为实际分类
                    template_html = ""
                    for category in game_info['categories']:
                        if isinstance(match, str) and '{{category}}' in match:
                            template_html += match.replace('{{category}}', category)
                        else:
                            template_html += f'{category}, '
                    
                    # 移除最后的逗号
                    if template_html.endswith(', '):
                        template_html = template_html[:-2]
                    
                    # 替换内容
                    content = content.replace(f'{{% for category in game.categories %}}{match}{{% endfor %}}', template_html)
        
        # 直接替换特定的模板变量格式
        content = content.replace('{% for category in game.categories %}{{category}}{% if not loop.last %}, {% endif %}{% endfor %}', ', '.join(game_info['categories']))
        content = content.replace('{% for category in game.categories %}{{category}}{% endfor %}', ', '.join(game_info['categories']))
        
        # 替换特定的带样式的category标签
        category_style_pattern = r'{{% for category in game\.categories %}}<span.*?{{category}}.*?</span>{{% endfor %}}'
        category_style_match = re.search(category_style_pattern, content, re.DOTALL)
        
        if category_style_match:
            original_html = category_style_match.group(0)
            # 提取单个category标签的HTML
            single_tag_match = re.search(r'<span.*?{{category}}.*?</span>', original_html, re.DOTALL)
            if single_tag_match:
                tag_template = single_tag_match.group(0)
                new_html = ""
                for category in game_info['categories']:
                    new_html += tag_template.replace('{{category}}', category) + "\n"
                content = content.replace(original_html, new_html)
        
        # 将更新后的内容保存回文件
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        
        return True
    except Exception as e:
        print(f"更新 {file_path} 时出错: {str(e)}")
        return False

def main():
    # 获取所有游戏HTML文件
    game_pages = glob.glob('pages/games/*.html')
    print(f"找到 {len(game_pages)} 个游戏文件")
    
    success_count = 0
    error_count = 0
    
    # 处理每个游戏页面
    for page in game_pages:
        try:
            # 提取游戏信息
            game_info = extract_game_categories(page)
            
            if not game_info['categories']:
                print(f"警告: {game_info['title']} 没有分类信息!")
                continue
            
            print(f"处理: {game_info['title']}")
            print(f"  分类: {', '.join(game_info['categories'])}")
            
            # 修复分类显示
            if fix_category_display(page, game_info):
                success_count += 1
            else:
                error_count += 1
        except Exception as e:
            print(f"处理 {page} 时出错: {str(e)}")
            error_count += 1
    
    # 输出完成消息
    print("\n分类显示修复完成!")
    print(f"成功更新: {success_count} 页面")
    print(f"更新失败: {error_count} 页面")

if __name__ == "__main__":
    main() 