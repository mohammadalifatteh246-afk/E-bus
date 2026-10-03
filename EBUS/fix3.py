import os
import glob
import re

replacements = [
    (r'<span class="nav-icon">.*?</span> Home', r'<span class="nav-icon">&#127968;</span> Home'),
    (r'<span class="nav-icon">.*?</span> Book Ticket', r'<span class="nav-icon">&#127915;</span> Book Ticket'),
    (r'<span class="nav-icon">.*?</span> My Passes', r'<span class="nav-icon">&#129682;</span> My Passes'),
    (r'<span class="nav-icon">.*?</span> My Tickets', r'<span class="nav-icon">&#129534;</span> My Tickets'),
    (r'<span class="nav-icon">.*?</span> Profile', r'<span class="nav-icon">&#128100;</span> Profile'),
    (r'<span class="nav-icon">.*?</span> Logout', r'<span class="nav-icon">&#128682;</span> Logout'),
    (r'<div class="sidebar-header">.*? E-BUS</div>', r'<div class="sidebar-header">&#128652; E-BUS</div>'),
    (r'>.*? Back to Dashboard</a>', r'>&larr; Back to Dashboard</a>'),
    (r'>.*? Back</a>', r'>&larr; Back</a>'),
    (r'>.*? Back to My Passes</a>', r'>&larr; Back to My Passes</a>'),
    (r'>.*? Back to Profile</a>', r'>&larr; Back to Profile</a>'),
    (r'>.*? Print Ticket</button>', r'>&#128424; Print Ticket</button>'),
    (r'>.*? Print Pass</button>', r'>&#128424; Print Pass</button>'),
    (r'<div style="font-size: 32px; margin-bottom: var\(--space-sm\);">.*?</div>', r'<div style="font-size: 32px; margin-bottom: var(--space-sm);">&#128652;</div>'),
    (r'<div style="font-size: 48px; margin-bottom: var\(--space-md\);">.*?</div>', lambda m: m.group(0).replace('ðŸŽ«', '&#127915;').replace('🎫', '&#127915;').replace('ðŸªª', '&#129682;').replace('🪪', '&#129682;')),
    # Replace any leftover weird characters
    (r'â† ', r'&larr;'),
    (r'ðŸ–¨ï¸ ', r'&#128424;'),
    (r'ðŸ  ', r'&#127968;'),
    (r'ðŸšŒ', r'&#128652;'),
]

files = glob.glob('user/*.php') + glob.glob('*.php')

for f in files:
    if os.path.isfile(f):
        try:
            with open(f, 'r', encoding='utf-8') as file:
                content = file.read()
            
            for pattern, repl in replacements:
                content = re.sub(pattern, repl, content)
                
            with open(f, 'w', encoding='utf-8') as file:
                file.write(content)
        except Exception as e:
            print(f"Error processing {f}: {e}")

print("Done")
