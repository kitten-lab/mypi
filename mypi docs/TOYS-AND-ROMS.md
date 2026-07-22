# Toys / ROMs — system-wide (any surface)

Not a WWW feature. Surfaces only **place** packages, like `getTool`.

## Sky install

```php
romStage();
placeToy('MRA-001', 'Julie');
placeToy('KCD-001', 'ClassicBoi');
// optional: showToyCatalog();
```

| Call | Role |
|------|------|
| `romStage()` | Stage + multi-window host (`RomHost`) + catalog JSON for JS |
| `placeToy($id, $shell)` | Cover + kit + dress-up (wraps `displayToy`) |
| `listToyShells($id)` | Shells from `t/toys/{id}/dressUps/*_SHELL.box.php` |
| `scanToyCatalog()` | Disk scan + merge `t/toys/_catalog.json` |
| `showToyCatalog()` | Human list in the room |

## Package layout

```text
t/toys/{VENCODE}/
  {VENCODE}.kit.js          # registers RomHost.register(id, buildFn)
  {VENCODE}.viz.css
  dressUps/
    {VENCODE}_JULIE.box.php
    {VENCODE}_JULIE.viz.css
    {VENCODE}_CLASSICBOI.box.php
    ...
t/toys/_catalog.json        # optional titles/notes (shells come from disk)
```

## Multi-window

- Covers call `ToggleMRA001()` / `RomHost.toggle('MRA-001', { title })`
- Each open ROM gets its own `.rom-window` on `#rom-stage`
- Drag title bar, focus, close (×) — `k/kittens/romWindow.*`

## Catalog (not Dewey)

Dewey tag catalogs are a different machine. **Toy catalog** is intentionally simple:

1. **Disk is truth** for which shells exist (scan dressUps).
2. **`_catalog.json`** only for human titles / notes / provider.
3. No need to hand-maintain shell lists unless you want override later.

## Later

Tool-in-ROM (blog-in-a-box): kit `build` mounts a tool into the window body.
