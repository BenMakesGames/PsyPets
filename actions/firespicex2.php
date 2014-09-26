<?php
if($okay_to_be_here !== true)
  exit();

$RECOUNT_INVENTORY = true;
$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

add_inventory_quantity($user['user'], '', 'Fire Spice', 'Harvested from a ' . $this_inventory['itemname'], $this_inventory['location'], 2);

echo '<p>You harvested two Fire Spice from the ' . $this_inventory['itemname'] . '!</p>';
?>
