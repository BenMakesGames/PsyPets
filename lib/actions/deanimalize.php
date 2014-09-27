<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

if(rand(1, 6) == 1)
{
  echo "You start to untwist the balloons, but they pop!";
}
else if(rand(1, 3) == 1)
{
  echo "You start to untwist the balloons, but one pops!";
  add_inventory($user['user'], '', 'Balloons', '', $this_inventory["location"]);
}
else
{
  add_inventory($user['user'], '', 'Balloons', '', $this_inventory["location"]);
  add_inventory($user['user'], '', 'Balloons', '', $this_inventory["location"]);
  echo "You untwist the animal into its constituent balloons.";
}
?>
