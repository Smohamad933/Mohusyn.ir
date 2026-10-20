<?php
/**
 * Site configuration — edit these values after deploying.
 */

// Admin panel password (change it!). Admin URL: /admin
define('ADMIN_PASSWORD', 'mohusyn2026');

// Where contact-form notifications are sent (leave empty to only store them in the panel).
define('NOTIFY_EMAIL', '');

// "From" address used by PHP mail() on IIS (must be allowed by your SMTP relay).
define('MAIL_FROM', 'no-reply@mohusyn.ir');

// Max upload size for images/files in the admin panel (bytes).
define('MAX_UPLOAD_SIZE', 25 * 1024 * 1024);

// Session cookie lifetime in seconds (default 30 days).
define('SESSION_LIFETIME', 30 * 24 * 3600);

// Allowed upload extensions.
define('ALLOWED_UPLOADS', 'jpg,jpeg,png,gif,webp,svg,mp3,mp4,wav,ogg,pdf,zip,rar,7z,doc,docx,xls,xlsx,ppt,pptx,txt,csv,ico,woff,woff2,ttf');
