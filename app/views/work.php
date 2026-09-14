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

    <?php $bodyHtml = rich_render(isset($project['body']) ? $project['body'] : ''); ?>
    <section class="case-layout">
      <aside class="case-meta">
        <div class="case-meta-card">
          <span class="mono case-meta-kicker">Project details</span>
          <dl class="case-meta-list">
            <div><dt>Project</dt><dd><?php echo e(bi($project['title'], $locale)); ?></dd></div>
            <div><dt>Category</dt><dd><?php echo e(bi($project['category'], $locale)); ?></dd></div>
            <?php if (!empty($project['createdAt'])): ?><div><dt>Year</dt><dd><?php echo e(substr($project['createdAt'], 0, 4)); ?></dd></div><?php endif; ?>
            <?php if (!empty($project['link'])): ?>
              <div><dt>Website</dt><dd><a href="<?php echo e($project['link']); ?>" target="_blank" rel="noopener"><?php echo e(preg_replace('#^https?://(www\.)?|/$#', '', $project['link'])); ?> ↗</a></dd></div>
            <?php endif; ?>
          </dl>
          <a class="work-action-btn case-meta-cta" href="/#contact">Start a similar project →</a>
        </div>
      </aside>

      <div class="case-panel">
        <div class="case-panel-head">
          <span class="mono case-panel-kicker">Case study</span>
          <span class="case-panel-dots"><i></i><i></i><i></i></span>
        </div>
        <?php if (trim($bodyHtml) !== ''): ?>
          <div class="case-body rich">
            <?php echo $bodyHtml; ?>
          </div>
        <?php else: ?>
          <div class="case-body case-empty">
            <p>Full write-up coming soon.</p>
          </div>
        <?php endif; ?>
        <div class="case-panel-foot">
          <span class="mono">Designed, built and shipped by <?php echo e(setting('shortName', 'MOHUSYN')); ?></span>
          <?php if (!empty($project['link'])): ?>
            <a class="case-panel-link" href="<?php echo e($project['link']); ?>" target="_blank" rel="noopener">Visit live ↗</a>
          <?php endif; ?>
        </div>
      </div>
    </section>
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
