<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;
  
$dirs = array('N', 'E', 'S', 'W', 'NE', 'ES', 'SW', 'NW', 'NS', 'EW', 'NES', 'ESW', 'NSW', 'NEW', 'NESW');

$itemname = 'Maze Piece (' . $dirs[array_rand($dirs)] . ')';

$command = 'UPDATE monster_inventory SET itemname=' . quote_smart($itemname) . ',health=250 WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'transforming scroll');
?>
<p>The scroll folds itself into a <?= $itemname ?>!</p>
