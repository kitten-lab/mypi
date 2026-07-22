<?php
$bkTitle = defined('SYS_ID') ? strtoupper(SYS_ID) : 'BOOK';
$bkTag = defined('SYS_TAG') ? SYS_TAG : (defined('WORLD_TAG') ? WORLD_TAG : 'Fragments into form');
?>
<header class="bk-header" role="banner">
  <div class="bk-header-top">
    <div class="bk-brand">
      <span class="bk-ornament" aria-hidden="true">❧</span>
      <div class="bk-brand-text">
        <h1 class="bk-title"><?= htmlspecialchars($bkTitle) ?></h1>
        <p class="bk-tagline"><?= htmlspecialchars($bkTag) ?></p>
      </div>
    </div>
    <p class="bk-motto">collect · bind · read</p>
  </div>
</header>
<!-- fixed header: shell CSS uses --bk-header-h; keep compact so panes clear it -->
