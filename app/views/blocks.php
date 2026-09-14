<?php
/**
 * Custom blocks (drag & drop builder content) — renderer.
 * Block types: heading, text, image, button, spacer, divider.
 */

function blocks_for_page($page)
{
    $out = array();
    foreach (get_collection('blocks') as $b) {
        if (!is_array($b)) {
            continue;
        }
        if (isset($b['page']) && $b['page'] === $page && !empty($b['published'])) {
            $out[] = $b;
        }
    }
    return $out;
}

/** True when the page is opened inside the admin visual builder. */
function builder_mode()
{
    static $mode = null;
    if ($mode === null) {
        $mode = isset($_GET['builder']) && $_GET['builder'] === '1' && is_authed();
    }
    return $mode;
}

function render_blocks($page)
{
    $blocks = blocks_for_page($page);
    if (builder_mode()) {
        /* the builder renders blocks client-side; we only provide the drop area */
        echo '<div class="blocks-area" data-blocks-area="' . e($page) . '"></div>';
        return;
    }
    if (empty($blocks)) {
        return;
    }

    echo '<div class="blocks-area">';
    foreach ($blocks as $b) {
        $type = isset($b['type']) ? $b['type'] : 'text';
        $d = isset($b['data']) && is_array($b['data']) ? $b['data'] : array();

        if ($type === 'heading') {
            $level = isset($d['level']) && in_array($d['level'], array('h2', 'h3', 'h4'), true) ? $d['level'] : 'h2';
            $text = isset($d['text']) ? $d['text'] : '';
            echo '<div class="blk blk-heading"><' . $level . ' class="blk-heading-text">' . e($text) . '</' . $level . '></div>';

        } elseif ($type === 'text') {
            $text = isset($d['text']) ? $d['text'] : '';
            echo '<div class="blk blk-text"><p>' . nl2br(e($text)) . '</p></div>';

        } elseif ($type === 'image') {
            $src = isset($d['src']) ? $d['src'] : '';
            if ($src === '') {
                continue;
            }
            $alt = isset($d['alt']) ? $d['alt'] : '';
            $align = isset($d['align']) && in_array($d['align'], array('left', 'center', 'right'), true) ? $d['align'] : 'center';
            $width = isset($d['width']) && (int) $d['width'] > 0 ? ' style="max-width:' . (int) $d['width'] . 'px"' : '';
            echo '<div class="blk blk-image blk-align-' . $align . '">'
                . '<div class="blk-image-box"' . $width . '>'
                . '<img src="' . e($src) . '" alt="' . e($alt) . '" loading="lazy">'
                . '</div></div>';

        } elseif ($type === 'button') {
            $label = isset($d['label']) ? $d['label'] : '';
            $url = isset($d['url']) && $d['url'] !== '' ? $d['url'] : '/contact/';
            $style = isset($d['style']) && $d['style'] === 'outline' ? ' blk-btn-outline' : '';
            $align = isset($d['align']) && in_array($d['align'], array('left', 'center', 'right'), true) ? $d['align'] : 'left';
            echo '<div class="blk blk-button blk-align-' . $align . '">'
                . '<a class="blk-btn' . $style . '" href="' . e($url) . '">' . e($label) . '</a></div>';

        } elseif ($type === 'spacer') {
            $h = isset($d['height']) ? (int) $d['height'] : 40;
            if ($h < 0) {
                $h = 0;
            }
            if ($h > 400) {
                $h = 400;
            }
            echo '<div class="blk blk-spacer" style="height:' . $h . 'px"></div>';

        } elseif ($type === 'divider') {
            echo '<div class="blk blk-divider"></div>';
        }
    }
    echo '</div>';
}
