<?php
if($okay_to_be_here !== true)
  exit();

echo '<p>A Hungry Silkworm (level 0) emerges!</p>';

$command = 'UPDATE monster_inventory SET `data`=\'\',itemname=\'Hungry Silkworm (level 0)\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'updating exp/level');

$AGAIN_WITH_ANOTHER = true;
?>
