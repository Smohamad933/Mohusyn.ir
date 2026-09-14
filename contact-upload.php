<?php
/**
 * Public endpoint — attachment upload for the contact form.
 * Accepts one file (max CONTACT_MAX_UPLOAD, see config.php), stores it under
 * uploads/attachments/ with a random name and returns a token the form submits.
 * The token is resolved back to the file when the message is saved (index.php).
 */

require __DIR__ . '/config.php';
require __DIR__ . '/app/helpers.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function contact_upload_fail($error, $code = 400)
{
    http_response_code($code);
    echo json_encode(array('ok' => false, 'error' => $error));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    contact_upload_fail('method', 405);
}

/* Same-origin guard (no cookies/CSRF for anonymous visitors, so check the origin header) */
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
if ($origin !== '' && $host !== '') {
    $oh = parse_url($origin, PHP_URL_HOST);
    if ($oh !== null && $oh !== false && strcasecmp($oh, preg_replace('/:\d+$/', '', $host)) !== 0) {
        contact_upload_fail('origin', 403);
    }
}

if (!isset($_FILES['file']) || !is_array($_FILES['file'])) {
    contact_upload_fail('nofile');
}
$f = $_FILES['file'];
if (!empty($f['error'])) {
    contact_upload_fail($f['error'] === UPLOAD_ERR_INI_SIZE || $f['error'] === UPLOAD_ERR_FORM_SIZE ? 'size' : 'upload');
}
$max = defined('CONTACT_MAX_UPLOAD') ? CONTACT_MAX_UPLOAD : 20 * 1024 * 1024;
if ($f['size'] <= 0 || $f['size'] > $max) {
    contact_upload_fail('size');
}

$allowed = defined('CONTACT_ALLOWED_EXTENSIONS') ? CONTACT_ALLOWED_EXTENSIONS
    : array('jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'zip', 'rar', '7z', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'mp4', 'mov', 'mp3', 'ai', 'psd', 'fig', 'sketch');
$origName = sanitize_filename($f['name']);
$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
if ($ext === '' || !in_array($ext, $allowed, true)) {
    contact_upload_fail('type');
}
/* never allow anything executable to land in the web root */
if (in_array($ext, array('php', 'phtml', 'php3', 'php5', 'phar', 'asp', 'aspx', 'ashx', 'exe', 'bat', 'cmd', 'js', 'html', 'htm', 'svg', 'config'), true)) {
    contact_upload_fail('type');
}

$dir = uploads_dir() . DIRECTORY_SEPARATOR . 'attachments';
if (!is_dir($dir) && !@mkdir($dir, 0775, true)) {
    contact_upload_fail('dir', 500);
}
/* keep the folder non-listable / non-executable on IIS */
$wc = $dir . DIRECTORY_SEPARATOR . 'web.config';
if (!is_file($wc)) {
    @file_put_contents($wc, "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\n<configuration>\n  <system.webServer>\n    <handlers accessPolicy=\"Read\" />\n    <directoryBrowse enabled=\"false\" />\n  </system.webServer>\n</configuration>\n");
}

$token = bin2hex(random_bytes(12));
$stored = $token . '.' . $ext;
if (!move_uploaded_file($f['tmp_name'], $dir . DIRECTORY_SEPARATOR . $stored)) {
    contact_upload_fail('move', 500);
}

/* remember original name + size for the admin inbox */
$pending = load_json('attachments.json', array());
$pending[$token] = array(
    'file' => $stored,
    'name' => $origName,
    'size' => (int) $f['size'],
    'createdAt' => date('Y-m-d H:i'),
);
/* prune stale (never-submitted) entries older than a day */
foreach ($pending as $k => $v) {
    if (isset($v['createdAt']) && strtotime($v['createdAt']) < time() - 86400 && empty($v['used'])) {
        @unlink($dir . DIRECTORY_SEPARATOR . $v['file']);
        unset($pending[$k]);
    }
}
save_json('attachments.json', $pending);

echo json_encode(array('ok' => true, 'token' => $token, 'name' => $origName, 'size' => (int) $f['size']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
