# Place naming (no sys / dom / mod)

**EDN impulse was right:** main words should not carry domination/modification narrative.  
`sys` / `dom` / `mod` are **retired from the ledger vocabulary** even if old PHP still uses them internally.

| Old (avoid in new code) | Prefer |
|-------------------------|--------|
| sys_slug / SYS | **surface** or **destination** (where the site/world face is) |
| dom_slug / DOM | **district** or fold into **place_path** |
| room_slug | **room** |
| mod_slug | **voice** / **mask** / omit until needed |

**Ledger v0:** store a single **`place_path`** string (e.g. `starline/offices/frontdesk`) plus optional **`place_label`**.  
No required three-letter hierarchy. Tags from place can be derived from path segments without calling them sys/dom/mod.

Old `import_env` JSON may still appear in archives; new writes do not require those keys.
