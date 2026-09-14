<?php
/**
 * Mohusyn.ir — Front controller (English-only site)
 * Routes:  /            -> landing
 *          /about/      -> about me
 *          /work/       -> portfolio archive (all projects)
 *          /work/slug   -> case study
 *          /blog/       -> blog list
 *          /blog/slug   -> single post
 * Legacy bilingual URLs (/fa/... /en/...) redirect to the plain path.
 */

require __DIR__ . '/config.php';
require __DIR__ . '/app/helpers.php';
require __DIR__ . '/app/i18n.php';
require __DIR__ . '/app/seo.php';
require __DIR__ . '/app/richtext.php';

$locale = 'en'; // the public site is English-only

$path = rawurldecode(current_request_path());
$path = rtrim($path, '/');
$segments = ($path === '') ? array() : explode('/', ltrim($path, '/'));

/* Legacy /fa/... and /en/... URLs -> redirect to the plain path */
if (count($segments) > 0 && ($segments[0] === 'fa' || $segments[0] === 'en')) {
    $rest = array_slice($segments, 1);
    $target = '/' . ltrim(implode('/', $rest), '/');
    if ($target === '/') {
        header('Location: /', true, 301);
    } else {
        header('Location: ' . $target . '/', true, 301);
    }
    exit;
}

$view = 'home';
$post = null;
$project = null;

/* ------------------------------------------- contact form submissions */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $hp = isset($_POST['contact_hp']) ? trim((string) $_POST['contact_hp']) : '';
    if ($hp === '') { /* honeypot empty -> real visitor */
        $cName = isset($_POST['contact_name']) ? trim((string) $_POST['contact_name']) : '';
        $cEmail = isset($_POST['contact_email']) ? trim((string) $_POST['contact_email']) : '';
        $cTopic = isset($_POST['contact_topic']) ? trim((string) $_POST['contact_topic']) : '';
        $cMessage = isset($_POST['contact_message']) ? trim((string) $_POST['contact_message']) : '';
        if (function_exists('mb_substr')) {
            $cName = mb_substr($cName, 0, 120);
            $cEmail = mb_substr($cEmail, 0, 160);
            $cTopic = mb_substr($cTopic, 0, 60);
            $cMessage = mb_substr($cMessage, 0, 4000);
        }
        if ($cName !== '' && $cEmail !== '' && $cMessage !== '') {
            $messages = load_json('messages.json', array());
            $entry = array(
                'id' => 'msg-' . substr(md5(uniqid('', true)), 0, 10),
                'name' => $cName,
                'email' => $cEmail,
                'topic' => $cTopic,
                'message' => $cMessage,
                'createdAt' => date('Y-m-d H:i'),
                'read' => false,
            );
            /* optional attachment (uploaded beforehand via contact-upload.php) */
            $tok = isset($_POST['contact_attachment']) ? preg_replace('/[^a-f0-9]/', '', (string) $_POST['contact_attachment']) : '';
            if ($tok !== '') {
                $pending = load_json('attachments.json', array());
                if (isset($pending[$tok]) && is_array($pending[$tok])) {
                    $entry['attachment'] = array(
                        'url' => '/uploads/attachments/' . $pending[$tok]['file'],
                        'name' => $pending[$tok]['name'],
                        'size' => isset($pending[$tok]['size']) ? (int) $pending[$tok]['size'] : 0,
                    );
                    $pending[$tok]['used'] = true;
                    save_json('attachments.json', $pending);
                }
            }
            array_unshift($messages, $entry);
            save_json('messages.json', array_values($messages));
        }
    }
    header('Location: /contact/?sent=1');
    exit;
}

/* sitemap.xml / robots.txt are generated (the physical files don't exist) */
if (count($segments) === 1 && $segments[0] === 'sitemap.xml') {
    seo_sitemap();
    exit;
}
if (count($segments) === 1 && $segments[0] === 'robots.txt') {
    seo_robots();
    exit;
}

if (count($segments) === 0) {
    $view = 'home';
} elseif ($segments[0] === 'about' && count($segments) === 1) {
    $view = 'about';
} elseif ($segments[0] === 'contact' && count($segments) === 1) {
    $view = 'contact';
} elseif ($segments[0] === 'work' && count($segments) === 1) {
    $view = 'portfolio';
} elseif ($segments[0] === 'work' && count($segments) === 2) {
    $view = 'work';
    $slug = $segments[1];
    foreach (published_items('projects') as $p) {
        if (isset($p['slug']) && $p['slug'] === $slug) {
            $project = $p;
            break;
        }
    }
    if ($project === null) {
        $view = '404';
        http_response_code(404);
    }
} elseif ($segments[0] === 'blog' && count($segments) === 1) {
    $view = 'blog';
} elseif ($segments[0] === 'blog' && count($segments) === 2) {
    $view = 'post';
    $slug = $segments[1];
    foreach (published_items('posts') as $p) {
        if (isset($p['slug']) && $p['slug'] === $slug) {
            $post = $p;
            break;
        }
    }
    if ($post === null) {
        $view = '404';
        http_response_code(404);
    }
} else {
    $view = '404';
    http_response_code(404);
}

require __DIR__ . '/app/views/blocks.php';
builder_mode(); /* resolve (and start the admin session if needed) before any output */

include __DIR__ . '/app/views/layout_top.php';
include __DIR__ . '/app/views/' . $view . '.php';
include __DIR__ . '/app/views/layout_bottom.php';
