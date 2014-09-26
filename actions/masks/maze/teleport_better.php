<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/mazelib.php';

maze_move_user($user['idnum'], 0);

$dir = (rand(1, 2) == 1 ? 'left' : 'right');

echo 'As the scroll turns to dust you feel as though every atom in your body shifts just a couple centimeters to the ' . $dir . '.  Or maybe you\'re just remembering having felt it before... or maybe you\'re remembering having felt it in a dream...';

delete_inventory_byid($_GET["idnum"]);
?>
