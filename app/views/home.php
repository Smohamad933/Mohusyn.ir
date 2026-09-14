<?php
/** Landing page (hdshy-style) — expects: $locale */
$projects = published_items('projects');
$services = published_items('services');
$skills = published_items('skills');
$collaborators = published_items('collaborators');
$experiences = published_items('experiences');
$posts = published_items('posts');

$showProjects = page_shows('home', 'showProjects') && !empty($projects);
$showCollaborators = page_shows('home', 'showCollaborators') && !empty($collaborators);
$showServices = page_shows('home', 'showServices') && !empty($services);
$showSkills = page_shows('home', 'showSkills') && !empty($skills);
$showAvailability = page_shows('home', 'showAvailability');
$showPosts = page_shows('home', 'showPosts') && !empty($posts);
?>

<!-- ====================================================== HERO/INTRO -->
<?php
$heroImg = trim((string) setting('heroImage'));
if ($heroImg === '') { $heroImg = trim((string) setting('profileImage')); }
if ($heroImg !== '' && strpos($heroImg, 'uploads/') === 0) { $heroImg = '/' . $heroImg; }
?>
<section class="hero-section">
  <div class="hero-card<?php echo $heroImg === '' ? ' hero-card--solo' : ''; ?>">
    <?php if ($heroImg !== ''): ?>
      <img class="hero-bg" src="<?php echo e($heroImg); ?>" alt="<?php echo e(setting_bi('fullName', $locale)); ?>" loading="eager">
      <div class="hero-overlay" aria-hidden="true"></div>
    <?php endif; ?>
    <div class="hero-inner">
      <h1 class="hero-name"><?php echo e(setting_bi('fullName', $locale)); ?></h1>
      <p class="hero-bio"><?php echo e(setting_bi('bio', $locale)); ?></p>
      <div class="hero-actions">
        <a class="hero-btn hero-btn-solid" href="/contact/">
          <?php echo e(setting_bi('cta', $locale)); ?>
        </a>
        <?php if (setting('workSiteUrl') !== ''): ?>
          <a class="hero-btn" href="<?php echo e(setting('workSiteUrl')); ?>" target="_blank" rel="noopener">
            <?php echo e(setting_bi('workSiteLabel', $locale)); ?> ↗
          </a>
        <?php endif; ?>
        <a class="hero-btn" href="/about/">About me</a>
      </div>
    </div>
  </div>
</section>

<!-- ================================================= PORTFOLIO GRID -->
<?php if ($showProjects): ?>
<?php
$homeLimit = (int) setting('homeProjectsLimit', 6);
if ($homeLimit <= 0) { $homeLimit = 6; }
$homeProjects = array_slice($projects, 0, $homeLimit);
?>
<section class="portfolio-section">
  <div class="portfolio-grid">
    <?php foreach ($homeProjects as $project):
        $href = !empty($project['slug']) ? '/work/' . $project['slug'] . '/' : (!empty($project['link']) ? $project['link'] : '#');
        $external = empty($project['slug']) && !empty($project['link']);
    ?>
      <a class="portfolio-card" href="<?php echo e($href); ?>" <?php echo $external ? 'target="_blank" rel="noopener"' : ''; ?>>
        <div class="portfolio-cover">
          <img src="<?php echo e($project['image']); ?>" alt="<?php echo e(bi($project['title'], $locale)); ?> portfolio cover" loading="lazy">
        </div>
        <div class="portfolio-meta">
          <span class="mono portfolio-cat"><?php echo e(bi($project['category'], $locale)); ?></span>
          <h2 class="portfolio-title"><?php echo e(bi($project['title'], $locale)); ?></h2>
          <span class="mono portfolio-open">Open →</span>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
  <?php if (count($projects) > count($homeProjects)): ?>
    <div class="portfolio-more">
      <a class="portfolio-more-btn" href="/work/">View all <?php echo e(count($projects)); ?> projects <span class="btn-arrow">→</span></a>
    </div>
  <?php endif; ?>
</section>
<?php endif; ?>

<!-- ==================================================== COLLABORATORS -->
<?php if ($showCollaborators): ?>
<section class="collab-section">
  <div class="container-box">
    <p class="collab-intro"><?php echo e(setting_bi('collaboratorsIntro', $locale, 'collaboratorsIntro')); ?></p>
    <div class="collaborators-grid">
      <?php foreach ($collaborators as $c): ?>
        <div class="collaborator-item">
          <?php if (!empty($c['link'])): ?>
            <a href="<?php echo e($c['link']); ?>" target="_blank" rel="noopener"><?php echo e($c['name']); ?></a>
          <?php else: ?>
            <?php echo e($c['name']); ?>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ==================================================== EXPERIENCE -->
