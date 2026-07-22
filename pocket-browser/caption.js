/**
 * Frameless pocket caption — injected after each page load.
 * Under document.body so pywebview drag (body mousedown) sees .pywebview-drag-region.
 * Doors menu: ☰ button or Alt+M / Ctrl+K
 */
(function (title, heightPx) {
  var TITLE = title || "mypi";
  var H = heightPx || 36;

  var DOORS = [
    { label: "Gate (home)", path: null, action: "home" },
    { sep: true },
    { label: "Terminal · BASE login", path: "terminal/base/login" },
    { label: "Terminal · IO files", path: "terminal/io/files" },
    { label: "Terminal · IO email", path: "terminal/io/email" },
    { label: "Terminal · IO login", path: "terminal/io/login" },
    { sep: true },
    { label: "WWW · danyi", path: "www/danyi/index" },
    { label: "Starline · News", path: "starline/news/headlines" },
    { label: "Book · Oriel", path: "book/terminal_girls/oriel" },
    { label: "Book · Connection", path: "book/fragments/connection" },
    { label: "Crates", path: "starline/chester/crates" },
    { label: "Charlie", path: "starline/charlie/threads" },
    { label: "TPS", path: "starline/satora/shelves" },
    { sep: true },
    { label: "Hard refresh", action: "hard_refresh" },
    { label: "Reload", action: "reload" },
    { label: "Back", action: "back" },
    { label: "Forward", action: "forward" },
  ];

  function setCaptionVar() {
    var v = H + "px";
    document.documentElement.style.setProperty("--pocket-caption-h", v);
    if (document.body) {
      document.body.style.setProperty("--pocket-caption-h", v);
    }
    document.documentElement.classList.add("mypi-pocket-frameless");
    if (document.body) document.body.classList.add("mypi-pocket-frameless");
  }

  function api() {
    return window.pywebview && window.pywebview.api;
  }

  function runDoor(item) {
    var a = api();
    if (item.action === "home") {
      if (a && a.home) a.home();
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
    m.hidden = !m.hidden;
    if (!m.hidden) {
      try {
        m.querySelector("button, [tabindex]") && m.querySelector("button").focus();
      } catch (e) {}
    }
  }

  function run() {
    if (!document.body) {
      setTimeout(run, 30);
      return;
    }

    setCaptionVar();

    var existing = document.getElementById("mypi-pocket-caption");
    if (existing) {
      var lab0 = existing.querySelector(".mypi-cap-title");
      if (lab0) lab0.textContent = TITLE;
      setCaptionVar();
      return;
    }

    if (!document.getElementById("mypi-pocket-caption-css")) {
      var style = document.createElement("style");
      style.id = "mypi-pocket-caption-css";
      style.textContent =
        "html.mypi-pocket-frameless,html.mypi-pocket-frameless body{" +
        "--pocket-caption-h:" +
        H +
        "px !important}" +
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
        "letter-spacing:.04em;opacity:.95}" +
        "#mypi-pocket-caption .mypi-cap-btns{display:flex;flex-shrink:0;align-items:stretch}" +
        "#mypi-pocket-caption .mypi-cap-btn{" +
        "width:40px;border:0;background:transparent;color:inherit;" +
        "font-size:13px;cursor:pointer;line-height:" +
        H +
        "px;padding:0}" +
        "#mypi-pocket-caption .mypi-cap-btn:hover{background:rgba(255,255,255,.1)}" +
        "#mypi-pocket-caption .mypi-cap-btn.close:hover{background:#c42b1c;color:#fff}" +
        "#mypi-pocket-caption .mypi-cap-btn.menu-btn{width:44px;font-size:16px;letter-spacing:0}" +
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
        "text-transform:uppercase;opacity:.45}";
      document.head.appendChild(style);
    }

    var bar = document.createElement("div");
    bar.id = "mypi-pocket-caption";

    var drag = document.createElement("div");
    drag.className = "mypi-cap-drag pywebview-drag-region";
    drag.title = "Drag to move";

    var mark = document.createElement("span");
    mark.className = "mypi-cap-mark";
    mark.setAttribute("aria-hidden", "true");

    var lab = document.createElement("span");
    lab.className = "mypi-cap-title";
    lab.textContent = TITLE;

    drag.appendChild(mark);
    drag.appendChild(lab);

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
        var a = api();
        if (!a) return;
        if (act === "min" && a.minimize) a.minimize();
        if (act === "max" && a.toggle_maximize) a.toggle_maximize();
        if (act === "close" && a.close) a.close();
      });
      return b;
    }

    // menu first (left of window controls)
    btns.appendChild(mkBtn("menu", "☰", "Doors · Alt+M / Ctrl+K", "menu-btn"));
    btns.appendChild(mkBtn("min", "─", "Minimize"));
    btns.appendChild(mkBtn("max", "□", "Maximize"));
    btns.appendChild(mkBtn("close", "✕", "Close", "close"));

    // dropdown
    var menu = document.createElement("div");
    menu.id = "mypi-pocket-menu";
    menu.hidden = true;
    menu.setAttribute("role", "menu");

    var hint = document.createElement("div");
    hint.className = "mypi-menu-hint";
    hint.textContent = "Doors · Alt+M";
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
        if (t.closest && (t.closest("#mypi-pocket-menu") || t.closest("[data-act=menu]"))) {
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
          // Alt+M or Ctrl+K → doors
          if (
            (e.altKey && (key === "m" || key === "M")) ||
            (e.ctrlKey && (key === "k" || key === "K") && !e.shiftKey)
          ) {
            e.preventDefault();
            toggleMenu();
            return;
          }
          if (key === "Escape") closeMenu();
        },
        true
      );
    }
  }

  run();
})(
  typeof window.__MYPI_CAPTION_TITLE === "string"
    ? window.__MYPI_CAPTION_TITLE
    : "mypi",
  typeof window.__MYPI_CAPTION_H === "number" ? window.__MYPI_CAPTION_H : 36
);
