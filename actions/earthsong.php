<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['step'] == 2)
{
  require_once 'commons/rocks.php';

  $RECOUNT_INVENTORY = true;

  $command = 'SELECT idnum,itemname FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' AND itemname IN (\'Small Rock\', \'Stone Block\', \'Large Rock\', \'Really Enormously Tremendous Rock\')';
  $inventory = $database->FetchMultiple($command, 'fetching rocks');

  $num_rocks = count($inventory);

  if($num_rocks > 0)
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

    if($num_rocks > 1)
    {
      $say_rocks = 'rocks';
      $say_them = 'them';
    }
    else
    {
      $say_rocks = 'rock';
      $say_them = 'it';
    }

    $message = 'The printed notes lift from the paper as you sing their melody...</p><p>No sooner than you\'ve sung the final note, the door of your house crashes open, revealing a monstrous earth golem!</p><p>The golem howls, looks around the room, and upon seeing your ' . $num_rocks . ' ' . $say_rocks . ' rushes toward ' . $say_them . ' and stuffs ' . $say_them . ' in its enormous mouth!</p><p>After swallowing the last of ' . $say_them . ', ';

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

      $message .= 'it howls again, shoots an accusing glance at you, and spits out ' . $listtext . ' before storming out the door.';
    }
    else
      $message .= 'it stomps back out.';
  }
  else
    $message = 'The printed notes lift from the paper as you sing their melody...</p><p>No sooner than you\'ve sung the final note, the door of your house crashes open, revealing a monstrous earth golem!</p><p>The golem howls, then looks around the room - searching for something - howls again, and stomps back out the way it came.</p>';

  delete_inventory_byid($this_inventory['idnum']);

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Earth Golems Summoned', 1);

  echo $message;
}
else
{
  echo '
    <p>Will you use this scroll?  Doing so will no doubt attract the attention of nearby earth golems!</p>
    <ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&amp;step=2">That\'s my intention!</a></li></ul>
  ';
}
?>
