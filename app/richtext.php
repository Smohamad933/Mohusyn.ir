<?php
/**
 * Rich text — whitelist sanitizer + renderer for admin-authored HTML
 * (case studies, blog posts). No external dependencies.
 */

/** Allowed tags -> allowed attributes. */
function rich_allowed()
{
    return array(
        'p' => array(), 'br' => array(), 'strong' => array(), 'b' => array(), 'em' => array(), 'i' => array(), 'u' => array(), 's' => array(),
        'h2' => array(), 'h3' => array(), 'h4' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(),
        'blockquote' => array(), 'code' => array(), 'pre' => array(), 'hr' => array(), 'span' => array(),
        'a' => array('href', 'target', 'rel', 'title'),
        'img' => array('src', 'alt', 'width', 'height'),
        'figure' => array(), 'figcaption' => array(),
        'mark' => array(), 'sub' => array(), 'sup' => array(),
        'table' => array(), 'thead' => array(), 'tbody' => array(), 'tr' => array(), 'th' => array(), 'td' => array(),
        'div' => array('class'),
    );
}

function rich_safe_url($url, $isImage = false)
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }
    if (preg_match('/^\s*(javascript|vbscript|data):/i', $url) && !($isImage && preg_match('#^data:image/(png|jpe?g|gif|webp);base64,#i', $url))) {
        return '';
    }
    return $url;
}

/** Sanitize HTML from the editor. Falls back to escaped text when DOM is unavailable. */
function rich_sanitize($html)
{
    $html = (string) $html;
    if (trim($html) === '') {
        return '';
    }
    if (!class_exists('DOMDocument')) {
        return '<p>' . nl2br(e(strip_tags($html))) . '</p>';
    }
    $allowed = rich_allowed();
    $doc = new DOMDocument('1.0', 'UTF-8');
    $prev = libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8"><div id="rt-root">' . $html . '</div>', LIBXML_NONET | (defined('LIBXML_HTML_NOIMPLIED') ? LIBXML_HTML_NOIMPLIED : 0) | (defined('LIBXML_HTML_NODEFDTD') ? LIBXML_HTML_NODEFDTD : 0));
    libxml_clear_errors();
    libxml_use_internal_errors($prev);
    $root = $doc->getElementById('rt-root');
    if (!$root) {
        return '<p>' . nl2br(e(strip_tags($html))) . '</p>';
    }
    rich_clean_node($root, $allowed);
    $out = '';
    foreach ($root->childNodes as $child) {
        $out .= $doc->saveHTML($child);
    }
    /* drop empty paragraphs the editor leaves behind */
    $out = preg_replace('#<p>(\s|&nbsp;|<br\s*/?>)*</p>#u', '', $out);
    return trim($out);
}

function rich_clean_node(DOMNode $node, $allowed)
{
    $children = array();
    foreach ($node->childNodes as $c) {
        $children[] = $c;
    }
    foreach ($children as $child) {
        if ($child->nodeType === XML_COMMENT_NODE) {
            $node->removeChild($child);
            continue;
        }
        if ($child->nodeType !== XML_ELEMENT_NODE) {
            continue;
        }
        $tag = strtolower($child->nodeName);
        if ($tag === 'script' || $tag === 'style' || $tag === 'iframe' || $tag === 'object' || $tag === 'embed') {
            $node->removeChild($child);
            continue;
        }
        if (!isset($allowed[$tag])) {
            /* unwrap unknown tags, keep their content */
            rich_clean_node($child, $allowed);
            while ($child->firstChild) {
                $node->insertBefore($child->firstChild, $child);
            }
            $node->removeChild($child);
            continue;
        }
        /* attributes */
        $attrs = array();
        foreach ($child->attributes as $a) {
            $attrs[] = $a->nodeName;
        }
        foreach ($attrs as $an) {
            $lan = strtolower($an);
            if (!in_array($lan, $allowed[$tag], true)) {
                $child->removeAttribute($an);
                continue;
            }
            if ($lan === 'href' || $lan === 'src') {
                $v = rich_safe_url($child->getAttribute($an), $lan === 'src');
                if ($v === '') {
                    $child->removeAttribute($an);
                } else {
                    $child->setAttribute($an, $v);
                }
            } elseif ($lan === 'class') {
                $v = preg_replace('/[^a-z0-9\- ]/i', '', $child->getAttribute($an));
                if (!preg_match('/^(rt-[a-z\-]+\s*)+$/', $v)) {
                    $child->removeAttribute($an);
                } else {
                    $child->setAttribute($an, trim($v));
                }
            }
        }
        if ($tag === 'a') {
            if ($child->getAttribute('target') === '_blank') {
                $child->setAttribute('rel', 'noopener');
            } else {
                $child->removeAttribute('target');
                $child->removeAttribute('rel');
            }
        }
        rich_clean_node($child, $allowed);
    }
}

/** Does the string look like editor HTML (vs. legacy plain/markdown text)? */
function rich_is_html($text)
{
    return (bool) preg_match('/<(p|h[2-4]|ul|ol|li|strong|em|a|br|img|blockquote|div)\b[^>]*>/i', (string) $text);
}

/** Legacy plain text (paragraphs, ## headings, - / * bullets) -> HTML. */
function rich_from_plain($body)
{
    $lines = preg_split('/\r?\n/', (string) $body);
    $out = '';
    $inList = false;
    $para = array();
    $flush = function () use (&$para, &$out) {
        if (!empty($para)) {
            $out .= '<p>' . implode('<br>', array_map('e', $para)) . '</p>';
            $para = array();
        }
    };
    foreach ($lines as $line) {
        $t = trim($line);
        if ($t === '') {
            $flush();
            if ($inList) { $out .= '</ul>'; $inList = false; }
            continue;
        }
        if (strpos($t, '## ') === 0) {
            $flush();
            if ($inList) { $out .= '</ul>'; $inList = false; }
            $out .= '<h2>' . e(substr($t, 3)) . '</h2>';
        } elseif (strpos($t, '- ') === 0 || strpos($t, '* ') === 0) {
            $flush();
            if (!$inList) { $out .= '<ul>'; $inList = true; }
            $out .= '<li>' . e(substr($t, 2)) . '</li>';
        } else {
            if ($inList) { $out .= '</ul>'; $inList = false; }
            $para[] = $t;
        }
    }
    $flush();
    if ($inList) { $out .= '</ul>'; }
    return $out;
}

/** Render a stored body (HTML from the editor or legacy text) safely. */
function rich_render($body)
{
    $body = (string) $body;
    if (trim($body) === '') {
        return '';
    }
    return rich_is_html($body) ? rich_sanitize($body) : rich_from_plain($body);
}

/** Plain-text excerpt for SEO descriptions. */
function rich_text($body, $max = 160)
{
    $t = html_entity_decode(strip_tags(rich_render($body)), ENT_QUOTES, 'UTF-8');
    $t = trim(preg_replace('/\s+/u', ' ', $t));
    if (function_exists('mb_strlen') && mb_strlen($t) > $max) {
        return rtrim(mb_substr($t, 0, $max - 1)) . '…';
    }
    return $t;
}
