<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/rocks.php';

$RECOUNT_INVENTORY = true;

$command = 'SELECT idnum,itemname FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' AND itemname IN (\'Small Rock\', \'Stone Block\', \'Large Rock\', \'Really Enormously Tremendous Rock\')';
$inventory = $database->FetchMultiple($command, 'fetching rocks');

if(count($inventory) > 0)
{
  $itemlist = array();

  $num_items = 0;
  $break_chance = 0;

  foreach($inventory as $item)
  {
    if($item['itemname'] == 'Small Rock' || $item['itemname'] == 'Stone Block')
      $num_items += (rand() % 4) - 1;
    else if($item['itemname'] == 'Large Rock')
      $num_items += (rand() % 7) - 1;
    else if($item['itemname'] == 'Really Enormously Tremendous Rock')
      $num_items += (rand() % 11) - 1;

    $break_chance += .2;

    delete_inventory_byid($item['idnum']);
  }

  $itemlist = GenerateItemsFromRocks($num_items);

  $message = 'You hop on the ' . $this_inventory['itemname'] . ' and start peddling!  Bits of broken rock ricochet across the room as rock after rock is pounded away.</p><p>';

  if(mt_rand(1, 100) <= $break_chance)
  {
    $message .= 'You hear a sudden snap as one of the peddles disappears from under your feet, followed by a solid *thunk*.  Several more loud noises later the entire aparatus lies in shambles beneath you.</p><p>';

    $itemlist[] = 'Hammer';

    if(mt_rand(1, 3) != 1)
      $itemlist[] = 'Hammer';
    if(mt_rand(1, 2) == 1)
      $itemlist[] = 'Hammer';

    if(mt_rand(1, 2) != 1)
      $itemlist[] = 'Tin';
    if(mt_rand(1, 2) != 1)
      $itemlist[] = 'Tin';

    $itemlist[] = 'Chain';

    $itemlist[] = 'Rubble';

    if(mt_rand(1, 2) == 1)
      $itemlist[] = 'Rubble';
    if(mt_rand(1, 2) == 1)
      $itemlist[] = 'Rubble';

    delete_inventory_byid($this_inventory['idnum']);
  }

  if(count($itemlist) > 0)
  {
    sort($itemlist);
  
    $items = 0;
    $listtext = '';
    foreach($itemlist as $itemname)
    {
      add_inventory($user['user'], '', $itemname, 'Recovered from smashing!', $this_inventory['location']);

      $items++;

      if($items > 1)
        $listtext .= ($items == count($itemlist) ? ' and ' : ', ');

      $listtext .= $itemname;
    }

    $message .= 'The dust eventually settles, revealing... ' . $listtext . '.';
  }
  else
    $message .= 'The dust eventually settles, revealing... nothing.';
}
else
  $message = 'There aren\'t any rocks here to smash...';

echo $message;
?>
