import os
import re
import glob

# 1. Fix js/sidebar.js logic
sidebar_path = r'D:\ebus\EBUS\js\sidebar.js'
with open(sidebar_path, 'r', encoding='utf-8') as f:
    js_content = f.read()

# Replace .includes(link) with .endsWith(link)
js_content = js_content.replace('currentPath.includes(link)', 'currentPath.endsWith(link)')
with open(sidebar_path, 'w', encoding='utf-8') as f:
    f.write(js_content)

print("Fixed js/sidebar.js")

# 2. Fix ticket.php to my_tickets.php in all user/*.php files
user_files = glob.glob(r'D:\ebus\EBUS\user\*.php')
for fpath in user_files:
    with open(fpath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # We only want to replace the sidebar link, which looks like:
    # <a href="ticket.php" class="nav-item">... My Tickets</a>
    # We'll use a regex to be safe and target exactly the link for My Tickets
    new_content = re.sub(
        r'<a href="ticket\.php"(.*?>.*?My Tickets\s*</a>)',
        r'<a href="my_tickets.php"\1',
        content
    )
    
    # If there are any other direct href="ticket.php" outside the table that should be my_tickets.php
    # Actually, just replacing href="ticket.php" in the sidebar is enough.
    
    if new_content != content:
        with open(fpath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Fixed link in {os.path.basename(fpath)}")

print("Done")
