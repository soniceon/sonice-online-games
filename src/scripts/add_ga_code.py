import os
import re

# Google Analytics code
ga_code = '''
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-C6DQJE930Z"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-C6DQJE930Z');
</script>
'''

# List of folders to process
folders = ['.', 'categories', 'games']

# Counters
processed_files = 0
modified_files = 0

# Loop through folders
for folder in folders:
    # Check if folder exists
    if not os.path.exists(folder):
        print(f"Warning: Folder '{folder}' does not exist, skipping")
        continue
    
    # Loop through all files in the folder
    for filename in os.listdir(folder):
        # Only process HTML files
        if filename.endswith('.html'):
            file_path = os.path.join(folder, filename)
            processed_files += 1
            
            # Read file content
            with open(file_path, 'r', encoding='utf-8') as file:
                content = file.read()
            
            # Check if file already contains GA code
            if 'G-C6DQJE930Z' in content:
                print(f"Skipping {file_path} - GA code already exists")
                continue
            
            # Use regex to insert GA code before </head> tag
            new_content = re.sub(r'(</head>)', f'{ga_code}\n\\1', content)
            
            # If content changed, write it back to the file
            if new_content != content:
                with open(file_path, 'w', encoding='utf-8') as file:
                    file.write(new_content)
                modified_files += 1
                print(f"Updated {file_path}")

print(f"\nDone! Processed {processed_files} HTML files, modified {modified_files} files.")
