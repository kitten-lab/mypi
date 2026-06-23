<?php 

//SHADOW ENV FUNCTION
function SHADOW_PROD_ENV($IS_IT) {
    if ($IS_IT == true) { return '_____/'; }
}

function TEMPLATE($FILE) {
    $GET = __DIR__ . '/templates/' . $FILE;
    return $GET;
}

function THEME_TEMPLATE($FILE,$VARIANT=null) {
    $GET = __DIR__ . '/templates/' . $VARIANT . $FILE;
    return $GET;
}

//error logger
function KDE($ERROR_TYPE, $SOURCE, $ECHO_CHAIN, $ERROR) {
    $TIME_ERRORED = time();
    $KDE_DATE = date('M d h:iA');
    $LOG_RECORD = [
        'UNIX' => $TIME_ERRORED,
        'DATE' => $KDE_DATE,
        'KDE__SOURCE' => $SOURCE,
        'KDE-TYPE' => 'ERR: ' . $ERROR_TYPE,
        'ECHO__TRACKBACK_CHAIN' => $ECHO_CHAIN ?? 'UNDEFINED',
        'KDE__ERROR_MSG' => $ERROR
        ];

    file_put_contents(
        __DIR__ . '/../../../z/KDE/' . date('Y-m') . '_error.json',
        json_encode($LOG_RECORD) . PHP_EOL,
        FILE_APPEND
    );

    die($ERROR);
}



// create a SKY AUTH file //
function CREATE_SKY_AUTH() {
    $template = file_get_contents(TEMPLATE('/SKY_AUTH.php')); 
    return str_replace('{{SYS_SLUG}}', $_POST['SYS_SLUG'], $template);
}

// create a WORLD SIG file //
function CREATE_SKY_SIG(){
    $template = file_get_contents(TEMPLATE('/SKY_SIG.php'));
    
    $result = str_replace(
        ['{{SYS_SLUG}}','{{SYS_DISPLAY}}','{{URI}}'],
        [$_POST['SYS_SLUG'], $_POST['SYS_DISPLAY'], $_POST['URI']],
    $template);
    
    return $result;
}


// create a NAV FIG file //
function CREATE_NAV_FIG(){
    $template = file_get_contents(TEMPLATE('/-FIG-nav.php'));
    
    $result = str_replace(
        ['{{DOM_SLUG}}','{{DOM_DISPLAY}}','{{KEY_SLUG}}','{{KEY_DISPLAY}}'],
        [$_POST['DOM_SLUG'], $_POST['DOM_DISPLAY'], $_POST['KEY_SLUG'], $_POST['KEY_DISPLAY']],
    $template);
    
    return $result;
}

// create a WORLD SIG file //
function CREATE_WORLD_SIG(){
    $template = file_get_contents(TEMPLATE('/WORLD_SIG.php'));

    $result = str_replace(
        ['{{LOVERS_MARK}}'],
        [$_POST['LOVERS_MARK']],
    $template);
    
    return $result;
}

// MAKE THE DEFAULT ERRORS FOR DOORS
function CREATE_ERROR_FIG(){
    $template = file_get_contents(TEMPLATE('/ERROR_FIG.php', $VARIANT));
    return $template;
}

 // MAKE THE INDEX
function CREATE_INDEX(){
    $template = file_get_contents(TEMPLATE('/index.php')); 
    return str_replace('{{SYS_SLUG}}', $_POST['SYS_SLUG'], $template);
}

 // MAKE THE WELCOME
function CREATE_WELCOME_HOME(){
    $template = file_get_contents(THEME_TEMPLATE('/welcome.php'));

    $result = str_replace(
        ['{{URI}}', '{{SYS_DISPLAY}}', '{{DOM_SLUG}}', '{{KEY_SLUG}}'],
        [$_POST['URI'], $_POST['SYS_DISPLAY'], $_POST['DOM_SLUG'], $_POST['KEY_SLUG']],
    $template);
    
    return $result;
}


 // MAKE THE FIRST KEY
function CREATE_FIRST_ROOMKEY(){
    $template = file_get_contents(TEMPLATE('/{{KEY_SLUG}}.php'));

    $result = str_replace(
        [
        '{{MOD_SLUG}}', 
        '{{MOD_DISPLAY}}', 
        '{{DOM_SLUG}}', 
        '{{DOM_DISPLAY}}',
        '{{KEY_SLUG}}', 
        '{{KEY_DISPLAY}}'
        ],
        [
        $_POST['MOD_SLUG'], 
        $_POST['MOD_DISPLAY'], 
        $_POST['DOM_SLUG'], 
        $_POST['DOM_DISPLAY'],
        $_POST['KEY_SLUG'], 
        $_POST['KEY_DISPLAY']
            ],
    $template);
    
    return $result;
}

function CREATE_BASE_SHELL($VARIANT) {
    $template = file_get_contents(THEME_TEMPLATE('/shell.php', $VARIANT));
    return str_replace('{{SYS_SLUG}}', $_POST['SYS_SLUG'], $template);
}

function CREATE_BASE_HEADER($VARIANT){
    $template = file_get_contents(THEME_TEMPLATE('/header.php', $VARIANT));
    $result = str_replace(
        ['{{SYS_SLUG}}','{{SYS_DISPLAY}}'],
        [$_POST['SYS_SLUG'], $_POST['SYS_DISPLAY']],
    $template);
    return $result;
}

function CREATE_BASE_FOOTER($VARIANT){
    $template = file_get_contents(THEME_TEMPLATE('/footer.php', $VARIANT));
    return $template;
}

function CREATE_BASE_NAV($VARIANT){
    $template = file_get_contents(THEME_TEMPLATE('/nav.php', $VARIANT));
    return $template;
}

function CREATE_BASIC_STYLE($VARIANT){
    $template = file_get_contents(THEME_TEMPLATE('/style.css', $VARIANT));
    return $template;
}
?>