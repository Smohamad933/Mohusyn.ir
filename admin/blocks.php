<?php
/**
 * Admin — VISUAL block builder.
 * Live preview of the real site in an iframe; blocks are dragged from the palette
 * and dropped straight onto the page. Changes are kept as a draft until "publish".
 */

require __DIR__ . '/_bootstrap.php';
require_auth();

$pages = array('home' => 'صفحهٔ اصلی', 'about' => 'دربارهٔ من');
$pageUrls = array('home' => '/', 'about' => '/about/');
$page = isset($_GET['page']) && isset($pages[$_GET['page']]) ? $_GET['page'] : 'home';

$blockTypes = array(
    'heading' => array('label' => 'تیتر', 'icon' => 'H', 'hint' => 'یک عنوان بزرگ'),
    'text' => array('label' => 'متن', 'icon' => '¶', 'hint' => 'پاراگراف توضیحی'),
    'image' => array('label' => 'تصویر', 'icon' => '▣', 'hint' => 'عکس با عرض دلخواه'),
    'button' => array('label' => 'دکمه', 'icon' => '⬭', 'hint' => 'لینک به صفحه یا فرم تماس'),
    'spacer' => array('label' => 'فاصله', 'icon' => '↕', 'hint' => 'فضای خالی عمودی'),
    'divider' => array('label' => 'خط جداکننده', 'icon' => '—', 'hint' => 'خط افقی نازک'),
);
$media = media_list();

admin_header('blocks', 'بلوک‌ساز بصری');
?>
<style>
  .admin-main { max-width: none; padding: 18px 20px 20px; display: flex; flex-direction: column; min-height: 100vh; }
</style>

<div class="builder" id="builder" data-page="<?php echo e($page); ?>" data-url="<?php echo e($pageUrls[$page]); ?>">

  <!-- ============================================================ top bar -->
  <div class="builder-bar">
    <div class="builder-bar-start">
      <strong class="builder-title">بلوک‌ساز بصری</strong>
      <div class="page-switch">
        <?php foreach ($pages as $pk => $plabel): ?>
          <a class="btn btn-mini <?php echo $pk === $page ? 'is-on' : ''; ?>" href="blocks.php?page=<?php echo e($pk); ?>"><?php echo e($plabel); ?></a>
        <?php endforeach; ?>
      </div>
      <div class="device-switch" role="group" aria-label="اندازهٔ پیش‌نمایش">
        <button type="button" class="is-on" data-device="desktop" title="دسکتاپ">🖥</button>
        <button type="button" data-device="tablet" title="تبلت">▭</button>
        <button type="button" data-device="mobile" title="موبایل">📱</button>
      </div>
    </div>
    <div class="builder-bar-end">
      <span class="builder-status" id="b-status">در حال بارگذاری…</span>
      <button type="button" class="btn btn-ghost btn-mini" id="b-discard" title="پیش‌نویس حذف و نسخهٔ منتشرشده بازگردانده می‌شود">بازگردانی</button>
      <button type="button" class="btn btn-mini" id="b-draft">ذخیرهٔ پیش‌نویس</button>
      <button type="button" class="btn btn-primary btn-mini" id="b-publish">انتشار در سایت</button>
    </div>
  </div>

  <div class="builder-body">

    <!-- ========================================================= palette -->
    <aside class="builder-side">
      <div class="side-section">
        <h3>بلوک‌ها</h3>
        <p class="hint">بکشید و روی صفحهٔ روبه‌رو رها کنید (یا برای افزودن به انتها کلیک کنید).</p>
        <div class="palette">
          <?php foreach ($blockTypes as $tk => $t): ?>
            <div class="palette-item" draggable="true" data-type="<?php echo e($tk); ?>" title="<?php echo e($t['hint']); ?>">
              <span class="palette-icon"><?php echo e($t['icon']); ?></span>
              <span><?php echo e($t['label']); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="side-section" id="inspector">
        <h3>تنظیمات بلوک</h3>
        <p class="hint" id="insp-empty">روی یک بلوک در پیش‌نمایش کلیک کنید تا اینجا ویرایشش کنید.</p>
        <div id="insp-form" hidden></div>
      </div>

      <div class="side-section">
        <h3>لایه‌ها</h3>
        <ol class="layers" id="layers"></ol>
      </div>
    </aside>

    <!-- ========================================================= preview -->
    <div class="builder-stage">
      <div class="stage-frame" id="stage-frame" data-device="desktop">
        <iframe id="b-frame" src="<?php echo e($pageUrls[$page]); ?>?builder=1" title="پیش‌نمایش زندهٔ سایت"></iframe>
      </div>
    </div>
  </div>
