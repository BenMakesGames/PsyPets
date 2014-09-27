<?php
if($okay_to_be_here !== true)
  exit();

delete_inventory_byid($this_inventory['idnum']);

$quantity = 9;

if(mt_rand(1, 100) == 1)
  $quantity--;

add_inventory_quantity($user['user'], $this_inventory['creator'], 'Raw Milk', '', $this_inventory['location'], 9);
?>
<p>You receive <?= $quantity ?> Raw Milk!</p>
<?php
if($quantity == 8)
  echo '<p>(Your grip slipped, causing you to spill the contents of one jug all over the place!  But please don\'t cry.)</p>';
?>
