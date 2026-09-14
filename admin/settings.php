<?php
/**
 * Admin — site settings (hero texts, section headings, links, fonts).
 * UI in Persian; content entered here is English (site language).
 */

require __DIR__ . '/_bootstrap.php';
require_auth();

/* Field definitions: [key, label, kind] kind = text | textarea | url | image */
$basicFields = array(
    array('brand', 'نام برند', 'text'),
    array('tagline', 'شعار کوتاه (تگ‌لاین)', 'text'),
    array('shortName', 'نام نمایشی کوتاه', 'text'),
    array('fullName', 'نام کامل', 'text'),
    array('bio', 'متن معرفی (بیو)', 'textarea'),
    array('availability', 'متن وضعیت همکاری', 'text'),
    array('cta', 'متن دکمهٔ اصلی', 'text'),
    array('heroImage', 'تصویر هرو (بنر عریض با نسبت 5:2 — چهره در سمت چپ قاب قرار بگیرد؛ متن و گرادیان سمت راست روی آن می‌نشینند)', 'image'),
    array('profileImage', 'تصویر پروفایل (فقط برای هدر صفحهٔ اصلی وقتی «تصویر هرو» خالی است)', 'image'),
    array('aboutImage', 'تصویر صفحهٔ «دربارهٔ من» (مربعی؛ جدا از هدر صفحهٔ اصلی)', 'image'),
    array('workSiteUrl', 'آدرس سایت کاری', 'url'),
    array('workSiteLabel', 'متن لینک سایت کاری', 'text'),
    array('contactEmail', 'ایمیل تماس', 'text'),
    array('instagram', 'اینستاگرام', 'url'),
    array('linkedin', 'لینکدین', 'url'),
    array('github', 'گیت‌هاب', 'url'),
);

/* Availability card fields: [key, label] */
$availFields = array(
    array('availTitle', 'عنوان کارت (مثلاً Current Availability)'),
    array('availPeriod', 'دوره (مثلاً Q3 2026)'),
    array('availSpots', 'ظرفیت (مثلاً 2 Spots Left)'),
    array('availCta', 'متن دکمه (مثلاً Apply for a Project)'),
);

