# Crate dual-rail · IMPORT work surface · tree-core numeration

**Status:** **PLANNER WRAP / SEALED** — ready for implementation  
**Vote (hands):** refine crate storage **now** — only test data in ledger; about to actually begin.  
**Working form:** Obsidian callouts — hands `> [!NOTE]`, agent `> [!TIP] Agent reply` (edit in place; keep the thread in this file).  
**Related:** [OIX-PROTOPASS-AND-JACKS.md](./OIX-PROTOPASS-AND-JACKS.md) · [WIRE-WOLF-WOODS.md](./WIRE-WOLF-WOODS.md) · [TOOL-LEDGER-STORE.md](./TOOL-LEDGER-STORE.md) · [MOTIF-INDEX.md](./MOTIF-INDEX.md)

---

## 1. Narration: tree-cores numbered by create_time

Last sawmill scattered **dates in filenames** without a stable **imposition number**. That fights prophetic load (*“16”*).

**Rule:**

1. Walk export conversations by **`create_time` ascending** (tie-break: `conversation_id`).  
2. Assign **tree-core index** `001…N` in that order (human face).  
3. **Never** trust OpenAI UI list order — it re-sorts when you touch a chat.  
4. Keep raw glass title + conversation_id + create_time on the core row.  
5. Optional display: `016 · 2024-03-11 · {glass title}` — number is for imposition; date is for truth.

Sawmill output = **catalog of tree-cores**, not a pretty folder of undated vibes.

---

## 2. The concern: Log Yard numbers vs ledger `crate.HEX`

| OIX Log Yard | Live mypi ledger today |
|--------------|-------------------------|
| Narrative tiles: `023.009`, `WLF001.000-1-65`, `WLF001.000-1-C-58:15:99` | Opaque: `crate.6CA682D501CF3E85` |
| Human can feel order / partition / chunk | Tools need stable opaque PK |
| Shipment-facing | Machine-facing |

**Both are legitimate.** Forcing everything into random hex makes the yard soulless. Forcing everything into WLF poetry breaks tool joins and soft-delete logs.

### Proposal: dual-rail on one spine (recommended)

**One SQLite world** (`d/_LEDGER/chesters_imports.sqlite`; motif index may stay `z/` until bridged), **two public faces**:

| Rail | What | Identity |
|------|------|----------|
| **A · Operational crates** | post, chat, fileKeeper, guestcu, soper — tools | `c_uid` opaque primary key **always** |
| **B · Narrative materials** | tree-cores, IMPORT submits, log-yard mats, Nim chunks, Noah crates | `c_uid` opaque PK **plus** **`face_id` / log codes** (narrative) |


> [!NOTE] Suggestion for crate numbers supporting both:
> From a mypi Tool: MYPI-{TOOL_ID}-{SURFACE_ID}-HASH
> From miwbs WBS-{REALM_ID}-{CYC_POS}-HASH 

> [!TIP] Agent reply
> **Yes — structured face codes, not only random hex on the wall.**
> Keep **`c_uid` / internal hash** as join PK. Add a **display id** (face) that can be patterned by rail:
> - Tools: `MYPI-{tool}-{surface}-{shortHash}` (readable in TUI without being the PK)
> - miwbs: `WBS-{realm}-{cyc}-{shortHash}` when that world writes in
> - Yard: keep WLF / face_id poetry separate (`016`, `WLF016.000-2`) — same spine, different face grammar
> Hash suffix stays collision-safe; prefix is *who made it / where*. Document generators per rail so we don’t invent three formats a week.


> [!question] Dev reply - A concern--
> CRATE is serving two purposes now, potentially 3:
> 1. the sense of "This is a package of data-row stored in the ledger"
> 2. the sense of "This is a pile of data-rows related to a topic"
> 
> There was a sense before of FRAGMENT vs CRATE. That the content of any given thing is the LEAF and that LEAVES connect to BRANCHES which are the "messages" which connect to LOGS which are the .. well they are the chat logs, that one is clear. and tags are... also tags, because that is also a tree word. And that a crate was really a thing made of the leaves of the branches of logs. How does that relate to the current crate system? 
>
> In my opinion-- we are needing to rethink the base level understanding of "bits of data row" and how they relate at scales.

