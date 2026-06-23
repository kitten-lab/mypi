
<?php




function getA_Style($css, $folder, $function) {
    $path = "/" . $folder . "/" . $function . "/" . $css . ".css";
    $full = $GLOBALS['SONAR'] . "a" . $path;
    if (is_file($full)) {
         echo '<link rel="stylesheet"  type="text/css" href="' . a_root . $path . '">';
         } else {
            error_log("PATH NOT FOUND getAStyle " . $path);

         }
}


function invokeStyle($css, $function) {
    $path = "/" . $folder . "/" . $css . ".css";
    $full = $GLOBALS['SONAR'] . "a" . $path;
    if (is_file($full)) {
         echo '<link rel="stylesheet"  type="text/css" href="' . a_root . $path . '">';
         } else {
            error_log("PATH NOT FOUND invokeStyle" . $path);

         }
}



// depreciating.....
// OLDER TOOL LOADERS - PHASING OUT //


// OLDER IMAGE LOADERS - PHASING OUT //
    function getImg($img, ?string $alt = '',?string $class = '') {
        $SITE = $GLOBALS['SITE'];

        $path = "/" . $GLOBALS[$SITE]['SYS_SLUG'] . '/' . $GLOBALS[$SITE]['DOM_SLUG'] . "/" . $img;
        $result = $GLOBALS['SONAR'] . "/i/" . $path;
        if (is_file($result)) {
            $hasClass = $class ? " class='$class'" : "";
            $hasAlt = $alt ? " alt='$alt'" : "";
            
            skylite("<img $hasClass src='" . i_root . "$path' $hasAlt>"); 

            } else {
                error_log("KDE! IMAGE file not found. " . $result);         
            }
    }

    function img($img, $folder, $prefix, ?string $alt = '', ?string $class = '') {
        $path = "/" . $folder . "/" . $prefix . "_" . $img;
        $result = $GLOBALS['SONAR'] . "/i/" . $path;
        if (is_file($result)) {
            $hasClass = $class ? " class='$class'" : "";
            $hasAlt = $alt ? " alt='$alt'" : "";

            echo "<img $hasClass src='" . i_root . "$path' $hasAlt>"; 
            } else {
                error_log("KDE! IMAGE file not found. " . $result);         
            }
    }

?>