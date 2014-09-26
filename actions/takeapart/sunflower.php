<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

$num_seeds = mt_rand(8, mt_rand(11, 13));

add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Sunflower Seeds', 'Harvested from a Sunflower', $this_inventory['location'], $num_seeds);

echo '<p>You harvest ' . $num_seeds . ' Sunflower Seeds.</p>';
?>
