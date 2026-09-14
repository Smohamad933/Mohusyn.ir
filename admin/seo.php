<?php
/**
 * Admin — SEO settings (keywords fa/en, titles, descriptions, social cards,
 * verification codes, structured-data identity). Output is handled by app/seo.php.
 */

require __DIR__ . '/_bootstrap.php';
require_auth();

$pageDefs = array(
    'home' => array('label' => 'صفحهٔ اصلی', 'url' => '/'),
    'work' => array('label' => 'پورتفولیو (آرشیو نمونه‌کارها)', 'url' => '/work/'),
    'about' => array('label' => 'دربارهٔ من', 'url' => '/about/'),
    'contact' => array('label' => 'تماس', 'url' => '/contact/'),
    'blog' => array('label' => 'بلاگ', 'url' => '/blog/'),
);
$textKeys = array('siteUrl', 'siteName', 'defaultTitle', 'titleSuffix', 'author', 'jobTitle', 'city', 'country', 'twitter', 'ogImage', 'googleVerify', 'bingVerify', 'yandexVerify');
$areaKeys = array('defaultDescription', 'keywordsEn', 'keywordsFa', 'sameAs', 'headExtra');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        header('Location: seo.php?msg=csrf');
        exit;
    }
    $settings = get_settings();
    $seo = isset($settings['seo']) && is_array($settings['seo']) ? $settings['seo'] : array();
    foreach (array_merge($textKeys, $areaKeys) as $k) {
        $seo[$k] = isset($_POST['seo_' . $k]) ? trim((string) $_POST['seo_' . $k]) : '';
    }
    $seo['siteUrl'] = rtrim($seo['siteUrl'], '/');
    $seo['headExtra'] = str_ireplace('</head', '', $seo['headExtra']);
    $seo['noindex'] = !empty($_POST['seo_noindex']);
    $seo['pages'] = array();
    foreach ($pageDefs as $pk => $def) {
        $seo['pages'][$pk] = array(
            'title' => isset($_POST['seo_p_' . $pk . '_title']) ? trim((string) $_POST['seo_p_' . $pk . '_title']) : '',
            'description' => isset($_POST['seo_p_' . $pk . '_description']) ? trim((string) $_POST['seo_p_' . $pk . '_description']) : '',
            'keywords' => isset($_POST['seo_p_' . $pk . '_keywords']) ? trim((string) $_POST['seo_p_' . $pk . '_keywords']) : '',
        );
    }
    $settings['seo'] = $seo;
    save_json('settings.json', $settings);
    header('Location: seo.php?msg=saved');
    exit;
}

$settings = get_settings();
$seo = isset($settings['seo']) && is_array($settings['seo']) ? $settings['seo'] : array();
$media = media_list();
function seo_val($seo, $k) { return isset($seo[$k]) && is_scalar($seo[$k]) ? (string) $seo[$k] : ''; }
function seo_pval($seo, $p, $k) { return isset($seo['pages'][$p][$k]) ? (string) $seo['pages'][$p][$k] : ''; }

/* quick health check */
$checks = array();
$checks[] = array(seo_val($seo, 'siteUrl') !== '', 'آدرس اصلی سایت (برای canonical و نقشهٔ سایت) وارد شده');
$checks[] = array(seo_val($seo, 'keywordsEn') !== '' || seo_val($seo, 'keywordsFa') !== '', 'کلمات کلیدی وارد شده');
$checks[] = array(seo_val($seo, 'defaultDescription') !== '' || setting('bio') !== '', 'توضیح پیش‌فرض (meta description) موجود است');
$checks[] = array(seo_val($seo, 'ogImage') !== '' || setting('heroImage') !== '' || setting('profileImage') !== '', 'تصویر اشتراک‌گذاری (OG image) موجود است');
$checks[] = array(seo_val($seo, 'googleVerify') !== '', 'کد تأیید Google Search Console');
$checks[] = array(seo_val($seo, 'bingVerify') !== '', 'کد تأیید Bing Webmaster');
$checks[] = array(empty($seo['noindex']), 'سایت برای موتورهای جستجو باز است (noindex خاموش)');

