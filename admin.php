<?php
// منع التخزين المؤقت في المتصفح لمنع العودة للصفحة بعد تسجيل الخروج
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// old version:
// session_start();

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
/*
old version (insecure RBAC - allowed any logged-in user or missing role check to access admin.php):
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
*/

// Secure RBAC check: allow only authenticated admin accounts
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

// Handle Deletion
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $delStmt = $pdo->prepare("DELETE FROM initiatives WHERE id = ?");
    $delStmt->execute([$deleteId]);
    header("Location: admin.php?msg=deleted");
    exit;
}

// Fetch all initiatives
$stmt = $pdo->query("SELECT * FROM initiatives ORDER BY id DESC");
$initiatives = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>غِراس | لوحة تحكم المسؤول</title>
    <link rel="icon" href="images/ghiras-logo.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- القائمة العلوية -->
    <header class="site-header">
        <div class="top-strip">
            <div class="container strip-inner">
                <span>لوحة التحكم الإدارية</span>
                <div style="display: flex; gap: 15px;">
                    <a href="index.php">زيارة الموقع</a>
                    <a href="logout.php" style="color: #dc2626;">تسجيل خروج</a>
                </div>
            </div>
        </div>

        <nav class="main-nav container" aria-label="التنقل الرئيسي">
            <a class="brand" href="index.php">
                <img src="images/ghiras-logo.svg" alt="شعار غراس" width="54" height="54">
                <span class="brand-label">
                    <strong>غِراس</strong>
                    <small>منصة الاستدامة البيئية</small>
                </span>
            </a>
        </nav>
    </header>

    <!-- محتوى لوحة التحكم -->
    <main class="container admin-container">
        <div style="background: #e6f0ec; color: #16583e; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px;">
            <strong>مرحبًا، <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></strong> | رقم المستخدم: <?= (int)$_SESSION['user_id'] ?> | الصلاحية: <strong><?= htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8') ?></strong>
        </div> 
        
        <div class="admin-header-row">
            <h2>لوحة التحكم - إدارة المبادرات</h2>
            <a href="add.php" class="admin-btn-primary">+ إضافة مبادرة جديدة</a>
        </div>

        <p>استخدم هذه الصفحة لإدارة محتوى المبادرات (عرض، تعديل، أو حذف).</p>

        <?php if (isset($_GET['msg'])): ?>
            <div class="admin-alert">
                <?php
                    if ($_GET['msg'] === 'added') echo 'تمت إضافة المبادرة بنجاح.';
                    if ($_GET['msg'] === 'updated') echo 'تم تحديث المبادرة بنجاح.';
                    if ($_GET['msg'] === 'deleted') echo 'تم حذف المبادرة بنجاح.';
                ?>
            </div>
        <?php endif; ?>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>المعرف (ID)</th>
                        <th>التصنيف</th>
                        <th>العنوان</th>
                        <th>الوصف</th>
                        <th style="text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                
                    <?php if (!empty($initiatives)): ?>
                        <?php foreach ($initiatives as $index => $ind): ?>
                            <tr>
                                <?php /*
                                old version (vulnerable to XSS / unescaped output):
                                <td><?= $ind['id'] ?></td>
                                <td><?= $ind['category'] ?></td>
                                <td><?= $ind['title'] ?></td>
                                <td><?= mb_substr($ind['description'], 0, 50) ?>...</td>
                                <td style="text-align: center;">
                                    <a href="edit.php?id=<?= $ind['id'] ?>" class="admin-action-edit">تعديل</a>
                                    <a href="admin.php?delete=<?= $ind['id'] ?>" onclick="return confirm('هل أنت متأكد من الحذف؟');" class="admin-action-delete">حذف</a>
                                </td>
                                */ ?>
                                <!-- Secure output using htmlspecialchars and integer casting to prevent XSS --> 
                                <td><?= (int)$ind['id'] ?></td>
                                <td><?= htmlspecialchars($ind['category'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($ind['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars(mb_substr($ind['description'] ?? '', 0, 50, 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>...</td>
                                <td style="text-align: center;">
                                    <a href="edit.php?id=<?= (int)$ind['id'] ?>" class="admin-action-edit">تعديل</a>
                                    <a href="admin.php?delete=<?= (int)$ind['id'] ?>" onclick="return confirm('هل أنت متأكد من الحذف؟');" class="admin-action-delete">حذف</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">لا توجد مبادرات مضافة حالياً.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <div class="footer-brand">
                    <img src="images/ghiras-logo.svg" alt="شعار غراس" width="54" height="54">
                    <div>
                        <strong>غِراس</strong>
                        <small>منصة الاستدامة البيئية</small>
                    </div>
                </div>
                <p>نغرس اليوم، لنزدهر غدًا.</p>
            </div>
            <div>
                <h2>روابط سريعة</h2>
                <a href="index.php">الرئيسية</a>
                <a href="initiatives.php">المبادرات</a>
                <a href="vision2030.html">رؤية 2030</a>
            </div>
            <div>
                <h2>رسالتنا</h2>
                <p>نشر الوعي البيئي وتشجيع الممارسات المستدامة في المملكة العربية السعودية.</p>
            </div>
        </div>
        <div class="container footer-bottom">
            <span>© 2026 غِراس — جميع الحقوق محفوظة</span>
            <span>نغرس اليوم، لنزدهر غدًا.</span>
        </div>
    </footer>

    <script src="script.js"></script>
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