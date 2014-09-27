<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

$items = array(
  "Gold", "Gold", "Gold", "Gold", "Gold Ring",
  "Pyrestone",
  "Small Giamond Ring",
  "Silver", "Silver",
  "Silver Tiara",
  "Skull",
  "Copper",
  'Hamlet: Act I Scene I',
  "Eye Patch",
);

shuffle($items);

delete_inventory_byid($this_inventory['idnum']);

$amount = rand(4, 7);

$items = array_slice($items, 0, $amount);
$items[] = "Arms";

echo "Opening the chest reveals: ";

foreach($items as $item)
{
  $item_list[] = $item;
  add_inventory($user["user"], '', $item, "Found in a Treasure Chest", $this_inventory["location"]);
}

sort($item_list);

echo implode(", ", $item_list);
?>
