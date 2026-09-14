/* Mohusyn.ir — theme, mobile menu, live clock, reveal */
(function () {
  "use strict";

  var root = document.documentElement;

  /* -------------------------------------------------- theme toggle */
  var themeBtn = document.querySelector("[data-theme-toggle]");
  if (themeBtn) {
    themeBtn.addEventListener("click", function () {
      var dark = root.classList.toggle("dark");
      try {
        localStorage.setItem("mohusyn-theme", dark ? "dark" : "light");
      } catch (e) { /* private mode */ }
    });
  }

  /* ------------------------------------------------ mobile menu */
  var burger = document.querySelector("[data-hamburger]");
  var mobileMenu = document.querySelector("[data-mobile-menu]");
  if (burger && mobileMenu) {
    burger.addEventListener("click", function () {
      burger.classList.toggle("open");
      mobileMenu.classList.toggle("active");
    });
  }

  /* ---------------------------------------------------- live clock */
  var weekdayEl = document.querySelector("[data-clock-weekday]");
  var dateEl = document.querySelector("[data-clock-date]");
  var timeEl = document.querySelector("[data-clock-time]");
  var tz = document.body.getAttribute("data-timezone") || "Asia/Tehran";
  var isFa = root.getAttribute("lang") === "fa";
  var localeTag = isFa ? "fa-IR" : "en-GB";

  function tick() {
    var now = new Date();
    try {
      if (weekdayEl) {
        weekdayEl.textContent = new Intl.DateTimeFormat(localeTag, {
          timeZone: tz, weekday: "long"
        }).format(now);
      }
      if (dateEl) {
        dateEl.textContent = new Intl.DateTimeFormat(localeTag, {
          timeZone: tz, day: "numeric", month: "long", year: "numeric"
        }).format(now);
      }
      if (timeEl) {
        timeEl.textContent = new Intl.DateTimeFormat("en-GB", {
          timeZone: tz, hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: false
        }).format(now);
      }
    } catch (e) { /* Intl unavailable */ }
  }
  if (dateEl || timeEl) {
    tick();
    setInterval(tick, 1000);
  }

  /* ---------------------------------------------------- scroll reveal */
  var targets = document.querySelectorAll(
    ".grid-item, .work-item, .build-item, .post-card, .profile-sidebar, .work-header, .build-intro"
  );
  if ("IntersectionObserver" in window && targets.length) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    targets.forEach(function (el) {
      el.classList.add("reveal");
      io.observe(el);
    });
  }
})();
