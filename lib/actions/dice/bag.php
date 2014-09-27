<?php
if($okay_to_be_here !== true)
  exit();

delete_inventory_byid($this_inventory['idnum']);

$items = array(
  '4-Sided Die',
  '6-Sided Die',
  '6-Sided Die',
  '8-Sided Die',
);

$n = rand(2, 4);
$itemlist = array();

for($i = 0; $i < $n; ++$i)
{
  $itemname = $items[array_rand($items)];
  $itemlist[] = $itemname;
  add_inventory($user["user"], '', $itemname, "Recovered from " . $this_item["itemname"], $this_inventory["location"]);
}

$AGAIN_WITH_ANOTHER = true;
?>
<p>Opening the bag reveals: <?= implode(', ', $itemlist) ?>.</p>
