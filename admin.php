<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_admin();

$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $version = trim($_POST['version'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $cover = trim($_POST['cover_url'] ?? '');
    $coverFile = $_FILES['cover_image'] ?? null;
    $file = $_FILES['game_file'] ?? null;

    // Optional cover image upload. A selected file takes priority over the URL.
    $savedCover = null;
    if ($coverFile && $coverFile['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($coverFile['error'] !== UPLOAD_ERR_OK) {
            $error = 'Cover image upload failed. Please try again.';
        } elseif ($coverFile['size'] > 5 * 1024 * 1024) {
            $error = 'Cover image must be 5 MB or smaller.';
        } else {
            $imageInfo = @getimagesize($coverFile['tmp_name']);
            $mime = $imageInfo['mime'] ?? '';
            $imageTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            if (!$imageInfo || !isset($imageTypes[$mime]) || $imageInfo[0] > 6000 || $imageInfo[1] > 6000) {
                $error = 'Choose a valid JPG, PNG or WEBP image up to 6000 x 6000 pixels.';
            } else {
                $coverDir = __DIR__ . '/assets/uploads/covers';
                if (!is_dir($coverDir) && !mkdir($coverDir, 0755, true)) {
                    $error = 'Could not create the cover image folder.';
                } else {
                    $coverName = bin2hex(random_bytes(16)) . '.' . $imageTypes[$mime];
                    if (move_uploaded_file($coverFile['tmp_name'], $coverDir . '/' . $coverName)) {
                        $savedCover = 'assets/uploads/covers/' . $coverName;
                    } else {
                        $error = 'Could not save the cover image. Check folder permissions.';
                    }
                }
            }
        }
    }
    if ($savedCover !== null) { $cover = $savedCover; }

    if ($error !== '') {
        // Keep the validation error from the optional cover image upload.
    } elseif ($title === '' || mb_strlen($title) > 120 || $description === '') {
        $error = 'Enter a title (maximum 120 characters) and description.';
    } elseif (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Choose a file to upload. Check PHP upload limits if the file is too large.';
    } elseif ($file['size'] > 500 * 1024 * 1024) {
        $error = 'Maximum file size is 500 MB.';
    } else {
        $original = basename($file['name']);
        $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $allowed = ['zip', 'exe', 'apk', 'iso'];
        if (!in_array($extension, $allowed, true)) {
            $error = 'Allowed file types: ZIP, EXE, APK and ISO.';
        } elseif (!is_dir(STORAGE_DIR) && !mkdir(STORAGE_DIR, 0750, true)) {
            $error = 'Could not create the private storage folder. Create gamehub_storage beside htdocs.';
        } else {
            $stored = bin2hex(random_bytes(20)) . '.' . $extension;
            $destination = STORAGE_DIR . DIRECTORY_SEPARATOR . $stored;
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                try {
                    $stmt = $pdo->prepare('INSERT INTO games (title, description, version, category, cover_url, original_filename, stored_filename, file_size) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$title, $description, $version, $category, $cover, $original, $stored, (int)$file['size']]);
                    $message = 'Game uploaded successfully.';
                } catch (Throwable $e) {
                    @unlink($destination);
                    if ($savedCover !== null) { @unlink(__DIR__ . '/' . $savedCover); }
                    $error = 'Database error while saving the game.';
                }
            } else {
                $error = 'Upload failed. Check folder permissions and PHP upload settings.';
            }
        }
    }
}

$games = $pdo->query('SELECT id, title, downloads, created_at FROM games ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Admin dashboard';
require __DIR__ . '/includes/header.php';
?>
<div class="section-heading"><div><p class="eyebrow">CONTROL PANEL</p><h1>Welcome, <?= e($_SESSION['admin_name'] ?? 'Admin') ?></h1></div><a class="text-link" href="logout.php">Log out</a></div>
<section class="form-panel">
  <h2>Upload a game</h2>
  <p class="muted">Upload only files you own or are authorized to distribute. Game files: maximum 500 MB. Cover images: JPG, PNG or WEBP, maximum 5 MB.</p>
  <?php if ($message): ?><p class="notice"><?= e($message) ?></p><?php endif; ?>
  <?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
  <form method="post" enctype="multipart/form-data" class="stack-form">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="two-col">
      <label>Game title<input name="title" maxlength="120" required></label>
      <label>Version<input name="version" maxlength="40" placeholder="e.g. 1.0"></label>
    </div>
    <div class="two-col">
      <label>Category<input name="category" maxlength="60" placeholder="e.g. Adventure"></label>
      <label>Cover image from phone <span class="muted">(optional)</span><input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"></label>
    </div>
    <label>Or use a cover image URL <span class="muted">(optional; used if no image file is selected)</span><input name="cover_url" type="url" maxlength="500" placeholder="https://..."></label>
    <label>Description<textarea name="description" rows="4" required></textarea></label>
    <label>Game file <span class="muted">(.zip, .exe, .apk, .iso)</span><input type="file" name="game_file" accept=".zip,.exe,.apk,.iso" required></label>
    <button class="button" type="submit">Upload game</button>
  </form>
</section>
<section class="admin-list">
  <h2>Uploaded games</h2>
  <?php if (!$games): ?><p class="muted">No uploads yet.</p><?php else: ?>
    <?php foreach ($games as $game): ?>
      <div class="admin-row"><div><strong><?= e($game['title']) ?></strong><p class="tiny muted"><?= e($game['created_at']) ?> · <?= number_format((int)$game['downloads']) ?> downloads</p></div><a class="text-link" href="game.php?id=<?= (int)$game['id'] ?>">View →</a></div>
    <?php endforeach; ?>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
