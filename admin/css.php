<?php
/**
 * Admin — full-page custom CSS editor.
 */

require __DIR__ . '/_bootstrap.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        header('Location: css.php?msg=csrf');
        exit;
    }
    $settings = get_settings();
    $css = isset($_POST['css']) ? (string) $_POST['css'] : '';
    /* never allow closing the inline style tag */
    $settings['customCss'] = str_ireplace('</style', '', $css);
    save_json('settings.json', $settings);
    header('Location: css.php?msg=saved');
    exit;
}

$settings = get_settings();
$css = isset($settings['customCss']) ? (string) $settings['customCss'] : '';

admin_header('css', 'ویرایشگر CSS');
admin_flash();
?>
<div class="page-head">
  <h1 class="page-title">ویرایشگر CSS سفارشی</h1>
  <a class="btn btn-ghost" href="settings.php">بازگشت به تنظیمات</a>
</div>
<p class="page-sub">
  قوانین CSS اینجا بعد از استایل اصلی سایت لود می‌شوند و می‌توانند هر چیزی را
  بازنویسی کنند: رنگ‌ها، فونت‌ها، فاصله‌ها، گوشه‌ها و… تغییرات بلافاصله بعد از
  ذخیره روی کل سایت اعمال می‌شوند.
</p>

<form method="post" action="css.php" class="css-editor-form">
  <?php echo csrf_field(); ?>

  <div class="css-toolbar">
    <span class="mono css-hint-dir" dir="ltr">site.css → overrides</span>
    <button class="btn btn-primary" type="submit">ذخیرهٔ CSS</button>
  </div>

  <textarea name="css" class="css-full-editor" spellcheck="false"
            placeholder="/* CSS rules */&#10;.build-main-title { letter-spacing: 2px; }&#10;.grid-item { border-radius: 6px; overflow: hidden; }"><?php echo e($css); ?></textarea>

  <div class="css-toolbar">
    <span class="hint" style="margin:0;">Ctrl+S در مرورگر ذخیره نمی‌کند — از دکمهٔ «ذخیرهٔ CSS» استفاده کنید.</span>
    <button class="btn btn-primary" type="submit">ذخیرهٔ CSS</button>
  </div>
</form>

<div class="tips">
  <h2>چند نمونه</h2>
  <pre dir="ltr">/* گوشه‌های کمی گرد برای کارت‌ها */
.grid-item, .work-section, .collaborators-section { border-radius: 6px; overflow: hidden; }

/* تغییر رنگ تاکساید */
:root { --accent: #1f4d3a; }

/* فاصلهٔ بیشتر بین بخش‌ها */
.skills-marquee-wrapper, .what-i-build-wrapper { margin-bottom: 60px; }</pre>
</div>
<?php
admin_footer();
