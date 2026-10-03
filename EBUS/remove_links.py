import os
import re

# 1. Remove Admin Login from user/index.php
with open('user/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = re.sub(
    r'<div class="mt-md text-center text-small pt-sm" style="border-top: 1px solid var\(--border\);">\s*<a href="\.\./index\.php" class="text-secondary">Admin Login</a>\s*</div>',
    '',
    content
)

with open('user/index.php', 'w', encoding='utf-8') as f:
    f.write(content)


# 2. Remove Passenger Login from index.php
with open('index.php', 'r', encoding='utf-8') as f:
    content2 = f.read()

content2 = re.sub(
    r'<div class="text-center text-small pt-sm" style="border-top: 1px solid var\(--border\);">\s*<a href="user/index\.php" class="text-secondary hover-primary">Passenger Login</a>\s*</div>',
    '',
    content2
)

with open('index.php', 'w', encoding='utf-8') as f:
    f.write(content2)

print("Done")
