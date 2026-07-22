# Report services · from the green terminal (no desk tour)

**Status:** PLAN / LAW — backend buckets first; Skyline report **desks** later  
**Hands:** Stay in **terminal (green)** during opening processes. Leaving the station to file → ~90% less likely to run the pass.  
**Not invent-ory alone:** “Capture now, ship later” fails the same way as untagged bags — **ship at capture** (or one click from the insert).

---

## 1. Opening concept (archive truth)

Skyline **REPORT DEPARTMENT** was a set of **services** you submit to — not one blog:

| service (demo room) | face / character | attention kind |
|---------------------|------------------|----------------|
| **omansOmens** | Oman O’mente | Omens — attention pulled hard (signs, animals, patterns…) |
| **songOfSongs** | **Princess Jasmine** (beach + cosmic romance; room may display “hymns”) | Sense of hymn / song / live voice / musical-myth charge |
| **teeHeeSecrets** | Lil’ Secret Keeper | Secrets known / whispered |

Tool spine was **reportBASIC** + room **flavor** (SIG FIG).  
Example path: `sys=SKYLINE` · `dom=reportDepartment` · `room=omansOmens`.

**Yellow swallowtail at the window** → **Oman** (omen).  
**Elon on a live Tesla call + feelings** → **songOfSongs** handle *for now* (hymn / live voice / song-of-the-sphere — not Oman). Taxonomy can refine; the **bucket** is what matters.

Characters will eventually have **own surfaces** over what they collected. **Not required to open the pipe.**

---

## 2. Rejected: invent-only then remember to file

**Option B** (dump everything in daily invent-ory, re-file later) is a **memory tax**.  

Same class of failure as:

- untagged Sam files waiting for Charlie  
- sendIT waiting until “after I remember which terminal”  

If shipping is a second session in another building, **the pass dies**.

---

## 3. Chosen shape: backend collectors, terminal submits

### What we build first
- **Buckets** in the ledger (place + kind), **no** public Oman/Skyline desk UI required.  
- **From the green terminal:** send/file an item into a bucket (insert form, invent entry action, or hotkey target).  
- Lists can be crude (ledgerREPORT / TUI / later desk) — **intake is the product.**

### What we defer
- Full Skyline building, reception, character-skinned forms  
- Leaving terminal to “go to Oman’s office” as the only path  
- Perfect multi-service UX chrome  

### Narrative that still holds
Terminal citizens **submit reports to Skyline services** — the **route** is Skyline-shaped; the **body** stays at the green glass.  
“Send to Oman” is a **conduit**, not a teleport.

---

## 4. Place / bucket map (v0 proposal)

Stable **collector** paths (adjust slugs once; don’t churn):

```text
sys:  skyline          # house law: stick with skyline
dom:  services         # collector layer (not “you are in the building”)
room: omens            # bucket — whom = this room
mod:  (empty)          # do NOT stamp character mod from terminal submit

sys:  skyline
dom:  services
room: hymns            # song-of-songs / Jasmine’s office bucket
mod:  (empty)

sys:  skyline
dom:  services
room: secrets
mod:  (empty)          # later
```

**crate fields:** `sys/dom/room` + `kind=report` (or finer kinds). **`mod` left blank** on terminal→bucket writes.  
**meta:** `service: oman|hymn|secret` (stable service key), `source: inventOry|hotkey|…`, `source_c_uid`, `reported_at`.  
Character faces (Oman, Jasmine, teehee) are **display / later desk skin**, not required write-path mods.

Charlie: optional `report*to>oman` — place `room:` already shelves the office.

### Display names (Chester drift OK)
- invent-0rium / Oman O’mente / Song of Songs — **display**  
- `skyline/services/omens` · `skyline/services/hymns` — **bucket id**  
- Jasmine narrative (beach, cosmic romance) lives in docs/UI copy, not forced into `mod` on submit

> [!NOTE] Hands
> Jasmine owns song-of-songs room; room can be called hymns, but the **mod** is **princess jasmine**. #sorrynotsorry — sits on the beach and reads songofsongs, favorite cosmic romance series.

> [!info] Hands — solution to mod naming
> Mods might change; the **reporting office doesn’t**. Keep **mods off** when reporting from terminal — no clear working mod assigned; **just send to the bucket**.

