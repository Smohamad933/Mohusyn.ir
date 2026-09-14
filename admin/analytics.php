<?php
/**
 * Admin — بازدید و تجربهٔ کاربری (first-party analytics dashboard).
 * Reads data/analytics.json written by /track.php.
 */

require __DIR__ . '/_bootstrap.php';
require_auth();

$ranges = array('7' => '۷ روز', '30' => '۳۰ روز', '90' => '۹۰ روز');
$range = isset($_GET['range']) && isset($ranges[$_GET['range']]) ? (int) $_GET['range'] : 30;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok() && isset($_POST['action']) && $_POST['action'] === 'reset') {
    save_json('analytics.json', array());
    header('Location: analytics.php?msg=deleted');
    exit;
}

$all = load_json('analytics.json', array());
$pageNames = array('/' => 'صفحهٔ اصلی', '/work/' => 'پورتفولیو', '/about/' => 'دربارهٔ من', '/contact/' => 'تماس', '/blog/' => 'بلاگ');
$sectionNames = array(
    'hero' => 'هدر / معرفی', 'portfolio' => 'نمونه‌کارها', 'collab' => 'همکاران', 'collaborators' => 'همکاران', 'work-list' => 'تجربه‌ها',
    'work' => 'متن اصلی', 'caps' => 'خدمات', 'skills' => 'مهارت‌ها', 'avail' => 'وضعیت همکاری', 'posts' => 'نوشته‌ها',
    'contact' => 'فوتر / تماس', 'contact-section' => 'فرم تماس', 'blocks-area' => 'بلوک‌های سفارشی', 'archive-head' => 'سرتیتر آرشیو',
    'case-study' => 'کیس‌استادی', 'section' => 'بخش', 'skills-marquee-wrapper' => 'نوار مهارت‌ها',
);
function pretty_page($p, $names) { return isset($names[$p]) ? $names[$p] : $p; }
function pretty_section($s, $names) { return isset($names[$s]) ? $names[$s] : $s; }

/* ---------------------------------------------------------- aggregate */
$days = array();
for ($i = $range - 1; $i >= 0; $i--) {
    $days[] = date('Y-m-d', strtotime("-$i days"));
}
$tot = array('sessions' => 0, 'duration' => 0, 'views' => array(), 'device' => array(), 'referrers' => array(), 'clicks' => array(), 'dwell' => array(), 'dwellHits' => array(), 'scroll' => array());
$series = array();
foreach ($days as $d) {
    $day = isset($all[$d]) && is_array($all[$d]) ? $all[$d] : array();
    $series[$d] = isset($day['sessions']) ? (int) $day['sessions'] : 0;
    $tot['sessions'] += $series[$d];
    $tot['duration'] += isset($day['duration']) ? (int) $day['duration'] : 0;
    foreach (array('views', 'device', 'referrers', 'clicks', 'dwell', 'dwellHits', 'scroll') as $k) {
        if (!isset($day[$k]) || !is_array($day[$k])) {
            continue;
        }
        foreach ($day[$k] as $key => $n) {
            $tot[$k][$key] = (isset($tot[$k][$key]) ? $tot[$k][$key] : 0) + (int) $n;
        }
    }
}
arsort($tot['views']);
arsort($tot['clicks']);
arsort($tot['referrers']);
$totalViews = array_sum($tot['views']);
$avgDur = $tot['sessions'] > 0 ? round($tot['duration'] / $tot['sessions']) : 0;
$maxSeries = max(1, max($series ? $series : array(0)));

/* clicks -> percentage of all clicks */
$totalClicks = array_sum($tot['clicks']);
$topClicks = array_slice($tot['clicks'], 0, 12, true);

