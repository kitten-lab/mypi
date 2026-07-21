-- mypi ledger v1 — single file store for postBASIC-class cargo
-- No sys/dom/mod columns. Place is place_path (+ optional label).

PRAGMA journal_mode = WAL;

CREATE TABLE IF NOT EXISTS ledger_meta (
  key TEXT PRIMARY KEY,
  value TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS crates (
  c_uid TEXT PRIMARY KEY,
  kind TEXT NOT NULL DEFAULT 'post',
  topic TEXT NOT NULL DEFAULT '',
  body TEXT NOT NULL DEFAULT '',
  agent TEXT NOT NULL DEFAULT 'user',
  tool TEXT NOT NULL DEFAULT 'postBASIC',
  tool_version INTEGER NOT NULL DEFAULT 1,
  place_path TEXT NOT NULL DEFAULT '',
  place_label TEXT NOT NULL DEFAULT '',
  sys TEXT NOT NULL DEFAULT '',
  dom TEXT NOT NULL DEFAULT '',
  room TEXT NOT NULL DEFAULT '',
  mod TEXT NOT NULL DEFAULT '',
  tags_json TEXT NOT NULL DEFAULT '[]',
  tags_raw TEXT NOT NULL DEFAULT '',
  event_unix INTEGER,
  ingest_unix INTEGER NOT NULL,
  timezone TEXT NOT NULL DEFAULT '',
  t_uid TEXT NOT NULL DEFAULT '',
  meta_json TEXT NOT NULL DEFAULT '{}',
  created_at INTEGER NOT NULL,
  updated_at INTEGER NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_crates_ingest ON crates(ingest_unix DESC);
CREATE INDEX IF NOT EXISTS idx_crates_place ON crates(place_path);
CREATE INDEX IF NOT EXISTS idx_crates_kind ON crates(kind);

CREATE TABLE IF NOT EXISTS crate_events (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  c_uid TEXT NOT NULL,
  event_type TEXT NOT NULL,
  payload_json TEXT NOT NULL DEFAULT '{}',
  actor TEXT NOT NULL DEFAULT '',
  place_path TEXT NOT NULL DEFAULT '',
  event_unix INTEGER,
  ingest_unix INTEGER NOT NULL,
  tool TEXT NOT NULL DEFAULT '',
  FOREIGN KEY (c_uid) REFERENCES crates(c_uid)
);

CREATE INDEX IF NOT EXISTS idx_events_cuid ON crate_events(c_uid, id);

CREATE TABLE IF NOT EXISTS tag_map (
  c_uid TEXT NOT NULL,
  tag TEXT NOT NULL,
  PRIMARY KEY (c_uid, tag),
  FOREIGN KEY (c_uid) REFERENCES crates(c_uid)
);

CREATE INDEX IF NOT EXISTS idx_tag_map_tag ON tag_map(tag);

CREATE TABLE IF NOT EXISTS tps_shelves (
  tps_uid TEXT PRIMARY KEY,
  window_unix INTEGER NOT NULL,
  window_seconds INTEGER NOT NULL,
  clock_id TEXT NOT NULL DEFAULT 'gaia',
  facets_json TEXT NOT NULL DEFAULT '{}',
  created_at INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_tps_window ON tps_shelves(window_unix DESC);
CREATE TABLE IF NOT EXISTS tps_attach (
  tps_uid TEXT NOT NULL,
  c_uid TEXT NOT NULL,
  kind TEXT NOT NULL DEFAULT 'post',
  seq INTEGER NOT NULL DEFAULT 0,
  attached_at INTEGER NOT NULL,
  PRIMARY KEY (tps_uid, c_uid)
);
CREATE INDEX IF NOT EXISTS idx_tps_attach_crate ON tps_attach(c_uid);
CREATE TABLE IF NOT EXISTS thread_edges (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  c_uid TEXT NOT NULL,
  from_term TEXT NOT NULL,
  rel TEXT NOT NULL,
  to_term TEXT NOT NULL,
  ingest_unix INTEGER NOT NULL,
  sys TEXT NOT NULL DEFAULT '',
  dom TEXT NOT NULL DEFAULT '',
  room TEXT NOT NULL DEFAULT '',
  mod TEXT NOT NULL DEFAULT ''
);
CREATE INDEX IF NOT EXISTS idx_edges_from ON thread_edges(from_term);
CREATE INDEX IF NOT EXISTS idx_edges_to ON thread_edges(to_term);
CREATE TABLE IF NOT EXISTS thread_terms (
  term TEXT PRIMARY KEY,
  gravity INTEGER NOT NULL DEFAULT 0,
  updated_at INTEGER NOT NULL
);