> [!TIP] Agent
> **Locked:** terminal report dual-write stamps **`sys/dom/room` only** (`skyline/services/omens` etc.). **`mod=''`**. Whom on the form = **which room/bucket**, not which character mod. Character mods attach later when a desk/surface claims the shelf — or stay forever as flavor in meta/display only.

---

## 5. Terminal UX (stay green) — **invent-first, dual-write**

**Hands law:** every field capture lands in **invent-ORIA first**. Report is a **copy out**, not a bypass of the day.

| action | behavior |
|--------|----------|
| **Hotkey / button: new invent** | Open insert → **always** write entry to today’s invent-ory |
| **On invent form: Submit to Skyline report + whom** | **Also** copy into chosen service bucket (Oman / Jasmine hymns / …); stay on page; “witnessed” |
| **Send to Oman** (etc.) | Same dual-write if not already on invent form — or only available *from* invent form so invent is never skipped |
| **From FILES** | Optional later; rarer |

**No desk required to accept.**  
**Desk later** = list/filter/character skin over the same buckets.

> [!NOTE] Hands
> Assume entry **must** go into invent-ORIA regardless. Best: keystroke and/or button for new invent; buttons on invent form to “submit to skyline report” and **whom**. Store **both** in inventory and a **copy** to reports dept.

> [!TIP] Agent
> Locked dual-write: invent entry = source of truth for the day; report crate = `source_c_uid` → invent entry, place = service bucket. No “report-only” path in v0 unless we explicitly add an escape hatch later.

---

## 6. Relation to other pipes

| pipe | vs reports |
|------|------------|
| **invent-ory** | **Always** receives the field entry; **may** dual-write a copy to a service in the same gesture |
| **sendIT** | Sam file → **terminal practice home** (AB/JX/PI) |
| **report send** | Copy of invent entry (or rare leaf) → **Skyline service bucket** |
| **fileKeeper** | Documents; not the omen intake |
| **Charlie** | Threads after; not the filing desk |

Three “ship” verbs, different destinations — don’t merge into one vague “send.”

---

## 7. Sequence (with invent-ory / Sam)

```text
[done]    Sam FILES in fileKeeper
[plan]    invent-ory (annotate planner)
[plan]    this note — report buckets + terminal submit
[build]   invent-ory insert (required spine)
          + dual-write: “submit to Skyline” + whom (Oman first, hymns close behind)
[build]   ledger place map for skyline/services/{omens,hymns,…}
[later]   Skyline doors / character surfaces / list UIs over buckets
[later]   teeHeeSecrets + more services
```

**v0 service targets:** Oman’s Omens (prove the pipe) **and** hymns (Jasmine) soon after / in same pass if cheap — sonas run hymn-shaped constantly. Secrets later.

> [!NOTE] Hands
> Start w/ just Oman’s Omens to catch the pipe right, but **hymns will be needed too**. Sonas are horny for hymn in every place possible. #stillsorrynotsorry

> [!TIP] Agent
> Build order inside the brick: (1) invent insert + Oman dual-write, (2) add Jasmine/hymns as second whom without waiting for full desks. One form, two buckets.

---

## 8. One-line law

> **Skyline receives; the green terminal sends. Buckets before buildings. Ship at the moment of notice — do not depend on going back.**  
> **Invent first, always; report is a copy to whom (Oman, Jasmine, …).**

---

## Related

- Archive: `m/doors/--archive/DEMO/SKYLINE/reportDepartment/omansOmens.php`  
- Archive SIG: `c/--archive/DEMO/SKYLINE/--SIG-FIGS--.php` → `SIGFIG_omansOmens()`  
- Nav: reportDepartment rooms omansOmens · songOfSongs · teeHeeSecrets  
- [DAILY-INVENTORY-TOOL.md](./DAILY-INVENTORY-TOOL.md) — capture while importing  
- [SENDIT-AND-TERMINAL-SHELVES.md](./SENDIT-AND-TERMINAL-SHELVES.md) — file → terminal home  
- [TOOL-LEDGER-STORE.md](./TOOL-LEDGER-STORE.md)  
