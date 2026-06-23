<?php 
// definers
define("post_topic", $_POST['post_topic'] ?? "");
define("post_leaf", $_POST['post_leaf'] ?? "");

function json_payload(){
    return [
    "post" => [
        "agent" => $_POST['agent'],
        "post_topic" => post_topic,
        "post_leaf" => post_leaf
    ]];
}

function json_route(){
    return [];
}