> [!TIP] Agent reply — scale words (do not overload “crate”)
> **You’re right: “crate” is overloaded.** Live mypi mostly uses sense (1): *one durable ledger row* (`c_uid`). OIX/yard + forestry memory also want sense (2): *assembled shipment of leaves*. Forcing both into one English word is why this hurts.
>
> ### Forest scale (narrative / IMPORT / Nim)
> | Word | Grain | Example |
> |------|-------|---------|
> | **LOG / tree-core** | Whole chat (or whole glass unit) | Core `016`, one conversation |
> | **BRANCH** | Message (or contiguous turn block) | One user/assistant node in the mapping |
> | **LEAF / fragment** | Smallest quotable bit | One sentence, one encode span, one Nim “chop” |
> | **TAG** | Threading label (tree word on purpose) | Charlie term, motif slug — *not* a row of body text |
> | **CRATE (yard sense)** | **Assembled** set of leaves(/branches) ready to ship | Noah crate, OIX “pile for Misery” |
>
> ### Ledger scale (machine)
> | Word | Grain | Example |
> |------|-------|---------|
> | **row** / **record** | One SQLite line with a PK | Avoid calling every row “a crate” in docs |
> | **`c_uid` row** | Durable unit with events/tags | Today’s `crates` table |
> | **kind** | *What scale this row claims to be* | `chat` line ≈ leaf-ish; `material` ≈ log after IMPORT; `timber` ≈ leaf/fragment; `arc`/`shipment` ≈ yard-crate |
>
> ### How they relate (honest mapping)
> - **Today’s tool “crates”** = mostly **leaves or single branches** written as rows (one post, one chat line, one file rev). Useful. Not a yard-crate.
> - **A yard CRATE** = **composition**: many leaf/timber rows + face_id, or one row whose body is a sealed bundle + child `c_uid`s in meta/lineage. Sense (2).
> - **FRAGMENT** = leaf content (or `kind=timber` / future `fragment`) — *not* synonym of operational post.
> - **LOG** = tree-core catalog + submitted **material** (post-IMPORT log), not every random `kind=post`.
>
> **Proposal for language going forward:**
> 1. In engineering: say **row / c_uid / kind** when we mean the table.
> 2. Reserve **crate** in product speech for **yard shipments** (assembled) *or* say **tool-row** vs **yard-crate** when ambiguous.
> 3. Schema: keep one `crates` table (or rename later to `records` if we must) but **kinds encode scale** — do not pretend every insert is a “crate of leaves.”
> 4. Lineage: `parent_c_uid` / `stem_c_uid` / segment list = how leaves stay on branches on logs.
>
> This is the rethink: **scale is first-class; “crate” is not the only atom.** Dual-rail is incomplete until kinds (or a `scale` field) name leaf · branch · log · yard-crate.


> [!info] Dev continue disc-
>Proposal, c_uid becomes just CHESTER_UID and is the unique id of anything stored in the system. Then we related c_uids into the types, which are just ways of saying parent and child. A chatbox session IS a whole log, with leaves. If it went to the logyard, the leaves could become branches of their own leaves. THAT is what we need to store at the base. A postbasic post is a leaf on the ground, which could turn into a branch of leaves but has no log or trunk besides the tool itself and the surface posted from. An email chain is a log of branches, just like a chat.
>
>I agree we don't nuke c_uid, we let it be just the unique id of every row, just call it the chesters import number. Perfect. But inside of that, we need to formulate very nicely this organization system to work with the tools.

