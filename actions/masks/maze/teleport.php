<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/mazelib.php';

$destination = $database->FetchSingle("SELECT idnum FROM psypets_maze WHERE obstacle='none' AND tile!='1111' AND idnum!=" . $user['mazeloc'] . " ORDER BY rand() LIMIT 1");

if($destination === false)
{
  echo 'You read the words on the scroll as loudly as you do clearly, but nothing happens.';
}
else
{
  maze_move_user($user['idnum'], $destination['idnum']);

  $dir = (rand(1, 2) == 1 ? 'left' : 'right');

  echo 'As the scroll turns to dust you feel as though every atom in your body shifts just a couple centimeters to the ' . $dir . '.  Or maybe you\'re just remembering having felt it before... or maybe you\'re remembering having felt it in a dream...';

  delete_inventory_byid($_GET["idnum"]);
}
?>
