<?php
if($okay_to_be_here !== true)
  exit();

$data = (int)$this_inventory['data'];
$now = time();
$day = 60 * 60 * 24;

$getitem = false;
$dies = false;

if($now > $data)
{
  if(rand(1, 40) == 1)
  {
    $dies = true;

    delete_inventory_byid($this_inventory['idnum']);
  }
  else
  {
    $data = $now + rand(60 * 60 * 2, 60 * 60 * 8);
    $getitem = true;

    $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1";
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  }
}

if($dies)
{
  for($x = 0; $x < 2; ++$x)
    add_inventory($user["user"], '', 'Stringy Rope', "The remains of an Alchemist's Knapsack", $this_inventory["location"]);

  echo "You reach inside the bag and pull something out: a thread!  Some of it is still inside the bag, so you pull on it some more, and more, and more... there seems no end to it!</p><p>Nearly a full minute later you feel that the end of the string is near.  You give one last tug, and as the end of the thread emerges the knapsack is unraveled, leaving you with nothing but a ball of string.";
}
else if($getitem)
{
  $items = array('Carrot', 'Talon', 'Blood', 'Venom', 'Ether Condensate', 'Abraxas Stone', 'Feather', 'Coal', 'Pearl', 'Vinegar');
  $item = $items[array_rand($items)];

  add_inventory($user["user"], '', $item, "Pulled out of an Alchemist's Knapsack", $this_inventory["location"]);
  echo 'You reach inside the bag and pull something out: ' . $item . '!';
}
else
  echo 'You try to open the bag, but it refuses!  Perhaps you should try again a little later...';

?>
