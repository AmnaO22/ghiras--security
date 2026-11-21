<?php

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
old version (insecure RBAC - only checked admin_logged_in without verifying user_id and admin role):
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
*/

// Secure RBAC check: require authenticated user_id and explicit 'admin' role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = trim($_POST['category'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $how_to_contribute = trim($_POST['how_to_contribute'] ?? '');
    $source_text = trim($_POST['source_text'] ?? '');
    $source_link = trim($_POST['source_link'] ?? '');

    if ($category && $title && $description && $how_to_contribute && $source_text && $source_link) {
        $stmt = $pdo->prepare("INSERT INTO initiatives (category, title, description, how_to_contribute, source_text, source_link) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$category, $title, $description, $how_to_contribute, $source_text, $source_link]);
        header("Location: admin.php?msg=added");
        exit;
    } else {
        $error = "يرجى تعبئة جميع الحقول المطلوبة.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>غِراس | إضافة مبادرة جديدة</title>
  <link rel="stylesheet" href="style.css?v=2">
</head>
<body>
<header class="site-header">
  <div class="top-strip">
    <div class="container strip-inner">
      <span>لوحة التحكم <span class="tiny-separator">|</span> إضافة مبادرة</span>
    </div>
  </div>
</header>

<main class="container" style="padding: 40px 20px; max-width: 700px;">
    <h2>إضافة مبادرة جديدة</h2>
    <?php if ($error): ?>
        <p style="color: red; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form id="initiativeForm" method="POST" action="add.php" class="initiative-form">
        <label>التصنيف:
            <select name="category" class="form-control" required>
                <option value="" disabled selected>اختر التصنيف...</option>
                <?php 
                $categories = ['البيئة', 'المياه', 'النفايات', 'الطاقة', 'المجتمع', 'جودة الحياة'];
                foreach ($categories as $cat): ?>
                    <option value="<?= $cat ?>"><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>عنوان المبادرة:
            <input type="text" name="title" id="titleInput" placeholder="أدخل عنوان المبادرة" class="form-control" required>
        </label>

        <label>الوصف:
            <textarea name="description" rows="3" placeholder="اكتب وصفًا مختصرًا للمبادرة..." class="form-control" required></textarea>
        </label>

        <label>كيف يمكنك المساهمة؟ (كل نقطة في سطر جديد):
            <textarea name="how_to_contribute" rows="5" placeholder="اكتب خطوات المساهمة هنا..." class="form-control" required></textarea>
        </label>

        <label>جهات ومصادر مفيدة:
            <textarea name="source_text" rows="2" placeholder="اذكر الجهات أو المصادر الداعمة..." class="form-control"></textarea>
        </label>

        <label>رابط موقع الجهة الرسمية:
            <input type="url" name="source_link" placeholder="https://example.com" class="form-control">
        </label>

        <div class="form-actions">
            <!-- Changed to type="submit" so the form successfully posts to PHP -->
            <button type="submit" id="submitBtn" class="btn-primary">إضافة المبادرة</button>
            <a href="admin.php" class="btn-secondary">إلغاء</a>
        </div>
    </form>
</main>

<script src="script.js" defer></script>
</body>
</html>