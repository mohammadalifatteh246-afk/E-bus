import os, glob

replacements = {
    'ðŸšŒ': '🚌',
    'ðŸ  ': '🏠',
    'ðŸŽ«': '🎫',
    'ðŸªª': '🪪',
    'ðŸ§¾': '🧾',
    'ðŸ‘¤': '👤',
    'ðŸšª': '🚪',
    'â† ': '←',
    'ðŸï¸ ': '🖨️',
    'â ³': '⏳',
    'âœ…': '✅',
    'â Œ': '❌',
    'âš ï¸ ': '⚠️',
    'â„¹ï¸ ': 'ℹ️',
    'â “': '❓',
    'ðŸ’³': '💳',
    'ðŸ“±': '📱',
    'ðŸ ¦': '🏦'
}

for f in glob.glob('user/*.php'):
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    for k, v in replacements.items():
        content = content.replace(k, v)
        
    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)
