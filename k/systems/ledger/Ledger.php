<?php
/**
 * Chester's Imports ledger — single SQLite store (shared with Python mypi_tui).
 * CHESTER_UID = c_uid column (every stored row). Scale + parent = composition.
 * DB: d/_LEDGER/chesters_imports.sqlite
 * Plan: mypi docs/CRATE-DUAL-RAIL-AND-IMPORT-WORK.md
 */

if (!function_exists('mypi_ledger_path')) {
    function mypi_ledger_path() {
        if (!defined('echoSONAR')) {
            throw new RuntimeException('echoSONAR not defined');
        }
        $dir = rtrim(str_replace('\\', '/', echoSONAR), '/') . '/d/_LEDGER';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $primary = $dir . '/chesters_imports.sqlite';
        // one-time: prefer renamed house file; fall back to legacy mypi.sqlite if present
        if (!is_file($primary) && is_file($dir . '/mypi.sqlite')) {
            return $dir . '/mypi.sqlite';
        }
        return $primary;
    }

    /** Default scale for a kind (leaf | branch | log | yard_crate). */
    function mypi_ledger_default_scale($kind) {
        $k = strtolower(trim((string) $kind));
        $map = [
            'post' => 'leaf',
            'chat' => 'leaf',
            'guestcu' => 'leaf',
            'soper' => 'leaf',
            'file' => 'leaf',
            'folder' => 'log',
            'material' => 'log',
            'log_material' => 'log',
            'timber' => 'leaf',
            'thought_bit' => 'leaf',
            'fragment' => 'leaf',
            'session' => 'log',
            'dailylog' => 'log',
            'dailylog_entry' => 'leaf',
            'report' => 'leaf',
            'dossier_person' => 'leaf',
            'dossier_faction' => 'log',
            'dossier_membership' => 'leaf',
            'dossier_note' => 'leaf',
            'shot_card' => 'leaf',
            'headline' => 'leaf',
            'codex_entry' => 'leaf',
            'arc' => 'yard_crate',
            'shipment' => 'yard_crate',
            'yard_crate' => 'yard_crate',
            // Terminal I/O · awareness notes (dual-write from private sidecars)
            'log_export' => 'leaf',
            'ven_ship' => 'leaf',
            'ven_add' => 'leaf',
            'ven_modify' => 'leaf',
        ];
        return $map[$k] ?? 'leaf';
    }

    function mypi_ledger_pdo() {
        static $pdo = null;
        if ($pdo instanceof PDO) {
            return $pdo;
        }
        $path = mypi_ledger_path();
        $pdo = new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA journal_mode = WAL');
        mypi_ledger_migrate($pdo);
        return $pdo;
    }

    function mypi_ledger_migrate(PDO $pdo) {
        $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS ledger_meta (
  key TEXT PRIMARY KEY,
  value TEXT NOT NULL
);
CREATE TABLE IF NOT EXISTS crates (
  c_uid TEXT PRIMARY KEY,
  kind TEXT NOT NULL DEFAULT 'post',
  scale TEXT NOT NULL DEFAULT 'leaf',
  face_id TEXT NOT NULL DEFAULT '',
  parent_c_uid TEXT NOT NULL DEFAULT '',
  stem_c_uid TEXT NOT NULL DEFAULT '',
  span_start INTEGER,
  span_end INTEGER,
  glass_title TEXT NOT NULL DEFAULT '',
  yard_title TEXT NOT NULL DEFAULT '',
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
  deleted_at INTEGER,
  created_at INTEGER NOT NULL,
  updated_at INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_crates_ingest ON crates(ingest_unix DESC);
CREATE INDEX IF NOT EXISTS idx_crates_place ON crates(place_path);
CREATE INDEX IF NOT EXISTS idx_crates_sys ON crates(sys);
CREATE INDEX IF NOT EXISTS idx_crates_kind ON crates(kind);
CREATE INDEX IF NOT EXISTS idx_crates_scale ON crates(scale);
CREATE INDEX IF NOT EXISTS idx_crates_parent ON crates(parent_c_uid);
CREATE INDEX IF NOT EXISTS idx_crates_stem ON crates(stem_c_uid);
CREATE INDEX IF NOT EXISTS idx_crates_face ON crates(face_id);
CREATE TABLE IF NOT EXISTS crate_events (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  c_uid TEXT NOT NULL,
  event_type TEXT NOT NULL,
  payload_json TEXT NOT NULL DEFAULT '{}',
  actor TEXT NOT NULL DEFAULT '',
  place_path TEXT NOT NULL DEFAULT '',
  event_unix INTEGER,
  ingest_unix INTEGER NOT NULL,
  tool TEXT NOT NULL DEFAULT ''
);
CREATE INDEX IF NOT EXISTS idx_events_cuid ON crate_events(c_uid, id);
CREATE TABLE IF NOT EXISTS tag_map (
  c_uid TEXT NOT NULL,
  tag TEXT NOT NULL,
  PRIMARY KEY (c_uid, tag)
);
CREATE INDEX IF NOT EXISTS idx_tag_map_tag ON tag_map(tag);
CREATE TABLE IF NOT EXISTS deleted_log (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  c_uid TEXT NOT NULL,
  snapshot_json TEXT NOT NULL,
  deleted_at INTEGER NOT NULL,
  actor TEXT NOT NULL DEFAULT '',
  hard INTEGER NOT NULL DEFAULT 0
);
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
CREATE INDEX IF NOT EXISTS idx_edges_crate ON thread_edges(c_uid);
CREATE TABLE IF NOT EXISTS thread_terms (
  term TEXT PRIMARY KEY,
  gravity INTEGER NOT NULL DEFAULT 0,
  updated_at INTEGER NOT NULL
);
SQL
        );
        // Add columns if DB was created by older schema
        $cols = [];
        foreach ($pdo->query('PRAGMA table_info(crates)') as $row) {
            $cols[$row['name']] = true;
        }
        foreach (['sys', 'dom', 'room', 'mod'] as $c) {
            if (empty($cols[$c])) {
                $pdo->exec("ALTER TABLE crates ADD COLUMN $c TEXT NOT NULL DEFAULT ''");
            }
        }
        if (empty($cols['deleted_at'])) {
            $pdo->exec('ALTER TABLE crates ADD COLUMN deleted_at INTEGER');
        }
        $textCols = [
            'scale' => 'leaf',
            'face_id' => '',
            'parent_c_uid' => '',
            'stem_c_uid' => '',
            'glass_title' => '',
            'yard_title' => '',
        ];
        foreach ($textCols as $c => $def) {
            if (empty($cols[$c])) {
                $pdo->exec(
                    "ALTER TABLE crates ADD COLUMN $c TEXT NOT NULL DEFAULT "
                    . $pdo->quote($def)
                );
            }
        }
        foreach (['span_start', 'span_end'] as $c) {
            if (empty($cols[$c])) {
                $pdo->exec("ALTER TABLE crates ADD COLUMN $c INTEGER");
            }
        }
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_crates_scale ON crates(scale)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_crates_parent ON crates(parent_c_uid)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_crates_stem ON crates(stem_c_uid)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_crates_face ON crates(face_id)');
        $pdo->prepare(
            'INSERT INTO ledger_meta(key, value) VALUES(?, ?)
             ON CONFLICT(key) DO UPDATE SET value=excluded.value'
        )->execute(['schema_version', '3']);
        $pdo->prepare(
            'INSERT INTO ledger_meta(key, value) VALUES(?, ?)
             ON CONFLICT(key) DO UPDATE SET value=excluded.value'
        )->execute(['ledger_name', "Chester's Imports"]);
        $pdo->prepare(
            'INSERT INTO ledger_meta(key, value) VALUES(?, ?)
             ON CONFLICT(key) DO NOTHING'
        )->execute(['tps_window_seconds', '900']);
    }

    /** Mint a CHESTER_UID (stored in c_uid). */
    function mypi_ledger_new_cuid() {
        return 'ch.' . strtoupper(bin2hex(random_bytes(8)));
    }

    function mypi_ledger_tps_window_seconds(PDO $pdo = null) {
        $pdo = $pdo ?: mypi_ledger_pdo();
        $st = $pdo->prepare("SELECT value FROM ledger_meta WHERE key='tps_window_seconds'");
        $st->execute();
        $row = $st->fetch();
        $w = $row ? (int) $row['value'] : 900;
        return $w > 0 ? $w : 900;
    }

    function mypi_ledger_window_unix($event_unix, $window_seconds = null) {
        $w = $window_seconds ?? mypi_ledger_tps_window_seconds();
        $e = (int) $event_unix;
        return $w <= 1 ? $e : ($e - ($e % $w));
    }

    function mypi_ledger_tps_uid($window_unix, $window_seconds) {
        return $window_unix . '.w' . $window_seconds;
    }

    function mypi_ledger_tps_facets($window_unix) {
        $u = (int) $window_unix;
        $dt = new DateTime('@' . $u);
        $dt->setTimezone(new DateTimeZone('UTC'));
        $y = (int) $dt->format('Y');
        $dayStart = gmmktime(0, 0, 0, (int) $dt->format('n'), (int) $dt->format('j'), $y);
        $pulse = (int) floor((($u - $dayStart) * 1000) / 86400);
        $pulse = max(0, min(999, $pulse));
        return [
            'utc' => $dt->format('c'),
            'year' => $y,
            'millennium' => intdiv($y, 1000),
            'century_residual' => intdiv($y % 1000, 100),
            'decade_residual' => intdiv($y % 100, 10),
            'block' => intdiv($u, 10000),
            'unix_mod_9' => $u % 9,
            'machine_pulse' => $pulse,
            'clock_id' => 'gaia',
        ];
    }

    /**
     * Charlie / tagSplicer stages (not flat this*rel>blob):
     *   1. `;` / newlines  → independent clauses
     *   2. `from*rest`     → subject + right-hand material
     *   3. `&`             → multi rel-segments under same from
     *   4. `rel>thats`     → relationship + destination(s)
     *   5. `,`             → multi-that: one edge per that (not one string of thats)
     *
     * Example: understanding*you>system,structure,format
     *   → edges understanding*you>system, …>structure, …>format
     * Example: this*related>that&holds>other
     *   → this*related>that + this*holds>other
     *
     * @return array{tags:list<string>,edges:list<array{from:string,rel:string,to:string}>}
     */
    function mypi_ledger_parse_charlie($tags_raw) {
        $tags = [];
        $edges = [];
        $raw = trim((string) $tags_raw);
        if ($raw === '') {
            return ['tags' => [], 'edges' => []];
        }

        $add_tag = static function ($t) use (&$tags) {
            $t = strtolower(trim((string) $t));
            $t = ltrim($t, '#');
            if ($t !== '' && !in_array($t, $tags, true)) {
                $tags[] = $t;
            }
        };
        $add_edge = static function ($from, $rel, $to) use (&$edges, $add_tag) {
            $from = strtolower(trim((string) $from));
            $rel = strtolower(trim((string) $rel));
            $to = strtolower(trim((string) $to));
            $from = ltrim($from, '#');
            $rel = ltrim($rel, '#');
            $to = ltrim($to, '#');
            if ($from === '' || $to === '') {
                return;
            }
            $edges[] = ['from' => $from, 'rel' => $rel, 'to' => $to];
            // a, connector, c, and full chain — connector is a real term too
            foreach ([$from, $rel, $to, $from . '*' . $rel . '>' . $to] as $t) {
                $add_tag($t);
            }
        };

        $clauses = preg_split('/[;\n]+/', $raw);
        foreach ($clauses as $clause) {
            $clause = strtolower(trim((string) $clause));
            if ($clause === '') {
                continue;
            }

            // Stage 2: from*rest  (no * → plain tags / bag words)
            if (strpos($clause, '*') === false) {
                foreach (preg_split('/[\s,]+/', $clause) as $c) {
                    $add_tag($c);
                }
                continue;
            }

            $star = explode('*', $clause, 2);
            $from = trim($star[0]);
            $rest = isset($star[1]) ? trim($star[1]) : '';
            if ($from === '' || $rest === '') {
                if ($from !== '') {
                    $add_tag($from);
                }
                continue;
            }

            // Stage 3: & multi rel-segments under same from
            if (strpos($rest, '&') !== false) {
                $segments = explode('&', $rest);
            } else {
                $segments = [$rest];
            }

            foreach ($segments as $segment) {
                $segment = trim($segment);
                if ($segment === '') {
                    continue;
                }

                // Stage 4: rel>thats  (no > → typed bare term under from)
                if (strpos($segment, '>') === false) {
                    $add_tag($from);
                    $add_tag($segment);
                    $add_tag($from . '*' . $segment);
                    continue;
                }

                $gt = explode('>', $segment, 2);
                $rel = trim($gt[0]);
                $childRaw = isset($gt[1]) ? trim($gt[1]) : '';
                if ($rel === '' || $childRaw === '') {
                    if ($rel !== '') {
                        $add_tag($from);
                        $add_tag($rel);
                    }
                    continue;
                }

                // Stage 5: , multi-that → one edge per that
                if (strpos($childRaw, ',') !== false) {
                    $thats = array_map('trim', explode(',', $childRaw));
                } else {
                    $thats = [$childRaw];
                }
                foreach ($thats as $to) {
                    $to = trim($to);
                    if ($to === '') {
                        continue;
                    }
                    $add_edge($from, $rel, $to);
                }
            }
        }

        return ['tags' => $tags, 'edges' => $edges];
    }

    function mypi_ledger_parse_tags($tags_raw, $sys, $dom, $room, $mod) {
        $parsed = mypi_ledger_parse_charlie($tags_raw);
        $tags = $parsed['tags'];
        $path = trim(implode('/', array_filter([$sys, $dom, $room], 'strlen')), '/');
        if ($path !== '') {
            $tags[] = 'path:' . $path;
            foreach (explode('/', $path) as $seg) {
                $seg = strtolower($seg);
                if ($seg !== '' && !in_array('@' . $seg, $tags, true)) {
                    $tags[] = '@' . $seg;
                }
            }
        }
        if ($mod !== '') {
            $tags[] = 'mod:' . strtolower($mod);
        }
        if ($sys !== '') {
            $tags[] = 'sys:' . strtolower($sys);
        }
        if ($dom !== '') {
            $tags[] = 'dom:' . strtolower($dom);
        }
        return $tags;
    }

    function mypi_ledger_tps_ensure_and_attach(PDO $pdo, $c_uid, $kind, $event_unix, $ingest_unix) {
        $w = mypi_ledger_tps_window_seconds($pdo);
        $window = mypi_ledger_window_unix($event_unix, $w);
        $tps_uid = mypi_ledger_tps_uid($window, $w);
        $facets = json_encode(mypi_ledger_tps_facets($window));
        $pdo->prepare(
            'INSERT INTO tps_shelves(tps_uid, window_unix, window_seconds, clock_id, facets_json, created_at)
             VALUES (?,?,?,?,?,?)
             ON CONFLICT(tps_uid) DO NOTHING'
        )->execute([$tps_uid, $window, $w, 'gaia', $facets, $ingest_unix]);
        $seq = (int) $pdo->query(
            'SELECT COUNT(*) FROM tps_attach WHERE tps_uid=' . $pdo->quote($tps_uid)
        )->fetchColumn();
        $pdo->prepare(
            'INSERT INTO tps_attach(tps_uid, c_uid, kind, seq, attached_at)
             VALUES (?,?,?,?,?)
             ON CONFLICT(tps_uid, c_uid) DO NOTHING'
        )->execute([$tps_uid, $c_uid, $kind, $seq, $ingest_unix]);
        return $tps_uid;
    }

    /**
     * Write Charlie edges + gravity for a crate.
     *
     * Edges come from relationship language in tags_raw (this*rel>that).
     * Gravity bumps EVERY production tag, including auto place tags
     * (path:, @seg, sys:, dom:, mod:) — those are real Charlie material, not hidden filters.
     *
     * @param list<string>|null $all_tags  full tag list from parse_tags (preferred); if null, uses parse of tags_raw only
     * @return list<array{from:string,rel:string,to:string}>
     */
    function mypi_ledger_charlie_write(PDO $pdo, $c_uid, $tags_raw, $sys, $dom, $room, $mod, $ingest, $all_tags = null) {
        $parsed = mypi_ledger_parse_charlie($tags_raw);
        $insE = $pdo->prepare(
            'INSERT INTO thread_edges(c_uid, from_term, rel, to_term, ingest_unix, sys, dom, room, mod)
             VALUES (?,?,?,?,?,?,?,?,?)'
        );
        $bump = $pdo->prepare(
            'INSERT INTO thread_terms(term, gravity, updated_at) VALUES (?,?,?)
             ON CONFLICT(term) DO UPDATE SET
               gravity = gravity + 1,
               updated_at = excluded.updated_at'
        );
        $already = [];
        foreach ($parsed['edges'] as $e) {
            $insE->execute([
                $c_uid, $e['from'], $e['rel'], $e['to'], $ingest, $sys, $dom, $room, $mod,
            ]);
            // Gravity for a, connector, c, and full chain (connector was missing before)
            foreach ([
                $e['from'],
                $e['rel'],
                $e['to'],
                $e['from'] . '*' . $e['rel'] . '>' . $e['to'],
            ] as $term) {
                if ($term === '' || isset($already[$term])) {
                    continue;
                }
                $bump->execute([$term, 1, $ingest]);
                $already[$term] = true;
            }
        }
        // Full production tag set (user tags + auto place tags) — do not hide path:/@/sys: from Charlie
        $tagList = is_array($all_tags) ? $all_tags : $parsed['tags'];
        foreach ($tagList as $t) {
            $t = strtolower(trim((string) $t));
            if ($t === '' || isset($already[$t])) {
                continue;
            }
            // Edge full-forms already bumped; plain * in the middle of non-edge junk is still a term if present
            $bump->execute([$t, 1, $ingest]);
            $already[$t] = true;
        }
        return $parsed['edges'];
    }

    function mypi_ledger_list_tps($limit = 40) {
        $pdo = mypi_ledger_pdo();
        $limit = max(1, min(100, (int) $limit));
        return $pdo->query(
            "SELECT s.*, (SELECT COUNT(*) FROM tps_attach a WHERE a.tps_uid=s.tps_uid) AS n_crates
             FROM tps_shelves s ORDER BY s.window_unix DESC LIMIT $limit"
        )->fetchAll();
    }

    function mypi_ledger_tps_crates($tps_uid) {
        // Order by exact event time inside the window (then ingest)
        $st = mypi_ledger_pdo()->prepare(
            'SELECT c.*, a.seq, a.attached_at FROM tps_attach a
             JOIN crates c ON c.c_uid = a.c_uid
             WHERE a.tps_uid = ?
             ORDER BY c.event_unix ASC, c.ingest_unix ASC, a.seq ASC'
        );
        $st->execute([$tps_uid]);
        return $st->fetchAll();
    }

    function mypi_ledger_charlie_terms($sort = 'gravity', $limit = 100) {
        $limit = max(1, min(200, (int) $limit));
        $pdo = mypi_ledger_pdo();
        if ($sort === 'term') {
            $order = 'term ASC';
        } elseif ($sort === 'recent') {
            $order = 'updated_at DESC';
        } else {
            $order = 'gravity DESC, term ASC';
        }
        return $pdo->query(
            "SELECT term, gravity, updated_at FROM thread_terms ORDER BY $order LIMIT $limit"
        )->fetchAll();
    }

    function mypi_ledger_charlie_gravity($limit = 30) {
        $limit = max(1, min(100, (int) $limit));
        return mypi_ledger_pdo()->query(
            "SELECT term, gravity, updated_at FROM thread_terms
             ORDER BY gravity DESC, term ASC LIMIT $limit"
        )->fetchAll();
    }

    function mypi_ledger_charlie_edges($limit = 40) {
        $limit = max(1, min(100, (int) $limit));
        // Live Charlie only: drop edges for missing / soft-deleted crates
        return mypi_ledger_pdo()->query(
            "SELECT e.* FROM thread_edges e
             INNER JOIN crates c ON c.c_uid = e.c_uid
             WHERE c.deleted_at IS NULL OR c.deleted_at = 0
             ORDER BY e.id DESC LIMIT $limit"
        )->fetchAll();
    }

    /**
     * Pull Charlie relationship edges for one crate (soft- or hard-delete path).
     * Gravity in thread_terms is left as-is (cheap; full rebuild is separate).
     */
    function mypi_ledger_charlie_detach(PDO $pdo, $c_uid) {
        $pdo->prepare('DELETE FROM thread_edges WHERE c_uid = ?')->execute([$c_uid]);
    }

    /**
     * Remove edges whose crate is gone or devalued. Safe to run anytime.
     * @return array{removed:int}
     */
    function mypi_ledger_charlie_scrub_orphans() {
        $pdo = mypi_ledger_pdo();
        $n = $pdo->exec(
            "DELETE FROM thread_edges WHERE c_uid NOT IN (
               SELECT c_uid FROM crates WHERE deleted_at IS NULL OR deleted_at = 0
             )"
        );
        return ['removed' => (int) $n];
    }

    /**
     * Replace tags_raw on a timber; rebuild tag_map + Charlie edges (mailroom / TUI).
     * @return array{ok:bool,error?:string,tags_raw?:string}
     */
    function mypi_ledger_set_charlie($c_uid, $tags_raw, array $opts = []) {
        $c_uid = trim((string) $c_uid);
        $tags_raw = trim((string) $tags_raw);
        $row = mypi_ledger_get($c_uid);
        if (!$row) {
            return ['ok' => false, 'error' => 'timber not found'];
        }
        $sys = (string) ($row['sys'] ?? '');
        $dom = (string) ($row['dom'] ?? '');
        $room = (string) ($row['room'] ?? '');
        $mod = (string) ($row['mod'] ?? '');
        $place_path = (string) ($row['place_path'] ?? '');
        if ($place_path === '') {
            $place_path = trim(implode('/', array_filter([$sys, $dom, $room], 'strlen')), '/');
        }
        $actor = (string) ($opts['actor'] ?? 'charlie');
        $tool = (string) ($opts['tool'] ?? 'mailroom');
        $ingest = time();
        $pdo = mypi_ledger_pdo();
        $parsed = mypi_ledger_parse_charlie($tags_raw);
        $placeTags = mypi_ledger_parse_tags('', $sys, $dom, $room, $mod);
        $all = [];
        foreach (array_merge($placeTags, $parsed['tags']) as $t) {
            $t = strtolower(trim((string) $t));
            if ($t !== '' && !in_array($t, $all, true)) {
                $all[] = $t;
            }
        }
        $oldRaw = (string) ($row['tags_raw'] ?? '');
        mypi_ledger_charlie_detach($pdo, $c_uid);
        $pdo->prepare('DELETE FROM tag_map WHERE c_uid=?')->execute([$c_uid]);
        $pdo->prepare(
            'UPDATE crates SET tags_json=?, tags_raw=?, updated_at=? WHERE c_uid=?'
        )->execute([json_encode($all), $tags_raw, $ingest, $c_uid]);
        $ins = $pdo->prepare('INSERT OR IGNORE INTO tag_map(c_uid, tag) VALUES(?,?)');
        foreach ($all as $t) {
            $ins->execute([$c_uid, $t]);
        }
        mypi_ledger_charlie_write($pdo, $c_uid, $tags_raw, $sys, $dom, $room, $mod, $ingest, $all);
        try {
            $pdo->prepare(
                'INSERT INTO crate_events(c_uid, event_type, payload_json, actor, place_path, event_unix, ingest_unix, tool)
                 VALUES (?,?,?,?,?,?,?,?)'
            )->execute([
                $c_uid,
                'charlie_set',
                json_encode(['tags_raw' => $tags_raw, 'was' => mb_substr($oldRaw, 0, 500)]),
                $actor,
                $place_path,
                $ingest,
                $ingest,
                $tool,
            ]);
        } catch (Throwable $e) {
            // events optional if schema lag
        }
        return ['ok' => true, 'tags_raw' => $tags_raw, 'c_uid' => $c_uid];
    }

    /** Append Charlie fragment onto timber tags_raw. */
    function mypi_ledger_append_charlie($c_uid, $fragment, array $opts = []) {
        $fragment = trim((string) $fragment);
        if ($fragment === '') {
            return ['ok' => false, 'error' => 'empty fragment'];
        }
        $row = mypi_ledger_get($c_uid);
        if (!$row) {
            return ['ok' => false, 'error' => 'timber not found'];
        }
        $old = trim((string) ($row['tags_raw'] ?? ''));
        if ($old !== '' && stripos($old, $fragment) !== false) {
            $new = $old;
        } elseif ($old !== '') {
            $new = $old . '; ' . $fragment;
        } else {
            $new = $fragment;
        }
        return mypi_ledger_set_charlie($c_uid, $new, $opts);
    }

    /**
     * Mailroom index: latest head per stem + tag/edge density.
     * @return list<array>
     */
    function mypi_ledger_list_timbers(array $opts = []) {
        $pdo = mypi_ledger_pdo();
        $limit = max(1, min(300, (int) ($opts['limit'] ?? 80)));
        $queue = (string) ($opts['queue'] ?? 'all'); // all | bare | wired | terms
        $kind = trim((string) ($opts['kind'] ?? ''));
        $agent = trim((string) ($opts['agent'] ?? ''));
        $place = trim((string) ($opts['place'] ?? '')); // substring match on place_path
        $q = trim((string) ($opts['q'] ?? ''));
        $sort = (string) ($opts['sort'] ?? 'ingest'); // ingest | event | tags | edges | kind | place

        $sql = "SELECT c.*,
            (SELECT COUNT(*) FROM tag_map tm WHERE tm.c_uid = c.c_uid) AS n_tags,
            (SELECT COUNT(*) FROM thread_edges te WHERE te.c_uid = c.c_uid) AS n_edges
          FROM crates c
          INNER JOIN (
            SELECT stem, MAX(ingest_unix) AS mx, MAX(c_uid) AS pick
            FROM (
              SELECT COALESCE(NULLIF(stem_c_uid, ''), c_uid) AS stem, ingest_unix, c_uid
              FROM crates
              WHERE (deleted_at IS NULL OR deleted_at = 0)
            )
            GROUP BY stem
          ) h ON COALESCE(NULLIF(c.stem_c_uid, ''), c.c_uid) = h.stem
              AND c.ingest_unix = h.mx
              AND c.c_uid = h.pick
          WHERE (c.deleted_at IS NULL OR c.deleted_at = 0)";
        $args = [];
        if ($kind !== '') {
            $sql .= ' AND c.kind = ?';
            $args[] = $kind;
        }
        if ($agent !== '') {
            $sql .= ' AND lower(c.agent) LIKE ?';
            $args[] = '%' . strtolower($agent) . '%';
        }
        if ($place !== '') {
            $sql .= ' AND lower(c.place_path) LIKE ?';
            $args[] = '%' . strtolower($place) . '%';
        }
        if ($q !== '') {
            $sql .= ' AND (lower(c.topic) LIKE ? OR lower(c.body) LIKE ? OR lower(c.c_uid) LIKE ? OR lower(c.tags_raw) LIKE ?)';
            $like = '%' . strtolower($q) . '%';
            $args[] = $like;
            $args[] = $like;
            $args[] = $like;
            $args[] = $like;
        }
        // queue filters need HAVING-like — apply after via wrap or AND subqueries
        if ($queue === 'bare') {
            $sql .= ' AND (SELECT COUNT(*) FROM tag_map tm WHERE tm.c_uid = c.c_uid
                        AND tm.tag NOT LIKE \'path:%\' AND tm.tag NOT LIKE \'@%\'
                        AND tm.tag NOT LIKE \'sys:%\' AND tm.tag NOT LIKE \'dom:%\'
                        AND tm.tag NOT LIKE \'mod:%\') = 0';
        } elseif ($queue === 'wired') {
            $sql .= ' AND (SELECT COUNT(*) FROM thread_edges te WHERE te.c_uid = c.c_uid) > 0';
        } elseif ($queue === 'terms') {
            $sql .= ' AND (SELECT COUNT(*) FROM tag_map tm WHERE tm.c_uid = c.c_uid) > 0
                      AND (SELECT COUNT(*) FROM thread_edges te WHERE te.c_uid = c.c_uid) = 0';
        }

        $order = 'c.ingest_unix DESC';
        if ($sort === 'event') {
            $order = 'COALESCE(c.event_unix, c.ingest_unix) DESC';
        } elseif ($sort === 'tags') {
            $order = 'n_tags DESC, c.ingest_unix DESC';
        } elseif ($sort === 'edges') {
            $order = 'n_edges DESC, c.ingest_unix DESC';
        } elseif ($sort === 'kind') {
            $order = 'c.kind ASC, c.ingest_unix DESC';
        } elseif ($sort === 'place') {
            $order = 'c.place_path ASC, c.ingest_unix DESC';
        }
        $sql .= ' ORDER BY ' . $order . ' LIMIT ' . $limit;

        $st = $pdo->prepare($sql);
        $st->execute($args);
        return $st->fetchAll() ?: [];
    }

    /**
     * @return array{ok:bool,c_uid?:string,tps_uid?:string,edges?:int,error?:string}
     */
    function mypi_ledger_create_post(array $in) {
        $topic = trim((string) ($in['topic'] ?? ''));
        $body = trim((string) ($in['body'] ?? ''));
        if ($topic === '' && $body === '') {
            return ['ok' => false, 'error' => 'empty post'];
        }
        $sys = (string) ($in['sys'] ?? '');
        $dom = (string) ($in['dom'] ?? '');
        $room = (string) ($in['room'] ?? '');
        $mod = (string) ($in['mod'] ?? '');
        $place_path = trim(implode('/', array_filter([$sys, $dom, $room], 'strlen')), '/');
        $place_label = (string) ($in['place_label'] ?? '');
        $tags_raw = (string) ($in['tags_raw'] ?? '');
        $agent = (string) ($in['agent'] ?? 'user');
        $tool = (string) ($in['tool'] ?? 'postBASIC');
        $kind = (string) ($in['kind'] ?? 'post');
        $scale = trim((string) ($in['scale'] ?? ''));
        if ($scale === '') {
            $scale = mypi_ledger_default_scale($kind);
        }
        $face_id = (string) ($in['face_id'] ?? '');
        $parent_c_uid = (string) ($in['parent_c_uid'] ?? '');
        $stem_c_uid = (string) ($in['stem_c_uid'] ?? '');
        $glass_title = (string) ($in['glass_title'] ?? '');
        $yard_title = (string) ($in['yard_title'] ?? '');
        $span_start = array_key_exists('span_start', $in) && $in['span_start'] !== '' && $in['span_start'] !== null
            ? (int) $in['span_start'] : null;
        $span_end = array_key_exists('span_end', $in) && $in['span_end'] !== '' && $in['span_end'] !== null
            ? (int) $in['span_end'] : null;
        $actor = (string) ($in['actor'] ?? $mod ?: 'hands');
        $tz = (string) ($in['timezone'] ?? '');
        $ingest = time();
        $event = isset($in['event_unix']) && $in['event_unix'] !== '' && $in['event_unix'] !== null
            ? (int) $in['event_unix']
            : $ingest;
        $c_uid = mypi_ledger_new_cuid();
        $tags = mypi_ledger_parse_tags($tags_raw, $sys, $dom, $room, $mod);
        $pdo = mypi_ledger_pdo();
        $w = mypi_ledger_tps_window_seconds($pdo);
        $window = mypi_ledger_window_unix($event, $w);
        $tps_uid = mypi_ledger_tps_uid($window, $w);
        $pdo->prepare(
            'INSERT INTO crates(
              c_uid, kind, scale, face_id, parent_c_uid, stem_c_uid,
              span_start, span_end, glass_title, yard_title,
              topic, body, agent, tool, tool_version,
              place_path, place_label, sys, dom, room, mod,
              tags_json, tags_raw, event_unix, ingest_unix, timezone, t_uid, meta_json,
              created_at, updated_at
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
        )->execute([
            $c_uid, $kind, $scale, $face_id, $parent_c_uid, $stem_c_uid,
            $span_start, $span_end, $glass_title, $yard_title,
            $topic, $body, $agent, $tool, (int) ($in['tool_version'] ?? 5),
            $place_path, $place_label, $sys, $dom, $room, $mod,
            json_encode($tags), $tags_raw, $event, $ingest, $tz, $tps_uid,
            json_encode($in['meta'] ?? new stdClass()),
            $ingest, $ingest,
        ]);
        mypi_ledger_tps_ensure_and_attach($pdo, $c_uid, $kind, $event, $ingest);
        $insTag = $pdo->prepare('INSERT OR IGNORE INTO tag_map(c_uid, tag) VALUES(?,?)');
        foreach ($tags as $t) {
            $insTag->execute([$c_uid, $t]);
        }
        // Pass full tags (incl. auto path/@/sys/dom/mod) so Charlie gravity is production-complete
        $edges = mypi_ledger_charlie_write($pdo, $c_uid, $tags_raw, $sys, $dom, $room, $mod, $ingest, $tags);
        $pdo->prepare(
            'INSERT INTO crate_events(
              c_uid, event_type, payload_json, actor, place_path,
              event_unix, ingest_unix, tool
            ) VALUES (?,?,?,?,?,?,?,?)'
        )->execute([
            $c_uid,
            'create',
            json_encode([
                'topic' => $topic,
                'body' => $body,
                'tags' => $tags,
                'edges' => $edges,
                'tps_uid' => $tps_uid,
                'sys' => $sys,
                'dom' => $dom,
                'room' => $room,
                'mod' => $mod,
            ]),
            $actor,
            $place_path,
            $event,
            $ingest,
            $tool,
        ]);
        return [
            'ok' => true,
            'c_uid' => $c_uid,
            'tps_uid' => $tps_uid,
            'edges' => count($edges),
        ];
    }

    /**
     * List crates. Filters: sys, dom, room, mod, kind, tool, session (meta.session),
     * order: 'desc'|'asc' on event_unix then ingest_unix.
     *
     * @return list<array>
     */
    function mypi_ledger_list(array $opts = []) {
        $pdo = mypi_ledger_pdo();
        $limit = (int) ($opts['limit'] ?? 50);
        $sys = $opts['sys'] ?? null;
        $dom = $opts['dom'] ?? null;
        $room = $opts['room'] ?? null;
        $mod = $opts['mod'] ?? null;
        $kind = $opts['kind'] ?? null;
        $tool = $opts['tool'] ?? null;
        $session = $opts['session'] ?? null;
        $includeDeleted = !empty($opts['include_deleted']);
        $order = strtolower((string) ($opts['order'] ?? 'desc'));
        if ($order !== 'asc') {
            $order = 'desc';
        }
        $sql = 'SELECT * FROM crates WHERE 1=1';
        $args = [];
        if (!$includeDeleted) {
            $sql .= ' AND (deleted_at IS NULL OR deleted_at = 0)';
        }
        if ($sys !== null && $sys !== '') {
            $sql .= ' AND sys = ?';
            $args[] = $sys;
        }
        if ($dom !== null && $dom !== '') {
            $sql .= ' AND dom = ?';
            $args[] = $dom;
        }
        if ($room !== null && $room !== '') {
            $sql .= ' AND room = ?';
            $args[] = $room;
        }
        if ($mod !== null && $mod !== '') {
            $sql .= ' AND mod = ?';
            $args[] = $mod;
        }
        if ($kind !== null && $kind !== '') {
            $sql .= ' AND kind = ?';
            $args[] = $kind;
        }
        if ($tool !== null && $tool !== '') {
            $sql .= ' AND tool = ?';
            $args[] = $tool;
        }
        // chat sessions (and any tool that stamps meta.session)
        if ($session !== null && $session !== '') {
            $sql .= " AND json_extract(meta_json, '$.session') = ?";
            $args[] = $session;
        }
        $sql .= ' ORDER BY COALESCE(event_unix, ingest_unix) ' . strtoupper($order)
            . ', ingest_unix ' . strtoupper($order)
            . ' LIMIT ' . max(1, min(500, $limit));
        $st = $pdo->prepare($sql);
        $st->execute($args);
        return $st->fetchAll();
    }

    /**
     * Distinct chat sessions at a place (from meta.session on kind=chat crates).
     *
     * @return list<array{session:string,n:int,last_unix:int,label:string}>
     */
    function mypi_ledger_chat_sessions(array $opts = []) {
        $pdo = mypi_ledger_pdo();
        $sys = (string) ($opts['sys'] ?? '');
        $dom = (string) ($opts['dom'] ?? '');
        $room = (string) ($opts['room'] ?? '');
        $sql = "SELECT
            COALESCE(json_extract(meta_json, '$.session'), 'live') AS session,
            COALESCE(json_extract(meta_json, '$.session_label'), '') AS label,
            COUNT(*) AS n,
            MAX(COALESCE(event_unix, ingest_unix)) AS last_unix
          FROM crates
          WHERE kind = 'chat'
            AND (deleted_at IS NULL OR deleted_at = 0)";
        $args = [];
        if ($sys !== '') {
            $sql .= ' AND sys = ?';
            $args[] = $sys;
        }
        if ($dom !== '') {
            $sql .= ' AND dom = ?';
            $args[] = $dom;
        }
        if ($room !== '') {
            $sql .= ' AND room = ?';
            $args[] = $room;
        }
        $sql .= ' GROUP BY session ORDER BY last_unix DESC LIMIT 40';
        $st = $pdo->prepare($sql);
        $st->execute($args);
        return $st->fetchAll() ?: [];
    }

    /** Shared place context from SKY__AUTH constants / SITE. */
    function mypi_ledger_place_from_sky(): array {
        $sys = defined('WORLD_ID') ? WORLD_ID : (defined('SYS_ID') ? SYS_ID : (string) ($GLOBALS['SITE'] ?? ''));
        $dom = defined('DOM_SLUG') ? DOM_SLUG : '';
        $room = defined('ROOM_SLUG') ? ROOM_SLUG : '';
        $mod = defined('MOD_SLUG') ? MOD_SLUG : '';
        $place_label = defined('ROOM_DISPLAY') ? ROOM_DISPLAY : '';
        return compact('sys', 'dom', 'room', 'mod', 'place_label');
    }

    function mypi_ledger_get($c_uid) {
        $st = mypi_ledger_pdo()->prepare('SELECT * FROM crates WHERE c_uid = ?');
        $st->execute([$c_uid]);
        $row = $st->fetch();
        return $row ?: null;
    }

    /**
     * Crates tagged with this Charlie/production tag (via tag_map).
     *
     * @return list<array>
     */
    function mypi_ledger_crates_for_tag(string $tag, int $limit = 80) {
        $tag = strtolower(trim($tag));
        if ($tag === '') {
            return [];
        }
        $limit = max(1, min(200, $limit));
        $st = mypi_ledger_pdo()->prepare(
            'SELECT c.* FROM crates c
             JOIN tag_map t ON t.c_uid = c.c_uid
             WHERE t.tag = ?
               AND (c.deleted_at IS NULL OR c.deleted_at = 0)
             ORDER BY COALESCE(c.event_unix, c.ingest_unix) DESC
             LIMIT ' . $limit
        );
        $st->execute([$tag]);
        return $st->fetchAll() ?: [];
    }

    function mypi_ledger_history($c_uid) {
        $st = mypi_ledger_pdo()->prepare(
            'SELECT * FROM crate_events WHERE c_uid = ? ORDER BY id ASC'
        );
        $st->execute([$c_uid]);
        return $st->fetchAll();
    }

    /**
     * Soft-delete: hides from lists, keeps row + history. Can restore.
     * @return array{ok:bool,error?:string}
     */
    function mypi_ledger_soft_delete($c_uid, $actor = 'hands', $tool = 'ledger') {
        $pdo = mypi_ledger_pdo();
        $row = mypi_ledger_get($c_uid);
        if (!$row) {
            return ['ok' => false, 'error' => 'not found'];
        }
        if (!empty($row['deleted_at'])) {
            return ['ok' => true]; // already gone from view
        }
        $now = time();
        $pdo->prepare(
            'UPDATE crates SET deleted_at = ?, updated_at = ? WHERE c_uid = ?'
        )->execute([$now, $now, $c_uid]);
        // Devalue = out of live Charlie graph (re-spliced on restore from tags_raw)
        mypi_ledger_charlie_detach($pdo, $c_uid);
        $pdo->prepare(
            'INSERT INTO crate_events(
              c_uid, event_type, payload_json, actor, place_path,
              event_unix, ingest_unix, tool
            ) VALUES (?,?,?,?,?,?,?,?)'
        )->execute([
            $c_uid,
            'soft_delete',
            json_encode(['topic' => $row['topic'] ?? '']),
            $actor,
            $row['place_path'] ?? '',
            $now,
            $now,
            $tool,
        ]);
        $pdo->prepare(
            'INSERT INTO deleted_log(c_uid, snapshot_json, deleted_at, actor, hard)
             VALUES (?,?,?,?,0)'
        )->execute([$c_uid, json_encode($row), $now, $actor]);
        return ['ok' => true];
    }

    /**
     * Hard-delete: removes crate, tags, events, Charlie edges, TPS attach.
     * Snapshot kept in deleted_log only.
     * Use when soft-delete is not enough. Does NOT nuke the world — one c_uid only.
     * @return array{ok:bool,error?:string}
     */
    function mypi_ledger_hard_delete($c_uid, $actor = 'hands', $tool = 'ledger') {
        $pdo = mypi_ledger_pdo();
        $row = mypi_ledger_get($c_uid);
        if (!$row) {
            return ['ok' => false, 'error' => 'not found'];
        }
        $now = time();
        $pdo->prepare(
            'INSERT INTO deleted_log(c_uid, snapshot_json, deleted_at, actor, hard)
             VALUES (?,?,?,?,1)'
        )->execute([$c_uid, json_encode($row), $now, $actor]);
        mypi_ledger_charlie_detach($pdo, $c_uid);
        $pdo->prepare('DELETE FROM tps_attach WHERE c_uid = ?')->execute([$c_uid]);
        $pdo->prepare('DELETE FROM tag_map WHERE c_uid = ?')->execute([$c_uid]);
        $pdo->prepare('DELETE FROM crate_events WHERE c_uid = ?')->execute([$c_uid]);
        $pdo->prepare('DELETE FROM crates WHERE c_uid = ?')->execute([$c_uid]);
        return ['ok' => true];
    }

    function mypi_ledger_restore($c_uid, $actor = 'hands', $tool = 'ledger') {
        $pdo = mypi_ledger_pdo();
        $row = mypi_ledger_get($c_uid);
        if (!$row) {
            return ['ok' => false, 'error' => 'not found'];
        }
        if (empty($row['deleted_at'])) {
            return ['ok' => true];
        }
        $now = time();
        $pdo->prepare(
            'UPDATE crates SET deleted_at = NULL, updated_at = ? WHERE c_uid = ?'
        )->execute([$now, $c_uid]);
        // Re-splice Charlie from stored tags_raw (edges were detached on soft-delete)
        $tags_raw = (string) ($row['tags_raw'] ?? '');
        $tags = mypi_ledger_parse_tags(
            $tags_raw,
            (string) ($row['sys'] ?? ''),
            (string) ($row['dom'] ?? ''),
            (string) ($row['room'] ?? ''),
            (string) ($row['mod'] ?? '')
        );
        mypi_ledger_charlie_write(
            $pdo,
            $c_uid,
            $tags_raw,
            (string) ($row['sys'] ?? ''),
            (string) ($row['dom'] ?? ''),
            (string) ($row['room'] ?? ''),
            (string) ($row['mod'] ?? ''),
            $now,
            $tags
        );
        $pdo->prepare(
            'INSERT INTO crate_events(
              c_uid, event_type, payload_json, actor, place_path,
              event_unix, ingest_unix, tool
            ) VALUES (?,?,?,?,?,?,?,?)'
        )->execute([
            $c_uid, 'restore', '{}', $actor, $row['place_path'] ?? '',
            $now, $now, $tool,
        ]);
        return ['ok' => true];
    }

    /**
     * Strip invisible / bidi junk Windows and rich-text paste inject
     * (LTR marks around "September ‎16 ‎2025 ‏‎10:16PM", etc.).
     */
    function mypi_sanitize_datetime_text(string $s): string
    {
        // Unicode format / bidi / zero-width / BOM
        $s = preg_replace('/[\x{200B}-\x{200F}\x{202A}-\x{202E}\x{2060}-\x{206F}\x{FEFF}\x{00AD}]/u', '', $s) ?? $s;
        // weird spaces → normal space
        $s = preg_replace('/[\x{00A0}\x{202F}\x{2000}-\x{200A}\x{3000}]/u', ' ', $s) ?? $s;
        // normalize am/pm glued to digits
        $s = preg_replace('/(\d)\s*([ap])\.?\s*m\.?/i', '$1 $2m', $s) ?? $s;
        $s = preg_replace('/\bat\b/i', ' ', $s) ?? $s;
        // drop weekday names (Tuesday,) — strtotime/DateTime handle better without sometimes
        $s = preg_replace(
            '/\b(monday|tuesday|wednesday|thursday|friday|saturday|sunday|mon|tue|wed|thu|fri|sat|sun)\b[, ]*/i',
            '',
            $s
        ) ?? $s;
        $s = str_replace([',', '·', '—', '–'], ' ', $s);
        $s = preg_replace('/\s+/', ' ', trim($s)) ?? $s;
        return $s;
    }

    /**
     * Parse free-text datetime → unix seconds (nearest second).
     * Accepts: bare unix, "now", ISO, US dates, "September 16 2025 10:16PM",
     * "09/16/2025 10:16pm", weekday-prefixed dumps, Windows LTR-mark garbage, etc.
     * Empty → null (caller uses "now").
     *
     * @return int|null
     */
    function mypi_parse_event_time($raw, $timezone = '') {
        if ($raw === null) {
            return null;
        }
        $s = mypi_sanitize_datetime_text((string) $raw);
        if ($s === '') {
            return null;
        }
        if (preg_match('/^(now|today|n)$/i', $s)) {
            return time();
        }
        // pure integer unix (9–13 digits)
        if (preg_match('/^\d{9,13}$/', $s)) {
            $n = (int) $s;
            if ($n > 9999999999) {
                $n = (int) floor($n / 1000);
            }
            return $n;
        }

        $tzName = trim((string) $timezone);
        $tzObj = null;
        if ($tzName !== '') {
            try {
                $tzObj = new DateTimeZone($tzName);
            } catch (Throwable $e) {
                $tzObj = null;
            }
        }
        if ($tzObj === null) {
            try {
                $tzObj = new DateTimeZone(date_default_timezone_get());
            } catch (Throwable $e) {
                $tzObj = new DateTimeZone('UTC');
            }
        }

        $try = $s;
        // "10:16PM" already normalized; unify to "10:16 PM" for formats with A
        $try = preg_replace_callback('/\b([ap])m\b/i', static function ($m) {
            return strtoupper($m[1]) . 'M';
        }, $try) ?? $try;

        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d g:i A',
            'Y-m-d g:iA',
            'Y-m-d',
            'm/d/Y H:i:s',
            'm/d/Y H:i',
            'm/d/Y g:i:s A',
            'm/d/Y g:i A',
            'm/d/Y g:iA',
            'm/d/Y',
            'n/j/Y g:i:s A',
            'n/j/Y g:i A',
            'n/j/Y H:i',
            'n/j/Y',
            'm/d/y g:i A',
            'm/d/y H:i',
            'm/d/y',
            'M j Y g:i:s A',
            'M j Y g:i A',
            'M j Y g:iA',
            'M j Y H:i:s',
            'M j Y H:i',
            'M j Y',
            'F j Y g:i:s A',
            'F j Y g:i A',
            'F j Y g:iA',
            'F j Y H:i:s',
            'F j Y H:i',
            'F j Y',
            'F j, Y g:i:s A',
            'F j, Y g:i A',
            'F j, Y',
            'j M Y g:i A',
            'j M Y H:i',
            'j M Y',
            DateTimeInterface::ATOM,
            DateTimeInterface::RFC2822,
            'c',
        ];

        $ts = null;
        foreach ($formats as $fmt) {
            if (!is_string($fmt) || $fmt === '') {
                continue;
            }
            $dt = DateTime::createFromFormat('!' . $fmt, $try, $tzObj);
            if (!$dt instanceof DateTime) {
                continue;
            }
            $errs = DateTime::getLastErrors();
            if (is_array($errs) && (!empty($errs['error_count']) || !empty($errs['warning_count']))) {
                continue;
            }
            $ts = $dt->getTimestamp();
            break;
        }

        if ($ts === null) {
            try {
                $dt2 = new DateTime($try, $tzObj);
                $ts = $dt2->getTimestamp();
            } catch (Throwable $e) {
                $prevTz = date_default_timezone_get();
                try {
                    date_default_timezone_set($tzObj->getName());
                } catch (Throwable $e2) {
                }
                $parsed = strtotime($try);
                date_default_timezone_set($prevTz);
                if ($parsed !== false) {
                    $ts = (int) $parsed;
                }
            }
        }

        return $ts !== null ? (int) $ts : null;
    }

    /** Single-depth folder name for fileKeeper (no slashes; empty = root). */
    function mypi_ledger_file_folder_norm(string $name): string
    {
        $name = trim($name);
        // strip path junk — one layer only
        $name = str_replace(['\\', '/'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name) ?? $name;
        $name = trim($name);
        if (function_exists('mb_substr')) {
            $name = mb_substr($name, 0, 80);
        } else {
            $name = substr($name, 0, 80);
        }
        return $name;
    }

    /**
     * Distinct folders at a place (from file heads + empty folder markers).
     *
     * @return list<string> sorted, never includes ''
     */
    function mypi_ledger_file_folders(array $opts = []): array
    {
        $pdo = mypi_ledger_pdo();
        $sys = (string) ($opts['sys'] ?? '');
        $dom = (string) ($opts['dom'] ?? '');
        $room = (string) ($opts['room'] ?? '');
        $sql = "SELECT DISTINCT TRIM(COALESCE(json_extract(meta_json, '$.folder'), '')) AS folder
          FROM crates
          WHERE tool = 'fileKeeper'
            AND kind IN ('file', 'folder')
            AND (deleted_at IS NULL OR deleted_at = 0)";
        $args = [];
        if ($sys !== '') {
            $sql .= ' AND sys = ?';
            $args[] = $sys;
        }
        if ($dom !== '') {
            $sql .= ' AND dom = ?';
            $args[] = $dom;
        }
        if ($room !== '') {
            $sql .= ' AND room = ?';
            $args[] = $room;
        }
        $st = $pdo->prepare($sql);
        $st->execute($args);
        $out = [];
        foreach ($st->fetchAll() ?: [] as $row) {
            $f = mypi_ledger_file_folder_norm((string) ($row['folder'] ?? ''));
            if ($f !== '') {
                $out[$f] = true;
            }
        }
        // also folder markers use topic as name
        $sql2 = "SELECT topic FROM crates
          WHERE tool = 'fileKeeper' AND kind = 'folder'
            AND (deleted_at IS NULL OR deleted_at = 0)";
        $args2 = [];
        if ($sys !== '') {
            $sql2 .= ' AND sys = ?';
            $args2[] = $sys;
        }
        if ($dom !== '') {
            $sql2 .= ' AND dom = ?';
            $args2[] = $dom;
        }
        if ($room !== '') {
            $sql2 .= ' AND room = ?';
            $args2[] = $room;
        }
        $st2 = $pdo->prepare($sql2);
        $st2->execute($args2);
        foreach ($st2->fetchAll() ?: [] as $row) {
            $f = mypi_ledger_file_folder_norm((string) ($row['topic'] ?? ''));
            if ($f !== '') {
                $out[$f] = true;
            }
        }
        $list = array_keys($out);
        natcasesort($list);
        return array_values($list);
    }

    /**
     * Create an empty folder marker (kind=folder) so it appears with no files yet.
     *
     * @return array{ok:bool,folder?:string,error?:string}
     */
    function mypi_ledger_file_mkdir(array $in): array
    {
        $folder = mypi_ledger_file_folder_norm((string) ($in['folder'] ?? $in['title'] ?? ''));
        if ($folder === '') {
            return ['ok' => false, 'error' => 'empty folder name'];
        }
        $sys = (string) ($in['sys'] ?? '');
        $dom = (string) ($in['dom'] ?? '');
        $room = (string) ($in['room'] ?? '');
        $mod = (string) ($in['mod'] ?? '');
        $place_path = trim(implode('/', array_filter([$sys, $dom, $room], 'strlen')), '/');
        $agent = (string) ($in['agent'] ?? $mod ?: 'user');
        $ingest = time();
        $c_uid = mypi_ledger_new_cuid();
        $pdo = mypi_ledger_pdo();
        $w = mypi_ledger_tps_window_seconds($pdo);
        $window = mypi_ledger_window_unix($ingest, $w);
        $tps_uid = mypi_ledger_tps_uid($window, $w);
        $meta = ['folder' => $folder];
        $pdo->prepare(
            'INSERT INTO crates(
              c_uid, kind, scale, face_id, parent_c_uid, stem_c_uid,
              span_start, span_end, glass_title, yard_title,
              topic, body, agent, tool, tool_version,
              place_path, place_label, sys, dom, room, mod,
              tags_json, tags_raw, event_unix, ingest_unix, timezone, t_uid, meta_json,
              created_at, updated_at
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
        )->execute([
            $c_uid, 'folder', 'log', '', '', '',
            null, null, '', '',
            $folder, '', $agent, 'fileKeeper', 1,
            $place_path, (string) ($in['place_label'] ?? ''), $sys, $dom, $room, $mod,
            '[]', '', $ingest, $ingest, (string) ($in['timezone'] ?? ''), $tps_uid,
            json_encode($meta),
            $ingest, $ingest,
        ]);
        mypi_ledger_tps_ensure_and_attach($pdo, $c_uid, 'folder', $ingest, $ingest);
        return ['ok' => true, 'folder' => $folder, 'c_uid' => $c_uid];
    }

    /**
     * fileKeeper: save a markdown file revision.
     * New file → stem_c_uid = this c_uid, parent_c_uid empty.
     * Edit → new c_uid, stem preserved, parent = previous head.
     * Lineage is structural (meta), not Charlie tags.
     *
     * @return array{ok:bool,c_uid?:string,stem_c_uid?:string,parent_c_uid?:string,error?:string}
     */
    function mypi_ledger_file_save(array $in) {
        $title = trim((string) ($in['title'] ?? $in['topic'] ?? ''));
        $body = (string) ($in['body'] ?? '');
        if ($title === '' && trim($body) === '') {
            return ['ok' => false, 'error' => 'empty file'];
        }
        if ($title === '') {
            $title = 'untitled';
        }

        $parent = trim((string) ($in['parent_c_uid'] ?? ''));
        $stem = trim((string) ($in['stem_c_uid'] ?? ''));
        $tags_raw = trim((string) ($in['tags_raw'] ?? ''));

        if ($parent !== '') {
            $prev = mypi_ledger_get($parent);
            if (!$prev) {
                return ['ok' => false, 'error' => 'parent not found'];
            }
            $prevMeta = json_decode((string) ($prev['meta_json'] ?? '{}'), true) ?: [];
            $stem = (string) ($prevMeta['stem_c_uid'] ?? $prev['c_uid']);
        }

        // pre-allocate stem for brand-new files
        $c_uid_preview = mypi_ledger_new_cuid();
        if ($stem === '') {
            $stem = $c_uid_preview;
        }

        // one-depth folder ('' = root). Explicit key wins; else inherit parent.
        $folder = '';
        if (array_key_exists('folder', $in)) {
            $folder = mypi_ledger_file_folder_norm((string) $in['folder']);
        } elseif ($parent !== '') {
            $prev0 = mypi_ledger_get($parent);
            $prev0Meta = json_decode((string) ($prev0['meta_json'] ?? '{}'), true) ?: [];
            $folder = mypi_ledger_file_folder_norm((string) ($prev0Meta['folder'] ?? ''));
        }

        $meta = array_merge(is_array($in['meta'] ?? null) ? $in['meta'] : [], [
            'stem_c_uid' => $stem,
            'parent_c_uid' => $parent,
            'rev' => (int) ($in['rev'] ?? 0),
            'folder' => $folder,
        ]);
        if ($parent !== '') {
            $prev = mypi_ledger_get($parent);
            $prevMeta = json_decode((string) ($prev['meta_json'] ?? '{}'), true) ?: [];
            $meta['rev'] = (int) ($prevMeta['rev'] ?? 1) + 1;
        } else {
            $meta['rev'] = 1;
        }

        // inject chosen c_uid via create path — create_post always mints; so call lower-level style
        $sys = (string) ($in['sys'] ?? '');
        $dom = (string) ($in['dom'] ?? '');
        $room = (string) ($in['room'] ?? '');
        $mod = (string) ($in['mod'] ?? '');
        $place_path = trim(implode('/', array_filter([$sys, $dom, $room], 'strlen')), '/');
        $place_label = (string) ($in['place_label'] ?? '');
        $agent = (string) ($in['agent'] ?? $mod ?: 'user');
        $tool = 'fileKeeper';
        $kind = 'file';
        $actor = (string) ($in['actor'] ?? $mod ?: 'hands');
        $tz = (string) ($in['timezone'] ?? '');
        $ingest = time();
        if (array_key_exists('event_unix', $in) && $in['event_unix'] !== '' && $in['event_unix'] !== null) {
            $event = (int) $in['event_unix'];
        } else {
            $event = $ingest;
        }
        // never trust 0 as a real event unless explicitly ancient; treat 0 as missing
        if ($event <= 0) {
            $event = $ingest;
        }

        $c_uid = $c_uid_preview;
        // first revision: stem is self
        if ($parent === '') {
            $stem = $c_uid;
            $meta['stem_c_uid'] = $stem;
            $meta['parent_c_uid'] = '';
        }
        $meta['event_unix'] = $event;
        $meta['event_raw'] = (string) ($in['event_raw'] ?? '');

        $tags = mypi_ledger_parse_tags($tags_raw, $sys, $dom, $room, $mod);
        $pdo = mypi_ledger_pdo();
        $w = mypi_ledger_tps_window_seconds($pdo);
        $window = mypi_ledger_window_unix($event, $w);
        $tps_uid = mypi_ledger_tps_uid($window, $w);
        $pdo->prepare(
            'INSERT INTO crates(
              c_uid, kind, scale, face_id, parent_c_uid, stem_c_uid,
              span_start, span_end, glass_title, yard_title,
              topic, body, agent, tool, tool_version,
              place_path, place_label, sys, dom, room, mod,
              tags_json, tags_raw, event_unix, ingest_unix, timezone, t_uid, meta_json,
              created_at, updated_at
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
        )->execute([
            $c_uid, $kind, 'leaf', '', $parent, $stem,
            null, null, '', '',
            $title, $body, $agent, $tool, (int) ($in['tool_version'] ?? 1),
            $place_path, $place_label, $sys, $dom, $room, $mod,
            json_encode($tags), $tags_raw, $event, $ingest, $tz, $tps_uid,
            json_encode($meta),
            $ingest, $ingest,
        ]);
        mypi_ledger_tps_ensure_and_attach($pdo, $c_uid, $kind, $event, $ingest);
        $insTag = $pdo->prepare('INSERT OR IGNORE INTO tag_map(c_uid, tag) VALUES(?,?)');
        foreach ($tags as $t) {
            $insTag->execute([$c_uid, $t]);
        }
        $edges = mypi_ledger_charlie_write($pdo, $c_uid, $tags_raw, $sys, $dom, $room, $mod, $ingest, $tags);
        $pdo->prepare(
            'INSERT INTO crate_events(
              c_uid, event_type, payload_json, actor, place_path,
              event_unix, ingest_unix, tool
            ) VALUES (?,?,?,?,?,?,?,?)'
        )->execute([
            $c_uid,
            $parent === '' ? 'file_create' : 'file_revise',
            json_encode([
                'topic' => $title,
                'stem_c_uid' => $stem,
                'parent_c_uid' => $parent,
                'rev' => $meta['rev'],
                'edges' => $edges,
                'tps_uid' => $tps_uid,
            ]),
            $actor,
            $place_path,
            $event,
            $ingest,
            $tool,
        ]);
        return [
            'ok' => true,
            'c_uid' => $c_uid,
            'stem_c_uid' => $stem,
            'parent_c_uid' => $parent,
            'rev' => $meta['rev'],
            'tps_uid' => $tps_uid,
        ];
    }

    /**
     * Latest revision of each file stem at a place (kind=file, tool=fileKeeper).
     *
     * @return list<array>
     */
    function mypi_ledger_file_heads(array $opts = []) {
        $pdo = mypi_ledger_pdo();
        $limit = max(1, min(200, (int) ($opts['limit'] ?? 80)));
        $sys = (string) ($opts['sys'] ?? '');
        $dom = (string) ($opts['dom'] ?? '');
        $room = (string) ($opts['room'] ?? '');
        $sql = "SELECT c.* FROM crates c
          INNER JOIN (
            SELECT
              COALESCE(json_extract(meta_json, '$.stem_c_uid'), c_uid) AS stem,
              MAX(ingest_unix) AS mx
            FROM crates
            WHERE kind = 'file'
              AND tool = 'fileKeeper'
              AND (deleted_at IS NULL OR deleted_at = 0)";
        $args = [];
        if ($sys !== '') {
            $sql .= ' AND sys = ?';
            $args[] = $sys;
        }
        if ($dom !== '') {
            $sql .= ' AND dom = ?';
            $args[] = $dom;
        }
        if ($room !== '') {
            $sql .= ' AND room = ?';
            $args[] = $room;
        }
        $sql .= " GROUP BY stem
          ) h ON COALESCE(json_extract(c.meta_json, '$.stem_c_uid'), c.c_uid) = h.stem
              AND c.ingest_unix = h.mx
          WHERE c.kind = 'file' AND c.tool = 'fileKeeper'
            AND (c.deleted_at IS NULL OR c.deleted_at = 0)
          ORDER BY c.ingest_unix DESC
          LIMIT " . $limit;
        $st = $pdo->prepare($sql);
        $st->execute($args);
        return $st->fetchAll() ?: [];
    }

    /**
     * All revisions for a stem (oldest → newest).
     *
     * @return list<array>
     */
    function mypi_ledger_file_revisions(string $stem_c_uid, int $limit = 50) {
        $stem_c_uid = trim($stem_c_uid);
        if ($stem_c_uid === '') {
            return [];
        }
        $limit = max(1, min(100, $limit));
        $st = mypi_ledger_pdo()->prepare(
            "SELECT * FROM crates
             WHERE kind = 'file' AND tool = 'fileKeeper'
               AND (deleted_at IS NULL OR deleted_at = 0)
               AND (
                 c_uid = ?
                 OR json_extract(meta_json, '$.stem_c_uid') = ?
               )
             ORDER BY ingest_unix ASC
             LIMIT " . $limit
        );
        $st->execute([$stem_c_uid, $stem_c_uid]);
        return $st->fetchAll() ?: [];
    }

    // ── inventOry / daily log (log shell + leaf entries) ───────────────

    /**
     * Skyline service buckets (terminal dual-write targets).
     * mod stays empty on submit — room is the office.
     *
     * @return array<string,array{sys:string,dom:string,room:string,mod:string,label:string,service:string}>
     */
    function mypi_ledger_report_buckets() {
        return [
            'omens' => [
                'sys' => 'skyline',
                'dom' => 'services',
                'room' => 'omens',
                'mod' => '',
                'label' => "Oman's Omens",
                'service' => 'oman',
            ],
            'hymns' => [
                'sys' => 'skyline',
                'dom' => 'services',
                'room' => 'hymns',
                'mod' => '',
                'label' => 'Song of Songs',
                'service' => 'hymn',
            ],
        ];
    }

    /** Normalize to YYYY-MM-DD or '' */
    function mypi_ledger_dailylog_day_norm($day) {
        $day = trim((string) $day);
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $day, $m)) {
            return sprintf('%04d-%02d-%02d', (int) $m[1], (int) $m[2], (int) $m[3]);
        }
        if (preg_match('/^(\d{2})(\d{2})(\d{2})$/', $day, $m)) {
            // YYMMDD
            $y = 2000 + (int) $m[1];
            return sprintf('%04d-%02d-%02d', $y, (int) $m[2], (int) $m[3]);
        }
        $ts = strtotime($day);
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }
        return '';
    }

    /**
     * Find day log crate for agent + day key.
     */
    function mypi_ledger_dailylog_find_day($day, $sys, $dom, $room, $agent = '') {
        $day = mypi_ledger_dailylog_day_norm($day);
        if ($day === '') {
            return null;
        }
        $pdo = mypi_ledger_pdo();
        $sql = "SELECT * FROM crates
                WHERE kind = 'dailylog' AND tool = 'inventOry'
                  AND (deleted_at IS NULL OR deleted_at = 0)
                  AND json_extract(meta_json, '$.day') = ?
                  AND sys = ? AND dom = ? AND room = ?";
        $args = [$day, $sys, $dom, $room];
        if ($agent !== '') {
            $sql .= ' AND agent = ?';
            $args[] = $agent;
        }
        $sql .= ' ORDER BY ingest_unix DESC LIMIT 1';
        $st = $pdo->prepare($sql);
        $st->execute($args);
        $row = $st->fetch();
        return $row ?: null;
    }

    /**
     * Open or create today's (or given) invent-ory day log.
     *
     * @return array{ok:bool,c_uid?:string,day?:string,created?:bool,error?:string,row?:array}
     */
    function mypi_ledger_dailylog_ensure_day(array $in) {
        $day = mypi_ledger_dailylog_day_norm($in['day'] ?? date('Y-m-d'));
        if ($day === '') {
            return ['ok' => false, 'error' => 'bad day'];
        }
        $sys = (string) ($in['sys'] ?? 'terminal');
        $dom = (string) ($in['dom'] ?? 'io');
        $room = (string) ($in['room'] ?? 'inventory');
        $mod = (string) ($in['mod'] ?? '');
        $agent = (string) ($in['agent'] ?? 'user');
        $existing = mypi_ledger_dailylog_find_day($day, $sys, $dom, $room, $agent);
        if ($existing) {
            return [
                'ok' => true,
                'c_uid' => $existing['c_uid'],
                'day' => $day,
                'created' => false,
                'row' => $existing,
            ];
        }
        $ts = strtotime($day . ' 12:00:00') ?: time();
        $weekday = date('l', $ts);
        $topic = $day . ' · ' . $weekday;
        $meta = [
            'day' => $day,
            'weekday' => $weekday,
            'standard_date' => date('m-d-Y', $ts),
            'closed' => false,
            'sections' => ['INCOMING EVENTS', 'FINAL DAILY RECORD'],
            'chronokey' => '',
            'last_updated' => date('H:i'),
        ];
        $r = mypi_ledger_create_post([
            'topic' => $topic,
            'body' => "# daily invent-ory\n###### for " . date('l M j, Y', $ts) . "\n",
            'kind' => 'dailylog',
            'scale' => 'log',
            'tool' => 'inventOry',
            'tool_version' => 1,
            'sys' => $sys,
            'dom' => $dom,
            'room' => $room,
            'mod' => $mod,
            'place_label' => (string) ($in['place_label'] ?? 'invent-0rium'),
            'agent' => $agent,
            'actor' => (string) ($in['actor'] ?? $agent),
            'timezone' => (string) ($in['timezone'] ?? ''),
            'event_unix' => $ts,
            'tags_raw' => '',
            'meta' => $meta,
        ]);
        if (empty($r['ok'])) {
            return $r;
        }
        return [
            'ok' => true,
            'c_uid' => $r['c_uid'],
            'day' => $day,
            'created' => true,
            'row' => mypi_ledger_get($r['c_uid']),
        ];
    }

    /**
     * List day logs newest first.
     *
     * @return list<array>
     */
    function mypi_ledger_dailylog_list_days(array $opts = []) {
        $limit = max(1, min(200, (int) ($opts['limit'] ?? 60)));
        $pdo = mypi_ledger_pdo();
        $sql = "SELECT * FROM crates
                WHERE kind = 'dailylog' AND tool = 'inventOry'
                  AND (deleted_at IS NULL OR deleted_at = 0)";
        $args = [];
        if (!empty($opts['sys'])) {
            $sql .= ' AND sys = ?';
            $args[] = $opts['sys'];
        }
        if (!empty($opts['dom'])) {
            $sql .= ' AND dom = ?';
            $args[] = $opts['dom'];
        }
        if (!empty($opts['room'])) {
            $sql .= ' AND room = ?';
            $args[] = $opts['room'];
        }
        if (!empty($opts['agent'])) {
            $sql .= ' AND agent = ?';
            $args[] = $opts['agent'];
        }
        $sql .= ' ORDER BY json_extract(meta_json, \'$.day\') DESC, ingest_unix DESC LIMIT ' . $limit;
        $st = $pdo->prepare($sql);
        $st->execute($args);
        return $st->fetchAll() ?: [];
    }

    /**
     * Leaves for a day log, event order.
     *
     * @return list<array>
     */
    function mypi_ledger_dailylog_list_entries($day_c_uid, $limit = 200) {
        $day_c_uid = trim((string) $day_c_uid);
        if ($day_c_uid === '') {
            return [];
        }
        $limit = max(1, min(500, (int) $limit));
        $st = mypi_ledger_pdo()->prepare(
            "SELECT * FROM crates
             WHERE kind = 'dailylog_entry' AND tool = 'inventOry'
               AND parent_c_uid = ?
               AND (deleted_at IS NULL OR deleted_at = 0)
             ORDER BY event_unix ASC, ingest_unix ASC
             LIMIT " . $limit
        );
        $st->execute([$day_c_uid]);
        return $st->fetchAll() ?: [];
    }

    /**
     * Insert invent leaf (+ optional Skyline report copy).
     *
     * @return array{ok:bool,c_uid?:string,day_c_uid?:string,report_c_uid?:string,error?:string}
     */
    function mypi_ledger_dailylog_insert(array $in) {
        $title = trim((string) ($in['title'] ?? $in['topic'] ?? ''));
        $body = trim((string) ($in['body'] ?? ''));
        if ($title === '' && $body === '') {
            return ['ok' => false, 'error' => 'empty entry'];
        }
        if ($title === '') {
            $title = '(untitled)';
        }

        $sys = (string) ($in['sys'] ?? 'terminal');
        $dom = (string) ($in['dom'] ?? 'io');
        $room = (string) ($in['room'] ?? 'inventory');
        $mod = (string) ($in['mod'] ?? '');
        $agent = (string) ($in['agent'] ?? 'user');
        $tz = (string) ($in['timezone'] ?? '');

        $day = mypi_ledger_dailylog_day_norm($in['day'] ?? date('Y-m-d'));
        if ($day === '') {
            $day = date('Y-m-d');
        }

        $event = null;
        if (isset($in['event_unix']) && $in['event_unix'] !== '' && $in['event_unix'] !== null) {
            $event = (int) $in['event_unix'];
        }
        if (($event === null || $event <= 0) && !empty($in['event_raw'])) {
            if (function_exists('mypi_parse_event_time')) {
                $event = mypi_parse_event_time((string) $in['event_raw'], $tz);
            } else {
                $ts = strtotime((string) $in['event_raw']);
                $event = $ts !== false ? $ts : null;
            }
        }
        if ($event === null || $event <= 0) {
            // backdate day at local noon if only day given; else now
            if ($day !== date('Y-m-d')) {
                $event = strtotime($day . ' ' . date('H:i:s')) ?: time();
            } else {
                $event = time();
            }
        }

        $dayRes = mypi_ledger_dailylog_ensure_day([
            'day' => $day,
            'sys' => $sys,
            'dom' => $dom,
            'room' => $room,
            'mod' => $mod,
            'agent' => $agent,
            'actor' => (string) ($in['actor'] ?? $agent),
            'timezone' => $tz,
            'place_label' => (string) ($in['place_label'] ?? 'invent-0rium'),
        ]);
        if (empty($dayRes['ok'])) {
            return $dayRes;
        }
        $day_c_uid = $dayRes['c_uid'];

        $section = trim((string) ($in['section'] ?? 'INCOMING EVENTS'));
        if ($section === '') {
            $section = 'INCOMING EVENTS';
        }
        $context = trim((string) ($in['context'] ?? ''));
        $tags_raw = trim((string) ($in['tags_raw'] ?? ''));
        $entry_type = trim((string) ($in['entry_type'] ?? 'free'));
        $wall = date('g:i A', $event);

        $leafBody = $body;
        if ($context !== '') {
            $leafBody .= ($leafBody !== '' ? "\n\n" : '') . 'CONTEXT: **' . $context . '**';
        }

        $meta = [
            'section' => $section,
            'wall_time' => $wall,
            'context' => $context,
            'entry_type' => $entry_type,
            'day' => $day,
            'day_c_uid' => $day_c_uid,
            'chronokey' => (string) ($in['chronokey'] ?? ''),
        ];
        if (!empty($in['meta_extra']) && is_array($in['meta_extra'])) {
            $meta = array_merge($meta, $in['meta_extra']);
        }

        $r = mypi_ledger_create_post([
            'topic' => $title,
            'body' => $leafBody,
            'kind' => 'dailylog_entry',
            'scale' => 'leaf',
            'tool' => 'inventOry',
            'tool_version' => 1,
            'sys' => $sys,
            'dom' => $dom,
            'room' => $room,
            'mod' => $mod,
            'place_label' => (string) ($in['place_label'] ?? 'invent-0rium'),
            'agent' => $agent,
            'actor' => (string) ($in['actor'] ?? $agent),
            'timezone' => $tz,
            'event_unix' => $event,
            'tags_raw' => $tags_raw,
            'parent_c_uid' => $day_c_uid,
            'stem_c_uid' => $day_c_uid,
            'meta' => $meta,
        ]);
        if (empty($r['ok'])) {
            return $r;
        }
        $entry_c_uid = $r['c_uid'];

        // bump day last_updated
        $pdo = mypi_ledger_pdo();
        $dayRow = mypi_ledger_get($day_c_uid);
        if ($dayRow) {
            $dmeta = json_decode((string) ($dayRow['meta_json'] ?? '{}'), true) ?: [];
            $dmeta['last_updated'] = date('H:i', $event);
            $secs = $dmeta['sections'] ?? [];
            if (!is_array($secs)) {
                $secs = [];
            }
            if (!in_array($section, $secs, true)) {
                $secs[] = $section;
                $dmeta['sections'] = $secs;
            }
            $pdo->prepare('UPDATE crates SET meta_json = ?, updated_at = ? WHERE c_uid = ?')
                ->execute([json_encode($dmeta), time(), $day_c_uid]);
        }

        $report_c_uid = null;
        $report_to = trim((string) ($in['report_to'] ?? ''));
        if ($report_to !== '' && $report_to !== 'none') {
            $copy = mypi_ledger_report_copy_entry([
                'entry_c_uid' => $entry_c_uid,
                'bucket' => $report_to,
                'agent' => $agent,
                'actor' => (string) ($in['actor'] ?? $agent),
                'timezone' => $tz,
            ]);
            if (!empty($copy['ok'])) {
                $report_c_uid = $copy['c_uid'];
            }
        }

        return [
            'ok' => true,
            'c_uid' => $entry_c_uid,
            'day_c_uid' => $day_c_uid,
            'day' => $day,
            'report_c_uid' => $report_c_uid,
        ];
    }

    /**
     * Dual-write: copy invent leaf into a Skyline service bucket (mod empty).
     *
     * @return array{ok:bool,c_uid?:string,error?:string}
     */
    function mypi_ledger_report_copy_entry(array $in) {
        $entry_c_uid = trim((string) ($in['entry_c_uid'] ?? ''));
        $bucketKey = trim((string) ($in['bucket'] ?? ''));
        $buckets = mypi_ledger_report_buckets();
        if ($entry_c_uid === '' || !isset($buckets[$bucketKey])) {
            return ['ok' => false, 'error' => 'bad entry or bucket'];
        }
        $entry = mypi_ledger_get($entry_c_uid);
        if (!$entry) {
            return ['ok' => false, 'error' => 'entry not found'];
        }
        $b = $buckets[$bucketKey];
        $emeta = json_decode((string) ($entry['meta_json'] ?? '{}'), true) ?: [];
        $meta = [
            'service' => $b['service'],
            'bucket' => $bucketKey,
            'source' => 'inventOry',
            'source_c_uid' => $entry_c_uid,
            'source_day_c_uid' => (string) ($emeta['day_c_uid'] ?? $entry['parent_c_uid'] ?? ''),
            'reported_at' => time(),
            'section' => (string) ($emeta['section'] ?? ''),
            'context' => (string) ($emeta['context'] ?? ''),
        ];
        return mypi_ledger_create_post([
            'topic' => (string) ($entry['topic'] ?? ''),
            'body' => (string) ($entry['body'] ?? ''),
            'kind' => 'report',
            'scale' => 'leaf',
            'tool' => 'inventOry',
            'tool_version' => 1,
            'sys' => $b['sys'],
            'dom' => $b['dom'],
            'room' => $b['room'],
            'mod' => '', // office bucket only — no character mod
            'place_label' => $b['label'],
            'agent' => (string) ($in['agent'] ?? $entry['agent'] ?? 'user'),
            'actor' => (string) ($in['actor'] ?? 'hands'),
            'timezone' => (string) ($in['timezone'] ?? $entry['timezone'] ?? ''),
            'event_unix' => (int) ($entry['event_unix'] ?? time()),
            'tags_raw' => (string) ($entry['tags_raw'] ?? ''),
            'parent_c_uid' => $entry_c_uid,
            'meta' => $meta,
        ]);
    }

    /**
     * Soft-close a day log.
     */
    function mypi_ledger_dailylog_set_closed($day_c_uid, $closed = true) {
        $row = mypi_ledger_get($day_c_uid);
        if (!$row || ($row['kind'] ?? '') !== 'dailylog') {
            return ['ok' => false, 'error' => 'not a day log'];
        }
        $meta = json_decode((string) ($row['meta_json'] ?? '{}'), true) ?: [];
        $meta['closed'] = (bool) $closed;
        mypi_ledger_pdo()->prepare('UPDATE crates SET meta_json = ?, updated_at = ? WHERE c_uid = ?')
            ->execute([json_encode($meta), time(), $day_c_uid]);
        return ['ok' => true];
    }

    /**
     * Parse day key from vault filename: "250916 - Tuesday Sep 16, 2025.md"
     */
    function mypi_ledger_dailylog_day_from_filename($name) {
        $base = basename((string) $name);
        if (preg_match('/^(\d{2})(\d{2})(\d{2})\b/', $base, $m)) {
            return mypi_ledger_dailylog_day_norm($m[1] . $m[2] . $m[3]);
        }
        if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $base, $m)) {
            return mypi_ledger_dailylog_day_norm($m[1] . '-' . $m[2] . '-' . $m[3]);
        }
        return '';
    }

    /**
     * Pull unix from chronokey tail "….1758157510AD" or bare digits.
     */
    function mypi_ledger_dailylog_unix_from_chronokey($text) {
        if (preg_match('/(\d{10})(?:AD)?\b/', (string) $text, $m)) {
            $u = (int) $m[1];
            if ($u > 1000000000 && $u < 2000000000) {
                return $u;
            }
        }
        return null;
    }

    /**
     * Parse vault invent-ory markdown into sectioned entry atoms.
     *
     * @return list<array{section:string,title:string,body:string,context:string,tags_raw:string,event_unix:?int,wall_time:string,chronokey:string}>
     */
    function mypi_ledger_dailylog_parse_vault_md($raw, $dayYmd) {
        $raw = str_replace(["\r\n", "\r"], "\n", (string) $raw);
        // drop yaml frontmatter
        if (preg_match('/^---\n.*?\n---\n/s', $raw, $fm)) {
            $raw = substr($raw, strlen($fm[0]));
        }
        $lines = explode("\n", $raw);
        $section = 'INCOMING EVENTS';
        $entries = [];
        $cur = null;

        $flush = static function () use (&$cur, &$entries) {
            if ($cur === null) {
                return;
            }
            $cur['body'] = trim($cur['body']);
            $cur['title'] = trim($cur['title']);
            if ($cur['title'] === '' && $cur['body'] === '') {
                $cur = null;
                return;
            }
            if ($cur['title'] === '') {
                $cur['title'] = '(entry)';
            }
            $entries[] = $cur;
            $cur = null;
        };

        foreach ($lines as $line) {
            // section headers ## name
            if (preg_match('/^##\s+(.+?)\s*$/u', $line, $m)) {
                $flush();
                $section = trim($m[1]);
                // skip pure template prompts
                continue;
            }
            // entry heads #### / ###### title @ time
            if (preg_match('/^#{3,6}\s+(.+?)(?:\s+@\s*(.+?))?\s*$/u', $line, $m)) {
                $flush();
                $title = trim($m[1]);
                // skip template stubs
                if (preg_match('/^(TRACK YOUR TRACKS|daily invent|for\s)/i', $title)) {
                    continue;
                }
                $wall = isset($m[2]) ? trim($m[2]) : '';
                $event = null;
                if ($wall !== '' && $dayYmd !== '') {
                    $ts = strtotime($dayYmd . ' ' . $wall);
                    if ($ts !== false) {
                        $event = $ts;
                    }
                }
                $cur = [
                    'section' => $section,
                    'title' => $title,
                    'body' => '',
                    'context' => '',
                    'tags_raw' => '',
                    'event_unix' => $event,
                    'wall_time' => $wall,
                    'chronokey' => '',
                ];
                continue;
            }
            if ($cur === null) {
                continue;
            }
            if (preg_match('/^(?:\*\*)?CONTEXT(?:\*\*)?\s*:\s*(?:\*\*)?(.*?)(?:\*\*)?\s*$/iu', $line, $m)) {
                $cur['context'] = trim($m[1], " \t*");
                continue;
            }
            if (preg_match('/^(?:\*\*)?TAGGED(?:\*\*)?\s*:\s*(.+)$/iu', $line, $m)) {
                $tags = trim($m[1]);
                $tags = preg_replace('/#/', '', $tags);
                $tags = preg_replace('/\s+/', ' ', $tags);
                // keep as space/comma-ish for tags_raw — use commas
                $parts = preg_split('/[\s,]+/', $tags, -1, PREG_SPLIT_NO_EMPTY);
                $cur['tags_raw'] = implode(',', array_map(static function ($t) {
                    return ltrim($t, '#');
                }, $parts ?: []));
                continue;
            }
            if (preg_match('/CHRONOKEY/i', $line)) {
                $cur['chronokey'] = trim(strip_tags($line));
                $u = mypi_ledger_dailylog_unix_from_chronokey($line);
                if ($u !== null) {
                    $cur['event_unix'] = $u;
                }
                continue;
            }
            if (preg_match('/^---+\s*$/', $line)) {
                $flush();
                continue;
            }
            // skip template prompt lines
            if (preg_match('/^<>\s/', $line)) {
                continue;
            }
            $cur['body'] .= ($cur['body'] !== '' ? "\n" : '') . $line;
        }
        $flush();
        return $entries;
    }

    /**
     * Import one vault invent-ory .md into day log + leaf rows.
     * Does NOT dual-write to Skyline (historical capture only).
     *
     * @return array{ok:bool,day?:string,day_c_uid?:string,leaves?:int,skipped?:bool,error?:string}
     */
    function mypi_ledger_dailylog_import_file(array $in) {
        $path = trim((string) ($in['path'] ?? ''));
        $raw = (string) ($in['raw'] ?? '');
        $name = trim((string) ($in['filename'] ?? ''));
        if ($path !== '') {
            if (!is_file($path)) {
                return ['ok' => false, 'error' => 'file not found'];
            }
            $raw = (string) file_get_contents($path);
            if ($name === '') {
                $name = basename($path);
            }
        }
        if (trim($raw) === '') {
            return ['ok' => false, 'error' => 'empty content'];
        }
        $day = mypi_ledger_dailylog_day_norm($in['day'] ?? '');
        if ($day === '') {
            $day = mypi_ledger_dailylog_day_from_filename($name !== '' ? $name : $path);
        }
        if ($day === '') {
            return ['ok' => false, 'error' => 'could not parse day from filename'];
        }

        $sys = (string) ($in['sys'] ?? 'terminal');
        $dom = (string) ($in['dom'] ?? 'io');
        $room = (string) ($in['room'] ?? 'inventory');
        $agent = (string) ($in['agent'] ?? 'user');
        $force = !empty($in['force']);

        $existing = mypi_ledger_dailylog_find_day($day, $sys, $dom, $room, $agent);
        if ($existing && !$force) {
            $n = count(mypi_ledger_dailylog_list_entries($existing['c_uid'], 5));
            if ($n > 0) {
                return [
                    'ok' => true,
                    'skipped' => true,
                    'day' => $day,
                    'day_c_uid' => $existing['c_uid'],
                    'leaves' => $n,
                    'error' => 'day already has leaves (pass force=1 to re-import)',
                ];
            }
        }

        $dayRes = mypi_ledger_dailylog_ensure_day([
            'day' => $day,
            'sys' => $sys,
            'dom' => $dom,
            'room' => $room,
            'agent' => $agent,
            'actor' => (string) ($in['actor'] ?? $agent),
            'timezone' => (string) ($in['timezone'] ?? ''),
            'place_label' => 'invent-0rium',
        ]);
        if (empty($dayRes['ok'])) {
            return $dayRes;
        }
        $day_c_uid = $dayRes['c_uid'];

        // stamp import source on day
        $dayRow = mypi_ledger_get($day_c_uid);
        if ($dayRow) {
            $dmeta = json_decode((string) ($dayRow['meta_json'] ?? '{}'), true) ?: [];
            $dmeta['source_path'] = $path !== '' ? $path : $name;
            $dmeta['imported_at'] = time();
            $dmeta['closed'] = true; // historical days close after import
            mypi_ledger_pdo()->prepare('UPDATE crates SET meta_json = ?, updated_at = ? WHERE c_uid = ?')
                ->execute([json_encode($dmeta), time(), $day_c_uid]);
        }

        $parsed = mypi_ledger_dailylog_parse_vault_md($raw, $day);
        if (!$parsed) {
            // whole file as one leaf
            $parsed = [[
                'section' => 'INCOMING EVENTS',
                'title' => 'imported day body',
                'body' => trim($raw),
                'context' => '',
                'tags_raw' => '',
                'event_unix' => strtotime($day . ' 12:00:00') ?: time(),
                'wall_time' => '',
                'chronokey' => '',
            ]];
        }

        // if force and leaves exist, soft-delete old invent leaves for this day only
        if ($force && $existing) {
            $old = mypi_ledger_dailylog_list_entries($day_c_uid, 500);
            $now = time();
            $pdo = mypi_ledger_pdo();
            foreach ($old as $o) {
                $om = json_decode((string) ($o['meta_json'] ?? '{}'), true) ?: [];
                if (empty($om['from_vault_import'])) {
                    continue; // keep live inserts
                }
                $pdo->prepare('UPDATE crates SET deleted_at = ?, updated_at = ? WHERE c_uid = ?')
                    ->execute([$now, $now, $o['c_uid']]);
            }
        }

        $n = 0;
        $sections = [];
        foreach ($parsed as $p) {
            $sec = $p['section'] !== '' ? $p['section'] : 'INCOMING EVENTS';
            $sections[$sec] = true;
            $r = mypi_ledger_dailylog_insert([
                'day' => $day,
                'title' => $p['title'],
                'body' => $p['body'],
                'section' => $sec,
                'context' => $p['context'],
                'tags_raw' => $p['tags_raw'],
                'event_unix' => $p['event_unix'] ?? null,
                'chronokey' => $p['chronokey'],
                'entry_type' => 'vault_import',
                'report_to' => 'none',
                'sys' => $sys,
                'dom' => $dom,
                'room' => $room,
                'agent' => $agent,
                'actor' => (string) ($in['actor'] ?? $agent),
                'timezone' => (string) ($in['timezone'] ?? ''),
                'place_label' => 'invent-0rium',
                'meta_extra' => ['from_vault_import' => true],
            ]);
            // inject from_vault_import on leaf meta after create
            if (!empty($r['ok']) && !empty($r['c_uid'])) {
                $leaf = mypi_ledger_get($r['c_uid']);
                if ($leaf) {
                    $lm = json_decode((string) ($leaf['meta_json'] ?? '{}'), true) ?: [];
                    $lm['from_vault_import'] = true;
                    $lm['wall_time'] = $p['wall_time'] !== '' ? $p['wall_time'] : ($lm['wall_time'] ?? '');
                    if ($p['chronokey'] !== '') {
                        $lm['chronokey'] = $p['chronokey'];
                    }
                    mypi_ledger_pdo()->prepare('UPDATE crates SET meta_json = ? WHERE c_uid = ?')
                        ->execute([json_encode($lm), $r['c_uid']]);
                }
                $n++;
            }
        }

        // merge sections onto day
        $dayRow = mypi_ledger_get($day_c_uid);
        if ($dayRow) {
            $dmeta = json_decode((string) ($dayRow['meta_json'] ?? '{}'), true) ?: [];
            $secs = $dmeta['sections'] ?? [];
            if (!is_array($secs)) {
                $secs = [];
            }
            foreach (array_keys($sections) as $s) {
                if (!in_array($s, $secs, true)) {
                    $secs[] = $s;
                }
            }
            $dmeta['sections'] = $secs;
            $dmeta['closed'] = true;
            mypi_ledger_pdo()->prepare('UPDATE crates SET meta_json = ?, updated_at = ? WHERE c_uid = ?')
                ->execute([json_encode($dmeta), time(), $day_c_uid]);
        }

        return [
            'ok' => true,
            'day' => $day,
            'day_c_uid' => $day_c_uid,
            'leaves' => $n,
            'skipped' => false,
        ];
    }

    /**
     * Import all YYMMDD*.md invent files from a directory.
     *
     * @return array{ok:bool,imported:list,skipped:list,errors:list}
     */
    function mypi_ledger_dailylog_import_dir(array $in) {
        $dir = trim((string) ($in['dir'] ?? ''));
        if ($dir === '' || !is_dir($dir)) {
            return ['ok' => false, 'imported' => [], 'skipped' => [], 'errors' => ['bad dir']];
        }
        $force = !empty($in['force']);
        $imported = [];
        $skipped = [];
        $errors = [];
        $files = glob(rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . '*.md') ?: [];
        sort($files);
        foreach ($files as $f) {
            $base = basename($f);
            if (!preg_match('/^\d{6}\b/', $base)) {
                continue; // skip Daily Inventory.md, Untitled, etc.
            }
            $r = mypi_ledger_dailylog_import_file(array_merge($in, [
                'path' => $f,
                'force' => $force,
            ]));
            if (empty($r['ok'])) {
                $errors[] = $base . ': ' . ($r['error'] ?? 'fail');
            } elseif (!empty($r['skipped'])) {
                $skipped[] = ($r['day'] ?? $base) . ' (' . ($r['leaves'] ?? 0) . ' leaves exist)';
            } else {
                $imported[] = ($r['day'] ?? $base) . ' → ' . (int) ($r['leaves'] ?? 0) . ' leaves';
            }
        }
        return [
            'ok' => true,
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    // ── dossierDesk (AB · person-first factions) ─────────────────────

    function mypi_dossier_statuses() {
        return ['unsure', 'active', 'inactive', 'dissolved'];
    }

    function mypi_dossier_norm_status($s, $default = 'unsure') {
        $s = strtolower(trim((string) $s));
        $ok = mypi_dossier_statuses();
        return in_array($s, $ok, true) ? $s : $default;
    }

    function mypi_dossier_place(array $in = []) {
        return [
            'sys' => (string) ($in['sys'] ?? 'terminal'),
            'dom' => (string) ($in['dom'] ?? 'ab'),
            'room' => (string) ($in['room'] ?? 'dossier'),
            'mod' => (string) ($in['mod'] ?? ''),
            'place_label' => (string) ($in['place_label'] ?? 'Dossier desk'),
        ];
    }

    function mypi_dossier_parse_akas($raw) {
        if (is_array($raw)) {
            $out = [];
            foreach ($raw as $a) {
                $a = trim((string) $a);
                if ($a !== '') {
                    $out[] = $a;
                }
            }
            return array_values(array_unique($out));
        }
        $raw = trim((string) $raw);
        if ($raw === '') {
            return [];
        }
        $parts = preg_split('/[,;\n]+/', $raw);
        $out = [];
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p !== '') {
                $out[] = $p;
            }
        }
        return array_values(array_unique($out));
    }

    /**
     * @return list<array>
     */
    function mypi_dossier_list(string $kind, array $opts = []) {
        $limit = max(1, min(300, (int) ($opts['limit'] ?? 100)));
        $place = mypi_dossier_place($opts);
        $pdo = mypi_ledger_pdo();
        $sql = "SELECT * FROM crates
                WHERE kind = ? AND tool = 'dossierDesk'
                  AND (deleted_at IS NULL OR deleted_at = 0)
                  AND sys = ? AND dom = ? AND room = ?";
        $args = [$kind, $place['sys'], $place['dom'], $place['room']];
        if (!empty($opts['q'])) {
            $sql .= ' AND (lower(topic) LIKE ? OR lower(body) LIKE ? OR lower(meta_json) LIKE ?)';
            $q = '%' . strtolower(trim((string) $opts['q'])) . '%';
            $args[] = $q;
            $args[] = $q;
            $args[] = $q;
        }
        $sql .= ' ORDER BY lower(topic) ASC, ingest_unix DESC LIMIT ' . $limit;
        $st = $pdo->prepare($sql);
        $st->execute($args);
        return $st->fetchAll() ?: [];
    }

    function mypi_dossier_save_person(array $in) {
        $name = trim((string) ($in['name'] ?? $in['topic'] ?? ''));
        $body = trim((string) ($in['body'] ?? ''));
        if ($name === '') {
            return ['ok' => false, 'error' => 'name required'];
        }
        $place = mypi_dossier_place($in);
        $c_uid = trim((string) ($in['c_uid'] ?? ''));
        $akas = mypi_dossier_parse_akas($in['akas'] ?? '');
        $status = mypi_dossier_norm_status($in['status'] ?? 'unsure');
        $agent = (string) ($in['agent'] ?? 'user');
        $meta = [
            'display_name' => $name,
            'akas' => $akas,
            'status' => $status,
            'portrait_asset' => (string) ($in['portrait_asset'] ?? ''),
        ];
        if ($c_uid !== '') {
            $row = mypi_ledger_get($c_uid);
            if (!$row || ($row['kind'] ?? '') !== 'dossier_person') {
                return ['ok' => false, 'error' => 'person not found'];
            }
            $old = json_decode((string) ($row['meta_json'] ?? '{}'), true) ?: [];
            if (!empty($old['portrait_asset']) && $meta['portrait_asset'] === '') {
                $meta['portrait_asset'] = $old['portrait_asset'];
            }
            $meta = array_merge($old, $meta);
            $pdo = mypi_ledger_pdo();
            $now = time();
            $pdo->prepare(
                'UPDATE crates SET topic=?, body=?, meta_json=?, tags_raw=?, tags_json=?, updated_at=? WHERE c_uid=?'
            )->execute([
                $name,
                $body,
                json_encode($meta),
                (string) ($in['tags_raw'] ?? $row['tags_raw'] ?? ''),
                json_encode(mypi_ledger_parse_tags(
                    (string) ($in['tags_raw'] ?? $row['tags_raw'] ?? ''),
                    $place['sys'], $place['dom'], $place['room'], $place['mod']
                )),
                $now,
                $c_uid,
            ]);
            return ['ok' => true, 'c_uid' => $c_uid, 'updated' => true];
        }
        $r = mypi_ledger_create_post([
            'topic' => $name,
            'body' => $body,
            'kind' => 'dossier_person',
            'scale' => 'leaf',
            'tool' => 'dossierDesk',
            'tool_version' => 1,
            'sys' => $place['sys'],
            'dom' => $place['dom'],
            'room' => $place['room'],
            'mod' => $place['mod'],
            'place_label' => $place['place_label'],
            'agent' => $agent,
            'actor' => (string) ($in['actor'] ?? $agent),
            'timezone' => (string) ($in['timezone'] ?? ''),
            'tags_raw' => (string) ($in['tags_raw'] ?? ''),
            'meta' => $meta,
        ]);
        if (!empty($r['ok']) && !empty($r['c_uid'])) {
            $pdo = mypi_ledger_pdo();
            $pdo->prepare('UPDATE crates SET stem_c_uid=? WHERE c_uid=?')->execute([$r['c_uid'], $r['c_uid']]);
        }
        return $r;
    }

    function mypi_dossier_save_faction(array $in) {
        $name = trim((string) ($in['name'] ?? $in['topic'] ?? ''));
        $body = trim((string) ($in['body'] ?? ''));
        if ($name === '') {
            return ['ok' => false, 'error' => 'name required'];
        }
        $place = mypi_dossier_place($in);
        $c_uid = trim((string) ($in['c_uid'] ?? ''));
        $status = mypi_dossier_norm_status($in['status'] ?? 'unsure');
        $agent = (string) ($in['agent'] ?? 'user');
        $meta = [
            'status' => $status,
            'sigil_asset' => (string) ($in['sigil_asset'] ?? ''),
        ];
        if ($c_uid !== '') {
            $row = mypi_ledger_get($c_uid);
            if (!$row || ($row['kind'] ?? '') !== 'dossier_faction') {
                return ['ok' => false, 'error' => 'faction not found'];
            }
            $old = json_decode((string) ($row['meta_json'] ?? '{}'), true) ?: [];
            if (!empty($old['sigil_asset']) && $meta['sigil_asset'] === '') {
                $meta['sigil_asset'] = $old['sigil_asset'];
            }
            $meta = array_merge($old, $meta);
            $now = time();
            mypi_ledger_pdo()->prepare(
                'UPDATE crates SET topic=?, body=?, meta_json=?, tags_raw=?, updated_at=? WHERE c_uid=?'
            )->execute([
                $name, $body, json_encode($meta),
                (string) ($in['tags_raw'] ?? $row['tags_raw'] ?? ''),
                $now, $c_uid,
            ]);
            return ['ok' => true, 'c_uid' => $c_uid, 'updated' => true];
        }
        return mypi_ledger_create_post([
            'topic' => $name,
            'body' => $body,
            'kind' => 'dossier_faction',
            'scale' => 'log',
            'tool' => 'dossierDesk',
            'tool_version' => 1,
            'sys' => $place['sys'],
            'dom' => $place['dom'],
            'room' => $place['room'],
            'mod' => $place['mod'],
            'place_label' => $place['place_label'],
            'agent' => $agent,
            'actor' => (string) ($in['actor'] ?? $agent),
            'timezone' => (string) ($in['timezone'] ?? ''),
            'tags_raw' => (string) ($in['tags_raw'] ?? ''),
            'meta' => $meta,
        ]);
    }

    /**
     * Upsert membership (person ↔ faction).
     */
    function mypi_dossier_save_membership(array $in) {
        $person = trim((string) ($in['person_c_uid'] ?? ''));
        $faction = trim((string) ($in['faction_c_uid'] ?? ''));
        if ($person === '' || $faction === '') {
            return ['ok' => false, 'error' => 'person and faction required'];
        }
        $p = mypi_ledger_get($person);
        $f = mypi_ledger_get($faction);
        if (!$p || ($p['kind'] ?? '') !== 'dossier_person') {
            return ['ok' => false, 'error' => 'bad person'];
        }
        if (!$f || ($f['kind'] ?? '') !== 'dossier_faction') {
            return ['ok' => false, 'error' => 'bad faction'];
        }
        $place = mypi_dossier_place($in);
        $status = mypi_dossier_norm_status($in['status'] ?? 'unsure');
        $isLeader = !empty($in['is_leader']);
        $role = trim((string) ($in['role'] ?? ''));
        $agent = (string) ($in['agent'] ?? 'user');

        // find existing membership
        $pdo = mypi_ledger_pdo();
        $st = $pdo->prepare(
            "SELECT * FROM crates
             WHERE kind = 'dossier_membership' AND tool = 'dossierDesk'
               AND (deleted_at IS NULL OR deleted_at = 0)
               AND json_extract(meta_json, '$.person_c_uid') = ?
               AND json_extract(meta_json, '$.faction_c_uid') = ?
             LIMIT 1"
        );
        $st->execute([$person, $faction]);
        $existing = $st->fetch();

        $meta = [
            'person_c_uid' => $person,
            'faction_c_uid' => $faction,
            'status' => $status,
            'is_leader' => $isLeader,
            'role' => $role,
        ];
        $topic = ($p['topic'] ?? 'person') . ' ∈ ' . ($f['topic'] ?? 'faction');

        if ($existing) {
            $c_uid = $existing['c_uid'];
            $old = json_decode((string) ($existing['meta_json'] ?? '{}'), true) ?: [];
            $meta = array_merge($old, $meta);
            $pdo->prepare('UPDATE crates SET topic=?, meta_json=?, updated_at=? WHERE c_uid=?')
                ->execute([$topic, json_encode($meta), time(), $c_uid]);
            $leaders = mypi_dossier_faction_leaders($faction);
            return [
                'ok' => true,
                'c_uid' => $c_uid,
                'updated' => true,
                'leader_warn' => count($leaders) >= 2,
                'leader_count' => count($leaders),
                'leaders' => $leaders,
            ];
        }

        $r = mypi_ledger_create_post([
            'topic' => $topic,
            'body' => $role,
            'kind' => 'dossier_membership',
            'scale' => 'leaf',
            'tool' => 'dossierDesk',
            'tool_version' => 1,
            'sys' => $place['sys'],
            'dom' => $place['dom'],
            'room' => $place['room'],
            'mod' => $place['mod'],
            'place_label' => $place['place_label'],
            'agent' => $agent,
            'actor' => (string) ($in['actor'] ?? $agent),
            'timezone' => (string) ($in['timezone'] ?? ''),
            'parent_c_uid' => $person,
            'stem_c_uid' => $person,
            'meta' => $meta,
        ]);
        if (empty($r['ok'])) {
            return $r;
        }
        $leaders = mypi_dossier_faction_leaders($faction);
        $r['leader_warn'] = count($leaders) >= 2;
        $r['leader_count'] = count($leaders);
        $r['leaders'] = $leaders;
        return $r;
    }

    /**
     * @return list<array{c_uid:string,name:string,membership_c_uid:string}>
     */
    function mypi_dossier_faction_leaders($faction_c_uid) {
        $faction_c_uid = trim((string) $faction_c_uid);
        if ($faction_c_uid === '') {
            return [];
        }
        $st = mypi_ledger_pdo()->prepare(
            "SELECT * FROM crates
             WHERE kind = 'dossier_membership' AND tool = 'dossierDesk'
               AND (deleted_at IS NULL OR deleted_at = 0)
               AND json_extract(meta_json, '$.faction_c_uid') = ?
               AND (
                 json_extract(meta_json, '$.is_leader') = 1
                 OR json_extract(meta_json, '$.is_leader') = 'true'
                 OR json_extract(meta_json, '$.is_leader') = true
               )"
        );
        $st->execute([$faction_c_uid]);
        $out = [];
        foreach ($st->fetchAll() ?: [] as $m) {
            $mm = json_decode((string) ($m['meta_json'] ?? '{}'), true) ?: [];
            if (empty($mm['is_leader'])) {
                continue;
            }
            $pid = (string) ($mm['person_c_uid'] ?? '');
            $person = $pid !== '' ? mypi_ledger_get($pid) : null;
            $out[] = [
                'c_uid' => $pid,
                'name' => $person ? (string) $person['topic'] : '?',
                'membership_c_uid' => $m['c_uid'],
            ];
        }
        return $out;
    }

    function mypi_dossier_memberships_for_person($person_c_uid) {
        $st = mypi_ledger_pdo()->prepare(
            "SELECT * FROM crates
             WHERE kind = 'dossier_membership' AND tool = 'dossierDesk'
               AND (deleted_at IS NULL OR deleted_at = 0)
               AND json_extract(meta_json, '$.person_c_uid') = ?
             ORDER BY ingest_unix DESC"
        );
        $st->execute([trim((string) $person_c_uid)]);
        return $st->fetchAll() ?: [];
    }

    function mypi_dossier_memberships_for_faction($faction_c_uid) {
        $st = mypi_ledger_pdo()->prepare(
            "SELECT * FROM crates
             WHERE kind = 'dossier_membership' AND tool = 'dossierDesk'
               AND (deleted_at IS NULL OR deleted_at = 0)
               AND json_extract(meta_json, '$.faction_c_uid') = ?
             ORDER BY ingest_unix DESC"
        );
        $st->execute([trim((string) $faction_c_uid)]);
        return $st->fetchAll() ?: [];
    }

    function mypi_dossier_add_note(array $in) {
        $body = trim((string) ($in['body'] ?? ''));
        $topic = trim((string) ($in['title'] ?? $in['topic'] ?? ''));
        if ($body === '' && $topic === '') {
            return ['ok' => false, 'error' => 'empty note'];
        }
        if ($topic === '') {
            $topic = '(field note)';
        }
        $place = mypi_dossier_place($in);
        $subjects = [];
        if (!empty($in['person_c_uid'])) {
            $subjects[] = trim((string) $in['person_c_uid']);
        }
        if (!empty($in['faction_c_uid'])) {
            $subjects[] = trim((string) $in['faction_c_uid']);
        }
        if (!empty($in['subject_c_uid'])) {
            $subjects[] = trim((string) $in['subject_c_uid']);
        }
        $subjects = array_values(array_unique(array_filter($subjects)));
        if (!$subjects) {
            return ['ok' => false, 'error' => 'subject required (person and/or faction)'];
        }
        $event = time();
        if (isset($in['event_unix']) && $in['event_unix'] !== '' && $in['event_unix'] !== null) {
            $event = (int) $in['event_unix'];
        } elseif (!empty($in['event_raw'])) {
            $ts = strtotime((string) $in['event_raw']);
            if ($ts !== false) {
                $event = $ts;
            }
        }
        $parent = $subjects[0];
        $agent = (string) ($in['agent'] ?? 'user');
        $meta = [
            'subject_c_uids' => $subjects,
            'person_c_uid' => (string) ($in['person_c_uid'] ?? ''),
            'faction_c_uid' => (string) ($in['faction_c_uid'] ?? ''),
            'confidence' => (string) ($in['confidence'] ?? 'rumor'),
            'context' => trim((string) ($in['context'] ?? '')),
            'source' => (string) ($in['source'] ?? 'field'),
        ];
        return mypi_ledger_create_post([
            'topic' => $topic,
            'body' => $body,
            'kind' => 'dossier_note',
            'scale' => 'leaf',
            'tool' => 'dossierDesk',
            'tool_version' => 1,
            'sys' => $place['sys'],
            'dom' => $place['dom'],
            'room' => $place['room'],
            'mod' => $place['mod'],
            'place_label' => $place['place_label'],
            'agent' => $agent,
            'actor' => (string) ($in['actor'] ?? $agent),
            'timezone' => (string) ($in['timezone'] ?? ''),
            'event_unix' => $event,
            'tags_raw' => (string) ($in['tags_raw'] ?? ''),
            'parent_c_uid' => $parent,
            'stem_c_uid' => $parent,
            'meta' => $meta,
        ]);
    }

    function mypi_dossier_list_notes(array $opts = []) {
        $limit = max(1, min(200, (int) ($opts['limit'] ?? 80)));
        $place = mypi_dossier_place($opts);
        $pdo = mypi_ledger_pdo();
        $sql = "SELECT * FROM crates
                WHERE kind = 'dossier_note' AND tool = 'dossierDesk'
                  AND (deleted_at IS NULL OR deleted_at = 0)
                  AND sys = ? AND dom = ? AND room = ?";
        $args = [$place['sys'], $place['dom'], $place['room']];
        if (!empty($opts['person_c_uid'])) {
            $sql .= " AND (
              json_extract(meta_json, '$.person_c_uid') = ?
              OR meta_json LIKE ?
            )";
            $pid = (string) $opts['person_c_uid'];
            $args[] = $pid;
            $args[] = '%' . $pid . '%';
        }
        if (!empty($opts['faction_c_uid'])) {
            $sql .= " AND (
              json_extract(meta_json, '$.faction_c_uid') = ?
              OR meta_json LIKE ?
            )";
            $fid = (string) $opts['faction_c_uid'];
            $args[] = $fid;
            $args[] = '%' . $fid . '%';
        }
        $sql .= ' ORDER BY event_unix DESC, ingest_unix DESC LIMIT ' . $limit;
        $st = $pdo->prepare($sql);
        $st->execute($args);
        return $st->fetchAll() ?: [];
    }

    // ── shotDesk (ICU · Watchers scene cards) ────────────────────────

    function mypi_shot_place(array $in = []) {
        return [
            'sys' => (string) ($in['sys'] ?? 'terminal'),
            'dom' => (string) ($in['dom'] ?? 'icu'),
            'room' => (string) ($in['room'] ?? 'shots'),
            'mod' => (string) ($in['mod'] ?? ''),
            'place_label' => (string) ($in['place_label'] ?? 'Shots'),
        ];
    }

    function mypi_shot_list(array $opts = []) {
        $limit = max(1, min(200, (int) ($opts['limit'] ?? 80)));
        $place = mypi_shot_place($opts);
        $pdo = mypi_ledger_pdo();
        $sql = "SELECT * FROM crates
                WHERE kind = 'shot_card' AND tool = 'shotDesk'
                  AND (deleted_at IS NULL OR deleted_at = 0)
                  AND sys = ? AND dom = ? AND room = ?";
        $args = [$place['sys'], $place['dom'], $place['room']];
        if (!empty($opts['q'])) {
            $sql .= ' AND (lower(topic) LIKE ? OR lower(body) LIKE ? OR lower(meta_json) LIKE ?)';
            $q = '%' . strtolower(trim((string) $opts['q'])) . '%';
            $args[] = $q;
            $args[] = $q;
            $args[] = $q;
        }
        $sql .= ' ORDER BY ingest_unix DESC LIMIT ' . $limit;
        $st = $pdo->prepare($sql);
        $st->execute($args);
        return $st->fetchAll() ?: [];
    }

    /**
     * Create or update a shot card.
     */
    function mypi_shot_save(array $in) {
        $title = trim((string) ($in['title'] ?? $in['topic'] ?? ''));
        if ($title === '') {
            return ['ok' => false, 'error' => 'title required'];
        }
        $place = mypi_shot_place($in);
        $c_uid = trim((string) ($in['c_uid'] ?? ''));
        $slugline = trim((string) ($in['slugline'] ?? ''));
        $visual = trim((string) ($in['visual'] ?? ''));
        $action = trim((string) ($in['action'] ?? ''));
        $dialogue = trim((string) ($in['dialogue'] ?? ''));
        $transition = trim((string) ($in['transition'] ?? ''));
        $amusement = trim((string) ($in['amusement'] ?? ''));
        $tags = trim((string) ($in['tags_raw'] ?? ''));
        $agent = (string) ($in['agent'] ?? 'user');

        // Body: readable card script
        $parts = [];
        if ($slugline !== '') {
            $parts[] = '## SLUGLINE' . "\n" . $slugline;
        }
        if ($visual !== '') {
            $parts[] = '## VISUAL' . "\n" . $visual;
        }
        if ($action !== '') {
            $parts[] = '## ACTION' . "\n" . $action;
        }
        if ($dialogue !== '') {
            $parts[] = '## DIALOGUE' . "\n" . $dialogue;
        }
        if ($transition !== '') {
            $parts[] = '## TRANSITION' . "\n" . $transition;
        }
        if ($amusement !== '') {
            $parts[] = '## AMUSEMENT' . "\n" . $amusement;
        }
        // allow free body override
        if (!empty($in['body']) && trim((string) $in['body']) !== '') {
            $body = (string) $in['body'];
        } else {
            $body = implode("\n\n", $parts);
        }

        $meta = [
            'slugline' => $slugline,
            'visual' => $visual,
            'action' => $action,
            'dialogue' => $dialogue,
            'transition' => $transition,
            'amusement' => $amusement,
            'code' => (string) ($in['code'] ?? $title),
        ];

        if ($c_uid !== '') {
            $row = mypi_ledger_get($c_uid);
            if (!$row || ($row['kind'] ?? '') !== 'shot_card') {
                return ['ok' => false, 'error' => 'shot not found'];
            }
            $old = json_decode((string) ($row['meta_json'] ?? '{}'), true) ?: [];
            $meta = array_merge($old, $meta);
            mypi_ledger_pdo()->prepare(
                'UPDATE crates SET topic=?, body=?, meta_json=?, tags_raw=?, tags_json=?, updated_at=? WHERE c_uid=?'
            )->execute([
                $title,
                $body,
                json_encode($meta),
                $tags,
                json_encode(mypi_ledger_parse_tags($tags, $place['sys'], $place['dom'], $place['room'], $place['mod'])),
                time(),
                $c_uid,
            ]);
            // refresh tag_map lightly
            $pdo = mypi_ledger_pdo();
            $pdo->prepare('DELETE FROM tag_map WHERE c_uid=?')->execute([$c_uid]);
            $ins = $pdo->prepare('INSERT OR IGNORE INTO tag_map(c_uid, tag) VALUES(?,?)');
            foreach (mypi_ledger_parse_tags($tags, $place['sys'], $place['dom'], $place['room'], $place['mod']) as $t) {
                $ins->execute([$c_uid, $t]);
            }
            return ['ok' => true, 'c_uid' => $c_uid, 'updated' => true];
        }

        $r = mypi_ledger_create_post([
            'topic' => $title,
            'body' => $body,
            'kind' => 'shot_card',
            'scale' => 'leaf',
            'tool' => 'shotDesk',
            'tool_version' => 1,
            'sys' => $place['sys'],
            'dom' => $place['dom'],
            'room' => $place['room'],
            'mod' => $place['mod'],
            'place_label' => $place['place_label'],
            'agent' => $agent,
            'actor' => (string) ($in['actor'] ?? $agent),
            'timezone' => (string) ($in['timezone'] ?? ''),
            'tags_raw' => $tags,
            'meta' => $meta,
        ]);
        return $r;
    }

    // ── Media store (diagram boards, covers, sine waves…) ─────────────

    function mypi_media_root() {
        if (!defined('echoSONAR')) {
            throw new RuntimeException('echoSONAR not defined');
        }
        $dir = rtrim(str_replace('\\', '/', echoSONAR), '/') . '/d/_MEDIA';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        return $dir;
    }

    /** New asset id: m.HEX */
    function mypi_media_new_id() {
        return 'm.' . strtoupper(bin2hex(random_bytes(8)));
    }

    function mypi_media_safe_id($id) {
        $id = trim((string) $id);
        if (!preg_match('/^m\.([A-Fa-f0-9]{16})$/', $id, $m)) {
            return '';
        }
        return 'm.' . strtoupper($m[1]);
    }

    /**
     * Store an uploaded / local image file into d/_MEDIA.
     *
     * @return array{ok:bool,asset_id?:string,ext?:string,name?:string,bytes?:int,error?:string}
     */
    function mypi_media_store($sourcePath, $originalName, array $opts = []) {
        if (!is_file($sourcePath)) {
            return ['ok' => false, 'error' => 'no source file'];
        }
        $orig = basename((string) $originalName);
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        $allowed = ['png' => 1, 'jpg' => 1, 'jpeg' => 1, 'gif' => 1, 'webp' => 1, 'svg' => 1];
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }
        if ($ext === '' || empty($allowed[$ext])) {
            return ['ok' => false, 'error' => 'unsupported type (png/jpg/gif/webp/svg)'];
        }
        $bytes = (int) filesize($sourcePath);
        $max = (int) ($opts['max_bytes'] ?? 12 * 1024 * 1024);
        if ($bytes <= 0 || $bytes > $max) {
            return ['ok' => false, 'error' => 'file too large or empty (max 12MB)'];
        }
        $id = mypi_media_new_id();
        $root = mypi_media_root();
        $dest = $root . '/' . $id . '.' . $ext;
        $moved = false;
        if (is_uploaded_file($sourcePath)) {
            $moved = @move_uploaded_file($sourcePath, $dest);
        }
        if (!$moved) {
            $moved = @copy($sourcePath, $dest);
        }
        if (!$moved) {
            return ['ok' => false, 'error' => 'could not store file'];
        }
        $meta = [
            'asset_id' => $id,
            'ext' => $ext,
            'name' => $orig,
            'bytes' => $bytes,
            'stored_at' => time(),
            'stem_c_uid' => (string) ($opts['stem_c_uid'] ?? ''),
            'c_uid' => (string) ($opts['c_uid'] ?? ''),
            'role' => (string) ($opts['role'] ?? 'attach'),
        ];
        file_put_contents($root . '/' . $id . '.json', json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return [
            'ok' => true,
            'asset_id' => $id,
            'ext' => $ext,
            'name' => $orig,
            'bytes' => $bytes,
            'path' => $dest,
        ];
    }

    /**
     * Resolve asset filesystem path (null if missing / bad id).
     */
    function mypi_media_resolve($asset_id) {
        $id = mypi_media_safe_id($asset_id);
        if ($id === '') {
            return null;
        }
        $root = mypi_media_root();
        $hits = glob($root . '/' . $id . '.*') ?: [];
        foreach ($hits as $f) {
            if (preg_match('/\.(json)$/i', $f)) {
                continue;
            }
            return $f;
        }
        return null;
    }

    function mypi_media_meta($asset_id) {
        $id = mypi_media_safe_id($asset_id);
        if ($id === '') {
            return null;
        }
        $jf = mypi_media_root() . '/' . $id . '.json';
        if (!is_file($jf)) {
            return null;
        }
        $m = json_decode((string) file_get_contents($jf), true);
        return is_array($m) ? $m : null;
    }

    /** Public href for img src (auth-gated door). */
    function mypi_media_href($asset_id) {
        $id = mypi_media_safe_id($asset_id);
        if ($id === '') {
            return '';
        }
        if (function_exists('mypi_room_href')) {
            return mypi_room_href('io', 'media') . '?id=' . rawurlencode($id);
        }
        return '/terminal/io/media?id=' . rawurlencode($id);
    }

    /**
     * data: URI for small local assets (reliable in-page display if media door glitches).
     * Empty string if missing or too large.
     */
    function mypi_media_data_uri($asset_id, $max_bytes = 3500000) {
        $path = mypi_media_resolve($asset_id);
        if ($path === null || !is_file($path)) {
            return '';
        }
        $bytes = (int) filesize($path);
        if ($bytes <= 0 || $bytes > (int) $max_bytes) {
            return '';
        }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $types = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
        ];
        $mime = $types[$ext] ?? '';
        if ($mime === '') {
            return '';
        }
        $bin = @file_get_contents($path);
        if ($bin === false || $bin === '') {
            return '';
        }
        return 'data:' . $mime . ';base64,' . base64_encode($bin);
    }

    /**
     * Attach asset to a crate's meta.attachments (stem head preferred).
     *
     * @return array{ok:bool,error?:string}
     */
    function mypi_media_attach_crate($c_uid, array $asset, $role = 'attach') {
        $row = mypi_ledger_get($c_uid);
        if (!$row) {
            return ['ok' => false, 'error' => 'crate not found'];
        }
        $meta = json_decode((string) ($row['meta_json'] ?? '{}'), true) ?: [];
        $atts = $meta['attachments'] ?? [];
        if (!is_array($atts)) {
            $atts = [];
        }
        $atts[] = [
            'asset_id' => $asset['asset_id'],
            'name' => $asset['name'] ?? '',
            'ext' => $asset['ext'] ?? '',
            'role' => $role,
            'bytes' => $asset['bytes'] ?? 0,
            'attached_at' => time(),
        ];
        $meta['attachments'] = $atts;
        mypi_ledger_pdo()->prepare('UPDATE crates SET meta_json = ?, updated_at = ? WHERE c_uid = ?')
            ->execute([json_encode($meta), time(), $c_uid]);
        return ['ok' => true, 'meta' => $meta];
    }

    /**
     * Prefer data: URI for in-page display; fall back to media door href.
     * Parsedown safeMode only allows data:image/* and http(s) — NOT relative /paths,
     * so relative media door URLs get mangled or blocked. data: is on the whitelist.
     */
    function mypi_media_img_src($asset_id) {
        $data = mypi_media_data_uri($asset_id);
        if ($data !== '') {
            return $data;
        }
        return mypi_media_href($asset_id);
    }

    /**
     * HTML for attachment strip + rewrite body media: refs before Parsedown.
     *
     * @return array{html:string,body:string}
     */
    function mypi_media_prepare_body_view($body, array $meta = []) {
        $body = (string) $body;
        $atts = $meta['attachments'] ?? [];
        if (!is_array($atts)) {
            $atts = [];
        }
        // rewrite ![alt](media:ID) → data:image/...;base64,... (Parsedown safeMode allows those schemes;
        // relative /terminal/io/media?... URLs get scrubbed and look "broken")
        $body = preg_replace_callback(
            '/!\[([^\]]*)\]\(\s*media:([a-zA-Z0-9.]+)\s*\)/u',
            static function ($m) {
                $src = mypi_media_img_src($m[2]);
                if ($src === '') {
                    return '*(missing image: ' . $m[2] . ')*';
                }
                return '![' . $m[1] . '](' . $src . ')';
            },
            $body
        );
        // Obsidian ![[file.png]] → if name matches an attachment, embed it
        $byName = [];
        foreach ($atts as $a) {
            $n = strtolower((string) ($a['name'] ?? ''));
            if ($n !== '') {
                $byName[$n] = $a;
            }
        }
        $body = preg_replace_callback(
            '/!\[\[([^\]|#]+)(?:\|[^\]]+)?\]\]/u',
            static function ($m) use ($byName) {
                $name = basename(trim($m[1]));
                $key = strtolower($name);
                if (!isset($byName[$key])) {
                    return '*(missing embed: ' . $name . ')*';
                }
                $src = mypi_media_img_src($byName[$key]['asset_id'] ?? '');
                if ($src === '') {
                    return '*(missing embed: ' . $name . ')*';
                }
                return '![' . $name . '](' . $src . ')';
            },
            $body
        );

        $html = '';
        if ($atts) {
            $html .= '<div class="fk-media-strip">';
            foreach ($atts as $a) {
                $id = $a['asset_id'] ?? '';
                $src = mypi_media_img_src($id);
                if ($src === '') {
                    continue;
                }
                $nm = htmlspecialchars((string) ($a['name'] ?? $id), ENT_QUOTES, 'UTF-8');
                $html .= '<figure class="fk-media-fig">';
                $html .= '<img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" alt="' . $nm . '" loading="lazy">';
                $html .= '<figcaption>' . $nm . '</figcaption>';
                $html .= '</figure>';
            }
            $html .= '</div>';
        }

        // narrative: bag asked for IMG SUPPORT
        if (stripos($body, 'INSTALL IMG SUPPORT') !== false || stripos($body, 'ERROR, PLEASE INSTALL IMG') !== false
            || stripos($body, 'install img support') !== false) {
            if ($atts) {
                $html = '<p class="fk-img-support-ok"><strong>IMG SUPPORT ONLINE</strong> · sine continues</p>' . $html;
            } else {
                $html = '<p class="fk-img-support-wait"><strong>IMG SUPPORT READY</strong> · attach an image to continue the wave</p>' . $html;
            }
        }

        return ['html' => $html, 'body' => $body];
    }
}
