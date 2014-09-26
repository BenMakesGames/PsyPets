<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

add_inventory($user['user'], '', 'Iron', '', $this_inventory['location']);
add_inventory($user['user'], '', 'Iron', '', $this_inventory['location']);
add_inventory($user['user'], '', 'Iron', '', $this_inventory['location']);

echo '<p>You scrap the ' . $this_inventory['itemname'] . ', retrieving three Iron.</p>';
?>
