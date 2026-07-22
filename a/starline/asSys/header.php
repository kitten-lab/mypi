<?php
// Brand + webBAR chrome. Room links live in top-nav.php (pills under the bar).
$slTitle = defined('SYS_ID') ? strtoupper(SYS_ID) : 'STARLINE';
$slTag = defined('SYS_TAG') ? SYS_TAG : (defined('WORLD_TAG') ? WORLD_TAG : '');
?>
<header class="sl-header" role="banner">
  <div class="sl-header-row sl-brand-row">
    <div class="sl-brand">
      <span class="sl-brand-mark" aria-hidden="true"></span>
      <div class="sl-brand-text">
        <h1 class="sl-title"><?= htmlspecialchars($slTitle) ?></h1>
        <?php if ($slTag !== ''): ?>
          <p class="sl-tagline"><?= htmlspecialchars($slTag) ?></p>
        <?php endif; ?>
      </div>
    </div>
    <div class="sl-status" title="surface status">
      <span class="sl-status-dot" aria-hidden="true"></span>
      <span class="sl-status-label">citadel</span>
    </div>
  </div>

  <div class="sl-header-row sl-webbar-row">
    <div class="wwwExplorer_linkBar sl-webbar" role="navigation" aria-label="Address bar">
      <div role="button" tabindex="0" class="sl-wb-btn" data-webbar="back"
           onclick="WWWBack()" onkeydown="if(event.key==='Enter')WWWBack()" title="Back">←</div>
      <div role="button" tabindex="0" class="sl-wb-btn" data-webbar="forward"
           onclick="WWWForward()" onkeydown="if(event.key==='Enter')WWWForward()" title="Forward">→</div>
      <div role="button" tabindex="0" class="sl-wb-btn" id="REFRESH" data-webbar="refresh"
           onclick="if(event.shiftKey||event.ctrlKey||event.altKey){WWWHardRefresh()}else{WWWRefresh()}"
           onkeydown="if(event.key==='Enter')WWWRefresh()"
           title="Refresh · Shift/Ctrl+click = hard (CSS/JS cache bust)">↻</div>
      <span id="wwwBar" class="linkSlug sl-path" contenteditable="true" spellcheck="false"
            role="textbox" aria-label="Path"></span>
      <div role="button" tabindex="0" class="sl-wb-btn sl-wb-go" id="GO"
           data-webbar="go" onclick="LetsGO()" onkeydown="if(event.key==='Enter')LetsGO()" title="Go">GO</div>
    </div>
  </div>

  <?php include __DIR__ . '/top-nav.php'; ?>
</header>
