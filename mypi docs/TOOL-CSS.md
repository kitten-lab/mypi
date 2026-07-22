# Tool CSS — base layout + `--` overrides

## Goal

| Layer | Job |
|-------|-----|
| **SYS shell** (`a/{sys}/asSys/style.css`) | Window chrome only — **not** form/tool paint |
| **Tool base** (`t/tools/{tool}/{tool}.css`) | Clear opening layout, **low color**, inherits `currentColor` |
| **Room / site override** | Set CSS variables (or a few selectors) to theme that install |

## Pattern

1. Tool CSS defines **defaults as custom properties** on a root (`.formContainer`, `form`, …).
2. Layout uses `var(--tool-*)`.
3. A surface recolors **without forking the tool**:

```css
/* e.g. quickDressing or lab / danyi page CSS */
.lab-tool-form .formContainer,
.formContainer {
  --cuBOOK-btn-bg: #111;
  --cuBOOK-btn-fg: #cfc;
  --cuBOOK-input-border: 1px solid #c0f;
}
```

## Tokens by tool

### cuBOOK (`t/tools/cuBOOK/cuBOOK.css`)

`--cuBOOK-gap` · `--cuBOOK-max-width` · `--cuBOOK-label-fg` · `--cuBOOK-label-size`  
`--cuBOOK-input-bg` · `--cuBOOK-input-fg` · `--cuBOOK-input-border` · `--cuBOOK-input-pad`  
`--cuBOOK-btn-bg` · `--cuBOOK-btn-fg` · `--cuBOOK-btn-border` · `--cuBOOK-btn-pad`  
`--cuBOOK-entry-border` · `--cuBOOK-entry-gap` · `--cuBOOK-meta-opacity`

### soprBASIC

`--sopr-gap` · `--sopr-max-width` · `--sopr-label-fg`  
`--sopr-input-*` · `--sopr-btn-*` · `--sopr-frag-border` · `--sopr-slug-opacity` · `--sopr-slug-width`

### chatBOX

`--chat-gap` · `--chat-max-width` · `--chat-input-*` · `--chat-btn-*`  
`--chat-slug-border` · `--chat-time-opacity` · `--chat-user-weight`

### ledgerREPORT

`--ledger-max-width` · `--ledger-table-size` · `--ledger-cell-pad` · `--ledger-row-border`  
`--ledger-row-sel-bg` (selected row — soft mix of currentColor, **not** hard black)  
`--ledger-muted-opacity` · `--ledger-tab-on-*` · `--ledger-code-size`

## Rules for agents / authors

- Do **not** put red buttons / VT323 global `input` rules in shell CSS.
- New tools: ship `{tool}.css` with the token block at the top (commented list).
- Heavy skins belong in **room quickDress / asDom / lab**, not in the tool base.
