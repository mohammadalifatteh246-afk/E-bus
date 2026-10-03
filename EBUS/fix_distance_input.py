import os

# 1. Update user/book_ticket.php
path_user = r'D:\ebus\EBUS\user\book_ticket.php'
with open(path_user, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the input type="number" with type="text" readonly
content = content.replace(
    '<input type="number" step="0.1" name="distance_km" id="distanceKm" class="form-control" placeholder="Enter distance" required>',
    '<input type="text" name="distance_km" id="distanceKm" class="form-control" placeholder="Auto-calculated distance" required readonly style="background-color: #f8f9fa; cursor: not-allowed;">'
)

# Remove the manual entry hint
hint_text = '<div class="text-small text-muted mt-xs">In a real scenario, this is filled by Leaflet/API. You can manually enter for now.</div>'
content = content.replace(hint_text, '')

with open(path_user, 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated user/book_ticket.php")


# 2. Update routes.php
path_routes = r'D:\ebus\EBUS\routes.php'
with open(path_routes, 'r', encoding='utf-8') as f:
    content2 = f.read()

content2 = content2.replace(
    '<input type="number" step="0.1" name="distance_km" class="form-control" placeholder="e.g. 145" required>',
    '<input type="text" name="distance_km" class="form-control" placeholder="Auto-calculated distance" required readonly style="background-color: #f8f9fa; cursor: not-allowed;">'
)

with open(path_routes, 'w', encoding='utf-8') as f:
    f.write(content2)
print("Updated routes.php")

print("Done")