> [!TIP] Agent reply — CHESTER_UID + composition (base model)
> **Yes. This is the clean base.**
>
> ### Identity
> | Name | Role |
> |------|------|
> | **CHESTER_UID** (today’s `c_uid` column, rename in speech / docs; column can stay `c_uid` or migrate to `chester_uid` later) | **Only** job: unique id of **any** stored row in Chester’s Imports spine |
> | **face / log_code / MYPI-…** | Human-facing labels — never the PK |
>
> Not “crate id.” **Chester’s number for a thing.** Soft-delete, events, tags, TPS all hang off CHESTER_UID.
>
> ### Composition (parent / child), not overloaded “crate”
> Every row has:
> - **CHESTER_UID**
> - **kind** (tool or yard vocabulary: `chat`, `post`, `material`, `timber`, …)
> - **scale** (or kind→scale map): `leaf` | `branch` | `log` | `yard_crate` | …
> - **parent_chester_uid** (nullable) — immediate container
> - optional **stem / root** for “same tree” (file revs already do stem; generalizes)
>
> **Types = how rows nest**, not a second PK system.
>
> ### Tool examples (your mapping)
> | Tool thing | Scale | Parent |
> |------------|-------|--------|
> | **chatBOX session** | **log** (session header or first class session row) | place / surface |
> | chat line | **leaf** (or branch if we treat a turn as branch) | session log |
> | If session → Log Yard | leaves may be **re-parented / re-scaled** into branches with their own child leaves (Nim chops) | material log |
> | **postBASIC post** | **leaf on the ground** | no log trunk except tool + sky place (parent = place or null) |
> | post that grows | can become **branch** with child leaves later | optional |
> | **email chain** | **log** of **branches** (messages) | same pattern as chat |
> | fileKeeper rev | leaf/rev under **stem** (file identity) | stem_c_uid pattern |
>
> ### Yard
> | Thing | Scale |
> |-------|--------|
> | submitted material | **log** (processed) |
> | Nim timber / fragment | **leaf** (parent = material) |
> | Noah shipment “crate” | **yard_crate** = **assembly** of child CHESTER_UIDs (composition row), not “every row is a crate” |
>
> ### What tools must implement
> 1. Mint **CHESTER_UID** on write.  
> 2. Set **kind + scale + parent** honestly (chat line parent = session; post parent = null/place).  
> 3. Display face optional.  
> 4. Never require yard poetry for operational tools.
>
> ### Migration note
> Keep column name `c_uid` in SQLite for a while if cheaper; **document as CHESTER_UID / Chester number**. Rename column when we reset test DB. Prefix `crate.` on ids can become `ch.` or stay hex-only — cosmetic.
>
> **Thesis:** One number for every thing. Nesting tells scale. “Crate” is only one scale (assembled shipment), not the name of the table’s soul.


> [!success] Dev confirmed
> Almost agreed with some Small notes; We can treat anything not fragmented as a branch if desired, but to me a body of text is a leaf of text, so in this way the content of the material is always its leaf. A log contains the total of all leafs, and each subsequent breakdown contains its smaller parts.
> 
>  I know we do not need to STORE the completeness in the ledger that way UNLESS IT MAKES SENSE LOGISTICALLY, but that is my working mental model. The tree is never broken down to make parts, its parts become instances of smaller factions of itself. How we store that, thats the challenger in my mind, but might be very logical and easy in yours.

> [!TIP] Agent reply — instances of fractions (storage is easy if we mean this)
> **Mental model locked:** the tree is **not butchered**. Parts are **new instances** that still know the whole.
>
> ### Content is always leaf-shaped
> - A body of text = **a leaf of text** (the quotable payload).
> - A **log** = container whose *meaning* is the **total of its leaves** (and nested fractions).
> - Breakdowns (IMPORT segments, Nim chops, encode spans) = **smaller leaves** (or leaves under intermediate nodes), each an **instance of a fraction** of a parent leaf/log — not a hole punched in the parent.
>
> ### How to store without storing “the whole tree twice” unless useful
> | Pattern | When |
> |---------|------|
> | **Parent keeps full body** (immutable glass → material body after apply) | Default for submitted material log |
> | **Child rows** = CHESTER_UID + parent + **span** (start/end or segment id) and/or **copied excerpt** | Nim timber, hand splits, redaction blocks |
> | **No delete of parent text** when child is born | Instance, not amputation |
> | **Optional denormalized copy** of excerpt on child | Logistics (search, ship without loading parent) — fine; lineage still required |
> | **Do not** require every message of a chat as its own row on day one | chatBOX may store leaves as lines; glass import may store one material leaf + segment table until Nim runs |
>
> ### Scale words refined
> - **leaf** = unit of *content* (body text / fragment).  
> - **log** = *collection identity* (session, material, email chain) — may hold body *and/or* children.  
> - **branch** = optional mid-scale (message, segment folder); use when a named middle helps; not mandatory for every tool.  
> - **yard_crate** = assembly of child CHESTER_UIDs for shipment.
>
> ### Logistics rule
> Store **completeness** only where it pays:
> - Always: parent link + scale + enough payload to use the row.  
> - Not always: full recursive materialization of every fraction.  
> Re-derive “all leaves under log” by walking children (or span index) when needed.
>
> **One line:** *Fraction instances point home; the trunk stays standing.*


