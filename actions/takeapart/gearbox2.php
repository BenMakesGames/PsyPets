<?php
if($okay_to_be_here !== true)
  exit();

delete_inventory_byid($this_inventory['idnum']);

$AGAIN_WITH_ANOTHER = true;

add_inventory_quantity($user['user'], '', 'Gears', '', $this_inventory['location'], 6);

echo '<p>You disassemble the Gearbox, recovering 6 Gears.</p>';
?>