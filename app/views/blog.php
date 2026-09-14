<?php
/** Expects: $locale */
$posts = published_items('posts');
?>
<section class="section">
  <div class="container container-narrow">
    <div class="section-head">
      <p class="kicker mono"><?php echo e(t('blog.subtitle', $locale)); ?></p>
      <h1 class="section-title"><?php echo e(t('blog.title', $locale)); ?></h1>
    </div>

    <?php if (empty($posts)): ?>
      <p class="empty mono"><?php echo e(t('blog.empty', $locale)); ?></p>
    <?php else: ?>
      <div class="post-list">
        <?php foreach ($posts as $post): ?>
          <a class="post-card" href="<?php echo e(locale_path($locale, 'blog/' . $post['slug'])); ?>">
            <?php if (!empty($post['cover'])): ?>
              <div class="post-cover"><img src="<?php echo e($post['cover']); ?>" alt="" loading="lazy"></div>
            <?php endif; ?>
            <div class="post-info">
              <span class="mono post-date"><?php echo e(localized_date($post['createdAt'], $locale, false)); ?></span>
              <h2 class="post-title"><?php echo e(bi($post['title'], $locale)); ?></h2>
              <p class="post-excerpt"><?php echo e(bi($post['excerpt'], $locale)); ?></p>
              <span class="mono post-read"><?php echo e(t('blog.read', $locale)); ?> →</span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
