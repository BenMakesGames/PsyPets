<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

$descript2 = array(
  'revealing',
  'rewarding you with',
  'exposing',
);

$items = array(
  'Figurine #C',
  'Figurine #D',
  'Lion Plushy',
  'Ghosty Plushy',
  'Snappy Bricks',
  'Windup Woof',
  'Pet Rock Amulet',
);

delete_inventory_byid($this_inventory['idnum']);

$itemname = $items[array_rand($items)];

add_inventory($user['user'], $this_inventory['creator'], $itemname, 'Recovered from ' . $this_item['itemname'], $this_inventory['location']);
add_inventory($user['user'], $this_inventory['creator'], 'Chocolate Drops', 'The remains of a ' . $this_item['itemname'], $this_inventory['location']);

echo 'You break open the ' . $this_item['itemname'] . ', ' . $descript2[array_rand($descript2)] . ' ' . $itemname . '.';
?>
