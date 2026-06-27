
<?php




function getA_Style(string $css, string $folder, string $function) {
    $path = "/" . $folder . "/" . $function . "/" . $css . ".css";
    $full = echoSONAR . "a" . $path;
    if (is_file($full)) {
         echo '<link rel="stylesheet"  type="text/css" href="' . A_ROUTE . $path . '">';
         } else {
            error_log("PATH NOT FOUND getAStyle " . $path);

         }
}


function invokeStyle(string $css, string $folder) {
    $path = "/" . $folder . "/" . $css . ".css";
    $full = echoSONAR . "a" . $path;
    if (is_file($full)) {
         echo '<link rel="stylesheet"  type="text/css" href="' . A_ROUTE . $path . '">';
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
        $result = echoSONAR . "/i/" . $path;
        if (is_file($result)) {
            $hasClass = $class ? " class='$class'" : "";
            $hasAlt = $alt ? " alt='$alt'" : "";
            
            skylite("<img $hasClass src='" . i_root . "$path' $hasAlt>"); 

            } else {
                error_log("KDE! IMAGE file not found. " . $result);         
            }
    }

    function img(string $img, string $folder, string $prefix, ?string $alt = '', ?string $class = '') {
        $path = "/" . $folder . "/" . $prefix . "_" . $img;
        $result = echoSONAR . "/i/" . $path;
        if (is_file($result)) {
            $hasClass = $class ? " class='$class'" : "";
            $hasAlt = $alt ? " alt='$alt'" : "";

            echo "<img $hasClass src='" . i_root . "$path' $hasAlt>"; 
            } else {
                error_log("KDE! IMAGE file not found. " . $result);         
            }
    }

?>