import os
import re

# 1. Update user/book_ticket.php
path_user = r'D:\ebus\EBUS\user\book_ticket.php'
with open(path_user, 'r', encoding='utf-8') as f:
    content = f.read()

# Add script tag before </body>
if 'geocoder.js' not in content:
    content = content.replace('</body>', '  <script src="../js/geocoder.js"></script>\n</body>')
    with open(path_user, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Updated user/book_ticket.php")

# 2. Update routes.php
path_routes = r'D:\ebus\EBUS\routes.php'
with open(path_routes, 'r', encoding='utf-8') as f:
    content2 = f.read()

if 'geocoder.js' not in content2:
    content2 = content2.replace('</body>', '  <script src="js/geocoder.js"></script>\n</body>')
    with open(path_routes, 'w', encoding='utf-8') as f:
        f.write(content2)
    print("Updated routes.php")

print("Done")
