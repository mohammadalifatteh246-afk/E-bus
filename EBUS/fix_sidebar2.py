import os

sidebar_path = r'D:\ebus\EBUS\js\sidebar.js'
with open(sidebar_path, 'r', encoding='utf-8') as f:
    js_content = f.read()

# First revert my previous replacement if it's there
js_content = js_content.replace('currentPath.endsWith(link)', 'currentPath.includes(link)')

# Now do it correctly using filename comparison
# Original code has: const currentPath = window.location.pathname;
# We want to change the checking logic
js_content = js_content.replace(
    "const currentPath = window.location.pathname;",
    "const currentPath = window.location.pathname;\n  const filename = currentPath.split('/').pop();"
)

js_content = js_content.replace(
    "if (link && currentPath.includes(link) && link !== '#') {",
    "if (link && filename === link && link !== '#') {"
)

with open(sidebar_path, 'w', encoding='utf-8') as f:
    f.write(js_content)

print("Fixed js/sidebar.js with precise filename matching")
