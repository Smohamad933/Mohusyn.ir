<?php
/**
 * Admin — media library (upload / browse / delete).
 */

require __DIR__ . '/_bootstrap.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        header('Location: media.php?msg=csrf');
        exit;
    }

    $action = isset($_POST['action']) ? $_POST['action'] : '';

    /* ------------------------------------------------------- upload */
    if ($action === 'upload') {
        if (!isset($_FILES['files']) || !is_array($_FILES['files']['name'])) {
            header('Location: media.php?msg=error');
            exit;
        }

        $media = media_list();
        $ok = false;

        $count = count($_FILES['files']['name']);
        for ($i = 0; $i < $count; $i++) {
            if (!isset($_FILES['files']['error'][$i])) {
                continue;
            }
            if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            if ($_FILES['files']['size'][$i] > MAX_UPLOAD_SIZE) {
                continue;
            }

            $name = sanitize_filename($_FILES['files']['name'][$i]);
            $dot = strrpos($name, '.');
            $ext = $dot !== false ? strtolower(substr($name, $dot + 1)) : '';
            if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
                continue;
            }

            $base = $dot !== false ? substr($name, 0, $dot) : $name;
            $final = $base . '-' . date('Ymd-His') . '-' . substr(md5(uniqid('', true)), 0, 4) . '.' . $ext;

            $target = uploads_dir() . DIRECTORY_SEPARATOR . $final;
            if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $target)) {
                $media[] = array(
                    'file' => $final,
                    'url' => '/uploads/' . $final,
                    'uploadedAt' => date('Y-m-d H:i'),
                );
                $ok = true;
            }
        }

        if ($ok) {
            save_json('media.json', $media);
        }
        header('Location: media.php?msg=' . ($ok ? 'uploaded' : 'error'));
        exit;
    }

    /* ------------------------------------------------------- delete */
    if ($action === 'delete') {
        $url = isset($_POST['url']) ? (string) $_POST['url'] : '';
        if (strpos($url, '/uploads/') === 0) {
            $file = basename($url);
            $path = uploads_dir() . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                unlink($path);
            }
            $media = array_values(array_filter(media_list(), function ($m) use ($url) {
                return $m['url'] !== $url;
            }));
            save_json('media.json', $media);
            header('Location: media.php?msg=deleted');
        } else {
            header('Location: media.php?msg=error');
        }
        exit;
    }

    header('Location: media.php?msg=error');
    exit;
}

$media = array_reverse(media_list());

admin_header('media', 'رسانه‌ها');
admin_flash();
?>
<h1 class="page-title">رسانه‌ها</h1>
<p class="page-sub">تصاویر را اینجا آپلود کنید و در فرم‌های محتوا انتخاب کنید. (حداکثر ۸ مگابایت — <?php
    echo e(implode('، ', ALLOWED_EXTENSIONS)); ?>)</p>

<form method="post" enctype="multipart/form-data" class="upload-box">
  <?php echo csrf_field(); ?>
  <input type="hidden" name="action" value="upload">
  <input type="file" name="files[]" multiple accept="image/*">
  <button class="btn btn-primary" type="submit">آپلود</button>
</form>

<div class="media-grid">
  <?php foreach ($media as $m): ?>
    <div class="media-card">
      <div class="media-thumb">
        <img src="<?php echo e($m['url']); ?>" alt="<?php echo e($m['file']); ?>" loading="lazy">
      </div>
      <div class="media-meta">
        <input type="text" dir="ltr" readonly value="<?php echo e($m['url']); ?>" onclick="this.select()">
        <div class="media-actions">
          <span class="mono"><?php echo e($m['uploadedAt']); ?></span>
          <?php if (strpos($m['url'], '/uploads/') === 0): ?>
            <form method="post" class="inline-form">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="url" value="<?php echo e($m['url']); ?>">
              <button class="btn btn-mini btn-danger" type="submit" data-confirm="این فایل حذف شود؟">حذف</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php
admin_footer();
