import os, glob

# Since some files were corrupted by powershell reading UTF-8 as ANSI and writing UTF-8,
# we need to decode the double-encoded UTF-8.
def fix_mojibake(text):
    try:
        # If it was read as ANSI (cp1252) and written as UTF-8, we can reverse it
        return text.encode('cp1252').decode('utf-8')
    except:
        return text

for f in glob.glob('user/*.php'):
    with open(f, 'rb') as file:
        content_bytes = file.read()
    
    content = content_bytes.decode('utf-8')
    
    # Try to fix mojibake
    try:
        fixed_content = content.encode('cp1252').decode('utf-8')
        with open(f, 'w', encoding='utf-8') as file:
            file.write(fixed_content)
    except Exception as e:
        pass
