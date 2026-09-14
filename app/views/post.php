<?php
/** Expects: $locale, $post */
?>
<article class="section">
  <div class="container container-narrow">
    <a class="mono back-link" href="<?php echo e(locale_path($locale, 'blog')); ?>">← <?php echo e(t('blog.back', $locale)); ?></a>

    <header class="post-header">
      <h1 class="post-single-title"><?php echo e(bi($post['title'], $locale)); ?></h1>
      <p class="mono post-date"><?php echo e(localized_date($post['createdAt'], $locale)); ?></p>
    </header>

    <?php if (!empty($post['cover'])): ?>
      <div class="post-single-cover">
        <img src="<?php echo e($post['cover']); ?>" alt="<?php echo e(bi($post['title'], $locale)); ?>">
      </div>
    <?php endif; ?>

    <div class="post-body rich">
      <?php echo rich_render(bi($post['body'], $locale)); ?>
    </div>
  </div>
</article>
