<?php
if($okay_to_be_here !== true)
  exit();

$time_between_feeding = 60 * 60;

$data = explode(';', $this_inventory['data']);

$exp = (int)$data[0];
$last_feed_time = (int)$data[1];

$upgraded = false;

if($last_feed_time + $time_between_feeding <= $now)
{
  echo '<p><i>(The Hungry Silkworm has built its cocoon!)</i></p>';

  $command = 'UPDATE monster_inventory SET `data`=\'\',itemname=\'Silkworm Cocoon\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating exp/level');

  $AGAIN_WITH_ANOTHER = true;
}
else
{
  echo '<p>The Sated Silkworm is building its cocoon.  <i>(And probably won\'t be done until ' . Duration(($last_feed_time + $time_between_feeding) - $now) . ' from now...)</i></p>'; 
}
?>
