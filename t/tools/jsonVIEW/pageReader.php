<?php
global $SONAR;

require_once ROUTE_TO_SYSTEMS . 'Borrows/parsedown/Parsedown.php'; 
$json = file_get_contents(__DIR__ . '/../../../z/logs/2025-02-10__Oyzis_ritual__46msgs.json');
$data = json_decode($json, true);

$mapping = $data['mapping'];

$current = null;

foreach ($mapping as $id => $node) {
    if ($node['parent'] === null) {
        $current = $node;
        break;
    }
}

$messages = [];

while ($current) {
    if (isset($current['message']['content']['parts'][0])) {
        $messages[] = [
            'create_time' => $current['message']['create_time'],
            'role' => $current['message']['author']['role'],
            'id' => $current['id'],
            'text' => $current['message']['content']['parts'][0]
        ];
    }

    $children = $current['children'] ?? [];
    if (count($children) > 0) {
        $current = $mapping[$children[0]];
    } else {
        $current = null;
    }
}
foreach ($messages as $msg) {
        $Parsedown = new Parsedown();
    echo "<div class='msg {$msg['role']}'>";
    echo "<input type='checkbox' id='checked' value='yes'>";
    echo "<strong>" . strtoupper($msg['role']) . ":</strong><br>";
    echo $Parsedown->text($msg['text']);
    echo "<pre>";
    echo "UNIX TIME: {$msg['create_time']}";
    echo " - GPT-UID: {$msg['id']}";
    echo "</pre>";
    echo "<hr>";
    echo "</div><br>";
}
?>