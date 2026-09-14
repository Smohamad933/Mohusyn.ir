<?php
/**
 * Admin — JSON API for the visual builder (admin/blocks.php).
 *
 *  GET  ?page=home              -> { ok, blocks, draft, published }
 *  POST { action:"draft",   page, blocks }  -> save draft only (site unchanged)
 *  POST { action:"publish", page, blocks }  -> write to live blocks + clear draft
 *  POST { action:"discard", page }          -> drop the draft
 *
 * Drafts live in blocks_draft.json keyed by page; live blocks stay in blocks.json.
 */

require __DIR__ . '/_bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_authed()) {
    http_response_code(401);
    echo json_encode(array('ok' => false, 'error' => 'auth'));
    exit;
}

$pages = array('home', 'about');
$types = array('heading', 'text', 'image', 'button', 'spacer', 'divider');

function builder_clean_blocks($raw, $page, $types)
{
    $out = array();
    if (!is_array($raw)) {
        return $out;
    }
    foreach ($raw as $b) {
        if (!is_array($b)) {
            continue;
        }
        $type = isset($b['type']) && in_array($b['type'], $types, true) ? $b['type'] : 'text';
        $id = isset($b['id']) && preg_match('/^blk-[a-z0-9]{1,24}$/i', (string) $b['id']) ? (string) $b['id'] : new_id('blk');
        $d = isset($b['data']) && is_array($b['data']) ? $b['data'] : array();
        $clean = array();
        $str = function ($k, $max = 4000) use ($d) {
            $v = isset($d[$k]) && is_scalar($d[$k]) ? trim((string) $d[$k]) : '';
            return function_exists('mb_substr') ? mb_substr($v, 0, $max) : substr($v, 0, $max);
        };
        $align = isset($d['align']) && in_array($d['align'], array('left', 'center', 'right'), true) ? $d['align'] : null;
        switch ($type) {
            case 'heading':
                $clean = array('text' => $str('text', 300), 'level' => isset($d['level']) && in_array($d['level'], array('h2', 'h3', 'h4'), true) ? $d['level'] : 'h2');
                break;
            case 'text':
                $clean = array('text' => $str('text'));
                break;
            case 'image':
                $w = isset($d['width']) ? (int) $d['width'] : 0;
                $clean = array('src' => $str('src', 500), 'alt' => $str('alt', 200), 'width' => $w > 0 ? (string) min(1400, $w) : '', 'align' => $align ? $align : 'center');
                break;
            case 'button':
                $clean = array('label' => $str('label', 120), 'url' => $str('url', 500), 'style' => isset($d['style']) && $d['style'] === 'outline' ? 'outline' : 'solid', 'align' => $align ? $align : 'left');
                if ($clean['url'] === '') {
                    $clean['url'] = '/contact/';
                }
                break;
            case 'spacer':
                $clean = array('height' => (string) max(0, min(400, isset($d['height']) ? (int) $d['height'] : 40)));
                break;
            default:
                $clean = array();
        }
        $out[] = array(
            'id' => $id,
            'page' => $page,
            'type' => $type,
            'published' => !isset($b['published']) || !empty($b['published']),
            'data' => $clean,
        );
    }
    return $out;
}

function builder_live_blocks($page)
{
    $out = array();
    foreach (get_collection('blocks') as $b) {
        if (is_array($b) && isset($b['page']) && $b['page'] === $page) {
            $out[] = $b;
        }
    }
    return $out;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page = isset($_GET['page']) && in_array($_GET['page'], $pages, true) ? $_GET['page'] : 'home';
    $drafts = load_json('blocks_draft.json', array());
    $live = builder_live_blocks($page);
    $hasDraft = isset($drafts[$page]) && is_array($drafts[$page]);
    echo json_encode(array(
        'ok' => true,
        'page' => $page,
        'blocks' => $hasDraft ? $drafts[$page] : $live,
        'draft' => $hasDraft,
        'published' => $live,
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
if (!is_array($body)) {
    $body = array();
}
$sent = isset($body['_csrf']) ? (string) $body['_csrf'] : '';
ensure_session();
$known = isset($_SESSION['csrf']) ? (string) $_SESSION['csrf'] : '';
if ($sent === '' || !hash_equals($known, $sent)) {
    http_response_code(403);
    echo json_encode(array('ok' => false, 'error' => 'csrf'));
    exit;
}

$action = isset($body['action']) ? $body['action'] : '';
$page = isset($body['page']) && in_array($body['page'], $pages, true) ? $body['page'] : 'home';
$drafts = load_json('blocks_draft.json', array());
if (!is_array($drafts)) {
    $drafts = array();
}

if ($action === 'draft') {
    $drafts[$page] = builder_clean_blocks(isset($body['blocks']) ? $body['blocks'] : array(), $page, $types);
    $ok = save_json('blocks_draft.json', $drafts);
    echo json_encode(array('ok' => (bool) $ok, 'blocks' => $drafts[$page]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($action === 'publish') {
    $clean = builder_clean_blocks(isset($body['blocks']) ? $body['blocks'] : array(), $page, $types);
    $others = array();
    foreach (get_collection('blocks') as $b) {
        if (!is_array($b) || !isset($b['page']) || $b['page'] !== $page) {
            $others[] = $b;
        }
    }
    $ok = save_json('blocks.json', array_merge($clean, $others));
    unset($drafts[$page]);
    save_json('blocks_draft.json', $drafts);
    echo json_encode(array('ok' => (bool) $ok, 'blocks' => $clean), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($action === 'discard') {
    unset($drafts[$page]);
    save_json('blocks_draft.json', $drafts);
    echo json_encode(array('ok' => true, 'blocks' => builder_live_blocks($page)), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

http_response_code(400);
echo json_encode(array('ok' => false, 'error' => 'action'));