<?php if (!empty($experiences)): ?>
<section class="work-list-section">
  <div class="container-box work-section">
    <div class="work-header">
      <span class="work-subtitle"><?php echo e(setting_bi('experienceKicker', $locale, 'experienceKicker')); ?></span>
      <h2 class="work-main-title"><?php echo e(setting_bi('experienceTitle', $locale, 'experienceTitle')); ?></h2>
      <p class="work-desc"><?php echo e(setting_bi('experienceSubtitle', $locale, 'experienceSubtitle')); ?></p>
    </div>

    <ol class="work-list work-list-numbered">
      <?php foreach ($experiences as $exp): ?>
        <li class="work-item">
          <div class="work-info">
            <div class="work-title-row">
              <h3 class="work-name"><?php echo e(bi($exp['role'], $locale)); ?></h3>
              <span class="work-tag"><?php echo e(bi($exp['company'], $locale)); ?></span>
            </div>
            <p class="work-text"><?php echo e(bi($exp['description'], $locale)); ?></p>
          </div>
          <?php if (!empty($exp['link'])): ?>
            <a class="work-action-btn" href="<?php echo e($exp['link']); ?>" target="_blank" rel="noopener">
              <?php echo e(t('experience.visit', $locale)); ?>
            </a>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
<?php endif; ?>

<!-- ================================================= CAPABILITIES -->
<?php if ($showServices): ?>
<section class="caps-section">
  <div class="container-box caps-box">
    <div class="build-intro">
      <span class="build-subtitle"><?php echo e(setting_bi('servicesKicker', $locale, 'servicesKicker')); ?></span>
      <h2 class="build-main-title"><?php echo e(setting_bi('servicesTitle', $locale, 'servicesTitle')); ?></h2>
      <p class="build-desc"><?php echo e(setting_bi('servicesSubtitle', $locale, 'servicesSubtitle')); ?></p>
    </div>
    <div class="build-list">
      <?php foreach ($services as $i => $service): ?>
        <div class="build-item">
          <span class="build-number"><?php echo e(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
          <div class="build-content">
            <h3><?php echo e(bi($service['title'], $locale)); ?></h3>
            <p><?php echo e(bi($service['description'], $locale)); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ======================================================= SKILLS -->
<?php if ($showSkills): ?>
<section class="skills-section">
  <div class="container-box skills-box">
    <div class="skills-header">
      <span class="skills-subtitle"><?php echo e(setting_bi('skillsKicker', $locale, 'skillsKicker')); ?></span>
    </div>
    <div class="marquee-container" dir="ltr">
      <div class="marquee-track">
        <?php for ($rep = 0; $rep < 2; $rep++): ?>
          <div class="skills-group">
            <?php foreach ($skills as $skill): ?>
              <span class="skill-box"><?php echo e($skill['label']); ?></span>
            <?php endforeach; ?>
          </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ================================================= AVAILABILITY -->
<?php if ($showAvailability): ?>
<section class="avail-section">
  <div class="container-box avail-card">
    <div class="avail-info">
      <span class="avail-label mono"><?php echo e(setting('availTitle', 'Current Availability')); ?></span>
      <div class="avail-main">
        <h2 class="avail-period"><?php echo e(setting('availPeriod', '')); ?></h2>
        <?php if (setting('availSpots') !== ''): ?>
          <span class="avail-spots"><?php echo e(setting('availSpots')); ?></span>
        <?php endif; ?>
      </div>
    </div>
    <a class="avail-cta" href="/contact/">
      <?php echo e(setting('availCta', 'Apply for a Project')); ?>
      <span class="btn-arrow">→</span>
    </a>
  </div>
</section>
<?php endif; ?>

<!-- ==================================================== BLOG HOME -->
<?php if ($showPosts): ?>
<section class="posts-section">
  <div class="container-box posts-box">
    <div class="posts-head">
      <span class="mono posts-kicker">Writing</span>
      <a class="mono posts-all" href="/blog/">All posts →</a>
    </div>
    <div class="posts-grid">
      <?php foreach (array_slice($posts, 0, 3) as $post): ?>
        <a class="post-mini" href="/blog/<?php echo e($post['slug']); ?>/">
          <?php if (!empty($post['cover'])): ?>
            <div class="post-mini-cover">
              <img src="<?php echo e($post['cover']); ?>" alt="" loading="lazy">
            </div>
          <?php endif; ?>
          <h3 class="post-mini-title"><?php echo e(bi($post['title'], $locale)); ?></h3>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ================================================== CUSTOM BLOCKS -->
<?php render_blocks('home'); ?>
