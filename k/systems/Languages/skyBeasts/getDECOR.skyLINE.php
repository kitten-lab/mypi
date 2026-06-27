<?php



// projectIMG for loading images in SKYLITE //
function getDecor(
  /* required */  string $Type, string $Projection, 
  /* optional */  ?string $shell = null, ?string $class = null, ?string $alt = null
){
  // hydrate:
  $URI = BLOCK_URI;

// handle Image Decorations
  if ($Type == "I"){
  // projection pathway:      
    $SKY_Validate = echoSONAR . "m/decor/" . $URI . "/" . $Projection;

    if(is_file($SKY_Validate)) {
      $hasClass = $class ? " class='$class'" : "";
      $hasAlt = $alt ? " class='$alt'" : "";
      if ($shell == "wires") {
        echo "<img src='" . M_ROUTE . "/decor/$URI/$Projection' $hasClass $hasAlt>";
      } else {
        skylite("<img src='" . M_ROUTE . "/decor/$URI/$Projection' $hasClass $hasAlt>");
      }
    
    } else {
    
      if ($shell == "wires") {
        echo "<span class='MissingProjection'></span>";
      } else {
        skylite("<span class='MissingProjection'></span>");
      }
    }
  }

  if ($Type == "CSS"){
  // projection pathway:      
    $SKY_Validate = echoSONAR . "m/dressings/" . $URI . "/" . $Projection;
    if(is_file($SKY_Validate)) {
      if ($shell == "wires") {
        getFILLER("<link rel='stylesheet'  type='text/css' href='" . M_ROUTE . "'/dressings/$URI/$Projection'>", "dressing");
      } else {
        skylite("<link rel='stylesheet'  type='text/css' href='" . M_ROUTE . "'/dressings/$URI/$Projection'>");
      }
    
    } else {
    
      KDE_Error_Logger("","KDE! CSS Stylesheet: $Projection not found in $SKY_Validate");
      if ($shell == "wires") {
        echo "<span class='MissingProjection'></span>";
      } else {
        skylite("<span class='MissingProjection'></span>");
      }

  }
}
}