/* dwell -> avg seconds per section (weighted by hits), sorted */
$dwellAvg = array();
foreach ($tot['dwell'] as $key => $sec) {
    $hits = isset($tot['dwellHits'][$key]) ? max(1, $tot['dwellHits'][$key]) : 1;
    $dwellAvg[$key] = array('avg' => $sec / $hits, 'total' => $sec, 'hits' => $hits);
}
uasort($dwellAvg, function ($a, $b) { if ($a['total'] == $b['total']) { return 0; } return $b['total'] > $a['total'] ? 1 : -1; });
$topDwell = array_slice($dwellAvg, 0, 12, true);
$maxDwell = 1;
foreach ($topDwell as $d) { $maxDwell = max($maxDwell, $d['avg']); }

/* scroll funnel per page */
$scrollByPage = array();
foreach ($tot['scroll'] as $key => $n) {
    list($p, $m) = array_pad(explode('|', $key, 2), 2, '');
    $scrollByPage[$p][$m] = $n;
}

/* contact funnel */
$contactViews = isset($tot['views']['/contact/']) ? $tot['views']['/contact/'] : 0;
$ctaClicks = 0;
foreach ($tot['clicks'] as $key => $n) {
    if (stripos($key, '/contact/') !== false && strpos($key, '/contact/|') !== 0) { $ctaClicks += $n; }
}
$sendClicks = 0;
foreach ($tot['clicks'] as $key => $n) {
    if (strpos($key, '/contact/|Send message') === 0) { $sendClicks += $n; }
}
$messagesCount = 0;
foreach (load_json('messages.json', array()) as $m) {
    if (isset($m['createdAt']) && strtotime($m['createdAt']) >= strtotime($days[0])) { $messagesCount++; }
}

function pct($a, $b) { return $b > 0 ? round($a / $b * 100) : 0; }
function fmt_dur($s) { $s = (int) $s; return $s >= 60 ? floor($s / 60) . ' دقیقه ' . ($s % 60) . ' ثانیه' : $s . ' ثانیه'; }

admin_header('analytics', 'بازدید و تجربهٔ کاربری');
admin_flash();
?>
<div class="page-head">
  <h1 class="page-title">بازدید و تجربهٔ کاربری</h1>
  <div class="page-head-actions">
    <div class="page-switch">
      <?php foreach ($ranges as $rk => $rl): ?>
        <a class="btn btn-mini <?php echo (int) $rk === $range ? 'is-on' : ''; ?>" href="analytics.php?range=<?php echo e($rk); ?>"><?php echo e($rl); ?></a>
      <?php endforeach; ?>
    </div>
    <form method="post" class="inline-form">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="action" value="reset">
      <button class="btn btn-mini btn-danger" type="submit" data-confirm="همهٔ آمار پاک شود؟">پاک‌کردن آمار</button>
    </form>
  </div>
</div>
<p class="page-sub">آمار داخلی و بدون کوکی (هیچ دادهٔ شخصی ذخیره نمی‌شود). می‌بینید کاربران کدام دکمه‌ها را می‌زنند، روی کدام بخش‌ها مکث می‌کنند و تا کجا اسکرول می‌کنند.</p>

<?php if ($tot['sessions'] === 0): ?>
  <p class="empty-note">هنوز بازدیدی ثبت نشده. به‌محض بازدید از سایت (خارج از پنل) آمار اینجا ظاهر می‌شود.</p>
<?php endif; ?>

<!-- ============================================================ KPIs -->
<div class="cards">
  <div class="stat-card"><span class="stat-num"><?php echo e(fa_digits($tot['sessions'])); ?></span><span class="stat-label">بازدید</span><span class="stat-pub mono"><?php echo e(fa_digits($totalViews)); ?> نمایش صفحه</span></div>
  <div class="stat-card"><span class="stat-num"><?php echo e(fa_digits($avgDur)); ?><small class="stat-unit">ث</small></span><span class="stat-label">میانگین زمان حضور</span><span class="stat-pub mono"><?php echo e(fmt_dur($avgDur)); ?></span></div>
  <div class="stat-card"><span class="stat-num"><?php echo e(fa_digits($totalClicks)); ?></span><span class="stat-label">کلیک</span><span class="stat-pub mono"><?php echo e(fa_digits($tot['sessions'] ? round($totalClicks / $tot['sessions'], 1) : 0)); ?> به‌ازای هر بازدید</span></div>
  <div class="stat-card"><span class="stat-num"><?php echo e(fa_digits($messagesCount)); ?></span><span class="stat-label">پیام دریافتی</span><span class="stat-pub mono">نرخ تبدیل <?php echo e(fa_digits(pct($messagesCount, $tot['sessions']))); ?>٪</span></div>
