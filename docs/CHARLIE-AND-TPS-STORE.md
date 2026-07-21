# CharlieTHREADS + TPS — store intention (not paper)

Paper reports were the **desired faces**. They should become **queries over intelligent storage**, not the primary write path.

---

## CharlieTHREADS

### Input shapes

| Shape | Meaning |
|-------|---------|
| Direct tags | Labels on a crate |
| Thread edges | `this*to>that` (and family) — **relationships**, not only bags of words |

### What to store (intelligent)

- **tag_map** — crate ↔ tag (already started)  
- **thread_edges** (next table) — `from_term`, `rel`, `to_term`, `c_uid`, `sys/dom/room/mod`, `ingest_unix`  
- Optional counters / rollups later — or compute on report generation  

### Reports (produced later, not handwritten each post)

- narrative gravity over time  
- use counts  
- who uses (mod / agent)  
- what it’s used *about* (topics, linked crates)  

**Rule:** postBASIC / tools **write edges + tags into the ledger**. Charlie “paper” = export/view of that graph.

---

## TPS — membrane time windows

### miwbs / your intent

- A **TPS shelf** groups crates that share a membrane time key  
- Shelf is **append-only** for membership (crates attach; shelves don’t get deleted)  
- Rich report face later (residual digits, machine_pulse, block, … — miwbs)  
- **Not** the same as CYC (story node / world clock)

### Multi-clock worlds (later)

| Clock | Role |
|-------|------|
| **Gaia / wall** | “Our” civil time (default membrane) |
| **Computational Age 0** | Some worlds treat 0 as start of computation |
| **CYC** | Story spine (miwbs node tracker) — separate from TPS key |
| Other surface clocks | World may map stamps differently |

**All crates still land on a shared membrane stamp** for the default store; world surfaces may *display* or *map* that stamp into local codings without forking the crate row.

### Granularity — “nearby,” not miwbs second-research

**mypi TPS windows are intentional and different from miwbs second-shelves.**

| miwbs TPS | mypi SATORA nearby |
|-----------|---------------------|
| Often **per event_unix second** | **Window** (default 15m) |
| Rich residual / mod-9 / pulse research | “These crates happened **nearby**” |
| CYC separate; shelf = membrane second | Exact `event_unix` still on crate; sort **inside** window |

We are **not** trying to clone “every mod-9 search forever” into mypi first.  
Nearby co-occurrence is **more valuable** for care/import work. Full miwbs-style facets can attach later as optional shelf metadata (already stubbed in `facets_json`).

**Preferred window:** configurable (default **900s / 15 min**; 60s ok).

```text
window_unix = event_unix - (event_unix % WINDOW_SECONDS)
tps_uid     = "{window_unix}.w{WINDOW_SECONDS}"   // e.g. 1782521400.w900
```

| Window | Feel |
|--------|------|
| 60s | “same minute” |
| 300s | five-minute beat |
| **900s** | **15-minute block** — “same sitting / same pulse” |
| 3600s | hour |

Shelf row holds: `tps_uid`, `window_unix`, `window_seconds`, clock_id (`gaia` default), metadata for full report fields later.  
**crate_tps** (or list on shelf): append `{c_uid, kind, seq, ms…}`.

Crates still keep `event_unix` / `ingest_unix` for exactness; **search/TUI** primarily by **window** (“everything in this 15 minutes”).

### TUI search shapes (goal)

- by window / tps_uid  
- by day (rollup of windows)  
- by clock_id when multi-clock exists  
- list crates on a shelf  
- not “one shelf per lonely second” unless WINDOW=1 for debug  

---

## Trust bar for “real” thoughts

Not complete until at least:

1. ~~Ledger + postBASIC + News~~  
2. ~~Soft delete authority in TUI; no casual surface nuke~~  
3. **TPS windows** + attach on post + TUI browse by window  
4. **Charlie edges** (`this*to>that`) + tag_map + one gravity report view  
5. Optional: cute Starline (play; parallel)

Cute CSS helps the *feeling*; **Charlie + windowed TPS** are what make the store worth real cargo.

---

## One sentence

**Charlie becomes edge+tag intelligence that can generate gravity reports; TPS becomes windowed membrane shelves (minute/15m, not every second) holding all crates on that stamp, with multi-clock display later — paper dies as write-path, lives as query face.**
