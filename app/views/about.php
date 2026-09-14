<?php
/** About me page — expects: $locale */
$experiences = published_items('experiences');
$skills = published_items('skills');
$collaborators = published_items('collaborators');

$showExperiences = page_shows('about', 'showExperiences') && !empty($experiences);
$showSkills = page_shows('about', 'showSkills') && !empty($skills);
$showCollaborators = page_shows('about', 'showCollaborators') && !empty($collaborators);
?>

<!-- ===================================================== ABOUT HEADER -->
<div class="work-container-wrapper">
  <section class="work-section">
    <div class="work-header">
      <span class="work-subtitle"><?php echo e(setting('brand', 'MOHUSYN')); ?></span>
      <h1 class="work-main-title"><?php echo e(page_setting('about', 'title', $locale)); ?></h1>
      <p class="work-desc"><?php echo e(page_setting('about', 'subtitle', $locale)); ?></p>
    </div>

    <div class="about-layout">
      <div class="about-avatar">
        <img src="<?php echo e(setting('profileImage')); ?>" alt="<?php echo e(setting_bi('fullName', $locale)); ?>">
      </div>
      <div class="about-text">
        <h2 class="about-name"><?php echo e(setting_bi('fullName', $locale)); ?></h2>
        <p class="about-bio"><?php echo e(setting_bi('bio', $locale)); ?></p>
        <?php if (setting('workSiteUrl') !== ''): ?>
          <a class="work-action-btn" href="<?php echo e(setting('workSiteUrl')); ?>" target="_blank" rel="noopener">
            <?php echo e(setting_bi('workSiteLabel', $locale)); ?> ↗
          </a>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>

<!-- ======================================================== EXPERIENCE -->
<?php if ($showExperiences): ?>
<div class="work-container-wrapper">
  <section class="work-section">
    <div class="work-header">
      <span class="work-subtitle"><?php echo e(setting_bi('experienceKicker', $locale, 'experienceKicker')); ?></span>
      <h2 class="work-main-title"><?php echo e(setting_bi('experienceTitle', $locale, 'experienceTitle')); ?></h2>
      <p class="work-desc"><?php echo e(setting_bi('experienceSubtitle', $locale, 'experienceSubtitle')); ?></p>
    </div>

    <div class="work-list">
      <?php foreach ($experiences as $exp): ?>
        <div class="work-item">
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
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>
<?php endif; ?>

<!-- ======================================================== COLLABORATORS -->
<?php if ($showCollaborators): ?>
<section class="collaborators-section">
  <h3 class="collaborators-title"><?php echo e(setting_bi('collaborators', $locale, 'collaborators')); ?></h3>
  <div class="collaborators-grid">
    <?php foreach ($collaborators as $c): ?>
      <div class="collaborator-item"><?php echo e($c['name']); ?></div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- =============================================================== SKILLS -->
<?php if ($showSkills): ?>
<div class="skills-marquee-wrapper">
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
<?php endif; ?>

<!-- ================================================== CUSTOM BLOCKS -->
<?php render_blocks('about'); ?>
