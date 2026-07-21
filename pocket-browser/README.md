# Pocket browser

Feel **inside** the pocket internet — a dedicated window on your XAMPP world, not Chrome’s shell.

## Run

```bat
cd C:\Builds\my-pocket-internet\pocket-browser
pip install pywebview
python pocket_browser.py
```

Optional shortcut later: desktop `.bat` that runs the same.

## Needs

- Apache/XAMPP running  
- **One host only for daily use:** `b` → DocumentRoot `…/b` (see `docs/B-FRONT.md`)  
- Junction: `htdocs\my-pocket-internet` → `Builds\my-pocket-internet`  
- Do **not** add a hosts line per new surface — open `http://b/{sys}/…` (pocket chrome hides the host leadline; title is path-only)

Optional: `go.edn` stays a separate stack if you still open Terminal IO that way.

## Git note

- **This stripped mypi:** `github.com/kitten-lab/mypi` (work here)  
- **Older fuller archive:** `silo-my-pocket-internet` (or similar) — leave alone; keep this one running  

## Next

Wire postBASIC → ledger; Skyline DOM **News** as headlines. Browser is the door; store is trust.
