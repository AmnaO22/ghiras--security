<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once 'db.php';

// Fetch all initiatives from the database in descending order
$stmt = $pdo->query("SELECT * FROM initiatives ORDER BY id DESC");
$initiatives = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to map categories to specific icons
function getInitiativeIcon($category) {
    switch (trim($category)) {
        case 'البيئة': return 'images/tree.png';
        case 'المياه': return 'images/water.png';
        case 'النفايات': return 'images/recycle.png';
        case 'الطاقة': return 'images/sun.png';
        case 'المجتمع': return 'images/waste.png';
        case 'جودة الحياة': return 'images/transport.png';
        default: return 'images/tree.png'; // Default icon fallback
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="مبادرات وممارسات غراس البيئية في التشجير والمياه والطاقة وإعادة التدوير.">
  <title>غِراس | المبادرات</title>
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
    <a class="brand" href="index.html" aria-label="غراس - الرئيسية">
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
</header>

<main id="main-content">
<section class="page-hero">
    <div class="container">
        <span class="eyebrow eyebrow-light">غِراس | مبادراتنا</span>
        <h1>مبادرات غِراس البيئية</h1>
        <p>اكتشف أفكارًا وممارسات مستدامة، وابدأ بخطوات بسيطة لصنع أثر إيجابي في بيئتنا.</p>
    </div>
    <!-- Category Filter Buttons -->
<div class="category-filters" dir="rtl">
    <button class="filter-btn active" data-filter="all">الكل</button>
    <button class="filter-btn" data-filter="البيئة">البيئة</button>
    <button class="filter-btn" data-filter="المياه">المياه</button>
    <button class="filter-btn" data-filter="النفايات">النفايات</button>
    <button class="filter-btn" data-filter="الطاقة">الطاقة</button>
    <button class="filter-btn" data-filter="المجتمع">المجتمع</button>
    <button class="filter-btn" data-filter="جودة الحياة">جودة الحياة</button>
</div>
</section>

<section class="initiatives-section container">
    <div class="list-header">
        <div>
            <span class="eyebrow">شارك في التغيير</span>
            <h2>استكشف المجالات</h2>
        </div>
        <p>اختر مبادرة لقراءة أهدافها وخطوات تطبيقها.</p>
    </div>

    <div class="initiatives-grid">
        <?php if (!empty($initiatives)): ?>
            <?php foreach ($initiatives as $row): ?>
                <article class="initiative-card">
                    <div class="initiative-card-top">
                        <div class="initiative-icon">
                            <?php /*
                            old version (vulnerable to XSS - unescaped database output):
                            <img src="<?= getInitiativeIcon($row['category']) ?>" alt="" width="76" height="76">
                        </div>
                        <span class="category-tag"><?= $row['category'] ?></span>
                    </div>
                    <div class="initiative-card-body">
                        <h2><?= $row['title'] ?></h2>
                        <p><?= $row['description'] ?></p>
                        <a href="details.php?id=<?= $row['id'] ?>" class="text-link">عرض التفاصيل <span aria-hidden="true">←</span></a>
                            */ ?>
                                <!-- Secure output using htmlspecialchars and integer casting to prevent XSS --> 
                            <img src="<?= htmlspecialchars(getInitiativeIcon($row['category'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" width="76" height="76">
                        </div>
                        <span class="category-tag"><?= htmlspecialchars($row['category'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="initiative-card-body">
                        <h2><?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= htmlspecialchars($row['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                        <a href="details.php?id=<?= (int)$row['id'] ?>" class="text-link">عرض التفاصيل <span aria-hidden="true">←</span></a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: span 3; color: #666; padding: 30px;">لا توجد مبادرات مضافة حالياً. يمكنك إضافتها من لوحة التحكم.</p>
        <?php endif; ?>
    </div>
</section>
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