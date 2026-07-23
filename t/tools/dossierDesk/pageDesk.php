<?php
/**
 * dossierDesk · person-first factions (AB)
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

$equip = ROUTE_TO_SYSTEMS . 'Borrows/parsedown/equip.parsedown.php';
if (is_file($equip)) {
    require_once $equip;
}

$place = mypi_ledger_place_from_sky();
$sys = $place['sys'] !== '' ? $place['sys'] : 'terminal';
$dom = $place['dom'] !== '' ? $place['dom'] : 'ab';
$room = 'dossier';
$basePlace = [
    'sys' => $sys,
    'dom' => $dom,
    'room' => $room,
];

$tab = isset($_GET['tab']) ? (string) $_GET['tab'] : 'people';
if (!in_array($tab, ['people', 'factions', 'person', 'faction', 'new_person', 'new_faction'], true)) {
    $tab = 'people';
}
$id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$err = $GLOBALS['DOSSIER_ERROR'] ?? null;
$ok = isset($_GET['ok']) ? (string) $_GET['ok'] : '';
$leaderWarn = isset($_GET['leader_warn']) ? (int) $_GET['leader_warn'] : 0;

$persons = mypi_dossier_list('dossier_person', array_merge($basePlace, ['q' => $q, 'limit' => 150]));
$factions = mypi_dossier_list('dossier_faction', array_merge($basePlace, ['q' => $q, 'limit' => 150]));

$focus = null;
$focusMeta = [];
if ($id !== '' && in_array($tab, ['person', 'faction'], true)) {
    $focus = mypi_ledger_get($id);
    if ($focus) {
        $focusMeta = json_decode((string) ($focus['meta_json'] ?? '{}'), true) ?: [];
        if (($focus['kind'] ?? '') === 'dossier_person') {
            $tab = 'person';
        } elseif (($focus['kind'] ?? '') === 'dossier_faction') {
            $tab = 'faction';
        }
    }
}

$self = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', ENT_QUOTES, 'UTF-8');

$statusOpts = ['unsure' => 'unsure', 'active' => 'active', 'inactive' => 'inactive', 'dissolved' => 'dissolved'];

$badge = static function (string $st): string {
    $st = mypi_dossier_norm_status($st);
    return '<span class="dd-badge st-' . htmlspecialchars($st, ENT_QUOTES, 'UTF-8') . '">'
        . htmlspecialchars($st, ENT_QUOTES, 'UTF-8') . '</span>';
};

$imgSrc = static function (string $assetId): string {
    if ($assetId === '' || !function_exists('mypi_media_img_src')) {
        return '';
    }
    return mypi_media_img_src($assetId);
};
?>
<div class="ddesk" id="dossier-desk">
  <div class="ddesk-layout">
    <aside class="ddesk-side">
      <div class="ddesk-tabs">
        <a class="<?= in_array($tab, ['people', 'person', 'new_person'], true) ? 'is-on' : '' ?>"
           href="<?= $self ?>?tab=people">People</a>
        <a class="<?= in_array($tab, ['factions', 'faction', 'new_faction'], true) ? 'is-on' : '' ?>"
           href="<?= $self ?>?tab=factions">Factions</a>
      </div>
      <form method="get" action="" class="dd-form" style="padding:0.35rem;margin:0">
        <input type="hidden" name="tab" value="<?= htmlspecialchars(
            in_array($tab, ['factions', 'faction', 'new_faction'], true) ? 'factions' : 'people',
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
        <input type="text" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="search…" style="font-size:0.85rem">
      </form>
      <p class="dd-hint">
        <a class="dd-btn" href="<?= $self ?>?tab=new_person">+ person</a>
        <a class="dd-btn" href="<?= $self ?>?tab=new_faction">+ faction</a>
      </p>
      <ul class="ddesk-list">
        <?php if (in_array($tab, ['factions', 'faction', 'new_faction'], true)): ?>
          <?php if (!$factions): ?>
            <li class="dd-empty">no factions · map blank on purpose</li>
          <?php endif; ?>
          <?php foreach ($factions as $f):
              $fm = json_decode((string) ($f['meta_json'] ?? '{}'), true) ?: [];
              $on = ($id === $f['c_uid']);
              $leaders = mypi_dossier_faction_leaders($f['c_uid']);
              ?>
            <li>
              <a class="<?= $on ? 'is-on' : '' ?>"
                 href="<?= $self ?>?tab=faction&amp;id=<?= rawurlencode($f['c_uid']) ?>">
                <?= htmlspecialchars((string) $f['topic'], ENT_QUOTES, 'UTF-8') ?>
                <?= $badge((string) ($fm['status'] ?? 'unsure')) ?>
                <span class="dd-meta">
                  <?= count(mypi_dossier_memberships_for_faction($f['c_uid'])) ?> members
                  <?php if (count($leaders) >= 2): ?> · multi-leader<?php endif; ?>
                </span>
              </a>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <?php if (!$persons): ?>
            <li class="dd-empty">no persons logged</li>
          <?php endif; ?>
          <?php foreach ($persons as $p):
              $pm = json_decode((string) ($p['meta_json'] ?? '{}'), true) ?: [];
              $on = ($id === $p['c_uid']);
              $akas = $pm['akas'] ?? [];
              ?>
            <li>
              <a class="<?= $on ? 'is-on' : '' ?>"
                 href="<?= $self ?>?tab=person&amp;id=<?= rawurlencode($p['c_uid']) ?>">
                <?= htmlspecialchars((string) $p['topic'], ENT_QUOTES, 'UTF-8') ?>
                <?= $badge((string) ($pm['status'] ?? 'unsure')) ?>
                <?php if (is_array($akas) && $akas): ?>
                  <span class="dd-meta">aka <?= htmlspecialchars(implode(', ', array_slice($akas, 0, 3)), ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
              </a>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </aside>

    <section class="ddesk-panel">
      <?php if ($err): ?>
        <p class="dd-status dd-err"><strong>error:</strong> <?= htmlspecialchars((string) $err, ENT_QUOTES, 'UTF-8') ?></p>
      <?php elseif ($ok !== ''): ?>
        <p class="dd-status"><strong>saved</strong> · <?= htmlspecialchars($ok, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>
      <?php if ($leaderWarn >= 2): ?>
        <div class="dd-warn">Faction believes it has <strong><?= (int) $leaderWarn ?></strong> leaders.</div>
      <?php endif; ?>

      <?php if ($tab === 'new_person' || ($tab === 'person' && !$focus)): ?>
        <h2 class="ddesk-title">New person</h2>
        <form method="post" class="dd-form">
          <input type="hidden" name="dd_action" value="save_person">
          <input type="hidden" name="dd_tz" class="dd-tz" value="">
          <label for="dd_name">Name / face</label>
          <input id="dd_name" name="dd_name" type="text" required placeholder="primary name">
          <label for="dd_akas">AKAs <span class="muted">(comma-separated)</span></label>
          <input id="dd_akas" name="dd_akas" type="text" placeholder="handle, code name, …">
          <label for="dd_status">Status</label>
          <select id="dd_status" name="dd_status">
            <?php foreach ($statusOpts as $k => $lab): if ($k === 'dissolved') {
                continue;
            } ?>
              <option value="<?= $k ?>"<?= $k === 'unsure' ? ' selected' : '' ?>><?= $lab ?></option>
            <?php endforeach; ?>
          </select>
          <label for="dd_body">Blurb</label>
          <textarea id="dd_body" name="dd_body" placeholder="who they are in one breath"></textarea>
          <label for="dd_tags">Tags</label>
          <input id="dd_tags" name="dd_tags" type="text">
          <div class="dd-row">
            <button type="submit" class="dd-btn dd-btn-primary">Save person</button>
          </div>
        </form>

      <?php elseif ($tab === 'new_faction' || ($tab === 'faction' && !$focus)): ?>
        <h2 class="ddesk-title">New faction</h2>
        <form method="post" class="dd-form">
          <input type="hidden" name="dd_action" value="save_faction">
          <input type="hidden" name="dd_tz" class="dd-tz" value="">
          <label for="dd_fname">Name</label>
          <input id="dd_fname" name="dd_name" type="text" required>
          <label for="dd_fstatus">Status</label>
          <select id="dd_fstatus" name="dd_status">
            <?php foreach ($statusOpts as $k => $lab): ?>
              <option value="<?= $k ?>"<?= $k === 'unsure' ? ' selected' : '' ?>><?= $lab ?></option>
            <?php endforeach; ?>
          </select>
          <label for="dd_fbody">Summary</label>
          <textarea id="dd_fbody" name="dd_body"></textarea>
          <div class="dd-row">
            <button type="submit" class="dd-btn dd-btn-primary">Save faction</button>
          </div>
        </form>

      <?php elseif ($tab === 'person' && $focus): ?>
        <?php
        $pm = $focusMeta;
        $akas = is_array($pm['akas'] ?? null) ? $pm['akas'] : [];
        $mems = mypi_dossier_memberships_for_person($focus['c_uid']);
        $notes = mypi_dossier_list_notes(array_merge($basePlace, ['person_c_uid' => $focus['c_uid']]));
        $portrait = (string) ($pm['portrait_asset'] ?? '');
        $pSrc = $portrait !== '' ? $imgSrc($portrait) : '';
        if ($pSrc === '' && !empty($pm['attachments'][0]['asset_id'])) {
            $pSrc = $imgSrc((string) $pm['attachments'][0]['asset_id']);
        }
        ?>
        <header class="ddesk-head">
          <h2 class="ddesk-title">
            <?= htmlspecialchars((string) $focus['topic'], ENT_QUOTES, 'UTF-8') ?>
            <?= $badge((string) ($pm['status'] ?? 'unsure')) ?>
          </h2>
        </header>
        <?php if ($pSrc !== ''): ?>
          <img class="dd-portrait" src="<?= htmlspecialchars($pSrc, ENT_QUOTES, 'UTF-8') ?>" alt="portrait">
        <?php endif; ?>
        <?php if ($akas): ?>
          <p class="dd-hint">aka <?= htmlspecialchars(implode(' · ', $akas), ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <?php if (trim((string) $focus['body']) !== ''): ?>
          <div class="dd-note-body">
            <?php if (function_exists('render_md')) {
                echo render_md((string) $focus['body']);
            } else {
                echo nl2br(htmlspecialchars((string) $focus['body'], ENT_QUOTES, 'UTF-8'));
            } ?>
          </div>
        <?php endif; ?>

        <h3 class="dd-section">Edit person</h3>
        <form method="post" class="dd-form">
          <input type="hidden" name="dd_action" value="save_person">
          <input type="hidden" name="dd_c_uid" value="<?= htmlspecialchars($focus['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="dd_tz" class="dd-tz" value="">
          <label>Name</label>
          <input name="dd_name" type="text" required value="<?= htmlspecialchars((string) $focus['topic'], ENT_QUOTES, 'UTF-8') ?>">
          <label>AKAs</label>
          <input name="dd_akas" type="text" value="<?= htmlspecialchars(implode(', ', $akas), ENT_QUOTES, 'UTF-8') ?>">
          <label>Status</label>
          <select name="dd_status">
            <?php foreach (['unsure', 'active', 'inactive'] as $k): ?>
              <option value="<?= $k ?>"<?= ($pm['status'] ?? 'unsure') === $k ? ' selected' : '' ?>><?= $k ?></option>
            <?php endforeach; ?>
          </select>
          <label>Blurb</label>
          <textarea name="dd_body"><?= htmlspecialchars((string) $focus['body'], ENT_QUOTES, 'UTF-8') ?></textarea>
          <div class="dd-row"><button type="submit" class="dd-btn dd-btn-primary">Update</button></div>
        </form>

        <form method="post" enctype="multipart/form-data" class="dd-form">
          <input type="hidden" name="dd_action" value="attach">
          <input type="hidden" name="dd_c_uid" value="<?= htmlspecialchars($focus['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="dd_media_role" value="portrait">
          <input type="hidden" name="dd_tz" class="dd-tz" value="">
          <label>Portrait</label>
          <input type="file" name="dd_image" accept="image/*" required>
          <div class="dd-row"><button type="submit" class="dd-btn">Install portrait</button></div>
        </form>

        <h3 class="dd-section">Memberships</h3>
        <?php if (!$mems): ?>
          <p class="dd-empty">no factions yet</p>
        <?php else: ?>
          <table class="dd-table">
            <thead><tr><th>Faction</th><th>Status</th><th>Role</th><th>Leader</th></tr></thead>
            <tbody>
            <?php foreach ($mems as $m):
                $mm = json_decode((string) ($m['meta_json'] ?? '{}'), true) ?: [];
                $fid = (string) ($mm['faction_c_uid'] ?? '');
                $frow = $fid !== '' ? mypi_ledger_get($fid) : null;
                ?>
              <tr>
                <td>
                  <?php if ($frow): ?>
                    <a href="<?= $self ?>?tab=faction&amp;id=<?= rawurlencode($fid) ?>">
                      <?= htmlspecialchars((string) $frow['topic'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                  <?php else: ?>?
                  <?php endif; ?>
                </td>
                <td><?= $badge((string) ($mm['status'] ?? 'unsure')) ?></td>
                <td><?= htmlspecialchars((string) ($mm['role'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= !empty($mm['is_leader']) ? '<span class="dd-leader">★</span>' : '—' ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>

        <form method="post" class="dd-form">
          <input type="hidden" name="dd_action" value="save_membership">
          <input type="hidden" name="dd_person" value="<?= htmlspecialchars($focus['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="dd_return_tab" value="person">
          <input type="hidden" name="dd_return_id" value="<?= htmlspecialchars($focus['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="dd_tz" class="dd-tz" value="">
          <label>Add / update membership</label>
          <select name="dd_faction" required>
            <option value="">— faction —</option>
            <?php foreach ($factions as $f): ?>
              <option value="<?= htmlspecialchars($f['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars((string) $f['topic'], ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
          <label>Status</label>
          <select name="dd_status">
            <?php foreach (['unsure', 'active', 'inactive'] as $k): ?>
              <option value="<?= $k ?>"<?= $k === 'unsure' ? ' selected' : '' ?>><?= $k ?></option>
            <?php endforeach; ?>
          </select>
          <label>Role</label>
          <input name="dd_role" type="text" placeholder="handler · cutout · …">
          <div class="dd-row">
            <label><input type="checkbox" name="dd_is_leader" value="1"> mark as leader claim</label>
            <button type="submit" class="dd-btn dd-btn-primary">Save membership</button>
          </div>
        </form>

        <h3 class="dd-section">Field notes</h3>
        <?php foreach ($notes as $n):
            $nm = json_decode((string) ($n['meta_json'] ?? '{}'), true) ?: [];
            $when = (int) ($n['event_unix'] ?? 0);
            ?>
          <article class="dd-note">
            <h4><?= htmlspecialchars((string) $n['topic'], ENT_QUOTES, 'UTF-8') ?></h4>
            <div class="dd-when">
              <?= $when ? htmlspecialchars(date('Y-m-d H:i', $when), ENT_QUOTES, 'UTF-8') : '' ?>
              · <?= htmlspecialchars((string) ($nm['confidence'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div class="dd-note-body">
              <?php if (function_exists('render_md')) {
                  echo render_md((string) $n['body']);
              } else {
                  echo nl2br(htmlspecialchars((string) $n['body'], ENT_QUOTES, 'UTF-8'));
              } ?>
            </div>
            <?php if (!empty($nm['context'])): ?>
              <p class="dd-hint">context: <?= htmlspecialchars((string) $nm['context'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
        <?php if (!$notes): ?><p class="dd-empty">no notes yet</p><?php endif; ?>

        <form method="post" class="dd-form">
          <input type="hidden" name="dd_action" value="add_note">
          <input type="hidden" name="dd_person" value="<?= htmlspecialchars($focus['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="dd_return_tab" value="person">
          <input type="hidden" name="dd_return_id" value="<?= htmlspecialchars($focus['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="dd_tz" class="dd-tz" value="">
          <label>New field note</label>
          <input name="dd_title" type="text" placeholder="title">
          <textarea name="dd_body" required placeholder="what you saw · involvement · behavior"></textarea>
          <label>Context</label>
          <input name="dd_context" type="text" placeholder="source · rumor · call">
          <label>Confidence</label>
          <select name="dd_confidence">
            <option value="rumor">rumor</option>
            <option value="confirmed">confirmed</option>
            <option value="contested">contested</option>
          </select>
          <label>When <span class="muted">(optional backdate)</span></label>
          <input name="dd_event" type="text" placeholder="now · 2025-09-16 14:00">
          <div class="dd-row"><button type="submit" class="dd-btn dd-btn-primary">Add note</button></div>
        </form>

      <?php elseif ($tab === 'faction' && $focus): ?>
        <?php
        $fm = $focusMeta;
        $mems = mypi_dossier_memberships_for_faction($focus['c_uid']);
        $leaders = mypi_dossier_faction_leaders($focus['c_uid']);
        $notes = mypi_dossier_list_notes(array_merge($basePlace, ['faction_c_uid' => $focus['c_uid']]));
        $sigil = (string) ($fm['sigil_asset'] ?? '');
        $sSrc = $sigil !== '' ? $imgSrc($sigil) : '';
        ?>
        <header class="ddesk-head">
          <h2 class="ddesk-title">
            <?= htmlspecialchars((string) $focus['topic'], ENT_QUOTES, 'UTF-8') ?>
            <?= $badge((string) ($fm['status'] ?? 'unsure')) ?>
          </h2>
        </header>
        <?php if (count($leaders) >= 2): ?>
          <div class="dd-warn">
            Faction believes it has <strong><?= count($leaders) ?></strong> leaders:
            <?= htmlspecialchars(implode(', ', array_map(static function ($L) {
                return $L['name'];
            }, $leaders)), ENT_QUOTES, 'UTF-8') ?>
          </div>
        <?php elseif (count($leaders) === 1): ?>
          <p class="dd-hint">Leader claim: <strong><?= htmlspecialchars($leaders[0]['name'], ENT_QUOTES, 'UTF-8') ?></strong></p>
        <?php endif; ?>
        <?php if ($sSrc !== ''): ?>
          <img class="dd-portrait" src="<?= htmlspecialchars($sSrc, ENT_QUOTES, 'UTF-8') ?>" alt="sigil">
        <?php endif; ?>
        <?php if (trim((string) $focus['body']) !== ''): ?>
          <div class="dd-note-body">
            <?php if (function_exists('render_md')) {
                echo render_md((string) $focus['body']);
            } else {
                echo nl2br(htmlspecialchars((string) $focus['body'], ENT_QUOTES, 'UTF-8'));
            } ?>
          </div>
        <?php endif; ?>

        <h3 class="dd-section">Edit faction</h3>
        <form method="post" class="dd-form">
          <input type="hidden" name="dd_action" value="save_faction">
          <input type="hidden" name="dd_c_uid" value="<?= htmlspecialchars($focus['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="dd_tz" class="dd-tz" value="">
          <label>Name</label>
          <input name="dd_name" type="text" required value="<?= htmlspecialchars((string) $focus['topic'], ENT_QUOTES, 'UTF-8') ?>">
          <label>Status</label>
          <select name="dd_status">
            <?php foreach ($statusOpts as $k => $lab): ?>
              <option value="<?= $k ?>"<?= ($fm['status'] ?? 'unsure') === $k ? ' selected' : '' ?>><?= $lab ?></option>
            <?php endforeach; ?>
          </select>
          <label>Summary</label>
          <textarea name="dd_body"><?= htmlspecialchars((string) $focus['body'], ENT_QUOTES, 'UTF-8') ?></textarea>
          <div class="dd-row"><button type="submit" class="dd-btn dd-btn-primary">Update</button></div>
        </form>

        <form method="post" enctype="multipart/form-data" class="dd-form">
          <input type="hidden" name="dd_action" value="attach">
          <input type="hidden" name="dd_c_uid" value="<?= htmlspecialchars($focus['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="dd_media_role" value="sigil">
          <input type="hidden" name="dd_tz" class="dd-tz" value="">
          <label>Sigil / cover</label>
          <input type="file" name="dd_image" accept="image/*" required>
          <div class="dd-row"><button type="submit" class="dd-btn">Install image</button></div>
        </form>

        <h3 class="dd-section">Members</h3>
        <?php if (!$mems): ?>
          <p class="dd-empty">no members logged</p>
        <?php else: ?>
          <table class="dd-table">
            <thead><tr><th>Person</th><th>Status</th><th>Role</th><th>Leader</th></tr></thead>
            <tbody>
            <?php foreach ($mems as $m):
                $mm = json_decode((string) ($m['meta_json'] ?? '{}'), true) ?: [];
                $pid = (string) ($mm['person_c_uid'] ?? '');
                $prow = $pid !== '' ? mypi_ledger_get($pid) : null;
                ?>
              <tr>
                <td>
                  <?php if ($prow): ?>
                    <a href="<?= $self ?>?tab=person&amp;id=<?= rawurlencode($pid) ?>">
                      <?= htmlspecialchars((string) $prow['topic'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                  <?php else: ?>?
                  <?php endif; ?>
                </td>
                <td><?= $badge((string) ($mm['status'] ?? 'unsure')) ?></td>
                <td><?= htmlspecialchars((string) ($mm['role'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= !empty($mm['is_leader']) ? '<span class="dd-leader">★</span>' : '—' ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>

        <form method="post" class="dd-form">
          <input type="hidden" name="dd_action" value="save_membership">
          <input type="hidden" name="dd_faction" value="<?= htmlspecialchars($focus['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="dd_return_tab" value="faction">
          <input type="hidden" name="dd_return_id" value="<?= htmlspecialchars($focus['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="dd_tz" class="dd-tz" value="">
          <label>Add / update member</label>
          <select name="dd_person" required>
            <option value="">— person —</option>
            <?php foreach ($persons as $p): ?>
              <option value="<?= htmlspecialchars($p['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars((string) $p['topic'], ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
          <label>Status</label>
          <select name="dd_status">
            <?php foreach (['unsure', 'active', 'inactive'] as $k): ?>
              <option value="<?= $k ?>"<?= $k === 'unsure' ? ' selected' : '' ?>><?= $k ?></option>
            <?php endforeach; ?>
          </select>
          <label>Role</label>
          <input name="dd_role" type="text">
          <div class="dd-row">
            <label><input type="checkbox" name="dd_is_leader" value="1"> leader claim</label>
            <button type="submit" class="dd-btn dd-btn-primary">Save membership</button>
          </div>
        </form>

        <h3 class="dd-section">Field notes</h3>
        <?php foreach ($notes as $n):
            $nm = json_decode((string) ($n['meta_json'] ?? '{}'), true) ?: [];
            $when = (int) ($n['event_unix'] ?? 0);
            ?>
          <article class="dd-note">
            <h4><?= htmlspecialchars((string) $n['topic'], ENT_QUOTES, 'UTF-8') ?></h4>
            <div class="dd-when"><?= $when ? date('Y-m-d H:i', $when) : '' ?></div>
            <div class="dd-note-body">
              <?php if (function_exists('render_md')) {
                  echo render_md((string) $n['body']);
              } else {
                  echo nl2br(htmlspecialchars((string) $n['body'], ENT_QUOTES, 'UTF-8'));
              } ?>
            </div>
          </article>
        <?php endforeach; ?>
        <?php if (!$notes): ?><p class="dd-empty">no notes yet</p><?php endif; ?>

        <form method="post" class="dd-form">
          <input type="hidden" name="dd_action" value="add_note">
          <input type="hidden" name="dd_faction" value="<?= htmlspecialchars($focus['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="dd_return_tab" value="faction">
          <input type="hidden" name="dd_return_id" value="<?= htmlspecialchars($focus['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="dd_tz" class="dd-tz" value="">
          <label>New field note (faction)</label>
          <input name="dd_title" type="text">
          <textarea name="dd_body" required></textarea>
          <label>Confidence</label>
          <select name="dd_confidence">
            <option value="rumor">rumor</option>
            <option value="confirmed">confirmed</option>
            <option value="contested">contested</option>
          </select>
          <div class="dd-row"><button type="submit" class="dd-btn dd-btn-primary">Add note</button></div>
        </form>

      <?php else: ?>
        <h2 class="ddesk-title">Dossier desk</h2>
        <p class="dd-hint">
          Person-first map for Agent K. Log people, put them in factions, mark status and leader claims,
          field notes with doubt attached. Soft-warn if a faction believes it has two leaders.
        </p>
        <p class="dd-empty">Pick a person or faction — or create one.</p>
      <?php endif; ?>
    </section>
  </div>
</div>
<script>
(function () {
  var tz = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
  document.querySelectorAll('.dd-tz').forEach(function (el) { el.value = tz; });
})();
</script>
