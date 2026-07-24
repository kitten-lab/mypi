"""
mypi-tui — EDN-style split: header section nav · left index · right viewer

  cd C:\\Builds\\my-pocket-internet\\ledger
  python mypi_tui.py

Left = list of report items (terms / crates / edges / TPS windows).
Right = materials viewer (related crates + full body / tags / history).
Primary list never replaces itself when you open a tag or window.
"""

from __future__ import annotations

import json
import re
import sys
from datetime import datetime
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))

from mypi_ledger import (  # noqa: E402
    DEFAULT_DB,
    append_charlie,
    charlie_edges,
    connect,
    create_crate,
    get_crate,
    hard_delete,
    history,
    init_db,
    list_crates,
    list_tps_shelves,
    set_crate_charlie,
    soft_delete,
    stats,
    stem_head_c_uid,
    tps_window_seconds,
)

# Oriel / RX venDesk code book (JSON — not crates)
_VEN_REGISTRY = (
    Path(__file__).resolve().parent.parent / "z" / "ven_registry" / "registry.json"
)

try:
    from rich.text import Text
    from textual import on
    from textual.app import App, ComposeResult
    from textual.binding import Binding
    from textual.containers import Horizontal, Vertical, VerticalScroll
    from textual.widgets import Button, DataTable, Footer, Header, Input, Label, Static
except ImportError:
    print("Need textual: pip install textual")
    sys.exit(1)

# Styles (applied via Text API — never parse crate payload as markup)
_S_HEAD = "bold #9ed4b0"
_S_SEC = "bold #7ab890"
_S_DIM = "dim #5a8a6a"
_S_VAL = "#b8e0c8"
_S_EDGE = "#8fc9a0"
_S_REL = "#5a8a6a"
_S_WARN = "bold red"
_S_META = "dim #6a9a7a"
_S_BODY = "#c5e6d0"
_S_H1 = "bold #d4f0dc"
_S_H2 = "bold #b8e0c8"
_S_H3 = "bold #9ed4b0"
_S_CODE = "#e2f5e8"
_S_CODE_EDGE = "dim #3a5a48"
_S_QUOTE = "italic #8fb89a"
_S_LINK = "underline #9ed4b0"
_S_URL = "dim #4a7a5a"

# Optional ``` fences still work if present, but are NOT required.
# Terminal styling may own ``` — prefer auto grid/prose detection.
_RE_FENCE = re.compile(r"^```")
_RE_HEADING = re.compile(r"^(#{1,6})\s+(.*)$")
_RE_QUOTE = re.compile(r"^>\s?(.*)$")
_RE_HR = re.compile(r"^(-{3,}|\*{3,}|_{3,})\s*$")
_RE_MD_LINK = re.compile(r"\[([^\]]+)\]\(([^)]+)\)")
_RE_BOLD = re.compile(r"\*\*(.+?)\*\*")
_RE_ITALIC = re.compile(r"(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)")
_RE_CODE_SPAN = re.compile(r"`([^`]+)`")
# Letter/glyph grids: "A L P H A", "A  E", spaced singles, short runs
_RE_GRID_LINE = re.compile(
    r"^(?:"
    r"(?:[\w'’.\-](?:\s+[\w'’.\-]){1,24})"  # spaced tokens (letter grids)
    r"|(?:[\w'’.\-]{1,3}(?:\s{2,}[\w'’.\-]{1,3}){1,24})"  # multi-space columns
    r")\s*$"
)
_RE_ONLY_SPACED_CHARS = re.compile(r"^(?:\S\s+){1,40}\S\s*$")


def _path_of(row) -> str:
    """Prefer place_path; only fall back to sys/dom/room when path empty."""
    try:
        pp = (row["place_path"] if "place_path" in row.keys() else "") or ""
    except Exception:
        pp = ""
    pp = str(pp).strip()
    if pp:
        return pp
    try:
        sys_ = (row["sys"] or "").strip()
        dom = (row["dom"] or "").strip()
        room = (row["room"] or "").strip()
    except Exception:
        return ""
    parts = [p for p in (sys_, dom, room) if p]
    return "/".join(parts)


def _clip(s: str, n: int = 48) -> str:
    s = (s or "").replace("\n", " ")
    return s if len(s) <= n else s[: n - 1] + "…"


def _load_ven_registry(path: Path | None = None) -> dict:
    """Load z/ven_registry/registry.json (same file RX venDesk uses)."""
    p = path or _VEN_REGISTRY
    empty: dict = {"version": 1, "updated_at": 0, "entries": [], "path": str(p)}
    if not p.is_file():
        empty["error"] = "missing file"
        return empty
    try:
        data = json.loads(p.read_text(encoding="utf-8"))
    except Exception as e:
        empty["error"] = str(e)
        return empty
    if not isinstance(data, dict):
        empty["error"] = "registry not an object"
        return empty
    entries = data.get("entries")
    if not isinstance(entries, list):
        # fossil map shape { "ABL-000": {...} }
        entries = []
        for k, v in data.items():
            if k in ("version", "updated_at", "entries") or not isinstance(v, dict):
                continue
            row = dict(v)
            row.setdefault("kven", k)
            entries.append(row)
    # sort by kven
    entries = [e for e in entries if isinstance(e, dict)]
    entries.sort(key=lambda e: str(e.get("kven") or e.get("id") or "").upper())
    return {
        "version": int(data.get("version") or 1),
        "updated_at": int(data.get("updated_at") or 0),
        "entries": entries,
        "path": str(p),
        "error": None,
    }


def _ven_entry_by_key(reg: dict, key: str) -> dict | None:
    key = (key or "").strip()
    if key.startswith("ven:"):
        key = key[4:]
    for e in reg.get("entries") or []:
        if not isinstance(e, dict):
            continue
        if str(e.get("id") or "") == key or str(e.get("kven") or "") == key:
            return e
    return None


def _render_ven_entry(e: dict, *, reg_path: str = "") -> Text:
    """Rich Text viewer for one VEN registry row."""
    out = Text()
    kven = str(e.get("kven") or "—")
    label = str(e.get("label") or "—")
    out.append(kven, style=_S_HEAD)
    out.append("  ·  ", style=_S_DIM)
    out.append(label, style=_S_H2)
    out.append("\n\n")

    def add(line: Text | str = "") -> None:
        if isinstance(line, Text):
            out.append_text(line)
        else:
            out.append(str(line))
        out.append("\n")

    add(_t_section("identity"))
    add(_t_kv("id", str(e.get("id") or "—")))
    add(_t_kv("kven", kven))
    add(_t_kv("label", label))
    add(_t_kv("type", str(e.get("type") or "—")))
    add()
    alts = e.get("alts") if isinstance(e.get("alts"), list) else []
    matches = e.get("matches") if isinstance(e.get("matches"), list) else []
    add(_t_section("alts · also written as"))
    if alts:
        for a in alts:
            add(Text(f"  · {a}", style=_S_VAL))
    else:
        add(Text("  (none)", style=_S_DIM))
    add()
    add(_t_section("matches · log spellings"))
    if matches:
        for m in matches:
            add(Text(f"  · {m}", style=_S_BODY))
    else:
        add(Text("  (none)", style=_S_DIM))
    add()
    notes = str(e.get("notes") or "").strip()
    add(_t_section("notes"))
    if notes:
        for line in notes.splitlines() or [notes]:
            add(Text(line, style=_S_BODY))
    else:
        add(Text("  (empty)", style=_S_DIM))
    add()
    add(_t_section("time"))
    add(_t_kv("created", _fmt_ts(e.get("created") or e.get("created_at"))))
    add(_t_kv("updated", _fmt_ts(e.get("updated") or e.get("updated_at"))))
    add()
    add(_t_section("source file"))
    add(Text(f"  {reg_path or str(_VEN_REGISTRY)}", style=_S_META))
    add(Text("  (JSON code book · not a crate in sqlite)", style=_S_DIM))
    add()
    add(Text("5 VEN  ·  r refresh  ·  encode → VEN from IO import also lands here", style=_S_DIM))
    return out


