<?php
/**
 * Analytics collector — receives beacons from assets/js/analytics.js and
 * aggregates them per day into data/analytics.json. Nothing personal is kept:
 * no IPs, no cookies, no user agents — just counters.
 *
 * analytics.json = {
 *   "YYYY-MM-DD": {
 *     "views": { "/path/": n }, "sessions": n, "duration": totalSeconds,
 *     "device": { "mobile": n, ... }, "referrers": { "host": n },
 *     "clicks": { "label": n }, "dwell": { "/path/|section": seconds }, "dwellHits": { ... : n },
 *     "scroll": { "/path/|25": n, ... }
 *   }
 * }
 */

require __DIR__ . '/config.php';
require __DIR__ . '/app/helpers.php';

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}
if (defined('ANALYTICS_ENABLED') && !ANALYTICS_ENABLED) {
    http_response_code(204);
    exit;
}
$raw = file_get_contents('php://input');
if ($raw === false || strlen($raw) > 20000) {
    http_response_code(204);
    exit;
}
$body = json_decode($raw, true);
if (!is_array($body) || !isset($body['e']) || !is_array($body['e'])) {
    http_response_code(204);
    exit;
}

$path = isset($body['p']) && is_string($body['p']) ? substr($body['p'], 0, 120) : '/';
if (!preg_match('#^/[A-Za-z0-9._~/\-]*$#', $path) || strpos($path, '/admin') === 0) {
    http_response_code(204);
    exit;
}
$device = isset($body['d']) && in_array($body['d'], array('mobile', 'tablet', 'desktop'), true) ? $body['d'] : 'desktop';
$ref = isset($body['r']) && is_string($body['r']) ? preg_replace('/[^a-z0-9.\-]/i', '', substr($body['r'], 0, 80)) : '';
$dur = isset($body['dur']) ? max(0, min(3600, (int) $body['dur'])) : 0;

function ana_bump(&$arr, $key, $n = 1)
{
    if ($key === '') {
        return;
    }
    if (!isset($arr[$key])) {
        if (count($arr) >= 400) {
            return; /* cap distinct keys per day */
        }
        $arr[$key] = 0;
    }
    $arr[$key] += $n;
}

$today = date('Y-m-d');
$all = load_json('analytics.json', array());
if (!is_array($all)) {
    $all = array();
}
if (!isset($all[$today]) || !is_array($all[$today])) {
    $all[$today] = array();
}
$day = &$all[$today];
foreach (array('views', 'device', 'referrers', 'clicks', 'dwell', 'dwellHits', 'scroll') as $k) {
    if (!isset($day[$k]) || !is_array($day[$k])) {
        $day[$k] = array();
    }
}
if (!isset($day['sessions'])) {
    $day['sessions'] = 0;
}
if (!isset($day['duration'])) {
    $day['duration'] = 0;
}

$count = 0;
foreach ($body['e'] as $ev) {
    if (!is_array($ev) || !isset($ev['t']) || ++$count > 60) {
        continue;
    }
    $t = (string) $ev['t'];
    $k = isset($ev['k']) && is_scalar($ev['k']) ? trim(preg_replace('/\s+/', ' ', (string) $ev['k'])) : '';
    $k = function_exists('mb_substr') ? mb_substr($k, 0, 110) : substr($k, 0, 110);
    $v = isset($ev['v']) ? (int) $ev['v'] : 1;
    if ($t === 'view') {
        ana_bump($day['views'], $path, 1);
        ana_bump($day['device'], $device, 1);
        if ($ref !== '') {
            ana_bump($day['referrers'], $ref, 1);
        }
        $day['sessions']++;
    } elseif ($t === 'click') {
        ana_bump($day['clicks'], $path . '|' . $k, 1);
    } elseif ($t === 'dwell') {
        $sec = max(0, min(600, $v));
        ana_bump($day['dwell'], $path . '|' . $k, $sec);
        ana_bump($day['dwellHits'], $path . '|' . $k, 1);
    } elseif ($t === 'scroll') {
        if (in_array($k, array('25', '50', '75', '100'), true)) {
            ana_bump($day['scroll'], $path . '|' . $k, 1);
        }
    }
}
$day['duration'] += $dur;
unset($day);

/* keep 120 days */
ksort($all);
while (count($all) > 120) {
    reset($all);
    unset($all[key($all)]);
}
save_json('analytics.json', $all);
http_response_code(204);
