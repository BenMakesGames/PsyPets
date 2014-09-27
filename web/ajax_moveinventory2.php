<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/inventory.php';
require_once 'commons/houselib.php';

if(!array_key_exists('ids', $_POST))
{
  echo 'message:No items were selected...';
  exit();
}

$itemids = explode(',', $_POST['ids']);

$house = get_house_byuser($user['idnum']);
if($house === false)
{
  echo 'message:Error loading your house.  If this problem persists (especially if there\'s nothing about it in the City Hall), please report it to <a href="admincontact.php">an administrator</a>.' . "\n" .
       'failure:' . implode(',', $itemids);
  exit();
}

$target = $_POST['to'];

$failed_ids = array();
$moved_ids = array();
$moved_names = array();

foreach($itemids as $id)
{
  $item = get_inventory_byid($id);

  if($item['user'] == $user['user'] && $item['location'] != 'seized' && $item['location'] != 'pet' && $item['location'] != 'storage/outgoing')
  {
    $item_details = get_item_byname($item['itemname']);

    if($item_details === false)
      continue;

    if($item_details['cursed'] == 'yes')
    {
      if(substr($item['location'], 0, 7) == 'storage' || $target == 'Basement' || $target == 'Library Add-on' || $target == 'My Store')
      {
        $failed_ids[] = $id;
        continue;
      }

      if(substr($item['location'], 0, 4) == 'home' && $target == 'Storage')
      {
        $failed_ids[] = $id;
        continue;
      }
    }

    if(($target == 'Basement' || $target == 'Library Add-on') && ($item_details['nomarket'] == 'yes' || $item['health'] < $item_details['durability']))
    {
      $failed_ids[] = $id;
      continue;
    }

    $library_exceptions = array(
      'The Astronomer',
      'The Two Travelers and the Axe',
      'Utterly Exhaustive List of Things That Don\'t Exist',
      'Genealogy of Ki Ri Kashu',
      'Super-Secret Ninja Technique Scroll',
    );

    if($target == 'Library Add-on'
      && substr($item_details['itemtype'], 0, 10) != 'print/book'
      && substr($item_details['itemtype'], 0, 15) != 'print/newspaper'
      && !in_array($item['itemname'], $library_exceptions)
    )
    {
      $failed_ids[] = $id;
      continue;
    }

    $rooms_changed[$item['location']] = true;

    $moved_ids[] = $id;
    $moved_name[$id] = $item['itemname'];
    $moved_itemid[$id] = $item_details['idnum'];
    $moved_quantity[$id]++;
    $moved_names[quote_smart($item['itemname'])] = true;
  }
  else
    $failed_ids[] = $id;
}

