#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys
import importlib.util
import subprocess
import time

def run_script(script_path):
    """运行指定的Python脚本"""
    print(f"Running script: {script_path}")
    print("=" * 50)
    
    try:
        result = subprocess.run([sys.executable, script_path], capture_output=True, text=True)
        print(result.stdout)
        if result.stderr:
            print("Errors:")
            print(result.stderr)
        
        if result.returncode == 0:
            print(f"Script completed successfully: {script_path}")
        else:
            print(f"Script failed with error code {result.returncode}: {script_path}")
        
        print("=" * 50)
        print()
        return result.returncode == 0
    except Exception as e:
        print(f"Error running script {script_path}: {e}")
        print("=" * 50)
        print()
        return False

def main():
    """运行所有需要的脚本按顺序更新网站"""
    start_time = time.time()
    
    # 获取脚本目录
    script_dir = os.path.dirname(os.path.abspath(__file__))
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    
    # 要运行的脚本列表
    scripts = [
        # 1. 更新游戏iframe
        os.path.join(script_dir, "update_game_iframes.py"),
        
        # 2. 生成分类页面
        os.path.join(script_dir, "create_all_category_pages.py"),
        
        # 3. 生成分类首页
        os.path.join(script_dir, "create_categories_index.py"),
    ]
    
    # 运行每个脚本
    success_count = 0
    for script in scripts:
        if os.path.exists(script):
            if run_script(script):
                success_count += 1
        else:
            print(f"Script not found: {script}")
    
    # 输出结果
    end_time = time.time()
    elapsed_time = end_time - start_time
    
    print("\n" + "=" * 50)
    print(f"全部更新完成！")
    print(f"成功运行了 {success_count}/{len(scripts)} 个脚本")
    print(f"总耗时: {elapsed_time:.2f} 秒")
    print("=" * 50)

if __name__ == "__main__":
    main() 