<?php
/**
 * ROM stage — system-wide toy placement (any surface).
 *
 * Sky install (like getTool):
 *   romStage();
 *   placeToy('MRA-001', 'Julie');
 *   placeToy('KCD-001', 'ClassicBoi');
 *
 * Catalog (shells per ROM): scan dressUps, optional t/toys/_catalog.json
 */

/**
 * One stage per room. Windows open here; does not navigate away.
 */
function romStage(string $stageId = 'rom-stage'): void {
    static $once = false;
    if ($once) {
        return;
    }
    $once = true;

    $id = preg_replace('/[^A-Za-z0-9_-]/', '', $stageId) ?: 'rom-stage';

    // CSS for stage + windows
    $GLOBALS['GETS']['dressing'][] = function () {
        $href = (defined('K_ROUTE') ? K_ROUTE : 'http://k') . '/kittens/romWindow.kitten.css';
        $full = echoSONAR . 'k/kittens/romWindow.kitten.css';
        if (is_file($full)) {
            // prefer inline so pocket browser does not need host k for CSS
            $css = file_get_contents($full);
            echo "<!-- romWindow css (inline) -->\n<style>\n" . $css . "\n</style>\n";
        } else {
            echo '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">';
        }
    };

    // Host kitten (inline via callKitten)
    if (function_exists('callKitten')) {
        callKitten('romWindow');
    }

    // Publish catalog JSON for JS (shells available)
    $catalog = scanToyCatalog();
    $GLOBALS['GETS']['scripts'][] = function () use ($catalog) {
        echo '<script>window.TOY_CATALOG = '
            . json_encode($catalog, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            . ';</script>' . "\n";
    };

    skylite(
        '<div id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8')
        . '" class="rom-stage" data-rom-stage="1" aria-label="ROM stage"></div>'
    );

    // back-compat: empty ROM_SCREEN still exists as alias for old kits mid-migrate
    skylite('<div id="ROM_SCREEN" class="ROM_SCREEN rom-screen-legacy" hidden aria-hidden="true"></div>');
}

/**
 * Place a toy package on the current surface (cover + kit + dress-up).
 * Requires romStage() earlier on the same page (or it will call it).
 */
function placeToy(string $toy, string $dressUp = 'ClassicBoi'): void {
    romStage(); // safe no-op if already staged
    if (function_exists('displayToy')) {
        displayToy($toy, $dressUp);
    }
}

/**
 * List dress-up shells available for one toy (from dressUps/*_SHELL.box.php).
 *
 * @return list<string>
 */
function listToyShells(string $toy): array {
    $dir = echoSONAR . 't/toys/' . $toy . '/dressUps/';
    if (!is_dir($dir)) {
        return [];
    }
    $shells = [];
    foreach (glob($dir . $toy . '_*.box.php') ?: [] as $file) {
        $base = basename($file, '.box.php'); // MRA-001_JULIE
        $prefix = $toy . '_';
        if (str_starts_with($base, $prefix)) {
            $shells[] = strtoupper(substr($base, strlen($prefix)));
        }
    }
    sort($shells);
    return array_values(array_unique($shells));
}

/**
 * Full catalog: every toy id under t/toys with shells + optional kit presence.
 * Merges hand-written t/toys/_catalog.json if present (fills titles/notes).
 *
 * @return array<string, array{id:string,shells:list<string>,hasKit:bool,title?:string,notes?:string}>
 */
function scanToyCatalog(): array {
    $root = echoSONAR . 't/toys/';
    $out = [];
    if (!is_dir($root)) {
        return $out;
    }

    $hand = [];
    $handPath = $root . '_catalog.json';
    if (is_file($handPath)) {
        $decoded = json_decode((string) file_get_contents($handPath), true);
        if (is_array($decoded)) {
            $hand = $decoded;
        }
    }

    foreach (scandir($root) ?: [] as $name) {
        if ($name === '.' || $name === '..' || $name[0] === '-' || $name[0] === '_') {
            continue;
        }
        $dir = $root . $name;
        if (!is_dir($dir)) {
            continue;
        }
        // skip legacy tree
        if ($name === 'v1' || $name === '-v1') {
            continue;
        }
        $shells = listToyShells($name);
        $hasKit = is_file($dir . '/' . $name . '.kit.js');
        $entry = [
            'id' => $name,
            'shells' => $shells,
            'hasKit' => $hasKit,
        ];
        if (!empty($hand[$name]) && is_array($hand[$name])) {
            $entry = array_merge($entry, $hand[$name]);
            // scan always wins for shells/hasKit if present on disk
            $entry['shells'] = $shells;
            $entry['hasKit'] = $hasKit;
            $entry['id'] = $name;
        }
        $out[$name] = $entry;
    }

    return $out;
}

/**
 * Sky helper: print a tiny human catalog (for play / debug rooms).
 */
function showToyCatalog(): void {
    $cat = scanToyCatalog();
    $html = "<div class='toy-catalog'><strong>Toy / ROM catalog</strong><ul>";
    foreach ($cat as $id => $row) {
        $shells = implode(', ', $row['shells'] ?? []);
        $title = htmlspecialchars($row['title'] ?? $id, ENT_QUOTES, 'UTF-8');
        $kit = !empty($row['hasKit']) ? 'kit' : 'no kit';
        $html .= '<li><code>' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '</code>'
            . ' — ' . $title
            . ' · shells: ' . htmlspecialchars($shells ?: '(none)', ENT_QUOTES, 'UTF-8')
            . ' · ' . $kit
            . '</li>';
    }
    $html .= '</ul></div>';
    skylite($html);
}

/** @deprecated use romStage — kept so old rooms do not fatally break */
if (!function_exists('ROM_SCREEN')) {
    function ROM_SCREEN(): void {
        romStage();
    }
}
