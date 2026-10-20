<?php
/**
 * Single-file JSON API for the portfolio.
 * Routes: /api/index.php?r=<route>
 *
 * Public:  content, contact, feedback, vault, signup, login, logout, me, profile, track
 * Admin:   admin/login, admin/logout, admin/me, admin/settings, admin/collection, admin/item,
 *          admin/reorder, admin/upload, admin/media, admin/messages, admin/users, admin/stats, admin/password
 */
require __DIR__ . '/config.php';
require __DIR__ . '/lib.php';

$route = isset($_GET['r']) ? $_GET['r'] : '';
$method = $_SERVER['REQUEST_METHOD'];
$body = body_json();

/* Collections that exist. Public ones are returned by `content`; testimonials/messages are filtered. */
$COLLECTIONS = array('projects', 'resources', 'feed', 'timeline', 'testimonials', 'gallery', 'vault', 'pages');

function content_payload($forAdmin = false) {
    global $COLLECTIONS;
    $out = array('settings' => load_json('settings', new stdClass()));
    foreach ($COLLECTIONS as $c) {
        $items = load_json($c, array());
        if (!is_array($items)) $items = array();
        if (!$forAdmin) {
            if ($c === 'vault') { continue; } // never expose vault items publicly
            $items = array_values(array_filter($items, function ($it) use ($c) {
                if ($c === 'testimonials') return !isset($it['approved']) || $it['approved'];
                return !isset($it['published']) || $it['published'];
            }));
        }
        $out[$c] = $items;
    }
    if (!$forAdmin && is_array($out['settings']) && isset($out['settings']['vaultPassword'])) unset($out['settings']['vaultPassword']);
    return $out;
}

