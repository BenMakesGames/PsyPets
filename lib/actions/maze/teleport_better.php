<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/mazelib.php';

maze_move_user($user, 0);

$dir = (rand(1, 2) == 1 ? 'left' : 'right');

echo '<p>As the scroll turns to dust you feel as though every atom in your body shifts just a couple centimeters to the ' . $dir . '.</p><p>Or maybe you\'re just remembering having felt it before...<p><p>... or maybe you\'re remembering having felt it in a dream...</p>';

delete_inventory_byid($_GET["idnum"]);
?>
