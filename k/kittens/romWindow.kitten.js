/**
 * romWindow — multi-ROM host for any surface.
 *
 * Drag uses position:fixed + clientX/Y only — NEVER offsetParent math.
 * WWW chrome / pocket chrome / any shell cannot skew the grab point.
 */
(function () {
  var zTop = 200; // above typical shell chrome
  var windows = Object.create(null);
  var builders = Object.create(null);
  var openState = Object.create(null);

  function stage() {
    return document.querySelector("[data-rom-stage]") || document.getElementById("rom-stage");
  }

  function ensureStage() {
    var s = stage();
    if (s) return s;
    s = document.createElement("div");
    s.id = "rom-stage";
    s.className = "rom-stage";
    s.setAttribute("data-rom-stage", "1");
    document.body.appendChild(s);
    return s;
  }

  function focusWin(win) {
    zTop += 1;
    win.style.zIndex = String(zTop);
    document.querySelectorAll(".rom-window.is-focused").forEach(function (n) {
      n.classList.remove("is-focused");
    });
    win.classList.add("is-focused");
  }

  /**
   * Viewport-only drag. left/top are always client pixels (position:fixed).
   * No offsetParent, no stage rect, no shell chrome compensation.
   */
  function makeDraggable(handle, panel) {
    var grabX = 0;
    var grabY = 0;
    var dragging = false;

    function placeAt(clientX, clientY) {
      panel.style.position = "fixed";
      panel.style.left = clientX - grabX + "px";
      panel.style.top = clientY - grabY + "px";
      panel.style.right = "auto";
      panel.style.bottom = "auto";
      panel.style.margin = "0";
    }

    function onMove(e) {
      if (!dragging) return;
      placeAt(e.clientX, e.clientY);
      e.preventDefault();
    }

    function onUp(e) {
      if (!dragging) return;
      dragging = false;
      try {
        if (handle.releasePointerCapture && e.pointerId != null) {
          handle.releasePointerCapture(e.pointerId);
        }
      } catch (err) { /* ignore */ }
      document.removeEventListener("pointermove", onMove, true);
      document.removeEventListener("pointerup", onUp, true);
      document.removeEventListener("pointercancel", onUp, true);
      // legacy mouse fallback
      document.removeEventListener("mousemove", onMove, true);
      document.removeEventListener("mouseup", onUp, true);
    }

    function onDown(e) {
      if (e.button != null && e.button !== 0) return;
      if (e.target && e.target.closest && e.target.closest(".rom-window-btn")) return;

      focusWin(panel);

      // Stick to cursor: grab offset inside the panel in viewport space
      var rect = panel.getBoundingClientRect();
      grabX = e.clientX - rect.left;
      grabY = e.clientY - rect.top;

      // Snap into fixed + same visual place (kills absolute/stage drift on first move)
      placeAt(e.clientX, e.clientY);
      dragging = true;

      try {
        if (handle.setPointerCapture && e.pointerId != null) {
          handle.setPointerCapture(e.pointerId);
        }
      } catch (err) { /* ignore */ }

      document.addEventListener("pointermove", onMove, true);
      document.addEventListener("pointerup", onUp, true);
      document.addEventListener("pointercancel", onUp, true);
      document.addEventListener("mousemove", onMove, true);
      document.addEventListener("mouseup", onUp, true);
      e.preventDefault();
    }

    handle.addEventListener("pointerdown", onDown);
    handle.addEventListener("mousedown", function (e) {
      // if pointer events unsupported, mousedown still works
      if (window.PointerEvent) return;
      onDown(e);
    });
  }

  function cascadePixels(n) {
    // viewport cascade — independent of shell layout
    var step = 28;
    return {
      left: 24 + (n % 8) * step,
      top: 72 + (n % 8) * step, // below most title bars without caring which
    };
  }

  function open(id, opts) {
    opts = opts || {};
    id = String(id || "").trim();
    if (!id) return null;

    if (windows[id] && document.body.contains(windows[id])) {
      windows[id].style.display = "";
      focusWin(windows[id]);
      openState[id] = true;
      return windows[id];
    }

    ensureStage(); // stacking layer for CSS / meow world; windows live on body
    var openCount = Object.keys(windows).length;
    var pos = cascadePixels(openCount);

    var win = document.createElement("div");
    win.className = "rom-window";
    win.id = "rom-win-" + id.replace(/[^A-Za-z0-9_-]/g, "");
    win.dataset.romId = id;
    win.style.position = "fixed";
    win.style.left = pos.left + "px";
    win.style.top = pos.top + "px";
    win.style.zIndex = String(++zTop);

    var title = opts.title || id;
    var bar = document.createElement("div");
    bar.className = "rom-window-titlebar";
    bar.innerHTML =
      '<span class="rom-window-title"></span>' +
      '<button type="button" class="rom-window-btn rom-window-close" title="Close">×</button>';
    bar.querySelector(".rom-window-title").textContent = title;

    var body = document.createElement("div");
    body.className = "rom-window-body";
    body.id = "rom-body-" + id.replace(/[^A-Za-z0-9_-]/g, "");

    win.appendChild(bar);
    win.appendChild(body);

    // Append to BODY not stage: fixed coords = viewport, zero shell math
    document.body.appendChild(win);

    bar.querySelector(".rom-window-close").addEventListener("click", function (e) {
      e.stopPropagation();
      close(id);
    });
    win.addEventListener("pointerdown", function () {
      focusWin(win);
    });
    makeDraggable(bar, win);

    windows[id] = win;
    openState[id] = true;
    focusWin(win);

    var builder = builders[id] || (opts.build && typeof opts.build === "function" ? opts.build : null);
    if (builder) {
      try {
        builder(body, win);
      } catch (err) {
        body.textContent = "ROM build error: " + err;
        console.error(err);
      }
    } else {
      body.innerHTML =
        "<p>No kit registered for <code>" +
        id +
        "</code>.</p><p class='dim'>Kit should call RomHost.register('" +
        id +
        "', fn).</p>";
    }

    console.log("%cRomHost open " + id, "color:#6af");
    return win;
  }

  function close(id) {
    id = String(id || "").trim();
    var win = windows[id];
    if (!win) return;
    win.remove();
    delete windows[id];
    openState[id] = false;
  }

  function toggle(id, opts) {
    if (openState[id] && windows[id] && document.body.contains(windows[id])) {
      close(id);
      return null;
    }
    return open(id, opts);
  }

  function register(id, builder) {
    builders[String(id)] = builder;
  }

  function isOpen(id) {
    return !!(openState[id] && windows[id]);
  }

  window.RomHost = {
    open: open,
    close: close,
    toggle: toggle,
    register: register,
    isOpen: isOpen,
    catalog: function () {
      return window.TOY_CATALOG || {};
    },
  };

  window.CloseROM = function (id) {
    if (id) close(id);
  };

  document.addEventListener(
    "click",
    function (e) {
      var cover = e.target && e.target.closest && e.target.closest("[data-rom]");
      if (!cover) return;
      if (cover.getAttribute("onclick")) return;
      var rid = cover.getAttribute("data-rom");
      if (rid) {
        e.preventDefault();
        toggle(rid, { title: rid });
      }
    },
    false
  );

  console.log(
    "%cMEOW MEOW! RomHost kitten is ACTIVATE (multi-window stage)",
    "color:pink;font-weight:700;background:#111;padding:6px 10px"
  );
})();
