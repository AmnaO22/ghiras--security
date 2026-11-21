<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$initiative = null;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM initiatives WHERE id = ?");
    $stmt->execute([$id]);
    $initiative = $stmt->fetch(PDO::FETCH_ASSOC);
}

function getInitiativeIcon($category) {
    switch (trim($category)) {
        case 'البيئة': return 'images/tree.png';
        case 'المياه': return 'images/water.png';
        case 'النفايات': return 'images/recycle.png';
        case 'الطاقة': return 'images/sun.png';
        case 'المجتمع': return 'images/waste.png';
        case 'جودة الحياة': return 'images/transport.png';
        default: return 'images/tree.png';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="تفاصيل المبادرة البيئية من منصة غراس.">
  <title>غِراس | تفاصيل المبادرة</title>
  <link rel="icon" href="images/ghiras-logo.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
  <div class="top-strip">
    <div class="container strip-inner">
      <span>المملكة العربية السعودية <span class="tiny-separator">|</span> للاستدامة البيئية</span>
      <span>نحو مستقبل أكثر استدامة <span class="sprout">✧</span></span>
    </div>
  </div>
  <nav class="main-nav container" aria-label="التنقل الرئيسي">
    <a class="brand" href="index.php" aria-label="غراس - الرئيسية">
      <img src="images/ghiras-logo.svg" alt="" width="66" height="66">
      <span class="brand-label"><strong>غِراس</strong><small>منصة الاستدامة البيئية</small></span>
    </a>
    <ul class="nav-menu">
        <li><a href="index.php">الرئيسية</a></li>
        <li><a href="initiatives.php">المبادرات</a></li>
        
    </ul>
   <?php if (!empty($_SESSION['username']) || !empty($_SESSION['user_id'])): ?>
    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
        <a class="admin-link" href="admin.php">
            لوحة الإدارة
        </a>
    <?php elseif (($_SESSION['role'] ?? '') === 'user'): ?>
        <a class="admin-link" href="dashboard.php">
            حسابي
        </a>
    <?php endif; ?>

    <a class="admin-link" href="logout.php">
        تسجيل الخروج
        <img class="admin-user-icon"
             src="images/admin-user.png"
             alt=""
             width="26"
             height="26">
    </a>
<?php else: ?>
    <a class="admin-link" href="login.php">
        تسجيل الدخول
        <img class="admin-user-icon"
             src="images/admin-user.png"
             alt=""
             width="26"
             height="26">
    </a>
<?php endif; ?>
  </nav>
</header>

<main id="main-content">
<section class="page-hero">
    <div class="container">
        <span class="eyebrow eyebrow-light">غِراس | دليل المبادرات</span>
        <h1>تفاصيل المبادرة</h1>
        <p>أهداف عملية وخطوات بسيطة تساعدك على تبني ممارسات مستدامة.</p>
    </div>
</section>

<section class="initiatives-section container">
    <div class="list-header">
        <div>
            <span class="eyebrow">تعرّف على المبادرة</span>
            <h2>اكتشف أهدافها وخطوات بسيطة يمكنك تطبيقها.</h2>
        </div>
        <a href="initiatives.php" class="text-link">العودة إلى المبادرات <span aria-hidden="true">←</span></a>
    </div>

   <?php if ($initiative): ?>
        <!-- Using your class name with display override so CSS can't hide it -->
        <article class="detail-panel" style="display: block !important; background: #fff; padding: 40px; border-radius: 16px; border: 1px solid #e0e0e0; margin-bottom: 40px;">
            <div class="detail-title-row">
                <div class="initiative-icon">
                    <?php /*
                    old version (vulnerable to XSS - raw output without htmlspecialchars or URL validation):
                    <img src="<?= getInitiativeIcon($initiative['category']) ?>" alt="">
                   </div>
                    <div>
                    <span class="category-tag" style="background: #eef5f1; color: #16553c; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 13px;"><?= $initiative['category'] ?></span>
                    <h2><?= $initiative['title'] ?></h2>
                </div>
            </div>

            <p class="detail-summary"><?= $initiative['description'] ?></p>

            <h3>كيف يمكنك المساهمة؟</h3>
            <ul class="steps">
                <?php
                $steps = explode("\n", $initiative['how_to_contribute']);
                foreach ($steps as $step):
                    if (trim($step) !== ''): ?>
                        <li><?= trim($step) ?></li>
                    <?php endif;
                endforeach; ?>
            </ul>

            <h3>جهات ومصادر مفيدة</h3>
            <p><?= $initiative['source_text'] ?></p>

            <div class="source-action">
                <a href="<?= $initiative['source_link'] ?>" target="_blank" rel="noopener noreferrer" class="source-link">زيارة موقع الجهة الرسمية ↗</a>
            </div>
                    */ ?>

                    <!-- Secure output using htmlspecialchars and integer casting to prevent XSS -->
                    <img src="<?= htmlspecialchars(getInitiativeIcon($initiative['category'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($initiative['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div>
                    <span class="category-tag" style="background: #eef5f1; color: #16553c; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 13px;"><?= htmlspecialchars($initiative['category'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    <h2><?= htmlspecialchars($initiative['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
                </div>
            </div>

            <p class="detail-summary"><?= htmlspecialchars($initiative['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>

            <h3>كيف يمكنك المساهمة؟</h3>
            <ul class="steps">
                <?php
                $rawSteps = (string)($initiative['how_to_contribute'] ?? '');
                $steps = explode("\n", $rawSteps);
                foreach ($steps as $step):
                    $trimmedStep = trim($step);
                    if ($trimmedStep !== ''): ?>
                        <li><?= htmlspecialchars($trimmedStep, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endif;
                endforeach; ?>
            </ul>

            <h3>جهات ومصادر مفيدة</h3>
            <p><?= htmlspecialchars($initiative['source_text'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>

            <?php
            $rawLink = trim((string)($initiative['source_link'] ?? ''));
            $isValidUrl = filter_var($rawLink, FILTER_VALIDATE_URL) !== false;
            $scheme = $isValidUrl ? strtolower((string)parse_url($rawLink, PHP_URL_SCHEME)) : '';
            $safeLink = ($isValidUrl && in_array($scheme, ['http', 'https'], true)) ? $rawLink : '#';
            ?>
            <div class="source-action">
                <a href="<?= htmlspecialchars($safeLink, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="source-link">زيارة موقع الجهة الرسمية ↗</a>
            </div>

            <div style="margin-top: 30px;">
                <a href="initiatives.php" class="text-link">← العودة إلى المبادرات</a>
            </div>
        </article>
    <?php else: ?>
        <div style="background: #fff; padding: 40px; border-radius: 12px; text-align: center; border: 1px solid #e0e0e0; margin-bottom: 40px;">
            <p style="font-size: 18px; color: #666; margin-bottom: 20px;">عذراً، لم يتم العثور على هذه المبادرة.</p>
            <a href="initiatives.php" class="btn" style="background: #1b382b; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none;">تصفح جميع المبادرات</a>
        </div>
    <?php endif; ?>
</section>
<?php if ($initiative): ?>
    <?php
    $user = $_SESSION['username'] ?? $_SESSION['user'] ?? $_SESSION['email'] ?? '';
    $role = $_SESSION['role'] ?? 'user';
    $initiativeId = (int)$initiative['id'];

    // إضافة التعليق عند الإرسال ثم التحويل لمنع تكرار الإرسال عند الضغط على Refresh
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text']) && $user !== '') {
        $text = trim((string)$_POST['comment_text']);
        if ($text !== '') {
            $stmt = $pdo->prepare(
                'INSERT INTO comments (initiative_id, username, role, comment_text) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([$initiativeId, $user, $role, $text]);

            $redirectUrl = 'details.php?id=' . $initiativeId;
            echo '<script>window.location.replace(' . json_encode($redirectUrl, JSON_UNESCAPED_SLASHES) . ');</script>';
            exit;
        }
    }

    $stmt = $pdo->prepare(
        'SELECT username, role, comment_text, created_at FROM comments WHERE initiative_id = ? ORDER BY id DESC'
    );
    $stmt->execute([$initiativeId]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <section class="comments-section container" style="background: #fff; padding: 24px; border-radius: 16px; border: 1px solid #e0e0e0; margin-bottom: 40px;">
        <h3>التعليقات</h3>

        <?php if ($user !== ''): ?>
            <?php /*
            old version (vulnerable to XSS / uncast ID in form action):
            <form method="POST" action="details.php?id=<?= $initiative['id'] ?>" style="margin-bottom: 20px;">
            */ ?>

            <!-- Secure form action using integer casting to prevent XSS -->
            <form method="POST" action="details.php?id=<?= $initiativeId ?>" style="margin-bottom: 20px;">
                <textarea name="comment_text" rows="3" required placeholder="اكتب تعليقك..." style="width: 100%; padding: 8px; margin-bottom: 8px;"></textarea>
                <button type="submit">إرسال</button>
            </form>
        <?php else: ?>
            <p>سجّل الدخول لإضافة تعليق.</p>
        <?php endif; ?>

        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $c): ?>
                <div style="border-top: 1px solid #eee; padding: 10px 0;">
                    <?php /*
                    old version (not secure to XSS - raw unescaped output without htmlspecialchars):
                    <strong><?= $c['username'] ?></strong>
                    <small>(<?= $c['role'] ?> - <?= $c['created_at'] ?>)</small>
                    <p><?= $c['comment_text'] ?></p>
                    */ ?>

                    <!-- Secure output using htmlspecialchars and integer casting to prevent XSS -->
                    <strong><?= htmlspecialchars($c['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                    <small>(<?= htmlspecialchars($c['role'] ?? '', ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($c['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</small>
                    <p><?= nl2br(htmlspecialchars($c['comment_text'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: #777;">لا توجد تعليقات بعد.</p>
        <?php endif; ?>
    </section>
<?php endif; ?>
</main>

<footer class="site-footer">
  <div class="container footer-grid">
    <div>
        <div class="footer-brand">
            <img src="images/ghiras-logo.svg" alt="" width="54" height="54">
            <div><strong>غِراس</strong><small>منصة الاستدامة البيئية</small></div>
        </div>
        <p>نغرس اليوم، لنزدهر غدًا.</p>
    </div>
    <div>
        <h2>روابط سريعة</h2>
        <a href="index.php">الرئيسية</a>
        <a href="initiatives.php">المبادرات</a>
        
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

    <script src="script.js" defer></script>
</body>
</html>
</body>
</html>