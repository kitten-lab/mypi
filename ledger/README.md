# Chester's Imports ledger + TUI

**Trust the store before real thoughts.**  
**Plan:** `mypi docs/CRATE-DUAL-RAIL-AND-IMPORT-WORK.md` (sealed).

## Run TUI

```bat
cd C:\Builds\my-pocket-internet\ledger
pip install textual
python mypi_tui.py
```

## What it is

| Piece | Path |
|-------|------|
| SQLite DB | `d/_LEDGER/chesters_imports.sqlite` |
| Schema | `schema.sql` (v3) |
| Python API | `mypi_ledger.py` |
| PHP API | `k/systems/ledger/Ledger.php` |
| Viewer | `mypi_tui.py` |

## CHESTER_UID + scale

- **`c_uid`** = CHESTER_UID (minted `ch.HEX…`) — every stored row  
- **`scale`** = `leaf` | `branch` | `log` | `yard_crate`  
- **`parent_c_uid` / `stem_c_uid`** = composition (instances of fractions, not butchered trees)  
- **`face_id`** = human tile (yard / optional tool face)  

## Naming (place)

Place is still `place_path` + sys/dom/room/mod columns.

## Trust drill

1. Open TUI → **Add demo crate**  
2. Select it → see place + tags (`place:…`, `@segment`)  
3. **Add tag trusted** → history shows `tag_add`  
4. **Edit body** → history shows `set_body` with old/new  
5. DB file alone is the backup  

## Next

Wire postBASIC MakePost → this ledger (same fields, no room JSON slips).
