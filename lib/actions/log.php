<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

if(rand(1, 2) == 1)
{
	add_inventory_quantity($user["user"], 'u:' . $user['idnum'], "Wood", "", $this_inventory["location"], 2);
  add_inventory($user["user"], 'u:' . $user['idnum'], "Paper", "", $this_inventory["location"]);
  $items = "two Wood planks.  The leftovers are processed to make some Paper";
}
else
{
	add_inventory_quantity($user["user"], 'u:' . $user['idnum'], "Wood", "", $this_inventory["location"], 3);
  $items = "three Wood planks";
}

if(rand(1, 100) == 1)
{
  add_inventory($user["user"], 'u:' . $user['idnum'], "Amber", "", $this_inventory["location"]);
  $items = "-- what's this?!  There's a bit of Amber on this Log!</p><p>You carefully put it aside, and continue to saw, yielding $items";
}
?>
<p>You saw the Log into <?= $items ?>.</p>