</div>

<template id="media-options">
  <option value="">— انتخاب از رسانه‌ها —</option>
  <?php foreach ($media as $m): ?>
    <option value="<?php echo e($m['url']); ?>"><?php echo e($m['url']); ?></option>
  <?php endforeach; ?>
</template>

<script>
(function () {
  var root = document.getElementById('builder');
  var PAGE = root.getAttribute('data-page');
  var CSRF = document.querySelector('meta[name="admin-csrf"]').content;
  var frame = document.getElementById('b-frame');
  var statusEl = document.getElementById('b-status');
  var layersEl = document.getElementById('layers');
  var inspForm = document.getElementById('insp-form');
  var inspEmpty = document.getElementById('insp-empty');
  var labels = { heading: 'تیتر', text: 'متن', image: 'تصویر', button: 'دکمه', spacer: 'فاصله', divider: 'خط جداکننده' };

  var blocks = [];
  var selectedId = null;
  var dirty = false;
  var isDraft = false;
  var frameReady = false;
  var autosaveTimer = null;

  function uid() { return 'blk-' + Math.random().toString(36).slice(2, 12); }
  function defaults(type) {
    switch (type) {
      case 'heading': return { text: '', level: 'h2' };
      case 'text': return { text: '' };
      case 'image': return { src: '', alt: '', width: '', align: 'center' };
      case 'button': return { label: 'Start a project', url: '/contact/', style: 'solid', align: 'left' };
      case 'spacer': return { height: '40' };
      default: return {};
    }
  }
  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]; }); }
  function find(id) { for (var i = 0; i < blocks.length; i++) if (blocks[i].id === id) return i; return -1; }

  /* ------------------------------------------------------------ status */
  function setStatus(txt, cls) {
    statusEl.textContent = txt;
    statusEl.className = 'builder-status ' + (cls || '');
  }
  function refreshStatus() {
    if (dirty) setStatus('● تغییرات ذخیره‌نشده', 'is-dirty');
    else if (isDraft) setStatus('پیش‌نویس ذخیره شد — هنوز منتشر نشده', 'is-draft');
    else setStatus('همگام با سایت ✓', 'is-ok');
  }
  function markDirty() {
    dirty = true;
    refreshStatus();
    clearTimeout(autosaveTimer);
    autosaveTimer = setTimeout(function () { saveDraft(true); }, 1500); /* auto-draft */
  }

  /* ------------------------------------------------------------ frame */
  function pushToFrame() {
    if (!frameReady) return;
    frame.contentWindow.postMessage({ source: 'mohusyn-builder', type: 'render', blocks: blocks, selectedId: selectedId }, window.location.origin);
  }
  function scrollFrameTo(id) {
    if (frameReady) frame.contentWindow.postMessage({ source: 'mohusyn-builder', type: 'scrollTo', id: id }, window.location.origin);
  }

  window.addEventListener('message', function (ev) {
    if (ev.origin !== window.location.origin || !ev.data || ev.data.source !== 'mohusyn-builder-frame') return;
    var m = ev.data;
    if (m.type === 'ready') { frameReady = true; pushToFrame(); return; }
    if (m.type === 'select') { select(m.id); return; }
    if (m.type === 'insert') { insertBlock(m.blockType, m.index); return; }
    if (m.type === 'move') { moveBlock(m.id, m.index); return; }
    if (m.type === 'action') {
      if (m.action === 'delete') removeBlock(m.id);
      else if (m.action === 'up') nudge(m.id, -1);
      else if (m.action === 'down') nudge(m.id, 1);
    }
  });

  /* ------------------------------------------------------------ model ops */
  function renderAll() { pushToFrame(); renderLayers(); }

  function insertBlock(type, index) {
    if (!labels[type]) return;
    var b = { id: uid(), page: PAGE, type: type, published: true, data: defaults(type) };
    if (index === undefined || index === null || index > blocks.length) index = blocks.length;
    blocks.splice(index, 0, b);
    selectedId = b.id;
    markDirty(); renderAll(); renderInspector();
    setTimeout(function () { scrollFrameTo(b.id); }, 80);
  }
  function moveBlock(id, index) {
    var i = find(id); if (i < 0) return;
    var b = blocks.splice(i, 1)[0];
    if (i < index) index--;
    blocks.splice(Math.max(0, Math.min(index, blocks.length)), 0, b);
    markDirty(); renderAll();
  }
  function nudge(id, dir) {
    var i = find(id); var j = i + dir;
    if (i < 0 || j < 0 || j >= blocks.length) return;
    var t = blocks[i]; blocks[i] = blocks[j]; blocks[j] = t;
    markDirty(); renderAll();
  }
  function removeBlock(id) {
    var i = find(id); if (i < 0) return;
    if (!window.confirm('این بلوک حذف شود؟')) return;
    blocks.splice(i, 1);
    if (selectedId === id) selectedId = null;
    markDirty(); renderAll(); renderInspector();
  }
  function select(id) {
    selectedId = id;
    pushToFrame(); renderLayers(); renderInspector();
  }

  /* ------------------------------------------------------------ layers */
  function renderLayers() {
    layersEl.innerHTML = '';
    if (!blocks.length) { layersEl.innerHTML = '<li class="layer-empty">هنوز بلوکی نیست</li>'; return; }
    blocks.forEach(function (b) {
      var li = document.createElement('li');
      li.className = 'layer' + (b.id === selectedId ? ' is-on' : '') + (b.published === false ? ' is-hidden' : '');
      var preview = b.type === 'heading' || b.type === 'text' ? (b.data.text || '') : b.type === 'button' ? (b.data.label || '') : b.type === 'image' ? (b.data.src || '') : '';
      li.innerHTML = '<span class="layer-type">' + labels[b.type] + '</span><span class="layer-prev" dir="ltr">' + esc(preview).slice(0, 34) + '</span>';
      li.addEventListener('click', function () { select(b.id); scrollFrameTo(b.id); });
      layersEl.appendChild(li);
    });
  }

  /* ------------------------------------------------------------ inspector */
  function field(label, inner) { return '<label class="insp-field"><span>' + label + '</span>' + inner + '</label>'; }
  function sel(name, opts, val) {
    return '<select data-k="' + name + '">' + opts.map(function (o) { return '<option value="' + o[0] + '"' + (o[0] === val ? ' selected' : '') + '>' + o[1] + '</option>'; }).join('') + '</select>';
  }
  var alignOpts = [['left', 'چپ'], ['center', 'وسط'], ['right', 'راست']];

  function renderInspector() {
    var i = find(selectedId);
    if (i < 0) { inspForm.hidden = true; inspEmpty.hidden = false; return; }
    var b = blocks[i]; var d = b.data;
    var h = '<div class="insp-head"><span class="block-badge">' + labels[b.type] + '</span>' +
      '<label class="switch switch-sm"><input type="checkbox" data-k="__published"' + (b.published !== false ? ' checked' : '') + '><span>نمایش در سایت</span></label></div>';
    if (b.type === 'heading') {
      h += field('متن تیتر (انگلیسی)', '<input type="text" dir="ltr" data-k="text" value="' + esc(d.text) + '">');
      h += field('اندازه', sel('level', [['h2', 'بزرگ (H2)'], ['h3', 'متوسط (H3)'], ['h4', 'کوچک (H4)']], d.level));
    } else if (b.type === 'text') {
      h += field('متن (انگلیسی)', '<textarea dir="ltr" rows="6" data-k="text">' + esc(d.text) + '</textarea>');
    } else if (b.type === 'image') {
      h += '<div class="image-field insp-image">' +
        field('آدرس تصویر', '<input type="text" dir="ltr" data-k="src" data-image-input="insp" value="' + esc(d.src) + '" placeholder="/uploads/...">') +
        '<select data-image-picker data-target="insp" data-k="src" id="insp-media"></select>' +
        '<img class="image-preview" data-image-preview="insp" src="' + esc(d.src) + '" alt=""' + (d.src ? '' : ' hidden') + '>' +
        '</div>';
      h += field('متن جایگزین (alt)', '<input type="text" dir="ltr" data-k="alt" value="' + esc(d.alt) + '">');
      h += field('حداکثر عرض (px) — خالی = تمام‌عرض', '<input type="number" dir="ltr" min="0" max="1400" data-k="width" value="' + esc(d.width) + '">');
      h += field('چینش', sel('align', alignOpts, d.align || 'center'));
    } else if (b.type === 'button') {
      h += field('متن دکمه', '<input type="text" dir="ltr" data-k="label" value="' + esc(d.label) + '">');
      h += field('لینک', '<input type="text" dir="ltr" data-k="url" value="' + esc(d.url) + '" placeholder="/contact/">');
      h += field('سبک', sel('style', [['solid', 'توپر'], ['outline', 'خطی']], d.style));
      h += field('چینش', sel('align', alignOpts, d.align || 'left'));
    } else if (b.type === 'spacer') {
      h += field('ارتفاع (px)', '<input type="number" dir="ltr" min="0" max="400" data-k="height" value="' + esc(d.height) + '">');
    } else {
      h += '<p class="hint">خط جداکننده تنظیمی ندارد.</p>';
    }
    h += '<div class="insp-actions"><button type="button" class="btn btn-mini" data-act="up">▲ بالاتر</button><button type="button" class="btn btn-mini" data-act="down">▼ پایین‌تر</button><button type="button" class="btn btn-mini btn-danger" data-act="delete">حذف</button></div>';
    inspForm.innerHTML = h;
    inspForm.hidden = false; inspEmpty.hidden = true;

    /* media picker options */
    var mediaSel = document.getElementById('insp-media');
    if (mediaSel) {
      mediaSel.innerHTML = document.getElementById('media-options').innerHTML;
      mediaSel.value = d.src && Array.prototype.some.call(mediaSel.options, function (o) { return o.value === d.src; }) ? d.src : '';
      mountUpload(inspForm.querySelector('.insp-image'), b);
    }

    inspForm.querySelectorAll('[data-k]').forEach(function (el) {
      var k = el.getAttribute('data-k');
      var ev = el.tagName === 'SELECT' || el.type === 'checkbox' ? 'change' : 'input';
      el.addEventListener(ev, function () {
        if (k === '__published') { b.published = el.checked; }
        else if (el.tagName === 'SELECT' && k === 'src') { if (el.value) { b.data.src = el.value; var inp = inspForm.querySelector('input[data-k="src"]'); if (inp) inp.value = el.value; showPrev(el.value); } }
        else { b.data[k] = el.value; if (k === 'src') showPrev(el.value); }
        markDirty(); pushToFrame(); renderLayers();
      });
    });
    inspForm.querySelectorAll('[data-act]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var a = btn.getAttribute('data-act');
        if (a === 'delete') removeBlock(b.id); else nudge(b.id, a === 'up' ? -1 : 1);
      });
    });
  }
  function showPrev(url) {
    var img = inspForm.querySelector('[data-image-preview="insp"]');
    if (!img) return;
    if (url) { img.src = url; img.hidden = false; } else { img.hidden = true; }
  }
  function mountUpload(box, b) {
    var file = document.createElement('input'); file.type = 'file'; file.accept = 'image/*'; file.style.display = 'none';
    var btn = document.createElement('button'); btn.type = 'button'; btn.className = 'btn btn-mini'; btn.textContent = '📤 آپلود تصویر';
    btn.addEventListener('click', function () { file.click(); });
    file.addEventListener('change', function () {
      if (!file.files || !file.files[0]) return;
      btn.disabled = true; btn.textContent = '… در حال آپلود';
      var fd = new FormData(); fd.append('file', file.files[0]); fd.append('kind', 'image'); fd.append('_csrf', CSRF);
      fetch('upload.php', { method: 'POST', body: fd }).then(function (r) { return r.json(); }).then(function (res) {
        if (res.ok && res.url) {
          b.data.src = res.url;
          var inp = inspForm.querySelector('input[data-k="src"]'); if (inp) inp.value = res.url;
          var tpl = document.getElementById('media-options'); var o = document.createElement('option'); o.value = res.url; o.textContent = res.url; tpl.content.appendChild(o);
          showPrev(res.url); markDirty(); pushToFrame(); renderLayers();
        } else { alert('آپلود ناموفق بود (' + (res.error || 'error') + ')'); }
      }).catch(function () { alert('خطای شبکه در آپلود'); })
        .then(function () { btn.disabled = false; btn.textContent = '📤 آپلود تصویر'; });
      file.value = '';
    });
    box.appendChild(btn); box.appendChild(file);
  }

  /* ------------------------------------------------------------ palette dnd */
  document.querySelectorAll('.palette-item').forEach(function (it) {
    var type = it.getAttribute('data-type');
    it.addEventListener('dragstart', function (ev) {
      ev.dataTransfer.effectAllowed = 'copy';
      ev.dataTransfer.setData('text/plain', 'new:' + type);
      root.classList.add('is-dragging');
    });
    it.addEventListener('dragend', function () { root.classList.remove('is-dragging'); });
    it.addEventListener('click', function () { insertBlock(type); });
  });

  /* ------------------------------------------------------------ device switch */
  document.querySelectorAll('[data-device]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('[data-device]').forEach(function (b) { b.classList.remove('is-on'); });
      btn.classList.add('is-on');
      document.getElementById('stage-frame').setAttribute('data-device', btn.getAttribute('data-device'));
    });
  });

  /* ------------------------------------------------------------ persistence */
  function api(payload) {
    payload._csrf = CSRF; payload.page = PAGE;
    return fetch('builder_api.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) }).then(function (r) { return r.json(); });
  }
  function saveDraft(silent) {
    clearTimeout(autosaveTimer);
    setStatus('در حال ذخیرهٔ پیش‌نویس…');
    return api({ action: 'draft', blocks: blocks }).then(function (res) {
      if (res.ok) { dirty = false; isDraft = true; refreshStatus(); }
      else { setStatus('ذخیره ناموفق بود', 'is-dirty'); if (!silent) alert('ذخیرهٔ پیش‌نویس ناموفق بود'); }
    }).catch(function () { setStatus('خطای شبکه', 'is-dirty'); });
  }
  function publish() {
    clearTimeout(autosaveTimer);
    setStatus('در حال انتشار…');
    api({ action: 'publish', blocks: blocks }).then(function (res) {
      if (res.ok) { blocks = res.blocks; dirty = false; isDraft = false; refreshStatus(); renderAll(); renderInspector(); setStatus('منتشر شد ✓ — سایت به‌روز است', 'is-ok'); }
      else { setStatus('انتشار ناموفق بود', 'is-dirty'); }
    }).catch(function () { setStatus('خطای شبکه', 'is-dirty'); });
  }
  function discard() {
    if (!window.confirm('پیش‌نویس حذف شود و نسخهٔ منتشرشدهٔ سایت بازگردانده شود؟')) return;
    api({ action: 'discard' }).then(function (res) {
      if (res.ok) { blocks = res.blocks; selectedId = null; dirty = false; isDraft = false; refreshStatus(); renderAll(); renderInspector(); }
    });
  }
  document.getElementById('b-draft').addEventListener('click', function () { saveDraft(false); });
  document.getElementById('b-publish').addEventListener('click', publish);
  document.getElementById('b-discard').addEventListener('click', discard);
  window.addEventListener('beforeunload', function (ev) { if (dirty) { ev.preventDefault(); ev.returnValue = ''; } });

  /* initial load */
  fetch('builder_api.php?page=' + encodeURIComponent(PAGE)).then(function (r) { return r.json(); }).then(function (res) {
    if (!res.ok) { setStatus('خطا در بارگذاری', 'is-dirty'); return; }
    blocks = res.blocks || []; isDraft = !!res.draft; dirty = false;
    refreshStatus(); renderAll(); renderInspector();
  });
})();
</script>
<?php
admin_footer();
