<?php
function catchjson($selected){
    // sanitize (VERY important)
    $selected = basename($selected);

    $newpath = __DIR__ . '/../../../z/logs/' . $selected;

    if (!file_exists($newpath)) {
        echo "File not found: " . htmlspecialchars($selected);
        return;
    }

    $json = file_get_contents($newpath);
    $data = json_decode($json, true);
    $mapping = $data['mapping'];

$current = null;

foreach ($mapping as $id => $node) {
    if ($node['parent'] === null) {
        $current = $node;
        break;
    }
}


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
        echo "<div class='msg {$msg['role']}'>";
        echo "<strong>" . strtoupper($msg['role']) . ":</strong><br>";
        echo nl2br(htmlspecialchars($msg['text']));
        echo "</div><br>";
    }
}

if (isset($_GET['file'])) {
    catchjson($_GET['file']);
}
?>