<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

$items = array(
  "Figurine #3", "Figurine #4", "Figurine #4",
  "Duck Plushy", "Desikh Plushy",
  "Chocolate Drops", "Chocolate Drops",
  "Fluff", "Fluff", "Fluff",
  'Hamlet: Act V Scene II', 'Hamlet: Act I Scene II',
  "Mintberry Swirls", "Mintberry Swirls", "Mintberry Swirls",
  "Red Taffy", "Purple Taffy", "Green Taffy", "Orange Taffy"
);

if($this_item["itemname"] == "Red-Dyed Egg")
  $items[] = "Hat";
else if($this_item["itemname"] == "Yellow-Dyed Egg")
  $items[] = "Jacks";
else if($this_item["itemname"] == "Blue-Dyed Egg")
  $items[] = "Black Bishop";

$n = rand(2, 3);
$itemlist = array();

for($i = 0; $i < $n; ++$i)
{
  $itemname = $items[array_rand($items)];
  $itemlist[] = $itemname;
  add_inventory($user["user"], '', $itemname, "Recovered from " . $this_item["itemname"], $this_inventory["location"]);
}

delete_inventory_byid($this_inventory['idnum']);
?>
<p>Opening the egg reveals: <?= implode(", ", $itemlist) ?>.</p>