> [!success] Dev confirmed
> AGREED. Branch becomes just a label for a middle message broken into further leaves. So log -> leaves at first, then a nim split leaf becomes a branch of its leaves. We are good. 

> [!TIP] Agent reply — final grain
> **Agreed and sealed.**  
> `log → leaves` first. When Nim (or IMPORT) splits a leaf into smaller leaves, **that former leaf is re-labeled branch** (container of its children) — still the same CHESTER_UID / instance of a fraction, not a new philosophy. Branch is a **role a node earns** when it has child leaves, not a mandatory middle layer everywhere.
>
> **Planner wrap.** No further callouts required on this doc unless implementation surfaces a real conflict.


Shared rules:

- Every durable row has a **CHESTER_UID** (`c_uid` today) — joins, events, soft-delete, TPS.  
- Rows **nest**: parent/child (+ stem); **kind/scale** say leaf · branch · log · yard_crate.  
- Narrative rail **adds** display identifiers humans speak:  
  - `face_id` e.g. `016` or `016.2` (core · partition)  
  - `log_code` e.g. `WLF016.000-2` (yard convention — refine, don’t invent weekly)  
  - `glass_title` (original title; may be private)  
  - `yard_title` (bonus name after privacy)  
- **Tools never require face_id.** Charlie/tags still use strings; place still place.  
- **Yard UI never leads with hex.** Show face_id / log_code; Chester number in inspector.


> [!NOTE] Considerations on titles and splitting
> Glass title should load as the first title inside of the importer, and can be renamed. When sectioning material, each section gets a new title field. The glass title is the default fill when first opening the log to import.

> [!TIP] Agent reply
> **Locked as IMPORT UX default:**
> 1. Load core → **title field = glass_title** (editable → becomes working / yard title).
> 2. Split → each segment gets its **own title** (empty or copy-from-parent on create; not forced to stay glass).
> 3. Persist both: `glass_title` (provenance, may stay private) and `yard_title` / segment titles (what the yard shows after submit).
> Glass never destroyed by rename — only the working title moves.


> [!NOTE] Dev reply
> Agreed. Prime important thing for me `do not edit the original json sawmilled chat file`. If SAM saves her progress before exporting, store the WIP as a temp file. When SAM exports the log, submit new json files to the sawmill named and shaped according to the export json. Never alter the conversations.jsons raw, nor their sawmilled individual chats.

> [!TIP] Agent reply — glass immutability
> **Locked.**
> | Layer | Mutable? |
> |-------|----------|
> | Thunderdome / raw export JSON | **Never** |
> | Sawmilled tree-core files (per chat) | **Never** (read-only source) |
> | IMPORT WIP | **Yes** — temp only (`z/…/wip/` or job id), not overwriting cores |
> | Submit / export out of IMPORT | **New** artifacts (ledger material row + optional export JSON shaped for yard/sawmill) |
>
> Progress save = write WIP sidecar. Resume = load core (immutable) + WIP overlay. Submit = mint new material; cores stay pristine for re-import or audit.


> [!success] Dev confirmed
> AGREED.

### Relation diagram

```text
tree_core (catalog, z/ or table)
   face_id 016 · create_time · conversation_id · glass_title
        │
        ▼ IMPORT load → work set (redact / encode / split)
        │
        ▼ submit
material crate (kind=material | log_material)
   c_uid + face_id + segments + redaction map + encode book ref
        │
        ├── Nim chunk crates (kind=timber | thought_bit)  parent = material
        └── Noah arc/crate later (kind=arc | shipment)    children = chunks
```

Operational kinds (`post`, `chat`, `file`, …) stay **Rail A**.  
Log Yard kinds stay **Rail B**.  
Bridge later: Charlie tag `face:016` or motif slug ↔ material — optional, not day one.

### What to refine *now* (while test data is throwaway)