switch ($route) {

    /* ───────────── PUBLIC ───────────── */
    case 'content':
        json_out(array('ok' => true, 'content' => content_payload(false), 'csrf' => csrf_token()));

    case 'contact':
        if ($method !== 'POST') fail('POST only', 405);
        rate_limit('contact', 5, 600);
        $name = in_str($body, 'name'); $email = in_str($body, 'email'); $subject = in_str($body, 'subject'); $message = in_str($body, 'message');
        if ($name === '' || $message === '') fail('Name and message are required.');
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Please enter a valid email address.');
        if (mb_strlen($message) > 5000) fail('Message is too long.');
        $msgs = load_json('messages', array());
        $item = array('id' => new_id('m'), 'name' => mb_substr($name, 0, 120), 'email' => $email, 'subject' => mb_substr($subject, 0, 200), 'message' => $message,
            'createdAt' => date('c'), 'read' => false, 'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        array_unshift($msgs, $item);
        save_json('messages', $msgs);
        if (NOTIFY_EMAIL !== '') send_mail(NOTIFY_EMAIL, 'New message from ' . $name, "Name: $name\nEmail: $email\nSubject: $subject\n\n$message");
        json_out(array('ok' => true));

    case 'feedback':
        if ($method !== 'POST') fail('POST only', 405);
        rate_limit('feedback', 3, 3600);
        $name = in_str($body, 'name'); $rating = (int) in_str($body, 'rating', '5'); $text = in_str($body, 'text');
        if ($name === '') fail('Name is required.');
        $items = load_json('testimonials', array());
        array_unshift($items, array('id' => new_id('c'), 'name' => mb_substr($name, 0, 80), 'role' => 'Visitor', 'text' => mb_substr($text, 0, 500), 'avatar' => '',
            'rating' => max(1, min(5, $rating)), 'verified' => false, 'createdAt' => date('c'), 'approved' => false));
        save_json('testimonials', $items);
        json_out(array('ok' => true, 'pending' => true));

    case 'vault':
        if ($method !== 'POST') fail('POST only', 405);
        rate_limit('vault', 30, 600);
        $code = mb_strtolower(in_str($body, 'code'));
        $items = load_json('vault', array());
        $found = array_values(array_filter($items, function ($it) use ($code) {
            return isset($it['secretCode']) && mb_strtolower(trim($it['secretCode'])) === $code && (!isset($it['published']) || $it['published']);
        }));
        json_out(array('ok' => true, 'items' => $found));

    case 'track':
        // Lightweight analytics: counts per day (views, clicks by key)
        if ($method !== 'POST') fail('POST only', 405);
        $kind = in_str($body, 'kind', 'view'); $key = mb_substr(in_str($body, 'key', ''), 0, 60);
        $day = date('Y-m-d');
        $st = load_json('stats', array());
        if (!isset($st[$day])) $st[$day] = array('views' => 0, 'clicks' => array());
        if ($kind === 'view') $st[$day]['views']++;
        elseif ($kind === 'click' && $key !== '') $st[$day]['clicks'][$key] = (isset($st[$day]['clicks'][$key]) ? $st[$day]['clicks'][$key] : 0) + 1;
        // keep 120 days
        krsort($st); $st = array_slice($st, 0, 120, true); ksort($st);
        save_json('stats', $st);
        json_out(array('ok' => true));

    /* ───────────── VISITOR ACCOUNTS ───────────── */
    case 'signup':
        if ($method !== 'POST') fail('POST only', 405);
        rate_limit('signup', 5, 3600);
        $email = mb_strtolower(in_str($body, 'email')); $pass = in_str($body, 'password'); $name = in_str($body, 'name');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Please enter a valid email.');
        if (strlen($pass) < 6) fail('Password must be at least 6 characters.');
        $users = load_json('users', array());
        foreach ($users as $u) if ($u['email'] === $email) fail('An account with this email already exists.');
        $u = array('id' => new_id('u'), 'email' => $email, 'displayName' => $name !== '' ? mb_substr($name, 0, 60) : explode('@', $email)[0],
            'photoURL' => '', 'bio' => '', 'hash' => password_hash($pass, PASSWORD_DEFAULT), 'createdAt' => date('c'));
        $users[] = $u; save_json('users', $users);
        start_session(); $_SESSION['uid'] = $u['id'];
        unset($u['hash']);
        json_out(array('ok' => true, 'user' => $u));

    case 'login':
        if ($method !== 'POST') fail('POST only', 405);
        rate_limit('login', 10, 600);
        $email = mb_strtolower(in_str($body, 'email')); $pass = in_str($body, 'password');
        foreach (load_json('users', array()) as $u) {
            if ($u['email'] === $email && password_verify($pass, $u['hash'])) {
                start_session(); session_regenerate_id(true); $_SESSION['uid'] = $u['id'];
                unset($u['hash']); json_out(array('ok' => true, 'user' => $u));
            }
        }
        fail('Invalid email or password.', 401);

    case 'logout':
        start_session(); unset($_SESSION['uid']); json_out(array('ok' => true, 'user' => null));

    case 'me':
        start_session();
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        if ($uid) foreach (load_json('users', array()) as $u) if ($u['id'] === $uid) { unset($u['hash']); json_out(array('ok' => true, 'user' => $u)); }
        json_out(array('ok' => true, 'user' => null));

    case 'profile':
        if ($method !== 'POST') fail('POST only', 405);
        start_session();
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        if (!$uid) fail('Not signed in', 401);
        $users = load_json('users', array());
        foreach ($users as $i => $u) {
            if ($u['id'] === $uid) {
                if (isset($body['displayName'])) $users[$i]['displayName'] = mb_substr(trim((string) $body['displayName']), 0, 60);
                if (isset($body['photoURL'])) $users[$i]['photoURL'] = mb_substr(trim((string) $body['photoURL']), 0, 500);
                if (isset($body['bio'])) $users[$i]['bio'] = mb_substr(trim((string) $body['bio']), 0, 300);
                if (!empty($body['password']) && strlen($body['password']) >= 6) $users[$i]['hash'] = password_hash($body['password'], PASSWORD_DEFAULT);
                save_json('users', $users);
                $u = $users[$i]; unset($u['hash']);
                json_out(array('ok' => true, 'user' => $u));
            }
        }
        fail('User not found', 404);

    /* ───────────── ADMIN ───────────── */
    case 'admin/login':
        if ($method !== 'POST') fail('POST only', 405);
        rate_limit('admin-login', 8, 900);
        $pass = in_str($body, 'password');
        $stored = load_json('_admin', array());
        $ok = isset($stored['hash']) ? password_verify($pass, $stored['hash']) : ($pass === ADMIN_PASSWORD);
        if (!$ok) fail('رمز عبور اشتباه است.', 401);
        start_session(); session_regenerate_id(true); $_SESSION['admin'] = true;
        json_out(array('ok' => true, 'csrf' => csrf_token()));

    case 'admin/logout':
        start_session(); unset($_SESSION['admin']); json_out(array('ok' => true));

    case 'admin/me':
        start_session();
        json_out(array('ok' => true, 'admin' => is_admin(), 'csrf' => csrf_token()));

    case 'admin/password':
        require_admin(); check_csrf();
        $new = in_str($body, 'password');
        if (strlen($new) < 6) fail('رمز باید حداقل ۶ کاراکتر باشد.');
        save_json('_admin', array('hash' => password_hash($new, PASSWORD_DEFAULT), 'updatedAt' => date('c')));
        json_out(array('ok' => true));

    case 'admin/content':
        require_admin();
        json_out(array('ok' => true, 'content' => content_payload(true), 'csrf' => csrf_token()));

    case 'admin/settings':
        require_admin(); check_csrf();
        if (!isset($body['settings']) || !is_array($body['settings'])) fail('settings required');
        $s = $body['settings'];
        // sanitize rich-text-ish fields lightly
        save_json('settings', $s);
        json_out(array('ok' => true));

    case 'admin/collection':
        // Replace whole collection (used for reorder / bulk save)
        require_admin(); check_csrf();
        $name = in_str($body, 'name');
        if (!in_array($name, $COLLECTIONS, true)) fail('Unknown collection');
        $items = isset($body['items']) && is_array($body['items']) ? array_values($body['items']) : array();
        foreach ($items as $i => $it) {
            if (empty($it['id'])) $items[$i]['id'] = new_id(substr($name, 0, 1));
            if ($name === 'pages' && isset($it['body'])) $items[$i]['body'] = sanitize_html($it['body']);
        }
        save_json($name, $items);
        json_out(array('ok' => true, 'items' => $items));

    case 'admin/item':
        // Upsert or delete a single item: { name, item } or { name, id, delete: true }
        require_admin(); check_csrf();
        $name = in_str($body, 'name');
        if (!in_array($name, $COLLECTIONS, true)) fail('Unknown collection');
        $items = load_json($name, array());
        if (!empty($body['delete'])) {
            $id = in_str($body, 'id');
            $items = array_values(array_filter($items, function ($it) use ($id) { return $it['id'] !== $id; }));
            save_json($name, $items);
            json_out(array('ok' => true, 'items' => $items));
        }
        $item = isset($body['item']) && is_array($body['item']) ? $body['item'] : null;
        if (!$item) fail('item required');
        if ($name === 'pages' && isset($item['body'])) $item['body'] = sanitize_html($item['body']);
        if (empty($item['id'])) { $item['id'] = new_id(substr($name, 0, 1)); $items[] = $item; }
        else {
            $found = false;
            foreach ($items as $i => $it) if ($it['id'] === $item['id']) { $items[$i] = $item; $found = true; break; }
            if (!$found) $items[] = $item;
        }
        save_json($name, $items);
        json_out(array('ok' => true, 'items' => $items, 'item' => $item));

    case 'admin/upload':
        require_admin(); check_csrf();
        if (!isset($_FILES['file'])) fail('No file');
        $f = $_FILES['file'];
        if ($f['error'] !== UPLOAD_ERR_OK) fail('Upload error code ' . $f['error']);
        if ($f['size'] > MAX_UPLOAD_SIZE) fail('File too large');
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, explode(',', ALLOWED_UPLOADS), true)) fail('File type not allowed: .' . $ext);
        $base = preg_replace('/[^a-z0-9_\-]/i', '-', pathinfo($f['name'], PATHINFO_FILENAME));
        $base = trim(substr($base, 0, 60), '-'); if ($base === '') $base = 'file';
        $sub = isset($_POST['folder']) ? preg_replace('/[^a-z0-9_\-]/i', '', $_POST['folder']) : '';
        $dir = UPLOAD_DIR . ($sub ? DIRECTORY_SEPARATOR . $sub : '');
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
        $name = $base . '-' . date('Ymd-His') . '.' . $ext;
        $target = $dir . DIRECTORY_SEPARATOR . $name;
        if (!move_uploaded_file($f['tmp_name'], $target)) fail('Could not save file (check /uploads permissions)', 500);
        $url = '/uploads/' . ($sub ? $sub . '/' : '') . $name;
        json_out(array('ok' => true, 'url' => $url, 'name' => $name, 'size' => $f['size']));

    case 'admin/media':
        require_admin();
        if ($method === 'POST' && !empty($body['delete'])) {
            check_csrf();
            $rel = in_str($body, 'url');
            if (strpos($rel, '/uploads/') !== 0 || strpos($rel, '..') !== false) fail('Bad path');
            $p = dirname(__DIR__) . str_replace('/', DIRECTORY_SEPARATOR, $rel);
            if (is_file($p)) @unlink($p);
            json_out(array('ok' => true));
        }
        $files = array();
        $rii = is_dir(UPLOAD_DIR) ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator(UPLOAD_DIR, FilesystemIterator::SKIP_DOTS)) : array();
        foreach ($rii as $file) {
            if ($file->isDir()) continue;
            $rel = str_replace(DIRECTORY_SEPARATOR, '/', substr($file->getPathname(), strlen(UPLOAD_DIR)));
            if (basename($rel) === '.gitkeep' || basename($rel) === 'web.config') continue;
            $files[] = array('url' => '/uploads' . $rel, 'name' => basename($rel), 'size' => $file->getSize(), 'mtime' => $file->getMTime(),
                'image' => (bool) preg_match('/\.(jpe?g|png|gif|webp|svg|ico)$/i', $rel));
        }
        usort($files, function ($a, $b) { return $b['mtime'] - $a['mtime']; });
        json_out(array('ok' => true, 'files' => $files));

    case 'admin/messages':
        require_admin();
        $msgs = load_json('messages', array());
        if ($method === 'POST') {
            check_csrf();
            $id = in_str($body, 'id');
            if (!empty($body['delete'])) $msgs = array_values(array_filter($msgs, function ($m) use ($id) { return $m['id'] !== $id; }));
            elseif (isset($body['read'])) foreach ($msgs as $i => $m) if ($m['id'] === $id) $msgs[$i]['read'] = (bool) $body['read'];
            save_json('messages', $msgs);
        }
        json_out(array('ok' => true, 'messages' => $msgs));

    case 'admin/users':
        require_admin();
        $users = load_json('users', array());
        if ($method === 'POST' && !empty($body['delete'])) {
            check_csrf();
            $id = in_str($body, 'id');
            $users = array_values(array_filter($users, function ($u) use ($id) { return $u['id'] !== $id; }));
            save_json('users', $users);
        }
        $safe = array_map(function ($u) { unset($u['hash']); return $u; }, $users);
        json_out(array('ok' => true, 'users' => $safe));

    case 'admin/stats':
        require_admin();
        $st = load_json('stats', array());
        $msgs = load_json('messages', array());
        $unread = 0; foreach ($msgs as $m) if (empty($m['read'])) $unread++;
        $pending = 0; foreach (load_json('testimonials', array()) as $t) if (isset($t['approved']) && !$t['approved']) $pending++;
        if (!empty($body['reset']) && $method === 'POST') { check_csrf(); $st = array(); save_json('stats', $st); }
        json_out(array('ok' => true, 'stats' => $st, 'unread' => $unread, 'pendingTestimonials' => $pending, 'users' => count(load_json('users', array())),
            'php' => PHP_VERSION, 'writable' => is_writable(DATA_DIR) && (is_writable(UPLOAD_DIR) || !is_dir(UPLOAD_DIR))));

    case 'admin/seed':
        // Re-seed data files from the bundled defaults (settings + collections) — only when they do not exist yet, unless force=1
        require_admin(); check_csrf();
        $seed = isset($body['content']) && is_array($body['content']) ? $body['content'] : array();
        $force = !empty($body['force']);
        $written = array();
        foreach ($seed as $k => $v) {
            if ($k !== 'settings' && !in_array($k, $COLLECTIONS, true)) continue;
            if ($force || !is_file(data_path($k))) { save_json($k, $v); $written[] = $k; }
        }
        json_out(array('ok' => true, 'written' => $written));

    case 'health':
        json_out(array('ok' => true, 'php' => PHP_VERSION, 'dataWritable' => is_writable(DATA_DIR), 'uploadsWritable' => is_dir(UPLOAD_DIR) ? is_writable(UPLOAD_DIR) : null, 'time' => date('c')));

    default:
        fail('Unknown route: ' . $route, 404);
}
