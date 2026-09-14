/* Mohusyn.ir — contact form attachment upload (progress ring + gated submit) */
(function () {
  "use strict";
  var zone = document.getElementById("cf-zone");
  if (!zone) return;

  var input = document.getElementById("cf-file");
  var token = document.getElementById("cf-token");
  var submit = document.getElementById("cf-submit");
  var errorEl = document.getElementById("cf-error");
  var idle = zone.querySelector(".upload-idle");
  var prog = zone.querySelector(".upload-progress");
  var done = zone.querySelector(".upload-done");
  var ring = zone.querySelector(".ring-fg");
  var pct = zone.querySelector(".ring-pct");
  var bar = zone.querySelector(".upload-bar i");
  var statusEl = prog.querySelector(".upload-status");
  var MAX = 20 * 1024 * 1024;
  var CIRC = 2 * Math.PI * 19;
  var xhr = null;
  var uploading = false;

  ring.style.strokeDasharray = CIRC;
  ring.style.strokeDashoffset = CIRC;

  function fmt(bytes) {
    if (bytes > 1024 * 1024) return (bytes / 1024 / 1024).toFixed(1) + " MB";
    if (bytes > 1024) return Math.round(bytes / 1024) + " KB";
    return bytes + " B";
  }
  function show(el) { [idle, prog, done].forEach(function (x) { x.hidden = x !== el; }); }
  function setError(msg) {
    errorEl.textContent = msg || "";
    errorEl.hidden = !msg;
    zone.classList.toggle("is-error", !!msg);
  }
  function setProgress(p) {
    var v = Math.max(0, Math.min(100, p));
    ring.style.strokeDashoffset = CIRC - (CIRC * v) / 100;
    pct.textContent = Math.round(v) + "%";
    bar.style.width = v + "%";
  }
  function gate(on) {
    uploading = on;
    submit.disabled = on;
    submit.classList.toggle("is-waiting", on);
  }
  function reset() {
    if (xhr) { try { xhr.abort(); } catch (e) {} xhr = null; }
    token.value = "";
    input.value = "";
    setProgress(0);
    gate(false);
    setError("");
    show(idle);
  }

  function start(file) {
    setError("");
    if (!file) return;
    if (file.size > MAX) { setError("This file is " + fmt(file.size) + " — the limit is 20 MB."); input.value = ""; return; }
    zone.querySelectorAll(".upload-name").forEach(function (n) { n.textContent = file.name + " · " + fmt(file.size); });
    statusEl.textContent = "UPLOADING…";
    setProgress(0);
    show(prog);
    gate(true);

    var fd = new FormData();
    fd.append("file", file);
    xhr = new XMLHttpRequest();
    xhr.open("POST", "/contact-upload.php", true);
    xhr.upload.addEventListener("progress", function (ev) {
      if (ev.lengthComputable) setProgress((ev.loaded / ev.total) * 100);
    });
    xhr.addEventListener("load", function () {
      var res = null;
      try { res = JSON.parse(xhr.responseText); } catch (e) {}
      if (xhr.status === 200 && res && res.ok) {
        setProgress(100);
        statusEl.textContent = "PROCESSING…";
        setTimeout(function () {
          token.value = res.token;
          show(done);
          gate(false);
          zone.classList.add("is-done");
          setTimeout(function () { zone.classList.remove("is-done"); }, 900);
        }, 350);
      } else {
        var code = res && res.error ? res.error : "upload";
        var msgs = {
          size: "The file is too large (max 20 MB).",
          type: "This file type isn't allowed. Try PDF, ZIP, images, documents or video.",
          dir: "The server can't store uploads right now. Please email me instead.",
          move: "The server can't store uploads right now. Please email me instead.",
          origin: "Upload blocked. Please reload the page and try again."
        };
        setError(msgs[code] || "Upload failed. Please try again.");
        show(idle);
        gate(false);
        input.value = "";
      }
      xhr = null;
    });
    xhr.addEventListener("error", function () { setError("Network error while uploading. Please try again."); show(idle); gate(false); xhr = null; });
    xhr.addEventListener("abort", function () { xhr = null; });
    xhr.send(fd);
  }

  input.addEventListener("change", function () { if (input.files && input.files[0]) start(input.files[0]); });
  zone.addEventListener("click", function (ev) {
    if (ev.target.closest(".upload-remove")) { ev.preventDefault(); reset(); return; }
    if (!idle.hidden) input.click();
  });
  zone.addEventListener("keydown", function (ev) {
    if ((ev.key === "Enter" || ev.key === " ") && !idle.hidden) { ev.preventDefault(); input.click(); }
  });
  ["dragenter", "dragover"].forEach(function (t) {
    zone.addEventListener(t, function (ev) { ev.preventDefault(); if (!uploading && !token.value) zone.classList.add("is-over"); });
  });
  ["dragleave", "drop"].forEach(function (t) {
    zone.addEventListener(t, function (ev) { ev.preventDefault(); zone.classList.remove("is-over"); });
  });
  zone.addEventListener("drop", function (ev) {
    if (uploading || token.value) return;
    var f = ev.dataTransfer && ev.dataTransfer.files && ev.dataTransfer.files[0];
    if (f) start(f);
  });

  /* never submit while an upload is in flight */
  var form = zone.closest("form");
  if (form) {
    form.addEventListener("submit", function (ev) {
      if (uploading) { ev.preventDefault(); setError("Please wait for the upload to finish."); }
    });
  }
})();
