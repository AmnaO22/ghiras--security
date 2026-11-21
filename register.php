<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'يرجى إدخال اسم المستخدم وكلمة المرور.';
    } else {
        /*
         old version (vulnerable to SQL injection)
         $checkSql = "SELECT id FROM users WHERE username = '$username'";
         $existing = $pdo->query($checkSql)->fetch();
       */

        // Secure authentication query using a prepared statement to prevent SQL Injectior
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);

        if ($stmt->fetch()) {
            $error = 'اسم المستخدم مستخدم بالفعل.';
        } else {
            /* old version 
             //using insecure  MD5 hash 
             $hashedPassword = md5($password);
             // no restriction on passward
            $insert = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'user')");
            $insert->execute([
                ':username' => $username,
                ':password' => $hashedPassword
            ]);
            */

                    $hasMinLength = strlen($password) >= 8;
                    $hasUpper     = preg_match('/[A-Z]/', $password);
                    $hasLower     = preg_match('/[a-z]/', $password);
                    $hasNumber    = preg_match('/[0-9]/', $password);
                    $hasSpecial   = preg_match('/[^A-Za-z0-9]/', $password);

                    if (!$hasMinLength || !$hasUpper || !$hasLower || !$hasNumber || !$hasSpecial) {
                    $error = 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل وتحتوي على حرف كبير، حرف صغير، رقم، ورمز خاص.';
                    } else {
                     // Secure password hashing using bcrypt (PASSWORD_DEFAULT)
                     $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                      $insert = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'user')");
                      $insert->execute([
                      ':username' => $username,
                     ':password' => $hashedPassword
                     ]);
            $success = 'تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.';
        }
    }
}}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>غِراس | إنشاء حساب</title>
    <link rel="icon" href="images/ghiras-logo.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #f7f8f3; font-family: 'Tajawal', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0;">

    <div style="background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 380px;">
        <div style="text-align: center; margin-bottom: 12px;">
            <img src="images/ghiras-logo.svg" alt="شعار غِراس" style="height: 54px;">
        </div>
        <h2 style="color: #16583e; text-align: center; margin-bottom: 20px;">إنشاء حساب جديد</h2>

        <?php if (!empty($error)): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center; font-size: 14px;">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div style="background: #dcfce7; color: #166534; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center; font-size: 14px;">
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #24352d;">اسم المستخدم</label>
                <input type="text" name="username" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #24352d;">كلمة المرور</label>
                <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">

               <ul style="margin: 8px 0 0; padding-right: 18px; font-size: 12px; color: #475569; line-height: 1.6;">
                 <li>8 أحرف على الأقل</li>
                 <li>حرف كبير واحد على الأقل (A-Z)</li>
                 <li>حرف صغير واحد على الأقل (a-z)</li>
                 <li>رقم واحد على الأقل (0-9)</li>
                 <li>رمز خاص واحد على الأقل (مثل: !@#$%^&*)</li>
               </ul>
            </div>

            <button type="submit" style="width: 100%; background: #16583e; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">
                تسجيل
            </button>
        </form>

        <div style="text-align: center; margin-top: 15px; font-size: 14px; color: #24352d;">
            لديك حساب بالفعل؟ <a href="login.php" style="color: #16583e; font-weight: 700; text-decoration: none;">تسجيل الدخول</a>
        </div>
    </div>

</body>
</html>