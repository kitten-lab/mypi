<?php
/**
 * Side list inside wwwExplorer_innerShell (master chrome).
 * Path hrefs for b-front; markup simple so VT323 chrome stays primary.
 */
if (is_file(dirname(__DIR__, 2) . '/_/href_local.php')) {
    require_once dirname(__DIR__, 2) . '/_/href_local.php';
}

$nav = $GLOBALS[BLOCK_ID]['NAV'] ?? [];
if (isset($nav['navSec'])) {
    $nav = isset($nav['navSec'][0]) ? $nav['navSec'] : [$nav['navSec']];
}
?>
<nav class="www-side" style="font-size:1.2vh;margin-bottom:1vh;opacity:0.9">
<?php foreach ($nav as $section): ?>
  <?php if (!is_array($section) || empty($section['DOM'])) continue;
    $dom = $section['DOM'];
    $prime = $section['KEY'] ?? ($section['ROOMS'][0]['KEY'] ?? $dom);
    $bh = function_exists('mypi_room_href') ? mypi_room_href($dom, $prime) : ('/www/' . $dom . '/' . $prime);
  ?>
  <div><a href="<?= htmlspecialchars($bh, ENT_QUOTES, 'UTF-8') ?>" style="color:#8cf"><?= htmlspecialchars($section['BUILDING'] ?? $dom, ENT_QUOTES, 'UTF-8') ?></a></div>
  <?php foreach ($section['ROOMS'] ?? [] as $item):
    $k = $item['KEY'] ?? '';
    $h = function_exists('mypi_room_href') ? mypi_room_href($dom, $k) : ('/www/' . $dom . '/' . $k);
    $label = $item['ROOM'] ?? $k;
  ?>
    <div style="padding-left:1em">- <a href="<?= htmlspecialchars($h, ENT_QUOTES, 'UTF-8') ?>" style="color:#6af"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a></div>
  <?php endforeach; ?>
<?php endforeach; ?>
</nav>
