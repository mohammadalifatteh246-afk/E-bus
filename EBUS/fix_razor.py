import os, glob

files = glob.glob('user/*.php') + glob.glob('*.php')

for f in files:
    if os.path.isfile(f):
        try:
            with open(f, 'r', encoding='utf-8') as file:
                content = file.read()
            
            if '&#129682;' in content:
                content = content.replace('&#129682;', '&#129706;')
                with open(f, 'w', encoding='utf-8') as file:
                    file.write(content)
        except Exception as e:
            pass

print("Done")
