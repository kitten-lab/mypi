<?php
/**
 * venDesk — Oriel's code book (z/ only · not prime ledger bodies)
 * KVEN · alts · label · matches · notes · type
 */

if (!function_exists('vendesk_paths')) {
    function vendesk_paths(): array {
        $root = rtrim(str_replace('\\', '/', echoSONAR), '/');
        $dir = $root . '/z/ven_registry';
        return [
            'dir' => $dir,
            'registry' => $dir . '/registry.json',
        ];
    }

    function vendesk_ensure_dir(): void {
        $dir = vendesk_paths()['dir'];
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }

    /**
     * @return array{version:int,updated_at:int,entries:list<array>}
     */
    function vendesk_load(): array {
        vendesk_ensure_dir();
        $p = vendesk_paths()['registry'];
        if (!is_file($p)) {
            return ['version' => 1, 'updated_at' => 0, 'entries' => []];
        }
        $j = json_decode((string) file_get_contents($p), true);
        if (!is_array($j)) {
            return ['version' => 1, 'updated_at' => 0, 'entries' => []];
        }
        if (!isset($j['entries']) || !is_array($j['entries'])) {
            // migrate map-shaped fossils { "ABL-000": {...} }
            $entries = [];
            foreach ($j as $k => $v) {
                if ($k === 'version' || $k === 'updated_at' || $k === 'entries') {
                    continue;
                }
                if (!is_array($v)) {
                    continue;
                }
                $entries[] = vendesk_normalize_entry(array_merge($v, [
                    'kven' => (string) ($v['kven'] ?? $k),
                ]));
            }
            return ['version' => 1, 'updated_at' => time(), 'entries' => $entries];
        }
        $out = [];
        foreach ($j['entries'] as $e) {
            if (is_array($e)) {
                $out[] = vendesk_normalize_entry($e);
            }
        }
        return [
            'version' => (int) ($j['version'] ?? 1),
            'updated_at' => (int) ($j['updated_at'] ?? 0),
            'entries' => $out,
        ];
    }

    function vendesk_save(array $reg): bool {
        vendesk_ensure_dir();
        $reg['version'] = (int) ($reg['version'] ?? 1);
        $reg['updated_at'] = time();
        $entries = [];
        foreach ($reg['entries'] ?? [] as $e) {
            if (is_array($e)) {
                $entries[] = vendesk_normalize_entry($e);
            }
        }
        // sort by kven
        usort($entries, static fn($a, $b) => strcmp($a['kven'], $b['kven']));
        $reg['entries'] = $entries;
        $p = vendesk_paths()['registry'];
        return file_put_contents(
            $p,
            json_encode($reg, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        ) !== false;
    }

    /** @return list<string> */
    function vendesk_split_list(string $raw): array {
        $parts = preg_split('/[,;|]+/', $raw) ?: [];
        $out = [];
        foreach ($parts as $p) {
            $t = trim($p);
            if ($t !== '') {
                $out[] = $t;
            }
        }
        return array_values(array_unique($out));
    }

    function vendesk_normalize_kven(string $kven): string {
        $kven = strtoupper(trim($kven));
        $kven = preg_replace('/\s+/', '', $kven) ?? '';
        // allow ABC-123 or loose ABC123
        if (preg_match('/^([A-Z]{3})-?(\d{3})$/', $kven, $m)) {
            return $m[1] . '-' . $m[2];
        }
        return $kven;
    }

    function vendesk_valid_kven(string $kven): bool {
        return (bool) preg_match('/^[A-Z]{3}-\d{3}$/', vendesk_normalize_kven($kven));
    }

    /**
     * @param array $e
     * @return array{id:string,kven:string,alts:list<string>,label:string,matches:list<string>,notes:string,type:string,created:int,updated:int}
     */
    function vendesk_normalize_entry(array $e): array {
        // accept old ven.* keys
        $kven = (string) ($e['kven'] ?? $e['ven.key'] ?? '');
        if ($kven === '' && !empty($e['ven.keyTYPE'])) {
            // fossil ABL-000 shape used key as map key only
        }
        $label = (string) ($e['label'] ?? $e['ven.keyLABEL'] ?? $e['ven.scrubLABEL'] ?? '');
        $alts = $e['alts'] ?? $e['ven.keyALTS'] ?? [];
        if (is_string($alts)) {
            $alts = vendesk_split_list($alts);
        }
        if (!is_array($alts)) {
            $alts = [];
        }
        $matches = $e['matches'] ?? [];
        if (is_string($matches)) {
            $matches = vendesk_split_list($matches);
        }
        if (!is_array($matches)) {
            $matches = [];
        }
        $type = (string) ($e['type'] ?? $e['ven.keyTYPE'] ?? 'other');
        $notes = (string) ($e['notes'] ?? $e['ven.keyNOTES'] ?? '');
        $id = (string) ($e['id'] ?? '');
        if ($id === '') {
            try {
                $id = 'v.' . strtoupper(bin2hex(random_bytes(4)));
            } catch (Throwable $ex) {
                $id = 'v.' . strtoupper(dechex(mt_rand()) . dechex(time()));
            }
        }
        $kvenN = vendesk_normalize_kven($kven);
        return [
            'id' => $id,
            'kven' => $kvenN,
            'alts' => array_values(array_filter(array_map('strval', $alts))),
            'label' => trim($label),
            'matches' => array_values(array_filter(array_map('strval', $matches))),
            'notes' => $notes,
            'type' => strtolower(trim($type)) !== '' ? strtolower(trim($type)) : 'other',
            'created' => (int) ($e['created'] ?? $e['created_at'] ?? time()),
            'updated' => (int) ($e['updated'] ?? $e['updated_at'] ?? time()),
        ];
    }

    function vendesk_find(array $reg, string $idOrKven): ?array {
        $key = trim($idOrKven);
        $kven = vendesk_normalize_kven($key);
        foreach ($reg['entries'] as $e) {
            if (($e['id'] ?? '') === $key || ($e['kven'] ?? '') === $kven) {
                return $e;
            }
        }
        return null;
    }

    function vendesk_mint_kven(array $reg, string $hint = ''): string {
        $letters = preg_replace('/[^a-zA-Z]/', '', $hint) ?? '';
        $prefix = strtoupper(substr($letters !== '' ? $letters : 'VEN', 0, 3));
        if (strlen($prefix) < 3) {
            $prefix = str_pad($prefix, 3, 'X');
        }
        $used = [];
        foreach ($reg['entries'] as $e) {
            $used[(string) ($e['kven'] ?? '')] = true;
        }
        for ($n = 1; $n < 1000; $n++) {
            $code = $prefix . '-' . sprintf('%03d', $n);
            if (empty($used[$code])) {
                return $code;
            }
        }
        return $prefix . '-' . sprintf('%03d', random_int(100, 999));
    }
}
