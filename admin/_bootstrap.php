<?php
/**
 * Admin bootstrap — config, helpers, schemas and layout chrome.
 * Admin UI language: Persian. Site content language: English.
 */

require dirname(__DIR__) . '/config.php';
require dirname(__DIR__) . '/app/helpers.php';
require dirname(__DIR__) . '/app/i18n.php';

ensure_session();

/**
 * Collection schemas for the generic editor.
 * Field types: text, textarea, url, image, date
 */
function admin_schemas()
{
    return array(
        'projects' => array(
            'desc' => 'نمونه‌کارهای گرید صفحهٔ اصلی',
            'label' => 'پروژه‌ها',
            'icon' => '▦',
            'idPrefix' => 'prj',
            'titleField' => 'title',
            'fields' => array(
                array('key' => 'title', 'label' => 'عنوان پروژه', 'type' => 'text'),
                array('key' => 'category', 'label' => 'دسته‌بندی (مثل WEB یا BRANDING)', 'type' => 'text'),
                array('key' => 'image', 'label' => 'کاور پروژه', 'type' => 'image', 'hint' => 'نسبت ۵:۴ (مثلاً ۱۲۵۰×۱۰۰۰). در سایت به‌صورت خودکار سیاه‌وسفید نمایش داده می‌شود.'),
                array('key' => 'slug', 'label' => 'آدرس صفحهٔ کیس‌استادی', 'type' => 'text', 'hint' => 'مثلاً: negahmedia → سایت می‌شود /work/negahmedia — خالی بگذارید تا از عنوان ساخته شود.'),
                array('key' => 'body', 'label' => 'متن کیس‌استادی', 'type' => 'textarea'),
                array('key' => 'link', 'label' => 'لینک خارجی پروژه', 'type' => 'url'),
                array('key' => 'seoTitle', 'label' => 'سئو: عنوان (Title)', 'type' => 'text', 'hint' => 'خالی = خودکار از عنوان و دسته. حداکثر ۷۰ کاراکتر.'),
                array('key' => 'seoDescription', 'label' => 'سئو: توضیح (Description)', 'type' => 'text', 'hint' => '۱۲۰ تا ۱۶۰ کاراکتر؛ خالی = از متن کیس‌استادی.'),
                array('key' => 'seoKeywords', 'label' => 'سئو: کلمات کلیدی (فارسی/انگلیسی، با ویرگول)', 'type' => 'text'),
            ),
        ),
        'experiences' => array(
            'desc' => 'سوابق کاری (لیست شماره‌دار)',
            'label' => 'تجربه‌ها',
            'icon' => '◈',
            'idPrefix' => 'exp',
            'titleField' => 'role',
            'fields' => array(
                array('key' => 'role', 'label' => 'عنوان نقش', 'type' => 'text'),
                array('key' => 'company', 'label' => 'شرکت / مجموعه', 'type' => 'text'),
                array('key' => 'description', 'label' => 'توضیح', 'type' => 'textarea'),
                array('key' => 'link', 'label' => 'لینک', 'type' => 'url'),
            ),
        ),
        'services' => array(
            'desc' => 'کارهایی که انجام می‌دهید',
            'label' => 'خدمات',
            'icon' => '◇',
            'idPrefix' => 'srv',
            'titleField' => 'title',
            'fields' => array(
                array('key' => 'title', 'label' => 'عنوان خدمت', 'type' => 'text'),
                array('key' => 'description', 'label' => 'توضیح', 'type' => 'textarea'),
            ),
        ),
        'skills' => array(
            'desc' => 'برچسب‌های مهارت (نوار متحرک)',
            'label' => 'مهارت‌ها',
            'icon' => '✦',
            'idPrefix' => 'skl',
            'titleField' => 'label',
            'fields' => array(
                array('key' => 'label', 'label' => 'نام مهارت', 'type' => 'text'),
            ),
        ),
        'collaborators' => array(
            'desc' => 'نام کارفرماها / برندها',
            'label' => 'همکاران',
            'icon' => '◉',
            'idPrefix' => 'col',
            'titleField' => 'name',
            'fields' => array(
                array('key' => 'name', 'label' => 'نام همکار', 'type' => 'text'),
                array('key' => 'logo', 'label' => 'لوگو / تصویر', 'type' => 'image'),
                array('key' => 'link', 'label' => 'لینک', 'type' => 'url'),
            ),
        ),
        'posts' => array(
            'desc' => 'مقالات بلاگ',
            'label' => 'نوشته‌ها',
            'icon' => '✎',
            'idPrefix' => 'post',
            'titleField' => 'title',
            'fields' => array(
                array('key' => 'title', 'label' => 'عنوان نوشته', 'type' => 'text'),
                array('key' => 'slug', 'label' => 'نامک (آدرس انگلیسی)', 'type' => 'text', 'hint' => 'خالی بگذارید تا از عنوان ساخته شود.'),
                array('key' => 'createdAt', 'label' => 'تاریخ انتشار', 'type' => 'date'),
                array('key' => 'cover', 'label' => 'تصویر شاخص', 'type' => 'image'),
                array('key' => 'excerpt', 'label' => 'خلاصه', 'type' => 'textarea'),
                array('key' => 'body', 'label' => 'متن کامل', 'type' => 'textarea'),
                array('key' => 'seoTitle', 'label' => 'سئو: عنوان (Title)', 'type' => 'text', 'hint' => 'خالی = عنوان نوشته.'),
                array('key' => 'seoDescription', 'label' => 'سئو: توضیح (Description)', 'type' => 'text', 'hint' => 'خالی = خلاصه.'),
                array('key' => 'seoKeywords', 'label' => 'سئو: کلمات کلیدی (با ویرگول)', 'type' => 'text'),
            ),
        ),
    );
}

