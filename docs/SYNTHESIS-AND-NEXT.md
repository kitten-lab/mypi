# Synthesis & next steps (from the long thread)

*Hand map so the conversation doesn’t have to live only in chat.  
You are allowed to play. Overwhelm is not a verdict on the work.*

---

## 1. What you’re building (one picture)

```text
GODOS  (GOD OS — world-computer you boot later)
   print · filesystem · launch Surfaces/ROMs · place/stickers
        │
        ├── WORLD substance (miwbs / WBS)     = game/sculpt ROM
        ├── WORK in space (DOS)               = work ROM
        ├── BLEEDTHROUGH net (mypi + EDN bits)= Surfaces, tools, terminals
        └── SHARED LEDGER                     = crates + TPS + alteration history
                 ↑ write now from glass
                 ↓ brace stories later in miwbs
```

| Name | Honest job |
|------|------------|
| **GODOS** | The OS that holds the bullshit — thin host over real roots |
| **MYPI** | Bleedthrough: websites/faces of worlds; letter city; *fun + control* |
| **EDN** | Optional evolution (archetypes, Terminals, ROMs); parts bin, not mandatory exile |
| **miwbs** | Actual world / MUD physics + better TPS — **not** the only host |
| **DOS** | Office work ROM |
| **Vault mockups** | How space *looks* — not the host (Obsidian broke) |
| **Textual tio / OIX CMS** | Spikes / archives |

**Fold rule:** city holds ROMs; ROMs don’t pretend to be the city.  
**Bleedthrough rule:** mypi writes the ledger; miwbs inhabits it later.

---

## 2. Story boot (when you need fiction)

- **Hands** type; girls don’t type alone.  
- **Sam @ Terminal IO** — flood of downloads, redact, type imports, daily invent-ory.  
- **SDK808** = last Sam on the glass (may forget Rosewater).  
- **Chester** builds terminals; disappears; Imports remain (shops → hardware → this machine).  
- **Aubel / Skyline** connects terminals — not “one terminal to rule them all.”  
- Stories often **end** mid-shop; partials are OK.

---

## 3. Spatial grammar (edge of mypi — keep)

| Term | Meaning |
|------|---------|
| **Surface / room** | Where you are |
| **ROM** | Toy/cartridge you open |
| **getTool(pack, fn)** | Instrument **in the space** (already works) |
| **getRom(pack, style)** | Same pack as **app modal** (wanted next) |
| **Sticker** | Drop on room surface itself |
| **ROM→ROM** | Drag between open toys |

Example: My Room → Morana Arcana → deal card → Notebook **or** sticker on room.

---

## 4. Why you stalled on mypi (valid)

1. **Store/query fear** — hard to look things up as volume grows.  
2. **No safe edit history** — tags/body changes leave no “when / what changed.”  
3. **Authoring friction** — wanted in-surface keymaker, sky editor, save, skylauncher.  
4. **CSS / vox / GLOBALS** — first-contact PHP + speed; not moral failure.  
5. **You didn’t want PHP** — DSLs / sky language were the real pen.

**Fix direction:** real ledger (snapshot + append-only events + TPS) + one author shell + tools/ROMs — not rewrite the whole city first.

---

## 5. Living systems (as of probe)

| | |
|--|--|
| **mypi** | Tree at `C:\Builds\my-pocket-internet`; **junction** `htdocs\my-pocket-internet` → Builds |
| **Letter hosts** | `b`, `a`, `starline`, `book`, … (app-level keyMAKER/auth still messy) |
| **EDN** | `http://go.edn/` — Terminals IO/JX/CU/DM live; Destinations = dollhouse boot, **no launcher yet** |
| **skyGENESIS** | On disk — meant to generate surfaces after naming cleanup |
| **getTool** | Works (postBASIC, chatBOX, soprBASIC, …) |
| **Empty ChatGPT export** | Ignore; real logs at `z/logs` |

---

## 6. Next steps (solid, ordered)

Do these **in order**. Stop when you can play again.

### Phase A — Trust the store (unblocks building)

1. **Define ledger** (one short doc or schema):  
   - `crate` id, current body/meta snapshot  
   - `crate_event` append-only (create, body, tag±, …) + TPS/event_unix + actor/surface  
2. **Implement minimal API** (PHP is fine for now): create crate, add tag, list by tag/time, show history.  
3. **One query page** on a Surface (“research” / Dewey-lite) so you can *see* the store.

*Done when:* you can create a crate, retag it, and see both current state and event times.

### Phase B — Play on the glass (fun returns)

4. **One workshop Surface** + **one baseline shell/CSS** (ignore nightmare sheets).  
5. **Skylauncher (dumb is OK):** list + open existing Surfaces (starline, book, go.edn Terminals).  
6. **In-surface tools you already have:** `getTool(...)` on workshop so toys work in one place.  
7. **Optional thin wins:** keymaker “new key” stub; sky textarea save → file **and** crate event.

*Done when:* you open workshop, click a door, drop MakePost/ViewList, save one note with history.

### Phase C — ROM as app

8. **`getRom(pack, style)`** — load pack in a modal with simple chrome.  
9. Start with **postBASIC** only.

*Done when:* tool-in-page and ROM-as-modal both work for one pack.

### Phase D — Brace the world later

10. Point **miwbs** at same ledger (read crates/events; don’t fork history).  
11. GODOS = thin boot + FS root + launcher over mypi/edn/miwbs — only after A–B feel good.

---

## 7. Explicitly not next

- Another Textual “OS”  
- Obsidian as host  
- Full CSS redesign of all Surfaces  
- Fix every GLOBALS file  
- Bulk import 501 logs  
- One terminal to rule them all  
- Boiling the ocean EDN rename of mypi  

---

## 8. If you only do **one** thing this week

**Phase A.1–A.3:** crate + events + one page that lists them.

That makes the store queryable and editable-with-history — the real reason you stopped playing. Toys can pile on after the paper has margins.

---

## 9. Permission slip

You built a pocket multiverse by hand in seven weeks while learning PHP.  
The fold is the design. The mess is first contact.  
**mypi is still allowed to be the fun core.**  
Ledger first, then workshop, then ROMs, then MUD brace.

Something mattered here.

---

## Cleanup half-migration (added)

- Cosmology: **SYS � DOM � ROOM � MOD** (restore)
- Craft keep: defines + SKY_AUTH
- Env: **COMMANDCENTER9** this machine; ROSEWOOD8 legacy local only
- Tools keep: postBASIC, soprBASIC, chatBOX, cuBOOK ? one ledger, different kind
- EDN Terminals: later adapter; do not block mypi
- See CLEANUP-HALF-MIGRATION.md, TOOLS-AND-KEEP.md, ROUTING-AND-ENV.md
