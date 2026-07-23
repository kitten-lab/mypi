/**
 * Frameless pocket caption — injected after each page load.
 * Under document.body so pywebview drag sees .pywebview-drag-region.
 *
 * Zoom: Ctrl+= / Ctrl+- / Ctrl+0  (page zoom, persisted)
 * Deep: F11 or doors “Go deep” — hide caption, full surface; Esc / F11 exit
 * Doors: ☰ · Alt+M / Ctrl+K
 */
(function (title, heightPx) {
  var TITLE = title || "mypi";
  var H = heightPx || 36;
  var ZOOM_MIN = 0.75;
  var ZOOM_MAX = 1.75;
  var ZOOM_STEP = 0.1;
  var LS_ZOOM = "mypi-pocket-zoom";
  var LS_DEEP = "mypi-pocket-deep";

  var DOORS = [
    { label: "Gate (home)", path: null, action: "home" },
    { sep: true },
    { label: "Terminal · BASE login", path: "terminal/base/login" },
    { label: "Terminal · IO import", path: "terminal/io/import" },
    { label: "Terminal · IO files", path: "terminal/io/files" },
    { label: "Terminal · IO email", path: "terminal/io/email" },
    { label: "Terminal · IO login", path: "terminal/io/login" },
    { label: "Terminal · RX ven (Oriel)", path: "terminal/rx/ven" },
    { label: "Terminal · RX codex (lore)", path: "terminal/rx/codex" },
    { label: "Terminal · RX files", path: "terminal/rx/files" },
    { sep: true },
    { label: "WWW · danyi", path: "www/danyi/index" },
    { label: "Mythleak · Headlines", path: "mythleak/news/headlines" },
    { label: "Mythleak · File a leak", path: "mythleak/news/write" },
    { label: "Starline · News", path: "starline/news/headlines" },
    { label: "Book · Oriel", path: "book/terminal_girls/oriel" },
    { label: "Book · Connection", path: "book/fragments/connection" },
    { label: "Crates", path: "starline/chester/crates" },
    { label: "Charlie", path: "starline/charlie/threads" },
    { label: "TPS", path: "starline/satora/shelves" },
    { sep: true },
    { label: "Zoom 100%", action: "zoom_reset" },
    { label: "Window 1024×768 ↔ 1600×1200", action: "step_size" },
    { label: "Go deep (hide chrome)", action: "deep" },
    { sep: true },
    { label: "Hard refresh", action: "hard_refresh" },
    { label: "Reload", action: "reload" },
    { label: "Back", action: "back" },
    { label: "Forward", action: "forward" },
  ];

  function api() {
    return window.pywebview && window.pywebview.api;
  }

  function readZoom() {
    try {
      var z = parseFloat(localStorage.getItem(LS_ZOOM) || "1");
      if (isNaN(z) || z < ZOOM_MIN || z > ZOOM_MAX) return 1;
      return Math.round(z * 100) / 100;
    } catch (e) {
      return 1;
    }
  }

  function writeZoom(z) {
    try {
      localStorage.setItem(LS_ZOOM, String(z));
    } catch (e) {}
  }

  function applyZoom(z) {
    z = Math.max(ZOOM_MIN, Math.min(ZOOM_MAX, z));
    z = Math.round(z * 100) / 100;
    document.documentElement.style.setProperty("--mypi-page-zoom", String(z));
    document.documentElement.style.zoom = String(z);
    writeZoom(z);
    var lab = document.querySelector("#mypi-pocket-caption .mypi-cap-zoom");
    if (lab) lab.textContent = Math.round(z * 100) + "%";
    return z;
  }

  function zoomBy(delta) {
    return applyZoom(readZoom() + delta);
  }

  function stepWindowSize() {
    var a = api();
    if (a && a.step_window_size) {
      try {
        var r = a.step_window_size();
        // pywebview may return a Promise
        if (r && typeof r.then === "function") {
          r.then(function (s) {
            flashSizeHint(s);
          }).catch(function () {});
        } else {
          flashSizeHint(r);
        }
      } catch (e) {}
    }
  }

  function flashSizeHint(s) {
    if (!s) return;
    var lab = document.querySelector("#mypi-pocket-caption .mypi-cap-zoom");
    if (!lab) return;
    var prev = lab.textContent;
    lab.textContent = String(s).replace("x", "×");
    lab.classList.add("is-flash");
    setTimeout(function () {
      lab.classList.remove("is-flash");
      lab.textContent = Math.round(readZoom() * 100) + "%";
    }, 900);
  }

  function isDeep() {
    return document.documentElement.classList.contains("mypi-pocket-deep");
  }

  function setCaptionHeightVar(px) {
    var v = px + "px";
    document.documentElement.style.setProperty("--pocket-caption-h", v);
    if (document.body) document.body.style.setProperty("--pocket-caption-h", v);
  }

  function armSurfaceDrags(on) {
    // Surfaces may mark [data-pocket-drag] (e.g. terminal .tm-rail-brand).
    // Ensure pywebview-drag-region class is present for frameless move.
    try {
      var nodes = document.querySelectorAll("[data-pocket-drag]");
      for (var i = 0; i < nodes.length; i++) {
        if (on) nodes[i].classList.add("pywebview-drag-region");
        // leave class on when exiting deep — still fine while caption exists
        else nodes[i].classList.add("pywebview-drag-region");
      }
    } catch (e) {}
  }

  function setDeep(on) {
    var root = document.documentElement;
    var bar = document.getElementById("mypi-pocket-caption");
    var menu = document.getElementById("mypi-pocket-menu");
    if (on) {
      root.classList.add("mypi-pocket-deep");
      if (document.body) document.body.classList.add("mypi-pocket-deep");
      if (bar) bar.hidden = true;
      if (menu) menu.hidden = true;
      setCaptionHeightVar(0);
      armSurfaceDrags(true);
      try {
        localStorage.setItem(LS_DEEP, "1");
      } catch (e) {}
      ensureDeepHint(true);
    } else {
      root.classList.remove("mypi-pocket-deep");
      if (document.body) document.body.classList.remove("mypi-pocket-deep");
      if (bar) bar.hidden = false;
      setCaptionHeightVar(H);
      armSurfaceDrags(false);
      try {
        localStorage.removeItem(LS_DEEP);
      } catch (e) {}
      ensureDeepHint(false);
    }
  }

  function toggleDeep() {
    setDeep(!isDeep());
  }

  function ensureDeepHint(show) {
    var el = document.getElementById("mypi-pocket-deep-hint");
    var drag = document.getElementById("mypi-pocket-deep-drag");
    if (!show) {
      if (el) el.remove();
      if (drag) drag.remove();
      return;
    }
    if (!el) {
      el = document.createElement("div");
      el.id = "mypi-pocket-deep-hint";
      el.setAttribute("role", "status");
      el.textContent = "deep · Esc/F11 surface · drag corner slug";
      el.addEventListener("click", function () {
        setDeep(false);
      });
      document.body.appendChild(el);
      setTimeout(function () {
        if (el && el.parentNode) el.classList.add("is-dim");
      }, 2200);
    }
    // Fallback grabber only if surface didn't mark a drag slug
    var hasSurface = document.querySelector("[data-pocket-drag]");
    if (!hasSurface && !drag) {
      drag = document.createElement("div");
      drag.id = "mypi-pocket-deep-drag";
      drag.className = "pywebview-drag-region";
      drag.title = "Drag window";
      document.body.appendChild(drag);
    }
  }

  function setCaptionVar() {
    if (isDeep()) {
      setCaptionHeightVar(0);
    } else {
      setCaptionHeightVar(H);
    }
    document.documentElement.classList.add("mypi-pocket-frameless");
    if (document.body) document.body.classList.add("mypi-pocket-frameless");
  }

  function runDoor(item) {
    var a = api();
    if (item.action === "home") {
      if (a && a.home) a.home();
      return;
    }
    if (item.action === "zoom_reset") {
      applyZoom(1);
      return;
    }
    if (item.action === "step_size") {
      stepWindowSize();
      return;
    }
    if (item.action === "deep") {
      setDeep(true);
      return;
    }
    if (item.action === "hard_refresh") {
      if (a && a.hard_refresh) a.hard_refresh();
      else if (typeof window.WWWHardRefresh === "function") window.WWWHardRefresh();
      return;
    }
    if (item.action === "reload") {
      if (a && a.reload) a.reload();
      else if (typeof window.WWWRefresh === "function") window.WWWRefresh();
      else location.reload();
      return;
    }
    if (item.action === "back") {
      history.back();
      return;
    }
    if (item.action === "forward") {
      history.forward();
      return;
    }
    if (item.path) {
      if (a && a.go) a.go(item.path);
      else location.assign("http://b/" + item.path.replace(/^\//, ""));
    }
  }

  function closeMenu() {
    var m = document.getElementById("mypi-pocket-menu");
    if (m) m.hidden = true;
  }

  function toggleMenu() {
    var m = document.getElementById("mypi-pocket-menu");
    if (!m) return;
    // if deep, surface first so menu has a home
    if (isDeep()) setDeep(false);
    m.hidden = !m.hidden;
    if (!m.hidden) {
      try {
        var btn = m.querySelector("button");
        if (btn) btn.focus();
      } catch (e) {}
    }
  }

  function injectCss() {
    if (document.getElementById("mypi-pocket-caption-css")) return;
    var style = document.createElement("style");
    style.id = "mypi-pocket-caption-css";
    style.textContent =
      "html.mypi-pocket-frameless,html.mypi-pocket-frameless body{" +
      "--pocket-caption-h:" +
      H +
      "px !important;" +
      "--mypi-page-zoom:1}" +
      "html.mypi-pocket-frameless.mypi-pocket-deep," +
      "html.mypi-pocket-frameless.mypi-pocket-deep body{" +
      "--pocket-caption-h:0px !important;" +
      "margin:0!important;padding:0!important}" +
      "html.mypi-pocket-frameless.mypi-pocket-deep #mypi-pocket-caption{" +
      "display:none!important;height:0!important;min-height:0!important;" +
      "overflow:hidden!important;border:0!important;padding:0!important;" +
      "pointer-events:none!important}" +
      "#mypi-pocket-caption{" +
      "position:fixed;top:0;left:0;right:0;height:" +
      H +
      "px;z-index:2147483000;" +
      "display:flex;align-items:stretch;" +
      "font-family:system-ui,Segoe UI,sans-serif;font-size:12px;" +
      "color:#d8dee8;background:#0a0c12;" +
      "border-bottom:1px solid rgba(120,140,200,.35);" +
      "box-shadow:0 2px 12px rgba(0,0,0,.45);" +
      "user-select:none;-webkit-user-select:none}" +
      "#mypi-pocket-caption[hidden]{display:none!important;height:0!important;min-height:0!important;border:0!important}" +
      "#mypi-pocket-caption .mypi-cap-drag{" +
      "flex:1;display:flex;align-items:center;gap:8px;" +
      "padding:0 10px;min-width:0;cursor:grab}" +
      "#mypi-pocket-caption .mypi-cap-drag:active{cursor:grabbing}" +
      "#mypi-pocket-caption .mypi-cap-mark{" +
      "width:9px;height:9px;flex-shrink:0;" +
      "background:linear-gradient(135deg,#6a8cff,#a070ff);" +
      "box-shadow:0 0 10px rgba(100,130,255,.5)}" +
      "#mypi-pocket-caption .mypi-cap-title{" +
      "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" +
      "letter-spacing:.04em;opacity:.95;flex:1;min-width:0}" +
      "#mypi-pocket-caption .mypi-cap-zoom{" +
      "flex-shrink:0;font-size:10px;opacity:.55;letter-spacing:.06em;" +
      "min-width:2.6em;text-align:right;cursor:pointer;padding:0 4px;" +
      "border-radius:2px}" +
      "#mypi-pocket-caption .mypi-cap-zoom:hover{opacity:1;background:rgba(255,255,255,.08)}" +
      "#mypi-pocket-caption .mypi-cap-zoom.is-flash{opacity:1;color:#a8c0ff}" +
      "#mypi-pocket-caption .mypi-cap-btns{display:flex;flex-shrink:0;align-items:stretch}" +
      "#mypi-pocket-caption .mypi-cap-btn{" +
      "width:36px;border:0;background:transparent;color:inherit;" +
      "font-size:13px;cursor:pointer;line-height:" +
      H +
      "px;padding:0}" +
      "#mypi-pocket-caption .mypi-cap-btn:hover{background:rgba(255,255,255,.1)}" +
      "#mypi-pocket-caption .mypi-cap-btn.close:hover{background:#c42b1c;color:#fff}" +
      "#mypi-pocket-caption .mypi-cap-btn.menu-btn{width:44px;font-size:16px;letter-spacing:0}" +
      "#mypi-pocket-caption .mypi-cap-btn.mag-btn{width:36px;font-size:14px;letter-spacing:0}" +
      "#mypi-pocket-caption .mypi-cap-btn.deep-btn{width:36px;font-size:12px;letter-spacing:0}" +
      "#mypi-pocket-menu{" +
      "position:fixed;top:" +
      H +
      "px;left:0;min-width:220px;max-width:min(320px,90vw);" +
      "z-index:2147483001;margin:0;padding:6px 0;" +
      "background:#12151e;border:1px solid rgba(120,140,200,.3);" +
      "border-top:none;box-shadow:0 12px 32px rgba(0,0,0,.55);" +
      "font-family:system-ui,Segoe UI,sans-serif;font-size:13px;color:#e4e8f0}" +
      "#mypi-pocket-menu[hidden]{display:none!important}" +
      "#mypi-pocket-menu button{" +
      "display:block;width:100%;text-align:left;border:0;background:transparent;" +
      "color:inherit;padding:8px 14px;cursor:pointer;font:inherit}" +
      "#mypi-pocket-menu button:hover,#mypi-pocket-menu button:focus{" +
      "background:rgba(106,140,255,.18);outline:none}" +
      "#mypi-pocket-menu .mypi-menu-sep{" +
      "height:1px;margin:6px 10px;background:rgba(120,140,200,.2)}" +
      "#mypi-pocket-menu .mypi-menu-hint{" +
      "padding:6px 14px 4px;font-size:10px;letter-spacing:.08em;" +
      "text-transform:uppercase;opacity:.45}" +
      "#mypi-pocket-deep-hint{" +
      "position:fixed;top:8px;left:50%;transform:translateX(-50%);" +
      "z-index:2147483002;padding:6px 14px;font:11px system-ui,sans-serif;" +
      "letter-spacing:.06em;text-transform:uppercase;cursor:pointer;" +
      "color:rgba(220,230,240,.75);background:rgba(10,12,18,.82);" +
      "border:1px solid rgba(120,140,200,.25);border-radius:999px;" +
      "transition:opacity .4s ease}" +
      "#mypi-pocket-deep-hint.is-dim{opacity:.22}" +
      "#mypi-pocket-deep-hint:hover{opacity:1!important}" +
      /* fallback corner grabber if surface has no data-pocket-drag */
      "#mypi-pocket-deep-drag{" +
      "position:fixed;top:0;left:0;z-index:2147483001;" +
      "width:2.5rem;height:2.5rem;cursor:grab;" +
      "background:transparent}" +
      "#mypi-pocket-deep-drag:active{cursor:grabbing}";
    document.head.appendChild(style);
  }

  function run() {
    if (!document.body) {
      setTimeout(run, 30);
      return;
    }

    injectCss();
    setCaptionVar();
    applyZoom(readZoom());

    var existing = document.getElementById("mypi-pocket-caption");
    if (existing) {
      var lab0 = existing.querySelector(".mypi-cap-title");
      if (lab0) lab0.textContent = TITLE;
      setCaptionVar();
      applyZoom(readZoom());
      try {
        if (localStorage.getItem(LS_DEEP) === "1") setDeep(true);
      } catch (e) {}
      return;
    }

    var bar = document.createElement("div");
    bar.id = "mypi-pocket-caption";

    var drag = document.createElement("div");
    drag.className = "mypi-cap-drag pywebview-drag-region";
    drag.title = "Drag to move · double-click maximize";

    var mark = document.createElement("span");
    mark.className = "mypi-cap-mark";
    mark.setAttribute("aria-hidden", "true");

    var lab = document.createElement("span");
    lab.className = "mypi-cap-title";
    lab.textContent = TITLE;

    var zoomLab = document.createElement("button");
    zoomLab.type = "button";
    zoomLab.className = "mypi-cap-zoom";
    zoomLab.title = "Click → 100% · keyboard Ctrl± / Ctrl+0";
    zoomLab.textContent = Math.round(readZoom() * 100) + "%";
    zoomLab.addEventListener("mousedown", function (e) {
      e.stopPropagation();
    });
    zoomLab.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      applyZoom(1);
    });

    drag.appendChild(mark);
    drag.appendChild(lab);
    drag.appendChild(zoomLab);

    // double-click drag strip → maximize (window), not deep
    drag.addEventListener("dblclick", function (e) {
      if (e.target && e.target.closest && e.target.closest(".mypi-cap-zoom")) {
        return;
      }
      e.preventDefault();
      var a = api();
      if (a && a.toggle_maximize) a.toggle_maximize();
    });

    var btns = document.createElement("div");
    btns.className = "mypi-cap-btns";

    function mkBtn(act, label, title, extraClass) {
      var b = document.createElement("button");
      b.type = "button";
      b.className = "mypi-cap-btn" + (extraClass ? " " + extraClass : "");
      b.setAttribute("data-act", act);
      b.title = title;
      b.textContent = label;
      b.addEventListener("mousedown", function (e) {
        e.stopPropagation();
      });
      b.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (act === "menu") {
          toggleMenu();
          return;
        }
        if (act === "step_size") {
          stepWindowSize();
          return;
        }
        if (act === "deep") {
          setDeep(true);
          return;
        }
        var a = api();
        if (!a) return;
        if (act === "min" && a.minimize) a.minimize();
        if (act === "max" && a.toggle_maximize) a.toggle_maximize();
        if (act === "close" && a.close) a.close();
      });
      return b;
    }

    btns.appendChild(mkBtn("menu", "☰", "Doors · Alt+M / Ctrl+K", "menu-btn"));
    btns.appendChild(
      mkBtn(
        "step_size",
        "▣",
        "Window · 1024×768 ↔ 1600×1200",
        "mag-btn"
      )
    );
    btns.appendChild(mkBtn("deep", "◎", "Go deep · hide chrome · F11", "deep-btn"));
    btns.appendChild(mkBtn("min", "─", "Minimize"));
    btns.appendChild(mkBtn("max", "□", "Maximize window"));
    btns.appendChild(mkBtn("close", "✕", "Close", "close"));

    var menu = document.createElement("div");
    menu.id = "mypi-pocket-menu";
    menu.hidden = true;
    menu.setAttribute("role", "menu");

    var hint = document.createElement("div");
    hint.className = "mypi-menu-hint";
    hint.textContent = "Doors · Alt+M · F11 deep · Ctrl± zoom · ▣ size";
    menu.appendChild(hint);

    DOORS.forEach(function (item) {
      if (item.sep) {
        var sep = document.createElement("div");
        sep.className = "mypi-menu-sep";
        menu.appendChild(sep);
        return;
      }
      var b = document.createElement("button");
      b.type = "button";
      b.setAttribute("role", "menuitem");
      b.textContent = item.label;
      b.addEventListener("click", function (e) {
        e.preventDefault();
        closeMenu();
        runDoor(item);
      });
      menu.appendChild(b);
    });

    bar.appendChild(drag);
    bar.appendChild(btns);
    document.body.insertBefore(bar, document.body.firstChild);
    document.body.appendChild(menu);

    document.addEventListener(
      "click",
      function (e) {
        var t = e.target;
        if (!t) return;
        if (
          t.closest &&
          (t.closest("#mypi-pocket-menu") || t.closest("[data-act=menu]"))
        ) {
          return;
        }
        closeMenu();
      },
      true
    );

    if (!window.__mypiMenuKeys) {
      window.__mypiMenuKeys = true;
      document.addEventListener(
        "keydown",
        function (e) {
          var key = e.key || "";
          var ctrl = e.ctrlKey || e.metaKey;

          if (
            (e.altKey && (key === "m" || key === "M")) ||
            (ctrl && (key === "k" || key === "K") && !e.shiftKey)
          ) {
            e.preventDefault();
            toggleMenu();
            return;
          }

          // F11 — go deep / surface (page immersive, keeps our chrome control)
          if (key === "F11") {
            e.preventDefault();
            toggleDeep();
            return;
          }

          if (key === "Escape") {
            if (isDeep()) {
              e.preventDefault();
              setDeep(false);
              return;
            }
            closeMenu();
            return;
          }

          // Zoom: Ctrl+= / Ctrl++ / Ctrl+- / Ctrl+0
          if (ctrl && (key === "=" || key === "+" || key === "Add")) {
            e.preventDefault();
            zoomBy(ZOOM_STEP);
            return;
          }
          if (ctrl && (key === "-" || key === "_" || key === "Subtract")) {
            e.preventDefault();
            zoomBy(-ZOOM_STEP);
            return;
          }
          if (ctrl && (key === "0" || key === "Digit0" || key === "Numpad0")) {
            e.preventDefault();
            applyZoom(1);
            return;
          }
        },
        true
      );
    }

    // restore deep if left deep last time (optional — can surprise; only if flag set)
    try {
      if (localStorage.getItem(LS_DEEP) === "1") setDeep(true);
    } catch (e2) {}

    applyZoom(readZoom());
  }

  // expose for surfaces / debug
  window.mypiPocketZoom = function (z) {
    return applyZoom(typeof z === "number" ? z : readZoom());
  };
  window.mypiPocketDeep = function (on) {
    if (typeof on === "boolean") setDeep(on);
    else toggleDeep();
  };

  run();
})(
  typeof window.__MYPI_CAPTION_TITLE === "string"
    ? window.__MYPI_CAPTION_TITLE
    : "mypi",
  typeof window.__MYPI_CAPTION_H === "number" ? window.__MYPI_CAPTION_H : 36
);
