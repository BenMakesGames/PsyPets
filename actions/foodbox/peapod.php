<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

$num_peas = mt_rand(2, 4);

add_inventory_quantity($user['user'], '', 'Peas', '', $this_inventory['location'], $num_peas);

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Pea Pods Opened', 1);

echo '
  <p>Breaking open the Pea Pods reveals ' . $num_peas . ' Peas!</p>
  <p>(Surely you weren\'t expecting any different...)</p>
';
?>
