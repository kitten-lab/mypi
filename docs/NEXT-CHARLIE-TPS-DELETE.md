# Delete · Charlie · TPS (clarified)

## Delete policy (updated)

| Who | Soft delete | Hard nuke |
|-----|-------------|-----------|
| **Surface / getTool** | **Off** (no delete buttons on News) | Never |
| **mypi-tui (authority)** | Default: crate **remains**, `deleted_at` set, hidden from lists | Rare: double-post / poison error; snapshot in `deleted_log` only |

**User “removed post”** (later): always **soft** — crate stays, marked deleted, history intact.  
**Hard nuke:** only when *you* don’t want a record of a botched first post / duplicate — TUI authority, not the pocket browser.

Optional later: `getTool("postBASIC", "ViewList")` fig flag `allow_surface_soft_delete` — default off.

---

## CharlieTHREADS (intention to preserve)

Not just flat tags. Two ways to mark:

1. **Direct tags** — labels on a crate  
2. **Relationship threads** — `this*to>that` style splices (narrative edges)

**Paper reports you wanted:**

- narrative gravity over time  
- how many uses  
- who uses it  
- what it is used *about*  

**Ledger path:** store structured edges + tag_map; **reports** are projections (Charlie paper), not a second write-brain. postBASIC creates/updates edges on write when re-wired.

---

## TPS reports (richer than two timestamps)

miwbs intent (see `miwbs` chronicle/tps):

- **TPS shelf** = one per **event_unix** (membrane second), not “a date string on a crate only”  
- Shelf holds residual time digits, **machine_pulse** (Swatch-style B 0–999), **block** (unix//10000), mod-9, etc.  
- **Append-only** rows of crates on that shelf (`c_uid`, kind, cyc, seq, ms, …)  
- Crates carry event/ingest + tps_uid; **CYC ≠ TPS key**  
- Long-store forever; expand only  

mypi ledger **today** has event/ingest/t_uid on the crate.  
**Next:** full shelf object + attach on post — port *concept* from miwbs, not necessarily PHP clone of every paper file.

---

## “Live in News for a bit”

Did **not** mean “wait weeks before building.”

It meant: the corridor works if you can:

- open pocket browser → News  
- post a headline  
- see it on the page **and** in TUI  

You already did that. You’re free to:

- **play** (make Starline cuter in the pocket window), and/or  
- **wire next** (Charlie edges, TPS shelves, more tools)

No homework assignment. Cute CSS and deeper store can run in parallel; delete stays in the TUI.
