<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM games WHERE id = ?');
$stmt->execute([$id ?: 0]);
$game = $stmt->fetch();
if (!$game) { http_response_code(404); exit('Game not found.'); }
$pageTitle = $game['title'];
require __DIR__ . '/includes/header.php';
?>
<a class="back-link" href="index.php">← Back to games</a>
<section class="detail-layout">
  <div class="detail-cover">
    <?php if (!empty($game['cover_url'])): ?>
      <img src="<?= e($game['cover_url']) ?>" alt="<?= e($game['title']) ?> cover">
    <?php else: ?><div class="cover-placeholder">GAMEHUB</div><?php endif; ?>
  </div>
  <div class="detail-info">
    <p class="eyebrow"><?= e($game['category'] ?: 'Game') ?></p>
    <h1><?= e($game['title']) ?></h1>
    <p class="muted"><?= e($game['description']) ?></p>
    <div class="info-list">
      <div><span>Version</span><strong><?= e($game['version'] ?: 'Not specified') ?></strong></div>
      <div><span>File size</span><strong><?= e(format_bytes((int)$game['file_size'])) ?></strong></div>
      <div><span>Downloads</span><strong><?= number_format((int)$game['downloads']) ?></strong></div>
    </div>
    <a class="button" href="download.php?id=<?= (int)$game['id'] ?>">Download game</a>
    <p class="tiny muted">Download only if you trust the source. Scan downloaded files before opening them.</p>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
