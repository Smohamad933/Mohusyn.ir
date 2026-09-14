<?php
/**
 * Admin — dashboard + generic CRUD for content collections.
 * UI in Persian; content entered here is English (site language).
 */

require __DIR__ . '/_bootstrap.php';
require_auth();

$schemas = admin_schemas();
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
if ($tab !== 'dashboard' && !isset($schemas[$tab])) {
    $tab = 'dashboard';
}

/* ------------------------------------------------------------ POST */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        header('Location: index.php?tab=' . urlencode($tab) . '&msg=csrf');
        exit;
    }

    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $postTab = isset($_POST['tab']) && isset($schemas[$_POST['tab']]) ? $_POST['tab'] : $tab;
    $schema = $schemas[$postTab];
    $items = get_collection($postTab);

    if ($action === 'delete') {
        $delId = isset($_POST['id']) ? $_POST['id'] : '';
        $items = array_values(array_filter($items, function ($it) use ($delId) {
            return !isset($it['id']) || $it['id'] !== $delId;
        }));
        save_json($postTab . '.json', $items);
        header('Location: index.php?tab=' . urlencode($postTab) . '&msg=deleted');
        exit;
    }

    if ($action === 'toggle') {
        $togId = isset($_POST['id']) ? $_POST['id'] : '';
        foreach ($items as &$it) {
            if (isset($it['id']) && $it['id'] === $togId) {
                $it['published'] = empty($it['published']);
                break;
            }
        }
        unset($it);
        save_json($postTab . '.json', $items);
        header('Location: index.php?tab=' . urlencode($postTab) . '&msg=saved');
        exit;
    }

    if ($action === 'save') {
        $editId = isset($_POST['id']) ? trim((string) $_POST['id']) : '';
        $item = null;
        $index = -1;
        foreach ($items as $i => $it) {
            if (isset($it['id']) && $it['id'] === $editId) {
                $item = $it;
                $index = $i;
                break;
            }
        }
        if ($item === null) {
            $item = array('id' => new_id($schema['idPrefix']));
        }

        foreach ($schema['fields'] as $field) {
            $key = $field['key'];
            $item[$key] = isset($_POST['f_' . $key]) ? trim((string) $_POST['f_' . $key]) : '';
        }

        /* projects: auto-generate the case-study slug from the title */
        if ($postTab === 'projects') {
            if (!isset($item['slug']) || trim((string) $item['slug']) === '') {
                $titleForSlug = isset($item['title']) && is_string($item['title']) ? $item['title'] : '';
                $item['slug'] = slugify($titleForSlug);
            }
        }

        /* posts: make sure there is a unique slug */
        if ($postTab === 'posts') {
            $slugRaw = isset($_POST['f_slug']) ? (string) $_POST['f_slug'] : '';
            if (trim($slugRaw) === '' && isset($item['title'])) {
                $slugRaw = is_string($item['title']) ? $item['title'] : '';
            }
            $slug = slugify($slugRaw);
            $base = $slug;
            $n = 2;
            foreach ($items as $i => $it) {
                if ($i === $index) {
                    continue;
                }
                if (isset($it['slug']) && $it['slug'] === $slug) {
                    $slug = $base . '-' . $n;
                    $n++;
                }
            }
            $item['slug'] = $slug;
            if (!isset($item['createdAt']) || $item['createdAt'] === '') {
                $item['createdAt'] = date('Y-m-d');
            }
        }

        $item['published'] = !empty($_POST['f_published']);

        if ($index >= 0) {
            $items[$index] = $item;
        } else {
            $items[] = $item;
        }

        save_json($postTab . '.json', array_values($items));
        header('Location: index.php?tab=' . urlencode($postTab) . '&msg=saved');
        exit;
    }

    header('Location: index.php?tab=' . urlencode($postTab) . '&msg=error');
    exit;
}

/* ------------------------------------------------------------ VIEW */
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$media = media_list();