admin_header('seo', 'سئو');
admin_flash();
?>
<div class="page-head">
  <h1 class="page-title">سئو (بهینه‌سازی برای گوگل و بینگ)</h1>
  <div class="page-head-actions">
    <a class="btn btn-ghost btn-mini" href="/sitemap.xml" target="_blank" rel="noopener">sitemap.xml ↗</a>
    <a class="btn btn-ghost btn-mini" href="/robots.txt" target="_blank" rel="noopener">robots.txt ↗</a>
  </div>
</div>
<p class="page-sub">
  کلمات کلیدی فارسی و انگلیسی را وارد کنید؛ سایت به‌طور خودکار عنوان، توضیحات، کارت‌های شبکه‌های اجتماعی (Open Graph / Twitter)،
  دادهٔ ساخت‌یافتهٔ Schema.org (Person, WebSite, CreativeWork, BreadcrumbList)، canonical، hreflang، نقشهٔ سایت و robots.txt را می‌سازد.
</p>

<div class="seo-health">
  <?php foreach ($checks as $c): ?>
    <span class="seo-check <?php echo $c[0] ? 'ok' : 'warn'; ?>"><?php echo $c[0] ? '✓' : '!'; ?> <?php echo e($c[1]); ?></span>
  <?php endforeach; ?>
</div>

<div class="tabs" role="tablist">
  <button type="button" class="is-on" data-tab="tab-kw">🔑 کلمات کلیدی</button>
  <button type="button" data-tab="tab-pages">📄 صفحه‌به‌صفحه</button>
  <button type="button" data-tab="tab-identity">🪪 هویت و شبکه‌ها</button>
  <button type="button" data-tab="tab-engines">🔎 گوگل / بینگ</button>
</div>

