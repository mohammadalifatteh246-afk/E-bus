import os, glob

replacements = {
    '⇄': '&harr;',
    'â‡„': '&harr;',
    '→': '&rarr;',
    'â†’': '&rarr;',
    '←': '&larr;',
    'â† ': '&larr;'
}

# Fix in user directory
for f in glob.glob('user/*.php'):
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    for k, v in replacements.items():
        content = content.replace(k, v)
        
    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)

# Fix in admin directory (just in case)
for f in glob.glob('*.php'):
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    for k, v in replacements.items():
        content = content.replace(k, v)
        
    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)
