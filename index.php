<?php

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

if (session_status() !== PHP_SESSION_ACTIVE) {
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
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="غراس منصة سعودية للتوعية بالاستدامة البيئية.">

    <title>غِراس | الرئيسية</title>

    <link rel="icon"
          href="images/ghiras-logo.svg"
          type="image/svg+xml">

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700&display=swap"
          rel="stylesheet">

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Header -->

    <header class="site-header">

        <div class="top-strip">

            <div class="container strip-inner">

                <span>
                    المملكة العربية السعودية
                    <span class="tiny-separator">|</span>
                    للاستدامة البيئية
                </span>

                <span>
                    نحو مستقبل أكثر استدامة
                    <span class="sprout">✧</span>
                </span>

            </div>

        </div>


        <!-- Navigation -->

        <nav class="main-nav container"
             aria-label="التنقل الرئيسي">

            <a class="brand"
               href="index.php"
               aria-label="غراس - الرئيسية">

                <img src="images/ghiras-logo.svg"
                     alt="شعار غراس"
                     width="66"
                     height="66">

                <span class="brand-label">

                    <strong>غِراس</strong>

                    <small>
                        منصة الاستدامة البيئية
                    </small>

                </span>
            <!-- edit -->
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


    <!-- Main Content -->

    <main id="main-content">


        <!-- Hero Section -->

        <section class="hero">

            <div class="container hero-inner">

                <div class="hero-copy">

                    <span class="eyebrow eyebrow-light">
                        غِراس | نحو مستقبل مستدام
                    </span>

                    <h1>
                        نغرس اليوم،<br>
                        لنزدهر غدًا
                    </h1>

                    <p>
                        نؤمن بأن الاستدامة تبدأ بالوعي،
                        وأن كل خطوة نحو حماية بيئتنا
                        تساهم في بناء مستقبل أفضل
                        للأجيال القادمة.
                    </p>

                    <a class="gold-btn"
                       href="initiatives.php">

                        استكشف المبادرات

                        <span aria-hidden="true">←</span>

                    </a>

                </div>

            </div>

        </section>


        <!-- About Section -->

        <section id="about" class="about-section">

            <div class="container about-layout">

                <div class="section-side">

                    <span class="eyebrow">
                        تعرّف علينا
                    </span>

                    <h2>من نحن</h2>

                </div>


                <div class="about-copy">

                    <p>
                        غِراس منصة سعودية للتوعية
                        بالاستدامة البيئية وتشجيع
                        المجتمع على تبني ممارسات
                        إيجابية تحافظ على الموارد
                        الطبيعية.
                    </p>

                    <p>
                        نسعى إلى نشر المعرفة بالمبادرات
                        البيئية وإلهام الأفراد للمشاركة
                        في بناء مستقبل أكثر استدامة،
                        بما يتماشى مع توجهات رؤية
                        السعودية 2030.
                    </p>

                </div>

            </div>

        </section>


        <!-- Sustainability Section -->

        <section class="pillars-section">

            <div class="container">

                <div class="section-title">

                    <span class="eyebrow">
                        معًا نصنع الفرق
                    </span>

                    <h2>مجالات الاستدامة</h2>

                    <p>
                        مجالات أساسية تساعد على حماية
                        بيئتنا والمحافظة على مواردها.
                    </p>

                </div>


                <div class="pillar-grid">


                    <!-- Trees -->

                    <article class="pillar-card">

                        <div class="icon-tile">

                            <img src="images/tree.png"
                                 alt="رمز التشجير"
                                 width="66"
                                 height="66">

                        </div>

                        <h3>التشجير وحماية البيئة</h3>

                        <p>
                            زيادة المساحات الخضراء
                            والحفاظ على البيئة
                            والموارد الطبيعية.
                        </p>

                    </article>


                    <!-- Water -->

                    <article class="pillar-card">

                        <div class="icon-tile">

                            <img src="images/water.png"
                                 alt="رمز المياه"
                                 width="66"
                                 height="66">

                        </div>

                        <h3>ترشيد استهلاك المياه</h3>

                        <p>
                            تعزيز الاستخدام المسؤول
                            للمياه ونشر الوعي
                            بأهمية تقليل الهدر.
                        </p>

                    </article>


                    <!-- Recycling -->

                    <article class="pillar-card">

                        <div class="icon-tile">

                            <img src="images/recycle.png"
                                 alt="رمز إعادة التدوير"
                                 width="66"
                                 height="66">

                        </div>

                        <h3>إعادة التدوير</h3>

                        <p>
                            تقليل النفايات وإعادة
                            استخدام المواد للمساهمة
                            في حماية البيئة.
                        </p>

                    </article>

                </div>

            </div>

        </section>


        <!-- Explore Section -->

        <section class="container cta-section">

            <div class="cta-content">

                <div>

                    <span class="eyebrow eyebrow-light">
                        ابدأ بخطوة
                    </span>

                    <h2>
                        مستقبل مستدام يبدأ بك
                    </h2>

                    <p>
                        تعرّف على المبادرات والممارسات
                        التي تساهم في حماية بيئتنا.
                    </p>

                </div>


                <a class="gold-btn"
                   href="initiatives.php">

                    اكتشف مبادراتنا

                    <span aria-hidden="true">←</span>

                </a>

            </div>

        </section>

    </main>


    <!-- Footer -->

    <footer class="site-footer">

        <div class="container footer-grid">


            <!-- Brand -->

            <div>

                <div class="footer-brand">

                    <img src="images/ghiras-logo.svg"
                         alt="شعار غراس"
                         width="54"
                         height="54">

                    <div>

                        <strong>غِراس</strong>

                        <small>
                            منصة الاستدامة البيئية
                        </small>

                    </div>

                </div>

                <p>
                    نغرس اليوم، لنزدهر غدًا.
                </p>

            </div>

            <!-- Quick Links (edit)-->
            <div>
                <h2>روابط سريعة</h2>
                <a href="index.php">الرئيسية</a>
                <a href="initiatives.php">المبادرات</a>
               
                
            </div>

            <!-- Mission -->

            <div>

                <h2>رسالتنا</h2>

                <p>
                    نشر الوعي البيئي وتشجيع
                    الممارسات المستدامة في
                    المملكة العربية السعودية.
                </p>

            </div>

        </div>


        <!-- Copyright -->

        <div class="container footer-bottom">

            <span>
                © 2026 غِراس — جميع الحقوق محفوظة
            </span>

            <span>
                نغرس اليوم، لنزدهر غدًا.
            </span>

        </div>

    </footer>

    <script src="script.js"></script>

</body>


</html>