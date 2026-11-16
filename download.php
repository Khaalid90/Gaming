<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT original_filename, stored_filename FROM games WHERE id = ?');
$stmt->execute([$id ?: 0]);
$game = $stmt->fetch();

if (!$game) { http_response_code(404); exit('Game not found.'); }

$path = STORAGE_DIR . DIRECTORY_SEPARATOR . basename($game['stored_filename']);
if (!is_file($path)) { http_response_code(404); exit('Game file is missing.'); }

$update = $pdo->prepare('UPDATE games SET downloads = downloads + 1 WHERE id = ?');
$update->execute([$id]);

$downloadName = preg_replace('/[^A-Za-z0-9._ -]/', '_', $game['original_filename']);
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize($path));
header('X-Content-Type-Options: nosniff');
readfile($path);
exit;