$sectionFields = array(
    array('collaborators', 'عنوان بخش همکاران'),
    array('collaboratorsIntro', 'متن معرفی همکاران'),
    array('experienceKicker', 'تیتر کوچک تجربه‌ها'),
    array('experienceTitle', 'تیتر تجربه‌ها'),
    array('experienceSubtitle', 'زیرتیتر تجربه‌ها'),
    array('servicesKicker', 'تیتر کوچک خدمات'),
    array('servicesTitle', 'تیتر خدمات'),
    array('servicesSubtitle', 'زیرتیتر خدمات'),
    array('skillsKicker', 'تیتر بخش مهارت‌ها'),
    array('footerTitle', 'تیتر فوتر'),
    array('footerSubtitle', 'زیرتیتر فوتر'),
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        header('Location: settings.php?msg=csrf');
        exit;
    }

    $settings = get_settings();

    foreach ($basicFields as $def) {
        $key = $def[0];
        $settings[$key] = isset($_POST['s_' . $key]) ? trim((string) $_POST['s_' . $key]) : '';
    }

    foreach ($availFields as $def) {
        $key = $def[0];
        $settings[$key] = isset($_POST['s_' . $key]) ? trim((string) $_POST['s_' . $key]) : '';
    }

    if (!isset($settings['sections']) || !is_array($settings['sections'])) {
        $settings['sections'] = array();
    }
    foreach ($sectionFields as $def) {
        $key = $def[0];
        $settings['sections'][$key] = isset($_POST['sec_' . $key]) ? trim((string) $_POST['sec_' . $key]) : '';
    }

    /* fonts */
    $fontKeys = array('cssUrl', 'enFamily', 'enFile');
    $fonts = isset($settings['fonts']) && is_array($settings['fonts']) ? $settings['fonts'] : array();
    foreach ($fontKeys as $fk) {
        $fonts[$fk] = isset($_POST['s_font_' . $fk]) ? trim((string) $_POST['s_font_' . $fk]) : '';
    }
    $settings['fonts'] = $fonts;

    /* per-tag fonts (h1..h4, p) */
    $tagFonts = array();
    foreach (array('h1', 'h2', 'h3', 'h4', 'p') as $tag) {
        $mode = isset($_POST['s_tf_' . $tag . '_mode']) ? (string) $_POST['s_tf_' . $tag . '_mode'] : 'default';
        if (!in_array($mode, array('default', 'uploaded', 'custom'), true)) {
            $mode = 'default';
        }
        $family = isset($_POST['s_tf_' . $tag . '_family']) ? trim((string) $_POST['s_tf_' . $tag . '_family']) : '';
        $tagFonts[$tag] = array('mode' => $mode, 'family' => $family);
    }
    $settings['tagFonts'] = $tagFonts;

    /* footer admin link visibility */
    $settings['showAdminLink'] = !empty($_POST['s_showAdminLink']);

    /* custom buttons on the About page */
    $aboutButtons = array();
    $labels = isset($_POST['ab_label']) && is_array($_POST['ab_label']) ? $_POST['ab_label'] : array();
    $urls = isset($_POST['ab_url']) && is_array($_POST['ab_url']) ? $_POST['ab_url'] : array();
    $styles = isset($_POST['ab_style']) && is_array($_POST['ab_style']) ? $_POST['ab_style'] : array();
    $news = isset($_POST['ab_new']) && is_array($_POST['ab_new']) ? $_POST['ab_new'] : array();
    foreach ($labels as $i => $lbl) {
        $lbl = trim((string) $lbl);
        $url = isset($urls[$i]) ? trim((string) $urls[$i]) : '';
        if ($lbl === '' || $url === '') {
            continue;
        }
        $aboutButtons[] = array(
            'label' => $lbl,
            'url' => $url,
            'style' => isset($styles[$i]) && $styles[$i] === 'solid' ? 'solid' : 'outline',
            'newTab' => !empty($news[$i]),
        );
    }
    $settings['aboutButtons'] = $aboutButtons;
    /* portfolio archive + blog menu */
    $settings['showBlogInMenu'] = !empty($_POST['s_showBlogInMenu']);
    $settings['portfolioTitle'] = isset($_POST['s_portfolioTitle']) ? trim((string) $_POST['s_portfolioTitle']) : '';
    $settings['portfolioSubtitle'] = isset($_POST['s_portfolioSubtitle']) ? trim((string) $_POST['s_portfolioSubtitle']) : '';
    $lim = isset($_POST['s_homeProjectsLimit']) ? (int) $_POST['s_homeProjectsLimit'] : 6;
    $settings['homeProjectsLimit'] = max(2, min(40, $lim));

    save_json('settings.json', $settings);
    header('Location: settings.php?msg=saved');
    exit;
}

$settings = get_settings();
$media = media_list();

admin_header('settings', 'تنظیمات سایت');
admin_flash();
?>
<h1 class="page-title">معرفی و متن‌های سایت</h1>
<p class="page-sub">همهٔ متن‌های ثابت سایت و تنظیمات ظاهری اینجاست. زبان سایت انگلیسی است؛ متن‌ها را به انگلیسی وارد کنید.</p>

<div class="tabs" role="tablist">
  <button type="button" class="is-on" data-tab="tab-intro">👤 معرفی و هدر</button>
  <button type="button" data-tab="tab-texts">✎ تیترها و متن بخش‌ها</button>
  <button type="button" data-tab="tab-fonts">Aa فونت‌ها</button>
  <button type="button" data-tab="tab-misc">⚙ سایر</button>
</div>

