/* Mohusyn admin — lightweight rich-text editor (no dependencies).
   Usage: <textarea data-richtext>…html…</textarea> → replaced by a toolbar + contenteditable area;
   the textarea stays in the form and receives the HTML on every change. */
(function () {
  "use strict";
  var CSRF = (document.querySelector('meta[name="admin-csrf"]') || {}).content || "";

  var TOOLS = [
    { cmd: "formatBlock", arg: "h2", label: "H2", tip: "تیتر بزرگ" },
    { cmd: "formatBlock", arg: "h3", label: "H3", tip: "تیتر متوسط" },
    { cmd: "formatBlock", arg: "p", label: "¶", tip: "پاراگراف معمولی" },
    { sep: true },
    { cmd: "bold", label: "<b>B</b>", tip: "بولد (Ctrl+B)" },
    { cmd: "italic", label: "<i>I</i>", tip: "ایتالیک (Ctrl+I)" },
    { cmd: "underline", label: "<u>U</u>", tip: "زیرخط (Ctrl+U)" },
    { cmd: "strikeThrough", label: "<s>S</s>", tip: "خط‌خورده" },
    { sep: true },
    { cmd: "insertUnorderedList", label: "•≡", tip: "لیست نقطه‌ای" },
    { cmd: "insertOrderedList", label: "1≡", tip: "لیست شماره‌دار" },
    { cmd: "formatBlock", arg: "blockquote", label: "❝", tip: "نقل‌قول" },
    { cmd: "insertHorizontalRule", label: "—", tip: "خط جداکننده" },
    { sep: true },
    { custom: "link", label: "🔗", tip: "لینک (Ctrl+K)" },
    { custom: "unlink", label: "⛓", tip: "حذف لینک" },
    { custom: "image", label: "🖼", tip: "درج تصویر (آپلود)" },
    { sep: true },
    { cmd: "removeFormat", label: "Tx", tip: "پاک‌کردن قالب‌بندی" },
    { custom: "html", label: "</>", tip: "نمایش/ویرایش HTML" }
  ];

  function mount(textarea) {
    var wrap = document.createElement("div");
    wrap.className = "rte";
    var bar = document.createElement("div");
    bar.className = "rte-bar";
    var area = document.createElement("div");
    area.className = "rte-area";
    area.contentEditable = "true";
    area.setAttribute("dir", textarea.getAttribute("dir") || "ltr");
    area.setAttribute("spellcheck", "true");
    area.innerHTML = textarea.value;
    if (!area.innerHTML.trim()) area.innerHTML = "<p><br></p>";

    var htmlMode = false;
    textarea.classList.add("rte-source");
    textarea.hidden = true;

    function sync() { if (!htmlMode) textarea.value = area.innerHTML; }
    function focusExec(cmd, arg) {
      area.focus();
      document.execCommand(cmd, false, arg || null);
      sync();
    }

    TOOLS.forEach(function (t) {
      if (t.sep) { var s = document.createElement("span"); s.className = "rte-sep"; bar.appendChild(s); return; }
      var b = document.createElement("button");
      b.type = "button";
      b.className = "rte-btn";
      b.innerHTML = t.label;
      b.title = t.tip;
      b.setAttribute("data-tip", t.tip);
      b.addEventListener("mousedown", function (ev) { ev.preventDefault(); }); /* keep selection */
      b.addEventListener("click", function () {
        if (htmlMode && t.custom !== "html") return;
        if (t.custom === "link") return doLink();
        if (t.custom === "unlink") return focusExec("unlink");
        if (t.custom === "image") return doImage();
        if (t.custom === "html") return toggleHtml(b);
        focusExec(t.cmd, t.arg ? "<" + t.arg + ">" : null);
      });
      bar.appendChild(b);
    });

    function doLink() {
      area.focus();
      var sel = window.getSelection();
      var anchor = sel && sel.anchorNode ? (sel.anchorNode.nodeType === 1 ? sel.anchorNode : sel.anchorNode.parentNode).closest("a") : null;
      var current = anchor ? anchor.getAttribute("href") : "";
      var url = window.prompt("آدرس لینک (مثلاً https://example.com یا /contact/):", current || "https://");
      if (url === null) return;
      url = url.trim();
      if (url === "" || url === "https://") { if (anchor) focusExec("unlink"); return; }
      if (anchor) { anchor.setAttribute("href", url); }
      else if (sel && sel.isCollapsed) {
        var a = document.createElement("a"); a.href = url; a.textContent = url;
        var r = sel.getRangeAt(0); r.insertNode(a);
      } else { document.execCommand("createLink", false, url); }
      area.querySelectorAll("a").forEach(function (a) {
        if (/^https?:\/\//i.test(a.getAttribute("href") || "") && a.host !== location.host) { a.target = "_blank"; a.rel = "noopener"; }
      });
      sync();
    }

    function doImage() {
      var file = document.createElement("input");
      file.type = "file"; file.accept = "image/*"; file.style.display = "none";
      file.addEventListener("change", function () {
        if (!file.files || !file.files[0]) return;
        var fd = new FormData();
        fd.append("file", file.files[0]); fd.append("kind", "image"); fd.append("_csrf", CSRF);
        area.classList.add("is-busy");
        fetch("upload.php", { method: "POST", body: fd }).then(function (r) { return r.json(); }).then(function (res) {
          if (res.ok && res.url) {
            area.focus();
            document.execCommand("insertHTML", false, '<figure class="rt-figure"><img src="' + res.url + '" alt=""><figcaption></figcaption></figure><p><br></p>');
            sync();
          } else { alert("آپلود ناموفق بود (" + (res.error || "error") + ")"); }
        }).catch(function () { alert("خطای شبکه در آپلود"); })
          .then(function () { area.classList.remove("is-busy"); file.remove(); });
      });
      document.body.appendChild(file);
      file.click();
    }

    function toggleHtml(btn) {
      htmlMode = !htmlMode;
      if (htmlMode) {
        textarea.value = area.innerHTML;
        textarea.hidden = false; area.hidden = true; btn.classList.add("is-on");
      } else {
        area.innerHTML = textarea.value; textarea.hidden = true; area.hidden = false; btn.classList.remove("is-on");
        sync();
      }
      wrap.classList.toggle("is-html", htmlMode);
    }

    /* paste: keep it clean (plain text + basic formatting) */
    area.addEventListener("paste", function (ev) {
      ev.preventDefault();
      var html = ev.clipboardData.getData("text/html");
      var text = ev.clipboardData.getData("text/plain");
      if (html) {
        var tmp = document.createElement("div"); tmp.innerHTML = html;
        tmp.querySelectorAll("script,style,meta,link").forEach(function (n) { n.remove(); });
        tmp.querySelectorAll("*").forEach(function (n) {
          Array.prototype.slice.call(n.attributes).forEach(function (a) { if (!/^(href|src|alt)$/i.test(a.name)) n.removeAttribute(a.name); });
        });
        document.execCommand("insertHTML", false, tmp.innerHTML);
      } else {
        document.execCommand("insertText", false, text);
      }
      sync();
    });
    area.addEventListener("input", sync);
    area.addEventListener("blur", sync);
    area.addEventListener("keydown", function (ev) {
      if ((ev.ctrlKey || ev.metaKey) && ev.key.toLowerCase() === "k") { ev.preventDefault(); doLink(); }
    });
    var form = textarea.closest("form");
    if (form) form.addEventListener("submit", function () { if (htmlMode) return; textarea.value = area.innerHTML; });

    var hint = document.createElement("div");
    hint.className = "rte-help";
    hint.innerHTML = "متن را انتخاب کنید و از نوار بالا بولد/لینک/تیتر بدهید. Ctrl+B بولد، Ctrl+K لینک. تیترها با فونت Doto و پاراگراف‌ها با فونت متن سایت نمایش داده می‌شوند — دقیقاً مثل بقیهٔ سایت.";

    textarea.parentNode.insertBefore(wrap, textarea);
    wrap.appendChild(bar);
    wrap.appendChild(area);
    wrap.appendChild(textarea);
    wrap.appendChild(hint);
  }

  document.querySelectorAll("textarea[data-richtext]").forEach(mount);
})();
