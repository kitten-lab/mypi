<?php
/**
 * Book shelf — scan m/doors/book for page doors (DOM/KEY.php).
 * Cosmology: SYS book / DOM / ROOM. Links via mypi_room_href.
 */
if (is_file(dirname(__DIR__, 2) . '/_/href_local.php')) {
    require_once dirname(__DIR__, 2) . '/_/href_local.php';
}

/**
 * @return array<string, list<array{key:string,label:string,path:string}>>
 */
function book_scan_pages(): array
{
    $roots = [];
    if (defined('echoSONAR')) {
        $roots[] = rtrim(echoSONAR, '/\\') . DIRECTORY_SEPARATOR . 'm' . DIRECTORY_SEPARATOR . 'doors' . DIRECTORY_SEPARATOR . 'book';
    }
    $roots[] = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'm' . DIRECTORY_SEPARATOR . 'doors' . DIRECTORY_SEPARATOR . 'book';
    // Builds vs htdocs junction
    $roots[] = 'C:' . DIRECTORY_SEPARATOR . 'Builds' . DIRECTORY_SEPARATOR . 'my-pocket-internet' . DIRECTORY_SEPARATOR
        . 'm' . DIRECTORY_SEPARATOR . 'doors' . DIRECTORY_SEPARATOR . 'book';

    $root = null;
    foreach ($roots as $cand) {
        if (is_dir($cand)) {
            $root = $cand;
            break;
        }
    }
    if ($root === null) {
        return [];
    }

    $byDom = [];
    $domDirs = scandir($root) ?: [];
    sort($domDirs, SORT_NATURAL | SORT_FLAG_CASE);

    foreach ($domDirs as $dom) {
        if ($dom === '.' || $dom === '..' || $dom[0] === '.') {
            continue;
        }
        $domPath = $root . DIRECTORY_SEPARATOR . $dom;
        if (!is_dir($domPath)) {
            continue;
        }
        $files = scandir($domPath) ?: [];
        sort($files, SORT_NATURAL | SORT_FLAG_CASE);
        $pages = [];
        foreach ($files as $file) {
            if (!preg_match('/^(.+)\.php$/i', $file, $m)) {
                continue;
            }
            $key = $m[1];
            if ($key === '' || $key[0] === '-' || $key[0] === '_') {
                continue;
            }
            $pages[] = [
                'key' => $key,
                'label' => book_human_label($key),
                'path' => $dom . '/' . $key,
            ];
        }
        if ($pages) {
            $byDom[$dom] = $pages;
        }
    }

    return $byDom;
}

function book_human_label(string $key): string
{
    $key = str_replace(['_', '-'], ' ', $key);
    $key = preg_replace('/\s+/', ' ', $key) ?? $key;
    return ucwords(trim($key));
}

function book_section_title(string $dom): string
{
    $map = [
        'fragments' => 'Fragment sheets',
        'terminal_girls' => 'Terminal girls',
        'scenes' => 'Scenes',
        'tavern' => 'Tavern',
        'hidden' => 'Hidden',
    ];
    if (isset($map[$dom])) {
        return $map[$dom];
    }
    return book_human_label($dom);
}

$path = $_SERVER['REQUEST_URI'] ?? '';
$pathOnly = parse_url($path, PHP_URL_PATH) ?: $path;
$pathOnly = preg_replace('#^/book(?=/|$)#i', '', $pathOnly);
$pathOnly = '/' . trim($pathOnly, '/');
$activeDom = '';
$activeKey = '';
if (preg_match('#^/([a-z0-9_-]+)(?:/([a-z0-9_-]+))?#i', $pathOnly, $m)) {
    $activeDom = strtolower($m[1]);
    $activeKey = isset($m[2]) ? strtolower($m[2]) : '';
}

$shelf = book_scan_pages();
$h = function ($dom, $key) {
    return function_exists('mypi_room_href')
        ? mypi_room_href($dom, $key)
        : ('/book/' . $dom . '/' . $key);
};
?>
<div class="bk-shelf">
  <?php /* webBAR lives ON THE SHELF (not header) — wacky layout experiment */ ?>
  <div class="bk-shelf-webbar">
    <p class="bk-shelf-webbar-label">Wayfinding</p>
    <div class="wwwExplorer_linkBar bk-webbar bk-webbar-stack" role="navigation" aria-label="Address on the shelf">
      <span id="wwwBar" class="linkSlug bk-path" contenteditable="true" spellcheck="false"
            role="textbox" aria-label="Path"></span>
      <div class="bk-wb-row">
        <div role="button" tabindex="0" class="bk-wb-btn" data-webbar="back"
             onclick="WWWBack()" onkeydown="if(event.key==='Enter')WWWBack()" title="Back">←</div>
        <div role="button" tabindex="0" class="bk-wb-btn" data-webbar="forward"
             onclick="WWWForward()" onkeydown="if(event.key==='Enter')WWWForward()" title="Forward">→</div>
        <div role="button" tabindex="0" class="bk-wb-btn" id="REFRESH" data-webbar="refresh"
             onclick="if(event.shiftKey||event.ctrlKey||event.altKey){WWWHardRefresh()}else{WWWRefresh()}"
             onkeydown="if(event.key==='Enter')WWWRefresh()"
             title="Refresh · Shift/Ctrl+click = hard">↻</div>
        <div role="button" tabindex="0" class="bk-wb-btn bk-wb-go" id="GO"
             data-webbar="go" onclick="LetsGO()" onkeydown="if(event.key==='Enter')LetsGO()" title="Go">GO</div>
      </div>
    </div>
  </div>

  <h2 class="bk-shelf-title">On the shelf</h2>
  <p class="bk-shelf-note">Live from <code>m/doors/book</code></p>

  <?php if (!$shelf): ?>
    <p class="bk-shelf-empty">No pages found.</p>
  <?php else: ?>
    <?php foreach ($shelf as $dom => $pages): ?>
      <section class="bk-section<?= $dom === 'hidden' ? ' bk-section-hidden' : '' ?>">
        <h3 class="bk-section-title"><?= htmlspecialchars(book_section_title($dom)) ?></h3>
        <ul class="bk-page-list">
          <?php foreach ($pages as $page):
              $isActive = ($activeDom === strtolower($dom) && $activeKey === strtolower($page['key']));
              ?>
            <li class="bk-page-item<?= $isActive ? ' is-active' : '' ?>">
              <a href="<?= htmlspecialchars($h($dom, $page['key'])) ?>">
                <span class="bk-page-label"><?= htmlspecialchars($page['label']) ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