def _g(row, key: str, default: str = "") -> str:
    """Safe row field → string (sqlite3.Row or mapping)."""
    try:
        if hasattr(row, "keys") and key not in row.keys():
            return default
        v = row[key]
    except Exception:
        return default
    if v is None:
        return default
    return str(v)


def _fmt_ts(unix) -> str:
    """Human local time + raw unix for puritanical dual-read."""
    if unix is None or unix == "" or unix == 0 or unix == "0":
        return "—"
    try:
        u = int(unix)
    except (TypeError, ValueError):
        return str(unix)
    if u <= 0:
        return "—"
    try:
        human = datetime.fromtimestamp(u).strftime("%Y-%m-%d %H:%M:%S")
    except (OSError, OverflowError, ValueError):
        human = "?"
    return f"{human}  ·  {u}"


def _split_tags(tags: list[str]) -> tuple[list[str], list[str], list[str]]:
    """user-ish / place-auto / edge full-forms."""
    user: list[str] = []
    place: list[str] = []
    chains: list[str] = []
    for t in tags:
        t = (t or "").strip()
        if not t:
            continue
        if "*" in t and ">" in t:
            chains.append(t)
        elif t.startswith(("path:", "sys:", "dom:", "mod:", "room:")) or t.startswith(
            "@"
        ):
            place.append(t)
        else:
            user.append(t)
    return user, place, chains


def _pretty_meta(meta_json: str, max_chars: int = 1200) -> str:
    raw = (meta_json or "").strip()
    if not raw or raw == "{}":
        return "—"
    try:
        obj = json.loads(raw)
        text = json.dumps(obj, indent=2, ensure_ascii=False, sort_keys=True)
    except Exception:
        text = raw
    if len(text) > max_chars:
        return text[: max_chars - 1] + "…"
    return text


def _t_blank() -> Text:
    return Text("")


def _t_section(title: str) -> Text:
    return Text(f"── {title} ──", style=_S_SEC)


def _t_kv(label: str, value: str, *, empty: str = "—") -> Text:
    """Label + value as plain Text (safe for any crate payload)."""
    t = Text()
    t.append(f"{label:<10} ", style=_S_DIM)
    val = (value if value is not None else "").strip()
    if not val or val == "—":
        t.append(empty, style=_S_DIM)
    else:
        t.append(val, style=_S_VAL)
    return t


def _t_line(*parts: tuple[str, str]) -> Text:
    t = Text()
    for text, style in parts:
        t.append(text, style=style or _S_VAL)
    return t