/* ---------- dashboard ---------- */
if ($tab === 'dashboard') {
    admin_header('dashboard', 'داشبورد');
    admin_flash();

    $allMessages = load_json('messages.json', array());
    $unreadCount = 0;
    foreach ($allMessages as $m) { if (empty($m['read'])) { $unreadCount++; } }
    ?>
    <div class="page-head">
      <h1 class="page-title">داشبورد مدیریت محتوا</h1>
      <div class="page-head-actions">
        <a class="btn btn-ghost" href="/contact/" target="_blank">فرم تماس ↗</a>
        <a class="btn btn-primary" href="/" target="_blank">مشاهدهٔ سایت ↗</a>
      </div>
    </div>
    <p class="page-sub">این پیش‌نمایش همان سایتِ زنده است. برای ویرایش، از کارت‌های «می‌خواهم چه کاری انجام دهم؟» یا منوی کناری استفاده کنید.</p>

    <div class="site-preview">
      <div class="preview-bar">
        <span class="preview-dots"><i></i><i></i><i></i></span>
        <span class="preview-url mono"><?php $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''; echo e($host !== '' ? $host . '/' : '/'); ?></span>
        <button class="btn btn-mini" type="button" onclick="var f=document.getElementById('site-frame'); f.src=f.getAttribute('data-src');">↻ به‌روزرسانی پیش‌نمایش</button>
        <a class="btn btn-mini" href="/" target="_blank" rel="noopener">بازکردن در تب جدید ↗</a>
      </div>
      <iframe id="site-frame" data-src="/" src="/" title="پیش‌نمایش سایت" loading="lazy"></iframe>
    </div>

    <h2 class="hub-group">می‌خواهم چه کاری انجام دهم؟</h2>
    <div class="hub">
      <a class="hub-card" href="settings.php"><span class="hub-icon">👤</span><strong>نام، بیو و عکس هدر را عوض کنم</strong><p>متن معرفی بالای صفحه، عکس بنر، دکمه‌ها و ایمیل تماس.</p><span class="mono">معرفی و متن‌ها</span></a>
      <a class="hub-card" href="index.php?tab=projects&action=edit"><span class="hub-icon">▦</span><strong>نمونه‌کار جدید اضافه کنم</strong><p>عنوان، دسته، کاور (نسبت ۵:۴، خودکار سیاه‌وسفید) و متن کیس‌استادی.</p><span class="mono">پروژه‌ها</span></a>
      <a class="hub-card" href="blocks.php"><span class="hub-icon">⬒</span><strong>به صفحه بخش دلخواه اضافه کنم</strong><p>تیتر، متن، عکس یا دکمه را روی پیش‌نمایش زندهٔ سایت بکشید و رها کنید؛ بعد پیش‌نویس یا منتشر کنید.</p><span class="mono">بلوک‌ساز بصری</span></a>
      <a class="hub-card" href="pages.php"><span class="hub-icon">▣</span><strong>بخشی از صفحه را مخفی کنم</strong><p>مثلاً بلاگ یا نوار مهارت‌ها را خاموش/روشن کنید.</p><span class="mono">بخش‌های صفحات</span></a>
      <a class="hub-card" href="messages.php"><span class="hub-icon">✉</span><strong>پیام‌های مشتری‌ها را ببینم</strong><p><?php echo e(fa_digits($unreadCount)); ?> پیام خوانده‌نشده از فرم «شروع پروژه».</p><span class="mono">پیام‌ها</span></a>
      <a class="hub-card" href="settings.php#tab-fonts"><span class="hub-icon">Aa</span><strong>فونت سایت را تغییر دهم</strong><p>فونت متن‌های کوچک و فونت تیترها (Doto) — گوگل‌فونت یا آپلود فایل.</p><span class="mono">فونت‌ها</span></a>
      <a class="hub-card" href="index.php?tab=posts&action=edit"><span class="hub-icon">✎</span><strong>مطلب بلاگ بنویسم</strong><p>تا اولین مطلب منتشر نشود، منوی بلاگ در سایت دیده نمی‌شود.</p><span class="mono">نوشته‌ها</span></a>
      <a class="hub-card" href="media.php"><span class="hub-icon">▤</span><strong>عکس آپلود کنم</strong><p>کتابخانهٔ همهٔ تصاویر؛ کنار هر فیلد تصویر هم دکمهٔ آپلود هست.</p><span class="mono">رسانه‌ها</span></a>
    </div>

    <h2 class="hub-group">وضعیت محتوا</h2>
    <div class="cards">
      <?php foreach ($schemas as $name => $schema):
          $all = get_collection($name);
          $pub = 0;
          foreach ($all as $it) { if (!empty($it['published'])) { $pub++; } } ?>
        <a class="stat-card" href="index.php?tab=<?php echo e($name); ?>">
          <span class="stat-num"><?php echo e(count($all)); ?></span>
          <span class="stat-label"><?php echo e($schema['label']); ?></span>
          <span class="stat-pub mono"><?php echo e($pub); ?> منتشرشده</span>
        </a>
      <?php endforeach; ?>
      <a class="stat-card" href="messages.php">
        <span class="stat-num"><?php echo e(count($allMessages)); ?></span>
        <span class="stat-label">پیام‌های تماس</span>
        <span class="stat-pub mono"><?php echo e($unreadCount); ?> خوانده‌نشده</span>
      </a>
    </div>

    <?php if (ADMIN_PASSWORD === 'mohusyn2026'): ?>
      <div class="flash flash-error">
        ⚠ هنوز از رمز پیش‌فرض استفاده می‌کنید. لطفاً رمز پنل را در فایل <code>config.php</code> تغییر دهید.
      </div>
    <?php endif; ?>

    <div class="dash-cols">
      <div class="dash-panel">
        <div class="dash-panel-head">
          <h2>آخرین پیام‌ها</h2>
          <a class="btn btn-mini" href="messages.php">همهٔ پیام‌ها</a>
        </div>
        <?php if (empty($allMessages)): ?>
          <p class="empty-note">هنوز پیامی از فرم تماس دریافت نشده است.</p>
        <?php else: ?>
          <?php foreach (array_slice($allMessages, 0, 4) as $m): ?>
            <a class="msg-row" href="messages.php">
              <span class="msg-dot <?php echo empty($m['read']) ? 'unread' : ''; ?>"></span>
              <span class="msg-row-body">
                <strong dir="ltr"><?php echo e($m['name']); ?></strong>
                <small dir="ltr"><?php echo e($m['email']); ?></small>
              </span>
              <span class="msg-row-date mono"><?php echo e($m['createdAt']); ?></span>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="tips">
        <h2>چطور کار می‌کند؟</h2>
        <ul>
          <li><strong>محتوای سایت</strong> (ستون کناری): هر چیزی که در سایت نوشته شده از همین‌جا عوض می‌شود.</li>
          <li><strong>پیش‌نویس</strong> یعنی فقط شما می‌بینید؛ <strong>منتشرشده</strong> یعنی روی سایت است.</li>
          <li><strong>بلوک‌ساز بصری</strong>: خودِ سایت را می‌بینید و بخش‌ها را روی آن می‌کشید؛ تا «انتشار» نزنید سایت تغییر نمی‌کند.</li>
          <li>سایت انگلیسی است؛ متن‌ها را انگلیسی وارد کنید. پنل فارسی است.</li>
        </ul>
      </div>
    </div>
    <?php
    admin_footer();
    exit;
}

