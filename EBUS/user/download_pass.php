<?php
include("../includes/db.php");

$pass_id = $_GET['id'] ?? 0;

$sql = "SELECT * FROM passes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pass_id);
$stmt->execute();
$pass = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pass) {
    die("Pass not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Digital Pass</title>
  
  <link rel="stylesheet" href="../css/reset.css">
  <link rel="stylesheet" href="../css/variables.css">
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/layout.css">
  <link rel="stylesheet" href="../css/components.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/tables.css">
  <link rel="stylesheet" href="../css/pages.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body style="background-color: var(--bg);">

<div class="flex-col items-center justify-center p-xl" style="min-height: 100vh;">
  
  <div class="mb-lg no-print">
    <a href="my_passes.php" class="btn btn-secondary mb-md">&larr; Back</a>
    <div class="flex gap-sm">
      <button class="btn btn-primary" onclick="window.print()">&#128424; Print Pass</button>
    </div>
  </div>

  <div class="digital-pass" style="width: 100%; max-width: 450px; transform: scale(1.1); transform-origin: top center;">
    <div class="digital-pass-header">
      <div class="font-bold" style="font-size: 24px;">E-BUS</div>
      <div class="badge" style="background-color: rgba(255,255,255,0.2); color: white; font-size: 14px;"><?= htmlspecialchars($pass['pass_type']) ?> Pass</div>
    </div>
    <div class="digital-pass-body" style="padding: 32px 24px;">
      <div class="pass-photo flex items-center justify-center" style="width: 100px; height: 120px;">
        <?php if($pass['photo']): ?>
          <img src="../<?= htmlspecialchars($pass['photo']) ?>" alt="Photo" style="width:100%; height:100%; object-fit:cover; border-radius:4px;">
        <?php else: ?>
          <span style="font-size: 48px;">👤</span>
        <?php endif; ?>
      </div>
      <div class="pass-details flex-col justify-center">
        <div class="font-bold mb-xs" style="font-size: 24px;"><?= htmlspecialchars($pass['name']) ?></div>
        <div class="text-small mb-md" style="color: rgba(255,255,255,0.7); font-size: 14px;">Pass ID: STU-<?= htmlspecialchars($pass['id']) ?></div>
        
        <div class="font-medium mt-md" style="font-size: 18px;"><?= htmlspecialchars($pass['from_location']) ?> &harr; <?= htmlspecialchars($pass['to_location']) ?></div>
      </div>
    </div>
    <div class="digital-pass-footer" style="padding: 24px;">
      <div>
        <div class="text-small" style="color: rgba(255,255,255,0.7); margin-bottom: 4px;">Valid Till</div>
        <div class="font-medium" style="font-size: 20px;"><?= date('d M Y', strtotime($pass['pass_to'])) ?></div>
      </div>
      <div style="width: 80px; height: 80px; background-color: white; color: black; display: flex; align-items: center; justify-content: center; font-size: 24px; border-radius: var(--radius-sm);">
        QR
      </div>
    </div>
  </div>

  <style>
    @media print {
      body { background-color: white !important; }
      .no-print { display: none !important; }
      .digital-pass { box-shadow: none !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; transform: scale(1) !important; margin: 0 auto; }
    }
  </style>

</div>

</body>
</html>