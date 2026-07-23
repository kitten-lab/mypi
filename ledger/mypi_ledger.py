"""
Chester's Imports ledger — single SQLite store (CHESTER_UID rows + events).
c_uid column = CHESTER_UID. scale + parent = composition.
DB default: d/_LEDGER/chesters_imports.sqlite
Plan: mypi docs/CRATE-DUAL-RAIL-AND-IMPORT-WORK.md
"""

from __future__ import annotations

import json
import secrets
import sqlite3
import time
from pathlib import Path
from typing import Any

SCHEMA_VERSION = "3"
DEFAULT_TPS_WINDOW = 900  # 15-minute membrane windows
_LEDGER_DIR = Path(__file__).resolve().parent.parent / "d" / "_LEDGER"
DEFAULT_DB = _LEDGER_DIR / "chesters_imports.sqlite"
LEGACY_DB = _LEDGER_DIR / "mypi.sqlite"
SCHEMA_SQL = Path(__file__).resolve().parent / "schema.sql"

# kind → default scale (leaf | branch | log | yard_crate)
_KIND_SCALE = {
    "post": "leaf",
    "chat": "leaf",
    "guestcu": "leaf",
    "soper": "leaf",
    "file": "leaf",
    "folder": "log",
    "material": "log",
    "log_material": "log",
    "timber": "leaf",
    "thought_bit": "leaf",
    "fragment": "leaf",
    "session": "log",
    "arc": "yard_crate",
    "shipment": "yard_crate",
    "yard_crate": "yard_crate",
}


def default_scale(kind: str) -> str:
    return _KIND_SCALE.get((kind or "").strip().lower(), "leaf")


def new_c_uid() -> str:
    """Mint a CHESTER_UID (stored in c_uid)."""
    return "ch." + secrets.token_hex(8).upper()


def resolve_default_db() -> Path:
    """Always use chesters_imports.sqlite (fresh house file). Legacy mypi.sqlite is not auto-opened."""
    return DEFAULT_DB


def connect(db_path: Path | None = None) -> sqlite3.Connection:
    path = Path(db_path) if db_path else resolve_default_db()
    path.parent.mkdir(parents=True, exist_ok=True)
    conn = sqlite3.connect(str(path))
    conn.row_factory = sqlite3.Row
    conn.execute("PRAGMA foreign_keys = ON")
    return conn


def init_db(conn: sqlite3.Connection) -> None:
    sql = SCHEMA_SQL.read_text(encoding="utf-8")
    conn.executescript(sql)
    _ensure_sys_cols(conn)
    _ensure_deleted_col(conn)
    _ensure_composition_cols(conn)
    conn.execute(
        "INSERT INTO ledger_meta(key, value) VALUES(?, ?) "
        "ON CONFLICT(key) DO UPDATE SET value=excluded.value",
        ("schema_version", SCHEMA_VERSION),
    )
    conn.execute(
        "INSERT INTO ledger_meta(key, value) VALUES(?, ?) "
        "ON CONFLICT(key) DO UPDATE SET value=excluded.value",
        ("ledger_name", "Chester's Imports"),
    )
    conn.execute(
        "INSERT INTO ledger_meta(key, value) VALUES(?, ?) "
        "ON CONFLICT(key) DO NOTHING",
        ("tps_window_seconds", str(DEFAULT_TPS_WINDOW)),
    )
    conn.commit()


def tps_window_seconds(conn: sqlite3.Connection) -> int:
    init_db(conn)
    row = conn.execute(
        "SELECT value FROM ledger_meta WHERE key='tps_window_seconds'"
    ).fetchone()
    w = int(row["value"]) if row else DEFAULT_TPS_WINDOW
    return w if w > 0 else DEFAULT_TPS_WINDOW


def list_tps_shelves(conn: sqlite3.Connection, limit: int = 40) -> list[sqlite3.Row]:
    init_db(conn)
    return list(
        conn.execute(
            """
            SELECT s.*,
              (SELECT COUNT(*) FROM tps_attach a WHERE a.tps_uid=s.tps_uid) AS n_crates
            FROM tps_shelves s
            ORDER BY s.window_unix DESC
            LIMIT ?
            """,
            (limit,),
        )
    )


