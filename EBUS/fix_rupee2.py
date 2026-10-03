import os, glob, re

files = glob.glob('user/*.php') + glob.glob('*.php')

for f in files:
    if os.path.isfile(f):
        try:
            with open(f, 'r', encoding='utf-8') as file:
                content = file.read()
            
            # Replace literal Rupee symbol and its mojibake
            if '₹' in content or 'â‚¹' in content or 'â,¹' in content:
                content = content.replace('₹', '&#8377;')
                content = content.replace('â‚¹', '&#8377;')
                content = content.replace('â,¹', '&#8377;')
                
                with open(f, 'w', encoding='utf-8') as file:
                    file.write(content)
        except Exception as e:
            pass

print("Done")
