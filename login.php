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
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'يرجى إدخال اسم المستخدم وكلمة المرور.';
    } else {
        /* 
         old version(vulnerable to SQL injection )
        $sql = "SELECT id, username, password, role FROM users WHERE username = '$username' LIMIT 1";
        $user = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);  
         */

        // Secure authentication query using a prepared statement to prevent SQL Injection
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        /* old version using insecure md5 hash 
        if ($user && $user['password'] === md5($password)) 
        */

        /*
         old version (no session ID regeneration on login):
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
        }
         */

        // Secure password hashing using bcrypt (PASSWORD_DEFAULT) + session token rotation on login
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                $_SESSION['admin_logged_in'] = true;
                header('Location: admin.php');
                exit;
            } else {
                header('Location: dashboard.php');
                exit;
            }
        }
    else {
            $error = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>غِراس | تسجيل الدخول</title>
    <link rel="icon" href="images/ghiras-logo.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #f7f8f3; font-family: 'Tajawal', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0;">

    <div style="background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 380px;">
        <div style="text-align: center; margin-bottom: 12px;">
            <img src="images/ghiras-logo.svg" alt="شعار غِراس" style="height: 54px;">
        </div>
        <h2 style="color: #16583e; text-align: center; margin-bottom: 20px;">تسجيل الدخول</h2>

        <?php if (!empty($error)): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center; font-size: 14px;">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #24352d;">اسم المستخدم</label>
                <input type="text" name="username" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #24352d;">كلمة المرور</label>
                <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <button type="submit" style="width: 100%; background: #16583e; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">
                دخول
            </button>
        </form>

        <div style="text-align: center; margin-top: 15px; font-size: 14px; color: #24352d;">
            مستخدم جديد؟ <a href="register.php" style="color: #16583e; font-weight: 700; text-decoration: none;">إنشاء حساب</a>
        </div>
    </div>

</body>
</html>