if(count($moved_ids) > 0)
{
  $target_xhtml = $target;

  if($target == 'Storage')
  {
    $newloc = 'storage';
    $target_xhtml = '<a href="storage.php">Storage</a>';
  }
  else if($target == 'Locked Storage')
  {
    $newloc = 'storage/locked';
    $target_xhtml = '<a href="storage_locked.php">Locked Storage</a>';
  }
  else if($target == 'Home' || $target == 'Common')
    $newloc = 'home';
  else if($target == 'Basement')
  {
    $addons = take_apart(',', $house['addons']);
    if(array_search('Basement', $addons) === false)
    {
      echo 'message:You do not have a Basement...' . "\n" .
           'failure:' . implode(',', $itemids);
      exit();
    }
    
    $do_basement_move = true;

    $target_xhtml = '<a href="/myhouse/addon/basement.php">Basement</a>';
  }
  else if($target == 'Library Add-on')
  {
    $addons = take_apart(',', $house['addons']);
    if(array_search('Library', $addons) === false)
    {
      echo 'message:You do not have a Library...' . "\n" .
           'failure:' . implode(',', $itemids);
      exit();
    }

    $do_library_move = true;

    $target_xhtml = '<a href="/myhouse/addon/library.php">Library</a>';
  }
  else if($target == 'My Store')
  {
    $newloc = 'storage/mystore';
    $target_xhtml = '<a href="mystore.php">My Store</a>';
  }
  else if(strlen($house['rooms']) > 0)
  {
    $rooms = explode(',', $house['rooms']);
    $newroom = $target;
    if(array_search($target, $rooms) !== false)
      $newloc = 'home/' . $target;
    else
    {
      echo 'message:You do not have a room called ' . $target . '...' . "\n" .
           'failure:' . implode(',', $itemids);
      exit();
    }
  }

  if($do_basement_move)
  {
    if(count($moved_ids) + $house['curbasement'] > $house['maxbasement'])
    {
      echo 'message:The items selected will not all fit in the basement.' . "\n" .
           'failure:' . implode(',', $itemids);
      exit();
    }
    else
    {
      require_once 'commons/basementlib.php';

      foreach($moved_ids as $id)
      {
        delete_inventory_byid($id);
        add_to_basement($user['idnum'], $user['locid'], $moved_name[$id], $moved_quantity[$id]);
      }

      recalc_basement_bulk($user['idnum'], $user['locid']);

      $new_bulk = recount_house_bulk($user, $house);
      $house['curbulk'] = $new_bulk;

      echo 'newbulk:' . render_house_bulk($house) . "\n";
    }
  }
  else if($do_library_move)
  {
    require_once 'commons/librarylib.php';
    
    foreach($moved_ids as $id)
    {
      delete_inventory_byid($id);
      add_to_library($user['idnum'], $moved_itemid[$id], $moved_quantity[$id]);
    }

    $new_bulk = recount_house_bulk($user, $house);
    $house['curbulk'] = $new_bulk;

    echo 'newbulk:' . render_house_bulk($house) . "\n";
  }
  else
  {
    $rooms_changed[$newloc] = true;

    $command = 'UPDATE monster_inventory SET `location`=' . quote_smart($newloc) . ',changed=' . $now . ',forsale=0 WHERE idnum IN (' . implode(',', $moved_ids) . ') LIMIT ' . count($moved_ids);
    $database->FetchNone($command, 'moving items');

    $new_bulk = recount_house_bulk($user, $house);
    $house['curbulk'] = $new_bulk;

    if($user['autosorterrecording'] == 'yes')
    {
      $command = 'DELETE FROM psypets_autosort WHERE userid=' . $user['idnum'] . ' AND itemname IN (' . implode(',', array_keys($moved_names)) . ') LIMIT ' . count($moved_names);
      $database->FetchNone($command, 'removing old rules');
    
      $command = '
        INSERT INTO psypets_autosort (userid, itemname, room)
        VALUES
          (' . $user['idnum'] . ', ' . implode(', ' . quote_smart($newloc) . '), (' . $user['idnum'] . ', ', array_keys($moved_names)) . ', ' . quote_smart($newloc) . ')
      ';
      $database->FetchNone($command, 'creating rules');
    }

    echo 'newbulk:' . render_house_bulk($house) . "\n";
  }
}

if(count($moved_ids) > 0)
{
/*
  foreach($rooms_changed as $room=>$dummy)
    unlink('cache/myhouse/' . $user['user'] . '-' . str_replace('/', '-', $room) . '.sqlserial');
*/
  echo 'message:<span class="success">' . count($moved_ids) . ' item' . (count($moved_ids) == 1 ? ' was' : 's were') . ' moved to ' . $target_xhtml . '.';

  if(count($failed_ids) > 0)
    echo '  ' . count($failed_ids) . ' item' . (count($failed_ids) == 1 ? '' : 's') . ' could not be moved.';

  echo '</span>' . "\n" . 
       'success:' . implode(',', $moved_ids);
}

if(count($failed_ids) > 0)
{
  if(count($moved_ids) == 0)
    echo 'message:<span class="failure">The selected item' . (count($failed_ids) == 1 ? '' : 's') . ' could not be moved to ' . $target_xhtml . '.</span>';

  echo "\n" . 'failure:' . implode(',', $failed_ids);
}
?>