/**
 * CBX-001 Chatterbox — 90s-ish live room on RomHost.
 * Reads window.CHATBOX_BOOT / #chatbox-boot-json.
 * Posts via fetch so the ROM window does NOT close on Say.
 */
const CBX001 = {
  vencode: "CBX-001",
  provider: "Chester's Imports",
  toy: "Chatterbox",
  funtitle: "live room",
  info: "Session + nick + scroll — ledger kind=chat",
};

function cbxEsc(s) {
  return String(s == null ? "" : s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function cbxBoot() {
  return window.CHATBOX_BOOT || {
    session: "live",
    lines: [],
    sessions: [],
    place: {},
    confirm: null,
    error: null,
  };
}

function cbxFmtWhen(unix) {
  if (!unix) return "";
  try {
    return new Date(unix * 1000).toLocaleString();
  } catch (e) {
    return String(unix);
  }
}

function cbxParseBootFromHtml(html) {
  try {
    var doc = new DOMParser().parseFromString(html, "text/html");
    var el = doc.getElementById("chatbox-boot-json");
    if (el && el.textContent) {
      return JSON.parse(el.textContent);
    }
  } catch (e) {
    console.warn("CBX boot parse", e);
  }
  return null;
}

function cbxPaintSessions(sessBar, boot, session) {
  var sessHtml = "sessions: ";
  var list =
    boot.sessions && boot.sessions.length
      ? boot.sessions
      : [{ session: "live", label: "live", n: 0 }];
  list.forEach(function (s) {
    var sid = s.session || "live";
    var lab = s.label || sid;
    var on = sid === session ? " on" : "";
    sessHtml +=
      '<a class="' +
      on.trim() +
      '" href="?session=' +
      encodeURIComponent(sid) +
      '">' +
      cbxEsc(lab) +
      " (" +
      (s.n || 0) +
      ")</a>";
  });
  sessBar.innerHTML = sessHtml;
}

function cbxPaintLog(log, lines) {
  if (!lines || !lines.length) {
    log.innerHTML =
      '<div class="cbx-empty">no lines in this session yet — say something</div>';
    return;
  }
  log.innerHTML = lines
    .map(function (ln) {
      return (
        '<div class="cbx-line">' +
        '<span class="who">' +
        cbxEsc(ln.agent || "anon") +
        "</span>" +
        '<span class="when">' +
        cbxEsc(cbxFmtWhen(ln.event_unix)) +
        "</span>" +
        '<div class="msg">' +
        cbxEsc(ln.body || "") +
        "</div>" +
        "</div>"
      );
    })
    .join("");
  log.scrollTop = log.scrollHeight;
}

function buildCBX001(body) {
  var boot = cbxBoot();
  var session = boot.session || "live";
  var nickKey = "cbx_nick_" + (location.pathname || "");
  var savedNick = "";
  try {
    savedNick = localStorage.getItem(nickKey) || "";
  } catch (e) {}

  body.innerHTML =
    '<div class="cbx-root" data-cbx="1">' +
    '<div class="cbx-title">Chatterbox <span>CBX-001 · ledger chat</span></div>' +
    '<div class="cbx-sessions" data-cbx-sessions></div>' +
    '<div class="cbx-toolbar">' +
    '<label>nick</label><input type="text" data-cbx-nick maxlength="40" />' +
    '<label>session</label><input type="text" class="wide" data-cbx-session maxlength="40" />' +
    '<label>title</label><input type="text" class="wide" data-cbx-label maxlength="60" />' +
    '<button type="button" class="cbx-refresh" data-cbx-refresh title="Reload channel (GET only — does not re-send)">Refresh</button>' +
    "</div>" +
    '<div class="cbx-log" data-cbx-log></div>' +
    '<div class="cbx-status" data-cbx-status></div>' +
    '<form class="cbx-compose" data-cbx-form method="POST" action="">' +
    '<textarea name="message" data-cbx-msg required placeholder="type a line…"></textarea>' +
    '<input type="hidden" name="username" data-cbx-user-hidden />' +
    '<input type="hidden" name="chat_session" data-cbx-sess-hidden />' +
    '<input type="hidden" name="chat_session_label" data-cbx-label-hidden />' +
    '<input type="hidden" name="POST__TZ" data-cbx-tz />' +
    '<input type="hidden" name="POST__EVENT_UNIX" value="" />' +
    '<button type="submit" data-cbx-say>Say</button>' +
    "</form>" +
    "</div>";

  var root = body.querySelector("[data-cbx]");
  var log = root.querySelector("[data-cbx-log]");
  var nickIn = root.querySelector("[data-cbx-nick]");
  var sessIn = root.querySelector("[data-cbx-session]");
  var labelIn = root.querySelector("[data-cbx-label]");
  var status = root.querySelector("[data-cbx-status]");
  var sessBar = root.querySelector("[data-cbx-sessions]");
  var form = root.querySelector("[data-cbx-form]");
  var msg = root.querySelector("[data-cbx-msg]");
  var tz = root.querySelector("[data-cbx-tz]");
  var sayBtn = root.querySelector("[data-cbx-say]");
  var refreshBtn = root.querySelector("[data-cbx-refresh]");

  nickIn.value = savedNick || "anon";
  sessIn.value = session;
  labelIn.value = "";
  if (boot.sessions && boot.sessions.length) {
    var cur = boot.sessions.filter(function (s) {
      return s.session === session;
    })[0];
    if (cur && cur.label) labelIn.value = cur.label;
  }
  try {
    tz.value = Intl.DateTimeFormat().resolvedOptions().timeZone;
  } catch (e) {
    tz.value = "";
  }

  cbxPaintSessions(sessBar, boot, session);
  cbxPaintLog(log, boot.lines || []);

  if (boot.confirm) status.textContent = boot.confirm;
  else if (boot.error) status.textContent = "error: " + boot.error;
  else
    status.textContent =
      "session " + session + " · " + (boot.lines || []).length + " line(s) · CBX-001";

  function syncHiddens() {
    var n = (nickIn.value || "anon").trim() || "anon";
    var s = (sessIn.value || "live").trim() || "live";
    var lab = (labelIn.value || "").trim();
    root.querySelector("[data-cbx-user-hidden]").value = n;
    root.querySelector("[data-cbx-sess-hidden]").value = s;
    root.querySelector("[data-cbx-label-hidden]").value = lab;
    try {
      localStorage.setItem(nickKey, n);
    } catch (e) {}
    return { n: n, s: s, lab: lab };
  }

  /** GET-only channel pull — never POSTs, never re-sends last message. */
  function cbxRefreshChannel(opts) {
    opts = opts || {};
    var ids = syncHiddens();
    var url =
      location.pathname +
      "?session=" +
      encodeURIComponent(ids.s) +
      "&_cbx=" +
      Date.now(); // cache-bust
    if (refreshBtn) refreshBtn.disabled = true;
    if (!opts.silent) status.textContent = "refreshing…";

    return fetch(url, {
      method: "GET",
      credentials: "same-origin",
      cache: "no-store",
      headers: { "X-Requested-With": "CBX-001-refresh" },
    })
      .then(function (res) {
        return res.text();
      })
      .then(function (html) {
        var next = cbxParseBootFromHtml(html);
        if (!next) {
          status.textContent = "refresh failed — could not read channel";
          return null;
        }
        window.CHATBOX_BOOT = next;
        session = next.session || ids.s;
        if (sessIn.value.trim() === "" || sessIn.value === boot.session) {
          sessIn.value = session;
        }
        cbxPaintSessions(sessBar, next, session);
        cbxPaintLog(log, next.lines || []);
        var n = (next.lines || []).length;
        status.textContent =
          "session " +
          session +
          " · " +
          n +
          " line(s) · refreshed " +
          new Date().toLocaleTimeString();
        return next;
      })
      .catch(function (err) {
        console.error(err);
        status.textContent = "refresh failed — try again";
        return null;
      })
      .finally(function () {
        if (refreshBtn) refreshBtn.disabled = false;
      });
  }

  if (refreshBtn) {
    refreshBtn.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      cbxRefreshChannel();
    });
  }

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    e.stopPropagation();

    var text = (msg.value || "").trim();
    if (!text) return;

    var ids = syncHiddens();
    var url =
      location.pathname +
      "?session=" +
      encodeURIComponent(ids.s);

    var fd = new FormData(form);
    // ensure message is current
    fd.set("message", text);
    fd.set("username", ids.n);
    fd.set("chat_session", ids.s);
    fd.set("chat_session_label", ids.lab);

    sayBtn.disabled = true;
    status.textContent = "sending…";

    fetch(url, {
      method: "POST",
      body: fd,
      credentials: "same-origin",
      headers: { "X-Requested-With": "CBX-001" },
    })
      .then(function (res) {
        return res.text();
      })
      .then(function (html) {
        var next = cbxParseBootFromHtml(html);
        if (!next) {
          // POST may have worked; pull with GET only (no second write)
          return cbxRefreshChannel({ silent: true }).then(function (pulled) {
            msg.value = "";
            msg.focus();
            if (!pulled) {
              status.textContent =
                "posted, but could not refresh log — hit Refresh";
            }
          });
        }
        window.CHATBOX_BOOT = next;
        session = next.session || ids.s;
        sessIn.value = session;
        cbxPaintSessions(sessBar, next, session);
        cbxPaintLog(log, next.lines || []);
        msg.value = "";
        msg.focus();
        if (next.confirm) status.textContent = next.confirm;
        else
          status.textContent =
            "session " +
            session +
            " · " +
            (next.lines || []).length +
            " line(s) · CBX-001";
      })
      .catch(function (err) {
        console.error(err);
        status.textContent = "send failed — try again";
      })
      .finally(function () {
        sayBtn.disabled = false;
      });
  });
}

function ToggleCBX001() {
  if (!window.RomHost) {
    console.warn("RomHost missing");
    return;
  }
  RomHost.toggle("CBX-001", { title: "Chatterbox · CBX-001" });
}

function registerCBX001() {
  if (!window.RomHost) {
    setTimeout(registerCBX001, 30);
    return;
  }
  RomHost.register("CBX-001", buildCBX001);
}
registerCBX001();
