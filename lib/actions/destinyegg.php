<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

$items = array(
  'Sword of Fire',
  'Shield of Earth',
  'Scarf of Wind',
  'Crown of Water',
);

$itemname = $items[array_rand($items)];
add_inventory($user['user'], '', $itemname, 'Recovered from ' . $this_item['itemname'], $this_inventory['location']);

delete_inventory_byid($this_inventory['idnum']);
?>
<p>Opening the egg reveals a <?= $itemname ?>!</p>
