<?php /** Contact / Start a Project — messages are stored for the admin inbox. */ ?>
<?php $sent = isset($_GET['sent']) && $_GET['sent'] === '1'; ?>

<section class="contact-section">
  <div class="contact-card">
    <div class="contact-intro">
      <span class="contact-kicker mono-dir">CONTACT — START A PROJECT</span>
      <h1 class="contact-title">Let's build something together</h1>
      <p class="contact-sub">
        Tell me about your project — goals, timeline and anything else that matters.
        I usually reply within 24 hours.
      </p>
      <?php $email = setting('contactEmail'); if ($email !== ''): ?>
        <span class="footer-email mono-dir"><?php echo e($email); ?></span>
      <?php endif; ?>
    </div>

    <?php if ($sent): ?>
      <div class="contact-success" style="grid-column: 2;">✓ Your message has been sent successfully. I'll get back to you soon.</div>
    <?php endif; ?>

    <form class="contact-form" method="post" action="/contact/">
      <input type="text" name="contact_hp" class="contact-hp" tabindex="-1" autocomplete="off" aria-hidden="true">

      <div class="contact-row">
        <div class="contact-field">
          <label for="cf-name">Your name *</label>
          <input id="cf-name" type="text" name="contact_name" required maxlength="120" placeholder="Jane Doe">
        </div>
        <div class="contact-field">
          <label for="cf-email">Email *</label>
          <input id="cf-email" type="email" name="contact_email" required maxlength="160" placeholder="you@example.com">
        </div>
      </div>

      <div class="contact-field">
        <label for="cf-topic">Project type</label>
        <select id="cf-topic" name="contact_topic">
          <option>Website design &amp; development</option>
          <option>Landing page</option>
          <option>E-commerce</option>
          <option>Branding / UI system</option>
          <option>Redesign of an existing site</option>
          <option>Other</option>
        </select>
      </div>

      <div class="contact-field">
        <label for="cf-message">Message *</label>
        <textarea id="cf-message" name="contact_message" rows="6" required maxlength="4000"
                  placeholder="Project goals, timeline, budget range, links to examples..."></textarea>
      </div>

      <div class="contact-field">
        <label for="cf-file">Attachment <span class="contact-optional">(optional — brief, references, up to 20 MB)</span></label>
        <input type="hidden" name="contact_attachment" id="cf-token" value="">
        <div class="upload-zone" id="cf-zone" tabindex="0" role="button" aria-label="Upload a file">
          <input id="cf-file" type="file" class="upload-input"
                 accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.zip,.rar,.7z,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.mp4,.mov,.mp3,.ai,.psd,.fig,.sketch">
          <div class="upload-idle">
            <span class="upload-icon" aria-hidden="true">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4"/><path d="m7 9 5-5 5 5"/><path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/></svg>
            </span>
            <span class="upload-text"><strong>Drop a file here</strong> or click to browse</span>
            <span class="upload-hint mono-dir">PDF · ZIP · IMAGES · DOCS · VIDEO — MAX 20 MB</span>
          </div>
          <div class="upload-progress" hidden>
            <div class="upload-ring">
              <svg viewBox="0 0 44 44" aria-hidden="true">
                <circle class="ring-bg" cx="22" cy="22" r="19"></circle>
                <circle class="ring-fg" cx="22" cy="22" r="19"></circle>
              </svg>
              <span class="ring-pct mono-dir">0%</span>
            </div>
            <div class="upload-info">
              <span class="upload-name" dir="ltr"></span>
              <span class="upload-bar"><i></i></span>
              <span class="upload-status mono-dir">UPLOADING…</span>
            </div>
          </div>
          <div class="upload-done" hidden>
            <span class="upload-check" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path class="check-path" d="M5 12.5l4.5 4.5L19 7.5"/></svg>
            </span>
            <div class="upload-info">
              <span class="upload-name" dir="ltr"></span>
              <span class="upload-status mono-dir">ATTACHED</span>
            </div>
            <button type="button" class="upload-remove" aria-label="Remove file">✕</button>
          </div>
        </div>
        <p class="upload-error" id="cf-error" hidden></p>
      </div>

      <button class="contact-submit" id="cf-submit" type="submit" name="contact_submit" value="1">
        Send message <span class="btn-arrow">→</span>
      </button>
    </form>
  </div>
</section>
