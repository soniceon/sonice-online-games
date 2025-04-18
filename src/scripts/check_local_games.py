import os
import re
import requests
from bs4 import BeautifulSoup
import pandas as pd
from concurrent.futures import ThreadPoolExecutor
import time

def extract_iframe_url(file_path):
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            soup = BeautifulSoup(content, 'html.parser')
            iframe = soup.find('iframe')
            if iframe and 'src' in iframe.attrs:
                return iframe['src']
    except Exception as e:
        print(f"Error reading {file_path}: {str(e)}")
    return None

def check_url(url, timeout=10):
    try:
        # 解析URL
        if not url.startswith(('http://', 'https://')):
            url = 'https://' + url.lstrip('/')

        # 发送HEAD请求检查URL可访问性
        response = requests.head(url, timeout=timeout, allow_redirects=True)
        return response.status_code == 200
    except Exception as e:
        print(f"Error checking {url}: {str(e)}")
        return False

def check_local_games(games_dir):
    games = []
    inaccessible_games = []
    
    # 获取所有HTML文件
    html_files = [f for f in os.listdir(games_dir) if f.endswith('.html')]
    
    print(f"\n开始检查 {len(html_files)} 个游戏文件...")
    
    for html_file in html_files:
        file_path = os.path.join(games_dir, html_file)
        iframe_url = extract_iframe_url(file_path)
        
        if iframe_url:
            game_info = {
                'file': html_file,
                'iframe_url': iframe_url
            }
            games.append(game_info)
            
            if not check_url(iframe_url):
                print(f"无法访问: {html_file} ({iframe_url})")
                inaccessible_games.append(game_info)
            else:
                print(f"可以访问: {html_file}")
            
            time.sleep(0.5)  # 添加延迟以避免请求过快
    
    # 生成不可访问游戏的报告
    if inaccessible_games:
        print(f"\n发现 {len(inaccessible_games)} 个不可访问的游戏:")
        df = pd.DataFrame(inaccessible_games)
        report_file = 'inaccessible_local_games.xlsx'
        df.to_excel(report_file, index=False)
        print(f"详细报告已保存到: {report_file}")
        
        # 打印不可访问的游戏列表
        for game in inaccessible_games:
            print(f"- {game['file']}")
            print(f"  iframe URL: {game['iframe_url']}")
        
        return inaccessible_games
    else:
        print("\n所有游戏URL都可以正常访问！")
        return []

if __name__ == '__main__':
    games_dir = 'games'
    inaccessible_games = check_local_games(games_dir)
    print(f"\n检查完成！发现 {len(inaccessible_games)} 个不可访问的游戏。") 