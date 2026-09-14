/* Mohusyn.ir — visual builder, iframe side.
   Loaded only when the page is opened from the admin builder (?builder=1 + admin session).
   Talks to the parent (admin/builder.php) via postMessage. */
(function () {
  "use strict";
  if (window.parent === window) return;

  var area = document.querySelector("[data-blocks-area]");
  if (!area) return;
  var pageKey = area.getAttribute("data-blocks-area");
  var blocks = [];
  var selectedId = null;
  var dragId = null;       // block being re-ordered inside the frame
  var placeholder = null;

  function send(msg) { msg.source = "mohusyn-builder-frame"; window.parent.postMessage(msg, window.location.origin); }
  function esc(s) { return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) { return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]; }); }

  /* ------------------------------------------------------------ render */
  function renderBlock(b) {
    var d = b.data || {};
    var html = "";
    if (b.type === "heading") {
      var lvl = ["h2", "h3", "h4"].indexOf(d.level) >= 0 ? d.level : "h2";
      html = '<div class="blk blk-heading"><' + lvl + ' class="blk-heading-text">' + (esc(d.text) || '<span class="bf-empty">Heading…</span>') + "</" + lvl + "></div>";
    } else if (b.type === "text") {
      html = '<div class="blk blk-text"><p>' + (esc(d.text).replace(/\n/g, "<br>") || '<span class="bf-empty">Paragraph text…</span>') + "</p></div>";
    } else if (b.type === "image") {
      var al = ["left", "center", "right"].indexOf(d.align) >= 0 ? d.align : "center";
      var w = parseInt(d.width, 10) > 0 ? ' style="max-width:' + parseInt(d.width, 10) + 'px"' : "";
      html = '<div class="blk blk-image blk-align-' + al + '"><div class="blk-image-box"' + w + ">" +
        (d.src ? '<img src="' + esc(d.src) + '" alt="' + esc(d.alt) + '">' : '<div class="bf-empty-img">تصویر انتخاب نشده</div>') + "</div></div>";
    } else if (b.type === "button") {
      var al2 = ["left", "center", "right"].indexOf(d.align) >= 0 ? d.align : "left";
      html = '<div class="blk blk-button blk-align-' + al2 + '"><a class="blk-btn' + (d.style === "outline" ? " blk-btn-outline" : "") + '" href="#">' + (esc(d.label) || "Button") + "</a></div>";
    } else if (b.type === "spacer") {
      var h = Math.max(0, Math.min(400, parseInt(d.height, 10) || 40));
      html = '<div class="blk blk-spacer" style="height:' + h + 'px"></div>';
    } else {
      html = '<div class="blk blk-divider"></div>';
    }
    return html;
  }

  var labels = { heading: "تیتر", text: "متن", image: "تصویر", button: "دکمه", spacer: "فاصله", divider: "خط" };

  function render() {
    area.innerHTML = "";
    area.classList.add("bf-area");
    if (!blocks.length) {
      var empty = document.createElement("div");
      empty.className = "bf-drop-empty";
      empty.textContent = "بلوک‌ها را از پنل کناری اینجا رها کنید";
      area.appendChild(empty);
    }
    blocks.forEach(function (b) {
      var wrap = document.createElement("div");
      wrap.className = "bf-block" + (b.id === selectedId ? " bf-selected" : "") + (b.published === false ? " bf-hidden" : "");
      wrap.setAttribute("data-bf-id", b.id);
      wrap.setAttribute("draggable", "true");
      wrap.innerHTML =
        '<div class="bf-tools"><span class="bf-tag">' + (labels[b.type] || b.type) + (b.published === false ? " · مخفی" : "") + "</span>" +
        '<button type="button" data-bf-act="up" title="بالا">▲</button>' +
        '<button type="button" data-bf-act="down" title="پایین">▼</button>' +
        '<button type="button" data-bf-act="edit" title="ویرایش">✎</button>' +
        '<button type="button" data-bf-act="delete" title="حذف">✕</button></div>' +
        renderBlock(b);
      area.appendChild(wrap);
    });
  }

  /* ------------------------------------------------------------ events */
  area.addEventListener("click", function (ev) {
    var btn = ev.target.closest("[data-bf-act]");
    var blk = ev.target.closest(".bf-block");
    if (ev.target.closest("a")) ev.preventDefault();
    if (!blk) return;
    var id = blk.getAttribute("data-bf-id");
    if (btn) {
      var act = btn.getAttribute("data-bf-act");
      if (act === "edit") { send({ type: "select", id: id }); }
      else { send({ type: "action", action: act, id: id }); }
      ev.stopPropagation();
      return;
    }
    send({ type: "select", id: id });
  });

  /* block the whole page from navigating away while editing */
  document.addEventListener("click", function (ev) {
    var a = ev.target.closest("a");
    if (a && !a.closest(".bf-block")) { ev.preventDefault(); }
  }, true);
  document.addEventListener("submit", function (ev) { ev.preventDefault(); }, true);

  /* ---- drag & drop: palette (from parent) + reorder (inside the frame) */
  function ensurePlaceholder() {
    if (!placeholder) {
      placeholder = document.createElement("div");
      placeholder.className = "bf-placeholder";
      placeholder.textContent = "اینجا رها کنید";
    }
    return placeholder;
  }
  function positionPlaceholder(clientY) {
    var ph = ensurePlaceholder();
    var items = Array.prototype.slice.call(area.querySelectorAll(".bf-block")).filter(function (el) { return el.getAttribute("data-bf-id") !== dragId; });
    var before = null;
    for (var i = 0; i < items.length; i++) {
      var r = items[i].getBoundingClientRect();
      if (clientY < r.top + r.height / 2) { before = items[i]; break; }
    }
    var emptyNote = area.querySelector(".bf-drop-empty");
    if (emptyNote) emptyNote.style.display = "none";
    if (before) area.insertBefore(ph, before); else area.appendChild(ph);
  }
  function placeholderIndex() {
    if (!placeholder || !placeholder.parentNode) return blocks.length;
    var idx = 0;
    var kids = area.children;
    for (var i = 0; i < kids.length; i++) {
      if (kids[i] === placeholder) break;
      if (kids[i].classList.contains("bf-block") && kids[i].getAttribute("data-bf-id") !== dragId) idx++;
    }
    return idx;
  }
  function clearPlaceholder() {
    if (placeholder && placeholder.parentNode) placeholder.parentNode.removeChild(placeholder);
    var emptyNote = area.querySelector(".bf-drop-empty");
    if (emptyNote) emptyNote.style.display = "";
    document.body.classList.remove("bf-dragging");
  }

  area.addEventListener("dragstart", function (ev) {
    var blk = ev.target.closest(".bf-block");
    if (!blk) return;
    dragId = blk.getAttribute("data-bf-id");
    blk.classList.add("bf-ghost");
    ev.dataTransfer.effectAllowed = "move";
    try { ev.dataTransfer.setData("text/plain", "move:" + dragId); } catch (e) {}
  });
  area.addEventListener("dragend", function () {
    dragId = null;
    area.querySelectorAll(".bf-ghost").forEach(function (el) { el.classList.remove("bf-ghost"); });
    clearPlaceholder();
  });

  document.addEventListener("dragenter", function (ev) { ev.preventDefault(); document.body.classList.add("bf-dragging"); });
  document.addEventListener("dragover", function (ev) {
    ev.preventDefault();
    ev.dataTransfer.dropEffect = dragId ? "move" : "copy";
    positionPlaceholder(ev.clientY);
    /* auto-scroll near the edges */
    var vh = window.innerHeight;
    if (ev.clientY < 60) window.scrollBy(0, -12); else if (ev.clientY > vh - 60) window.scrollBy(0, 12);
  });
  document.addEventListener("dragleave", function (ev) {
    if (ev.relatedTarget === null || ev.clientY <= 0 || ev.clientY >= window.innerHeight) { /* left the frame */ }
  });
  document.addEventListener("drop", function (ev) {
    ev.preventDefault();
    var index = placeholderIndex();
    var data = "";
    try { data = ev.dataTransfer.getData("text/plain") || ""; } catch (e) {}
    if (dragId || data.indexOf("move:") === 0) {
      var id = dragId || data.slice(5);
      send({ type: "move", id: id, index: index });
    } else if (data.indexOf("new:") === 0) {
      send({ type: "insert", blockType: data.slice(4), index: index });
    }
    dragId = null;
    clearPlaceholder();
  });

  /* ------------------------------------------------------------ messages */
  window.addEventListener("message", function (ev) {
    if (ev.origin !== window.location.origin || !ev.data || ev.data.source !== "mohusyn-builder") return;
    var m = ev.data;
    if (m.type === "render") {
      blocks = Array.isArray(m.blocks) ? m.blocks : [];
      if (m.selectedId !== undefined) selectedId = m.selectedId;
      render();
    } else if (m.type === "scrollTo") {
      var el = area.querySelector('[data-bf-id="' + m.id + '"]');
      if (el) el.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  });

  document.documentElement.classList.add("builder-frame");
  send({ type: "ready", page: pageKey });
})();