<form method="post" class="admin-form" action="seo.php">
  <?php echo csrf_field(); ?>

  <div class="tab-panel" id="tab-kw">
    <h2 class="form-section-title">کلمات کلیدی اصلی</h2>
    <p class="hint">با ویرگول جدا کنید. این‌ها به همهٔ صفحات اضافه می‌شوند؛ کلمات هر صفحه در تب «صفحه‌به‌صفحه» قابل افزودن است. نکته: گوگل به metaKeywords وزن نمی‌دهد، اما این کلمات در توضیحات، دادهٔ ساخت‌یافته و alt تصاویر هم استفاده می‌شوند — پس عنوان و توضیحات را هم با همین کلمات بنویسید.</p>
    <div class="field-row">
      <label class="field-label">کلمات کلیدی انگلیسی</label>
      <textarea dir="ltr" rows="3" name="seo_keywordsEn" placeholder="UI designer, motion designer, web design Iran, filmmaker Tehran"><?php echo e(seo_val($seo, 'keywordsEn')); ?></textarea>
    </div>
    <div class="field-row">
      <label class="field-label">کلمات کلیدی فارسی</label>
      <textarea dir="rtl" rows="3" name="seo_keywordsFa" placeholder="طراح سایت، طراح رابط کاربری، موشن دیزاین، فیلمساز، سید محمد حسین شیخ‌الاسلامی"><?php echo e(seo_val($seo, 'keywordsFa')); ?></textarea>
    </div>
    <div class="field-row">
      <label class="field-label">عنوان پیش‌فرض سایت (Title صفحهٔ اصلی)</label>
      <input type="text" dir="ltr" name="seo_defaultTitle" maxlength="70" value="<?php echo e(seo_val($seo, 'defaultTitle')); ?>" placeholder="Seyyed Mohammad Hossein Sheikholeslami — UI/Motion Designer & Filmmaker">
      <p class="hint">حداکثر ۶۰–۷۰ کاراکتر. کلمهٔ کلیدی اصلی را اول بیاورید.</p>
    </div>
    <div class="field-row">
      <label class="field-label">توضیح پیش‌فرض (meta description)</label>
      <textarea dir="ltr" rows="3" name="seo_defaultDescription" maxlength="170" placeholder="Portfolio of ..."><?php echo e(seo_val($seo, 'defaultDescription')); ?></textarea>
      <p class="hint">۱۲۰ تا ۱۶۰ کاراکتر؛ اگر خالی باشد از «متن معرفی (بیو)» استفاده می‌شود.</p>
    </div>
    <div class="field-row">
      <label class="field-label">پسوند عنوان صفحات داخلی</label>
      <input type="text" dir="ltr" name="seo_titleSuffix" value="<?php echo e(seo_val($seo, 'titleSuffix')); ?>" placeholder=" — MOHUSYN">
    </div>
  </div>

  <div class="tab-panel" id="tab-pages" hidden>
    <?php foreach ($pageDefs as $pk => $def): ?>
      <h2 class="form-section-title"><?php echo e($def['label']); ?> <span class="mono" dir="ltr"><?php echo e($def['url']); ?></span></h2>
      <div class="field-row">
        <label class="field-label">عنوان (Title)</label>
        <input type="text" dir="ltr" maxlength="70" name="seo_p_<?php echo e($pk); ?>_title" value="<?php echo e(seo_pval($seo, $pk, 'title')); ?>" placeholder="خالی = خودکار">
      </div>
      <div class="field-row">
        <label class="field-label">توضیح (Description)</label>
        <textarea dir="ltr" rows="2" maxlength="170" name="seo_p_<?php echo e($pk); ?>_description" placeholder="خالی = خودکار"><?php echo e(seo_pval($seo, $pk, 'description')); ?></textarea>
      </div>
      <div class="field-row">
        <label class="field-label">کلمات کلیدی اضافی این صفحه (فارسی یا انگلیسی)</label>
        <input type="text" dir="ltr" name="seo_p_<?php echo e($pk); ?>_keywords" value="<?php echo e(seo_pval($seo, $pk, 'keywords')); ?>">
      </div>
    <?php endforeach; ?>
    <p class="hint">برای هر نمونه‌کار و هر مطلب بلاگ هم فیلدهای «عنوان سئو / توضیح سئو / کلمات کلیدی» در فرم ویرایش همان آیتم هست.</p>
  </div>

  <div class="tab-panel" id="tab-identity" hidden>
    <h2 class="form-section-title">هویت (Schema.org Person)</h2>
    <div class="field-row">
      <label class="field-label">آدرس اصلی سایت</label>
      <input type="url" dir="ltr" name="seo_siteUrl" value="<?php echo e(seo_val($seo, 'siteUrl')); ?>" placeholder="https://www.mohusyn.ir">
      <p class="hint">بدون اسلش انتهایی. برای canonical، نقشهٔ سایت و کارت‌های اجتماعی لازم است.</p>
    </div>
    <div class="bi-grid">
      <div class="field-row"><label class="field-label">نام سایت</label><input type="text" dir="ltr" name="seo_siteName" value="<?php echo e(seo_val($seo, 'siteName')); ?>" placeholder="MOHUSYN"></div>
      <div class="field-row"><label class="field-label">نویسنده (author)</label><input type="text" dir="ltr" name="seo_author" value="<?php echo e(seo_val($seo, 'author')); ?>"></div>
      <div class="field-row"><label class="field-label">عنوان شغلی (jobTitle)</label><input type="text" dir="ltr" name="seo_jobTitle" value="<?php echo e(seo_val($seo, 'jobTitle')); ?>" placeholder="Filmmaker, editor and UI/motion designer"></div>
      <div class="field-row"><label class="field-label">شهر</label><input type="text" dir="ltr" name="seo_city" value="<?php echo e(seo_val($seo, 'city')); ?>" placeholder="Tehran"></div>
      <div class="field-row"><label class="field-label">کد کشور</label><input type="text" dir="ltr" name="seo_country" value="<?php echo e(seo_val($seo, 'country')); ?>" placeholder="IR"></div>
      <div class="field-row"><label class="field-label">اکانت X / توییتر</label><input type="text" dir="ltr" name="seo_twitter" value="<?php echo e(seo_val($seo, 'twitter')); ?>" placeholder="@mohusyn"></div>
    </div>
    <div class="field-row">
      <label class="field-label">سایر پروفایل‌ها (sameAs — هر خط یک لینک)</label>
      <textarea dir="ltr" rows="3" name="seo_sameAs" placeholder="https://behance.net/...&#10;https://dribbble.com/..."><?php echo e(seo_val($seo, 'sameAs')); ?></textarea>
      <p class="hint">اینستاگرام، لینکدین، گیت‌هاب و سایت کاری از «معرفی و متن‌ها» خودکار اضافه می‌شوند.</p>
    </div>
    <div class="field-row">
      <label class="field-label">تصویر اشتراک‌گذاری پیش‌فرض (OG image — ۱۲۰۰×۶۳۰)</label>
      <div class="image-field">
        <input type="text" dir="ltr" name="seo_ogImage" data-image-input="ogImage" value="<?php echo e(seo_val($seo, 'ogImage')); ?>" placeholder="/uploads/og.jpg">
        <select data-image-picker data-target="ogImage">
          <option value="">— انتخاب از رسانه‌ها —</option>
          <?php foreach ($media as $m): ?>
            <option value="<?php echo e($m['url']); ?>" <?php echo seo_val($seo, 'ogImage') === $m['url'] ? 'selected' : ''; ?>><?php echo e($m['url']); ?></option>
          <?php endforeach; ?>
        </select>
        <img class="image-preview" data-image-preview="ogImage" src="<?php echo e(seo_val($seo, 'ogImage')); ?>" alt="" <?php echo seo_val($seo, 'ogImage') === '' ? 'hidden' : ''; ?>>
      </div>
    </div>
  </div>

  <div class="tab-panel" id="tab-engines" hidden>
    <h2 class="form-section-title">ثبت در موتورهای جستجو</h2>
    <p class="hint">
      ۱) در <a href="https://search.google.com/search-console" target="_blank" rel="noopener">Google Search Console</a> و
      <a href="https://www.bing.com/webmasters" target="_blank" rel="noopener">Bing Webmaster Tools</a> سایت را اضافه کنید و روش «HTML tag» را انتخاب کنید.
      ۲) فقط مقدار <code>content</code> را اینجا بچسبانید. ۳) بعد از تأیید، آدرس <code dir="ltr">/sitemap.xml</code> را در هر دو ثبت کنید.
    </p>
    <div class="field-row"><label class="field-label">Google Search Console (google-site-verification)</label><input type="text" dir="ltr" name="seo_googleVerify" value="<?php echo e(seo_val($seo, 'googleVerify')); ?>"></div>
    <div class="field-row"><label class="field-label">Bing Webmaster (msvalidate.01)</label><input type="text" dir="ltr" name="seo_bingVerify" value="<?php echo e(seo_val($seo, 'bingVerify')); ?>"></div>
    <div class="field-row"><label class="field-label">Yandex (yandex-verification)</label><input type="text" dir="ltr" name="seo_yandexVerify" value="<?php echo e(seo_val($seo, 'yandexVerify')); ?>"></div>
    <div class="field-row">
      <label class="field-label">کد اضافی در &lt;head&gt; (مثلاً Google Analytics / Tag Manager)</label>
      <textarea dir="ltr" rows="5" name="seo_headExtra" class="mono-area"><?php echo e(seo_val($seo, 'headExtra')); ?></textarea>
    </div>
    <div class="field-row">
      <label class="switch">
        <input type="checkbox" name="seo_noindex" <?php echo !empty($seo['noindex']) ? 'checked' : ''; ?>>
        <span>فعلاً سایت ایندکس نشود (noindex — برای زمان توسعه)</span>
      </label>
    </div>
  </div>

  <div class="form-actions sticky-actions">
    <button class="btn btn-primary" type="submit">ذخیرهٔ تنظیمات سئو</button>
  </div>
</form>
<script>
(function () {
  var btns = document.querySelectorAll('.tabs [data-tab]');
  function show(id) {
    document.querySelectorAll('.tab-panel').forEach(function (p) { p.hidden = p.id !== id; });
    btns.forEach(function (b) { b.classList.toggle('is-on', b.getAttribute('data-tab') === id); });
    try { history.replaceState(null, '', '#' + id); } catch (e) {}
  }
  btns.forEach(function (b) { b.addEventListener('click', function () { show(b.getAttribute('data-tab')); }); });
  var h = location.hash.replace('#', '');
  if (h && document.getElementById(h)) show(h);
})();
</script>
<?php
admin_footer();
