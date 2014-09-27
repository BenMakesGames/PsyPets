<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

echo '<p>The ' . $this_inventory['itemname'] . ' yields three Blue Dye.</p>';

add_inventory($user['user'], $this_inventory['creator'], 'Blue Dye', '', $this_inventory['location']);
add_inventory($user['user'], $this_inventory['creator'], 'Blue Dye', '', $this_inventory['location']);
add_inventory($user['user'], $this_inventory['creator'], 'Blue Dye', '', $this_inventory['location']);
?>
