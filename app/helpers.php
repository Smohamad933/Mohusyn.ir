<?php
/**
 * Mohusyn.ir — Core helpers (PHP 7.4+ compatible, IIS safe)
 */

if (!defined('ROOT_PATH')) {
    require dirname(__DIR__) . '/config.php';
}
require_once __DIR__ . '/db.php';

/* ------------------------------------------------------------------ data */

function data_path($file)
{
    return ROOT_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $file;
}

/**
 * Load a content document. Primary source: the database.
 * Falls back to the JSON seed file when the DB is unavailable
 * or the document doesn't exist yet.
 */
function load_json($file, $fallback = array())
{
    $raw = db_get_doc($file);
    if ($raw !== null) {
        $data = json_decode($raw, true);
        if (is_array($data)) {
            return $data;
        }
    }

    $path = data_path($file);
    if (!is_file($path)) {
        return $fallback;
    }
    $raw = file_get_contents($path);
    if ($raw === false || $raw === '') {
        return $fallback;
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : $fallback;
}

/**
 * Save a content document to the database (with JSON file fallback).
 */
function save_json($file, $data)
{
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if ($json === false) {
        return false;
    }

    if (db_set_doc($file, $json)) {
        return true;
    }

    /* Database unavailable -> write the JSON file directly */
    $path = data_path($file);
    $fp = fopen($path, 'c');
    if ($fp === false) {
        return false;
    }
    $ok = false;
    if (flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        fwrite($fp, $json);
        fflush($fp);
        flock($fp, LOCK_UN);
        $ok = true;
    }
    fclose($fp);
    return $ok;
}

function get_settings()
{
    static $settings = null;
    if ($settings === null) {
        $settings = load_json('settings.json', array());
    }
    return $settings;
}

function get_collection($name)
{
    return load_json($name . '.json', array());
}

/** Items that are published (or all when $includeDrafts). */
function published_items($name, $includeDrafts = false)
{
    $items = get_collection($name);
    if ($includeDrafts) {
        return $items;
    }
    $out = array();
    foreach ($items as $item) {
        if (!empty($item['published'])) {
            $out[] = $item;
        }
    }
    return $out;
}

/* ------------------------------------------------------------------ i18n */

/** Pick the right language value out of a bilingual field. */
function bi($field, $locale)
{
    if (is_string($field)) {
        return $field;
    }
    if (is_array($field)) {
        if (isset($field[$locale]) && $field[$locale] !== '') {
            return $field[$locale];
        }
        foreach (array('en', 'fa') as $fallback) {
            if (isset($field[$fallback]) && $field[$fallback] !== '') {
                return $field[$fallback];
            }
        }
    }
    return '';
}

/** Shorthand: bilingual field from settings. */
function setting_bi($key, $locale, $section = null)
{
    $settings = get_settings();
    if ($section !== null) {
        return isset($settings['sections'][$section]) ? bi($settings['sections'][$section], $locale) : '';
    }
    return isset($settings[$key]) ? bi($settings[$key], $locale) : '';
}

function setting($key, $default = '')
{
    $settings = get_settings();
    return isset($settings[$key]) ? $settings[$key] : $default;
}

/** Page-level settings (titles, subtitles, section toggles). */
function page_setting($page, $key, $locale = null)
{
    $settings = get_settings();
    if (!isset($settings['pages'][$page][$key])) {
        return '';
    }
    $value = $settings['pages'][$page][$key];
    if ($locale !== null && is_array($value)) {
        return bi($value, $locale);
    }
    return $value;
}

function page_shows($page, $key)
{
    $value = page_setting($page, $key);
    return !empty($value);
}

function t($key, $locale)
{
    global $UI_STRINGS;
    if (isset($UI_STRINGS[$key][$locale])) {
        return $UI_STRINGS[$key][$locale];
    }
    return $key;
}

/* ---------------------------------------------------------------- output */

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function fa_digits($value)
{
    return strtr((string) $value, array(
        '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
        '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
    ));
}

/* ---------------------------------------------------------------- dates */

/** Convert Gregorian to Jalali. Returns array($jy, $jm, $jd). */
function gregorian_to_jalali($gy, $gm, $gd)
{
    $gdm = array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $days = 355666 + (365 * $gy) + ((int) (($gy2 + 3) / 4)) - ((int) (($gy2 + 99) / 100))
        + ((int) (($gy2 + 399) / 400)) + $gd + $gdm[$gm - 1];
    $jy = -1595 + (33 * ((int) ($days / 12053)));
    $days %= 12053;
    $jy += 4 * ((int) ($days / 1461));
    $days %= 1461;
    if ($days > 365) {
        $jy += (int) (($days - 1) / 365);
        $days = ($days - 1) % 365;
    }
    if ($days < 186) {
        $jm = 1 + (int) ($days / 31);
        $jd = 1 + ($days % 31);
    } else {
        $jm = 7 + (int) (($days - 186) / 30);
        $jd = 1 + (($days - 186) % 30);
    }
    return array($jy, $jm, $jd);
}

function jalali_month_name($jm)
{
    $names = array(
        1 => 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
        'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند',
    );
    return isset($names[$jm]) ? $names[$jm] : '';
}

function weekday_name_fa($ts)
{
    $names = array(
        'Saturday' => 'شنبه', 'Sunday' => 'یکشنبه', 'Monday' => 'دوشنبه',
        'Tuesday' => 'سه‌شنبه', 'Wednesday' => 'چهارشنبه',
        'Thursday' => 'پنجشنبه', 'Friday' => 'جمعه',
    );
    $en = date('l', $ts);
    return isset($names[$en]) ? $names[$en] : $en;
}

/** Localized date string like "یکشنبه ۲۲ شهریور ۱۴۰۵" or "Sunday, September 13". */
function localized_date($dateString, $locale, $withWeekday = true)
{
    $ts = strtotime($dateString);
    if ($ts === false) {
        $ts = time();
    }
    if ($locale === 'fa') {
        list($jy, $jm, $jd) = gregorian_to_jalali((int) date('Y', $ts), (int) date('n', $ts), (int) date('j', $ts));
        $out = fa_digits($jd) . ' ' . jalali_month_name($jm) . ' ' . fa_digits($jy);
        if ($withWeekday) {
            $out = weekday_name_fa($ts) . '، ' . $out;
        }
        return $out;
    }
    return $withWeekday ? date('l, F j, Y', $ts) : date('F j, Y', $ts);
}

/* ----------------------------------------------------------------- misc */

function slugify($text)
{
    $text = strtolower(trim((string) $text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text === '' ? 'item-' . substr(md5(uniqid('', true)), 0, 6) : $text;
}

function new_id($prefix)
{
    return $prefix . '-' . substr(md5(uniqid('', true)), 0, 10);
}

/**
 * Build a site URL. The site is English-only now, so the locale
 * argument is kept for backward compatibility and ignored.
 */
function locale_path($locale, $sub = '')
{
    return '/' . ltrim($sub, '/');
}

function current_request_path()
{
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    $path = parse_url($uri, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        $path = '/';
    }
    return $path;
}

/* ----------------------------------------------------- sessions and auth */

function ensure_session()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name('mohusyn_admin');
        if (PHP_VERSION_ID >= 70300) {
            session_set_cookie_params(array(
                'lifetime' => 0,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ));
        } else {
            session_set_cookie_params(0, '/; samesite=Lax', '', false, true);
        }
        session_start();
    }
}

function csrf_token()
{
    ensure_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field()
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_ok()
{
    ensure_session();
    $sent = isset($_POST['_csrf']) ? (string) $_POST['_csrf'] : '';
    $known = isset($_SESSION['csrf']) ? (string) $_SESSION['csrf'] : '';
    return $sent !== '' && hash_equals($known, $sent);
}

function is_authed()
{
    ensure_session();
    return !empty($_SESSION['mohusyn_auth']);
}

function require_auth()
{
    if (!is_authed()) {
        header('Location: login.php');
        exit;
    }
}

/* --------------------------------------------------------------- uploads */

function uploads_dir()
{
    return ROOT_PATH . DIRECTORY_SEPARATOR . 'uploads';
}

function media_list()
{
    return load_json('media.json', array());
}

function sanitize_filename($name)
{
    $name = basename((string) $name);
    $name = preg_replace('/[^A-Za-z0-9._-]/', '-', $name);
    $name = preg_replace('/-{2,}/', '-', $name);
    return $name === '' || $name === '.' ? 'file' : $name;
}

/* ------------------------------------------------------ asset versioning */

/** Append the file's mtime so browsers/IIS never serve a stale CSS/JS after an update. */
function asset($path)
{
    $file = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($path, '/'));
    $v = is_file($file) ? (string) filemtime($file) : '1';
    return $path . '?v=' . $v;
}
