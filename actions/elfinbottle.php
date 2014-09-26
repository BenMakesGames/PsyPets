<?php
if($okay_to_be_here !== true)
  exit();

$a = mt_rand(1, 3);

if($a == 1)
{
  $descript = "scroll";
  $itemname = "Food-Summoning Scroll";
}
else if($a == 2)
{
  $descript = "scroll";
  $itemname = "Scroll of Musical Instrument Summoning";
}
else if($a == 3)
{
  $descript = "wand";
  $itemname = "Wand of Wonder";
}

delete_inventory_byid($this_inventory['idnum']);

add_inventory($user['user'], '', $itemname, '', $this_inventory['location']);

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Opened an Elfin Bottle', 1);
?>
<p>You open up the bottle and tip it upside down, but it takes a good bit of jostling before the <?= $descript ?> inside finally slips out.</p>