def _looks_like_grid_line(line: str) -> bool:
    """True for letter grids / columny lines — no fences needed."""
    s = line.rstrip("\n")
    if not s or not s.strip():
        return False
    # Never treat markdown structure as grid
    if s.lstrip().startswith(("#", ">", "-", "*", "|", "[")):
        if s.lstrip().startswith("|"):
            return True  # md tables are pre-ish
        # bare list / heading / quote — not grid
        if _RE_HEADING.match(s) or _RE_QUOTE.match(s) or _RE_HR.match(s):
            return False
        if s.lstrip().startswith(("- ", "* ", "+ ")):
            return False
    stripped = s.strip()
    # Pure short glyph runs with internal spaces (A L P H A, A E, …)
    if _RE_ONLY_SPACED_CHARS.match(stripped) and "  " not in stripped:
        # single spaces between 1-char tokens
        parts = stripped.split()
        if 2 <= len(parts) <= 32 and all(len(p) <= 2 for p in parts):
            return True
    if _RE_GRID_LINE.match(stripped):
        parts = stripped.split()
        # Prefer short tokens (glyphs/syllables), not prose sentences
        if parts and all(len(p) <= 8 for p in parts) and len(parts) >= 2:
            # Reject "normal" sentences (many long words)
            longish = sum(1 for p in parts if len(p) > 4)
            if longish <= max(1, len(parts) // 3):
                return True
    # Multi-space column layout
    if re.search(r"\S\s{2,}\S", s) and len(s) < 80:
        return True
    return False


def _strip_optional_fences(lines: list[str]) -> list[str]:
    """If the whole body (or a block) is wrapped in ```, unwrap — never required."""
    if len(lines) >= 2 and _RE_FENCE.match(lines[0].strip()) and _RE_FENCE.match(
        lines[-1].strip()
    ):
        return lines[1:-1]
    return lines


def _append_inline_md(t: Text, s: str, base_style: str = _S_BODY) -> None:
    """
    Light inline markdown into Text (no Rich markup parse).
    Handles [text](url), **bold**, *italic*, `code` — rest is literal.
    """
    if not s:
        return

    # Tokenize by finding earliest special construct
    i = 0
    n = len(s)
    while i < n:
        # find next candidate
        candidates: list[tuple[int, str, re.Match[str]]] = []
        for name, rx in (
            ("link", _RE_MD_LINK),
            ("bold", _RE_BOLD),
            ("code", _RE_CODE_SPAN),
            ("italic", _RE_ITALIC),
        ):
            m = rx.search(s, i)
            if m:
                candidates.append((m.start(), name, m))
        if not candidates:
            t.append(s[i:], style=base_style)
            break
        candidates.sort(key=lambda x: x[0])
        start, name, m = candidates[0]
        if start > i:
            t.append(s[i:start], style=base_style)
        if name == "link":
            label, url = m.group(1), m.group(2)
            t.append(label, style=_S_LINK)
            if url and not url.startswith("#"):
                t.append(" ", style=base_style)
                t.append(url, style=_S_URL)
        elif name == "bold":
            t.append(m.group(1), style="bold " + base_style)
        elif name == "italic":
            t.append(m.group(1), style="italic " + base_style)
        elif name == "code":
            t.append(m.group(1), style=_S_CODE)
        i = m.end()


def _render_prose_line(line: str) -> Text:
    """One non-grid line: headings, quotes, hr, lists, inline md."""
    s = line.rstrip("\n")
    t = Text(no_wrap=False)

    if not s.strip():
        return t

    hm = _RE_HEADING.match(s)
    if hm:
        level = len(hm.group(1))
        style = _S_H1 if level <= 1 else _S_H2 if level <= 3 else _S_H3
        t.append("▸ ", style=_S_DIM)
        _append_inline_md(t, hm.group(2).strip(), style)
        return t

    if _RE_HR.match(s.strip()):
        t.append("─" * min(40, max(8, len(s))), style=_S_DIM)
        return t

    qm = _RE_QUOTE.match(s)
    if qm:
        t.append("│ ", style=_S_DIM)
        _append_inline_md(t, qm.group(1), _S_QUOTE)
        return t

    stripped = s.lstrip()
    indent = len(s) - len(stripped)
    if indent:
        t.append(" " * indent, style=_S_BODY)
    if stripped.startswith(("- ", "* ", "+ ")):
        t.append("• ", style=_S_DIM)
        _append_inline_md(t, stripped[2:], _S_BODY)
        return t
    if re.match(r"^\d+\.\s+", stripped):
        m = re.match(r"^(\d+\.)\s+(.*)$", stripped)
        if m:
            t.append(m.group(1) + " ", style=_S_DIM)
            _append_inline_md(t, m.group(2), _S_BODY)
            return t

    _append_inline_md(t, s, _S_BODY)
    return t


def _render_grid_line(line: str) -> Text:
    """Preserve spacing; style as material block (letter grids, columns)."""
    # Keep trailing spaces meaningful for alignment; drop only newline
    s = line.rstrip("\n")
    t = Text(no_wrap=True)
    t.append("  ", style=_S_CODE_EDGE)
    t.append(s, style=_S_CODE)
    return t


def _render_body_material(body: str) -> list[Text]:
    """
    Format crate body for the viewer.

    - Does NOT require ``` fences (terminal may use those for its own chrome).
    - Auto-detects letter/glyph grids and multi-space columns as pre blocks.
    - Light markdown: # headings, >, lists, [text](url), **bold**, *italic*.
    - Fences, if present, still unwrap as pre (optional, never required).
    """
    raw = (body or "").replace("\r\n", "\n").replace("\r", "\n")
    if not raw.strip():
        return [Text("(empty body)", style=_S_DIM)]

    lines = raw.split("\n")
    lines = _strip_optional_fences(lines)
    out: list[Text] = []

    i = 0
    n = len(lines)
    in_fence = False

    while i < n:
        line = lines[i]

        # Optional fence toggle — only if user used them; not required
        if _RE_FENCE.match(line.strip()):
            in_fence = not in_fence
            if in_fence:
                out.append(Text("  ┌── material ──", style=_S_CODE_EDGE))
            else:
                out.append(Text("  └──", style=_S_CODE_EDGE))
            i += 1
            continue

        if in_fence:
            out.append(_render_grid_line(line))
            i += 1
            continue

        # Auto-detect a run of grid lines (letter matrices, columns)
        if _looks_like_grid_line(line):
            run: list[str] = []
            while i < n and (
                _looks_like_grid_line(lines[i])
                or (
                    run
                    and not lines[i].strip()
                    and i + 1 < n
                    and _looks_like_grid_line(lines[i + 1])
                )
            ):
                # blank line inside grid run → keep as spacer
                run.append(lines[i])
                i += 1
            out.append(Text("  ┌── grid ──", style=_S_CODE_EDGE))
            for gl in run:
                if not gl.strip():
                    out.append(Text("  │", style=_S_CODE_EDGE))
                else:
                    out.append(_render_grid_line(gl))
            out.append(Text("  └──", style=_S_CODE_EDGE))
            continue

        # Markdown table row → pre-ish
        if line.lstrip().startswith("|") and "|" in line.lstrip()[1:]:
            out.append(_render_grid_line(line))
            i += 1
            continue

        out.append(_render_prose_line(line))
        i += 1

    return out


class MypiTui(App[None]):
    TITLE = "mypi ledger"
    CSS = """
    /* layout (shared) */
    #topnav {
        height: auto;
        dock: top;
        padding: 0 1;
    }
    #topnav Horizontal { height: auto; }
    #topnav Button {
        margin: 0 1 0 0;
        min-width: 12;
        width: auto;
        border: none;
    }
    #body { height: 1fr; }
    #index-pane {
        width: 38%;
        min-width: 26;
        padding: 0 1;
    }
    #viewer-pane {
        width: 1fr;
        padding: 0 1;
    }
    #index-title, #viewer-title { text-style: bold; margin: 1 0 0 0; }
    #index-table { height: 1fr; }
    #related-table { height: 8; margin-bottom: 1; }
    #viewer-scroll {
        height: 1fr;
        overflow-x: auto;
    }
    #viewer {
        height: auto;
        padding: 1 1 2 1;
    }
    /* bottom stack: tag bar sits under work, status under that, footer last */
    #bottom-stack {
        dock: bottom;
        height: auto;
        layout: vertical;
    }
    #tag-row {
        height: 3;
        padding: 0 1;
        layout: horizontal;
    }
    #tag-input { width: 1fr; }
    #btn-tag, #btn-tag-set { min-width: 10; margin-left: 1; }
    #status { height: 2; padding: 0 1; }

    /* ── forest (default) ───────────────────────────── */
    Screen.theme-forest { background: #0c1210; color: #b8e0c8; }
    Screen.theme-forest #topnav {
        background: #0a100e;
        border-bottom: tall #2a4a38;
    }
    Screen.theme-forest #topnav .nav-on { background: #143314; text-style: bold; }
    Screen.theme-forest #index-pane { border-right: tall #2a4a38; }
    Screen.theme-forest #index-title,
    Screen.theme-forest #viewer-title { color: #7ab890; }
    Screen.theme-forest #related-table { border: tall #2a4a38; }
    Screen.theme-forest #viewer-scroll {
        border: tall #2a4a38;
        background: #0a100e;
    }
    Screen.theme-forest #viewer { background: #0a100e; }
    Screen.theme-forest .muted { color: #5a8a6a; }
    Screen.theme-forest #bottom-stack { background: #0a100e; border-top: tall #2a4a38; }
    Screen.theme-forest #status { color: #7ab890; background: #0a100e; }
    Screen.theme-forest #tag-row { background: #0a100e; }
    Screen.theme-forest #tag-input {
        background: #0c1410;
        border: tall #2a4a38;
        color: #b8e0c8;
    }

    /* ── barbie (toggle) ────────────────────────────── */
    Screen.theme-barbie { background: #2a1020; color: #ffe4f0; }
    Screen.theme-barbie #topnav {
        background: #3d1530;
        border-bottom: tall #ff69b4;
    }
    Screen.theme-barbie #topnav Button {
        color: #fff0f8;
        background: #5a2048;
    }
    Screen.theme-barbie #topnav .nav-on {
        background: #ff69b4;
        color: #2a1020;
        text-style: bold;
    }
    Screen.theme-barbie #index-pane { border-right: tall #ff69b4; }
    Screen.theme-barbie #index-title,
    Screen.theme-barbie #viewer-title { color: #ffb6d9; }
    Screen.theme-barbie #related-table { border: tall #e85a9b; }
    Screen.theme-barbie #viewer-scroll {
        border: tall #ff69b4;
        background: #1f0a18;
    }
    Screen.theme-barbie #viewer { background: #1f0a18; color: #ffe4f0; }
    Screen.theme-barbie .muted { color: #d48ab0; }
    Screen.theme-barbie #bottom-stack {
        background: #3d1530;
        border-top: tall #ff69b4;
    }
    Screen.theme-barbie #status {
        color: #ffb6d9;
        background: #3d1530;
    }
    Screen.theme-barbie #tag-row { background: #3d1530; }
    Screen.theme-barbie #tag-input {
        background: #2a1020;
        border: tall #ff69b4;
        color: #ffe4f0;
    }
    Screen.theme-barbie #btn-tag,
    Screen.theme-barbie #btn-tag-set {
        background: #ff69b4;
        color: #2a1020;
        text-style: bold;
    }
    Screen.theme-barbie DataTable {
        background: #1f0a18;
        color: #ffe4f0;
    }
    Screen.theme-barbie Footer { background: #3d1530; color: #ffb6d9; }
    Screen.theme-barbie Header { background: #5a2048; color: #fff0f8; }
    """
    BINDINGS = [
        Binding("q", "quit", "Quit"),
        Binding("r", "refresh", "Refresh"),
        Binding("1", "sec_crates", "Crates"),
        Binding("2", "sec_charlie", "Charlie"),
        Binding("3", "sec_edges", "Edges"),
        Binding("4", "sec_tps", "TPS"),
        Binding("5", "sec_ven", "VEN"),
        Binding("d", "demo", "Demo"),
        Binding("t", "focus_tag", "Tag"),
        Binding("b", "toggle_theme", "Barbie"),
        Binding("l", "toggle_lineage", "Lineage"),
        # Soft-del = devalue (deleted_at); NUKE = hard remove one crate
        Binding("delete", "soft_del", "Soft-del"),
        Binding("backspace", "soft_del", "Soft-del", show=False),
        Binding("shift+delete", "hard_del", "NUKE"),
    ]

    def __init__(self) -> None:
        super().__init__()
        self.conn = connect()
        init_db(self.conn)
        # crates | charlie | edges | tps | ven
        self._section = "crates"
        self._focus_term: str | None = None
        self._focus_tps: str | None = None
        self._selected_crate: str | None = None
        self._selected_ven: str | None = None  # kven or id
        self._ven_reg: dict | None = None
        self._theme = "forest"  # forest | barbie

    def compose(self) -> ComposeResult:
        yield Header(show_clock=True)
        with Horizontal(id="topnav"):
            yield Button("1 Crates", id="sec-crates")
            yield Button("2 Charlie", id="sec-charlie")
            yield Button("3 Edges", id="sec-edges")
            yield Button("4 TPS", id="sec-tps")
            yield Button("5 VEN", id="sec-ven")
            yield Static(" · ", classes="muted")
            yield Button("Refresh", id="btn-refresh")
            yield Button("Demo", id="btn-demo")
            yield Button("Soft-del", id="btn-del")
            yield Button("NUKE", id="btn-hard")
            yield Static(" · ", classes="muted")
            yield Button("Barbie ♡", id="btn-theme")
        with Horizontal(id="body"):
            with Vertical(id="index-pane"):
                yield Label("Index", id="index-title")
                yield Static("report items", id="index-hint", classes="muted")
                yield DataTable(id="index-table")
            with Vertical(id="viewer-pane"):
                yield Label("Viewer", id="viewer-title")
                yield Static(
                    "related crates (select for full payload)",
                    id="related-hint",
                    classes="muted",
                )
                yield DataTable(id="related-table")
                with VerticalScroll(id="viewer-scroll"):
                    yield Static(
                        "Select an item on the left.",
                        id="viewer",
                        markup=False,
                    )
        # Charlie lives under the work (not floating in the header)
        with Vertical(id="bottom-stack"):
            with Horizontal(id="tag-row"):
                yield Input(
                    placeholder="Charlie: tag · aubel,lore · aubel*knows>iox  (selected crate)",
                    id="tag-input",
                )
                yield Button("Tag+", id="btn-tag")
                yield Button("Set raw", id="btn-tag-set")
            yield Static("", id="status")
        yield Footer()

    def on_mount(self) -> None:
        for tid in ("index-table", "related-table"):
            t = self.query_one(f"#{tid}", DataTable)
            t.cursor_type = "row"
        self._apply_theme()
        self._load_index()
        self._set_nav_highlight()

    def _apply_theme(self) -> None:
        """forest (default ledger) ↔ barbie (cute pink)"""
        scr = self.screen
        scr.remove_class("theme-forest")
        scr.remove_class("theme-barbie")
        if self._theme == "barbie":
            scr.add_class("theme-barbie")
            self.TITLE = "mypi ledger ♡ barbie"
        else:
            scr.add_class("theme-forest")
            self.TITLE = "mypi ledger"
        try:
            btn = self.query_one("#btn-theme", Button)
            btn.label = "Forest" if self._theme == "barbie" else "Barbie ♡"
        except Exception:
            pass

    def action_toggle_theme(self) -> None:
        self._theme = "barbie" if self._theme == "forest" else "forest"
        self._apply_theme()
        label = "BARBIE MODE" if self._theme == "barbie" else "forest mode"
        self._status_line(label)

    def _set_nav_highlight(self) -> None:
        mapping = {
            "crates": "sec-crates",
            "charlie": "sec-charlie",
            "edges": "sec-edges",
            "tps": "sec-tps",
            "ven": "sec-ven",
        }
        for sec, bid in mapping.items():
            btn = self.query_one(f"#{bid}", Button)
            if sec == self._section:
                btn.add_class("nav-on")
            else:
                btn.remove_class("nav-on")

    def action_refresh(self) -> None:
        if self._section == "ven":
            self._ven_reg = None  # force reload from disk
        self._load_index()
        # re-show focus if any
        if self._section == "ven" and self._selected_ven:
            self._show_ven(self._selected_ven)
        elif self._focus_term and self._section == "charlie":
            self._show_tag_in_viewer(self._focus_term)
        elif self._focus_tps and self._section == "tps":
            self._show_tps_in_viewer(self._focus_tps)
        elif self._selected_crate:
            self._show_crate(self._selected_crate)

    def action_sec_crates(self) -> None:
        self._section = "crates"
        self._focus_term = None
        self._focus_tps = None
        self._selected_ven = None
        self._load_index()
        self._clear_viewer("Select a crate on the left.")

    def action_sec_charlie(self) -> None:
        self._section = "charlie"
        self._focus_tps = None
        self._selected_ven = None
        self._load_index()
        self._clear_viewer("Select a term → related crates appear above; pick one for full body.")

    def action_sec_edges(self) -> None:
        self._section = "edges"
        self._focus_term = None
        self._focus_tps = None
        self._selected_ven = None
        self._load_index()
        self._clear_viewer("Select an edge → crate opens in the viewer.")

    def action_sec_tps(self) -> None:
        self._section = "tps"
        self._focus_term = None
        self._selected_ven = None
        self._load_index()
        self._clear_viewer("Select a TPS window → crates in that window; pick one for full body.")

    def action_sec_ven(self) -> None:
        self._section = "ven"
        self._focus_term = None
        self._focus_tps = None
        self._selected_crate = None
        self._ven_reg = None
        self._load_index()
        self._clear_viewer(
            "VEN registry (z/ven_registry) · select a KVEN on the left · not sqlite crates"
        )

    def action_demo(self) -> None:
        self.demo()

    def action_soft_del(self) -> None:
        self.del_soft()

    def action_focus_tag(self) -> None:
        """Focus Charlie tag input (backend tagging on selected crate)."""
        try:
            self.query_one("#tag-input", Input).focus()
        except Exception:
            pass

    def action_hard_del(self) -> None:
        self.del_hard()

    def _clear_viewer(self, msg: str) -> None:
        related = self.query_one("#related-table", DataTable)
        related.clear(columns=True)
        self.query_one("#viewer", Static).update(msg)
        self.query_one("#viewer-title", Label).update("Viewer")
        self.query_one("#related-hint", Static).update("related crates")
        self._selected_crate = None

    def _status_line(self, note: str = "") -> None:
        st = stats(self.conn)
        try:
            w = tps_window_seconds(self.conn)
        except Exception:
            w = 900
        n_ven = 0
        if self._section == "ven":
            reg = self._ven_reg if self._ven_reg is not None else _load_ven_registry()
            self._ven_reg = reg
            n_ven = len(reg.get("entries") or [])
            base = f"  VEN codes={n_ven}  ·  {_VEN_REGISTRY}"
        else:
            base = f"  crates={st['crates']}  TPS={w}s  v{st['schema_version']}  ·  {DEFAULT_DB}"
        if note:
            base = f"{base}  ·  {note}"
        if self._selected_crate and self._section != "ven":
            base = f"{base}  ·  sel={self._selected_crate[:18]}"
        if self._selected_ven and self._section == "ven":
            base = f"{base}  ·  sel={self._selected_ven}"
        self.query_one("#status", Static).update(base)

    @staticmethod
    def _crate_uid_from_row_key(key: str | None) -> str | None:
        """Map a DataTable row_key value to a c_uid, if the row is crate-shaped."""
        if not key:
            return None
        key = str(key)
        if key.startswith("term:") or key.startswith("tps:"):
            return None
        if key.startswith("edge:"):
            parts = key.split(":", 2)
            if len(parts) >= 3 and parts[2]:
                return parts[2]
            return None
        # crates section uses bare c_uid; related table too
        if key.startswith("crate.") or key:
            return key
        return None

    def _note_selected_from_key(self, key: str | None) -> None:
        """Track highlighted/selected crate without requiring Enter."""
        uid = self._crate_uid_from_row_key(key)
        if not uid:
            return
        if get_crate(self.conn, uid):
            self._selected_crate = uid

    def _resolve_target_crate(self) -> str | None:
        """Prefer explicit selection; else cursor row on either table."""
        if self._selected_crate and get_crate(self.conn, self._selected_crate):
            return self._selected_crate
        for tid in ("related-table", "index-table"):
            try:
                table = self.query_one(f"#{tid}", DataTable)
            except Exception:
                continue
            if not table.row_count:
                continue
            try:
                coord = table.cursor_coordinate
                cell = table.coordinate_to_cell_key(coord)
                rk = cell.row_key
                key = str(rk.value) if rk is not None else None
            except Exception:
                key = None
            uid = self._crate_uid_from_row_key(key)
            if uid and get_crate(self.conn, uid):
                self._selected_crate = uid
                return uid
        return None

    def _load_index(self) -> None:
        self._set_nav_highlight()
        self._status_line()
        table = self.query_one("#index-table", DataTable)
        title = self.query_one("#index-title", Label)
        hint = self.query_one("#index-hint", Static)
        table.clear(columns=True)

        if self._section == "crates":
            title.update("Index · Crates")
            hint.update("heads only (latest rev) — related lists revs · L → head")
            table.add_columns("c_uid", "kind", "place", "agent", "body")
            for r in list_crates(self.conn, limit=120, heads_only=True):
                table.add_row(
                    r["c_uid"][:18],
                    (r["kind"] or "")[:8],
                    _clip(_path_of(r), 16),
                    _clip(r["agent"] or "", 10),
                    _clip(r["body"] or "", 28),
                    key=r["c_uid"],
                )

        elif self._section == "charlie":
            title.update("Index · Charlie terms")
            hint.update("select term → crates in right pane")
            table.add_columns("term", "gravity", "updated")
            rows = list(
                self.conn.execute(
                    "SELECT term, gravity, updated_at FROM thread_terms "
                    "ORDER BY gravity DESC, term ASC LIMIT 250"
                )
            )
            for g in rows:
                term = g[0] or ""
                if "*" in term or ">" in term:
                    continue
                table.add_row(term, str(g[1]), str(g[2]), key=f"term:{term}")

        elif self._section == "edges":
            title.update("Index · Charlie edges")
            hint.update("select edge → crate in viewer")
            table.add_columns("from", "rel", "to", "c_uid")
            for e in charlie_edges(self.conn, 100):
                eid = e["id"] if "id" in e.keys() else id(e)
                table.add_row(
                    e["from_term"],
                    e["rel"],
                    e["to_term"],
                    e["c_uid"][:16],
                    key=f"edge:{eid}:{e['c_uid']}",
                )

        elif self._section == "tps":
            title.update("Index · TPS windows")
            hint.update("select window → crates in right pane")
            table.add_columns("tps_uid", "window", "width", "n")
            for s in list_tps_shelves(self.conn, 80):
                table.add_row(
                    s["tps_uid"],
                    str(s["window_unix"]),
                    str(s["window_seconds"]),
                    str(s["n_crates"]),
                    key=f"tps:{s['tps_uid']}",
                )

        else:  # ven — JSON code book (RX venDesk / encode → VEN)
            title.update("Index · VEN registry")
            reg = self._ven_reg if self._ven_reg is not None else _load_ven_registry()
            self._ven_reg = reg
            n = len(reg.get("entries") or [])
            err = reg.get("error")
            if err:
                hint.update(f"ERROR · {err} · {reg.get('path', '')}")
            else:
                hint.update(
                    f"{n} codes · z/ven_registry · encode→VEN from IO lands here"
                )
            table.add_columns("kven", "label", "type", "matches")
            for e in reg.get("entries") or []:
                if not isinstance(e, dict):
                    continue
                kven = str(e.get("kven") or "")
                eid = str(e.get("id") or kven)
                matches = e.get("matches") if isinstance(e.get("matches"), list) else []
                table.add_row(
                    kven or "—",
                    _clip(str(e.get("label") or ""), 22),
                    _clip(str(e.get("type") or ""), 8),
                    _clip(", ".join(str(m) for m in matches[:4]), 28),
                    key=f"ven:{eid}",
                )
            if n == 0 and not err:
                table.add_row("(empty)", "push from IO encode → VEN", "", "", key="ven:__empty__")

    def _fill_related_crates(self, crates, headline: str) -> None:
        related = self.query_one("#related-table", DataTable)
        related.clear(columns=True)
        related.add_columns("when", "kind", "agent", "body", "tps", "c_uid")
        self.query_one("#related-hint", Static).update(headline)
        self.query_one("#viewer-title", Label).update("Viewer · related")
        for r in crates:
            when = r["event_unix"] or r["ingest_unix"] or 0
            related.add_row(
                str(when),
                _clip((r["kind"] or "") + "·" + (r["tool"] or ""), 12),
                _clip(r["agent"] or "", 10),
                _clip(r["body"] or "", 36),
                _clip(r["t_uid"] or "", 12),
                r["c_uid"][:16],
                key=r["c_uid"],
            )
        if not crates:
            self.query_one("#viewer", Static).update("No crates for this selection.")
        else:
            self.query_one("#viewer", Static).update(
                f"{len(crates)} crate(s). Select a row above for full payload "
                "(body, tags, TPS, history)."
            )

    def _show_tag_in_viewer(self, term: str) -> None:
        self._focus_term = term
        self._focus_tps = None
        crates = list_crates(self.conn, tag=term, limit=80, heads_only=True)
        self._fill_related_crates(
            crates,
            f"crates tagged [{term}]  ·  select a crate for full view",
        )
        if crates:
            # auto-show first crate body so the viewer isn't empty
            lines = [
                f"TAG  {term}",
                f"{len(crates)} crate(s) via tag_map",
                "",
                "— previews —",
            ]
            for r in crates[:12]:
                lines.append(f"  {r['agent']}: {_clip(r['body'] or '', 70)}")
            lines.append("")
            lines.append("Select a related-crate row for full tags + history.")
            self.query_one("#viewer", Static).update("\n".join(lines))

    def _show_tps_in_viewer(self, tps_uid: str) -> None:
        self._focus_tps = tps_uid
        self._focus_term = None
        rows = list(
            self.conn.execute(
                """
                SELECT c.*
                FROM tps_attach a
                JOIN crates c ON c.c_uid=a.c_uid
                WHERE a.tps_uid=?
                ORDER BY c.event_unix ASC, c.ingest_unix ASC, a.seq ASC
                """,
                (tps_uid,),
            )
        )
        self._fill_related_crates(
            rows,
            f"TPS [{tps_uid}]  ·  event_unix order  ·  select crate for full view",
        )
        if rows:
            lines = [
                f"TPS WINDOW  {tps_uid}",
                f"{len(rows)} crate(s) ordered by event_unix",
                "",
            ]
            for r in rows[:15]:
                lines.append(
                    f"  {r['event_unix']}  {r['kind']}  {r['agent']}: {_clip(r['body'] or '', 50)}"
                )
            self.query_one("#viewer", Static).update("\n".join(lines))

    def _stem_family(self, row) -> list:
        """Revisions sharing stem (oldest → newest)."""
        stem = (_g(row, "stem_c_uid") or "").strip() or _g(row, "c_uid")
        if not stem:
            return []
        try:
            return list(
                self.conn.execute(
                    """
                    SELECT * FROM crates
                    WHERE COALESCE(NULLIF(stem_c_uid, ''), c_uid) = ?
                      AND (deleted_at IS NULL OR deleted_at = 0)
                    ORDER BY ingest_unix ASC, c_uid ASC
                    """,
                    (stem,),
                )
            )
        except Exception:
            return []

    def action_toggle_lineage(self) -> None:
        """Jump viewer to stem head (safe for tagging)."""
        if not self._selected_crate:
            self._status_line("L · select a crate first")
            return
        try:
            head = stem_head_c_uid(self.conn, self._selected_crate)
        except Exception:
            head = self._selected_crate
        self._show_crate(head, prefer_head=True)
        self._status_line(f"head {head[:20]}…")

    def _crate_edges(self, c_uid: str) -> list:
        try:
            return list(
                self.conn.execute(
                    """
                    SELECT from_term, rel, to_term, ingest_unix
                    FROM thread_edges
                    WHERE c_uid = ?
                    ORDER BY id ASC
                    """,
                    (c_uid,),
                )
            )
        except Exception:
            return []

    def _show_crate(self, c_uid: str, *, prefer_head: bool = True) -> None:
        """
        Show crate payload. prefer_head=True (index / tag) jumps to latest rev
        so Tag+ doesn't hit linebreak history. Related-table pick uses prefer_head=False.
        Always lists stem revs in the related pane when there are multiple.
        """
        if prefer_head:
            try:
                head = stem_head_c_uid(self.conn, c_uid)
                if head and head != c_uid:
                    c_uid = head
            except Exception:
                pass

        self._selected_crate = c_uid
        row = get_crate(self.conn, c_uid)
        viewer = self.query_one("#viewer", Static)
        self.query_one("#viewer-title", Label).update("Viewer · crate")
        if not row:
            viewer.update(Text("missing crate", style=_S_WARN))
            return

        family = self._stem_family(row)
        n_revs = len(family)
        related = self.query_one("#related-table", DataTable)
        head_uid = _g(family[-1], "c_uid") if family else c_uid

        if n_revs > 1:
            # Always list revs here (this is what the "N revs" note was for)
            related.clear(columns=True)
            related.add_columns("rev", "when", "kind", "snippet", "c_uid")
            self.query_one("#related-hint", Static).update(
                f"{n_revs} revs · ★ = head (tag this) · pick a row to inspect · Tag+ uses selection"
            )
            for i, r in enumerate(family, start=1):
                when = r["event_unix"] or r["ingest_unix"] or 0
                is_head = _g(r, "c_uid") == head_uid
                is_open = _g(r, "c_uid") == c_uid
                mark = "★" if is_head else str(i)
                if is_open:
                    mark = mark + "·"
                related.add_row(
                    mark,
                    str(when),
                    _clip((r["kind"] or "") + "·" + (r["tool"] or ""), 10),
                    _clip(r["body"] or r["topic"] or "", 40),
                    r["c_uid"][:18],
                    key=r["c_uid"],
                )
        else:
            if self._section == "crates":
                related.clear(columns=True)
                related.add_columns("note")
                self.query_one("#related-hint", Static).update("related · single leaf")
                related.add_row("(no other revs)")

        try:
            tags = json.loads(_g(row, "tags_json") or "[]")
            if not isinstance(tags, list):
                tags = []
        except Exception:
            tags = []
        tags = [str(t) for t in tags]
        user_tags, place_tags, chain_tags = _split_tags(tags)
        tags_raw = _g(row, "tags_raw")
        edges = self._crate_edges(c_uid)

        body = (_g(row, "body") or "").replace("\r\n", "\n").replace("\r", "\n")
        topic = _g(row, "topic")
        scale = _g(row, "scale") or "—"
        face = _g(row, "face_id") or "—"
        parent = _g(row, "parent_c_uid") or "—"
        stem = _g(row, "stem_c_uid") or "—"
        place_label = _g(row, "place_label")
        tz = _g(row, "timezone") or "—"
        tool_ver = _g(row, "tool_version")
        tool = _g(row, "tool")
        tool_s = f"{tool} v{tool_ver}" if tool_ver else tool

        meta_raw = _g(row, "meta_json") or "{}"
        try:
            meta_obj = json.loads(meta_raw) if meta_raw else {}
        except Exception:
            meta_obj = {}
        if not isinstance(meta_obj, dict):
            meta_obj = {}
        folder = str(meta_obj.get("folder") or "")
        rev = meta_obj.get("rev", "")
        event_raw = str(meta_obj.get("event_raw") or "")

        out = Text()
        soft = _g(row, "deleted_at")
        out.append(_g(row, "c_uid"), style=_S_HEAD)
        if soft and soft not in ("0", "None"):
            out.append("  DEVALUED", style=_S_WARN)
            out.append(f" soft @{soft}", style=_S_DIM)
        out.append("\n\n")

        def add(line: Text | str = "") -> None:
            if isinstance(line, Text):
                out.append_text(line)
            else:
                out.append(str(line))
            out.append("\n")

        add(_t_section("identity"))
        add(_t_kv("kind", f"{_g(row, 'kind')}  ·  {tool_s}"))
        add(_t_kv("scale", scale))
        add(_t_kv("face_id", face))
        add(_t_kv("topic", topic or "—"))
        add(_t_kv("agent", _g(row, "agent") or "—"))
        add(_t_kv("mod", _g(row, "mod") or "—"))
        add()
        add(_t_section("place"))
        add(_t_kv("path", _path_of(row) or "—"))
        add(
            _t_kv(
                "sys/dom/rm",
                "/".join(
                    p
                    for p in (_g(row, "sys"), _g(row, "dom"), _g(row, "room"))
                    if p
                )
                or "—",
            )
        )
        add(_t_kv("label", place_label or "—"))
        add()
        add(_t_section("lineage"))
        add(_t_kv("stem", stem))
        add(_t_kv("parent", parent))
        add(_t_kv("folder", folder or "—"))
        add(_t_kv("rev", str(rev) if rev != "" else "—"))
        add()
        add(_t_section("time"))
        add(_t_kv("event", _fmt_ts(_g(row, "event_unix"))))
        add(_t_kv("event_raw", event_raw or "—"))
        add(_t_kv("ingest", _fmt_ts(_g(row, "ingest_unix"))))
        add(_t_kv("created", _fmt_ts(_g(row, "created_at"))))
        add(_t_kv("updated", _fmt_ts(_g(row, "updated_at"))))
        add(_t_kv("tps", _g(row, "t_uid") or "—"))
        add(_t_kv("tz", tz))
        add()
        add(_t_section("charlie"))
        add(
            _t_kv(
                "tags_raw",
                tags_raw or "(none — no user thread language)",
            )
        )

        if edges:
            add(_t_kv("edges", f"{len(edges)} relationship(s)"))
            for e in edges:
                et = Text("           ")
                et.append(str(e["from_term"]), style="bold " + _S_EDGE)
                et.append(f"*{e['rel']}>", style=_S_REL)
                et.append(str(e["to_term"]), style="bold " + _S_EDGE)
                add(et)
        else:
            add(_t_kv("edges", "(none on this crate)"))

        if chain_tags and not edges:
            add(_t_kv("chains", ", ".join(chain_tags)))
        if user_tags:
            add(_t_kv("terms", ", ".join(user_tags)))
        if place_tags:
            add(_t_kv("place tags", ", ".join(place_tags)))
        if not user_tags and not place_tags and not chain_tags and not tags_raw:
            add(_t_kv("terms", "(empty tag set)"))

        add()
        add(_t_section("body"))
        for bline in _render_body_material(body):
            add(bline)

        add()
        add(_t_section("meta"))
        for mline in _pretty_meta(meta_raw).split("\n"):
            add(Text(mline, style=_S_META))

        add()
        add(_t_section("history"))
        evs = list(history(self.conn, c_uid))
        if not evs:
            add(Text("  (no crate_events)", style=_S_DIM))
        for ev in evs:
            payload = _g(ev, "payload_json")
            if len(payload) > 160:
                payload = payload[:157] + "…"
            ht = Text("  ")
            ht.append(f"#{_g(ev, 'id')} ", style=_S_DIM)
            ht.append(_g(ev, "event_type") + " ", style="bold " + _S_VAL)
            ht.append(_fmt_ts(_g(ev, "ingest_unix")), style=_S_DIM)
            add(ht)
            if payload and payload not in ("{}", "null"):
                add(Text(f"    {payload}", style=_S_META))

        add()
        if n_revs > 1:
            is_head = c_uid == head_uid
            add(
                _t_kv(
                    "revs",
                    f"{n_revs} in stem · viewing "
                    + ("HEAD ★" if is_head else f"older {c_uid[:16]}…")
                    + " · L → jump to head · Tag+ uses selection",
                )
            )
        add(
            Text(
                "t Tag  ·  L → head  ·  Tag+  ·  b barbie  ·  del Soft  ·  r refresh",
                style=_S_DIM,
            )
        )

        viewer.update(out)
        self._status_line()
        try:
            self.query_one("#viewer-scroll", VerticalScroll).scroll_home(animate=False)
        except Exception:
            pass

    def _show_ven(self, key: str) -> None:
        """Open one VEN registry entry in the materials viewer."""
        if key in ("ven:__empty__", "__empty__"):
            self.query_one("#viewer", Static).update(
                "Registry empty. On IO import: encode book → → VEN"
            )
            return
        reg = self._ven_reg if self._ven_reg is not None else _load_ven_registry()
        self._ven_reg = reg
        e = _ven_entry_by_key(reg, key)
        related = self.query_one("#related-table", DataTable)
        related.clear(columns=True)
        self.query_one("#related-hint", Static).update("VEN code book (not crates)")
        if not e:
            self.query_one("#viewer-title", Label).update("Viewer · VEN")
            self.query_one("#viewer", Static).update(f"No entry for {key}")
            return
        self._selected_ven = str(e.get("kven") or e.get("id") or key)
        self._selected_crate = None
        self.query_one("#viewer-title", Label).update(
            f"Viewer · VEN · {e.get('kven') or '—'}"
        )
        # related pane: list matches as a quick table
        related.add_columns("field", "value")
        for a in e.get("alts") or []:
            related.add_row("alt", str(a), key=f"venmeta:alt:{a}")
        for m in e.get("matches") or []:
            related.add_row("match", str(m), key=f"venmeta:match:{m}")
        self.query_one("#viewer", Static).update(
            _render_ven_entry(e, reg_path=str(reg.get("path") or _VEN_REGISTRY))
        )
        self._status_line(f"ven={self._selected_ven}")
        try:
            self.query_one("#viewer-scroll", VerticalScroll).scroll_home(animate=False)
        except Exception:
            pass

    def _apply_index_key(self, key: str, *, open_viewer: bool) -> None:
        """Shared path for highlight (select only) vs select (open materials)."""
        if key.startswith("term:"):
            if open_viewer:
                self._show_tag_in_viewer(key[5:])
            return
        if key.startswith("edge:"):
            parts = key.split(":", 2)
            if len(parts) >= 3:
                self._note_selected_from_key(parts[2])
                if open_viewer:
                    self._show_crate(parts[2])
            return
        if key.startswith("tps:"):
            if open_viewer:
                self._show_tps_in_viewer(key[4:])
            return
        if key.startswith("ven:"):
            self._selected_ven = key[4:]
            if open_viewer:
                self._show_ven(key)
            else:
                self._status_line(f"ven={self._selected_ven}")
            return
        if key.startswith("venmeta:"):
            return
        # bare c_uid / crate.*
        if key.startswith("crate.") or get_crate(self.conn, key):
            self._note_selected_from_key(key)
            if not open_viewer:
                self._status_line()
                return
            related = self.query_one("#related-table", DataTable)
            related.clear(columns=True)
            related.add_columns("c_uid", "kind", "place")
            row = get_crate(self.conn, key)
            if row:
                related.add_row(
                    row["c_uid"][:18],
                    row["kind"] or "",
                    _clip(_path_of(row), 20),
                    key=row["c_uid"],
                )
                self.query_one("#related-hint", Static).update("current crate")
            self._show_crate(key)

    @on(DataTable.RowHighlighted)
    def row_highlighted(self, event: DataTable.RowHighlighted) -> None:
        """Arrow/cursor moves set the devalue target without needing Enter."""
        if not event.row_key:
            return
        key = str(event.row_key.value)
        table_id = event.data_table.id if event.data_table else ""
        if table_id == "related-table":
            self._note_selected_from_key(key)
            self._status_line()
            return
        if table_id == "index-table":
            self._apply_index_key(key, open_viewer=False)

    @on(DataTable.RowSelected)
    def row_selected(self, event: DataTable.RowSelected) -> None:
        if not event.row_key:
            return
        key = str(event.row_key.value)
        table_id = event.data_table.id if event.data_table else ""

        # Right pane related table → that exact rev (do not snap to head)
        if table_id == "related-table":
            if get_crate(self.conn, key):
                self._show_crate(key, prefer_head=False)
            return

        self._apply_index_key(key, open_viewer=True)

    @on(Button.Pressed, "#sec-crates")
    def b_crates(self) -> None:
        self.action_sec_crates()

    @on(Button.Pressed, "#sec-charlie")
    def b_charlie(self) -> None:
        self.action_sec_charlie()

    @on(Button.Pressed, "#sec-edges")
    def b_edges(self) -> None:
        self.action_sec_edges()

    @on(Button.Pressed, "#sec-tps")
    def b_tps(self) -> None:
        self.action_sec_tps()

    @on(Button.Pressed, "#sec-ven")
    def b_ven(self) -> None:
        self.action_sec_ven()

    @on(Button.Pressed, "#btn-refresh")
    def refresh_btn(self) -> None:
        self.action_refresh()

    @on(Button.Pressed, "#btn-demo")
    def demo(self) -> None:
        create_crate(
            self.conn,
            topic="demo trust post",
            body="Split pane: index left, materials right.",
            sys="starline",
            dom="chester",
            room="crates",
            mod="hands",
            tags_raw="demo; care*to>store",
            actor="mypi-tui",
            tool="mypi-tui",
        )
        self.action_sec_crates()

    @on(Button.Pressed, "#btn-del")
    def del_soft(self) -> None:
        """Devalue: soft-delete (hidden from lists, history + snapshot kept)."""
        c_uid = self._resolve_target_crate()
        if not c_uid:
            self.query_one("#status", Static).update(
                "  Soft-del: highlight a crate row (index or related) first"
            )
            return
        try:
            soft_delete(self.conn, c_uid, actor="mypi-tui")
        except KeyError:
            self.query_one("#status", Static).update(f"  Soft-del: missing {c_uid[:24]}")
            return
        note = f"devalued (soft) {c_uid[:24]}"
        self._selected_crate = None
        self._load_index()
        self._clear_viewer(f"Soft-deleted {c_uid}\n(devalued — gone from index, in deleted_log)")
        self._status_line(note)

    @on(Button.Pressed, "#btn-hard")
    def del_hard(self) -> None:
        """Hard remove one crate only (never the world)."""
        c_uid = self._resolve_target_crate()
        if not c_uid:
            self.query_one("#status", Static).update(
                "  NUKE: highlight a crate row (index or related) first"
            )
            return
        try:
            hard_delete(self.conn, c_uid, actor="mypi-tui")
        except KeyError:
            self.query_one("#status", Static).update(f"  NUKE: missing {c_uid[:24]}")
            return
        note = f"NUKED {c_uid[:24]}"
        self._selected_crate = None
        self._load_index()
        self._clear_viewer(f"Hard-deleted {c_uid}\n(snapshot in deleted_log)")
        self._status_line(note)

    def _apply_tag_input(self, *, replace: bool) -> None:
        """Backend Charlie: append/replace on **stem head** (never old linebreak revs)."""
        c_uid = self._resolve_target_crate()
        if not c_uid:
            self.query_one("#status", Static).update(
                "  Tag: select a crate first (index or related)"
            )
            return
        try:
            head = stem_head_c_uid(self.conn, c_uid)
            if head:
                c_uid = head
        except Exception:
            pass
        try:
            frag = self.query_one("#tag-input", Input).value
        except Exception:
            frag = ""
        frag = (frag or "").strip()
        if not frag:
            self.query_one("#status", Static).update(
                "  Tag: type a term or Charlie clause (aubel · lore · aubel*knows>iox)"
            )
            return
        try:
            if replace:
                set_crate_charlie(
                    self.conn, c_uid, frag, actor="mypi-tui", tool="mypi-tui"
                )
                note = f"charlie SET on HEAD {c_uid[:18]}…  {frag[:40]}"
            else:
                append_charlie(
                    self.conn, c_uid, frag, actor="mypi-tui", tool="mypi-tui"
                )
                note = f"charlie + on HEAD {c_uid[:18]}…  {frag[:40]}"
        except Exception as e:
            self.query_one("#status", Static).update(f"  Tag failed: {e}")
            return
        try:
            self.query_one("#tag-input", Input).value = ""
        except Exception:
            pass
        self._show_crate(c_uid, prefer_head=True)
        if self._section == "charlie":
            self._load_index()
        self._status_line(note)

    @on(Button.Pressed, "#btn-tag")
    def tag_append(self) -> None:
        self._apply_tag_input(replace=False)

    @on(Button.Pressed, "#btn-tag-set")
    def tag_set(self) -> None:
        self._apply_tag_input(replace=True)

    @on(Input.Submitted, "#tag-input")
    def tag_submit(self) -> None:
        """Enter in tag field = append Charlie."""
        self._apply_tag_input(replace=False)

    @on(Button.Pressed, "#btn-theme")
    def theme_btn(self) -> None:
        self.action_toggle_theme()


def main() -> None:
    print(f"ledger db: {DEFAULT_DB}")
    print("Layout: top nav · left index · right viewer · Charlie tag bar at BOTTOM")
    print("Charlie: select crate → tag bar → Tag+ (append) or Set raw (replace) · t focuses")
    print("  plain: aubel,lore  ·  edges: aubel*knows>iox")
    print("Theme: Barbie ♡ / Forest  ·  key b")
    print("Heads only in crate list (latest rev) · L toggles full lineage")
    print("Delete: Soft-del / Del / Backspace  ·  Shift+Del or NUKE")
    MypiTui().run()


if __name__ == "__main__":
    main()
