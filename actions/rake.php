<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/debris.php';

$RECOUNT_INVENTORY = true;

$command = 'SELECT idnum,itemname FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' AND itemname IN (\'Rubble\', \'Ruins\', \'Debris\')';
$inventory = $database->FetchMultiple($command, 'fetching rubble, ruin, and debris (NOT said "deb-riss" e_e  jesus.  seriously.)');

$itemlist = array();

foreach($inventory as $item)
{
  $itemlist[] = GenerateItemFromDebris($user, ($item['itemname'] == 'Debris'));
  delete_inventory_byid($item['idnum']);
}

if(count($itemlist) > 0)
{
  $items = 0;
  $listtext = '';
  foreach($itemlist as $itemname)
  {
    add_inventory($user['user'], '', $itemname, 'Recovered from raking', $this_inventory['location']);

    $items++;

    if($items > 1)
      $listtext .= ($items == count($itemlist) ? ' and ' : ', ');

    $listtext .= $itemname;
  }

  $message = 'You rake through any and all Debris, Rubble and Ruins, turning up ' . $listtext . '.';
}
else
  $message = 'There isn\'t any Debris, Rubble, or Ruins to rake through.';

echo $message;
?>
