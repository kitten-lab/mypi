<?php
/**
 * timberBay · Rail — tool controls only (queues + filters).
 * Facility chrome (MAIL / ROOM labels) lives in a/mailroom/asSys shell.
 */
require_once __DIR__ . '/_state.php';
extract($GLOBALS['TBAY'], EXTR_SKIP);
?>
<div class="tbay-rail-tool">
  <div class="tbay-rail-label">queue</div>
  <ul class="tbay-rail-nav">
    <?php foreach (['all' => 'All', 'bare' => 'Bare', 'terms' => 'Terms', 'wired' => 'Wired'] as $k => $lab): ?>
      <li>
        <a class="<?= $queue === $k ? 'is-on' : '' ?>"
           href="<?= $self ?>?<?= $h($qs(['queue' => $k, 'id' => $id])) ?>"><?= $lab ?></a>
      </li>
    <?php endforeach; ?>
  </ul>

  <div class="tbay-rail-label">filter</div>
  <form class="tbay-rail-filters" method="get" action="">
    <input type="hidden" name="queue" value="<?= $h($queue) ?>">
    <input type="hidden" name="id" value="<?= $h($id) ?>">
    <label class="tbay-flab">search</label>
    <input type="search" name="q" value="<?= $h($q) ?>" placeholder="topic · body · id">
    <label class="tbay-flab">sort</label>
    <select name="sort">
      <?php foreach (['ingest' => 'Newest', 'event' => 'Event', 'tags' => 'Most tags', 'edges' => 'Most edges', 'kind' => 'Kind', 'place' => 'Place'] as $k => $lab): ?>
        <option value="<?= $k ?>"<?= $sort === $k ? ' selected' : '' ?>><?= $lab ?></option>
      <?php endforeach; ?>
    </select>
    <label class="tbay-flab">kind</label>
    <input type="text" name="kind" value="<?= $h($kind) ?>" placeholder="kind">
    <label class="tbay-flab">agent</label>
    <input type="text" name="agent" value="<?= $h($agent) ?>" placeholder="agent">
    <label class="tbay-flab">place</label>
    <input type="text" name="place" value="<?= $h($place) ?>" placeholder="place">
    <button type="submit">go</button>
  </form>
</div>
