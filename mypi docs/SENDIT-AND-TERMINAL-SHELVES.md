# sendIT · Sam-first import · multi-terminal shelves

**Status:** NOTE / PLAN — not building yet  
**Hands decision (2026):** Finish **Sam’s files** vault import into **IO fileKeeper** first.  
**Then:** install other terminals (fileKeeper-only) + **sendIT**.  
**Until then:** new thought-leaves continue to enter **Sam’s desk** (true narrative: the bag held them).

---

## 1. Narrative law

| fact | meaning |
|------|---------|
| **Sam had the files** | Custody / leak shelf / how material entered the house |
| **Practice home** | AB · PI · IO · JX (which *job* owns the work) |
| **Bleed is temporary** | One live door + one bag ≠ identity of every leaf |

**Import path ≠ authorship of practice.**  
Bag is **provenance**. Placement is **terminal home**.

While only IO is online, Sam’s fileKeeper is the honest front door. Refile later with lineage — do not pretend boards were always JX *as crates* if they arrived in Sam’s vault.

---

## 2. Terminal cast (for later doors)

| terminal | mandate (v0 = files only) |
|----------|---------------------------|
| **IO** | Chat-log world, imports, **Sam bag / leak shelf** |
| **AB** (red) | Suppression of success / good outcomes; networks the protagonist is in |
| **PI** | Investigation workshop notes |
| **JX** | Train repair between worlds; letter boards, rotational devices, strange thinking |

**v0 online shape (deferred):** each terminal gets a **files** room + fileKeeper only. No AB/PI/JX specialty tools until that world is actually online.

**Do not scaffold AB/JX/PI doors until Sam’s vault import is complete.**

---

## 3. Current work mode

1. Import / enter material into **Sam’s files** (IO · fileKeeper) in full from the vault.  
2. Keep folders useful for *human* sorting if needed (`MUSIC`, `ALEPH BET A-US`, …) — still **Sam custody**.  
3. No sendIT yet; no multi-terminal install yet.  
4. Optional light tags later: `bag*from>sam` — not required mid-import.

---

## 4. sendIT (plan — build after Sam import)

### Purpose
Move **practice home** without erasing **Sam custody**.

Default action: **Send copy** (not hard delete of Sam’s leaf).

- **Source (Sam / IO):** stays in list; marked **fwded** / faded; still readable as leak.  
- **Target (AB | JX | PI | …):** new stem at correct `sys`/`dom`/`room`, full body/title/tags as of send.  
- **Lineage:** both sides know each other.

### UX (Sam desk)

| signal | idea |
|--------|------|
| **List mark** | `fwded` badge (or `→JX` / `→AB`) on the row |
| **Fade** | Lower opacity on fwded heads — still clickable |
| **Sent display** | Optional filter/tab: **Active** · **Fwded** · **All** (or a small “sent” strip under the tree) |
| **Open fwded** | View still works; maybe banner: *copy lives at JX · stem ch.…* |
| **sendIT control** | From view (or row action): target terminal → **Send copy** |

Hard **Move** (drop from Sam list) is optional later; default is **keep leak + mark fwded**.

### Meta (suggested)

On **source** head/rev (or stem meta):

```text
fwded: true
fwded_at: <unix>
fwded_to_place: jx/…/files   # or ab/pi/…
fwded_to_stem: ch.…
send_action: copy
```

On **target** crate:

```text
provenance: sam-files
sent_from_c_uid: ch.…
sent_from_stem: ch.…
sent_from_place: terminal/io/files
sent_at: <unix>
```

Charlie optional: `bag*from>sam`, `sent*to>jx` — only if useful; place tags already carry home.

### Ledger behavior

- fileKeeper already keys desks by **place** (`sys`/`dom`/`room` from sky).  
- sendIT = **clone stem → create at target place** + stamp meta on source + target.  
- Do **not** rewrite Sam’s historical revs; mark at **stem/head** level so the whole file reads as fwded.  
- Charlie: target write bumps gravity at new place; source edges stay unless we later scrub — fine for v1.

### Non-goals (v1)

- No bulk auto-classify by content.  
- No deleting Sam’s copy by default.  
- No full AB/JX tool suites.  
- No pretending sendIT is import (import = vault→Sam; sendIT = Sam→practice home).

---

## 5. Sequence (locked for now)

```text
[now]     Finish Sam’s vault → IO fileKeeper (full)
[next]    Note stays; hands keep entering on Sam’s desk
[after]   Scaffold AB / JX / PI (files-only doors + place-scoped desk)
[after]   sendIT: copy + fwded mark + optional Sent filter
[later]   Skins (AB red), specialty tools per terminal
```

---

## 6. One-line house law

> **Sam keeps the leak. sendIT forwards a living copy home and fades the original in the list as fwded — after the bag is fully in.**

---

## Related

- [CRATE-DUAL-RAIL-AND-IMPORT-WORK.md](./CRATE-DUAL-RAIL-AND-IMPORT-WORK.md) — place, scale, dual-rail  
- [TOOL-LEDGER-STORE.md](./TOOL-LEDGER-STORE.md) — ledger / Charlie  
- [PLACE-AND-NAMING.md](./PLACE-AND-NAMING.md) — if present, sys/dom/room language  
