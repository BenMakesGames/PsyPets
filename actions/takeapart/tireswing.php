<?php
if($okay_to_be_here !== true)
  exit();

delete_inventory_byid($this_inventory['idnum']);

$AGAIN_WITH_ANOTHER = true;

add_inventory($user['user'], '', 'Old Tire', '', $this_inventory['location']);
add_inventory($user['user'], '', 'Stringy Rope', '', $this_inventory['location']);

echo '<p>You disassemble the Tire Swing into an Old Tire and a bit of Stringy Rope.</p>';
?>