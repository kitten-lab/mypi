<?php
// Unified b-front hrefs: /{SYS}/{DOM}/{KEY}
if (is_file(dirname(__DIR__, 2) . '/_/href_local.php')) {
    require_once dirname(__DIR__, 2) . '/_/href_local.php';
}
$h = function ($dom, $key) {
    return function_exists('mypi_room_href') ? mypi_room_href($dom, $key) : ('/starline/' . $dom . '/' . $key);
};

// Highlight current DOM/KEY from path when possible
$path = $_SERVER['REQUEST_URI'] ?? '';
$pathOnly = parse_url($path, PHP_URL_PATH) ?: $path;
$pathOnly = preg_replace('#^/starline(?=/|$)#i', '', $pathOnly);
$pathOnly = '/' . trim($pathOnly, '/');
$activeDom = '';
$activeKey = '';
if (preg_match('#^/([a-z0-9_-]+)(?:/([a-z0-9_-]+))?#i', $pathOnly, $m)) {
    $activeDom = strtolower($m[1]);
    $activeKey = isset($m[2]) ? strtolower($m[2]) : '';
}
$items = [
    ['dom' => 'news', 'key' => 'headlines', 'label' => 'News'],
    ['dom' => 'chester', 'key' => 'crates', 'label' => 'Crates'],
    ['dom' => 'charlie', 'key' => 'threads', 'label' => 'Charlie'],
    ['dom' => 'satora', 'key' => 'shelves', 'label' => 'TPS'],
    ['dom' => 'offices', 'key' => 'frontdesk', 'label' => 'Front Desk'],
    ['dom' => 'offices', 'key' => 'directory', 'label' => 'Directory'],
    ['dom' => 'offices', 'key' => 'meetingroom', 'label' => 'Meeting'],
    ['dom' => 'offices', 'key' => 'authdesk', 'label' => 'Auth desk'],
];
?>
<nav class="sl-room-nav navigation" aria-label="Starline rooms">
  <ul class="sl-room-list">
    <?php foreach ($items as $it):
        // multi-room DOMs (offices): match KEY; single-room DOMs: match DOM
        if ($activeDom === $it['dom'] && $activeKey !== '') {
            $isActive = ($activeKey === $it['key']);
        } elseif ($activeDom === $it['dom']) {
            $isActive = true;
        } else {
            $isActive = false;
        }
        ?>
      <li class="nav-item sl-room-item<?= $isActive ? ' is-active' : '' ?>">
        <a href="<?= htmlspecialchars($h($it['dom'], $it['key'])) ?>"><?= htmlspecialchars($it['label']) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>
