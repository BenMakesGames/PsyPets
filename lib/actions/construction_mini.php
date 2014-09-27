<?php
if($okay_to_be_here !== true)
  exit();

$size = $action_info[2];

delete_inventory_byid($this_inventory['idnum']);

$command = 'UPDATE monster_houses SET maxbulk=maxbulk+' . $size . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'expanding your house (deliciously)');

echo '<p>Your house has been expanded by ' . ($size / 10) . ' units.</p>';

$AGAIN_WITH_ANOTHER = true;
?>
