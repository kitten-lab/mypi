<?php 
// wire world DSL:

function wireINPUT(
    string $uniqueID, 
    ?string $ph = null,
    ?string $required = null,
    ){
    global $mySIGFIG;
    $placement = $uniqueID . '_pl';
        
        if($ph == true)
        { $hasPH = "placeholder='$mySIGFIG[$placement]'"; } 
            else { $hasPH = ""; }

        if($required == true)
        { $isRequired = "required='true'"; } 
            else { $isRequired = ""; }

        echo "<span><label for='$uniqueID' id='$uniqueID'>" . $mySIGFIG[$uniqueID] . "
            </label><br>
            <input 
            name='$uniqueID' 
            class='$uniqueID'
            $hasPH  
            $isRequired></span>";
}

function wireINPUT_TAG(
    string $uniqueID, 
    ?string $ph = null,
    ?string $required = null,
    ){
    global $mySIGFIG;
    $placement = $uniqueID . '_pl';
        
        if($ph == true)
        { $hasPH = "placeholder='<?= $mySIGFIG[$placement]'"; } 
            else { $hasPH = ""; }
        
        if($required == true)
        { $isRequired = "required='true'"; } 
            else { $isRequired = ""; }

        echo "<span><label for='$uniqueID' id='$uniqueID'>" . $mySIGFIG[$uniqueID] . "
            </label><br><textarea 
            cols='60' rows='5'
            id='tag-input' 
            name='$uniqueID' 
            class='$uniqueID'
            $hasPH  
            $isRequired></textarea></span>";
}

function wireFILEinput (
    string $uniqueID, 
    ?string $ph = null,
    ?string $required = null,
    ){
        global $mySIGFIG;
        $hasPH = $ph ? " placeholder='$ph'" : "";
        
        if($required == true)
        { $isRequired = "required='true'"; } 
            else { $isRequired = ""; }

        echo "<span><label for='$uniqueID' id='$uniqueID'>" . $mySIGFIG[$uniqueID] . "</label><br>
            <input 
            name='$uniqueID' 
            class='$uniqueID'
            type='file' 
            $hasPH  
            $isRequired></span>";
}


function wireTEXTAREA($uniqueID, 
    ?string $ph = null,
    ?string $required = null,
    ){
        global $mySIGFIG;
    $placement = $uniqueID . '_pl';
        
        if($ph == true)
        { $hasPH = "placeholder='<?= $mySIGFIG[$placement]'"; } 
            else { $hasPH = ""; }
        
        if($required == true)
        { $isRequired = "required='true'"; } 
            else { $isRequired = ""; }

        echo "<span><label for='$uniqueID' id='$uniqueID'>" . $mySIGFIG[$uniqueID] . "</label><br>
        <textarea 
        cols='60' rows='5'
        name='$uniqueID'
        class='$uniqueID'  
            $hasPH  
            $isRequired></textarea>
            </span>";

}

function wireAVENACTIVE($user, $assistant){
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