<?php
/**
 * Admin — pages control: titles and section visibility per page.
 * UI in Persian; titles entered here are English (site language).
 */

require __DIR__ . '/_bootstrap.php';
require_auth();

$pageDefs = array(
    'home' => array(
        'label' => 'صفحهٔ اصلی (لندینگ)',
        'sections' => array(
            'showProjects' => 'نمایش گرید پروژه‌ها (پورتفولیو)',
            'showCollaborators' => 'نمایش بخش همکاران',
            'showServices' => 'نمایش بخش توانمندی‌ها',
            'showSkills' => 'نمایش نوار مهارت‌ها',
            'showAvailability' => 'نمایش کارت وضعیت همکاری',
            'showPosts' => 'نمایش نوشته‌های اخیر',
        ),
    ),
    'about' => array(
        'label' => 'دربارهٔ من',
        'hasTitle' => true,
        'sections' => array(
            'showExperiences' => 'نمایش تجربه‌ها',
            'showCollaborators' => 'نمایش بخش همکاران',
            'showSkills' => 'نمایش نوار مهارت‌ها',
        ),
    ),
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        header('Location: pages.php?msg=csrf');
        exit;
    }

    $settings = get_settings();
    if (!isset($settings['pages']) || !is_array($settings['pages'])) {
        $settings['pages'] = array();
    }

    foreach ($pageDefs as $pageKey => $def) {
        if (!isset($settings['pages'][$pageKey]) || !is_array($settings['pages'][$pageKey])) {
            $settings['pages'][$pageKey] = array();
        }
        foreach ($def['sections'] as $sectionKey => $sectionLabel) {
            $settings['pages'][$pageKey][$sectionKey] = !empty($_POST['pg_' . $pageKey . '_' . $sectionKey]);
        }
        if (!empty($def['hasTitle'])) {
            $settings['pages'][$pageKey]['title'] = isset($_POST['pg_' . $pageKey . '_title']) ? trim((string) $_POST['pg_' . $pageKey . '_title']) : '';
            $settings['pages'][$pageKey]['subtitle'] = isset($_POST['pg_' . $pageKey . '_subtitle']) ? trim((string) $_POST['pg_' . $pageKey . '_subtitle']) : '';
        }
    }

    save_json('settings.json', $settings);
    header('Location: pages.php?msg=saved');
    exit;
}

$settings = get_settings();
$pages = isset($settings['pages']) ? $settings['pages'] : array();

admin_header('pages', 'صفحات');
admin_flash();
?>
<h1 class="page-title">بخش‌های صفحات</h1>
<p class="page-sub">هر تیک یک بخش از صفحه را روشن یا خاموش می‌کند (مثلاً اگر فعلاً بلاگ نمی‌خواهید، تیکش را بردارید). برای افزودن بخش جدید از «بلوک‌ساز بصری» استفاده کنید.</p>

<form method="post" class="admin-form" action="pages.php">
  <?php echo csrf_field(); ?>

  <?php foreach ($pageDefs as $pageKey => $def):
      $pageData = isset($pages[$pageKey]) ? $pages[$pageKey] : array(); ?>
    <div class="page-block">
      <h2 class="form-section-title form-section-first"><?php echo e($def['label']); ?></h2>

      <?php if (!empty($def['hasTitle'])):
          $title = isset($pageData['title']) ? $pageData['title'] : '';
          if (is_array($title)) { $title = bi($title, 'en'); }
          $subtitle = isset($pageData['subtitle']) ? $pageData['subtitle'] : '';
          if (is_array($subtitle)) { $subtitle = bi($subtitle, 'en'); } ?>
        <div class="field-row">
          <label class="field-label">عنوان صفحه <span class="en-hint">(به انگلیسی)</span></label>
          <p class="hint">تیتر بزرگ بالای صفحه و عنوان تب مرورگر.</p>
          <input type="text" dir="ltr" name="pg_<?php echo e($pageKey); ?>_title" value="<?php echo e($title); ?>">
        </div>
        <div class="field-row">
          <label class="field-label">زیرعنوان <span class="en-hint">(به انگلیسی)</span></label>
          <p class="hint">یک خط توضیح زیر تیتر؛ خالی = حذف.</p>
          <input type="text" dir="ltr" name="pg_<?php echo e($pageKey); ?>_subtitle" value="<?php echo e($subtitle); ?>">
        </div>
      <?php endif; ?>

      <div class="field-row">
        <label class="field-label">بخش‌های صفحه</label>
        <p class="hint">هر بخش را خاموش کنید تا از این صفحه حذف شود؛ محتوای آن پاک نمی‌شود.</p>
        <div class="toggles-grid">
          <?php foreach ($def['sections'] as $sectionKey => $sectionLabel):
              $checked = !empty($pageData[$sectionKey]); ?>
            <label class="switch">
              <input type="checkbox" name="pg_<?php echo e($pageKey); ?>_<?php echo e($sectionKey); ?>"
                     <?php echo $checked ? 'checked' : ''; ?>>
              <span><?php echo e($sectionLabel); ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <div class="form-actions">
    <button class="btn btn-primary" type="submit">ذخیره</button>
  </div>
</form>
<?php
admin_footer();
