<footer class="ml-footer">
  <p class="ml-foot-line">
    We are aware of you, <?= htmlspecialchars(
        defined('MOD_DISPLAY') ? MOD_DISPLAY : '-MOUSE-',
        ENT_QUOTES,
        'UTF-8'
    ) ?>.
  </p>
  <p class="ml-foot-meta">
    hosted on IMPORTED.TO · lifetime paid several thousand times over ·
    <?= date('Y-m-d H:i:s') ?> · <?= bin2hex(random_bytes(2)) ?>
  </p>
  <p class="ml-foot-links">
    <a href="/mythleak/news/headlines">HEADLINES</a>
    ·
    <a href="/mythleak/news/write">FILE A LEAK</a>
    ·
    <span class="ml-distort">OUAVA</span>
  </p>
</footer>
