<?php
// wire world DSL:

/**
 * Placeholder rules:
 *  - $ph === true  → use $mySIGFIG["{$id}_pl"] (or *Hint keys)
 *  - $ph is string → use that string as placeholder
 *  - $ph null/false → no placeholder
 */
function wire_placeholder_attr(string $uniqueID, $ph): string {
    global $mySIGFIG;
    $fig = is_array($mySIGFIG ?? null) ? $mySIGFIG : [];

    if ($ph === true || $ph === 1 || $ph === '1') {
        $placement = $uniqueID . '_pl';
        $hintKeys = [
            $placement,
            $uniqueID . 'Hint',
            $uniqueID . '_hint',
            // common cuBOOK / form aliases
            ($uniqueID === 'USER' ? 'UserHint' : null),
            ($uniqueID === 'MESSAGE' ? 'MsgHint' : null),
            ($uniqueID === 'username' ? 'UserHint' : null),
            ($uniqueID === 'message' ? 'MsgHint' : null),
        ];
        $text = '';
        foreach ($hintKeys as $k) {
            if ($k && isset($fig[$k]) && $fig[$k] !== '') {
                $text = (string) $fig[$k];
                break;
            }
        }
        if ($text === '') {
            return '';
        }
        return "placeholder='" . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . "'";
    }

    if (is_string($ph) && $ph !== '') {
        return "placeholder='" . htmlspecialchars($ph, ENT_QUOTES, 'UTF-8') . "'";
    }

    return '';
}

function wire_label_text(string $uniqueID): string {
    global $mySIGFIG;
    $fig = is_array($mySIGFIG ?? null) ? $mySIGFIG : [];
    $label = $fig[$uniqueID] ?? $uniqueID;
    return htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8');
}

function wireINPUT(
    string $uniqueID,
    $ph = null,
    $required = null,
) {
    $hasPH = wire_placeholder_attr($uniqueID, $ph);
    $isRequired = ($required === true || $required === 1) ? "required='true'" : '';

    echo "<span><label for='" . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "' id='"
        . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "-label'>"
        . wire_label_text($uniqueID) . '
            </label><br>
            <input 
            name="' . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . '" 
            id="' . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . '"
            class="' . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . '"
            ' . $hasPH . '  
            ' . $isRequired . '></span>';
}

function wireINPUT_TAG(
    string $uniqueID,
    $ph = null,
    $required = null,
) {
    $hasPH = wire_placeholder_attr($uniqueID, $ph);
    $isRequired = ($required === true || $required === 1) ? "required='true'" : '';

    echo "<span><label for='" . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "' id='"
        . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "-label'>"
        . wire_label_text($uniqueID) . "
            </label><br><textarea 
            cols='60' rows='5'
            id='tag-input' 
            name='" . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "' 
            class='" . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "'
            $hasPH  
            $isRequired></textarea></span>";
}

function wireFILEinput(
    string $uniqueID,
    $ph = null,
    $required = null,
) {
    $hasPH = '';
    if (is_string($ph) && $ph !== '') {
        $hasPH = " placeholder='" . htmlspecialchars($ph, ENT_QUOTES, 'UTF-8') . "'";
    }
    $isRequired = ($required === true || $required === 1) ? "required='true'" : '';

    echo "<span><label for='" . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "' id='"
        . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "-label'>"
        . wire_label_text($uniqueID) . "</label><br>
            <input 
            name='" . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "' 
            class='" . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "'
            type='file' 
            $hasPH  
            $isRequired></span>";
}

function wireTEXTAREA(
    string $uniqueID,
    $ph = null,
    $required = null,
) {
    $hasPH = wire_placeholder_attr($uniqueID, $ph);
    $isRequired = ($required === true || $required === 1) ? "required='true'" : '';

    echo "<span><label for='" . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "' id='"
        . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "-label'>"
        . wire_label_text($uniqueID) . "</label><br>
        <textarea 
        cols='60' rows='5'
        name='" . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "'
        id='" . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "'
        class='" . htmlspecialchars($uniqueID, ENT_QUOTES, 'UTF-8') . "'  
            $hasPH  
            $isRequired></textarea>
            </span>";
}

function wireAVENACTIVE($user, $assistant) {
    $user = htmlspecialchars((string) $user, ENT_QUOTES, 'UTF-8');
    $assistant = htmlspecialchars((string) $assistant, ENT_QUOTES, 'UTF-8');
    echo '<span style="display: grid; grid-template-columns: auto; gap: 2px; text-align:left;padding: 4px;">';
    echo '<div>ACTING AGENT:</div>';
    echo '<div>';
    echo '<input type="radio" id="MRA" name="agent" value="' . $user . '" style="width:25px;">';
    echo '<label for="MRA">' . $user . '</label>';
    echo '</div>';
    echo '<div>';
    echo '<input type="radio" id="ADM" name="agent" value="' . $assistant . '" style="width:25px;">';
    echo '<label for="ADM">' . $assistant . '</label>';
    echo '</div>';
    echo '</span>';
}
