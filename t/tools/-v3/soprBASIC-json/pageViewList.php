<?php 
require_once ROUTE_TO_SYSTEMS . 'Borrows/parsedown/Parsedown.php'; 
require_once ROUTE_TO_SYSTEMS . 'shadowENVO.php';

require_once __DIR__ . '/-SIG-soprBASIC.php'; // ASSISTANT SETTINGS
require_once __DIR__ . '/-CRATE-soprBASIC.php'; // CRATE FILLER SETTINGS


// SHADOW ENVIRONMENT SETTINGS AND OVERLAY
$IS_IT = SHADOW_TOGGLE;

if ($IS_IT == true) {
  echo "<div class='sha_env'>shadow mode on</div>";
}

$CHEST = ROUTE_TO_LOCALSTORE . DOM_SLUG . '-' . ROOM_SLUG . '.sopr.frags.json';    
  


if (file_exists($CHEST)) {
    $CHEST_THINGS = json_decode((string) file_get_contents($CHEST), true) ?: [];
    $Parsedown = new Parsedown();

    // Support both { SECTION: { id: { LABEL, SOPERS } } } and flat nests
    $sections = $CHEST_THINGS['SECTION'] ?? $CHEST_THINGS;

    if (!is_array($sections) || $sections === []) {
        echo 'No fragments found.';
    } else {
        foreach ($sections as $TIMBER) {
            if (!is_array($TIMBER)) {
                continue;
            }
            echo '<h3>' . htmlspecialchars($TIMBER['LABEL'] ?? 'section', ENT_QUOTES, 'UTF-8') . '</h3>';
            foreach ($TIMBER['SOPERS'] ?? [] as $SOPR) {
                if (!is_array($SOPR)) {
                    continue;
                }
                echo "<div class='soper_frag'>";
                echo "<div class='slug'>" . htmlspecialchars($SOPR['ID'] ?? '', ENT_QUOTES, 'UTF-8') . '<br>'
                    . htmlspecialchars($SOPR['METADATA']['ADDED'] ?? '', ENT_QUOTES, 'UTF-8');
                echo '</div>';
                echo "<div class='content'>" . $Parsedown->text($SOPR['FRAG'] ?? '');

                $tags = $SOPR['METADATA']['TAGS'] ?? [];
                if (is_array($tags)) {
                    foreach ($tags as $TAG => $SUBTAG) {
                        if (!is_array($SUBTAG)) {
                            continue;
                        }
                        foreach ($SUBTAG as $TAG2 => $TAG3) {
                            echo '<pre>' . htmlspecialchars((string) $TAG, ENT_QUOTES, 'UTF-8')
                                . ' > ' . htmlspecialchars((string) $TAG2, ENT_QUOTES, 'UTF-8') . ' > ';
                            if (is_array($TAG3)) {
                                foreach ($TAG3 as $T3) {
                                    echo htmlspecialchars((string) $T3, ENT_QUOTES, 'UTF-8') . '; ';
                                }
                            } else {
                                echo htmlspecialchars((string) $TAG3, ENT_QUOTES, 'UTF-8');
                            }
                            echo '</pre>';
                        }
                    }
                }

                echo '</div>';
                echo '</div>';
            }
        }
    }
} else {
    echo 'No fragments found.';
}
