<?php
/**
 * CHARLIE · Terms (single) + Edges — tool page: echo OK.
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

$tab = isset($_GET['tab']) ? (string) $_GET['tab'] : 'terms';
if (!in_array($tab, ['terms', 'edges'], true)) {
    $tab = 'terms';
}
$sort = isset($_GET['sort']) ? (string) $_GET['sort'] : 'gravity';
if (!in_array($sort, ['gravity', 'term', 'recent'], true)) {
    $sort = 'gravity';
}

function charlie_is_single_term($term) {
    $term = (string) $term;
    if ($term === '' || strpos($term, '*') !== false || strpos($term, '>') !== false) {
        return false;
    }
    return true;
}

function charlie_q($tab, $sort) {
    return '?tab=' . rawurlencode($tab) . '&sort=' . rawurlencode($sort);
}

$gravity = [];
$edges = [];
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
} catch (Throwable $e) {
    $err = $e->getMessage();
}
?>
<section class="ledger-report charlie-report">
  <h2>CHARLIE · Threads</h2>
  <p class="muted">
    Direct tags + relationship edges. Full <code>this*rel&gt;that</code> chains only under <strong>Edges</strong>.
  </p>
  <p class="tabs">
    <a class="<?= $tab === 'terms' ? 'on' : '' ?>" href="<?= charlie_q('terms', $sort) ?>">Terms</a>
    ·
    <a class="<?= $tab === 'edges' ? 'on' : '' ?>" href="<?= charlie_q('edges', $sort) ?>">Edges</a>
  </p>
<?php if ($err): ?>
  <p class="err"><?= htmlspecialchars($err) ?></p>
<?php elseif ($tab === 'edges'): ?>
  <h3>Edges (simple list)</h3>
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
          <td><code><?= htmlspecialchars($e['from_term']) ?></code></td>
          <td><em><?= htmlspecialchars($e['rel']) ?></em></td>
          <td><code><?= htmlspecialchars($e['to_term']) ?></code></td>
          <td><code><?= htmlspecialchars($e['c_uid']) ?></code></td>
          <td><?= (int) $e['ingest_unix'] ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
<?php else: ?>
  <p class="sort">
    Sort terms:
    <a class="<?= $sort === 'gravity' ? 'on' : '' ?>" href="<?= charlie_q('terms', 'gravity') ?>">gravity</a>
    ·
    <a class="<?= $sort === 'term' ? 'on' : '' ?>" href="<?= charlie_q('terms', 'term') ?>">A–Z</a>
    ·
    <a class="<?= $sort === 'recent' ? 'on' : '' ?>" href="<?= charlie_q('terms', 'recent') ?>">recent</a>
  </p>
  <h3>Single terms</h3>
  <?php if (!$gravity): ?>
    <p>No single terms yet.</p>
  <?php else: ?>
    <table class="ledger-table">
      <thead>
        <tr><th>term</th><th>gravity</th><th>updated</th></tr>
      </thead>
      <tbody>
      <?php foreach ($gravity as $g): ?>
        <tr>
          <td><strong><?= htmlspecialchars($g['term']) ?></strong></td>
          <td><?= (int) $g['gravity'] ?></td>
          <td><?= (int) $g['updated_at'] ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
<?php endif; ?>
</section>
<!-- styles: t/tools/ledgerREPORT/ledgerREPORT.css via getTool loadTool_Style -->