1. **CHESTER_UID** (`c_uid`) stays as **the** PK for every row — not replaced by WLF.  
2. **Composition fields:** parent_chester_uid + scale (or kind→scale map); keep/generalize `stem` lineage.  
3. Add **`face_id`** (nullable) for yard / optional tool display codes.  
4. Document **kind families** + scale:  
   - *tool:* `post` (leaf) `chat` (leaf under session-log) `file` `guestcu` `soper` …  
   - *yard:* `material` (log) `timber` (leaf) `yard_crate` / `arc` (assembly) …  
5. Soft-delete / history: same machinery for all CHESTER_UID rows.  
6. **Reset test ledger** when schema lands (ok per hands).  
7. TUI: face_id when present; Chester number secondary.

**Not required day one:** full WLF generator parity with OIX — only **fields + kinds + scale rules**.

---

## 3. IMPORT work surface (encode · alias · split · redact)

### 3.1 Encode (VEN / privacy code)

Highlight a span (word or phrase) → **Encode**:

| Field | Rule |
|-------|------|
| **code** | Auto-generated **VEN-style** code (e.g. `HJI-048`) **or** operator-written |
| **alias** | Public face string (e.g. `Haji`) |
| **original** | Stored **only** in private encode book (never on default export) |
| **scope** | This material / this import job (default); later global book optional |

**On export / submit printout:**

- All occurrences of **original** → **alias** (whole-word / phrase rules TBD).  
- Encode book retains `code → alias → original` for recovery and Log Yard “reveal” modes.  
- **Default surfaces:** original names **gone** — only alias (+ optional code in inspector).


> [!NOTE] Added context:
> Yes, noting that the current Terminal IO in EDN doesn't auto apply on load, but lets you click a button to apply all definitions to aliases. That is also fine. I can click a button.

> [!TIP] Agent reply
> **Prefer explicit Apply** (EDN-style) over silent rewrite-on-load.
> - Encode book accumulates definitions while you work.
> - Body stays raw until **Apply aliases** (or Apply this / Apply all).
> - Submit runs apply (or requires applied state) so Log Yard never gets half-encoded glass by accident.
> Undo / re-apply from book keeps control with SAM.


> [!success] Dev confirmed
> AGREED.


DB sketch (private, careful access):

```text
encode_book(
  book_id, material_c_uid or job_id,
  code, alias, original,
  created_at, actor
)
```

Global book (cross-material “Shane always Haji”) = **phase 2**, with merge UI — dangerous if automatic.


> [!NOTE] Security of files
> Yes -- this DB is in z/ and is not part of the prime ledger. See concepts of Ven Registary in my pocket internet early tools and chesters-toy-box concepts for the idea of private storage of VEN material original aliases and the idea that all entities in the story will have codes just like crates do (see miwbs concepts for "all things are ven" and documentation in D:\repos\aidm-dream-machine) for random fractal examples of trying to make this code structure. SDK-808 for example IS sam rosewaters ven code.

> [!TIP] Agent reply
> **Strong yes — encode / VEN registry ≠ prime ledger.**
> | Store | Holds |
> |-------|--------|
> | `d/_LEDGER/chesters_imports.sqlite` | Public-ish spine: body as **aliased/redacted-safe**, face ids, kinds, Charlie |
> | `z/…` VEN / encode registry (gitignored) | **original ↔ code ↔ alias**, reveal keys, private maps |
>
> “All things are ven” + crates having codes = same instinct: **identity can be coded without putting the true name on the public shelf.** SDK-808 as Sam’s ven is the pattern for person-entities; IMPORT encode is the same muscle for *this material’s* names.
> Prime ledger may store **code + alias only** on material meta; **original** only in z/ registry.
> Follow chesters-toy-box / ven-minter / AIDM as **fuel for code shape**, not a hard dependency on day one.


> [!success] Dev confirmed
> AGREED.

### 3.2 Split logs by hand

- Operator marks **segment boundaries** in the loaded core.  
- Names each segment (yard_title / segment title).  
- Submit may produce **one material with segments** or **N materials** with face_id `016.1`, `016.2` — **prefer one material + ordered segments** first (easier encode book scope), export can still emit multiple files.

### 3.3 Redact chunks (black bars)