def charlie_gravity(conn: sqlite3.Connection, limit: int = 30) -> list[sqlite3.Row]:
    init_db(conn)
    return list(
        conn.execute(
            "SELECT term, gravity, updated_at FROM thread_terms "
            "ORDER BY gravity DESC, term ASC LIMIT ?",
            (limit,),
        )
    )


def charlie_edges(conn: sqlite3.Connection, limit: int = 40) -> list[sqlite3.Row]:
    """Live Charlie only — edges for missing / soft-deleted crates are hidden."""
    init_db(conn)
    return list(
        conn.execute(
            """
            SELECT e.* FROM thread_edges e
            INNER JOIN crates c ON c.c_uid = e.c_uid
            WHERE c.deleted_at IS NULL OR c.deleted_at = 0
            ORDER BY e.id DESC LIMIT ?
            """,
            (limit,),
        )
    )


def charlie_detach(conn: sqlite3.Connection, c_uid: str) -> None:
    """Remove relationship edges for one crate (soft/hard delete path)."""
    conn.execute("DELETE FROM thread_edges WHERE c_uid=?", (c_uid,))


def charlie_scrub_orphans(conn: sqlite3.Connection) -> int:
    """
    Delete edges whose crate is gone or devalued.
    Returns number of rows removed. Safe anytime.
    """
    init_db(conn)
    cur = conn.execute(
        """
        DELETE FROM thread_edges WHERE c_uid NOT IN (
          SELECT c_uid FROM crates WHERE deleted_at IS NULL OR deleted_at = 0
        )
        """
    )
    conn.commit()
    return int(cur.rowcount or 0)


def _now() -> int:
    return int(time.time())


def _ensure_sys_cols(conn: sqlite3.Connection) -> None:
    cols = {r[1] for r in conn.execute("PRAGMA table_info(crates)")}
    for col in ("sys", "dom", "room", "mod"):
        if col not in cols:
            conn.execute(
                f"ALTER TABLE crates ADD COLUMN {col} TEXT NOT NULL DEFAULT ''"
            )
    conn.commit()


def _ensure_composition_cols(conn: sqlite3.Connection) -> None:
    """v3: scale, face_id, parent/stem, spans, glass/yard titles."""
    cols = {r[1] for r in conn.execute("PRAGMA table_info(crates)")}
    alters = [
        ("scale", "TEXT NOT NULL DEFAULT 'leaf'"),
        ("face_id", "TEXT NOT NULL DEFAULT ''"),
        ("parent_c_uid", "TEXT NOT NULL DEFAULT ''"),
        ("stem_c_uid", "TEXT NOT NULL DEFAULT ''"),
        ("glass_title", "TEXT NOT NULL DEFAULT ''"),
        ("yard_title", "TEXT NOT NULL DEFAULT ''"),
        ("span_start", "INTEGER"),
        ("span_end", "INTEGER"),
    ]
    for col, decl in alters:
        if col not in cols:
            conn.execute(f"ALTER TABLE crates ADD COLUMN {col} {decl}")
    conn.execute("CREATE INDEX IF NOT EXISTS idx_crates_scale ON crates(scale)")
    conn.execute("CREATE INDEX IF NOT EXISTS idx_crates_parent ON crates(parent_c_uid)")
    conn.execute("CREATE INDEX IF NOT EXISTS idx_crates_stem ON crates(stem_c_uid)")
    conn.execute("CREATE INDEX IF NOT EXISTS idx_crates_face ON crates(face_id)")
    conn.commit()


def _parse_tags(
    tags_raw: str,
    place_path: str,
    sys: str = "",
    dom: str = "",
    room: str = "",
    mod: str = "",
) -> list[str]:
    tags: list[str] = []
    raw = (tags_raw or "").replace("\n", " ").strip()
    if raw:
        for part in raw.replace(",", " ").split():
            t = part.strip().lstrip("#")
            if t and t not in tags:
                tags.append(t)
    path = (place_path or "").strip() or "/".join(
        x for x in (sys, dom, room) if x
    )
    if path:
        ptag = f"path:{path}"
        if ptag not in tags:
            tags.append(ptag)
        for seg in path.strip("/").split("/"):
            if not seg:
                continue
            at = f"@{seg}"
            if at not in tags:
                tags.append(at)
    if sys:
        t = f"sys:{sys}"
        if t not in tags:
            tags.append(t)
    if dom:
        t = f"dom:{dom}"
        if t not in tags:
            tags.append(t)
    if mod:
        t = f"mod:{mod}"
        if t not in tags:
            tags.append(t)
    return tags


