<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

add_inventory($user['user'], '', 'Hammer', '', $this_inventory['location']);
add_inventory($user['user'], '', 'Hammer', '', $this_inventory['location']);

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Took Apart a ' . $this_inventory['itemname'], 1);
?>
You take the <?= $this_inventory['itemname'] ?> apart.  (But you lose the string.)
