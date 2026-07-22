<?php
/**
 * Starline · temporary auth roster desk (manage users later).
 * Source: k/configs/auth_users.php
 */

SKY__AUTH(
/* mod */  'sysop', 'Sysop',
/* dom */  'offices', 'Offices',
/* room */ 'authdesk', 'Auth desk',
/* texture */ 'classic'
);

require_once echoSONAR . 'k/puppies/authSession.puppy.php';

openSky(ROOM_DISPLAY);
h1('Auth desk');

leaf('Temporary home for terminal (and future surface) logins. Edit k/configs/auth_users.php — real UI later.');

$users = mypi_auth_users();
skylite('<table class="auth-roster" style="width:100%;border-collapse:collapse;font-size:0.9rem">');
skylite('<tr style="text-align:left;border-bottom:1px solid rgba(255,255,255,0.2)"><th>user</th><th>display</th><th>sys</th><th>dom</th><th>roles</th></tr>');
foreach ($users as $u) {
    $roles = isset($u['roles']) && is_array($u['roles']) ? implode(', ', $u['roles']) : '';
    skylite(
        '<tr style="border-bottom:1px solid rgba(255,255,255,0.08)">'
        . '<td><code>' . htmlspecialchars((string) ($u['username'] ?? ''), ENT_QUOTES, 'UTF-8') . '</code></td>'
        . '<td>' . htmlspecialchars((string) ($u['display'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>'
        . '<td>' . htmlspecialchars((string) ($u['sys'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>'
        . '<td>' . htmlspecialchars((string) ($u['dom'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>'
        . '<td style="opacity:0.7">' . htmlspecialchars($roles, ENT_QUOTES, 'UTF-8') . '</td>'
        . '</tr>'
    );
}
skylite('</table>');
skylite('<p style="opacity:0.6;margin-top:1rem">Keyphrases not shown. Reusable strut: <code>k/puppies/authSession.puppy.php</code> + <code>getTool(\'authGATE\',\'Login\')</code>.</p>');

closeSky();
