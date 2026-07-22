<?php
/**
 * Compact logout control for sidebars / tool shelves.
 */
require_once (defined('echoSONAR') ? echoSONAR : '') . 'k/puppies/authSession.puppy.php';
mypi_auth_boot();
if (!mypi_auth_check()) {
    return;
}
$a = mypi_auth_agent();
?>
<form method="post" class="authgate-logout" style="margin:0.75rem 0">
  <input type="hidden" name="authgate_action" value="logout">
  <button type="submit" title="Log out <?= htmlspecialchars($a['display'], ENT_QUOTES, 'UTF-8') ?>">
    Log out (<?= htmlspecialchars($a['display'], ENT_QUOTES, 'UTF-8') ?>)
  </button>
</form>