def parse_charlie(tags_raw: str) -> dict[str, Any]:
    """
    Parse tags_raw into plain tags + relationship edges.

    Multi-stage Charlie / tagSplicer (not flat this*rel>blob):
      1. `;` / newlines  → independent clauses
      2. `from*rest`     → subject + right-hand material
      3. `&`             → multi rel-segments under same from
      4. `rel>thats`     → relationship + destination(s)
      5. `,`             → multi-that: one edge per that

    Examples:
      understanding*you>system,structure,format
        → understanding*you>system, …>structure, …>format
      this*related>that&holds>other
        → this*related>that + this*holds>other
    """
    import re

    tags: list[str] = []
    edges: list[dict[str, str]] = []
    raw = (tags_raw or "").strip()
    if not raw:
        return {"tags": tags, "edges": edges}

    def add_tag(t: str) -> None:
        t = (t or "").strip().lower().lstrip("#")
        if t and t not in tags:
            tags.append(t)

    def add_edge(frm: str, rel: str, to: str) -> None:
        frm = (frm or "").strip().lower().lstrip("#")
        rel = (rel or "").strip().lower().lstrip("#")
        to = (to or "").strip().lower().lstrip("#")
        if not frm or not to:
            return
        edges.append({"from": frm, "rel": rel, "to": to})
        for t in (frm, rel, to, f"{frm}*{rel}>{to}"):
            add_tag(t)

    for clause in re.split(r"[;\n]+", raw):
        clause = clause.strip().lower()
        if not clause:
            continue

        # Stage 2: from*rest  (no * → plain tags)
        if "*" not in clause:
            for c in re.split(r"[\s,]+", clause):
                add_tag(c)
            continue

        star = clause.split("*", 1)
        frm = star[0].strip()
        rest = star[1].strip() if len(star) > 1 else ""
        if not frm or not rest:
            if frm:
                add_tag(frm)
            continue

        # Stage 3: & multi rel-segments under same from
        segments = rest.split("&") if "&" in rest else [rest]

        for segment in segments:
            segment = segment.strip()
            if not segment:
                continue

            # Stage 4: rel>thats  (no > → typed bare term under from)
            if ">" not in segment:
                add_tag(frm)
                add_tag(segment)
                add_tag(f"{frm}*{segment}")
                continue

            gt = segment.split(">", 1)
            rel = gt[0].strip()
            child_raw = gt[1].strip() if len(gt) > 1 else ""
            if not rel or not child_raw:
                if rel:
                    add_tag(frm)
                    add_tag(rel)
                continue

            # Stage 5: , multi-that → one edge per that
            thats = [x.strip() for x in child_raw.split(",")] if "," in child_raw else [child_raw]
            for to in thats:
                to = to.strip()
                if to:
                    add_edge(frm, rel, to)

    return {"tags": tags, "edges": edges}


def charlie_write(
    conn: sqlite3.Connection,
    *,
    c_uid: str,
    tags_raw: str,
    all_tags: list[str] | None = None,
    sys: str = "",
    dom: str = "",
    room: str = "",
    mod: str = "",
    ingest_unix: int | None = None,
) -> list[dict[str, str]]:
    """
    Bump Charlie gravity for production tags + write relationship edges.

    Mirrors PHP mypi_ledger_charlie_write: every tag (including path:/@seg)
    is real Charlie material.
    """
    init_db(conn)
    ingest = int(ingest_unix if ingest_unix is not None else _now())
    parsed = parse_charlie(tags_raw)
    already: set[str] = set()

    for e in parsed["edges"]:
        conn.execute(
            """
            INSERT INTO thread_edges(
              c_uid, from_term, rel, to_term, ingest_unix, sys, dom, room, mod
            ) VALUES (?,?,?,?,?,?,?,?,?)
            """,
            (
                c_uid,
                e["from"],
                e["rel"],
                e["to"],
                ingest,
                sys or "",
                dom or "",
                room or "",
                mod or "",
            ),
        )
        for term in (
            e["from"],
            e["rel"],
            e["to"],
            f'{e["from"]}*{e["rel"]}>{e["to"]}',
        ):
            if not term or term in already:
                continue
            conn.execute(
                """
                INSERT INTO thread_terms(term, gravity, updated_at) VALUES (?,?,?)
                ON CONFLICT(term) DO UPDATE SET
                  gravity = gravity + 1,
                  updated_at = excluded.updated_at
                """,
                (term, 1, ingest),
            )
            already.add(term)

    tag_list = list(all_tags) if all_tags is not None else list(parsed["tags"])
    for t in tag_list:
        t = str(t).strip().lower()
        if not t or t in already:
            continue
        conn.execute(
            """
            INSERT INTO thread_terms(term, gravity, updated_at) VALUES (?,?,?)
            ON CONFLICT(term) DO UPDATE SET
              gravity = gravity + 1,
              updated_at = excluded.updated_at
            """,
            (t, 1, ingest),
        )
        already.add(t)
    return list(parsed["edges"])


