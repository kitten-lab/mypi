# How storage works + how to look at it

## Where data lives

| What | Path |
|------|------|
| **SQLite ledger** | `C:\Builds\my-pocket-internet\d\_LEDGER\mypi.sqlite` |
| Same via junction | `C:\xampp\htdocs\my-pocket-internet\d\_LEDGER\mypi.sqlite` |

**Not** written anymore by new postBASIC: `d/starline/news-headlines.post.json` style room slips.

## What a row holds (crate)

- `c_uid` — stable id (`crate.…`)
- `kind` — `post` (later fragment/chat/…)
- `topic` / `body`
- **`sys` / `dom` / `room` / `mod`** — where posted + who (modifier)
- `place_path` — `sys/dom/room` joined (query helper)
- `tags_json` / `tags_raw` — user tags + auto `@segment`, `sys:`, `dom:`, `mod:`
- `event_unix` / `ingest_unix` / `t_uid` — time buckets
- `tool` — `postBASIC`

## History

Table **`crate_events`**: every create (and later edits) is append-only with timestamp.

## How to look

### 1. First-class report Surfaces (like `d/` folders)

| d/ sense | Starline DOM / URL |
|----------|---------------------|
| **_CHESTER** | `http://starline/chester/crates` |
| **_CHARLIE** | `http://starline/charlie/threads` |
| **_SATORA** | `http://starline/satora/shelves` |
| **News write** | `http://starline/news/headlines` |

Nav: **News · Crates · Charlie · TPS**

### 2. mypi-tui (same three sections)

```bat
cd C:\Builds\my-pocket-internet\ledger
python mypi_tui.py
```

Keys **1 / 2 / 3** or buttons: **CRATES · CHARLIE · TPS**.  
Authority delete only in CRATES section.

### 3. SQLite CLI (if installed)

```bat
sqlite3 C:\Builds\my-pocket-internet\d\_LEDGER\mypi.sqlite
```

```sql
SELECT c_uid, sys, dom, room, mod, topic, ingest_unix FROM crates ORDER BY ingest_unix DESC LIMIT 20;
SELECT * FROM crate_events ORDER BY id DESC LIMIT 20;
SELECT tag, COUNT(*) FROM tag_map GROUP BY tag;
```

### 4. DB Browser for SQLite

Open `mypi.sqlite` in [DB Browser for SQLite](https://sqlitebrowser.org/) — free GUI.

### 5. One-file backup

Copy `d/_LEDGER/mypi.sqlite` somewhere safe. That **is** your thoughts DB for postBASIC-class cargo.

## Charlie + TPS (schema v2)

| Table | Meaning |
|-------|---------|
| **tps_shelves** | Membrane **windows** (default **900s / 15 min**), not every second |
| **tps_attach** | Which crates sit on that window |
| **thread_edges** | `from *rel> to` from tag string |
| **thread_terms** | Gravity counts for reports |

**Tag examples on MakePost:**

```text
online
grief*connects>hope
system*to>skyline
```

Separate multi-edges with **semicolons**.

Crate `t_uid` looks like `1784667600.w900` (window start + width).

**TUI:** button **Show TPS + Charlie** lists shelves, gravity, edges.

Change window size (seconds) in SQLite:

```sql
UPDATE ledger_meta SET value='60' WHERE key='tps_window_seconds';  -- 1 minute
-- or '900' for 15 minutes (default)
```

## Flow

```text
Skyline / news / headlines  (MOD system)
  MakePost  →  Ledger.php  →  crate + TPS window + Charlie edges
  SoperView →  list + gravity + shelves
  mypi-tui  →  same file (TPS + Charlie panel)
```

## Skyline News coordinates

| Role | Value |
|------|--------|
| SYS | starline |
| DOM | news |
| ROOM | headlines |
| MOD | system (System Voice) |
