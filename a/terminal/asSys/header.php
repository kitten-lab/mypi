<?php
$tmTitle = defined('SYS_ID') ? strtoupper(SYS_ID) : 'TERMINAL';
$domLabel = defined('DOM_DISPLAY') ? DOM_DISPLAY : (defined('DOM_SLUG') ? DOM_SLUG : 'base');
$roomLabel = defined('ROOM_DISPLAY') ? ROOM_DISPLAY : '';
$agent = defined('MOD_DISPLAY') ? MOD_DISPLAY : '';
$domSlug = defined('DOM_SLUG') ? DOM_SLUG : 'base';
?>
<header class="tm-header" role="banner">
  <div class="tm-header-row">
    <div class="tm-brand">
      <span class="tm-prompt" aria-hidden="true">&gt;_</span>
      <div class="tm-brand-text">
        <h1 class="tm-title"><?= htmlspecialchars($tmTitle) ?></h1>
        <p class="tm-sub">
          <span class="tm-dom"><?= htmlspecialchars(strtoupper((string) $domSlug)) ?></span>
          <?php if ($domLabel !== ''): ?>
            <span class="tm-sep">·</span>
            <span class="tm-dom-label"><?= htmlspecialchars($domLabel) ?></span>
          <?php endif; ?>
          <?php if ($roomLabel !== ''): ?>
            <span class="tm-sep">·</span>
            <span class="tm-room"><?= htmlspecialchars($roomLabel) ?></span>
          <?php endif; ?>
        </p>
      </div>
    </div>
    <div class="tm-agent" title="logged face">
      <span class="tm-agent-dot" aria-hidden="true"></span>
      <span class="tm-agent-name"><?= htmlspecialchars($agent !== '' ? $agent : 'NO AGENT') ?></span>
    </div>
  </div>
  <div class="tm-webbar-row">
    <div class="wwwExplorer_linkBar tm-webbar" role="navigation" aria-label="Path">
      <div role="button" tabindex="0" class="tm-wb-btn" data-webbar="back"
           onclick="WWWBack()" onkeydown="if(event.key==='Enter')WWWBack()" title="Back">←</div>
      <div role="button" tabindex="0" class="tm-wb-btn" data-webbar="forward"
           onclick="WWWForward()" onkeydown="if(event.key==='Enter')WWWForward()" title="Forward">→</div>
      <div role="button" tabindex="0" class="tm-wb-btn" id="REFRESH" data-webbar="refresh"
           onclick="if(event.shiftKey||event.ctrlKey||event.altKey){WWWHardRefresh()}else{WWWRefresh()}"
           onkeydown="if(event.key==='Enter')WWWRefresh()"
           title="Refresh · Shift/Ctrl = hard">↻</div>
      <span id="wwwBar" class="linkSlug tm-path" contenteditable="true" spellcheck="false"
            role="textbox" aria-label="Path"></span>
      <div role="button" tabindex="0" class="tm-wb-btn tm-wb-go" id="GO"
           data-webbar="go" onclick="LetsGO()" onkeydown="if(event.key==='Enter')LetsGO()" title="Go">GO</div>
    </div>
  </div>
</header>
