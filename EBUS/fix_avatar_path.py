import os
import glob
import re

files = glob.glob('user/*.php')

for f in files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Replace the incorrect prepended '../' for the photo url
    content = content.replace(
        "$header_avatar_url = '../' . $_SESSION['user']['photo'];",
        "$header_avatar_url = $_SESSION['user']['photo'];"
    )
    
    content = content.replace(
        "<img src=\"../<?= htmlspecialchars($user['photo']) ?>?t=<?= time() ?>\"",
        "<img src=\"<?= htmlspecialchars($user['photo']) ?>?t=<?= time() ?>\""
    )
    
    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)

print("Done")
