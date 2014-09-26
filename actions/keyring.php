<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/statlib.php';

delete_inventory_byid($this_inventory['idnum']);

$AGAIN_WITH_ANOTHER = true;

if(rand() % 1000 == 0)
{
  add_inventory($user["user"], '', "Gold Key", "Found on a Key Ring", $this_inventory["location"]);

  $message = 'The Key Ring holds a single Gold Key, which you take.';

  record_stat($user['idnum'], 'Received a Gold Key From a Key Ring', 1);
}
else
{
  $num_items = rand(1, 3);
  if($num_items == 3)
    $num_items = rand(1, 3);

  for($x = 0; $x < $num_items; ++$x)
    add_inventory($user["user"], '', "Skeleton Key", "Found on a Key Ring", $this_inventory["location"]);

  record_stat($user['idnum'], 'Received a Skeleton Key From a Key Ring', $num_items);

  if($num_items == 1)
    $message = 'The Key Ring holds a single Skeleton Key, which you take.';
  else
    $message = "The Key Ring holds $num_items Skeleton Keys, which you take.";
}
?>
<p><?= $message ?></p>
