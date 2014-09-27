<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['action'] == 'really')
{
  delete_inventory_byid($this_inventory['idnum']);

  add_inventory_quantity($user['user'], '', 'Fluff', 'Released from a ' . $this_inventory['itemname'], $this_inventory['location'], 500);
  add_inventory($user['user'], '', 'Super Stringy Orange Belt', '', $this_inventory['location']);

  $AGAIN_WITH_ANOTHER = true;
  
  echo '<p>The Super Stringy Orange Belt holding the bale together comes undone with the gentlest of pulls, yet the 500 Fluff it contained explode the moment the belt is released.</p>';
}
else
{
  echo '<p>Whoa, really?</p>';
  echo '<ul><li><a href="?idnum=' . $this_inventory['idnum'] . '&action=really">Yeah, man - really.</a></li></ul>';
}
