<?php if (!isset($locale) || ($locale !== 'fa' && $locale !== 'en')) { $locale = DEFAULT_LOCALE; } ?>
<section class="section notfound">
  <div class="container">
    <p class="kicker mono">404</p>
    <h1 class="section-title"><?php echo e(t('404.title', $locale)); ?></h1>
    <p class="section-sub"><?php echo e(t('404.body', $locale)); ?></p>
    <a class="btn btn-primary" href="<?php echo e(locale_path($locale)); ?>"><?php echo e(t('404.home', $locale)); ?></a>
  </div>
</section>
