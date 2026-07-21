<?php
/**
 * mypi ledger — single SQLite store (shared with Python mypi_tui).
 * Cosmology fields: sys, dom, room, mod (not world/block in new writes).
 * DB: d/_LEDGER/mypi.sqlite
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
        return $dir . '/mypi.sqlite';
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
CREATE INDEX IF NOT EXISTS idx_crates_sys ON crates(sys);
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
SQL
        );
        // Add columns if DB was created by older Python schema
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
        $pdo->prepare(
            'INSERT INTO ledger_meta(key, value) VALUES(?, ?)
             ON CONFLICT(key) DO UPDATE SET value=excluded.value'
        )->execute(['schema_version', '1']);
    }

    function mypi_ledger_new_cuid() {
        return 'crate.' . strtoupper(bin2hex(random_bytes(8)));
    }

    function mypi_ledger_parse_tags($tags_raw, $sys, $dom, $room, $mod) {
        $tags = [];
        $raw = trim(str_replace(["\r", "\n", "\t"], ' ', (string) $tags_raw));
        if ($raw !== '') {
            foreach (preg_split('/[\s,]+/', $raw) as $part) {
                $t = ltrim(trim($part), '#');
                if ($t !== '' && !in_array($t, $tags, true)) {
                    $tags[] = $t;
                }
            }
        }
        // Half tagging: where posted (SYS/DOM/ROOM/MOD chain)
        $path = trim(implode('/', array_filter([$sys, $dom, $room], 'strlen')), '/');
        if ($path !== '') {
            $tags[] = 'path:' . $path;
            foreach (explode('/', $path) as $seg) {
                if ($seg !== '' && !in_array('@' . $seg, $tags, true)) {
                    $tags[] = '@' . $seg;
                }
            }
        }
        if ($mod !== '') {
            $tags[] = 'mod:' . $mod;
        }
        if ($sys !== '') {
            $tags[] = 'sys:' . $sys;
        }
        if ($dom !== '') {
            $tags[] = 'dom:' . $dom;
        }
        return $tags;
    }

    /**
     * @return array{ok:bool,c_uid?:string,error?:string}
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
        $actor = (string) ($in['actor'] ?? $mod ?: 'hands');
        $tz = (string) ($in['timezone'] ?? '');
        $ingest = time();
        $event = isset($in['event_unix']) && $in['event_unix'] !== '' && $in['event_unix'] !== null
            ? (int) $in['event_unix']
            : $ingest;
        $t_uid = $event . '.tps';
        $c_uid = mypi_ledger_new_cuid();
        $tags = mypi_ledger_parse_tags($tags_raw, $sys, $dom, $room, $mod);
        $pdo = mypi_ledger_pdo();
        $pdo->prepare(
            'INSERT INTO crates(
              c_uid, kind, topic, body, agent, tool, tool_version,
              place_path, place_label, sys, dom, room, mod,
              tags_json, tags_raw, event_unix, ingest_unix, timezone, t_uid, meta_json,
              created_at, updated_at
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
        )->execute([
            $c_uid, $kind, $topic, $body, $agent, $tool, (int) ($in['tool_version'] ?? 4),
            $place_path, $place_label, $sys, $dom, $room, $mod,
            json_encode($tags), $tags_raw, $event, $ingest, $tz, $t_uid,
            json_encode($in['meta'] ?? new stdClass()),
            $ingest, $ingest,
        ]);
        $insTag = $pdo->prepare('INSERT OR IGNORE INTO tag_map(c_uid, tag) VALUES(?,?)');
        foreach ($tags as $t) {
            $insTag->execute([$c_uid, $t]);
        }
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
        return ['ok' => true, 'c_uid' => $c_uid];
    }

    /**
     * @return list<array>
     */
    function mypi_ledger_list(array $opts = []) {
        $pdo = mypi_ledger_pdo();
        $limit = (int) ($opts['limit'] ?? 50);
        $sys = $opts['sys'] ?? null;
        $dom = $opts['dom'] ?? null;
        $room = $opts['room'] ?? null;
        $kind = $opts['kind'] ?? null;
        $includeDeleted = !empty($opts['include_deleted']);
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
        if ($kind !== null && $kind !== '') {
            $sql .= ' AND kind = ?';
            $args[] = $kind;
        }
        $sql .= ' ORDER BY ingest_unix DESC LIMIT ' . max(1, min(200, $limit));
        $st = $pdo->prepare($sql);
        $st->execute($args);
        return $st->fetchAll();
    }

    function mypi_ledger_get($c_uid) {
        $st = mypi_ledger_pdo()->prepare('SELECT * FROM crates WHERE c_uid = ?');
        $st->execute([$c_uid]);
        $row = $st->fetch();
        return $row ?: null;
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
     * Hard-delete: removes crate, tags, events. Snapshot kept in deleted_log only.
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
}
