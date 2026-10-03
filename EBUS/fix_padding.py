import os
import re

# Fix user/index.php padding
with open('user/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace p-xl with a custom padding that looks better when there is no footer
content = content.replace(
    '<div class="card p-xl" style="max-width: 400px; width: 100%; margin: var(--space-xl);">',
    '<div class="card" style="padding: 40px; max-width: 400px; width: 100%; margin: var(--space-xl);">'
)
# Ensure the button inside doesn't have unnecessary margin if it's the last element
# But wait, in user/index.php, the "Don't have an account?" is the last element.
# Let's give it a slight margin-bottom or just rely on the 40px padding.

with open('user/index.php', 'w', encoding='utf-8') as f:
    f.write(content)


# Fix index.php padding
with open('index.php', 'r', encoding='utf-8') as f:
    content2 = f.read()

content2 = content2.replace(
    '<div class="card p-xl" style="max-width: 400px; width: 100%; margin: var(--space-xl);">',
    '<div class="card" style="padding: 40px; max-width: 400px; width: 100%; margin: var(--space-xl);">'
)
# Remove the mb-md from the Log In button so the bottom padding is purely the 40px from the card
content2 = content2.replace(
    'class="btn btn-primary w-full mb-md"',
    'class="btn btn-primary w-full"'
)

with open('index.php', 'w', encoding='utf-8') as f:
    f.write(content2)

print("Done")
