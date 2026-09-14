<?php
/** Case study page — expects: $locale, $project */

/* body: rich HTML from the admin editor (legacy plain text is converted automatically) */
?>
<article class="case-study">
  <div class="case-container">
    <a class="mono back-link" href="/">← All projects</a>

    <header class="case-header">
      <span class="case-category mono"><?php echo e(bi($project['category'], $locale)); ?></span>
      <h1 class="case-title"><?php echo e(bi($project['title'], $locale)); ?></h1>
      <?php if (!empty($project['link'])): ?>
        <a class="work-action-btn case-visit" href="<?php echo e($project['link']); ?>" target="_blank" rel="noopener">
          Visit live ↗
        </a>
      <?php endif; ?>
    </header>

    <?php if (!empty($project['image'])): ?>
      <div class="case-cover">
        <img src="<?php echo e($project['image']); ?>" alt="<?php echo e(bi($project['title'], $locale)); ?>">
      </div>
    <?php endif; ?>

    <div class="case-body rich">
      <?php echo rich_render(isset($project['body']) ? $project['body'] : ''); ?>
    </div>
  </div>

  <?php
  /* Related projects in the same category */
  $related = array();
  foreach (published_items('projects') as $p) {
      if ($p['id'] === $project['id']) { continue; }
      $related[] = $p;
      if (count($related) >= 3) { break; }
  }
  if (!empty($related)):
  ?>
  <div class="case-container">
    <div class="related-head">
      <span class="mono related-kicker">More in <?php echo e(bi($project['category'], $locale)); ?></span>
    </div>
    <div class="related-grid">
      <?php foreach ($related as $rp):
          $href = !empty($rp['slug']) ? '/work/' . $rp['slug'] . '/' : (!empty($rp['link']) ? $rp['link'] : '#');
          $external = empty($rp['slug']) && !empty($rp['link']);
      ?>
        <a class="related-card" href="<?php echo e($href); ?>" <?php echo $external ? 'target="_blank" rel="noopener"' : ''; ?>>
          <div class="related-cover">
            <img src="<?php echo e($rp['image']); ?>" alt="<?php echo e(bi($rp['title'], $locale)); ?>" loading="lazy">
          </div>
          <div class="related-meta">
            <span class="mono related-cat"><?php echo e(bi($rp['category'], $locale)); ?></span>
            <h3 class="related-title"><?php echo e(bi($rp['title'], $locale)); ?></h3>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</article>