<form method="post" class="admin-form" action="settings.php">
  <?php echo csrf_field(); ?>

  <div class="tab-panel" id="tab-intro">
  <h2 class="form-section-title">هویت و معرفی</h2>
  <p class="hint">نام، بیوی کوتاه، عکس بنر بالای صفحه (چهره سمت چپ عکس باشد)، دکمه‌ها و اطلاعات تماس.</p>

  <?php foreach ($basicFields as $def):
      $key = $def[0];
      $label = $def[1];
      $kind = $def[2];
      $val = isset($settings[$key]) ? $settings[$key] : '';
      if (is_array($val)) { $val = bi($val, 'en'); } /* legacy bilingual data */
      ?>
    <div class="field-row">
      <label class="field-label"><?php echo e($label); ?> <span class="en-hint">(به انگلیسی)</span></label>

      <?php if ($kind === 'textarea'): ?>
        <textarea dir="ltr" rows="3" name="s_<?php echo e($key); ?>"><?php echo e($val); ?></textarea>

      <?php elseif ($kind === 'image'): ?>
        <div class="image-field">
          <input type="text" dir="ltr" name="s_<?php echo e($key); ?>"
                 data-image-input="<?php echo e($key); ?>" value="<?php echo e($val); ?>">
          <select data-image-picker data-target="<?php echo e($key); ?>">
            <option value="">— انتخاب از رسانه‌ها —</option>
            <?php foreach ($media as $m): ?>
              <option value="<?php echo e($m['url']); ?>" <?php echo $val === $m['url'] ? 'selected' : ''; ?>>
                <?php echo e($m['url']); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <img class="image-preview" data-image-preview="<?php echo e($key); ?>"
               src="<?php echo e($val); ?>" alt="" <?php echo $val === '' ? 'hidden' : ''; ?>>
        </div>

      <?php else: ?>
        <input type="<?php echo $kind === 'url' ? 'url' : 'text'; ?>" dir="ltr"
               name="s_<?php echo e($key); ?>" value="<?php echo e($val); ?>">
      <?php endif; ?>
    </div>
  <?php endforeach; ?>


  <h2 class="form-section-title">دکمه‌های صفحهٔ «دربارهٔ من»</h2>
  <p class="hint">کنار دکمهٔ «سایت کاری» هر تعداد دکمهٔ دلخواه با نام و لینک خودتان بسازید (مثلاً رزومه، اینستاگرام، تلگرام). ردیف خالی نادیده گرفته می‌شود.</p>
  <div id="about-buttons">
    <?php
    $aboutButtons = isset($settings['aboutButtons']) && is_array($settings['aboutButtons']) ? $settings['aboutButtons'] : array();
    $aboutButtons[] = array('label' => '', 'url' => '', 'style' => 'outline', 'newTab' => true); /* one empty row */
    foreach ($aboutButtons as $ab): ?>
      <div class="ab-row">
        <input type="text" dir="ltr" name="ab_label[]" placeholder="Button text (e.g. Resume)" value="<?php echo e($ab['label']); ?>">
        <input type="text" dir="ltr" name="ab_url[]" placeholder="https://… or /contact/" value="<?php echo e($ab['url']); ?>">
        <select name="ab_style[]">
          <option value="outline" <?php echo $ab['style'] !== 'solid' ? 'selected' : ''; ?>>خطی</option>
          <option value="solid" <?php echo $ab['style'] === 'solid' ? 'selected' : ''; ?>>توپر</option>
        </select>
        <label class="switch switch-sm"><input type="checkbox" name="ab_new[]" value="1" <?php echo !empty($ab['newTab']) ? 'checked' : ''; ?>><span>تب جدید</span></label>
        <button type="button" class="btn btn-mini btn-danger" data-ab-remove title="حذف">✕</button>
      </div>
    <?php endforeach; ?>
  </div>
  <button type="button" class="btn btn-mini" id="ab-add">+ دکمهٔ جدید</button>
  </div>

  <div class="tab-panel" id="tab-texts" hidden>
  <h2 class="form-section-title">کارت «وضعیت همکاری» (Availability)</h2>
  <?php foreach ($availFields as $def):
      $akey = $def[0]; ?>
    <div class="field-row">
      <label class="field-label"><?php echo e($def[1]); ?> <span class="en-hint">(به انگلیسی)</span></label>
      <input type="text" dir="ltr" name="s_<?php echo e($akey); ?>"
             value="<?php echo e(isset($settings[$akey]) && is_string($settings[$akey]) ? $settings[$akey] : ''); ?>">
    </div>
  <?php endforeach; ?>

  <h2 class="form-section-title">پورتفولیو (صفحهٔ /work/)</h2>
  <p class="hint">همهٔ نمونه‌کارها در صفحهٔ «Portfolio» با فیلتر دسته‌بندی نمایش داده می‌شوند؛ صفحهٔ اصلی فقط تعداد محدودی را نشان می‌دهد و دکمهٔ «View all» دارد.</p>
  <div class="bi-grid">
    <div class="field-row">
      <label class="field-label">عنوان صفحهٔ پورتفولیو <span class="en-hint">(به انگلیسی)</span></label>
      <input type="text" dir="ltr" name="s_portfolioTitle" value="<?php echo e(setting('portfolioTitle', 'Selected Work')); ?>">
    </div>
    <div class="field-row">
      <label class="field-label">تعداد نمونه‌کار در صفحهٔ اصلی</label>
      <input type="number" dir="ltr" min="2" max="40" name="s_homeProjectsLimit" value="<?php echo e((int) setting('homeProjectsLimit', 6)); ?>">
    </div>
  </div>
  <div class="field-row">
    <label class="field-label">زیرعنوان صفحهٔ پورتفولیو <span class="en-hint">(به انگلیسی)</span></label>
    <input type="text" dir="ltr" name="s_portfolioSubtitle" value="<?php echo e(setting('portfolioSubtitle', 'Websites, brands and motion — every project, in one place.')); ?>">
  </div>
  <div class="field-row">
    <label class="switch">
      <input type="checkbox" name="s_showBlogInMenu" <?php echo !empty($settings['showBlogInMenu']) ? 'checked' : ''; ?>>
      <span>نمایش «Blog» در منوی سایت (به‌جای آن «Portfolio» همیشه در منو هست)</span>
    </label>
  </div>

  <h2 class="form-section-title">تیتر بخش‌های صفحهٔ نخست</h2>

  <?php foreach ($sectionFields as $def):
      $key = $def[0];
      $label = $def[1];
      $val = isset($settings['sections'][$key]) ? $settings['sections'][$key] : '';
      if (is_array($val)) { $val = bi($val, 'en'); }
      ?>
    <div class="field-row">
      <label class="field-label"><?php echo e($label); ?> <span class="en-hint">(به انگلیسی)</span></label>
      <input type="text" dir="ltr" name="sec_<?php echo e($key); ?>" value="<?php echo e($val); ?>">
    </div>
  <?php endforeach; ?>

  </div>

  <div class="tab-panel" id="tab-fonts" hidden>
  <h2 class="form-section-title">فونت‌ها</h2>
  <p class="hint">تیترها و متن‌های بولد با فونت Doto نمایش داده می‌شوند؛ اینجا فونت متن‌های کوچک را تعیین می‌کنید.</p>
  <?php
  $fonts = isset($settings['fonts']) ? $settings['fonts'] : array();
  $fontFields = array(
      array('cssUrl', 'لینک CSS فونت خارجی (اختیاری)', 'url'),
      array('enFamily', 'نام فونت متن‌های ریز (پیش‌فرض: Space Grotesk) — تیترهای بولد همیشه Doto هستند', 'text'),
      array('enFile', 'فایل فونت سفارشی (آپلود کنید یا آدرس بدهید)', 'font'),
  );
  foreach ($fontFields as $ff):
      $fk = $ff[0];
      $fval = isset($fonts[$fk]) ? $fonts[$fk] : '';
      if (is_array($fval)) { $fval = ''; }
      ?>
    <div class="field-row">
      <label class="field-label"><?php echo e($ff[1]); ?></label>
      <input type="text" dir="ltr" name="s_font_<?php echo e($fk); ?>"
             value="<?php echo e($fval); ?>"
             <?php echo $ff[2] === 'font' ? 'data-font-upload="1"' : ''; ?>>
      <?php if ($ff[2] === 'font'): ?>
        <p class="hint">فرمت‌های مجاز: woff2, woff, ttf — بعد از آپلود، فونت به‌صورت خودکار روی سایت اعمال می‌شود.</p>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

  <h2 class="form-section-title">فونت تگ‌ها (h1 تا h4 و p)</h2>
  <p class="hint">برای هر تگ می‌توانید «فونت پیش‌فرض سایت»، «فونت آپلودشده» یا «نام دلخواه» را انتخاب کنید. اگر فونت گوگلی می‌خواهید، لینک CSS آن را در فیلد «لینک CSS فونت خارجی» بالا بگذارید و نامش را اینجا بنویسید — مثلاً: <span dir="ltr" style="display:inline-block;">'Playfair Display', serif</span></p>

  <?php
  $tagFonts = isset($settings['tagFonts']) ? $settings['tagFonts'] : array();
  $tagLabels = array(
      'h1' => 'تیتر ۱ (h1)',
      'h2' => 'تیتر ۲ (h2)',
      'h3' => 'تیتر ۳ (h3)',
      'h4' => 'تیتر ۴ (h4)',
      'p' => 'متن بدنه (p)',
  );
  foreach ($tagLabels as $tag => $tlabel):
      $tf = isset($tagFonts[$tag]) && is_array($tagFonts[$tag]) ? $tagFonts[$tag] : array();
      $mode = isset($tf['mode']) ? $tf['mode'] : 'default';
      $fam = isset($tf['family']) ? $tf['family'] : '';
      ?>
    <div class="field-row">
      <label class="field-label">فونت <?php echo e($tlabel); ?></label>
      <div class="tagfont-grid">
        <select name="s_tf_<?php echo e($tag); ?>_mode">
          <option value="default" <?php echo $mode === 'default' ? 'selected' : ''; ?>>فونت پیش‌فرض سایت</option>
          <option value="uploaded" <?php echo $mode === 'uploaded' ? 'selected' : ''; ?>>فونت آپلودشده</option>
          <option value="custom" <?php echo $mode === 'custom' ? 'selected' : ''; ?>>نام دلخواه</option>
        </select>
        <input type="text" dir="ltr" name="s_tf_<?php echo e($tag); ?>_family"
               value="<?php echo e($fam); ?>" placeholder="'Playfair Display', serif">
      </div>
    </div>
  <?php endforeach; ?>

  </div>

  <div class="tab-panel" id="tab-misc" hidden>
  <h2 class="form-section-title">CSS سفارشی</h2>
  <div class="field-row">
    <p class="hint">ویرایشگر کامل و تمام‌صفحهٔ CSS در بخش جداگانه قرار دارد.</p>
    <a class="btn btn-ghost" href="css.php">🎨 بازکردن ویرایشگر CSS</a>
  </div>

  <h2 class="form-section-title">نمایش</h2>
  <div class="field-row">
    <div class="toggles-grid">
      <label class="switch">
        <input type="checkbox" name="s_showAdminLink"
               <?php echo !empty($settings['showAdminLink']) ? 'checked' : ''; ?>>
        <span>نمایش لینک «مدیریت» در فوتر سایت</span>
      </label>
    </div>
    <p class="hint">اگر غیرفعال باشد، لینک ورود به پنل از فوتر حذف می‌شود (خود پنل همیشه از آدرس /admin/ در دسترس است).</p>
  </div>

  </div>

  <div class="form-actions sticky-actions">
    <button class="btn btn-primary" type="submit">ذخیرهٔ تنظیمات</button>
    <a class="btn btn-ghost" href="/" target="_blank" rel="noopener">دیدن سایت ↗</a>
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

  /* about-page buttons repeater */
  var abBox = document.getElementById('about-buttons');
  var abAdd = document.getElementById('ab-add');
  if (abBox && abAdd) {
    abAdd.addEventListener('click', function () {
      var rows = abBox.querySelectorAll('.ab-row');
      var clone = rows[rows.length - 1].cloneNode(true);
      clone.querySelectorAll('input[type="text"]').forEach(function (i) { i.value = ''; });
      clone.querySelector('select').value = 'outline';
      clone.querySelector('input[type="checkbox"]').checked = true;
      abBox.appendChild(clone);
      clone.querySelector('input').focus();
    });
    abBox.addEventListener('click', function (ev) {
      var btn = ev.target.closest('[data-ab-remove]');
      if (!btn) return;
      var rows = abBox.querySelectorAll('.ab-row');
      if (rows.length > 1) btn.closest('.ab-row').remove();
      else { btn.closest('.ab-row').querySelectorAll('input[type="text"]').forEach(function (i) { i.value = ''; }); }
    });
  }
  var h = location.hash.replace('#', '');
  if (h && document.getElementById(h) && document.getElementById(h).classList.contains('tab-panel')) show(h);
  /* if the server reports a validation problem, reveal every panel so nothing is hidden */
})();
</script>
<?php
admin_footer();
