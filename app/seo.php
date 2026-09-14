<?php
/**
 * Mohusyn.ir — on-page SEO helpers.
 *
 * Admin-managed values live in settings.json under "seo":
 *   siteUrl, siteName, defaultTitle, titleSuffix, defaultDescription,
 *   keywordsEn, keywordsFa (comma separated), ogImage, twitter, author,
 *   jobTitle, city, country, sameAs (one per line), googleVerify, bingVerify,
 *   yandexVerify, headExtra (raw), noindex (bool)
 *   pages: { home|about|work|contact|blog : { title, description, keywords } }
 * Per project / post: optional seoTitle, seoDescription, seoKeywords fields.
 */

function seo_settings()
{
    $settings = get_settings();
    $seo = isset($settings['seo']) && is_array($settings['seo']) ? $settings['seo'] : array();
    return $seo;
}

function seo_get($key, $default = '')
{
    $seo = seo_settings();
    return isset($seo[$key]) && is_scalar($seo[$key]) && (string) $seo[$key] !== '' ? (string) $seo[$key] : $default;
}

function seo_page($page, $key, $default = '')
{
    $seo = seo_settings();
    if (isset($seo['pages'][$page][$key]) && is_scalar($seo['pages'][$page][$key]) && (string) $seo['pages'][$page][$key] !== '') {
        return (string) $seo['pages'][$page][$key];
    }
    return $default;
}

/** Absolute site URL without trailing slash (admin value or derived from the request). */
function seo_site_url()
{
    $u = rtrim(seo_get('siteUrl'), '/');
    if ($u !== '') {
        return $u;
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'www.mohusyn.ir';
    return ($https ? 'https://' : 'http://') . $host;
}

function seo_abs($path)
{
    if ($path === '' || $path === null) {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return seo_site_url() . '/' . ltrim($path, '/');
}

/** Merge comma-separated keyword lists into one clean, de-duplicated string. */
function seo_keywords()
{
    $parts = array();
    foreach (func_get_args() as $arg) {
        if (!is_string($arg) || trim($arg) === '') {
            continue;
        }
        foreach (preg_split('/[,\x{060C}\n]+/u', $arg) as $k) {
            $k = trim($k);
            if ($k !== '' && !in_array($k, $parts, true)) {
                $parts[] = $k;
            }
        }
    }
    return implode(', ', $parts);
}

function seo_trim($text, $max = 160)
{
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $text)));
    if (function_exists('mb_strlen') && mb_strlen($text) > $max) {
        $text = rtrim(mb_substr($text, 0, $max - 1)) . '…';
    } elseif (strlen($text) > $max) {
        $text = rtrim(substr($text, 0, $max - 1)) . '…';
    }
    return $text;
}

/**
 * Build the meta bundle for the current view.
 * Returns: title, description, keywords, canonical, image, type, robots, jsonld (array of arrays)
 */
