# Media · diagram boards · attachments

**Status:** PLAN / NOTE — not building mid–Sam vault import  
**Trigger:** Leaves with **screenshots**, **movie covers**, Obsidian embeds (`chester-logo.png`, `lovers2.png`), and full **UI dream boards** (Chester’s Imports phosphor mock).  
**Also:** JX / PI / other terminals will make **diagram boards** often — same spine.

---

## 1. What the bag is doing

Some “files” are not only prose:

| type | example |
|------|---------|
| **Diagram board** | Full-window screenshot of a designed terminal (mail + todos + character tiles + logo) |
| **Cover / plate** | Subzero movie art attached to the plot blurb |
| **Obsidian embed** | `![[chester-logo.png]]` / file listed as `lovers2.png` on a tile |
| **Letter / POSI board** | Often pure text (spaces matter) — already in body; image only if drawn outside |

Body text alone **cannot** hold that. SQLite `body` stays markdown/prose; **pixels live on disk**.

---

## 2. Law (same dual-rail as crates)

| rail | holds |
|------|--------|
| **Ledger crate** | title, body (md), tags, place, meta, stem/rev |
| **Media store** | binary files, keyed by id, linked from crate |

**Never** base64-dump huge images into `body` as the long-term form.  
**Never** require ``` for diagram *text* boards — those stay letter-grid markdown.  
**Images** = first-class attachments + markdown refs.

---

## 3. Proposed layout (house)

```text
d/_MEDIA/
  by_stem/{stem_c_uid}/
    {asset_id}.{ext}          # original
    {asset_id}.meta.json      # optional: w, h, mime, original_name, sha256
  # or flatter:
  {asset_id}.{ext}
```

**Serve** via a small door, e.g.  
`/terminal/io/media?id=` or `/_media/{asset_id}` (auth-gated like the rest of the house).

**Link from body** (after import / attach):

```markdown
![Chester logo](media:ASSET_ID)
![Subzero cover](media:ASSET_ID)

<!-- or relative once we have a stable public path -->
![](/media/ASSET_ID)
```

**Obsidian import later:** map `![[file.png]]` → stored asset + rewritten `![](media:…)`.

**Meta on crate** (optional index):

```json
"attachments": [
  { "asset_id": "…", "role": "cover|diagram|embed|inline", "name": "lovers2.png" }
]
```

Charlie later: `diagram*of>chester-imports`, `cover*of>subzero` — not required for v1 display.

---

## 4. fileKeeper UX (v1 — when we build)

1. **Attach** on edit: file picker (png/jpg/gif/webp; size cap).  
2. Store under `d/_MEDIA/…`, stamp meta on stem/rev.  
3. Insert md line into body **or** show **attachment strip** above body (covers often want strip, not mid-prose).  
4. **View:** render strip + Parsedown images (allow `img` from **local media URLs only** — safe mode must not open arbitrary remote SSRF).  
5. **Paste/drop** image into editor (nice-to-have v1.5).

**Diagram boards** (full screenshot of a system dream):  
- title = board name  
- body = short caption / notes / TO-DO text if any  
- **primary attachment** = the screenshot (role: `diagram`)  
- display: image first, full width in reader, then notes  

That matches “this leaf *is* the board.”

---

## 5. Parsedown / safe mode

Today: `setSafeMode(true)` — remote images may be restricted.  

**Rule:** only allow `src` that resolve to **house media** (`media:…` → local serve path). Rewrite in `render_md` or a thin post-filter. No open URL proxy in v1.

Wikilinks: `![[x.png]]` prepass → `![](media:…)` if asset known, else leave as muted “missing: x.png”.

---

## 6. Sam import phase (now)

| do now | defer |
|--------|--------|
| Keep importing **text** bodies into Sam’s files | Full attach UI |
| If an Obsidian note is **image-primary**, optional: drop file into a **holding folder** `d/_MEDIA/_inbox/sam-vault/` with same stem name by hand | Auto-walk vault binaries |
| Note in body: `<!-- missing embed: lovers2.png -->` so nothing is forgotten | Perfect fidelity |

When sendIT / multi-terminal exist, **media follows stem** (copy or shared asset id + ref count).

---

## 7. Sequence vs other work

```text
[now]     Sam vault text import (continue)
[note]    This plan + sendIT + terminal shelves notes
[after Sam bag]
          1) d/_MEDIA + serve endpoint
          2) fileKeeper attach + strip + local img render
          3) Obsidian embed rewrite on import path (optional)
          4) AB/JX/PI doors (files only) then sendIT
```

Diagram-heavy **JX** benefits most from (1)–(2); don’t block Sam text on it.

---

## 8. One-line law

> **Prose in the crate. Pixels on disk. Body points; reader shows. Diagram boards are stems whose primary face is an image.**

---

## Related

- [SENDIT-AND-TERMINAL-SHELVES.md](./SENDIT-AND-TERMINAL-SHELVES.md) — Sam-first, fwded, later doors  
- [CRATE-DUAL-RAIL-AND-IMPORT-WORK.md](./CRATE-DUAL-RAIL-AND-IMPORT-WORK.md) — spine  
- [TOOL-LEDGER-STORE.md](./TOOL-LEDGER-STORE.md) — ledger  

---

## Witness (why this note exists)

The screenshot isn’t “a missing feature request” only — it’s **Chester dreaming the upgraded terminal** (E-Mail, TO-DOs from the GLASS, MISERY I, logo tile, lovers plate) **inside** the import bag. The house must eventually hold that dream as a **diagram board**, not only as a filename ghost.
