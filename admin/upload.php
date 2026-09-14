<?php
/**
 * Admin — AJAX upload endpoint (images + font files).
 * POST: file (single file), kind = "image" | "font", _csrf
 * Response: {"ok":true,"url":"/uploads/..."} or {"ok":false,"error":"..."}
 */

require __DIR__ . '/_bootstrap.php';
require_auth();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array('ok' => false, 'error' => 'method'));
    exit;
}

$token = isset($_POST['_csrf']) ? (string) $_POST['_csrf'] : '';
ensure_session();
$known = isset($_SESSION['csrf']) ? (string) $_SESSION['csrf'] : '';
if ($token === '' || !hash_equals($known, $token)) {
    echo json_encode(array('ok' => false, 'error' => 'csrf'));
    exit;
}

$kind = isset($_POST['kind']) && $_POST['kind'] === 'font' ? 'font' : 'image';
$allowed = ($kind === 'font')
    ? array('woff', 'woff2', 'ttf', 'otf')
    : ALLOWED_EXTENSIONS;

if (!isset($_FILES['file']) || !isset($_FILES['file']['error'])) {
    echo json_encode(array('ok' => false, 'error' => 'upload'));
    exit;
}
if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(array('ok' => false, 'error' => 'upload'));
    exit;
}
if ($_FILES['file']['size'] > MAX_UPLOAD_SIZE) {
    echo json_encode(array('ok' => false, 'error' => 'size'));
    exit;
}

$name = sanitize_filename($_FILES['file']['name']);
$dot = strrpos($name, '.');
$ext = $dot !== false ? strtolower(substr($name, $dot + 1)) : '';
if (!in_array($ext, $allowed, true)) {
    echo json_encode(array('ok' => false, 'error' => 'type'));
    exit;
}

$base = $dot !== false ? substr($name, 0, $dot) : $name;
$final = $base . '-' . date('Ymd-His') . '-' . substr(md5(uniqid('', true)), 0, 4) . '.' . $ext;

$dir = ($kind === 'font') ? uploads_dir() . DIRECTORY_SEPARATOR . 'fonts' : uploads_dir();
if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}

$target = $dir . DIRECTORY_SEPARATOR . $final;
if (!move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
    echo json_encode(array('ok' => false, 'error' => 'move'));
    exit;
}

$urlBase = ($kind === 'font') ? '/uploads/fonts/' : '/uploads/';
$url = $urlBase . $final;

/* Register images in the media library so they appear in pickers */
if ($kind === 'image') {
    $media = media_list();
    $media[] = array(
        'file' => $final,
        'url' => $url,
        'uploadedAt' => date('Y-m-d H:i'),
    );
    save_json('media.json', $media);
}

echo json_encode(array('ok' => true, 'url' => $url));
