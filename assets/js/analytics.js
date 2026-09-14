/* Mohusyn.ir — first-party, cookie-less analytics (page views, clicks, section dwell, scroll depth).
   No personal data is stored: only aggregated counters per day. */
(function () {
  "use strict";
  if (window.parent !== window) return;                      /* not inside the admin builder preview */
  if (/[?&]builder=1/.test(location.search)) return;
  if (navigator.doNotTrack === "1" || window.doNotTrack === "1") return;
  try { if (localStorage.getItem("mohusyn-no-track") === "1") return; } catch (e) {}

  var path = location.pathname.replace(/\/+$/, "") + "/";
  if (path.indexOf("/admin") === 0) return;
  var ENDPOINT = "/track.php";
  var events = [];
  var start = Date.now();
  var w = window.innerWidth;
  var device = w < 600 ? "mobile" : (w < 1024 ? "tablet" : "desktop");
  var ref = "";
  try { ref = document.referrer ? new URL(document.referrer).hostname : ""; } catch (e) {}
  if (ref === location.hostname) ref = "";

  function push(type, key, value) {
    events.push({ t: type, k: key || "", v: value || 0 });
    if (events.length >= 25) flush();
  }
  var flushTimer = null;
  function flush(final) {
    if (!events.length && !final) return;
    var payload = JSON.stringify({
      p: path, d: device, r: ref, e: events.splice(0, events.length),
      dur: final ? Math.round((Date.now() - start) / 1000) : 0
    });
    try {
      if (navigator.sendBeacon) {
        navigator.sendBeacon(ENDPOINT, new Blob([payload], { type: "text/plain" }));
      } else {
        var x = new XMLHttpRequest(); x.open("POST", ENDPOINT, true); x.setRequestHeader("Content-Type", "text/plain"); x.send(payload);
      }
    } catch (e) {}
  }

  /* ---- page view */
  push("view", path, 1);

  /* ---- clicks: every link/button gets a readable label */
  function labelFor(el) {
    var named = el.getAttribute("data-track");
    if (named) return named;
    var txt = (el.getAttribute("aria-label") || el.textContent || "").replace(/\s+/g, " ").trim().slice(0, 40);
    var cls = el.className && typeof el.className === "string" ? el.className.split(" ")[0] : el.tagName.toLowerCase();
    return (txt ? txt : cls) + (el.tagName === "A" && el.getAttribute("href") ? " → " + el.getAttribute("href").slice(0, 60) : "");
  }
  document.addEventListener("click", function (ev) {
    var el = ev.target.closest("a, button, [data-track]");
    if (!el) return;
    push("click", labelFor(el), 1);
    if (el.tagName === "A" && el.getAttribute("href") && !el.getAttribute("href").match(/^#/)) flush();
  }, true);

  /* ---- section dwell time (IntersectionObserver) */
  var sections = Array.prototype.slice.call(document.querySelectorAll("main section, main article, footer.site-footer, .blocks-area"));
  var dwell = {};
  function secName(el, i) {
    var id = el.id || (el.className && typeof el.className === "string" ? el.className.split(" ")[0] : "") || ("section-" + i);
    return id.replace(/-section$/, "");
  }
  if ("IntersectionObserver" in window && sections.length) {
    var visibleSince = {};
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        var name = en.target.getAttribute("data-sec");
        if (en.isIntersecting && en.intersectionRatio >= 0.4) {
          if (!visibleSince[name]) visibleSince[name] = Date.now();
        } else if (visibleSince[name]) {
          dwell[name] = (dwell[name] || 0) + (Date.now() - visibleSince[name]);
          visibleSince[name] = 0;
        }
      });
    }, { threshold: [0, 0.4, 1] });
    sections.forEach(function (s, i) { s.setAttribute("data-sec", secName(s, i)); io.observe(s); });
    function closeDwell() {
      Object.keys(visibleSince).forEach(function (n) {
        if (visibleSince[n]) { dwell[n] = (dwell[n] || 0) + (Date.now() - visibleSince[n]); visibleSince[n] = Date.now(); }
      });
      Object.keys(dwell).forEach(function (n) {
        var sec = Math.round(dwell[n] / 1000);
        if (sec > 0) push("dwell", n, sec);
        dwell[n] = 0;
      });
    }
  } else {
    var closeDwell = function () {};
  }

  /* ---- scroll depth milestones */
  var marks = [25, 50, 75, 100], hit = {};
  function onScroll() {
    var doc = document.documentElement;
    var max = doc.scrollHeight - window.innerHeight;
    var pct = max <= 0 ? 100 : Math.round(((window.scrollY || doc.scrollTop) / max) * 100);
    marks.forEach(function (m) { if (pct >= m && !hit[m]) { hit[m] = true; push("scroll", String(m), 1); } });
  }
  window.addEventListener("scroll", onScroll, { passive: true });
  setTimeout(onScroll, 800);

  /* ---- leave */
  function leave() { closeDwell(); flush(true); }
  document.addEventListener("visibilitychange", function () { if (document.visibilityState === "hidden") leave(); });
  window.addEventListener("pagehide", leave);
  flushTimer = setInterval(function () { closeDwell(); flush(); }, 15000);
})();
