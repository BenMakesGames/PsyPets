<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

$num_peas = mt_rand(1, 3);

add_inventory_quantity($user['user'], '', 'Pasta', '', $this_inventory['location'], $num_peas);

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Spaghetti Squash Opened', 1);

echo '
  <p>Breaking open the Spaghetti Sqash reveals ' . $num_peas . ' Pasta!</p>
  <p>(Surely you weren\'t expecting any different...)</p>
';
?>