/* ---------- edit form ---------- */
if ($action === 'edit') {
    $schema = $schemas[$tab];
    $editId = isset($_GET['id']) ? $_GET['id'] : '';
    $item = array();
    foreach (get_collection($tab) as $it) {
        if ($editId !== '' && isset($it['id']) && $it['id'] === $editId) {
            $item = $it;
            break;
        }
    }
    $isNew = empty($item);

    admin_header($tab, $isNew ? 'افزودن' : 'ویرایش');
    admin_flash();
    ?>
    <div class="page-head">
      <h1 class="page-title"><?php echo $isNew ? 'افزودن' : 'ویرایش'; ?> — <?php echo e($schema['label']); ?></h1>
      <a class="btn btn-ghost" href="index.php?tab=<?php echo e($tab); ?>">بازگشت به فهرست</a>
    </div>

    <form method="post" class="admin-form" action="index.php">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="tab" value="<?php echo e($tab); ?>">
      <input type="hidden" name="id" value="<?php echo e(isset($item['id']) ? $item['id'] : ''); ?>">

      <div class="field-row">
        <label class="switch">
          <input type="checkbox" name="f_published" <?php echo !empty($item['published']) ? 'checked' : ''; ?>>
          <span>انتشار در سایت</span>
        </label>
      </div>

      <?php foreach ($schema['fields'] as $field):
          $key = $field['key'];
          $type = $field['type'];
          $val = isset($item[$key]) ? $item[$key] : '';
          if (is_array($val)) { $val = bi($val, 'en'); } /* legacy bilingual data */
          ?>
        <div class="field-row">
          <label class="field-label"><?php echo e($field['label']); ?> <span class="en-hint">(به انگلیسی)</span></label>
          <?php if (isset($field['hint'])): ?>
            <p class="hint"><?php echo e($field['hint']); ?></p>
          <?php endif; ?>

          <?php if ($type === 'textarea'): ?>
            <textarea dir="ltr" rows="4" name="f_<?php echo e($key); ?>"><?php echo e($val); ?></textarea>

          <?php elseif ($type === 'date'): ?>
            <input type="date" dir="ltr" name="f_<?php echo e($key); ?>"
                   value="<?php echo e($val !== '' ? $val : date('Y-m-d')); ?>">

          <?php elseif ($type === 'image'): ?>
            <div class="image-field">
              <input type="text" dir="ltr" name="f_<?php echo e($key); ?>"
                     data-image-input="<?php echo e($key); ?>"
                     value="<?php echo e($val); ?>" placeholder="/uploads/... یا /assets/images/...">
              <select data-image-picker data-target="<?php echo e($key); ?>">
                <option value="">— انتخاب از رسانه‌ها —</option>
                <?php foreach ($media as $m): ?>
                  <option value="<?php echo e($m['url']); ?>" <?php echo $val === $m['url'] ? 'selected' : ''; ?>>
                    <?php echo e($m['url']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if ($val !== ''): ?>
                <img class="image-preview" data-image-preview="<?php echo e($key); ?>" src="<?php echo e($val); ?>" alt="">
              <?php else: ?>
                <img class="image-preview" data-image-preview="<?php echo e($key); ?>" src="" alt="" hidden>
              <?php endif; ?>
            </div>

          <?php else: /* text / url */ ?>
            <input type="<?php echo $type === 'url' ? 'url' : 'text'; ?>" dir="ltr"
                   name="f_<?php echo e($key); ?>" value="<?php echo e($val); ?>">
          <?php endif; ?>
        </div>
      <?php endforeach; ?>

      <div class="form-actions">
        <button class="btn btn-primary" type="submit">ذخیره</button>
        <a class="btn btn-ghost" href="index.php?tab=<?php echo e($tab); ?>">انصراف</a>
      </div>
    </form>
    <?php
    admin_footer();
    exit;
}

/* ---------- list ---------- */
$schema = $schemas[$tab];
$items = get_collection($tab);

admin_header($tab, $schema['label']);
admin_flash();
?>
<div class="page-head">
  <h1 class="page-title"><?php echo e($schema['label']); ?> <span class="count mono"><?php echo e(count($items)); ?></span></h1>
  <a class="btn btn-primary" href="index.php?tab=<?php echo e($tab); ?>&action=edit">+ افزودن</a>
</div>

<?php if (empty($items)): ?>
  <p class="empty-note">موردی وجود ندارد. با دکمهٔ «افزودن» اولین آیتم را بسازید.</p>
<?php else: ?>
<table class="admin-table">
  <thead>
    <tr>
      <th>عنوان</th>
      <th>وضعیت</th>
      <th>عملیات</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($items as $it):
        $tf = $schema['titleField'];
        $title = isset($it[$tf]) ? bi($it[$tf], 'en') : (isset($it['id']) ? $it['id'] : '—');
        ?>
      <tr>
        <td class="td-title" dir="ltr" style="text-align:right;">
          <?php echo e($title); ?>
          <?php if ($tab === 'posts' && isset($it['slug'])): ?>
            <span class="mono slug-hint">/blog/<?php echo e($it['slug']); ?></span>
          <?php endif; ?>
        </td>
        <td>
          <?php if (!empty($it['published'])): ?>
            <span class="badge badge-pub">منتشرشده</span>
          <?php else: ?>
            <span class="badge badge-draft">پیش‌نویس</span>
          <?php endif; ?>
        </td>
        <td class="td-actions">
          <a class="btn btn-mini" href="index.php?tab=<?php echo e($tab); ?>&action=edit&id=<?php echo e($it['id']); ?>">ویرایش</a>
          <form method="post" class="inline-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="tab" value="<?php echo e($tab); ?>">
            <input type="hidden" name="id" value="<?php echo e($it['id']); ?>">
            <button class="btn btn-mini" type="submit"><?php echo !empty($it['published']) ? 'لغو انتشار' : 'انتشار'; ?></button>
          </form>
          <form method="post" class="inline-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="tab" value="<?php echo e($tab); ?>">
            <input type="hidden" name="id" value="<?php echo e($it['id']); ?>">
            <button class="btn btn-mini btn-danger" type="submit" data-confirm="این آیتم برای همیشه حذف شود؟">حذف</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
<?php
admin_footer();
