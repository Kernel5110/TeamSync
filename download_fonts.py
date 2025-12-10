
import requests
import os
import re

def download_fonts():
    fonts_dir = "/home/kernel/Univercity/Programacion_Web/proyecto_final/public/fonts"
    if not os.path.exists(fonts_dir):
        os.makedirs(fonts_dir)

    # Direct links to TTF files (using a reliable source or parsing CSS)
    # Since parsing CSS can be tricky with user agents, I'll try to fetch the CSS first and extract.
    
    css_url = "https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Great+Vibes&family=Open+Sans:wght@400;600&display=swap"
    # User-Agent for Safari 5.1 (Windows) often gets TTF
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 6.1; Safari/534.50) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50"
    }

    try:
        print("Fetching CSS...")
        response = requests.get(css_url, headers=headers)
        response.raise_for_status()
        css = response.text
        
        # Extract URLs
        urls = re.findall(r'url\((https?://[^)]+)\)', css)
        print(f"Found {len(urls)} font URLs")
        
        # Mapping based on order (Cinzel Regular, Cinzel Bold, Great Vibes, Open Sans Regular, Open Sans SemiBold)
        font_names = [
            "Cinzel-Regular.ttf",
            "Cinzel-Bold.ttf",
            "GreatVibes-Regular.ttf",
            "OpenSans-Regular.ttf",
            "OpenSans-SemiBold.ttf"
        ]
        
        for i, url in enumerate(urls):
            if i >= len(font_names): break
            filename = font_names[i]
            path = os.path.join(fonts_dir, filename)
            print(f"Downloading {filename} from {url}...")
            r = requests.get(url)
            with open(path, 'wb') as f:
                f.write(r.content)
            print(f"Saved {path}")

    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    download_fonts()
