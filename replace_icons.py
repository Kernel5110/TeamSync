import os
import re

def replace_icons(content):
    # Pattern to match <span class="material-icons" ...>icon_name</span>
    # We capture attributes in group 1 and icon name in group 2
    pattern = r'<span\s+class="material-icons"\s*([^>]*)>\s*([^<]+)\s*</span>'
    
    def replacement(match):
        attrs = match.group(1).strip()
        icon_name = match.group(2).strip()
        
        # If there are attributes, include them
        if attrs:
            return f'<x-icon name="{icon_name}" {attrs} />'
        else:
            return f'<x-icon name="{icon_name}" />'

    return re.sub(pattern, replacement, content)

def process_directory(directory):
    for root, dirs, files in os.walk(directory):
        for file in files:
            if file.endswith('.blade.php'):
                filepath = os.path.join(root, file)
                with open(filepath, 'r') as f:
                    content = f.read()
                
                new_content = replace_icons(content)
                
                if new_content != content:
                    print(f"Updating {filepath}")
                    with open(filepath, 'w') as f:
                        f.write(new_content)

if __name__ == "__main__":
    process_directory('/home/kernel/Univercity/Programacion_Web/proyecto_final/resources/views')
