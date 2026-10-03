import os

path_user = r'D:\ebus\EBUS\user\book_ticket.php'
with open(path_user, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace('geocoder.js', 'geocoder.js?v=2')
with open(path_user, 'w', encoding='utf-8') as f:
    f.write(content)

path_routes = r'D:\ebus\EBUS\routes.php'
with open(path_routes, 'r', encoding='utf-8') as f:
    content2 = f.read()

content2 = content2.replace('geocoder.js', 'geocoder.js?v=2')
with open(path_routes, 'w', encoding='utf-8') as f:
    f.write(content2)

print("Cache busted")