def tps_window_unix(event_unix: int, window_seconds: int) -> int:
    """Floor event into a membrane window (same idea as PHP ledger)."""
    w = window_seconds if window_seconds > 0 else DEFAULT_TPS_WINDOW
    # negative event times still get a stable window key
    return (int(event_unix) // w) * w


def tps_uid_for_window(window_unix: int, window_seconds: int) -> str:
    # Match PHP mypi_ledger_tps_uid: "{window}.w{seconds}" (no tps. prefix)
    return f"{int(window_unix)}.w{int(window_seconds)}"


def ensure_tps_attach(
    conn: sqlite3.Connection,
    *,
    c_uid: str,
    kind: str,
    event_unix: int,
    ingest_unix: int | None = None,
) -> str:
    """Ensure TPS shelf for event window and attach crate. Returns tps_uid."""
    init_db(conn)
    w = tps_window_seconds(conn)
    ingest = int(ingest_unix if ingest_unix is not None else _now())
    window = tps_window_unix(int(event_unix), w)
    tps_uid = tps_uid_for_window(window, w)
    facets = json.dumps(
        {
            "window_unix": window,
            "window_seconds": w,
            "event_unix": int(event_unix),
        }
    )
    conn.execute(
        """
        INSERT INTO tps_shelves(
          tps_uid, window_unix, window_seconds, clock_id, facets_json, created_at
        ) VALUES (?,?,?,?,?,?)
        ON CONFLICT(tps_uid) DO NOTHING
        """,
        (tps_uid, window, w, "gaia", facets, ingest),
    )
    row = conn.execute(
        "SELECT COUNT(*) AS n FROM tps_attach WHERE tps_uid=?",
        (tps_uid,),
    ).fetchone()
    seq = int(row["n"] if row else 0)
    conn.execute(
        """
        INSERT INTO tps_attach(tps_uid, c_uid, kind, seq, attached_at)
        VALUES (?,?,?,?,?)
        ON CONFLICT(tps_uid, c_uid) DO NOTHING
        """,
        (tps_uid, c_uid, kind or "post", seq, ingest),
    )
    return tps_uid


def split_place_path(place_path: str) -> tuple[str, str, str, str]:
    """
    Split ``sys/dom/room[/mod…]`` into columns.

    First three segments → sys, dom, room. Extra segments join into mod
    (or empty). Mirrors postBASIC place as path pieces, not free-text only.
    """
    segs = [s for s in (place_path or "").strip().strip("/").split("/") if s]
    if not segs:
        return "", "", "", ""
    if len(segs) == 1:
        return segs[0], "", "", ""
    if len(segs) == 2:
        return segs[0], segs[1], "", ""
    if len(segs) == 3:
        return segs[0], segs[1], segs[2], ""
    return segs[0], segs[1], segs[2], "/".join(segs[3:])


def join_place_path(sys: str = "", dom: str = "", room: str = "", mod: str = "") -> str:
    return "/".join(x for x in (sys, dom, room, mod) if (x or "").strip())


def create_crate(
    conn: sqlite3.Connection,
    *,
    topic: str = "",
    body: str = "",
    agent: str = "user",
    tool: str = "postBASIC",
    place_path: str = "",
    place_label: str = "",
    sys: str = "",
    dom: str = "",
    room: str = "",
    mod: str = "",
    tags_raw: str = "",
    event_unix: int | None = None,
    timezone: str = "",
    actor: str = "hands",
    kind: str = "post",
    scale: str | None = None,
    face_id: str = "",
    parent_c_uid: str = "",
    stem_c_uid: str = "",
    span_start: int | None = None,
    span_end: int | None = None,
    glass_title: str = "",
    yard_title: str = "",
    meta: dict[str, Any] | None = None,
    c_uid: str | None = None,
) -> str:
    if not (topic or body):
        raise ValueError("need topic or body")
    init_db(conn)
    _ensure_sys_cols(conn)
    _ensure_composition_cols(conn)
    c_uid = (c_uid or "").strip() or new_c_uid()
    scale = (scale or "").strip() or default_scale(kind)
    ingest = _now()
    ev = event_unix if event_unix is not None else ingest
    place_path = (place_path or "").strip()
    sys = (sys or "").strip()
    dom = (dom or "").strip()
    room = (room or "").strip()
    mod = (mod or "").strip()
    # Prefer explicit pieces → path; else path → pieces. Never leave only a
    # free-text room with an empty sys/dom while place_path is multi-seg.
    if not place_path and (sys or dom or room or mod):
        place_path = join_place_path(sys, dom, room, mod)
    if place_path and not (sys and dom and room):
        s2, d2, r2, m2 = split_place_path(place_path)
        sys = sys or s2
        dom = dom or d2
        room = room or r2
        mod = mod or m2
    if not place_path:
        place_path = join_place_path(sys, dom, room, mod)
    tags = _parse_tags(tags_raw, place_path, sys, dom, room, mod)
    tags_json = json.dumps(tags)
    meta_json = json.dumps(meta or {})
    # provisional t_uid; replaced after ensure_tps_attach
    t_uid = f"{ev}.tps"
    conn.execute(
        """
        INSERT INTO crates(
          c_uid, kind, scale, face_id, parent_c_uid, stem_c_uid,
          span_start, span_end, glass_title, yard_title,
          topic, body, agent, tool, tool_version,
          place_path, place_label, sys, dom, room, mod,
          tags_json, tags_raw,
          event_unix, ingest_unix, timezone, t_uid, meta_json,
          created_at, updated_at
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        """,
        (
            c_uid,
            kind,
            scale,
            face_id or "",
            parent_c_uid or "",
            stem_c_uid or "",
            span_start,
            span_end,
            glass_title or "",
            yard_title or "",
            topic,
            body,
            agent,
            tool,
            1,
            place_path,
            place_label,
            sys,
            dom,
            room,
            mod,
            tags_json,
            tags_raw,
            ev,
            ingest,
            timezone,
            t_uid,
            meta_json,
            ingest,
            ingest,
        ),
    )
    for t in tags:
        conn.execute(
            "INSERT OR IGNORE INTO tag_map(c_uid, tag) VALUES(?, ?)",
            (c_uid, t),
        )
    t_uid = ensure_tps_attach(
        conn,
        c_uid=c_uid,
        kind=kind,
        event_unix=int(ev),
        ingest_unix=ingest,
    )
    conn.execute(
        "UPDATE crates SET t_uid=? WHERE c_uid=?",
        (t_uid, c_uid),
    )
    # Charlie: gravity for all production tags (path:/@seg/user) + edges
    charlie_write(
        conn,
        c_uid=c_uid,
        tags_raw=tags_raw,
        all_tags=tags,
        sys=sys,
        dom=dom,
        room=room,
        mod=mod,
        ingest_unix=ingest,
    )
    conn.execute(
        """
        INSERT INTO crate_events(
          c_uid, event_type, payload_json, actor, place_path,
          event_unix, ingest_unix, tool
        ) VALUES (?,?,?,?,?,?,?,?)
        """,
        (
            c_uid,
            "create",
            json.dumps(
                {
                    "topic": topic,
                    "body": body,
                    "tags": tags,
                    "place_path": place_path,
                    "t_uid": t_uid,
                }
            ),
            actor,
            place_path,
            ev,
            ingest,
            tool,
        ),
    )
    conn.commit()
    return c_uid


def add_tag(
    conn: sqlite3.Connection,
    c_uid: str,
    tag: str,
    *,
    actor: str = "hands",
    place_path: str = "",
    tool: str = "mypi-tui",
) -> None:
    """Append one plain tag (no Charlie edges). Prefer append_charlie for full language."""
    tag = tag.strip().lstrip("#")
    if not tag:
        raise ValueError("empty tag")
    append_charlie(conn, c_uid, tag, actor=actor, place_path=place_path, tool=tool)


def set_crate_charlie(
    conn: sqlite3.Connection,
    c_uid: str,
    tags_raw: str,
    *,
    actor: str = "hands",
    place_path: str = "",
    tool: str = "mypi-tui",
) -> None:
    """
    Replace tags_raw on a crate, rebuild tag_map + Charlie edges/terms.
    Full multi-stage Charlie syntax supported (a*b>c,d ; clauses).
    """
    init_db(conn)
    row = conn.execute(
        "SELECT tags_raw, sys, dom, room, mod, place_path FROM crates WHERE c_uid=?",
        (c_uid,),
    ).fetchone()
    if not row:
        raise KeyError(c_uid)

    sys = str(row["sys"] or "")
    dom = str(row["dom"] or "")
    room = str(row["room"] or "")
    mod = str(row["mod"] or "")
    ppath = place_path or str(row["place_path"] or "")
    if not ppath:
        ppath = join_place_path(sys, dom, room, mod)

    tags_raw = (tags_raw or "").strip()
    parsed = parse_charlie(tags_raw)
    # place path tags + charlie production tags
    place_bits = _parse_tags("", ppath, sys, dom, room, mod)
    all_tags: list[str] = []
    for t in place_bits + list(parsed["tags"]):
        t = str(t).strip().lower()
        if t and t not in all_tags:
            all_tags.append(t)

    ingest = _now()
    old_raw = str(row["tags_raw"] or "")

    # rebuild map + edges for this crate
    charlie_detach(conn, c_uid)
    conn.execute("DELETE FROM tag_map WHERE c_uid=?", (c_uid,))
    conn.execute(
        "UPDATE crates SET tags_json=?, tags_raw=?, updated_at=? WHERE c_uid=?",
        (json.dumps(all_tags), tags_raw, ingest, c_uid),
    )
    for t in all_tags:
        conn.execute(
            "INSERT OR IGNORE INTO tag_map(c_uid, tag) VALUES(?, ?)",
            (c_uid, t),
        )
    charlie_write(
        conn,
        c_uid=c_uid,
        tags_raw=tags_raw,
        all_tags=all_tags,
        sys=sys,
        dom=dom,
        room=room,
        mod=mod,
        ingest_unix=ingest,
    )
    conn.execute(
        """
        INSERT INTO crate_events(
          c_uid, event_type, payload_json, actor, place_path,
          event_unix, ingest_unix, tool
        ) VALUES (?,?,?,?,?,?,?,?)
        """,
        (
            c_uid,
            "charlie_set",
            json.dumps({"tags_raw": tags_raw, "was": old_raw[:500]}),
            actor,
            ppath,
            ingest,
            ingest,
            tool,
        ),
    )
    conn.commit()


def append_charlie(
    conn: sqlite3.Connection,
    c_uid: str,
    fragment: str,
    *,
    actor: str = "hands",
    place_path: str = "",
    tool: str = "mypi-tui",
) -> None:
    """
    Append Charlie language onto existing tags_raw (backend tagging from TUI).
    Examples:  aubel   |  lore,system   |  aubel*knows>iox
    """
    fragment = (fragment or "").strip()
    if not fragment:
        raise ValueError("empty charlie fragment")
    init_db(conn)
    row = conn.execute(
        "SELECT tags_raw FROM crates WHERE c_uid=?", (c_uid,)
    ).fetchone()
    if not row:
        raise KeyError(c_uid)
    old = (row["tags_raw"] or "").strip()
    if old:
        # avoid double-append exact
        if fragment.lower() in old.lower():
            # still allow re-apply to rebuild edges if needed
            new_raw = old
        else:
            new_raw = f"{old}; {fragment}"
    else:
        new_raw = fragment
    set_crate_charlie(
        conn,
        c_uid,
        new_raw,
        actor=actor,
        place_path=place_path,
        tool=tool,
    )


def set_body(
    conn: sqlite3.Connection,
    c_uid: str,
    body: str,
    *,
    actor: str = "hands",
    place_path: str = "",
    tool: str = "mypi-tui",
) -> None:
    init_db(conn)
    row = conn.execute("SELECT body FROM crates WHERE c_uid=?", (c_uid,)).fetchone()
    if not row:
        raise KeyError(c_uid)
    old = row["body"]
    ingest = _now()
    conn.execute(
        "UPDATE crates SET body=?, updated_at=? WHERE c_uid=?",
        (body, ingest, c_uid),
    )
    conn.execute(
        """
        INSERT INTO crate_events(
          c_uid, event_type, payload_json, actor, place_path,
          event_unix, ingest_unix, tool
        ) VALUES (?,?,?,?,?,?,?,?)
        """,
        (
            c_uid,
            "set_body",
            json.dumps({"old": old, "new": body}),
            actor,
            place_path,
            ingest,
            ingest,
            tool,
        ),
    )
    conn.commit()


def list_crates(
    conn: sqlite3.Connection,
    *,
    place_path: str | None = None,
    tag: str | None = None,
    limit: int = 50,
    include_deleted: bool = False,
    heads_only: bool = True,
) -> list[sqlite3.Row]:
    """
    List crates. By default **heads_only**: one row per stem lineage
    (latest ingest_unix) so fileKeeper linebreak revs don't flood the index.
    Pass heads_only=False for full revision lists.
    """
    init_db(conn)
    _ensure_deleted_col(conn)
    _ensure_composition_cols(conn)
    alive = "" if include_deleted else " AND (c.deleted_at IS NULL OR c.deleted_at=0)"
    alive_plain = "" if include_deleted else " AND (deleted_at IS NULL OR deleted_at=0)"

    # stem key: explicit stem_c_uid, else self (leaf with no lineage)
    stem_expr = "COALESCE(NULLIF(c.stem_c_uid, ''), c.c_uid)"
    stem_expr_plain = "COALESCE(NULLIF(stem_c_uid, ''), c_uid)"

    if heads_only:
        # latest ingest per stem; tie-break c_uid for stability
        head_join = f"""
        INNER JOIN (
          SELECT {stem_expr_plain} AS stem, MAX(ingest_unix) AS mx
          FROM crates
          WHERE 1=1 {alive_plain}
          GROUP BY stem
        ) _head ON {stem_expr} = _head.stem AND c.ingest_unix = _head.mx
        """
        # if two revs same second, pick max c_uid
        head_join = f"""
        INNER JOIN (
          SELECT stem, MAX(ingest_unix) AS mx, MAX(c_uid) AS pick
          FROM (
            SELECT {stem_expr_plain} AS stem, ingest_unix, c_uid
            FROM crates
            WHERE 1=1 {alive_plain}
          )
          GROUP BY stem
        ) _head ON {stem_expr} = _head.stem
            AND c.ingest_unix = _head.mx
            AND c.c_uid = _head.pick
        """
    else:
        head_join = ""

    if tag:
        q = f"""
        SELECT c.* FROM crates c
        JOIN tag_map t ON t.c_uid = c.c_uid
        {head_join}
        WHERE t.tag = ?
        {alive}
        """
        args: list[Any] = [tag]
        if place_path:
            q += " AND c.place_path = ?"
            args.append(place_path)
        q += " ORDER BY c.ingest_unix DESC LIMIT ?"
        args.append(limit)
        return list(conn.execute(q, args))
    if place_path:
        q = f"""
        SELECT c.* FROM crates c
        {head_join}
        WHERE c.place_path = ?
        {alive}
        ORDER BY c.ingest_unix DESC LIMIT ?
        """
        return list(conn.execute(q, (place_path, limit)))
    q = f"""
    SELECT c.* FROM crates c
    {head_join}
    WHERE 1=1
    {alive}
    ORDER BY c.ingest_unix DESC LIMIT ?
    """
    return list(conn.execute(q, (limit,)))


def stem_head_c_uid(conn: sqlite3.Connection, c_uid: str) -> str:
    """Return latest c_uid in this crate's stem lineage (or self)."""
    init_db(conn)
    _ensure_composition_cols(conn)
    row = conn.execute(
        "SELECT c_uid, stem_c_uid FROM crates WHERE c_uid=?", (c_uid,)
    ).fetchone()
    if not row:
        return c_uid
    stem = (row["stem_c_uid"] or "").strip() or c_uid
    head = conn.execute(
        """
        SELECT c_uid FROM crates
        WHERE (COALESCE(NULLIF(stem_c_uid, ''), c_uid) = ?)
          AND (deleted_at IS NULL OR deleted_at = 0)
        ORDER BY ingest_unix DESC, c_uid DESC
        LIMIT 1
        """,
        (stem,),
    ).fetchone()
    return str(head["c_uid"]) if head else c_uid


def get_crate(conn: sqlite3.Connection, c_uid: str) -> sqlite3.Row | None:
    init_db(conn)
    return conn.execute("SELECT * FROM crates WHERE c_uid=?", (c_uid,)).fetchone()


def history(conn: sqlite3.Connection, c_uid: str) -> list[sqlite3.Row]:
    init_db(conn)
    return list(
        conn.execute(
            "SELECT * FROM crate_events WHERE c_uid=? ORDER BY id ASC",
            (c_uid,),
        )
    )


def _ensure_deleted_col(conn: sqlite3.Connection) -> None:
    cols = {r[1] for r in conn.execute("PRAGMA table_info(crates)")}
    if "deleted_at" not in cols:
        conn.execute("ALTER TABLE crates ADD COLUMN deleted_at INTEGER")
    conn.execute(
        """
        CREATE TABLE IF NOT EXISTS deleted_log (
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          c_uid TEXT NOT NULL,
          snapshot_json TEXT NOT NULL,
          deleted_at INTEGER NOT NULL,
          actor TEXT NOT NULL DEFAULT '',
          hard INTEGER NOT NULL DEFAULT 0
        )
        """
    )
    conn.commit()


def soft_delete(
    conn: sqlite3.Connection,
    c_uid: str,
    *,
    actor: str = "hands",
    tool: str = "mypi-tui",
) -> None:
    init_db(conn)
    _ensure_deleted_col(conn)
    row = get_crate(conn, c_uid)
    if not row:
        raise KeyError(c_uid)
    if row["deleted_at"]:
        return
    now = _now()
    conn.execute(
        "UPDATE crates SET deleted_at=?, updated_at=? WHERE c_uid=?",
        (now, now, c_uid),
    )
    # Devalue = out of live Charlie graph (re-spliced on restore from tags_raw)
    charlie_detach(conn, c_uid)
    conn.execute(
        """
        INSERT INTO crate_events(
          c_uid, event_type, payload_json, actor, place_path,
          event_unix, ingest_unix, tool
        ) VALUES (?,?,?,?,?,?,?,?)
        """,
        (
            c_uid,
            "soft_delete",
            json.dumps({"topic": row["topic"]}),
            actor,
            row["place_path"] or "",
            now,
            now,
            tool,
        ),
    )
    conn.execute(
        """
        INSERT INTO deleted_log(c_uid, snapshot_json, deleted_at, actor, hard)
        VALUES (?,?,?,?,0)
        """,
        (c_uid, json.dumps(dict(row)), now, actor),
    )
    conn.commit()


def hard_delete(
    conn: sqlite3.Connection,
    c_uid: str,
    *,
    actor: str = "hands",
    tool: str = "mypi-tui",
) -> None:
    """Remove one crate only (incl. Charlie edges + TPS attach). Never the whole world."""
    init_db(conn)
    _ensure_deleted_col(conn)
    row = get_crate(conn, c_uid)
    if not row:
        raise KeyError(c_uid)
    now = _now()
    conn.execute(
        """
        INSERT INTO deleted_log(c_uid, snapshot_json, deleted_at, actor, hard)
        VALUES (?,?,?,?,1)
        """,
        (c_uid, json.dumps(dict(row)), now, actor),
    )
    charlie_detach(conn, c_uid)
    conn.execute("DELETE FROM tps_attach WHERE c_uid=?", (c_uid,))
    conn.execute("DELETE FROM tag_map WHERE c_uid=?", (c_uid,))
    conn.execute("DELETE FROM crate_events WHERE c_uid=?", (c_uid,))
    conn.execute("DELETE FROM crates WHERE c_uid=?", (c_uid,))
    conn.commit()


def stats(conn: sqlite3.Connection) -> dict[str, Any]:
    init_db(conn)
    _ensure_deleted_col(conn)
    n = conn.execute(
        "SELECT COUNT(*) AS n FROM crates WHERE deleted_at IS NULL OR deleted_at=0"
    ).fetchone()["n"]
    e = conn.execute("SELECT COUNT(*) AS n FROM crate_events").fetchone()["n"]
    ver = conn.execute(
        "SELECT value FROM ledger_meta WHERE key='schema_version'"
    ).fetchone()
    return {
        "crates": n,
        "events": e,
        "schema_version": ver["value"] if ver else "?",
        "db": str(DEFAULT_DB),
    }
