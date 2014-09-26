<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

$num_items = mt_rand(3, 6);

delete_inventory_byid($this_inventory['idnum']);

$itemnames = array();

for($i = 0; $i < $num_items; ++$i)
  $itemnames[] = 'Town Square';

if(mt_rand(1, 100) == 1)
  $itemnames[] = 'Maze Piece Summoning Scroll';

sort($itemnames);

$items = 0;

foreach($itemnames as $itemname)
{
  add_inventory($user['user'], '', $itemname, 'Recovered from ' . $this_item['itemname'], $this_inventory['location']);

  $items++;

  if($items > 1)
    $itemlist .= ($items == count($itemnames) ? ' and ' : ', ');

  $itemlist .= $itemname;
}

$message = 'You shuffle through the tiles, revealing '. $itemlist . '.';
?>
<p><?= $message ?></p>
