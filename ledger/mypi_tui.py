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
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))

from mypi_ledger import (  # noqa: E402
    DEFAULT_DB,
    charlie_edges,
    connect,
    create_crate,
    get_crate,
    hard_delete,
    history,
    init_db,
    list_crates,
    list_tps_shelves,
    soft_delete,
    stats,
    tps_window_seconds,
)

try:
    from textual import on
    from textual.app import App, ComposeResult
    from textual.binding import Binding
    from textual.containers import Horizontal, Vertical
    from textual.widgets import Button, DataTable, Footer, Header, Label, Static
except ImportError:
    print("Need textual: pip install textual")
    sys.exit(1)


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


class MypiTui(App[None]):
    TITLE = "mypi ledger"
    CSS = """
    Screen { background: #0c1210; color: #b8e0c8; }
    #topnav {
        height: auto;
        dock: top;
        padding: 0 1;
        background: #0a100e;
        border-bottom: tall #2a4a38;
    }
    #topnav Horizontal { height: auto; }
    #topnav Button {
        margin: 0 1 0 0;
        min-width: 12;
        width: auto;
        border: none;
    }
    #topnav .nav-on { background: #143314; text-style: bold; }
    #body { height: 1fr; }
    #index-pane {
        width: 42%;
        min-width: 28;
        border-right: tall #2a4a38;
        padding: 0 1;
    }
    #viewer-pane {
        width: 1fr;
        padding: 0 1;
    }
    #index-title, #viewer-title { text-style: bold; color: #7ab890; margin: 1 0 0 0; }
    #index-table { height: 1fr; }
    #related-table { height: 12; border: tall #2a4a38; margin-bottom: 1; }
    #viewer {
        height: 1fr;
        border: tall #2a4a38;
        padding: 1;
        background: #0a100e;
    }
    .muted { color: #5a8a6a; }
    #status { color: #7ab890; dock: bottom; height: 2; padding: 0 1; }
    """
    BINDINGS = [
        Binding("q", "quit", "Quit"),
        Binding("r", "refresh", "Refresh"),
        Binding("1", "sec_crates", "Crates"),
        Binding("2", "sec_charlie", "Charlie"),
        Binding("3", "sec_edges", "Edges"),
        Binding("4", "sec_tps", "TPS"),
        Binding("d", "demo", "Demo"),
        # Soft-del = devalue (deleted_at); NUKE = hard remove one crate
        Binding("delete", "soft_del", "Soft-del"),
        Binding("backspace", "soft_del", "Soft-del", show=False),
        Binding("shift+delete", "hard_del", "NUKE"),
    ]

    def __init__(self) -> None:
        super().__init__()
        self.conn = connect()
        init_db(self.conn)
        # crates | charlie | edges | tps
        self._section = "crates"
        self._focus_term: str | None = None
        self._focus_tps: str | None = None
        self._selected_crate: str | None = None

    def compose(self) -> ComposeResult:
        yield Header(show_clock=True)
        with Horizontal(id="topnav"):
            yield Button("1 Crates", id="sec-crates")
            yield Button("2 Charlie", id="sec-charlie")
            yield Button("3 Edges", id="sec-edges")
            yield Button("4 TPS", id="sec-tps")
            yield Static(" · ", classes="muted")
            yield Button("Refresh", id="btn-refresh")
            yield Button("Demo", id="btn-demo")
            yield Button("Soft-del", id="btn-del")
            yield Button("NUKE", id="btn-hard")
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
                yield Static("Select an item on the left.", id="viewer")
        yield Static("", id="status")
        yield Footer()

    def on_mount(self) -> None:
        for tid in ("index-table", "related-table"):
            t = self.query_one(f"#{tid}", DataTable)
            t.cursor_type = "row"
        self._load_index()
        self._set_nav_highlight()

    def _set_nav_highlight(self) -> None:
        mapping = {
            "crates": "sec-crates",
            "charlie": "sec-charlie",
            "edges": "sec-edges",
            "tps": "sec-tps",
        }
        for sec, bid in mapping.items():
            btn = self.query_one(f"#{bid}", Button)
            if sec == self._section:
                btn.add_class("nav-on")
            else:
                btn.remove_class("nav-on")

    def action_refresh(self) -> None:
        self._load_index()
        # re-show focus if any
        if self._focus_term and self._section == "charlie":
            self._show_tag_in_viewer(self._focus_term)
        elif self._focus_tps and self._section == "tps":
            self._show_tps_in_viewer(self._focus_tps)
        elif self._selected_crate:
            self._show_crate(self._selected_crate)

    def action_sec_crates(self) -> None:
        self._section = "crates"
        self._focus_term = None
        self._focus_tps = None
        self._load_index()
        self._clear_viewer("Select a crate on the left.")

    def action_sec_charlie(self) -> None:
        self._section = "charlie"
        self._focus_tps = None
        self._load_index()
        self._clear_viewer("Select a term → related crates appear above; pick one for full body.")

    def action_sec_edges(self) -> None:
        self._section = "edges"
        self._focus_term = None
        self._focus_tps = None
        self._load_index()
        self._clear_viewer("Select an edge → crate opens in the viewer.")

    def action_sec_tps(self) -> None:
        self._section = "tps"
        self._focus_term = None
        self._load_index()
        self._clear_viewer("Select a TPS window → crates in that window; pick one for full body.")

    def action_demo(self) -> None:
        self.demo()

    def action_soft_del(self) -> None:
        self.del_soft()

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
        base = f"  crates={st['crates']}  TPS={w}s  v{st['schema_version']}  ·  {DEFAULT_DB}"
        if note:
            base = f"{base}  ·  {note}"
        if self._selected_crate:
            base = f"{base}  ·  sel={self._selected_crate[:18]}"
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
            hint.update("all crates — select → full viewer")
            table.add_columns("c_uid", "kind", "place", "agent", "body")
            for r in list_crates(self.conn, limit=120):
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

        else:  # tps
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
        crates = list_crates(self.conn, tag=term, limit=80)
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

    def _show_crate(self, c_uid: str) -> None:
        self._selected_crate = c_uid
        row = get_crate(self.conn, c_uid)
        viewer = self.query_one("#viewer", Static)
        self.query_one("#viewer-title", Label).update(f"Viewer · crate")
        if not row:
            viewer.update("missing crate")
            return
        tags = json.loads(row["tags_json"] or "[]")
        meta = row["meta_json"] or "{}"
        if len(meta) > 400:
            meta = meta[:397] + "..."
        body = row["body"] or ""
        soft = row["deleted_at"] if "deleted_at" in row.keys() else None
        lines = [
            f"c_uid   {row['c_uid']}",
            f"kind    {row['kind']}    tool  {row['tool']} v{row['tool_version']}",
            f"place   {row['sys']}/{row['dom']}/{row['room']}   mod={row['mod']}",
            f"agent   {row['agent']}",
            f"topic   {row['topic']}",
            "",
            "— body —",
            body,
            "",
            f"tags    {', '.join(tags)}",
            f"tps     {row['t_uid']}",
            f"event   {row['event_unix']}    ingest {row['ingest_unix']}",
            f"meta    {meta}",
        ]
        if soft:
            lines.append(f"DELETED soft @{soft}  (hidden from index; history kept)")
        lines.append("")
        lines.append("— history —")
        for ev in history(self.conn, c_uid):
            payload = ev["payload_json"]
            if len(payload) > 140:
                payload = payload[:137] + "..."
            lines.append(f"  #{ev['id']} {ev['event_type']} @{ev['ingest_unix']} {payload}")
        # clickable tag hints
        if tags:
            lines.append("")
            lines.append("— tags (open Charlie index + select term to list) —")
            lines.append("  " + "  ".join(tags[:24]))
        lines.append("")
        lines.append("— del  Soft-del / Backspace  ·  shift+del NUKE  ·  topnav buttons —")
        viewer.update("\n".join(lines))
        self._status_line()

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

        # Right pane related table → full crate
        if table_id == "related-table":
            if get_crate(self.conn, key):
                self._show_crate(key)
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


def main() -> None:
    print(f"ledger db: {DEFAULT_DB}")
    print("Layout: top nav · left index · right viewer (related + full payload)")
    print("Delete: highlight crate → Soft-del / Del / Backspace  ·  Shift+Del or NUKE")
    MypiTui().run()


if __name__ == "__main__":
    main()
