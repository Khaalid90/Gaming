<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
if (is_admin()) { header('Location: admin.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$admin['id'];
        $_SESSION['admin_name'] = $admin['username'];
        header('Location: admin.php'); exit;
    }
    $error = 'Incorrect username or password.';
}
$pageTitle = 'Admin login';
require __DIR__ . '/includes/header.php';
?>
<section class="form-panel narrow">
  <p class="eyebrow">ADMIN AREA</p><h1>Sign in</h1>
  <?php if (isset($_GET['setup'])): ?><p class="notice">Admin account created. Please sign in.</p><?php endif; ?>
  <?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
  <form method="post" class="stack-form">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Username<input name="username" required autocomplete="username"></label>
    <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
    <button class="button" type="submit">Login</button>
  </form>
  <p class="tiny muted">First time? <a href="setup_admin.php">Create the admin account</a>.</p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
