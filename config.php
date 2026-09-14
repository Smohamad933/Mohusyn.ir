<?php
/**
 * Mohusyn.ir — Configuration
 * ----------------------------------------------------------------
 * NOTE: Change the admin password below right after deploying!
 */

// Admin panel password (plain text; compared with hash_equals).
define('ADMIN_PASSWORD', 'mohusyn2026');

// Absolute path of the site root (this folder).
define('ROOT_PATH', __DIR__);

// Default locale used for the "/" redirect and fallbacks.
define('DEFAULT_LOCALE', 'en');

// Timezone used for dates shown on the site.
define('SITE_TIMEZONE', 'Asia/Tehran');

// Max upload size in bytes (8 MB).
define('MAX_UPLOAD_SIZE', 8 * 1024 * 1024);

// Allowed upload extensions.
define('ALLOWED_EXTENSIONS', array('jpg', 'jpeg', 'png', 'gif', 'webp'));

/* Contact-form attachments (public upload): max size + allowed types */
define('CONTACT_MAX_UPLOAD', 20 * 1024 * 1024);
define('CONTACT_ALLOWED_EXTENSIONS', array('jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'zip', 'rar', '7z', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'mp4', 'mov', 'mp3', 'ai', 'psd', 'fig', 'sketch'));

/* --------------------------------------------------------------- database
   DB_TYPE = 'sqlite' -> zero-config, database file inside /data (default)
   DB_TYPE = 'mysql'  -> fill DB_HOST / DB_NAME / DB_USER / DB_PASS below
-------------------------------------------------------------------------- */
define('DB_TYPE', 'sqlite');

// SQLite database file (used when DB_TYPE = 'sqlite')
define('DB_SQLITE_PATH', __DIR__ . '/data/cms-database.sqlite');

// MySQL connection (used when DB_TYPE = 'mysql')
define('DB_HOST', 'localhost');
define('DB_NAME', 'mohusyn');
define('DB_USER', 'dbuser');
define('DB_PASS', 'dbpass');
define('DB_CHARSET', 'utf8mb4');

date_default_timezone_set(SITE_TIMEZONE);
