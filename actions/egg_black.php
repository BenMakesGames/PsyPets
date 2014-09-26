<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

create_random_offspring($user['user'], 1, array('chickie_black.png'));
echo 'The egg hatches, and a small, silvery-black chickie emerges!';
?>
