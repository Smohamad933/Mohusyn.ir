<?php
/**
 * Development router — lets you preview the site locally with:
 *     php -S 0.0.0.0:3000 router.php
 * On IIS this file is not used (web.config handles routing).
 */

$file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) !== '/' && is_file($file)) {
    return false; // serve static files directly
}
require __DIR__ . '/index.php';
