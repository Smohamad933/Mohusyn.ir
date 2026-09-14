<?php
/**
 * Mohusyn.ir — Database layer (PDO)
 * Stores all CMS content as documents in a `cms_documents` table.
 * Works with SQLite (zero-config default) or MySQL.
 * If no database is available, the site transparently falls back
 * to the JSON files in /data (see helpers.php).
 */

if (!defined('ROOT_PATH')) {
    require dirname(__DIR__) . '/config.php';
}

/** Documents that are seeded from /data/*.json on first run. */
function db_seed_files()
{
    return array(
        'settings.json', 'projects.json', 'experiences.json', 'services.json',
        'skills.json', 'collaborators.json', 'posts.json', 'media.json', 'blocks.json',
    );
}

/** Open (and lazily initialize) the database. Returns PDO or null. */
function db_pdo()
{
    static $pdo = false; // false = not tried yet, null = unavailable
    if ($pdo !== false) {
        return $pdo;
    }

    $pdo = null;
    $type = defined('DB_TYPE') ? DB_TYPE : 'sqlite';

    try {
        if ($type === 'mysql') {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ));
            $pdo->exec('CREATE TABLE IF NOT EXISTS cms_documents (
                doc_key VARCHAR(190) NOT NULL PRIMARY KEY,
                doc_value LONGTEXT NOT NULL,
                updated_at DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        } else {
            $path = defined('DB_SQLITE_PATH') ? DB_SQLITE_PATH : ROOT_PATH . '/data/cms-database.sqlite';
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            $pdo = new PDO('sqlite:' . $path, null, null, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ));
            $pdo->exec('PRAGMA busy_timeout = 5000');
            $pdo->exec('CREATE TABLE IF NOT EXISTS cms_documents (
                doc_key TEXT PRIMARY KEY,
                doc_value TEXT NOT NULL,
                updated_at TEXT
            )');
        }
    } catch (Exception $ex) {
        $pdo = null; // helpers.php will fall back to JSON files
    }

    db_seed_if_needed($pdo);
    return $pdo;
}

/** First run: import the JSON seed files into the empty database. */
function db_seed_if_needed($pdo)
{
    if (!$pdo) {
        return;
    }
    try {
        $count = (int) $pdo->query('SELECT COUNT(*) FROM cms_documents')->fetchColumn();
        if ($count > 0) {
            return;
        }
        foreach (db_seed_files() as $file) {
            $path = ROOT_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                $raw = file_get_contents($path);
                if ($raw !== false && trim($raw) !== '') {
                    db_set_doc($file, $raw);
                }
            }
        }
    } catch (Exception $ex) {
        /* seeding is best-effort */
    }
}

/** Read a document (raw JSON string) or null. */
function db_get_doc($key)
{
    $pdo = db_pdo();
    if (!$pdo) {
        return null;
    }
    try {
        $stmt = $pdo->prepare('SELECT doc_value FROM cms_documents WHERE doc_key = ?');
        $stmt->execute(array($key));
        $value = $stmt->fetchColumn();
        return $value === false ? null : $value;
    } catch (Exception $ex) {
        return null;
    }
}

/** Write a document (raw JSON string). */
function db_set_doc($key, $value)
{
    $pdo = db_pdo();
    if (!$pdo) {
        return false;
    }
    try {
        if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
            $sql = 'INSERT OR REPLACE INTO cms_documents (doc_key, doc_value, updated_at) VALUES (?, ?, ?)';
        } else {
            $sql = 'REPLACE INTO cms_documents (doc_key, doc_value, updated_at) VALUES (?, ?, ?)';
        }
        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array($key, $value, date('Y-m-d H:i:s')));
    } catch (Exception $ex) {
        return false;
    }
}