/** Number of unread contact messages. */
function admin_unread_count()
{
    $unread = 0;
    foreach (load_json('messages.json', array()) as $m) {
        if (empty($m['read'])) {
            $unread++;
        }
    }
    return $unread;
}

/** Shared admin <head> + sidebar. */
function admin_header($active, $pageTitle)
{
    $schemas = admin_schemas();
    $unread = admin_unread_count();
    ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<meta name="admin-csrf" content="<?php echo e(csrf_token()); ?>">
<title><?php echo e($pageTitle); ?> — پنل مدیریت محسین</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-body">
<div class="admin-shell">

  <aside class="sidebar">
    <a class="sidebar-brand" href="index.php">MOHUSYN <span>پنل مدیریت</span></a>
    <nav class="sidebar-nav">
      <div class="sidebar-group">نمای کلی</div>
      <a href="index.php" class="<?php echo $active === 'dashboard' ? 'active' : ''; ?>">⌂ داشبورد<small>پیش‌نمایش سایت و دسترسی سریع</small></a>
      <a href="messages.php" class="<?php echo $active === 'messages' ? 'active' : ''; ?>">
        ✉ پیام‌ها
        <?php if ($unread > 0): ?><span class="nav-badge"><?php echo e(fa_digits($unread)); ?></span><?php endif; ?>
        <small>درخواست‌های «شروع پروژه»</small>
      </a>

      <div class="sidebar-group">محتوای سایت</div>
      <a href="settings.php" class="<?php echo $active === 'settings' ? 'active' : ''; ?>">👤 معرفی و متن‌ها<small>نام، بیو، عکس هدر، ایمیل، تیترها</small></a>
      <?php foreach ($schemas as $name => $schema): ?>
        <a href="index.php?tab=<?php echo e($name); ?>" class="<?php echo $active === $name ? 'active' : ''; ?>">
          <?php echo e($schema['icon']); ?> <?php echo e($schema['label']); ?>
          <small><?php echo e(isset($schema['desc']) ? $schema['desc'] : ''); ?></small>
        </a>
      <?php endforeach; ?>

      <div class="sidebar-group">طراحی و چیدمان</div>
      <a href="blocks.php" class="<?php echo $active === 'blocks' ? 'active' : ''; ?>">⬒ بلوک‌ساز بصری<small>درگ‌اند‌دراپ روی پیش‌نمایش زنده</small></a>
      <a href="pages.php" class="<?php echo $active === 'pages' ? 'active' : ''; ?>">▣ بخش‌های صفحات<small>روشن/خاموش‌کردن هر بخش</small></a>
      <a href="settings.php#tab-fonts" class="<?php echo $active === 'fonts' ? 'active' : ''; ?>">Aa فونت‌ها<small>فونت متن‌ها و تیترها</small></a>
      <a href="css.php" class="<?php echo $active === 'css' ? 'active' : ''; ?>">🎨 CSS سفارشی<small>برای کاربران حرفه‌ای</small></a>

      <div class="sidebar-group">دیده‌شدن</div>
      <a href="seo.php" class="<?php echo $active === 'seo' ? 'active' : ''; ?>">🔎 سئو<small>کلمات کلیدی، گوگل و بینگ</small></a>

      <div class="sidebar-group">فایل‌ها</div>
      <a href="media.php" class="<?php echo $active === 'media' ? 'active' : ''; ?>">▤ رسانه‌ها<small>همهٔ تصاویر آپلودشده</small></a>
    </nav>
    <div class="sidebar-foot">
      <a href="/" target="_blank">مشاهدهٔ سایت ↗</a>
      <a href="logout.php">خروج</a>
    </div>
  </aside>

  <main class="admin-main">
    <?php
}

