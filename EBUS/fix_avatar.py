import os
import glob
import re

files = glob.glob('user/*.php')

avatar_html = '''<div class="user-avatar" style="overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: var(--primary);">
            <?php 
              $header_avatar_url = '';
              $header_avatar_char = 'U';
              if(isset($_SESSION['user']) && is_array($_SESSION['user'])) {
                  if(!empty($_SESSION['user']['photo'])) $header_avatar_url = '../' . $_SESSION['user']['photo'];
                  $header_avatar_char = $_SESSION['user']['name'][0] ?? 'U';
              }
            ?>
            <?php if($header_avatar_url): ?>
              <img src="<?= htmlspecialchars($header_avatar_url) ?>?t=<?= time() ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
              <?= strtoupper(substr($header_avatar_char, 0, 1)) ?>
            <?php endif; ?>
          </div>'''

profile_card_avatar_html = '''<div class="user-avatar mb-md" style="width: 100px; height: 100px; font-size: 32px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: var(--primary);">
            <?php if(!empty($user['photo'])): ?>
              <img src="../<?= htmlspecialchars($user['photo']) ?>?t=<?= time() ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
              <?= strtoupper(substr($user['name'], 0, 1)) ?>
            <?php endif; ?>
          </div>'''

for f in files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Standardize it first if it has my previous multi-line fix
    # Since I only did the multi-line fix in update_profile.php, let's just do a manual replace for topbar.
    
    # 1. Replace the generic topbar avatar
    content = re.sub(
        r'<div class="user-avatar">.*?</div>',
        avatar_html,
        content,
        flags=re.DOTALL
    )

    # 2. In profile.php, replace the main card avatar
    content = re.sub(
        r'<div class="user-avatar mb-md" style="width: 100px; height: 100px; font-size: 32px;">.*?</div>',
        profile_card_avatar_html,
        content,
        flags=re.DOTALL
    )

    # 3. Clean up the multi-line topbar I just put in update_profile.php
    content = re.sub(
        r'<div class="user-avatar" style="overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: var\(--primary\);">.*?<div class="user-info">',
        avatar_html + '\n          <div class="user-info">',
        content,
        flags=re.DOTALL
    )
    
    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)

print("Done")
