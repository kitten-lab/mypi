<?php
global $SONAR;

require_once $SONAR . 't/tools/parsedown/Parsedown.php'; 
$json = file_get_contents(__DIR__ . '/../../../z/logs/2025-08-17__Build_entry_levels__663msgs.json');
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
    echo "<div>{$msg['create_time']}</div>";
    echo "<strong>" . strtoupper($msg['role']) . ":</strong><br>";
    echo $Parsedown->text($msg['text']);
    echo "</div><br>";
}
?>