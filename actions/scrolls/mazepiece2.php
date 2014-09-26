<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;
  
delete_inventory_byid($this_inventory['idnum']);

$tiles = mt_rand(2, mt_rand(3, mt_rand(4, mt_rand(5, 8))));

echo '<p>The scroll bends and twists, replaced by ' . $tiles . ' Maze Pieces which clatter to the ground:</p><ul>';

for($x = 0; $x < $tiles; ++$x)
{
  $dirs = array('N', 'E', 'S', 'W', 'NE', 'ES', 'SW', 'NW', 'NS', 'EW', 'NES', 'ESW', 'NSW', 'NEW', 'NESW');

  $itemname = 'Maze Piece (' . $dirs[array_rand($dirs)] . ')';

  echo '<li>' . $itemname . '</li>';
  
  add_inventory($user['user'], '', $itemname, 'Created by a ' . $this_inventory['itemname'], $this_inventory['location']);
}

echo '</ul>';
?>
