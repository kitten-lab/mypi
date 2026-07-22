<?php
/**
 * CHARLIE · Terms / Edges / Tag drill-down (crates + body).
 * Traverse: term → crates → tps on each row.
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

$tab = isset($_GET['tab']) ? (string) $_GET['tab'] : 'terms';
if (!in_array($tab, ['terms', 'edges', 'tag'], true)) {
    $tab = 'terms';
}
$sort = isset($_GET['sort']) ? (string) $_GET['sort'] : 'gravity';
if (!in_array($sort, ['gravity', 'term', 'recent'], true)) {
    $sort = 'gravity';
}
$focus = isset($_GET['term']) ? strtolower(trim((string) $_GET['term'])) : '';
if ($focus !== '') {
    $tab = 'tag';
}

function charlie_is_single_term($term) {
    $term = (string) $term;
    if ($term === '' || strpos($term, '*') !== false || strpos($term, '>') !== false) {
        return false;
    }
    return true;
}

function charlie_q(array $q) {
    return '?' . http_build_query($q);
}

$gravity = [];
$edges = [];
$crates = [];
$err = null;
try {
    $pdo = mypi_ledger_pdo();
    if ($sort === 'term') {
        $order = 'term ASC';
    } elseif ($sort === 'recent') {
        $order = 'updated_at DESC';
    } else {
        $order = 'gravity DESC, term ASC';
    }
    $all = $pdo->query(
        "SELECT term, gravity, updated_at FROM thread_terms ORDER BY $order LIMIT 200"
    )->fetchAll();
    foreach ($all as $g) {
        if (charlie_is_single_term($g['term'])) {
            $gravity[] = $g;
        }
        if (count($gravity) >= 100) {
            break;
        }
    }
    $edges = mypi_ledger_charlie_edges(80);
    if ($focus !== '') {
        $crates = mypi_ledger_crates_for_tag($focus, 80);
    }
} catch (Throwable $e) {
    $err = $e->getMessage();
}
?>
<section class="ledger-report charlie-report">
  <h2>CHARLIE · Threads</h2>
  <p class="muted">
    Terms are production tags (including auto place + @nicks).
    Click a term to see <strong>crates</strong> that carry it — body, place, TPS.
  </p>
  <p class="tabs">
    <a class="<?= $tab === 'terms' ? 'on' : '' ?>" href="<?= htmlspecialchars(charlie_q(['tab' => 'terms', 'sort' => $sort]), ENT_QUOTES, 'UTF-8') ?>">Terms</a>
    ·
    <a class="<?= $tab === 'edges' ? 'on' : '' ?>" href="<?= htmlspecialchars(charlie_q(['tab' => 'edges', 'sort' => $sort]), ENT_QUOTES, 'UTF-8') ?>">Edges</a>
    <?php if ($focus !== ''): ?>
      ·
      <a class="on" href="<?= htmlspecialchars(charlie_q(['tab' => 'tag', 'term' => $focus]), ENT_QUOTES, 'UTF-8') ?>">Tag: <?= htmlspecialchars($focus, ENT_QUOTES, 'UTF-8') ?></a>
    <?php endif; ?>
  </p>
<?php if ($err): ?>
  <p class="err"><?= htmlspecialchars($err) ?></p>
<?php elseif ($tab === 'tag'): ?>
  <h3>Crates tagged <code><?= htmlspecialchars($focus, ENT_QUOTES, 'UTF-8') ?></code></h3>
  <p class="muted">
    <a href="<?= htmlspecialchars(charlie_q(['tab' => 'terms', 'sort' => $sort]), ENT_QUOTES, 'UTF-8') ?>">← all terms</a>
    · <?= count($crates) ?> crate(s)
  </p>
  <?php if (!$crates): ?>
    <p>No crates on this tag (yet). Gravity can exist from older writes; tag_map is source for this list.</p>
  <?php else: ?>
    <table class="ledger-table">
      <thead>
        <tr>
          <th>when</th>
          <th>kind / tool</th>
          <th>agent</th>
          <th>place</th>
          <th>tps</th>
          <th>body</th>
          <th>c_uid</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($crates as $c):
          $when = (int) ($c['event_unix'] ?: $c['ingest_unix']);
          $body = (string) ($c['body'] ?? '');
          if (function_exists('mb_strimwidth')) {
              $preview = mb_strimwidth($body, 0, 120, '…', 'UTF-8');
          } else {
              $preview = strlen($body) > 120 ? substr($body, 0, 117) . '…' : $body;
          }
          $place = trim(($c['sys'] ?? '') . '/' . ($c['dom'] ?? '') . '/' . ($c['room'] ?? ''), '/');
      ?>
        <tr>
          <td><code><?= $when ?></code></td>
          <td><code><?= htmlspecialchars(($c['kind'] ?? '') . ' · ' . ($c['tool'] ?? ''), ENT_QUOTES, 'UTF-8') ?></code></td>
          <td><?= htmlspecialchars($c['agent'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><code><?= htmlspecialchars($place, ENT_QUOTES, 'UTF-8') ?></code></td>
          <td><code><?= htmlspecialchars($c['t_uid'] ?? '', ENT_QUOTES, 'UTF-8') ?></code></td>
          <td><?= htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') ?></td>
          <td><code><?= htmlspecialchars($c['c_uid'] ?? '', ENT_QUOTES, 'UTF-8') ?></code></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
<?php elseif ($tab === 'edges'): ?>
  <h3>Edges (relationship language)</h3>
  <?php if (!$edges): ?>
    <p>No edges yet. Example: <code>grief*connects&gt;hope</code></p>
  <?php else: ?>
    <table class="ledger-table">
      <thead>
        <tr><th>from</th><th>rel</th><th>to</th><th>c_uid</th><th>when</th></tr>
      </thead>
      <tbody>
      <?php foreach ($edges as $e): ?>
        <tr>
          <td>
            <a href="<?= htmlspecialchars(charlie_q(['tab' => 'tag', 'term' => $e['from_term']]), ENT_QUOTES, 'UTF-8') ?>">
              <code><?= htmlspecialchars($e['from_term'], ENT_QUOTES, 'UTF-8') ?></code>
            </a>
          </td>
          <td><em><?= htmlspecialchars($e['rel'], ENT_QUOTES, 'UTF-8') ?></em></td>
          <td>
            <a href="<?= htmlspecialchars(charlie_q(['tab' => 'tag', 'term' => $e['to_term']]), ENT_QUOTES, 'UTF-8') ?>">
              <code><?= htmlspecialchars($e['to_term'], ENT_QUOTES, 'UTF-8') ?></code>
            </a>
          </td>
          <td><code><?= htmlspecialchars($e['c_uid'], ENT_QUOTES, 'UTF-8') ?></code></td>
          <td><?= (int) $e['ingest_unix'] ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
<?php else: ?>
  <p class="sort">
    Sort terms:
    <a class="<?= $sort === 'gravity' ? 'on' : '' ?>" href="<?= htmlspecialchars(charlie_q(['tab' => 'terms', 'sort' => 'gravity']), ENT_QUOTES, 'UTF-8') ?>">gravity</a>
    ·
    <a class="<?= $sort === 'term' ? 'on' : '' ?>" href="<?= htmlspecialchars(charlie_q(['tab' => 'terms', 'sort' => 'term']), ENT_QUOTES, 'UTF-8') ?>">A–Z</a>
    ·
    <a class="<?= $sort === 'recent' ? 'on' : '' ?>" href="<?= htmlspecialchars(charlie_q(['tab' => 'terms', 'sort' => 'recent']), ENT_QUOTES, 'UTF-8') ?>">recent</a>
  </p>
  <h3>Terms (click → crates)</h3>
  <?php if (!$gravity): ?>
    <p>No single terms yet.</p>
  <?php else: ?>
    <table class="ledger-table">
      <thead>
        <tr><th>term</th><th>gravity</th><th>updated</th><th></th></tr>
      </thead>
      <tbody>
      <?php foreach ($gravity as $g): ?>
        <tr>
          <td>
            <a href="<?= htmlspecialchars(charlie_q(['tab' => 'tag', 'term' => $g['term']]), ENT_QUOTES, 'UTF-8') ?>">
              <strong><?= htmlspecialchars($g['term'], ENT_QUOTES, 'UTF-8') ?></strong>
            </a>
          </td>
          <td><?= (int) $g['gravity'] ?></td>
          <td><?= (int) $g['updated_at'] ?></td>
          <td><a href="<?= htmlspecialchars(charlie_q(['tab' => 'tag', 'term' => $g['term']]), ENT_QUOTES, 'UTF-8') ?>">crates →</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
<?php endif; ?>
</section>
<!-- styles: t/tools/ledgerREPORT/ledgerREPORT.css via getTool loadTool_Style -->
