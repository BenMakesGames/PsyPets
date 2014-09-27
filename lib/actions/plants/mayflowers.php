<?php
if($okay_to_be_here !== true)
  exit();

$data = (int)$this_inventory['data'];
$day = 60 * 60 * 22;

$harvested = false;

if($now > $data)
{
  $data = $now + $day;

  $command = 'UPDATE monster_inventory SET data=\'' . $data . '\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating');

  add_inventory($user['user'], '', 'May Flower', '', $this_inventory['location']);

  echo '<p>You pluck a single May Flower from the vase.</p>';
}
else
  echo '<p>You will be able to pick another May Flower in ' . duration($data - $now, 2) . '...</p>';
?>