</div>

<!-- ============================================================ trend -->
<div class="dash-panel">
  <div class="dash-panel-head"><h2>روند بازدید روزانه</h2><span class="mono"><?php echo e($ranges[(string) $range]); ?> اخیر</span></div>
  <div class="chart-bars" dir="ltr">
    <?php foreach ($series as $d => $n): ?>
      <div class="chart-col" title="<?php echo e($d . ' — ' . $n . ' بازدید'); ?>">
        <span class="chart-val"><?php echo $n > 0 ? e(fa_digits($n)) : ''; ?></span>
        <i style="height: <?php echo e(max(2, round($n / $maxSeries * 100))); ?>%"></i>
        <?php if ($range <= 7 || date('j', strtotime($d)) === '1' || $d === end($days)): ?><small><?php echo e(date('m/d', strtotime($d))); ?></small><?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="dash-cols ana-cols">
  <!-- ========================================================== clicks -->
  <div class="dash-panel">
    <div class="dash-panel-head"><h2>پرکلیک‌ترین دکمه‌ها و لینک‌ها</h2><span class="mono">درصد از کل کلیک‌ها</span></div>
    <?php if (empty($topClicks)): ?><p class="empty-note">هنوز کلیکی ثبت نشده.</p><?php endif; ?>
    <?php foreach ($topClicks as $key => $n): list($p, $label) = array_pad(explode('|', $key, 2), 2, ''); $pc = pct($n, $totalClicks); ?>
      <div class="hbar">
        <div class="hbar-head"><span class="hbar-label" dir="ltr" title="<?php echo e($label); ?>"><?php echo e($label); ?></span><span class="hbar-meta"><span class="mono"><?php echo e(pretty_page($p, $pageNames)); ?></span> <b><?php echo e(fa_digits($pc)); ?>٪</b> <span class="mono"><?php echo e(fa_digits($n)); ?></span></span></div>
        <div class="hbar-track"><i style="width: <?php echo e($pc); ?>%"></i></div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- ========================================================== dwell -->
  <div class="dash-panel">
    <div class="dash-panel-head"><h2>کجا مکث می‌کنند؟</h2><span class="mono">میانگین ثانیه در هر بخش</span></div>
    <?php if (empty($topDwell)): ?><p class="empty-note">هنوز داده‌ای ثبت نشده.</p><?php endif; ?>
    <?php foreach ($topDwell as $key => $d): list($p, $sec) = array_pad(explode('|', $key, 2), 2, ''); $w = round($d['avg'] / $maxDwell * 100); ?>
      <div class="hbar">
        <div class="hbar-head"><span class="hbar-label"><?php echo e(pretty_section($sec, $sectionNames)); ?></span><span class="hbar-meta"><span class="mono"><?php echo e(pretty_page($p, $pageNames)); ?></span> <b><?php echo e(fa_digits(round($d['avg'], 1))); ?> ث</b> <span class="mono"><?php echo e(fa_digits($d['hits'])); ?> بار</span></span></div>
        <div class="hbar-track hbar-warm"><i style="width: <?php echo e(max(2, $w)); ?>%"></i></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="dash-cols ana-cols">
  <!-- ========================================================== pages + scroll -->
  <div class="dash-panel">
    <div class="dash-panel-head"><h2>صفحات و عمق اسکرول</h2><span class="mono">چند درصد تا کجا رسیدند</span></div>
    <?php foreach (array_slice($tot['views'], 0, 8, true) as $p => $n): $sc = isset($scrollByPage[$p]) ? $scrollByPage[$p] : array(); ?>
      <div class="page-row">
        <div class="page-row-head"><strong><?php echo e(pretty_page($p, $pageNames)); ?></strong> <span class="mono" dir="ltr"><?php echo e($p); ?></span><span class="page-row-n"><?php echo e(fa_digits($n)); ?> نمایش · <?php echo e(fa_digits(pct($n, $totalViews))); ?>٪</span></div>
        <div class="funnel">
          <?php foreach (array('25', '50', '75', '100') as $m): $v = isset($sc[$m]) ? $sc[$m] : 0; $pp = pct($v, $n); ?>
            <div class="funnel-step" title="<?php echo e($m . '% — ' . $v); ?>"><i style="height: <?php echo e(max(3, $pp)); ?>%"></i><small><?php echo e(fa_digits($m)); ?>٪</small><b><?php echo e(fa_digits($pp)); ?>٪</b></div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="dash-panel">
    <div class="dash-panel-head"><h2>قیف تماس</h2><span class="mono">از بازدید تا پیام</span></div>
    <?php
    $funnel = array(
        array('بازدید سایت', $tot['sessions']),
        array('کلیک روی «شروع پروژه / تماس»', $ctaClicks),
        array('دیدن فرم تماس', $contactViews),
        array('زدن دکمهٔ ارسال', $sendClicks),
        array('پیام ثبت‌شده', $messagesCount),
    );
    $base = max(1, $tot['sessions']);
    foreach ($funnel as $st): ?>
      <div class="hbar">
        <div class="hbar-head"><span class="hbar-label"><?php echo e($st[0]); ?></span><span class="hbar-meta"><b><?php echo e(fa_digits(pct($st[1], $base))); ?>٪</b> <span class="mono"><?php echo e(fa_digits($st[1])); ?></span></span></div>
        <div class="hbar-track"><i style="width: <?php echo e(max(1, pct($st[1], $base))); ?>%"></i></div>
      </div>
    <?php endforeach; ?>

    <h3 class="ana-sub">دستگاه‌ها</h3>
    <?php $devTot = max(1, array_sum($tot['device'])); foreach (array('desktop' => 'دسکتاپ', 'mobile' => 'موبایل', 'tablet' => 'تبلت') as $dk => $dl): $v = isset($tot['device'][$dk]) ? $tot['device'][$dk] : 0; ?>
      <div class="hbar hbar-slim"><div class="hbar-head"><span class="hbar-label"><?php echo e($dl); ?></span><span class="hbar-meta"><b><?php echo e(fa_digits(pct($v, $devTot))); ?>٪</b></span></div><div class="hbar-track"><i style="width: <?php echo e(pct($v, $devTot)); ?>%"></i></div></div>
    <?php endforeach; ?>

    <?php if (!empty($tot['referrers'])): ?>
      <h3 class="ana-sub">از کجا می‌آیند؟</h3>
      <ul class="ref-list">
        <?php foreach (array_slice($tot['referrers'], 0, 8, true) as $r => $n): ?>
          <li><span dir="ltr"><?php echo e($r); ?></span><span class="mono"><?php echo e(fa_digits($n)); ?></span></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<div class="tips">
  <h2>چطور بخوانیم؟</h2>
  <ul>
    <li><strong>پرکلیک‌ترین‌ها</strong>: اگر دکمه‌ای که برایتان مهم است (مثلاً «Start a project») پایین لیست است، جایش را بالاتر ببرید یا رنگش را پررنگ‌تر کنید.</li>
    <li><strong>مکث</strong>: بخش‌هایی با میانگین بالا یعنی محتوایشان جذاب است؛ بخش‌هایی که تقریباً صفرند یا دیده نمی‌شوند یا سریع رد می‌شوند.</li>
    <li><strong>عمق اسکرول</strong>: افت شدید بین ۲۵٪ و ۵۰٪ یعنی بالای صفحه کاربران را از دست می‌دهد.</li>
    <li>کاربرانی که «Do Not Track» دارند شمرده نمی‌شوند. آمار در <code>data/analytics.json</code> نگه‌داری می‌شود (۱۲۰ روز).</li>
  </ul>
</div>
<?php
admin_footer();
