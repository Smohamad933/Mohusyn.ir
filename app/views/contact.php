<?php /** Contact / Start a Project — messages are stored for the admin inbox. */ ?>
<?php $sent = isset($_GET['sent']) && $_GET['sent'] === '1'; ?>

<section class="contact-section">
  <div class="contact-card">
    <span class="contact-kicker mono-dir">CONTACT — START A PROJECT</span>
    <h1 class="contact-title">Let's build something together</h1>
    <p class="contact-sub">
      Tell me about your project — goals, timeline and anything else that matters.
      I usually reply within 24 hours.
    </p>

    <?php if ($sent): ?>
      <div class="contact-success">✓ Your message has been sent successfully. I'll get back to you soon.</div>
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

      <button class="contact-submit" type="submit" name="contact_submit" value="1">
        Send message <span class="btn-arrow">→</span>
      </button>
    </form>
  </div>
</section>
