
<?php 
require __DIR__ . '/../../systems/rehydrateSelf.php';

include __DIR__ . '/../parsedown/Parsedown.php';

$Parsedown = new Parsedown();

$posts = json_decode(file_get_contents($GLOBALS['sonar'] . 'd/mailRoom/' . $sys . '/data.json'), true);
$id = $_GET['mail'];

$config = $blogBasic ?? []; 

foreach ($posts as $post) {
  if ($post['meta.DATA']['chest.UNIX'] == $id) {
    echo "<h1>{$post['log.leafTopic']}</h1>";
    echo "<small>" . date("Y-m-d H:i", $post['meta.DATA']['chest.UNIX']) . "</small>";
    echo $Parsedown->text($post['log.leafText']);
  }
}

echo '<br><a href="javascript:history.go(-1)" title="Return to previous page">« Go back</a>';
?>
</div>