| Store | Show |
|-------|------|
| DB keeps redacted text (encrypted-at-rest later if needed) | **Default print / export / copy mats:** black bars only |
| Redaction spans: start/end or block ids | **Log Yard toggle:** optional “show redactions” for SAM with key |
| | **Any other consumer** (copy, Jacks, books unless unsealed): **bars only** |

Rules:

- Redact is **not** the same as encode. Encode = rename entity. Redact = hide content.  
- Copy-paste from mats = **clean redaction blocks** (bars or `█` / `[REDACTED]`), never silent original.  
- Soft-delete of a material does not orphan encode book without policy (cascade or retain).

### 3.4 Work surface product shape (v1)

```text
[ catalog: type number → load core ]
[ body: select text ]
    → Encode… (code auto/manual + alias)
    → Redact block
    → Split here (name segment)
[ annotation notes ]
[ Submit → material crate(s) + books + segments ]
```

Tool pack: grow under `t/tools/` (e.g. `logImport` / `protopass`) installed on **Terminal I/O · IMPORT** — not fileKeeper-as-markdown-desk forever (fileKeeper may stay for free files).

---

## 4. Discussion locks (propose defaults)

| Topic | Proposed default | Open? |
|-------|------------------|-------|
| Refine crates now | **Yes** — test data disposable | Hands vote YES |
| Opaque id | **CHESTER_UID** (c_uid) forever as PK of any row | rename column optional |
| Composition | parent + scale; types = nesting | LOCKED direction |
| Narrative face | **`face_id` + log_code** on yard kinds | Exact WLF grammar open |
| Same DB as tools | **Yes** one ledger spine; kinds/scales separate | |
| Motif index | Stays `z/motif_index.sqlite` until Pass 0–4; bridge later | |
| Encode book | **`z/` VEN registry** (not prime ledger); originals never default-export | Global book later |
| Tool face ids | Optional `MYPI-{tool}-{surface}-{hash}` display | Exact TOOL_ID table open |
| Apply aliases | **Button**, not auto-on-load (EDN) | LOCKED |
| Redaction reveal | Log Yard only + auth; mats always barred outside | |
| Split | Segments on one material first | Multi-material face_id later |
| Tree-core catalog | Number by create_time; store in z/ or `tree_cores` table | |
| Glass / sawmill files | **Immutable**; WIP sidecar; submit = new artifacts | LOCKED |
| Scale vocabulary | leaf · branch · log · yard_crate; “crate” = assembly only | LOCKED |
| Content grain | **body text is always leaf-shaped**; log = sum of leaves | LOCKED |
| Breakdown | **instances of fractions** (parent stays); not destructive split | LOCKED |
| Store completeness | only when logistics need it; else parent + span/children walk | LOCKED |
| chat session | **log** of leaves | LOCKED |
| postBASIC post | **leaf on ground** (place as weak trunk) | LOCKED |
| VEN registry in z/ | Confirmed | LOCKED |

---

## 5. Implementation order (discussion sealed → build)

```text
0. ✅ Agree dual-rail + CHESTER_UID + scale + glass immutability + IMPORT surface (this doc)
1. ✅ Schema v3 + ledger file chesters_imports.sqlite (PHP + Python)
2. ✅ Reset / fresh DB on first connect after rename
3. ✅ Union catalog OT+NT: 893 cores by create_time, `testament_tag` OT|NT|OT+NT (`z/logs/tree_cores/`)
4. 🔧 Terminal IMPORT v1: logImport tool — load N, view thread, WIP notes (encode/redact/submit next)
5. z/ VEN encode registry (originals out of prime ledger)
6. Log Yard surface (OIX shape, list by face_id)
7. Nim/Noah automats later
```

---

## 6. One-line thesis

**Every stored thing is a CHESTER_UID. Content is leaf; logs collect leaves; fractions are new instances that point home. Faces are for humans. IMPORT turns immutable glass into safe wood; the Log Yard only holds what was submitted.**

---

## 7. Seal

**Wrapped.** Callout thread closed. Final grain: log→leaves; split leaf becomes **branch of its leaves**.  

Next workstream: **§5 step 1** — schema bump (CHESTER_UID, parent, scale, face_id, spans) + test ledger reset — when hands says build.
