<?php
/**
 * Admin — contact message inbox (submissions from the public contact form).
 * UI in Persian; message content is whatever the visitor wrote.
 */

require __DIR__ . '/_bootstrap.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        header('Location: messages.php?msg=csrf');
        exit;
    }

    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $msgId = isset($_POST['id']) ? (string) $_POST['id'] : '';
    $messages = load_json('messages.json', array());

    if ($action === 'delete') {
        $messages = array_values(array_filter($messages, function ($m) use ($msgId) {
            return !isset($m['id']) || $m['id'] !== $msgId;
        }));
        save_json('messages.json', $messages);
        header('Location: messages.php?msg=deleted');
        exit;
    }

    if ($action === 'toggle-read') {
        foreach ($messages as &$m) {
            if (isset($m['id']) && $m['id'] === $msgId) {
                $m['read'] = empty($m['read']);
                break;
            }
        }
        unset($m);
        save_json('messages.json', $messages);
        header('Location: messages.php?msg=saved');
        exit;
    }

    header('Location: messages.php?msg=error');
    exit;
}

$messages = load_json('messages.json', array());

admin_header('messages', 'پیام‌ها');
admin_flash();
?>
<div class="page-head">
  <h1 class="page-title">پیام‌های تماس <span class="count mono"><?php echo e(count($messages)); ?></span></h1>
</div>
<p class="page-sub">پیام‌هایی که بازدیدکننده‌ها از فرم «شروع پروژه» در سایت ارسال کرده‌اند.</p>

<?php if (empty($messages)): ?>
  <p class="empty-note">هنوز پیامی دریافت نشده است. فرم تماس در آدرس <span dir="ltr" class="mono">/contact/</span> قرار دارد.</p>
<?php else: ?>
  <?php foreach ($messages as $m):
      $isUnread = empty($m['read']); ?>
    <div class="msg-card <?php echo $isUnread ? 'msg-unread' : ''; ?>">
      <div class="msg-card-head">
        <span class="msg-dot <?php echo $isUnread ? 'unread' : ''; ?>"></span>
        <div class="msg-meta">
          <strong dir="ltr"><?php echo e($m['name']); ?></strong>
          <span class="msg-email mono" dir="ltr"><?php echo e($m['email']); ?></span>
          <?php if (isset($m['topic']) && $m['topic'] !== ''): ?>
            <span class="badge"><?php echo e($m['topic']); ?></span>
          <?php endif; ?>
        </div>
        <span class="msg-date mono"><?php echo e($m['createdAt']); ?></span>
      </div>
      <p class="msg-body" dir="ltr"><?php echo nl2br(e($m['message'])); ?></p>
      <div class="msg-actions">
        <a class="btn btn-mini" href="mailto:<?php echo e($m['email']); ?>">↩ پاسخ با ایمیل</a>
        <form method="post" class="inline-form">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="toggle-read">
          <input type="hidden" name="id" value="<?php echo e($m['id']); ?>">
          <button class="btn btn-mini" type="submit"><?php echo $isUnread ? 'علامت: خوانده شد' : 'برگرداندن به خوانده‌نشده'; ?></button>
        </form>
        <form method="post" class="inline-form">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?php echo e($m['id']); ?>">
          <button class="btn btn-mini btn-danger" type="submit" data-confirm="این پیام برای همیشه حذف شود؟">حذف</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
<?php
admin_footer();
