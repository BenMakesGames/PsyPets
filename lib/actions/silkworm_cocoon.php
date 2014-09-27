<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;
$RECOUNT_INVENTORY = true;

delete_inventory_byid($this_inventory['idnum']);

add_inventory($user['user'], 'u:' . $user['idnum'], 'Silk', 'Harvested from a Silkworm Cocoon', $this_inventory['location']);
add_inventory($user['user'], 'u:' . $user['idnum'], 'Silk', 'Harvested from a Silkworm Cocoon', $this_inventory['location']);
add_inventory($user['user'], 'u:' . $user['idnum'], 'Silk', 'Harvested from a Silkworm Cocoon', $this_inventory['location']);
add_inventory($user['user'], 'u:' . $user['idnum'], 'Fluff', 'Harvested from a Silkworm Cocoon', $this_inventory['location']);
add_inventory($user['user'], 'u:' . $user['idnum'], 'Fluff', 'Harvested from a Silkworm Cocoon', $this_inventory['location']);

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Harvested a Silkworm Cocoon', 1);
?>
<p>Harvesting the Silkworm Cocoon yields three Silk and two Fluff.</p>
