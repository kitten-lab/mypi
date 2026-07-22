<?php
/**
 * authGATE · Login — reusable form. Install: getTool('authGATE', 'Login');
 * Actor handles POST. Surface supplies narrative around the tool.
 */
require_once (defined('echoSONAR') ? echoSONAR : '') . 'k/puppies/authSession.puppy.php';
if (is_file((defined('echoSONAR') ? echoSONAR : '') . 'a/_/href_local.php')) {
    require_once echoSONAR . 'a/_/href_local.php';
}

mypi_auth_boot();

// already in?
if (mypi_auth_check()) {
    $home = mypi_auth_home_path();
    echo '<p class="authgate-ok">Already logged in as <strong>'
        . htmlspecialchars(mypi_auth_agent()['display'], ENT_QUOTES, 'UTF-8')
        . '</strong>.</p>';
    echo '<p><a href="' . htmlspecialchars($home, ENT_QUOTES, 'UTF-8') . '">Enter station →</a></p>';
    $logout = htmlspecialchars($_SERVER['REQUEST_URI'] ?? '', ENT_QUOTES, 'UTF-8');
    echo '<form method="post" class="authgate-box" style="margin-top:1rem">';
    echo '<input type="hidden" name="authgate_action" value="logout">';
    echo '<button type="submit">Log out</button>';
    echo '</form>';
    return;
}

$err = isset($_GET['auth_err']) ? (string) $_GET['auth_err'] : '';
$next = isset($_GET['next']) ? (string) $_GET['next'] : '';
?>
<form method="post" class="authgate-box" autocomplete="on">
  <p class="authgate-title">Authenticate</p>
  <input type="hidden" name="authgate_action" value="login">
  <?php if ($next !== ''): ?>
    <input type="hidden" name="auth_next" value="<?= htmlspecialchars($next, ENT_QUOTES, 'UTF-8') ?>">
  <?php endif; ?>

  <label for="auth_user">Username</label>
  <input id="auth_user" name="auth_user" type="text" required
         placeholder="sdk808" autocomplete="username"
         value="<?= htmlspecialchars($_POST['auth_user'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

  <label for="auth_pass">Keyphrase</label>
  <input id="auth_pass" name="auth_pass" type="password" required
         placeholder="••••••••" autocomplete="current-password">

  <?php if ($err !== ''): ?>
    <p class="authgate-err"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></p>
  <?php endif; ?>

  <div class="authgate-actions">
    <button type="submit">Log in</button>
  </div>
  <p class="authgate-hint">Station is assigned by account. One house · many faces.</p>
</form>
