<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $pdo->prepare('SELECT * FROM games WHERE title LIKE ? OR description LIKE ? ORDER BY created_at DESC');
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query('SELECT * FROM games ORDER BY created_at DESC');
}
$games = $stmt->fetchAll();
$pageTitle = 'Browse games';
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <p class="eyebrow">YOUR GAME LIBRARY</p>
  <h1>Discover your next <span>game.</span></h1>
  <p>Browse games shared by our community.</p>
  <form class="search-form" method="get">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search games..." aria-label="Search games">
    <button type="submit">Search</button>
  </form>
</section>

<div class="section-heading">
  <div><p class="eyebrow">LIBRARY</p><h2><?= $q !== '' ? 'Search results' : 'Latest uploads' ?></h2></div>
  <span class="muted"><?= count($games) ?> game(s)</span>
</div>

<?php if (!$games): ?>
  <div class="empty-state"><h3>No games found</h3><p>Try another search, or check back later.</p></div>
<?php else: ?>
  <section class="game-grid">
    <?php foreach ($games as $game): ?>
      <article class="game-card">
        <a class="cover" href="game.php?id=<?= (int)$game['id'] ?>">
          <?php if (!empty($game['cover_url'])): ?>
            <img src="<?= e($game['cover_url']) ?>" alt="<?= e($game['title']) ?> cover" loading="lazy">
          <?php else: ?><div class="cover-placeholder">GAMEHUB</div><?php endif; ?>
        </a>
        <div class="game-card-body">
          <p class="eyebrow"><?= e($game['category'] ?: 'Game') ?></p>
          <h3><a href="game.php?id=<?= (int)$game['id'] ?>"><?= e($game['title']) ?></a></h3>
          <p class="muted clamp"><?= e($game['description']) ?></p>
          <div class="card-meta"><span><?= e($game['version'] ?: 'Version not set') ?></span><span><?= number_format((int)$game['downloads']) ?> downloads</span></div>
          <a class="text-link" href="game.php?id=<?= (int)$game['id'] ?>">View game →</a>
        </div>
      </article>
    <?php endforeach; ?>
  </section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
