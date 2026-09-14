<?php
/** Expects: $view, optional $post — the public site is English-only. */
$locale = 'en';
$settings = get_settings();

/* Meta title / description */
$brand = setting('brand', 'MOHUSYN');
if ($view === 'post' && isset($post['title'])) {
    $metaTitle = bi($post['title'], $locale) . ' — ' . $brand;
    $metaDesc = bi(isset($post['excerpt']) ? $post['excerpt'] : '', $locale);
} elseif ($view === 'work' && isset($project['title'])) {
    $metaTitle = bi($project['title'], $locale) . ' — ' . $brand;
    $metaDesc = bi(isset($project['category']) ? $project['category'] : '', $locale);
} elseif ($view === 'blog') {
    $metaTitle = t('blog.title', $locale) . ' — ' . $brand;
    $metaDesc = t('blog.subtitle', $locale);
} elseif ($view === 'about') {
    $metaTitle = page_setting('about', 'title') . ' — ' . $brand;
    $metaDesc = page_setting('about', 'subtitle');
} elseif ($view === 'contact') {
    $metaTitle = 'Contact — ' . $brand;
    $metaDesc = 'Start a project — send a message and get a reply.';
} else {
    $metaTitle = $brand . ' | ' . setting_bi('fullName', $locale);
    $metaDesc = setting_bi('bio', $locale);
}

$reqPath = current_request_path();
$canonicalPath = rtrim($reqPath, '/') . '/';

/* ------------------------------------------------ fonts (admin-managed) */
$fonts = isset($settings['fonts']) ? $settings['fonts'] : array();
$fontCssUrl = isset($fonts['cssUrl']) ? trim((string) $fonts['cssUrl']) : '';
$enFamily = isset($fonts['enFamily']) && trim((string) $fonts['enFamily']) !== '' ? trim((string) $fonts['enFamily']) : 'Space Grotesk';
$enFile = isset($fonts['enFile']) ? trim((string) $fonts['enFile']) : '';

/* body/small-text stack (admin-manageable) */
$stack = array();
if ($enFile !== '') { $stack[] = "'custom-en'"; }
$stack[] = "'" . $enFamily . "'";
$stack[] = 'sans-serif';
$fontStack = implode(', ', $stack);

/* bold/display stack — Doto */
$displayStack = "'Doto', monospace, sans-serif";

/* ---------------------------------------- per-tag fonts (admin-managed) */
$tagFontRules = array();
$tagFonts = isset($settings['tagFonts']) ? $settings['tagFonts'] : array();
foreach (array('h1', 'h2', 'h3', 'h4', 'p') as $tag) {
    if (!isset($tagFonts[$tag]) || !is_array($tagFonts[$tag])) {
        continue;
    }
    $mode = isset($tagFonts[$tag]['mode']) ? $tagFonts[$tag]['mode'] : 'default';
    $family = isset($tagFonts[$tag]['family']) ? trim((string) $tagFonts[$tag]['family']) : '';
    $stackValue = '';
    if ($mode === 'uploaded' && $enFile !== '') {
        $stackValue = "'custom-en', monospace, sans-serif";
    } elseif ($mode === 'custom' && $family !== '') {
        $stackValue = $family;
    }
    if ($stackValue !== '') {
        $tagFontRules[] = $tag . ' { font-family: ' . $stackValue . '; }';
    }
}

/* ------------------------------------------------ custom CSS (raw) */
$customCss = isset($settings['customCss']) ? (string) $settings['customCss'] : '';
$customCss = str_ireplace('</style', '', $customCss);

$hasPosts = count(published_items('posts')) > 0;
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo e($metaTitle); ?></title>
<meta name="description" content="<?php echo e($metaDesc); ?>">
<link rel="canonical" href="<?php echo e($canonicalPath); ?>">
<meta property="og:title" content="<?php echo e($metaTitle); ?>">
<meta property="og:description" content="<?php echo e($metaDesc); ?>">
<meta property="og:type" content="website">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' fill='%23111111'/%3E%3Ctext x='32' y='46' font-family='monospace' font-size='36' font-weight='900' fill='%23ffffff' text-anchor='middle'%3EM%3C/text%3E%3C/svg%3E">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Doto:wght@400..900&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
<?php if ($fontCssUrl !== ''): ?>
<link rel="stylesheet" href="<?php echo e($fontCssUrl); ?>">
<?php endif; ?>
<style>
<?php if ($enFile !== ''): ?>
@font-face {
  font-family: 'custom-en';
  src: url('<?php echo e($enFile); ?>') format('woff2'), url('<?php echo e($enFile); ?>');
  font-weight: 100 900;
  font-display: swap;
}
<?php endif; ?>
:root { --font-body: <?php echo $fontStack; ?>; --font-display: <?php echo $displayStack; ?>; --font-main: <?php echo $fontStack; ?>; }
</style>
<link rel="stylesheet" href="/assets/css/site.css">
<?php if (!empty($tagFontRules) || trim($customCss) !== ''): ?>
<style id="site-custom">
<?php echo implode("\n", $tagFontRules); ?>
<?php echo $customCss; ?>
</style>
<?php endif; ?>
<script>
/* Apply saved theme before first paint (light is the default) */
try { if (localStorage.getItem('mohusyn-theme') === 'dark') document.documentElement.classList.add('dark'); } catch (e) {}
</script>
</head>
<body data-timezone="<?php echo e(setting('timezone', SITE_TIMEZONE)); ?>">

<header class="header">
  <div class="container-header">
    <a class="title-name-header" href="/"><?php echo e($brand); ?></a>

    <nav class="header-nav">
      <a class="link-menu-text <?php echo $view === 'home' ? 'is-active' : ''; ?>" href="/">Home</a>
      <a class="link-menu-text <?php echo $view === 'about' ? 'is-active' : ''; ?>" href="/about/">About Me</a>
      <?php if ($hasPosts): ?>
      <a class="link-menu-text <?php echo ($view === 'blog' || $view === 'post') ? 'is-active' : ''; ?>" href="/blog/">Blog</a>
      <?php endif; ?>
      <a class="link-menu-text <?php echo $view === 'contact' ? 'is-active' : ''; ?>" href="/contact/">Contact</a>
    </nav>

    <div class="header-actions">
      <button class="theme-toggle" type="button" data-theme-toggle aria-label="Toggle theme">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <circle cx="12" cy="12" r="5"></circle>
          <path d="M12 1v3M12 20v3M1 12h3M20 12h3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"></path>
        </svg>
      </button>
      <button class="hamburger-btn" type="button" data-hamburger aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>

  <div class="mobile-menu" data-mobile-menu>
    <a href="/">Home</a>
    <a href="/about/">About Me</a>
    <?php if ($hasPosts): ?>
    <a href="/blog/">Blog</a>
    <?php endif; ?>
    <a href="/contact/">Contact</a>
  </div>
</header>

<div class="sub-header-bar">
  <div class="sub-header-content">
    <div class="left-group-all">
      <div class="time-box">
        <span class="date-text">
          <span data-clock-weekday>—</span><br>
          <span data-clock-date>—</span>
        </span>
        <div class="time-divider"></div>
        <span class="clock-display" data-clock-time>--:--:--</span>
      </div>
      <div class="online-status">
        <span class="status-dot"></span>
        <span class="status-name"><?php echo e(setting_bi('availability', $locale)); ?></span>
      </div>
    </div>
    <div class="left-group-all">
      <span class="badge-box"><?php echo e(setting_bi('tagline', $locale)); ?></span>
      <span class="timezone-box mono-dir">UTC+3:30</span>
    </div>
  </div>
</div>

<main>
