<?php
/**
 * Admin — login page.
 */

require __DIR__ . '/_bootstrap.php';

if (is_authed()) {
    header('Location: index.php');
    exit;
}

$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        $error = true;
    } else {
        $pass = isset($_POST['password']) ? (string) $_POST['password'] : '';
        if (hash_equals(ADMIN_PASSWORD, $pass)) {
            session_regenerate_id(true);
            $_SESSION['mohusyn_auth'] = true;
            header('Location: index.php?msg=login-ok');
            exit;
        }
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>ورود — پنل مدیریت محسین</title>
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-body login-body">
  <form method="post" class="login-card">
    <?php echo csrf_field(); ?>
    <h1 class="login-brand">MOHUSYN</h1>
    <p class="login-sub">پنل مدیریت محتوا</p>

    <?php if ($error): ?>
      <div class="flash flash-error">رمز عبور نادرست است.</div>
    <?php endif; ?>

    <label class="field-label" for="password">رمز عبور</label>
    <input type="password" id="password" name="password" autofocus autocomplete="current-password">
    <button class="btn btn-primary login-btn" type="submit">ورود</button>

    <a class="login-back" href="/">بازگشت به سایت ←</a>
  </form>
</body>
</html>
