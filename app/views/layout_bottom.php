<?php if (!isset($locale)) { $locale = DEFAULT_LOCALE; } ?>
</main>

<footer class="site-footer" id="contact">
  <div class="footer-content">
    <span class="footer-contact-tag"><?php echo e(t('nav.contact', $locale)); ?></span>
    <h2 class="footer-title"><?php echo e(setting_bi('footerTitle', $locale, 'footerTitle')); ?></h2>
    <p class="footer-desc"><?php echo e(setting_bi('footerSubtitle', $locale, 'footerSubtitle')); ?></p>

    <?php $email = setting('contactEmail'); ?>
      <a class="lets-talk-btn" href="/contact/">
        <?php echo e(t('footer.talk', $locale)); ?>
        <span class="btn-arrow">→</span>
      </a>
    <?php if ($email !== ''): ?>
      <span class="footer-email mono-dir"><?php echo e($email); ?></span>
    <?php endif; ?>
  </div>

  <div class="footer-bottom">
    <span>© <?php echo e(date('Y')); ?> <?php echo e(setting('brand', 'MOHUSYN')); ?> — <?php echo e(t('footer.rights', $locale)); ?></span>
    <span class="footer-links">
      <?php if (setting('instagram') !== ''): ?><a href="<?php echo e(setting('instagram')); ?>" target="_blank" rel="noopener">Instagram</a><?php endif; ?>
      <?php if (setting('linkedin') !== ''): ?><a href="<?php echo e(setting('linkedin')); ?>" target="_blank" rel="noopener">LinkedIn</a><?php endif; ?>
      <?php if (setting('github') !== ''): ?><a href="<?php echo e(setting('github')); ?>" target="_blank" rel="noopener">GitHub</a><?php endif; ?>
      <?php if (setting('workSiteUrl') !== ''): ?><a href="<?php echo e(setting('workSiteUrl')); ?>" target="_blank" rel="noopener"><?php echo e(setting_bi('workSiteLabel', $locale)); ?></a><?php endif; ?>
      <?php if (setting('showAdminLink', true)): ?><a href="/admin/"><?php echo e(t('footer.admin', $locale)); ?></a><?php endif; ?>
    </span>
  </div>
</footer>

<script src="<?php echo e(asset('/assets/js/site.js')); ?>"></script>
<?php if (!defined('ANALYTICS_ENABLED') || ANALYTICS_ENABLED): ?>
<script src="<?php echo e(asset('/assets/js/analytics.js')); ?>" defer></script>
<?php endif; ?>
<?php if (isset($view) && $view === 'contact'): ?>
<script src="<?php echo e(asset('/assets/js/contact.js')); ?>"></script>
<?php endif; ?>
<?php if (function_exists('builder_mode') && builder_mode()): ?>
<script src="<?php echo e(asset('/assets/js/builder-frame.js')); ?>"></script>
<?php endif; ?>
</body>
</html>
