<?php
if($okay_to_be_here !== true)
  exit();

$amount = delete_inventory_byname($user['user'], 'Dirty Linen', 1, $this_inventory['location']);

if($amount == 0)
  echo '<p>There aren\'t any Dirty Linens to wash here.</p>';
else
{
  add_inventory($user['user'], 'u:' . $user['idnum'], 'White Cloth', $user['display'] . ' washed this Dirty Linen', $this_inventory['location']);

  echo '<p>You wash a single Dirty Linen.</p>';

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Dirty Linens Washed', 1);

  $AGAIN_WITH_SAME = true;
}
?>