function seo_build($view, $locale, $post = null, $project = null)
{
    $brand = setting('brand', 'MOHUSYN');
    $fullName = setting_bi('fullName', $locale);
    $siteName = seo_get('siteName', $brand);
    $suffix = seo_get('titleSuffix', ' — ' . $siteName);
    $defaultDesc = seo_get('defaultDescription', setting_bi('bio', $locale));
    $baseKw = seo_keywords(seo_get('keywordsEn'), seo_get('keywordsFa'));
    $site = seo_site_url();
    $path = rtrim(current_request_path(), '/') . '/';
    $image = seo_get('ogImage', setting('heroImage', setting('profileImage')));
    $type = 'website';
    $kw = $baseKw;
    $jsonld = array();

    $person = array(
        '@type' => 'Person',
        'name' => $fullName,
        'alternateName' => $brand,
        'url' => $site . '/',
        'jobTitle' => seo_get('jobTitle', 'Filmmaker, editor and UI/motion designer'),
    );
    if (setting('profileImage') !== '') {
        $person['image'] = seo_abs(setting('profileImage'));
    }
    $sameAs = array();
    foreach (array('instagram', 'linkedin', 'github', 'workSiteUrl') as $sk) {
        if (setting($sk) !== '') {
            $sameAs[] = setting($sk);
        }
    }
    foreach (preg_split('/\r?\n/', seo_get('sameAs')) as $line) {
        $line = trim($line);
        if ($line !== '' && !in_array($line, $sameAs, true)) {
            $sameAs[] = $line;
        }
    }
    if (!empty($sameAs)) {
        $person['sameAs'] = $sameAs;
    }
    if (seo_get('city') !== '' || seo_get('country') !== '') {
        $person['address'] = array('@type' => 'PostalAddress', 'addressLocality' => seo_get('city'), 'addressCountry' => seo_get('country', 'IR'));
    }
    if (setting('contactEmail') !== '') {
        $person['email'] = 'mailto:' . setting('contactEmail');
    }

    $website = array(
        '@type' => 'WebSite',
        'name' => $siteName,
        'url' => $site . '/',
        'inLanguage' => 'en',
        'publisher' => array('@type' => 'Person', 'name' => $fullName),
    );

    $crumbs = array(array('@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $site . '/'));

    if ($view === 'home') {
        $title = seo_page('home', 'title', seo_get('defaultTitle', $fullName . ' — ' . $person['jobTitle']));
        $suffixUse = seo_page('home', 'title') !== '' ? '' : ' | ' . $siteName;
        $title .= $suffixUse;
        $desc = seo_page('home', 'description', $defaultDesc);
        $kw = seo_keywords(seo_page('home', 'keywords'), $baseKw);
        $type = 'profile';
        $jsonld[] = array_merge(array('@context' => 'https://schema.org'), $person);
        $jsonld[] = array_merge(array('@context' => 'https://schema.org'), $website);
        $crumbs = array();
    } elseif ($view === 'about') {
        $title = seo_page('about', 'title', 'About ' . $fullName) . $suffix;
        $desc = seo_page('about', 'description', page_setting('about', 'subtitle') !== '' ? page_setting('about', 'subtitle') : $defaultDesc);
        $kw = seo_keywords(seo_page('about', 'keywords'), $baseKw);
        $type = 'profile';
        $jsonld[] = array('@context' => 'https://schema.org', '@type' => 'AboutPage', 'name' => $title, 'url' => $site . '/about/', 'mainEntity' => $person);
        $crumbs[] = array('@type' => 'ListItem', 'position' => 2, 'name' => 'About', 'item' => $site . '/about/');
    } elseif ($view === 'portfolio') {
        $title = seo_page('work', 'title', setting('portfolioTitle', 'Portfolio') . ' — ' . $fullName) . $suffix;
        $desc = seo_page('work', 'description', setting('portfolioSubtitle', 'Websites, brands and motion — every project by ' . $fullName . '.'));
        $kw = seo_keywords(seo_page('work', 'keywords'), $baseKw);
        $items = array();
        $i = 1;
        foreach (published_items('projects') as $p) {
            if (!empty($p['slug'])) {
                $items[] = array('@type' => 'ListItem', 'position' => $i++, 'name' => bi($p['title'], $locale), 'url' => $site . '/work/' . $p['slug'] . '/');
            }
        }
        $jsonld[] = array('@context' => 'https://schema.org', '@type' => 'CollectionPage', 'name' => $title, 'url' => $site . '/work/', 'mainEntity' => array('@type' => 'ItemList', 'itemListElement' => $items));
        $crumbs[] = array('@type' => 'ListItem', 'position' => 2, 'name' => 'Portfolio', 'item' => $site . '/work/');
    } elseif ($view === 'work' && $project) {
        $ptitle = bi($project['title'], $locale);
        $pcat = bi(isset($project['category']) ? $project['category'] : '', $locale);
        $title = (!empty($project['seoTitle']) ? $project['seoTitle'] : $ptitle . ' — ' . $pcat . ' case study') . $suffix;
        $bodyDesc = isset($project['body']) ? (function_exists('rich_text') ? rich_text($project['body']) : seo_trim($project['body'])) : '';
        $desc = !empty($project['seoDescription']) ? $project['seoDescription'] : ($bodyDesc !== '' ? $bodyDesc : $ptitle . ' — ' . $pcat . ' project by ' . $fullName . '.');
        $kw = seo_keywords(isset($project['seoKeywords']) ? $project['seoKeywords'] : '', $ptitle, $pcat, $baseKw);
        if (!empty($project['image'])) {
            $image = $project['image'];
        }
        $type = 'article';
        $jsonld[] = array(
            '@context' => 'https://schema.org', '@type' => 'CreativeWork', 'name' => $ptitle, 'genre' => $pcat,
            'url' => $site . '/work/' . $project['slug'] . '/', 'image' => seo_abs($image), 'description' => $desc,
            'creator' => $person, 'inLanguage' => 'en',
        );
        $crumbs[] = array('@type' => 'ListItem', 'position' => 2, 'name' => 'Portfolio', 'item' => $site . '/work/');
        $crumbs[] = array('@type' => 'ListItem', 'position' => 3, 'name' => $ptitle, 'item' => $site . '/work/' . $project['slug'] . '/');
    } elseif ($view === 'blog') {
        $title = seo_page('blog', 'title', 'Blog — notes and project updates') . $suffix;
        $desc = seo_page('blog', 'description', 'Articles and notes by ' . $fullName . ' on design, web and film.');
        $kw = seo_keywords(seo_page('blog', 'keywords'), $baseKw);
        $jsonld[] = array('@context' => 'https://schema.org', '@type' => 'Blog', 'name' => $title, 'url' => $site . '/blog/', 'author' => $person);
        $crumbs[] = array('@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $site . '/blog/');
    } elseif ($view === 'post' && $post) {
        $ptitle = bi($post['title'], $locale);
        $title = (!empty($post['seoTitle']) ? $post['seoTitle'] : $ptitle) . $suffix;
        $desc = !empty($post['seoDescription']) ? $post['seoDescription'] : seo_trim(bi(isset($post['excerpt']) ? $post['excerpt'] : '', $locale));
        if ($desc === '') {
            $desc = function_exists('rich_text') ? rich_text(bi(isset($post['body']) ? $post['body'] : '', $locale)) : seo_trim(bi(isset($post['body']) ? $post['body'] : '', $locale));
        }
        $kw = seo_keywords(isset($post['seoKeywords']) ? $post['seoKeywords'] : '', $baseKw);
        if (!empty($post['cover'])) {
            $image = $post['cover'];
        }
        $type = 'article';
        $jsonld[] = array(
            '@context' => 'https://schema.org', '@type' => 'BlogPosting', 'headline' => $ptitle, 'description' => $desc,
            'image' => seo_abs($image), 'datePublished' => isset($post['createdAt']) ? $post['createdAt'] : '',
            'dateModified' => isset($post['updatedAt']) ? $post['updatedAt'] : (isset($post['createdAt']) ? $post['createdAt'] : ''),
            'author' => $person, 'publisher' => array('@type' => 'Person', 'name' => $fullName),
            'mainEntityOfPage' => $site . '/blog/' . $post['slug'] . '/', 'inLanguage' => 'en',
        );
        $crumbs[] = array('@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $site . '/blog/');
        $crumbs[] = array('@type' => 'ListItem', 'position' => 3, 'name' => $ptitle, 'item' => $site . '/blog/' . $post['slug'] . '/');
    } elseif ($view === 'contact') {
        $title = seo_page('contact', 'title', 'Contact — start a project with ' . $brand) . $suffix;
        $desc = seo_page('contact', 'description', 'Tell me about your website, brand or film project. I usually reply within 24 hours.');
        $kw = seo_keywords(seo_page('contact', 'keywords'), $baseKw);
        $jsonld[] = array('@context' => 'https://schema.org', '@type' => 'ContactPage', 'name' => $title, 'url' => $site . '/contact/', 'mainEntity' => $person);
        $crumbs[] = array('@type' => 'ListItem', 'position' => 2, 'name' => 'Contact', 'item' => $site . '/contact/');
    } else {
        $title = 'Page not found' . $suffix;
        $desc = $defaultDesc;
        $crumbs = array();
    }

    if (count($crumbs) > 1) {
        $jsonld[] = array('@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $crumbs);
    }

    $robots = 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    $seoAll = seo_settings();
    if ($view === '404' || !empty($seoAll['noindex']) || (function_exists('builder_mode') && builder_mode())) {
        $robots = 'noindex, nofollow';
    }

    return array(
        'title' => seo_trim($title, 70),
        'description' => seo_trim($desc, 160),
        'keywords' => $kw,
        'canonical' => $site . $path,
        'image' => seo_abs($image),
        'type' => $type,
        'robots' => $robots,
        'jsonld' => $jsonld,
        'siteName' => $siteName,
    );
}

/** Print all SEO tags for <head>. */
function seo_head($meta, $locale)
{
    $seo = seo_settings();
    echo '<title>' . e($meta['title']) . "</title>\n";
    echo '<meta name="description" content="' . e($meta['description']) . "\">\n";
    if ($meta['keywords'] !== '') {
        echo '<meta name="keywords" content="' . e($meta['keywords']) . "\">\n";
    }
    echo '<meta name="robots" content="' . e($meta['robots']) . "\">\n";
    echo '<meta name="author" content="' . e(seo_get('author', setting_bi('fullName', $locale))) . "\">\n";
    echo '<link rel="canonical" href="' . e($meta['canonical']) . "\">\n";
    echo '<link rel="alternate" hreflang="en" href="' . e($meta['canonical']) . "\">\n";
    echo '<link rel="alternate" hreflang="x-default" href="' . e($meta['canonical']) . "\">\n";
    echo '<link rel="sitemap" type="application/xml" href="' . e(seo_site_url()) . "/sitemap.xml\">\n";
    /* Open Graph */
    echo '<meta property="og:type" content="' . e($meta['type']) . "\">\n";
    echo '<meta property="og:site_name" content="' . e($meta['siteName']) . "\">\n";
    echo '<meta property="og:locale" content="en_US">' . "\n";
    echo '<meta property="og:locale:alternate" content="fa_IR">' . "\n";
    echo '<meta property="og:title" content="' . e($meta['title']) . "\">\n";
    echo '<meta property="og:description" content="' . e($meta['description']) . "\">\n";
    echo '<meta property="og:url" content="' . e($meta['canonical']) . "\">\n";
    if ($meta['image'] !== '') {
        echo '<meta property="og:image" content="' . e($meta['image']) . "\">\n";
        echo '<meta property="og:image:alt" content="' . e($meta['title']) . "\">\n";
    }
    /* Twitter / X */
    echo '<meta name="twitter:card" content="' . ($meta['image'] !== '' ? 'summary_large_image' : 'summary') . "\">\n";
    if (seo_get('twitter') !== '') {
        $tw = '@' . ltrim(seo_get('twitter'), '@');
        echo '<meta name="twitter:site" content="' . e($tw) . "\">\n";
        echo '<meta name="twitter:creator" content="' . e($tw) . "\">\n";
    }
    echo '<meta name="twitter:title" content="' . e($meta['title']) . "\">\n";
    echo '<meta name="twitter:description" content="' . e($meta['description']) . "\">\n";
    if ($meta['image'] !== '') {
        echo '<meta name="twitter:image" content="' . e($meta['image']) . "\">\n";
    }
    /* Search-engine verification */
    foreach (array('googleVerify' => 'google-site-verification', 'bingVerify' => 'msvalidate.01', 'yandexVerify' => 'yandex-verification') as $k => $name) {
        if (seo_get($k) !== '') {
            echo '<meta name="' . $name . '" content="' . e(seo_get($k)) . "\">\n";
        }
    }
    echo '<meta name="theme-color" content="#111111">' . "\n";
    /* Structured data */
    foreach ($meta['jsonld'] as $obj) {
        $json = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json !== false) {
            echo '<script type="application/ld+json">' . str_replace('</', '<\/', $json) . "</script>\n";
        }
    }
    /* Raw extra head (analytics etc.) */
    if (isset($seo['headExtra']) && trim((string) $seo['headExtra']) !== '') {
        echo $seo['headExtra'] . "\n";
    }
}

/** XML sitemap for every public URL. */
function seo_sitemap()
{
    $site = seo_site_url();
    $urls = array(
        array('loc' => $site . '/', 'priority' => '1.0', 'changefreq' => 'weekly'),
        array('loc' => $site . '/work/', 'priority' => '0.9', 'changefreq' => 'weekly'),
        array('loc' => $site . '/about/', 'priority' => '0.8', 'changefreq' => 'monthly'),
        array('loc' => $site . '/contact/', 'priority' => '0.7', 'changefreq' => 'yearly'),
    );
    foreach (published_items('projects') as $p) {
        if (!empty($p['slug'])) {
            $u = array('loc' => $site . '/work/' . $p['slug'] . '/', 'priority' => '0.8', 'changefreq' => 'monthly');
            if (!empty($p['image'])) {
                $u['image'] = seo_abs($p['image']);
            }
            $urls[] = $u;
        }
    }
    $posts = published_items('posts');
    if (!empty($posts)) {
        $urls[] = array('loc' => $site . '/blog/', 'priority' => '0.7', 'changefreq' => 'weekly');
        foreach ($posts as $p) {
            if (!empty($p['slug'])) {
                $u = array('loc' => $site . '/blog/' . $p['slug'] . '/', 'priority' => '0.6', 'changefreq' => 'monthly');
                if (!empty($p['createdAt'])) {
                    $u['lastmod'] = $p['createdAt'];
                }
                $urls[] = $u;
            }
        }
    }
    header('Content-Type: application/xml; charset=utf-8');
    echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
    foreach ($urls as $u) {
        echo "  <url>\n    <loc>" . e($u['loc']) . "</loc>\n";
        if (isset($u['lastmod'])) {
            echo '    <lastmod>' . e($u['lastmod']) . "</lastmod>\n";
        }
        echo '    <changefreq>' . $u['changefreq'] . "</changefreq>\n    <priority>" . $u['priority'] . "</priority>\n";
        if (isset($u['image'])) {
            echo "    <image:image><image:loc>" . e($u['image']) . "</image:loc></image:image>\n";
        }
        echo "  </url>\n";
    }
    echo '</urlset>';
}

function seo_robots()
{
    header('Content-Type: text/plain; charset=utf-8');
    $site = seo_site_url();
    $seoAll = seo_settings();
    if (!empty($seoAll['noindex'])) {
        echo "User-agent: *\nDisallow: /\n";
        return;
    }
    echo "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /data/\nDisallow: /app/\nDisallow: /*?builder=1\n\nSitemap: " . $site . "/sitemap.xml\n";
}
