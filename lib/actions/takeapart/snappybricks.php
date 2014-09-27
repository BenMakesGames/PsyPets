<?php
if($okay_to_be_here !== true)
  exit();

$bricks = (int)$action_info[2];

if($bricks < 1)
  die('Error in item action!  Please notify an administrator!');

delete_inventory_byid($this_inventory['idnum']);

for($x = 0; $x < $bricks; ++$x)
  add_inventory($user['user'], '', 'Snappy Bricks', '', $this_inventory['location']);

$AGAIN_WITH_ANOTHER = true;

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Took Apart a Snappy Brick Construction', 1);
?>
You take the <?= $this_inventory['itemname'] ?> apart into its constituent Snappy Bricks.
