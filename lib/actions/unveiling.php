<?php
if($okay_to_be_here !== true)
  exit();

$health = $this_inventory['health'];
$data = $this_inventory['data'];
$now = time();
$day = 60 * 60 * 24;

$coconut = false;
$dies = false;

if(strlen($data) == 0)
{
  $data = $now + $day;

  $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1";
  $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
}
else if($now > $data)
{
  if(rand(1, 20) == 1)
  {
    $dies = true;
    delete_inventory_byid($this_inventory['idnum']);
  }
  else
  {
    $data = $now + rand($day, $day * 3);
    $coconut = true;

    $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1";
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  }
}

if($dies)
{
  add_inventory($user["user"], '', "Log", "Recovered from a Minipalm", $this_inventory["location"]);
  echo "You shake the tree; it collapses, apparently dead.</p>\n<p>You recover a Log from its trunk.";
}
else if($coconut)
{
  add_inventory($user["user"], '', "Coconut", "Shaken loose from a Minipalm", $this_inventory["location"]);
  echo "You shake the tree; a Coconut falls out.";
}
else
  echo "You shake the tree; nothing falls out.  Maybe you should try again tomorrow.";

?>
