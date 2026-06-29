<?php 
include ROUTE_TO_SYSTEMS . 'skyInvocations.php';
include ROUTE_TO_SYSTEMS . 'wireWORDS.php';
include ROUTE_TO_SYSTEMS . 'Languages/skyBeasts.php';

function skylite(string $result) {
  getFILLER($result, "set");
}

function openSky(string $title){
  $GLOBALS['pageTitle'] = $title;
}

function h1(string $text){
    $text = htmlspecialchars($text);
    skylite("<h1>$text</h1>");
}

function bigHeading(string $text){
    h1($text);

    }
function makeLink(string $link, string $title){
    skylite("<a href='$link'>$title</a>");
}

function shelf(string $shelf_name){
    skylite('<SHELF
  id="' . $shelf_name . '" 
  class="SHELF" 
>');
}

function ROM_SCREEN(){
 skylite('<ROM_SCREEN id="ROM_SCREEN" class="ROM_SCREEN">
 
 </ROM_SCREEN>');
}

function close_shelf(){
    skylite("</SHELF>");
}

function getMyID(string $thing) {
    
  $SITE = $GLOBALS['SITE'];
    $thing = strtoupper($thing);
    $YourThing = $GLOBALS[$SITE][$thing];

    return $YourThing;
}

function title(string $text, string $id, string $hscale){
    $text = htmlspecialchars($text);
    skylite("<h$hscale id='$id'>$text</h$hscale>");
    }

function hr(){
    skylite("<hr>");
    }
function medHeading(string $text){
    $text = htmlspecialchars($text);
    skylite("<h2>$text</h2>");
}

function colorize(string $color) {
    skylite("<span style='color: $color;'>");
}

function stop_colorize() {
    skylite("</span>");
}

function leaf(string $text) {
    skylite("<p>" . nl2br($text) . "</p>");
}

function wordsx(string $text, $c="") {
    skylite("<span style='$c'>$text</span>");
}

function section(string $instructions, string $class) {
    $GLOBALS['SKY_STACK'][$class] = "on";
    skylite("<div class='$class' style='$instructions'>");
}

function close_section() {
    array_pop($GLOBALS['SKY_STACK']);
    skylite("</div>");
}

function closeSky() {
    if (!empty($GLOBALS['SKY_STACK'])) {
        $times = count($GLOBALS['SKY_STACK']);
    for ($i = 0; $i < $times; $i++) {
            skylite("1</div>");
    }
        error_log("KDE! KDE! Unclosed section detected\n" . print_r($GLOBALS['SKY_STACK'], true));
    };
}
