<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$count = (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
$message = '';
if ($count === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!preg_match('/^[A-Za-z0-9_]{4,50}$/', $username)) {
        $message = 'Username must be 4-50 characters: letters, numbers or underscore.';
    } elseif (strlen($password) < 12) {
        $message = 'Use a password with at least 12 characters.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
        header('Location: login.php?setup=success'); exit;
    }
}
$pageTitle = 'Admin setup';
require __DIR__ . '/includes/header.php';
?>
<section class="form-panel narrow">
<?php if ($count > 0): ?>
  <h1>Setup is closed</h1><p>An admin account already exists. Please use the login page.</p>
  <a class="button" href="login.php">Admin login</a>
<?php else: ?>
  <p class="eyebrow">FIRST-TIME SETUP</p><h1>Create admin account</h1>
  <p class="muted">This page automatically closes after the first admin is created.</p>
  <?php if ($message): ?><p class="notice error"><?= e($message) ?></p><?php endif; ?>
  <form method="post" class="stack-form">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Username<input name="username" required minlength="4" maxlength="50" autocomplete="username"></label>
    <label>Password<input type="password" name="password" required minlength="12" autocomplete="new-password"></label>
    <button class="button" type="submit">Create admin</button>
  </form>
<?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
