<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

$items = array(
  "4-Leaf Clover", "Clover Leaf",
  "Paper Hat", "Paper Hat",
  "Figurine #5", "Figurine #6", "Figurine #B",
  "Pearl", "6-Sided Die",
  "Chocolate Drops", "Chocolate Drops",
  "Black Dye", "Leather", "Black Dye",
  "Mintberry Swirls", "Mintberry Swirls", "Mintberry Swirls",
  "Red Taffy", "Purple Taffy", "Sour Taffy", "Orange Taffy"
);

$n = rand(2, 4);
$itemlist = array();

for($i = 0; $i < $n; ++$i)
{
  $itemname = $items[array_rand($items)];
  $itemlist[] = $itemname;
  add_inventory($user['user'], '', $itemname, 'Recovered from ' . $this_item['itemname'], $this_inventory['location']);
}

delete_inventory_byid($this_inventory['idnum']);
?>
<p>Opening the egg reveals: <?= implode(', ', $itemlist) ?>.</p>
