<?php
/** Shared helpers: JSON storage with file locking, responses, auth, uploads. */

// mbstring fallbacks (in case extension=mbstring is disabled in php.ini on Windows)
if (!function_exists('mb_strlen')) { function mb_strlen($s) { return strlen($s); } }
if (!function_exists('mb_substr')) { function mb_substr($s, $a, $b = null) { return $b === null ? substr($s, $a) : substr($s, $a, $b); } }
if (!function_exists('mb_strtolower')) { function mb_strtolower($s) { return strtolower($s); } }

define('DATA_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data');
define('UPLOAD_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads');

function json_out($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
function fail($msg, $status = 400) { json_out(array('ok' => false, 'error' => $msg), $status); }

function body_json() {
    static $cache = null;
    if ($cache !== null) return $cache;
    $raw = file_get_contents('php://input');
    $d = json_decode($raw, true);
    $cache = is_array($d) ? $d : array();
    return $cache;
}
function in_str($arr, $key, $default = '') { return isset($arr[$key]) && is_scalar($arr[$key]) ? trim((string) $arr[$key]) : $default; }

function data_path($name) { return DATA_DIR . DIRECTORY_SEPARATOR . preg_replace('/[^a-z0-9_\-]/i', '', $name) . '.json'; }

function load_json($name, $default = array()) {
    $p = data_path($name);
    if (!is_file($p)) return $default;
    $raw = @file_get_contents($p);
    if ($raw === false || $raw === '') return $default;
    $d = json_decode($raw, true);
    return $d === null ? $default : $d;
}
function save_json($name, $data) {
    if (!is_dir(DATA_DIR)) @mkdir(DATA_DIR, 0775, true);
    $p = data_path($name);
    $tmp = $p . '.tmp';
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    $fh = @fopen($tmp, 'wb');
    if (!$fh) fail('Cannot write data file (check folder permissions for /data).', 500);
    if (flock($fh, LOCK_EX)) { fwrite($fh, $json); fflush($fh); flock($fh, LOCK_UN); }
    fclose($fh);
    if (!@rename($tmp, $p)) { @copy($tmp, $p); @unlink($tmp); }
    return true;
}

function new_id($prefix = '') { return $prefix . substr(str_replace('.', '', uniqid('', true)), -10) . substr(md5(mt_rand()), 0, 4); }

function start_session() {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params(SESSION_LIFETIME, '/', '', $secure, true);
    session_name('MHSESS');
    session_start();
}
function is_admin() { start_session(); return !empty($_SESSION['admin']); }
function require_admin() { if (!is_admin()) fail('Unauthorized', 401); }

function csrf_token() { start_session(); if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = md5(uniqid(mt_rand(), true) . microtime(true)); return $_SESSION['csrf']; }
function check_csrf() {
    $hdr = isset($_SERVER['HTTP_X_CSRF']) ? $_SERVER['HTTP_X_CSRF'] : '';
    if ($hdr === '' || $hdr !== csrf_token()) fail('Invalid CSRF token', 403);
}

function sanitize_html($html) {
    // Whitelist-based cleanup for rich text saved from the admin editor.
    $html = preg_replace('#<(script|style|iframe|object|embed)[^>]*>.*?</\1>#is', '', $html);
    $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1="#"', $html);
    return $html;
}

function public_url($path) { return str_replace(DIRECTORY_SEPARATOR, '/', $path); }

function rate_limit($bucket, $max, $window) {
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'x';
    $all = load_json('_ratelimit', array());
    $now = time();
    $key = $bucket . ':' . $ip;
    $hits = isset($all[$key]) ? array_values(array_filter($all[$key], function ($t) use ($now, $window) { return $t > $now - $window; })) : array();
    if (count($hits) >= $max) fail('Too many requests. Please try again later.', 429);
    $hits[] = $now;
    $all[$key] = $hits;
    // prune old buckets
    foreach ($all as $k => $v) { if (empty($v) || max($v) < $now - 86400) unset($all[$k]); }
    save_json('_ratelimit', $all);
}

function send_mail($to, $subject, $body) {
    if ($to === '') return false;
    $headers = "From: " . MAIL_FROM . "\r\nReply-To: " . MAIL_FROM . "\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
}
