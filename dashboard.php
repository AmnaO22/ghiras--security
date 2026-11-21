<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


// old version:
// if (session_status() !== PHP_SESSION_ACTIVE) {
//     session_start();
// }

// new version: set secure session cookie flags before session_start()
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once 'db.php';
$stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $_SESSION['user_id']]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$currentUser) {
    header("Location: logout.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>غِراس | لوحة المستخدم</title>
    <link rel="icon" href="images/ghiras-logo.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body style="background: #f7f8f3; font-family: 'Tajawal', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px;">

    <div style="background: white; padding: 36px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 520px; border-top: 5px solid #16583e;">
        
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <img src="images/ghiras-logo.svg" alt="شعار غِراس" style="height: 44px;">
                <div>
                    <h2 style="color: #16583e; margin: 0; font-size: 20px;">مرحبًا، <?= htmlspecialchars($currentUser['username'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <span style="font-size: 12px; color: #64766a;">منصة غِراس للاستدامة البيئية</span>
                </div>
            </div>
            <a href="logout.php" style="background: #fee2e2; color: #991b1b; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 13px;">
                تسجيل الخروج
            </a>
        </div>

        <!-- Account Info -->
        <div style="background: #f7f8f3; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="color: #16583e; margin: 0; font-size: 16px;">معلومات الحساب</h3>
                <span style="background: #e6f0ec; color: #16583e; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;">
                    <?= htmlspecialchars($currentUser['role'], ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
            <p style="margin: 6px 0; color: #24352d; font-size: 14px;"><strong>رقم المستخدم:</strong> <?= (int)$currentUser['id'] ?></p>
            <p style="margin: 6px 0; color: #24352d; font-size: 14px;"><strong>اسم المستخدم:</strong> <?= htmlspecialchars($currentUser['username'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 10px; justify-content: center;">
            <a href="index.php" style="background: #16583e; color: white; padding: 11px 22px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px;">
                تصفح منصة غِراس
            </a>
            <?php if ($currentUser['role'] === 'admin'): ?>
                <a href="admin.php" style="background: #24352d; color: white; padding: 11px 22px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px;">
                    لوحة تحكم المسؤول
                </a>
            <?php endif; ?>
        </div>

        <p style="text-align: center; color: #64766a; font-size: 12px; margin-top: 20px; margin-bottom: 0;">
            نغرس اليوم، لنزدهر غدًا
        </p>
    </div>
    <script>
        window.addEventListener('pageshow', function (event) {
            const navEntries = performance.getEntriesByType('navigation');
            const isBackForward = event.persisted || (navEntries.length > 0 && navEntries[0].type === 'back_forward');
            if (isBackForward) {
                document.body.style.display = 'none';
                window.location.reload();
            }
        });
    </script>
</body>
</html>

</body>
</html>