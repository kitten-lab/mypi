# mypi ledger + TUI

**Trust the store before real thoughts.**

## Run TUI

```bat
cd C:\Builds\my-pocket-internet\ledger
pip install textual
python mypi_tui.py
```

## What it is

| Piece | Path |
|-------|------|
| SQLite DB | `d/_LEDGER/mypi.sqlite` |
| Schema | `schema.sql` |
| Python API | `mypi_ledger.py` |
| Viewer | `mypi_tui.py` |

## Naming

No **sys / dom / mod** in the ledger. Place is `place_path` (e.g. `starline/offices/frontdesk`) + optional `place_label`.  
See `docs/PLACE-AND-NAMING.md`.

## Trust drill

1. Open TUI → **Add demo crate**  
2. Select it → see place + tags (`place:…`, `@segment`)  
3. **Add tag trusted** → history shows `tag_add`  
4. **Edit body** → history shows `set_body` with old/new  
5. DB file alone is the backup  

## Next

Wire postBASIC MakePost → this ledger (same fields, no room JSON slips).
