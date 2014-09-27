<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/debris.php';

$AGAIN_WITH_ANOTHER = true;
$RECOUNT_INVENTORY = true;

delete_inventory_byid($this_inventory['idnum']);

$itemname = GenerateItemFromDebris($user, ($this_item['itemname'] == 'Debris'));

add_inventory($user['user'], '', $itemname, 'Recovered from ' . $this_item['itemname'], $this_inventory['location'], $this_inventory['locid']);

$descript = array(
  'rummage through',
  'search',
  'sort out',
);

$descript2 = array(
  'eventually recovering',
  'revealing',
  'exposing',
);

echo '<p>You ', $descript[array_rand($descript)], ' the ', $this_item['itemname'], ', ', $descript2[array_rand($descript2)], ' some ', $itemname, '.</p>';
?>
