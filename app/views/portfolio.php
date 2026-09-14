<?php
/** Portfolio archive — every published project, filterable by category. Expects: $locale */
$projects = array();
foreach (published_items('projects') as $pp) {
    if (!array_key_exists('showOnPortfolio', $pp) || !empty($pp['showOnPortfolio'])) { $projects[] = $pp; }
}
$categories = array();
foreach ($projects as $p) {
    $cat = trim((string) bi(isset($p['category']) ? $p['category'] : '', $locale));
    if ($cat !== '' && !in_array($cat, $categories, true)) {
        $categories[] = $cat;
    }
}
$archiveTitle = setting('portfolioTitle', 'Selected Work');
$archiveSub = setting('portfolioSubtitle', 'Websites, brands and motion — every project, in one place.');
?>
<section class="archive-head">
  <div>
    <h1 class="archive-title"><?php echo e($archiveTitle); ?></h1>
    <p class="archive-sub"><?php echo e($archiveSub); ?></p>
  </div>
  <?php if (count($categories) > 1): ?>
    <div class="archive-filters" data-filters>
      <button type="button" class="archive-filter is-on" data-filter="*">All (<?php echo e(count($projects)); ?>)</button>
      <?php foreach ($categories as $cat): ?>
        <button type="button" class="archive-filter" data-filter="<?php echo e(strtolower($cat)); ?>"><?php echo e($cat); ?></button>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<section class="portfolio-section">
  <?php if (empty($projects)): ?>
    <p class="archive-empty mono">No projects published yet.</p>
  <?php else: ?>
  <div class="portfolio-grid" data-archive>
    <?php foreach ($projects as $project):
        $href = !empty($project['slug']) ? '/work/' . $project['slug'] . '/' : (!empty($project['link']) ? $project['link'] : '#');
        $external = empty($project['slug']) && !empty($project['link']);
        $cat = bi(isset($project['category']) ? $project['category'] : '', $locale);
    ?>
      <a class="portfolio-card" data-cat="<?php echo e(strtolower(trim((string) $cat))); ?>" href="<?php echo e($href); ?>" <?php echo $external ? 'target="_blank" rel="noopener"' : ''; ?>>
        <div class="portfolio-cover">
          <img src="<?php echo e($project['image']); ?>" alt="<?php echo e(bi($project['title'], $locale)); ?> — <?php echo e($cat); ?>" loading="lazy">
        </div>
        <div class="portfolio-meta">
          <span class="mono portfolio-cat"><?php echo e($cat); ?></span>
          <h2 class="portfolio-title"><?php echo e(bi($project['title'], $locale)); ?></h2>
          <span class="mono portfolio-open">Open →</span>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<script>
(function () {
  var wrap = document.querySelector('[data-filters]');
  var grid = document.querySelector('[data-archive]');
  if (!wrap || !grid) return;
  wrap.addEventListener('click', function (ev) {
    var btn = ev.target.closest('[data-filter]');
    if (!btn) return;
    var f = btn.getAttribute('data-filter');
    wrap.querySelectorAll('[data-filter]').forEach(function (b) { b.classList.toggle('is-on', b === btn); });
    grid.querySelectorAll('.portfolio-card').forEach(function (card) {
      card.classList.toggle('is-filtered-out', f !== '*' && card.getAttribute('data-cat') !== f);
    });
  });
})();
</script>