function admin_footer()
{
    ?>
  </main>
</div>
<script>
(function () {
  var csrfMeta = document.querySelector('meta[name="admin-csrf"]');
  var CSRF = csrfMeta ? csrfMeta.content : '';

  function showPreview(key, url) {
    var preview = document.querySelector('[data-image-preview="' + key + '"]');
    if (preview && url) {
      preview.removeAttribute("hidden");
      preview.src = url;
    }
  }

  /* -------------------------------------------- image picker select */
  document.querySelectorAll("[data-image-picker]").forEach(function (sel) {
    var input = document.querySelector('[data-image-input="' + sel.dataset.target + '"]');
    if (!input) return;
    sel.addEventListener("change", function () {
      if (sel.value) input.value = sel.value;
      showPreview(sel.dataset.target, input.value);
    });
    input.addEventListener("input", function () {
      showPreview(sel.dataset.target, input.value);
    });
  });

  /* ---------------------------------------- inline upload (images) */
  function uploadFile(file, kind) {
    var fd = new FormData();
    fd.append('file', file);
    fd.append('kind', kind);
    fd.append('_csrf', CSRF);
    return fetch('upload.php', { method: 'POST', body: fd }).then(function (r) { return r.json(); });
  }

  document.querySelectorAll(".image-field").forEach(function (box) {
    var input = box.querySelector("[data-image-input]");
    if (!input) return;
    var key = input.getAttribute("data-image-input");

    var file = document.createElement("input");
    file.type = "file";
    file.accept = "image/*";
    file.style.display = "none";

    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "btn btn-mini";
    btn.textContent = "📤 آپلود تصویر";

    btn.addEventListener("click", function () { file.click(); });
    file.addEventListener("change", function () {
      if (!file.files || !file.files[0]) return;
      btn.disabled = true;
      var old = btn.textContent;
      btn.textContent = "... در حال آپلود";
      uploadFile(file.files[0], "image").then(function (res) {
        if (res.ok && res.url) {
          input.value = res.url;
          showPreview(key, res.url);
          var sel = box.querySelector("[data-image-picker]");
          if (sel) {
            var opt = document.createElement("option");
            opt.value = res.url;
            opt.textContent = res.url;
            opt.selected = true;
            sel.appendChild(opt);
          }
        } else {
          alert("آپلود ناموفق بود (" + (res.error || "error") + ")");
        }
      }).catch(function () { alert("خطای شبکه در آپلود"); })
        .then(function () { btn.disabled = false; btn.textContent = old; });
      file.value = "";
    });

    box.appendChild(btn);
    box.appendChild(file);
  });

  /* ------------------------------------------ inline upload (fonts) */
  document.querySelectorAll("[data-font-upload]").forEach(function (input) {
    var file = document.createElement("input");
    file.type = "file";
    file.accept = ".woff,.woff2,.ttf,.otf";
    file.style.display = "none";

    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "btn btn-mini";
    btn.textContent = "📤 آپلود فونت";

    btn.addEventListener("click", function () { file.click(); });
    file.addEventListener("change", function () {
      if (!file.files || !file.files[0]) return;
      btn.disabled = true;
      var old = btn.textContent;
      btn.textContent = "... در حال آپلود";
      uploadFile(file.files[0], "font").then(function (res) {
        if (res.ok && res.url) { input.value = res.url; }
        else { alert("آپلود ناموفق بود (" + (res.error || "error") + ")"); }
      }).catch(function () { alert("خطای شبکه در آپلود"); })
        .then(function () { btn.disabled = false; btn.textContent = old; });
      file.value = "";
    });

    input.insertAdjacentElement("afterend", btn);
    input.insertAdjacentElement("afterend", file);
  });

  /* ----------------------------------------- confirm dangerous ops */
  document.querySelectorAll("[data-confirm]").forEach(function (el) {
    el.addEventListener("click", function (ev) {
      if (!window.confirm(el.dataset.confirm)) ev.preventDefault();
    });
  });
})();
</script>
</body>
</html>
    <?php
}

/** Render a message banner from ?msg= */
function admin_flash()
{
    $map = array(
        'saved' => 'ذخیره شد ✓',
        'deleted' => 'حذف شد.',
        'uploaded' => 'فایل آپلود شد ✓',
        'login-ok' => 'خوش آمدید 👋',
        'error' => 'خطایی پیش آمد؛ دوباره تلاش کنید.',
        'csrf' => 'نشست منقضی شده بود؛ لطفاً دوباره ذخیره کنید.',
    );
    $key = isset($_GET['msg']) ? $_GET['msg'] : '';
    if ($key !== '' && isset($map[$key])) {
        $cls = ($key === 'error' || $key === 'csrf') ? 'flash flash-error' : 'flash';
        echo '<div class="' . $cls . '">' . e($map[$key]) . '</div>';
    }
}
