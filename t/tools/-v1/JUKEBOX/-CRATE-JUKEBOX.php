<?php 

function json_payload(){
    return [
    "jukebox" => [
        "JUKEID" => $GLOBALS['JUKEID'],
        "artist" => $_POST['artist'],
        "song" => $_POST['song_title'],
        "spotify_link" => $_POST['link'],
    ]];
}

function json_route(){
$SITE = $GLOBALS['SITE'];
    return [
        "listener" => $_POST['UPLOADER']
    ];
}
