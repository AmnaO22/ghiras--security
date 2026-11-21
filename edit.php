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

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: admin.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM initiatives WHERE id = ?");
$stmt->execute([$id]);
$initiative = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$initiative) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = trim($_POST['category'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $how_to_contribute = trim($_POST['how_to_contribute'] ?? '');
    $source_text = trim($_POST['source_text'] ?? '');
    $source_link = trim($_POST['source_link'] ?? '');

    if ($category && $title && $description && $how_to_contribute && $source_text && $source_link) {
        $updateStmt = $pdo->prepare("UPDATE initiatives SET category = ?, title = ?, description = ?, how_to_contribute = ?, source_text = ?, source_link = ? WHERE id = ?");
        $updateStmt->execute([$category, $title, $description, $how_to_contribute, $source_text, $source_link, $id]);
        header("Location: admin.php?msg=updated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>غِراس | تعديل المبادرة</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
  <div class="top-strip">
    <div class="container strip-inner">
      <span>لوحة التحكم <span class="tiny-separator">|</span> تعديل مبادرة</span>
    </div>
  </div>
</header>

<main class="container" style="padding: 40px 20px; max-width: 700px;">
    <h2>تعديل بيانات المبادرة</h2>
   <form id="initiativeForm" method="POST" action="edit.php?id=<?= $initiative['id'] ?>" style="display: flex; flex-direction: column; gap: 15px; background: #fff; padding: 30px; border-radius: 16px; border: 1px solid #e0e0e0; box-shadow: 0 4px 16px rgba(0,0,0,0.03);">
        <label>التصنيف:
            <select name="category" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 8px; border: 1px solid #ccc;">
                <?php 
                $categories = ['البيئة', 'المياه', 'النفايات', 'الطاقة', 'المجتمع', 'جودة الحياة'];
                foreach ($categories as $cat): ?>
                    <option value="<?= $cat ?>" <?= $initiative['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>عنوان المبادرة:
            <input type="text" name="title" id="titleInput" value="<?= htmlspecialchars($initiative['title']) ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 8px; border: 1px solid #ccc;">
        </label>

        <label>الوصف:
            <textarea name="description" rows="3" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 8px; border: 1px solid #ccc;"><?= htmlspecialchars($initiative['description']) ?></textarea>
        </label>

        <label>كيف يمكنك المساهمة؟ (كل نقطة في سطر جديد):
            <textarea name="how_to_contribute" rows="5" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 8px; border: 1px solid #ccc;"><?= htmlspecialchars($initiative['how_to_contribute']) ?></textarea>
        </label>

        <label>جهات ومصادر مفيدة:
            <textarea name="source_text" rows="2" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 8px; border: 1px solid #ccc;"><?= htmlspecialchars($initiative['source_text']) ?></textarea>
        </label>

        <label>رابط موقع الجهة الرسمية:
            <input type="url" name="source_link" value="<?= htmlspecialchars($initiative['source_link']) ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 8px; border: 1px solid #ccc;">
        </label>

        <div style="display: flex; gap: 10px; margin-top: 10px;">
            <!-- Notice: type="button" and id="submitBtn" so JS controls it safely -->
            <button type="button" id="submitBtn" style="background: #1b382b; color: #fff; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">تحديث البيانات</button>
            <a href="admin.php" style="padding: 12px 24px; text-decoration: none; color: #555; background: #eee; border-radius: 8px; display: inline-block;">إلغاء</a>
        </div>
</form>


<script src="script.js" defer></script>
</body>
</html>