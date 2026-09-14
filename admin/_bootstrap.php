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
                array('key' => 'showOnHome', 'label' => 'نمایش در صفحهٔ اصلی', 'type' => 'checkbox', 'hint' => 'اگر خاموش باشد، این پروژه فقط در صفحهٔ Portfolio (/work/) دیده می‌شود.'),
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
<link rel="stylesheet" href="<?php echo e(asset('/admin/assets/admin.css')); ?>">
</head>
<body class="admin-body">
<script>try { if (localStorage.getItem('mohusyn-admin-rail') === 'open') document.body.classList.add('rail-open'); } catch (e) {}</script>
<div class="admin-shell">

  <?php
  $ico = array(
    'dashboard' => '<svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>',
    'analytics' => '<svg viewBox="0 0 24 24"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>',
    'messages' => '<svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>',
    'settings' => '<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>',
    'projects' => '<svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>',
    'experiences' => '<svg viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18"/></svg>',
    'services' => '<svg viewBox="0 0 24 24"><path d="m12 3 2.5 5.5L20 9l-4 4 1 6-5-3-5 3 1-6-4-4 5.5-.5z"/></svg>',
    'skills' => '<svg viewBox="0 0 24 24"><path d="M20 7h-9M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/></svg>',
    'collaborators' => '<svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4a3.5 3.5 0 0 1 0 7M21.5 20a6.5 6.5 0 0 0-5-6.3"/></svg>',
    'posts' => '<svg viewBox="0 0 24 24"><path d="M4 20h4l10.5-10.5a2.1 2.1 0 0 0-3-3L5 17z"/><path d="m13.5 6.5 3 3"/></svg>',
    'blocks' => '<svg viewBox="0 0 24 24"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><path d="M17 14v6M14 17h6"/></svg>',
    'pages' => '<svg viewBox="0 0 24 24"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>',
    'fonts' => '<svg viewBox="0 0 24 24"><path d="M4 20 10 4h1l6 16M6.5 14h8"/><path d="M17 12h3v8"/></svg>',
    'css' => '<svg viewBox="0 0 24 24"><path d="M12 3a9 9 0 1 0 0 18c1.5 0 2-1 2-2s-1-1.5-1-2.5 1-1.5 2.5-1.5H17a4 4 0 0 0 4-4c0-4.5-4-8-9-8z"/><circle cx="7.5" cy="11" r="1"/><circle cx="10.5" cy="7" r="1"/><circle cx="15.5" cy="7.5" r="1"/></svg>',
    'seo' => '<svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.3-4.3M8.5 11h5M11 8.5v5"/></svg>',
    'media' => '<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="1.8"/><path d="m21 16-5-5-9 9"/></svg>',
    'site' => '<svg viewBox="0 0 24 24"><path d="M14 4h6v6M20 4l-9 9M19 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5"/></svg>',
    'logout' => '<svg viewBox="0 0 24 24"><path d="M10 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h4M15 8l4 4-4 4M19 12H9"/></svg>',
    'expand' => '<svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>',
  );
  $nav = array(
    array('group' => 'نمای کلی'),
    array('key' => 'dashboard', 'href' => 'index.php', 'label' => 'داشبورد', 'desc' => 'پیش‌نمایش سایت و دسترسی سریع'),
    array('key' => 'messages', 'href' => 'messages.php', 'label' => 'پیام‌ها', 'desc' => 'درخواست‌های «شروع پروژه»', 'badge' => $unread),
    array('key' => 'analytics', 'href' => 'analytics.php', 'label' => 'بازدید و رفتار', 'desc' => 'کلیک‌ها، مکث‌ها، اسکرول'),
    array('group' => 'محتوای سایت'),
    array('key' => 'settings', 'href' => 'settings.php', 'label' => 'معرفی و متن‌ها', 'desc' => 'نام، بیو، عکس هدر، ایمیل'),
  );
  foreach ($schemas as $name => $schema) {
      $nav[] = array('key' => $name, 'href' => 'index.php?tab=' . $name, 'label' => $schema['label'], 'desc' => isset($schema['desc']) ? $schema['desc'] : '');
  }
  $nav[] = array('group' => 'طراحی و چیدمان');
  $nav[] = array('key' => 'blocks', 'href' => 'blocks.php', 'label' => 'بلوک‌ساز بصری', 'desc' => 'درگ‌اند‌دراپ روی پیش‌نمایش زنده');
  $nav[] = array('key' => 'pages', 'href' => 'pages.php', 'label' => 'بخش‌های صفحات', 'desc' => 'روشن/خاموش‌کردن هر بخش');
  $nav[] = array('key' => 'fonts', 'href' => 'settings.php#tab-fonts', 'label' => 'فونت‌ها', 'desc' => 'فونت متن‌ها و تیترها');
  $nav[] = array('key' => 'css', 'href' => 'css.php', 'label' => 'CSS سفارشی', 'desc' => 'برای کاربران حرفه‌ای');
  $nav[] = array('group' => 'دیده‌شدن');
  $nav[] = array('key' => 'seo', 'href' => 'seo.php', 'label' => 'سئو', 'desc' => 'کلمات کلیدی، گوگل و بینگ');
  $nav[] = array('group' => 'فایل‌ها');
  $nav[] = array('key' => 'media', 'href' => 'media.php', 'label' => 'رسانه‌ها', 'desc' => 'همهٔ تصاویر آپلودشده');
  ?>
  <header class="topbar" id="topbar">
    <div class="topbar-row">
      <a class="topbar-brand" href="index.php">MOHUSYN <small>پنل مدیریت</small></a>
      <nav class="tabbar" aria-label="بخش‌های پنل">
        <?php foreach ($nav as $item): ?>
          <?php if (isset($item['group'])): ?>
            <span class="tab-sep" aria-hidden="true"></span>
          <?php else: ?>
            <a href="<?php echo e($item['href']); ?>" class="tab <?php echo $active === $item['key'] ? 'active' : ''; ?>" data-tip="<?php echo e($item['label']); ?>">
              <span class="tab-ico"><?php echo isset($ico[$item['key']]) ? $ico[$item['key']] : $ico['pages']; ?>
                <?php if (!empty($item['badge'])): ?><i class="tab-dot"></i><?php endif; ?>
              </span>
              <span class="tab-label"><?php echo e($item['label']); ?><?php if (!empty($item['badge'])): ?> <b class="nav-badge"><?php echo e(fa_digits($item['badge'])); ?></b><?php endif; ?></span>
            </a>
          <?php endif; ?>
        <?php endforeach; ?>
      </nav>
      <div class="topbar-end">
        <button type="button" class="rail-toggle" data-rail-toggle aria-label="نمایش/پنهان‌کردن نام تب‌ها" title="نمایش/پنهان‌کردن نام تب‌ها"><?php echo $ico['expand']; ?></button>
        <a href="/" target="_blank" class="tab" data-tip="مشاهدهٔ سایت"><span class="tab-ico"><?php echo $ico['site']; ?></span><span class="tab-label">سایت</span></a>
        <a href="logout.php" class="tab" data-tip="خروج"><span class="tab-ico"><?php echo $ico['logout']; ?></span><span class="tab-label">خروج</span></a>
      </div>
    </div>
  </header>

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

  /* ------------------------------------- collapsible icon sidebar */
  var railBtn = document.querySelector('[data-rail-toggle]');
  if (railBtn) {
    railBtn.addEventListener('click', function () {
      var open = document.body.classList.toggle('rail-open');
      try { localStorage.setItem('mohusyn-admin-rail', open ? 'open' : 'closed'); } catch (e) {}
    });
  }

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
