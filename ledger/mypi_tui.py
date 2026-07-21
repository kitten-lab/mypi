"""
mypi-tui — first-class sections: Crates | Charlie | TPS
(authority delete lives here, not on Surfaces)

  cd C:\\Builds\\my-pocket-internet\\ledger
  python mypi_tui.py
"""

from __future__ import annotations

import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))

from mypi_ledger import (  # noqa: E402
    DEFAULT_DB,
    add_tag,
    charlie_edges,
    charlie_gravity,
    connect,
    create_crate,
    get_crate,
    hard_delete,
    history,
    init_db,
    list_crates,
    list_tps_shelves,
    set_body,
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


class MypiTui(App[None]):
    TITLE = "mypi ledger"
    CSS = """
    Screen { background: #0c1210; color: #b8e0c8; }
    #sidebar { width: 30; border-right: tall #2a4a38; padding: 1; }
    #main { padding: 1 2; }
    #detail { height: 1fr; border: tall #2a4a38; padding: 1; }
    Button { margin: 0 0 1 0; width: 100%; }
    DataTable { height: 16; }
    .muted { color: #5a8a6a; }
    #status { color: #7ab890; margin-top: 1; }
    .section-on { text-style: bold; background: #143314; }
    """
    BINDINGS = [
        Binding("q", "quit", "Quit"),
        Binding("r", "refresh", "Refresh"),
        Binding("1", "sec_crates", "Crates"),
        Binding("2", "sec_charlie", "Charlie"),
        Binding("3", "sec_tps", "TPS"),
    ]

    def __init__(self) -> None:
        super().__init__()
        self.conn = connect()
        init_db(self.conn)
        self._section = "crates"  # crates | charlie | tps
        self._selected: str | None = None

    def compose(self) -> ComposeResult:
        yield Header()
        with Horizontal():
            with Vertical(id="sidebar"):
                yield Label("mypi ledger")
                yield Static("d/_CHESTER · _CHARLIE · _SATORA", classes="muted")
                yield Button("1 · CRATES", id="sec-crates")
                yield Button("2 · CHARLIE", id="sec-charlie")
                yield Button("Charlie: Terms", id="ch-terms")
                yield Button("Charlie: Edges", id="ch-edges")
                yield Button("3 · TPS", id="sec-tps")
                yield Static("— authority —", classes="muted")
                yield Button("Refresh", id="btn-refresh")
                yield Button("Demo crate", id="btn-demo")
                yield Button("Soft-delete crate", id="btn-del")
                yield Button("NUKE crate", id="btn-hard")
                yield Static("", id="status")
            with Vertical(id="main"):
                yield Label("", id="section-title")
                yield DataTable(id="table")
                yield Label("Detail")
                yield Static("", id="detail")
        yield Footer()

    def on_mount(self) -> None:
        table = self.query_one("#table", DataTable)
        table.cursor_type = "row"
        self._load_section()

    def action_refresh(self) -> None:
        self._load_section()

    def action_sec_crates(self) -> None:
        self._section = "crates"
        self._load_section()

    def action_sec_charlie(self) -> None:
        self._section = "charlie"
        self._load_section()

    def action_sec_tps(self) -> None:
        self._section = "tps"
        self._load_section()

    def _load_section(self) -> None:
        table = self.query_one("#table", DataTable)
        title = self.query_one("#section-title", Label)
        detail = self.query_one("#detail", Static)
        table.clear(columns=True)
        self._selected = None
        st = stats(self.conn)
        try:
            w = tps_window_seconds(self.conn)
        except Exception:
            w = 900
        self.query_one("#status", Static).update(
            f"crates={st['crates']}  TPS={w}s  v{st['schema_version']}\n{DEFAULT_DB}"
        )

        if self._section == "crates":
            title.update("CHESTER · Crates")
            table.add_columns("c_uid", "sys/dom/room", "topic", "tps")
            for r in list_crates(self.conn, limit=100):
                path = f"{r['sys']}/{r['dom']}/{r['room']}" if "sys" in r.keys() else (r["place_path"] or "")
                table.add_row(
                    r["c_uid"],
                    path,
                    (r["topic"] or "")[:36],
                    (r["t_uid"] or "")[:18],
                    key=r["c_uid"],
                )
            detail.update("Select a crate. Delete only here (authority).")
        elif self._section == "charlie":
            # Sub-mode: terms (A–Z) vs edges list — toggle with detail hint
            if not hasattr(self, "_charlie_tab"):
                self._charlie_tab = "terms"
            if self._charlie_tab == "edges":
                title.update("CHARLIE · Edges (tab: terms via refresh cycles — use Edges button)")
                table.add_columns("from", "rel", "to", "c_uid", "when")
                for e in charlie_edges(self.conn, 80):
                    table.add_row(
                        e["from_term"],
                        e["rel"],
                        e["to_term"],
                        e["c_uid"][:20],
                        str(e["ingest_unix"]),
                        key=f"edge:{e['id']}",
                    )
                detail.update(
                    "Edges list. Press CHARLIE again or Terms mode: "
                    "click section Charlie toggles — use sidebar Terms/Edges."
                )
            else:
                title.update("CHARLIE · Terms (single only, A–Z)")
                table.add_columns("term", "gravity", "updated")
                rows = list(
                    self.conn.execute(
                        "SELECT term, gravity, updated_at FROM thread_terms "
                        "ORDER BY term ASC LIMIT 200"
                    )
                )
                for g in rows:
                    term = g[0] or ""
                    # hide full this*rel>that chains from terms (see Edges)
                    if "*" in term or ">" in term:
                        continue
                    table.add_row(
                        term,
                        str(g[1]),
                        str(g[2]),
                        key=f"term:{term}",
                    )
                detail.update(
                    "Single terms only (no *chains*). Edges button for relationships.\n"
                    "Gravity = use weight for later reports."
                )
        else:
            title.update("SATORA · TPS windows")
            table.add_columns("tps_uid", "window_unix", "width", "crates")
            for s in list_tps_shelves(self.conn, 80):
                table.add_row(
                    s["tps_uid"],
                    str(s["window_unix"]),
                    str(s["window_seconds"]),
                    str(s["n_crates"]),
                    key=s["tps_uid"],
                )
            detail.update(
                "Select a shelf to list crates ordered by event_unix inside the window."
            )

    def _show_crate(self, c_uid: str) -> None:
        self._selected = c_uid
        row = get_crate(self.conn, c_uid)
        detail = self.query_one("#detail", Static)
        if not row:
            detail.update("missing")
            return
        tags = json.loads(row["tags_json"] or "[]")
        lines = [
            f"c_uid:  {row['c_uid']}",
            f"sys/dom/room/mod: {row['sys']}/{row['dom']}/{row['room']}/{row['mod']}",
            f"topic:  {row['topic']}",
            f"body:   {(row['body'] or '')[:400]}",
            f"tags:   {', '.join(tags)}",
            f"tps:    {row['t_uid']}",
            f"event:  {row['event_unix']}  ingest: {row['ingest_unix']}",
            "",
            "— history —",
        ]
        for ev in history(self.conn, c_uid):
            payload = ev["payload_json"]
            if len(payload) > 100:
                payload = payload[:97] + "..."
            lines.append(f"  #{ev['id']} {ev['event_type']} @{ev['ingest_unix']} {payload}")
        detail.update("\n".join(lines))

    @on(DataTable.RowSelected)
    def row_selected(self, event: DataTable.RowSelected) -> None:
        if not event.row_key:
            return
        key = str(event.row_key.value)
        if self._section == "crates" and key.startswith("crate."):
            self._show_crate(key)
        elif self._section == "tps":
            rows = list(
                self.conn.execute(
                    """
                    SELECT c.c_uid, c.event_unix, c.ingest_unix, c.topic
                    FROM tps_attach a
                    JOIN crates c ON c.c_uid=a.c_uid
                    WHERE a.tps_uid=?
                    ORDER BY c.event_unix ASC, c.ingest_unix ASC, a.seq ASC
                    """,
                    (key,),
                )
            )
            lines = [
                f"TPS {key}",
                "ordered by event_unix inside window",
                "",
                "event_unix     ingest       c_uid / topic",
            ]
            for r in rows:
                lines.append(
                    f"  {r[1]:<12}  {r[2]:<12}  {r[0]}  {r[3] or ''}"
                )
            self.query_one("#detail", Static).update("\n".join(lines) or "empty shelf")
            self._selected = None

    @on(Button.Pressed, "#sec-crates")
    def b_crates(self) -> None:
        self.action_sec_crates()

    @on(Button.Pressed, "#sec-charlie")
    def b_charlie(self) -> None:
        self._charlie_tab = "terms"
        self.action_sec_charlie()

    @on(Button.Pressed, "#ch-terms")
    def b_ch_terms(self) -> None:
        self._section = "charlie"
        self._charlie_tab = "terms"
        self._load_section()

    @on(Button.Pressed, "#ch-edges")
    def b_ch_edges(self) -> None:
        self._section = "charlie"
        self._charlie_tab = "edges"
        self._load_section()

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
            body="First-class reports: Crates / Charlie / TPS.",
            sys="starline",
            dom="chester",
            room="crates",
            mod="hands",
            tags_raw="demo; care*to>store",
            actor="mypi-tui",
            tool="mypi-tui",
        )
        self._section = "crates"
        self.action_refresh()

    @on(Button.Pressed, "#btn-del")
    def del_soft(self) -> None:
        if not self._selected:
            self.query_one("#status", Static).update("select a crate in CRATES section")
            return
        soft_delete(self.conn, self._selected, actor="mypi-tui")
        self._selected = None
        self.action_refresh()

    @on(Button.Pressed, "#btn-hard")
    def del_hard(self) -> None:
        if not self._selected:
            self.query_one("#status", Static).update("select a crate in CRATES section")
            return
        hard_delete(self.conn, self._selected, actor="mypi-tui")
        self._selected = None
        self.action_refresh()


def main() -> None:
    print(f"ledger db: {DEFAULT_DB}")
    MypiTui().run()


if __name__ == "__main__":
    main()
