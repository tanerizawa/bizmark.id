#!/usr/bin/env python3

from PIL import Image, ImageDraw, ImageFont
import os

# Create icons directory if it doesn't exist
icons_dir = '/Users/odangrodiana/Desktop/01_DEVELOPMENT_PROJECTS/bizmark.id/frontend/public/icons'
os.makedirs(icons_dir, exist_ok=True)

# Icon sizes needed for PWA
sizes = [72, 96, 128, 144, 152, 192, 384, 512]

# Colors
bg_color = '#2563eb'  # Blue-600
text_color = '#ffffff'  # White

for size in sizes:
    # Create a new image with the specified size
    img = Image.new('RGB', (size, size), bg_color)
    draw = ImageDraw.Draw(img)
    
    # Try to load a font, fallback to default if not available
    try:
        font_size = size // 3
        font = ImageFont.truetype('/System/Library/Fonts/Arial.ttf', font_size)
    except:
        try:
            font_size = size // 3
            font = ImageFont.load_default()
        except:
            font = None
    
    # Draw the letter "B" in the center
    text = "B"
    if font:
        # Get text bounding box
        bbox = draw.textbbox((0, 0), text, font=font)
        text_width = bbox[2] - bbox[0]
        text_height = bbox[3] - bbox[1]
    else:
        text_width = size // 4
        text_height = size // 4
    
    # Calculate position to center the text
    x = (size - text_width) // 2
    y = (size - text_height) // 2
    
    # Draw the text
    if font:
        draw.text((x, y), text, fill=text_color, font=font)
    else:
        draw.text((x, y), text, fill=text_color)
    
    # Add a subtle border
    border_width = max(1, size // 64)
    for i in range(border_width):
        draw.rectangle([i, i, size-1-i, size-1-i], outline='#1d4ed8', width=1)
    
    # Save the image
    filename = f'icon-{size}x{size}.png'
    filepath = os.path.join(icons_dir, filename)
    img.save(filepath, 'PNG')
    print(f'Created {filename}')

print('All PWA icons created successfully!')

# Create favicon.ico as well
try:
    # Create favicon from 32x32 version
    favicon_img = Image.new('RGB', (32, 32), bg_color)
    draw = ImageDraw.Draw(favicon_img)
    
    try:
        font = ImageFont.truetype('/System/Library/Fonts/Arial.ttf', 20)
    except:
        font = ImageFont.load_default()
    
    if font:
        bbox = draw.textbbox((0, 0), "B", font=font)
        text_width = bbox[2] - bbox[0]
        text_height = bbox[3] - bbox[1]
        x = (32 - text_width) // 2
        y = (32 - text_height) // 2
        draw.text((x, y), "B", fill=text_color, font=font)
    else:
        draw.text((12, 8), "B", fill=text_color)
    
    favicon_path = '/Users/odangrodiana/Desktop/01_DEVELOPMENT_PROJECTS/bizmark.id/frontend/public/favicon.ico'
    favicon_img.save(favicon_path, 'ICO')
    print('Created favicon.ico')
except Exception as e:
    print(f'Could not create favicon.ico: {e}')

print('Icon generation complete!')
