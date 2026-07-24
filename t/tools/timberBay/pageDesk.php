<?php
/**
 * timberBay · Desk — yard rows + manage panel only.
 * Facility shell owns rail chrome + three-zone layout.
 */
require_once __DIR__ . '/_state.php';
extract($GLOBALS['TBAY'], EXTR_SKIP);
?>
<div class="tbay-yard">
  <?php if ($err): ?>
    <p class="tbay-status tbay-err"><?= $h((string) $err) ?></p>
  <?php elseif ($ok !== ''): ?>
    <p class="tbay-status">ok · <?= $h($ok) ?></p>
  <?php endif; ?>

  <div class="timber-rows" role="list">
    <?php if (!$timbers): ?>
      <p class="tbay-empty">No timbers in this queue.</p>
    <?php else: ?>
      <?php foreach ($timbers as $t):
          $cid = (string) $t['c_uid'];
          $nt = (int) ($t['n_tags'] ?? 0);
          $ne = (int) ($t['n_edges'] ?? 0);
          [$bclass, $blab] = $stateBadge($nt, $ne);
          $on = ($id === $cid);
          $topic = trim((string) ($t['topic'] ?? ''));
          if ($topic === '') {
              $topic = '(no topic)';
          }
          ?>
        <a class="timber-row<?= $on ? ' is-on' : '' ?>"
           role="listitem"
           href="<?= $self ?>?<?= $h($qs(['id' => $cid])) ?>">
          <span class="timber-row-id"><?= $h($cid) ?></span>
          <span class="timber-row-topic"><?= $h($topic) ?></span>
          <span class="timber-row-meta">
            <?= $h((string) ($t['kind'] ?? '')) ?>
            · <?= $h((string) ($t['agent'] ?? '')) ?>
            · <?= $h((string) ($t['place_path'] ?? '')) ?>
          </span>
          <span class="timber-row-badges">
            <span class="tbay-badge <?= $bclass ?>"><?= $blab ?></span>
            <span class="tbay-badge">t<?= $nt ?></span>
            <span class="tbay-badge">e<?= $ne ?></span>
          </span>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<aside class="tbay-manage" aria-label="manage timber">
  <?php if (!$focus): ?>
    <div class="tbay-manage-empty">
      <p>pick a timber</p>
      <p class="tbay-manage-hint">Bare → terms → wired</p>
    </div>
  <?php else: ?>
    <div class="tbay-manage-head">
      <div class="timber-idTag">
        <?= $h((string) ($focus['kind'] ?? '')) ?>
        | <?= $h((string) ($focus['topic'] ?: '(no topic)')) ?>
      </div>
      <div class="tbay-manage-head-meta">
        <span class="tbay-badge <?= $stateBadge($nTags, $nEdges)[0] ?>"><?= $stateBadge($nTags, $nEdges)[1] ?></span>
        <span class="tbay-badge">t<?= (int) $nTags ?></span>
        <span class="tbay-badge">e<?= (int) $nEdges ?></span>
        <a class="btn" href="<?= $self ?>?<?= $h($qs(['id' => ''])) ?>">close</a>
      </div>
      <div class="timber-meta-line">
        <?= $h($id) ?><br>
        <?= $h((string) ($focus['place_path'] ?? '')) ?>
        · <?= $h((string) ($focus['agent'] ?? '')) ?>
        · <?= $h((string) ($focus['tool'] ?? '')) ?>
      </div>
    </div>

    <div class="tbay-manage-chips" aria-label="Charlie chips">
      <?php if (!$userTags && !$edges && empty($parsed['edges'])): ?>
        <span class="tbay-chip-empty">bare · no red thread</span>
      <?php endif; ?>
      <?php foreach ($userTags as $t): ?>
        <span class="tbay-chip"><?= $h($t) ?></span>
      <?php endforeach; ?>
      <?php
      $edgeShow = $edges ?: ($parsed['edges'] ?? []);
      foreach ($edgeShow as $e):
          $frm = (string) ($e['from_term'] ?? $e['from'] ?? '');
          $rel = (string) ($e['rel'] ?? '');
          $to = (string) ($e['to_term'] ?? $e['to'] ?? '');
          if ($frm === '' || $to === '') {
              continue;
          }
          ?>
        <span class="tbay-chip edge">
          <?= $h($frm) ?><span class="tbay-rel">*<?= $h($rel) ?>></span><?= $h($to) ?>
        </span>
      <?php endforeach; ?>
    </div>

    <div class="tbay-manage-body"><?php
      $body = (string) ($focus['body'] ?? '');
      if (function_exists('render_md') && $body !== '') {
          echo render_md($body);
      } else {
          echo nl2br($h($body));
      }
    ?></div>

    <form class="tbay-manage-tools" method="post" action="">
      <input type="hidden" name="tb_c_uid" value="<?= $h($id) ?>">
      <input type="hidden" name="tb_queue" value="<?= $h($queue) ?>">
      <input type="hidden" name="tb_sort" value="<?= $h($sort) ?>">
      <input type="hidden" name="tb_kind" value="<?= $h($kind) ?>">
      <input type="hidden" name="tb_agent" value="<?= $h($agent) ?>">
      <input type="hidden" name="tb_place" value="<?= $h($place) ?>">
      <input type="hidden" name="tb_q" value="<?= $h($q) ?>">
      <input type="text" name="tb_frag" required autocomplete="off"
             placeholder="aubel · lore · aubel*with>chester">
      <button type="submit" name="tb_action" value="append" class="btn">Tag+</button>
      <button type="submit" name="tb_action" value="set" class="btn" title="Replace full tags_raw">Set raw</button>
    </form>
  <?php endif; ?>
</aside>
